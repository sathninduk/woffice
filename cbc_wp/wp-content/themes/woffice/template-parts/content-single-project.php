<?php

global $process_result;
global $post;

$project_date_start = woffice_get_post_option( $post->ID, 'project_date_start');
$project_date_end = woffice_get_post_option( $post->ID, 'project_date_end');
$project_completion_date = false;

$project_progress = woffice_get_post_option( $post->ID, 'project_progress');
if ($project_progress == "tasks" && woffice_projects_percentage() == 100) {
	$completion_date_timestamp = woffice_get_post_option( $post->ID, 'project_date_end', 'fw_option:completion_date' );
	$project_completion_date = date( get_option('date_format', strtotime($completion_date_timestamp)) );
}

// GET THE TERMS
$project_terms = get_the_term_list( $post->ID, 'project-category', '', ', ' );

// GET MEMBERS
$project_members = woffice_get_project_members( $post->ID );

// GET THE LINKS
$project_links = woffice_get_post_option( $post->ID, 'project_links');

$is_archived = (bool)woffice_get_post_option( $post->ID, 'project_completed', false);

$project_edit = woffice_get_post_option( $post->ID, 'project_edit');

$post_classes = array('box', 'content', 'woffice-tab-layout');
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>
	<?php if ( has_post_thumbnail()) : ?>
		<!-- THUMBNAIL IMAGE -->
		<?php Woffice_Frontend::render_featured_image_single_post($post->ID) ?>

	<?php endif; ?>
	<div id="project-nav" class="intern-box">
		<div class="item-list-tabs-project">
			<?php
			if(class_exists('WooCommerce') && class_exists('WofficeCustomPostTypesupportforWooCommerce')){
				if(WC_CPT_List::is_active('project')){
			?>
			<div class="woffice-project-sub">
			<?php
				global $product;
				$price_html = $product->get_price_html();
				$productid = $product->get_id();
				$user_id = get_current_user_id();
				$assigned_project_members = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option(get_the_ID(), 'project_members') : '';
				$is_subscription_enabled = (function_exists('wowcps_is_subscription_product')) ? wowcps_is_subscription_product($post->ID) : false;
				$is_subscirbed = (function_exists('wowcps_customer_has_validsubscription_product')) ? wowcps_customer_has_validsubscription_product($post->ID) : false;
				$is_purchasable = $product->is_purchasable();
				$has_pruchased = wc_customer_bought_product('dev1.techeshta@gmail.com',$user_id,$productid);
				
				if(isset($_POST['add-to-cart'])){
					wc_print_notices();
					wc_clear_notices();
				} 

				if(class_exists('WOFFICE_SUBSCRIPTION')) {
					if($is_purchasable && !$is_subscription_enabled && !$has_pruchased) {
						echo sprintf('%s',$price_html,'woffice');
				?>
				<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
					<?php
						do_action( 'woocommerce_before_add_to_cart_button' );
						do_action( 'woocommerce_before_add_to_cart_quantity' );
						do_action( 'woocommerce_after_add_to_cart_quantity' );
					?>
					<input type="hidden" class="input-text qty text" step="1" min="1" max="" name="quantity" value="1" title="Qty">
					<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

					<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
				</form>		
				<?php	
					} else if($is_purchasable && !$is_subscription_enabled && $has_pruchased) {
						echo sprintf(__('You have purchased this project with %s','woffice'),$price_html);
					} else if($is_purchasable && $is_subscription_enabled && $is_subscirbed == 'active' || $assigned_project_members){
						echo sprintf(__('Subscription is enabled for this project with %s','woffice'),$price_html);
					} else if($is_purchasable && $is_subscription_enabled && $is_subscirbed == 'active'){
						echo sprintf(__('Your subscription for this project with %s','woffice'),$price_html);
					} else if($is_purchasable && $is_subscription_enabled ) {
						echo sprintf('%s',$price_html,'woffice');
				?>
					<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
						<?php
							do_action( 'woocommerce_before_add_to_cart_button' );
							do_action( 'woocommerce_before_add_to_cart_quantity' );
							do_action( 'woocommerce_after_add_to_cart_quantity' );
						?>
						<input type="hidden" class="input-text qty text" step="1" min="1" max="" name="quantity" value="1" title="Qty">
						<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

						<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
					</form>	
				<?php
						return;
					}
				} else if( $is_purchasable && $has_pruchased) {
					esc_html_e('You have purchased this Project','woffice');
				} else if( $is_purchasable && !$has_pruchased) {
					echo sprintf('%s',$price_html);
				?>
				<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
					<?php
						do_action( 'woocommerce_before_add_to_cart_button' );
						do_action( 'woocommerce_before_add_to_cart_quantity' );
						do_action( 'woocommerce_after_add_to_cart_quantity' );
					?>
					<input type="hidden" class="input-text qty text" step="1" min="1" max="" name="quantity" value="1" title="Qty">
					<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

					<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
				</form>
				<?php	
				}
			?>
			</div> 
			<?php
				}
			}
			if (function_exists('woffice_get_project_menu')){
				echo woffice_get_project_menu($post);
			}
			?>
		
	</div>

	<div class="project-tabs-wrapper woffice-tab-layout__content intern-padding">
                                  
        <div id="project-content-view" class="woffice-tab-layout__tab" data-tab="view">

            <header id="project-meta" class="border-0 rounded">

				<?php //GET THE PROGRESS BAR
				woffice_project_progressbar();
				?>

                <div class="row">

					<?php
					/**
					 * Before all the project meta columns, in the single project page
					 */
					do_action('woffice_before_project_meta'); ?>

					<?php if (
						!empty($project_terms)
						|| !empty($project_date_start)
						|| !empty($project_date_end)
						|| !empty($project_completion_date)
					): ?>

                        <div class="col-md-4">
                            <ul class="project-meta-list">

								<?php
								if( $is_archived ) {
									echo '<li class="project-meta-archived">';
									echo esc_html__( 'Archived', 'woffice' );
									echo '</li>';
								}
								?>

								<?php if(!empty($project_date_start) || !empty($project_date_end)): ?>
                                    <li class="project-meta-date">
										<?php
										if( !empty($project_date_start) )
											echo date_i18n(get_option('date_format'),strtotime(esc_html($project_date_start)));

										if( !empty($project_date_start) && !empty($project_date_end))
											echo ' - ';

										if( !empty($project_date_end) )
											echo date_i18n(get_option('date_format'),strtotime(esc_html($project_date_end)));

										if( $project_completion_date )
											echo '<p>' . esc_html__('Completed on: ', 'woffice') . $project_completion_date . '</p>'
										?>
                                    </li>
								<?php endif; ?>

								<?php if (!empty($project_terms)): ?>
                                    <li class="project-meta-category">
										<?php echo get_the_term_list( $post->ID, 'project-category', '', ', ' ); ?>
                                    </li>
								<?php endif; ?>

	                            <?php
                                $project_status = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option(get_the_ID(), 'project_current_status') : 'planned';
	                            if ($project_status == 'archived') {
		                            $project_status_icon = 'fa-archive';
		                            $project_status_label = __('Archived','woffice');
	                            } else if ($project_status == 'done') {
		                            $project_status_icon = 'fa-check-square';
		                            $project_status_label = __('Done','woffice');
	                            } else if ($project_status == 'in_progress') {
		                            $project_status_icon = 'fa-sync';
		                            $project_status_label = __('In progress','woffice');
	                            } else if ($project_status == 'in_review') {
		                            $project_status_icon = 'fa-cog';
		                            $project_status_label = __('In review','woffice');
	                            } else {
		                            $project_status_icon = 'fa-book';
		                            $project_status_label = __('Planned','woffice');
	                            }
                                ?>
	                            <?php echo '<span class="project-status badge badge-pill '. esc_attr($project_status) . '"><i class="fa '. esc_attr($project_status_icon) .' pr-2"></i>'. esc_html($project_status_label) . '</span>'; ?>
                            </ul>
                        </div>
					<?php endif; ?>

					<?php if (!empty($project_members)): ?>
                        <div class="col-md-4">
                            <ul class="project-meta-list">
                                <li class="project-meta-users"><?php _e("Project's Members","woffice"); ?></li>
                            </ul>
                            <div class="project-members">
								<?php
								/**
								 * In the single project page, this is performed before the list of project members
								 */
								do_action('woffice_before_project_meta_members');

								foreach($project_members as $project_member) {
									if (function_exists('bp_is_active')):
										$user_info = get_userdata($project_member);
										if (!empty($user_info->display_name)){
											$name = woffice_get_name_to_display($user_info);
											echo'<a href="'. esc_url(bp_core_get_user_domain($project_member)) .'" title="'. $name .'" data-toggle="tooltip" data-placement="top">';
											echo get_avatar($project_member);
											echo'</a>';
										}
										else {
											echo'<a href="'. esc_url(bp_core_get_user_domain($project_member)) .'">';
											echo get_avatar($project_member);
											echo'</a>';
										}
									else :
										echo get_avatar($project_member);
									endif;
								}

								/**
								 * In the single project page, this is performed after the list of project members
								 */
								do_action('woffice_after_project_meta_members');
								?>
                            </div>
                        </div>
					<?php endif; ?>

					<?php if (!empty($project_links)): ?>
                        <div class="col-md-4">
                            <ul class="project-meta-list">
                                <li  class="project-meta-links"><?php _e("Project's Links","woffice"); ?></li>
                            </ul>

							<?php
							/**
							 * In the single project page, this is performed before the list of project links
							 */
							do_action('woffice_before_project_meta_links'); ?>

                            <ul id="project-links">
								<?php
								foreach($project_links as $project_link){
									echo'<li><a href="'.esc_url($project_link['href']).'" target="_blank">';
									echo'<i class="' .esc_attr(woffice_convert_fa4_to_fa5($project_link['icon'])) .'"></i> '. esc_html($project_link['title']);
									echo'</a></li>';
								}
								?>
                            </ul>

							<?php
							/**
							 * In the single project page, this is performed after the list of project links
							 */
							do_action('woffice_after_project_meta_links'); ?>

                        </div>
					<?php endif; ?>

					<?php
					/**
					 * After all the project meta columns, in the single project page
					 */
					do_action('woffice_after_project_meta'); ?>

                </div>
            </header>

            <div class="intern-padding">
				<?php the_content(); ?>
            </div>
        </div>

		<?php if ( $project_edit == 'frontend-edit' && woffice_current_user_can_edit_project(get_the_ID()) ) : ?>
			<div id="project-content-edit" class="woffice-tab-layout__tab" data-tab="edit">
				<?php Woffice_Frontend::frontend_render('project', $process_result, get_the_ID()); ?>
			</div>
		<?php endif; ?>

		<?php $project_todo = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option(get_the_ID(), 'project_todo') : '';
		if($project_todo): 
			woffice_add_todo_tabs();
		?>
			<div id="project-content-todo" class="woffice-tab-layout__tab extratabs-item" data-tab="todo" data-extratab="todo">
				<?php woffice_projects_todo($post); ?>
			</div>
			<?php if(class_exists('WOKSS_KANBAN')) { ?>
				<div id="project-content-kanban" class="pt-4 woffice-tab-layout__tab extratabs-item" data-extratab="kanban">
					<?php  woffice_projects_kaban_tab(); ?>
				</div>
			<?php } ?>
			<?php if(class_exists('Woffice_Timeline')) { ?>
				<div id="project-content-timeline" class="pt-5 woffice-tab-layout__tab extratabs-item" data-extratab="timeline">
					<div class="col-md-3 woffice-timeline-select pl-0">
						<select class="woffice-timeline-view">
							<option value=""><?php _e('Select Timeline View','woffice');?></option>
							<option value="by_task"><?php _e('By Task','woffice');?></option>
							<option value="by_team"><?php _e('By Team','woffice');?></option>
						</select>
					</div>
					<?php woffice_projects_timeline_tab(); ?>
				</div>
			<?php } ?>
		<?php endif; ?>

		<!-- SEE THE FILES-->
		<div id="project-content-files" class="woffice-tab-layout__tab" data-tab="files">
			<?php
			//Subdir fix
			if(isset($_GET['drawer']) && strpos('projects_', $_GET['drawer']) === FALSE ) { ?>
				<script>
					(function($){
						if(!window.location.hash) {
							location.href = window.location.href + '#project-content-files';
						}
					})(jQuery);
				</script>
				<?php
			}
			?>
			<?php // IF THERE IS FILES
			$project_files = ( function_exists( 'fw_get_db_post_option' ) && defined('fileaway')) ? fw_get_db_post_option(get_the_ID(), 'project_files') : '';
			if(!empty($project_files)):

				if (defined('fileaway')):
					$post_slug = $post->post_name;
					woffice_projects_fileway_manager($post_slug);
				else :
					$post_slug = $post->post_name;
					$the_terms = get_term_by( 'slug', $post_slug, 'multiverso-categories');
					$first = true;
					foreach ($the_terms as $term):
						if (!empty($term) && $first):
							echo do_shortcode('[mv_single_category id='.$term.' team="false"]');
							woffice_mv_managefiles_projects($term);
							$first = false;
						endif;
					endforeach;
				endif;
			endif; ?>
		</div>

		<div id="project-content-comments" class="woffice-tab-layout__tab" data-tab="comments">
			<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( (comments_open() || get_comments_number()) && woffice_projects_have_comments()) {
				comments_template();
			}
			else {
				_e("Comments are closed...","woffice");
			}
			?>
		</div>
        <?php
        $project_calendar = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option(get_the_ID(), 'project_calendar') : '';
        if ($project_calendar === true && fw_ext('woffice-event')):
        ?>
        <div id="project-content-calendar" class="woffice-tab-layout__tab" data-tab="project-content-calendar">
            <?php
              echo do_shortcode('[woffice_calendar visibility="project" id="'. get_the_ID() .'"]');
            ?>
        </div>
        <?php endif?>

	</div>
</article> 