<?php
/**
 * The template for displaying period time content within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/table-price/period_time.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

// Get product_id from do_action - use when insert shortcode
if ( isset( $args['product_id'] ) && $args['product_id'] ) {
	$pid = $args['product_id'];
} else {
	$pid = get_the_id();
}

$ovabrw_date_format = ovabrw_get_date_format();
$ovabrw_time_format = ovabrw_get_time_format_php();

$ovabrw_date_time_format = $ovabrw_date_format . ' ' . $ovabrw_time_format;

$petime_label = get_post_meta( $pid, 'ovabrw_petime_label', true ); 
$petime_price = get_post_meta( $pid, 'ovabrw_petime_price', true );

$price_text = esc_html__( 'Price', 'ova-brw' );
$start_time_text = esc_html__( 'Start Time', 'ova-brw' );
$end_time_text = esc_html__( 'End Time', 'ova-brw' );

if ( !empty( $petime_label ) ):
	foreach( $petime_label as $key => $value ):
		$petime_discount 			= get_post_meta( $pid, 'ovabrw_petime_discount', true );
		$discount_price_row 		= isset( $petime_discount[$key]['price'] ) ? $petime_discount[$key]['price'] : '';
		$discount_start_time_row 	= isset( $petime_discount[$key]['start_time'] ) ? $petime_discount[$key]['start_time'] : '';
		$discount_end_time_row 		= isset( $petime_discount[$key]['end_time'] ) ? $petime_discount[$key]['end_time'] : '';
?>
	<div class="price_table">
		<label>
			<?php echo $petime_label[$key].' : '.ovabrw_wc_price($petime_price[$key]); ?>
		</label>
		<?php if( !empty( $discount_price_row ) ): ?>
			<table>
				<thead>
					<tr>
						<th><?php echo $price_text; ?></th>
						<th><?php echo $start_time_text; ?></th>
						<th><?php echo $end_time_text; ?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$h = 0;
						foreach( $discount_price_row as $k => $v ):
					?>
							<tr class="<?php echo intval($h%2) ? 'eve' : 'odd'; $h++; ?>">
								<td class="bold" data-title="<?php echo $price_text; ?>">
									<?php echo ovabrw_wc_price( $discount_price_row[$k] ); ?>
								</td>
								<td data-title="<?php echo $start_time_text; ?>">
									<?php echo $discount_start_time_row[$k] != '' ? date( $ovabrw_date_time_format, strtotime( $discount_start_time_row[$k] ) ) : ''; ?>	
								</td>
								<td data-title="<?php echo $end_time_text; ?>">
									<?php echo $discount_end_time_row[$k] != '' ? date( $ovabrw_date_time_format, strtotime( $discount_end_time_row[$k] ) ) : ''; ?>
								</td>
							</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
<?php endforeach; endif; ?>
