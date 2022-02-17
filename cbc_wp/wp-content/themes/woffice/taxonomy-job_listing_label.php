<?php
/**
* Archive page for the directory
*/
get_header();  
?>

	<div id="left-content">

		<?php  //GET THEME HEADER CONTENT
		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$title =  $term->name . __(' Archives','woffice'); 
		woffice_title($title); ?> 

		<!-- START THE CONTENT CONTAINER -->
		<div id="content-container">

			<!-- START CONTENT -->
			<div id="content">
				
				<?php /* If the directory extension is one we display the items */
				if(defined('ASTOUNDIFY_WPJMLL_VERSION')){
					
					if ( have_posts() ) : 
					
						echo '<div id="lising-label" class="masonry-layout">';
						
						while(have_posts()) : the_post();
						
							echo '<div class="box lising-label-item">';
								/* Featured Image */
								if ( has_post_thumbnail() ) :
                                    Woffice_Frontend::render_featured_image_single_post($post->ID, '', true);
								endif; 
								/* Content */
								echo '<div class="intern-padding">';
									/* Title */
									echo'<div class="intern-box box-title">
										<h3><a href="'. get_the_permalink() .'">'.get_the_title().'</a></h3>
									</div>';
									/* Excerpt */
									wpjm_the_job_description();
								?>
								<div class="lising-label-item-meta">
									<ul class="job-listing-meta meta">
										<?php do_action( 'single_job_listing_meta_start' ); ?>

										<?php if ( get_option( 'job_manager_enable_types' ) ) { ?>
											<?php $types = wpjm_get_the_job_types(); ?>
											<?php if ( ! empty( $types ) ) : foreach ( $types as $type ) : ?>
												<li class="lising-label-item-field job-type <?php echo esc_attr( sanitize_title( $type->slug ) ); ?>">
													<i class='fa fa-briefcase'></i>
													<span><?php echo esc_html( $type->name ); ?></span>
												</li>

											<?php endforeach; endif; ?>
										<?php } ?>

										<li class="lising-label-item-field location">
											<i class="fa fa-map-marker"></i>
											<span><?php the_job_location(); ?></span>
										</li>

										<li class="lising-label-item-field date-posted">
											<i class="fa fa-calendar"></i>
											<span><?php the_job_publish_date(); ?></span>
										</li>

										<?php if ( is_position_filled() ) : ?>
											<li class="lising-label-item-field position-filled"><?php _e( 'This position has been filled', 'woffice' ); ?></li>
										<?php elseif ( ! candidates_can_apply() && 'preview' !== $post->post_status ) : ?>
											<li class="lising-label-item-field listing-expired"><?php _e( 'Applications have closed', 'woffice' ); ?></li>
										<?php endif; ?>

										<?php do_action( 'single_job_listing_meta_end' ); ?>
									</ul>
								</div>
								<?php
									echo '</div>';
								
							echo '</div>';
					
						endwhile;
								
						wp_reset_postdata();
						
						echo '</div>';
                        woffice_paging_nav();
					endif; 
					
				} else {
					
					get_template_part( 'content', 'none' );
					
				} ?>
				
			</div>
				
		</div><!-- END #content-container -->
	
		<?php woffice_scroll_top(); ?>

	</div><!-- END #left-content -->

<?php 
get_footer();
