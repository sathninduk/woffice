<?php if ( ! defined( 'FW' ) ) {
    die( 'Forbidden' );
}

if(!function_exists('woffice_todos_fetch')) {
    /**
     * We fetch the To-Dos using AJAX
     */
    function woffice_todos_fetch()
    {

        if(!check_ajax_referer('woffice_todos') || !isset($_POST['id'])) {
            echo json_encode(array('status' => 'fail'));
            die();
        }

        // We get the ID from the current Project post
        $the_ID = $_POST['id'];

        // We get the todos
        $project_todo_lists = woffice_get_post_option( $the_ID, 'project_todo_lists', array());
	    $project_todo_lists = ( is_array( $project_todo_lists ) ) ? $project_todo_lists : array();

        // More check to add some extra data
        $post = get_post($the_ID);
        $allowed_modify = woffice_current_user_can_edit_project($post->ID);

        // We format our data
        foreach ($project_todo_lists as $key=>$todo) {
            $project_todo_lists[$key]['_can_check'] = woffice_current_user_can_check_task( $todo, $post, $allowed_modify);
            $project_todo_lists[$key]['_has_user_domain'] = function_exists('bp_core_get_user_domain');
            $project_todo_lists[$key]['_display_note'] = false;
            $project_todo_lists[$key]['_display_edit'] = false;
	        $project_todo_lists[$key]['_completion_date'] = (isset($todo['completion_date'])) ? $todo['completion_date'] : 0;
            if(isset($todo['date'])) {
                $project_todo_lists[$key]['_timestamp_date'] = strtotime($todo['date']);
                $project_todo_lists[$key]['_formatted_date'] = date_i18n(get_option('date_format'), strtotime($todo['date']));
            }
            if(!empty($todo['assigned']) && $todo['assigned'] != array('nope')) {
                $todo['assigned'] = (is_array($todo['assigned'])) ? $todo['assigned'] : explode(',',$todo['assigned']);
                $new_assigned = array();
                foreach($todo['assigned'] as $key2=>$assigned) {
                    $new_assigned[$key2]['_id'] = $assigned;
                    $new_assigned[$key2]['_avatar'] = get_avatar($assigned);
                    if (function_exists('bp_members_get_user_nicename')) {
                        $new_assigned[$key2]['_name'] = bp_members_get_user_nicename($assigned);
                    }
                    if (function_exists('bp_core_get_user_domain')) {
                        $new_assigned[$key2]['_profile_url'] = bp_core_get_user_domain($assigned);
                    }
                }
                $project_todo_lists[$key]['assigned'] = $new_assigned;
            }
        }

        // We return them through AJAX
        echo json_encode(array(
            'status' => 'success',
            'todos' => $project_todo_lists
        ));

        die();

    }
}
add_action('wp_ajax_nopriv_woffice_todos_fetch', 'woffice_todos_fetch');
add_action('wp_ajax_woffice_todos_fetch', 'woffice_todos_fetch');

