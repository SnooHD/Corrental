<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<tr class="tr_rt_discount_distance">
    <td width="30%">
        <input
            type="text"
            name="ovabrw_discount_distance_from[]" 
            value=""
            placeholder="<?php esc_attr_e( 'Number', 'ova-brw' ); ?>"
            autocomplete="off"
        />
    </td>
    <td width="30%">
        <input
            type="text"
            name="ovabrw_discount_distance_to[]" 
            value=""
            placeholder="<?php esc_attr_e( 'Number', 'ova-brw' ); ?>"
            autocomplete="off"
        />
    </td>
    <td width="30%">
        <input
            type="text"
            name="ovabrw_discount_distance_price[]" 
            value=""
            placeholder="<?php esc_attr_e( 'Number', 'ova-brw' ); ?>"
            autocomplete="off"
        />
    </td>
    <td width="1%"><a href="#" class="button remove_discount_distance">x</a></td>
</tr>