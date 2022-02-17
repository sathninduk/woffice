<?php
/**
 * Pulls posts from a specified posttype.
 *
 * @package	Carousel Anything for VC
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
// Initializes plugin class.
if ( ! class_exists( 'GambitCarouselPosts' ) ) {

	/**
	 * Does all the heavy lifting to pull posts.
	 */
	class GambitCarouselPosts {

		/**
		 * Sets a unique identifier of each carousel posts.
		 *
		 * @var id
		 */
	    private static $id = 0;

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Initializes VC shortcode.
			add_filter( 'init', array( $this, 'create_cp_shortcode' ), 999 );

			// Render shortcode for the plugin.
			add_shortcode( 'carousel_posts', array( $this, 'render_cp_shortcode' ) );

			// Enqueues scripts and styles specific for all parts of the plugin.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts_and_css' ), 5 );
		}


		/**
		 * Pulls a list of options with dependencies related to posttypes and taxonomies.
		 *
		 * @return	$posttypes
		 * @since	1.5
		 */
		function generate_posttype_options() {
			// Initialize option.
			$tax_dependency = array();
			$term_dependency = array();
			$the_terms = array();
			$taxonomy_names = array();
			$all_taxonomies = array();
			$taxonomy_label = array();
			$all_terms = array();

			// First, pull all post types.
			$post_types = $this->get_post_type( 'array' );

			// Now pull their taxonomies, using the array generated.
			foreach ( $post_types['slug'] as $post_type ) {

				// This does the dirty work of getting the taxonomies, it returns as an array object.
				$taxonomy_names[ $post_type ] = get_object_taxonomies( $post_type );

				// No taxonomy found? Terminate.
				if ( ! is_array( $taxonomy_names[ $post_type ] ) ) {
					break;
				}

				// Now parse the taxonomy contents.
				if ( ! empty( $taxonomy_names[ $post_type ] ) ) {
					foreach ( $taxonomy_names[ $post_type ] as $taxonomy_name ) {
						$all_taxonomies[] = $taxonomy_name;
						$tax = get_taxonomy( $taxonomy_name );

						// Store the Taxonomy name so we have a human-readable name for the VC option later.
						$taxonomy_label[ $taxonomy_name ] = $tax->labels->name;

						// Populate dependency data.
						$tax_dependency[ $taxonomy_name ][] = $post_type;
					}
				}

				// Iterate through the collected taxonomy and terms and now print them all.
				if ( count( $taxonomy_names[ $post_type ] ) > 0 ) {
					// Initialize the array for collecting the terms selectable.
					$all_terms[ $post_type ] = array();
					foreach ( $taxonomy_names[ $post_type ] as $taxonomy_name ) {
						// Does the heavy lifting of getting all the terms in a given taxonomy.
						$the_term_names[ $post_type ] = $this->get_terms( $taxonomy_name );
						// The temporary placeholder for collected terms.
						$names_now[ $post_type ] = array();
						if ( count( $the_term_names[ $post_type ] ) > 0 ) {
							// Apply the default selection ONLY if there's a term to print.
							$names_now[ $post_type ] = array( __( 'All Categories', GAMBIT_CAROUSEL_ANYTHING ) => 'all' );
							foreach ( $the_term_names[ $post_type ] as $key => $value ) {
							    $key .= ' (' . $taxonomy_label[ $taxonomy_name ] . ')';
								$value = $taxonomy_name . '|' . $value;
								$names_now[ $post_type ][ $key ] = $value;
							}
							// This collects all the terms from separate taxonomies into a unified array identified by post type.
							$all_terms[ $post_type ] = array_merge( $all_terms[ $post_type ], $names_now[ $post_type ] );
						}
					}

					// Makes sure no duplicate terms get printed.
					$all_terms[ $post_type ] = array_unique( $all_terms[ $post_type ] );

					// Now print out the option ONLY if there's terms to use.
					if ( count( $all_terms[ $post_type ] ) > 0 ) {

						$output[] = array(
							'type' => 'dropdown',
							'heading' => 'Post Category',
							'param_name' => 'taxonomy_' . $post_type,
							'value' => $all_terms[ $post_type ],
		                    'description' => __( 'Choose a Category', GAMBIT_CAROUSEL_ANYTHING ),
							'dependency' => array(
								'element' => 'posttype',
								'value' => $tax_dependency[ $taxonomy_name ],
							),
							'group' => __( 'Contents', GAMBIT_CAROUSEL_ANYTHING ),
						);
					}
				}
			}
			return $output;
		}


		/**
		 * Limits wordcount.
		 *
		 * @param string $string is the target of limiting.
		 * @param number $offset details where to start. Needed by array_splice.
		 * @param number $word_limit tells how many words should be retained.
		 * @return string of subtracted words
		 * @since 1.6
		 */
		function limit_words( $string, $offset = 0, $word_limit = 0 ) {
			if ( $offset == $word_limit ) {
				return $string;
			}

		    $words = explode( ' ',$string );
		    $out = implode( ' ', array_splice( $words, $offset, $word_limit ) );
			return $out;
		}


		/**
		 * Strip shortcodes, even inactive ones.
		 *
		 * @param string $in - The input.
		 * @return string - The output, now stripped of all shortcodes.
		 */
		function strip_all_shortcodes( $in ) {
			$process = strip_shortcodes( $in );
			$pattern = '/\[.*\]/';
			return preg_replace( '/\[.*\]/', '$2 $1', $in );
		}


		/**
		 * Counts words.
		 *
		 * @param string $string is the subject of counting.
		 * @return number of words given.
		 * @since 1.6
		 */
		function count_words( $string ) {
		    $words = explode( ' ',$string );
		    return count( $words );
		}


		/**
		 * Pulls a list of terms with their Taxonomies
		 *
		 * @param string $taxonomy - A needed argument for getting the terms by get_terms.
		 * @return array $posttypes - Parsable in Visual Composer.
		 * @since 1.5
		 */
		function get_terms( $taxonomy ) {
			$output = array();

			$terms = get_terms( $taxonomy, array(
				'parent' => 0,
				'hide_empty' => false,
			) );

			if ( is_wp_error( $terms ) ) {
				return $output;
			}

			foreach ( $terms as $term ) {

				$output[ $term->name ] = $term->slug;
				$term_children = get_term_children( $term->term_id, $taxonomy );

				if ( is_wp_error( $term_children ) ) {
					continue;
				}

				// If the term has a child, this disambiguates the entry.
				foreach ( $term_children as $term_child_id ) {

					$term_child = get_term_by( 'id', $term_child_id, $taxonomy );

					if ( is_wp_error( $term_child ) ) {
						continue;
					}

					$term_childname = is_object( $term_child ) ? $term_child->name : $term_child->term_id;
	                $term_childslug = is_object( $term_child ) ? $term_child->slug : $term_child->term_id;

	                $output[ $term->name . ' - ' . $term_childname ] = $term_childslug;
				}
			}

			return $output;
		}


		/**
		 * Pulls a list of post types and their slugs.
		 *
		 * @param string $type - Defines whether a simple array or multidimensional array will be returned as a list.
		 * @return $posttypes
		 * @since 1.5
		 */
		public function get_post_type( $type = 'list' ) {
			if ( 'list' == $type ) {
				$posttypes = array( 'Posts' => 'post', 'Pages' => 'page' );
			} else {
				$posttypes['slug'][] = 'post';
				$posttypes['slug'][] = 'page';
				$posttypes['name']['post'] = 'Posts';
				$posttypes['name']['page'] = 'Pages';
			}
			$args = array(
			   'public' => true,
			   '_builtin' => false,
			);
			$post_types = get_post_types( $args, 'objects' );

			// If no expected output comes out, do this to come clean in output.
			if ( is_wp_error( $post_types ) ) {
				return $posttypes;
			}

			foreach ( $post_types as $post_type ) {
				// Get slug name.
				if ( ! empty( $post_type->query_var ) ) {
					$slugname = $post_type->query_var;
				} elseif ( ! empty( $post_type->rewrite->slug ) ) {
					$slugname = $post_type->rewrite->slug;
				} else {
					$slugname = '';
				}

				// Assemble name.
				if ( 'list' == $type ) {
					$posttypes[ $post_type->labels->name ] = $slugname;
				} else {

					$posttypes['slug'][] = $slugname;
					$posttypes['name'][ $slugname ] = $post_type->labels->name;
				}
			}

			return $posttypes;
		}


		/**
		 * Retrieves post terms and places them as classes in a post loop.
		 *
		 * @param int    $the_id - The post ID in a loop.
		 * @param string $the_taxonomy - The taxonomy, required by the query.
		 * @param string $output - Selects the kind of output. Choose from array or class.
		 * @return either a string or array, dependent on $output value.
		 * @since 1.5
		 */
		public function get_post_terms( $the_id, $the_taxonomy, $output = 'class' ) {
			$terms = get_the_terms( $the_id, $the_taxonomy );

			if ( is_wp_error( $terms ) ) {
				return false;
			}

			if ( ! empty( $terms ) ) {
			    foreach ( $terms as $term ) {
			    	$classes[] = $term->slug;
			    }
				$classes = array_map( $this->make_term_name, $classes );
			    if ( 'class' == $output ) {
			    	$out = implode( ' ', $classes );
			    } else {
					$out = $classes;
				}
				return $out;
			} else {
				return false;
			}
		}


		/**
		 * Processes term name for post term parsing. Done for better PHP compatibility.
		 *
		 * @param string $value - The term value.
		 * @return gcp-term-$value, concatinated value.
		 * @since 1.5
		 */
		public function make_term_name( $value ) {
			return 'gcp-term-' . $value;
		}


		/**
		 * Includes normal scripts and css purposed globally by the plugin.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function enqueue_frontend_scripts_and_css() {

			// Loads the general styles used by the carousel.
			wp_enqueue_style( 'gcp-owl-carousel-css', plugins_url( 'carousel-anything/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_CAROUSEL_ANYTHING );

			// Loads styling specific to Owl Carousel.
			wp_enqueue_style( 'carousel-anything-owl', plugins_url( 'carousel-anything/css/owl.theme.default.css', __FILE__ ), array(), VERSION_GAMBIT_CAROUSEL_ANYTHING );

			// Loads scripts specific to Owl Carousel.
			wp_enqueue_script( 'carousel-anything-owl', plugins_url( 'carousel-anything/js/min/owl.carousel2-min.js', __FILE__ ), array( 'jquery' ), '1.3.3' );

			// Loads transitions specific to Owl Carousel.
			wp_enqueue_style( 'carousel-anything-transitions', plugins_url( 'carousel-anything/css/owl.carousel.css', __FILE__ ), array(), VERSION_GAMBIT_CAROUSEL_ANYTHING );

			// Loads scripts.
			wp_enqueue_script( 'carousel-anything', plugins_url( 'carousel-anything/js/min/script-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_CAROUSEL_ANYTHING );

			wp_enqueue_style( 'carousel-anything-animate', plugins_url( 'carousel-anything/css/animate.css', __FILE__ ), array(), VERSION_GAMBIT_CAROUSEL_ANYTHING );

			// Loads extra styles for displaying posts.
			wp_enqueue_style( 'carousel-anything-single-post', plugins_url( 'carousel-anything/css/single-post.css', __FILE__ ), array(), VERSION_GAMBIT_CAROUSEL_ANYTHING );
		}


		/**
		 * Creates the carousel element inside VC, for posts.
		 *
		 * @return	void
		 * @since	1.5
		 */
		public function create_cp_shortcode() {
			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			// Set up VC Element Array here since we use dynamically generated stuff.
			$vc_element = array(
			    'name' => __( 'Carousel Posts', GAMBIT_CAROUSEL_ANYTHING ),
			    'base' => 'carousel_posts',
				'icon' => plugins_url( 'carousel-anything/images/carousel-icon.svg', __FILE__ ),
				'description' => __( 'A modern and responsive posts carousel system', GAMBIT_CAROUSEL_ANYTHING ),
				'as_parent' => array( 'only' => 'vc_row,vc_row_inner' ),
				'admin_enqueue_css' => plugins_url( 'carousel-anything/css/admin.css', __FILE__ ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle' ) : '',
				//'content_element' => true,
				//'is_container' => true,
				//'container_not_allowed' => false,
			);

			// Make the options here.
			$vc_element['params'] = array(
				array(
					'type' => 'textfield',
					'heading' => __( 'Items to display on screen', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'items',
					'value' => '1',
					'group' => __( 'General Options', GAMBIT_CAROUSEL_ANYTHING ),
					'description' => __( 'Maximum items to display at a time', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Slide Animation', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'slide_anim',
					'value' => array(
						__( 'None', GAMBIT_CAROUSEL_ANYTHING ) => '',
						__( 'Bounce', GAMBIT_CAROUSEL_ANYTHING ) => 'bounce',
						__( 'Flash', GAMBIT_CAROUSEL_ANYTHING ) => 'flash',
						__( 'Pulse', GAMBIT_CAROUSEL_ANYTHING ) => 'pulse',
						__( 'RubberBand', GAMBIT_CAROUSEL_ANYTHING ) => 'rubberband',
						__( 'Shake', GAMBIT_CAROUSEL_ANYTHING ) => 'shake',
						__( 'Swing', GAMBIT_CAROUSEL_ANYTHING ) => 'swing',
						__( 'Tada', GAMBIT_CAROUSEL_ANYTHING ) => 'tada',
						__( 'Wobble', GAMBIT_CAROUSEL_ANYTHING ) => 'wobble',
						__( 'Jello', GAMBIT_CAROUSEL_ANYTHING ) => 'jello',
						__( 'Bounce In', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceIn',
						__( 'Bounce In Down', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceInDown',
						__( 'Bounce In Left', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceInLeft',
						__( 'Bounce In Right', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceInRight',
						__( 'Bounce In Up', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceInUp',
						// __( 'Bounce Out', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceOut',
						// __( 'Bounce Out Down', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceOutDown',
						// __( 'Bounce Out Left', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceOutLeft',
						// __( 'Bounce Out Right', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceOutRight',
						// __( 'Bounce Out Up', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceOutUp',
						__( 'Fade In', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeIn',
						__( 'Fade In Down', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInDown',
						__( 'Fade In Down Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInDownBig',
						__( 'Fade In Left', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInLeft',
						__( 'Fade In Left Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInLeftBig',
						__( 'Fade In Right', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInRight',
						__( 'Fade In Right Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInRightBig',
						__( 'Fade In Up', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInUp',
						__( 'Fade In Up Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInUpBig',
						// __( 'Fade Out', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOut',
						// __( 'Fade Out Down', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutDown',
						// __( 'Fade Out Down Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutDownBig',
						// __( 'Fade Out Left', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutLeft',
						// __( 'Fade Out Left Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutLeftBig',
						// __( 'Fade Out Right', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutRight',
						// __( 'Fade Out Right Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutRightBig',
						// __( 'Fade Out Up', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutUp',
						// __( 'Fade Out Up Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutUpBig',
						__( 'Flip', GAMBIT_CAROUSEL_ANYTHING ) => 'flip',
						__( 'Flip In X', GAMBIT_CAROUSEL_ANYTHING ) => 'flipInX',
						__( 'Flip In Y', GAMBIT_CAROUSEL_ANYTHING ) => 'flipInY',
						// __( 'Flip Out X', GAMBIT_CAROUSEL_ANYTHING ) => 'flipOutX',
						// __( 'Flip Out Y', GAMBIT_CAROUSEL_ANYTHING ) => 'flipOutY',
						__( 'Light Speed In', GAMBIT_CAROUSEL_ANYTHING ) => 'lightSpeedIn',
						// __( 'Light Speed Out', GAMBIT_CAROUSEL_ANYTHING ) => 'lightSpeedOut',
						__( 'Rotate In', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateIn',
						__( 'Rotate In Down Left', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateInDownLeft',
						__( 'Rotate In Down Right', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateInDownRight',
						__( 'Rotate In Up Left', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateInUpLeft',
						__( 'Rotate In Up Right', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateInUpRight',
						// __( 'Rotate Out', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateOut',
						// __( 'Rotate Out Down Left', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateOutDownLeft',
						// __( 'Rotate Out Down Right', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateOutDownRight',
						// __( 'Rotate Out Up Left', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateOutUpLeft',
						// __( 'Rotate Out Up Right', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateOutUpRight',
						__( 'Slide In Up', GAMBIT_CAROUSEL_ANYTHING ) => 'slideInUp',
						__( 'Slide In Down', GAMBIT_CAROUSEL_ANYTHING ) => 'slideInDown',
						__( 'Slide In Left', GAMBIT_CAROUSEL_ANYTHING ) => 'slideInLeft',
						__( 'Slide In Right', GAMBIT_CAROUSEL_ANYTHING ) => 'slideInRight',
						// __( 'Slide Out Up', GAMBIT_CAROUSEL_ANYTHING ) => 'slideOutUp',
						// __( 'Slide Out Down', GAMBIT_CAROUSEL_ANYTHING ) => 'slideOutDown',
						// __( 'Slide Out Left', GAMBIT_CAROUSEL_ANYTHING ) => 'slideOutLeft',
						// __( 'Slide Out Right', GAMBIT_CAROUSEL_ANYTHING ) => 'slideOutRight',
						__( 'Zoom In', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomIn',
						__( 'Zoom In Down', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomInDown',
						__( 'Zoom In Left', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomInLeft',
						__( 'Zoom In Right', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomInRight',
						__( 'Zoom In Up', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomInUp',
						// __( 'Zoom Out', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomOut',
						// __( 'Zoom Out Down', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomOutDown',
						// __( 'Zoom Out Left', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomOutLeft',
						// __( 'Zoom Out Right', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomOutRight',
						// __( 'Zoom Out Up', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomOutUp',
						__( 'Hinge', GAMBIT_CAROUSEL_ANYTHING ) => 'hinge',
						__( 'Jack In The Box', GAMBIT_CAROUSEL_ANYTHING ) => 'jackInTheBox',
						__( 'Roll In', GAMBIT_CAROUSEL_ANYTHING ) => 'rollIn',
						// __( 'Roll Out', GAMBIT_CAROUSEL_ANYTHING ) => 'rollOut',
					),
					'group' => __( 'General Options', GAMBIT_CAROUSEL_ANYTHING ),
					'description' => __( 'Note: Slide Animations only work with one item per slide and only in modern browsers. Slide Animations will not work on touch dragging and has to be navigated using thumbnails or arrows.', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'posttypes',
					'heading' => __( 'Select Post Type', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'posttype',
					'holder' => 'span',
					'description' => '',
					'group' => __( 'Contents', GAMBIT_CAROUSEL_ANYTHING ),
				),
			);
			// Insert options generated by a function.
			$dynamic_options = $this->generate_posttype_options();
			if ( ! empty( $dynamic_options ) ) {
				foreach ( $dynamic_options as $dynamic_option ) {
					$vc_element['params'][] = $dynamic_option;
				}
			}

			// Continue with the rest of the options.
			$other_options = array(
				array(
					'type' => 'textfield',
					'heading' => __( 'Number of Total Posts', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'numofallposts',
					'value' => '9',
					'description' => '',
					'group' => __( 'Contents', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Post ordering', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'orderby',
					'value' => array(
						__( 'By Date', GAMBIT_CAROUSEL_ANYTHING ) => 'date',
						__( 'By Post Title', GAMBIT_CAROUSEL_ANYTHING ) => 'title',
						__( 'By Comment count', GAMBIT_CAROUSEL_ANYTHING ) => 'comment_count',
						__( 'Random', GAMBIT_CAROUSEL_ANYTHING ) => 'rand',
					),
					'description' => __( 'Select the order of posting to pull', GAMBIT_CAROUSEL_ANYTHING ),
					'group' => __( 'Contents', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Post direction', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'order_direction',
					'value' => array(
						__( 'Descending', GAMBIT_CAROUSEL_ANYTHING ) => 'DESC',
						__( 'Ascending', GAMBIT_CAROUSEL_ANYTHING ) => 'ASC',
					),
					'description' => __( 'Choose sorting order of the post', GAMBIT_CAROUSEL_ANYTHING ),
					'group' => __( 'Contents', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'checkbox',
					'heading' => __( 'Post Details to Display', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'show_featured_image',
					'value' => array(
						__( 'Featured Image', GAMBIT_CAROUSEL_ANYTHING ) => 'true',
					),
					'description' => '',
					'std' => 'true',
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'checkbox',
					'heading' => '',
					'param_name' => 'show_title',
					'value' => array(
						__( 'Title', GAMBIT_CAROUSEL_ANYTHING ) => 'true',
					),
					'description' => '',
					'std' => 'true',
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'checkbox',
					'heading' => '',
					'param_name' => 'show_author',
					'value' => array(
						__( 'Post Author', GAMBIT_CAROUSEL_ANYTHING ) => 'true',
					),
					'description' => '',
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'checkbox',
					'heading' => '',
					'param_name' => 'show_excerpt',
					'value' => array(
						__( 'Content', GAMBIT_CAROUSEL_ANYTHING ) => 'true',
					),
					'description' => '',
					'std' => 'true',
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'dropdown',
					'heading' => 'Post Design',
					'param_name' => 'featured',
					'value' => array(
						__( 'Plain image', GAMBIT_CAROUSEL_ANYTHING ) => 'image',
						__( 'Use as background image', GAMBIT_CAROUSEL_ANYTHING ) => 'bg',
					),
					'description' => __( '(Tip: Images will only appear if your post has an uploaded Featured Image)', GAMBIT_CAROUSEL_ANYTHING ),
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'show_featured_image',
						'value' => array( 'true' ),
					),
				),
				array(
					'type' => 'dropdown',
					'heading' => 'Alignment',
					'param_name' => 'alignment',
					'value' => array(
						__( 'Default Alignment', GAMBIT_CAROUSEL_ANYTHING ) => '',
						__( 'Align left', GAMBIT_CAROUSEL_ANYTHING ) => ' gcp-alignleft',
						__( 'Align center', GAMBIT_CAROUSEL_ANYTHING ) => ' gcp-aligncenter',
						__( 'Align right', GAMBIT_CAROUSEL_ANYTHING ) => ' gcp-alignright',
					),
					'description' => __( '(Tip: You can force content alignment of the pulled posts)', GAMBIT_CAROUSEL_ANYTHING ),
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'dropdown',
					'heading' => 'Excerpt ellipsis',
					'param_name' => 'ellipsis',
					'value' => array(
						__( 'Use WordPress default behavior for ellipsis', GAMBIT_CAROUSEL_ANYTHING ) => 'false',
						__( 'Customize excerpt or full content behavior', GAMBIT_CAROUSEL_ANYTHING ) => 'true',
					),
					'description' => __( 'Select the type of ellipsis to apply to excerpts<br>(Tip: If the wordcount is shorter than the limit, the ellipsis will not be added)', GAMBIT_CAROUSEL_ANYTHING ),
					// 'std' => 'false',
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Excerpt word count', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'excerpt_count',
					'value' => '25',
					'dependency' => array(
						'element' => 'ellipsis',
						'value' => array( 'true' ),
					),
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Custom ellipsis', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'custom_ellipsis',
					'value' => '...',
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'ellipsis',
						'value' => array( 'true' ),
					),
				),
				array(
					'type' => 'checkbox',
					'heading' => '',
					'param_name' => 'use_image_proportions',
					'value' => array( __( 'Check to use the original image aspect ratio instead of it being adjusted on the fly and having uniform size', GAMBIT_CAROUSEL_ANYTHING ) => 'true' ),
					'description' => '',
					'dependency' => array(
						'element' => 'featured',
						'value' => array( 'image' ),
					),
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
				),
				// array(
				// 	'type' => 'checkbox',
				// 	'heading' => '',
				// 	'param_name' => 'use_full_content',
				// 	'value' => array(
				// 		__( "Use the post's full content instead of excerpt for Content", GAMBIT_CAROUSEL_ANYTHING ) => 'true',
				// 	),
				// 	'description' => '',
				// 	'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
				// ),
				array(
					'type' => 'textfield',
					'heading' => __( 'Image Height', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'image_height',
					'value' => '200',
					'description' => __( 'Set to 0 to use the default size of your images', GAMBIT_CAROUSEL_ANYTHING ),
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'featured',
						'value' => array( 'image' ),
					),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Minimum Content Height', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'content_height',
					'value' => '400',
					'description' => __( 'Specify the total height of the carousel content for each post, inclusive of image and text content', GAMBIT_CAROUSEL_ANYTHING ),
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'featured',
						'value' => array( 'bg' ),
					),
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Title Text Color', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'title_color',
					'value' => '#000',
					'description' => __( 'The color of the title for each pulled post', GAMBIT_CAROUSEL_ANYTHING ),
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'featured',
						'value' => array( 'bg' ),
					),
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Author Text Color', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'author_color',
					'value' => '#000',
					'description' => __( 'The color of the text of the author of each pulled post', GAMBIT_CAROUSEL_ANYTHING ),
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'featured',
						'value' => array( 'bg' ),
					),
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Body Text Color', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'body_color',
					'value' => '#000',
					'description' => __( 'The color of the body text for each pulled post', GAMBIT_CAROUSEL_ANYTHING ),
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'featured',
						'value' => array( 'bg' ),
					),
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Body Background Color', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'body_bg_color',
					'value' => '',
					'description' => __( 'The background color of the pulled post, if no featured image is present', GAMBIT_CAROUSEL_ANYTHING ),
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'featured',
						'value' => array( 'bg' ),
					),
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Background Tint Color', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'body_bg_tint',
					'value' => 'rgba(255,255,255,0.5)',
					'description' => __( 'The selected color will apply a tint to your featured image, if present', GAMBIT_CAROUSEL_ANYTHING ),
					'group' => __( 'Design', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'featured',
						'value' => array( 'bg' ),
					),
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Navigation', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'thumbnails',
					'value' => array(
						__( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ) => 'thumbnail',
						__( 'Arrows (will display navigation arrows at each side)', GAMBIT_CAROUSEL_ANYTHING ) => 'arrow',
						__( 'Thumbnails and Arrows', GAMBIT_CAROUSEL_ANYTHING ) => 'both',
						__( 'None', GAMBIT_CAROUSEL_ANYTHING ) => 'none',
					),
					'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
					'description' => '',
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Navigation Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'thumbnail_shape',
					'value' => array(
						__( 'Circle', GAMBIT_CAROUSEL_ANYTHING ) => 'circles',
						__( 'Square', GAMBIT_CAROUSEL_ANYTHING ) => 'squares',
					),
					'description' => __( 'Select the thumbnail type for your carousel for navigation', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'thumbnails',
						'value' => array( 'thumbnail', 'both' ),
					),
					'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Thumbnail Offset', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'thumbnail_offset',
					'value' => '15',
					'description' => __( 'The distance of the thumbnails from the carousel, in pixels', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'thumbnails',
						'value' => array( 'thumbnail', 'both' ),
					),
					'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Thumbnail Default Color', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'thumbnail_color',
					'value' => '#c3cbc8',
					'description' => __( 'The color of the non-active thumbnail', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'thumbnails',
						'value' => array( 'thumbnail', 'both' ),
					),
					'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Thumbnail Active Color', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'thumbnail_active_color',
					'value' => '#869791',
					'description' => __( 'The color of the active / current thumbnail', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'thumbnails',
						'value' => array( 'thumbnail', 'both' ),
					),
					'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Arrow Offset', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'arrows_offset',
					'value' => '40',
					'description' => __( 'The horizontal distance of the arrows, in pixels. Use this to either put the arrows within the box or outside of it', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'thumbnails',
						'value' => array( 'arrow', 'both' ),
					),
					'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Arrow Alignment', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'arrows_alignment',
					'value' => '50',
					'description' => __( 'Use this to align the arrows vertically to an ideal place, such as portrait areas in the carousel', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'thumbnails',
						'value' => array( 'arrow', 'both' ),
					),
					'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Arrows Size', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'arrows_size',
					'value' => '20',
					'description' => __( 'Customize the size of arrows, in pixels', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'thumbnails',
						'value' => array( 'arrow', 'both' ),
					),
					'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Arrows Default Color', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'arrows_color',
					'value' => '#c3cbc8',
					'description' => __( 'The default color of the navigation arrow', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'thumbnails',
						'value' => array( 'arrow', 'both' ),
					),
					'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Arrows Active Color', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'arrows_active_color',
					'value' => '#869791',
					'description' => __( 'The color of the active / current arrows when highlighted', GAMBIT_CAROUSEL_ANYTHING ),
					'dependency' => array(
						'element' => 'thumbnails',
						'value' => array( 'arrow', 'both' ),
					),
					'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Items to display on tablets', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'items_tablet',
					'value' => '1',
					'group' => __( 'Responsive', GAMBIT_CAROUSEL_ANYTHING ),
					'description' => __( 'Maximum items to display at a time for tablet devices', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Items to display on mobile phones', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'items_mobile',
					'value' => '1',
					'group' => __( 'Responsive', GAMBIT_CAROUSEL_ANYTHING ),
					'description' => __( 'Maximum items to display at a time for mobile devices', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Autoplay', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'autoplay',
					'value' => '5000',
					'description' => __( 'Enter an amount in milliseconds for the carousel to move. Leave blank to disable autoplay', GAMBIT_CAROUSEL_ANYTHING ),
					'group' => __( 'Advanced', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'checkbox',
					'heading' => '',
					'param_name' => 'stop_on_hover',
					'value' => array( __( 'Pause the carousel when the mouse is hovered onto it.', GAMBIT_CAROUSEL_ANYTHING ) => 'true' ),
					'description' => '',
	                'dependency' => array(
	                    'element' => 'autoplay',
	                    'not_empty' => true,
	                ),
					'group' => __( 'Advanced', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Scroll Speed', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'speed_scroll',
					'value' => '800',
					'description' => __( 'The speed the carousel scrolls in milliseconds. Use a reasonable duration, as slower speeds higher than 1500ms may bring unpredictable results in browsers', GAMBIT_CAROUSEL_ANYTHING ),
					'group' => __( 'Advanced', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'checkbox',
					'heading' => '',
					'param_name' => 'touchdrag',
					'value' => array( __( 'Check this box to disable touch dragging of the carousel. (Normally enabled by default)', GAMBIT_CAROUSEL_ANYTHING ) => 'true' ),
					'description' => '',
					'group' => __( 'Advanced', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'checkbox',
					'heading' => __( 'Keyboard Navigation', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'keyboard',
					'value' => array(
						__( 'Enable keyboard navigation', GAMBIT_CAROUSEL_ANYTHING ) => 'cursor',
					),
					'description' => '',
					'group' => __( 'Advanced', GAMBIT_CAROUSEL_ANYTHING ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Custom Class', GAMBIT_CAROUSEL_ANYTHING ),
					'param_name' => 'class',
					'value' => '',
					'description' => __( 'Add a custom class name for the carousel here', GAMBIT_CAROUSEL_ANYTHING ),
					'group' => __( 'Advanced', GAMBIT_CAROUSEL_ANYTHING ),
				),
			);
			foreach ( $other_options as $other_option ) {
				$vc_element['params'][] = $other_option;
			}

			// Put everything together and make it a whole array of options.
			vc_map( $vc_element );
		}

		/**
		 * Shortcode logic.
		 *
		 * @param array  $atts - WordPress shortcode attributes, defined by Visual Composer.
		 * @param string $content - Not needed in this plugin.
		 * @return string - The rendered html.
		 * @since 1.0
		 */
		public function render_cp_shortcode( $atts, $content = null ) {
	        $defaults = array(
				'start' => '0',
				'posttype' => 'post',
				'specified_posttype' => 'post',
				'taxonomy_posts' => 'category',
				'numofallposts' => '9',
				'orderby' => 'date',
				'order_direction' => 'DESC',
				'items' => '1',
				'items_tablet' => '1',
				'items_mobile' => '1',
				'autoplay' => '5000',
				'stop_on_hover' => false,
				'scroll_per_page' => false,
				'speed_scroll' => '800',
				'speed_rewind' => '1000',
				'show_featured_image' => 'true',
				'show_title' => 'true',
				'show_author' => '',
				'show_excerpt' => 'true',
				// 'use_full_content' => 'false',
				'full_content_shortcode' => 'false',
				'featured' => 'image',
				'hyperlink_image' => 'true',
				'alignment'	=> '',
				'transition' => 'false',
				'thumbnails' => 'thumbnail',
				'thumbnail_shape' => 'circles',
				'thumbnail_color' => '#c3cbc8',
				'thumbnail_active_color' => '#869791',
				'thumbnail_offset' => '15',
				'arrows_color' => '#c3cbc8',
				'arrows_active_color' => '#869791',
				'arrows_inactive_color' => '#ffffff',
				'arrows_size' => '20',
				'arrows_offset' => '40',
				'arrows_alignment' => '50',
				'equal_slide_height' => 'true',
				'title_color' => '#000',
				'author_color' => '#000',
				'body_color' => '#000',
				'body_bg_color' => '',
				'body_bg_tint' => 'rgba(255,255,255,0.5)',
				'touchdrag' => 'false',
				'keyboard' => 'false',
				'use_image_proportions' => 'false',
				'image_height' => '200',
				'content_height' => '400',
				'class' => '',
				'excerpt_count'	=> '25',
				'ellipsis' => 'default',
				'custom_ellipsis' => '...',
				'read_more' => '',
				'read_more_color' => '',
				'slide_anim' => '',
	        );
			if ( empty( $atts ) ) {
				$atts = array();
			}
			$atts = array_merge( $defaults, $atts );

			self::$id++;
			$id = 'carousel-posts-' . esc_attr( self::$id );

			// Initialize styles.
			wp_register_style( 'gambit-carousel-posts-styles', false );

			// Parse what to show.
			$arrows_offset = ! empty( $atts['arrows_offset'] ) && is_numeric( $atts['arrows_offset'] ) ? $atts['arrows_offset'] * -1 : '-40';

			$items = '' != $atts['items'] ? $atts['items'] : '3';
			$items_tablet = '' != $atts['items_tablet'] ? $atts['items_tablet'] : '2';
			$items_mobile = '' != $atts['items_mobile'] ? $atts['items_mobile'] : '1';

			// Initialize necessary arrays and defaults.
			$title_styles = array();
			$author_styles = array();
			$content_styles = array();
			$title_other_styles = array();
			$author_other_styles = array();
			$content_other_styles = array();
			$styles = '';
			$carousel_class = '';
			$navigation_buttons = 'false';
	        $ret = '';
			$title_entry = '';
			$author_entry = '';
			$content_entry = '';

			// Post number checker.
			if ( empty( $atts['numofallposts'] ) || $atts['numofallposts'] < 1 ) {
				$numposts = -1;
			} else {
				$numposts = $atts['numofallposts'];
			}

			// Determine the post type used.
			$the_post_type = ! empty( $atts['posttype'] ) ? explode( ',', $atts['posttype'] ) : 'any';

			// Pull posts.
			$querypost = array(
				'posts_per_page' => $numposts,
				'orderby' => $atts['orderby'],
				'order' => $atts['order_direction'],
				'post_status' => 'publish',
				'post_type' => $the_post_type,
				'ignore_sticky_posts' => 1,
			);

			// Check if term entry exists, or if set to all. Do not bother processing if all public post types are to be searched.
			if ( is_array( $the_post_type ) ) {
				foreach ( $the_post_type as $each_post_type ) {
					if ( ! empty( $atts[ 'taxonomy_' . $each_post_type ] ) ) {
						$term_of_post	= $atts[ 'taxonomy_' . $each_post_type ];
						if ( 'all' != $term_of_post ) {
							$cat_in = explode( '|', $term_of_post );
							if ( is_array( $cat_in ) ) {
								$key = $cat_in[0];
								if ( 'category' == $key ) {
									$key = 'category_name';
								}
								$querypost[ $key ] = $cat_in[1];
							}
						}
					}
				}
			}

			// Using filters, arguments for get_posts can be manipulated on the fly. Use the gca_carousel_posts_parameters function within an included PHP file if you want to do so.
			$querypost = apply_filters( 'gca_carousel_posts_parameters', $querypost );
			$posts = new WP_Query( $querypost );
			$postentries = '';
			if ( $posts->have_posts() ) :

				if ( 'bg' == $atts['featured'] ) {
					$title_styles[] = 'color: ' . $atts['title_color'] . '; ';
					$author_styles[] = 'color: ' . $atts['author_color'] . '; ';
					$content_styles[] = 'color: ' . $atts['body_color'] . '; ';
				}

				// Style initialization based on display preference.
				if ( 'true' == $atts['show_featured_image'] && 'true' == $atts['show_title'] && 'bg' == $atts['featured'] ) {
					$title_other_styles[] = 'padding: 5px;';
				} elseif ( 'true' == $atts['show_featured_image'] && 'true' == $atts['show_author'] && 'bg' == $atts['featured'] ) {
					$author_other_styles[] = 'padding: 5px;';
				} elseif ( 'true' == $atts['show_featured_image'] && 'true' == $atts['show_excerpt'] && 'bg' == $atts['featured'] ) {
					$content_other_styles[] = 'padding: 5px; margin-bottom: 0;';
				} elseif ( 'bg' == $atts['featured'] ) {
					$title_other_styles[] = 'padding: 5px;';
				}

				// Explode all individual styles for Title into the main style.
				if ( count( $title_styles ) > 0 ) {
					$styles .= '.' . $id . ' .gcp-post-title, .gcp-post-title a, .gcp-post-title a:link, .gcp-post-title a:visited, .gcp-post-title a:hover { ' . implode( ' ', $title_styles ) . ' }';
				}
				if ( count( $title_other_styles ) > 0 ) {
					$styles .= '.' . $id . ' .gcp-post-title { ' . implode( ' ', $title_other_styles ) . ' }';
				}

				// Explode all individual styles for Author into the main style.
				if ( count( $author_styles ) > 0 ) {
					$styles .= '.' . $id . ' .gcp-post-author, .gcp-post-author a, .gcp-post-author a:link, .gcp-post-author a:visited, .gcp-post-author a:hover { ' . implode( ' ', $author_styles ) . ' }';
				}
				if ( count( $author_other_styles ) > 0 ) {
					$styles .= '.' . $id . ' .gcp-post-author { ' . implode( ' ', $author_other_styles ) . ' }';
				}

				// Explode all individual styles for Content Excerpt into the main style.
				if ( count( $content_styles ) > 0 ) {
					$styles .= '.' . $id . ' .gcp-post-content, .gcp-post-content a, .gcp-post-content a:link, .gcp-post-content a:hover, .gcp-post-content a:visited { ' . implode( ' ', $content_styles ) . ' }';
				}
				if ( count( $content_other_styles ) > 0 ) {
					$styles .= '.' . $id . ' .gcp-post-content { ' . implode( ' ', $content_other_styles ) . ' }';
				}

				// If readmore links are detected and its color, print out its style.
				if ( '' != $atts['read_more'] && '' != $atts['read_more_color'] ) {
					$styles .= '.' . $id . ' .gcp-post-readmore a:link, .' . $id . ' .gcp-post-readmore a:visited, .' . $id . ' .gcp-post-readmore a:active { color: ' . $atts['read_more_color'] . '; }';
					$styles .= '.' . $id . ' .gcp-post-readmore a:hover { color: ' . $atts['body_color'] . '; }';
				}

				// Apply the classes to the main carousel div, coming from the VC options.
				if ( ! empty( $atts['class'] ) ) {
					$carousel_class .= ' ' . esc_attr( $atts['class'] );
				}

				// If we're a background image and we're tinting, add to style.
				if ( 'bg' == $atts['featured'] && '' != $atts['body_bg_tint']  ) {
					$styles .= '.' . $id . ' .gcp-design-bg-has-bg:before { background-color: ' . $atts['body_bg_tint'] . ' } ';
				}

				$the_post_count = $posts->post_count;

				while ( $posts->have_posts() ) : $posts->the_post();

					$overridden_slide = apply_filters( 'gca_carousel_posts_slide_override', '', get_the_ID() );
					if ( $overridden_slide ) {
						$postentries .= '<div>' . $overridden_slide . '</div>';
						continue;
					}

					// Determine the content used.
					// $the_content = 'true' == $atts['use_full_content'] ? get_the_content("",true) : get_the_excerpt();

					// Process the featured image.
					$postclasses = '';
					$thumbnail = '';
					$content_entry = '';
					$css = array();

					if ( 'image' != $atts['featured'] ) {
						$css[] = 'min-height: ' . esc_attr( $atts['content_height'] ) . 'px;';
					}

					if ( 'true' == $atts['show_featured_image'] ) {
						$post_thumbnail_id = get_post_thumbnail_id();

						$size = apply_filters( 'gca_carousel_posts_image_size', 'full' );

						// Jetpack issue, Photon is not giving us the image dimensions.
						// This snippet gets the dimensions for us.
						add_filter( 'jetpack_photon_override_image_downsize', '__return_true' );
						$image_info = wp_get_attachment_image_src( $post_thumbnail_id, $size );
						remove_filter( 'jetpack_photon_override_image_downsize', '__return_true' );

						$attachment_image = wp_get_attachment_image_src( $post_thumbnail_id, $size );
						$bg_image_width = $image_info[1];
						$bg_image_height = $image_info[2];
						$bg_image = $attachment_image[0];

						if ( $post_thumbnail_id ) {
							if ( 'bg' == $atts['featured'] ) {
								$css[] = 'background-repeat: no-repeat; background-size: cover; background-position: center;  background-image: url(' . $attachment_image[0] . ');';
								$postclasses .= ' gcp-design-bg-has-bg';
							} elseif ( 'image' == $atts['featured'] ) {
								$thumbnail = '';
								// $thumbnail = '<a href="' . get_permalink() . '"><div id="post-image" style="height: ' . $atts['image_height'] . 'px; background-repeat: no-repeat; background-size: cover; background-position: center;  background-image: url(' . $attachment_image[0] . ');"></div></a>';
								// Determine height based on settings, else, use native image height.
								$thumb_dimensions = $atts['image_height'] > 0 ? 'height: ' . $atts['image_height'] . 'px; ' : 'height: ' . $bg_image_height . 'px; ';

								// If enabled, use image's native dimensions proportionally. Else, cover like it is usually done.
								$bg_dimensions = 'true' == $atts['use_image_proportions'] ? 'contain' : 'cover';
								$bg_position = 'true' == $atts['use_image_proportions'] ? 'top' : 'center';
								$thumbnail .= '<a href="' . get_permalink() . '">';
								$thumbnail .= '<span class="gcp-post-image" style="' . $thumb_dimensions . 'background-repeat: no-repeat; background-size: ' . $bg_dimensions . '; background-position: ' . $bg_position . ';  background-image: url(' . $attachment_image[0] . ');"></span>';
								$thumbnail .= '</a>';
							}
						}
					}

					// Render background color image, if a color is defined. Do not add if none.
					if ( ! empty( $atts['body_bg_color'] ) ) {
						$css[] = 'background-color: ' . $atts['body_bg_color'] . ';';
					}

					// Render the entries.
					if ( 'true' == $atts['show_title'] ) {
						$title_entry = 'image' == $atts['featured'] ? '<h4 class="gcp-post-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h4>' : '<h4 class="gcp-post-title">' . get_the_title() . '</h4>';
					}

					if ( 'true' == $atts['show_author'] ) {
						$author_entry = 'image' == $atts['featured'] ? '<p class="gcp-post-author"><a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '">' . get_the_author() . '</a></p>' : '<p class="gcp-post-author">' . get_the_author() . '</p>';
					}

					// Process pulled post content, whether to use excerpts or full content.
					if ( 'true' == $atts['show_excerpt'] ) {

						if ( 'true' == $atts['ellipsis'] ) {

							// Strip shortcodes, they don't belong in excerpts.
							$the_content = $this->strip_all_shortcodes( $the_content );
							$the_excerpt = $the_content;

							// Do excerpts the WordPress way.
						} else {
							// Get the excerpt as per WordPress style.
							$the_excerpt = get_the_excerpt('', FALSE);
						}

						// Use Excerpts.
						// if ( 'true' != $atts['use_full_content'] ) {

							// Use custom excerpt instead of the builtin one.


							// Use full content.
						// } else {
							// Use the content unabridged.
							// $the_excerpt = $this->strip_all_shortcodes( $the_content );
						// }

						// If Character Count is enforced, do so here... It does so for excerpts and full content because excerpts pull full content when there's no excerpts defined.
						if ( 'true' === $atts['ellipsis'] ) {
							if ( ! empty( $atts['excerpt_count'] ) && (int) $atts['excerpt_count'] > 0 ) {

								// Limit words based on the given excerpt word count. If none is specified, return the content as is.
								$the_excerpt_count = str_word_count( $the_excerpt, 0 );

								if ( $the_excerpt && (int) $atts['excerpt_count'] && $the_excerpt_count >= (int) $atts['excerpt_count'] ) {

									// Limit the excerpt.
									$the_excerpt = $this->limit_words( $the_excerpt, 0, $atts['excerpt_count'] );
									$the_excerpt = trim( $the_excerpt );
									$the_excerpt = preg_replace( '/[.,;!?]$/', '', $the_excerpt );

									// Append ellispes.
									$the_excerpt .= $atts['custom_ellipsis'];
								}
							}
						}

						// Print out the chosen post content.
						$content_entry .= '<div class="gcp-post-content">' . $the_excerpt . '</div>';

					}

					// Assemble all css parameters into a single array.
					$main_styling = ' style="' . implode( ' ', $css ) . ' "';

					// Get the terms and taxonomy and add them as classes.
					$its_the_taxonomy = get_post_taxonomies( get_the_ID() );

					// Identify the post uniquely by making a class.
					$postclasses .= ' gcp-post-id-' . get_the_ID();

					// Put in taxonomy name as classes.
					if ( $its_the_taxonomy ) {
						foreach ( $its_the_taxonomy as $a_taxonomy ) {
							$postclasses .= ' gcp-taxonomy-' . $a_taxonomy;
						}

						// Pull the terms and add them to the postclasses if they exist.
						$terms = wp_get_post_terms( get_the_id(), $a_taxonomy, array( 'fields' => 'slugs' ) );
						if ( ! is_wp_error( $terms ) ) {
							foreach ( $terms as $a_term ) {
								$postclasses .= ' gcp-term-' . $a_taxonomy . '-' . $a_term;
							}
						}
					}

					// If there's an excerpt, add to the classes.
					$captionclass = 'true' == $atts['show_excerpt'] ? ' gcp-caption-excerpt' : '';

					// Assemble the container of each pulled post.
					$postentries .= '<div class="gcp-post ' . esc_attr( $atts['alignment'] ) . ' gcp-design-' . esc_attr( $atts['featured'] ) . $postclasses . ' "' . $main_styling . '>';
					$postentries .= 'bg' == $atts['featured'] ? '<a class= "gcp-posts-link-overlay" href="' . get_permalink() . '"></a>' : '';

					if ( 'image' == $atts['featured'] ) {
						$postentries .= $thumbnail;
					}

					$do_wrapper = 'true' == $atts['show_title'] || 'true' == $atts['show_author'] || 'true' == $atts['show_excerpt'];

					$postentries .= $do_wrapper ? '<div class="gcp-caption-wrapper ' . esc_attr( $atts['alignment'] ) . $captionclass . '">' : '';

					$title_entry = 'image' == $atts['featured'] ? '<a href="' . get_permalink() . '">' . $title_entry . '</a>': $title_entry;

					$postentries .= 'true' == $atts['show_title'] ? $title_entry : '';
					$postentries .= 'true' == $atts['show_author'] ? $author_entry : '';
					$postentries .= 'true' == $atts['show_excerpt'] ? $content_entry : '';
					$postentries .= $do_wrapper ? '</div>' : '';

					$postentries .= '</div>';

				endwhile;

				$slide_number = $atts['start'] > 0 ? $atts['start'] - 1 : $atts['start'];

				// Force loading of scripts and styles if we're on the frontend editor.
				if ( vc_is_frontend_editor() ) {
					$this->enqueue_frontend_scripts_and_css();
				}

			endif;
			wp_reset_query();

			wp_enqueue_style( 'gambit-carousel-posts-styles' );
			wp_add_inline_style( 'gambit-carousel-posts-styles', $styles );
			$ret = '[carousel_anything ' .
				'items="' . $atts['items'] . '" ' .
				'class="gambit-carousel-posts ' . $id . ' ' . $atts['class'] . '" ' .
				'thumbnails="' . $atts['thumbnails'] . '" ' .
				'thumbnail_shape="' . $atts['thumbnail_shape'] . '" ' .
				'thumbnail_color="' . $atts['thumbnail_color'] .'" ' .
				'thumbnail_active_color="' . $atts['thumbnail_active_color'] .'" ' .
				'thumbnail_offset="' . $atts['thumbnail_offset'] .'" ' .
				'arrows_color="' . $atts['arrows_color'] .'" ' .
				'slide_anim="' . $atts['slide_anim'] .'" ' .
				'arrows_active_color="' . $atts['arrows_active_color'] .'" ' .
				'arrows_inactive_color="' . $atts['arrows_inactive_color'] .'" ' .
				'arrows_size="' . $atts['arrows_size'] .'" ' .
				'arrows_offset="' . $atts['arrows_offset'] .'" ' .
				'arrows_alignment="' . $atts['arrows_alignment'] .'" ' .
				'items_tablet="' . $atts['items_tablet'] .'" ' .
				'items_mobile="' . $atts['items_mobile'] .'" ' .
				'autoplay="' . $atts['autoplay'] .'" ' .
				'stop_on_hover="' . $atts['stop_on_hover'] .'" ' .
				'scroll_per_page="' . $atts['scroll_per_page'] .'" ' .
				'speed_scroll="' . $atts['speed_scroll'] .'" ' .
				'speed_rewind="' . $atts['speed_rewind'] .'" ' .
				'keyboard="' . $atts['keyboard'] .'" ' .
				'touchdrag="' . $atts['touchdrag'] .'" ' .
				']' . $postentries . '[/carousel_anything]';
			$ret = do_shortcode( $ret );

			return apply_filters( 'gambit_cp_output', $ret );
		}
	}
	new GambitCarouselPosts();
}
