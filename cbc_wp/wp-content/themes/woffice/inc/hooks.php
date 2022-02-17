<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * This files contains custom hooks/actions/filters used by Woffice
 * You can find many of them organized within woffice/inc/classes/
 */

if(!function_exists('woffice_remove_fa_scripts')) {
	/**
	 * Remove any Font Awesome loading call so we use the Woffice one only
	 * This is to avoid conflicts between the version 4.7.0 and 5.x (loaded by Woffice)
	 *
	 * @since 2.8.1
	 */
	function woffice_remove_fa_scripts()
	{
		wp_dequeue_style('​fontawesome');
		wp_dequeue_style('font-awesome-original');
		wp_dequeue_style('uagb-fontawesome-css');
	}
}
add_action('wp_print_styles', 'woffice_remove_fa_scripts', 100);


if ( !function_exists( 'woffice_remove_default_wperp_admin_styles' ) ) {
    /**
     * Remove default WP ERP jQuery ui styles as it conflicts with some theme admin components (Unyson, etc)
     * @param string $hook
     */
    function woffice_remove_default_wperp_admin_styles( $hook ) {

        if ( $hook !== 'appearance_page_fw-settings' && $hook !== 'toplevel_page_fw-extensions' ) {
            return;
        }

        wp_dequeue_style( 'jquery-ui' );
    }
}
add_action( 'admin_enqueue_scripts', 'woffice_remove_default_wperp_admin_styles' );

if(!function_exists('woffice_allfiles')) {
    /**
     * All File Shortcode to exclude portfolio's NEW category
     * @return mixed|void
     */
    function woffice_allfiles()
    {
        if (!class_exists('multiverso_mv_category_files'))
            return;
        // Include allfiles.php template
        return include(get_template_directory() . '/inc/allfiles.php');
    }
}

if(function_exists('woffice_create_shortcode')){    
    woffice_create_shortcode('woffice_allfiles','woffice_allfiles');
}
if(!function_exists('woffice_fix_admin_buddypress_style')) {
    /**
     * BuddyPress Admin CSS patch
     */
    function woffice_fix_admin_buddypress_style()
    {
        echo '<style>
      .bp-profile-field .datebox > label:first-child {width: 200px;}
      .bp-profile-field .datebox > label{width: auto;}
      .bp-profile-field select{margin-right:20px}
     </style>';
    }
}
add_action('admin_print_scripts', 'woffice_fix_admin_buddypress_style');

if(!function_exists('woffice_trashed_post_handler')) {
    /**
     * Redirect to the home page after a post is deleted
     */
	function woffice_trashed_post_handler() {
		if ( ! is_admin() && ( ( array_key_exists( 'deleted', $_GET ) && $_GET['deleted'] == '1' ) || ( array_key_exists( 'trashed', $_GET ) && $_GET['trashed'] == '1' ) ) ) {
			wp_redirect( home_url() );
			exit;
		}
	}
}
add_action( 'parse_request', 'woffice_trashed_post_handler' );

if(!function_exists('woffice_display_feeds_error')) {
    /**
     * Keep the feed private
     */
    function woffice_display_feeds_error() {
        $feeds_private = woffice_get_settings_option('feeds_private');
        if($feeds_private) {
            wp_die( __( 'No feed available, please visit our home page!', 'woffice' ) );
        }
    }
}
add_action('do_feed', 'woffice_display_feeds_error', 1);
add_action('do_feed_rdf', 'woffice_display_feeds_error', 1);
add_action('do_feed_rss', 'woffice_display_feeds_error', 1);
add_action('do_feed_rss2', 'woffice_display_feeds_error', 1);
add_action('do_feed_atom', 'woffice_display_feeds_error', 1);
add_action('do_feed_rss2_comments', 'woffice_display_feeds_error', 1);
add_action('do_feed_atom_comments', 'woffice_display_feeds_error', 1);

if(!function_exists('woffice_display_feeds_error_2')) {
    /**
     * Keep BuddyPress feed private
     *
     * @return void
     */
    function woffice_display_feeds_error_2() {
        $feeds_private = woffice_get_settings_option('feeds_private');
        if($feeds_private) {
            echo '>';
            echo '</rss>';
            die();
        }
    }
}
add_action('bp_activity_sitewide_feed', 'woffice_display_feeds_error_2', 1);
add_action('bp_activity_personal_feed', 'woffice_display_feeds_error_2', 1 );
add_action('bp_activity_friends_feed', 'woffice_display_feeds_error_2', 1 );
add_action('bp_activity_my_groups_feed', 'woffice_display_feeds_error_2', 1 );
add_action('bp_activity_mentions_feed', 'woffice_display_feeds_error_2', 1 );
add_action('bp_activity_favorites_feed', 'woffice_display_feeds_error_2', 1 );
add_action('groups_group_feed', 'woffice_display_feeds_error_2', 1 );


