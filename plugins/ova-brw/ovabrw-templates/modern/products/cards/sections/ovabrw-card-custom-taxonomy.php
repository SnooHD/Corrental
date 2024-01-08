<?php if ( ! defined( 'ABSPATH' ) ) exit();
	$card = ovabrw_get_card_template();
	if ( get_option( 'ovabrw_glb_'.$card.'_custom_taxonomy' , 'yes' ) != 'yes' ) return;

	global $product;
	if ( ! $product ) return;

	$product_id = $product->get_id();
	$taxonomies = ovabrw_get_taxonomy_choosed_product( $product_id, 'on' );
?>
<?php if ( ! empty( $taxonomies ) ): ?>
	<ul class="ovabrw-custom-taxonomy">
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