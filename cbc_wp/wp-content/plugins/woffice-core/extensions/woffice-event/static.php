<?php if (!defined('FW')) {
    die('Forbidden');
}

/**
 * Load js and css for events
 */
if (!is_admin() && defined('WOFFICE_THEME_VERSION')) {

    $ext_instance = fw()->extensions->get('woffice-event');

    // Post options used in front end for translations and settings
    $post_options                                = fw()->theme->get_post_options('woffice-event')['event-box']['options'];
    $post_options['new_event_label']             = __('Add a new event', 'woffice');
    $post_options['event_repeat_end_date_label'] = __('Repeat until', 'woffice');
    $post_options['edit_event_label']            = __('Edit event', 'woffice');
    $post_options['new_event_btn_save']          = __('Create event', 'woffice');
    $post_options['event_btn_save']              = __('Save event', 'woffice');
    $post_options['woffice_event_image_label']   = __('Event image', 'woffice');
    $post_options['create_event_label']          = __('CREATE A NEW EVENT', 'woffice');
    $post_options['event_chose_file_label']      = __('Upload event image', 'woffice');
    $post_options['import_ics_file_label']       = __('Import .ics file instead', 'woffice');
    $post_options['user']                        = get_current_user_id();
    $post_options['advanced_settings']           = __('Advanced settings', 'woffice');
    $post_options['less_settings']               = __('Collapse', 'woffice');

    wp_enqueue_script(
        'woffice-events-main',
        woffice_get_extension_uri('event', 'static/js/events.vue.js'),
        array('jquery', 'woffice-theme-script'),
        WOFFICE_THEME_VERSION,
        true
    );

	/**
	 * Filter `woffice_events_frontend_config`
	 *
	 * Let you configure the Woffice Events config passed to the Frontend
	 *
	 * @param array
	 */
    $events_config = apply_filters('woffice_events_frontend_config', array(
	    'ajax_url'        => admin_url('admin-ajax.php'),
	    'nonce'           => wp_create_nonce('woffice_events'),
	    'fetch_action'    => 'woffice_events_fetch',
	    'add_action'      => 'woffice_events_create',
	    'edit_action'     => 'woffice_events_edit',
	    'events_download' => 'woffice_events_download',
	    'previous_month'  => __('Previous month', 'woffice'),
	    'next_month'      => __('Next month', 'woffice'),
	    'export_events'   => __('Export events', 'woffice'),
	    'full_day'        => __('Full day', 'woffice'),
	    'day_names'       => array(
		    __('Sunday', 'woffice'),
		    __('Monday', 'woffice'),
		    __('Tuesday', 'woffice'),
		    __('Wednesday', 'woffice'),
		    __('Thursday', 'woffice'),
		    __('Friday', 'woffice'),
		    __('Saturday', 'woffice')
	    ),
        'day_names_assoc' => array(
            'Sunday'    => __('Sunday', 'woffice'),
            'Monday'    => __('Monday', 'woffice'),
            'Tuesday'   => __('Tuesday', 'woffice'),
            'Wednesday' => __('Wednesday', 'woffice'),
            'Thursday'  => __('Thursday', 'woffice'),
            'Friday'    => __('Friday', 'woffice'),
            'Saturday'  => __('Saturday', 'woffice')
        ),
	    'short_day_names'       => array(
		    __('Sun', 'woffice'),
		    __('Mon', 'woffice'),
		    __('Tue', 'woffice'),
		    __('Wed', 'woffice'),
		    __('Thu', 'woffice'),
		    __('Fri', 'woffice'),
		    __('Sat', 'woffice')
	    ),
	    'month_names'     => array(
		    __('January', 'woffice'),
		    __('February', 'woffice'),
		    __('March', 'woffice'),
		    __('April', 'woffice'),
		    __('May', 'woffice'),
		    __('June', 'woffice'),
		    __('July', 'woffice'),
		    __('August', 'woffice'),
		    __('September', 'woffice'),
		    __('October', 'woffice'),
		    __('November', 'woffice'),
		    __('December', 'woffice'),
	    ),

	    'starting_day'    => __(ucfirst(fw_get_db_ext_settings_option($ext_instance->get_name(), 'woffice_calendar_starting_day', 'monday')), 'woffice'),
	    'month'           => date('m'),
	    'year'            => date('Y'),
	    'day'             => date('d'),
	    'time_format'     => woffice_date_php_to_moment_js(get_option('time_format')),
	    'date_format'     => woffice_date_php_to_moment_js(get_option('date_format')),
	    'available_days'  => fw_get_db_ext_settings_option($ext_instance->get_name(), 'woffice_calendar_days'),
	    'fields'          => array_keys($post_options),
	    'enable_event'    => (!function_exists('bp_is_active') || empty(bp_displayed_user_id())) ? 1 : get_current_user_id() === bp_displayed_user_id(),
	    'field_options'   => $post_options,
	    'datepicker'      => array(
	    	'format' => 'YYYY-MM-DD H:mm',
	    	'format_time' => 'H:mm',
	    	'format_date' => 'YYYY-MM-DD',
	    	'default_time' => '09:00',
	    )
    ));


    wp_localize_script(
        'woffice-events-main',
        'WOFFICE_EVENTS',
        $events_config
    );

}
