<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Main class of the Woffice Auto Friends extension
 *
 */
 
class FW_Extension_Woffice_Auto_Friends extends FW_Extension {

	/**
	 * Setup the extension
	 *
	 * @internal
	 */
	public function _init()
	{
		if (!woffice_bp_is_active('friends')) {
			return;
		}

		add_action('user_register',                 array($this, 'run'));
		add_action('admin_init',                    array($this, 'adminRun'));
		add_action('bp_activity_before_save',       array($this, 'removeActivity'), 10, 1);
	}

	/**
	 * Remove the BP activity items
     *
     * @param Object $activity_object
	 */
	public function removeActivity($activity_object)
    {
	    $exclude = array('friendship_created', 'friendship_accepted');

	    if (in_array($activity_object->type, $exclude)) {
		    $activity_object->type = false;
	    }
    }

	/**
	 * Run the creation from the admin side.
	 */
	public function adminRun()
	{
		$is_enabled = (isset($_GET['extension']) && $_GET['extension'] === 'woffice-auto-friends' && isset($_GET['run']) && $_GET['run'] === 'true');

		if (!$is_enabled) {
			return;
		}

		$count = $this->run(null, true);

		?>

		<div class="notice notice-info is-dismissible">
			<p><?php echo sprintf(__('The creation process is now done 🙌 %d relationships were created.','woffice'), $count); ?></p>
		</div>

		<?php
	}

	/**
	 * Create the relationships
	 *
	 * @param  int|null   $initiator_id - user ID
	 * @param  bool       $from_admin - Whether it's from the admin button or not, if so overrides the setting
	 *
	 * @return int       Number of friendships created 🤝
	 */
	public function run($initiator_id = null, $from_admin = false)
	{

        /**
         * Check for the auto friend status
         * Do nothing if disabled
         */
        $auto_friend_status = fw_get_db_ext_settings_option( 'woffice-auto-friends', 'status' );
        if ($auto_friend_status === 'disable' && !$from_admin) {
            return;
        }

		$users = get_users(array('fields' => array('id')));

		/**
		 * Filter `woffice_auto_friends_pool`
		 *
		 * Let's you customize which users is included in the auto-friend creation process
		 *
		 * @param array $users
		 */
		$users = apply_filters('woffice_auto_friends_pool', $users);

		$counter = 0;

		if ($initiator_id) {
			return $this->createFriendships($initiator_id, $users);
		}

		foreach ($users as $user) {
			$initiator_id = $user->ID;

			$counter = $counter + $this->createFriendships($initiator_id, $users);
		}

		return $counter;
	}

	/**
	 * Create friendship relationships for a given user
	 *
	 * @param  int     $user_id
	 * @param  array   $users
	 *
	 * @return int
	 */
	private function createFriendships($user_id, $users)
	{
		$counter = 0;

		/**
         * Prevent sending mail notifications for auto friends
         * Turn off user request accept notifications setting
         * Then turn on again after auto friends complete
         * Remove action bp_friends_add_friendship_accepted_notification to remove app notification
         */
        $notification_setting = bp_get_user_meta($user_id, 'notification_friends_friendship_accepted', true );
        remove_action( 'friends_friendship_accepted', 'bp_friends_add_friendship_accepted_notification', 10 );

        if ($notification_setting === 'yes') {
            bp_update_user_meta($user_id, 'notification_friends_friendship_accepted', 'no');
        }

		foreach ($users as $user_2) {
			$second_id = $user_2->id;

			if ($user_id === $second_id) {
				continue;
			}

			if (friends_check_friendship($user_id, $second_id) === true) {
				continue;
			}

			$creation = friends_add_friend($user_id, $second_id, true);

			if ($creation === true) {
				friends_update_friend_totals($user_id, $second_id);
				$counter = $counter + 1;
			}
		}

        /**
         * Turn notification settings back if it was enabled
         */
        if ($notification_setting === 'yes') {
            bp_update_user_meta($user_id, 'notification_friends_friendship_accepted', 'yes');
        }

		return $counter;
	}


}