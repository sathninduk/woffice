/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* PROJECTS
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
(function($) {
    "use strict";

    /**
     * Handle the autocomplete members for every input field in the page with the right HTML layout and attributes
     */
    $.wofficeMembersAutoompleteWatcher = function () {
        var $body = $( 'body' ),
            inst = this;

        inst.init = function() {
            inst.keyDownWatcher();

            inst.removeMemberWatcher();
        };

        /**
         * Listen the keydown of the fields and fetch the suggested members
         */
        inst.keyDownWatcher = function() {

            $body.on('keydown.autocomplete', '.woffice-users-suggest_input', function() {

                //console.log('Triggered autocomplete members fetcher');
                $(this).autocomplete({
                    source: ajaxurl + '?action=woffice_members_suggestion_autocomplete&nonce=' + WOFFICE.nonce,
                    delay: 500,
                    minLength: 2,
                    position: ( 'undefined' !== typeof isRtl && isRtl ) ? {
                        my: 'right top',
                        at: 'right bottom'
                    } : {
                        my: 'left top',
                        at: 'left bottom'
                    },
                    open: function () {
                        $(this).addClass('open');
                    },
                    close: function () {
                        $(this).removeClass('open');
                        $(this).val('');
                    },
                    select: function (event, ui) {
                        inst.add_member_to_list(event, ui);
                    }
                });
            });
        };

        /**
         *  Remove a member on 'x' click
         */
        inst.removeMemberWatcher = function() {
            $body.on( 'click', '.woffice-users-suggest_members-list .woffice-users-suggest_remove-member', function( e ) {
                e.preventDefault();

                var $wrapper = $(e.target).closest('.woffice-users-suggest');

                //Remove the item
                $( $(e.target).closest('li') ).remove();

                //Update the hidden field containing all ids
                var users_to_add = [];
                $wrapper.find('.woffice-users-suggest_members-list li').each( function() {
                    users_to_add.push( $(this).data('id' ) );
                } );
                $wrapper.find('.woffice-users-suggest_members-ids').first().val('').val(users_to_add);

            } );
        };

        /**
         * Add the id of the member to an hidden input field
         *
         * @param e
         * @param ui
         */
        inst.add_member_to_list = function( e, ui ) {
            //Add the user to the visible list
            var $wrapper = $(e.target).closest('div');

            $wrapper.find('.woffice-users-suggest_members-list').first().append('<li data-id="' + ui.item.value + '"><a href="javascript:void(0)" class="woffice-users-suggest_remove-member"><i class="fa fa-times"></i></a> ' + ui.item.label + '</li>');

            //Add the id of the member to an hidden input
            var members_added = $wrapper.find('.woffice-users-suggest_members-ids').val(),
                members_excluded = $wrapper.data( 'members-excluded' );

            members_added = (!members_added.trim()) ? [] : members_added.split(',');
            //members_excluded = (!members_excluded.trim()) ? [] : members_excluded.split(',');

            members_added.push(parseInt(ui.item.value));
            //members_excluded.push(parseInt(ui.item.value));

            $wrapper.find('.woffice-users-suggest_members-ids').first().val( members_added );
            //$wrapper.attr( 'data-members-excluded', members_excluded);
        };

        inst.init();
    };

    /*
     * The to-do JS here
     */
    // THE CHECKBOX ACTIONS
    $('.woffice-task header label input').each(function(){
    	var Checkbox = $(this);
	    if (Checkbox.is(':checked')) {
		    Checkbox.closest('.woffice-task').addClass('is-done');
	    }

	});

	// THE NOTE TOGGLE
	$(".woffice-task .todo-note").hide();
	$("#woffice-project-todo").on('click', '.woffice-task header i.fa.fa-file-text-o', function(){
		var Task = $(this).closest('.woffice-task');
	    Task.find('.todo-note').slideToggle();
	    Task.toggleClass('unfolded');
	});

    // NAVAIGATION ACTIVE CLASS
    $('#project-nav ul').on('click', 'li', function(){
	    $('#project-nav ul li').removeClass('active');
	    $(this).addClass('active');

		$("#right-sidebar").mCustomScrollbar("update");

	});

	//DATEPICKER :
    $.datetimepicker.setDateFormatter({
        parseDate: function (date, format) {
            var d = moment(date, format);
            return d.isValid() ? d.toDate() : false;
        },
        
        formatDate: function (date, format) {
            return moment(date).format(format);
        },
    });
	$('.row .datepicker').datetimepicker(WOFFICE.datepicker_options);

	// CHANGE BUDDYPRESS LINK EFFECT
	$( "#project-nav .item-list-tabs #project-tab-delete a").unbind( "click" );

	/*
	 * Fire on events
	 */
    $(document).ready( function() {
        $.wofficeMembersAutoompleteWatcher();
    });

})(jQuery);
