<?php
/**
 * Backend Log
 */

$users = get_users(array('fields' => array('id', 'user_login')));

$ext_instance = fw()->extensions->get( 'woffice-time-tracking' );

$user_id = (isset($_REQUEST['user']) && !empty($_REQUEST['user'])) ? $_REQUEST['user'] : false;

$log = array();

if($user_id) {
    $log = $ext_instance->getLog($user_id);
}

?>

    <div id="woffice-log-user-tracker" style="width: 400px;">

        <select name="woffice-log-user-tracked" id="woffice-log-user-tracked">
            <option value="--">--</option>
            <?php foreach ($users as $user) : ?>
                <option value="<?php echo $user->ID; ?>" <?php echo ($user_id == $user->ID) ? 'selected' : ''; ?>><?php echo $user->user_login; ?></option>
            <?php endforeach; ?>
        </select>
        <script type="text/javascript">
            jQuery('#woffice-log-user-tracked').on('change', function () {
                window.location.href = window.location.protocol + "//" + window.location.host + window.location.pathname + window.location.search + '&user='+jQuery('#woffice-log-user-tracked').val();
            });
        </script>

        <hr>

        <div id="woffice-log-user-results">
            <?php foreach ($log as $day=>$item) : ?>
                <div class="woffice-time-tracking-day">
                    <span class="highlight">
                        <?php echo $day; ?>
                    </span>
                    <span>
                        <?php _e('Total time record:', 'woffice'); ?> <b><?php echo $item['total']; ?></b>
                        &mdash;
	                    <?php _e('Meta summary:', 'woffice'); ?> <b><?php echo $item['meta']; ?></b>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

<?php
