<?php if ( ! defined( 'ABSPATH' ) ) exit(); ?>

<div class="wcst-title">
    <h2><?php esc_html_e( 'Text', 'ova-brw' ); ?></h2>
    <span class="dashicons dashicons-plus-alt2 ovabrw-more"></span>
    <span class="dashicons dashicons-minus ovabrw-less"></span>
</div>
<div class="ovabrw-wcst-fields ovabrw-wcst-text">
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="ovabrw_glb_text_font_size">
                        <?php esc_html_e( 'Font size', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <input
                        name="ovabrw_glb_text_font_size"
                        id="ovabrw_glb_text_font_size"
                        type="text"
                        value="<?php echo esc_attr( get_option( 'ovabrw_glb_text_font_size', '14px' ) ); ?>"
                        class="ovabrw_glb_text_font_size"
                        placeholder="14px"
                        autocomplete="off"
                    />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="ovabrw_glb_text_font_weight">
                        <?php esc_html_e( 'Font weight', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <input
                        name="ovabrw_glb_text_font_weight"
                        id="ovabrw_glb_text_font_weight"
                        type="text"
                        value="<?php echo esc_attr( get_option( 'ovabrw_glb_text_font_weight', '400' ) ); ?>"
                        class="ovabrw_glb_text_font_weight"
                        placeholder="400"
                        autocomplete="off"
                    />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="ovabrw_glb_text_line_height">
                        <?php esc_html_e( 'Line height', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <input
                        name="ovabrw_glb_text_line_height"
                        id="ovabrw_glb_text_line_height"
                        type="text"
                        value="<?php echo esc_attr( get_option( 'ovabrw_glb_text_line_height', '22px' ) ); ?>"
                        class="ovabrw_glb_text_line_height"
                        placeholder="22px"
                        autocomplete="off"
                    />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="ovabrw_glb_text_color">
                        <?php esc_html_e( 'Color', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-color">
                    <span class="colorpickpreview" style="background-color: <?php echo esc_attr( get_option( 'ovabrw_glb_text_color', '#555555' ) ); ?>"></span>
                    <input
                        name="ovabrw_glb_text_color"
                        id="ovabrw_glb_text_color"
                        type="text"
                        dir="ltr"
                        style="<?php echo esc_attr( 'color: '. get_option( 'ovabrw_glb_text_color', '#555555' ) ); ?>"
                        value="<?php echo esc_attr( get_option( 'ovabrw_glb_text_color', '#555555' ) ); ?>"
                        class="colorpick"
                        placeholder="#555555"
                        autocomplete="off"
                    />
                        <div id="colorPickerDiv_ovabrw_glb_text_color" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
                </td>
            </tr>
        </tbody>
    </table>
</div>