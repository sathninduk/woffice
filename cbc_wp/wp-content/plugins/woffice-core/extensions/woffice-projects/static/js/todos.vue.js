/**
 * Task form component used for the Edit / New task
 */

var WofficeTodoForm = {
    props: ['labels', 'todo', 'isNew'],
    methods: {
        /**
         * Auto populates the member list on typing
         */
        autoFetchUsers: function () {

            var self = this;

            self.isLoading = true;

            if (self.newMemberSearch.length < 2)
                return;

            Woffice.$.ajax({
                url: Woffice.data.ajax_url.toString(),
                type: 'GET',
                data: {
                    'action': 'woffice_members_suggestion_autocomplete',
                    'nonce':WOFFICE.nonce,
                    'term': self.newMemberSearch,
                    'filter': self.labels.available_users
                },
                success: function (result) {
                    self.isLoading = false;
                    var potentialUsers = JSON.parse(result),
                        validatedUsers = [];
                    potentialUsers.forEach(function (user) {
                        var alreadyThereCount = 0;
                        self.assignedMembers.forEach(function (oldMember) {
                            alreadyThereCount = (parseInt(user.value) === parseInt(oldMember.value)) ? alreadyThereCount + 1 : alreadyThereCount;
                        });
                        if(alreadyThereCount === 0 )
                            validatedUsers.push(user);
                    });
                    self.newPotentialUsers = validatedUsers;
                }
            });
        },
        /**
         * Sets the a, user to the project users IDs array
         * This is triggered when the user is clicked from the auto-select list
         *
         * @param {object} member
         */
        assignProjectUser: function(member) {

            var self = this;

            self.assignedMembers.push(member);
            self.newPotentialUsers = [];
            self.newMemberSearch = '';
        }
    },
    data: function () {
        return {
            newMemberSearch: '',
            newPotentialUsers: [],
            isLoading: false,
            assignedMembers: [],
            changeMade: false
        }
    },
    mounted: function () {
        var self = this;
        var $ = Woffice.$;
            $.datetimepicker.setDateFormatter({
            parseDate: function (date, format) {
                var d = moment(date, format);
                return d.isValid() ? d.toDate() : false;
            },
            
            formatDate: function (date, format) {
                return moment(date).format(format);
            },

                //Optional if using mask input
            formatMask: function(format){
                return format
                    .replace(/Y{4}/g, '9999')
                    .replace(/Y{2}/g, '99')
                    .replace(/M{2}/g, '19')
                    .replace(/D{2}/g, '39')
                    .replace(/H{2}/g, '29')
                    .replace(/m{2}/g, '59')
                    .replace(/s{2}/g, '59');
            }
        });
        // Datepicker:
        var optionss = WOFFICE.datepicker_options;
        $(this.$el).find('.datetimepicker').datetimepicker({
            format: optionss.format,
            formatTime: optionss.formatTime,
            formatDate: optionss.formatDate,
            onClose:function(dp,$input){
                var key = $input.attr('name');
                self.todo._formatted_date = $input.val();
                if (key === 'todo_date') {
                    self.todo.date = $input.val();
                }
                if (key === 'start_date') {
                    self.todo.start_date = $input.val();
                }
            },
        });

        self.$parent.$on('addedTodo', function () {
            self.assignedMembers = [];
            self.newPotentialUsers = [];
        });

    },
    beforeMount: function() {
        var self = this;
        // We set our current assigned members
        if(typeof self.todo.assigned !== 'undefined' && self.todo.assigned && self.todo.assigned.length !== 0) {
            self.assignedMembers = self.todo.assigned.map(function (assigned) {
                assigned.value = parseInt(assigned._id);
                assigned.label = assigned._name;
                return assigned;
            });
        }
    },
    watch: {
        /**
         * On new Member typing
         *
         * @param {string} newVal
         */
        newMemberSearch: function (newVal) {

            var self = this;

            if (newVal.length < 2) {
                self.newPotentialUsers = [];
                return;
            }

            self.changeMade = true;

            self.autoFetchUsers();

        },
        /**
         * We listen for any assignedMember change - would be way better to use events later down the line
         *
         * @param {array} newVal
         */
        assignedMembers: function(newVal) {

            var self = this;

            // We check if mounted: to avoid self.todo.assigned to be clear by default
            if (!self.changeMade)
                return;

            self.todo.assigned = [];

            newVal.forEach(function (assigned) {
                self.todo.assigned.push(parseInt(assigned.value));
            });

        }
    },
    template: '\
    <form class="woffice-task-form" method="post">\
        <div class="row">\
            <div class="col-md-12">\
                <label for="todo_name">{{ labels.label_name }}</label>\
                <input type="text" name="todo_name" v-model="todo.title" required="required">\
            </div>\
        </div>\
        <div class="row">\
            <div class="col-md-6">\
                <label for="todo_start_date">{{ labels.label_start_date }}</label>\
                <input type="text" name="start_date" v-model="todo.start_date" autocomplete="off" class="datetimepicker">\
            </div>\
            <div class="col-md-6">\
                <label for="todo_date">{{ labels.label_due_date }}</label>\
                <input type="text" name="todo_date" v-model="todo.date" autocomplete="off" class="datetimepicker">\
            </div>\
        </div>\
        <div class="row">\
            <div class="col-md-6 woffice-add-todo-note">\
                <label for="todo_note">{{ labels.label_note }}</label>\
                <textarea rows="2" name="todo_note" v-model="todo.note"></textarea>\
            </div>\
            <div v-if="labels.is_advanced_task == 1" class="col-md-6 woffice-add-todo-comment">\
                <label for="todo_comment">{{ labels.label_comment }}</label>\
                <textarea rows="2" name="todo_comment" v-model="todo.comment"></textarea>\
            </div>\
            <div class="col-md-6 woffice-add-todo-assigned">\
                <div class="auto-fetch-members-wrapper">\
                    <label v-text="labels.label_assign"></label>\
                    <input type="text" v-model="newMemberSearch" :placeholder="labels.label_type">\
                    <ul v-if="newPotentialUsers" class="potential-users">\
                        <li v-for="member in newPotentialUsers" @click="assignProjectUser(member)" v-text="member.label"></li>\
                    </ul>\
                </div>\
                <ul class="project-users">\
                    <li v-for="(member, index) in assignedMembers">\
                        <span v-text="member.label"></span>\
                        <a href="#" @click.prevent="assignedMembers.splice(index, 1); changeMade = true" class="fa fa-times"></a>\
                    </li>\
                </ul>\
            </div>\
        </div>\
        <div class="clearfix">\
            <div class="float-left">\
                <label for="todo_urgent">{{ labels.label_urgent }}</label>\
                <input type="checkbox" id="todo_urgent" name="todo_urgent" v-model="todo.urgent" :checked="todo.urgent">\
            </div>\
            <div class="text-right">\
                <button v-if="isNew" href="#" @click.prevent="$root.addTodo(todo)" class="btn btn-default" type="submit"><i class="fa fa-plus-square"></i> {{ labels.label_add }}</button>\
                <button v-else href="#" @click.prevent="$root.editTodo(todo)" class="btn btn-default" type="submit"><i class="fa fa-pencil-alt"></i> {{ labels.label_edit }}</button>\
            </div>\
        </div>\
    </form>'
};

