<?php
/**
 *  @package Video Popup for Visual Composer.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

if ( ! class_exists( 'GMB_Video_Popup_Shortcode' ) ) {

	class GMB_Video_Popup_Shortcode {

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Initialize plugin.
			add_shortcode( 'video_popup', array( $this, 'render_shortcode' ) );

			// Create as a Visual Composer addon.
			add_action( 'init', array( $this, 'create_shortcode' ), 999 );

			// Add our own custom VC param for file picker.
			add_action( 'after_setup_theme', array( $this, 'add_file_picker_param' ) );
		}

		/**
		 * Include the file picker custom parameter.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function add_file_picker_param() {
			if ( ! function_exists( 'vc_add_shortcode_param' ) ) {
				return;
			}
			vc_add_shortcode_param( 'file_picker', array( $this, 'file_picker_settings_field' ), plugins_url( '/video-popup/js/min/file-picker-min.js', __FILE__ ) );
		}


		/**
		 * Add file picker shartcode param.
		 *
		 * @param array $settings   Array of param seetings.
		 * @param int   $value      Param value.
		 *
		 * @since	1.0
		 */
		function file_picker_settings_field( $settings, $value ) {
			$output = '';
			$select_file_class = '';
			$remove_file_class = ' hidden';
			$attachment_url = wp_get_attachment_url( $value );
			if ( $attachment_url ) {
				$select_file_class = ' hidden';
				$remove_file_class = '';
			}
			$output .= '<div class="file_picker_block">
						  <div class="' . esc_attr( $settings['type'] ) . '_display">' .
							$attachment_url .
						  '</div>
						  <input type="hidden" name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value wpb-textinput ' .
						   esc_attr( $settings['param_name'] ) . ' ' .
						   esc_attr( $settings['type'] ) . '_field" value="' . esc_attr( $value ) . '" />
						  <button class="button file-picker-button' . $select_file_class . '">Select File</button>
						  <button class="button file-remover-button' . $remove_file_class . '">Remove File</button>
						</div>
						';
			return $output;
		}


		/**
		 * Creates the necessary shortcode for text effects.
		 *
		 * @since 1.0
		 */
		public function create_shortcode() {
			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			vc_map( array(
				'base' => 'video_popup',
				'name' => __( 'Video Lightbox', GAMBIT_VC_VIDEO_POPUP ),
				'description' => __( 'Fullscreen-like Video Lightbox', GAMBIT_VC_VIDEO_POPUP ),
				'icon' => plugins_url( 'video-popup/images/vc-video.png', __FILE__ ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle', GAMBIT_VC_VIDEO_POPUP ) : '',
				'params' => array(
					array(
						'type' => 'dropdown',
						'heading' => __( 'Video Type', GAMBIT_VC_PARALLAX_BG ),
						'param_name' => 'video_type',
						'group' => __( 'Video', GAMBIT_VC_VIDEO_POPUP ),
						'value' => array(
							'YouTube or Vimeo' => 'yt_vimeo',
							'Upload Video (Self Hosted)' => 'upload',
						),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'YouTube ID, Vimeo ID or Video URL to play', GAMBIT_VC_VIDEO_POPUP ),
						'param_name' => 'video_id',
						'value' => '',
						'description' => __( 'Enter the YouTube or Vimeo video ID or URL to play inside the popup.', GAMBIT_VC_VIDEO_POPUP ),
						'group' => __( 'Video', GAMBIT_VC_VIDEO_POPUP ),
						'dependency' => array(
							'element' => 'video_type',
							'value' => array( 'yt_vimeo' ),
						),
					),
					array(
						'type' => 'file_picker',
						'heading' => __( 'MP4 Video to play', GAMBIT_VC_VIDEO_POPUP ),
						'param_name' => 'video_mp4',
						'value' => '',
						'description' => __( 'Use this to upload an MP4 video to play inside the popup. Also upload the WEBM version below.', GAMBIT_VC_VIDEO_POPUP ),
						'group' => __( 'Video', GAMBIT_VC_VIDEO_POPUP ),
						'dependency' => array(
							'element' => 'video_type',
							'value' => array( 'upload' ),
						),
					),
					array(
						'type' => 'file_picker',
						'heading' => __( 'WEBM Video to play', GAMBIT_VC_VIDEO_POPUP ),
						'param_name' => 'video_webm',
						'value' => '',
						'description' => __( 'Use this to upload a WEBM video to play inside the popup. Also upload the MP4 version below.', GAMBIT_VC_VIDEO_POPUP ),
						'group' => __( 'Video', GAMBIT_VC_VIDEO_POPUP ),
						'dependency' => array(
							'element' => 'video_type',
							'value' => array( 'upload' ),
						),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Thumbnail Type', GAMBIT_VC_PARALLAX_BG ),
						'param_name' => 'thumb_type',
						'group' => __( 'Thumbnail', GAMBIT_VC_VIDEO_POPUP ),
						'value' => array(
							'Image' => 'image',
							'Upload Video (Self Hosted)' => 'video',
						),
					),
					array(
						'type' => 'attach_image',
						'heading' => __( 'Thumbnail image', GAMBIT_VC_VIDEO_POPUP ),
						'param_name' => 'thumb_image',
						'value' => '',
						'description' => __( 'Note: The thumbnail always spans 100% of the available width.', GAMBIT_VC_VIDEO_POPUP ),
						'group' => __( 'Thumbnail', GAMBIT_VC_VIDEO_POPUP ),
						'dependency' => array(
							'element' => 'thumb_type',
							'value' => array( 'image' ),
						),
					),
					array(
						'type' => 'file_picker',
						'heading' => __( 'MP4 video thumbnail', GAMBIT_VC_VIDEO_POPUP ),
						'param_name' => 'thumb_mp4',
						'value' => '',
						'description' => __( 'Pick an MP4 video thumbnail. The thumbnail always spans 100% of the available width. Also upload the WEBM version below.', GAMBIT_VC_VIDEO_POPUP ),
						'group' => __( 'Thumbnail', GAMBIT_VC_VIDEO_POPUP ),
						'dependency' => array(
							'element' => 'thumb_type',
							'value' => array( 'video' ),
						),
					),
					array(
						'type' => 'file_picker',
						'heading' => __( 'WEBM video thumbnail', GAMBIT_VC_VIDEO_POPUP ),
						'param_name' => 'thumb_webm',
						'value' => '',
						'description' => __( 'Pick a WEBM video thumbnail. The thumbnail always spans 100% of the available width. Also upload the MP4 version above.', GAMBIT_VC_VIDEO_POPUP ),
						'group' => __( 'Thumbnail', GAMBIT_VC_VIDEO_POPUP ),
						'dependency' => array(
							'element' => 'thumb_type',
							'value' => array( 'video' ),
						),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Thumbnail image size / resolution', GAMBIT_VC_VIDEO_POPUP ),
						'param_name' => 'thumb_image_size',
						'value' => 'full',
						'description' => __( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', GAMBIT_VC_VIDEO_POPUP ),
						'group' => __( 'Thumbnail', GAMBIT_VC_VIDEO_POPUP ),
						'dependency' => array(
							'element' => 'thumb_type',
							'value' => array( 'image' ),
						),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Thumbnail Tint Color', GAMBIT_VC_VIDEO_POPUP ),
						'param_name' => 'thumb_tint',
						'value' => '#000000',
						'group' => __( 'Thumbnail', GAMBIT_VC_VIDEO_POPUP ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Play Icon', GAMBIT_VC_VIDEO_POPUP ),
						'param_name' => 'icon',
						'value' => array(
							__( 'Simple Play Icon', GAMBIT_VC_VIDEO_POPUP ) => 'simple',
							__( 'Circle Play Icon', GAMBIT_VC_VIDEO_POPUP ) => 'circle',
							__( 'Outlined Circle Play Icon', GAMBIT_VC_VIDEO_POPUP ) => 'circle_outline',
						),
						'group' => __( 'Play Icon', GAMBIT_VC_VIDEO_POPUP ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Icon Color', GAMBIT_VC_VIDEO_POPUP ),
						'param_name' => 'icon_color',
						'value' => '#ffffff',
						'group' => __( 'Play Icon', GAMBIT_VC_VIDEO_POPUP ),
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
		 * @since 1.0
		 */
		public function render_shortcode( $atts, $content = '' ) {

			$defaults = array(
				'video_type' => 'yt_vimeo',
				'video_id' => '',
				'video_mp4' => '',
				'video_webm' => '',
				'thumb_type' => 'image',
				'thumb_image' => '',
				'thumb_mp4' => '',
				'thumb_webm' => '',
				'thumb_image_size' => 'full',
				'thumb_tint' => '#000000',
				'icon' => 'simple',
				'icon_color' => '#ffffff',
			);

			if ( empty( $atts ) ) {
				$atts = array();
			}
			$atts = array_merge( $defaults, $atts );

			wp_enqueue_style( 'video-popup', plugins_url( 'video-popup/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_VC_VIDEO_POPUP );
			wp_enqueue_script( 'video-popup', plugins_url( 'video-popup/js/min/video-popup-min.js', __FILE__ ), array(), VERSION_GAMBIT_VC_VIDEO_POPUP );

			$video_atts = array();
			$video_atts[] = 'style="background: ' . esc_attr( $atts['thumb_tint'] ) . '"';

			if ( $atts['video_type'] === 'yt_vimeo' ) {
				$video_url = $atts['video_id'];

				// Also support people pasting in whole links.
				if ( preg_match( '/^https?:/', $video_url ) ) {
					$video_meta = self::get_video_provider( $video_url );
					$video_url = $video_meta['id'];
				}

				$video_atts[] = 'data-video="' . esc_attr( $video_url ) . '"';
			} else {
				$video_mp4 = wp_get_attachment_url( $atts['video_mp4'] );
				$video_webm = wp_get_attachment_url( $atts['video_webm'] );
				$video_atts[] = 'data-video="' . esc_attr( $video_mp4 ) . '"';
				$video_atts[] = 'data-webm="' . esc_attr( $video_webm ) . '"';
			}

			$video = '<div class="eb-video-popup" ' . implode( ' ', $video_atts ) . '>';

			// Thumbnail.
			if ( $atts['thumb_type'] === 'image' ) {
				$thumb_url = self::get_image_by_size( $atts['thumb_image'], $atts['thumb_image_size'] );
				$video .= '<div class="eb-video-preview" style="background-image: url(\'' . esc_url( $thumb_url ) . '\');"></div>';
			} else {
				$video .= '<video class="eb-video-preview" autoPlay loop muted playsinline="true">';
				if ( ! empty( $atts['thumb_webm'] ) ) {
					$thumb_webm = wp_get_attachment_url( $atts['thumb_webm'] );
					$video .= '<source src="' . esc_url( $thumb_webm ) . '" type="video/webm">';
				}
				if ( ! empty( $atts['thumb_mp4'] ) ) {
					$thumb_mp4 = wp_get_attachment_url( $atts['thumb_mp4'] );
					$video .= '<source src="' . esc_url( $thumb_mp4 ) . '" type="video/mp4">';
				}
				$video .= '</video>';
			}

			$video .= '<div class="eb-video-wrapper">';
			$video .= '<a href="#"></a>';

			$video .= '<span class="eb-play-button">';

			$style = 'style="fill: ' . esc_attr( $atts['icon_color'] ) . ' !important;"';
			if ( $atts['icon'] === 'simple' ) {
				$video .= '<svg ' . $style . ' xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 256 320"><path d="M0 0v320l256-160L0 0z"/></svg>';
			} else if ( $atts['icon'] === 'circle' ) {
				$video .= '<svg ' . $style . ' xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 40 40"><path d="M16 29l12-9-12-9v18zm4-29C8.95 0 0 8.95 0 20s8.95 20 20 20 20-8.95 20-20S31.05 0 20 0zm0 36c-8.82 0-16-7.18-16-16S11.18 4 20 4s16 7.18 16 16-7.18 16-16 16z"/></svg>';
			} else {
				$video .= '<svg ' . $style . ' xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 34 34"><path d="M17 34C7.6 34 0 26.4 0 17S7.6 0 17 0s17 7.6 17 17-7.6 17-17 17zm0-32C8.7 2 2 8.7 2 17s6.7 15 15 15 15-6.7 15-15S25.3 2 17 2z"/><path d="M12 25.7V8.3L27 17l-15 8.7zm2-14v10.5l9-5.3-9-5.2z"/></svg>';
			}

			$video .= '</span>';

			$video .= '</div>';
			$video .= '</div>';

			return $video;
		}


		/**
		 * Gets the Video ID & Provider from a video URL or ID.
		 *
		 * @param string $video_string - The URL or ID of a video.
		 * @return	array - The container whether the video is a YouTube video or a Vimeo video along with the video ID.
		 * @since	1.0
		 */
		protected static function get_video_provider( $video_string ) {

			$video_string = trim( $video_string );

			/*
			 * Check for YouTube.
			 */
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

			/*
			 * Check for Vimeo.
			 */
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

			/*
			 * Non-URL form.
			 */
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
		 * Gets the URL of a resized image.
		 *
		 * @param	int $image_id Image / attachment ID
		 * @param	string $size Image size, can be wxh
		 *
		 * @return	string The image URL
		 */
		protected static function get_image_by_size( $image_id, $size = 'thumbnail' ) {
			global $_wp_additional_image_sizes;
			$thumbnail = '';

			if ( is_string( $size ) && ( ( ! empty( $_wp_additional_image_sizes[ $size ] ) && is_array( $_wp_additional_image_sizes[ $size ] ) ) || in_array( $size, array(
						'thumbnail',
						'thumb',
						'medium',
						'large',
						'full',
					) ) )
			) {
				$thumbnail = wp_get_attachment_image_src( $image_id, $size, false );
				if ( $thumbnail ) {
					return $thumbnail[0];
				}
			}

			if ( is_string( $size ) ) {
				preg_match_all( '/\d+/', $size, $thumb_matches );
				if ( isset( $thumb_matches[0] ) ) {
					$size = array();
					$count = count( $thumb_matches[0] );
					if ( $count > 1 ) {
						$size[] = $thumb_matches[0][0]; // Width.
						$size[] = $thumb_matches[0][1]; // Height.
					} elseif ( 1 === $count ) {
						$size[] = $thumb_matches[0][0]; // Width.
						$size[] = $thumb_matches[0][0]; // Height.
					} else {
						$size = false;
					}
				}
			}
			if ( is_array( $size ) ) {
				// Resize image to custom size
				if ( function_exists( 'wpb_resize' ) ) {
					$p_img = wpb_resize( $image_id, null, $size[0], $size[1], true );
				} else {
					$p_img = gmbvp_resize( $image_id, null, $size[0], $size[1], true );
				}
				if ( $p_img ) {
					return $p_img['url'];
				}
			}

			$large = wp_get_attachment_image_src( $image_id, 'large' );
			if ( $large ) {
				return $large[0];
			}
		}
	}
	new GMB_Video_Popup_Shortcode();
} // End if().


/*
* Resize images dynamically using wp built in functions
* Victor Teixeira
*
* php 5.2+
*
* Exemplo de uso:
*
* <?php
* $thumb = get_post_thumbnail_id();
* $image = vt_resize( $thumb, '', 140, 110, true );
* ?>
* <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
*
*/
if ( ! function_exists( 'gmbvp_resize' ) ) {
	/**
	 * @param int $attach_id
	 * @param string $img_url
	 * @param int $width
	 * @param int $height
	 * @param bool $crop
	 *
	 * @since 4.2
	 * @return array
	 */
	function gmbvp_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {
		// this is an attachment, so we have the ID
		$image_src = array();
		if ( $attach_id ) {
			$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			$actual_file_path = get_attached_file( $attach_id );
			// this is not an attachment, let's use the image url
		} elseif ( $img_url ) {
			$file_path = parse_url( $img_url );
			$actual_file_path = rtrim( ABSPATH, '/' ) . $file_path['path'];
			$orig_size = getimagesize( $actual_file_path );
			$image_src[0] = $img_url;
			$image_src[1] = $orig_size[0];
			$image_src[2] = $orig_size[1];
		}
		if ( ! empty( $actual_file_path ) ) {
			$file_info = pathinfo( $actual_file_path );
			$extension = '.' . $file_info['extension'];

			// the image path without the extension
			$no_ext_path = $file_info['dirname'] . '/' . $file_info['filename'];

			$cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . $extension;

			// checking if the file size is larger than the target size
			// if it is smaller or the same size, stop right here and return
			if ( $image_src[1] > $width || $image_src[2] > $height ) {

				// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
				if ( file_exists( $cropped_img_path ) ) {
					$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
					$vt_image = array(
						'url' => $cropped_img_url,
						'width' => $width,
						'height' => $height,
					);

					return $vt_image;
				}

				if ( false == $crop ) {
					// calculate the size proportionaly
					$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
					$resized_img_path = $no_ext_path . '-' . $proportional_size[0] . 'x' . $proportional_size[1] . $extension;

					// checking if the file already exists
					if ( file_exists( $resized_img_path ) ) {
						$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

						$vt_image = array(
							'url' => $resized_img_url,
							'width' => $proportional_size[0],
							'height' => $proportional_size[1],
						);

						return $vt_image;
					}
				}

				// no cache files - let's finally resize it
				$img_editor = wp_get_image_editor( $actual_file_path );

				if ( is_wp_error( $img_editor ) || is_wp_error( $img_editor->resize( $width, $height, $crop ) ) ) {
					return array(
						'url' => '',
						'width' => '',
						'height' => '',
					);
				}

				$new_img_path = $img_editor->generate_filename();

				if ( is_wp_error( $img_editor->save( $new_img_path ) ) ) {
					return array(
						'url' => '',
						'width' => '',
						'height' => '',
					);
				}
				if ( ! is_string( $new_img_path ) ) {
					return array(
						'url' => '',
						'width' => '',
						'height' => '',
					);
				}

				$new_img_size = getimagesize( $new_img_path );
				$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

				// resized output
				$vt_image = array(
					'url' => $new_img,
					'width' => $new_img_size[0],
					'height' => $new_img_size[1],
				);

				return $vt_image;
			}

			// default output - without resizing
			$vt_image = array(
				'url' => $image_src[0],
				'width' => $image_src[1],
				'height' => $image_src[2],
			);

			return $vt_image;
		}

		return false;
	}
}
