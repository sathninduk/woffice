// Event for trigger event
var Event = new Vue();

Vue.component('calendar-event-form', {
    props: ['fieldOptions', 'visibility', 'isEdit', 'event'],
    component: {
        Event
    },
    data: function() {
        return {
            calendarEvent: {
                woffice_event_title: '',
                woffice_event_date_start: '',
                woffice_event_date_end: '',
                woffice_event_repeat_date_end: '',
                woffice_event_repeat: this.fieldOptions.woffice_event_repeat.value,
                woffice_event_color: this.fieldOptions.woffice_event_color.value,
                woffice_event_visibility: this.getVisibility(),
                woffice_event_description: '',
                woffice_event_location: '',
                woffice_event_image: '',
                woffice_event_image_name: '',
                woffice_event_ics: '',
                woffice_event_ics_name: '',
                woffice_event_link: ''
            },
            alertContent: '',
            advanceMode: false,
            repeatingMode: false,
            alertClass: '',
            exchanger: WOFFICE_EVENTS
        }
    },
    methods:{
        /**
         * Display date
         *
         * @param {object} date
         *
         * @returns {*}
         */
        showDate: function(date) {
            if (date[0] === '' || date[0] === undefined)
                return '';
            return date[0].split('-')[2];
        },

        /**
         * Check for default visibility
         *
         * @return {String}
         */
        getVisibility: function(){
           if (this.fieldOptions.woffice_event_visibility.choices.general === undefined) {
               return 'personal';
           }

           return this.visibility;
        },

        /**
         * Set repeat end field visibility based on the repetition value
         *
         * @param {Event} event
         */
        updateRepetition: function(event) {
            this.repeatingMode = event.target.value !== 'No';
        },
        /**
         * Set calendar attribute on file select
         *
         * @param {Event} event
         */
        processFile(event) {
            this.calendarEvent.woffice_event_image = event.target.files[0];
            jQuery('.custom-file-label').html(this.calendarEvent.woffice_event_image.name);
        },

        /**
         * Handle submit form, validate then submit
         */
        submitForm: function() {
            if (jQuery('#woffice-event-form')[0].checkValidity()) {
                if (this.isEdit === true) {
                    Event.$emit('editEvent', this.calendarEvent);
                } else {
                    Event.$emit('addEvent', this.calendarEvent);
                }
            }
            else {
                this.checkRequiredFields();
            }
        },

        /**
         * Hidden file input click trigger to show upload image selector
         */
        importEvents: function() {
            jQuery('#woffice_event_ics').click();
        },

        /**
         * Validate required event attributes
         */
        checkRequiredFields: function() {
            var missing_fields = [];
            var self = this;

            ['woffice_event_title', 'woffice_event_date_start', 'woffice_event_date_end'].forEach(function(value){
                if (!self.calendarEvent[value]) {
                    missing_fields.push(
                        '<div><strong>' + self.fieldOptions[value].label + '</strong>' + ' is required</div>'
                    );
                }
            });

            this.alertContent = missing_fields.join(' ');
            this.alertClass   = 'alert-danger';
        },

        /**
         * Validate required event attributes
         */
        hideModal: function() {
            Event.$emit('hideModal');
        }
    },
    mounted: function(){
        var self = this;

        if (this.isEdit === true) {
            this.calendarEvent = this.event;
        }

        var SingleEditVisibility = this.event;

        Event.$on('eventFailed', function(message) {
            self.alertClass = 'alert-danger';
            self.alertContent = message;
        });

        Event.$on('eventSucceed', function(message) {
            self.alertClass = 'alert-success';
            self.alertContent = message;
        });

        var locale = jQuery('html').attr('lang').substr(0, 2);
        jQuery.datetimepicker.setLocale(locale);

        jQuery.datetimepicker.setDateFormatter({
            parseDate: function (date, format) {
                var d = moment(date, format);
                return d.isValid() ? d.toDate() : false;
            },
            formatDate: function (date, format) {
                return moment(date).format(format);
            }
        });

        if (this.calendarEvent.woffice_event_repeat_date_end) {
            this.repeatingMode = true;
        }

        jQuery('.datetimepicker').datetimepicker({
            minDate: 0,
            format: this.exchanger.datepicker.format,
            formatTime: this.exchanger.datepicker.format_time,
            formatDate: this.exchanger.datepicker.format_date,
            defaultTime: this.exchanger.datepicker.default_time,
            dayOfWeekStart: this.exchanger.day_names.findIndex(day => day === this.exchanger.starting_day) || 0,
            onChangeDateTime: function (date, $input) {
                var key = $input.attr('name');

                if (date === null) {
                    return;
                }
                self.calendarEvent[key] = $input.val();

                if (key === 'woffice_event_date_start') {
                    // Set end date to start date plus 1 day
                    self.calendarEvent.woffice_event_date_end = moment(
                        new Date(date.getTime() + 24 * 60 * 60 * 1000)
                    ).format('YYYY-MM-DD H:mm');
                }
            }
        });

        jQuery('#woffice_event_ics').change(function(event) {
            if (event.target.files.length > 0) {
                self.calendarEvent.woffice_event_ics = event.target.files[0];
                Event.$emit('addEvent', self.calendarEvent);
            }
        });

        jQuery('body').click(function() {
            if(SingleEditVisibility !== undefined){
                jQuery('#visibility option:contains('+ SingleEditVisibility.woffice_event_visibility +')').attr('selected', 'selected');
            }
        });
    },
    template: `
            <transition name="modal">
              <div class="modal-mask">
                <div class="modal-wrapper">
       
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="event-modal-header">
                        <h5 class="modal-title">{{ fieldOptions.new_event_label }}</h5>
                        <a href="javascript:void(0)" @click.prevent="hideModal()" class="close">
                            <span aria-hidden="true" class="fa fa-times"></span>
                        </a>
                      </div>
                      <div class="modal-body">
                        <div class="alert" :class="alertClass" v-if="alertContent" v-html="alertContent"></div>
                        <form enctype="multipart/form-data" id="woffice-event-form">

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="required">{{ fieldOptions.woffice_event_title.label }}</label>
                                    <input type="text"
                                       class="form-control"
                                       aria-label="Default"
                                       aria-describedby="title" 
                                       v-model="calendarEvent.woffice_event_title"
                                       required="required">
                                </div>
                            </div>
                            
                            <div class="form-row wof-col-2">
                                <div class="form-group">
                                    <label class="required">{{ fieldOptions.woffice_event_date_start.label }}</label>
                                    <input type="text" 
                                      name="woffice_event_date_start" 
                                      class="form-control datetimepicker" 
                                      aria-label="Default" 
                                      aria-describedby="start_date"
                                      v-model="calendarEvent.woffice_event_date_start" 
                                      required="required"> 
                                </div>
                                <div class="form-group">
                                    <label class="required">{{ fieldOptions.woffice_event_date_end.label }}</label>
                                    <input type="text" 
                                        name="woffice_event_date_end"
                                        class="form-control datetimepicker" 
                                        aria-describedby="end_date" 
                                        v-model="calendarEvent.woffice_event_date_end" 
                                        required="required">
                                </div>
                            </div>
                            
                            <div class="form-row wof-col-3">
                                <div class="form-group">
                                    <label>{{ fieldOptions.woffice_event_repeat.label }}</label>
                                    <select id="repeat" v-model="calendarEvent.woffice_event_repeat" @change="updateRepetition($event)">
                                         <option v-for="(opt, key) in fieldOptions.woffice_event_repeat.choices" :value="key"> {{opt}} </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ fieldOptions.woffice_event_color.label }}</label>
                                    <select id="color" v-model="calendarEvent.woffice_event_color">
                                         <option v-for="(opt, key) in fieldOptions.woffice_event_color.choices" :value="key"> {{opt}} </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ fieldOptions.woffice_event_visibility.label }}</label>
                                    <select id="visibility" v-model="calendarEvent.woffice_event_visibility">
                                        <template v-for="(opt, key) in fieldOptions.woffice_event_visibility.choices">
                                            <optgroup :label="opt.attr.label" v-if="typeof opt === 'object'">
                                                <option v-for="(sub_opt, sub_key) in opt.choices" :value="sub_key"> {{ sub_opt }} </option>
                                            </optgroup>
                                            <option  :value="key" v-else> {{opt}} </option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row wof-col-2"  :class="repeatingMode ? 'd-block' : 'hidden'">
                                <div class="form-group">
                                    <label class="required">{{ fieldOptions.event_repeat_end_date_label }}</label>
                                    <input type="text" 
                                      name="woffice_event_repeat_date_end" 
                                      class="form-control datetimepicker" 
                                      aria-label="Default" 
                                      aria-describedby="start_date"
                                      v-model="calendarEvent.woffice_event_repeat_date_end"> 
                                </div>
                            </div>
                            <div v-if="advanceMode">
                                <div class="form-row wof-col-2">
                                    <div class="form-group">
                                        <label>{{ fieldOptions.woffice_event_location.label }}</label>
                                        <input type="text" 
                                            class="form-control" 
                                            aria-label="Large" 
                                            aria-describedby="inputGroup-sizing-sm" 
                                            v-model="calendarEvent.woffice_event_location">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ fieldOptions.woffice_event_link.label }}</label>
                                        <input type="text" 
                                            class="form-control" 
                                            aria-label="Large" 
                                            aria-describedby="inputGroup-sizing-sm" 
                                            v-model="calendarEvent.woffice_event_link">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>{{ fieldOptions.woffice_event_description.label }}</label>
                                        <textarea class="form-control" 
                                            aria-label="With textarea" 
                                            v-model="calendarEvent.woffice_event_description">
                                        </textarea>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <div class="input-group mb-3">
                                          <div class="input-group-prepend">
                                            <span class="input-group-text">{{ fieldOptions.woffice_event_image_label  }}</span>
                                          </div>
                                          <div class="custom-file">
                                            <input type="file" name="woffice_event_image_name" v-model="calendarEvent.woffice_event_image_name" @change="processFile($event)" accept="image/*" id="event_file">
                                            <label class="custom-file-label" for="event_file">
                                                {{ fieldOptions.event_chose_file_label}}
                                            </label>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <a href="javascript:void(0)" @click="advanceMode=false">
                                            {{ fieldOptions.less_settings }} 
                                            <i class="fas fa-chevron-up"></i>
                                        </a>
                                    </div>
                                 </div>
                            </div>
                            
                            <div v-else class="form-row">
                                <div class="form-group">
                                    <a href="javascript:void(0)" @click="advanceMode=true">
                                        {{ fieldOptions.advanced_settings }} 
                                        <i class="fas fa-chevron-down"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                        <div class="event-modal-footer text-center">
                            <button type="button" class="btn btn-primary mx-auto" @click.prevent="submitForm()">
                                {{fieldOptions.new_event_btn_save}}
                            </button>
                            <template v-if="!isEdit">
                                <span class="px-2">or</span>
                                <a href="javascript:void(0)" @click.prevent="importEvents">
                                    {{ fieldOptions.import_ics_file_label}} <span class="fa fa-file-import"></span>
                                </a>
                                <input type="file" class="hidden" v-model="calendarEvent.woffice_event_ics_name" 
                                id="woffice_event_ics" accept="text/calendar">
                            </template>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </transition>
    `
});

