<?php if ( ! defined( 'ABSPATH' ) ) exit;
	// ST
	$ovabrw_st_pickup_distance = get_post_meta( $post_id, 'ovabrw_st_pickup_distance', true );
?>

<div class="ovabrw_st_distance_table">
	<table class="widefat">
		<thead>
			<th><?php esc_html_e( 'Start Date', 'ova-brw' ); ?></th>
			<th><?php esc_html_e( 'End Date', 'ova-brw' ); ?></th>
			<th><?php printf( esc_html__( 'Price (%s)', 'ova-brw' ), get_woocommerce_currency_symbol() ); ?></th>
			<th><?php esc_html_e( 'Discount', 'ova-brw' ); ?></th>
		</thead>
		<tbody>
		<?php if ( ! empty( $ovabrw_st_pickup_distance ) && is_array( $ovabrw_st_pickup_distance ) ):
			$time_format = ovabrw_get_time_format_php();
			$date_format = ovabrw_get_date_format();

			// ST
			$ovabrw_st_pickoff_distance = get_post_meta( $post_id, 'ovabrw_st_pickoff_distance', true );
			$ovabrw_st_price_distance 	= get_post_meta( $post_id, 'ovabrw_st_price_distance', true );

			// ST Discount
			$st_discount_distance 	= get_post_meta( $post_id, 'ovabrw_st_discount_distance', true );
			$price_by 				= get_post_meta( $post_id, 'ovabrw_map_price_by', true );

			if ( ! $price_by ) $price_by = 'km';

			foreach ( $ovabrw_st_pickup_distance as $k => $pickup ):
				$pickoff 	= isset( $ovabrw_st_pickoff_distance[$k] ) ? $ovabrw_st_pickoff_distance[$k] : '';
				$price 		= isset( $ovabrw_st_price_distance[$k] ) ? $ovabrw_st_price_distance[$k] : '';
				$dsc_from 	= isset( $st_discount_distance[$k]['from'] ) ? $st_discount_distance[$k]['from'] : '';
				$dsc_to 	= isset( $st_discount_distance[$k]['to'] ) ? $st_discount_distance[$k]['to'] : '';
				$dsc_price 	= isset( $st_discount_distance[$k]['price'] ) ? $st_discount_distance[$k]['price'] : '';
		?>
			<tr class="row_st_distance" data-index="<?php echo esc_attr( $k ); ?>">
				<td width="20%">
					<input
						type="text"
						name="ovabrw_st_pickup_distance[]"
						class="ovabrw_datetimepicker"
						placeholder="<?php echo esc_attr( $date_format . ' ' . $time_format ); ?>"
						value="<?php echo esc_attr( $pickup ); ?>"
						autocomplete="off"
				      	onfocus="blur();"
					/>
				</td>
				<td width="20%">
					<input
						type="text"
						name="ovabrw_st_pickoff_distance[]"
						class="ovabrw_datetimepicker"
						placeholder="<?php echo esc_attr( $date_format . ' ' . $time_format ); ?>"
						value="<?php echo esc_attr( $pickoff ); ?>"
						autocomplete="off"
				      	onfocus="blur();"
					/>
				</td>
				<td width="14%">
					<input
						type="text"
						name="ovabrw_st_price_distance[]"
						placeholder="<?php esc_html_e( 'Number', 'ova-brw' ); ?>"
						value="<?php echo esc_attr( $price ); ?>"
						autocomplete="off"
					/>
				</td>
				<td width="45%">
					<table width="100%" class="widefat ovabrw_st_discount_distance">
						<thead>
							<tr>
								<th><?php printf( esc_html__( 'From (%s)', 'ova-brw' ), $price_by ); ?></th>
								<th><?php printf( esc_html__( 'To (%s)', 'ova-brw' ), $price_by ); ?></th>
								<th><?php printf( esc_html__( 'Price (%s)', 'ova-brw' ), get_woocommerce_currency_symbol() ); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php if ( ! empty( $dsc_from ) && is_array( $dsc_from ) ):
							foreach ( $dsc_from as $k_dsc => $val_from ):
								$to 	= is_array( $dsc_to ) && isset( $dsc_to[$k_dsc] ) ? $dsc_to[$k_dsc] : '';
								$price 	= is_array( $dsc_price ) && isset( $dsc_price[$k_dsc] ) ? $dsc_price[$k_dsc] : '';
						?>
							<tr class="row_st_discount_distance">
								<td width="30%">
									<input
										type="text"
										class="ovabrw_st_discount_distance_from"
										name="ovabrw_st_discount_distance[<?php echo esc_attr( $k ); ?>][from][]"
										placeholder="<?php esc_html_e( 'Number', 'ova-brw' ); ?>"
										value="<?php echo esc_attr( $val_from ); ?>"
										autocomplete="off"
									/>
								</td>
								<td width="30%">
									<input
										type="text"
										class="ovabrw_st_discount_distance_to"
										name="ovabrw_st_discount_distance[<?php echo esc_attr( $k ); ?>][to][]"
										placeholder="<?php esc_html_e( 'Number', 'ova-brw' ); ?>"
										value="<?php echo esc_attr( $to ); ?>"
										autocomplete="off"
									/>
								</td>
								<td width="30%">
									<input
										type="text"
										class="ovabrw_st_discount_distance_price"
										name="ovabrw_st_discount_distance[<?php echo esc_attr( $k ); ?>][price][]"
										placeholder="<?php esc_html_e( 'Number', 'ova-brw' ); ?>"
										value="<?php echo esc_attr( $price ); ?>"
										autocomplete="off"
									/>
								</td>
								<td width="1%"><a href="#" class="button btn-remove-st-discount-distance">x</a></td>
							</tr>
						<?php endforeach; endif; ?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="6">
									<a href="#" class="button btn-add-st-discount-distance">
										<?php esc_html_e( 'Add Discount', 'ova-brw' ); ?>
									</a>
								</th>
							</tr>
						</tfoot>
					</table>
				</td>
				<td width="1%"><a href="#" class="button btn-remove-st-distance">x</a></td>
			</tr>
		<?php endforeach; endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="6">
					<a href="#" class="button btn-add-st-distance" data-row="
						<?php
							ob_start();
							include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_st_distance_field.php' );
							echo esc_attr( ob_get_clean() );
						?>
					">
					<?php esc_html_e( 'Add Special Time', 'ova-brw' ); ?></a>
					</a>
				</th>
			</tr>
		</tfoot>
	</table>
	<input
		type="hidden"
		name="ovabrw_html_st_discount_distance"
		data-html="<?php
			ob_start();
			include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_st_distance_discount.php' );
			echo esc_attr( ob_get_clean() );
		?>"
	/>
</div>