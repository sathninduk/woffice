<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Function to quickly checks that the extension is enabled
 *
 * @return void
 */
function woffice_directory_extension_on(){
    return;
}

/**
 * Custom Excerpt function
 *
 * @return string
 */
function woffice_directory_get_excerpt() {
	$limit = 20;

	// If we have a content set: we display content
    // Otherwise, fall back to excerpt (we need to keep that as a lot of users use excerpts
    if (get_the_content() !== '') {
        $excerpt = explode(' ', get_the_content(), $limit);
    } else {
        $excerpt = explode(' ', get_the_excerpt(), $limit);
    }

	if (count($excerpt)>=$limit) {
	    array_pop($excerpt);
	    $excerpt = implode(" ",$excerpt).'...';
	} else {
	    $excerpt = implode(" ",$excerpt);
	}	

	$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
	
	return $excerpt;
}
/**
 * Map on the single page
 */
function woffice_directory_single_map(){
	$item_location = woffice_get_post_option( get_the_ID(), 'item_location');

	if (!$item_location) {
		return;
	}

	if (!empty($item_location['location']) || !empty($item_location['coordinates'])) {
		
		echo '<div id="map-directory-single"></div>';

		$label = '';

		if (!empty($item_location['city'])) {
			$label = $item_location['city'] .', '. $item_location['country'];
		} elseif (!empty($item_location['coordinates'])) {
			$label = $item_location['coordinates']['lat'] .', '. $item_location['coordinates']['lng'];
		}

		echo '<span class="bottom-map-location"><i class="fa fa-map-marker text-light"></i>'. $label .'</span>';
	}
}

/**
 * Return the HTML printed by the function woffice_directory_single_map()
 *
 * @return string
 */
function woffice_get_directory_single_map(){
	ob_start();
	woffice_directory_single_map();
	return ob_get_clean();
}

/**
 * Get a the custom fields
 *
 * @param string $type
 */
function woffice_directory_single_fields($type){
	$class = ($type == "single") ? "on-single" : "intern-box";
	
	// IF NO DEFAULT FIELDS 
	$default_fields = fw_get_db_ext_settings_option('woffice-directory', 'default_fields');
	if (empty($default_fields)) {
		
		$item_fields = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option(get_the_ID(), 'item_fields') : '';

		if (!empty($item_fields) && function_exists('woffice_convert_fa4_to_fa5')) {
			echo '<div class="directory-item-fields '.$class.'">';
				echo '<ul>';
				foreach ($item_fields as $field) {
					echo '<li class="directory-item-field">';
						echo (!empty($field['icon'])) ? '<i class="'. woffice_convert_fa4_to_fa5($field['icon']) .'"></i>' : '';
						echo (!empty($field['title'])) ? $field['title'] : '';
					echo '</li>';
				} 
				echo '</ul>';
			echo '</div>';
		}
		
	} else {
		
		// IF DEFAULT FIELDS
		echo '<div class="directory-item-fields '.$class.'">';
			echo '<ul>';
				$counter = 1;
				foreach ($default_fields as $default_field) {
					echo '<li class="directory-item-field">';
						$content = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option(get_the_ID(), $counter.'-content') : '';
						echo (!empty($default_field['icon'])) ? '<i class="fa '.$default_field['icon'].'"></i>' : '';
						echo (!empty($content)) ? $content : '';
					echo '</li>';
					$counter++;	
				}
			echo '</ul>';
		echo '</div>';
	}
}

/**
 * Return the HTML printedn by the function woffice_directory_single_fields()
 *
 * @param $type
 *
 * @return string
 */
function woffice_get_directory_single_fields( $type ) {
	ob_start();
	woffice_directory_single_fields( $type );
	return ob_get_clean();
}

if(!function_exists('woffice_directory_content_exists')) {
	/**
	 * Check if the current directory has some content to render in the header (It works in the loop)
	 *
	 * @return bool
	 */
	function woffice_directory_content_exists()
	{
		$directory_filter = fw_get_db_ext_settings_option('woffice-directory', 'directory_filter', false);

		return $directory_filter;
	}
}