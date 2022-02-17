<?php
/**
 * Component settings, used in the Woffice admin pages
 */

if (!woffice_bp_is_active('notifications')) {
    $html = '<div>';
    $html .= '<h4>' . __('Live push notificatons are not enabled...', 'woffice') . '</h4>';
    $html .= '<p>' . __('The BuddyPress live push notifications component must be enabled in order to use this plugin. Enable it on',
            'woffice');
    $html .= ' <a href="' . get_admin_url() . 'options-general.php?page=bp-components">' . __('the BuddyPress settings page here',
            'woffice') . '</a>.</p>';
    $html .= '</div>';

    $options = array(
        array(
            'name'    => 'notitications_requirement',
            'type'    => 'box',
            'title'   => __('Important', 'woffice'),
            'options' => array(
                'notitications_requirement' => array(
                    'type'  => 'html',
                    'label' => __('Info', 'woffice'),
                    'html'  => $html,
                ),
            )
        )
    );
}
elseif (empty(woffice_get_https_protocol()) || strpos($_SERVER['SERVER_NAME'], 'localhost') !== false) {
    $html = '<div class="is-dismissable">';
    $html .= '<h4>' . __('Push notification will not work without SSL', 'woffice') . '</h4>';
    $html .= '<p>' . __('The BuddyPress live notifications component will only work if you have SSL enabled.',
            'woffice') . '</p>';
    $html .= '</div>';

    $options = array(
        array(
            'name'    => 'notitications_requirement',
            'type'    => 'box',
            'title'   => __('Important', 'woffice'),
            'options' => array(
                'notitications_requirement' => array(
                    'type'  => 'html',
                    'label' => __('Info', 'woffice'),
                    'html'  => $html,
                ),
            )
        )
    );
}
else {
    $help_text = __('You will need a <a href="https://firebase.google.com">Firebase</a> account to use live push notification. Create a new project from the the <a href="https://console.firebase.google.com"> Firebase console.</a> Then you should have all below settings from the Firebase project settings in the Firebase console.', 'woffice');
    $html = '<p>' . $help_text . '</p>';

    $detail = __('See <a href="https://alkaweb.atlassian.net/wiki/spaces/WOF/pages/268075009/Live+notifications+setup">this article</a> for more details.', 'woffice');

    $options = array(
        'build'               => array(
            'type'    => 'tab',
            'title'   => __('Notification Content Setting', 'woffice'),
            'options' => array(
                'content_settings' => array(
                    'title'   => __('Content Options', 'woffice'),
                    'type'    => 'box',
                    'options' => array(
                        'notifications_title'   =>
                            array(
                                'name'  => 'notifications_title',
                                'type'  => 'text',
                                'label' => __('Notification title', 'woffice'),
                                'desc'  => __('Title of the notification box. You can use variable {component_name} inside title. {component_name} will be replaced by the component name.',
                                    'woffice'),
                                'value' => 'You have a new {component_name}\'s notification!'
                            ),
                        'notifications_content' =>
                            array(
                                'name'  => 'notifications_content',
                                'type'  => 'textarea',
                                'label' => __('Notification content', 'woffice'),
                                'desc'  => __('Content of the notification box. You can use variable {component_name} inside content. {component_name} will be replaced by the component name.',
                                    'woffice'),
                                'value' => 'You have a new {component_name}\'s notification!'
                            ),
                        'notifications_icon'    => array(
                            'label'       => __('Notification icon', 'woffice'),
                            'desc'        => __('Upload your image for the icon', 'woffice'),
                            'type'        => 'upload',
                            'images_only' => true
                        )
                    )
                ),

            )
        ),
        'fcm_account_setting' => array(
            'type'    => 'tab',
            'title'   => __('Firebase Account Setting', 'woffice'),
            'options' => array(
                'setting' => array(
                    'type'    => 'box',
                    'title'   => __('Firebase Account Setting', 'woffice'),
                    'options' => array(
                        'info'                                => array(
                            'name' => 'Important',
                            'type' => 'html',
                            'html' => $html
                        ),
                        'notifications_fcm_apikey'            =>
                            array(
                                'name'  => 'notifications_fcm_apikey',
                                'type'  => 'text',
                                'label' => __('Firebase API Key', 'woffice'),
                                'desc'  => __('Get the “apiKey” attribute from the “Add Firebase to your web app” screen. ' . $detail,
                                    'woffice'),
                                'value' => ''
                            ),
                        'notifications_fcm_authDomain'        =>
                            array(
                                'name'  => 'notifications_fcm_authDomain',
                                'type'  => 'text',
                                'label' => __('Firebase Auth Domain', 'woffice'),
                                'desc'  => __('Get the “authDomain” attribute from the “Add Firebase to your web app” screen. ' . $detail,
                                    'woffice'),
                                'value' => ''
                            ),
                        'notifications_fcm_databaseURL'       =>
                            array(
                                'name'  => 'notifications_fcm_databaseURL',
                                'type'  => 'text',
                                'label' => __('Firebase Database URL', 'woffice'),
                                'desc'  => __('Get the “databaseURL” attribute from the “Add Firebase to your web app” screen. ' . $detail,
                                    'woffice'),
                                'value' => ''
                            ),
                        'notifications_fcm_projectId'         =>
                            array(
                                'name'  => 'notifications_fcm_projectId',
                                'type'  => 'text',
                                'label' => __('Firebase Project Id', 'woffice'),
                                'desc'  => __('Get the “projectId” attribute from the “Add Firebase to your web app” screen. ' . $detail,
                                    'woffice'),
                                'value' => ''
                            ),
                        'notifications_fcm_storageBucket'     =>
                            array(
                                'name'  => 'notifications_fcm_storageBucket',
                                'type'  => 'text',
                                'label' => __('Firebase Storage Bucket', 'woffice'),
                                'desc'  => __('Get the “storageBucket” attribute from the “Add Firebase to your web app” screen. ' . $detail,
                                    'woffice'),
                                'value' => ''
                            ),
                        'notifications_fcm_messagingSenderId' =>
                            array(
                                'name'  => 'notifications_fcm_messagingSenderId',
                                'type'  => 'text',
                                'label' => __('Firebase Messaging SenderId', 'woffice'),
                                'desc'  => __('Get the “messagingSenderId” attribute from the “Add Firebase to your web app” screen. ' . $detail,
                                    'woffice'),
                                'value' => ''
                            ),

                        'notifications_fcm_legacyServerKey' =>
                            array(
                                'name'  => 'notifications_fcm_legacyServerKey',
                                'type'  => 'text',
                                'label' => __('Firebase legacyServerKey', 'woffice'),
                                'desc'  => __('Get “Legacy server key” from the “Cloud Messaging” setting. ' . $detail,
                                    'woffice'),
                                'value' => ''
                            ),

                        'notifications_fcm_webPushCertificate' =>
                            array(
                                'name'  => 'notifications_fcm_webPushCertificate',
                                'type'  => 'text',
                                'label' => __('Firebase Web Push Certificate', 'woffice'),
                                'desc'  => __('Get “Web Push certificates” from the “Cloud Messaging” setting. ' . $detail,
                                    'woffice'),
                                'value' => ''
                            ),
                    )
                )
            )
        )

    );
}
