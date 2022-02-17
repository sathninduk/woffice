<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

if(!function_exists('woffice_projects_extension_on')) {
    /**
     * Used to check whether the function is enabled or not
     *
     * @return bool
     */
    function woffice_projects_extension_on()
    {
        return true;
    }
}

if(!function_exists('woffice_projects_have_comments')) {
    /**
     * Simple function to check whether the projects must have comments or not
     */
    function woffice_projects_have_comments() {
        /**
         * Filter the value returned by the function woffice_projects_have_comments()
         *
         * @param bool
         */
        return apply_filters('woffice_project_display_comments', true);
    }
}

if(!function_exists('woffice_projects_percentage')) {
	/**
	 * Project Percentage
	 *
	 * @param null $post_id
	 *
	 * @return float - the completed percentage
	 */
    function woffice_projects_percentage( $post_id = null )
    {

        if( is_null($post_id) || !is_int($post_id))
            $post_id = get_the_ID();

        // Check how we check the progress first :
        $project_progress = woffice_get_post_option($post_id, 'project_progress');

        if ($project_progress == "tasks") {

            // GET VALUES FROM OPTIONS
	        $project_todo_lists = woffice_get_post_option($post_id, 'project_todo_lists');

            // WE track by tasks
            if (!empty($project_todo_lists)) {
                $tasks_count = 0;
                $tasks_done = 0;
                foreach ($project_todo_lists as $todo) {
                    $tasks_count++;
                    if ($todo['done'] == TRUE) {
                        $tasks_done++;
                    }
                }
                $percent = (($tasks_done / $tasks_count) * 100);

            } else {
                $percent_f = 0;
            }

        } else {

            // GET VALUES FROM OPTIONS
	        $project_date_start = woffice_get_post_option($post_id, 'project_date_start');
	        $project_date_end = woffice_get_post_option($post_id, 'project_date_end');

            // WE track by time
            $begin = strtotime($project_date_start);
            $now = strtotime("now");
            $end = strtotime($project_date_end);

            $percent = ($end - $begin > 0) ? (($now - $begin) / ($end - $begin)) * 100 : 0;

	        // If the end date is not set, this percentage just cannot be calculated
            $percent = ( empty($project_date_end) ) ? 0 : $percent;
        }

        if ($percent < 0):
            $percent_f = 0;
        elseif ($percent > 100) :
            $percent_f = 100;
        else :
            $percent_f = $percent;
        endif;

        return floor($percent_f);
    }
}

if(!function_exists('woffice_project_progressbar')) {
    /**
     * The project progress bar markup
     */
    function woffice_project_progressbar()
    {

        $post_id = get_the_ID();

        // Check how we check the progress first :
        $project_progress = woffice_get_post_option( $post_id, 'project_progress');
        $theme_skin = woffice_get_settings_option('theme_skin');
        if ($project_progress == "tasks") {

            $project_todo_lists = woffice_get_post_option( $post_id, 'project_todo_lists');
            // THE PROGRESS BAR
            if (!empty($project_todo_lists)):
                if($theme_skin == 'modern'){
                    echo '<div class="progress-modern"><span class="progress-current">
                            <i class="fa fa-tasks"></i> ' . woffice_projects_percentage() . ' % / 100%
                        </span>
                        <div class="progress project-progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="' . woffice_projects_percentage() . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . woffice_projects_percentage() . '%">
                         </div>
                    </div></div>';
                } else {
                    echo '<div class="progress project-progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="' . woffice_projects_percentage() . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . woffice_projects_percentage() . '%">
                            <span class="progress-current">
                                <i class="fa fa-tasks"></i> ' . woffice_projects_percentage() . ' %
                            </span>
                        </div>
                    </div>';
                }
            endif;

        } else {

            // THE PROGRESS BAR
            $project_date_start = woffice_get_post_option( $post_id, 'project_date_start');
            $project_date_end = woffice_get_post_option( $post_id, 'project_date_end');
            if (!empty($project_date_start)):
                if($theme_skin == 'modern'){
                        echo '<!-- <span class="progress-start">' . $project_date_start . '</span> -->
                        <div class="progress-modern"><span class="progress-current">
                            <i class="fa fa-clock"></i> ' . woffice_projects_percentage() . ' % / 100 %
                        </span>
                        <!-- <span class="progress-end">' . $project_date_end . '</span> --><div class="progress project-progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="' . woffice_projects_percentage() . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . woffice_projects_percentage() . '%">
                        </div>
                    </div></div>';
                } else {
                    echo '<div class="progress project-progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="' . woffice_projects_percentage() . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . woffice_projects_percentage() . '%">
                            <!-- <span class="progress-start">' . $project_date_start . '</span> -->
                            <span class="progress-current">
                                <i class="fa fa-clock"></i> ' . woffice_projects_percentage() . ' %
                            </span>
                            <!-- <span class="progress-end">' . $project_date_end . '</span> -->
                        </div>
                    </div>';
                }
            endif;

        }

    }
}

