<?php
/**
 * CC Image Search main functionality class
 *
 * This is the main script that adds in the plugin's main shortcode/behavior/etc.
 * Each important component has it's own class-shortcode.php file.
 *
 * @package CC Image Search
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Initializes plugin class.
if ( ! class_exists( 'CCImageSearch' ) ) {

	/**
	 * This is where all the plugin's functionality happens.
	 */
	class CCImageSearch {

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Our admin-side scripts & styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

			// Enqueues scripts and styles specific for all parts of the plugin.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts_and_css' ), 99 );

			// Add the frame template used for the Media Manager search View.
			add_action( 'admin_footer', array( $this, 'include_frame_template' ) );
			add_action( 'customize_controls_print_footer_scripts', array( $this, 'include_frame_template' ) );

			// Ajax handler for downloading images.
			add_action( 'wp_ajax_ccimage_download_image', array( $this, 'ajax_download_image' ) );
		}



		/**
		 * Includes admin scripts and styles needed.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function enqueue_admin_scripts() {

			// Admin styles.
			wp_enqueue_style( __CLASS__ . '-admin', plugins_url( 'cc_image_search/css/admin.css', __FILE__ ), array(), VERSION_GAMBIT_CC_IMAGE_SEARCH );

			// Admin javascript.
			wp_enqueue_script( __CLASS__ . '-admin', plugins_url( 'cc_image_search/js/min/admin-min.js', __FILE__ ), array( 'media-views', 'jquery' ), VERSION_GAMBIT_CC_IMAGE_SEARCH );
			wp_localize_script( __CLASS__ . '-admin', 'CCImageParams', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( __CLASS__ ),
				'admin_post_url' => admin_url( 'post.php' ),
				'media_default_url' => includes_url( 'images/media/default.png' ),
				'media_title' => __( 'Search for Free Images', 'cc_image_search' ),
				'tab_title' => __( 'Free Image Search', 'cc_image_search' ),
			) );

			if ( is_admin() ) {
				$current_screen = get_current_screen();
				if ( 'media' === $current_screen->base || 'upload' === $current_screen->base ) {
					wp_enqueue_media();
					wp_enqueue_script( __CLASS__ . '-media', plugins_url( 'cc_image_search/js/min/media-min.js', __FILE__ ), array( 'media-views', 'jquery' ), VERSION_GAMBIT_CC_IMAGE_SEARCH );
				}
			}
		}


		/**
		 * Includes normal scripts and css purposed globally by the plugin.
		 * Do not use this section to initialize styles and scripts used in shortcodes! Use the shortcode section instead for that.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function enqueue_frontend_scripts_and_css() {

			if ( wp_script_is( 'media-editor', 'enqueued' ) ) {
				$this->enqueue_admin_scripts();
				add_action( 'wp_footer', array( $this, 'include_frame_template' ) );
			}
		}


		/**
		 * Includes the search template, if called.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function include_frame_template() {
			include 'search-template.php';
		}


		/**
		 * Size comparison function, if called, which returns if width of $a is greater than width of $b.
		 *
		 * @param object $a - The first object of comparison, pulling up the width object.
		 * @param object $b - The second object of comparison, pulling up the width object.
		 * @return	boolean - If object width of A is bigger than of B, true, else false.
		 * @since	1.0
		 */
		public function sort_by_size( $a, $b ) {
			return $a->width > $b->width;
		}

		/**
		 * Downloads selected image from the selection.
		 *
		 * @param string $url - The URL of the image to be acquired.
		 * @param string $title - The image title.
		 * @return int $id - The image ID of the downloaded image.
		 * @see https://codex.wordpress.org/Function_Reference/media_handle_sideload
		 */
		public function download_single_image( $url, $title ) {

			// Need to require these files.
			if ( ! function_exists( 'media_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
			}

			$tmp = download_url( $url );
			if ( is_wp_error( $tmp ) ) {
				// Download failed, handle error.
				return false;
			}

			$post_id = 0;
			$file_array = array();

			// Set variables for storage
			// fix file filename for query strings.
			preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches );
			$file_array['name'] = 'ccimage-' . basename( $matches[0] );

			// If the file name doesn't have an extension (e.g. most of
			// Unsplash images), media_handle_sideload() will error out.
			// This fixes things, assume they're jpgs.
			$path = pathinfo( $tmp );
			if ( empty( $matches[0] ) ) {
				$tmpnew = $tmp . '.tmp';
				// @codingStandardsIgnoreLine
				if ( ! rename( $tmp, $tmpnew ) ) {
					return false;
				} else {
					$file_array['name'] = 'ccimage-' . basename( $matches ) . '.jpg';
					$tmp = $tmpnew;
				}
			}

			$file_array['tmp_name'] = $tmp;

			// Do the validation and storage stuff.
			$id = media_handle_sideload( $file_array, $post_id, $title );

			// If error storing permanently, unlink.
			// Note: We're copying what media_sideload_image
			// ( https://developer.wordpress.org/reference/functions/media_sideload_image/ )
			// is doing, so unlink should be okay here.
			if ( is_wp_error( $id ) ) {
				// @codingStandardsIgnoreLine
				@unlink( $file_array['tmp_name'] );
				return false;
			}

			return $id;
		}


		/**
		 * Forms the attribution to be used as an image's caption based on the
		 * image's data.
		 *
		 * @see https://wiki.creativecommons.org/wiki/Best_practices_for_attribution#Examples_of_attribution
		 *
		 * @param object $data The image object from our provider scripts.
		 *
		 * @return string The attribution for the image.
		 */
		public function form_attribution( $data ) {

			// Allow providers to use their own attribution.
			if ( ! empty( $data->attribution ) ) {
				return $data->attribution;
			}

			// Public domains don't need attribution.
			if ( in_array( 'zero', $data->badges, true ) ) {
				return '';
			}

			$title = __( 'Photo', 'cc_image_search' );
			if ( ! empty( $data->url ) ) {
				$title = sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $data->url ), __( 'Photo', 'cc_image_search' ) );
			}
			if ( ! empty( $data->title ) ) {
				$title = '"' . $data->title . '"';
				if ( ! empty( $data->url ) ) {
					$title = sprintf( '"<a href="%s" target="_blank">%s</a>"', esc_url( $data->url ), $data->title );
				}
			}

			return sprintf( __( '%1$s by %2$s is licensed under %3$s', 'cc_image_search' ),
				$title,
				sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $data->user_link ), $data->user ),
				sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $data->license_link ), $data->license_shortname )
			);
		}


		/**
		 * Download an image based off it's image data given by our provider scripts.
		 *
		 * @param object $image_data Our image data.
		 *
		 * @return mixed The attachment ID of the new image, false if failed.
		 */
		public function download_image( $image_data ) {

			usort( $image_data->sizes, array( $this, 'sort_by_size' ) );

			$min_size = 1400;
			$num_larger = 0;
			$last_index = -1;
			foreach ( $image_data->sizes as $i => $size ) {
				if ( $image_data->sizes[ $i ]->width > $min_size || $image_data->sizes[ $i ]->height > $min_size ) {
					$num_larger++;
					if ( -1 === $last_index ) {
						$last_index = $i;
					}
				}
			}
			if ( $num_larger > 1 ) {
				while ( count( $image_data->sizes ) > $last_index + 1 ) {
					array_pop( $image_data->sizes );
				}
			}

			// Create the title.
			$title = sprintf( __( 'Image by %1$s from %1$s', 'cc_image_search' ), $image_data->user, $image_data->provider_name );
			if ( ! empty( $image_data->title ) ) {
				$title = $image_data->title;
			}

			// Download one of the images (try largest first).
			for ( $i = count( $image_data->sizes ) - 1; $i >= 0; $i-- ) {
				$post_id = $this->download_single_image( $image_data->sizes[ $i ]->url, $title );
				if ( $post_id ) {
					break;
				}
			}

			if ( empty( $post_id ) ) {
				return false;
			}

			wp_update_post( array(
				'ID' => $post_id,
				'post_excerpt' => $this->form_attribution( $image_data ),
			) );

			update_post_meta( $post_id, 'ccimage', '1' );

			return $post_id;
		}


		/**
		 * Generates attachment data needed by the media manager for force-adding
		 * newly added images into the current media manager view.
		 *
		 * @param int $post_id The attachment ID.
		 *
		 * @return array Attachment data.
		 */
		public function generate_media_manager_data( $post_id ) {
			$post = get_post( $post_id, ARRAY_A );
			$meta_data = wp_generate_attachment_metadata( $post_id, get_attached_file( $post_id ) );

			// We need these to update the media manager.
			return array(
				'id' => $post_id,
				'attachment_data' => $post,
				'sizes_data' => $meta_data,
				'attachment_url' => wp_get_attachment_url( $post_id ),
				'attachment_link' => get_attachment_link( $post_id ),
				'delete_nonce' => wp_create_nonce( 'delete-post_' . $post_id ),
				'update_nonce' => wp_create_nonce( 'update-post_' . $post_id ),
				'edit_nonce' => wp_create_nonce( 'image_editor-' . $post_id ),
				'compat_fields' => get_compat_media_markup( $post_id ),
			);
		}


		/**
		 * Ajax handler for downloading images from Freemage search results.
		 */
		public function ajax_download_image() {
			check_ajax_referer( __CLASS__, 'nonce' );

			if ( ! isset( $_POST['data'] ) ) { // Input var okay.
				return;
			}

			$image_data = json_decode( wp_unslash( $_POST['data'] ) ); // Input var okay. WPCS: sanitization ok.

			$post_id = $this->download_image( $image_data );
			echo wp_json_encode( $this->generate_media_manager_data( $post_id ) );
			die();
		}
	}

	new CCImageSearch();

} // End if().
