<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Extension_Woffice_Projects extends FW_Extension {

	/**
	 * @internal
	 */
	public function _init() {
	
		add_action( 'init', array( $this, 'action_register_post_type' ) );
		add_action( 'init', array( $this, 'action_register_taxonomy' ) );
		add_action('fw_extensions_after_activation', array($this, 'woffice_projects_flush'));
	
	}
	
	/**
     * We register the post type
	 */
	public function action_register_post_type() {

		$labels = array(
			'name'               => __( 'Projects', 'woffice' ),
			'singular_name'      => __( 'Project', 'woffice' ),
			'menu_name'          => __( 'Projects', 'woffice' ),
			'name_admin_bar'     => __( 'Project', 'woffice' ),
			'add_new'            => __( 'Add New', 'woffice' ),
			'new_item'           => __( 'Project', 'woffice' ),
			'edit_item'          => __( 'Edit Project', 'woffice' ),
			'view_item'          => __( 'View Project', 'woffice' ),
			'all_items'          => __( 'All Projects', 'woffice' ),
			'search_items'       => __( 'Search Project', 'woffice' ),
			'not_found'          => __( 'No Project found.', 'woffice' ),
			'not_found_in_trash' => __( 'No Project found in Trash.', 'woffice' )
		);

		/**
		 * Filter the labels of the custom post type "Project"
		 *
		 * @param array $labels The array containing all the labels
		 */
		$labels = apply_filters('woffice_post_type_project_labels', $labels);

		/**
		 * Filter the slug of the custom post type "Project"
		 *
		 * @param string $slug
		 */
		$slug = apply_filters('woffice_rewrite_slug_post_type_project', 'project');
		
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'menu_icon'          => 'dashicons-index-card',
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => $slug ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => true,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor','thumbnail', 'revisions', 'author', 'comments' )
		);

		/**
		 * Filter the args of the custom post type "Project"
		 *
		 * @param array $args the args of the custom post type
		 * @param array $labels The array containing the labels
		 */
		$args = apply_filters('woffice_post_type_project_args', $args, $labels);
	
		register_post_type( 'project', $args );
		
	}
	
	/**
	 * Flush Rewrite Rules
     *
     * @param $extensions array
	 */
	public function woffice_projects_flush($extensions) {
	
		if (!isset($extensions['woffice-projects'])) {
	        return;
	    }
	    
	    flush_rewrite_rules();
		
	}

	/**
	 * Register the taxonomy
	 */
	public function action_register_taxonomy() {

		$labels = array(
			'name'              => __( 'Project Categories', 'woffice' ),
			'singular_name'     => __( 'Project Category', 'woffice' ),
			'search_items'      => __( 'Search Project Categories', 'woffice' ),
			'all_items'         => __( 'All Project Categories', 'woffice' ),
			'edit_item'         => __( 'Edit Category', 'woffice' ),
			'update_item'       => __( 'Update Project Category', 'woffice' ),
			'add_new_item'      => __( 'Add New Project Category', 'woffice' ),
			'new_item_name'     => __( 'New Project Category', 'woffice' ),
			'menu_name'         => __( 'Categories', 'woffice' ),
		);
	
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'project-category' ),
		);

		/**
		 * Filter the args of the taxonomy "project-category"
		 *
		 * @param array $args the args of the taxonomy
		 */
		$args = apply_filters('woffice_taxonomy_project-category_args', $args);

		register_taxonomy( 'project-category', array( 'project' ), $args );
		
	}

	/**
	 * Update the Project post meta data with the post name and the new todos
     *
     * @param $the_ID
     * @param $new_todos
     * @return bool
	 */
	public function woffice_projects_update_postmeta($the_ID, $new_todos) {

		$existing_meta = get_post_meta($the_ID,'fw_options', true);
		$existing_meta['project_todo_lists'] = $new_todos;

		return update_post_meta($the_ID,'fw_options', $existing_meta);
	}
	
	/**
	 * Send email if needed for the assigned user
     *
     * @param int $post_id
     * @param array $project_todo_lists
     * @return array
	 */
	public function woffice_projects_assigned_email($post_id, $project_todo_lists) {

		/* We're using an array to save the new value once updated */
		$new_todos = array();
		
		/* We check if there is an assignment */
		foreach ($project_todo_lists as $key=>$todo) {

			$sent_counter = 0;
			
			/* We check if the email isn't sent AND there is a member assigned */
			if ($todo['email_sent'] == 'not_sent' && $todo['assigned'] != array('nope') && $todo['assigned'] != 'nope') {

				$post_title = get_the_title( $post_id );
				$post_url = get_permalink( $post_id );
			
				/* Then, We send the email : */
				$subject = $post_title . ': '. __('You have a new task','woffice') .'->'. $todo['title'];

				/**
				 * Filter the subject of the email sent when a new task is assigned to a member
				 *
				 * @param string $subject The subkect string
				 * @param string $post_title The title of the post
				 * @param string $todo['title'] The title of the task
				 */
				$subject = apply_filters('woffice_projects_assigned_email_subject', $subject, $post_title, $todo['title']);

				$message =  woffice_get_settings_option('projects_assigned_email_content');
				$message = str_replace('{project_url}',     $post_url, $message);
				$message = str_replace('{project_title}',   $post_title, $message);
				$message = str_replace('{todo_title}',      $todo['title'], $message);

				// Send email to the user.
				$assigned_ready = (!is_array($todo['assigned'])) ? explode(",",$todo['assigned']) : $todo['assigned'];
				foreach ($assigned_ready as $assigned) {
					$user_info = get_userdata($assigned);
					$user_email = $user_info->user_email;
					$headers = null;

					$message = str_replace('{user_name}', woffice_get_name_to_display($assigned), $message);

					/**
					 * Filter the headers of the email sent when a new task is assigned to a member
					 *
					 * @param array $headers
					 */
					$headers = apply_filters('woffice_projects_assigned_email_headers', $headers);

					$email = wp_mail($user_email, $subject, $message, $headers);

					if ($email == true) {
						/*We update the value of the post meta sent so it's not a loop */
						$sent_counter = $sent_counter + 1;
						if($sent_counter == 1) {
							$sent = 'sent';
						}
					}
				}
				
			} elseif ($todo['email_sent'] == 'sent' || $sent_counter >= 1){
				$sent = 'sent';
			}
			else {
				$sent = 'not_sent';
			}
			
			/* We keep the same values except for the email_sent */
			$new_todos[$key] = array(
				'title'             => $todo['title'],
				'done'              => $todo['done'],
				'completion_date'   => $todo['completion_date'],
                'urgent'            => $todo['urgent'],
				'date'              => $todo['date'],
				'note'              => $todo['note'],
				'assigned'          => $todo['assigned'],
				'email_sent'        => $sent
			);
			
		}

		return $new_todos;
		
	}

    /**
     * Sort two tasks by date
     *
     * @param $a
     * @param $b
     * @return false|int
     */
    private static function woffice_sort_tasks_by_date($a, $b) {

        if(empty($a['task_date']) && empty($b['task_date']))
            return 0;
        elseif(empty($a['task_date']))
            return 1;
        elseif(empty($b['task_date']))
            return -1;

        return strtotime($a['task_date']) - strtotime($b['task_date']);

    }

	/**
	 * Return array list of the assigned tasks for a certain user
     *
     * @param $user_ID
     * @return array
	 */
	public function woffice_projects_assigned_tasks($user_ID) {

		/*Array of assigned tasks*/
		$the_assigned_tasks = array();
		/*Counter*/
		$count = 0;
		
		if ($user_ID != 0){
		
			/*We loop all the projects to fetch tasks*/
			$projects_query = new WP_Query('post_type=project&showposts=-1');
			while($projects_query->have_posts()) : $projects_query->the_post(); 
					
				/*We get the tasks*/
				$project_tasks = woffice_get_post_option(get_the_ID(), 'project_todo_lists', '');
				if (!empty($project_tasks)) {
					
					/*We loop the task*/
					foreach ($project_tasks as $task){

						if(!isset($task['assigned']))
							continue;
						
						/* We check if it's not done AND it's assigned to the user */
						$task['assigned'] = (is_array($task['assigned'])) ? $task['assigned'] : explode(',',$task['assigned']);
						if (isset($task['done']) && $task['done'] == false && in_array($user_ID, $task['assigned'])){
							
							$title_task = (!empty($task['title'])) ? $task['title'] : "";
							$title_date = (!empty($task['date'])) ? $task['date'] : "";
							
							$the_assigned_tasks[] = array(
								'task_name' => $title_task,
								'task_date' => $title_date,	
								'task_project' => get_permalink(),
							);
							$count++;
							
						}
					
					}
				}
				
			endwhile;
			wp_reset_postdata();
			
		}

        usort($the_assigned_tasks, array('FW_Extension_Woffice_Projects', 'woffice_sort_tasks_by_date'));
		return array( 'number' => $count , 'tasks' => $the_assigned_tasks); 
		
	}
	
	/**
	 * Return array list of the assigned tasks without excluding completed task
     *
     * @param $user_ID
     * @return array
	 */
	public function woffice_projects_assigned_tasks_full_list($user_ID) {

		/*Array of assigned tasks*/
		$the_assigned_tasks = array();
		/*Counter*/
		$count = 0;
		
		if ($user_ID != 0){
		
			/*We loop all the projects to fetch tasks*/
			$projects_query = new WP_Query('post_type=project&showposts=-1');
			while($projects_query->have_posts()) : $projects_query->the_post(); 
					
				/*We get the tasks*/
				$project_tasks = woffice_get_post_option(get_the_ID(), 'project_todo_lists', '');
				if (!empty($project_tasks)) {
					
					/*We loop the task*/
					foreach ($project_tasks as $task){

						if(!isset($task['assigned']))
							continue;
						
						/* We check if it's not done AND it's assigned to the user */
						$task['assigned'] = (is_array($task['assigned'])) ? $task['assigned'] : explode(',',$task['assigned']);
						if (in_array($user_ID, $task['assigned'])){
							
							$title_task = (!empty($task['title'])) ? $task['title'] : "";
							$title_date = (!empty($task['date'])) ? $task['date'] : "";
							
							$the_assigned_tasks[] = array(
								'task_name' => $title_task,
								'task_date' => $title_date,	
								'task_done' => $task['done'],	
								'task_project' => get_permalink(),
							);
							$count++;
							
						}
					
					}
				}
				
			endwhile;
			wp_reset_postdata();
			
		}

        usort($the_assigned_tasks, array('FW_Extension_Woffice_Projects', 'woffice_sort_tasks_by_date'));
		return array( 'number' => $count , 'tasks' => $the_assigned_tasks); 
		
	}	
			
}
