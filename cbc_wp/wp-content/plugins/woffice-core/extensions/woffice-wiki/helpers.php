<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Helper to check if the extension is enabled
 */
function woffice_wiki_extension_on(){
    return;
}

if(!function_exists('woffice_wiki_have_comments')) {
    /**
     * Simple function to check whether the wiki must have comments or not
     */
    function woffice_wiki_have_comments() {
        /**
         * Filter `woffice_wiki_display_comments`
         *
         * @param bool
         */
        return apply_filters('woffice_wiki_display_comments', true);
    }
}

if(!function_exists('woffice_is_user_allowed_wiki')) {
	/**
	 * Check if the current user can see the wiki article
	 *
	 * @param $post_id
	 *
	 * @return boolean
	 */
	function woffice_is_user_allowed_wiki( $post_id = null ) {

		if ( is_null( $post_id ) ) {
			global $post;
			$post_id = $post->ID;
		} else {
			$post = get_post( $post_id );
		}

		// Check if Woffice permissions settings are overrited by meta caps
		$use_meta_caps = woffice_check_meta_caps( 'wiki' );

		if ( is_user_logged_in() && ! current_user_can( 'woffice_read_wikies' ) && ! current_user_can( 'woffice_read_private_wikies' ) ) {
            return false;
		}

		if ( $use_meta_caps ) {


			/* We get the current user data */
			$user = wp_get_current_user();

			$is_allowed = false;
			if ( $post->post_status == 'publish' ) {
				$is_allowed = true;
			} elseif ( $post->post_status == 'draft' ) {
				if ( $post->post_author == $user->ID && current_user_can( 'woffice_edit_wikies' ) ) {
					$is_allowed = true;
				} elseif ( $post->post_author != $user->ID && current_user_can( 'woffice_edit_others_wikies' ) ) {
					$is_allowed = true;
				}

            }
            
			return $is_allowed;

		} else {

			/* Fetch data from options both settings & post options */
			$exclude_roles = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option( $post_id, 'exclude_roles' ) : '';

			$is_allowed = true;

			/* We check now if the role is excluded */
			$role_allowed = true;
			if ( ! empty( $exclude_roles ) ) :
				$user = wp_get_current_user();
				/* Thanks to BuddyPress we only keep the main role */
				$the_user_role = ( is_array( $user->roles ) ) ? $user->roles : array( $user->roles );

				/* We check if it's in the array, OR if it's the administrator  */
				if ( array_intersect( $the_user_role, $exclude_roles ) && $the_user_role != "administrator" ) {
					$role_allowed = false;
				}
			endif;

			/*We check the results*/
			if ( $role_allowed == false ) {
				$is_allowed = false;
            }
            
			return $is_allowed;
		}

	}
}
/**
 * Create the Like Button HTML
 * @param $post_ID
 * @return string
 */
function woffice_get_wiki_like_html($post_ID) {
    $enable_wiki_like = woffice_get_settings_option('enable_wiki_like');
    if($enable_wiki_like != 'yep')
        return '';

	$ext_instance = fw()->extensions->get( 'woffice-wiki' );
	
	$vote_count = get_post_meta($post_ID, "votes_count", true);
	$vote_count_disp = (empty($vote_count)) ? '0' : $vote_count;
	
	$html='<div class="wiki-like-container">';
		$html .='<p class="wiki-like">';
		    if($ext_instance->woffice_user_has_already_voted($post_ID)) {
		        $html .= ' <span title="'.__('I like this article', 'woffice').'" class="like alreadyvoted">
		        	<i class="fa fa-thumbs-up"></i>
		        </span>';
		    } else { 
		        $html .= '<a href="javascript:void(0)" data-post_id="'.$post_ID.'">
	                <i class="fa fa-thumbs-up"></i>
	            </a>';
		    }
		    $html .='<span class="count">'.$vote_count_disp.'</span>';
		$html .='</p>';
	$html .='</div>';
	
	return $html;
	
}

/**
 * Get likes number
 * @param $post_ID
 * @return null, string
 */
