<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;
	if ( ! $product ) return;

	$product_id = $product->get_id();
	$features_featured = get_post_meta( $product_id, 'ovabrw_features_featured', true );
	$card = ovabrw_get_card_template();
?>

<?php if ( $product && $product->is_featured() && get_option( 'ovabrw_glb_'.$card.'_featured' , 'yes' ) === 'yes' ): ?>
	<span class="ovabrw-featured-product">
		<?php esc_html_e( 'Featured', 'ova-brw' ); ?>
	</span>
<?php endif; ?>
<?php if ( ! empty( $features_featured ) && is_array( $features_featured ) && get_option( 'ovabrw_glb_'.$card.'_feature_featured' , 'yes' ) === 'yes' ): ?>
	<?php foreach ( $features_featured as $k => $val ): ?>
		<?php if ( $val === 'yes' ):
			$features_desc = get_post_meta( $product_id, 'ovabrw_features_desc', true );

			$desc = isset( $features_desc[$k] ) ? $features_desc[$k] : '';

			if ( ! $desc ) continue;
		?>
			<span class="ovabrw-features-featured">
				<?php echo esc_html( $desc ); ?>
			</span>
		<?php break; endif; ?>
	<?php endforeach; ?>
<?php endif; ?>