if(!function_exists('woffice_get_project_menu')) {
    /**
     * Returns the Project Menu
     *
     * @param $post WP_Post
     * @return string
     */
    function woffice_get_project_menu($post)
    {

        $html = '<ul class="woffice-tab-layout__nav">';
        $current_user_is_admin = woffice_current_is_admin();
        /* View Link */
        $html .= '<li id="project-tab-view" class="active" data-tab="view">
			<a href="#project-content-view" class="fa-file">' . __("View", "woffice") . '</a>
		</li>';

        /* Edit Link */
        $project_edit = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option(get_the_ID(), 'project_edit') : '';
        if ($project_edit != 'no-edit' && is_user_logged_in()) :
            $user_can_edit = woffice_current_user_can_edit_project(get_the_ID());

            if($user_can_edit) {
                $html .= '<li id="project-tab-edit" data-tab="edit">';
                if ($project_edit == 'frontend-edit'):
                    $html .= '<a href="#project-content-edit" class="fa-edit">' . __("Edit", "woffice") . '</a>';
                else :
                    $html .= '<a href="' . get_edit_post_link($post->ID) . '" class="fa-pencil-square">' . __("Edit", "woffice") . '</a>';
                endif;
                $html .= '</li>';
            }
        endif;

        /* To-do Link */
        // IF TO-DO IS ENABLED
        $project_todo = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option(get_the_ID(), 'project_todo') : '';
        if ($project_todo):
            $html .= '<li id="project-tab-todo" data-tab="todo">
				<a href="#project-content-todo" class="fa-clipboard-list">' . __("Todo", "woffice") . '</a>
			</li>';
        endif;

	    /* Calendar Link */
	    $project_calendar = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option(get_the_ID(), 'project_calendar') : '';
	    if ($project_calendar === true && fw_ext('woffice-event')) :
		    $html .= '<li id="project-tab-calendar" data-tab="project-content-calendar">
                    <a href="#project-content-calendar" class="fa-calendar-alt">' . __("Calendar", "woffice") . '</a>
                </li>';
	    endif;

        /* Files Link */
        // IF THERE IS FILES
        $project_files = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option(get_the_ID(), 'project_files') : '';
        if (!empty($project_files)):
            $html .= '<li id="project-tab-files" data-tab="files">
				<a href="#project-content-files" class="fa-folder-open">' . __("Files", "woffice") . '</a>
			</li>';
        endif;

        /* Comments Link */
        if (comments_open() && woffice_projects_have_comments()) {
            $html .= '<li id="project-tab-comments" data-tab="comments">
				<a href="#project-content-comments" class="fa-comments">
					' . __("Comments", "woffice") . '
					<span>' . get_comments_number() . '</span>
				</a>
			</li>';
        }

        /* Delete Link */
        $user = wp_get_current_user();
	    $user_can_delete = ($post->post_author == $user->ID || $current_user_is_admin);

        /**
         * Filter if the user can delete a project
         *
         * @param bool $user_can_delete If the user can delete or not the project
         * @param WP_Post $post The project post
         * @param WP_user $user The user object
         *
         */
	    $user_can_delete = apply_filters( 'woffice_user_can_delete_project', $user_can_delete, $post, $user);

        if ( $user_can_delete ) :
            $html .= '<li id="project-tab-delete">
				<a onclick="return confirm(\'' . __('Are you sure you wish to delete article :', 'woffice') . ' ' . get_the_title() . ' ?\')" href="' . get_delete_post_link(get_the_ID(), '') . '" class="fa-trash">
					' . __("Delete", "woffice") . '
				</a>
			</li>';
        endif;

        $html .= '</ul>';

        return $html;

    }
}

if(!function_exists('woffice_projects_todo')) {
    /**
     * Returns the to-do Form (List + Add form)
     *
     * @param $post WP_Post
     */
    function woffice_projects_todo($post) {

        global $post;
        $allowed_modify = woffice_current_user_can_edit_project($post->ID);
        $is_advanced_task = false;

        if(class_exists('Woffice_Advanced_Tasks')) {
            $is_advanced_task = true;
        }

        /**
         * Checks whether the current user can create task
         *
         * @param bool $allowed_modify
         * @param Post $post
         */
        $allowed_create_tasks = apply_filters( 'woffice_allowed_create_project_tasks', $allowed_modify, $post);
        ?>

        <div id="woffice-project-todo" class="woffice-project-todo-group">

            <div v-if="todos.length != 0" class="woffice-project-filters clearfix">
                <ul class="list-inline float-left">
                    <li class="list-inline-item"><a href="#" @click.prevent="currentFilter = 'all'" :class="{ 'is-on' : currentFilter == 'all' }"><?php _e('All', 'woffice'); ?></a></li>
                    <li class="list-inline-item"><a href="#" @click.prevent="currentFilter = 'active'" :class="{ 'is-on' : currentFilter == 'active'}"><?php _e('Active','woffice'); ?></a></li>
                    <li class="list-inline-item"><a href="#" @click.prevent="currentFilter = 'urgent'" :class="{ 'is-on' : currentFilter == 'urgent'}"><?php _e('Urgent','woffice'); ?></a></li>
                    <li class="list-inline-item"><a href="#" @click.prevent="currentFilter = 'done'" :class="{ 'is-on' : currentFilter == 'done'}"><?php _e('Completed', 'woffice'); ?></a></li>
                </ul>
                <div class="float-right">
                    <select name="woffice-project-date-filter" id="woffice-project-date-filter" v-model="dueDateFilter">
                        <option value="no"><?php _e('Order by','woffice'); ?></option>
                        <option value="desc_due_date"><?php _e('Descending due date','woffice'); ?></option>
                        <option value="asc_due_date"><?php _e('Ascending due date','woffice'); ?></option>
                        <option value="desc_completion_date"><?php _e('Descending completion date','woffice'); ?></option>
                        <option value="asc_completion_date"><?php _e('Ascending completion date','woffice'); ?></option>
                    </select>
                </div>
            </div>

            <div v-show="isSuccess" class="tiny-alert tiny-alert-success">
                <i class="fa fa-check-circle"></i>
                <?php _e('Done!', 'woffice'); ?>
            </div>

            <div v-show="isFailure" class="tiny-alert tiny-alert-error">
                <i class="fa fa-times-circle"></i>
                <?php _e('Something went wrong!', 'woffice'); ?>
            </div>

            <div v-show="todos.length == 0" class="text-center">
                <div class="special-404 text-center">
                    <i class="fa fa-list-ul"></i>
                    <h2><?php _e('No to-do created so far.','woffice'); ?></h2>
                </div>
            </div>

            <transition-group name="fade" tag="div" class="woffice-tasks-wrapper mb-4">

                <div v-if="todos.length != 0" v-for="(todo, index) in filteredTodos" :key="todo._id" class="woffice-task" :class="{ 'has-note' : todo.note, 'is-done' : (todo.done == 1 || todo.done == 'true') }">

                    <header>

                        <div class="drag-handle"><i class="fa fa-bars"></i></div>

                        <label v-if="todo._can_check" class="woffice-todo-label">
                            <input type="checkbox" name="woffice-todo-done" @click="checkTodo(todo)" :checked="todo.done == 'true' || todo.done == 1">
                            <span class="checkbox-style"></span>
                            <span v-show="todo.title" v-text="todo.title"></span>
                        </label>
                        <label v-else class="woffice-todo-label">
                            <i v-show="todo.done == 'true' || todo.done == 1" class="fa fa-check-square"></i>
                            <span v-show="todo.title" v-text="todo.title"></span>
                        </label>

                        <?php if($allowed_modify) : ?>
                            <a href="#" @click.prevent="removeTodo(todo)" class="woffice-todo-action woffice-todo-delete"><i class="fa fa-trash"></i></a>
                        <?php if($is_advanced_task) { ?>
                            <span class="edit-advanced-todo"><i class="fa fa-pen-square"></i></span>
                        <?php } else { ?>
                            <a href="#" @click.prevent="toggleEit(todo)" class="woffice-todo-action woffice-todo-edit"><i class="fa" :class="[todo._display_edit ? 'fa-times' : 'fa-pen-square']"></i></a>
                            <a href="#" v-show="todo.note" @click.prevent="toggleNote(todo)" class="woffice-todo-action woffice-todo-note"><i class="fas" :class="[todo._display_note ? 'fa-times' : 'fa-file-text']"></i></a>    
                        <?php } endif; ?>
                        
                        <span>
                            <span v-if="todo._has_user_domain">
                                <span v-for="assigned in todo.assigned" class="todo-assigned">
                                    <a :href="assigned._profile_url" class="clearfix" v-html="assigned._avatar"></a>
                                </span>
                            </span>
                            <span v-else>
                                <span v-for="assigned in todo.assigned" class="todo-assigned" v-html="assigned._avatar"></span>
                            </span>
                        </span>

                        <span v-show="todo.date" class="todo-date"><i class="fa fa-calendar"></i><b v-text="todo._formatted_date"></b></span>

                        <span v-show="todo.urgent || todo.urgent == 'true'" class="todo-urgent"><i class="fa fa-bookmark"></i></span>

                    </header>

                    <transition name="slide-fade">
                        <section class="todo-note" v-show="todo.note && todo._display_note">
                            <p v-html="todo.note"></p>
                        </section>
                    </transition>
                    <?php if(!$is_advanced_task) { ?>
                    <transition name="slide-fade">
                        <section class="todo-edit" v-show="todo._display_edit">
                            <woffice-task-form :todo="todo" :labels="exchanger" :is-new="false"></woffice-task-form>
                        </section>
                    </transition>
                    <?php } ?>

                </div>

            </transition-group>

            <?php if ($allowed_create_tasks) : ?>

                <div class="heading"><h3><?php _e('Add a New Task', 'woffice'); ?></h3></div>
                <woffice-task-form :todo="newTodo" :labels="exchanger" :is-new="true"></woffice-task-form>

            <?php endif; ?>

        </div>

        <?php

    }
}