if(!function_exists('woffice_todos_update')) {
    /**
     * We update the To-Dos using AJAX
     */
    function woffice_todos_update()
    {

        if(!check_ajax_referer('woffice_todos') || !isset($_POST['id']) || !isset($_POST['type'])) {
            echo json_encode(array('status' => 'fail'));
            die();
        }

        // We get the ID from the current Project post
        $id = intval($_POST['id']);
        $project_sync = fw_get_db_post_option($id, 'project_calendar');
        // We get the type of update : add / delete / check / order / edit
        $type = $_POST['type'];

        // We get the todos
        $todos = (!isset($_POST['todos']) || empty($_POST['todos'])) ? array() : $_POST['todos'];
        // Deleted ToDo ids
        $todo_ids = isset($_POST['deleted']) ? $_POST['deleted'] : array();
        $excluded_users_keys = array('-1', -1, 'NaN', 'No One');
        $current_user = get_current_user_id();
        // We sanitize our data
        foreach ($todos as $key => $todo) {
            foreach ($todo as $key2 => &$val) {

                // We re-format our assigned array
                $val = ( $val == 'false' ) ? false : $val;
                $val = ( $val == 'true' ) ? true : $val;
               

                if( !isset($todo['todo_comments'])) {
                    $todo['todo_comments'] = array();
                }

                /* Add new comment to array */
                if( $key2 == 'comment') {
                    $add_comment = array();
                    $now = new DateTime();
                    $zone = wp_timezone_string();
                    $time_zone  = date_default_timezone_get();
                    $now->setTimeZone(new DateTimezone($time_zone));
                   
                    $add_comment['comment'] = $todo['comment'];
                    $add_comment['user'] = $current_user;
                    $add_comment['date']= $now->format('Y-m-d H:i:s');
                    $todo['todo_comments'][] = $add_comment;
                    unset($todo[$key2]);
                }

                if($key2 == 'assigned' && is_array($todo['assigned'])) {

                    $new_assigned = array();

                    foreach ($todo['assigned'] as $assigned) {

                        /*
                         * Each assigned is either an array if that's an old task:
                         * [6] => Array
                         *   (
                         *       [_id] => ...
                         *       [_avatar] => ....
                         *       [_profile_url] => ...
                         *   )
                         * OR if it's a new task OR an edit
                         * [6] => 7 // and integer sent by the select form
                         */
                        if (is_array($assigned) && !in_array($assigned['_id'], $excluded_users_keys)) {
                            $new_assigned[] = $assigned['_id'];
                        } elseif(!in_array($assigned,$excluded_users_keys)) {
                            $new_assigned[] = $assigned;
                        }

                    }

                    if (isset($todo['_is_new'])) {
                        woffice_projects_new_task_actions($id, $todo);
                    }

                    // We assign the users to the saved to-do
                    $todo['assigned'] = $new_assigned;

                }
    
                if (isset($todo['_is_edited']) || isset($todo['_is_new'])) {
                    $todo['eventable'] = true;
                }
                // We have to save task id since we are creating task based event
                // Task id = post_id '--' random task id
                if ($key2 === '_id' && sizeof(explode('--', $val)) === 1) {
                    $val = $id . '--'. $val;
                }
    
                
                // We remove all the information related to the view, starting by "_"
                if($key2 !== '_id' && substr($key2, 0, 1) == '_')
                    unset($todo[$key2]);

            }

	        if ( $todo['done'] == 'true' && empty( $todo['completion_date'] ) ) {

		        $todo['completion_date'] = time();

	        } else if ( empty($todo['done']) ) {

		        $todo['completion_date'] = 0;

	        }
    
            if ($project_sync === true && isset($todo['eventable'], $todo['date'])) {
                /**
                 * Whenever single todo updated/created
                 *
                 * @param string $type
                 * @param array $todo
                 * @param int $id
                 */
                do_action('woffice_single_todo_update', $type, $todo, $id);
            }
    
            if (isset($todo['eventable'])) {
                unset($todo['eventable']);
            }
            
            $cleaned_todos[$key] = $todo;
        }
        
        if ($project_sync === true && sizeof($todo_ids) > 0) {
            /**
             * Whenever todos deleted
             *
             * @param array $todo_ids
             * @param int $id
             */
            do_action('woffice_todos_deleted', $todo_ids, $id);
        }
        
        // We get our extension instance
        $ext_instance = fw()->extensions->get('woffice-projects');

        // We update the meta
        $projects_assigned_email = woffice_get_settings_option('projects_assigned_email');

        if ($type == 'add' && $projects_assigned_email == "yep") {
            // We send email if needed
            $new_todos_email_checked = $ext_instance->woffice_projects_assigned_email($id, $cleaned_todos);

            // We update the meta finally
            $updated = $ext_instance->woffice_projects_update_postmeta($id, $new_todos_email_checked);
        } else {
            // Otherwise we just update the meta
            $updated = $ext_instance->woffice_projects_update_postmeta($id, $cleaned_todos);
        }

        // In case of an issue
        if($updated == false) {
            echo json_encode(array('status' => 'fail'));
            die();
        }

        /**
         * Whenever the todos are updated
         *
         * @param string $type
         * @param array $cleaned_todos
         * @param int $id
         */
        do_action('woffice_todo_update', $type, $cleaned_todos, $id);


        // We save the project
        $post = get_post( $id );
        do_action('save_post', $id, $post, true);

        // Update the completion date of the project
	    woffice_project_set_project_completion_date_on_update($id);

        // We return a success to let our user know
        echo json_encode(array(
            'status' => 'success'
        ));

        die();

    }
}
add_action('wp_ajax_nopriv_woffice_todos_update', 'woffice_todos_update');
add_action('wp_ajax_woffice_todos_update', 'woffice_todos_update');

if(!function_exists('woffice_project_assigned_user')) {
    /**
     * Send email to an user if he's assigned to an user
     *
     * @param $post_id int
     * @param $post WP_Post
     */
    function woffice_project_assigned_user($post_id, $post)
    {
        if (empty($post)) {
	        return;
        }

        $ext_instance = fw()->extensions->get('woffice-projects');

        if ($post->post_type != 'project') {
            return;
        }

        // If this is just a revision, don't send the email.
        if (wp_is_post_revision($post_id)) {
            return;
        }

        // Only if the option is turned on
        $projects_assigned_email = woffice_get_settings_option('projects_assigned_email');
        if ($projects_assigned_email == "yep") {

            // We get all the todos
            $project_todo_lists = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post_id, 'project_todo_lists') : '';
            if (!empty($project_todo_lists)) {

                // We send email if needed
	            $project_todo_lists = $ext_instance->woffice_projects_assigned_email($post_id, $project_todo_lists);

                // We save the new data in the postmeta
                $ext_instance->woffice_projects_update_postmeta($post_id, $project_todo_lists);
            }

        }

    }
}
// Deactivated as WOffice 2.8.3.3 - No idea why we have this, BUT it's causing a sync issue on the todos when 'projects_assigned_email' is enavled
// add_action('save_post','woffice_project_assigned_user', 100, 3 );

