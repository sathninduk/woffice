<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
?>

<?php 
if (!empty($atts['post_type'])):

	echo '<div class="woffie-post-creation">';

	if($atts['post_type'] == 'project') {
		$option_name = 'projects_create';
	} elseif($atts['post_type'] == 'wiki') {
		$option_name = 'wiki_create';
	} else {
		$option_name = 'post_create';
	}
	$allowed_data = woffice_get_settings_option('projects_create');
	if (Woffice_Frontend::role_allowed($allowed_data, $atts['post_type'])):

		/**
		 * BACKEND SIDE :
		 */
		$process_value = Woffice_Frontend::frontend_process($atts['post_type'], 0, true);

		/**
		 * FORM rendering
		 */
        Woffice_Frontend::frontend_render($atts['post_type'],$process_value);

	endif;

	echo '</div>';


endif;	
?>