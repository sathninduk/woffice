<?php
/**
 * Hero Boxes class file.
 *
 * @package Hero Boxes
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

if ( ! class_exists( 'GambitHeroBoxShortcode' ) ) {

	/**
	 * Hero Boxes, assemble!
	 */
	class GambitHeroBoxShortcode {

		/**
		 * Initializes the Hero Box type.
		 *
		 * @var $type
		 */
		public $type = array();

		/**
		 * Counts the hero boxes to uniquely identify them.
		 *
		 * @var $hero_box_id
		 */
		public static $hero_box_id = 0;

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			$this->type = array(
				__( 'Random', GAMBIT_HERO_BOX ) => 'random',
				__( 'Lily', GAMBIT_HERO_BOX ) => 'lily',
				__( 'Sadie', GAMBIT_HERO_BOX ) => 'sadie',
				__( 'Honey', GAMBIT_HERO_BOX ) => 'honey',
				__( 'Layla', GAMBIT_HERO_BOX ) => 'layla',
				__( 'Zoe', GAMBIT_HERO_BOX ) => 'zoe',
				__( 'Oscar', GAMBIT_HERO_BOX ) => 'oscar',
				__( 'Marley', GAMBIT_HERO_BOX ) => 'marley',
				__( 'Ruby', GAMBIT_HERO_BOX ) => 'ruby',
				__( 'Roxy', GAMBIT_HERO_BOX ) => 'roxy',
				__( 'Bubba', GAMBIT_HERO_BOX ) => 'bubba',
				__( 'Romeo', GAMBIT_HERO_BOX ) => 'romeo',
				__( 'Dexter', GAMBIT_HERO_BOX ) => 'dexter',
				__( 'Sarah', GAMBIT_HERO_BOX ) => 'sarah',
				__( 'Chico', GAMBIT_HERO_BOX ) => 'chico',
				__( 'Milo', GAMBIT_HERO_BOX ) => 'milo',
				__( 'Julia', GAMBIT_HERO_BOX ) => 'julia',
				__( 'Goliath', GAMBIT_HERO_BOX ) => 'goliath',
				__( 'Hera', GAMBIT_HERO_BOX ) => 'hera',
				__( 'Winston', GAMBIT_HERO_BOX ) => 'winston',
				__( 'Selena', GAMBIT_HERO_BOX ) => 'selena',
				__( 'Terry', GAMBIT_HERO_BOX ) => 'terry',
				__( 'Apollo', GAMBIT_HERO_BOX ) => 'apollo',
				__( 'Kira', GAMBIT_HERO_BOX ) => 'kira',
				__( 'Steve', GAMBIT_HERO_BOX ) => 'steve',
				__( 'Moses', GAMBIT_HERO_BOX ) => 'moses',
				__( 'Jazz', GAMBIT_HERO_BOX ) => 'jazz',
				__( 'Ming', GAMBIT_HERO_BOX ) => 'ming',
				__( 'Lexi', GAMBIT_HERO_BOX ) => 'lexi',
				__( 'Duke', GAMBIT_HERO_BOX ) => 'duke',
			);

			add_action( 'init', array( $this, 'create_shortcode' ) );
			add_action( 'init', array( $this, 'create_shortcode_gallery' ) );
			add_shortcode( 'hero_box', array( $this, 'render_shortcode' ) );
			add_shortcode( 'hero_box_gallery', array( $this, 'render_shortcode_gallery' ) );
			add_filter( 'attachment_fields_to_edit', array( $this, 'add_link_fields' ), 10, 2 );
			add_filter( 'attachment_fields_to_save', array( $this, 'save_link_fields' ), 10 , 2 );
		}


		/**
		 * Manages Hero Boxes links.
		 *
		 * @param array  $form_fields - Filled with options.
		 * @param object $post - The accompanying post entry.
		 * @return array $form_fields - Now with Hero Boxes data.
		 **/
		public function add_link_fields( $form_fields, $post ) {
			$form_fields['hero-box-tile-link-to'] = array(
				'label' => __( 'Hero Box Link To', GAMBIT_HERO_BOX ),
				'input' => 'text',
				'value' => get_post_meta( $post->ID, '_hero-box-tile-link-to', true ),
				'helps' => __( 'The URL to go to when the Hero Box is clicked. This is only for the Hero Box for Visual Composer addon.', GAMBIT_HERO_BOX ),
			);

			$value = get_post_meta( $post->ID, '_hero-box-tile-link-to-new-window', true );
			$checked = checked( $value, '1', false );
			$form_fields['hero-box-tile-link-to-new-window'] = array(
				'label' => '',
				'input' => 'html',
				'value' => (bool) $value,
				'html' => "<label><input type='checkbox' name='attachments[{$post->ID}][hero-box-tile-link-to-new-window]' value='1' {$checked}/> " . __( 'Open link in new window', GAMBIT_HERO_BOX ) . '</label>',
			);
			return $form_fields;
		}

		/**
		 * Save Link Fields.
		 *
		 * @param array $post - The post entry.
		 * @param array $attachment - The attachment with or separated the post.
		 * @return array $post - Amended with Hero Boxes data.
		 */
		public function save_link_fields( $post, $attachment ) {
			if ( isset( $attachment['hero-box-tile-link-to'] ) ) {
				update_post_meta( $post['ID'], '_hero-box-tile-link-to', $attachment['hero-box-tile-link-to'] );
			}

			$new_window = isset( $attachment['hero-box-tile-link-to-new-window'] ) ? '1' : '0';
			update_post_meta( $post['ID'], '_hero-box-tile-link-to-new-window', $new_window );

			return $post;
		}


		/**
		 * Creates our shortcode settings in Visual Composer.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function create_shortcode() {
			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			vc_map( array(
				'name' => __( 'Hero Box', GAMBIT_HERO_BOX ),
				'base' => 'hero_box',
				'icon' => plugins_url( 'hero-boxes/images/Hero-Box_Element_Icon.svg', __FILE__ ),
				'description' => __( 'Showcase your image in a hero box', GAMBIT_HERO_BOX ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle', GAMBIT_HERO_BOX ) : '',
				'admin_enqueue_css' => plugins_url( 'hero-boxes/css/admin.css', __FILE__ ),
				'params' => array(
					array(
						'type' => 'attach_image',
						'heading' => __( 'Upload Image', GAMBIT_HERO_BOX ),
						'param_name' => 'image',
						'value' => '',
						'description' => __( 'Select the image that you want to display.' , GAMBIT_HERO_BOX ),
						'group' => __( 'Content', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'dropdown',
						'holder' => 'span',
						'heading' => __( 'Choose your Hero Box design', GAMBIT_HERO_BOX ),
						'param_name' => 'type',
						'value' => $this->type,
						'group' => __( 'Content', GAMBIT_HERO_BOX ),
					),
					// array(
					// 'type' => 'dropdown',
					// 'heading' => __( 'Select Header content input', GAMBIT_HERO_BOX ),
					// 'param_name' => 'title_html',
					// 'value' => array(
					// __( 'Regular Text', GAMBIT_HERO_BOX ) => 'false',
					// __( 'Custom HTML', GAMBIT_HERO_BOX ) => 'true',
					// ),
					// ),
					array(
						'type' => 'textfield',
						'heading' => __( 'Title' , GAMBIT_HERO_BOX ),
						'param_name' => 'title',
						'value' => 'My Hero Box',
						'description' => __( 'Enter the title of your image here', GAMBIT_HERO_BOX ),
						'group' => __( 'Content', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'textarea',
						'heading' => __( 'Caption/Description', GAMBIT_HERO_BOX ),
						'param_name' => 'caption',
						'value' => 'My Hero Box Description',
						'description' => __( 'Enter a short caption of your image here', GAMBIT_HERO_BOX ),
						'group' => __( 'Content', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Title font size' , GAMBIT_HERO_BOX ),
						'param_name' => 'title_size',
						'value' => '',
						'description' => __( 'Font size with unit  (px, em, %, etc)<br />Leave blank to use site defaults', GAMBIT_HERO_BOX ),
						'group' => __( 'Content', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Caption/Description font size', GAMBIT_HERO_BOX ),
						'param_name' => 'caption_size',
						'value' => '',
						'description' => __( 'Font size with unit (px, em, %, etc)<br/>Leave blank to use site defaults', GAMBIT_HERO_BOX ),
						'group' => __( 'Content', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Tint', GAMBIT_HERO_BOX ),
						'param_name' => 'tint',
						'value' => '#000000',
						'description' => __( 'Tint your hero box with a cool color', GAMBIT_HERO_BOX ),
						'group' => __( 'Colors', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'checkbox',
						'heading' => '',
						'param_name' => 'no_tint',
						'value' => array(
							__( 'Disable Tint', GAMBIT_HERO_BOX ) => 'true',
						),
						'group' => __( 'Colors', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Title Color', GAMBIT_HERO_BOX ),
						'param_name' => 'font_color_title',
						'value' => '',
						'description' => __( 'Leave empty to use the default', GAMBIT_HERO_BOX ),
						'group' => __( 'Colors', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Caption/Description Color', GAMBIT_HERO_BOX ),
						'param_name' => 'font_color_caption',
						'value' => '',
						'description' => __( 'Leave empty to use the default', GAMBIT_HERO_BOX ),
						'group' => __( 'Colors', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'textarea_raw_html',
						'heading' => __( 'Custom Title' , GAMBIT_HERO_BOX ),
						'param_name' => 'custom_title',
						'value' => '',
						'description' => __( 'If you would like to use HTML code for the title part of your hero box, enter the HTML codes here', GAMBIT_HERO_BOX ),
						'group' => __( 'Advanced', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Link', GAMBIT_HERO_BOX ),
						'param_name' => 'link',
						'value' => '',
						'description' => __( 'Enter a link here to make your hero box clickable', GAMBIT_HERO_BOX ),
						'group' => __( 'Advanced', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'checkbox',
						'heading' => '',
						'param_name' => 'link_new_window',
						'value' => array(
							__( 'Check this to open the link in a new window', GAMBIT_HERO_BOX ) => 'true',
						),
						'group' => __( 'Advanced', GAMBIT_HERO_BOX ),
					),
					// array(
					// 'type' => 'textfield',
					// 'heading' => __( 'Width', GAMBIT_HERO_BOX ),
					// 'param_name' => 'width',
					// 'value' => '',
					// 'description' => __( 'Fill up this field with its unit (eg. 400px, 50vw, etc.) if you want to define a specific width.', GAMBIT_HERO_BOX ),
					// ),
					array(
						'type' => 'textfield',
						'heading' => __( 'Height', GAMBIT_HERO_BOX ),
						'param_name' => 'height',
						'value' => '',
						'description' => __( 'Fill up this field with its unit (eg. 700px, 100vh, etc.) if you want to define a specific height.', GAMBIT_HERO_BOX ),
						'group' => __( 'Advanced', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Custom Class', GAMBIT_HERO_BOX ),
						'param_name' => 'class',
						'value' => '',
						'description' => __( 'Add a custom class name for the hero box here', GAMBIT_HERO_BOX ),
						'group' => __( 'Advanced', GAMBIT_HERO_BOX ),
					),
				),
			) );
		}


		/**
		 * Creates Hero Box Gallery in Visual Composer.
		 */
		public function create_shortcode_gallery() {
			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			vc_map( array(
				'name' => __( 'Hero Box Gallery', GAMBIT_HERO_BOX ),
				'base' => 'hero_box_gallery',
				'icon' => plugins_url( 'hero-boxes/images/Hero-Box_Element_Icon.svg', __FILE__ ),
				'description' => __( 'Create multiple hero boxes', GAMBIT_HERO_BOX ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle', GAMBIT_HERO_BOX ) : '',
				'params' => array(
					array(
						'type' => 'attach_images',
						'heading' => __( 'Upload Images', GAMBIT_HERO_BOX ),
						'param_name' => 'images',
						'value' => '',
						'description' => __( 'TIP: Change your image’s Title and Description here.' , GAMBIT_HERO_BOX ),
						'group' => __( 'Content', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Columns', GAMBIT_HERO_BOX ),
						'param_name' => 'column',
						'value' => array(
							__( '1 Column', GAMBIT_HERO_BOX ) => '1',
							__( '2 Columns', GAMBIT_HERO_BOX ) => '2',
							__( '3 Columns', GAMBIT_HERO_BOX ) => '3',
						),
						'description' => __( 'Choose whether to display your hero box in 1-3 columns.', GAMBIT_HERO_BOX ),
						'group' => __( 'Content', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Select ONE design for your Hero Boxes.', GAMBIT_HERO_BOX ),
						'param_name' => 'type',
						'value' => $this->type,
						'group' => __( 'Content', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Tint', GAMBIT_HERO_BOX ),
						'param_name' => 'tint',
						'value' => '#000000',
						'description' => __( 'Tint your hero box with a cool color', GAMBIT_HERO_BOX ),
						'group' => __( 'Advanced', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'checkbox',
						'heading' => '',
						'param_name' => 'no_tint',
						'value' => array(
							__( 'Check this to disable tint transparency.', GAMBIT_HERO_BOX ) => 'true',
						),
						'group' => __( 'Advanced', GAMBIT_HERO_BOX ),
					),
					// array(
					// 'type' => 'textfield',
					// 'heading' => __( 'Width', GAMBIT_HERO_BOX ),
					// 'param_name' => 'width',
					// 'value' => '',
					// 'description' => __( 'Fill up this field (in pixels) if you want to define a specific width.', GAMBIT_HERO_BOX ),
					// ),
					array(
						'type' => 'textfield',
						'heading' => __( 'Height', GAMBIT_HERO_BOX ),
						'param_name' => 'height',
						'value' => '',
						'description' => __( 'Fill up this field (in pixels) if you want to define a specific height.', GAMBIT_HERO_BOX ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Custom Class', GAMBIT_HERO_BOX ),
						'param_name' => 'class',
						'value' => '',
						'description' => __( 'Add a custom class name for the hero boxes here.', GAMBIT_HERO_BOX ),
						'group' => __( 'Advanced', GAMBIT_HERO_BOX ),
					),
				),
			) );
		}

		/**
		 * Shortcode logic.
		 *
		 * @param	array  $atts - The attributes of the shortcode.
		 * @param	string $content - The content enclosed inside the shortcode if any.
		 * @return	string - The rendered html.
		 * @since	1.0
		 */
		public function render_shortcode( $atts, $content = null ) {
			$atts = shortcode_atts( array(
				'type' => 'random',
				'image' => '',
				'title' => 'My Hero Box Title',
				// 'title_encoded' => 'true',
				'custom_title' => '',
				'caption' => 'My Hero Box Description',
				'title_size' => '',
				'caption_size' => '',
				'link' => '',
				'link_new_window' => 'false',
				'font_color_title' => '',
				'font_color_caption' => '',
				'tint' => '#000000',
				'no_tint' => 'false',
				// 'width' => '',
				'height' => '',
				'class' => '',
			), $atts );

			$ret = '';
			// $title_css = 0;
			$caption_css = 0;
			$size = array();

			$title = ! empty( $atts['custom_title'] ) ? rawurldecode( base64_decode( strip_tags( $atts['custom_title'] ) ) ) : $atts['title'];
			$caption = $atts['caption'];

			self::$hero_box_id++;

			while ( 'random' === $atts['type'] ) {
				$rand_key = array_rand( $this->type, 1 );
				$atts['type'] = $this->type[ $rand_key ];
			}

			$classes = array(
				'hero-box',
				'hero-box-effect-' . esc_attr( $atts['type'] ),
			);
			if ( ! empty( $atts['class'] ) ) {
				$classes[] = $atts['class'];
			}

			$styles = array();
			$styles[] = 'background-color: ' . esc_attr( $atts['tint'] ) . ';';
			$styles[] = 'border-color:' . esc_attr( $atts['tint'] ) . ';';
			if ( is_numeric( $atts['height'] ) ) {
				$atts['height'] .= 'px';
			}
			$styles[] = 'height: ' . $atts['height'] . ';';

			$ret .= '<div id="gambit-hero-box-' . self::$hero_box_id . '" class="' . implode( ' ', $classes ) . '" style="' . implode( ' ', $styles ) . '">';
			$ret .= '<figure class="hero-box-wrapper">';

			if ( ! empty( $atts['image'] ) ) {
				$styles = array();
				$image = wp_get_attachment_image_src( $atts['image'], 'large' );
				$styles[] = 'background-image: url(' . $image[0] . ');';
				if ( 'true' === $atts['no_tint'] ) {
					$styles[] = 'opacity: 1 !important;';
				}
				$ret .= '<div class="hero-box-img" style="' . implode( ' ', $styles ) . '"></div>';
			}

			if ( ! empty( $title ) || ! empty( $caption ) || ! empty( $atts['link'] ) ) {
				$ret .= '<figcaption>';

				$ret .= '<div class="hero-box-text">';

				// Indicate that styles should be printed because one of these fields are not empty.
				$title_styles = array();

				// Prepare title CSS attributes.
				if ( ! empty( $atts['font_color_title'] ) ) {
					$title_styles[] = 'color: ' . esc_attr( $atts['font_color_title'] ) . ';';
				}
				if ( ! empty( $atts['title_size'] ) ) {
					if ( is_numeric( $atts['title_size'] ) ) {
						$atts['title_size'] .= 'px';
					}
					$title_styles[] = 'font-size: ' . esc_attr( $atts['title_size'] ) . ';';
				}

				// Print title tag along with the styles.
				if ( ! empty( $title ) ) {
					if ( empty( $atts['custom_title'] ) ) {
						$ret .= '<h3 class="hero-box-title" style="' . implode( ' ', $title_styles ) . '">' . esc_attr( $title ) . '</h3>';
					} else {
						$ret .= '<div class="hero-box-title" style="' . implode( ' ', $title_styles ) . '">' . $title . '</div>';
					}
				}

				// Prepare caption CSS attributes.
				$caption_styles = array();
				if ( ! empty( $atts['font_color_caption'] ) ) {
					$caption_styles[] = 'color: ' . esc_attr( $atts['font_color_caption'] ) . ';';
				}
				if ( ! empty( $atts['caption_size'] ) ) {
					if ( is_numeric( $atts['caption_size'] ) ) {
						$atts['caption_size'] .= 'px';
					}
					$caption_styles[] = 'font-size: ' . esc_attr( $atts['caption_size'] ) . ';';
				}

				// Print caption tag along with the styles.
				if ( ! empty( $caption ) ) {
					$ret .= '<p class="hero-box-caption" style="' . implode( ' ', $caption_styles ) . '">' . esc_attr( $caption ) . '</p>';
				}

				$ret .= '</div>'; // .hero-box-text

				if ( ! empty( $atts['link'] ) ) {
					$newwindow = ( 'true' === $atts['link_new_window'] ? ' target="_blank"' : '');
					$ret .= '<a href="' . esc_url( $atts['link'] ) . '"' . $newwindow . '>' . __( 'View more', GAMBIT_HERO_BOX ) . '</a>';
				}

				$ret .= '</figcaption>';

			} // End if().

			$ret .= '</figure>';
			$ret .= '</div>';

			wp_enqueue_style( __CLASS__, plugins_url( 'hero-boxes/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_HERO_BOX );
			wp_enqueue_script( __CLASS__, plugins_url( 'hero-boxes/js/min/script-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_HERO_BOX, true );

			return apply_filters( 'gambit_heroboxes_output', $ret );
		}

		/**
		 * Gallery logic.
		 *
		 * @param	array  $atts - The attributes of the shortcode.
		 * @param	string $content - The content enclosed inside the gallery if any.
		 * @return	string - The rendered gallery.
		 * @since	1.0
		 */
		public function render_shortcode_gallery( $atts, $content = null ) {
			$atts = shortcode_atts( array(
				'type' => 'random',
				'images' => '',
				'tint' => '#000000',
				'column' => '1',
				// 'width' => '',
				'height' => '',
				'class' => '',
				'no_tint' => 'false',
				// 'link' => '',
				// 'link_new_window' => 'false',
			), $atts );

			$images = explode( ',', $atts['images'] );

			$ret = '';
			$content = '';

			foreach ( $images as $image_id ) {
				$attachment = get_post( $image_id );

				$link = get_post_meta( $image_id, '_hero-box-tile-link-to', true );
				$link_new_window = get_post_meta( $image_id, '_hero-box-tile-link-to-new-window', true );

				if ( $attachment ) {
					$image_title = $attachment->post_title;
					$caption = $attachment->post_excerpt;

					$image_title = empty( $image_title ) ? __( 'Gallery Title', GAMBIT_HERO_BOX ) : $image_title;
					$caption = empty( $caption ) ? __( 'Gallery Caption', GAMBIT_HERO_BOX ) : $caption;

					$content .= do_shortcode( '[hero_box image="' . esc_attr( $image_id ) . '" height="' . esc_attr( $atts['height'] ) . '" title="' . esc_attr( $image_title ) . '" caption="' . esc_attr( $caption ) . '" type="' . esc_attr( $atts['type'] ) . '" height="' . esc_attr( $atts['height'] ) . '" link_new_window="' . esc_attr( $link_new_window ) . '" tint="' . esc_attr( $atts['tint'] ) . '" no_tint="' . esc_attr( $atts['no_tint'] ) . '" link="' . $link . '"]' );
				}
			}

			// Container classes.
			$classes = array(
				'hero-box-gallery',
				'hero-box-columns-' . $atts['column'],
			);
			if ( ! empty( $atts['class'] ) ) {
				$classes[] = $atts['class'];
			}

			$ret .= '<div class="' . implode( ' ', $classes ) . '">' . $content . '</div>';

			return apply_filters( 'gambit_heroboxes_gallery_output', $ret );
		}
	}
	new GambitHeroBoxShortcode();
}