/**
 * Filters list
 */
var filters = {
    all: function (todos) {
        return todos
    },
    active: function (todos) {
        return todos.filter(function (todo) {
            return (todo.done === false);
        })
    },
    urgent: function (todos) {
        return todos.filter(function (todo) {
            return todo.urgent
        })
    },
    done: function (todos) {
        return todos.filter(function (todo) {
            return (todo.done === '1' || todo.done === true);
        })
    }
};

/**
 * Woffice To-do Manager using VUE.JS
 *
 * @since 2.4.0
 */
var wofficeTodo = new Vue({

    // Wrapper
    el: '#project-content-todo',

    // Components
    components: {
        'woffice-task-form': WofficeTodoForm
    },

    // Data handler
    data: {

        exchanger: WOFFICE_TODOS,

        // New to-do
        newTodo: {
            assigned: []
        },

        // Current filter
        currentFilter: 'all',

        // Date filter:
        dueDateFilter: 'no',

        // Todos:
        todos: [],

        // Deleted todos ids
        deletedTodos: [],

        // To display the alerts
        isSuccess: false,
        isFailure: false

    },

    computed: {
        filteredTodos: function () {

            var self = this;

            var filteredTodos = filters[self.currentFilter](self.todos);

            if(self.dueDateFilter !== 'no') {
                return (filteredTodos.sort(function (task1, task2) {
                    if(self.dueDateFilter === 'asc_due_date') {
                        return (task1._timestamp_date > task2._timestamp_date) ? 1 : -1;
                    }
                    else if(self.dueDateFilter === 'desc_due_date') {
                        return (task1._timestamp_date < task2._timestamp_date) ? 1 : -1;
                    }
                    else if(self.dueDateFilter === 'asc_completion_date') {
                        return (task1._completion_date > task2._completion_date) ? 1 : -1;
                    }
                    else if(self.dueDateFilter === 'desc_completion_date') {
                        return (task1._completion_date < task2._completion_date) ? 1 : -1;
                    }
                    else {
                        return 0;
                    }
                }));
            }

            return filteredTodos;

        }
    },

    mounted: function () {

        this.fetch();

        this.dragAndDrop();

    },

    methods: {

        /**
         * Fetches our todos
         */
        fetch: function () {

            var self = this;

            var loader = new Woffice.loader(jQuery('#project-content-todo'));

            jQuery.ajax({
                type:"POST",
                url: self.exchanger.ajax_url,
                data: {
                    action: 'woffice_todos_fetch',
                    _wpnonce: self.exchanger.nonce,
                    id: self.exchanger.project_id
                },
                success:function(data){
                    data = jQuery.parseJSON(data);
                    if(data.status === 'success') {
                        if (data.todos !== null) {
                            data.todos.forEach(function(todo) {
                                todo._id = todo._id ? todo._id : self.getId();
                            });
                        }
                        self.todos = data.todos;
                    }
                    loader.remove();
                }
            });

        },

        /**
         * Update our todos
         *
         * @param type {string} - add / delete / check / order / edit
         */
        update: function (type) {

            var self = this;

            var loader = new Woffice.loader(jQuery('#project-content-todo'));

            jQuery.ajax({
                type:"POST",
                url: self.exchanger.ajax_url,
                data: {
                    action: 'woffice_todos_update',
                    _wpnonce: self.exchanger.nonce,
                    id: self.exchanger.project_id,
                    todos: self.todos,
                    type: type,
                    deleted: self.deletedTodos
                },
                success:function(data){
                    self.deletedTodos = [];
                    data = jQuery.parseJSON(data);
                    if (type === "edit" || type === "add") {
                        self.fetch();
                    }
                    self.$emit(type +'edTodo');
                    loader.remove();
                    self.toggleAlert(data.status);
                }
            });

        },

        /**
         * Add a to-do and update the list
         *
         */
        addTodo: function(to_do) {

            var self = this;

            if (typeof to_do.title === 'undefined')
                return;

            // Default attributes
            to_do._id = self.getId();
            to_do._can_check = true;
            to_do._display_note = false;
            to_do._display_edit = false;
            to_do._is_new = true;
            to_do.email_sent = "not_sent";
            to_do.done = false;

            self.todos.push(to_do);

            // We refresh the assigned field
            self.newTodo = {
                assigned: []
            };

            self.update('add');

        },

        /**
         * Remove a to-do and update the list
         *
         * @param to_do {object}
         */
        removeTodo: function (to_do) {
            var self = this;
            if (window.confirm(self.exchanger.remove_confirm_text)) {
                this.todos.splice(this.todos.indexOf(to_do), 1);
                self.deletedTodos.push(to_do._id);
                this.update('delete');
            }
        },

        /**
         * Order the to-dos and update the list
         */
        orderTodo: function () {
            this.update('order');
        },

        /**
         * Edit a to-do and update the list
         *
         * @param to_do {object}
         */
        editTodo: function (to_do) {
            this.$forceUpdate();
            to_do._is_edited = true;
            this.update('edit');
        },

        /**
         * Toggle the edit form
         *
         * @param to_do {object}
         */
        toggleEit: function (to_do) {
            to_do._display_note = false;
            to_do._display_edit = !to_do._display_edit;
            this.$forceUpdate();
        },

        /**
         * Toggle the note
         *
         * @param to_do {object}
         */
        toggleNote: function (to_do) {
            to_do._display_edit = false;
            to_do._display_note = !to_do._display_note;
            this.$forceUpdate();
        },

        /**
         * Check a to-do
         *
         * @param to_do {object}
         */
        checkTodo: function (to_do) {
            if(to_do.done) {
                to_do.done = false;
            } else {
                to_do.done = true;
            }
            this.$forceUpdate();
            this.update('check');
        },

        /**
         * Set up the drag and drop layout
         */
        dragAndDrop: function () {

            var self = this,
                $wrapper = jQuery(".woffice-tasks-wrapper"),
                from;

            $wrapper.sortable({
                pullPlaceholder: false,
                itemSelector: '.woffice-task',
                placeholder: '<div class="todo-placeholder placeholder"><i class="fa fa-arrow-right"></i></div>',
                handle: ".drag-handle",
                start: function( event, ui ) {
                    jQuery(window).on('resize', function() {
                        if(jQuery(window).width() <= 450) {
                            $wrapper.sortable('disable');
                        } else {
                            $wrapper.sortable('enable');
                        }
                    });
                },
                onDragStart: function ($item, container, _super) {
                    var offset = $item.offset(),
                        pointer = container.rootGroup.pointer;
                    adjustment = {
                        left: pointer.left - offset.left,
                        top: pointer.top - offset.top
                    };
                    _super($item, container);
                },
                onDrag: function ($item, position) {
                    var index = jQuery('.woffice-task').index($item[0]);
                    from = index;
                    $item.css({
                        left: position.left - adjustment.left,
                        top: position.top - adjustment.top
                    });
                },
                onDrop: function ($item, container, _super) {
                    var index = jQuery('.woffice-task').index($item[0]);

                    _super($item, container);

                    var movedTask = self.todos[from];

                    self.todos.splice(from, 1);

                    self.todos.splice(index, 0, movedTask);

                    self.$forceUpdate();

                    self.orderTodo();

                }
            });

        },

        /**
         * Toggles an alert for 5 second
         *
         * @param status {string} - success / fail
         */
        toggleAlert: function (status) {

            var self = this;

            if(status === 'success') {
                self.isSuccess = true;
            } else {
                self.isFailure = true;
            }

            setTimeout(function () {
                self.isSuccess = false;
                self.isFailure = false;
            }, 5000);

        },

        /**
         * Generate an unique ID
         */
        getId : function() {
            return ((new Date().getTime() * (Math.floor(Math.random() * 1000) + 1))).toString(36);
        }

    }

});