if(!function_exists('woffice_add_bp_mentions_on_comments_area')) {
	/**
	 * Enable BuddyPress mentions on every comment area
	 *
	 * @param $field
	 * @return mixed
	 */
	function woffice_add_bp_mentions_on_comments_area( $field ) {
		return str_replace( 'textarea', 'textarea class="bp-suggestions"', $field );
	}
}
add_filter( 'comment_form_field_comment', 'woffice_add_bp_mentions_on_comments_area' );

if( !function_exists('woffice_woocommerce_prevent_admin_accesss')) {
    /**
     * Disable WooCommerce to prevent access from the dashboard
     *
     * @return boolean
     */
    function woffice_woocommerce_prevent_admin_accesss() {

        return false;

    }
}
add_filter( 'woocommerce_prevent_admin_access', '__return_false' );

if( !function_exists('woffice_remove_restricted_posts_from_query')) {
	/**
	 * Remove by default all restricted blog posts from all the query call, if the quey is not relative to a single post
	 *
	 * @param WP_Query $query
	 *
	 * @return mixed
	 */
	function woffice_remove_restricted_posts_from_query( $query ) {

		if (
			current_user_can( 'manage_options' )
			|| $query->is_single
			|| $query->is_page
			|| ( isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] != 'post' )
			|| ( isset( $query->query_vars['woffice_ignore_posts_permission'] ) && $query->query_vars['woffice_ignore_posts_permission'] )
		) {
			return $query;
		}

		$new_args                                    = $query->query_vars;
		$new_args['woffice_ignore_posts_permission'] = true;
		$my_query                                    = new WP_Query( $new_args );

		$excluded_posts = array();

		while ( $my_query->have_posts() ) : $my_query->the_post();
			if ( ! woffice_is_user_allowed( get_the_ID() ) ) {
				array_push( $excluded_posts, get_the_ID() );
			}
		endwhile;

		wp_reset_postdata();

		//If not exclude it from the real query call
		$query->set( 'post__not_in', $excluded_posts );

		return $query;

	}
}
add_filter('pre_get_posts', 'woffice_remove_restricted_posts_from_query');

if ( !function_exists( 'woffice_set_posts_per_page' ) ) {
	/**
	 * Set the posts per page of posts
	 *
	 * @param WP_Query $query
	 * @return mixed
	 */
	function woffice_set_posts_per_page( $query ) {

		if(is_admin()) return $query;

		if ( $query->is_main_query() && ( $query->is_home() || $query->is_tag() || $query->is_category() || $query->is_archive()) ) {

			$posts_per_page = woffice_get_settings_option('blog_number');
			$query->set( 'posts_per_page', (int) $posts_per_page );
		}

		return $query;
	}
}
add_filter('pre_get_posts', 'woffice_set_posts_per_page');

if( !function_exists( 'woffice_override_embed_site_icon' ) ) {
	/**
	 * Override the default embed site title in order to use the icon of Woffice theme
	 *
	 * @param $site_title
	 * @return string
	 */
	function woffice_override_embed_site_icon( $site_title ) {

		$site_title = sprintf(
			'<a href="%s" target="_top"><span>%s</span></a>',
			esc_url( home_url() ),
			esc_html( get_bloginfo( 'name' ) )
		);

		return '<div class="wp-embed-site-title">' . $site_title . '</div>';

	}
}
add_filter( 'embed_site_title_html', 'woffice_override_embed_site_icon' );

if( !function_exists('woffice_load_admin_textdomain_in_front') ) {
	/**
	 * Used foremost in order to translate the roles in frontend
	 */
	function woffice_load_admin_textdomain_in_front() {
		if ( ! is_admin() ) {
			load_textdomain( 'default', WP_LANG_DIR . '/admin-' . get_locale() . '.mo' );
		}
	}
}
add_action( 'init', 'woffice_load_admin_textdomain_in_front' );

