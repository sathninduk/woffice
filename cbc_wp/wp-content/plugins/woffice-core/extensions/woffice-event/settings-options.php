<?php if (!defined('FW')) {
    die('Forbidden');
}

$days = [
    'monday'    => __('Monday', 'woffice'),
    'tuesday'   => __('Tuesday', 'woffice'),
    'wednesday' => __('Wednesday', 'woffice'),
    'thursday'  => __('Thursday', 'woffice'),
    'friday'    => __('Friday', 'woffice'),
    'saturday'  => __('Saturday', 'woffice'),
    'sunday'    => __('Sunday', 'woffice')
];

$options = array(
    'build' => array(
        'type'    => 'box',
        'title'   => __('Calendar', 'woffice'),
        'options' => array(
            'woffice_calendar_days'         => array(
                'type'    => 'multi-select',
                'label'   => __('Days', 'woffice'),
                'desc'    => __('Select the days that\'ll show up in the calendar.', 'woffice'),
                'choices' => $days,
                'value'  => array_keys($days)
            ),
            'woffice_calendar_starting_day' => array(
                'type'    => 'select',
                'label'   => __('Starting day', 'woffice'),
                'desc'    => __('Select starting day', 'woffice'),
                'choices' => $days,
                'value'   => 'monday'
            ),
            'woffice_calendar_status' => array(
                'type'    => 'select',
                'label'   => __('Default status', 'woffice'),
                'desc'    => __('Select calendar event default status', 'woffice'),
                'choices' => [
                    'publish' => __('Publish', 'woffice'),
                    'draft'   => __('Draft', 'woffice'),
                    'pending' => __('Pending', 'woffice'),
                ],
                'value'   => 'publish'
            ),
        )
    ),
);
