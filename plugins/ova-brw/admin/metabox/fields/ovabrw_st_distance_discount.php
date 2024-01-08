<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<tr class="row_st_discount_distance">
	<td width="30%">
		<input
			type="text"
			class="ovabrw_st_discount_distance_from"
			name="ovabrw_st_discount_distance[ovabrw_key][from][]"
			placeholder="<?php esc_html_e( 'Number', 'ova-brw' ); ?>"
			autocomplete="off"
		/>
	</td>
	<td width="30%">
		<input
			type="text"
			class="ovabrw_st_discount_distance_to"
			name="ovabrw_st_discount_distance[ovabrw_key][to][]"
			placeholder="<?php esc_html_e( 'Number', 'ova-brw' ); ?>"
			autocomplete="off"
		/>
	</td>
	<td width="30%">
		<input
			type="text"
			class="ovabrw_st_discount_distance_price"
			name="ovabrw_st_discount_distance[ovabrw_key][price][]"
			placeholder="<?php esc_html_e( 'Number', 'ova-brw' ); ?>"
			autocomplete="off"
		/>
	</td>
	<td width="1%"><a href="#" class="button btn-remove-st-discount-distance">x</a></td>
</tr>