function woffice_get_wiki_likes($post_ID) {
    $enable_wiki_like = woffice_get_settings_option('enable_wiki_like');
    if($enable_wiki_like != 'yep')
        return '';

	/* We get the data from the post meta */
	$vote_count = get_post_meta($post_ID, "votes_count", true);
	$vote_count_disp = (empty($vote_count)) ? '0' : $vote_count; 

	/* We only return something if there is more than one like */
	
	if ($vote_count_disp > 0) {
		
		$html='<span class="ml-2 count badge badge-primary badge-pill"><i class="fa fa-thumbs-up"></i> '.$vote_count_disp.'</span>';
		
		return $html;
		
	}
	else {
		return;
	}

}

/**
 * Sort by like
 */
function woffice_wiki_sort_by_like() {
    /*We check first to display it or not */
    $wiki_sortbylike = woffice_get_settings_option('wiki_sortbylike');
    if ($wiki_sortbylike == "yep") {

        global $wp;
        $current_url = home_url(add_query_arg(array(),$wp->request));
        echo '<p class="text-center">';
        echo '<a id="woffice-members-filter-btn" class="btn btn-default mt-0" href="'.esc_url($current_url).'?sortby=like"><i class="fa fa-sort-amount-desc"></i>'.__('Sort By Likes','woffice') .'</a>';
        echo '</p>';

    }
}

/**
 * Sort by likes
 * @param $a
 * @param $b
 * @return int
 */
function woffice_sort_objects_by_likes($a, $b) {
    return ($b['likes'] > $a['likes']);
}

