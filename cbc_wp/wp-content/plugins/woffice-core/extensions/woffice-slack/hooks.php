<?php if ( ! defined( 'FW' ) ) {
    die( 'Forbidden' );
}
/**
 * Create Slack notification for :
 * Comments
 * @param $comment_id
 * @param $comment_object
 */
function woffice_slack_hook_comments($comment_id, $comment_object) {

    $enabled = fw_get_db_ext_settings_option('woffice-slack', 'enable_comments');

    if($enabled == 'on') {

        $pretext = ':speech_balloon: '. __('New comment posted!','woffice');
        $link = (function_exists('bp_core_get_user_domain')) ? bp_core_get_user_domain($comment_object->user_id) : get_site_url();

        $notification = array(
            'title' => get_the_title($comment_object->comment_post_ID),
            'title_link' => get_the_permalink($comment_object->comment_post_ID),
            'pretext' => $pretext,
            'text' => $comment_object->comment_content,
            'author_name' => $comment_object->comment_author,
            'author_link' => $link,
            'author_icon' => get_avatar($comment_object->user_id, 16),
        );

        woffice_slack_send_notification($notification);

    }

}
add_action('wp_insert_comment', 'woffice_slack_hook_comments', 99, 2);

/**
 * Create Slack notification for :
 * Post Creations
 * @param $post_id
 * @param $post
 * @param $update
 */
function woffice_slack_hook_posts( $post_id, $post, $update) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if ( wp_is_post_revision( $post_id ) || $update == true )
        return;

    $enabled = fw_get_db_ext_settings_option('woffice-slack', 'enable_posts');

    if($enabled == 'on') {

        $link = (function_exists('bp_core_get_user_domain')) ? bp_core_get_user_domain($post->post_author) : get_site_url();

        $post_type_label = get_post_type_object($post->post_type)->labels->singular_name;

        $notification = array(
            'title' => $post->post_title,
            'title_link' => get_the_permalink($post_id),
            'pretext' => ':new: '. $post_type_label .' '.__('post created!','woffice'),
            'text' => $post->post_content,
            'author_name' => woffice_get_name_to_display($post->post_author),
            'author_link' => $link,
            'author_icon' => get_avatar($post->post_author, 16),
        );

        woffice_slack_send_notification($notification);

    }

}
add_action('wp_insert_post', 'woffice_slack_hook_posts', 99, 3);

/**
 * Create Slack notification for :
 * Projects Tasks
 * See : woffice-projects/hooks.php for details
 * @param $post_id
 * @param $task_details
 */
function woffice_slack_hook_tasks($post_id, $task_details) {

    $enabled = fw_get_db_ext_settings_option('woffice-slack', 'enable_tasks');

    if($enabled == 'on') {

        $fields = array();
        if(isset($task_details['assigned']) && !empty($task_details['assigned']) && $task_details['assigned'] != "nope") {
            $fields[] = array(
                'title' => ':+1: '.__('Assigned to','woffice'),
                'value' => woffice_get_name_to_display($task_details['assigned']),
                'short' => true
            );
        }
        if(isset($task_details['date']) && !empty($task_details['date'])) {
            $fields[] = array(
                'title' => ':clock1: '. __('Due on','woffice'),
                'value' => date_i18n(get_option('date_format'), strtotime($task_details['date'])),
                'short' => true
            );
        }

        $notification = array(
            'title' => $task_details['title'],
            'title_link' => get_the_permalink($post_id),
            'pretext' => ':pushpin:' . __('New task added in','woffice'). ' '. get_the_title($post_id),
            'text' => $task_details['note'],
            'fields' => $fields
        );

        woffice_slack_send_notification($notification);

    }

}
add_action('woffice_project_task_added', 'woffice_slack_hook_tasks', 10, 2);

/**
 * Create Slack notification for :
 * BuddyPress Activities
*  @param $activity Array of parsed arguments for the activity item being added.
 */
function woffice_slack_hook_activities($activity) {

    $enabled = fw_get_db_ext_settings_option('woffice-slack', 'enable_activities');

    if($enabled == 'on') {

        if(
            !woffice_bp_is_active('activity') ||
            $activity['hide_sitewide'] == true ||
            $activity['component'] == 'project' ||
            $activity['component'] == 'wiki'
        )
            return;

        $notification = array(
            'title' => $activity['content'],
            'title_link' => $activity['primary_link'],
            'pretext' => ':bell: '. __('New notification on','woffice').' '.$activity['component'],
            'author_name' => woffice_get_name_to_display($activity['user_id']),
            'author_link' =>  bp_core_get_user_domain($activity['user_id']),
            'author_icon' => get_avatar($activity['user_id'], 16),
        );

        woffice_slack_send_notification($notification);

    }

}
add_action('bp_activity_add', 'woffice_slack_hook_activities');

/**
 * Create Slack notification for :
 * Registrations
 * @param $user_id : new registered user ID
 */
function woffice_slack_hook_registrations($user_id) {

    $enabled = fw_get_db_ext_settings_option('woffice-slack', 'enable_registration');

    if($enabled == 'on') {

        $link = (function_exists('bp_core_get_user_domain')) ? bp_core_get_user_domain($user_id) : get_site_url();
        $title = __('Welcome to','woffice'). ' ' .woffice_get_name_to_display($user_id);

        $notification = array(
            'title' => $title,
            'title_link' => $link,
            'pretext' => ':bust_in_silhouette: '. __('New user registered!','woffice'),
        );

        woffice_slack_send_notification($notification);

    }

}
add_action('user_register', 'woffice_slack_hook_registrations');
