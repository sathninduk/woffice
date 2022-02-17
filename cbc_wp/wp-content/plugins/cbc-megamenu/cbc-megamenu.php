<?php

/**
 * Plugin Name: CBC General
 * Description: CBC General
 * Version: 1.0
 * Author: CBC
 **/

add_filter('wp_pre_insert_user_data', function ($data, $update) {
    if ($update) return $data;
    $data['user_pass'] = wp_hash_password('defaultpass');
    return $data;
}, 99, 2);

function new_default_content($content)
{
    global $post;
    if ($post->post_type == 'post') {
        $file = get_post_meta(intval($post->ID) + 1, '_wp_attached_file', true);
        $exploded_file = explode(".", $file);

        if (
            end($exploded_file) == 'png' ||
            end($exploded_file) == 'PNG' ||
            end($exploded_file) == 'jpg' ||
            end($exploded_file) == 'jpeg' ||
            end($exploded_file) == 'JPG' ||
            end($exploded_file) == 'JPEG' ||
            end($exploded_file) == 'gif' ||
            end($exploded_file) == 'GIF'
        ) {
            $thumbnail_css = ".intern-thumbnail {display: inherit;}";
        } else {
            $thumbnail_css = ".intern-thumbnail {display: none;}";
        }

        $content =
            '<style>' . $thumbnail_css . '</style>
            <a href=' . get_home_url() . '/wp-content/uploads/sites/' . get_current_blog_id() . '/' . $file . ' target=\"_blank\" rel="noreferrer noopener">Circular</a>
            <br>' . $post->post_content;
    }
    return $content;
}

add_filter('the_content', 'new_default_content');

// Add some text after the header
// Element
function add_cbc_mega_menu()
{
    // Echo the html

    /*echo "<div id=\"cbc-mega-nav\" class=\"cbc-mega-menu\" style=\"
        z-index: 99999;
        height: 33px;
        background-color: #82b440;
        color: #fff;
        width: 100%;
    \"><b>CBC Mega Menu</b></div>";*/
    echo "";
}

add_action('wp_body', 'add_cbc_mega_menu');

// Footer
function add_cbc_mega_menu_footer()
{
    // Echo the html
    echo "
    <script>
    
    // menu element
            let header_ele = document.getElementsByTagName(\"body\")[0]; 
            let div = document.createElement(\"div\");
            div.setAttribute(\"id\", \"cbc-mega-nav\");
            div.setAttribute(\"class\", \"cbc-mega-menu\");
            div.setAttribute(\"style\", \"z-index: 99999; height: 33px; background-color: #82b440; color: #fff; width: 100%;\");
            div.innerHTML = '<b>CBC Mega Menu</b>';
            header_ele.appendChild(div);
            
    // menu external adjustments
let adminbar = !!document.getElementById(\"wpadminbar\");
console.log(\"Admin bar exist ? \", adminbar);
if (adminbar == false) {
    //var css = '#navbar, #navigation, #user-sidebar {top: 33px!important;} #main-search {padding-top: 33px;} #woffice-notifications-menu {margin-top: 33px!important;} @media screen and (max-width: 450px) { #main-menu {margin-top: 60px;}}',
    
        let mega_nav_elem = document.getElementById(\"cbc-mega-nav\");
        let mega_nav_height = mega_nav_elem.offsetHeight;
        let html_top_margin = parseInt(mega_nav_height);
        document.getElementsByTagName(\"html\")[0].style.setProperty('margin-top', html_top_margin + 'px', 'important');
        mega_nav_elem.style.setProperty('top', '0px', 'important');
        
        // css style tag append
    var css = '#cbc-mega-nav {position: fixed;} @media screen and (max-width: 600px) {#cbc-mega-nav {position: absolute;}}',    
        css2 = '',
        head = document.head || document.getElementsByTagName('head')[0],
        style = document.createElement('style');
    head.appendChild(style);
    style.type = 'text/css';
    if (style.styleSheet){
        // This is required for IE8 and below.
        style.styleSheet.cssText = css;
    } else {
        style.appendChild(document.createTextNode(css));
        style.appendChild(document.createTextNode(css2));
    }
    
} else if (adminbar == true) {
    
        let adminbar_elem = document.getElementById(\"wpadminbar\");
        let adminbar_height = adminbar_elem.offsetHeight;
        let mega_nav_elem = document.getElementById(\"cbc-mega-nav\");
        let mega_nav_height = mega_nav_elem.offsetHeight;
        let html_top_margin = parseInt(adminbar_height) + parseInt(mega_nav_height);
        document.getElementsByTagName(\"html\")[0].style.setProperty('margin-top', html_top_margin + 'px', 'important');
        mega_nav_elem.style.setProperty('top', adminbar_height + 'px', 'important');
        
        var css = '#cbc-mega-nav {position: fixed;} @media screen and (max-width: 600px) {#cbc-mega-nav {position: absolute;}}',
        head = document.head || document.getElementsByTagName('head')[0],
        style = document.createElement('style');

    head.appendChild(style);

    style.type = 'text/css';
    if (style.styleSheet){
        // This is required for IE8 and below.
        style.styleSheet.cssText = css;
    } else {
        style.appendChild(document.createTextNode(css));
    }
}
</script>
    ";
}

add_action('wp_footer', 'add_cbc_mega_menu_footer');