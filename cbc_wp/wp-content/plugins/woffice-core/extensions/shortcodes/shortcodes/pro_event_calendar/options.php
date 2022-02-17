<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * We get the calendars :
 * from dpProEventCalendar/settings/custom_shortcodes.php
 */
$calendars = array();

if(defined('DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS')) {

    global $wpdb, $table_prefix;
    $table_name_calendars = DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
    $querystr = "
SELECT *
FROM $table_name_calendars
ORDER BY title ASC
";
    $calendars_obj = $wpdb->get_results($querystr, OBJECT);

    foreach ($calendars_obj as $calendar_key) {
        $calendars[$calendar_key->id] = $calendar_key->title;
    }

    $options = array(
        'calendar' => array(
            'type' => 'select',
            'label' => __('Calendar', 'woffice'),
            'choices' => $calendars,
        ),
        'type' => array(
            'type' => 'select',
            'label' => __('Layout', 'woffice'),
            'choices' => array(
                'default' => __('Default (calendar)', 'woffice'),
                'upcoming' => __('Upcoming Events', 'woffice'),
                'past' => __('Past Events', 'woffice'),
                'accordion' => __('Accordion List', 'woffice'),
                'accordion-upcoming' => __('Accordion Upcoming Events', 'woffice'),
                'add-event' => __('Add Event', 'woffice'),
                'bookings-user' => __('List of Bookings by Logged in User', 'woffice'),
                'today-events' => __('Today Events', 'woffice'),
                'gmap-upcoming' => __('Google Map Upcoming Events', 'woffice'),
                'grid-upcoming' => __('Grid Upcoming Events', 'woffice'),
                'compact' => __('Compact', 'woffice'),
                'countdown' => __('Countdown', 'woffice'),
            ),
            'value' => 'upcoming',
        ),
        'view' => array(
            'type' => 'select',
            'label' => __('View', 'woffice'),
            'choices' => array(
                'monthly' => __('Monthly Calendar (Displays a calendar with counters or event titles.)', 'woffice'),
                'monthly-all-events' => __('Monthly Event List (List all events in the month)', 'woffice'),
                'weekly' => __('Weekly (Events weekly as a schedule or a list)', 'woffice'),
                'daily' => __('Daily (Events daily as a schedule or a list)', 'woffice'),
            ),
            'value' => 'calendar'
        ),
        'all-events' => array(
            'type' => 'switch',
            'label' => __('All events ?', 'woffice'),
            'desc' => __('Display the events from all the calendars and unassigned events.', 'woffice'),
            'right-choice' => array(
                'value' => 'yep',
                'label' => __('Yep', 'woffice')
            ),
            'left-choice' => array(
                'value' => 'nope',
                'label' => __('Nope', 'woffice')
            ),
            'value' => 'nope',
        ),

    );
}