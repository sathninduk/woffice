<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
?>

<?php 
if (!empty($atts['calendar'])):

	/*
	 * Custom parameters from the options :
	 */
	$all_events = (isset($atts['all-events']) && $atts['all-events'] == 'yep') ? ' include_all_events="1"' : '';
	$view = (!empty($atts['view'])) ? ' view="'.$atts['view'].'"' : '';
	$type = (!empty($atts['type']) && $atts['type'] !== 'default') ? ' type="'.$atts['type'].'"' : '';
	$calendar = (!empty($atts['calendar'])) ? ' id="'.$atts['calendar'].'"' : '';

	/*
	 * We render the shortcode :
	 */
	echo do_shortcode('[dpProEventCalendar '. $calendar . $type . $view . $all_events .']');

else :
	_e('Please select an unique ID in the shortcode option.','woffice'); 
endif;	
?>