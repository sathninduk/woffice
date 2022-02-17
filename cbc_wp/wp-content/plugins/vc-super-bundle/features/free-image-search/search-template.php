<?php
/**
 * Template file for the widget picker modal popup.
 *
 * @package CC Image Search
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

?>
<script type="text/html" id="tmpl-ccimage-search">
	<div class="media-toolbar ccimage-search-toolbar">
		<div class="media-toolbar-secondary">
			<label for="ccimage-provider-filters" class="screen-reader-text"><?php _e( 'Filter by provider', 'cc_image_search' ) ?></label>
			<select id="ccimage-provider-filters" class="attachment-filters">
				<option value=""><?php _e( 'All image providers', 'cc_image_search' ) ?></option>
				<option value="flickr"><?php _e( 'Flickr', 'cc_image_search' ) ?></option>
				<option value="pixabay"><?php _e( 'Pixabay', 'cc_image_search' ) ?></option>
				<option value="unsplash"><?php _e( 'Unsplash', 'cc_image_search' ) ?></option>
				<option value="pexels"><?php _e( 'Pexels', 'cc_image_search' ) ?></option>
				<option value="giphy"><?php _e( 'Giphy', 'cc_image_search' ) ?></option>
			</select>
			<label for="ccimage-license-filters" class="screen-reader-text"><?php _e( 'Filter by license', 'cc_image_search' ) ?></label>
			<select id="ccimage-license-filters" class="attachment-filters">
				<option value=""><?php _e( 'Licenses that allow commercial use', 'cc_image_search' ) ?></option>
				<option value="noncommercial"><?php _e( 'Include noncommercial licenses', 'cc_image_search' ) ?></option>
				<option value="noattribution"><?php _e( 'Licenses with no attribution required', 'cc_image_search' ) ?></option>
			</select>
			<span class="spinner"></span>
		</div>
		<div class="media-toolbar-primary search-form">
			<div class="media-search-input-note"><? _e( 'Downloaded images will appear in your library', 'cc_image_search' ) ?></div>
			<label for="media-search-input" class="screen-reader-text"><?php _e( 'Search Image', 'cc_image_search' ) ?></label>
			<input type="search" placeholder="Search" id="media-search-input" class="search"/>
		</div>
	</div>
	<div class="media-sidebar">
		<div class="ccimage-details" style="display: none;">
			<h2><?php _e( 'Image Details', 'cc_image_search' ) ?></h2>
			<div class='attachment-info'>
				<div class='thumbnail thumbnail-image'>
					<img class='preview' draggable="false"/>
				</div>
			</div>
			<div class='provider'><strong><?php _e( 'Image Provider:', 'cc_image_search' ) ?></strong> <span></span></div>
			<div class='title'><strong><?php _e( 'Title:', 'cc_image_search' ) ?></strong> <span></span></div>
			<div class='date'><strong><?php _e( 'Date:', 'cc_image_search' ) ?></strong> <span></span></div>
			<div class='owner'><strong><?php _e( 'Owner:', 'cc_image_search' ) ?></strong> <span></span></div>
			<div class='license'><strong><?php _e( 'License:', 'cc_image_search' ) ?></strong> <span></span></div>
			<div class='sizes'><strong><?php _e( 'Sizes:', 'cc_image_search' ) ?></strong> <span></span></div>
		</div>
	</div>
	<ul tabindex="-1" class="attachments ccimage-result-container">
	</ul>
</script>


<script type="text/html" id="tmpl-ccimage-search-result">
	<div class="attachment-preview {{ data.orientation }}">
		<div class="thumbnail">
			<div class="centered">
				<img src="{{ data.preview }}" draggable="false" alt="" width="{{ data.preview_width }}" height="{{ data.preview_height }}">
			</div>
		</div>
		<div class="ccimage-overlay">
			<# if ( data.provider === 'flickr' ) { #>
				<div class="ccimage-provider-flickr" role="presentation" title="<?php echo esc_attr( __( 'From Flickr', 'cc_image_search' ) ) ?>"></div>
			<# } else if ( data.provider === 'pixabay' ) { #>
				<div class="ccimage-provider-pixabay" role="presentation" title="<?php echo esc_attr( __( 'From Pixabay', 'cc_image_search' ) ) ?>"></div>
			<# } else if ( data.provider === 'unsplash' ) { #>
				<div class="ccimage-provider-unsplash" role="presentation" title="<?php echo esc_attr( __( 'From Unsplash', 'cc_image_search' ) ) ?>"></div>
			<# } else if ( data.provider === 'pexels' ) { #>
				<div class="ccimage-provider-pexels" role="presentation" title="<?php echo esc_attr( __( 'From Pexels', 'cc_image_search' ) ) ?>"></div>
			<# } else if ( data.provider === 'giphy' ) { #>
				<div class="ccimage-provider-giphy" role="presentation" title="<?php echo esc_attr( __( 'From Giphy', 'cc_image_search' ) ) ?>"></div>
			<# } #>
			<div class="ccimage-info"></div>
			<div class="ccimage-downloading"></div>
			<div class="ccimage-download-label"><?php _e( 'Download', 'cc_image_search' ) ?></div>
			<div class="ccimage-download" title="<?php echo esc_attr( __( 'Download to site', 'cc_image_search' ) ) ?>"></div>
			<# if ( data.badges.indexOf( 'attribution' ) !== -1 ) { #>
				<div class="ccimage-badge-cc" role="presentation" title="<?php echo esc_attr( __( 'Needs attribution', 'cc_image_search' ) ) ?>"></div>
			<# } #>
			<# if ( data.badges.indexOf( 'noncommercial' ) !== -1 ) { #>
				<div class="ccimage-badge-noncommercial" role="presentation" title="<?php echo esc_attr( __( 'Noncommercial', 'cc_image_search' ) ) ?>"></div>
			<# } #>
			<# if ( data.badges.indexOf( 'zero' ) !== -1 ) { #>
				<!-- <div class="ccimage-badge-zero" role="presentation" title="<?php echo esc_attr( __( 'Public Domain', 'cc_image_search' ) ) ?>"></div> -->
			<# } #>
			<# if ( data.badges.indexOf( 'warning' ) !== -1 ) { #>
				<div class="ccimage-badge-warning" role="presentation" title="<?php echo esc_attr( __( 'Check License', 'cc_image_search' ) ) ?>"></div>
			<# } #>
		</div>
	</div>
</script>