if(!function_exists('woffice_projects_new_task_actions')) {
    /**
     * Notifications, Messages whenever a new task is added
     *
     * @param $the_ID int - post id
     * @param $to_do array
     */
    function woffice_projects_new_task_actions($the_ID, $to_do) {

    //Send notification
    if ((Woffice_Notification_Handler::is_notification_enabled('project-todo-assigned'))) {

        $the_assigned = (!is_array($to_do['assigned'])) ? array($to_do['assigned']) : $to_do['assigned'];

        foreach ($the_assigned as $assigned_user) {
            bp_notifications_add_notification(array(
                'user_id' => $assigned_user,
                'item_id' => $the_ID,
                'secondary_item_id' => get_current_user_id(),
                'component_name' => 'woffice_project',
                'component_action' => 'woffice_project_assigned_todo',
                'date_notified' => bp_core_current_time(),
                'is_new' => 1,
            ));
        }

    }

    /*
     * We add it to the BuddyPress activity Personal Stream
     */
    $current_user_id = get_current_user_id();
    if ( $current_user_id != 0 && woffice_bp_is_active('activity') ) {
        $activity_args = array(
            'action' => '<a href="' . bp_loggedin_user_domain() . '">' . bp_get_displayed_user_mentionname() . '</a> ' . __('Added a new task in', 'woffice') . ' <a href="' . get_the_permalink($the_ID) . '">' . get_the_title($the_ID) . '</a>',
            'content' => $to_do['title'],
            'component' => 'project',
            'type' => 'todo-manager',
            'item_id' => $the_ID,
            'user_id' => $current_user_id,
            //'hide_sitewide' => true
        );
        bp_activity_add($activity_args);
    }

    /*
     * We add it to the BuddyPress Group activity Feed
     */
    // We fetch the option :
    $projects_groups = woffice_get_settings_option('projects_groups');
    if ($projects_groups == "yep" && woffice_bp_is_active('activity') && woffice_bp_is_active('groups')) {
        // We get the group name associated to the project
        $post_terms = get_the_terms($the_ID, 'project-category');
        if ($post_terms && !is_wp_error($post_terms)) {
            foreach ($post_terms as $term) {
                // We consider there is only one term for the project, might need to be improved later
                $group_name = $term->name;
                $group_id = groups_get_id(sanitize_title_with_dashes($group_name));
            }
        }
        if (isset($group_id)) {
            groups_record_activity(array(
                'action' => '<a href="' . bp_loggedin_user_domain() . '">' . bp_get_displayed_user_mentionname() . '</a> ' . __('Added a new task in', 'woffice') . ' <a href="' . get_the_permalink($the_ID) . '">' . get_the_title($the_ID) . '</a>',
                'content' => $to_do['title'],
                'item_id' => $group_id,
                'user_id' => $current_user_id,
                'type' => 'activity_update',
            ));
        }
    }
}
}