Vue.component('calendar-day', {
    props: ['date','date_type', 'user_events'],
    data: function() {
        return {
            dailyView: false,
            exchanger: WOFFICE_EVENTS,
            dailyViewDate: ''
        }
    },
    watch: {
        dailyView: {
            handler: function(){
                jQuery('body').toggleClass('hide-overflow');
            }
        }
    },
    methods: {
        /**
         * Display date
         *
         * @param {object} date
         *
         * @returns {*}
         */
        showDate: function(date) {
          if (date === '' || date === undefined)
              return '';
          return parseInt(date.split('-')[2]);
        },
        /**
         * Get time frame and visibility
         *
         * @param {Object} user_event
         *
         * @return {string}
         */
        eventTimeSpan(user_event) {
            var startDate   = moment(user_event.woffice_event_date_start).format('Y/M/D h:mm');
            var endDate     = moment(user_event.woffice_event_date_end).format('Y/M/D h:mm');

            var visibility  = user_event.event_visibility;
            var end         = user_event.event_end_time;
            var start       = user_event.event_time;
            var hourDiff    = moment(endDate).diff(startDate, "hours");

            if (hourDiff === 24) {
                return this.exchanger.full_day + ' - ' + visibility;
            }
            else if(hourDiff > 24) {
                return `${this.displayDate(startDate)} ${start} - ${this.displayDate(endDate)} ${end} <div> ${visibility}</div>`;
            }

            return `${start} - ${end} - ${visibility}`;
        },

        /**
         * Check if user has access to view in event if yes then return link otherwise no link
         * Its for when admin viewing other user's event
         *
         * @param {String} link
         *
         * @return {String|*}
         */
        getPostLink(link) {
            if (this.exchanger.enable_event) {
                return link;
            }
            return 'javascript:void(0)';
        },

        /**
         * Do not show link cursor for admin when viewing other profile
         *
         * @returns {String}
         */
        cursorClass() {
            return this.exchanger.enable_event ? '' : 'cursor:default;';
        },

        /**
         * Translate date and format for show
         *
         * @param {String} date
         *
         * @return {String}
         */
        displayDate(date) {
            var strDate = moment(date).format(this.exchanger.date_format);
            var days = this.exchanger.day_names_assoc;

            for (var day in days) {
                if (strDate.includes(day)) {
                    return strDate.replace(day, days[day]);
                }
            }

            return strDate;
        },

        /**
         * Show pop of daily events
         *
         * @param {String} viewDate
         */
        popUpDailyView(viewDate) {
            this.dailyViewDate = this.displayDate(viewDate);
            this.dailyView = true;
        }
    },
    template: `
    <div class="calendar-cell-container" :data-after-content="showDate(date)">
        <div class="ml-1 d-block text-left mb-1"><span class="date-day"> {{showDate(date)}}</span></div>
        <template v-if="user_events">
            <div class="wo-date-events">
                <div @click="popUpDailyView(date)" class="event-box font-weight-normal small text-white" 
                    v-for="user_event in user_events" :class="'bg-' + user_event.woffice_event_color">
                    <span class="time">{{ user_event.event_time }}</span>
                    <span class="name">{{ user_event.woffice_event_title }}</span>
                </div>
            </div>
        </template>
        <div v-if="dailyView">
            <transition name="modal">
              <div class="modal-mask">
                <div class="modal-wrapper">
       
                  <div class="modal-dialog" role="document">
                    <div class="modal-content event-daily-view text-left">
                      <div class="event-modal-header">
                        <h5 class="modal-title">{{ dailyViewDate }}</h5>
                        <a href="javascript:void(0)" @click.prevent="dailyView=false" class="close">
                            <span aria-hidden="true" class="fa fa-times"></span>
                        </a>
                      </div>
                      <div class="modal-body">
                        <div class="daily-event" 
                            :class="'bg-' + user_event.woffice_event_color" v-for="user_event in user_events">
                            <a :href="getPostLink(user_event.post_link)" class="event-link" :style="cursorClass()">
                                <div class="details">
                                    <h6>{{ user_event.woffice_event_title }}</h6>
                                    <p v-html="eventTimeSpan(user_event)"></p>
                                </div>
                                <span class="fa fa-external-link-square-alt"></span>
                            </a>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </transition>
        </div>
    </div>
    `
});
Vue.config.devtools = true;
/**
 * Woffice Event calendar using VUE.JS
 *
 * @since 2.8.2
 */
