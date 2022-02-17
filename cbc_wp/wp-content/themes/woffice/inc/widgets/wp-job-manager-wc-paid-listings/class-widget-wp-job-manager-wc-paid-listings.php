<?php
// Creating the widget 
class Widget_Wp_Job_Manager_Wc_Paid_Listings extends WP_Widget {
  
    /**
	 * Register widget settings.
	 *
	 * @since 3.1.1
	 */
	public function __construct() {
		$this->widget_description = __( 'Display the pricing packages available for listings', 'woffice' );
		$this->widget_id          = 'woffice_widget_panel_wcpl_pricing_table';
		$this->widget_name        = __( 'Woffice - Page: Pricing Table', 'woffice' );
		
		parent::__construct($this->widget_id, $this->widget_name,$this->widget_description );
	}
      
        /**
         * Echoes the widget content.
         *
         * @since 3.1.1
         *
         * @param array $args     Display arguments including 'before_title', 'after_title',
         *                        'before_widget', and 'after_widget'.
         * @param array $instance The settings for the particular instance of the widget.
         */
        function widget( $args, $instance ) {
            extract( $args );

            $packages = $this->get_packages();
            $count    = count( $packages );

            if ( $count > 3 ) {
                $count = 3;
            }

            if ( ! $packages ) {
                return;
            }

            $title       = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
            $description = isset( $instance['description'] ) ? esc_attr( $instance['description'] ) : false;
            $stacked     = isset( $instance['stacked'] ) && 1 === (int) $instance['stacked'] ? true : false;

            if ( $description && strpos( $after_title, '</div>' ) ) {
                $after_title = str_replace( '</div>', '', $after_title ) . '<p class="home-widget-description">' . $description . '</p></div>';
            }

            $layout = 'inline';

            ob_start();

            echo sprintf('%1$s',$before_widget); // WPCS: XSS ok.

            if ( $title ) {
                $widget_title = $before_title . $title . $after_title;
                echo sprintf('%1$s',$widget_title); // WPCS: XSS ok.
            }

            // HTML Class.
            $packages_class = $stacked ? 'job-packages job-packages--stacked' : "job-packages job-packages--inline job-packages--count-{$count}";

            ?>
        
                <ul class="<?php echo esc_attr( $packages_class ); ?>">

                        <?php
                        foreach ( $packages as $package ) :
                            $product = wc_get_product( method_exists( $package, 'get_id' ) ? $package : $package->ID );
                            ?>

                            <?php
                            $tags       = wc_get_product_tag_list( $product->get_id() );
                            $action_url = add_query_arg( 'choose_package', $product->get_id(), job_manager_get_permalink( 'submit_job_form' ) );

                            // Dynamic HTML Classes.
                            // @todo: Style it using parent div instead of adding "stacked" class to each element.
                            $package_class  = $stacked ? 'job-package job-package--stacked' : 'job-package';
                            $tag_class      = $stacked ? 'job-package-tag job-package-tag--stacked' : 'job-package-tag';
                            $header_class   = $stacked ? 'job-package-header job-package-header--stacked' : 'job-package-header';
                            $title_class    = $stacked ? 'job-package-title job-package-title--stacked' : 'job-package-title';
                            $price_class    = $stacked ? 'job-package-price job-package-price--stacked' : 'job-package-price';
                            $purchase_class = $stacked ? 'job-package-purchase job-package-purchase--stacked' : 'job-package-purchase';
                            $includes_class = $stacked ? 'job-package-includes job-package-includes--stacked' : 'job-package-includes';
                            ?>

                    <li class="<?php echo esc_attr( $package_class ); ?>">
                            <?php if ( $tags ) : ?>
                            <span class="<?php echo esc_attr( $tag_class ); ?>"><span class="job-package-tag__text"><?php echo esc_attr( strip_tags( $tags ) ); ?></span></span>
                        <?php endif; ?>

                        <div class="<?php echo esc_attr( $header_class ); ?>">
                            <div class="<?php echo esc_attr( $title_class ); ?>">
                                <?php echo esc_attr( $product->get_title() ); ?>
                            </div>
                            <div class="<?php echo esc_attr( $price_class ); ?>">
                                <?php echo wp_kses_post($product->get_price_html()); // WPCS: XSS ok. ?>
                            </div>

                            <div class="<?php echo esc_attr( $purchase_class ); ?>">
                                <a href="<?php echo esc_url( $action_url ); ?>" class="btn btn-default"><?php esc_html_e( 'Get Started Now &rarr;', 'woffice' ); ?></a>
                            </div>
                        </div>

                        <div class="<?php echo esc_attr( $includes_class ); ?>">
                            <?php
                                $content = $product->get_description();
                                $content = (array) explode( "\n", $content );
                            ?>
                            <ul>
                                <li><?php echo implode( '</li><li>', $content ); // WPCS: XSS ok. ?></li>
                            </ul>
                        </div>

                        <div class="<?php echo esc_attr( $purchase_class ); ?>">
                            <a href="<?php echo esc_url( $action_url ); ?>" class="btn btn-default"><?php esc_html_e( 'Get Started Now &rarr;', 'woffice' ); ?></a>
                        </div>
                    </li>

                        <?php endforeach; ?>

                </ul>

            <?php
            echo sprintf('%1$s',$after_widget); // WPCS: XSS ok.

            echo apply_filters( $this->widget_id, ob_get_clean() ); // WPCS: XSS ok.
        }