if(!function_exists('woffice_projects_fileway_manager')) {
    /**
     * Returns the File Away file manager
     */
    function woffice_projects_fileway_manager($post_slug) {

        $sub_name = "projects_" . $post_slug;

	    /**
	     * Filter to enable or not the manager mode for the project file manager
         *
         * @param bool
	     */
        $has_manager_mode = apply_filters('woffice_projects_fileway_manager_mode', true);
        $manager_att = ($has_manager_mode) ? 'manager="on"' : '';

        /* We output the directory */
        echo do_shortcode('[fileaway base="1" makedir="true" sub="' . $sub_name . '" '.$manager_att.' type="table" directories="true" paginate="false" makedir="true"  flightbox="images" bulkdownload="on"]');

        /* We output the file uploader */
        echo do_shortcode('[fileup base="1" makedir="true" exclude=".exe,.php" matchdrawer="true" sub="' . $sub_name . '"]');

    }
}

if(!function_exists('woffice_projects_filter')) {
    /**
     * Returns the project filter
     * In the listing page
     */
    function woffice_projects_filter()
    {

	    $projects_filter = woffice_get_settings_option('projects_filter');
	    $projects_date_filter = woffice_get_settings_option('projects_date_filter', true);
        $projects_status_filter = woffice_get_settings_option('projects_status_filter', true);
        $projects_layout = woffice_get_settings_option('projects_layout');
        $filter_class = '';

        if($projects_layout == 'grid') {
            $filter_class = "is-grid-filter";
        }
	    if ( !$projects_filter && !$projects_date_filter && !$projects_status_filter )
            return;

        echo '<div class="text-center '.$filter_class.'">';
        // display list-view/grid-view button
        if($projects_layout == 'grid'){
            echo '<div class="filter-item"><div class="btn-group project-view-btn">
                    <a id="list" class="btn btn-view-type" data-toggle="tooltip" data-placement="top" title="List View">
                    <span class="fa fa-list"></span>
                    </a>
                    <a id="grid" class="btn btn-view-type is-active" data-toggle="tooltip" data-placement="top" title="Grid View">
                        <span class="fa fa-th"></span>
                    </a>
                </div></div>';
        }
        echo '<div class="filter-item">';
        // Filter projects by category
        if ($projects_filter ) {
	        echo '<div id="woffice-project-filter" class="dropdown woffice-project-filter">';
	        echo '<button id="woffice-projects-filter-btn" type="button" class="btn btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
	        echo '<i class="fa fa-archive"></i>';
	        echo __( "Select Category", "woffice" );
	        echo '<i class="fa fa-caret-down"></i>';
	        echo '</button>';
	        echo '<ul class="dropdown-menu" role="menu">';
	        // SEARCH FOR PROJECT CATEGORIES
	        $terms = get_terms( 'project-category' );
	        if ( $terms ) :
		        // DROPDOWN LIST
		        foreach ( $terms as $term ) {
			        echo '<li class="dropdown-item"><a href="' . get_term_link( $term ) . '" data-slug="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</a></li>';
		        }
	        endif;
	        echo '</ul>';
	        echo '</div> <!-- #woffice-project-filter -->';

	        // Hide the other filters form the taxonomy pages
	        if ( ! is_page() ) {
		        return;
	        }
        }

        // Sort projects by dates
        if( $projects_date_filter ) {
            global $wp;
            $current_url = home_url( add_query_arg( array(), $wp->request ) );
            echo '<div id="woffice-project-date-filters" class="dropdown woffice-project-filter">';
            echo '<form id="woffice-projects-filter-date-form" action="' . esc_url( $current_url ) . '" method="get">';
            echo '<input type="hidden" name="filterDate" id="filterDate">';

            echo '<button id="woffice-projects-date-filter-btn" type="button" class="btn btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            echo '<i class="fa fa-calendar-times pr-3 ml-0"></i>' . __( "Sort by date", "woffice" ) . '<i class="fa fa-caret-down"></i>';
            echo '</button>';

            echo '<ul class="dropdown-menu" role="menu">';
            echo '<li><a href="javascript:void(0)" data-date="desc_creation_date" class="dropdown-item">' . esc_html__( 'Creation Date - Desc', 'woffice' ) . '</a></li>';
            echo '<li><a href="javascript:void(0)" data-date="asc_creation_date" class="dropdown-item">' . esc_html__( 'Creation Date - Asc', 'woffice' ) . '</a></li>';
            echo '<li><a href="javascript:void(0)" data-date="desc_completion_date" class="dropdown-item">' . esc_html__( 'Completion Date - Desc', 'woffice' ) . '</a></li>';
            echo '<li><a href="javascript:void(0)" data-date="asc_completion_date" class="dropdown-item">' . esc_html__( 'Completion Date - Asc', 'woffice' ) . '</a></li>';
            echo '</ul>';
            echo '</form>';
            echo '</div>';

            echo '<script type="text/javascript">
                jQuery("#woffice-project-date-filters .dropdown-menu a").on("click",function(){
                    jQuery("#filterDate").val(jQuery(this).data("date"));
                    jQuery("#woffice-projects-filter-date-form").submit();
                 });
                </script>';
        }

        // Sort projects by status
        if( $projects_status_filter ) {
            global $wp;
            $current_url = home_url( add_query_arg( array(), $wp->request ) );
            echo '<div id="woffice-project-status-filters" class="dropdown woffice-project-filter">';
            echo '<form id="woffice-projects-filter-status-form" action="' . esc_url( $current_url ) . '" method="get">';
            echo '<input type="hidden" name="filterStatus" id="filterStatus">';

            echo '<button id="woffice-projects-status-filter-btn" type="button" class="btn btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            echo '<i class="fa fa-hourglass-start pr-3 ml-0"></i>' . __( "Sort by status", "woffice" ) . '<i class="fa fa-caret-down"></i>';
            echo '</button>';

            echo '<ul class="dropdown-menu" role="menu">';
            echo '<li><a href="javascript:void(0)" data-status="all" class="dropdown-item">' . esc_html__( 'All Projects', 'woffice' ) . '</a></li>';
            echo '<li><a href="javascript:void(0)" data-status="archived" class="dropdown-item">' . esc_html__( 'Archived', 'woffice' ) . '</a></li>';
            echo '<li><a href="javascript:void(0)" data-status="done" class="dropdown-item">' . esc_html__( 'Done', 'woffice' ) . '</a></li>';
            echo '<li><a href="javascript:void(0)" data-status="in_progress" class="dropdown-item">' . esc_html__( 'In Progress', 'woffice' ) . '</a></li>';
            echo '<li><a href="javascript:void(0)" data-status="in_review" class="dropdown-item">' . esc_html__( 'In Review', 'woffice' ) . '</a></li>';
            echo '<li><a href="javascript:void(0)" data-status="planned" class="dropdown-item">' . esc_html__( 'Planned', 'woffice' ) . '</a></li>';
            echo '</ul>';
            echo '</form>';
            echo '</div>';

            echo '<script type="text/javascript">
                jQuery("#woffice-project-status-filters .dropdown-menu a").on("click",function(){
                    jQuery("#filterStatus").val(jQuery(this).data("status"));
                    jQuery("#woffice-projects-filter-status-form").submit();
                 });
                </script>';
        }
        echo '</div>';
        echo '</div>';
    }

}

