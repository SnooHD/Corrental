<?php
    $time_format        = ovabrw_get_time_format();
    $date_format        = ovabrw_get_date_format();
    $disable_week_day   = get_post_meta( $post_id, 'ovabrw_product_disable_week_day', true );
?>
<tr class="tr_rt_untime">
    <td width="20%">
        <input 
            data-time="<?php echo $time_format; ?>" 
            type="text" 
            name="ovabrw_untime_startdate[]" 
            value="" 
            placeholder="<?php echo esc_html( $date_format ); ?>" 
            class="unavailable_time start_date" autocomplete="off" 
            onfocus="blur();" 
            data-disable-week-day="<?php echo esc_attr( $disable_week_day ); ?>" />
    </td>
    <td width="20%">
        <input 
            data-time="<?php echo $time_format; ?>" 
            type="text" name="ovabrw_untime_enddate[]" 
            value="" 
            placeholder="<?php echo esc_html( $date_format ); ?>" 
            class="unavailable_time end_date" 
            autocomplete="off" 
            onfocus="blur();" 
            data-disable-week-day="<?php echo esc_attr( $disable_week_day ); ?>" />
    </td>
    <td width="1%"><a href="#" class="button delete_untime">x</a></td>
</tr>