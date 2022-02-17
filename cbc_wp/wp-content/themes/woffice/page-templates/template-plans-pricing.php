<?php
/**
 * Template Name: Page: Plans and Pricing
 *
 * @package Woffice
 */


get_header();  
?>

	<div id="left-content">

		<?php  //GET THEME HEADER CONTENT
		woffice_title(get_the_title()); ?> 	

		<!-- START THE CONTENT CONTAINER -->
		<div id="content-container">
					<?php 
						// CUSTOM CLASSES ADDED BY THE THEME
						$post_classes = array('box','content');
					?>
			<!-- START CONTENT -->
			<div id="content">
				<article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>
						
					<div class="intern-padding">
						<?php the_content(); ?>
						<?php
						// CUSTOM CLASSES ADDED BY THE THEME
						$post_classes = array('box','content');
							if ( function_exists( 'astoundify_wpjmlp_get_job_packages' ) || function_exists( 'wc_paid_listings_get_user_packages' ) ) {
								$defaults = array(
									'before_widget' => '<aside class="woffice_widget_wcpl">',
									'after_widget'  => '</aside>',
									'before_title'  => '<div class="home-widget-section-title"><h3 class="home-widget-title">',
									'after_title'   => '%s</h3></div>',
									'widget_id'     => '',
								);

								the_widget(
									'Widget_Wp_Job_Manager_Wc_Paid_Listings',
									array(
										'title'       => '',
										'description' => '',
										'stacked'     => is_page_template( 'page-templates/template-plans-pricing-stacked.php' ),
									),
									$defaults
								);
							}
						?>
					</div>
				</article>
			</div>
				
		</div><!-- END #content-container -->

		<?php woffice_scroll_top(); ?>
		
	</div><!-- END #left-content -->

<?php 
get_footer(); 
