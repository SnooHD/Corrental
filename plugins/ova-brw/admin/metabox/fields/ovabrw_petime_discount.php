<?php
	$time_format = ovabrw_get_time_format();
?>
<table class="widefat wrap_petime_discount">
	<tbody>
		<tr class="tr_petime_discount">												
			<td width="30%">
			    <input 
			    	type="text" 
			    	class="ovabrw_petime_discount_price" 
			    	placeholder="<?php esc_html_e('10.5', 'ova-brw'); ?>" 
			    	name="ovabrw_petime_discount[ovabrw_key][price][]" 
			    	value="" />
			</td>
			<td width="30%">
			    <input 
			    	type="text" 
			    	data-time="<?php echo $time_format; ?>" 
			    	class="ovabrw_petime_discount_start_time ovabrw_start_date ovabrw_datetimepicker" 
			    	placeholder="<?php esc_html_e('Start Time', 'ova-brw'); ?>" 
			    	name="ovabrw_petime_discount[ovabrw_key][start_time][]" 
			    	value="" />
			</td>
			<td width="30%">
			    <input 
			    	type="text" 
			    	data-time="<?php echo $time_format; ?>" 
			    	class="ovabrw_petime_discount_end_time ovabrw_end_date ovabrw_datetimepicker" 
			    	placeholder="<?php esc_html_e('End Time', 'ova-brw'); ?>" 
			    	name="ovabrw_petime_discount[ovabrw_key][end_time][]" 
			    	value="" />
			</td>
			<td width="9%"><a href="#" class="button delete_petime_discount">x</a></td>
		</tr> 
	</tbody>
</table>