/**
 * Check if the current project has some content to render in the header (It works in the loop)
 *
 * @return bool
 */
function woffice_project_content_exists() {

    $projects_filter = woffice_get_settings_option('projects_filter');
	$projects_date_filter = woffice_get_settings_option('projects_date_filter', true);
	$projects_archived_filter = woffice_get_settings_option('projects_archived_filter', true);
	$content = get_the_content();

    return ( !empty($content) || $projects_filter || $projects_date_filter || $projects_archived_filter);

}

if( !function_exists('woffice_get_projects_loop_query_args') ) {

	/**
     * Calculate the query args for the projects loop.
     * Basically it returns an array containing the ids of the project to exclude form the loop
     *
	 * @return array
	 */
	function woffice_get_projects_loop_query_args() {

		$project_query_args = array(
			'post_type' => 'project',
			'posts_per_page' => '-1',
		);

		$projects = new WP_query($project_query_args);
		$excluded = array();
        $hide_projects_archived = woffice_get_settings_option( 'hide_projects_completed', true );

        while ( $projects->have_posts() ) : $projects->the_post();

            $project_status = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option(get_the_ID(), 'project_current_status') : 'planned';
			$hide = false;

			if( $hide_projects_archived ) {

                if ($project_status == 'archived')
                    $hide = true;

				if( isset($_GET['filterStatus']) && $_GET['filterStatus'] == 'archived' ) {
					$hide = !$hide;
				}
            }

			if(!woffice_is_user_allowed_projects() || $hide) {
				array_push($excluded, get_the_ID());
			}

		endwhile;

		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		$project_query_args = array(
			'post_type' => 'project',
			'paged' => $paged,
            'post__not_in' => $excluded,
		);

		// Validate date filter
        $filters_allowed = array(
            'desc_creation_date',
            'asc_creation_date',
            'desc_completion_date',
            'asc_completion_date',
            'archived',
            'done',
            'in_progress',
            'in_review',
            'planned'
        );
        $filter_date = isset($_GET['filterDate']) ? $_GET['filterDate'] : '';
		if (empty($filter_date) && !isset($_GET['filterStatus']) && !in_array(array($filter_date , $filter_date), $filters_allowed))
	        return apply_filters('woffice_projects_loop_args', $project_query_args);

        // Filter by date
		$the_date_filter = $filter_date;

        if ($the_date_filter == 'desc_creation_date') {

            $project_query_args['orderby'] = 'date';
            $project_query_args['order'] = 'DESC';

        } else if($the_date_filter == 'asc_creation_date') {

            $project_query_args['orderby'] = 'date';
            $project_query_args['order'] = 'ASC';

        }

        // Filter by status
        $the_status_filter = $_GET['filterStatus'];

        if ($the_status_filter == 'archived') {

            $project_query_args['meta_key'] = 'fw_option:project_current_status';
            $project_query_args['meta_value'] = 'archived';
            $project_query_args['meta_compare'] = 'LIKE';

        } else if ($the_status_filter == 'done') {

            $project_query_args['meta_key'] = 'fw_option:project_current_status';
            $project_query_args['meta_value'] = 'done';
            $project_query_args['meta_compare'] = 'LIKE';

        } else if ($the_status_filter == 'in_progress') {

            $project_query_args['meta_key'] = 'fw_option:project_current_status';
            $project_query_args['meta_value'] = 'in_progress';
            $project_query_args['meta_compare'] = 'LIKE';

        } else if ($the_status_filter == 'in_review') {

            $project_query_args['meta_key'] = 'fw_option:project_current_status';
            $project_query_args['meta_value'] = 'in_review';
            $project_query_args['meta_compare'] = 'LIKE';

        } else if ($the_status_filter == 'planned') {

            $project_query_args['meta_key'] = 'fw_option:project_current_status';
            $project_query_args['meta_value'] = 'planned';
            $project_query_args['meta_compare'] = 'LIKE';
        }

		/**
		 * Filter the args of the query for project items loop
		 *
		 * @param array
		 */
		return apply_filters('woffice_projects_loop_args', $project_query_args);

	}
}

if( ! function_exists( 'woffice_usort_projects_by_completion_date_asc' ) ) {
	/**
	 * usort function to sort the project by completion date, ascending
	 *
	 * @param WP_Post|int $a
	 * @param WP_Post|int $b
	 *
	 * @return bool
	 */
	function woffice_usort_projects_by_completion_date_asc( $a, $b ) {

		$completion_date_a = woffice_get_project_completion_date_timestamp( $a );
		$completion_date_b = woffice_get_project_completion_date_timestamp( $b );

		return ( $completion_date_a > $completion_date_b );
	}
}

if( ! function_exists( 'woffice_usort_projects_by_completion_date_desc' ) ) {
	/**
     * usort function to sort the project by completion date, descending
     *
	 * @param WP_Post|int $a
	 * @param WP_Post|int $b
	 *
	 * @return bool
	 */
	function woffice_usort_projects_by_completion_date_desc( $a, $b ) {

		$completion_date_a = woffice_get_project_completion_date_timestamp( $a );
		$completion_date_b = woffice_get_project_completion_date_timestamp( $b );

		return ( $completion_date_a < $completion_date_b );
	}
}

