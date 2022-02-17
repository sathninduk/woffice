<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }

echo sprintf('%1$s',$before_widget);

echo wp_kses_post($title);
?>

	<!-- WIDGET -->
	<?php 
    	woffice_language_switcher();
	?>
	
<?php echo sprintf('%1$s',$after_widget); ?>