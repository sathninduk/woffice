<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * LOAD THE JAVASCRIPT FOR THE PROJECT
 */
if ( !is_admin() ) {

	$ext_instance = fw()->extensions->get( 'woffice-projects' );

	// LOAD PROJECTS SCRIPTS STYLES
	if (is_page_template("page-templates/projects.php") || is_singular('project')):

		wp_enqueue_script(
			'woffice-sortable',
			woffice_get_extension_uri( 'projects', 'static/js/jquery-sortable-min.js' ),
			array( 'jquery' ),
            WOFFICE_THEME_VERSION,
			true
		);
		wp_enqueue_script(
			'woffice-projects',
			woffice_get_extension_uri( 'projects', 'static/js/projects.js' ),
			array( 'jquery', 'woffice-theme-script' ),
            WOFFICE_THEME_VERSION,
			true
		);
        wp_enqueue_script(
            'woffice-todos',
            woffice_get_extension_uri( 'projects', 'static/js/todos.vue.js' ),
            array( 'jquery', 'woffice-theme-script' ),
            WOFFICE_THEME_VERSION,
            true
        );


        /*
         * Members select, so we can pass it to our JS
         */
        $project_members = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option(get_the_ID(), 'project_members') : '';
        $tt_users = array();
        if(empty($project_members)) {
            $tt_users_obj = get_users(array('fields' => array('ID', 'user_nicename')));

	        /**
	         * Filter the members available to be assigned to task
	         *
	         * @param array $tt_users_obj All the members available
	         * @param array $project_members All the members of the project
	         */
            $tt_users_obj = apply_filters( 'woffice_members_available_for_task_assignation', $tt_users_obj, $project_members );

            foreach ($tt_users_obj as $tt_user) {
                $tt_users[$tt_user->ID] = woffice_get_name_to_display($tt_user->ID);
            }
        } else {
            $tt_users_obj = $project_members;

            /**
	         * This filter has been documented above
	         */
            $tt_users_obj = apply_filters( 'woffice_members_available_for_task_assignation', $tt_users_obj, $project_members );

            foreach ($tt_users_obj as $tt_user) {
                if(!empty($tt_user)){
                    $user_info = get_userdata($tt_user);
                    if($user_info)
                        $tt_users[$user_info->ID] = woffice_get_name_to_display($user_info);
                }
            }
        }
        $tt_users_tmp = array('-1' => __("No one", "woffice")) + $tt_users;

        $is_advanced_task = false;

        if(class_exists('Woffice_Advanced_Tasks')) {
            $is_advanced_task = true;
        }

        wp_localize_script( 'woffice-todos', 'WOFFICE_TODOS', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'project_id' => get_the_ID(),
            'nonce' => wp_create_nonce('woffice_todos'),
            'label_name' => __('Name', 'woffice'),
            'label_start_date' => __('Start date', 'woffice'),
            'label_due_date' => __('Due date', 'woffice'),
            'label_urgent' => __('Is it urgent?', 'woffice'),
            'label_note' => __('Add a note (optional)', 'woffice'),
            'label_assign' => __('Assign a user (tip: type the username)', 'woffice'),
            'label_type' => __('Look for a username', 'woffice'),
            'label_add' => __('Add task', 'woffice'),
            'label_edit' => __('Edit task', 'woffice'),
            'remove_confirm_text' => __('Do you really want to delete this task?', 'woffice'),
            'available_users' => $tt_users_tmp,
            'is_advanced_task' => $is_advanced_task,
            'label_comment' =>  __('Add Comment', 'woffice'),
        ) );

        /**
         * JavaScript plugin options for the datepicker
         * Useful to specify a language
         *
         * @param $data
         * @return mixed
         */ 
        if(!function_exists('woffice_projects_datepicker_options')) {
            function woffice_projects_datepicker_options($data)
            {
                $data['datepicker_options'] = array(
                    'format' => 'YYYY-MM-DD H:mm',
                    'formatDate' => 'YYYY-MM-DD',
                    'formatTime' => 'H:mm',
                );
                return $data;
            }
        }
        add_filter('woffice_js_exchanged_data', 'woffice_projects_datepicker_options');

	endif;

	// AJAX STUFF FOR THE TO-DO MANAGER
	if (is_singular('project')):

		wp_localize_script('fw-extension-'. $ext_instance->get_name() .'-woffice-todo-manager', 'fw-extension-'. $ext_instance->get_name() .'-woffice-todo-manager', array('ajaxurl' =>  admin_url('admin-ajax.php')));

	endif;

}
