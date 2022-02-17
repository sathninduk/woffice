<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Class FW_Extension_Woffice_Time_Tracking
 *
 * Unyson extension to create a simple time Tracker
 *
 * @since 2.4.0
 * @license GPL
 */
class FW_Extension_Woffice_Time_Tracking extends FW_Extension {

    /**
     * User ID
     *
     * @var int
     */
    protected $user_id = 0;

    /**
     * User Meta name
     *
     * Composed of an array of dates, containing tracks:
     * array(
     *      'DD-MM-YYYY' => array(
     *          array('timestamp', 'action', 'meta'),
     *          ...
     *      ...
     *
     * @var string
     */
    protected $field = 'woffice_time_tracking';

    /**
     * FW_Extension_Woffice_Time_Tracking constructor.
     */
    public function _init()
    {
    	add_action('admin_init', array($this, 'adminRequests'));

        add_action('fw_extension_settings_form_render:woffice-time-tracking', array($this, 'customButtons'));

        add_action( 'wp_ajax_woffice_time_tracking', array($this, 'ajaxCallback') );
        add_action( 'wp_ajax_nopriv_woffice_time_tracking', array($this, 'ajaxCallback') );

        add_action( 'wp_enqueue_scripts', array($this, 'addScript') );

        add_action( 'woffice_main_container_end', array($this, 'addModel'));
    }

	/**
	 * Add a model to our footer
	 */
    public function addModel()
    {
    	?>
	    <div class="woffice-modal modal fade" id="woffice-time-tracking-meta">
		    <div class="modal-dialog" role="document">
			    <div class="modal-content">
				    <div class="modal-body">
					    <div class="form-group">
						    <h3>
							    <?php _e('What are you working on?', 'woffice'); ?>

						    </h3>
						    <input type="text" class="form-control" name="woffice-time-tracking-meta">
					    </div>
				    </div>
				    <div class="modal-footer">
					    <button type="button" class="btn btn-danger btn-secondary mr-2" data-dismiss="modal">
						    <?php _e('Close', 'woffice'); ?>
					    </button>
					    <a href="#"
					            data-action="start"
					            class="btn btn-default woffice-time-tracking-state-toggle">
						    <i class="fa fa-play"></i> <?php _e('Go!', 'woffice'); ?>
					    </a>
				    </div>
			    </div>
		    </div>
	    </div>
		<?php
    }

