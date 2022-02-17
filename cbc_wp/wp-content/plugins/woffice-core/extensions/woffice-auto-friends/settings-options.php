<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}


$html = '';
$html .= '<b>'. __('Once enabled, any new user registered will be automatically made "friend" with all other users.', 'woffice') .'</b>';

if (!woffice_bp_is_active('friends')) {
	$html .= '<div class="error notice is-dismissable">';
		$html .= sprintf( '<p>%s <a href="'. admin_url('admin.php?page=bp-components') .'">%s</a>.</p>',
			__( 'This extension requires BuddyPress and the Friends component enabled. You can activate them from your', 'text_domain' ),
			__( 'BuddyPress settings', 'woffice' )
		);
	$html .= '</div>';
} else {
	$html .= '<hr>';
	$html .= '<p>'. __('You can re-create them with button underneath. It is an expensive operation for your server and might take more than 1 minute.','woffice') .'</p>';
	$html .= fw_html_tag('a', array(
		'href'  => admin_url('admin.php?page=fw-extensions&sub-page=extension&extension=woffice-auto-friends&run=true'),
		'class' => 'button-secondary',
		'style' => 'margin-top: 10px;',
	), __('Run relationships', 'woffice'));
}


$options = array(
	'build' => array(
		'type'    => 'box',
		'title'   => __( 'Auto BuddyPress friends', 'woffice' ),
		'options' => array(
			'wordpress_debug' => array(
				'type'  => 'html',
				'label' => __('Info', 'woffice'),
				'html'  => $html,
			),
            'status'    => array(
                'label' => __( 'Status', 'woffice' ),
                'desc'  => __( 'You can switch between "Enable" / "Disable"', 'woffice' ),
                'type'         => 'switch',
                'right-choice' => array(
                    'value' => 'enable',
                    'label' => __( 'Enable', 'woffice' )
                ),
                'left-choice'  => array(
                    'value' => 'disable',
                    'label' => __( 'Disable', 'woffice' )
                ),
                'value'        => 'disable',
            ),
		)
	),
);