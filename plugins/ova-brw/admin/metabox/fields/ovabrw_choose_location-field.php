<?php
if ( ! defined( 'ABSPATH' ) ) exit();

if ( class_exists('OVABRW') && class_exists('woocommerce') ) {
    $locaitons = ovabrw_get_locations_array();
} else {
    $locaitons = array();
}

?>
<tr class="tr_setup_loc">
    <td width="50%">
        <select name="ovabrw_st_pickup_loc[]" >
            <option value=""><?php echo esc_html__( 'Select Location', 'ova-brw' ); ?></option>
            <?php 
            if ( $locaitons ): 
                foreach ( $locaitons as $location ):
            ?>
                <option value="<?php echo esc_attr( $location ); ?>"><?php echo esc_html( $location ); ?></option>
            <?php endforeach; endif; ?>
        </select>
    </td>
    <td width="50%">
        <select name="ovabrw_st_dropoff_loc[]" disabled>
            <option value="" ><?php echo esc_html__( 'Select Location', 'ova-brw' ); ?></option>
            <?php 
            if ( $locaitons ): 
                foreach( $locaitons as $location ):
            ?>
                <option value="<?php echo esc_attr( $location ); ?>"><?php echo esc_html( $location ); ?></option>
            <?php endforeach; endif; ?>
        </select>
    </td>
    <td width="1%"><a href="#" class="button delete_st_location">x</a></td>
</tr>