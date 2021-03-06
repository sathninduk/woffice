<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Output the JS and HTML markup
 *
 * @return void
 */
function woffice_cover_js_html_upload() {
	if (!woffice_bp_is_active( 'xprofile' )) {
		return;
	}

	// We display it only if it's the user on his own profile
	if (!bp_is_my_profile() && !(bp_is_user() && woffice_current_is_admin())) {
		return;
	}
			
	$user_id = bp_displayed_user_id();
			
	/* HTML FROM THE JS */
	echo '<div id="woffice-cover-process">';

		echo'<div id="woffice-coverprogressOuter" class="progress active" style="display:none;">';
			echo'<div id="woffice-cover-progressBar" class="progress-bar progress-bar-striped bg-success progress-bar-animated"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0;"></div>';
		echo'</div>';

		echo'<div id="woffice-cover-message"></div>';

	echo '</div>';
			
	/* JS  NOW */
	echo"<script>
	jQuery(document).ready( function() {
						
		window.onload = function() {
			
		  var btn = document.getElementById('woffice_cover_upload'),
		      progressBar = document.getElementById('woffice-cover-progressBar'),
		      progressOuter = document.getElementById('woffice-coverprogressOuter'),
		      msgBox = document.getElementById('woffice-cover-message');
		      
		  function show_msgBox() {
		      var msgBoxContainer = jQuery('#woffice-cover-process');
			  msgBoxContainer.fadeIn();
			  setTimeout(function () {
	             msgBoxContainer.fadeOut('fast');
			  }, 2000);
		  }
		      
		  new ss.SimpleUpload({
		        button: btn,
		        url: '".get_site_url()."/wp-admin/admin-ajax.php',
		        name: 'woffice_cover_image',
		        hoverClass: 'hover',
		        focusClass: 'focus',
		        allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
		        responseType: 'json',
		        multipart: true,
		        debug: false,
		        data: {'action':'wofficeAjaxCoverUpload'},
		        startXHR: function() {
		            progressOuter.style.display = 'flex'; 
		            this.setProgressBar( progressBar );
		        },
		        onSubmit: function() {
		            msgBox.innerHTML = ''; 
		            btn.innerHTML = '". __("Uploading...", "woffice") ."'; 
		            jQuery('#woffice-cover-process').fadeIn();
		        },
		        onComplete: function(filename, response) {
		            btn.innerHTML = '<i class=\"fa fa-pencil-alt\"></a>';
		            progressOuter.style.display = 'none'; 
		            if (!response) {
		                msgBox.innerHTML = '". __("Unable to upload file...", "woffice") ."';
		                return;
		            }
		            if (response.success === true) {
		                msgBox.innerHTML = '<i class=\"fa fa-check-circle\"></i> ". __("Image uploaded.", "woffice") ."';
		                jQuery('.featured-background').css('background-image', 'url(' + response.file + ')');
						jQuery('#woffice_cover_upload').fadeOut();
						jQuery('#woffice_cover_delete').fadeIn();
		            } else {
		                if (response.msg)  {
		                    msgBox.innerHTML = response.msg;
		                } else {
		                    msgBox.innerHTML = '". __("An error occurred and the upload failed.", "woffice") ."';
		                }
		            }
		            show_msgBox();
		          },
		        onError: function() {
		            progressOuter.style.display = 'none';
		            msgBox.innerHTML = '". __("Unable to upload file.", "woffice") ."';
		            show_msgBox();
		        }
			});
			
			// Delete option here : 
			jQuery('#woffice_cover_delete').click(function(){
		
				jQuery.ajax({
					url: '".get_site_url()."/wp-admin/admin-ajax.php', 
					type: 'POST',
					data: { 'action': 'wofficeAjaxCoverDelete', 'nonce': WOFFICE.nonce, 'user': '".$user_id."' },
					success: function(message){
						console.log('Cover was deleted : '+ message);
						jQuery('#woffice_cover_delete').fadeOut();
						jQuery('#woffice_cover_upload').fadeIn();
						jQuery('.featured-background').css('background-image', 'none');
			            msgBox.innerHTML = '<i class=\"fa fa-check-circle\"></i> ". __("Image Deleted", "woffice") ."';
						show_msgBox();
					}   
				});
		
			});
		
		};
		
	});
	
	</script>";
}
add_action('wp_footer', 'woffice_cover_js_html_upload');

/**
 * AJAX handling for the upload process
 *
 * @return void
 */
