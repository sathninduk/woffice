<?php
/**
 * Main class
 *
 * @package VC Preview
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
if ( ! class_exists( 'GVcPreview' ) ) {

	/**
	 * The class that does the functions.
	 */
	class GVcPreview {

		/**
		 * Note that the preview should only be real-time rendered once.
		 *
		 * @var bool
		 */
		public $loaded_preview_once = false;

		/**
		 * Hook into WordPress.
		 *
		 * @return void.
		 * @since 1.0
		 */
		function __construct() {

			// Add our iframe beside VC.
			add_action( 'vc_backend_editor_render', array( $this, 'add_iframe' ) );

			// Remove the admin bar in the preview.
			add_action( 'wp_head', array( $this, 'remove_adminbar_from_preview' ) );

			// Show the preview contents.
			add_action( 'the_content', array( $this, 'show_preview_content' ), -1 );

			// Save the preview contents.
			add_action( 'wp_ajax_save_preview', array( $this, 'save_preview' ) );
		}

		/**
		 * Show the preview content.
		 *
		 * @param string $content The post content.
		 *
		 * @return string The modified content.
		 */
		public function show_preview_content( $content ) {
			if ( ! isset( $_GET['post_ID'] ) ) {
				return $content;
			}
			if ( ! is_user_logged_in() && ! isset( $_GET['gvc_preview'] ) ) {
				return $content;
			}

			if ( $this->loaded_preview_once ) {
				return $content;
			}

			// Check if we're inside the main loop in a single post page.
			if ( ( is_single() || is_page() ) && in_the_loop() && is_main_query() ) {

				$this->loaded_preview_once = true;
				$content = get_transient( 'gvc_preview_' . absint( $_GET['post_ID'] ) );

				// Custom CSS values from shortcodes that need to be manually placed.
				$vc = new Vc_Base();
				$css = apply_filters( 'vc_base_build_shortcodes_custom_css', $vc->parseShortcodesCustomCss( $content ) );
				if ( ! empty( $css ) ) {
					$content .= '<style type="text/css" data-type="vc_shortcodes-custom-css">';
					$content .= strip_tags( $css );
					$content .= '</style>';
				}

				// Custom CSS values from the custom CSS field need to be manually placed.
				$css = get_transient( 'gvc_custom_css_' . absint( $_GET['post_ID'] ) );
				if ( ! empty( $css ) ) {
					$content .= '<style type="text/css" data-type="vc_custom-css">';
					$content .= strip_tags( $css );
					$content .= '</style>';
				}
			}

			return $content;
		}

		/**
		 * Save the preview contents.
		 */
		public function save_preview() {
			if ( ! isset( $_POST['_wpnonce'] ) || ! isset( $_POST['post_ID'] ) || ! isset( $_POST['content'] ) ) { // Input var ok.
				return;
			}

			$nonce = sanitize_key( $_POST['_wpnonce'] ); // Input var ok.
			$post_id = absint( $_POST['post_ID'] ); // Input var ok.

			if ( false === wp_verify_nonce( $nonce, 'update-post_' . $post_id ) ) {
				return;
			}

			// Set the acquired preview content.
			set_transient( 'gvc_preview_' . $post_id, wp_unslash( $_POST['content'] ), HOUR_IN_SECONDS ); // Input var ok.

			// Set the acquired preview custom css.
			set_transient( 'gvc_custom_css_' . $post_id, wp_unslash( $_POST['custom_css'] ), HOUR_IN_SECONDS ); // Input var ok.

			// Send our success message.
			wp_send_json_success();
		}

		/**
		 * Fix the styles of the preview iframe content.
		 */
		public function remove_adminbar_from_preview() {
			if ( ! isset( $_GET['post_ID'] ) ) {
				return;
			}
			if ( ! is_user_logged_in() && ! isset( $_GET['gvc_preview'] ) ) {
				return;
			}

			?>
			<style>
			#wpadminbar {
				display: none;
			}
			html[class][class][class] {
				margin-top: 0 !important;
			}

			/* Make full-height stuff not full height. */
			.vc_element .vc_element-container.vc_section.vc_row-o-full-height,
			.vc_section.vc_row-o-full-height,
			.vc_row.vc_row-o-full-height {
				min-height: 700px !important;
			}
			</style>
			<?php
		}

		/**
		 * Load the iframe beside VC.
		 */
		public function add_iframe() {

			if ( ! function_exists( 'vc_editor_post_types' ) ) {
				return;
			}

			global $current_screen;
			if ( false !== array_search( $current_screen->post_type, vc_editor_post_types(), true ) ) {
				wp_enqueue_style( 'gvc', plugins_url( 'vc-preview/css/admin.css', __FILE__ ), array(), VERSION_GAMBIT_VC_PREVIEW );
				wp_enqueue_script( 'gvc', plugins_url( 'vc-preview/js/min/admin-min.js', __FILE__ ), array(), VERSION_GAMBIT_VC_PREVIEW );
				?>
				<div class="gvc-resp-buttons">
					<div class="gvc-refresh gvc-resp-button"><i class="dashicons dashicons-image-rotate"></i></div>
					<div class="gvc-desktop gvc-resp-button gvc-active"><i class="dashicons dashicons-desktop"></i></div>
					<div class="gvc-tablet gvc-resp-button"><i class="dashicons dashicons-tablet"></i></div>
					<div class="gvc-phone gvc-resp-button"><i class="dashicons dashicons-smartphone"></i></div>
				</div>
				<iframe id="gvcpreview" name="gvcpreview"></iframe>
				<?php
			}
		}
	}

	new GVcPreview();
} // End if().
