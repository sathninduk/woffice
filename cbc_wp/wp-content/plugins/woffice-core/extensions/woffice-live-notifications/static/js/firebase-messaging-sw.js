/**
 * Get sender id from the worker URL
 *
 * @param {string} query_name
 *
 * @return {string}
 */
function get_service_worker_url_parameters(query_name) {

    var query_vars = {};
    self.location.href.replace(self.location.hash, '').replace(
        /[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
        function (m, key, value) { // callback
            query_vars[key] = value !== undefined ? value : '';
        }
    );
    if (query_name) {
        return query_vars[query_name] ? query_vars[query_name] : null;
    }
    return query_vars;
}

var sender_id = get_service_worker_url_parameters('messagingSenderId');

// Only load firebase scripts when importScripts available
if ('function' === typeof importScripts) {
    importScripts("https://www.gstatic.com/firebasejs/5.8.5/firebase-app.js");
    importScripts("https://www.gstatic.com/firebasejs/5.8.5/firebase-messaging.js");

    // Initialize Firebase
    var config = {
        messagingSenderId: sender_id
    };
    if (!firebase.apps.length) {
        firebase.initializeApp(config);
    }
    var messaging = firebase.messaging();

    // Show user notification out side the browser
    messaging.setBackgroundMessageHandler(function (payload) {
        console.info('[firebase-messaging-sw.js] Received background message ', payload);
        // Customize notification here
        var notificationTitle = payload.data.title;
        var notificationOptions = {
            body: payload.data.body,
            icon: payload.data.icon
        };

        return self.registration.showNotification(notificationTitle,
            notificationOptions);
    });

    /**
     * Add listener for push and how notification
     */
    self.addEventListener('push', function (event) {

        var notification = event.data.json().notification;
        var data = event.data.json().data;
        var title = notification.title;
        var body = notification.body;
        var icon = notification.icon;
        var tag = notification.tag;

        event.waitUntil(
            self.registration.showNotification(title, {
                body: body,
                icon: icon,
                tag: tag,
                data: data,
                vibrate: [100, 50, 100]
            })
        );
    });

    /**
     * On click notification
     */
    self.addEventListener('notificationclick', function (event) {

        event.notification.close();

        var redirect_url = event.notification.data.click_action || '/';
        // This looks to see if the current is already open and
        // focuses if it is
        event.waitUntil(clients.openWindow(redirect_url));
    })

}