if(!function_exists('woffice_project_sync_events')) {
    /**
     * ADD Project to the calendar
     *
     * @param $post_id int
     * @param $post WP_Post
     */
    function woffice_project_sync_events($post_id, $post) {

        if (defined('DOING_AJAX') && DOING_AJAX) 
            return;

        // We only process if it's a project :
        $slug = "project";
        if ($post->post_type != $slug)
            return;

        // If this is just a revision, don't go further.
        if (wp_is_post_revision($post_id)) {
            return;
        }

        // We check if the option is turned on
        $project_calendar = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post_id, 'project_calendar') : '';
        
        /**
         * If the calendar sync option is turned on we proceed with the event post creation
         * compatible with EventOn and DP Pro Event Calendar
         */  
        if ($project_calendar) {
            
            global $wpdb;

            if (defined('DP_PRO_EVENT_CALENDAR_VER')) {
                $project_calendar_choice = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post_id, 'project_calendar_choice') : '';
            }

            // We get the dates first
            $project_date_start = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post_id, 'project_date_start') : '';
            $project_date_end = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post_id, 'project_date_end') : '';

            // If not two date set we exit now
            if (empty($project_date_end) || empty($project_date_start))
                return;

            // Unix times
            $begin = strtotime($project_date_start);
            $end = strtotime($project_date_end);
            
            // We get the title
            $title = get_the_title($post_id);

            // We don't set the content for now
            //$content = get_the_excerpt($post_id);

            // We get the project's URL
            $url = get_permalink($post_id);

            // We get the project's color from the Theme Settings
            $color_colored = woffice_get_settings_option('color_colored');

            // We get the project's members
            $project_members = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post_id, 'project_members') : '';

            // We check if the event already exists
            if (class_exists('EventON'))
                $query = $wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE post_title = %s AND post_type = 'ajde_events'", $title);

            if (defined('DP_PRO_EVENT_CALENDAR_VER'))
                $query = $wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE post_title = %s AND post_type = 'pec-events'", $title);

            $check_exists = $wpdb->get_results($query);

            // If it does exist we exit
            if($check_exists)
                return;

             // We set the post information for the event post creation
             $post_information = array(
                'post_title' => wp_strip_all_tags($title),
                'post_status' => 'publish',
                'post_type' => (class_exists('EventON')) ? 'ajde_events' : 'pec-events',
            );

            // If the project has members (private) and it's not EventON we don't create it as we won't guarantee privacy
            if(!empty($project_members) && defined( 'DP_PRO_EVENT_CALENDAR_VER' )) {
                $calendar_event_id = 0;
            } else {
                $calendar_event_id = wp_insert_post($post_information);
            }

            if ($calendar_event_id != 0) {
                
                // We add all the additional information for the EventOn posts
                if (class_exists('EventON')) {

                    //We add the post meta - http://www.myeventon.com/documentation/event-post-meta-variables/
                    add_post_meta($calendar_event_id, 'evcal_srow', $begin);
                    add_post_meta($calendar_event_id, 'evcal_erow', $end);
                    add_post_meta($calendar_event_id, 'evcal_event_color', $color_colored);
                    add_post_meta($calendar_event_id, 'evcal_allday', 'yes');
                    add_post_meta($calendar_event_id, 'evcal_lmlink', $url);

                    //We add the taxonomy
                    $eventON_category_object = get_term_by('slug', 'Projects', 'event_type');
                    if ($eventON_category_object != false) {
                        $value_set = wp_set_post_terms($calendar_event_id, array($eventON_category_object->term_id), 'event_type');
                    }

                    /* We add the users */
                    if (!empty($project_members)) {
                        $tagged = wp_set_object_terms($calendar_event_id, $project_members, 'event_users');
                    }
                }

                // We add all the information for the DP Pro Event Calendar Posts
                if (defined('DP_PRO_EVENT_CALENDAR_VER')) {

                    // We add the taxonomy
                    //$dp_event_category_object = get_term_by('slug', 'Projects', 'pec_events_category');
                    $dp_event_category_object = wp_get_object_terms($post_id, 'project-category');
                    if (!empty($dp_event_category_object)) {
                        //$value_set = wp_set_post_terms($calendar_event_id, array($dp_event_category_object->term_id), 'pec_events_category');
                        wp_set_object_terms($calendar_event_id, $dp_event_category_object->term_id, 'pec_events_category');
                    }

                    // We add the post meta
                    add_post_meta($calendar_event_id, 'pec_date', date('Y-m-d',$begin));
                    add_post_meta($calendar_event_id, 'pec_end_date', date('Y-m-d',$end));
                    add_post_meta($calendar_event_id, 'pec_id_calendar', $project_calendar_choice );
                    add_post_meta($calendar_event_id, 'pec_link', $url );
                    add_post_meta($calendar_event_id, 'pec_use_link', true );
                }
            }
        /**
         * If the calendar sync is not activated we check for an event with the same title
         * as project and delete it.
         */    
        } else {

            global $wpdb;
            $title = get_the_title($post_id);

            // We query the database for the EventOn posts
            if (class_exists('EventON')) {
                $query = $wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE post_title = %s AND post_type = 'ajde_events'", $title);
            }

            // We query the database for the DP Pro Event Calendar posts
            if (defined('DP_PRO_EVENT_CALENDAR_VER')) {
                $query = $wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE post_title = %s AND post_type = 'pec-events'", $title);
            }

            /* We check if the calendar event already exists */
	        if (!isset($query)) {
	        	return;
	        }

            $check_exists = $wpdb->get_results($query);

            /*If exist delete it*/
            if ($check_exists) {
                wp_delete_post($check_exists[0]->ID);
            }
        }
    }
}
add_action('save_post','woffice_project_sync_events', 100, 3 );

