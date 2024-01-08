<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<tr class="tr_rt_extra_time">
    <td width="20%">
        <input
            type="text"
            name="ovabrw_extra_time_hour[]" 
            value=""
            placeholder="<?php esc_attr_e( 'Number', 'ova-brw' ); ?>"
            autocomplete="off"
        />
    </td>
    <td width="20%">
        <input
            type="text"
            name="ovabrw_extra_time_label[]"
            value=""
            placeholder="<?php esc_attr_e( 'Text', 'ova-brw' ); ?>"
            autocomplete="off"
        />
    </td>
    <td width="20%">
        <input
            type="text"
            name="ovabrw_extra_time_price[]"
            value=""
            placeholder="<?php esc_attr_e( 'Number', 'ova-brw' ); ?>"
            autocomplete="off"
        />
    </td>
    <td width="1%"><a href="#" class="button remove_extra_time">x</a></td>
</tr>