if (!function_exists('woffice_reset_extrafooter_transient')) {
	/**
	 * Refresh the transient of the extrafooter when a new user is added or when an old user is deleted
	 */
	function woffice_reset_extrafooter_transient() {

		delete_transient('woffice_extrafooter_member_ids');

	}
}
add_action( 'user_register', 'woffice_reset_extrafooter_transient');
add_action( 'delete_user', 'woffice_reset_extrafooter_transient');
add_action( 'fw_settings_form_saved', 'woffice_reset_extrafooter_transient');

if ( !function_exists( 'woffice_ajax_extrafooter_avatars' ) ) {
	/**
	 * Return to the AJAX callback the avatars to display in the extrafooter
	 */
	function woffice_ajax_extrafooter_avatars() {

        if ( !wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
            wp_die( __('Sorry! Direct Access is not allowed.', "woffice"));
        }

		woffice_extrafooter_print_avatars();

		wp_die();

	}
}
add_action( 'wp_ajax_load_extrafooter_avatars', 'woffice_ajax_extrafooter_avatars');
add_action( 'wp_ajax_nopriv_load_extrafooter_avatars', 'woffice_ajax_extrafooter_avatars');

if(!function_exists('woffice_members_suggestion_autocomplete')) {
    /**
     * AJAX handler for project member autocomplete requests.
     *
     */
    function woffice_members_suggestion_autocomplete() {

        if ( !wp_verify_nonce( $_GET['nonce'], 'ajax-nonce' ) ) {
            wp_die( __('Sorry! Direct Access is not allowed.', "woffice"));
        }

        // Fail it's a large network.
        if ( is_multisite() && wp_is_large_network( 'users' ) ) {
            wp_die( - 1 );
        }

        $term = isset( $_GET['term'] ) ? sanitize_text_field( $_GET['term'] ) : '';

        /**
         * Filter the members ids included in the members suggestion (in project assignation)
         *
         * @param array
         */
        $include = apply_filters( 'woffice_members_suggestion_include', array());

        /**
         * Filter the members ids excluded from the members suggestion (in project assignation)
         *
         * @param array
         */
        $exclude = apply_filters( 'woffice_members_suggestion_exclude', array());

        if ( ! $term ) {
            wp_die( - 1 );
        }

        $user_fields = array( 'ID' );

        //TODO remove users already added?
        $users       = new \WP_User_Query( array(
            'fields' => $user_fields,
            'search'         => "*{$term}*",
            'search_columns' => array(
                'user_login',
                'user_nicename',
                'user_email',
            ),
            'include' => $include,
            'exclude' => $exclude
        ) );

        $users_found_1 = $users->get_results();

        $users       = new \WP_User_Query( array(
            'fields' => $user_fields,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key'     => 'first_name',
                    'value'   => esc_attr( $term ),
                    'compare' => 'LIKE'
                ),
                array(
                    'key'     => 'last_name',
                    'value'   => esc_attr( $term ),
                    'compare' => 'LIKE'
                ),
            ),
            'include' => $include,
            'exclude' => $exclude

        ) );
        $users_found_2 = $users->get_results();

        $users_found_3 = array_unique( array_merge($users_found_1, $users_found_2), SORT_REGULAR );

        // If we have a filter coming
        $users_filtered = isset( $_GET['filter'] ) ? $_GET['filter'] : '';

        $users_found = array();

        if ($users_filtered !== '') {
            foreach ($users_found_3 as $user) {
                if (array_key_exists($user->ID, $users_filtered))
                    array_push($users_found, $user);
            }
        } else {
            $users_found = $users_found_3;
        }

        $matches = array();

        if ( $users_found && ! is_wp_error( $users_found ) ) {
            foreach ( $users_found as $user ) {

                if( function_exists( 'bp_is_user_active' ) && !bp_is_user_active($user->ID) )
                    continue;

                $matches[] = array(
                    'label' => woffice_get_name_to_display($user->ID),
                    'value' => $user->ID,
                );
            }
        }

        wp_die( json_encode( $matches ) );
    }
}
add_action( 'wp_ajax_woffice_members_suggestion_autocomplete', 'woffice_members_suggestion_autocomplete'  );
add_action( 'wp_ajax_nopriv_woffice_members_suggestion_autocomplete', 'woffice_members_suggestion_autocomplete'  );

/**
 * Woffice Manage signup tabs.
 *
 * Logic for allowing admins to user approval page.
 *
 */