if( !function_exists('woffice_project_set_todo_completion_date_on_update')) {
	/**
	 * Set the completion date of tasks when they are edited by backend
	 *
	 * The field saved corresponds to the timestamp of the moment when the tasks is checked, if the task is unchecked,
	 * it will be set again to 0
	 *
	 * @param int $post_id
	 */
	function woffice_project_set_todo_completion_date_on_update( $post_id ) {

		if (defined('DOING_AJAX') && DOING_AJAX || !is_admin())
			return;

		$slug = "project";
		$post = get_post( $post_id );
		if ( empty( $post ) || $post->post_type != $slug ) {
			return;
		}

		$todos = ( ! isset( $_POST['fw_options']['project_todo_lists'] ) || empty( $_POST['fw_options']['project_todo_lists'] ) ) ? array() : $_POST['fw_options']['project_todo_lists'];
		$todos = ( is_array( $todos ) ) ? $todos : array();
		$new_todos = array();

		// For each to-do saved
		foreach ( $todos as $todo ) {

			// If it is completed and there isn't a completion date already saved, then save it
			if ( $todo['done'] == 'true' && empty( $todo['completion_date'] ) ) {

				$todo['completion_date'] = time();

			//If it isn't completed, then force the completion date to 0
			} else if ( $todo['done'] == '' ) {

				$todo['completion_date'] = 0;

			}

			$new_todos[] = $todo;

		}

		// Save all the to-do, containing the new completion dates
		fw_set_db_post_option($post_id, 'project_todo_lists', $new_todos);

	}
}
add_action('save_post', 'woffice_project_set_todo_completion_date_on_update', 120);


if( !function_exists('woffice_project_set_project_completion_date_on_update') ) {
	/**
	 * Calculate and save the completion timestamp of the project saved
	 *
	 * The field saved corresponds to the timestamp of:
	 *
	 * CASE 1 (tracked by tasks): the timestamp of the last task checked, if there is some tasks missing yet,
	 * then it is set to 0
	 *
	 * CASE 2 (tracked by time): the timestamp of the ending date of the project, if the ending date is already passed.
	 * If the ending date isn't passed yet or it isn't set at all, the field is set to 0
	 *
	 * @param int $post_ID
	 */
	function woffice_project_set_project_completion_date_on_update( $post_ID ) {

		$slug = "project";
		$post = get_post( $post_ID );
		if ( empty( $post ) || $post->post_type != $slug ) {
			return;
		}


		$progress_percentage = woffice_projects_percentage( $post_ID );



		// If the progress of the project is less than 100, then force the completion date to 0
		if ( $progress_percentage < 100 ) {

			fw_set_db_post_option( $post_ID, 'completion_date', 0 );

			return;

		}

		$progress_type = fw_get_db_post_option( $post_ID, 'project_progress' );

		if ( $progress_type == "tasks" ) {

			$project_todo_lists = fw_get_db_post_option( $post_ID, 'project_todo_lists' );

			if ( ! is_array( $project_todo_lists ) ) {
				return;
			}

			// Find the most recent completion timestamp among all the to-dos
			$completion_date = 0;
			foreach ( $project_todo_lists as $todo ) {

				if ( isset( $todo['completion_date'] ) && (int) $todo['completion_date'] > $completion_date ) {
					$completion_date = $todo['completion_date'];
				}

			}

		} else {

			$project_date_end = fw_get_db_post_option( $post_ID, 'project_date_end' );

			$completion_date = strtotime( $project_date_end );

		}

		// Save the new completion date
		fw_set_db_post_option( $post_ID, 'completion_date', $completion_date );

	}
}
add_action( 'save_post', 'woffice_project_set_project_completion_date_on_update', 140 );
add_action( 'woffice_frontend_process_completed_success', 'woffice_project_set_project_completion_date_on_update', 140 );


if(!function_exists('woffice_groups_create_new_categories')) {
    /**
     * BuddyPress create a new category for each Group
     *
     * @param $group_id int
     */
    function woffice_groups_create_new_categories($group_id)
    {

        // We fetch the option :
        $projects_groups = woffice_get_settings_option('projects_groups');

        if ($projects_groups == "yep") {
            // We get all the groups :
            if (function_exists('woffice_bp_is_active') && woffice_bp_is_active('groups')) {

                // Get all groups
                $groups = groups_get_groups(array('show_hidden' => true));

                foreach ($groups['groups'] as $group) {
                    // we check if there is already a ctageory with the group's name
                    $term = term_exists($group->name, 'project-category');
                    // If it doesn't exist then create it
                    if ($term == 0 || $term == null) {
                        wp_insert_term($group->name, 'project-category');
                    }
                }

            }
        }

    }
}
add_action('groups_group_create_complete','woffice_groups_create_new_categories');
add_action('fw_settings_form_saved','woffice_groups_create_new_categories');

if(!function_exists('woffice_groups_sync_members')) {
    /**
     * BuddyPress add all members to the project whenever a post is saved
     *
     * @param $post_id
     * @param  $post
     */
    function woffice_groups_sync_members($post_id, $post) {

        // We check if it's a project being saved
        if (
        	    defined('DOING_AJAX') && DOING_AJAX
                || $post->post_type != "project"
	            || wp_is_post_revision($post_id)
	            || !woffice_bp_is_active('groups')
        )
        	return;

        // We fetch the option :
        $projects_groups = woffice_get_settings_option('projects_groups');
        if ($projects_groups == "yep") {

            // we check for each group if it's a term name :
            $groups = groups_get_groups(array('show_hidden' => true));
            foreach ($groups['groups'] as $group) {
                // If it has the term and it's a buddypress group name
                if (has_term($group->name, 'project-category', $post_id)) {

                    // We create an array :
                    $array_members = array();
                    // we get the members
                    $group_members = groups_get_group_members(array('group_id' => $group->id));
                    if (!empty($group_members)) {
                        foreach ($group_members['members'] as $member) {
                            $array_members[] = $member->ID;
                        }
                    }
                    // we get the admins
                    $group_admins = groups_get_group_admins($group->id);
                    if (!empty($group_admins)) {
                        foreach ($group_admins as $admins) {
                            $array_members[] = $admins->user_id;
                        }
                    }
                    // we get the mods
                    $group_mods = groups_get_group_mods($group->id);
                    if (!empty($group_mods)) {
                        foreach ($group_mods as $mods) {
                            $array_members[] = $mods->user_id;
                        }
                    }

                    // We update the option :
                    if (!empty($array_members)) {

                        // Get the metas :
                        $project_data = get_post_meta($post_id, 'fw_options', true);
                        $new_project_data = $project_data;
                        $new_project_data['project_members'] = $array_members;
                        update_post_meta($post_id, 'fw_options', $new_project_data);
                        //fw_set_db_post_option($post_id, 'project_members', $array_members);

                    }

                    // We exit the loop
                    break;
                }
            }

        }
    }
}
add_action('save_post','woffice_groups_sync_members', 11, 3 );
add_action('woffice_after_frontend_process','woffice_groups_sync_members', 10, 2 );

