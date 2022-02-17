<?php

if (!defined('FW')) {
    die('Forbidden');
}

if (!class_exists('Fw_Extension_Woffice_Live_Notifications')) {

    class Fw_Extension_Woffice_Live_Notifications extends FW_Extension
    {

        /**
         * The Firebase version
         *
         * @var string
         */
        private $clientVersion = '5.8.5';

        /**
         * Slug of the component
         *
         * @var string
         */
        public $slug = "woffice-live-notifications";

        /**
         * Ajax action name
         *
         * @var string
         */
        public $ajaxAction = 'woffice_fetch_notifications';

        /**
         * FCM push URL
         *
         * @var string
         */
        public $fcmEndpoint = 'https://fcm.googleapis.com/fcm/send';

        /**
         * Setup the extension
         *
         * @internal
         */
        public function _init()
        {
            if ($this->pushNotAvailable()) {
                return null;
            }

            // Add manifest file in the head section
            add_action('wp_head', array($this, 'addManifestMeta'));

            // Send push notification when new notification created
            add_action('bp_notification_after_save', array($this, 'pushToFCM'));

            // Ajax actions to handle the subscriptions
            add_action('wp_ajax_' . $this->ajaxAction . '_subscription', array($this, 'saveSubscription'));
            add_action('wp_ajax_nopriv_' . $this->ajaxAction . '_subscription', array($this, 'saveSubscription'));

            add_action('wp_enqueue_scripts', array($this, 'loadScripts'));

            do_action('woffice_live_notifications_construct');
        }

        /**
         * Check if everything is set for using push notification
         *
         * @return bool
         */
        public function pushNotAvailable()
        {
            return
                !function_exists('bp_is_active') ||
                !bp_is_active('notifications') ||
                $this->getUserID() == 0 ||
                empty(fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_apikey')) ||
                empty(fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_webPushCertificate')) ||
                empty(fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_messagingSenderId')) ||
                empty(fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_projectId')) ||
                empty(fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_databaseURL')) ||
                empty(fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_legacyServerKey')) ||
                empty(fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_authDomain')) ||
                (empty($_SERVER['HTTPS']) || strpos($_SERVER['SERVER_NAME'], 'localhost') !== false);
        }

        /**
         * Add manifest file for fcm, require for google
         *
         * @return void
         */
        public function addManifestMeta()
        {
            $path = $this->getUrl('assets/js/manifest.json');

            echo '<link rel="manifest" href="' . $path . '">';
        }

        /**
         * Get current logged in user ID
         *
         * @return int
         */
        public function getUserID()
        {
            return apply_filters('woffice_notifications_user', get_current_user_id());
        }

        /**
         * Add the scripts used by the notification extension
         * Add exchanger between backend and front end
         *
         * @return void
         */
        public function loadScripts()
        {

            $data = array(
                'ajax_url'                   => admin_url('admin-ajax.php'),
                'notifications_nonce'        => wp_create_nonce($this->ajaxAction . '_nonce'),
                'notifications_subscription' => $this->ajaxAction . '_subscription',
                'apiKey'                     => fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_apikey'),
                'authDomain'                 => fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_authDomain'),
                'databaseURL'                => fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_databaseURL'),
                'projectId'                  => fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_projectId'),
                'storageBucket'              => fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_storageBucket', ''),
                'messagingSenderId'          => fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_messagingSenderId'),
                'webPushCertificate'         => fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_webPushCertificate'),
                'serviceWorker'              => $this->getUrl('static/js/firebase-messaging-sw.js') . '?ver=0',
                'fcm_user'                   => $this->getUserID() === 0 ? null : $this->getUserID(),
                'fcm_user_tokens'            => $this->getUserFCMTokens($this->getUserID()),
            );

            wp_enqueue_script(
            	$this->slug . '-firebase',
	            'https://www.gstatic.com/firebasejs/'. $this->clientVersion .'/firebase.js',
                array(),
                WOFFICE_THEME_VERSION,
	            false
            );

            wp_enqueue_script(
            	$this->slug . '-firebase-app',
	            'https://www.gstatic.com/firebasejs/'. $this->clientVersion .'/firebase-app.js',
                array(),
                WOFFICE_THEME_VERSION,
	            false
            );

            wp_enqueue_script(
            	$this->slug . '-firebase-messaging',
                'https://www.gstatic.com/firebasejs/'. $this->clientVersion .'/firebase-messaging.js',
	            array(),
                WOFFICE_THEME_VERSION,
	            false
            );

            wp_enqueue_script(
            	$this->slug . '-firebase-script',
	            $this->getUrl('static/js/firebase-messaging-sw.js'),
                array('jquery'),
                WOFFICE_THEME_VERSION,
	            false
            );

            wp_localize_script(
            	$this->slug . '-firebase-script',
	            'WOFFICE_NOTIFICATIONS',
	            $data
            );

            wp_enqueue_script(
            	$this->slug . '-root',
	            $this->getUrl('static/js/woffice-push-notifications.js'),
                array('jquery'),
                WOFFICE_THEME_VERSION,
	            false
            );

            wp_localize_script(
            	$this->slug . 'root',
	            'WOFFICE_NOTIFICATIONS',
	            $data
            );

        }

        /**
         * Mark notification as read
         *
         * @return void
         */
        public function ajaxMarkRead()
        {
            // We check whether it's from our page or not.
            check_ajax_referer($this->ajaxAction . '_nonce', 'security');

            if (isset($_POST['component_action']) && isset($_POST['component_name']) && isset($_POST['item_id'])) {
                $user_id = $this->getUserID();
                $component_action = sanitize_text_field($_POST['component_action']);
                $component_name   = sanitize_text_field($_POST['component_name']);
                $item_id          = intval($_POST['item_id']);

                bp_notifications_mark_notifications_by_item_id($user_id, $item_id, $component_name, $component_action,false, 0);
            }

            wp_die();
        }

        /**
         * Save user FCM token for sending push notification later
         * When user accept browser notification, we will get this ajax call
         *
         * @return void
         */
        public function saveSubscription()
        {
            // We check whether it's from our page or not.
            check_ajax_referer($this->ajaxAction . '_nonce', 'security');

            $token = $_POST['token'];
            $user_id = $this->getUserID();
            $user_meta = get_user_meta($user_id, $this->fcmTokenUserMetaName(), true);

            // We store fcm token as array, since one user can have have multiple devices
            $token_data = array($token);

            if ($user_meta) {
                $user_meta = json_decode($user_meta, true);
                $token_data = array_unique(array_merge($user_meta, $token_data));
                $returned = update_user_meta($user_id, $this->fcmTokenUserMetaName(), json_encode($token_data));
            } else {
                $returned = add_user_meta($user_id, $this->fcmTokenUserMetaName(), json_encode($token_data));
            }

            echo json_encode($returned);
            wp_die();
        }

        /**
         * Name of the user meta for storing fcm token
         *
         * @return string
         */
        public function fcmTokenUserMetaName()
        {
            return 'woffice_live_push_token';
        }

        /**
         * Send push notification to FCM
         *
         * @param object $notification
         *
         * @return void
         */
        public function pushToFCM($notification)
        {

            $receiver_id = $notification->user_id;
            $registration_tokens = json_decode($this->getUserFCMTokens($receiver_id), true);

            // User have not subscribed to push notification
            if (!isset($registration_tokens) || sizeof($registration_tokens) == 0) {
                return;
            }

            // Notification icon
            $icon = fw_get_db_ext_settings_option($this->slug, 'notifications_icon');
            if (isset($icon, $icon['url'])) {
                $icon_url = $icon['url'];
            } else {
                $icon_url = $this->getUrl('assets/img/thumbnails/push.jpg');
            }

            $post_data = array(
                'registration_ids' => $registration_tokens,
                'notification'     => array(
                    'title' => $this->getTitle($notification),
                    'body'  => $this->getContent($notification),
                    'tag'   => time(),
                    'icon'  => $icon_url
                ),
                'data' => array(
                    'click_action' => bp_get_notifications_permalink($receiver_id)
                )
            );

            $header = array(
                'Expect'           => '',
                'Authorization'    => 'key='. fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_legacyServerKey'),
                'Content-Type'     => 'application/json',
                'cache-control'    => 'no-cache',
                'content-encoding' => '',
                'senderId'         => fw_get_db_ext_settings_option($this->slug, 'notifications_fcm_messagingSenderId')
            );

            wp_remote_post($this->fcmEndpoint, array(
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => $header,
                'body'        => json_encode($post_data),
                'cookies'     => array()
            ));
        }

        /**
         * Get current user's fcm tokens
         *
         * @param int $receiver_id
         *
         * @return string
         */
        public function getUserFCMTokens($receiver_id)
        {
            return get_user_meta($receiver_id, $this->fcmTokenUserMetaName(), true);
        }

        /**
         * Return file from this extension directory
         *
         * @param string $file_path slug of the component
         *
         * @return string path to the component's index
         */
        public function getUrl($file_path)
        {
            return woffice_get_extension_uri('live-notifications', $file_path);
        }

        /**
         * Format text for more readable
         *
         * @param $text
         *
         * @return string
         */
        public function formatText($text)
        {
            return str_replace('_', ' ', $text);
        }

        /**
         * Get notification title
         *
         * @param object $live_notification
         *
         * @return string
         */
        public function getTitle($live_notification)
        {
            $content = fw_get_db_ext_settings_option($this->slug, 'notifications_title', 'You\'ve a new notification!');
            $content = $this->formatText(str_replace('{component_name}', $live_notification->component_name, $content));

            /**
             * Filter `woffice_live_notification_title`
             *
             * @since 2.8.2
             *
             * @params string content
             * @params integer notification id
             */
            return apply_filters('woffice_live_notification_title', $content, $live_notification->id);
        }

        /**
         * Get notification content
         *
         * @param object $live_notification
         *
         * @return string
         */
        public function getContent($live_notification)
        {
            $content = fw_get_db_ext_settings_option(
            	$this->slug,
	            'notifications_content',
                'You\'ve a new notification!'
            );

            $content = $this->formatText(str_replace('{component_name}', $live_notification->component_name, $content));

            /**
             * Filter `woffice_live_notification_content`
             *
             * @since 2.8.2
             *
             * @params string content
             * @params integer notification id
             */
            return apply_filters('woffice_live_notification_content', $content, $live_notification->id);
        }
    }
}