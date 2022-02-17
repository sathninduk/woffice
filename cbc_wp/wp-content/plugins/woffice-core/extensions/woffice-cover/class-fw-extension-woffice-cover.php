<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Extension_Woffice_Cover extends FW_Extension {

	/**
	 * @internal
	 */
	public function _init() {
		add_filter( 'bp_is_profile_cover_image_active', '__return_false' );
	}

	/**
	 * We check if the user has a cover image, if yes we return the URL of the image
	 *
	 * @param int $user_ID
	 *
	 * @return bool
	 */
	public function woffice_cover_member_state($user_ID) {

		return (bool)woffice_get_cover_image( $user_ID );
	}
	
}