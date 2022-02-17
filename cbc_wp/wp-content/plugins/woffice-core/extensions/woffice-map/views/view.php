<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * VIEW OF FW_Extension_Woffice_Map
 */

$js_array = get_option('woffice_map_locations');
$has_localization = fw_get_db_ext_settings_option('woffice-map', 'map_localization');

if (!empty($js_array)) : ?>
	<div id="members-map-container">
		<div id="members-map"></div>
		<?php if (is_ssl() && $has_localization) : ?>
            <div id="members-map-localize" class="text-center">
                <a href="javascript:void(0)" class="btn btn-default">
                    <i class="fa fa-map-pin"></i> <?php _e('Localize me','woffice'); ?>
                </a>
            </div>
		<?php endif; ?>
	</div>
<?php else: ?>
	<div class="center"><p><?php _e('Sorry there is no users locations so we can not display the map. As it is empty.','woffice'); ?></p></div>
<?php endif; ?>