if(!function_exists('woffice_register_project_notification')) {
    /**
     * Register project notifications
     */
    function woffice_register_project_notification() {

        // Register component manually into buddypress() singleton
        buddypress()->woffice_project = new stdClass;
        // Add notification callback function
        buddypress()->woffice_project->notification_callback = 'woffice_project_format_notifications';

        // Now register components into active components array
        buddypress()->active_components['woffice_project'] = 1;

    }
}
add_action( 'bp_setup_globals', 'woffice_register_project_notification' );

if(!function_exists('woffice_clear_project_notifications')) {
    /**
     * Clear project notifications
     */
    function woffice_clear_project_notifications() {

        // One check for speed optimization
        if ( is_singular( 'project' ) ) {

            if (is_user_logged_in() && woffice_bp_is_active('notifications')) {

                global $post;
                $current_user_id = get_current_user_id();

                if ($post->post_author == $current_user_id) {
                    bp_notifications_mark_notifications_by_item_id($current_user_id, $post->ID, 'woffice_project', 'Woffice_project_comment', false, 0);
                }

                bp_notifications_mark_notifications_by_item_id($current_user_id, $post->ID, 'woffice_project', 'woffice_project_assigned_todo', false, 0);

                bp_notifications_mark_notifications_by_item_id($current_user_id, $post->ID, 'woffice_project', 'woffice_project_assigned_member', false, 0);

            }

        }

    }
}
add_action('wp', 'woffice_clear_project_notifications');

if(!function_exists('woffice_project_notification_members_added')) {
    /**
     * Add BuddyPress notification for the Project, whenever a member is added
     *
     * @throws
     *
     * @param int $post_id
     * @param WP_Post $post
     */
    function woffice_project_notification_members_added($post_id, $post) {

        if ( $post->post_type != 'project' || ! Woffice_Notification_Handler::is_notification_enabled('project-member-assigned')) {
            return;
        }

        // Assigned members
        $members_assigned = fw_get_db_post_option($post_id, 'project_members');

        foreach ($members_assigned as $member_id) {
            bp_notifications_add_notification( array(
                'user_id'           => $member_id,
                'item_id'           => $post_id,
                'secondary_item_id' => get_current_user_id(),
                'component_name'    => 'woffice_project',
                'component_action'  => 'woffice_project_assigned_member',
                'date_notified'     => bp_core_current_time(),
                'is_new'            => 1,
            ) );
        }

    }
}
add_action('woffice_frontend_process_completed_success', 'woffice_project_notification_members_added', 10, 2);

if(!function_exists('woffice_add_activity_stream_for_project_creation')) {
    /**
     * Add BuddyPress activity for the Project
     *
     * @throws
     *
     * @param int $post_id
     * @param WP_Post $post
     */
    function woffice_add_activity_stream_for_project_creation($post_id, $post) {

        if ( $post->post_type != 'project' || !Woffice_Activity_Handler::is_activity_enabled('project-creation')) {
            return;
        }

        // Current user ID
        $current_user_id = get_current_user_id();

        if ($current_user_id != 0) {
            $activity_args = array(
                'action' => '<a href="'.bp_loggedin_user_domain().'">'.woffice_get_name_to_display($current_user_id).'</a> '.__('created the project ','woffice').' <a href="'.get_the_permalink($post_id).'">'.get_the_title($post_id).'</a>',
                'component' => 'project',
                'type' => 'project-creation',
                'item_id' => $post_id,
                'user_id' => $current_user_id,
            );
            bp_activity_add( $activity_args );
        }

    }
}
add_action('woffice_after_project_created', 'woffice_add_activity_stream_for_project_creation', 10, 2);

if(!function_exists('woffice_add_activity_stream_for_project_editing')) {
    /**
     * New notification whenever a project is edited
     *
     * @throws
     *
     * @param int $post_id
     * @param WP_Post $post
     */
    function woffice_add_activity_stream_for_project_editing($post_id, $post) {

        if ( !Woffice_Activity_Handler::is_activity_enabled('project-creation') && !function_exists('bp_activity_add')) {
            return;
        }

        // Current user ID
        $current_user_id = get_current_user_id();

        if ($current_user_id != 0) {
            $activity_args = array(
                'action' => '<a href="'.bp_loggedin_user_domain().'">'.woffice_get_name_to_display($current_user_id).'</a> '.__('edited the project ','woffice').' <a href="'.get_the_permalink($post->ID).'">'.get_the_title($post->ID).'</a>',
                'component' => 'project',
                'type' => 'project-editing',
                'item_id' => $post->ID,
                'user_id' => $current_user_id,
            );
            bp_activity_add( $activity_args );
        }

    }
}
add_action('woffice_after_project_updated', 'woffice_add_activity_stream_for_project_editing', 10, 2);