new Vue({

    props: {
        visibility: String,
        id: Number
    },
    component: {
        Event
    },
    name: 'woffice-calendar',
    el: '#js-woffice-calendar-events',
    // Data handler
    data: function(){
        return {
            exchanger: WOFFICE_EVENTS,
            // Events:
            calendarEvents: [],
            // Assoc events date => event
            assocEvents: [],

            // Selected year
            selectedYear: '',
            // Selected month
            selectedMonth: '',
            day: 0,
            mappedDay: 0,
            daysInMonth: 30,
            days: [],
            short_days: [],
            monthNames: [],
            dateCount: 1,
            minYear: 0,
            maxYear: 0,
            adding: false,
            available_days_index:[],
            container: jQuery('#js-woffice-calendar-events:visible').length,
            isWidget: false,
        }
    },
    created: function() {
        if (jQuery('#js-woffice-calendar-events:visible').length === 0) {
            return false;
        }

        var props_data = jQuery('#js-woffice-calendar-events').data('woffice-event-calendar');
        this.visibility = props_data.visibility;
        this.id = parseInt(props_data.id);
        this.isWidget = props_data.is_widget;
    },
    mounted: function() {
        this.initializeSetup();
        this.fetchEvents();

        var $element = jQuery(this.$el);
        var _this = this;

        setTimeout(function() {
            $element.find('[data-toggle="tooltip"]').tooltip();
        }, 1000);

        Event.$on('hideModal', _this.hideModal);
        Event.$on('addEvent', _this.addEvent);

        jQuery('.woffice-tab-layout__nav').find('li').on('woffice.tab.change', function (e, tab) {
            if (tab.indexOf('calendar') !== -1) {
                _this.fetchEvents();
            }
        });
    },
    beforeMount: function() {
        var dateObj = new Date();
        this.exchanger      = WOFFICE_EVENTS;
        this.selectedMonth  = dateObj.getUTCMonth();
        this.selectedYear   = dateObj.getUTCFullYear();
        this.monthNames     = this.exchanger.month_names;
    },
    computed: {
        /**
         * Populate multi dimensional array for calendar
         *
         * @returns {Array}
         */
        populateData: function() {

            var data=[];
            var date = 1;
            for (var i = 0; i < 6; i++) {
                data[i] = [];
                for (var j = 0; j < 7; j++) {
                    data[i][j] = '';
                    if (i === 0 && j < this.mappedDay) {
                        data[i][j] = ('');
                    }
                    else if (date > this.daysInMonth) {
                        break;
                    }
                    else {
                        data[i][j] = this.selectedYear + '-' + (("0" + (this.selectedMonth + 1)).slice(-2)) + '-' + (("0" + date).slice(-2));
                        date++;
                    }
                }

                var allEmpty = true;
                for (var m=0; m < data[i].length; m++) {
                    if ((data[i][m])) {
                        allEmpty = false;
                       break;
                    }
                }

                if (allEmpty) {
                    delete data[i];
                }
            }

            return data;
        }
    },

    watch: {
        /**
         * Month change to refetch and re-render calendar
         *
         * @param {string} newVal
         * @param {string} oldVal
         */
        selectedMonth: {
            handler: function(newVal, oldVal){
                if (!Number.isInteger(oldVal)) {
                    return;
                }

                this.eventsReload();
            }
        },

        container: {
            handler: function(){
                this.eventsReload();
            }
        },

        /**
         * Year change to refetch and re-render calendar
         *
         * @param {string} newVal
         * @param {string} oldVal
         */
        selectedYear: {
            handler: function(newVal, oldVal){
                if (!Number.isInteger(oldVal)) {
                    return;
                }

                this.eventsReload();
            }
        },

        /**
         * New event form pop up track
         */
        adding: {
            handler: function(){
                jQuery('body').toggleClass('hide-overflow has-modal');
            }
        }
    },
    methods: {

        /**
         * Fetch and regenerate calendar
         */
        eventsReload: function() {
            this.assocEvents = [];
            this.fetchEvents();
            this.initializeSetup();
            this.$forceUpdate();
        },

        /**
         * Generate calendar based on the extension setting
         */
        initializeSetup: function () {
            var ordered = this.orderedDays();
            var firstDay = (new Date(this.selectedYear, this.selectedMonth)).getDay();

            this.days        = ordered.days;
            this.short_days  = ordered.short_days;
            this.mappedDay   = this.mapFirstDay(firstDay);
            this.daysInMonth = parseInt(32 - new Date(this.selectedYear, this.selectedMonth, 32).getDate());
            this.day         = this.exchanger.day;
            this.minYear     = parseInt(this.exchanger.year) - 10;
            this.maxYear     = this.minYear + 20;
        },

        /**
         * Fetches user events
         */
        fetchEvents: function () {

            var self = this;

            var loader = new Woffice.loader(jQuery('#woffice-calendar'));
            jQuery.ajax({
                type: 'POST',
                url: self.exchanger.ajax_url + '?_=' + new Date().getTime(),
                data: {
                    action: self.exchanger.fetch_action,
                    _wpnonce: self.exchanger.nonce,
                    month: this.selectedMonth + 1,
                    year: this.selectedYear,
                    visibility: self.visibility,
                    id: self.id
                },
                success: function (data) {
                    loader.remove();
                    data = jQuery.parseJSON(data);

                    if (data.status === 'success') {
                        self.calendarEvents = data.events;
                        self.populateDateWiseEvents();
                    }
                },
                error: function () {
                    loader.remove();
                }
            });

        },

        /**
         * Populate date wise array of events
         */
        populateDateWiseEvents: function () {
            this.assocEvents = [];
            self = this;

            this.calendarEvents.forEach(function(calendarEvent){
                if (self.assocEvents[calendarEvent.event_date] === undefined) {
                    self.assocEvents[calendarEvent.event_date] = [];
                }

                self.assocEvents[calendarEvent.event_date].push(calendarEvent);
            });
        },

        /**
         * Map between regular index of the day and extension setting starting day
         *
         * @param {int} regular_day_index
         *
         * @return {int}
         */
        mapFirstDay: function(regular_day_index){
            var month_first_day = this.exchanger.day_names[regular_day_index];

            return this.orderedDays().days.indexOf(month_first_day);
        },


        /**
         * Order days according to the admin setting
         *
         * @return {Object}
         */
        orderedDays: function() {
            var first_day      = this.exchanger.starting_day,
                cal_days       = this.exchanger.day_names,
                short_cal_days = this.exchanger.short_day_names;

            var start_day      = cal_days.indexOf(first_day);
            var days           = [first_day];
            var short_days     = [short_cal_days[start_day]];

            for (var i = start_day + 1; i < 7; i++) {
                days.push(cal_days[i]);
                short_days.push(short_cal_days[i]);
            }

            for (var i = 0; i < start_day; i++) {
                days.push(cal_days[i]);
                short_days.push(short_cal_days[i]);
            }

            return {
                days: days,
                short_days: short_days
            };
        },

        /**
         * Add new event
         *
         * @param {object} calendarEvent
         */
        addEvent: function (calendarEvent) {
            var self     = this;
            var loader   = new Woffice.loader(jQuery('#woffice-event-form'));
            var formData = new FormData();

            formData.append('action', self.exchanger.add_action);
            formData.append('_wpnonce', self.exchanger.nonce);

            jQuery.each(calendarEvent, function(key, val) {
                formData.append('post_meta[' + key + ']', val);
            });

            if (calendarEvent.woffice_event_ics_name) {
                formData.append('old_events', this.eventHash());
            }

            jQuery.ajax({
                type: 'POST',
                url: self.exchanger.ajax_url,
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    loader.remove();
                    data = jQuery.parseJSON(data);
                    if (data.success == 1) {
                        Event.$emit('eventSucceed', data.message);
                        setTimeout(function(){
                            self.adding = false;
                            },
                            2000
                        );
                        self.eventsReload()
                    }
                    else {
                        Event.$emit('eventFailed', data.message);
                    }
                },
                error: function() {
                    loader.remove();
                }
            });

        },

        /**
         * Add loaded event title and date when we are uploading ics file for duplication check
         *
         * @return {String}
         */
        eventHash: function() {
            let oldEvents = {};

            this.calendarEvents.forEach( function(event) {
                oldEvents[event.woffice_event_title] = [event.woffice_event_date_start, event.woffice_event_visibility];
            });

             return JSON.stringify(oldEvents);
        },

        /**
         * Download events of selected month
         */
        downloadEvents: function() {
            var self = this;
            var data = {
                action: self.exchanger.events_download,
                _wpnonce: self.exchanger.nonce,
                    month: this.selectedMonth + 1,
                    year: this.selectedYear,
                    visibility: self.visibility,
                    id: self.id
            };
            var urlParams = jQuery.param(data);
            window.open(self.exchanger.ajax_url +'?'+ urlParams, '_blank');
        },

        /**
         * Hide modal
         */
        hideModal: function() {
            this.adding= false;
        },

        /**
         * Get events of a specific date
         *
         * @param {string} eventDate
         * @returns {*}
         */
        getEvents: function(eventDate) {
            if (this.assocEvents[eventDate] === undefined) {
                return [];
            }

            return this.assocEvents[eventDate];
        },

        /**
         * Year range for year select
         *
         * @returns {*[]}
         */
        yearRange: function() {
            var self = this;
            
            return new Array(this.maxYear - this.minYear).fill().map(function(d, i) {
                return (i + self.minYear);
            });
        },

        /**
         * Get css class based on the event type
         *
         * @param {string} cellDate
         * @returns {string}
         */
        getType: function(cellDate){
            if (!cellDate.length) {
                return 'disabled v-hidden';
            }

            var calendarDate = new Date(cellDate+' 00:00:00');
            var currentDate  = new Date();

            if(calendarDate.getFullYear() > currentDate.getFullYear()) {
                return 'future';
            }
            else if(calendarDate.getFullYear() < currentDate.getFullYear()) {
                return 'passed';
            }

            if (calendarDate.getMonth() > currentDate.getMonth()) {
                return 'future';
            }
            else if (calendarDate.getMonth() < currentDate.getMonth()) {
                return 'passed';
            }


            if (calendarDate.getDate() === currentDate.getDate()) {
                return 'present';
            }
            else if(calendarDate.getDate() > currentDate.getDate()){
                return  'future';
            }

            return 'passed';
        },

        /**
         * Get css class for weekend and week days
         *
         * @param {int} index
         * @returns {string}
         */
        weekDaysClass: function(index) {
            var dayName = this.days[index];
            
            if (undefined === dayName || !this.exchanger.available_days.includes(dayName.toLowerCase())) {
                return 'half-opacity';
            }
            
            return 'ok';
        },
        /**
         * Get css class of calendar cell based on the admin selected days
         *
         * @param {string} cellDate
         * @param {int} index
         *
         * @return {string}
         */
        getCellClass: function(cellDate, index) {
            return this.weekDaysClass(index) + ' ' + this.getType(cellDate);
        },

        /**
         * Get the event's visibility
         *
         * @return {*}
         */
        getVisibility: function() {
            if (this.visibility === 'personal' || this.visibility === 'general') {
                return this.visibility;
            }

            return this.visibility + '_' + this.id;
        }
    },
    template: `
    <div id="woffice-calendar" class="woffice-calendar" :class="{'woffice-calendar__widget': isWidget}">
       <div class="container">
            <div class="row mb-5 woffice-calendar__nav">
                <div id="month-year-select" class="col-sm-8">
                    <div class="select">
                        <select class="form-control" id="month" v-model="selectedMonth">
                            <option v-for="(month, index) in monthNames" :key="index" :value="index" >{{month}}</option>
                        </select>
                    </div>
                    <div class="select">
                        <select class="form-control" id="year" v-model="selectedYear">
                            <template v-for="year in yearRange()">
                                <option :value="year">{{year}}</option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="calendar-actions col-sm-4 text-right">
                    <span class="calendar-nav">
                        <span @click="selectedMonth -=1"
                              class="fa fa-angle-left"
                              v-show="selectedMonth > 0"
                              data-toggle="tooltip"
                              data-placement="top"
                              :title="exchanger.previous_month"></span>
                        <span @click="selectedMonth +=1"
                              class="fa fa-angle-right"
                              v-show="selectedMonth < 11"
                              data-toggle="tooltip"
                              data-placement="top"
                              :title="exchanger.next_month"></span>
                    </span>
                    <span v-if="exchanger.enable_event" 
                       class="calendar-event-export p-2"
                       href="javascript:void(0)"
                       @click.prevent="downloadEvents"
                       v-show="calendarEvents.length > 0"
                       data-toggle="tooltip"
                       data-placement="top"
                       :title="exchanger.export_events">
                        <span class="fa fa-file-export"></span>
                    </span>
                </div>
            </div>
       </div>
       <div class="container">
            <div class="text-center" id="calendar">
                <div class="calendar-header row">
                    <div v-for="(day, index) in days" :key="day" class="calendar-cell" :class="weekDaysClass(index)">
                        <span class="full">{{ day }}</span>
                        <span class="short">{{ short_days[index] }}</span>
                    </div>
                </div>
                <div id="calendar-body" class="text-center">
                
                    <div v-for="(calendar_row, index) in populateData" class="row" :key="index">
                        <div v-for="(calendar_cell, index) in calendar_row" class="border calendar-cell calendar-box"  
                            :key="calendar_cell" :class="getCellClass(calendar_cell, index)">
                          <calendar-day 
                              :date="calendar_cell" 
                              :date_type="getType(calendar_cell)" 
                              :user_events="getEvents(calendar_cell)">
                          </calendar-day>
                        </div>
                    </div>
                   
                </div>
            </div>
       </div>
       <div class="text-center" v-if="exchanger.enable_event && exchanger.field_options.user > 0">
           <a href="javascript:void(0)" class="btn btn-primary" @click="adding=true">
            <span class="fa fa-calendar mr-2"></span>
            {{ exchanger.field_options.create_event_label }}
           </a>
       </div>
        
        <div v-if="adding">
            <calendar-event-form :fieldOptions="exchanger.field_options" :visibility="getVisibility()"></calendar-event-form>
        </div>
        
    </div>`
});

