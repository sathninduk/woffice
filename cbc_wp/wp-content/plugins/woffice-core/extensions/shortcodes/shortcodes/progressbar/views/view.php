

	<div class="progress project-progress  shortcode-progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $atts['progress']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $atts['progress']; ?>%">
			<!-- <span class="progress-start">'.$project_date_start.'</span> -->
			<span class="progress-current">
				<i class="<?php echo esc_attr(woffice_convert_fa4_to_fa5($atts['icon'])); ?>"></i> <?php echo $atts['progress']; ?> %
			</span>
			<!-- <span class="progress-end">'.$project_date_end.'</span> -->
		</div>
	</div>