<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'build' => array(
		'type'    => 'tab',
		'title'   => __( 'Birthdays Extension', 'woffice' ),
		'options' => array(
			'birthdays-box' => array(
				'title'   => __( 'Wigdet Options', 'woffice' ),
				'type'    => 'box',
				'options' => array(
					'display_age' => array(
					    'label' => __('Show the age of the person', 'woffice'),
					    'desc'  => __('In the widget beside the name', 'woffice'),
					    'type'         => 'switch',
						'right-choice' => array(
							'value' => 'yes',
							'label' => __( 'Yep', 'woffice' )
						),
						'left-choice'  => array(
							'value' => 'nope',
							'label' => __( 'Nope', 'woffice' )
						),
						'value'        => 'yes',
					),
                    'birthday_date_format' => array(
                        'type' => 'text',
                        'label' => __('Date Format', 'woffice'),
                        'desc' => __("Change date format of Birthday displayed in widgets. Default is 'F d', check other on <a href='http://php.net/manual/en/function.date.php' target='_blank'>php manual</a>", 'woffice'),
                        'value' => 'F d',
                    ),
                    'birthdays_range_limit' => array(
                        'type'      => 'select',
                        'label'     => __('Birthday range limit', 'woffice'),
                        'desc'      => __('Select birthday range.', 'woffice' ),
                        'value'     => 'no_limit',
                        'choices'   => array(
                            'no_limit'      => __('No Limit', 'woffice'),
                            'weekly'          => __('Weekly', 'woffice'),
                            'monthly'   => __('Monthly', 'woffice'),
                        ),
                    ),
					'birthday_field_name' => array(
						'type' => 'text',
						'label' => __('Field\'s name', 'woffice'),
						'desc' => __('This is the name of the field available in the user\'s profile', 'woffice'),
						'value' => 'Birthday',
					),
                    'birthdays_to_display' => array(
                        'type' => 'text',
                        'label' => __('Number of birthdays to show', 'woffice'),
                        'desc' => __('The max number of elements to show in the list.', 'woffice'),
                        'value' => '5',
                    ),
                    'birthday_working' => array(
                        'type' => 'html',
                        'label' => __('Working Anniversaries?', 'woffice'),
                        'html' => __("All instructions can be found <a href='https://alkaweb.ticksy.com/article/10400/' target='_blank'>on this article</a>.", 'woffice'),
                    ),
				)
			)
		)
	)
);