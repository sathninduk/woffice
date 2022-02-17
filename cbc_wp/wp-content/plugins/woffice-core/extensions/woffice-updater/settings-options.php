<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$ext_instance = fw()->extensions->get( 'woffice-updater' );

$status = get_option('woffice_license');
$status_ready = ($status == "checked") ? "Checked" : "Not Checked";

$options = array(
	'build' => array(
		'type'    => 'tab',
		'title'   => __( 'Updater Settings', 'woffice' ),
		'options' => array(
			'tf_username' => array(
				'label'   => __( 'Envato Username', 'woffice' ),
				'type'    => 'text',
				'desc'  => __('Your Envato (Themeforest) Username goes here. ','woffice'),
			),
			'tf_purchasecode' => array(
				'label'   => __( 'Woffice Purchase code', 'woffice' ),
				'type'    => 'text',
				'desc'  => __('The Purchase code of this license. You can find it in the Themeforest\'s download page (it\'s a large number).','woffice'),
			),
			'tf_status' => array(
				'label'   => __( 'Purchase code status.', 'woffice' ),
				'type'    => 'html',
				'html'  => '<span class="highlight">'.$status_ready.'</span>',
                'desc' => __('The license works for one site only and if you are not on Localhost. Otherwise, your license will be activated when your site will be online. Feel free to get in touch if you need to change that.', 'woffice')
			),
            'beta' => array(
                'label'   => __( 'Beta tester', 'woffice' ),
                'type'    => 'switch',
                'right-choice' => array(
                    'value' => 'yep',
                    'label' => __( 'Yep', 'woffice' )
                ),
                'left-choice'  => array(
                    'value' => 'nope',
                    'label' => __( 'Nope', 'woffice' )
                ),
                'val' => 'nope',
                'desc'  => __('Beta releases will be updated as well. You can report any issue, and we\'ll fix them. You\'ll get updates first though.', 'woffice')
            ),
            'note' => array(
                'label'   => __( 'Note', 'woffice' ),
                'type'    => 'html',
                'html'  => __('For security purpose we collect your admin email address and attach it to the license in our server. This is done in order to provide a better and safer service to our customers.', 'woffice'),
            ),
		)
	),
);