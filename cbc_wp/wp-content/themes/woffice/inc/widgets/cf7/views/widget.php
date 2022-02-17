<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }

echo sprintf('%1$s',$before_widget);

echo wp_kses_post($title);
?>
	<!-- WIDGET -->
	<?php 
    	$widget_text = empty($instance['form']) ? '' : stripslashes($instance['form']);
		echo do_shortcode('[contact-form-7 id="' . $widget_text . '"]');
	?>
	
<?php echo sprintf('%1$s',$after_widget); ?>