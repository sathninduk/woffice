<?php 

echo $before_widget;

/* Let's make things easier */
$ext_instance = fw()->extensions->get( 'woffice-birthdays' );

if(function_exists('woffice_bp_is_active') && woffice_bp_is_active( 'xprofile' ) ) {
?>
	<!-- WIDGET -->
	<div class="birthdays-container">
	
		<div class="birthdays-head">
			<i class="fa fa-birthday-cake"></i>
			<div class="intern-box box-title">
				<?php
				$birthdays    = $ext_instance->woffice_birthdays_get_array();
				$widget_title = $ext_instance->woffice_birthdays_title($birthdays);

				echo $widget_title;
				?>
			</div>
		</div>
		<ul class="birthdays-list">
			<?php
            /**
             * Before the list birthdays, in the birthdays widget
             */
			do_action('woffice_birthdays_widget_before_birthdays_list');

			$ext_instance->woffice_birthdays_content($birthdays);

            /**
             * After the list birthdays, in the birthdays widget
             */
			do_action('woffice_birthdays_widget_after_birthdays_list');

			?>
		</ul>
		
	</div>
<?php 
}	
echo $after_widget ?>