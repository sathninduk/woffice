<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$ext_instance = fw()->extensions->get( 'woffice-slack' );

$url = get_site_url() . "/wp-admin/admin-ajax.php?action=slack_callback";

$ext_instance->initialize_slack_interface();

if($ext_instance->is_authenticated()){
    $button = fw_html_tag('a', array(
        'href'  => admin_url('admin.php?page=fw-extensions&sub-page=extension&extension=woffice-slack&clear-channel=yes'),
        'class' => 'button-secondary',
    ), __('Choose another Slack channel', 'woffice'));
    $status = '<span class="highlight">'. __('Authenticated', 'woffice') .'</span><br><br>'.$button;
} else {
    $button = '<a href="https://slack.com/oauth/authorize?scope=incoming-webhook,commands&client_id='. $ext_instance->get_client_id() .'"><img alt="Add to Slack" height="40" width="139" src="https://platform.slack-edge.com/img/add_to_slack.png" srcset="https://platform.slack-edge.com/img/add_to_slack.png 1x, https://platform.slack-edge.com/img/add_to_slack@2x.png 2x"></a>';
    $status = '<span class="highlight">'. __('Not authenticated', 'woffice') .'</span><br><br>'. $button;
}

$notice = __('You need to create a new application on Slack','woffice').' <a href="https://api.slack.com/apps" target="_blank">'. __('Create a new application','woffice') .'</a>.<br><br>';
$notice .= '<a href="https://alka-web.com/blog/use-slack-along-with-wordpress-woffice/" target="_blank">'. __('First time ? Follow our tutorial','woffice') .'</a>';

$options = array(
	'slack-config' => array(
		'type'    => 'tab',
		'title'   => __( 'Slack Config', 'woffice' ),
		'options' => array(
            'slack_info' => array(
                'label' => __( 'Important :', 'woffice' ),
                'type'  => 'html',
                'html'  => $notice,
            ),
            'slack_callbackurl' => array(
                'label' => __( 'Your callback URL :', 'woffice' ),
                'type'  => 'html',
                'html'  => 'When enabling Slack API, plesase click "OAuth & Permissions" and copy/past :<br> <span class="highlight">'.$url.'</span>',
            ),
            'slack_client_id' => array(
                'label' => __( 'Client ID', 'woffice' ),
                'type' => 'text',
                'desc' => __( 'Your Slack API client ID for this app.', 'woffice' ),
            ),
            'slack_client_secret' => array(
                'label' => __( 'Client Secret', 'woffice' ),
                'type' => 'text',
                'desc' => __( 'Your Slack API Secret key for this app.', 'woffice' ),
            ),
        )
	),
    'slack-notifications' => array(
        'type'    => 'tab',
        'title'   => __( 'Slack Notifications', 'woffice' ),
        'options' => array(
            'status' => array(
                'label' => __( 'Status :', 'woffice' ),
                'type'  => 'html',
                'html'  => $status,
            ),
            'enable_comments' => array(
                'type' => 'switch',
                'value' => 'off',
                'label' => __('Trigger notification on new comments', 'woffice'),
                'left-choice' => array(
                    'value' => 'off',
                    'label' => __('Off', 'woffice'),
                ),
                'right-choice' => array(
                    'value' => 'on',
                    'label' => __('On', 'woffice'),
                ),
            ),
            'enable_posts' => array(
                'type' => 'switch',
                'value' => 'off',
                'label' => __('Trigger notification on new posts', 'woffice'),
                'desc'  => __('Whether it\'s a post, project, wiki, page, event...', 'woffice'),
                'left-choice' => array(
                    'value' => 'off',
                    'label' => __('Off', 'woffice'),
                ),
                'right-choice' => array(
                    'value' => 'on',
                    'label' => __('On', 'woffice'),
                ),
            ),
            'enable_tasks' => array(
                'type' => 'switch',
                'value' => 'off',
                'label' => __('Trigger notification on new project tasks', 'woffice'),
                'desc'  => __('Project privacy won\'t be handled here, all tasks will be fetched.', 'woffice'),
                'left-choice' => array(
                    'value' => 'off',
                    'label' => __('Off', 'woffice'),
                ),
                'right-choice' => array(
                    'value' => 'on',
                    'label' => __('On', 'woffice'),
                ),
            ),
            'enable_registration' => array(
                'type' => 'switch',
                'value' => 'off',
                'label' => __('Trigger notification on new user creation and registration', 'woffice'),
                'left-choice' => array(
                    'value' => 'off',
                    'label' => __('Off', 'woffice'),
                ),
                'right-choice' => array(
                    'value' => 'on',
                    'label' => __('On', 'woffice'),
                ),
            ),
            'enable_activities' => array(
                'type' => 'switch',
                'value' => 'off',
                'label' => __('Trigger notification on new public BuddyPress activities', 'woffice'),
                'left-choice' => array(
                    'value' => 'off',
                    'label' => __('Off', 'woffice'),
                ),
                'right-choice' => array(
                    'value' => 'on',
                    'label' => __('On', 'woffice'),
                ),
            ),
        )
    ),
);