<?php
/**
 * Used as event closure to set event attributes
 *
 * Class SimpleICSEvent
 */
if (!class_exists('SimpleICSEvent')) {

    class SimpleICSEvent
    {
        use SimpleICSUtil;

        /**
         * Unique id for each event required by .ics file required
         *
         * @var string
         */
        var $uniqueId;

        /**
         * Event start date
         *
         * @var string
         */
        var $startDate;

        /**
         * Event end date
         *
         * @var string
         */
        var $endDate;

        /**
         * Date stamp
         *
         * @var string
         */
        var $dateStamp;

        /**
         * Event location
         *
         * @var string
         */
        var $location;

        /**
         * Event description
         *
         * @var string
         */
        var $description;

        /**
         * Event url
         *
         * @var string
         */
        var $uri;

        /**
         * Event title
         *
         * @var string
         */
        var $summary;

        /**
         * Template name
         *
         * @var string
         */
        static $Template;

        /**
         * SimpleICSEvent constructor.
         */
        function __construct()
        {
            $this->uniqueId = uniqid();
        }

        /**
         * Serialize event object
         *
         * @return mixed
         */
        function serialize()
        {
            return $this->render(self::$Template, $this);
        }
    }

SimpleICSEvent::$Template = <<<EOT
BEGIN:VEVENT
UID:{{uniqueId|escape}}
DTSTART;TZID={{timeZone}}:{{startDate|calDate}}
DTSTAMP;TZID={{timeZone}}:{{dateStamp|calDate}}
DTEND;TZID={{timeZone}}:{{endDate|calDate}}
LOCATION:{{location|escape}}
DESCRIPTION:{{description|escape}}
URL;VALUE=URI:{{uri|escape}}
SUMMARY:{{summary|escape}}
SEQUENCE:0
END:VEVENT

EOT;
}
