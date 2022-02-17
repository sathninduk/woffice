<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Extension_Woffice_Map extends FW_Extension {
	/**
	 * @internal
	 */
	public function _init() {
        add_action('xprofile_updated_profile', array($this, 'saveSingleMember'));
		add_action('fw_extension_settings_form_saved:woffice-map', array($this, 'mapSaveApi'));
		add_action('fw_extensions_before_deactivation', array($this, 'mapDeleteField'));
        add_action('fw_extension_settings_form_render:woffice-map', array($this, 'addRefreshButtons'));
	}

    /**
     * Returns the member's lat / lng and name if an address is set
     *
     * @param int $user_id
     * @return array - can be empty
     */
	private function getMemberCoordinates($user_id) {

	    $user_data = array();

        if (!woffice_bp_is_active('xprofile'))
            return $user_data;

        $field_name = $this->mapFieldName();

        $address = xprofile_get_field_data($field_name, $user_id);

        if (empty($address))
            return $user_data;

        $location = urlencode($address);
        $json_decoded = $this->apiRequest($location);

        if (empty($json_decoded) || $json_decoded['status'] !== "OK")
            return $user_data;

        return array(
            'name'      => $json_decoded['results'][0]['formatted_address'],
            'lat'       => $json_decoded['results'][0]['geometry']['location']['lat'],
            'long'      => $json_decoded['results'][0]['geometry']['location']['lng'],
            'user_id'   => $user_id
        );

    }

    /**
     * Save all members locations
     * This can be expensive if there are many users
     */
    public function saveAllMembers() {

        $users = get_users(array('fields' => array('ID')));
        $users_coordinates = array();

        foreach ($users as $user) {
            $user_coordinates = $this->getMemberCoordinates($user->ID);
            if (empty($user_coordinates))
                continue;
            array_push($users_coordinates, $user_coordinates);
        }

        update_option('woffice_map_locations', json_encode($users_coordinates));

    }

    /**
     * Save a single member's position
     *
     * @param int $user_id
     */
    public function saveSingleMember($user_id) {

        $users_coordinates = get_option('woffice_map_locations');
        $users_coordinates = json_decode($users_coordinates, true);
        $users_coordinates = (empty($users_coordinates)) ? array() : $users_coordinates;

        $already_exists = false;

        foreach ($users_coordinates as $key=>$coordinate_array) {

            if ((int) $coordinate_array['user_id'] !== $user_id)
                continue;

            $already_exists = true;

            $new_user_coordinates = $this->getMemberCoordinates($user_id);
            if (!empty($new_user_coordinates))
                $users_coordinates[$key] = $new_user_coordinates;

            break;

        }

        if (!$already_exists) {
            $new_user_coordinates = $this->getMemberCoordinates($user_id);
            if (!empty($new_user_coordinates))
                array_push($users_coordinates, $new_user_coordinates);
        }

        update_option('woffice_map_locations', json_encode($users_coordinates));
    }
	
	/**
	 * Get the API connection status
     *
     * @return array
	 */
	public function mapApiTest() {
		// Default location from Google DOC
	 	$location = "1600+Amphitheatre+Parkway,+Mountain+View,+CA";
		$result = $this->apiRequest($location);
		return $result;
	}

    /**
     * Makes an API call to the geocoding API
     *
     * @param $address
     * @return array
     */
	private function apiRequest($address) {

        // Get the API key
        $key_option = get_option('woffice_fw_get_api_google_geocoding','');
        $key = (!empty($key_option)) ? $key_option : "";

        // Make the request
        $request = wp_remote_get("https://maps.google.com/maps/api/geocode/json?address=" . $address . "&sensor=false&key=" . $key);
        $response = wp_remote_retrieve_body( $request );
        $json_decoded = json_decode($response, true);

	    return $json_decoded;

    }
	
	/**
	 * Generates Map's JS
     *
	 * @param string $type : members / widget
     * @return string
	 */
	public function usersMapJs($type) {

		$the_data = get_option('woffice_map_locations');

		if (!function_exists('bp_is_active'))
		    return '';

		if (empty($the_data) || $the_data == '[]')
			$the_data = '[{"name":"No Data","lat":0,"long":0,"user_id":1}]';
		
		$js_array = json_decode($the_data);
		$map_zoom = fw_get_db_ext_settings_option('woffice-map', 'map_zoom');
		$map_center = fw_get_db_ext_settings_option('woffice-map', 'map_center');
			
		$map_id = ($type == "members") ? "members-map" : "members-map-widget";

		if (!empty($js_array)) {
			
			$html = '<script type="text/javascript">
			jQuery(function () {
			
				var locations = '. json_encode($js_array) .';
				
				var c = new google.maps.LatLng('.$map_center['coordinates']['lat'].','.$map_center['coordinates']['lng'].');
				 
				var map = new google.maps.Map(document.getElementById("'.$map_id.'"), {
				  zoom: '.$map_zoom.',
				  center: c,
				  mapTypeId: google.maps.MapTypeId.ROADMAP,
				  scrollwheel: false,
				});
			
				var infowindow = new google.maps.InfoWindow();
			
				var marker;';
				$count = 0;
                $buddy_excluded_directory = woffice_get_settings_option('buddy_excluded_directory');
                $buddy_excluded_directory_ready = (!empty($buddy_excluded_directory) && $buddy_excluded_directory != 'nope') ? $buddy_excluded_directory : array('zZzZzZzZzZ');

                foreach($js_array as $location) {
					if(is_object(get_userdata($location->user_id))){
						$user = get_userdata($location->user_id);
	                    if(is_array($buddy_excluded_directory_ready) && count(array_intersect($buddy_excluded_directory_ready, $user->roles)) == 0) {
                            $avatar_url = bp_core_fetch_avatar( array(
                                    'item_id' => $location->user_id,
                                    'type' => 'thumb',
                                    'class' => 'avatar',
                                    'html' => false
                                )
                            );
        
                            $echo_avatar = (!empty($avatar_url)) ? '<img width=\"100\" height=\"100\" class=\"avatar user-14-avatar avatar-150 photo\" src=\"' . esc_url($avatar_url) . '\">': '';
                            $profile_url = bp_core_get_user_domain($location->user_id);
        
                            $info_box_content = $this->getMemberCardHtml($location->user_id, $profile_url, $echo_avatar, esc_html($location->name));

		                    /**
		                     * The info box user content
		                     *
		                     * @param string - the content
		                     * @param int - the user id
		                     *
		                     * @return string
		                     */
		                    $info_box_content = apply_filters('woffice_map_info_box_content', $info_box_content, $location->user_id);


	                        if (!empty($location->lat) && !empty($location->long)){
	                            $html .= 'marker = new google.maps.Marker({
									position: new google.maps.LatLng('.esc_html($location->lat).', '.esc_html($location->long).'),
									map: map
								});';

								$html .= 'google.maps.event.addListener(marker, "click", (function(marker) {
									return function() {
										infowindow.setContent("'.$info_box_content.'");
										infowindow.open(map, marker);
									}
								})(marker));';
	                        }
	
	                        $count++;
	                    }
					}
				}
				
				if ($type == "members") {
					$html .= 'jQuery("#members-map-trigger").on("click", function () {
						jQuery("#members-map").slideToggle(300, function(){
						    google.maps.event.trigger(map, "resize"); // resize map
						    map.setCenter(c); // set the center
						});
						jQuery(this).toggleClass("active");
						jQuery("#members-map-loader").fadeIn();
						function slideMap(){
							jQuery("#members-map-loader").fadeOut();
						}
						setTimeout(slideMap, 2000);
						
						jQuery("#buddypress").toggleClass("has-map");
						
						var $localizeButton = jQuery("#members-map-localize > a");
						if (!navigator.geolocation){
						    $localizeButton.remove();
						}
						$localizeButton.on("click", function () {
						    navigator.geolocation.getCurrentPosition(function(position) {
    							var initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
         						map.setCenter(initialLocation);
         						map.setZoom(9);
         						var positionMarker = new google.maps.Marker({
						          position: {lat: position.coords.latitude, lng: position.coords.longitude},
						          map: map,
						          title: "'. __('You are here!', 'woffice') .'"
						        });
						    }, function(error) {
						       $localizeButton.remove(); 
						    });
						});
						
			        });';
			    }
			
			$html .= '});
			</script>';
		}
		else {
			if ($type == "members") {
				// We display an empty map
				$html = '<script type="text/javascript">
					jQuery(function () {
						
						var c = new google.maps.LatLng('.$map_center['coordinates']['lat'].','.$map_center['coordinates']['lng'].');
						 
						var map = new google.maps.Map(document.getElementById("'.$map_id.'"), {
						  zoom: '.$map_zoom.',
						  center: c,
						  mapTypeId: google.maps.MapTypeId.ROADMAP
						});
						
						jQuery("#members-map-trigger").on("click", function () {
							jQuery("#members-map").slideToggle(300, function(){
							    google.maps.event.trigger(map, "resize"); // resize map
							    map.setCenter(c); // set the center
							});
							jQuery(this).toggleClass("active");
							jQuery("#members-map-loader").fadeIn();
							function slideMap(){
								jQuery("#members-map-loader").fadeOut();
							}
							setTimeout(slideMap, 2000);
							
							jQuery("#buddypress").toggleClass("has-map");
				        });
					});
					
				</script>';
			}
			else {
				$html = "";
			}
		}
		
		return $html;
	}
    
    /**
     * Get card view of the user
     *
     * @param int $user_id
     * @param string $profile_url
     * @param string $avatar_url
     * @param string $location
     *
     * @return string
     */
	public function getMemberCardHtml($user_id, $profile_url, $avatar_url, $location)
    {
		
		$members_role_enabled = apply_filters('woffice_enable_member_role_on_members_page', true);
        $html = '<div class=\"list-wrap\">';
        $html .= '<div class=\"item-avatar\">';
        $role = woffice_get_user_role($user_id);
    
        if ($members_role_enabled && !empty($role)) {
            $html .= '<span class=\"badge badge-primary\" data-template=\"woffice\">' . $role . '</span>';
        }
    
        $html .= '<a href=\"' . $profile_url . '\">' . $avatar_url . '</a></div>';
        $html .= '<div class=\"item text-center\"><div class=\"item-block\">'.
                    '<h2 class=\"list-title member-name\">'.
                        '<a class=\"profile-link\" href=\"' . $profile_url. '\">'.
                        '<i class=\"fa mr-1 fa-link\"></i>'.
                            woffice_get_name_to_display($user_id)
                        .'</a>'.
                    '</h2>';
    
        
        $html .= '<div class=\"location\">' . $location . '</div>';
        do_action('woffice_before_list_xprofile_fields');
        $html .= woffice_list_xprofile_fields($user_id, false);
        do_action('woffice_after_list_xprofile_fields');
        $html .= '</div></div></div>';
        
        return $html;
    }
    
	/**
	 * Callback whenever the extension is enabled
	 */
	public function mapOnActivate($extensions) {
		if (!isset($extensions['woffice-map']))
	        return;
	    $this->updateLocations();
	}
 
	
	/**
	 * Returns the view
	 */
	public function render() {
		return $this->render_view( 'view' );
	}
	
	/**
	 * Get the avatar's URL
	 */
	public function getAvatarUrl($get_avatar){
		preg_match('/src="(.*?)"/i', $get_avatar, $matches);
		return ( isset( $matches[1] ) ) ? $matches[1] : null;
	}
	/**
	 * Deletes the map field
     *
     * @param array $extensions
	 */
	public function mapDeleteField($extensions) {

		/* Only if it's the map extension */
		if (!function_exists('bp_is_active') || !isset($extensions['woffice-map']))
	        return;
	    
	    if (bp_is_active('xprofile')) {
			global $wpdb;
			$table_name = woffice_get_xprofile_table('fields');
			$field_name = $this->mapFieldName();
		    $sqlStr = "SELECT `id` FROM $table_name WHERE `name` = '$field_name'";
		    $field = $wpdb->get_results($sqlStr);
		    if(count($field) > 0) {
		        xprofile_delete_field($field[0]->id);
		    }
		}
		
	}

	/**
	 * Gets the field's name
     *
	 * @return string
	 */
	public function mapFieldName() {
		return fw_get_db_ext_settings_option($this->get_name(), 'map_field_name');
	}

    /**
     * Save API KEY Value
     */
    public function mapSaveApi() {
        $key_option = fw_get_db_ext_settings_option($this->get_name(), 'map_api');
        update_option('woffice_fw_get_api_google_geocoding', $key_option);
    }

    /**
     * Whether the current page is the extension's settings or not
     *
     * @return bool
     */
    public function isExtensionSettings() {
        return (isset($_GET['page']) && $_GET['page'] === 'fw-extensions' && isset($_GET['extension']) && $_GET['extension'] === 'woffice-map');
    }

    /**
     * Adds the refresh button to the extension settings page
     */
    public function addRefreshButtons() {

        echo fw_html_tag('a', array(
            'href'  => admin_url('admin.php?page=fw-extensions&sub-page=extension&extension=woffice-map&refresh_all_coordinates=true'),
            'class' => 'button-secondary',
            'style' => 'margin-bottom: 20px;',
        ), __('Refresh coordinates for all members', 'woffice'));

    }

}