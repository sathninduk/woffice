<?php

require_once dirname(__FILE__) . '/class-simple-ics-event.php';

/**
 * Handle database events to .ics events export
 *
 * Class IcsExport
 */
class IcsExport
{
    use SimpleICSUtil;

    const MIME_TYPE = 'text/calendar; charset=utf-8';

    /**
     * Hold events
     *
     * @var array
     */
    var $events = [];
    /**
     * Product string
     *
     * @var string
     */
    var $productString = '-//woffice/xtendify/EN';

    /**
     * Time zone attribute used in .ics file
     *
     * @var string
     */
    var $timeZone = '';

    /**
     * User email address used in .ics file
     *
     * @var string
     */
    var $userEmail = '';

    /**
     * Template name
     *
     * @var string
     */
    static $Template = null;

    /**
     * IcsExport constructor.
     *
     * @param string $time_zone
     * @param string $user_email
     */
    function __construct($time_zone, $user_email)
    {
        $this->timeZone = $time_zone;
        $this->userEmail = $user_email;
    }

    /**
     * Add event by closure
     *
     * @param Closure $eventOrClosure
     *
     * @return SimpleICSEvent
     */
    function addEvent($eventOrClosure)
    {
        if (is_object($eventOrClosure) && ($eventOrClosure instanceof Closure)) {
            $event = new SimpleICSEvent();
            $eventOrClosure($event);
        }
        $this->events[] = $event;
        return $event;
    }

    /**
     * Serialize calendar events
     *
     * @return string
     * @throws Exception
     */
    function serialize()
    {
        return $this->filterLineLimit($this->render(self::$Template, $this));
    }
}

IcsExport::$Template = <<<EOT
BEGIN:VCALENDAR
VERSION:2.0
PRODID:{{productString}}
METHOD:PUBLISH
CALSCALE:GREGORIAN
X-WR-CALNAME:{{userEmail}}
X-WR-TIMEZONE:{{timeZone}}
{{events|serialize}}
END:VCALENDAR

EOT;

trait SimpleICSUtil
{
    /**
     * Set line and check limit of the calendar events
     *
     * @param string $input
     * @param int    $lineLimit
     *
     * @return string
     */
    function filterLineLimit($input, $lineLimit = 70)
    {
        // Go through each line and make them shorter.
        $output = '';
        $pos    = 0;
        while ($pos < strlen($input)) {
            // Find the newline
            $newLinepos = strpos($input, "\n", $pos + 1);
            if (!$newLinepos) {
                $newLinepos = strlen($input);
            }
            $line = substr($input, $pos, $newLinepos - $pos);
            if (strlen($line) <= $lineLimit) {
                $output .= $line;
            } else {
                // First line cut-off limit is $lineLimit
                $output .= substr($line, 0, $lineLimit);
                $line    = substr($line, $lineLimit);

                // Subsequent line cut-off limit is $lineLimit - 1 due to the leading white space
                $output .= "\n " . substr($line, 0, $lineLimit - 1);

                while (strlen($line) > $lineLimit - 1) {
                    $line   = substr($line, $lineLimit - 1);
                    $output .= "\n " . substr($line, 0, $lineLimit - 1);
                }
            }
            $pos = $newLinepos;
        }
        return $output;
    }

    /**
     * Dynamically called event attributes
     *
     * @param mixed $input
     *
     * @return string
     * @throws Exception
     */
    function filterCalDate($input)
    {
        if (!is_a($input, 'DateTime')) {
            $input = new DateTime($input);
        } else {
            $input = clone $input;
        }
        $input->setTimezone(new DateTimeZone('UTC'));
        return $input->format('Ymd\THis\Z');
    }

    /**
     * Serialize event attributes if required
     *
     * @param $input
     *
     * @return array|string
     */
    function filterSerialize($input)
    {
        if (is_object($input)) {
            return $input->serialize();
        }
        if (is_array($input)) {
            $output = '';
            array_walk($input, function ($item) use (&$output) {
                $output .= $this->filterSerialize($item);
            });
            return trim($output, "\n");
        }
        return $input;
    }

    /**
     * Filter quote
     *
     * @param string $input
     *
     * @return string
     */
    function filterQuote($input)
    {
        return quoted_printable_encode($input);
    }

    /**
     * Filter scape
     *
     * @param $input
     *
     * @return mixed|string|string[]|null
     */
    function filterEscape($input)
    {
        $input = preg_replace('/([\,;])/', '\\\$1', $input);
        $input = str_replace("\n", "\\n", $input);
        $input = str_replace("\r", "\\r", $input);
        return $input;
    }


    /**
     * Render events as .ics format
     *
     * @param string $tpl
     * @param string $scope
     *
     * @return mixed
     * @throws Exception
     */
    function render($tpl, $scope)
    {
        while (preg_match("/\{\{([^\|\}]+)((?:\|([^\|\}]+))+)?\}\}/", $tpl, $m)) {
            $replace = $m[0];
            $varname = $m[1];
            $filters = isset($m[2]) ? explode('|', trim($m[2], '|')) : [];
            $value   = $this->fetchVariable($scope, $varname);
            $self    = &$this;
            array_walk($filters, function (&$item) use (&$value, $self) {
                $item = trim($item, " \n ");
                if (!is_callable([$self, 'filter' . ucfirst($item)])) {
                    throw new Exception('No such filter: ' . $item);
                }

                $value = call_user_func_array([$self, 'filter' . ucfirst($item)], [$value]);
            });

            $tpl = str_replace($m[0], $value, $tpl);
        }
        return $tpl;
    }

    /**
     * Fetch event variable
     *
     * @param string $scope
     * @param string $var
     *
     * @return mixed
     * @throws Exception
     */
    function fetchVariable($scope, $var)
    {
        if (strpos($var, '.') !== false) {
            $split  = explode('.', $var);
            $var    = array_shift($split);
            $rest   = implode('.', $split);
            $val    = $this->fetchVariable($scope, $var);
            return $this->fetchVariable($val, $rest);
        }

        if (is_object($scope)) {
            $getterMethod = 'get' . ucfirst($var);
            if (method_exists($scope, $getterMethod)) {
                return $scope->{$getterMethod}();
            }
            return $scope->{$var};
        }

        if (is_array($scope)) {
            return $scope[$var];
        }

        throw new Exception('A strange scope');
    }
}
