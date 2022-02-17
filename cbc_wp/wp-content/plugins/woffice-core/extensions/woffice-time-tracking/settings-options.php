<?php if ( ! defined( 'FW' ) ) {
    die( 'Forbidden' );
}

$ext_instance = fw()->extensions->get( 'woffice-time-tracking' );

$options = array(
    'log' => array(
        'type'    => 'tab',
        'title'   => __( 'Tracking Log', 'woffice' ),
        'options' => array(
            'log-box' => array(
                'type'    => 'box',
                'options' => array(
                    'content' => array(
                        'type'  => 'html',
                        'label' => __('Select an user', 'woffice'),
                        'html'  => fw_render_view($ext_instance->locate_view_path( 'log' )),
                    )
                )
            ),
        )
    )

);