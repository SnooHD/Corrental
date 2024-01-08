<?php if ( ! defined( 'ABSPATH' ) ) exit(); ?>

<div class="wcst-title">
    <h2><?php esc_html_e( 'Heading', 'ova-brw' ); ?></h2>
    <span class="dashicons dashicons-plus-alt2 ovabrw-more"></span>
    <span class="dashicons dashicons-minus ovabrw-less"></span>
</div>
<div class="ovabrw-wcst-fields ovabrw-wcst-headding">
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="ovabrw_glb_heading_font_size">
                        <?php esc_html_e( 'Font size', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <input
                        name="ovabrw_glb_heading_font_size"
                        id="ovabrw_glb_heading_font_size"
                        type="text"
                        value="<?php echo esc_attr( get_option( 'ovabrw_glb_heading_font_size', '24px' ) ); ?>"
                        class="ovabrw_glb_heading_font_size"
                        placeholder="24px"
                        autocomplete="off"
                    />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="ovabrw_glb_heading_font_weight">
                        <?php esc_html_e( 'Font weight', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <input
                        name="ovabrw_glb_heading_font_weight"
                        id="ovabrw_glb_heading_font_weight"
                        type="text"
                        value="<?php echo esc_attr( get_option( 'ovabrw_glb_heading_font_weight', '600' ) ); ?>"
                        class="ovabrw_glb_heading_font_weight"
                        placeholder="600"
                        autocomplete="off"
                    />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="ovabrw_glb_heading_line_height">
                        <?php esc_html_e( 'Line height', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <input
                        name="ovabrw_glb_heading_line_height"
                        id="ovabrw_glb_heading_line_height"
                        type="text"
                        value="<?php echo esc_attr( get_option( 'ovabrw_glb_heading_line_height', '36px' ) ); ?>"
                        class="ovabrw_glb_heading_line_height"
                        placeholder="36px"
                        autocomplete="off"
                    />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="ovabrw_glb_heading_color">
                        <?php esc_html_e( 'Color', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-color">
                    <span class="colorpickpreview" style="background-color: <?php echo esc_attr( get_option( 'ovabrw_glb_heading_color', '#222222' ) ); ?>"></span>
                    <input
                        name="ovabrw_glb_heading_color"
                        id="ovabrw_glb_heading_color"
                        type="text"
                        dir="ltr"
                        style="<?php echo esc_attr( 'color: '. get_option( 'ovabrw_glb_heading_color', '#222222' ) ); ?>"
                        value="<?php echo esc_attr( get_option( 'ovabrw_glb_heading_color', '#222222' ) ); ?>"
                        class="colorpick"
                        placeholder="#222222"
                        autocomplete="off"
                    />
                        <div id="colorPickerDiv_ovabrw_glb_heading_color" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
                </td>
            </tr>
        </tbody>
    </table>
</div>