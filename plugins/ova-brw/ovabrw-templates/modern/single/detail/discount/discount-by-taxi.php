<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;
	if ( ! $product ) return;

	$product_id = $product->get_id();

	$discount_price = get_post_meta( $product_id, 'ovabrw_discount_distance_price', true );
?>
<?php if ( ! empty( $discount_price ) ):
	$discount_from 	= get_post_meta( $product_id, 'ovabrw_discount_distance_from', true );
	$discount_to 	= get_post_meta( $product_id, 'ovabrw_discount_distance_to', true );
?>
	<div class="ovabrw-product-discount">
		<label class="ovabrw-label"><?php esc_html_e( 'Global Discount', 'ova-brw' ); ?></label>
		<table class="ovabrw-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'From (Km)', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'To (Km)', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'Price/Km', 'ova-brw' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $discount_price as $k => $price ):
					$from 	= isset( $discount_from[$k] ) ? $discount_from[$k] : '';
					$to 	= isset( $discount_to[$k] ) ? $discount_to[$k] : '';
				?>
					<?php if ( $price != '' && $from != '' && $to != '' ): ?>
						<tr>
							<td><?php echo esc_html( $from ); ?></td>
							<td><?php echo esc_html( $to ); ?></td>
							<td><?php echo ovabrw_wc_price( $price ); ?></td>
						</tr>
				<?php endif; endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>