<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;

	if ( isset( $args['product_id'] ) && $args['product_id'] ) {
		$product = wc_get_product( $args['product_id'] );
	}

	if ( ! $product ) return;

	$product_id = $product->get_id();
	$taxonomies = ovabrw_get_taxonomy_choosed_product( $product_id );
?>
<?php if ( ! empty( $taxonomies ) ): ?>
	<ul class="ovabrw-product-custom-taxonomy">
		<?php foreach ( $taxonomies as $k => $taxonomy ):
			$name 	= isset( $taxonomy['name'] ) ? $taxonomy['name'] : '';
			$value 	= isset( $taxonomy['value'] ) ? $taxonomy['value'] : [];
			$link 	= isset( $taxonomy['link'] ) ? $taxonomy['link'] : [];
		?>
			<li class="item-taxonomy">
				<span class="label"><?php echo esc_html( $taxonomy['name'].':' ); ?></span>
				<span class="value">
					<?php if ( ! empty( $value ) && is_array( $value ) ): ?>
						<?php foreach ( $value as $k_v => $val ):
							$v_link 	= isset( $link[$k_v] ) ? $link[$k_v] : '';
							$separator 	= '';

							if ( $k_v < count( $value ) - 1 ) $separator = ',';
						?>
							<a href="<?php echo esc_url( $v_link ); ?>">
								<?php echo esc_html( $val ).$separator; ?>
							</a>
						<?php endforeach; ?>
					<?php endif; ?>
				</span>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>