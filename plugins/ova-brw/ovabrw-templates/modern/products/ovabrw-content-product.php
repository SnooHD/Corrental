<?php if ( ! defined( 'ABSPATH' ) ) exit();

global $product;
if ( ! $product ) return;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$product_id = $product->get_id();

?>
<li <?php wc_product_class( '', $product ); ?>>
	<?php ovabrw_get_template( 'modern/products/cards/ovabrw-'.ovabrw_get_card_template().'.php', [ 'product_id' => $product_id ] ); ?>
</li>