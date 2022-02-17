<?php if (!defined('FW')) {
    die('Forbidden');
}

/**
 * Main class of the Woffice calendar events
 *
 */
class FW_Extension_Woffice_Event extends FW_Extension
{

    /**
     * Setup the extension
     *
     * @internal
     */
    public function _init()
    {
        add_action('init', array($this, 'actionRegisterPostType'));
        add_shortcode('woffice_calendar', array($this, 'eventShortCode'));

        add_action('wp_ajax_nopriv_woffice_events_fetch', array($this, 'fetchEvents'));
        add_action('wp_ajax_woffice_events_fetch', array($this, 'fetchEvents'));

        add_action('wp_ajax_nopriv_woffice_events_create', array($this, 'addEvent'));
        add_action('wp_ajax_woffice_events_create', array($this, 'addEvent'));

        add_action('wp_ajax_nopriv_woffice_events_edit', array($this, 'editEvent'));
        add_action('wp_ajax_woffice_events_edit', array($this, 'editEvent'));

        add_action('wp_ajax_nopriv_woffice_events_download', array($this, 'downloadEvents'));
        add_action('wp_ajax_woffice_events_download', array($this, 'downloadEvents'));

        add_filter('woffice_page_title_title', array($this, 'filterTitle'), 10, 1);
        add_action('woffice_single_todo_update', array($this, 'updateToDoEvents'), 10, 3);
        add_action('woffice_todos_deleted', array($this, 'deleteToDoEvents'), 10, 2);
	    add_filter('user_has_cap', array($this, 'setDeleteCap'), 10, 3);
    }

	/**
	 * Let the event author delete their own events
	 *
	 * @param array $all_caps
	 * @param array $caps
	 * @param array $args
	 *
	 * @return array
	 */
    public function setDeleteCap($all_caps, $caps, $args)
    {
	    if (!in_array('delete_published_posts', $caps) && !in_array('delete_posts', $caps))
		    return $all_caps;

	    $user = ( isset($args[1]) ) ? get_userdata(absint( $args[1] )) : null;
	    $post = ( isset($args[2]) ) ? get_post(absint( $args[2] )) : null;

	    if ($user instanceof WP_User && $post instanceof WP_Post && $post->post_type == 'woffice-event') {
		    $user_can_delete = ($post->post_author == $user->ID);

		    /**
		     * Filter if the user can delete an event
		     *
		     * @param bool $user_can_delete If the user can delete or not the project
		     * @param WP_Post $post The project post
		     * @param WP_user $user The user object
		     *
		     */
		    $user_can_delete = apply_filters( 'woffice_user_can_delete_event', $user_can_delete, $post, $user);

		    if ($user_can_delete) {
			    $all_caps['delete_posts'] = true;
			    $all_caps['delete_published_posts'] = true;
		    }
	    }

	    return $all_caps;
    }

    /**
     * Short code to vue component render
     *
     * @param array $atts
     *
     * @return string
     */
    public function eventShortCode($atts)
    {

        $vue_atts = esc_attr(json_encode([
            'id'               => isset($atts['id']) ? $atts['id'] : '',
            'visibility'       => isset($atts['visibility']) ? $atts['visibility'] : false,
            'is_widget'        => isset($atts['widget'])
        ]));

        return "<div id='js-woffice-calendar-events' data-woffice-event-calendar='{$vue_atts}'></div>";
    }

