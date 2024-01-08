<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;

	if ( isset( $args['product_id'] ) && $args['product_id'] ) {
		$product = wc_get_product( $args['product_id'] );
	}

	if ( ! $product ) return;

	$title = $product->get_title();
?>
<?php if ( $title ): ?>
	<h2 class="ovabrw-product-title"><?php echo esc_html( $title ); ?></h2>
<?php endif; ?>