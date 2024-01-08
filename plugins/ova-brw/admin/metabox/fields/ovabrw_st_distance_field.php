<?php if ( ! defined( 'ABSPATH' ) ) exit;
	$time_format = ovabrw_get_time_format_php();
	$date_format = ovabrw_get_date_format();
?>

<tr class="row_st_distance" data-index="">
	<td width="20%">
		<input
			type="text"
			name="ovabrw_st_pickup_distance[]"
			class="ovabrw_datetimepicker"
			placeholder="<?php echo esc_attr( $date_format . ' ' . $time_format ); ?>"
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
			autocomplete="off"
	      	onfocus="blur();"
		/>
	</td>
	<td width="14%">
		<input
			type="text"
			name="ovabrw_st_price_distance[]"
			placeholder="<?php esc_html_e( 'Number', 'ova-brw' ); ?>"
			autocomplete="off"
		/>
	</td>
	<td width="45%">
		<table width="100%" class="widefat ovabrw_st_discount_distance">
			<thead>
				<tr>
					<th><?php esc_html_e( 'From (km)', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'To (km)', 'ova-brw' ); ?></th>
					<th><?php printf( esc_html__( 'Price (%s)', 'ova-brw' ), get_woocommerce_currency_symbol() ); ?></th>
				</tr>
			</thead>
			<tbody></tbody>
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