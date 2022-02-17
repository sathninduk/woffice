<?php if (!defined('ABSPATH')) die('Direct access forbidden.');

global $bp;

echo $before_widget;
echo $title;

$current_user = wp_get_current_user();

if ($event_visibility == 'personal' || $event_visibility == 'general') {
	$visibility = $event_visibility;
	$visibility_id = $current_user->ID;
} else {
	$visibility_data = explode("_", $event_visibility);
	$visibility = $visibility_data[0];
	$visibility_id = $visibility_data[1];
}
if ($current_user) {
	echo do_shortcode("[woffice_calendar widget='true' visibility='$visibility' id='{$visibility_id}']");
}

echo $after_widget;
