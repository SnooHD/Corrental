<?php if ( ! defined( 'ABSPATH' ) ) exit(); ?>

<div class="wcst-title">
    <h2><?php esc_html_e( 'Color', 'ova-brw' ); ?></h2>
    <span class="dashicons dashicons-plus-alt2 ovabrw-more"></span>
    <span class="dashicons dashicons-minus ovabrw-less"></span>
</div>
<div class="ovabrw-wcst-fields ovabrw-wcst-primary-color">
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="ovabrw_glb_primary_color">
                        <?php esc_html_e( 'Primary color', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-color">
                    <span class="colorpickpreview" style="background-color: <?php echo esc_attr( get_option( 'ovabrw_glb_primary_color', '#E56E00' ) ); ?>"></span>
                    <input
                        name="ovabrw_glb_primary_color"
                        id="ovabrw_glb_primary_color"
                        type="text"
                        dir="ltr"
                        style="<?php echo esc_attr( 'color: '. get_option( 'ovabrw_glb_primary_color', '#E56E00' ) ); ?>"
                        value="<?php echo esc_attr( get_option( 'ovabrw_glb_primary_color', '#E56E00' ) ); ?>"
                        class="colorpick"
                        placeholder="#E56E00"
                        autocomplete="off"
                    />
                        <div id="colorPickerDiv_ovabrw_glb_primary_color" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="ovabrw_glb_light_color">
                        <?php esc_html_e( 'Light color', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-color">
                    <span class="colorpickpreview" style="background-color: <?php echo esc_attr( get_option( 'ovabrw_glb_light_color', '#C3C3C3' ) ); ?>"></span>
                    <input
                        name="ovabrw_glb_light_color"
                        id="ovabrw_glb_light_color"
                        type="text"
                        dir="ltr"
                        style="<?php echo esc_attr( 'color: '. get_option( 'ovabrw_glb_light_color', '#C3C3C3' ) ); ?>"
                        value="<?php echo esc_attr( get_option( 'ovabrw_glb_light_color', '#C3C3C3' ) ); ?>"
                        class="colorpick"
                        placeholder="#C3C3C3"
                        autocomplete="off"
                    />
                        <div id="colorPickerDiv_ovabrw_glb_light_color" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
                </td>
            </tr>
        </tbody>
    </table>
</div>