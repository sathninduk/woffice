<?php if (!defined('FW')) die( 'Forbidden' ); ?>
	
	<div class="infobox <?php echo esc_attr(woffice_convert_fa4_to_fa5($atts['icon'])); ?>" style="background-color: <?php echo esc_html($atts['color']); ?>;">
		<span class="infobox-head"><?php echo $atts['title']; ?></span>
		<?php echo $atts['content']; ?>
	</div>