function woffice_manage_signups_tab() {
	global $pagenow;
	$manage_signups = woffice_get_settings_option('buddy_manage_signups');

	if ( !empty($manage_signups) && !in_array(get_current_user_id(), $manage_signups) ) {
		remove_submenu_page('users.php','bp-signups');
		add_filter( "views_users", 'woffice_remove_pending_tab',99 );

		if ( 'users.php' == $pagenow && isset($_GET['page']) && 'bp-signups' == $_GET['page'] ) {
			wp_redirect(admin_url());
			exit();
		}

	}

}

/**
 * Helper function for manage signup pages.
 *
 * @param $filters.
 *
 * @return mixed.
 */
function woffice_remove_pending_tab($filters) {

	unset($filters['registered']);
	return $filters;

}

add_action('admin_menu','woffice_manage_signups_tab', 999);

// remove font-awesome dependency if fontawesome url is empty in DpProEventCalendar
if(class_exists('DpProEventCalendar_Init')){

    function woffice_dppro_dequeue_script() {
        
        global $dpProEventCalendar;
       
        if(!array_key_exists($dpProEventCalendar['fontawesome_url'],$dpProEventCalendar)) {
            wp_dequeue_script('font-awesome');
        } elseif( isset( $dpProEventCalendar['fontawesome_url'] ) && empty($dpProEventCalendar['fontawesome_url'])) {
            wp_dequeue_script('font-awesome');
        }
    }

    add_action( 'wp_footer', 'woffice_dppro_dequeue_script');
}

/**
 * Create pages for WP JOB MANAGER 
 */

