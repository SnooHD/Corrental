<tr class="tr_rt_resource">
    <td width="15%">
        <input
            type="text"
            name="ovabrw_resource_id[]"
            placeholder="<?php esc_html_e( 'Not space', 'ova-brw' ); ?>"
            value=""
        />
    </td>
    <td width="29%">
        <input
            type="text"
            name="ovabrw_resource_name[]"
            value=""
            placeholder="<?php esc_html_e( 'Name', 'ova-brw' ); ?>"
        />
    </td>
    <td width="25%">
        <input
            type="text"
            name="ovabrw_resource_price[]"
            value=""
            placeholder="<?php esc_html_e( '10.5', 'ova-brw' ); ?>"
        />
    </td>
    <td width="15%">
        <input
            type="text"
            name="ovabrw_resource_quantity[]"
            value=""
            placeholder="<?php esc_html_e( 'Number', 'ova-brw' ); ?>"
        />
    </td>
    <td width="15%">
        <select name="ovabrw_resource_duration_type[]" class="short_dura">
            <option value="hours"><?php esc_html_e( '/Hours', 'ova-brw' ); ?></option>
            <option value="days"><?php esc_html_e( '/Days', 'ova-brw' ); ?></option>
            <option value="total"><?php esc_html_e( '/Total', 'ova-brw' ); ?></option>
        </select>
    </td>
    <td width="1%"><a href="#" class="button delete_resource">x</a></td>
</tr>