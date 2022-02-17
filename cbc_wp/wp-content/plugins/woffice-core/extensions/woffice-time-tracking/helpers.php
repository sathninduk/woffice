<?php
/**
 * Helpers for the Woffice Time Tracking
 */
if ( ! function_exists('woffice_get_tracking_total')) {
    /**
     * Returns the time worked for the log's track timestamps
     *
     * @param $tracks array - array of Timestamps
     * @return string - the total time HH:MM
     */
    function woffice_get_tracking_total($tracks) {
        if (empty($tracks))
            return '00:00';

        $total_time = 0;

        foreach ($tracks as $index=>$track) {

            // Only if it's a stop
            if ($track['action'] == 'stop') {

                // The end timestamp
                $end_float = (float) woffice_get_time_float($tracks[$index]['timestamp']);

                // The associated start is the one before the stop
                $start_float = (float) woffice_get_time_float($tracks[$index-1]['timestamp']);
                // We add the difference
                $total_time = $total_time + ($end_float - $start_float);

            }

        }

        // If last was a start we compare to now
        $last = end($tracks);

        if ($last['action'] == 'start') {
            $start_float = woffice_get_time_float($last['timestamp']);
            $now_float = woffice_get_time_float(strtotime("now"));
            $total_time = $total_time + ((int) $now_float - (int) $start_float);
        }

        // Returns a HH:MM:SS
        $formatted_time = sprintf('%02d:%02d:%02d', (int) $total_time, fmod($total_time, 1) * 60, 0);

        return $formatted_time;
    }
}

if ( ! function_exists('woffice_get_formatted_tracking_key_date')) {
    /**
     * Returns a float from a Timestamp
     *
     * @param $timestamp
     * @return float
     */
    function woffice_get_time_float($timestamp) {

        // To the right format
        $time = date('H:i', $timestamp);

        // We split to a float number
        $parts = explode(':', $time);
        $float = $parts[0] + floor(($parts[1]/60)*100) / 100 . PHP_EOL;

        return $float;
    }
}

if ( ! function_exists('woffice_tracking_is_working')) {
    /**
     * Is the current user currently working?
     *
     * @return boolean
     */
    function woffice_tracking_is_working() {
        if (!is_user_logged_in())
            return false;

        // Get our log
        $saved_log = get_user_option('woffice_time_tracking', get_current_user_id());
        $saved_log = (!is_array($saved_log)) ? array() : $saved_log;

        if (empty($saved_log))
            return false;

        // We get the right date key for our current time
        $key_date = woffice_get_formatted_tracking_key_date(time());

        if (!isset($saved_log[$key_date]))
            return false;

        // Get the last track in the log
        $last_track_full = array_values(array_slice($saved_log[$key_date], -1));
        $last_track  = $last_track_full[0];

        // If the last track was a start and before the current time
        if ($last_track['action'] == 'start' && $last_track['timestamp'] < time())
            return true;

        return false;
    }
}

if ( ! function_exists('woffice_current_tracking_time')) {
    /**
     * Get the current tracking of the logged user
     *
     * @return string
     */
    function woffice_current_tracking_time() {
        if (!is_user_logged_in())
            return '00:00';

        // Get our log
        $saved_log = get_user_option('woffice_time_tracking', get_current_user_id());
        $saved_log = (!is_array($saved_log)) ? array() : $saved_log;

        if (empty($saved_log))
            return '00:00:00';

        // We get the right date key for our current time
        $key_date = woffice_get_formatted_tracking_key_date(time());

        if (!isset($saved_log[$key_date]))
            return '00:00:00';

        return woffice_get_tracking_total($saved_log[$key_date]);
    }
}

if ( ! function_exists('woffice_get_formatted_tracking_key_date')) {
    /**
     * Return the right format from a timestamp
     * This format is used a key in our log array
     * [dd-mm-yy] => array(...)
     *
     * @param string $timestamp
     *
     * @return string
     */
    function woffice_get_formatted_tracking_key_date($timestamp) {
        return date('d-m-Y', $timestamp);

    }
}