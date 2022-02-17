<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

use Slack_Interface\Slack_Access;

/**
 * Main class of the Woffice Slack extension
 * It creates notifications in Slack channels from WP triggered events
 * @author Xtendify
 * @link https://github.com/tutsplus/php-slack-tutorial
 */
 
class FW_Extension_Woffice_Slack extends FW_Extension {

    /**
     * API endpoint
     * @var string
     */
    public $api_root = 'https://slack.com/api/';

    /**
     * Option name in the database
     * @var string
     */
    public $option_name = 'woffice_slack_access';

    /** Slack authorization data
     * @var Slack_Access
     */
    public $access;

	/**
	 * @internal
	 */
	public function _init() {

        add_action('wp_ajax_slack_callback', array($this, 'handle_callback'));
        add_action('wp_ajax_nopriv_slack_callback', array($this, 'handle_callback'));
        add_action('admin_notices', array($this,"handle_message"));
        add_action('admin_init', array($this, 'refresh_channel'));

	}

	/**
	 * Handle the callback when the app is authorized by the user
     * It's all handled by WP AJAX
	 */
	public function handle_callback(){

        $code = $_GET['code'];

        $result_message= '';
        $error = 'no';

        try {
            $access = $this->do_oauth( $code );
            if ( $access ) {
                update_option($this->option_name, $access->to_json());
                $result_message = __('The application was successfully added to your Slack channel', 'woffice');
                $error = 'no';
            }
        } catch ( \WP_Error $e ) {
            $result_message = $e->get_error_message();
            $error = 'yes';
        }

        $url_data = array(
            'page' => 'fw-extensions',
            'sub-page' => 'extension',
            'extension' => 'woffice-slack',
            'has-error' => $error,
            'message' => urlencode($result_message)
        );

        $url = add_query_arg( $url_data, admin_url('admin.php') );

        wp_redirect($url);


        die();
    }

    /**
     * Handles the message returned by the callback
     */
    public function handle_message() {

        $html = '';

        if(!isset($_GET['extension']) || $_GET['extension'] != 'woffice-slack' || !isset($_GET['has-error']) || !isset($_GET['message']) ) {
            $html .= '';
        } else {
            $class = ($_GET['has-error'] == 'yes') ? 'error' : 'success';
            $html .= '<div class="notice notice-'.$class.' is-dismissible">';
                   $html .= ' <p>'.urldecode($_GET['message']).'</p>';
            $html .= '</div>';
        }

        echo $html;

    }

    /**
     * Completes the OAuth authentication flow by exchanging the received
     * authentication code to actual authentication data.
     * @param string $code  Authentication code sent to the OAuth callback function
     * @return bool|Slack_Access    An access object with the authentication data in place
     *                              if the authentication flow was completed successfully.
     *                              Otherwise false.
     * @throws WP_Error
     */
    public function do_oauth( $code ) {

        $headers = array(
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode( $this->get_client_id() . ':' . $this->get_client_secret() ),
        );
        $data = array( 'code' => $code );

        $response = wp_remote_post( $this->api_root . 'oauth.access', array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => $headers,
                'body' => $data,
                'cookies' => array()
            )
        );

        // Handle the JSON response
        $json_response = json_decode( $response['body'], false );

        if ( ! $json_response->ok) {
            // There was an error in the request
            new \WP_Error( 'slack_error', $json_response->error);
        }

        // The action was completed successfully, store and return access data
        $this->access = new Slack_Access(
            array(
                'access_token' => $json_response->access_token,
                'scope' => explode( ',', $json_response->scope ),
                'team_name' => $json_response->team_name,
                'team_id' => $json_response->team_id,
                'incoming_webhook' => $json_response->incoming_webhook
            )
        );

        return $this->access;
    }

    /**
     * Initializes the Slack handler object, loading the authentication
     * information from an option set in the database. If the option is not present or empty,
     * the Slack handler is initialized in a non-authenticated state.
     *
     * @return Slack_Access
     */
	public function initialize_slack_interface() {

        if ( get_option($this->option_name) ) {
            $access_string = get_option($this->option_name);
        } else {
            $access_string = '{}';
        }

        $access_data = json_decode( $access_string, true );

        $slack = $this->authorize( $access_data );

        return $slack;
    }

    /**
     * Authorize data & Slack interface object.
     * @param array $access_data An associative array containing OAuth
     *                           authentication information. If the user
     *                           is not yet authenticated, pass an empty array.
     */
    public function authorize( $access_data ) {
        if ( $access_data ) {
            $this->access = new Slack_Access( $access_data );
        }
    }

    /**
     * Checks if the Slack interface was initialized with authorization data.
     * @return bool True if authentication data is present. Otherwise false.
     */
    public function is_authenticated() {
        return isset( $this->access ) && $this->access->is_configured();
    }

    /**
     * Function to return the client ID of the Slack APP
     * @return string
     */
    public function get_client_id(){

        $client_id = fw_get_db_ext_settings_option('woffice-slack', 'slack_client_id');
        if(!empty($client_id)) {
            return $client_id;
        } else {
            return '';
        }

    }

    /**
     * Function to return the client Secret of the Slack APP
     * @return string
     */
    public function get_client_secret(){

        $client_secret = fw_get_db_ext_settings_option('woffice-slack', 'slack_client_secret');
        if(!empty($client_secret)) {
            return $client_secret;
        } else {
            return '';
        }

    }

    public function refresh_channel() {

        $refresh = (isset($_GET["clear-channel"]) && $_GET["clear-channel"] == 'yes') ? true : false;
        if ($refresh == "yes") {

            /* User array refresh */
            delete_option($this->option_name);

            /* We remove the GET param */
            wp_redirect(admin_url('admin.php?page=fw-extensions&sub-page=extension&extension=woffice-slack'));

        }

    }


}