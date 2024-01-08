<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;
	if ( ! $product ) return;

	$product_id = $product->get_id();

	$special_price = get_post_meta( $product_id, 'ovabrw_st_price_distance', true );
?>
<?php if ( ! empty( $special_price ) && is_array( $special_price ) ):
	$date_format 		= ovabrw_get_date_format() . ' ' . ovabrw_get_time_format_php();
	$special_startdate 	= get_post_meta( $product_id, 'ovabrw_st_pickup_distance', true ); 
	$special_enddate 	= get_post_meta( $product_id, 'ovabrw_st_pickoff_distance', true );
	$special_discount 	= get_post_meta( $product_id, 'ovabrw_st_discount_distance', true );
?>
	<div class="ovabrw-product-special-time">
		<label class="ovabrw-label"><?php esc_html_e( 'Special Time', 'ova-brw' ); ?></label>
		<table class="ovabrw-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Start Date', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'End Date', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'Price/Km', 'ova-brw' ); ?></th>
					<?php if ( ! empty( $special_discount ) && is_array( $special_discount ) ): ?>
						<th><?php esc_html_e( 'Discount', 'ova-brw' ); ?></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $special_price as $k => $price ):
					$start_date = isset( $special_startdate[$k] ) ? $special_startdate[$k] : '';
					$end_date 	= isset( $special_enddate[$k] ) ? $special_enddate[$k] : '';
					$start = $end = '';

					if ( $start_date ) {
						$start = date( $date_format, strtotime( $start_date ) );
					}

					if ( $end_date ) {
						$end = date( $date_format, strtotime( $end_date ) );
					}
				?>
					<?php if ( $price != '' && $start != '' && $end != '' ): ?>
						<tr>
							<td><?php echo esc_html( $start ); ?></td>
							<td><?php echo esc_html( $end ); ?></td>
							<td><?php echo ovabrw_wc_price( $price ); ?></td>
							<?php if ( ! empty( $special_discount ) && is_array( $special_discount ) ):
								$dsc_price 	= isset( $special_discount[$k]['price'] ) ? $special_discount[$k]['price'] : '';
								$dsc_from 	= isset( $special_discount[$k]['from'] ) ? $special_discount[$k]['from'] : '';
								$dsc_to 	= isset( $special_discount[$k]['to'] ) ? $special_discount[$k]['to'] : '';
							?>
								<td>
									<a href="#" class="ovabrw_open_popup">
										<?php esc_html_e( 'View', 'ova-brw' ); ?>
									</a>
									<div class="popup">
										<div class="popup-inner">
											<div class="price_table">
												<div class="time_discount">
													<span><?php esc_html_e( 'Time: ', 'ova-brw' ); ?></span>
													<span class="start-time"><?php echo esc_html( $start ); ?></span>
													<span class="seperate">-</span>
													<span class="end-time"><?php echo esc_html( $end ); ?></span>
												</div>
												<?php if ( $dsc_price ): ?>
													<table class="ovabrw-table">
														<thead>
															<tr>
																<th><?php esc_html_e( 'From - To (Km)', 'ova-brw' ); ?></th>
																<th><?php esc_html_e( 'Price/Km', 'ova-brw' ); ?></th>
															</tr>
														</thead>
														<tbody>
															<?php foreach ( $dsc_price as $dsc_k => $dsc_v_price ):
																$dsc_v_from = isset( $dsc_from[$dsc_k] ) ? $dsc_from[$dsc_k] : '';
																$dsc_v_to 	= isset( $dsc_to[$dsc_k] ) ? $dsc_to[$dsc_k] : '';
															?>
																<?php if ( $dsc_v_from != '' && $dsc_v_to != '' && $dsc_v_price != '' ): ?>
																	<tr>
																		<td>
																			<span><?php echo esc_html( $dsc_v_from ); ?></span>
																			<span>-</span>
																			<span><?php echo esc_html( $dsc_v_to ); ?></span>
																		</td>
																		<td><?php echo ovabrw_wc_price( $dsc_v_price ); ?></td>
																	</tr>
															<?php endif; endforeach; ?>
														</tbody>
													</table>
												<?php endif; ?>
											</div>
											<div class="close_discount">
												<a class="popup-close-2" href="#">
													<?php esc_html_e( 'Close', 'ova-brw' ); ?>
												</a>
											</div>
											<a class="popup-close" href="#">x</a>
										</div>
									</div>
								</td>
							<?php endif; ?>
						</tr>
				<?php endif; endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>