if( ! function_exists( 'woffice_sort_projects_by_completion_date' ) ) {
	/**
     * If needed, sorts the projects by the completion date
     *
	 * @param array $projects
	 *
	 * @return array
	 */
	function woffice_sort_projects_by_completion_date( $projects ) {

		$filters_allowed = array( 'desc_completion_date', 'asc_completion_date' );

		if (!isset($_GET['filterDate']) || !in_array($_GET['filterDate'], $filters_allowed)) {
			return $projects;
		}

		if ($_GET['filterDate'] == 'desc_completion_date' ) {
			usort( $projects, "woffice_usort_projects_by_completion_date_desc" );
		} else {
			usort( $projects, "woffice_usort_projects_by_completion_date_asc" );
		}

		return $projects;
	}
}

if( ! function_exists( 'woffice_get_project_completion_date_timestamp' ) ) {
	/**
     * Returns the completion date timestamp of a fiven project
     *
	 * @param WP_Post|int $post
	 *
	 * @return int
	 */
	function woffice_get_project_completion_date_timestamp( $post = 0 ) {

		if ( ! $post instanceof WP_Post ) {
			$post = get_post( $post );
		}

		$progress_type = fw_get_db_post_option( $post->ID, 'project_progress' );
		if ( $progress_type == 'tasks' && woffice_projects_percentage( $post->ID ) == 100 ) {
			$completion_date_timestamp = woffice_get_post_option( $post->ID, 'completion_date', 0 );
			$completion_date           = ( $completion_date_timestamp ) ? strtotime( $completion_date_timestamp ) : 0;
		} else {
			$date_end        = woffice_get_post_option( $post->ID, 'project_date_end', 0 );
			$completion_date = ( $date_end ) ? strtotime( $date_end ) : 0;
		}

		return $completion_date;

	}
}

if(!function_exists('woffice_is_user_allowed_projects')) {
	/**
	 * Check if the user can see the project
	 * wp_insert_post_datawp_insert_post_data
     *
	 * @param null|int $post_id
	 * @return mixed
	 */
	function woffice_is_user_allowed_projects( $post_id = null ) {

		if (is_null($post_id))
			$post_id = get_the_ID();

		// Check if the projects are public for everyone
		$projects_public = woffice_get_settings_option( 'projects_public' );

		$single_project_visibility = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option($post_id, 'single_project_public') : 'default';

		if ($single_project_visibility != 'default') {
			if( $single_project_visibility == 'public')
				$projects_public = 'yep';
			else
				$projects_public = 'nope';
		}

		if ($projects_public == 'yep') {
          /**
           * Filter if the current user is allowed to see a project
           *
           * @param bool $allowed
           * @param int $post_id
           */
          return apply_filters( 'woffice_is_user_allowed_projects', true, $post_id );
        } else if(!is_user_logged_in()){

			$privacy_settings = woffice_get_settings_option('privacy_project');
			$public = woffice_get_settings_option( 'public' );

			$allowed = ($privacy_settings === 'public' || ($privacy_settings === 'default' && $public === 'yep'));

            /**
			 * This filter if documented above
			 */
			return apply_filters('woffice_is_user_allowed_projects', $allowed, $post_id);
        }


		// PROJECT MEMBERS
		$project_members = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option( $post_id, 'project_members' ) : array();
		// We exclude the author and the admins as they must access the project anyway
		$author_id = get_the_author_meta( 'ID' );
		$excluded  = get_users( array( 'fields' => 'id', 'role' => 'administrator' ) );
		if ( ! in_array( $author_id, $excluded ) ) {
			array_push( $excluded, (string) $author_id );
		}
		// ALL USERS WITHOUT THE ADMINS
		$all_users = get_users( array( 'fields' => 'id', 'exclude' => $excluded ) );
		// ALL USERS - PROJECT MEMBERS = EXCLUDED MEMBERS
		if ( ! empty( $project_members ) ) {
			$exclude_members = array_diff( $all_users, $project_members );
		} else {
			$exclude_members = array();
		}
		$user_ID    = get_current_user_id();
		$is_allowed = true;

		/* We check if the member is excluded */
		if ( ! empty( $exclude_members ) ) :
			foreach ( $exclude_members as $exclude_member ) {
				if ( $exclude_member == $user_ID ):
					$is_allowed = false;
				endif;
			}
		endif;

        /**
         * This filter if documented above
         */
		return apply_filters( 'woffice_is_user_allowed_projects', $is_allowed, $post_id );

	}
}

if(!function_exists('woffice_current_user_can_edit_project')) {
	/**
	 * Check if the current user is allowed to edit the project (edit and check the task)
	 *
	 * @param null $post_id
	 *
	 * @return bool
	 */
	function woffice_current_user_can_edit_project( $post_id = null ) {

		if( current_user_can('manage_options'))
			return true;

		if ( is_null( $post_id ) )
			$post_id = get_the_ID();


		$user_can_edit = true;
		$user_id = get_current_user_id();
		$only_author_can_edit = woffice_get_post_option( $post_id, 'only_author_can_edit');

		// If only the author can edit
		if ($only_author_can_edit == true) {

			if (get_the_author_meta('ID') != $user_id) {
				$user_can_edit = false;
			}

		// If all the members of the project can edit
		} else {

			$project_members = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option( $post_id, 'project_members' ) : '';
			if (!empty($project_members) && is_array($project_members) && !in_array($user_id, $project_members))
				$user_can_edit = false;
		}

		// Author can always edit
		if ($user_id && get_the_author_meta('ID') == $user_id)
			$user_can_edit = true;

        /**
         * Filter if the current user can edit a project
         *
         * @param bool $user_can_edit If the user can edit or not the project
         * @param int $post_id The id of the project post
         * @param int $user_id The id of the current user
         */
		return (bool)apply_filters( 'woffice_current_user_can_edit_project', $user_can_edit, $post_id, $user_id);
	}
}

