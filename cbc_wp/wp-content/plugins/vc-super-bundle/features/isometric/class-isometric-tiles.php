<?php
/**
 * Isometric Tiles shortcode file.
 *
 * @version 1.1
 * @package Isometric Tiles for Visual Composer
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Initializes plugin class.
if ( ! class_exists( 'GambitIsoTilesShortcode' ) ) {

	/**
	 * This is where all the plugin's functionality happens.
	 */
	class GambitIsoTilesShortcode {

		/**
		 * Tile instance counter.
		 *
		 * @var	int $iso_tiles_id - Counts instance of isometric tiles rendered.
		 */
		private static $iso_tiles_id = 1;

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Initialize as a Visual Composer addon.
			add_action( 'init', array( $this, 'create_shortcodes' ), 999 );

			// Add the media link field styles.
			add_action( 'admin_head', array( $this, 'add_media_link_field_styles' ) );

			// IE9 backward compatibility.
			add_action( 'wp_head', array( $this, 'add_ie9_checker' ) );

			// Admin stuff.
			add_action( 'admin_enqueue_scripts', array( $this, 'add_media_link_field_styles' ) );
			add_filter( 'attachment_fields_to_edit', array( $this, 'add_tile_link_fields' ), 10, 2 );
			add_filter( 'attachment_fields_to_save', array( $this, 'save_tile_link_fields' ), 10 , 2 );

			// Render as shortcode.
			add_shortcode( 'iso_tiles', array( $this, 'render_shortcode' ) );
		}

		/**
		 * Initializes admin styling.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function add_media_link_field_styles() {
			wp_enqueue_style( 'iso-tiles-admin', plugins_url( 'isometric-tiles/css/admin.css', __FILE__ ), array(), VERSION_GAMBIT_VC_ISO_TILES );
		}

		/**
		 * Support for Internet Explorer 9.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function add_ie9_checker() {
			echo '<script>var isoTilesIsIE9 = false</script>
				<!--[if lte IE 9 ]>
				<script>isoTilesIsIE9 = true</script>
				<![endif]-->';
		}

		/**
		 * Add a hyperlink to tiles.
		 *
		 * @param array  $form_fields - The array of fields.
		 * @param object $post - The post as fetched by WordPress.
		 * @return	array $form_fields - Combined data with the post above.
		 * @since	1.0
		 */
		public function add_tile_link_fields( $form_fields, $post ) {
			$form_fields['iso-tile-link-to'] = array(
				'label' => __( 'Tile Link To', 'isometric-tiles' ),
				'input' => 'text',
				'value' => get_post_meta( $post->ID, '_iso-tile-link-to', true ),
				'helps' => __( 'The URL to go to when the image tile is clicked. This is only used for the Isometric Tiles for Visual Composer addon.', 'isometric-tiles' ),
			);

			$value = get_post_meta( $post->ID, '_iso-tile-link-to-new-window', true );
			$checked = checked( $value, '1', false );
			$form_fields['iso-tile-link-to-new-window'] = array(
				'label' => '',
				'input' => 'html',
				'value' => (bool) $value,
				'html' => "<label><input type='checkbox' name='attachments[{$post->ID}][iso-tile-link-to-new-window]' value='1' {$checked}/> " . __( 'Open link in new window', 'isometric-tiles' ) . '</label>',
			);
			return $form_fields;
		}

		/**
		 * Saves the hyperlink for future rendering in the isometric tiles.
		 *
		 * @param object $post - The post as fetched by WordPress.
		 * @param array  $attachment - The attachment as fetched by WordPress.
		 * @return	object $post - Now merged with data from the attachment.
		 * @since	1.0
		 */
		public function save_tile_link_fields( $post, $attachment ) {
			if ( isset( $attachment['iso-tile-link-to'] ) ) {
				update_post_meta( $post['ID'], '_iso-tile-link-to', $attachment['iso-tile-link-to'] );
			}

			$new_window = isset( $attachment['iso-tile-link-to-new-window'] ) ? '1' : '0';
			update_post_meta( $post['ID'], '_iso-tile-link-to-new-window', $new_window );

			return $post;
		}

		/**
		 * Creates our shortcode settings in Visual Composer.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function create_shortcodes() {
			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			vc_map( array(
				'name' => __( 'Isometric Tiles', 'isometric-tiles' ),
				'base' => 'iso_tiles',
				'icon' => plugins_url( 'isometric-tiles/images/Isometric_Tiles_Element_Icon.svg', __FILE__ ),
				'description' => __( 'Display images as isometric tiles', 'isometric-tiles' ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle', 'isometric-tiles' ) : '',
				'params' => array(
					array(
						'type' => 'attach_images',
						'heading' => __( 'Tile Images', 'isometric-tiles' ),
						'param_name' => 'image_ids',
						'value' => '',
						'admin_label' => true,
						'description' => __( 'Choose images that will be placed inside the tiles.<br>You can enter the <strong>links for each image tile</strong> on the sidebar when selecting images.<br>Check the layout details for the ideal number of images you&apos;ll need', 'isometric-tiles' ),
						'group' => __( 'Resources', 'isometric-tiles' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Area Height', 'isometric-tiles' ),
						'param_name' => 'height',
						'value' => '230',
						'description' => __( 'The height of the whole tile area (in pixels). You can use this to clip the area of the tiles, to get some nice effects.', 'isometric-tiles' ),
						'group' => __( 'Design', 'isometric-tiles' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Tiles Layout', 'isometric-tiles' ),
						'param_name' => 'layout',
						'value' => array(
							__( 'Rectangles (use 11-22 images)', 'isometric-tiles' ) => 'rectangles',
							__( 'Rectangles Arrow (use 9-18 images)', 'isometric-tiles' ) => 'rectangles-arrow',
							__( 'Squares (use 12-32 images)', 'isometric-tiles' ) => 'squares',
							__( 'Squares Arrow (use 10-25 images)', 'isometric-tiles' ) => 'squares-arrow',
							__( 'Single Rectangle Left (use 1 image)', 'isometric-tiles' ) => 'single-rectangle-left',
							__( 'Rectangles Left Layered (use 2-10 images)', 'isometric-tiles' ) => 'rectangles-left-layered',
							__( 'Single Rectangle Right (use 1 image)', 'isometric-tiles' ) => 'single-rectangle-right',
							__( 'Rectangles Right Layered (use 2-10 images)', 'isometric-tiles' ) => 'rectangles-right-layered',
							__( 'Single Square (use 1 image)', 'isometric-tiles' ) => 'single-square',
							__( 'Squares Layered (use 2-10 images)', 'isometric-tiles' ) => 'squares-layered',
						),
						'description' => "<img src='" . plugins_url( 'isometric-tiles/images/vc-layouts.png', __FILE__ ) . "' style='width: 100%; height: auto'/>",
						'group' => __( 'Design', 'isometric-tiles' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Image Direction', 'isometric-tiles' ),
						'param_name' => 'image_direction',
						'value' => array(
							__( 'Facing Left', 'isometric-tiles' ) => 'left',
							__( 'Facing Right', 'isometric-tiles' ) => 'right',
							__( 'Facing Left or Right (Randomized)', 'isometric-tiles' ) => 'random',
						),
						'description' => __( 'The direction the image&apos;s are facing. <strong>For &quot;Rectangles&quot; and &quot;Rectangles Arrow&quot;, the directions are set for you.</strong>', 'isometric-tiles' ),
						'dependency' => array(
							'element' => 'layout',
							'value' => array( 'single-rectangle-left', 'single-rectangle-right', 'rectangles-left-layered', 'rectangles-right-layered', 'single-square', 'squares-layered', 'squares', 'squares-arrow' ),
						),
						'group' => __( 'Design', 'isometric-tiles' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Tile Size', 'isometric-tiles' ),
						'param_name' => 'size',
						'value' => '100',
						'description' => __( 'The size of each tile (in pixels). Rectangular tiles have one side doubled. Value should be from 100 to 300', 'isometric-tiles' ),
						'group' => __( 'Design', 'isometric-tiles' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Tile Gap', 'isometric-tiles' ),
						'param_name' => 'gap',
						'value' => '20',
						'description' => __( 'The gap between the tiles (in pixels). Value should be from 0 to 50', 'isometric-tiles' ),
						'group' => __( 'Design', 'isometric-tiles' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Tilt Angle', 'isometric-tiles' ),
						'param_name' => 'angle',
						'value' => '40',
						'description' => __( 'The tilt angle of the tiles (in degrees). Value should be from 0 to 70', 'isometric-tiles' ),
						'group' => __( 'Design', 'isometric-tiles' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Tile Display', 'isometric-tiles' ),
						'param_name' => 'tile_display',
						'value' => array(
							__( '3D (with sides)', 'isometric-tiles' ) => '3d',
							__( 'Flat (without sides)', 'isometric-tiles' ) => 'flat',
						),
						'description' => __( 'The look of the tiles.', 'isometric-tiles' ),
						'group' => __( 'Design', 'isometric-tiles' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Tile Height', 'isometric-tiles' ),
						'param_name' => 'side_height',
						'value' => '10',
						'description' => __( 'The height of the tile (in pixels). Value should be from 0 to 15', 'isometric-tiles' ),
						'dependency' => array(
							'element' => 'tile_display',
							'value' => array( '3d' ),
						),
						'group' => __( 'Design', 'isometric-tiles' ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Left Side Tile Color', 'isometric-tiles' ),
						'param_name' => 'left_side_color',
						'value' => '#006c94',
						'description' => __( 'This is the color of the left side of the 3d tile.', 'isometric-tiles' ),
						'dependency' => array(
							'element' => 'tile_display',
							'value' => array( '3d' ),
						),
						'group' => __( 'Design', 'isometric-tiles' ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Right Side Tile Color', 'isometric-tiles' ),
						'param_name' => 'right_side_color',
						'value' => '#0084b5',
						'description' => __( 'This is the color of the left side of the 3d tile.', 'isometric-tiles' ),
						'dependency' => array(
							'element' => 'tile_display',
							'value' => array( '3d' ),
						),
						'group' => __( 'Design', 'isometric-tiles' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Hover Direction', 'isometric-tiles' ),
						'param_name' => 'hover_direction',
						'value' => array(
							__( 'Right', 'isometric-tiles' ) => 'right',
							__( 'Left', 'isometric-tiles' ) => 'left',
						),
						'description' => __( 'For layered layouts, tiles with links hover either to the right or left. Choose the direction here.', 'isometric-tiles' ),
						'dependency' => array(
							'element' => 'layout',
							'value' => array( 'rectangles-left-layered', 'rectangles-right-layered', 'squares-layered' ),
						),
						'group' => __( 'Design', 'isometric-tiles' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Hover Distance', 'isometric-tiles' ),
						'param_name' => 'hover_distance',
						'value' => '30',
						'description' => __( 'The distance a tile rises or slides when hovered on (in pixels).<br><strong>If you&apos;re finding that your tiles do not hover, that means you need to put in a <em>Tile Link To</em> value while selecting your images in the &quot;Add Images&quot; modal window.</strong>', 'isometric-tiles' ),
						'group' => __( 'Design', 'isometric-tiles' ),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Lightbox', 'isometric-tiles' ),
						'param_name' => 'lightbox',
						'value' => array(
							__( 'If checked, clicking the isometric tiles will trigger a lightbox that displays the image in full.', 'isometric-tiles' ) => '1',
						),
						'description' => '',
						'group' => __( 'Lightbox', 'isometric-tiles' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Image to display in lightbox', 'isometric-tiles' ),
						'param_name' => 'lightbox_image',
						'value' => array(
							__( 'Use attached image', 'isometric-tiles' ) => 'image',
							__( 'Use the link provided in the post meta', 'isometric-tiles' ) => 'meta',
						),
						'description' => __( 'Choose what to show in the lightbox.<br />If you use the "Use the link provided in the post meta", the image URLs you have placed in the "Tile Link To" when selecting images will be used in the lightbox.<br />If the "Tile Link To" URL is not an image, or if it is blank, it will not open a lightbox.', 'isometric-tiles' ),
						'dependency' => array(
							'element' => 'lightbox',
							'value' => '1',
						),
						'group' => __( 'Lightbox', 'isometric-tiles' ),
					),
					// array(
					// 	'type' => 'textfield',
					// 	'heading' => __( 'Lightbox ID', 'isometric-tiles' ),
					// 	'param_name' => 'lightbox_id',
					// 	'description' => __( 'If you wish to uniquely identify a particular lightbox, or group lightboxes together, specify an ID here.', 'js_composer' ),
					// 	'dependency' => array(
					// 		'element' => 'lightbox',
					// 		'value' => '1',
					// 	),
					// 	'group' => __( 'Lightbox', 'isometric-tiles' ),
					// ),
					array(
						'type' => 'textfield',
						'heading' => __( 'Lightbox Caption', 'isometric-tiles' ),
						'param_name' => 'lightbox_name',
						'description' => __( 'Optionally, you can give your lightbox a name or description. It will appear when the lightbox is activated.', 'js_composer' ),
						'dependency' => array(
							'element' => 'lightbox',
							'value' => '1',
						),
						'group' => __( 'Lightbox', 'isometric-tiles' ),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Enable hover effect', 'isometric-tiles' ),
						'param_name' => 'hover_effect',
						'value' => array(
							__( 'Check this to enable hover effect', 'isometric-tiles' ) => '1',
						),
						'description' => __( 'To make sure the hover effect is fully disabled, remove all links entered on the field.', 'isometric-tiles' ),
						'group' => __( 'Advanced', 'isometric-tiles' ),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Disable on mobile devices', 'isometric-tiles' ),
						'param_name' => 'disable_mobile',
						'value' => array(
							__( 'Check this to disable the display in mobile devices.', 'isometric-tiles' ) => '1',
						),
						'description' => '',
						'group' => __( 'Advanced', 'isometric-tiles' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Extra class name', 'js_composer' ),
						'param_name' => 'el_class',
						'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' ),
						'group' => __( 'Advanced', 'isometric-tiles' ),
					),
				),
			) );
		}


		/**
		 * Sorts by image size.
		 *
		 * @param array $a - Parameters of 1st image.
		 * @param array $b - Parameters of 2nd image.
		 * @return int - Difference of either width or height between compared images.
		 * @since	1.0
		 */
		public function sort_by_image_size( $a, $b ) {
			if ( $a['width'] <= $a['height'] ) {
				return $a['width'] - $b['width'];
			} else {
				return $a['height'] - $b['height'];
			}
		}


		/**
		 * Retrieve the appropriate image.
		 *
		 * @param int $image_id - The image ID.
		 * @param int $tile_width - The tile width.
		 * @param int $tile_height - The tile height.
		 * @return array $sizes - Contains data for the image to be used.
		 * @since	1.0
		 */
		private function get_correct_image( $image_id, $tile_width, $tile_height ) {
			$upload_dir = wp_upload_dir();
			$upload_url = trailingslashit( $upload_dir['baseurl'] );

			// Jetpack issue, Photon is not giving us the image dimensions.
			// This snippet gets the dimensions for us.
			if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' ) ) {
				add_filter( 'jetpack_photon_override_image_downsize', '__return_true' );
				$image_meta = wp_get_attachment_metadata( $image_id );
				remove_filter( 'jetpack_photon_override_image_downsize', '__return_true' );
			} else {
				$image_meta = wp_get_attachment_metadata( $image_id );
			}

			// Render only if the image is valid. Return false if it's not valid.
			if ( $image_meta ) {
				$image_meta_internal = wp_get_attachment_metadata( $image_id );
				$sizes = array();
				$sizes['full'] = array(
					'width' => $image_meta['width'],
					'height' => $image_meta['height'],
					'url' => $upload_url . $image_meta_internal['file'],
				);

				if ( ! empty( $image_meta['sizes'] ) ) {
					foreach ( $image_meta['sizes'] as $size => $size_details ) {
						$sizes[ $size ] = array(
							'width' => $size_details['width'],
							'height' => $size_details['height'],
							'url' => trailingslashit( dirname( $upload_url . $image_meta_internal['file'] ) ) . $size_details['file'],
						);
					}
				}

				if ( count( $sizes ) > 1 ) {
					usort( $sizes, array( $this, 'sort_by_image_size' ) );
				}

				// Find the size that fits our tile.
				foreach ( $sizes as $size_details ) {
					if ( $size_details['width'] >= $tile_width && $size_details['height'] >= $tile_height ) {
						return $size_details['url'];
					}
				}
			}

			$image = wp_get_attachment_image_src( $image_id, 'full' );
			if ( $image ) {
				return esc_url( $image[0] );
			}

			return false;
		}


		/**
		 * Rendering of image locations.
		 *
		 * @param string $layout - As noted.
		 * @param int    $tile_size - Size of the isometric tile.
		 * @param int    $tile_gap - Spacing between isometric tiles.
		 * @param string $image_direction - Rendering direction.
		 * @return array - Specifics of the isometric tile to render.
		 * @since	1.0
		 */
		private function get_image_locations( $layout, $tile_size, $tile_gap, $image_direction ) {
			$tile_size_double = $tile_size * 2 + $tile_gap;
			$tile_loc = $tile_size + $tile_gap;

			$rotations = array();
			for ( $i = 0; $i < 100; $i++ ) {
				if ( 'random' === $image_direction ) {
					$rotations[] = rand( 1, 2 ) == 1 ? true : false;
				} elseif ( 'right' === $image_direction ) {
					$rotations[] = true;
				} else { // Left.
					$rotations[] = false;
				}
			}

			if ( 'rectangles' == $layout ) {
				return array(
					// 1.
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => 0, 'y' => 0 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => 0, 'y' => $tile_loc * 2 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc, 'y' => 0 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => $tile_loc, 'y' => $tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => -$tile_loc, 'y' => $tile_loc * 2 ),
					// 6.
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc * 2, 'y' => -$tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => -$tile_loc * 2, 'y' => $tile_loc * 3 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => -$tile_loc, 'y' => $tile_loc * 4 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => $tile_loc * 2, 'y' => $tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => $tile_loc * 3, 'y' => 0 ),
					// 11.
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc * 3, 'y' => -$tile_loc * 2 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => -$tile_loc * 3, 'y' => $tile_loc * 5 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => -$tile_loc, 'y' => $tile_loc * 5 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => $tile_loc * 4, 'y' => -$tile_loc ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc, 'y' => $tile_loc * 3 ),
					// 16.
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => $tile_loc * 5, 'y' => -$tile_loc * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => $tile_loc * 5, 'y' => -$tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => $tile_loc, 'y' => $tile_loc * 4 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc * 2, 'y' => $tile_loc * 4 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => $tile_loc * 3, 'y' => $tile_loc * 2 ),
					// 21.
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc * 4, 'y' => $tile_loc ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc * 4, 'y' => $tile_loc * 2 ),
				);

			} elseif ( 'rectangles-arrow' == $layout ) {
				return array(
					// 1.
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => 0, 'y' => 0 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc, 'y' => 0 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => 0, 'y' => $tile_loc * 2 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => $tile_loc, 'y' => $tile_loc ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc * 2, 'y' => $tile_loc ),
					// 6.
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc * 3, 'y' => 0 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => 0, 'y' => $tile_loc * 4 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc, 'y' => $tile_loc * 3 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc * 2, 'y' => $tile_loc * 2 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => 0, 'y' => $tile_loc * 5 ),
					// 11.
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => $tile_loc * 4, 'y' => $tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => $tile_loc * 5, 'y' => 0 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => $tile_loc * 2, 'y' => $tile_loc * 4 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc * 3, 'y' => $tile_loc * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => $tile_loc * 3, 'y' => $tile_loc * 5 ),
					// 16.
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => false, 'x' => $tile_loc * 5, 'y' => $tile_loc * 2 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc * 4, 'y' => $tile_loc * 4 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => true, 'x' => $tile_loc * 4, 'y' => $tile_loc * 5 ),
				);

			} elseif ( 'squares' == $layout ) {
				return array(
					// 1.
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[0], 'x' => 0, 'y' => 0 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[1], 'x' => 0, 'y' => $tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[2], 'x' => $tile_loc, 'y' => 0 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[3], 'x' => -$tile_loc, 'y' => $tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[4], 'x' => $tile_loc, 'y' => -$tile_loc ),
					// 6.
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[5], 'x' => -$tile_loc * 2, 'y' => $tile_loc * 2 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[6], 'x' => -$tile_loc, 'y' => $tile_loc * 2 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[7], 'x' => $tile_loc * 2, 'y' => -$tile_loc * 2 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[8], 'x' => $tile_loc * 2, 'y' => -$tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[9], 'x' => 0, 'y' => $tile_loc * 2 ),
					// 11.
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[10], 'x' => $tile_loc, 'y' => $tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[11], 'x' => $tile_loc * 2, 'y' => 0 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[12], 'x' => $tile_loc, 'y' => $tile_loc * 2 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[13], 'x' => $tile_loc * 2, 'y' => $tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[14], 'x' => -$tile_loc * 3, 'y' => $tile_loc * 3 ),
					// 16.
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[15], 'x' => -$tile_loc * 2, 'y' => $tile_loc * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[16], 'x' => -$tile_loc, 'y' => $tile_loc * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[17], 'x' => $tile_loc * 3, 'y' => -$tile_loc * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[18], 'x' => $tile_loc * 3, 'y' => -$tile_loc * 2 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[19], 'x' => $tile_loc * 3, 'y' => -$tile_loc ),
					// 21.
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[20], 'x' => 0, 'y' => $tile_loc * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[21], 'x' => $tile_loc * 3, 'y' => 0 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[22], 'x' => -$tile_loc * 3, 'y' => $tile_loc * 4 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[23], 'x' => -$tile_loc * 2, 'y' => $tile_loc * 4 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[24], 'x' => -$tile_loc, 'y' => $tile_loc * 4 ),
					// 26.
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[25], 'x' => $tile_loc * 4, 'y' => -$tile_loc * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[26], 'x' => $tile_loc * 4, 'y' => -$tile_loc * 2 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[27], 'x' => $tile_loc * 4, 'y' => -$tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[28], 'x' => -$tile_loc * 3, 'y' => $tile_loc * 5 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[29], 'x' => -$tile_loc * 2, 'y' => $tile_loc * 5 ),
					// 31.
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[30], 'x' => $tile_loc * 5, 'y' => -$tile_loc * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[31], 'x' => $tile_loc * 5, 'y' => -$tile_loc * 2 ),
				);

			} elseif ( 'squares-arrow' == $layout ) {
				return array(
					// 1.
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[0], 'x' => 0, 'y' => 0 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[1], 'x' => 0, 'y' => $tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[2], 'x' => $tile_loc, 'y' => 0 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[3], 'x' => $tile_loc, 'y' => $tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[4], 'x' => 0, 'y' => $tile_loc * 2 ),
					// 6.
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[5], 'x' => $tile_loc * 2, 'y' => 0 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[6], 'x' => $tile_loc, 'y' => $tile_loc * 2 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[7], 'x' => $tile_loc * 2, 'y' => $tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[8], 'x' => 0, 'y' => $tile_loc * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[9], 'x' => $tile_loc * 3, 'y' => 0 ),
					// 11.
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[10], 'x' => 0, 'y' => $tile_loc * 4 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[11], 'x' => $tile_loc, 'y' => $tile_loc * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[12], 'x' => $tile_loc * 2, 'y' => $tile_loc * 2 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[13], 'x' => $tile_loc * 3, 'y' => $tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[14], 'x' => $tile_loc * 4, 'y' => 0 ),
					// 16.
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[15], 'x' => $tile_loc, 'y' => $tile_loc * 4 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[16], 'x' => $tile_loc * 2, 'y' => $tile_loc * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[17], 'x' => $tile_loc * 3, 'y' => $tile_loc * 2 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[18], 'x' => $tile_loc * 4, 'y' => $tile_loc ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[19], 'x' => $tile_loc * 2, 'y' => $tile_loc * 4 ),
					// 21.
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[20], 'x' => $tile_loc * 3, 'y' => $tile_loc * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[21], 'x' => $tile_loc * 4, 'y' => $tile_loc * 2 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[22], 'x' => $tile_loc * 3, 'y' => $tile_loc * 4 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[23], 'x' => $tile_loc * 4, 'y' => $tile_loc * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[24], 'x' => $tile_loc * 4, 'y' => $tile_loc * 4 ),
				);

			} elseif ( 'squares-layered' == $layout ) {
				return array(
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[0], 'zindex' => 11, 'x' => 0, 'y' => 0 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[1], 'zindex' => 10, 'x' => $tile_gap, 'y' => $tile_gap ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[2], 'zindex' => 9, 'x' => $tile_gap * 2, 'y' => $tile_gap * 2 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[3], 'zindex' => 8, 'x' => $tile_gap * 3, 'y' => $tile_gap * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[4], 'zindex' => 7, 'x' => $tile_gap * 4, 'y' => $tile_gap * 4 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[5], 'zindex' => 6, 'x' => $tile_gap * 5, 'y' => $tile_gap * 5 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[6], 'zindex' => 5, 'x' => $tile_gap * 6, 'y' => $tile_gap * 6 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[7], 'zindex' => 4, 'x' => $tile_gap * 7, 'y' => $tile_gap * 7 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[8], 'zindex' => 3, 'x' => $tile_gap * 8, 'y' => $tile_gap * 8 ),
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[9], 'zindex' => 2, 'x' => $tile_gap * 9, 'y' => $tile_gap * 9 ),
				);

			} elseif ( 'single-square' == $layout ) {
				return array(
					array( 'width' => $tile_size, 'height' => $tile_size, 'rotated' => $rotations[0], 'x' => 0, 'y' => 0 ),
				);

			} elseif ( 'rectangles-left-layered' == $layout ) {
				return array(
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => $rotations[0], 'zindex' => 11, 'x' => 0, 'y' => 0 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => $rotations[1], 'zindex' => 10, 'x' => $tile_gap, 'y' => $tile_gap ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => $rotations[2], 'zindex' => 9, 'x' => $tile_gap * 2, 'y' => $tile_gap * 2 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => $rotations[3], 'zindex' => 8, 'x' => $tile_gap * 3, 'y' => $tile_gap * 3 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => $rotations[4], 'zindex' => 7, 'x' => $tile_gap * 4, 'y' => $tile_gap * 4 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => $rotations[5], 'zindex' => 6, 'x' => $tile_gap * 5, 'y' => $tile_gap * 5 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => $rotations[6], 'zindex' => 5, 'x' => $tile_gap * 6, 'y' => $tile_gap * 6 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => $rotations[7], 'zindex' => 4, 'x' => $tile_gap * 7, 'y' => $tile_gap * 7 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => $rotations[8], 'zindex' => 3, 'x' => $tile_gap * 8, 'y' => $tile_gap * 8 ),
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => $rotations[9], 'zindex' => 2, 'x' => $tile_gap * 9, 'y' => $tile_gap * 9 ),
				);

			} elseif ( 'single-rectangle-left' == $layout ) {
				return array(
					array( 'width' => $tile_size, 'height' => $tile_size_double, 'rotated' => $rotations[0], 'x' => 0, 'y' => 0 ),
				);

			} elseif ( 'rectangles-right-layered' == $layout ) {
				return array(
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => $rotations[0], 'zindex' => 11, 'x' => 0, 'y' => 0 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => $rotations[1], 'zindex' => 10, 'x' => $tile_gap, 'y' => $tile_gap ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => $rotations[2], 'zindex' => 9, 'x' => $tile_gap * 2, 'y' => $tile_gap * 2 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => $rotations[3], 'zindex' => 8, 'x' => $tile_gap * 3, 'y' => $tile_gap * 3 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => $rotations[4], 'zindex' => 7, 'x' => $tile_gap * 4, 'y' => $tile_gap * 4 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => $rotations[5], 'zindex' => 6, 'x' => $tile_gap * 5, 'y' => $tile_gap * 5 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => $rotations[6], 'zindex' => 5, 'x' => $tile_gap * 6, 'y' => $tile_gap * 6 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => $rotations[7], 'zindex' => 4, 'x' => $tile_gap * 7, 'y' => $tile_gap * 7 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => $rotations[8], 'zindex' => 3, 'x' => $tile_gap * 8, 'y' => $tile_gap * 8 ),
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => $rotations[9], 'zindex' => 2, 'x' => $tile_gap * 9, 'y' => $tile_gap * 9 ),
				);

			} elseif ( 'single-rectangle-right' == $layout ) {
				return array(
					array( 'width' => $tile_size_double, 'height' => $tile_size, 'rotated' => $rotations[0], 'x' => 0, 'y' => 0 ),
				);
			}
		}


		/**
		 * Shortcode logic.
		 *
		 * @param array  $atts - The attributes of the shortcode.
		 * @param string $content - The content enclosed inside the shortcode if any.
		 * @return string - The rendered html.
		 * @since 1.0
		 */
		public function render_shortcode( $atts, $content = null ) {
			$defaults = array(
				'height' => '230',
				'image_ids' => '',
				'layout' => 'rectangles',
				'image_direction' => 'left',
				'size' => '100',
				'gap' => '20',
				'angle' => '40',
				'tile_display' => '3d',
				'side_height' => '10',
				'surface_color' => '#00aeef',
				'left_side_color' => '#006c94',
				'right_side_color' => '#0084b5',
				'hover_distance' => '30',
				'hover_direction' => 'right',
				'lightbox' => '0',
				'el_class' => '',
				'lightbox_image' => 'image',
				'lightbox_id' => '',
				'lightbox_name' => '',
				'disable_mobile' => '0',
				'hover_effect' => '0',
			);
			if ( empty( $atts ) ) {
				$atts = array();
			}

			$atts = array_merge( $defaults, $atts );

			$id = self::$iso_tiles_id++;

			$ret = '';

			$image_ids = $atts['image_ids'];
			if ( empty($image_ids) ) {
				return;
			}
			elseif ( stripos( $image_ids, ',' ) != false ) {
					$image_ids = explode( ',', $image_ids );
			} else {
					$image_ids = array( $image_ids );
			}

			wp_enqueue_style( 'vc-iso-tiles', plugins_url( 'isometric-tiles/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_VC_ISO_TILES );
			wp_enqueue_script( 'vc-iso-tiles', plugins_url( 'isometric-tiles/js/min/script-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_VC_ISO_TILES, true );

			if ( '1' == $atts['lightbox'] ) {
				wp_enqueue_style( 'vc-iso-lightbox', plugins_url( 'isometric-tiles/css/lightbox.css', __FILE__ ), array(), VERSION_GAMBIT_VC_ISO_TILES );
				wp_enqueue_script( 'vc-iso-lightbox', plugins_url( 'isometric-tiles/js/min/lightbox-min.js', __FILE__ ), array( 'jquery' ), '1.3.3', true );
			}

			// Styles.
			$scale = 1.0 - (int) $atts['angle'] / 90;
			$hover_amount = (int) $atts['hover_distance'];
			$hover_translations = "translateX(-{$hover_amount}px) translateY(-{$hover_amount}px)";
			$is_vertical_hover = true;
			if ( stripos( $atts['layout'], 'layered' ) != false ) {
				if ( 'right' == $atts['hover_direction'] ) {
					$hover_translations = "translateX({$hover_amount}px) translateY(-{$hover_amount}px)";
				} else { // Left.
					$hover_translations = "translateX(-{$hover_amount}px) translateY({$hover_amount}px)";
				}
				$is_vertical_hover = false;
			}
			echo "
			<style>
			#iso-tiles-{$id} .iso-tiles-wrapper {
				-webkit-transform: scaleY({$scale});
				-moz-transform: scaleY({$scale});
				-ms-transform: scaleY({$scale});
				transform: scaleY({$scale});
			}
			#iso-tiles-{$id}.iso-tiles-container:not(.ie9) .iso-tile[data-link-to]:hover {
				-webkit-transform: {$hover_translations};
				-moz-transform: {$hover_translations};
				-ms-transform: {$hover_translations};
				transform: {$hover_translations};
			}
			#iso-tiles-{$id} .iso-tile-inner:before {
				background-color: {$atts['right_side_color']};
				width: {$atts['side_height']}px;
			}
			#iso-tiles-{$id} .iso-tile-inner:after {
				background-color: {$atts['left_side_color']};
				height: {$atts['side_height']}px;
			}
			</style>
			";

			$height = (int) $atts['height'];
			$padding = $is_vertical_hover ? ceil( ( (int) $atts['hover_distance'] / 0.707106781187 ) * $scale ) : 0;
			$ret .= "<div class='iso-tiles-container {$atts['el_class']}' id='iso-tiles-{$id}' style='height: {$height}px; padding-top: {$padding}px' data-layout='{$atts['layout']}' data-hover-direction='{$atts['hover_direction']}' data-disable-mobile='{$atts['disable_mobile']}'>";
			$ret .= "<div class='iso-tiles-wrapper'>";
			$ret .= "<div class='iso-tiles-inner'>";

			// Image locations based on the layout.
			$image_locations = $this->get_image_locations( $atts['layout'], $atts['size'], $atts['gap'], $atts['image_direction'] );

			foreach ( $image_ids as $image_index => $image_id ) {
				if ( empty( $image_locations[ $image_index ] ) ) {
					break;
				}
				if ( empty( $image_id ) ) {
					break;
				}
				$metadata = wp_get_attachment_metadata( $image_id );
				if ( ! $metadata  ) {
					break;
				}

				$image_location = $image_locations[ $image_index ];

				$image_width = $image_location['width'];
				$image_height = $image_location['height'];
				if ( $image_location['rotated'] ) {
					$image_width = $image_location['height'];
					$image_height = $image_location['width'];
				}

				$image_url = $this->get_correct_image( $image_id, $image_width, $image_height );

				$classes = array();
				$classes[] = $atts['tile_display'];
				if ( $image_location['rotated'] ) {
					$classes[] = 'rotated';
				}

				$data = array();
				$link_to = get_post_meta( $image_id, '_iso-tile-link-to', true );

				if ( ! empty( $atts['hover_distance'] ) ) {
					$data[] = "data-hover-amount='" . esc_attr( $atts['hover_distance'] ) . "'";
				} else {
					$data[] = "data-hover-amount='30'";
				}

				if ( empty( $atts['lightbox_id'] ) ) {
					$atts['lightbox_id'] = 'iso-gallery-' . $id;
				}
				$lightbox_ID = " data-lightbox='" . $atts['lightbox_id'] . "'";

				$style_tag = " style='background-image: url(" . esc_url( $image_url ) . "); width: {$image_width}px; height: {$image_height}px;'";
				$tag = "div" . $lightbox_ID;
				$closing_tag = "div";
				if ( '1' === $atts['lightbox'] ) {
					if ( ! empty( $link_to ) && filter_var( $link_to, FILTER_VALIDATE_URL ) && is_array( getimagesize( $link_to ) ) && 'meta' == $atts['lightbox_image'] ) {
						// Lightbox display is set to meta & "Tile Link to" is an image.
						$data[] = "data-link-to='" . esc_url( $link_to ) . "'";
						$tag = "a href='" . esc_url( $link_to ) . "' " . $lightbox_ID . " data-title='" . $atts['lightbox_name'] . "'";
						$closing_tag = 'a';
					} elseif ( ! empty( $link_to ) && filter_var( $link_to, FILTER_VALIDATE_URL ) && ! is_array( getimagesize( $link_to ) ) &&  'meta' == $atts['lightbox_image'] ) {
						// Lightbox display is set to meta & "Tile Link to" is NOT an image.
						$data[] = "data-link-to='" . esc_url( $link_to ) . "'";
						$tag = "a href='" . esc_url( $link_to ) . "' ";
						$closing_tag = 'a';
					} elseif ( '1' === $atts['lightbox'] && 'image' === $atts['lightbox_image'] ) {
						// Lightbox display is set to image.
						$data[] = "data-link-to='" . esc_url( $image_url ) . "'";
						$tag = "a href='" . esc_url( $image_url ) . "' " . $lightbox_ID . " data-title='" . $atts['lightbox_name'] . "'";
						$closing_tag = 'a';
					}

				} else {
					if ( ! empty( $link_to ) && filter_var( $link_to, FILTER_VALIDATE_URL ) ) {
						// "Tile Link to" is not empty.
						$data[] = "data-link-to='" . esc_url( $link_to ) . "'";
						$tag = "a href='" . esc_url( $link_to ) . "'";
						$closing_tag = 'a';
					}
				}

				if ( $atts['hover_effect'] === '1' ) {
					$data[] = "data-hover-on='true'";
				}

				$link_to_new_window = get_post_meta( $image_id, '_iso-tile-link-to-new-window', true );
				if ( ! empty( $link_to_new_window ) ) {
					if ( '0' !== $link_to_new_window ) {
						$tag .= " target='_blank'";
					}
				}

				$z_index = '';
				if ( ! empty( $image_location['zindex'] ) ) {
					$z_index = "z-index: {$image_location['zindex']};";
				}

				$ret .= "
				<div class='iso-tile " . join( ' ', $classes ) . "' style='width: {$image_location['width']}px; height: {$image_location['height']}px; top: {$image_location['y']}px; left: {$image_location['x']}px; {$z_index}' " . join( ' ', $data ) . ">
				  <div class='iso-tile-inner'>
					<{$tag} {$style_tag}></{$closing_tag}>
				  </div>
				</div>
				";
			} // End foreach().

			// Tiles inner.
			$ret .= '</div>';

			// Wrapper div.
			$ret .= '</div>';

			// Container div.
			$ret .= '</div>';

			return $ret;
		}
	}

	new GambitIsoTilesShortcode();
} // End if().
