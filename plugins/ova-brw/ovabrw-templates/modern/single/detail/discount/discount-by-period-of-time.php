<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;
	if ( ! $product ) return;

	$product_id = $product->get_id();

	$peoftime_label = get_post_meta( $product_id, 'ovabrw_petime_label', true ); 
	$peoftime_price = get_post_meta( $product_id, 'ovabrw_petime_price', true );
?>
<?php if ( ! empty( $peoftime_label ) && ! empty( $peoftime_price ) ): ?>
	<div class="ovabrw-product-discount">
		<?php foreach ( $peoftime_label as $k => $label ):
			$price 				= isset( $peoftime_price[$k] ) ? $peoftime_price[$k] : '';
			$peoftime_discount 	= get_post_meta( $product_id, 'ovabrw_petime_discount', true );
			$discount_price 	= isset( $peoftime_discount[$k]['price'] ) ? $peoftime_discount[$k]['price'] : '';
			$discount_start 	= isset( $peoftime_discount[$k]['start_time'] ) ? $peoftime_discount[$k]['start_time'] : '';
			$discount_end 		= isset( $peoftime_discount[$k]['end_time'] ) ? $peoftime_discount[$k]['end_time'] : '';
		?>
			<label class="ovabrw-label">
				<?php printf( esc_html__( '%s: %s', 'ova-brw' ), $label, ovabrw_wc_price( $price ) ); ?>
			</label>
			<?php if ( ! empty( $discount_price ) ):
				$date_format = ovabrw_get_date_format() . ' ' . ovabrw_get_time_format_php();
			?>
				<table class="ovabrw-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
							<th><?php esc_html_e( 'Start Date', 'ova-brw' ); ?></th>
							<th><?php esc_html_e( 'End Date', 'ova-brw' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $discount_price as $dsc_k => $dsc_price ):
							$start 	= isset( $discount_start[$dsc_k] ) ? $discount_start[$dsc_k] : '';
							$end 	= isset( $discount_end[$dsc_k] ) ? $discount_end[$dsc_k] : '';
						?>
							<?php if ( $dsc_price != '' && $start != '' && $end != '' ): ?>
								<tr>
									<td><?php echo ovabrw_wc_price( $dsc_price ); ?></td>
									<td><?php echo date( $date_format, strtotime( $start ) ); ?></td>
									<td><?php echo date( $date_format, strtotime( $end ) ); ?></td>
								</tr>
						<?php endif; endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>