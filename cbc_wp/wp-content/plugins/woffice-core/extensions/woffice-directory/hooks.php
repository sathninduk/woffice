<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Add the directory to the search result
 *
 * @param $query
 * @return mixed
 */
function woffice_directory_filter_search_results($query) {
    if (is_page_template('page-templates/directory.php') && $query->is_search && !is_admin()) {
	    $query->set('post_type', array('directory'));
    }

    return $query;
}
add_filter('pre_get_posts','woffice_directory_filter_search_results');


/**
 * JS FOR THE MAP IN THE FOOTER
 */
function woffice_directory_js_load(){
	if (is_page_template("page-templates/page-directory.php") || is_tax( 'directory-category' )) {
		echo fw()->extensions->get( 'woffice-directory' )->woffice_directory_map_js_main();
	}

	if (is_singular("directory")) {
		echo fw()->extensions->get( 'woffice-directory' )->woffice_directory_map_js_single();
	}
}
add_action('wp_footer', 'woffice_directory_js_load');