if(!function_exists('woffice_display_wiki_subcategories')) {
    /**
     * Display the wiki subcategories of a given category
     *
     * @param $category_id
     * @param $enable_wiki_accordion
     * @param $wiki_sortbylike
     * @return string
     */
    function woffice_display_wiki_subcategories($category_id, $enable_wiki_accordion, $wiki_sortbylike)
    {
        $return = array('html' => '', 'summed_elements' => 0, 'n_elements' => 0, 'children' => array());
        // We check for excluded categories
        $wiki_excluded_categories = woffice_get_settings_option('wiki_excluded_categories');
        /*If it's not a child only*/
        $wiki_excluded_categories_ready = (!empty($wiki_excluded_categories)) ? $wiki_excluded_categories : array();

        // Get all child categories of the current one
        $child_categories = get_categories(array(
            'type' => 'wiki',
            'taxonomy' => 'wiki-category',
            'parent' => $category_id,
            'exclude' => $wiki_excluded_categories_ready
        ));

        if (!empty($child_categories)) {
            //Foreach child category of the current one
            foreach ($child_categories as $category_child) {

                //Get the subcategories items
                $return['children'] = woffice_display_wiki_subcategories($category_child->term_id, $enable_wiki_accordion, $wiki_sortbylike);

                $wiki_termchildren = get_term_children($category_child->term_id, 'wiki-category');
                $wiki_query_childes = new WP_Query(
                    /**
                     * Filter `woffice_wiki_query_args`
                     *
                     * Let you filter the Query args that are passed for the Wiki page
                     *
                     * @param array
                     */
                    apply_filters('woffice_wiki_query_args', array(
	                    'post_type' => 'wiki',
	                    'showposts' => '-1',
	                    'orderby' => 'post_title',
	                    'order' => 'ASC',
	                    'post_status' => array( 'publish', 'draft' ),
	                    'tax_query' =>
		                    array('relation' => 'AND',
			                    array('taxonomy' => 'wiki-category',
			                          'field' => 'slug',
			                          'terms' => $category_child->slug,
			                          'operator' => 'IN'
			                    ),
			                    array('taxonomy' => 'wiki-category',
			                          'field' => 'id',
			                          'terms' => $wiki_termchildren,
			                          'operator' => 'NOT IN'
			                    ),
			                    array('taxonomy' => 'wiki-category',
			                          'field' => 'id',
			                          'terms' => $wiki_excluded_categories_ready,
			                          'operator' => 'NOT IN'
			                    )
		                    )
                    ))
                );
                $wiki_array = array();
                $html = '';

	            // Avoid to sum all subclasses consequentially
	            $return['n_elements'] = 0;

                //Get all wiki elements of the current category and store in a variable
                while ($wiki_query_childes->have_posts()) : $wiki_query_childes->the_post();

                    /*WE DISPLAY IT*/
                    if (woffice_is_user_allowed_wiki()) {
                        $return['n_elements']++;
                        $likes = woffice_get_wiki_likes(get_the_id());
                        $likes_display = (!empty($likes)) ? $likes : '';
                        $featured_wiki = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option(get_the_ID(), 'featured_wiki') : '';
                        $featured_wiki_class = ($featured_wiki) ? 'featured' : '';
                        if ($wiki_sortbylike) {
                            $like = woffice_get_string_between($likes_display, '</i> ', '</span>');
                            array_push($wiki_array, array(
                                    'string' => '<li class="is-'.get_post_status().' pt-2"><a href="' . get_the_permalink() . '" rel="bookmark" data-post-id="'.get_the_ID().'" class="' . $featured_wiki_class . ' text-body">' . get_the_title() . $likes_display . '</a></li>',
                                    'likes' => (!empty($like)) ? (int)$like : 0
                                )
                            );
                        } else {
                            $html .= '<li class="is-'.get_post_status().' pt-2">
                                <a href="' . get_the_permalink() . '" rel="bookmark" data-post-id="'.get_the_ID().'" class="' . $featured_wiki_class . ' text-body">' . get_the_title() . $likes_display . '</a>
                            </li>';
                        }

                    }

                endwhile;


                $return['summed_elements'] = $return['n_elements'] + $return['children']['summed_elements'];

                if ($enable_wiki_accordion) {

                    $return['html'] .= apply_filters('woffice_wiki_subcategory_title', '<li class="sub-category text-body"><span data-toggle="collapse" data-target="#' . $category_child->slug . '" expanded="false" aria-controls="' . $category_child->slug . '">' . esc_html($category_child->name) . ' (' . $return['summed_elements'] . ')</span>', $category_child->name, $return['summed_elements'], $category_child->slug);
                    $return['html'] .= '<ul id="' . $category_child->slug . '" class="list-styled list-wiki collapse" aria-expanded="false">';
                } else {
                    $return['html'] .= '<li class="sub-category">
                        <span>' . esc_html($category_child->name) . ' (<span class="wiki-category-count">' . $return['summed_elements'] . '</span>)</span>
                        <ul class="list-styled list-wiki ">';
                }

                //Save the subcategories that have to be returned
                if($return['children']['n_elements'] > 0)
                    $return['html'] .= $return['children']['html'];

                //Save the current wiki articles that have to be returned
                $return['html'] .= $html;

                //Sort the wiki articles if it is requested
                if ($wiki_sortbylike) {
                    usort($wiki_array, 'woffice_sort_objects_by_likes');
                    foreach ($wiki_array as $wiki) {
                        $return['html'] .= $wiki['string'];
                    }
                }

                wp_reset_postdata();


                $return['html'] .= '</ul></li>';

            }

        }

        return $return;
    }
}

/**
 * Display the actions buttons on wiki directory page
 */
function woffice_wiki_display_actions_buttons() {
	$wiki_create          = woffice_get_settings_option( 'wiki_create' );
	$woffice_role_allowed = Woffice_Frontend::role_allowed( $wiki_create, 'wiki' );

	if ( $woffice_role_allowed ): ?>
        <hr>
        <div class="text-center" id="wiki-bottom">
            <?php

                /**
                * Filter the text of the button "New Wiki Article"
                *
                * @param string
                */
                $new_wiki_article_button_text = apply_filters( 'woffice_new_wiki_article_button_text', __( "New Wiki Article", "woffice" ) );

                echo '<a href="#" class="btn btn-default frontend-wrapper__toggle" data-action="display" id="show-wiki-create"><i class="fa fa-plus-square"></i> ' . $new_wiki_article_button_text . '</a>';
             ?>

        </div>
    <?php
	endif;

}