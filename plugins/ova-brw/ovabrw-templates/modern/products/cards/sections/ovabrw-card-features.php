<?php if ( ! defined( 'ABSPATH' ) ) exit();
	$card = ovabrw_get_card_template();
	if ( get_option( 'ovabrw_glb_'.$card.'_features' , 'yes' ) != 'yes' ) return;

	global $product;
	if ( ! $product ) return;

	$product_id = $product->get_id();

	$features_desc = get_post_meta( $product_id, 'ovabrw_features_desc', true );
?>
<?php if ( ! empty( $features_desc ) && is_array( $features_desc ) ):
	$features_icons 	= get_post_meta( $product_id, 'ovabrw_features_icons', true );
	$features_special 	= get_post_meta( $product_id, 'ovabrw_features_special', true );

	if ( ! in_array( 'yes', $features_special ) ) return;
?>
	<ul class="ovabrw-features">
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