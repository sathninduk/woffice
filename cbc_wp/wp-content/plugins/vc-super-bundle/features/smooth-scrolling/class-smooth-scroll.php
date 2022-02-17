<?php
/**
 * Calls the smooth scroll library.
 *
 * @version 1.1
 * @package Smooth MouseWheel
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

if ( ! class_exists( 'GambitSmoothScroll' ) ) {

	/**
	 * Main class file for the plugin functionality.
	 */
	class GambitSmoothScroll {

		const SETTINGS_PAGE = 'general';
		const SETTINGS_SECTION = 'gambit_smoothscroll';
		const OPTION_NAME = 'gambit_smoothscroll_options';


		/**
		 * These things will run immediately upon call.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Loads the smooth scrolling plugin throughout.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_smooth_scroll_script' ) );

			// Settings page.
			add_action( 'admin_init', array( $this, 'create_general_settings' ) );
		}


		/**
		 * Includes the smooth scrolling script.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function enqueue_smooth_scroll_script() {
			wp_enqueue_script( __CLASS__, plugins_url( 'smooth-scrolling/js/min/gambit-smoothscroll-min.js', __FILE__ ), array(), VERSION_GAMBIT_SMOOTH_SCROLLING_PLUGIN );

			// Print out our starting script.
			$options = get_option( self::OPTION_NAME );

			$script = '';
			if ( ! empty( $options['speed'] ) ) {
				$script .= 'speed: ' . esc_attr( $options['speed'] );
			}
			if ( ! empty( $options['amount'] ) ) {
				$script .= ! empty( $script ) ? ',' : '';
				$script .= 'amount: ' . esc_attr( $options['amount'] );
			}
			if ( ! empty( $script ) ) {
				$script = '{' . $script . '}';
			}

			wp_add_inline_script( __CLASS__, "new GambitSmoothScroll($script);" );
		}

		/**
		 * Create the options for the plugin.
		 *
		 * @return	void
		 * @since	1.1
		 **/
		public function create_general_settings() {
			add_settings_section(
				self::SETTINGS_SECTION,
				__( 'Smooth Scroll Settings', 'smooth-scrolling' ),
				false,
				self::SETTINGS_PAGE
			);

			register_setting(
				self::SETTINGS_PAGE,
				self::OPTION_NAME,
				array( $this, 'validate_options' )
			);

			add_settings_field(
				'gambit_smoothscroll_speed_new',
				__( 'Scroll Speed', 'smooth-scrolling' ),
				array( $this, 'display_speed_option' ),
				self::SETTINGS_PAGE,
				self::SETTINGS_SECTION
			);

			add_settings_field(
				'gambit_smoothscroll_amount',
				__( 'Scroll Tick Amount', 'smooth-scrolling' ),
				array( $this, 'display_amount_option' ),
				self::SETTINGS_PAGE,
				self::SETTINGS_SECTION
			);
		}


		/**
		 * Display our speed option.
		 *
		 * @return	void
		 * @since	1.1
		 **/
		public function display_speed_option() {
			$options = get_option( self::OPTION_NAME );
			$value = empty( $options['speed'] ) ? '900' : $options['speed'];

			?>
			<input id='speed' name='<?php echo esc_attr( self::OPTION_NAME ) ?>[speed]' type='number' min='400' max='2000' step='100' class='small-text' value='<?php echo esc_attr( $value ); ?>' placeholder='12'/>
			<?php esc_html_e( 'The mouse wheel scroll speed. A higher number will scroll the page slower.', 'smooth-scrolling' ) ?>
			<?php
		}


		/**
		 * Display our amountosition rate option.
		 *
		 * @return	void
		 * @since	1.1
		 **/
		public function display_amount_option() {
			$options = get_option( self::OPTION_NAME );
			$value = empty( $options['amount'] ) ? '150' : $options['amount'];

			?>
			<input id='amount' name='<?php echo esc_attr( self::OPTION_NAME ) ?>[amount]' type='number' min='50' max='300' step='1' value='<?php echo esc_attr( $value ); ?>' placeholder='0.94' />
			<?php esc_html_e( 'The scroll amount per mouse wheel tick. A larger number here will scroll the page farther per wheel.', 'smooth-scrolling' ) ?>
			<?php
		}


		/**
		 * Validate & sanitize our input options.
		 *
		 * @param array $input - The input options.
		 * @return	array $valid - Returns validated settings.
		 * @since	1.1
		 **/
		public function validate_options( $input ) {
			$valid = array();

			$valid['speed'] = sanitize_text_field( $input['speed'] );
			if ( ! is_numeric( $valid['speed'] ) && '' !== $valid['speed'] ) {
				add_settings_error(
					'gambit_smoothscroll_speed_new',
					'gambit_smoothscroll_speed_new_error',
					__( 'Scroll speed should be a number', 'smooth-scrolling' ),
					'error'
				);
			}

			$valid['amount'] = sanitize_text_field( $input['amount'] );
			if ( ! is_numeric( $valid['amount'] ) && '' !== $valid['amount'] ) {
				add_settings_error(
					'gambit_smoothscroll_amount',
					'gambit_smoothscroll_amount_error',
					__( 'Scroll amount should be a number', 'smooth-scrolling' ),
					'error'
				);
			}

			return $valid;
		}
	}

	new GambitSmoothScroll();

} // End if().
