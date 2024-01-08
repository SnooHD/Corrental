<?php
	$time_format = ovabrw_get_time_format();
?>
<tr class="row_rt_record" data-pos="">
    <td width="12%">
    	<input type="text" class="ovabrw_rt_price" placeholder="<?php esc_html_e('10.5', 'ova-brw'); ?>" name="ovabrw_rt_price[]" value="" />
    </td>
    <td width="12%">
    	<input type="text" class="ovabrw_rt_price_hour" placeholder="<?php esc_html_e('10.5', 'ova-brw'); ?>" name="ovabrw_rt_price_hour[]" value="" />
    </td>
    <td width="16%">
      	<input
      		type="text"
      		name="ovabrw_rt_startdate[]"
      		class="ovabrw_rt_startdate ova_brw_datepicker_short datetimepicker"
      		placeholder="<?php esc_html_e( 'DD-MM-YY', 'ova-brw' ); ?>"
      		autocomplete="off"
      		value=""
      	/>
      	<input
      		type="text"
      		data-time="<?php echo $time_format; ?>"
      		name="ovabrw_rt_starttime[]"
      		class="ovabrw_rt_starttime ova_brw_datepicker_time datetimepicker"
      		placeholder="<?php esc_html_e( 'From Time', 'ova-brw' ); ?>"
      		autocomplete="off"
      		value=""
      	/>
    </td>
    <td width="16%">
      	<input
      		type="text"
      		name="ovabrw_rt_enddate[]"
      		class="ovabrw_rt_enddate ova_brw_datepicker_short datetimepicker"
      		placeholder="<?php esc_html_e( 'DD-MM-YY', 'ova-brw' ); ?>"
      		autocomplete="off"
      		value=""
      	/>
      	<input
      		type="text"
      		data-time="<?php echo $time_format; ?>"
      		name="ovabrw_rt_endtime[]"
      		class="ovabrw_rt_endtime ova_brw_datepicker_time datetimepicker"
      		placeholder="<?php esc_html_e( 'End Time', 'ova-brw' ); ?>"
      		autocomplete="off"
      		value=""
      	/>
    </td>
    <td width="43%">
    	<table width="100%" class="widefat ovabrw_rt_discount">
	      	<thead>
				<tr>
					<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'Min - Max: Number', 'ova-brw' ); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody class="real"></tbody>
			<tfoot>
				<tr>
					<th colspan="6">
						<a href="#" class="button insert_rt_discount">
							<?php esc_html_e( 'Add PST', 'ova-brw' ); ?>
							<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_rt_discount.php' ); ?>
						</a>
					</th>
				</tr>
			</tfoot>
      	</table>
    </td>
    <td width="1%"><a href="#" class="button delete_rt">x</a></td>
</tr>