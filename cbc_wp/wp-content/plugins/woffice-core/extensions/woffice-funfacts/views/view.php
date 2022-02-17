<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }

echo $before_widget;

echo $title;
?>
	<!-- WIDGET -->
	<div class="flexslider">
		<ul class="slides">
			<?php 
			foreach($funfacts as $value) {
				echo'<li class="'.esc_attr(woffice_convert_fa4_to_fa5($value['fact_icon'])).'">';
				echo '<p>'.sanitize_text_field($value['fact_content']).'</p>';
				echo'</li>';
			}
			?>
		</ul>
	</div>
<?php echo $after_widget ?>