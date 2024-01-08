<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;
	if ( ! $product ) return;

	$product_id = $product->get_id();

	$features_featured = get_post_meta( $product_id, 'ovabrw_features_featured', true );
	$card = ovabrw_get_card_template();
?>

<?php if ( $product && $product->is_featured() ): ?>
	<span class="ovabrw-product-is-featured">
		<?php esc_html_e( 'Featured', 'ova-brw' ); ?>
	</span>
<?php endif; ?>
<?php if ( ! empty( $features_featured ) && is_array( $features_featured ) ): ?>
	<?php foreach ( $features_featured as $k => $val ): ?>
		<?php if ( $val === 'yes' ):
			$features_desc = get_post_meta( $product_id, 'ovabrw_features_desc', true );

			$desc = isset( $features_desc[$k] ) ? $features_desc[$k] : '';

			if ( ! $desc ) continue;
		?>
			<span class="ovabrw-product-features-is-featured">
				<?php echo esc_html( $desc ); ?>
			</span>
		<?php break; endif; ?>
	<?php endforeach; ?>
<?php endif; ?>