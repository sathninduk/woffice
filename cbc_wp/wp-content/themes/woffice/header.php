<?php
/**
 * The Header of WOFFICE
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<!-- MAKE IT RESPONSIVE -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<?php
        $hide_seo = woffice_get_settings_option('hide_seo');
		echo ('yep' === $hide_seo ) ? '<meta name="robots" content="noindex">' : '';
		?>
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php // GET FAVICONS
		woffice_favicons();
		?>
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="<?php echo get_template_directory_uri(); ?>/js/html5shiv.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/respond.min.js"></script>
		<![endif]-->
		<?php wp_head(); ?>
	</head>

	<?php // We add a class if the navigation horizontal :
	$menu_layout = woffice_get_settings_option('menu_layout');
    $theme_skin = woffice_get_settings_option('theme_skin');
    $menu_class = ($menu_layout == "horizontal" || isset($_GET['horizontal_menu'])) ? "menu-is-horizontal" : "menu-is-vertical";

    /**
     * Menu layout for Woffice
     *
     * @param string
     *
     * @return string - must be either "menu-is-horizontal" or "menu-is-vertical"
     */
    $menu_class = apply_filters('woffice_menu_layout', $menu_class);
    if($theme_skin == 'modern') {
        $menu_class .= ' is-modern-skin';
    }   
    //IF Fixed we add a nav class
    $header_fixed = woffice_get_settings_option('header_fixed');
    $extra_navbar_class = ( $header_fixed == "yep" ) ? 'has_fixed_navbar' :'';

    $nav_opened_state = woffice_get_navigation_state();
    $sidebar_state = woffice_get_sidebar_state();
    $sidebar_show_class = ($sidebar_state != 'show') ? 'sidebar-hidden' : '';

	$design_update = woffice_get_settings_option('design_update');
	$design_update_class = ($design_update == "2.X") ? "woffice-2-5" : "";

    /**
     * Filter to change the design version
     *
     * @param string $design_update_class - you can use "woffice-2-x"
     */
    $design_update_class = apply_filters('woffice_design_version', $design_update_class);

	$is_blank_template = woffice_is_current_page_using_blank_template();
	$blank_template_class = ($is_blank_template) ? 'is-blank-template' : '';


    /**
     * SEO hentry class applied to the container
     *
     * @param string
     */
    $hentry_class = apply_filters('woffice_hentry_class', 'hentry');
     // We add a class if the menu is closed by default
     $navigation_hidden_class = woffice_get_navigation_class();
	?>

	<!-- START BODY -->
	<body <?php body_class($menu_class . ' ' . $sidebar_show_class . ' ' . $extra_navbar_class .' '.$design_update_class . ' ' . $blank_template_class); ?>>

        <?php wp_body_open(); ?>

		<?php // If Unyson isn't enabled :
		if(!function_exists('fw_print')) :
			woffice_unyson_is_required();
		endif; ?>

		<?php
		
        if($theme_skin == 'modern') {
            get_template_part('header/header', 'modern');
        } else {
            get_template_part('header/header', 'classic');
        }