<?php
/**
 * The template for displaying seasons content within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/table-price/seasons_day.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

// Get product_id from do_action - use when insert shortcode
if ( isset( $args['product_id'] ) && $args['product_id'] ) {
	$pid = $args['product_id'];
} else {
	$pid = get_the_id();
}

$rt_startdate 	= get_post_meta( $pid, 'ovabrw_rt_startdate', true ); 
$rt_enddate 	= get_post_meta( $pid, 'ovabrw_rt_enddate', true );
$rt_price 		= get_post_meta( $pid, 'ovabrw_rt_price', true );
$rt_discount 	= get_post_meta( $pid, 'ovabrw_rt_discount', true );
$rt_starttime 	= get_post_meta( $pid, 'ovabrw_rt_starttime', true );
$rt_endtime 	= get_post_meta( $pid, 'ovabrw_rt_endtime', true) ;

$ovabrw_date_format = ovabrw_get_date_format();

if ( empty( $rt_price ) ) return;

?>

<div class="price_table">
	<label><?php esc_html_e( 'Special Time', 'ova-brw' ); ?></label>
	<table>
		<thead>
			<tr>
				<th><?php esc_html_e( 'Start Date', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'End Date', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Price/Day', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Special Discount', 'ova-brw' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php 
				if ( $rt_price ):
					$d = 0;

					foreach( $rt_price as $key => $value ):
						if ( $rt_price[$key] ):
							$date_start = $rt_startdate[$key] ? date_i18n( $ovabrw_date_format, strtotime( $rt_startdate[$key] ) ).' '.$rt_starttime[$key] : '';
							$date_end = $rt_enddate[$key] ? date_i18n( $ovabrw_date_format, strtotime( $rt_enddate[$key] ) ).' '.$rt_endtime[$key] : '';
							?>
							<tr class="<?php echo intval($d%2) ? 'eve' : 'odd'; $d++; ?>">
								<td class="bold"  data-title="<?php esc_html_e( 'Start Date', 'ova-brw' ); ?>">
									<?php echo $date_start; ?>
								</td>
								<td class="bold"  data-title="<?php esc_html_e( 'End Date', 'ova-brw' ); ?>">
									<?php echo $date_end; ?>
								</td>
								<td data-title="<?php echo esc_html__( 'Price/Day from', 'ova-brw' ).' '.$date_start.' - '.$date_end; ?>">
									<?php echo ovabrw_wc_price( $rt_price[$key] ); ?>
								</td>
								<td data-title="<?php esc_html_e( 'Special Discount', 'ova-brw' ); ?>">
									<a href="#" class="ovabrw_open_popup" data-popup-open="popup-ovacrs-rt-discount-day-<?php echo esc_attr( $key ); ?>">
										<?php esc_html_e( 'View Discount', 'ova-brw' ); ?>
										<div class="ovacrs_rt_discount popup" data-popup="popup-ovacrs-rt-discount-day-<?php echo esc_attr( $key ); ?>">
											<div class="popup-inner">
												<div class="price_table">
													<div class="time_discount">														
														<span>
															<?php esc_html_e( 'Time Discount: ', 'ova-brw' ); ?>
														</span>
														<span class="time">
															<?php echo $date_start.' - '.$date_end; ?>
														</span>
													</div>
													<?php 
														$rt_discount_price = isset( $rt_discount[$key]['price'] ) ? $rt_discount[$key]['price'] : '';
														$rt_discount_duration_min = isset( $rt_discount[$key]['min'] ) ? $rt_discount[$key]['min'] : '';
														$rt_discount_duration_max = isset( $rt_discount[$key]['max'] ) ? $rt_discount[$key]['max'] : '';
														$rt_discount_duration_type = isset( $rt_discount[$key]['duration_type'] ) ? $rt_discount[$key]['duration_type'] : ''; ?>

													<?php if ( $rt_discount_duration_min || $rt_discount_duration_max ):
														asort($rt_discount_duration_min);
														asort($rt_discount_duration_max);
													?>
														<table>
															<thead>
																<tr>
																	<th><?php esc_html_e( 'Min - Max (Days)', 'ova-brw' ); ?></th>
																	<th><?php esc_html_e( 'Price/Day ', 'ova-brw' ); ?></th>
																</tr>
															</thead>
															<tbody>
																<?php $n = 0;
																	foreach ($rt_discount_duration_min as $k => $v) {

																		if( $rt_discount_duration_type[$k] == 'days' ){ ?>

																			<tr class="<?php echo intval($n%2) ? 'eve' : 'odd'; $n++; ?>">

																				<td class="bold" data-title="<?php esc_html_e( 'Min - Max (Days)', 'ova-brw' ); ?>">

																					<?php echo esc_html( $rt_discount_duration_min[$k] ).' - '.esc_html( $rt_discount_duration_max[$k] ); ?>
																					
																				</td>
																				

																				<td data-title="<?php echo esc_html__( 'Price/Day from', 'ova-brw' ).' '.esc_html( $rt_discount_duration_min[$k] ).' - '.esc_html( $rt_discount_duration_max[$k] ).' '.esc_html__('days', 'ova-brw'); ?>">

																					<?php echo ovabrw_wc_price( $rt_discount_price[$k] ); ?>
																					
																				</td>

																			</tr>

																		<?php }
																	} ?>
															</tbody>
														</table>
													<?php else: ?>
														<div class="no_discount">
															<?php esc_html_e( 'No Discount in this time', 'ova-brw' ); ?>
														</div>
													<?php endif; ?>
												</div>	
												<div class="close_discount">
													<a class="popup-close-2" data-popup-close="popup-ovacrs-rt-discount-day-<?php echo esc_attr( $key ); ?>" href="#">
														<?php esc_html_e( 'Close', 'ova-brw' ); ?>
													</a>
												</div>
												<a class="popup-close" data-popup-close="popup-ovacrs-rt-discount-day-<?php echo esc_attr( $key ); ?>" href="#">x</a>
											</div>
										</div>
									</a>
								</td>
							</tr>			
				<?php endif; endforeach; endif; ?>
		</tbody>
	</table>
</div>
