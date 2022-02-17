<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Returns the Button Upload for the cover
 *
 * @return string
 */
function woffice_upload_cover_btn() {
	
	$ext_instance = fw()->extensions->get( 'woffice-cover' );
	
	if( ! woffice_bp_is_active( 'xprofile' ) )
		return '';
		
	/* We display it only if it's the user on his own profile or if the current user is an admin*/
	if (!bp_is_my_profile() && !woffice_current_is_admin())
		return '';
			
	$displayed_user_ID = bp_displayed_user_id();

	$extra_style = " style='display:none;'";

	if (!$ext_instance->woffice_cover_member_state($displayed_user_ID)){
		echo'<button id="woffice_cover_upload" class="btn-cover-upload"><i class="fa fa-camera"></i></button>';
		echo'<button id="woffice_cover_delete" class="btn-cover-upload" '.$extra_style.'><i class="fa fa-times"></i></button>';
	}
	else {
		echo'<button id="woffice_cover_upload" class="btn-cover-upload" '.$extra_style.'><i class="fa fa-camera"></i></button>';
		echo'<button id="woffice_cover_delete" class="btn-cover-upload"><i class="fa fa-times"></i></button>';
	}
	
}

/**
 * Custom Directory for the upload
 *
 * @return array
 */
function woffice_cover_upload_dir($upload) {
	
	$upload['subdir'] = '/woffice-covers' . $upload['subdir'];
	$upload['path']   = $upload['basedir'] . $upload['subdir'];
	$upload['url']    = $upload['baseurl'] . $upload['subdir'];

	return $upload;

}

