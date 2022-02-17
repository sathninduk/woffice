<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Send a notification to the slack channel
 * @param array $content
 * @return bool
 */
function woffice_slack_send_notification( $content = array() ) {

    $ext_instance = fw()->extensions->get( 'woffice-slack' );

    if ( ! $ext_instance->is_authenticated() || empty($content) ) {
        return false;
    }

    $headers = array( 'Accept' => 'application/json' );

    $url = $ext_instance->access->get_incoming_webhook();

    /**
     * @link https://api.slack.com/docs/message-attachments
     */
    $defaults = array(
        'title_link' => get_site_url(),
        'pretext' => __('New notification on', 'woffice') . ' ' . get_bloginfo('name'),
    );
    $content = wp_parse_args($content, $defaults);
    $content['fallback'] = $defaults['pretext'];
    $content['color'] = woffice_get_settings_option('color_colored');
    $content['text'] = strip_tags($content['text']);

    $data = json_encode(
        array(
            'attachments' => array($content),
            'channel' => $ext_instance->access->get_incoming_webhook_channel(),
        )
    );

    $response = wp_remote_post( $url, array('headers' => $headers, 'body' => $data));

    return ( array_key_exists( 'body', $response) && $response['body'] == 'ok' );

}