/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* FIXED NAVIGATION
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
(function($) {
    "use strict";
    // EXISTS ALSO IN SCRIPT.JS
    function UserSidebar() {
		var topbarHeight = $("#topbar").height(),
		 menuHeight = $("#navbar").height(),
		 sidebarTop = 0;
        if($("topbar").hasClass("topbar-closed")){
            sidebarTop = menuHeight + topbarHeight;
        }
        else{
            sidebarTop = menuHeight;
        }
	    $("#user-sidebar").css("padding-top",sidebarTop);
    }

    $(window).bind('load', function(){
        fixedMenuPosition();
    });

    $(window).bind('scroll', function(){
        fixedMenuPosition();
    });

    $(window).bind( 'resize', function () {
        fixedMenuPosition();
    });

    function fixedMenuPosition() {
        var scroll = $(window).scrollTop(),
            menu_is_horizontal = $('body').hasClass('menu-is-horizontal'),
            adminbar_height = parseInt($("html").css("margin-top").replace("px", "")),
            $navbar = $("#navbar"),
            $navigation = $("#navigation"),
            $is_modern_skin = $('body').hasClass("is-modern-skin");
        
        var height = $('#main-header').height();
        if(height === 0)
            height = $navbar.height();
        if(menu_is_horizontal)
            height += $navigation.height() + 50;

        var animation_classes = 'animate-me animated slideInDown';
        if($is_modern_skin) { animation_classes = 'animate-me animated fadeIn'; }
        if($(window).width() <= 600) {

            var $body = $('body');
            if( $body.hasClass( 'has_fixed_navbar' ) )
                $body.addClass( 'has-navigation-fixed' );
            else
                $body.removeClass( 'has-navigation-fixed' );

            animation_classes = '';

            var top_scroll = (adminbar_height - scroll > 0) ? (adminbar_height - scroll) : 0;
            $navbar.css('top', top_scroll);

            var navbar_plus = 0;
            if($(window).width() <= 450)
                navbar_plus = $navbar.height();

            if(scroll > adminbar_height) {
                //addFixedMenu(menu_is_horizontal, animation_classes);
                
                if(menu_is_horizontal)
                    $navigation.css('top',$navbar.height());
                else
                    $navigation.css('top',parseInt(navbar_plus + top_scroll));
            } else {
                //removeFixedMenu(menu_is_horizontal);

                if(menu_is_horizontal)
                    $navigation.css('top',parseInt( $navbar.height() + adminbar_height - scroll));
                else
                    $navigation.css('top',parseInt(navbar_plus + adminbar_height - scroll));
            }
        } else {

            $navbar.css('top', adminbar_height);
    
            if(menu_is_horizontal)
                $navigation.css('top',parseInt( $navbar.height() + adminbar_height ));
            else
                $navigation.css('top', adminbar_height);

            // todo Should this be actually removed? Now the fixed menu is set on page loading
            if(scroll >= height + 50 ) {
                addFixedMenu(menu_is_horizontal, animation_classes);
            } else if(scroll <= 0){
                removeFixedMenu(menu_is_horizontal);
            }
        }

    }

    function addFixedMenu(is_horizontal, animation){
        $('body.has-navigation-fixed .modern-top-menu').addClass('modern-is-fixed');
        var $nav_width = $('#navigation').width();
        $('body.has-navigation-fixed .menu-is-vertical .modern-is-fixed').css({'margin-left' : $nav_width + 'px'});
        $("#navbar").addClass('navigation-fixed ' + animation);
        if (is_horizontal)
            $("#navigation").addClass('navigation-fixed ' + animation);
        $("body").addClass('has-navigation-fixed');
        $("#user-sidebar").addClass('onscroll');
        if ($("#user-sidebar").length > 0) {
            UserSidebar();
        }
    }

    function removeFixedMenu(is_horizontal,$is_modern_skin) {
        $('body.has-navigation-fixed .modern-top-menu').removeClass('modern-is-fixed');
        $("#navbar").removeClass('navigation-fixed animated slideInDown fadeIn');
        if(is_horizontal)
            $("#navigation").removeClass('navigation-fixed animated slideInDown fadeIn');
        if($is_modern_skin) {
            $("#navigation").removeClass('navigation-fixed animated fadeIn');
        }
        $("body").removeClass('has-navigation-fixed');
        $("#user-sidebar").removeClass('onscroll');
        if($("#user-sidebar").length >0 ){
            UserSidebar();
        }
    }
})(jQuery);