if(!function_exists('woffice_current_user_can_check_task')) {
	/**
	 * Check if the current user can check the given task
	 *
	 * @param array $task
	 * @param WP_Post $project
	 * @param null|bool $allowed_edit_project If the current user has the permissions to edit the project. If null it will be calculated into the function
	 *
	 * @return bool
	 */
	function woffice_current_user_can_check_task( $task, $project, $allowed_edit_project = null ) {

	    if (!is_user_logged_in()) {
            return false;
        }

		if (is_null($allowed_edit_project)) {
			$allowed_edit_project = woffice_current_user_can_edit_project( $project->ID );
		}

		if ($allowed_edit_project) {
			$allowed_check = true;
		} else {
			$allowed_check = (in_array(get_current_user_id(), $task['assigned']));
		}

	    /**
         * Filter if the current user can check a project task. By default every user who can edit a project, can also
         * check the tasks
         *
         * @param bool $allowed_check If the user can check the task or not
         * @param array $task
         * @param WP_Post $project
         * @param bool $allowed_edit_project If the current user is allowed to edit the project
         */
		return apply_filters( 'woffice_allowed_check_project_task', $allowed_check, $task, $project, $allowed_edit_project );
	}
}

if(!function_exists('woffice_current_user_can_complete_project')) {
	/**
	 * Check if the current user can mark the projectas completed
	 *
	 * @param int|WP_Post|null $project
	 * @param null|bool $allowed_edit_project If the current user has the permissions to edit the project. If null it will be calculated into the function
	 * @return bool
	 */
	function woffice_current_user_can_complete_project( $project = null, $allowed_edit_project = null ) {

		// If project completation is disabled, then return false
		if (! apply_filters('woffice_frontend_project_completed_enabled', true) )
			return false;

		$project = get_post($project);

		if ( is_null( $allowed_edit_project ) ) {
			$allowed_edit_project = woffice_current_user_can_edit_project( $project->ID );
		}

		$is_allowed = false;
		if ( $project->post_author == get_current_user_id() || current_user_can( 'manage_options' ) )
			$is_allowed = true;


	  /**
	   * Filter if the current user can set a project as completed. By default every user who can edit a project, can also
	   * set a project as completed
	   *
	   * @param bool $is_allowed If the user can change the status or not
	   * @param WP_Post $project
	   * @param bool $allowed_edit_project If the current user is allowed to edit the project
	   */
		return apply_filters( 'woffice_allowed_complete_project', $is_allowed, $project, $allowed_edit_project );

	}
}

if(!function_exists('woffice_current_user_can_see_only_author_checkbox')) {
	/**
	 * Check if the current user can see the checkbox "Only author can edit"
	 *
	 * @param int|WP_Post|null $project
	 * @param null|bool $allowed_edit_project If the current user has the permissions to edit the project. If null it will be calculated into the function
	 * @return bool
	 */
	function woffice_current_user_can_see_only_author_checkbox( $project = null, $allowed_edit_project = null ) {

		$project = get_post($project);

		if ( is_null( $allowed_edit_project ) ) {
			$allowed_edit_project = woffice_current_user_can_edit_project( $project->ID );
		}

		$is_allowed = false;
		if ( $project->post_author == get_current_user_id() || current_user_can( 'manage_options' ) )
			$is_allowed = true;


	  /**
	   * Filter if the current user can see the checkbox "Only author can edit this". By default every user who
     * can edit a project, can also see that box
	   *
	   * @param bool $allowed_check If the user can see the task or not
	   * @param WP_Post $project
	   * @param bool $allowed_edit_project If the current user is allowed to edit the project
	   */
		return apply_filters( 'woffice_allowed_see_only_author_checkbox', $is_allowed, $project, $allowed_edit_project );

	}
}

if(!function_exists('woffice_project_format_notifications')) {
    /**
     * Format the notification for BP
     *
     * @param $action
     * @param $item_id
     * @param $secondary_item_id
     * @param $total_items
     * @param string $format
     * @return mixed|void
     */
    function woffice_project_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {

        if ( ! ('woffice_project_comment' === $action || 'woffice_project_assigned_todo' === $action || 'woffice_project_assigned_member' === $action) ) {
            return $action;
        }

        // Get the Title
        $post_title = get_the_title( $item_id );

        if ('woffice_project_comment' === $action) {
            $custom_title = sprintf( esc_html__( 'New comment received', 'woffice' ), $post_title );
            $custom_link  = get_permalink( $item_id ) .'#project-content-todo';
            if ( (int) $total_items > 1 ) {
                $custom_text  = sprintf( esc_html__( 'You received %1$s new comments on projects', 'woffice' ), $total_items );
                $custom_link = bp_get_notifications_permalink();
            } else {
                $custom_text  = sprintf( esc_html__( 'Your project "%1$s" received a new comment', 'woffice' ), $post_title );
            }

        }

        if ('woffice_project_assigned_todo' === $action) {
            $custom_title = sprintf( esc_html__( 'New task received', 'woffice' ), $post_title );
            $custom_link  = get_permalink( $item_id ) .'#project-content-todo';
            if ( (int) $total_items > 1 ) {
                $custom_text  = sprintf( esc_html__( 'You received %1$s new tasks', 'woffice' ), $total_items );
                $custom_link = bp_get_notifications_permalink();
            } else {
                $sender = woffice_get_name_to_display($secondary_item_id);
                $custom_text  = sprintf( esc_html__( '%2$s assigned you a new task on "%1$s"', 'woffice' ), $post_title, $sender );
            }
        }

        if ('woffice_project_assigned_member' === $action) {
            $custom_title = sprintf( esc_html__( 'Added to a project', 'woffice' ), $post_title );
            $custom_link  = get_permalink( $item_id );
            if ( (int) $total_items > 1 ) {
                $custom_text  = sprintf( esc_html__( 'You were added to %1$s new projects', 'woffice' ), $total_items );
                $custom_link = bp_get_notifications_permalink();
            } else {
                $sender = woffice_get_name_to_display($secondary_item_id);
                $custom_text  = sprintf( esc_html__( '%2$s added you to the project "%1$s"', 'woffice' ), $post_title, $sender );
            }
        }

        // WordPress Toolbar
        if ( 'string' === $format ) {
            $message = (!empty($custom_link)) ? '<a href="' . esc_url( $custom_link ) . '" title="' . esc_attr( $custom_title ) . '">' . esc_html( $custom_text ) . '</a>' : $custom_text;
            $return = apply_filters( 'woffice_project_format', $message, $custom_text, $custom_link );


        }

        // Deprecated BuddyBar
        else {
            $return = apply_filters( 'woffice_project_format', array(
                'text' => $custom_text,
                'link' => $custom_link
            ), $custom_link, (int) $total_items, $custom_text, $custom_title );
        }

        return $return;

    }
}

