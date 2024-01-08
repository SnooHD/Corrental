<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;
	if ( ! $product ) return;

	$product_id = $product->get_id();

	$discount_type 	= get_post_meta( $product_id, 'ovabrw_global_discount_duration_type', true );
	$discount_min 	= get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_min', true );
	$discount_max  	= get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_max', true );
	$discount_price = get_post_meta( $product_id, 'ovabrw_global_discount_price', true );
?>
<?php if ( ! empty( $discount_min ) && ! empty( $discount_min ) ): ?>
	<div class="ovabrw-product-discount">
		<label class="ovabrw-label"><?php esc_html_e( 'Global Discount', 'ova-brw' ); ?></label>
		<table class="ovabrw-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Min - Max (Hours)', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'Price/Hour', 'ova-brw' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $discount_min as $k => $min ):
					$type 	= isset( $discount_type[$k] ) ? $discount_type[$k] : '';
					$max 	= isset( $discount_max[$k] ) ? $discount_max[$k] : '';
					$price 	= isset( $discount_price[$k] ) ? $discount_price[$k] : '';
				?>
					<?php if ( $type === 'hours' && $min != '' && $max != '' && $price != '' ): ?>
						<tr>
							<td>
								<span><?php echo esc_html( $min ); ?></span>
								<span> - </span>
								<span><?php echo esc_html( $max ); ?></span>
							</td>
							<td><?php echo ovabrw_wc_price( $price ); ?></td>
						</tr>
				<?php endif; endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>