if(!function_exists('woffice_add_title_as_mv_category')) {
    /**
     * Set Multiverso categories for the project
     *
     * @param $postid
     */
    function woffice_add_title_as_mv_category($postid)
    {
        if (!class_exists('multiverso_mv_category_files') || defined('fileaway')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        $post = get_post($postid);
        if ($post->post_type == 'project') {
            $term = get_term_by('slug', $post->post_name, 'multiverso-categories');
            if (empty($term)) {
                $add = wp_insert_term($post->post_title, 'multiverso-categories', array('slug' => $post->post_name));
                if (is_array($add) && isset($add['term_id'])) {
                    wp_set_object_terms($postid, $add['term_id'], 'multiverso-categories', true);
                }
            }
        }
    }
}
add_action('save_post', 'woffice_add_title_as_mv_category');

function woffice_user_has_cap_delete_project( $allcaps, $cap, $args ) {

	if( !in_array('delete_published_posts', $cap) )
		return $allcaps;

	$user = ( isset($args[1]) ) ? get_userdata(absint( $args[1] )) : null;
	$post = ( isset($args[2]) ) ? get_post(absint( $args[2] )) : null;

	if( $user instanceof WP_User && $post instanceof WP_Post && $post->post_type == 'project') {
		$user_can_delete = ( $post->post_author == $user->ID );

		/**
		 * Filter if the user can delete a project
		 *
		 * @param bool $user_can_delete If the user can delete or not the project
		 * @param WP_Post $post The project post
		 * @param WP_user $user The user object
		 *
		 */
		$user_can_delete = apply_filters( 'woffice_user_can_delete_project', $user_can_delete, $post, $user);

		if( $user_can_delete ) {
			$allcaps['delete_posts'] = true;
			$allcaps['delete_published_posts'] = true;
		}
    }
    
	return $allcaps;
}
add_filter('user_has_cap', 'woffice_user_has_cap_delete_project', 10, 3);


if(!function_exists('woffice_assign_user_to_projects_after_group_update')) {
    /**
     * Add new group member to projects when group member add/remove
     *
     * @param integer $group_id
     *
     * @return void
     */
    function woffice_assign_user_to_projects_after_group_update($group_id) {

        $projects_groups = woffice_get_settings_option('projects_groups');

        if ($projects_groups === "nope") {
            return;
        }

        // We create an array for members id :
        $array_members = array();

        // We get the members
        $group_members = groups_get_group_members(array('group_id' => $group_id));

        if (!empty($group_members)) {
            foreach ($group_members['members'] as $member) {
                $array_members[] = $member->ID;
            }
        }

        // We get the admins
        $group_admins = groups_get_group_admins($group_id);
        if (!empty($group_admins)) {
            foreach ($group_admins as $admins) {
                $array_members[] = $admins->user_id;
            }
        }

        // We get the mods
        $group_mods = groups_get_group_mods($group_id);
        if (!empty($group_mods)) {
            foreach ($group_mods as $mods) {
                $array_members[] = $mods->user_id;
            }
        }

        if (empty($array_members))
            return;

        // Get all projects of that group
        $group = groups_get_group($group_id);
        $args = array(
            'post_type' => 'project',
            'tax_query' => array(
                array(
                    'taxonomy' => 'project-category',
                    'field'    => 'name',
                    'terms'    => bp_get_group_name( $group ),
                ),
            ),
        );
        $projects = get_posts($args);

        foreach ($projects as $project) {
            // Get the metas and set updated meta
            $project_data = get_post_meta($project->ID, 'fw_options', true);
            $new_project_data = $project_data;
            $new_project_data['project_members'] = $array_members;

            update_post_meta($project->ID, 'fw_options', $new_project_data);
        }

    }
}

// Update project meta of the members when user join in the group
add_action('groups_join_group', 'woffice_assign_user_to_projects_after_group_update', 10, 2);

if(!function_exists('woffice_remove_user_to_projects_after_group_update')) {
    /**
     * Add new group member to projects when group member add/remove
     *
     * @param integer $group_id
     * @param integer $user_id
     *
     * @return void
     */
    function woffice_remove_user_to_projects_after_group_update($group_id, $user_id) {

        $projects_groups = woffice_get_settings_option('projects_groups');
        if ($projects_groups === "nope") {
            return;
        }

        // We create an array of members id :
        $array_members = array();

        // We get the members
        $group_members = groups_get_group_members(array('group_id' => $group_id));

        if (!empty($group_members)) {
            foreach ($group_members['members'] as $member) {
                $array_members[] = $member->ID;
            }
        }
        // We get the admins
        $group_admins = groups_get_group_admins($group_id);
        if (!empty($group_admins)) {
            foreach ($group_admins as $admins) {
                $array_members[] = $admins->user_id;
            }
        }
        // We get the mods
        $group_mods = groups_get_group_mods($group_id);
        if (!empty($group_mods)) {
            foreach ($group_mods as $mods) {
                $array_members[] = $mods->user_id;
            }
        }

        // Removed the selected user
        $array_members = array_diff($array_members , array($user_id));

        if (empty($array_members))
            return;


        // Get all projects of that group
        $group = groups_get_group($group_id);
        $args = array(
            'post_type' => 'project',
            'tax_query' => array(
                array(
                    'taxonomy' => 'project-category',
                    'field'    => 'name',
                    'terms'    => bp_get_group_name( $group ),
                ),
            ),
        );
        $projects = get_posts($args);

        foreach ($projects as $project) {
            // Get the metas and set updated meta
            $project_data = get_post_meta($project->ID, 'fw_options', true);
            $new_project_data = $project_data;
            $new_project_data['project_members'] = $array_members;
            update_post_meta($project->ID, 'fw_options', $new_project_data);
        }
    }
}

// Update project meta of the members when user leaving the group
add_action('groups_remove_member', 'woffice_remove_user_to_projects_after_group_update', 10, 2);

/*
* Hook into that action that'll fire every day
*/

if(!function_exists('woffice_project_daily_email_notification')) {
    function woffice_project_daily_email_notification() {
            
       $email_notification =  woffice_get_settings_option('project_daily_notification');

        if($email_notification == 'nope'){
            return;
        }

        $args = array(
            'post_type' => 'project',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'date',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'fw_option:project_completed',
                    'value' => 1,
                    'compare' => '!='
                ),
                array(
                    'key' => 'fw_options',
                    'value'   => array(''),
                    'compare' => 'NOT IN'
                ),
                array(
                    'key' => 'fw_options',
                    'value'   => serialize('project_todo'),
                    'compare' => 'NOT IN'
                )
            )
        );
        
        $projects_query = new WP_Query($args);
        
        $user_list  = array();
        $available_users = get_users();
        $project_posts = $projects_query->posts;
        $project_list  = array();
        $assigned_list = array();

        foreach ( $available_users as $available_user) {
            $user_list[$available_user->data->user_email] = array();
        }

        foreach ($project_posts as $post) {
           
            $post_id            = $post->ID;
            $post_title         = $post->post_title;
            $project_url           = get_permalink( $post_id );
            $project_meta[$post_title] = array();

            /*We get the tasks*/
            $project_tasks = woffice_get_post_option($post_id, 'project_todo_lists', '');
            
            if (!empty($project_tasks)) {
                  /*We loop the task*/
                foreach ($project_tasks as $task){
                    if(!isset($task['assigned'])){ 
                        continue;
                    } 
                     /* We check if it's not done AND it's assigned to the user */
                    $assigned_ready = (is_array($task['assigned'])) ? $task['assigned'] : explode(',',$task['assigned']);
                    
                    foreach ($assigned_ready as $assigned) {
                        if ( !empty($assigned) && $task['done'] == false) {
                            
                            $user_info = get_userdata($assigned);
                            $user_email = $user_info->user_email;
                            $user_name = $user_info->display_name;

                            $user_list[$user_email]['user_name'] = $user_name;
                            $user_list[$user_email][$post_title]['project_title'] = $post_title;
                            $user_list[$user_email][$post_title]['project_url'] = $project_url;
                            
                            if(!array_key_exists('todo_list',$user_list[$user_email][$post_title])) {
                                $user_list[$user_email][$post_title]['todo_list'] = array();
                                array_push($user_list[$user_email][$post_title]['todo_list'], $task['title']);
                            } else {
                                array_push($user_list[$user_email][$post_title]['todo_list'], $task['title']);
                            }

                            if(!array_key_exists('due_date',$user_list[$user_email][$post_title])) {
                                $user_list[$user_email][$post_title]['due_date'] = array();
                                array_push($user_list[$user_email][$post_title]['due_date'], $task['date']);
                            } else {
                                array_push($user_list[$user_email][$post_title]['due_date'], $task['date']);
                            } 
                        }
                    }
                }
            }
        }
        return $user_list; 
    }
}

