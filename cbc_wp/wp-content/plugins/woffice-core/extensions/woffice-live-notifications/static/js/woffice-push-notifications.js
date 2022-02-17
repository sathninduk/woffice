(function ($) {

    if (!('Notification' in window)) {
        alert('This browser does not support notifications!. You need to use modern browser.');
        return;
    }


    // Initialize Firebase
    var config = {
        apiKey: WOFFICE_NOTIFICATIONS.apiKey,
        authDomain: WOFFICE_NOTIFICATIONS.authDomain,
        databaseURL: WOFFICE_NOTIFICATIONS.databaseURL,
        projectId: WOFFICE_NOTIFICATIONS.projectId,
        storageBucket: WOFFICE_NOTIFICATIONS.storageBucket,
        messagingSenderId: WOFFICE_NOTIFICATIONS.messagingSenderId
    };

    if (!firebase.apps.length) {
        firebase.initializeApp(config);
    }

    var vapidPublicKey = WOFFICE_NOTIFICATIONS.webPushCertificate;
    var convertedVapidKey = urlBase64ToUint8Array(vapidPublicKey);
    var messaging = firebase.messaging();

    messaging.usePublicVapidKey(WOFFICE_NOTIFICATIONS.webPushCertificate);

    // Register service worker
    if ('serviceWorker' in navigator) {

        var worker_url = WOFFICE_NOTIFICATIONS.serviceWorker + '&messagingSenderId=' + WOFFICE_NOTIFICATIONS.messagingSenderId;
        navigator.serviceWorker.register(worker_url).then(function (registration) {

            // Use this service worker
            messaging.useServiceWorker(registration);

            // Request permission
            messaging.requestPermission().then(function () {

                messaging.getToken().then(function (currentToken) {
                    if (currentToken) {
                        sendTokenToServer(currentToken);
                    }
                }).catch(function (err) {
                    console.error('An error occurred while retrieving token. ', err);
                });
            }).catch(function (err) {
                console.error('Unable to get permission to notify.', err);
            });

            // Subscribe user
            registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: convertedVapidKey
            }).catch(function (e) {
                console.error('Subcription error:', e);
            });

            // Show notification on browser
            messaging.onMessage(function (payload) {
                displayNotification(payload);
            });
        }).catch(function (err) {
            // registration failed :(
            console.error('ServiceWorker registration failed: ', err);
        });

        /**
         * Callback fired if Instance ID token is updated.
         */
        messaging.onTokenRefresh(function () {
            messaging.getToken().then(function (refreshedToken) {
                // Indicate that the new Instance ID token has not yet been sent to the app server.
                // Send Instance ID token to app server.
                sendTokenToServer(refreshedToken);
            }).catch(function (err) {
                console.error('Unable to retrieve refreshed token ', err);
            });
        });
    }
    else {
        console.error('Browser does not support push notification');
    }

    /**
     * Show notification on browser
     *
     * @param {object} payload
     */
    function displayNotification(payload) {
        if (Notification.permission == 'granted') {
            jQuery.when(navigator.serviceWorker.getRegistration()).done(function (registration) {
                var options = {
                    body: payload.notification.body,
                    icon: payload.notification.icon,
                    vibrate: [100, 50, 100],
                    data: {
                        click_action: payload.data.click_action
                    }
                };
                new Notification(payload.notification.title, options);
            });
        }
    }

    /**
     * Send token to server and saved it
     *
     * @param {string} currentToken
     *
     * @return {void}
     */
    function sendTokenToServer(currentToken) {
        var woffice_fcm_user = WOFFICE_NOTIFICATIONS.fcm_user;
        var user_tokens = WOFFICE_NOTIFICATIONS.fcm_user_tokens;

        if (!user_tokens.includes(currentToken) && woffice_fcm_user) {
            storeToken(currentToken);
        }
    }

    /**
     * Encode public key
     *
     * @param {string} base64String
     *
     * @return {Uint8Array}
     */
    function urlBase64ToUint8Array(base64String) {
        var padding = '='.repeat((4 - base64String.length % 4) % 4);
        var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        var rawData = window.atob(base64);
        var outputArray = new Uint8Array(rawData.length);

        for (var i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }

    /**
     * Store token into server by ajax call
     *
     * @param {string} token
     */
    function storeToken(token) {
        var data = {
            'action': WOFFICE_NOTIFICATIONS.notifications_subscription,
            'security': WOFFICE_NOTIFICATIONS.notifications_nonce,
            'token': token
        };
        jQuery.post(WOFFICE_NOTIFICATIONS.ajax_url, data, function (response) {
            if (response.length === 0) {
                console.error('Something wrong. could not saved token!');
            }
        });
    }
})(jQuery);
