<?php
/*
 * These are plugin territory functions
 */

if (!function_exists('woffice_get_extension_uri')) {
	/**
	 * Get the Woffice URI extension of a Unyson extension
	 *
	 * @param string $extension (without the woffice- suffix
	 * @param string $path
	 *
	 * @return string
	 */
	function woffice_get_extension_uri($extension, $path = '')
	{
		return plugin_dir_url( __FILE__ ) .'extensions/woffice-'. $extension .'/'. $path;
	}
}

if(!function_exists('woffice_send_user_registration_email')) {
	/**
	 * Send an email to registered user that confirm the complete registration
	 *
	 * @param int $user_id Id of registered user
	 */
	function woffice_send_user_registration_email($user_id){

		$register_new_user_email = woffice_get_settings_option('register_new_user_email');
		if($register_new_user_email != 'yep' || ! woffice_is_custom_login_page_enabled() )
			return;

		$site_name = get_option( 'blogname' );
		$admin_email = get_option( 'admin_email' );

		$user = get_userdata( $user_id );

		//Body
		$message = sprintf(esc_html__( 'Your registration on %s is completed.', 'woffice' ), $site_name) . "\r\n\r\n";
		$message .= esc_html__('Login url:', 'woffice'). ' ' . wp_login_url()."\r\n";
		$message .= esc_html__('Username:', 'woffice') . ' ' . $user->user_login ."\r\n";
		$message .= esc_html__('Password: The password chosen during the registration', 'woffice');

		/**
		 * Filter the body of the email sent to the user once he signs up
		 *
		 * @param string $message
		 * @param WP_User $user
		 */
		$message = apply_filters( 'woffice_user_registration_message_body', $message, $user );

		//Subject
		$subject = esc_html__( 'Your registration is completed', 'woffice' );
		/**
		 * Filter the subject of the email sent to the user once he signs up
		 *
		 * @param string $subject
		 * @param WP_User $user
		 */
		$subject = apply_filters( 'woffice_user_registration_message_subject', $subject, $user );

		//Headers
		$headers = array(
			"From: \"{$site_name}\" <{$admin_email}>\n",
			"Content-Type: text/plain; charset=\"" . get_option( 'blog_charset' ) . "\"\n",
		);
		/**
		 * Filter the headers of the email sent to the user once he signs up
		 *
		 * @param string $message
		 * @param WP_User $user
		 */
		$headers = apply_filters( 'woffice_user_registration_message_headers', $headers );

		wp_mail( $user->user_email, $subject, $message, $headers );
	}
}

if(!function_exists('woffice_bp_is_active')) {
	/**
	 * It's a wrapper for the function bp_is_active(). Checks if a given BuddyPress component is active
	 *
	 * @param $component
	 *
	 * @return bool
	 */
	function woffice_bp_is_active($component) {
		return (function_exists('bp_is_active') && bp_is_active( $component ));
	}
}

if (!function_exists('woffice_decode')) {
	/**
	 * It's a wrapper for the base64_decode() PHP
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	function woffice_decode($content) {
		return base64_decode($content);
	}
}

if (!function_exists('woffice_get_settings_option')) {
	/**
	 * Get an option from the theme settings
	 *
	 * @param string $option
	 * @param string $default
	 *
	 * @return mixed|null|string
	 */
	function woffice_get_settings_option($option, $default = ''){
		$option_value = (function_exists( 'fw_get_db_settings_option' )) ? fw_get_db_settings_option($option) : $default;

		/**
		 * Overrides the value returned from the function woffice_get_settings_option($option)
		 *
		 * @see woffice/inc/helpers.php
		 *
		 * @param mixed $option_value - the value returned by the database
		 * @param string $option - the option name
		 * @param mixed $default - the default value
		 */
		return apply_filters( 'woffice_get_settings_option', $option_value, $option, $default );
	}
}

	/**
	 * create shortcode
	 *
	 * @param string $option
	 * @param string $option
	 *
	 * @return Adds a new shortcode.
	 */

	if(!function_exists('woffice_create_shortcode')) {
		function woffice_create_shortcode($shortcode_name,$shortcode_callback) {
			add_shortcode($shortcode_name, $shortcode_callback);
		}
	}
		/**
		 * register widget
		 *
		 * @param string $option
		 * @param string $option
		 *
		 * @return register mew widget
		 */
	if(!function_exists('woffice_register_new_widget')){
		function woffice_register_new_widget($widget_name){
				register_widget($widget_name);
		}
	}
	
		/**
		 * Return HTTPS Protocol
		 */
	if(!function_exists('woffice_get_https_protocol')) {
		function woffice_get_https_protocol() {
			return isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : '';
		}
	}
	
		/**
		 * Return HTTP_HOST
		 */
	if(!function_exists('woffice_get_http_host')) {
		function woffice_get_http_host() {
			return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
		}
	}
	
		/**
		 * Return REQUEST_URI
		 */
	if(!function_exists('woffice_get_request_uri')) {
		function woffice_get_request_uri() {
			return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		}
	}
	
		/**
		 * Return REQUEST_METHOD
		 */
	if(!function_exists('woffice_get_request_method')) {
		function woffice_get_request_method() {
			return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
		}
	}
	
		/**
		 * Return REQUEST_METHOD
		 */
	if(!function_exists('woffice_get_remote_addr')) {
		function woffice_get_remote_addr() {
			return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		}
	}

		/**
		 * Return SERVER_PORT
		 */
	if(!function_exists('woffice_get_remote_port')) {
		function woffice_get_remote_port() {
			return isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : '';
		}
	}
		/**
		 * echo output
		 */
	if(!function_exists('woffice_echo_output')) {
		function woffice_echo_output($data) {
			echo $data;
		}
	}