function woffice_ajax_cover_delete_handler(){

	if ( !wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
		die( __('Sorry! Direct Access is not allowed.', "woffice"));
	}
	
	$user_id = intval($_POST['user']);

	// We delete the file
	$old_cover_url = bp_get_profile_field_data(array('field' => 'woffice_cover', 'user_id' => $user_id));

	if ($old_cover_url) {
		$old_cover_path = explode( 'wp-content/uploads', $old_cover_url);

		if (count($old_cover_path) > 0) {
			$wp_upload_dir = wp_upload_dir();
			$old_cover_path = $wp_upload_dir['basedir'] . $old_cover_path[1];

			@unlink($old_cover_path);

		}
	}

	// We eventually delete the image managed by BuddyPress
	bp_attachments_delete_file( array( 'item_id' => $user_id) );

	// We clean the Buddypress settings it
	xprofile_delete_field_data( 'woffice_cover', $user_id);
	
	echo 'Success for '.$user_id;
	
	exit();
}
add_action('wp_ajax_nopriv_wofficeAjaxCoverDelete', 'woffice_ajax_cover_delete_handler');
add_action('wp_ajax_wofficeAjaxCoverDelete', 'woffice_ajax_cover_delete_handler');

/**
 * AJAX handling for the upload process
 *
 * @return void
 */
function woffice_ajax_cover_upload_handler(){

	if ( !wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
		die( __('Sorry! Direct Access is not allowed.', "woffice"));
	}

	if (!(is_array($_FILES) && defined('DOING_AJAX') && DOING_AJAX)){
        return;
    }
    
    // We grab the file : 
    $file = $_FILES['woffice_cover_image'];
	
	// We include the library 
	if (!function_exists('wp_handle_upload'))
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        
    // Register our path override.
	add_filter( 'upload_dir', 'woffice_cover_upload_dir' );

	$upload_overrides = array( 'test_form' => false );
	$new_cover = wp_handle_upload( $file, $upload_overrides );
	
	// Set everything back to normal.
	remove_filter( 'upload_dir', 'woffice_cover_upload_dir' );
	
	if ($new_cover && !isset( $new_cover['error'])) {
		
		$url = $new_cover['url'];
		$user_id = bp_displayed_user_id();

		/* We save to the Buddypress settings it */
		xprofile_set_field_data( 'woffice_cover', $user_id, $url);

		$cover_image = wp_get_image_editor( $new_cover['file'] );
		if (!is_wp_error($cover_image)) {
			/**
			 * Filter to crop automatically the cover image on upload
			 *
			 * @param boolean
			 */
			$has_cropping = apply_filters('woffice_cover_auto_crop', false);
			if ($has_cropping)
				$cover_image->resize( 1500, 500, true );
			/**
			 * Filter to set the cover quality on upload
			 * 
			 * @param int
			 */
			$quality = apply_filters('woffice_cover_quality', 40);
			$cover_image->set_quality( $quality );
			$cover_image->save( $new_cover['file'] );
		}
		
		echo json_encode(array('success' => true, 'file' => $new_cover['url'])); 
	}
	else {
		echo json_encode(array('success' => false, 'msg' => $file['name'])); 
	}
	
	wp_die();
}
add_action('wp_ajax_nopriv_wofficeAjaxCoverUpload', 'woffice_ajax_cover_upload_handler');
add_action('wp_ajax_wofficeAjaxCoverUpload', 'woffice_ajax_cover_upload_handler');


/**
 * CREATE FUNCTIONS TO ADD THE COVER FIELD TO XPROFILE
 */
function woffice_cover_add_field() {
	if (!woffice_bp_is_active('xprofile')) {
		return;
	}
		
	global $wpdb;

	$table_name = woffice_get_xprofile_table();
	$sqlStr = "SELECT * FROM ".$table_name." WHERE name = 'CoverOptions'; ";
    $groups = $wpdb->get_results($sqlStr);

    if(count($groups) > 0)
        return;

    /*
	 * Cover FIELD
	 */
    xprofile_insert_field(
        array (
            'field_group_id'  => 'woffice_cover_options',
			'can_delete' => true,
			'type' => 'textbox',
			'description' => __('URL to the cover image, you can upload the image from your profile page on the top right corner.','woffice'),
			'name' => 'Woffice_Cover',
			'field_order'     => 1,
			'is_required'     => false,
        )
    );
	 
}		
add_action('fw_settings_form_saved', 'woffice_cover_add_field');


/**
 * We hide the group from the edit fields
 *
 * @param array $args
 * @return  array
 */
function woffice_cover_hide_profile_group( $args ) {
  	$exclude_groups = 'woffice_cover_options';

	if (!empty($exclude_groups)) {
		$args['exclude_groups'] = $exclude_groups;
	}

	return $args;
}
add_filter('bp_after_has_profile_parse_args', 'woffice_cover_hide_profile_group');
	
