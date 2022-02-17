<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'number'  => array(
	    'type'  => 'select',
	    'value' => 5,
	    'label' => __('Number of posts', 'woffice'),
	    'choices' => array(
	        '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
            '6' => 6,
            '7' => 7,
            '8' => 8,
            '9' => 9,
            '10' => 10,
            '20' => 20),
	),
	'excerpt'  => array(
		'type'  => 'checkbox',
		'value' => true,
		'label' => __('Show posts excerpt', 'woffice'),
	),
	'category'  => array(
		'type'  => 'multi-select',
		'value' => '',
		'population' => 'taxonomy',
		'source' => 'category',
		'label' => __('Categories', 'woffice'),
		'desc'  => __('Leave empty to remove the filter.', 'woffice'),
	),
);