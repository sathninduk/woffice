<?php
/**
 * Register calendar in personal profile menu item
 *
 */
function woffice_personal_calendar_menu()
{
    if (get_current_user_id() < 1 || !function_exists('woffice_current_is_admin') || !fw_ext('woffice-event')) {
        return;
    }

    if (get_current_user_id() !== bp_displayed_user_id() && !apply_filters('woffice_event_calendar_view_allowed', woffice_current_is_admin(), get_current_user_id())) {
        return;
    }
    
    bp_core_new_nav_item(array(
        'name'                    => __('Calendar', 'woffice'),
        'slug'                    => 'calendar',
        'default_subnav_slug'     => 'calendar',
        'screen_function'         => 'woffice_personal_calendar_screen',
        'position'                => 20,
        'show_for_displayed_user' => true,
    ));
}

/**
 * Title of the calendar
 *
 * @return void
 */
function woffice_members_page_function_to_show_screen_title()
{
    _e('My Calendar', 'woffice');
}

if (!function_exists('woffice_personal_calendar_screen')) {
    /**
     * We register the screen for Buddypress engine
     *
     * @return null
     */
    function woffice_personal_calendar_screen()
    {
        add_action('bp_template_title', 'woffice_members_page_function_to_show_screen_title');
        add_action('bp_template_content', 'woffice_personal_calendar_content');
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
    }
}

add_action('bp_setup_nav', 'woffice_personal_calendar_menu');

if (!function_exists('woffice_personal_calendar_content')) {
    /**
     * Shorcode for the calendar
     *
     * @return string
     */
    function woffice_personal_calendar_content()
    {
        global $bp;
        $user_id = $bp->displayed_user->id;

        echo do_shortcode('[woffice_calendar visibility="personal" id="' . $user_id . '"]');
    }
}
