<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Extension_Woffice_Birthdays extends FW_Extension {

	/**
	 * @internal
	 */
	public function _init() {
		add_action('fw_extensions_before_deactivation', array($this, 'woffice_birthdays_delete_field'));
		add_action('fw_settings_form_saved', array($this, 'woffice_birthdays_add_field'));
	}

	/**
	 * This function returns an array of the members birthdays :
     * Only today and upcoming birthdays sorted in ascending order
     *
	 * array(
	 * 	  id_member => array('datetime' => DateTime Object);
	 * )
	 */
	public function woffice_birthdays_get_array() {

		$woffice_wp_users = get_users(array('fields' => array('ID')));
		$members_birthdays = array();

		// Get the Birthday field name
		$field_name = $this->woffice_birthdays_field_name();
		$field_name = str_replace( "'", "\'", $field_name);

		// Get the Birthday field ID
		$field_id = xprofile_get_field_id_from_name( $field_name );

		// Set all data for the date limit check
        $birthdays_limit = $this->woffice_birthdays_range_limit();
        $max_date = $this->woffice_birthday_range_limit_date($birthdays_limit);

		// We check if the member has a birthday set
		foreach ($woffice_wp_users as $woffice_wp_user) {

            $birthday_string = maybe_unserialize(BP_XProfile_ProfileData::get_value_byid( $field_id, $woffice_wp_user->ID));

			if (empty($birthday_string)) {
				continue;
			}

			// We transform the string in a date
			$birthday = DateTime::createFromFormat('Y-m-d H:i:s', $birthday_string);

			/**
			 * Filter if the current birthday (in the birthdays widget) can be displayed
			 *
			 * @param bool $is_displayed
			 * @param int $user_id
			 * @param DateTime $birthday
			 */
			$display_this_birthday = apply_filters('woffice_display_this_birthday', true, $woffice_wp_user->ID, $birthday);

			if ($birthday !== false && $display_this_birthday) {

                // Skip if birth date is not in the selected limit range
                if (!$this->woffice_birthday_is_in_range_limit($birthday, $max_date)) {
                    continue;
                }

				$celebration_year = (date('md', $birthday->getTimestamp()) >= date('md') ) ? date('Y') : date('Y', strtotime('+1 years') );

			    $years_old = (int) $celebration_year - (int)date("Y", $birthday->getTimestamp());

			    // If gone for this year already, we remove one year
			    if (date('md', $birthday->getTimestamp()) > date('md')) {
					$years_old = $years_old;
				} else if (date('md', $birthday->getTimestamp()) == date('md')) {
				    $years_old = $years_old;
			    }

				/**
				 * Filter `woffice_birthdays_date_format`
				 *
				 * Let you change the date format in which the birthday is displayed
				 * See: http://php.net/manual/en/function.date.php
				 *
				 * @param string - the date format PHP value
				 *
				 * @return string
				 */
				$format = apply_filters('woffice_birthdays_date_format', 'md');

			    $celebration_string = $celebration_year . date($format, $birthday->getTimestamp());

				$members_birthdays[$woffice_wp_user->ID] = array(
                    'datetime' => $birthday,
					'next_celebration_comparable_string' => $celebration_string,
					'years_old' => $years_old
				);
			}

		}

        uasort($members_birthdays, array($this, "date_comparison"));

		return $members_birthdays;
	}

    /**
     * Get the max date(dayMonth) we will show for the birthday
     *
     * @param string $birthdays_limit
     *
     * @return string
     */
    public function woffice_birthday_range_limit_date($birthdays_limit) {

        if ($birthdays_limit == 'monthly') {
            $int_date_time = strtotime('+30 day', time());
            return date('md', $int_date_time);
        }

        if ($birthdays_limit == 'weekly') {
            $int_date_time = strtotime('+7 day', time());
            return date('md', $int_date_time);
        }
        return 'all';
    }

    /**
     * Check if given birth day is within the range
     *
     * @param string $birth_date
     * @param string $max_date
     *
     * @return boolean
     */
    public function woffice_birthday_is_in_range_limit($birth_date, $max_date) {
        if ($max_date == 'all') {
            return true;
        }

        $target_date = date('md', $birth_date->getTimestamp());
        $now_date = date('md');

        return $max_date >= $target_date && $target_date >= $now_date;
    }


	/**
	 * Custom function to search in our array in the function below (from http://stackoverflow.com/questions/4128323/in-array-and-multidimensional-array)
	 */
	public function woffice_in_multiarray($value, $array) {
		if(in_array($value, $array)) {
		  return true;
		}
		foreach($array as $item) {
		  if(is_array($item) && $this->woffice_in_multiarray($value, $item))
		       return true;
		}
		return false;
	}

	/**
	 * It will generate the title for the front view
	 *
	 * @param array $all_bithdays
	 *
	 * @return string
	 */
	public function woffice_birthdays_title($all_bithdays) {

		if (!empty($all_bithdays)){
			$widget_title =  '<h3>'. __('Upcoming Birthdays','woffice') .'</h3>';
		}
		else {
			$widget_title = '<h3>'. __('No birthdays found','woffice') .'</h3>';
		}

		/**
		 * Filter the title of the Birthdays widget
		 *
		 * @param string $widget_title The title of the widget
		 * @param array $all_bithdays the array of all birthdays
		 */
		return apply_filters('woffice_birthdays_widget_title', $widget_title, $all_bithdays);
	}

	/**
	 * It will generate the content for the front viw
	 *
	 * @param array $all_bithdays
	 */
	public function woffice_birthdays_content($all_bithdays) {

		if (empty($all_bithdays)) {
			return;
		}

        $max_items = $this->woffice_birthdays_to_display();
        $c = 0;

		$date_ymd = date('Ymd');

        foreach($all_bithdays as $user_id => $birthday) {
            if ($c === $max_items)
                break;

            $activation_key = get_user_meta($user_id, 'activation_key');
            if (empty($activation_key)) {
                $name_to_display = woffice_get_name_to_display($user_id);

				$age = $birthday["years_old"];

				// We don't display negative ages
				if($age > 0) {
					echo '<li class="clearfix">';
					if (function_exists('bp_is_active')):
						echo '<a href="' . bp_core_get_user_domain($user_id) . '">';
						echo get_avatar($user_id);
						echo '</a>';
					else :
						echo get_avatar($user_id);
					endif;
					echo '<span class="birthday-item-content">';
					echo '<strong>' . $name_to_display . '</strong>';
					if ($this->woffice_birthdays_display_age() != 'nope') {
						echo ' <i>(' . $age . ')</i>';
					}
					echo ' ', _x('on', 'happy birthday ON 25-06', 'woffice');
					$date_format = $this->woffice_birthdays_date_format();
					$date_format = (!empty($date_format)) ? $date_format : 'F d';

					if ( 'F d' === $date_format ) {
						//To make months translatable
						echo ' <span class="badge badge-primary badge-pill">';
						_e(date('F', $birthday["datetime"]->getTimestamp()), 'woffice');
						echo ' ';
						echo date('d', $birthday["datetime"]->getTimestamp());
						echo '</span>';
					} else {
						echo ' <span class="badge badge-primary badge-pill">' . date_i18n( $date_format, $birthday["datetime"]->getTimestamp() ) . '</span>';
					}

					$happy_birthday_label = '';
					if($birthday["next_celebration_comparable_string"] == $date_ymd)
						$happy_birthday_label = '<span class="badge badge-primary badge-pill">' . __( 'Happy Birthday!', 'woffice') . '</span>';

					/**
					 * The label "Happy birthday", if today is the birthday of an user
					 *
					 * @param string $happy_birthday_label The text of the label (contains some HTML)
					 * @param int $user_id
					 */
					echo apply_filters( 'woffice_today_happy_birthday_label', $happy_birthday_label, $user_id );

					echo '</span>';
					echo '</li>';

                    $c++;
				}
            }

        }
	}

	/**
	 *  CREATE FUNCTIONS TO ADD THE BIRTHDAY FIELD TO XPROFILE
	 */
	public function woffice_birthdays_add_field() {

		if (!woffice_bp_is_active('xprofile'))
			return;

		// Get the Birthday field name
		$field_name = $this->woffice_birthdays_field_name();
		$field_name = str_replace( "'", "\'", $field_name);

		// Get the Birthday field ID
		$field_id = xprofile_get_field_id_from_name( $field_name );

		// If the field already exists, don't create it again
	    if ( !is_null($field_id) )
	        return;

		$insert_field = xprofile_insert_field(
	        array (
	            'field_group_id'  => 1,
				'type' => 'datebox',
				'name' => $field_name,
	        )
	    );
	}

	/**
	 * DELETE THE BIRTHDAY FIELD IN XPROFILE
	 *
	 * @param string $extensions
	 */
	public function woffice_birthdays_delete_field($extensions) {
		if (!isset($extensions['woffice-birthdays'])) {
	        return;
	    }

		$field_name = $this->woffice_birthdays_field_name();

		$id = xprofile_get_field_id_from_name($field_name);
		xprofile_delete_field($id);
	}

	/**
	 * CREATE FUNCTIONS TO GET THE OPTION FROM THE SETTINGS
	 *
	 * @return string - yes or nope
	 */
	public function woffice_birthdays_display_age() {
		return fw_get_db_ext_settings_option( $this->get_name(), 'display_age' );
	}

    /**
     * Get birthdays range limit
     *
     * possible values:
     * - no_limit
     * - weekly
     * - monthly
     *
     * @return string
     */
    public function woffice_birthdays_range_limit() {
        return fw_get_db_ext_settings_option( $this->get_name(), 'birthdays_range_limit' );
    }

	/**
	 * Get date format of birthday set in Birthday extension options
	 *
	 * @return string
	 */
	public function woffice_birthdays_date_format() {
		return fw_get_db_ext_settings_option( $this->get_name(), 'birthday_date_format' );
	}

    /**
     * Get the field's name
     *
     * @return string
     */
    public function woffice_birthdays_field_name() {
        return fw_get_db_ext_settings_option( $this->get_name(), 'birthday_field_name' );
    }

    /**
     * Get the max number of the item to display
     *
     * @return string
     */
    public function woffice_birthdays_to_display() {
        return (int) fw_get_db_ext_settings_option( $this->get_name(), 'birthdays_to_display' );
    }

    /**
     * Used for order array of object, containing dates
     *
     * @param array $a
     * @param array $b
     *
     * @return boolean
     */
    private function date_comparison($a, $b) {
        return strcmp($a['next_celebration_comparable_string'] , $b['next_celebration_comparable_string']);
    }
}