if(class_exists('WP_Job_Manager')) {

    // check the hr page is exist or not
    $get_hr_page = get_option('woffice_hr_page_id') ? get_option('woffice_hr_page_id') : '';
    $locations = get_nav_menu_locations();
    $menu = wp_get_nav_menu_object($locations['primary']);
    $menu_id  = $menu ? $menu->term_id : 0;

    // page data for the HR page
    $page_data = [
        'post_status'    => 'publish',
        'post_type'      => 'page',
        'post_author'    => 1,
        'post_name'      => __('HR','woffice'),
        'post_title'     => __('HR','woffice'),
        'post_content'   => sanitize_text_field('[job_dashboard]'),
        'post_parent'    => 0,
        'comment_status' => 'closed',
    ];

    // check if option is empty 
    if (empty($get_hr_page)) {
        // create new page called HR 
        $page_id   = wp_insert_post( $page_data );
        update_post_meta($page_id,'_wp_page_template','job_manager/template-jobdashboard.php');
        update_option( 'woffice_hr_page_id', $page_id );
        update_option('job_manager_job_dashboard_page_id',$page_id);
        update_option('job_manager_submit_job_form_page_id',$page_id);
    } else {
        // check if option is not empty and page is not exist
        if( is_null(get_post($get_hr_page))){ 
            $page_id   = wp_insert_post( $page_data );
            update_post_meta($page_id,'_wp_page_template','job_manager/template-jobdashboard.php');
            update_option( 'woffice_hr_page_id', $page_id );
            update_option('job_manager_job_dashboard_page_id',$page_id);
            update_option('job_manager_submit_job_form_page_id',$page_id);
        }
    }

    if (!empty($get_hr_page)) {
        
        // get the HR page
        $page = get_post($get_hr_page) ? get_post($get_hr_page) : array(); 

        // args for menu entry
        $args =  array(
            'menu-item-title' => $page->post_title,
            'menu-item-object-id' => $page->ID,
            'menu-item-object' => 'page',
            'menu-item-status' => 'publish',
            'menu-item-type' => 'post_type',
        );
        
        $woffice_hr_menu_id = get_option( 'woffice_hr_menu_id');
        
        if(empty($woffice_hr_menu_id)) {
            // create menu item 
            $woffice_hr_menu_id = wp_update_nav_menu_item($menu_id, 0, $args);
            update_option( 'woffice_hr_menu_id', $woffice_hr_menu_id );
        } else {
            if( is_null(get_post($woffice_hr_menu_id))){ 
                // create menu item if HR page is exist but menu item is not exist
                $woffice_hr_menu_id = wp_update_nav_menu_item($menu_id, 0, $args);
                update_option( 'woffice_hr_menu_id', $woffice_hr_menu_id );
            }
        }
    }

    // check the job page is exist or not
    $get_job_page = get_option('woffice_job_page_id') ? get_option('woffice_job_page_id') : '';

    // page data for the job page
    $job_page_data = [
        'post_status'    => 'publish',
        'post_type'      => 'page',
        'post_author'    => 1,
        'post_name'      => __('Open Positions','woffice'),
        'post_title'     => __('Open Positions','woffice'),
        'post_content'   => sanitize_text_field('[jobs]'),
        'post_parent'    => 0,
        'comment_status' => 'closed',
    ];

    // check if option is empty 
    if (empty($get_job_page)) {
        // create new page called submit a job 
        $job_page_id   = wp_insert_post( $job_page_data );
        update_option( 'woffice_job_page_id', $job_page_id );
    } else {
        // check if option is not empty and page is not exist
        if( is_null(get_post($get_hr_page))){ 
            $job_page_id   = wp_insert_post( $job_page_data );
            update_option( 'woffice_job_page_id', $job_page_id );
        }
    }

    if (!empty($get_job_page)) {
    
        // get the submit a job page
        $job_page = get_post($get_job_page); 

        // args for menu entry
        $job_args =  array(
            'menu-item-title' => $job_page->post_title,
            'menu-item-object-id' => $job_page->ID,
            'menu-item-object' => 'page',
            'menu-item-status' => 'publish',
            'menu-item-type' => 'post_type',
        );
        
        $woffice_job_menu_id = get_option( 'woffice_job_menu_id');
        
        if(empty($woffice_job_menu_id)) {
            // create menu item 
            $woffice_job_menu_id = wp_update_nav_menu_item($menu_id, 0, $job_args);
            update_option( 'woffice_job_menu_id', $woffice_job_menu_id );
        } else {
            if( is_null(get_post($woffice_job_menu_id))){ 
                // create menu item if submit a job page is exist but menu item is not exist
                $woffice_job_menu_id = wp_update_nav_menu_item($menu_id, 0, $job_args);
                update_option( 'woffice_job_menu_id', $woffice_job_menu_id );
            }
        }
    }

} else {
    // Delete the entry of HR page and menu item if WP JOB MANAGER plugin is not active
    $get_hr_page = get_option('woffice_hr_page_id');
    $woffice_hr_menu_id = get_option( 'woffice_hr_menu_id');
    $get_job_page = get_option('woffice_job_page_id');
    $woffice_job_menu_id = get_option( 'woffice_job_menu_id');
    
    if(!empty($get_hr_page)) {
        wp_delete_post( $get_hr_page, true );
        delete_option('woffice_hr_page_id');
    }

    if(!empty($woffice_hr_menu_id)) {
        wp_delete_post( $woffice_hr_menu_id, true );
        delete_option('woffice_hr_menu_id');
    }

    if(!empty($woffice_job_menu_id)) {
        wp_delete_post( $woffice_job_menu_id, true );
        delete_option('woffice_job_menu_id');
    }

    if(!empty($get_job_page)) {
        wp_delete_post( $get_job_page, true );
        delete_option('woffice_job_page_id');
    }
}

function woffice_add_todo_tabs() {

    $html = '';
    if(class_exists('WOKSS_KANBAN') || class_exists('Woffice_Timeline')) {

        $html .= "<div class='list-styled pl-0 woffice-todo-extratabs'>";
        $html .= sprintf('<span class="list-inline-item pl-1 todo-extratabs-item extratabs-item-active" data-extratab="todo"><a href="">%1$s</a></span>',__('TODO','woffice'));
        
        if(class_exists('WOKSS_KANBAN')) {
            $html .= sprintf('<span class="list-inline-item pl-1 todo-extratabs-item" data-extratab="kanban"><a href="">%1$s</a></span>',__('Kanban','woffice'));
        }
        
        if(class_exists('Woffice_Timeline')) {

            $html .= sprintf('<span class="list-inline-item pl-1 todo-extratabs-item" data-extratab="timeline"><a href="">%1$s</a></span>',__('Timeline','woffice'));
        }

        $html .= "</div>";
    }

    woffice_echo_output($html);
}

function woffice_projects_kaban_tab() {
    woffice_echo_output(do_shortcode('[woffice_kanban id='.get_the_ID().']'));
}

function woffice_projects_timeline_tab() {
    woffice_echo_output(do_shortcode('[woffice_timeline id='.get_the_ID().']'));
}