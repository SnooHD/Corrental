<tr class="tr_rt_service">
    <td width="15%">
        <input
            type="text"
            name="ovabrw_service_id[ovabrw_key][]"
            placeholder="<?php esc_html_e( 'Not space', 'ova-brw' ); ?>"
            value=""
        />
    </td>
    <td width="34%">
        <input
            type="text"
            name="ovabrw_service_name[ovabrw_key][]"
            placeholder="<?php esc_html_e( 'Name', 'ova-brw' ); ?>"
            value=""
        />
    </td>
    <td width="20%">
        <input
            type="text"
            name="ovabrw_service_price[ovabrw_key][]"
            value=""
            placeholder="<?php esc_html_e( 'Price', 'ova-brw' ); ?>"
        />
    </td>
    <td width="15%">
        <input
            type="number"
            name="ovabrw_service_qty[ovabrw_key][]"
            value=""
            placeholder="<?php esc_html_e( 'Number', 'ova-brw' ); ?>"
        />
    </td>
    <td width="15%">
        <select name="ovabrw_service_duration_type[ovabrw_key][]" class="short_dura">
            <option value="hours" ><?php esc_html_e( '/Hours', 'ova-brw' ); ?></option>
            <option value="days" ><?php esc_html_e( '/Days', 'ova-brw' ); ?></option>
            <option value="total" ><?php esc_html_e( '/Total', 'ova-brw' ); ?></option>
        </select>
    </td>
    <td width="1%"><a href="#" class="button delete_service">x</a></td>
</tr>