jQuery(document).ready(function(t){"use strict";window.gambitStartCSSAnimator=function(){var n=".unfold-3d-to-left, .unfold-3d-to-right, .unfold-3d-to-top, .unfold-3d-to-bottom, .unfold-3d-horizontal, .unfold-3d-vertical";t(n).each(function(){t(this).find(".unfolder-content").width(t(this).width())})},window.gambitStartCSSAnimator(),t(window).resize(window.gambitStartCSSAnimator),navigator.userAgent.match(/(Mobi|Android)/)&&t(".gambit-css-animation[data-enable_animator=nomobile]").each(function(){t(this).removeAttr("style"),t(this).removeClass("wpb_animate_when_almost_visible gambit-css-animation")})}),jQuery(document).ready(function(t){"undefined"!=typeof t.fn.waypoint&&t(".gambit-css-animation.wpb_animate_when_almost_visible").waypoint(function(){t(this).addClass("wpb_start_animation")},{offset:"90%",triggerOnce:!0})});