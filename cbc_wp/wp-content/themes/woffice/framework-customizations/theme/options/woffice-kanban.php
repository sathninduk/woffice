<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
$columns_values = array(
		'0' => array(
			'title' => 'TODO',
			'color' => '#0275d8',
		),
		'1' => array(
			'title' => 'Active',
			'color' => '#5bc0de',
		),
		'2' => array(
			'title' => 'Urgent',
			'color' => '#f0ad4e',
		),
		'3' => array(
			'title' => 'Completed',
			'color' => '#5cb85c',
		),
	);

$options = array(
	'woffice-kanban' => array(
		'title'   => __( 'Woffice Kanban', 'woffice' ),
		'type'    => 'tab',
		'options' => array(
			'kanban-box' => array(
				'title'   => __( 'Main Options', 'woffice' ),
				'type'    => 'box',
				'options' => array(
					'kanban-columns' => array(
						'type' => 'addable-popup',
						'value' => $columns_values,
						'label' => __('Column Settings', '{domain}'),
						'desc'  => __('Title is used as Kanban column title and color is used as column background color.', '{domain}'),
						'template' => '{{- title }}',
						'popup-title' => null,
						'size' => 'small', // small, medium, large
						'limit' => 4, // limit the number of popup`s that can be added
						'add-button-text' => __('Add', '{domain}'),
					    'sortable' => false,
						'popup-options' => array(
							'title' => array(
								'type'  => 'text',
								'label'  => __('Column Title', 'woffice'),
								'attr'  => array('autocomplete' => 'off'),
								'desc' => __('This is your background color (used in the column title background color).','woffice')
							),
							'color' => array(
								'type'  => 'color-picker',
								'attr'  => array('autocomplete' => 'off'),
								'label'  => __('Column Background color', 'woffice'),
								'desc' => __('This is your background color (used in the column title background color).','woffice')
							),
						),
					),
				),
			),
		)
	)
);