/**
 * Woffice Event single view and edit using VUE.JS
 *
 * @since 2.8.2
 */
Vue.component('calendar-single-view', {
    props: ['event'],
    component: {
        Event
    },
    data: function() {
        return {
            exchanger: WOFFICE_EVENTS,
            edit: false,
            editObject: {}
        }
    },
    mounted: function() {
        jQuery('#event-tab-edit a').on('click', this.showModal);

        this.exchanger.field_options.new_event_label = this.exchanger.field_options.edit_event_label;
        this.exchanger.field_options.new_event_btn_save = this.exchanger.field_options.event_btn_save;

        var _this = this;
        Event.$on('hideModal', _this.hideModal);
        Event.$on('editEvent', _this.editEvent);

    },
    methods: {
        /**
         * Show modal
         */
        showModal: function(e) {
            e.preventDefault();
            this.editObject = JSON.parse(JSON.stringify(this.event));
            this.edit = true;
        },
        /**
         * Hide modal
         */
        hideModal: function() {
            this.edit = false;
        },
        /**
         * Event single update handler
         */
        editEvent: function(calendarEvent) {
            var self = this;
            var loader = new Woffice.loader(jQuery('#woffice-event-form'));
            var formData = new FormData();

            formData.append('action', self.exchanger.edit_action);
            formData.append('_wpnonce', self.exchanger.nonce);

            jQuery.each(calendarEvent, function(key, val) {
                formData.append('post_meta[' + key + ']', val);
            });

            jQuery.ajax({
                type: 'POST',
                url: self.exchanger.ajax_url,
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    loader.remove();
                    if (data.success === 1) {

                        Event.$emit('eventSucceed', data.message);

                        // Assign updated event for view
                        self.event = data.updated_event;

                        var title = jQuery(document).prop('title');
                        title = title.replace(
                            jQuery('.single-woffice-event').find('h1.entry-title').html(),
                            self.event.woffice_event_title
                        );
                        jQuery(document).prop('title', title);
                        jQuery('.single-woffice-event').find('h1.entry-title').html(self.event.woffice_event_title);

                        // Event feature image changed if uploaded
                        if (data.feature_image) {
                            var $element = jQuery('.intern-thumbnail');

                            if ($element.length) {
                                jQuery('.intern-thumbnail').empty();
                                $element.append(jQuery('<img>').attr('src', data.feature_image));
                            } else {
                                jQuery('#event-nav').before(
                                    jQuery('<div></div>').addClass('intern-thumbnail ')
                                        .append(jQuery('<img>').attr('src', data.feature_image))
                                );
                            }
                        }
                        var _self = self;
                        setTimeout(function(){
                            _self.hideModal();
                        }, 2000);
                    } else {
                        Event.$emit('eventFailed', data.message);
                    }
                },
                error: function() {
                    loader.remove();
                }
            });
        },
    },
    template: `
        <div class="woffice-calendar">
            <header id="project-meta" class="event-metas">
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <div class="d-flex p-3 mr-2">
                            <span class="event-meta">
                                <i class="text-light fa fa-calendar-alt"></i>
                                {{ event.woffice_event_date_start_i18n }}
                            </span>
                        </div>
                    </div>
                    <div v-if="event.woffice_event_repeat_date_end" class="col-md-4 col-sm-12">
                        <div class="d-flex p-3 mr-2">
                            <span class="event-meta">
                                <i class="text-light fa fa-calendar-alt"></i>
                                {{ event.woffice_event_repeat_date_end_i18n }}
                            </span>
                        </div>
                    </div>
                    <div v-else class="col-md-4 col-sm-12">
                        <div class="d-flex p-3 mr-2">
                            <span class="event-meta">
                                <i class="text-light fa fa-calendar-alt"></i>
                                {{ event.woffice_event_date_end_i18n }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="d-flex p-3 mr-2">
                            <span class="event-meta">
                                <i class="text-light fa fa-sync"></i>
                                {{ event.woffice_event_repeat }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="d-flex p-3 mr-2">
                            <span class="event-meta event-meta--color">
                                <i class="text-light fa fa-pallet"></i>
                                {{ event.woffice_event_color }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="d-flex p-3 mr-2">
                            <span class="event-meta">
                                <i class="text-light fa fa-user-shield"></i>
                                {{ event.woffice_event_visibility }}
                            </span>
                        </div>
                    </div>
                    <div v-if="event.woffice_event_location" class="col-md-4 col-sm-12">
                        <div class="d-flex p-3 mr-2">
                            <span class="event-meta">
                                <i class="text-light fa fa-map-marked-alt"></i>
                                {{ event.woffice_event_location }}
                            </span>
                        </div>
                    </div>
                    <div v-if="event.woffice_event_link" class="col-md-4 col-sm-12">
                        <div class="d-flex p-3 mr-2">
                            <span class="event-meta">
                                <i class="text-light fa fa-link"></i>
                                <a :href="event.woffice_event_link">{{ event.woffice_event_link }}</a>
                            </span>
                        </div>
                    </div>
    
                </div>
            </header>

            <div class="intern-padding text-center">
                {{ event.woffice_event_description }}
            </div>

            <calendar-event-form
                v-if="edit"
                :fieldOptions="exchanger.field_options"
                :visibility="true"
                :is-edit="true"
                :event="editObject"></calendar-event-form>
        </div>
    `
});

if (document.getElementById('event-view')) {
    /**
     * Vue instance for single event
     */
    new Vue({
        el: '#event-view'
    });
}
