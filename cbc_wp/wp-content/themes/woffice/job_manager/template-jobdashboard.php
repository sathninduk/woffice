<?php
/**
* Template Name: WP Job Manager Template (Woffice)
*/


get_header();  
?>

	<?php // Start the Loop.

    // We check for excluded categories
    
    /*If it's not a child only*/

	while ( have_posts() ) : the_post(); ?>

		<div id="left-content">

			<?php  //GET THEME HEADER CONTENT

			woffice_title(get_the_title()); ?> 	

			<!-- START THE CONTENT CONTAINER -->
			<div id="content-container">

				<!-- START CONTENT -->
				<div id="content">
					<?php if (woffice_is_user_allowed()) { ?>
						<?php 
						// CUSTOM CLASSES ADDED BY THE THEME
						$post_classes = array('box','content');
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>

							<div id="wiki-page-content" class="intern-padding">
								<?php 
								// THE PAGE CONTENT
								the_content();
								//DISABLED IN THIS THEME
								wp_link_pages(array('echo'  => 0));
								//EDIT LINK
								edit_post_link( __( 'Edit', 'woffice' ), '<span class="edit-link">', '</span>' );
								
								?>
							</div>
					
                    	</article>
						<?php if(is_user_logged_in()){?>
							<div class="woffice-job-manager-frontend-wrapper box intern-padding">

								<div class="center content" id="wp-jobmanager-bottom">
									<?php
									/**
									 * Filter the text of the button "Create a job"
									 *
									 * @param string
									 */
										if(isset($_GET['action']) && $_GET['action'] == 'edit') {
											return;
										}

										$new_job_button_text = apply_filters('woffice_wp_jobmanager_button_text', __("Create a job", "woffice"));
										
										if(isset($_GET['job_id']) && !empty($_GET['job_id'])) {
											$new_job_button_text = apply_filters('woffice_wp_jobmanager_button_text', __("Edit job", "woffice"));
										}
									?>
									<a href="javascript:void(0)" class="btn btn-default woffice-job-manager-frontend-wrapper__toggle" data-action="display" id="show-jobmanager-create">
										<i class="fa fa-plus-square"></i> <?php echo esc_html($new_job_button_text); ?>
									</a>
								</div>

								<div id="job-post-create" class="intern-padding woffice-job-manager-frontend-wrapper__content">
									<?php echo do_shortcode('[submit_job_form]'); ?>
								</div>

							</div>
					<?php
					} }
					?>
				</div>
					
			</div><!-- END #content-container -->
		
			<?php woffice_scroll_top(); ?>

		</div><!-- END #left-content -->

	<?php // END THE LOOP 
	endwhile; ?>

<?php 
get_footer();