/*
* Cron Schedule change filter
*/
function woffice_check_notification_everyday($schedules) {
    $schedules['everyday'] = array(
        'interval'  => 86400, //86400 day
        'display'   => __('Everyday', 'woffice')
    );

    return $schedules;
}
add_filter('cron_schedules', 'woffice_check_notification_everyday');

// run cron job after 24 hours
if (!wp_next_scheduled('woffice_check_notification_everyday')) {
    wp_schedule_event(time(), 'everyday', 'woffice_check_notification_everyday');
}

/*
* Hook into that action that'll fire every day
*/
add_action('woffice_check_notification_everyday', 'woffice_project_send_email_notifiction');

if(!function_exists('woffice_project_send_email_notifiction')){
    function woffice_project_send_email_notifiction() {
        if(class_exists('WOAE_Utils')) {
            do_action('woffice_advanced_email_notifiction');
        } else {
            do_action('woffice_lite_email_notifiction');
        }
    }
}

if(!function_exists('woffice_lite_project_email_notifictaion')) {
    function woffice_lite_project_email_notifictaion(){

        $user_list = woffice_project_daily_email_notification();
        if(!empty($user_list)){

            // remove the empty list
            $user_list = array_filter($user_list);
            $subject =  __('Daily task notification', 'woffice');
            $headers = array('Content-Type: text/html; charset=UTF-8');
            // loop the assigned user
            foreach($user_list as $itemkey => $user){
                $item_content = '';
                $message =  woffice_get_settings_option('projects_assigned_dailyemail_notification');
                $message = str_replace('{user_name}', $user["user_name"] . '<br/>', $message);
                
                foreach($user as $key => $task_item) {
                    if(isset($task_item["project_title"])){
                      $item_content .= '<p>'. $task_item["project_title"].' - '.$task_item["project_url"] . '</p>';
                      $j = 0;
                        echo "<ul style='text-align:left;margin-left:0px;'>";
                            foreach($task_item["todo_list"] as $task_list){
                                $item_content .= '<li>' . $task_list . __(' with Due Date: ','woffice') . $task_item["due_date"][$j] . '</li>';
                                $j++;
                            }
                        echo "</ul>";
                    }
                }

                $message = str_replace('{project_info}', $item_content, $message);
                $email = wp_mail($itemkey, $subject, $message, $headers);

                $log_message = sprintf(
                    __( "Woffice Project notification failed to send.\nSend time: %s\nTo: %s\nSubject: %s\n\n", 'woffice' ),
                    date_i18n( 'F j Y H:i:s', current_time( 'timestamp' ) ),
                    $key,
                    $subject
                );

                if( $email == false ){
                     error_log( $log_message );
                }
            }
        }
    }
}

