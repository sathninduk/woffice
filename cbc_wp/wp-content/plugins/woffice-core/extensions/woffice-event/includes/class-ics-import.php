<?php

/**
 * Handle .ics file event extraction
 *
 * Class icsImport
 */
class icsImport
{
    /**
     * Function is to get all the contents from ics and explode all the data according to the events and its sections
     *
     * @param string $file
     *
     * @return array|mixed
     */
    public function getIcsEventsAsArray($file)
    {
        $ical_string    = file_get_contents($file);
        $ics_dates      = array();
        $ics_dates_meta = array();
        // Explode the ICs Data to get datas as array according to string ‘BEGIN:’
        $ics_data       = explode("BEGIN:", $ical_string);
        // Iterating the icsData value to make all the start end dates as sub array
        foreach ($ics_data as $key => $value) {
            $ics_dates_meta[trim($key)] = explode("\n", trim($value));
        }
        // Iterating the Ics Meta Value
        foreach ($ics_dates_meta as $key => $value) {
            foreach ($value as $sub_key => $sub_value) {
                // To get ics events in proper order
                $ics_dates = $this->getICSDates($key, $sub_key, $sub_value, $ics_dates);
            }
        }
        
        return $ics_dates;
    }

    /**
     * Function is to avoid the elements which is not having the proper start, end  and summary information
     *
     * @param string $key
     * @param string $sub_key
     * @param string $sub_value
     * @param string $ics_dates
     *
     * @return string
     */
    public function getICSDates($key, $sub_key, $sub_value, $ics_dates)
    {
        $key       = trim($key);
        $sub_key   = trim($sub_key);
        $sub_value = trim($sub_value);
        
        if ($key != 0 && $sub_key == 0) {
            $ics_dates[$key]['BEGIN'] = $sub_value;
        } else {
            $arr_sub_value = explode(':', $sub_value, 2);
            if (isset($arr_sub_value[1])) {
                $ics_dates[$key][$arr_sub_value[0]] = $arr_sub_value[1];
            }
        }
        
        return $ics_dates;
    }
}
