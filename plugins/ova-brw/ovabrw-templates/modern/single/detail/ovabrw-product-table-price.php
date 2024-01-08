<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;

	if ( isset( $args['product_id'] ) && $args['product_id'] ) {
		$product = wc_get_product( $args['product_id'] );
	}

	if ( ! $product ) return;

	if ( get_option( 'ova_brw_template_show_table_price', 'yes' ) != 'yes' ) return;

	$product_id = $product->get_id();
	if ( ! $product_id ) return;

	$is_table_price = ovabrw_check_table_price( $product_id );
	if ( ! $is_table_price ) return;
?>
<div class="ovabrw-product-table-price">
	<h2 class="title"><?php esc_html_e( 'Table Price', 'ova-brw' ); ?></h2>
	<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-weekdays-price.php' ); ?>
	<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-global-discount.php' ); ?>
	<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-special-time.php' ); ?>
</div>