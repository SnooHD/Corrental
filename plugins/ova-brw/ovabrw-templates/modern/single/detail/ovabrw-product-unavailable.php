<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;

	if ( isset( $args['product_id'] ) && $args['product_id'] ) {
		$product = wc_get_product( $args['product_id'] );
	}

	if ( ! $product ) return;

	$product_id = $product->get_id();

	$unavailabel_from = get_post_meta( $product_id, 'ovabrw_untime_startdate', true );
?>
<?php if ( ! empty( $unavailabel_from ) && is_array( $unavailabel_from ) ):
	$date_format 	= ovabrw_get_date_format() . ' ' . ovabrw_get_time_format_php();
	$unavailabel_to = get_post_meta( $product_id, 'ovabrw_untime_enddate', true );
?>
	<div class="ovabrw-product-unavailable">
		<label class="ovabrw-label"><?php esc_html_e( 'You can\'t rent product in this time', 'ova-brw' ); ?></label>
		<table class="ovabrw-table">
			<tbody>
				<?php foreach ( $unavailabel_from as $k => $from ):
					$to = isset( $unavailabel_to[$k] ) ? $unavailabel_to[$k] : '';

					if ( $from && $to ):
				?>
					<tr>
						<td><?php echo date( $date_format, strtotime( $from ) ). ' - '. date( $date_format, strtotime( $to ) ); ?></td>
					</tr>
				<?php endif; endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>