if(!function_exists('woffice_mv_managefiles_projects')) {
    /**
     * Multiverso assets
     *
     */
    function woffice_mv_managefiles_projects()
    {
        if(class_exists( 'multiverso_mv_category_files' ) && !defined('fileaway')) {

            require_once(WP_PLUGIN_DIR . '/multiverso/inc/functions.php');

            require_once(get_template_directory() . '/inc/multiverso.php');

        }
    }
}
if ( !function_exists( 'woffice_get_project_members' ) ) {
	/**
	 * Get the members IDs of a giver project
	 *
	 * @param $project_id
	 *
	 * @return mixed
	 */
	function woffice_get_project_members( $project_id = null ) {

		if (is_null($project_id))
			get_the_ID();

		$project_members = woffice_get_post_option( get_the_ID(), 'project_members', array() );

	  /**
	   * Filter the list of members of a given project
       *
       * @param array[int] $project_members The array containing the ids of the members
       * @param int $project_id the id of the prject
	   */
		return apply_filters( 'woffice_get_project_members', $project_members, $project_id );

	}
}

if ( !function_exists( 'woffice_projects_loop_render_dates' ) ) {
	/**
	 * Render the HTML for the dates of the projects. Used in the loop
	 *
	 * @param $post_id
	 */
	function woffice_projects_loop_render_dates( $post_id ) {
        $woffice_projects_post= get_post($post_id);
		$date = date('d-m-Y');
		if( !empty($post_id) ) {
			$project_date_start = woffice_get_post_option( $post_id, 'project_date_start', $date );
			$project_date_end = woffice_get_post_option( $post_id, 'project_date_end', $date );
		} else {
			$project_date_start = $date;
			$project_date_end = $date;
		}

		$dateTimestampStart = strtotime($project_date_start);
		$dateTimestampEnd = strtotime($project_date_end);

		$project_date_start = date_i18n(get_option('date_format'), $dateTimestampStart );
		$project_date_end = date_i18n(get_option('date_format'), $dateTimestampEnd );

		echo '<span class="project-category"><i class="fa fa-calendar"></i>';

		$date_now = strtotime(date('Y-m-d'));

		$project_progress = woffice_get_post_option( $post_id, 'project_progress');

		$date_string = '';
		if ($project_progress == "tasks") {

			// todo se percentuale == 100, stampa la data di fine (ultimo task
			if( !empty($post_id) && woffice_projects_percentage() == 100 ) {
				$completion_date_timestamp = woffice_get_post_option( $post_id, 'project_date_end', 'fw_option:completion_date' );

				if( $completion_date_timestamp )
					$date_string = esc_html__('Ended on: ', 'woffice').date(get_option('date_format', strtotime($completion_date_timestamp)));
				else if ( !empty($dateTimestampStart) ) {
					$date_string = esc_html__('Started on: ', 'woffice') . $project_date_start;
				}
			} else if ( !empty($dateTimestampStart) ) {

				if ( $dateTimestampStart > $date_now )
					$date_string = esc_html__('Starts on: ', 'woffice') . $project_date_start;
				else
					$date_string = esc_html__('Started on: ', 'woffice') . $project_date_start;

			}

		}else if ( !empty($dateTimestampEnd) ) {

			if ( !empty($dateTimestampStart) && $dateTimestampStart > $date_now )
				$date_string = esc_html__('Starts on: ', 'woffice') . $project_date_start;
            elseif ( $dateTimestampEnd > $date_now )
				$date_string = esc_html__('Ends on: ', 'woffice') . $project_date_end;
			else
				$date_string = esc_html__('Ended on: ', 'woffice') . $project_date_end;

		} else if ( !empty($dateTimestampStart) ) {

			if ( $dateTimestampStart > $date_now )
				$date_string = esc_html__('Starts on: ', 'woffice') . $project_date_start;
			else
				$date_string = esc_html__('Started on: ', 'woffice') . $project_date_start;

		}

		if( $date_string )
			echo $date_string;
		else
			echo esc_html__('Created on: ', 'woffice') . date(get_option('date_format', $woffice_projects_post->post_date ) );

		echo '</span>';

	}
}

if(!function_exists('woffice_directory_filter')) {
    /**
     * Returns the directory filter
     * In the listing page
     */
    function woffice_directory_filter()
    {
        $directory_filter = fw_get_db_ext_settings_option( 'woffice-directory', 'directory_filter', false);

        echo '<div class="text-center">';

        // Filter directory by category
        if ($directory_filter) {
            echo '<div id="woffice-directory-filter" class="dropdown woffice-directory-filter">';
            echo '<button type="button" class="btn btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            echo '<i class="fa fa-archive"></i>';
            echo __("Select Category", "woffice");
            echo '<i class="fa fa-caret-down"></i>';
            echo '</button>';
            echo '<ul class="dropdown-menu" role="menu">';
            // Get directory categories
            $terms = get_terms('directory-category');
            if ($terms) :
                // Generate list
                foreach ($terms as $term) {
                    echo '<li class="dropdown-item"><a href="' . get_term_link($term) . '" data-slug="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</a></li>';
                }
            endif;
            echo '</ul>';
            echo '</div> <!-- #woffice-directory-filter -->';
        }
        echo '</div>';
    }
}
