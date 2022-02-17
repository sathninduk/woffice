<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * Include static files: Javascript and Css
 * Compiled files
 */
if (is_admin()) {
	return;
}
/*---------------------------------------------------------
**
** COMMENTS SCRIPTS FROM WP
**
----------------------------------------------------------*/
if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply', '', array(), WOFFICE_THEME_VERSION );
}
/*---------------------------------------------------------
**
** CSS FILES NEEDED FOR WOFFICE
**
----------------------------------------------------------*/
if (function_exists('woffice_get_fonts_url') && woffice_get_fonts_url() ) {
	wp_enqueue_style('theme-fonts', woffice_get_fonts_url(), array(), WOFFICE_THEME_VERSION);
}

// Assets
wp_enqueue_style(
	'assets-css',
	get_template_directory_uri() . '/css/assets.min.css',
	array(),
    WOFFICE_THEME_VERSION
);

// Load our main stylesheet.
wp_register_style(
    'woffice-theme-style',
    get_template_directory_uri() . '/style.css',
    array(),
    WOFFICE_THEME_VERSION
);

// Load modern stylesheet.
wp_register_style(
    'woffice-theme-modern-style',
    get_template_directory_uri() . '/css/modern.css',
    array(),
    WOFFICE_THEME_VERSION
);

$theme_skin = woffice_get_settings_option('theme_skin');
if($theme_skin == 'modern') {
    wp_enqueue_style('woffice-theme-modern-style');
} else {
    wp_enqueue_style('woffice-theme-style');
}

// Load printed stylesheet.
wp_enqueue_style(
    'woffice-printed-style',
    get_template_directory_uri() . '/css/print.min.css',
    array(),
    WOFFICE_THEME_VERSION,
    'print'
);
/*---------------------------------------------------------
**
** JS FILES NEEDED FOR WOFFICE
**
----------------------------------------------------------*/
// LOAD JS PLUGINS FOR THE THEME

wp_enqueue_script(
	'woffice-theme-script',
	get_template_directory_uri() . '/js/woffice.min.js',
	array( 'jquery', 'underscore' ),
    WOFFICE_THEME_VERSION,
	true
);

// Load modern stylesheet.
if(function_exists('woffice_projects_extension_on') || class_exists('Widget_Woffice_Event')){
    wp_enqueue_style(
        'woffice-theme-datetimepicker',
        get_template_directory_uri() . '/css/jquery.datetimepicker.css',
        array(),
        WOFFICE_THEME_VERSION
    );

    wp_enqueue_script(
        'woffice-theme-script-moment',
        get_template_directory_uri() . '/js/moment.js',
        array( 'jquery')
    );

    wp_enqueue_script(
        'woffice-theme-script-datetimepicker',
        get_template_directory_uri() . '/js/jquery.datetimepicker.js',
        array( 'jquery' )
    );
}

//NAVIGATION FIXED
$header_fixed = woffice_get_settings_option('header_fixed');
if( $header_fixed == "yep" ) :
    wp_enqueue_script(
        'woffice-fixed-navigation',
        get_template_directory_uri() . '/js/fixed-nav.js',
        array( 'jquery' ),
        WOFFICE_THEME_VERSION,
        true
    );
endif;



// We load the chat JS
if(Woffice_AlkaChat::isChatEnabled()) {


    $has_emojis = woffice_get_settings_option('alka_pro_chat_emojis_enabled');
    if ($has_emojis) {
        // Emojis CSS
        wp_enqueue_style('woffice-css-emojis-picker', get_template_directory_uri() . '/css/emojis/jquery.emojipicker.css', array(), WOFFICE_THEME_VERSION);
        wp_enqueue_style('woffice-css-emojis-twitter', get_template_directory_uri() . '/css/emojis/jquery.emojipicker.tw.css', array(), WOFFICE_THEME_VERSION);
        // Emojis JS
        wp_enqueue_script('woffice-js-emojis-picker', get_template_directory_uri() . '/js/emojis/jquery.emojipicker.js', array('jquery'), WOFFICE_THEME_VERSION, true);
        wp_enqueue_script('woffice-js-emojis', get_template_directory_uri() . '/js/emojis/jquery.emojis.js', array('jquery'), WOFFICE_THEME_VERSION, true);
    }

    // Main JS
    wp_enqueue_script(
        'woffice-alka-chat-script',
        get_template_directory_uri() . '/js/alkaChat.vue.js',
        array( 'jquery', 'woffice-theme-script' ),
        WOFFICE_THEME_VERSION,
        true
    );

}

//Load scripts needed to attach image in the frontend editors
wp_enqueue_media();

{

    $data = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'site_url' => get_site_url(),
        'user_id' => get_current_user_id(),
        'nonce' => wp_create_nonce('ajax-nonce')
    );

    // Masonry Refresh Delay in MS
	$data['masonry_refresh_delay'] = 2000;

    // Mobile menu threshold
    $data['menu_threshold'] = woffice_get_settings_option('menu_threshold');

    $data['cookie_allowed'] = [
	    /**
	     * Filter `woffice_cookie_sidebar_enabled`
	     *
	     * Whether we save the sidebar state in a browser cookie
	     *
	     * @package boolean
	     */
	    'sidebar'  => apply_filters('woffice_cookie_sidebar_enabled', true),
    ];

    /**
     * The data is passed to the JS file in order to adjust the timeout delay for alerts
     * This paramenter need to be passed in milliseconds for example 4000 for 4s duration
     *
     * @param int $timeout
     */
    $data['alert_timeout'] = apply_filters( 'woffice_alert_timeout', 4000 );

	/**
	 * We give the possibility to hook new data for the Theme Script JS
	 * It's basically used for all things related to the Ajax calls
	 *
	 * @param array $data
	 */
	$data = apply_filters('woffice_js_exchanged_data', $data);

    wp_localize_script('woffice-theme-script', 'WOFFICE', $data);

}

