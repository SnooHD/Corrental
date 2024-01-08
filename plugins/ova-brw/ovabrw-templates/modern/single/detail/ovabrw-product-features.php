<?php if ( ! defined( 'ABSPATH' ) ) exit();
	if ( get_option( 'ova_brw_template_show_feature', 'yes' ) != 'yes' ) return;

	global $product;

	if ( isset( $args['product_id'] ) && $args['product_id'] ) {
		$product = wc_get_product( $args['product_id'] );
	}

	if ( ! $product ) return;

	$product_id = $product->get_id();

	$features_desc = get_post_meta( $product_id, 'ovabrw_features_desc', true );
?>
<?php if ( ! empty( $features_desc ) && is_array( $features_desc ) ):
	$features_icons 	= get_post_meta( $product_id, 'ovabrw_features_icons', true );
	$features_special 	= get_post_meta( $product_id, 'ovabrw_features_special', true );

	if ( ! in_array( 'yes', $features_special ) ) return;
?>
	<ul class="ovabrw-product-features">
		<?php foreach ( $features_desc as $k => $desc ):
			$special 	= isset( $features_special[$k] ) ? $features_special[$k] : '';
			$icon_class = isset( $features_icons[$k] ) ? $features_icons[$k] : '';

			if ( ! $special || $special != 'yes' ) continue;
		?>
			<li class="item-feature">
				<?php if ( $icon_class ): ?>
					<i aria-hidden="true" class="<?php echo esc_attr( $icon_class ); ?>"></i>
				<?php endif; ?>
				<span><?php echo esc_html( $desc ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>