        function update( $new_instance, $old_instance ) {
            return $new_instance;
        }

        function form( $instance ) {
            $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'description' => '', 'stacked' => 0 ));
    
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'woffice' ); ?> </label>
                <input type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                       value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat"
                       id="<?php esc_attr( $this->get_field_id( 'title' ) ); ?>"/>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php _e( 'Description', 'woffice' ); ?> </label>
                <input type="text" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>"
                       value="<?php echo esc_attr( $instance['description'] ); ?>" class="widefat"
                       id="<?php esc_attr( $this->get_field_id( 'description' ) ); ?>"/>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'stacked' ) ); ?>">
                    <input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'stacked' ) ); ?>"
                           id="<?php echo esc_attr( $this->get_field_id( 'stacked' ) ); ?>"
                           value="1"
                        <?php checked($instance['stacked'])?>
                    />
                    <?php _e( 'Use "stacked" display style', 'woffice' ); ?>
                </label>
            </p>
        <?php
        }

              
        /**
        * Find packagees available for purchase.
        *
        * @since 3.1.1
        *
        * @return array
        */
        private function get_packages() {
            $packages = array();

            if ( function_exists( 'astoundify_wpjmlp_get_job_packages' ) ) {
                add_filter( 'astoundify_wpjmlp_get_job_packages_args', array( $this, 'get_packages_filter' ) );
                $packages = astoundify_wpjmlp_get_job_packages();
            } elseif ( function_exists( 'wc_paid_listings_get_user_packages' ) ) {
                add_filter( 'wcpl_get_job_packages_args', array( $this, 'get_packages_filter' ) );
                $packages = WP_Job_Manager_WCPL_Submit_Job_Form::get_packages();
            }

            return apply_filters( 'listify_pricing_table_packages_results', $packages );
        }
          
        /**
         * Get Packages Filters.
         * This function added to maintain backward compatibility.
         * It's recommended to filter the plugin args directly.
         *
         * @since 3.1.1
         *
         * @param array $args Get packages args.
         * @return array
         */
        public function get_packages_filter( $args ) {
            return apply_filters( 'listify_pricing_table_packages', $args );
        }

    } 
     
     
    // Register and load the widget
    function woffice_listing_table_pricing_table() {
        woffice_register_new_widget('Widget_Wp_Job_Manager_Wc_Paid_Listings');
    }


    function add_to_cart_url( $url, $product ) {
		if ( ! ( is_page_template( 'page-templates/template-plans-pricing.php' ) ) ) {
			return $url;
		}

		if ( ! in_array( $product->product_type, array( 'subscription', 'job_package', 'job_package_subscription' ), true ) ) {
			return $url;
		}

		$submit = job_manager_get_permalink( 'submit_job_form' );

		if ( '' === $submit ) {
			return $url;
		}

		$url = add_query_arg( 'choose_package', $product->get_id(), $submit );

		return esc_url( $url );
	}

    if(function_exists('astoundify_wpjmlp_get_user_package')) {

        add_action( 'widgets_init', 'woffice_listing_table_pricing_table' );
        add_filter( 'woocommerce_product_add_to_cart_url', 'add_to_cart_url' , 10, 2 );
        
    }