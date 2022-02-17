<?php
/**
 * Mockups main functionality class
 *
 * @package Mockups for VC
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Initializes plugin class.
if ( ! class_exists( 'GambitMockupShortcode' ) ) {

	/**
	 * This is where all the plugin's functionality happens.
	 */
	class GambitMockupShortcode {

		/**
		 * Signifies first usage of the plugin.
		 *
		 * @var int $first_load  - Usage counter.
		 */
		private static $first_load  = 0;

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Initializes as a Visual Composer addon.
			add_action( 'init', array( $this, 'create_shortcodes' ), 999 );

			// Initialize as shortcode.
			add_shortcode( 'screen_mockup', array( $this, 'render_shortcode' ) );
		}


		/**
		 * Pulls video IDs from provided URLs.
		 *
		 * @param string $video_string - The YouTube URL as shortened to youtu.be.
		 * @return array - The proper IDs with identifiers, in an array element.
		 * @since	1.0
		 */
		public function gambit_get_video_provider( $video_string ) {
			$video_string = trim( $video_string );

			// Check for YouTube.
			$video_id = false;
			if ( preg_match( '/youtube\.com\/watch\?v=([^\&\?\/]+)/', $video_string, $id ) ) {
				if ( count( $id ) > 1 ) {
					$video_id = $id[1];
				}
			} elseif ( preg_match( '/youtube\.com\/embed\/([^\&\?\/]+)/', $video_string, $id ) ) {
				if ( count( $id ) > 1 ) {
					$video_id = $id[1];
				}
			} elseif ( preg_match( '/youtube\.com\/v\/([^\&\?\/]+)/', $video_string, $id ) ) {
				if ( count( $id ) > 1 ) {
					$video_id = $id[1];
				}
			} elseif ( preg_match( '/youtu\.be\/([^\&\?\/]+)/', $video_string, $id ) ) {
				if ( count( $id ) > 1 ) {
					$video_id = $id[1];
				}
			}
			if ( ! empty( $video_id ) ) {
				return array(
					'type' => 'youtube',
					'id' => $video_id,
				);
			}

			 // Check for Vimeo.
			if ( preg_match( '/vimeo\.com\/(\w*\/)*(\d+)/', $video_string, $id ) ) {
				if ( count( $id ) > 1 ) {
					$video_id = $id[ count( $id ) - 1 ];
				}
			}
			if ( ! empty( $video_id ) ) {
				return array(
					'type' => 'vimeo',
					'id' => $video_id,
				);
			}

			// Non-URL form.
			if ( preg_match( '/^\d+$/', $video_string ) ) {
				return array(
					'type' => 'vimeo',
					'id' => $video_string,
				);
			}
			return array(
				'type' => 'youtube',
				'id' => $video_string,
			);
		}


		/**
		 * Creates the loupe element inside VC.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function create_shortcodes() {
			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			vc_map( array(
				'name' => __( 'Screen Mockup', 'mockups' ),
				'base' => 'screen_mockup',
				'icon' => plugins_url( 'mockups/images/image-mockup-icon.svg', __FILE__ ),
				'description' => __( 'An image placed inside a mockup device', 'mockups' ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle', 'mockups' ) : '',
				'admin_enqueue_css' => plugins_url( 'mockups/css/admin.css', __FILE__ ),
				'params' => array(
					array(
						'type' => 'dropdown',
						'holder' => 'span',
						'heading' => __( 'Mockup Type', 'mockups' ),
						'param_name' => 'type',
						'value' => array(
							__( 'Image Mockup', 'mockups' ) => 'image',
							__( 'Video Mockup - YouTube', 'mockups' ) => 'video-youtube',
							__( 'Video Mockup - Vimeo', 'mockups' ) => 'video-vimeo',
							__( 'IFrame', 'mockups' ) => 'iframe',
							__( 'Custom Content', 'mockups' ) => 'custom',
						),
						'description' => __( 'Choose the type of mockup to display', 'mockups' ),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'IFrame URL to Show', 'mockups' ),
						'param_name' => 'iframe_src',
						'value' => '',
						'description' => __( 'Enter the URL to load in the iframe. Remember though that iframes are governed by the same-site policy and Javascripts can only parse iframes of the same origin.', 'mockups' ),
						'dependency' => array(
							'element' => 'type',
							'value' => array( 'iframe' ),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'textarea_html',
						'heading' => __( 'Custom Content', 'mockups' ),
						'param_name' => 'content',
						'value' => '',
						'description' => __( 'If you wish to place customizable content inside your mockup, do so here. Shortcodes are also accepted.', 'mockups' ),
						'dependency' => array(
							'element' => 'type',
							'value' => array( 'custom' ),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Scrollbars', 'mockups' ),
						'param_name' => 'content_scroll',
						'value' => array(
								__( 'If checked, scrolling will be enabled', 'mockups' ) => 'true',
						),
						'description' => '',
						'dependency' => array(
							'element' => 'type',
							'value' => array( 'custom' ),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'colorpicker',
						'class' => '',
						'heading' => __( 'Custom Content background color', 'mockups' ),
						'param_name' => 'content_bgcolor',
						'value' => '',
						'description' => __( 'Select the background color for your custom content, if you so choose', 'mockups' ),
						'dependency' => array(
							'element' => 'type',
							'value' => array( 'custom' ),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'attach_images',
						'heading' => __( 'Mocked Up Image', 'mockups' ),
						'param_name' => 'image_id',
						'value' => '',
						'description' => __( 'Select the image that will be displayed inside the mockup device. The size of your image adjusts depending on the width of your container, just remember that the device adjusts to fill the whole width of the container. Use a larger image if your mockup image gts a bit blurry because of scaling.<br><br><strong>If you upload multiple images, the mockup will be converted into a slider element.</strong><br><br>Your image will be cropped to fit the screen size of your mockup, you can use these dimensions to fit your images exactly into your mockups:<br>For browser mockups, use 1400x789, 700x395 or 350x198<br>For iPhone portrait mockups, use 640x1136 or 320x568<br>For iPhone landscape mockups, use 1136x640 or 568x320<br>For iPad portrait mockups, use 1536x2048, 768x1024 or 384x512<br>For iPad landscape mockups, use 2048x1536, 1024x768 or 512x384<br>For iMac mockups, use 2560x1440 1280x720 or 640x360<br>For MacBook Pro mockups, use 2880x1800 1440x900 or 720x450', 'mockups' ),
						'dependency' => array(
							'element' => 'type',
							'value' => array( 'image' ),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Mocked Up Video ID', 'mockups' ),
						'param_name' => 'video_id',
						'value' => '',
						'description' => __( 'Enter the ID of your YouTube or Vimeo video.<br><br>You can get the ID of your video from the URL. For example:<br>https://www.youtube.com/watch?v=XXXXXXXXX<br>https://youtu.be/XXXXXXXXX<br>http://vimeo.com/XXXXXXXXX<br><em>The XXXX is your video ID</em>', 'mockups' ),
						'dependency' => array(
							'element' => 'type',
							'value' => array( 'video-youtube', 'video-vimeo' ),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Hide Video Controls', 'mockups' ),
						'param_name' => 'hidecontrols',
						'value' => array(
								__( 'If checked, video controls will not be shown', 'mockups' ) => 'hidecontrols',
						),
						'description' => '',
						'dependency' => array(
							'element' => 'type',
							'value' => array( 'video-youtube' ),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'checkbox',
						'class' => '',
						'heading' => __( 'Mute Video', 'mockups' ),
						'param_name' => 'mute',
						'value' => array(
							__( 'No sound will play from the video being played', 'mockups' ) => 'mute',
						),
						'group' => __( 'General Options', 'mockups' ),
						'dependency' => array(
							'element' => 'type',
							'value' => array( 'video-youtube', 'video-vimeo' ),
						),
					),
					array(
						'type' => 'checkbox',
						'class' => '',
						'heading' => __( 'Loop Video', 'mockups' ),
						'param_name' => 'loop',
						'value' => array(
							__( 'The video will play repeatedly if checked', 'mockups' ) => '1',
						),
						'group' => __( 'General Options', 'mockups' ),
						'dependency' => array(
							'element' => 'type',
							'value' => array( 'video-youtube', 'video-vimeo' ),
						),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Autoplay Video', 'mockups' ),
						'param_name' => 'autoplay',
						'value' => array(
								__( 'If checked, your video will play automatically once loaded', 'mockups' ) => 'autoplay',
						),
						'description' => '',
						'dependency' => array(
							'element' => 'type',
							'value' => array( 'video-youtube', 'video-vimeo' ),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Horizontal Black Bar Video Fix', 'mockups' ),
						'param_name' => 'video_horizontal_fix',
						'value' => '0',
						'description' => __( 'If you&apos;re getting horizontal black bars in your video, you can widen the video to make the viewable area larger. Enter here a percentage value from 0 to 50.', 'mockups' ),
						'dependency' => array(
							'element' => 'type',
							'value' => array( 'video-youtube', 'video-vimeo' ),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Vertical Black Bar Video Fix', 'mockups' ),
						'param_name' => 'video_vertical_fix',
						'value' => '0',
						'description' => __( 'If you&apos;re getting vertical black bars in your video, you can increase the height of the video to make the viewable area larger. Enter here a percentage value from 0 to 50.', 'mockups' ),
						'dependency' => array(
							'element' => 'type',
							'value' => array( 'video-youtube', 'video-vimeo' ),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'dropdown',
						'holder' => 'span',
						'heading' => __( 'Mockup Device', 'mockups' ),
						'param_name' => 'device',
						'value' => array(
							__( 'Browser - Flat Silver', 'mockups' ) => 'browser-flat-silver',
							__( 'Browser - Flat White', 'mockups' ) => 'browser-flat-white',
							__( 'Browser - Flat Black', 'mockups' ) => 'browser-flat-black',
							__( 'iPhone - Flat White', 'mockups' ) => 'iphone-flat-white',
							__( 'iPhone - Flat Black', 'mockups' ) => 'iphone-flat-black',
							__( 'iPhone - Flat Gold', 'mockups' ) => 'iphone-flat-gold',
							__( 'iPhone - Outlined White', 'mockups' ) => 'iphone-outline-white',
							__( 'iPhone - Outlined Black', 'mockups' ) => 'iphone-outline-black',

							__( 'iPhone 6 / 6s / 7 / 7s - Flat Gold', 'mockups' ) => 'iphone_6-flat-gold',
							__( 'iPhone 6 / 6s / 7 / 7s - Flat Rose Gold', 'mockups' ) => 'iphone_6-flat-rose_gold',
							__( 'iPhone 6 / 6s / 7 / 7s - Flat Silver', 'mockups' ) => 'iphone_6-flat-silver',
							__( 'iPhone 6 / 6s / 7 / 7s - Flat Space Gray', 'mockups' ) => 'iphone_6-flat-space_gray',
							__( 'iPhone 6 / 6s / 7 / 7s - Outline Silver', 'mockups' ) => 'iphone_6-outline-silver',
							__( 'iPhone 6 / 6s / 7 / 7s - Outline Space Gray', 'mockups' ) => 'iphone_6-outline-space_gray',

							__( 'iPhone 6 / 6s / 7 / 7s Plus - Flat Gold', 'mockups' ) => 'iphone_6plus-flat-gold',
							__( 'iPhone 6 / 6s / 7 / 7s Plus - Flat Rose Gold', 'mockups' ) => 'iphone_6plus-flat-rose_gold',
							__( 'iPhone 6 / 6s / 7 / 7s Plus - Flat Silver', 'mockups' ) => 'iphone_6plus-flat-silver',
							__( 'iPhone 6 / 6s / 7 / 7s Plus - Flat Space Gray', 'mockups' ) => 'iphone_6plus-flat-space_gray',
							__( 'iPhone 6 / 6s / 7 / 7s Plus - Outline Silver', 'mockups' ) => 'iphone_6plus-outline-silver',
							__( 'iPhone 6 / 6s / 7 / 7s Plus - Outline Space Gray', 'mockups' ) => 'iphone_6plus-outline-space_gray',

							__( 'iPad - Flat White', 'mockups' ) => 'ipad-flat-white',
							__( 'iPad - Flat Black', 'mockups' ) => 'ipad-flat-black',
							__( 'iPad - Outlined White', 'mockups' ) => 'ipad-outline-white',
							__( 'iPad - Outlined Black', 'mockups' ) => 'ipad-outline-black',
							__( 'iMac - Flat Silver', 'mockups' ) => 'imac-flat-silver',
							__( 'iMac - Flat White', 'mockups' ) => 'imac-flat-white',
							__( 'iMac - Flat Black', 'mockups' ) => 'imac-flat-black',
							__( 'iMac - Outlined White', 'mockups' ) => 'imac-outline-white',
							__( 'iMac - Outlined Black', 'mockups' ) => 'imac-outline-black',
							__( 'MacBook Pro - Flat Silver', 'mockups' ) => 'macbook-flat-silver',
							__( 'MacBook Pro - Flat White', 'mockups' ) => 'macbook-flat-white',
							__( 'MacBook Pro - Flat Black', 'mockups' ) => 'macbook-flat-black',
							__( 'MacBook Pro - Outlined White', 'mockups' ) => 'macbook-outline-white',
							__( 'MacBook Pro - Outlined Black', 'mockups' ) => 'macbook-outline-black',
							__( 'Lumia 930 - Flat White', 'mockups' ) => 'lumia_930-flat-white',
							__( 'Lumia 930 - Flat Silver', 'mockups' ) => 'lumia_930-flat-silver',
							__( 'Lumia 930 - Flat Black', 'mockups' ) => 'lumia_930-flat-black',
							__( 'Lumia 930 - Outlined White', 'mockups' ) => 'lumia_930-outline-white',
							__( 'Lumia 930 - Outlined Black', 'mockups' ) => 'lumia_930-outline-black',
							__( 'Galaxy S5 - Flat White', 'mockups' ) => 'galaxy_s5-flat-white',
							__( 'Galaxy S5 - Flat Silver', 'mockups' ) => 'galaxy_s5-flat-silver',
							__( 'Galaxy S5 - Flat Black', 'mockups' ) => 'galaxy_s5-flat-black',
							__( 'Galaxy S5 - Flat Gold', 'mockups' ) => 'galaxy_s5-flat-gold',
							__( 'Galaxy S5 - Outlined White', 'mockups' ) => 'galaxy_s5-outline-white',
							__( 'Galaxy S5 - Outlined Black', 'mockups' ) => 'galaxy_s5-outline-black',
							__( 'HTC One M8 - Flat White', 'mockups' ) => 'htc_one_m8-flat-white',
							__( 'HTC One M8 - Flat White 2', 'mockups' ) => 'htc_one_m8-flat-white-2',
							__( 'HTC One M8 - Flat Silver', 'mockups' ) => 'htc_one_m8-flat-silver',
							__( 'HTC One M8 - Flat Gold', 'mockups' ) => 'htc_one_m8-flat-gold',
							__( 'HTC One M8 - Flat Red', 'mockups' ) => 'htc_one_m8-flat-red',
							__( 'HTC One M8 - Outlined White', 'mockups' ) => 'htc_one_m8-outline-white',
							__( 'HTC One M8 - Outlined Black', 'mockups' ) => 'htc_one_m8-outline-black',
						),
						'description' => __( 'The device to display your image in', 'mockups' ),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'dropdown',
						'holder' => 'span',
						'heading' => __( 'Device Orientation', 'mockups' ),
						'param_name' => 'orientation',
						'value' => array(
							__( 'Portrait', 'mockups' ) => 'portrait',
							__( 'Landscape', 'mockups' ) => 'landscape',
						),
						'description' => __( 'The orientation of the selected device above', 'mockups' ),
						'dependency' => array(
							'element' => 'device',
							'value' => array(
								'iphone-flat-white',
								'iphone-flat-black',
								'iphone-flat-gold',
								'iphone-outline-white',
								'iphone-outline-black',
								'iphone_6-flat-gold',
								'iphone_6-flat-rose_gold',
								'iphone_6-flat-silver',
								'iphone_6-flat-space_gray',
								'iphone_6-outline-silver',
								'iphone_6-outline-space_gray',
								'iphone_6plus-flat-gold',
								'iphone_6plus-flat-rose_gold',
								'iphone_6plus-flat-silver',
								'iphone_6plus-flat-space_gray',
								'iphone_6plus-outline-silver',
								'iphone_6plus-outline-space_gray',
								'ipad-flat-white',
								'ipad-flat-black',
								'ipad-outline-white',
								'ipad-outline-black',
								'lumia_930-flat-white',
								'lumia_930-flat-silver',
								'lumia_930-flat-black',
								'lumia_930-outline-white',
								'lumia_930-outline-black',
								'galaxy_s5-flat-white',
								'galaxy_s5-flat-silver',
								'galaxy_s5-flat-black',
								'galaxy_s5-flat-gold',
								'galaxy_s5-outline-white',
								'galaxy_s5-outline-black',
								'htc_one_m8-flat-white',
								'htc_one_m8-flat-white-2',
								'htc_one_m8-flat-silver',
								'htc_one_m8-flat-gold',
								'htc_one_m8-flat-red',
								'htc_one_m8-outline-white',
								'htc_one_m8-outline-black',
							),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Flip Mockup Device', 'mockups' ),
						'param_name' => 'rotated',
						'value' => array(
								__( 'By default, mockup devices are displayed standing up for portrait, or rotated to the left for landscape. Check this if you want to flip the device.', 'mockups' ) => 'rotated',
						),
						'description' => '',
						'dependency' => array(
							'element' => 'device',
							'value' => array(
								'iphone-flat-white',
								'iphone-flat-black',
								'iphone-flat-gold',
								'iphone-outline-white',
								'iphone-outline-black',
								'iphone_6-flat-gold',
								'iphone_6-flat-rose_gold',
								'iphone_6-flat-silver',
								'iphone_6-flat-space_gray',
								'iphone_6-outline-silver',
								'iphone_6-outline-space_gray',
								'iphone_6plus-flat-gold',
								'iphone_6plus-flat-rose_gold',
								'iphone_6plus-flat-silver',
								'iphone_6plus-flat-space_gray',
								'iphone_6plus-outline-silver',
								'iphone_6plus-outline-space_gray',
								'ipad-flat-white',
								'ipad-flat-black',
								'ipad-outline-white',
								'ipad-outline-black',
								'lumia_930-flat-white',
								'lumia_930-flat-silver',
								'lumia_930-flat-black',
								'lumia_930-outline-white',
								'lumia_930-outline-black',
								'galaxy_s5-flat-white',
								'galaxy_s5-flat-silver',
								'galaxy_s5-flat-black',
								'galaxy_s5-flat-gold',
								'galaxy_s5-outline-white',
								'galaxy_s5-outline-black',
								'htc_one_m8-flat-white',
								'htc_one_m8-flat-white-2',
								'htc_one_m8-flat-silver',
								'htc_one_m8-flat-gold',
								'htc_one_m8-flat-red',
								'htc_one_m8-outline-white',
								'htc_one_m8-outline-black',
							),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Device Opacity', 'mockups' ),
						'param_name' => 'opacity',
						'value' => '1.0',
						'description' => __( 'For outlined mockup devices, you can change the opacity of the outline. Values here can be 0.0 to 1.0.', 'mockups' ),
						'dependency' => array(
							'element' => 'device',
							'value' => array(
								'iphone-outline-white',
								'iphone-outline-black',
								'iphone_6-outline-silver',
								'iphone_6-outline-space_gray',
								'iphone_6plus-outline-silver',
								'iphone_6plus-outline-space_gray',
								'ipad-outline-white',
								'ipad-outline-black',
								'imac-outline-white',
								'imac-outline-black',
								'macbook-outline-white',
								'macbook-outline-black',
								'lumia_930-outline-white',
								'lumia_930-outline-black',
								'galaxy_s5-outline-white',
								'galaxy_s5-outline-black',
								'htc_one_m8-outline-white',
								'htc_one_m8-outline-black',
							),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Mockup Display Area', 'mockups' ),
						'param_name' => 'display_area',
						'value' => array(
							__( 'Display the whole device', 'mockups' ) => 'full',
							__( 'Display only the top part of the device', 'mockups' ) => 'top',
							__( 'Display only the bottom part of the device', 'mockups' ) => 'bottom',
							__( 'Display only the left part of the device', 'mockups' ) => 'left',
							__( 'Display only the right part of the device', 'mockups' ) => 'right',
						),
						'description' => __( 'Use if you want to display only a specific portion of the mockup', 'mockups' ),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Mockup Display Area Offset', 'mockups' ),
						'param_name' => 'display_offset',
						'value' => '50',
						'description' => __( 'Select a value from 0 (no offset) to 75 (show 1/4 of the device)', 'mockups' ),
						'dependency' => array(
							'element' => 'display_area',
							'value' => array( 'top', 'bottom', 'left', 'right' ),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Horizontal Offset', 'mockups' ),
						'param_name' => 'horizontal_offset',
						'value' => '0',
						'description' => __( 'Select a negative value to pull the mockup to the left, or a positive value to push it to the right', 'mockups' ),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Add Link to Image', 'mockups' ),
						'param_name' => 'link',
						'value' => '',
						'description' => __( '<strong>(Tip: Links will only work if you have selected a single image for your mockup)</strong>', 'mockups' ),
						'dependency' => array(
							'element' => 'type',
							'value' => array( 'image' ),
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Open Link in a New Window', 'mockups' ),
						'param_name' => 'newwindow',
						'value' => array(
							__( '<strong>(Tip: Links will only work if you have selected a single image for your mockup)</strong>', 'mockups' ) => 'newwindow',
						),
						'description' => '',
						'dependency' => array(
							'element' => 'link',
							'not_empty' => true,
						),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Extra class name', 'js_composer' ),
						'param_name' => 'el_class',
						'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file', 'js_composer' ),
						'group' => __( 'General Options', 'mockups' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Slider Duration', 'mockups' ),
						'param_name' => 'slider_duration',
						'value' => '5000',
						'description' => __( 'The duration in milliseconds each slide will stay until switching to the other slide', 'mockups' ),
						'group' => __( 'Slider Options', 'mockups' ),
					),
				),
			) );
		}

		/**
		 * Shortcode logic.
		 *
		 * @param array  $atts - The attributes of the shortcode.
		 * @param string $content - The content enclosed inside the shortcode if any.
		 * @return string - The rendered html.
		 * @since	1.0
		 */
		public function render_shortcode( $atts, $content = null ) {
			$defaults = array(
				'type' => 'image',
				'video_id' => '',
				'hidecontrols' => '',
				'mute' => '',
				'loop' => '0',
				'autoplay' => '',
				'video_horizontal_fix' => '0',
				'video_vertical_fix' => '0',
				'device' => 'browser-flat-silver',
				'orientation' => 'portrait',
				'rotated' => '',
				'opacity' => '1.0',
				'image_id' => '',
				'display_area' => 'full',
				'display_offset' => '50',
				'horizontal_offset' => '0',
				'link' => '',
				'newwindow' => '',
				'el_class' => '',
				'slider_duration' => '5000',
				'iframe_src' => '',
				'content_bgcolor' => '',
				'content_scroll' => '',
			);
			$atts = array_merge( $defaults, $atts );

			$el_class = ' ' . $atts['el_class'];
			$is_slider = strpos( $atts['image_id'], ',' ) !== false;

			wp_enqueue_script( 'vc-mockup-scripts', plugins_url( 'mockups/js/min/script-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_VC_MOCKUP, true );
			wp_enqueue_style( 'vc-mockup', plugins_url( 'mockups/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_VC_MOCKUP );

			if ( ( empty( $atts['image_id'] ) && 'image' === $atts['type'] ) && ( empty( $atts['video_id'] ) && ( 'video-youtube' === $atts['type'] || 'video-vimeo' === $atts['type'] ) ) ) {
				return '';
			}

			// Determine if multiple images exist, by exploding commas.
			if ( $is_slider ) {

				wp_enqueue_style( 'vc-mockup-owl', plugins_url( 'mockups/css/owl.carousel.css', __FILE__ ), array(), VERSION_GAMBIT_VC_MOCKUP );
				wp_enqueue_style( 'vc-mockup-owl-style', plugins_url( 'mockups/css/owl.theme.default.css', __FILE__ ), array(), VERSION_GAMBIT_VC_MOCKUP );
				wp_enqueue_script( 'vc-mockup-owl', plugins_url( 'mockups/js/min/owl.carousel2-min.js', __FILE__ ), array( 'jquery' ), '1.3.3', true );

				// Separate each numbers delimited by a comma.
				$images = explode( ',', $atts['image_id'] );

				foreach ( $images as $imgtoshow ) {
					$image = wp_get_attachment_image_src( $imgtoshow, 'full' );
					$image_url[] = esc_url( $image[0] );
				}
			} else {
				$image = wp_get_attachment_image_src( $atts['image_id'], 'full' );
				$image_url[0] = esc_url( $image[0] );
			}

			$ret = '';

			preg_match( '/^(\\w+)-/', $atts['device'], $matches );

			if ( count( $matches ) < 2 ) {
				return '';
			}

			$classes = array();
			$landscape = '';
			if ( preg_match( '/^(ipad|iphone|iphone_6|iphone_6plus|lumia_930|galaxy_s5|htc_one_m8)-/', $atts['device'] ) ) {
				if ( ! empty( $atts['orientation'] ) ) {
					$classes[] = $atts['orientation'];
				}
				if ( 'landscape' === $atts['orientation'] ) {
					$landscape = '-' . $atts['orientation'];
				}
				if ( ! empty( $atts['rotated'] ) ) {
					$classes[] = $atts['rotated'];
				}
			}
			$classes = implode( ' ', $classes );

			$style = '';
			if ( preg_match( '/outline/', $atts['device'] ) ) {
				$style = "style='opacity: {$atts['opacity']}'";
			}

			$inner_styles = '';
			$data_clip = '';
			$data_clip_amount = '';
			if ( ! empty( $atts['display_area'] ) ) {
				if ( 'full' !== $atts['display_area'] ) {
					$margin_location = 'left';
					if ( 'top' === $atts['display_area'] ) {
						$margin_location = 'bottom';
					} elseif ( 'bottom' === $atts['display_area'] ) {
						$margin_location = 'top';
					} elseif ( 'left' === $atts['display_area'] ) {
						$margin_location = 'right';
					}
					if ( 'left' === $atts['display_area'] || 'right' === $atts['display_area'] ) {
						if ( ! empty( $atts['display_offset'] ) && is_numeric( $atts['display_offset'] ) ) {
							$inner_styles .= "margin-{$margin_location}: -" . ( (float) $atts['display_offset'] * 2 ) . '%;';
						}
					} else {
						$data_clip = $atts['display_area'];
						$data_clip_amount = $atts['display_offset'];
					}
				}
			}
			if ( ! empty( $data_clip ) ) {
				$data_clip = "data-clip='{$data_clip}'";
				$data_clip_amount = "data-clip-amount='{$data_clip_amount}'";
			}

			if ( ! empty( $inner_styles ) ) {
				$inner_styles = "style='{$inner_styles}'";
			}

			$container_style = '';
			$container_styles = '';
			if ( ! empty( $atts['horizontal_offset'] ) ) {
				$container_style .= "left: {$atts['horizontal_offset']}px;";
			}
			if ( ! empty( $container_style ) ) {
				$container_styles = "style='{$container_style}'";
			}

			$ret .= "<div class='gambit_mock_container{$el_class}' {$container_styles}>";

			$ret .= "<figure class='gambit_mock_{$matches[1]} {$classes}' {$inner_styles} {$data_clip} {$data_clip_amount}>";

			$ret .= "<img class='gambit_mock_bg' src='" . plugins_url( "mockups/images/{$atts['device']}{$landscape}.png", __FILE__ ) . "' {$style} alt='{$matches[1]} background'/>";

			if ( 'image' === $atts['type'] ) {

				$tag = 'div';
				$href = '';
				$new_window = '';
				$imgtolink = '';
				if ( ! empty( $atts['link'] ) ) {
					$tag = 'a';
					$href = "href='{$atts['link']}'";
					if ( ! empty( $atts['newwindow'] ) && ! empty( $atts['link'] ) ) {
						$new_window = 'target="_blank"';
					}
				}
				if ( $is_slider ) {
					$ret .= '<div class="owl-mockup-carousel slider-container gambit_mock_content"  data-slider-duration="' . $atts['slider_duration'] . '">';
					foreach ( $image_url as $imgsrcs ) {
						$ret .= "<{$tag} {$imgtolink} {$new_window} style='display: block; height: 100%; background-image: url(" . esc_url( $imgsrcs ) . ")'";
						$ret .= '>';
						$ret .= "&nbsp;</{$tag}>";
					}
					$ret .= '</div>';
				} else {
					$imgtolink = $href;
					$ret .= "<{$tag} {$imgtolink} {$new_window} class='gambit_mock_content' style='background-image: url(" . esc_url( $image_url[0] ) . ")'";
					$ret .= '>';
					$ret .= "&nbsp;</{$tag}>";
				}
			} elseif ( 'video-youtube' === $atts['type'] || 'video-vimeo' === $atts['type'] ) {

				$video_url_raw = $this->gambit_get_video_provider( $atts['video_id'] );
				$video_url = $video_url_raw['id'];

				$style = '';
				if ( ! empty( $atts['video_horizontal_fix'] ) ) {
					$style .= 'max-width: ' . ( (float) $atts['video_horizontal_fix'] + 100 ) . '%; width: ' . ( (float) $atts['video_horizontal_fix'] + 100 ) . '%; left: -' . ( (float) $atts['video_horizontal_fix'] / 2 ) . '%;';
				}
				if ( ! empty( $atts['video_vertical_fix'] ) ) {
					$style .= 'max-height: ' . ( (float) $atts['video_vertical_fix'] + 100 ) . '%; height: ' . ( (float) $atts['video_vertical_fix'] + 100 ) . '%; top: -' . ( (float) $atts['video_vertical_fix'] / 2 ) . '%;';
				}
				if ( ! empty( $style ) ) {
					$style = "style='{$style}'";
				}
				$autoplay = '0';
				if ( ! empty( $atts['autoplay'] ) ) {
					$autoplay = '1';
				}
				$hidecontrols = '2';
				if ( ! empty( $atts['hidecontrols'] ) ) {
					$hidecontrols = '0';
				}

				if ( 'video-vimeo' === $atts['type'] ) {

					wp_enqueue_script( 'vc-mockup-froogaloop', plugins_url( 'mockups/js/min/froogaloop-fixed-min.js', __FILE__ ), array(), VERSION_GAMBIT_VC_MOCKUP, true );
					$ret .= "<div class='gambit_mock_content vimeo_mockup' data-mute='" . esc_attr( $atts['mute'] ) . "'><iframe id='vimeo-mockup-" . esc_attr( $video_url ) . "' class='mockup-vimeo-iframe' src='https://player.vimeo.com/video/" . esc_attr( $video_url ) . "?autoplay={$autoplay}&autopause=0&badge=0&player_id=vimeo-mockup-" . esc_attr( $video_url ) . '&byline=0&portrait=0&title=0&html5=1&loop=' . $atts['loop'] . "' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen {$style}></iframe></div>";

				} else {
					$style .= '';
					$mute = ( 'mute' === $atts['mute'] ? 'event.target.mute();' : '' );
					$ret .= '<div class="gambit_mock_content youtube_mockup_element">'
							. '<div id="ytplayer-' . esc_attr( $video_url ) . '" class="youtube_mockup youtube-' . esc_attr( $video_url ) . '"' . $style . ' '
							. 'data-mute="' . esc_attr( $atts['mute'] ) . '" data-video-id="' . esc_attr( $video_url ) . '" data-autoplay="' . $autoplay . '" data-loop="' . $atts['loop'] . '" data-hidecontrols="' . $hidecontrols . '"></div>'
							. '</div>';
				}
			} elseif ( 'iframe' === $atts['type'] ) {
				 $ret .= '<div class="gambit_mock_content iframe_content"><iframe class="gambit_iframe_content_inner" src="' . esc_url( $atts['iframe_src'] ) . '"></iframe></div>';
			} elseif ( 'custom' === $atts['type'] ) {
				$custom_styling = array();
				$custom_styling[] = '' !== $atts['content_bgcolor'] ? 'background-color: ' . esc_attr( $atts['content_bgcolor'] ) . ';' : '';
				$custom_styling[] = 'true' === $atts['content_scroll'] ? 'overflow-y: ' . esc_attr( $atts['content_scroll'] ) . ';' : '';
				$custom_style = ! empty( $custom_styling ) ? ' style="' . esc_attr( implode( ' ', $custom_styling ) ) . '"' : '';
				$ret .= '<div class="gambit_mock_content custom_content"' . $custom_style . '>';
				$ret .= do_shortcode( $content );
				$ret .= '</div>';
			} // End if().

			$ret .= '</figure>';

			$ret .= '</div>';

			return $ret;
		}
	}

	new GambitMockupShortcode();
} // End if().
