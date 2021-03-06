<?php
/**
 * The default template part for the download
 * confirmation url in the content-downloads
 * template part's product loop.
 *
 * @since 1.1.0
 * @version 1.1.0
 * @package IT_Exchange
 *
 * WARNING: Do not edit this file directly. To use
 * this template in a theme, simply copy this file's
 * content to the exchange/content-downloads/elements
 * directory located in your theme.
*/
?>

<?php do_action( 'it_exchange_content_download_before_confirmation_url_element' ); ?>
<div class="it-exchange-download-product">
	<a href="<?php it_exchange( 'transaction', 'product-attribute', array( 'attribute' => 'confirmation-url' ) ); ?>" class="btn btn-default"><?php _e( 'Transaction', 'woffice' ); ?></a>
</div>
<?php do_action( 'it_exchange_content_download_after_confirmation_url_element' ); ?>