add_action('woffice_lite_email_notifiction', 'woffice_lite_project_email_notifictaion');

if(!function_exists('woffice_advaced_project_email_notifictaion')) {
    function woffice_advaced_project_email_notifictaion() {
        global $wp_filesystem;
        $user_list = woffice_project_daily_email_notification();
        $pro_message;

        if(class_exists('WOAE_Utils')) {
            $pro_subject = WOAE_Utils::woae_template_subject();
            $subject = $pro_subject['woffice-project'];

            require_once ( ABSPATH . '/wp-admin/includes/file.php' );
            WP_Filesystem();
            $pro_content = WOAE_Utils::woae_template_map();
            $email_template_path = WOAE_Utils::woae_template_directory() . $pro_content['woffice-project'].'.php';
            if(file_exists($email_template_path)){
                $tpath = $email_template_path;  
            } else {
                do_action('woffice_lite_email_notifiction');
                return;
            }
            
            $message = $wp_filesystem->get_contents($tpath); 
       
            if(!empty($user_list)){

                // remove the empty list
                $user_list = array_filter($user_list);
                $headers = array('Content-Type: text/html; charset=UTF-8');
                // loop the assigned user
                foreach($user_list as $itemkey => $user){
                    $item_content = '';
                    $message = str_replace('{user_name}', $user["user_name"] . '<br/>', $message);
                    foreach($user as $key => $task_item) {
                        if(isset($task_item["project_title"])){
                          $item_content .= '<p>'. $task_item["project_title"].' - '.$task_item["project_url"] . '</p>';
                          $j = 0;
                            echo "<ul style='text-align:left;margin-left:0px;'>";
                                foreach($task_item["todo_list"] as $task_list){
                                    $item_content .= '<li>' . $task_list . __(' with Due Date: ','woffice') . $task_item["due_date"][$j] . '</li>';
                                    $j++;
                                }
                            echo "</ul>";   
                        }
                    }

                    $message = str_replace('{project_info_hook}', $item_content, $message);
                    $email = wp_mail($itemkey, $subject, $message, $headers);
                    $log_message = sprintf(
                        __( "Woffice Project notification failed to send.\nSend time: %s\nTo: %s\nSubject: %s\n\n", 'woffice' ),
                        date_i18n( 'F j Y H:i:s', current_time( 'timestamp' ) ),
                        $key,
                        $subject
                    );

                    if( $email == false ){
                         error_log( $log_message );
                    }
                }
            }
        }
    }
}

add_action('woffice_advanced_email_notifiction', 'woffice_advaced_project_email_notifictaion');

/*
* Hook into that action that'll run for Woffice Subscription
*/
function woffice_subscription_payment_complete($order_id){  
    global $wpdb;
    $order = wc_get_order($order_id);
    $subscription_meta = $order->get_meta('_wo_sub:related_subscription');
    $order_items = $order->get_items();
   
    if(!empty($order)){
        foreach ($order_items as $order_item_id => $order_item) {
            $postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $order_item["name"] . "'" );
            $order_items_post_type = get_post_type($postid);
            if($order_items_post_type == 'project'){
            wc_update_order_item_meta($order_item_id,'_product_id', $postid);
            }
        }
    }
    
    if(!empty($subscription_meta)){

        $subscription_order = wc_get_order( $subscription_meta );
        $subscription_items = $subscription_order->get_items();
        
        foreach ($subscription_items as $subscription_item_id => $subscription_item) {
            echo $subscription_item_id;
            $subscriptionid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $subscription_item["name"] . "'" );
            $subscriptio_post_type = get_post_type($subscriptionid);
            if($subscriptio_post_type == 'project'){
                wc_update_order_item_meta($subscription_item_id,'_product_id', $subscriptionid);
            }
        }
    }
}

add_action( 'woocommerce_payment_complete', 'woffice_subscription_payment_complete' );

/*
* Hook into that action that'll run for Woffice Subscription when woffice project found in cart item
*/

function woffice_project_change_quantity_input( $product_quantity, $cart_item_key, $cart_item ) {
    $product_id = $cart_item['product_id'];
    $item_type = get_post_type($product_id);

    // whatever logic you want to determine whether or not to alter the input
    if ( $item_type == 'project' ) {
        return '<h3>' . $cart_item['quantity'] . '</h3>';
    }

    return $product_quantity;
}
add_filter( 'woocommerce_cart_item_quantity', 'woffice_project_change_quantity_input', 10, 3);