    /**
     * Register calendar post type
     *
     * @return void
     */
    public function actionRegisterPostType()
    {

        $labels = array(
            'name'               => __('Events', 'woffice'),
            'singular_name'      => __('Event', 'woffice'),
            'menu_name'          => __('Events', 'woffice'),
            'name_admin_bar'     => __('Events', 'woffice'),
            'add_new'            => __('Add New', 'woffice'),
            'new_item'           => __('Event', 'woffice'),
            'edit_item'          => __('Edit Event', 'woffice'),
            'view_item'          => __('View Event', 'woffice'),
            'all_items'          => __('All Events', 'woffice'),
            'search_items'       => __('Search Events', 'woffice'),
            'not_found'          => __('No Event found.', 'woffice'),
            'not_found_in_trash' => __('No Event found in Trash.', 'woffice')
        );

        /**
         * Filter the labels of the custom post type "Woffice calendar"
         *
         * @param array $labels The array containing all the labels
         */
        $labels = apply_filters('woffice_post_type_calendar_labels', $labels);

        /**
         * Filter the slug of the custom post type "calendar"
         *
         * @param string $slug
         */
        $slug = apply_filters('woffice_rewrite_slug_post_type_calendar', 'woffice-event');

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'menu_icon'          => 'dashicons-calendar-alt',
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => $slug),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => true,
            'menu_position'      => null,
            'supports'           => array('title', 'thumbnail', 'revisions', 'author', 'comments')
        );

        /**
         * Filter the args of the custom post type "woffice-event"
         *
         * @param array $args the args of the custom post type
         * @param array $labels The array containing the labels
         */
        $args = apply_filters('woffice_post_type_calendar_args', $args, $labels);

        register_post_type('woffice-event', $args);
    }

    /**
     * Save post from array post data
     *
     * @param array $post_data
     * @param boolean $create
     *
     * @return int|WP_Error
     */
    public function savePost($post_data, $create = true)
    {
        $current_user_id = isset($post_data['post_author']) ? $post_data['post_author'] : get_current_user_id();
        $status          = 'publish';
        if ($post_data[$this->prefix() . 'visibility'] !== 'personal') {
            $status = fw_get_db_ext_settings_option('woffice-event', 'woffice_calendar_status', 'publish');
        }

	    $post = array(
		    'post_title'     => wp_strip_all_tags($post_data[$this->prefix() . 'title']),
		    'post_name'      => wp_strip_all_tags($post_data[$this->prefix() . 'title']) . '-' . $current_user_id,
		    'post_content'   => '',
		    'post_status'    => $status,
		    'post_author'    => $current_user_id,
		    'post_type'      => 'woffice-event'
	    );

        if ($create) {
	        $post_id = wp_insert_post($post);
        } else {
            $post_id = $post_data[$this->prefix() . 'post_id'];
            wp_update_post(array(
                'ID'         => $post_id,
                'post_title' => $post['post_title']
            ));
        }

        fw_set_db_post_option($post_id, $this->prefix() . 'title', stripslashes(htmlspecialchars($post_data[$this->prefix() . 'title'])));
        fw_set_db_post_option($post_id, $this->prefix() . 'date_start', date('Y-m-d H:i:s', strtotime($post_data[$this->prefix() . 'date_start'])));
        fw_set_db_post_option($post_id, $this->prefix() . 'date_end', date('Y-m-d H:i:s', strtotime($post_data[$this->prefix() . 'date_end'])));
        fw_set_db_post_option($post_id, $this->prefix() . 'repeat', $post_data[$this->prefix() . 'repeat']);
        fw_set_db_post_option($post_id, $this->prefix() . 'color', $post_data[$this->prefix() . 'color']);
        fw_set_db_post_option($post_id, $this->prefix() . 'visibility', $post_data[$this->prefix() . 'visibility']);
        fw_set_db_post_option($post_id, $this->prefix() . 'description', stripslashes(htmlspecialchars($post_data[$this->prefix() . 'description'])));
        fw_set_db_post_option($post_id, $this->prefix() . 'location', $post_data[$this->prefix() . 'location']);
        fw_set_db_post_option($post_id, $this->prefix() . 'link', $post_data[$this->prefix() . 'link']);

        // Repeated event end date
        if (isset($post_data[$this->prefix() . 'repeat_date_end'])) {
            fw_set_db_post_option($post_id, $this->prefix() . 'repeat_date_end', $post_data[$this->prefix() . 'repeat_date_end']);
        }

        // Saving post meta todo_id
        if (isset($post_data[$this->prefix() . 'todo'])) {
            add_post_meta($post_id, $this->prefix() . 'todo', $post_data[$this->prefix() . 'todo']);
        }

        $feature_file = $this->extractFile($this->prefix() . 'image');

        if (isset($feature_file['name']) && !empty($feature_file['name'])) {
            $this->saveFeaturedImage($feature_file, $post_id);
        }

        return $post_id;
    }

    /**
     * Get events for fetch and downloads with current short code attributes filter
     *
     * @return array
     */
    protected function postEvents()
    {

        $start_date = date('Y-m-d H:i:s', strtotime($_REQUEST['year'] . '-' . $_REQUEST['month'] . '-01'));
        $end_date   = date('Y-m-t H:i:s', strtotime($_REQUEST['year'] . '-' . $_REQUEST['month'] . '-01 23:59:59'));
        $args       = array(
                        'post_type'      => 'woffice-event',
                        'posts_per_page' => '-1',
                        'meta_key'       => 'fw_option:woffice_event_date_start',
                        'orderby'        => 'meta_value',
                        'order'          => 'ASC',
                        'meta_query'    => array(
                            array(
                                'key'       => 'fw_option:woffice_event_visibility',
                                'value'     => $this->visibility(),
                                'compare'   => '='
                            ),
                        )
        );

        if(!empty($_REQUEST['visibility']) && $_REQUEST['id'] == 'NaN'){
            $args = array(
                        'post_type'      => 'woffice-event',
                        'posts_per_page' => '-1',
                        'orderby'        => 'title',
                        'order'          => 'ASC',
                        'meta_query'    => array(
                            array(
                                'key'       => 'fw_option:woffice_event_visibility',
                                'value'     => $this->visibility(),
                                'compare'   => 'LIKE'
                            ),
                        )
                        
                    );
        }

        if (strtolower($this->visibility()) === 'personal') {
            return get_posts($args);
        }

        $id = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : 0;

        $visibility_filter  = $this->getVisibilityFilter($id);
        $args               = array_merge($args, $visibility_filter);
        $arr_date_filter    = array(
                            'relation' => 'OR',
                            array(
                                'key'     => 'fw_option:woffice_event_date_start',
                                'value'   => array($start_date, $end_date),
                                'type'    => 'DATE',
                                'compare' => 'BETWEEN'
                            ),
                            array(
                                'key'     => 'fw_option:woffice_event_repeat',
                                'value'   => 'No',
                                'compare' => '!='
                            )
        );

        if (isset($args['meta_query'])) {
            array_push($args['meta_query'], $arr_date_filter);
        } else {
            $args['meta_query'] = $arr_date_filter;
        }

        return get_posts($args);
    }

    /**
     * Fetch current user's events
     * Used by ajax to load events in the calendar
     */
    public function fetchEvents()
    {
        if (!check_ajax_referer('woffice_events')) {
            echo json_encode(array('status' => 'fail'));
            wp_die();
        }

        $user_posts = $this->postEvents();
        $events     = $this->eventsFromPosts($user_posts);
        echo json_encode(array(
            'status' => 'success',
            'events' => $events
        ));

        wp_die();
    }

    /**
     * Check for authorization for single post for personal visibility
     *
     * @param Object $post
     *
     * @return bool
     */
    public function isPersonalEventAuthorized($post)
    {
        $post_type = strtolower(fw_get_db_post_option($post->ID, $this->prefix() . 'visibility'));
        $post_meta = explode('_', $post_type);
        if ($post_type === 'general') {
            return true;
        } elseif ($post_type === 'personal') {
            return $this->checkUserAccess($post);
        } elseif (sizeof($post_meta) > 1) {
            return $this->user_authorize($post_meta[1], $post_meta[0]);
        }

        return false;
    }

    /**
     * Populate event data from event type posts
     *
     * @param Post collection $user_posts
     *
     * @return array
     */
    protected function eventsFromPosts($user_posts)
    {
        $data = array();
        $visibility = isset($_REQUEST['visibility']) ? $_REQUEST['visibility'] : false;
        
        if(empty($user_posts) && $visibility == 'false' && $_REQUEST['id'] == 'NaN'){
            $args = array(
                        'post_type'      => 'woffice-event',
                        'posts_per_page' => '-1',
                        'orderby'        => 'title',
                        'order'          => 'ASC',
                    );
            $user_posts = get_posts($args);
        }

        foreach ($user_posts as $post) {
            
            $event_data         = $this->getEventData($post->ID);
            $month_end_date     = date('Y-m-t H:i:s', strtotime($_REQUEST['year'] . '-' . $_REQUEST['month'] . '-01 23:59:59'));
            $repeat_event_dates = $this->repeatEventDates(
                $event_data[$this->prefix() . 'date_start'],
                $event_data[$this->prefix() . 'date_end'],
                $month_end_date,
                strtolower($event_data[$this->prefix() . 'repeat']),
                $event_data[$this->prefix(). 'repeat_date_end']
            );
            if (sizeof($repeat_event_dates) === 0) {
                continue;
            }

            $event_data['_has_user_domain']     = function_exists('bp_core_get_user_domain');
            $event_data['_display_note']        = false;
            $event_data['_display_edit']        = false;
            $event_data['event_date_display']   = date('l jS, Y', strtotime($event_data[$this->prefix() . 'date_start']));
            $event_data['event_time']           = date(get_option('time_format'), strtotime($event_data[$this->prefix() . 'date_start']));
            $event_data['event_end_time']       = date(get_option('time_format'), strtotime($event_data[$this->prefix() . 'date_end']));
            $event_data['event_visibility']     = ucfirst(explode('_', $event_data[$this->prefix() . 'visibility'])[0]);
            $event_data['post_link']            = get_post_permalink($post->ID);

            foreach ($repeat_event_dates as $event_start_date) {
                if (is_array($event_start_date)) {
                    $event_data['event_date'] = date('Y-m-d', strtotime($event_start_date['date']));
                    $event_data['event_start'] = $event_start_date['start'] === 1;
                    $event_data['event_end'] = $event_start_date['end'] === 1;
                    $data[] = $event_data;
                }
                else {
                    $event_data['event_start'] = false;
                    $event_data['event_end'] = false;
                    $event_data['event_date'] = date('Y-m-d', strtotime($event_start_date));
                    $data[] = $event_data;
                }
            }
        }

        return $data;
    }

    /**
     * Get number of days of the event
     *
     * @param string $start
     * @param string $end
     *
     * @return int
     */
    public function daySpan($start, $end)
    {
        $start_date = new DateTime($start);
        $end_date   = new DateTime($end);
        $days       = $end_date->diff($start_date)->format('%a');

        return $days;
    }
    /**
     * Check if user authorize to access the post(project, group)
     *
     * @param int    $post_id
     * @param string $visibility
     *
     * @return bool
     */
    public function user_authorize($post_id, $visibility = '')
    {
        $post_id = (int)$post_id;

        if ($post_id < 1) {
            return false;
        }

        $visibility = isset($visibility) ? $visibility : $this->visibility();

        if ($visibility === 'project') {
            $project = get_post($post_id);

            if (get_current_user_id() === (int)$project->post_author) {
                return true;
            }
            $project_members = fw_get_db_post_option($project->ID, 'project_members');

            if (empty($project_members) || !in_array(get_current_user_id(), $project_members)) {
                return false;
            }

            return true;
        } elseif ($visibility === 'group') {
            return groups_is_user_member(get_current_user_id(), $post_id);
        }

        return true;
    }

    /**
     * Repeat events(daily, weekly, monthly, yearly) dates to show in the calendar
     *
     * @param string $start_date
     * @param string $end_date
     * @param string $month_end_date
     * @param string $event_repeat
     * @param string $repeat_end_date
     *
     * @return array
     */
    public function repeatEventDates($start_date, $end_date, $month_end_date, $event_repeat, $repeat_end_date)
    {
        $event_date = array();

        if ($event_repeat === 'weekly') {
            $event_date = $this->weeklyEventDates($start_date, $end_date, $month_end_date, $repeat_end_date);
        } elseif ($event_repeat === 'monthly') {
            $event_date = $this->singleRepeatEventDate($start_date, $end_date, $month_end_date, '+1 month', $repeat_end_date);
        } elseif ($event_repeat === 'yearly') {
            $event_date = $this->singleRepeatEventDate($start_date, $end_date, $month_end_date, '+1 year', $repeat_end_date);
        } elseif ($event_repeat === 'daily') {
            $event_date = $this->dailyRepeatEventDates($start_date, $end_date, $month_end_date, '+1 day', $repeat_end_date);
        } else {
            $total_days = $this->daySpan($start_date, $end_date);
            $span_dates = $this->dateRange($start_date, $total_days);
            foreach ($span_dates as $span_date) {
                $event_date[] = $span_date;
            }
        }

        return $event_date;
    }

    /**
     * Monthly and yearly event dates
     *
     * @param string $start_date
     * @param string $end_date
     * @param string $month_end_date
     * @param string $increment
     * @param string $repeat_end_date
     *
     * @return array
     */
    protected function singleRepeatEventDate($start_date, $end_date, $month_end_date, $increment, $repeat_end_date)
    {

        $event_date = array();
        $total_days = $this->daySpan($start_date, $end_date);

        // Just precaution to avoid infinity loop
        $limit = 100;
        while ($limit > 0) {
            $limit -= 1;
            $time   = strtotime($start_date);
            // Check for repetition end
            if ($repeat_end_date && strtotime($start_date) > strtotime($repeat_end_date)) {
                break;
            }

            if (date("Y-m", $time) === date("Y-m", strtotime($month_end_date))) {
                $span_dates = $this->dateRange($start_date, $total_days);
                foreach ($span_dates as $span_date) {
                    $event_date[] = $span_date;
                }

                break;
            }

            if (date("Y-m", $time) > date("Y-m", strtotime($month_end_date))) {
                break;
            }
            $start_date = date("Y-m-d", strtotime($start_date . ' ' . $increment));
        }

        return $event_date;
    }

    /**
     * Daily repeat event dates
     *
     * @param string $start_date
     * @param string $end_date
     * @param string $month_end_date
     * @param string $increment
     * @param string $repeat_end_date
     *
     * @return array
     */
    public function dailyRepeatEventDates($start_date, $end_date, $month_end_date, $increment, $repeat_end_date)
    {
        $event_dates    = array();
        $total_days     = $this->daySpan($start_date, $end_date);
        $event_date     = date("Y-m-01", strtotime($month_end_date));
        while (strtotime($event_date) <= strtotime($month_end_date)) {

            // Check for repetition end
            if ($repeat_end_date && strtotime($event_date) > strtotime($repeat_end_date)) {
                break;
            }

            if ($event_date >= date('Y-m-d', strtotime($start_date))) {
                $span_dates = $this->dateRange($event_date, $total_days);
                foreach ($span_dates as $span_date) {
                    $event_dates[] = $span_date;
                }
            }
            $event_date = date("Y-m-d", strtotime($event_date . ' ' . $increment));
        }

        return $event_dates;
    }

    /**
     * Weekly event dates
     *
     * @param string $start_date
     * @param string $end_date
     * @param string $month_end_date
     * @param string $repeat_end_date
     *
     * @return array
     */
    public function weeklyEventDates($start_date, $end_date, $month_end_date, $repeat_end_date)
    {
        $event_dates = array();
        $day_span    = $this->daySpan($start_date, $end_date);
        $event_day   = date('l', strtotime($start_date));
        $month       = date('M', strtotime($month_end_date));

        // First event date of the selected month
        $event_date  = date("Y-m-d", strtotime("first $event_day of $month"));

        while (strtotime($event_date) <= strtotime($month_end_date)) {
            // Check for repetition end
            if ($repeat_end_date && strtotime($event_date) > strtotime($repeat_end_date)) {
                break;
            }

            if ($event_date >= date('Y-m-d', strtotime($start_date))) {
                 $span_dates = $this->dateRange($event_date, $day_span);
                 foreach ($span_dates as $span_date) {
                     $event_dates[] = $span_date;
                 }
            }
            $event_date = date("Y-m-d", strtotime($event_date . ' +7 days'));
        }

        return $event_dates;
    }

    /**
     * Get event length(in days) from start to end date
     *
     * @param string $start_date
     * @param int $range
     *
     * @return array
     */
    public function dateRange($start_date, $range)
    {
        $event_dates = array();

        for ($count = 0; $count <= $range; $count += 1) {
            if ($count === 0) {
                $event_dates[] = array('start' => 1, 'end' => 0, 'date' => $start_date);
            }
            elseif ($count === $range) {
                $event_dates[] = array('start' => 0, 'end' => 1, 'date' => $start_date);
            }
            else {
                $event_dates[] = $start_date;
            }
            $start_date = date("Y-m-d", strtotime($start_date . ' +1 day'));
        }

        return $event_dates;
    }

    /**
     * Download events as .ics file
     */
    public function downloadEvents()
    {
        if(!class_exists('IcsExport')){
            include dirname(__FILE__) . '/includes/class-ics-export.php';
        }

        if (!check_ajax_referer('woffice_events')) {
            echo json_encode(array('status' => 'fail'));
            die();
        }
        $user_email = '';
        if(function_exists('bp_core_get_user_email')) {
            $user_email = bp_core_get_user_email(get_current_user_id());
        } else {
            $current_user = wp_get_current_user();
            $user_email = $current_user->user_email;
        }
        $user_posts = $this->postEvents();
        $events     = $this->eventsFromPosts($user_posts);
        $cal        = new IcsExport(date_default_timezone_get(), $user_email);

        foreach ($events as $event) {
            $cal->addEvent(function ($e) use ($event) {
                $e->timeZone    = date_default_timezone_get();
                $e->startDate   = $event[$this->prefix() . 'date_start'];
                $e->endDate     = $event[$this->prefix() . 'date_end'];;
                $e->uri         = $event[$this->prefix() . 'link'];
                $e->location    = $event[$this->prefix() . 'location'];
                $e->description = $event[$this->prefix() . 'description'];
                $e->summary     = $event[$this->prefix() . 'title'];
            });
        }

        $file_name = get_current_user_id() . '_' . time() . '_events.ics';
        header('Content-Type: ' . IcsExport::MIME_TYPE);
        header('Content-Disposition: attachment; filename=' . $file_name);
        echo $cal->serialize();

        wp_die();
    }

    /**
     * Get event visibility filter
     *
     * @param int $id visibility id
     *
     * @return array
     */
    public function getVisibilityFilter($id)
    {
        $visibility = strtolower($this->visibility());
        $id         = (int)$id;
        $filter     = array();
        if ($visibility === 'general') {
            $value = 'general';
        } elseif ($visibility === 'personal' || $id < 1) {
            return [];
        } elseif ($visibility === 'project') {
            $value = 'project_' . $id;
        } else {
            $value = 'group_' . $id;
        }

        $filter['meta_query'] = array(
            array(
                'key'     => 'fw_option:woffice_event_visibility',
                'value'   => $value,
                'compare' => '='
            ),
        );

        return $filter;
    }

    /**
     * Current visibility
     *
     * @return string
     */
    public function visibility()
    {
    	$visibility = (isset($_REQUEST['visibility'])) ? $_REQUEST['visibility'] : 'personal';

        return strtolower($visibility);
    }

    /**
     * Create event
     * Called by ajax request
     */
    public function addEvent()
    {
        $post_data    = $_POST['post_meta'];
        $event_create = woffice_get_settings_option('event_create');
        $not_personal = $post_data[$this->prefix() . 'visibility'] !== 'personal';

        if (!Woffice_Frontend::role_allowed($event_create) && $not_personal) {
            echo json_encode(array(
                'success' => false,
                'message' => __('Insufficient permission for creating events.', 'woffice')
            ));

            wp_die();
        }

        if (!check_ajax_referer('woffice_events')) {
            echo json_encode(array(
                'success' => false,
                'message' => __('Invalid request', 'woffice')
            ));

            wp_die();
        }

        if (!is_user_logged_in()) {
            echo json_encode(array(
                'success' => false,
                'message' => __('You need logging to create event.', 'woffice')
            ));

            wp_die();
        }

        $arr_error = array(
            'success' => false,
            'message' => __('Insufficient data for events', 'woffice')
        );

        // Events from .ics file
        if (isset($post_data[$this->prefix() . 'ics_name']) && !empty($post_data[$this->prefix() . 'ics_name'])) {
            $ics_file   = $this->extractFile($this->prefix() . 'ics');
            $ics_events = $this->populateEventDataFromFile($ics_file['tmp_name'], $post_data);
            $new_posts  = array();
            foreach ($ics_events as $arr_event) {
                $new_posts[] = $this->savePost($arr_event);
            }
            echo json_encode(array(
                'success' => 1,
                'message' => __('Total ' . count($new_posts) . ' calendar events imported into calendar successfully',
                    'woffice'),
                'ids'     => $new_posts
            ));

            wp_die();
        }

        // Event came from event form
        if (!isset($post_data[$this->prefix() . 'title'], $post_data[$this->prefix() . 'date_start'], $post_data[$this->prefix() . 'date_end'])) {
            echo json_encode($arr_error);
            wp_die();
        }

        $post_id = $this->savePost($post_data);
        echo json_encode(array(
            'success' => 1,
            'message' => __('Calendar event added into calendar successfully', 'woffice'),
            'ids'     => array($post_id)
        ));

        wp_die();
    }

	/**
	 * Edit event Called by ajax request
	 */
	public function editEvent()
	{
		if (!check_ajax_referer('woffice_events')) {
			echo json_encode(array(
				'success' => false,
				'message' => __('Invalid request', 'woffice')
			));

			wp_die();
		}

		if (!is_user_logged_in()) {
			echo json_encode(array(
				'success' => false,
				'message' => __('You need logging to create event.', 'woffice')
			));

			wp_die();
		}

		$arr_error = array(
			'success' => false,
			'message' => __('Insufficient data for events', 'woffice')
		);

		$post_data = $_POST['post_meta'];

		// Event came from event form
		if (!isset($post_data[$this->prefix() . 'post_id'], $post_data[$this->prefix() . 'title'], $post_data[$this->prefix() . 'date_start'], $post_data[$this->prefix() . 'date_end'])) {
			echo json_encode($arr_error);
			wp_die();
		}

		$post_id = $this->savePost($post_data, false);

		$start_date = esc_html(woffice_get_post_option($post_id, 'woffice_event_date_start'));
		$end_date = esc_html(woffice_get_post_option($post_id, 'woffice_event_date_start'));

		$visibility_str  = woffice_get_post_option($post_id, 'woffice_event_visibility');
		$visibility_obj  = explode('_',$visibility_str);
		$visibility = $visibility_str;
		if ( 'project' === $visibility_obj[0] ) {
			$pid = $visibility_obj[1];
			$post_obj = get_posts(array( 'post_type' => 'project','post__in' => array( $pid )) );
			$visibility = $post_obj[0]->post_title;
		} elseif ( 'group' === $visibility_obj[0] ) {
			$group_id = $visibility_obj[1];
			$visibility = bp_get_group_name( groups_get_group( $group_id ));
		}

		$event_color_str = woffice_get_post_option($post_id, 'woffice_event_color');
		$event_color = str_replace('-',' ', $event_color_str );

		$updated = (object) array(
			'woffice_event_title'           => woffice_get_post_option($post_id, 'woffice_event_title'),
			'woffice_event_date_start'      => $start_date,
			'woffice_event_date_start_i18n' => date_i18n(get_option(
					'date_format'),
					strtotime($start_date)
			                                   ) . ', ' . date(get_option('time_format'), strtotime($start_date)),
			'woffice_event_date_end'        => woffice_get_post_option($post_id, 'woffice_event_date_end'),
			'woffice_event_date_end_i18n'   => date_i18n(get_option(
					'date_format'),
					strtotime($end_date)
			                                   ) . ', ' . date(get_option('time_format'), strtotime($end_date)),
			'woffice_event_repeat'          => woffice_get_post_option($post_id, 'woffice_event_repeat'),
			'woffice_event_color'           => __( $event_color,'woffice'),
			'woffice_event_visibility'      => $visibility,
			'woffice_event_description'     => woffice_get_post_option($post_id, 'woffice_event_description'),
			'woffice_event_location'        => woffice_get_post_option($post_id, 'woffice_event_location'),
			'woffice_event_image'           => '',
			'woffice_event_image_name'      => '',
			'woffice_event_link'            => woffice_get_post_option($post_id, 'woffice_event_link'),
			'woffice_event_post_id'         => $post_id
		);

		$response = array(
			'success' => 1,
			'message' => __('Calendar event updated successfully', 'woffice'),
			'ids'     => array($post_id),
			'updated_event' => $updated
		);

		$feature_file = $this->extractFile($this->prefix() . 'image');

		if (isset($feature_file['name'])) {
			$feature_image_url = wp_get_attachment_url(get_post_thumbnail_id( $post_id ));
			$response['feature_image'] = $feature_image_url;
		}

		wp_send_json($response, 200);

		die();
	}

    /**
     * Populate an array with file info
     *
     * @param string $type
     *
     * @return array
     */
    protected function extractFile($type)
    {
        $arr_ics = array();
        if (!empty($_FILES) && $_FILES['post_meta'] && sizeof($_FILES['post_meta']['name']) > 0) {
            foreach ($_FILES['post_meta'] as $key => $arr_val) {
                $arr_ics[$key] = isset($_FILES['post_meta'][$key][$type]) ? $_FILES['post_meta'][$key][$type] : '';
            }
        }

        return $arr_ics;
    }

    /**
     * Get start date from ics array
     *
     * @param array $ics_event
     *
     * @return string
     */
    protected function getIcsStart($ics_event) {

        if (isset($ics_event['DTSTART;VALUE=DATE'])) {
            return $ics_event['DTSTART;VALUE=DATE'];
        }

        if (isset($ics_event['DTSTART'])) {
            return $ics_event['DTSTART'];
        }

        return $this->parseTimeStamp($ics_event, 'DTSTART');
    }

    /**
     * Get end date from ics array
     *
     * @param array $ics_event
     *
     * @return string
     */
    protected function getIcsEnd($ics_event) {

        if (isset($ics_event['DTEND;VALUE=DATE'])) {
            return $ics_event['DTEND;VALUE=DATE'];
        }

        if (isset($ics_event['DTEND'])) {
            return $ics_event['DTEND'];
        }

        $end = $this->parseTimeStamp($ics_event, 'DTEND');

        if ($end) {
            return $end;
        }

        return $this->parseTimeStamp($ics_event, 'EXDATE');
    }


    /**
     * Populate an array of events from .ics file
     *
     * @param string $file
     * @param array  $post_meta
     *
     * @return array
     * @throws Exception
     */
    protected function populateEventDataFromFile($file, $post_meta)
    {
        $old_events = json_decode(stripslashes($_POST['old_events']), true);

        if(!class_exists('icsImport')){
            include dirname(__FILE__) . '/includes/class-ics-import.php';
        }

        $import_obj = new icsImport();
        $ics_events = $import_obj->getIcsEventsAsArray($file);
        $time_zone  = date_default_timezone_get();
        $set_zone = '';

        if(!empty($ics_events)){
            $set_zone = isset($ics_events[1]['X-WR-TIMEZONE']) ? $ics_events[1]['X-WR-TIMEZONE'] : '';
        }
        unset($ics_events[1]);

        $arr_events = array();
        foreach ($ics_events as $ics_event) {
            $is_repeating = false;

            // Only event import allowed(VEVENT)
            if (!$ics_event['BEGIN'] || trim($ics_event['BEGIN']) !== 'VEVENT') {
                continue;
            }

            date_default_timezone_set($set_zone);
            
            $start_dttimearr = explode('T', $this->getIcsStart($ics_event));
            $StartDate = isset($start_dttimearr[0]) ? $start_dttimearr[0] : '';
            $startTime = isset($start_dttimearr[1]) ? $start_dttimearr[1] : '';

            //get EndDate And EndTime
            $end_dttimearr = explode('T', $this->getIcsEnd($ics_event));
            $EndDate = isset($end_dttimearr[0]) ? $end_dttimearr[0] : '';
            $EndTime = isset($end_dttimearr[1]) ? $end_dttimearr[1] : '';
          

            $tmp_event = array();
            $start_date   = new DateTime($this->getIcsStart($ics_event));

            if ($this->isDuplicateEvent($old_events, $ics_event['SUMMARY'], $start_date, $post_meta[$this->prefix() . 'visibility'])) {
                continue;
            }

            $endDt = new DateTime($this->getIcsEnd($ics_event));
            $endDt->setTimeZone(new DateTimezone($time_zone));

            if (isset($ics_event['RRULE'])) {
                $repeat_data  = $this->getRecurringData($ics_event['RRULE'], $post_meta[$this->prefix() . 'repeat']);
                $tmp_event[$this->prefix() . 'repeat'] = $repeat_data['repeat'];
                $is_repeating = true;

                // Looking for recurring end date
                if (isset($repeat_data['until']) && !empty($repeat_data['until'])) {
                    $repeat_end_date = new DateTime($repeat_data['until']);
                    $repeat_end_date->setTimeZone(new DateTimezone($time_zone));

                    if ($repeat_end_date < (new DateTime())) {
                        continue;
                    }

                    $tmp_event[$this->prefix() . 'repeat_date_end']  = $repeat_end_date->format('Y-m-d h:i');
                }
            } else {
                $tmp_event[$this->prefix() . 'repeat']  = $post_meta[$this->prefix() . 'repeat'];
            }



            // Skip past not repeated event
            if ($start_date < (new DateTime()) && !$is_repeating) {
                continue;
            }

            // If start and end has more than 30 hours length then skip only for repeat event
            $hours = ($endDt->getTimestamp() - $start_date->getTimestamp())/ (60 * 60);
            if ($is_repeating && $hours > 30) {
                continue;
            }

            // Event attributes
            // $tmp_event[$this->prefix() . 'date_start']  = $start_date->format('Y-m-d H:i:s');
            // $tmp_event[$this->prefix() . 'date_end']    = $endDt->format('Y-m-d H:i:s');
            $tmp_event[$this->prefix() . 'date_start']  = date('Y-m-d', strtotime($StartDate)) . date("H:i:s", strtotime($startTime));
            $tmp_event[$this->prefix() . 'date_end']    = date('Y-m-d', strtotime($EndDate)) . date("H:i:s", strtotime($EndTime));
            $tmp_event[$this->prefix() . 'title']       = $this->shortTernary($ics_event['SUMMARY'], $post_meta[$this->prefix() . 'title']);
            $tmp_event[$this->prefix() . 'description'] = $this->shortTernary($ics_event['DESCRIPTION'], $post_meta[$this->prefix() . 'description']);
            $tmp_event[$this->prefix() . 'location']    = $this->shortTernary($ics_event['LOCATION'], $post_meta[$this->prefix() . 'location']);
            $tmp_event[$this->prefix() . 'link']        = isset($ics_event['URL;VALUE=URI']) ? $ics_event['URL;VALUE=URI'] : $post_meta[$this->prefix() . 'link'];

            $tmp_event[$this->prefix() . 'color']       = $post_meta[$this->prefix() . 'color'];
            $tmp_event[$this->prefix() . 'visibility']  = $post_meta[$this->prefix() . 'visibility'];
            $arr_events[] = $tmp_event;
        }

        return $arr_events;
    }

    /**
     * Check whether event with same name, start date and visibilty exists
     *
     * @param array    $old_events
     * @param string   $title
     * @param DateTime $start_date
     * @param string   $visibility
     *
     * @return bool
     * @throws Exception
     */
    public function isDuplicateEvent($old_events, $title, $start_date, $visibility)
    {
        if (isset($title, $old_events[$title])) {
            $old_date = new DateTime($old_events[$title][0]);
            return ($old_date->format('Y-m-d') === $start_date->format('Y-m-d') && $old_events[$title][0] === $visibility);
        }

        return false;
    }

    /**
     * Get recurring type & end date
     *
     * @param string $recurring_string
     * @param string $default
     *
     * @return array
     */
    public function getRecurringData($recurring_string, $default)
    {
        $data = explode('RRULE:', $recurring_string);
        if (!isset($data[0])) {
            return ['repeat' => $default, 'until' => ''];
        }

        $repeat_data    = explode(';', $data[0]);
        $arr_recurring  = array('Daily', 'Weekly', 'Monthly', 'Yearly');
        $repeat_type    = '';
        $repeat_end    = '';

        foreach ($repeat_data as $meta) {
            $meta = explode('=', $meta);
            if ($meta[0] && $meta[0] === 'FREQ' && isset($meta[1])) {
                $repeat_type = $meta[1];
            }

            if ($meta[0] && $meta[0] === 'UNTIL' && isset($meta[1])) {
                $repeat_end = $meta[1];
            }
        }


        $repeat_type = ucfirst(strtolower($repeat_type));

        if (in_array($repeat_type, $arr_recurring)) {
            return ['repeat' => $repeat_type, 'until' => $repeat_end];
        }

        return ['repeat' => $default, 'until' => ''];
    }

    /**
     * Parse time stamp with time zone (e.g DTSTART;TZID=America/Toronto:20170701T160000)
     *
     * @param array  $arr_event
     * @param string $key
     *
     * @return string
     */
    public function parseTimeStamp($arr_event, $key)
    {
        $arr_date = preg_grep("/^$key*/", array_keys($arr_event), 0);
        foreach ($arr_date as $key => $val) {
            return isset($val, $arr_event[$val]) ? $arr_event[$val] : '';
        }

        return '';
    }

    /**
     * Shortcut for ternary to save bites
     *
     * @param string $first_key
     * @param string $default_value
     *
     * @return mixed
     */
    public function shortTernary($first_key, $default_value)
    {
        return isset($first_key) ? $first_key : $default_value;
    }

    /**
     * Save event image as post featured image
     *
     * @param array $file
     * @param int   $post_id
     *
     * @return void
     */
    public function saveFeaturedImage($file, $post_id)
    {

        $file_name  = $file['name'];
        $file_type  = $file['type'];
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($file['tmp_name']);
        $filename   = basename(str_replace(' ', '_', $file_name));
        if (wp_mkdir_p($upload_dir['path'])) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }

        file_put_contents($file, $image_data);

        $attachment = array(
            'post_mime_type' => $file_type,
            'post_title'     => sanitize_file_name($filename),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $file, $post_id);
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        wp_update_attachment_metadata($attach_id, $attach_data);
        set_post_thumbnail($post_id, $attach_id);
    }

    /**
     * Get event image
     *
     * @param int $post_id
     *
     * @return false|string
     */
    public function getImage($post_id)
    {
        return get_the_post_thumbnail_url($post_id);
    }

    /**
     * Get event assoc data by the post id
     *
     * @param int $post_id
     *
     * @return array
     */
    public function getEventData($post_id)
    {
        $field_prefix   = $this->prefix();
        $event_data     = ['post_id' => $post_id];
        foreach ($this->getFields() as $field) {
            $event_data[$field_prefix . $field] = fw_get_db_post_option($post_id, $field_prefix . $field, false);
        }
        return $event_data;
    }

    /**
     * Field name prefix
     *
     * @return string
     */
    public function prefix()
    {
        return 'woffice_event_';
    }

    /**
     * Get field name of event without prefix
     *
     * @return array
     */
    public function getFields()
    {
        return array(
            'title',
            'date_start',
            'date_end',
            'repeat',
            'color',
            'visibility',
            'description',
            'location',
            'link',
            'repeat_date_end',
        );
    }

	/**
	 * Filter the title
	 *
	 * @param string $title
	 *
	 * @return string
	 */
    public function filterTitle($title) {
    	global $post;

    	if (!isset($post) || !isset($post->ID) || $post->post_type !== 'woffice-event') {
    		return $title;
	    }

    	if (is_archive('woffice-event')) {
    	    return __('Events', 'woffice');
        }

		if ($post->ID) {
			return woffice_get_post_option( $post->ID, 'woffice_event_title' );
		}

		return $title;
    }

    /**
     * Add personal events to assigned members and project event for project page when task is created
     *
     * @param int $project_id
     * @param array $todo
     *
     * @return  void
     */
    public function addTodoEvent($project_id, $todo)
    {
        $todo['assigned'] = isset($todo['assigned']) ? $todo['assigned'] : array();
        $assigned_members = (!is_array($todo['assigned'])) ? array($todo['assigned']) : $todo['assigned'];
        $event_attrs = $this->getToDoEventAttrs($todo);

        // Project event
        $event_attrs['post_author'] = get_current_user_id();
        $event_attrs[$this->prefix() . 'visibility'] = 'project_' . $project_id;
        $this->savePost($event_attrs);

        // Each assigned member personal event
        foreach ($assigned_members as $user) {
            // Prevent self assignment duplicate event
            if ((int)$user === (int)$event_attrs['post_author']) {
                continue;
            }

            $event_attrs['post_author'] = $user;
            $event_attrs[$this->prefix() . 'visibility'] = 'personal';
            $this->savePost($event_attrs);
        }
    }

    /**
     * Add personal events to assigned members and project event for project page when task is created
     *
     * @param int $project_id
     * @param array $todo
     * @param int $post_id
     *
     * @return  void
     */
    public function updateToDoEvent($project_id, $todo, $post_id)
    {
        $event_attrs = $this->getToDoEventAttrs($todo);
        $event_attrs[$this->prefix() . 'post_id']    = $post_id;

        // We are not gonna update event visibility
        $event_attrs[$this->prefix() . 'visibility'] = fw_get_db_post_option($post_id, $this->prefix() . 'visibility');
        $this->savePost($event_attrs, false);

    }

    /**
     * ToDo event attributes from the array
     *
     * @param array $todo
     *
     * @return array
     */
    private function getToDoEventAttrs($todo)
    {
       return array(
           $this->prefix() . 'title'           => $todo['title'],
           $this->prefix() . 'date_start'      => date('Y-m-d H:i', strtotime($todo['date'] . ' 00:00')),
           $this->prefix() . 'date_end'        => date('Y-m-d H:i', strtotime($todo['date'] . ' 23:59')),
           $this->prefix() . 'repeat'          => 'No',
           $this->prefix() . 'color'           => 'default',
           $this->prefix() . 'visibility'      => 'personal',
           $this->prefix() . 'description'     => $todo['note'],
           $this->prefix() . 'location'        => '',
           $this->prefix() . 'link'            => '',
           $this->prefix() . 'todo'            => $todo['_id'],
       );
    }

    /**
     * Called from todos delete event, we are deleting calendar events
     *
     * @param array $todo_ids
     * @param int $project_id
     */
    public function deleteToDoEvents($todo_ids, $project_id)
    {
        $args = array(
            'post_type'      => 'woffice-event',
            'posts_per_page' => '-1',
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'     => $this->prefix() . 'todo',
                    'value'   => $todo_ids,
                    'compare' => 'IN',
                )
            )
        );

        $post_ids = get_posts($args);
        if (sizeof($post_ids) > 0) {
            foreach ($post_ids as $post_id) {
                wp_delete_post($post_id, true);
            }
        }
    }

    /**
     * Called from todo saved event, we are adjusting calendar event based on the type of action taken
     *
     * @param string $type add|edit|delete
     * @param array  $todo
     * @param int    $id
     */
    public function updateToDoEvents($type, $todo, $id)
    {
        if ($type === 'edit') {
            $args = array(
                'post_type'      => 'woffice-event',
                'posts_per_page' => '-1',
                'fields'         => 'ids',
                'meta_query'     => array(
                    array(
                        'key'     => $this->prefix() . 'todo',
                        'value'   => $todo['_id'],
                        'compare' => '=',
                    )
                )
            );

            $post_ids = get_posts($args);
            if (sizeof($post_ids) > 0) {
                foreach ($post_ids as $post_id) {
                    $this->updateToDoEvent($id, $todo, $post_id);
                }
            }
            else {
                $this->addTodoEvent($id, $todo);
            }
        } elseif ($type === 'add') {
            $this->addTodoEvent($id, $todo);
        }
    }

    /**
     * Get user id if its admin then return current profile's user id
     *
     * @param Object $post
     *
     * @return boolean
     */
    private function checkUserAccess($post) {
        $user_id    = get_current_user_id();

        /**
         * Current user can view other profile's calendar
         *
         * @param int $user_id  current user id
         */
        if (apply_filters('woffice_event_calendar_view_allowed', woffice_current_is_admin(), $user_id) && function_exists('bp_displayed_user_id')) {
            $user_id = bp_displayed_user_id();
        }

        return $user_id === (int)$post->post_author;
    }
}