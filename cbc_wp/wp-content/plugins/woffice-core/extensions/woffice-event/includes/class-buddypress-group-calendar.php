<?php
if (class_exists('BP_Group_Extension')) :

    class Group_Extension_Calendar extends BP_Group_Extension
    {
        /**
         * Group page calendar extension registration
         */
        function __construct()
        {
            if (!fw_ext('woffice-event')) {
                return false;
            }

            $args = array(
                'slug'              => 'group-calendar',
                'name'              => __('Calendar', 'woffice'),
                'nav_item_position' => 106,
            );

	        /**
	         * Let the user overrides the Events group args
	         *
	         * @return array $args
	         */
	        $args = apply_filters('woffice_events_group_init_args', $args);

            parent::init($args);
        }

        /**
         * Content for the extension
         *
         * @param int $group_id
         */
        function display($group_id = null)
        {
            $group_id = bp_get_group_id();
            echo do_shortcode('[woffice_calendar visibility="group" id="' . $group_id . '"]');
        }

    }

    bp_register_group_extension('Group_Extension_Calendar');

endif;