    /**
     * We enqueue our script
     */
    public function addScript() {

        if (!defined('WOFFICE_THEME_VERSION')) {
            return;
        }

        wp_enqueue_script( 'woffice-time-tracking-js', woffice_get_extension_uri('time-tracking', 'static/js/woffice-time-tracking.js'), array(), WOFFICE_THEME_VERSION);

        wp_localize_script( 'woffice-time-tracking-js', 'WOFFICE_TIME_TRACKING', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'time_tracking_nonce' => wp_create_nonce('woffice_time_tracking_nonce'),
            'text_start' => __('Start', 'woffice'),
            'text_stop' => __('Stop', 'woffice')
        ) );
    }

    /**
     * Function called whenever the user start / end his time tracking
     */
    public function ajaxCallback () {

        $action  = (isset($_POST['tracking_action'])) ? $_POST['tracking_action'] : '';
        $meta    = (isset($_POST['tracking_meta'])) ? $_POST['tracking_meta'] : '';
        $user_id = get_current_user_id();

        // Any issue we end the process here
        if (empty($user_id) || empty($action) || !check_ajax_referer('woffice_time_tracking_nonce')) {
            wp_die();
        }

        $this->user_id = $user_id;

        $track = array(
            'action'    => $action,
            'timestamp' => time(),
            'meta'      => $meta
        );

        // We get our saved data and format it
        $saved_log  = get_user_option($this->field, $this->user_id);
        $new_log    = (!empty($saved_log)) ? $saved_log : array();
        $key_date   = woffice_get_formatted_tracking_key_date(time());

        // If no track in the daily log
        if (!isset($new_log[$key_date]) || !is_array($new_log[$key_date])) {
            $new_log[$key_date] = array();
        }

        array_push($new_log[$key_date], $track);

        $returned = update_user_meta($this->user_id, $this->field, $new_log);

        echo json_encode($returned);

        wp_die();
    }

    /**
     * Get the user's work history
     *
     * @param $user_id int
     * @param $nbr int
     * @return array
     */
    public function getLog($user_id = 0, $nbr = 10)
    {
        if (!$user_id)
            return array();

        $saved_log = get_user_option($this->field, $user_id);
        $saved_log = (!is_array($saved_log)) ? array() : $saved_log;

        $log = array();

        foreach ($saved_log as $date=>$tracks) {
        	$meta = array();
        	foreach ($tracks as $track) {
				if (!isset($track['meta']) || empty($track['meta'])) {
					continue;
				}

		        $meta[] = $track['meta'];
	        }

            $log[$date] = array(
            	'total'  => woffice_get_tracking_total($tracks),
	            'meta'   => implode(', ', $meta),
	            'tracks' => $tracks
            );
        }

        // We returns the last $nbr entries
        $log_returned = array_slice($log, 0, $nbr);

        /**
         * Filter `woffice_time_tracking_log`
         *
         * @param $log_returned array - The user's log once sliced
         * @param $full_log array - The user's full log
         * @param $user_id int - The user's ID
         */
        return apply_filters('woffice_time_tracking_log', $log_returned, $saved_log, $user_id);
    }

    /**
     * Handles admin actions, like the $_GET sent by our custom buttons
     */
    public function adminRequests() {

        if (!isset($_GET['extension']) || $_GET['extension'] != 'woffice-time-tracking')
            return;

        if(isset($_REQUEST['flush']) && $_REQUEST['flush'] == true) {
            $this->flush();
        }

        if(isset($_REQUEST['export']) && $_REQUEST['export'] == true) {
            $this->export();
        }
    }

    /**
     * Export to a CSV file the log
     */
    public function export() {

        // Create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        // Let's get our header result
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');

        // File header
        fputcsv($output, array(__('Time Tracking','woffice') . ' - '. date(get_option('date_format'))));

        // We get our users
        $users = get_users(array('fields' => array('ID', 'user_login')));

        // We delete the meta
        foreach ($users as $user) {
            $log = $this->getLog($user->ID);

            fputcsv($output, array('#'. $user->ID .' '. $user->user_login));

            foreach ($log as $day=>$item) {
                fputcsv($output, array($day, $item['total'], $item['meta']));
                fputcsv($output, array(__('Tracks:','woffice'). ' '. $day));
                foreach ($item['tracks'] as $track) {
	                fputcsv( $output, array($track['action'], date('m/d/Y H:i:s', $track['timestamp']), $track['meta']));
                }
            }
        }

        // Close the channel
        fclose($output);

        exit();
    }

    /**
     * Flush all the work logs for all users
     */
    public function flush() {

        // We get our users
        $users = get_users(array('fields' => array('id', 'user_login')));

        // We delete the meta
        foreach ($users as $user) {
            delete_user_meta($user->ID, $this->field);
        }

        wp_redirect(admin_url('admin.php?page=fw-extensions&sub-page=extension&extension=woffice-time-tracking'));
        die();
    }

    /**
     * Add a Reset and Export button to our extension's settings
     */
    public function customButtons() {

        // Flush
        echo fw_html_tag('a', array(
            'href'  => admin_url('admin.php?page=fw-extensions&sub-page=extension&extension=woffice-time-tracking&flush=true'),
            'class' => 'button-secondary',
            'style' => 'margin-bottom: 20px;',
            'onclick' => 'return confirm("'.__('Are you sure you want to delete all the current data, make sure to export it before?', 'woffice').'");'
        ), __('Empty all tracking logs', 'woffice'));

        // Export
        echo fw_html_tag('a', array(
            'href'  => admin_url('admin.php?page=fw-extensions&sub-page=extension&extension=woffice-time-tracking&export=true'),
            'class' => 'button-secondary',
            'style' => 'margin-bottom: 20px;margin-left:10px;',
        ), __('Export Tracking Log', 'woffice'));
    }


}