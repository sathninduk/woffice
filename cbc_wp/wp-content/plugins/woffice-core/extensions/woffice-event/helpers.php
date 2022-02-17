<?php if ( ! defined( 'FW' ) ) {
    die( 'Forbidden' );
}

if(!function_exists('woffice_is_user_visible_event')) {
    /**
     * Check if current user has access into the event
     *
     * @param string $visibility
     *
     * @return mixed
     */
    function woffice_is_user_visible_event($visibility) {
        $ext_instance = fw()->extensions->get('woffice-event');
        $arr_visibility = explode('_', $visibility);
        $post_id = isset($arr_visibility[1]) ? $arr_visibility[1] : get_the_ID();
        
        return $ext_instance->user_authorize($post_id, $arr_visibility[0]);
    }
    
}
