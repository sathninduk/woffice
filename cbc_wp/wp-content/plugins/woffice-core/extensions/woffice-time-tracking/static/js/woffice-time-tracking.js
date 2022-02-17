/**
 * Woffice Time Tracking JS
 *
 * @type {{wrapper: *, init: WofficeTimeTracking.init}}
 */
var WofficeTimeTracking = {

    wrapper: null,
    isClockActive: false,

    /**
     * Live time clock, increases every minute
     */
    liveClock: function() {

        var self = this,
            $clock = self.wrapper.find('.woffice-time-tracking-view > .woffice-time-tracking_time-displayed');

        var timeString = $clock.html();

        if (!timeString) {
            return;
        }

        var timeArray  = timeString.split(':');
        var t          = new Date();

        t.setHours(timeArray[0]);
        t.setMinutes(timeArray[1]);
        t.setSeconds(timeArray[2]);

        this.liveTime = setInterval(function () {
            if (!self.isClockActive) {
                return;
            }

            t.setSeconds(t.getSeconds() + 1);

            var hours = t.getHours();
            hours = (hours.toString().length === 1) ? '0' + hours.toString() : hours.toString();

            var minutes = t.getMinutes();
            minutes = (minutes.toString().length === 1) ? '0' + minutes.toString() : minutes.toString();

            var seconds = t.getSeconds();
            seconds = (seconds.toString().length === 1) ? '0' + seconds.toString() : seconds.toString();

            $clock.html(hours +':'+ minutes +':'+ seconds);
        }, 1000);

    },

    /**
     * Switch between the timer and the history tab
     */
    switchTab: function () {

        var self = this,
            $toggle = self.wrapper.find('.woffice-time-tracking-history-toggle'),
            $tabs = self.wrapper.find('.woffice-time-tracking-view'),
            $icon = self.wrapper.find('.woffice-time-tracking-history-toggle i');

        $toggle.toggleClass('is-history');

        if ($toggle.hasClass('is-history')) {
            $tabs.first().fadeOut();
            $tabs.last().fadeIn();
            $icon.removeClass('fa-history');
            $icon.addClass('fa-times');
        } else {
            $tabs.first().fadeIn();
            $tabs.last().fadeOut();
            $icon.addClass('fa-history');
            $icon.removeClass('fa-times');
        }

    },

    /**
     * Change the state: stop || start
     *
     * @param {Event} e
     */
    stateChange: function (e) {
        var self = this,
            $target = jQuery(e.target);

        $target = (['a'].indexOf($target.get(0).tagName) !== -1) ? $target : $target.closest('a');

        var action = $target.data('action');
        var $btnStart = self.wrapper.find('.woffice-time-tracking-actions .woffice-time-tracking-state-toggle.start'),
            $btnStop  = self.wrapper.find('.woffice-time-tracking-actions .woffice-time-tracking-state-toggle.stop');

        if (action === 'modal') {
            jQuery('#woffice-time-tracking-meta').modal('show');
            return ;
        }

        if (action !== 'modal') {
            self.isClockActive = !self.isClockActive;
            self.wrapper.toggleClass('is-tracking');
            self.wrapper.addClass('is-loading');

            jQuery.post(WOFFICE_TIME_TRACKING.ajax_url, {
                action: 'woffice_time_tracking',
                _wpnonce: WOFFICE_TIME_TRACKING.time_tracking_nonce,
                tracking_action: action,
                tracking_meta: jQuery('input[name="woffice-time-tracking-meta"]').val()
            }).done(function () {

                if (action === 'start') {
                    $btnStart.addClass('d-none');
                    $btnStop.removeClass('d-none');
                    jQuery('#woffice-time-tracking-meta').modal('hide');
                }
                else {
                    $btnStop.addClass('d-none');
                    $btnStart.removeClass('d-none');
                }

                self.wrapper.removeClass('is-loading');
            });
        }
    },

    /**
     * Init the widget
     */
    init: function () {

        var self = this;

        self.wrapper = jQuery('.woffice-time-tracking');

        self.wrapper.find('[data-toggle="popover"]').popover();

        // If we are currently tracking
        if(self.wrapper.hasClass('is-tracking')) {
            self.isClockActive = true;
        }

        // Clock:
        self.liveClock();

        // Tracking state
        jQuery('.woffice-time-tracking-state-toggle').on('click', function (e) {
            e.preventDefault();
            self.stateChange(e);
        });

        // Show history
        self.wrapper.find('.woffice-time-tracking-history-toggle').on('click', function (e) {
            e.preventDefault();
            self.switchTab();
        });

    }

};

// Starts it!
jQuery( document ).ready(function() {
    WofficeTimeTracking.init();
});