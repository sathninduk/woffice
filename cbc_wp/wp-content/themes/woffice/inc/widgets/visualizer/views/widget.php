<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }

echo sprintf('%1$s',$before_widget);

echo wp_kses_post($title);
?>
	<!-- WIDGET -->
	<?php 
    	$widget_text = empty($instance['graph']) ? '' : stripslashes($instance['graph']);
		echo apply_filters('widget_text','[visualizer id="' . $widget_text . '"]');
	?>
	
<?php echo sprintf('%1$s',$after_widget) ?>