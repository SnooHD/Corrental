<?php if ( ! defined( 'ABSPATH' ) ) exit();
	$all_font 		= ovabrw_get_all_font();
	$default_font 	= apply_filters( 'ovabrw_glb_default_font', 'Poppins' );
	$current_font 	= get_option( 'ovabrw_glb_primary_font', $default_font );
	$font_weight 	= get_option( 'ovabrw_glb_primary_font_weight', array(
		"100",
        "100italic",
        "200",
        "200italic",
        "300",
        "300italic",
        "regular",
        "italic",
        "500",
        "500italic",
        "600",
        "600italic",
        "700",
        "700italic",
        "800",
        "800italic",
        "900",
        "900italic",
	));
?>

<div class="wcst-title">
    <h2><?php esc_html_e( 'Font', 'ova-brw' ); ?></h2>
    <span class="dashicons dashicons-plus-alt2 ovabrw-more"></span>
    <span class="dashicons dashicons-minus ovabrw-less"></span>
</div>
<div class="ovabrw-wcst-fields ovabrw-wcst-primary-font">
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="ovabrw_glb_primary_font">
                        <?php esc_html_e( 'Primary font', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-select">
                    <select
                        name="ovabrw_glb_primary_font"
                        id="ovabrw_glb_primary_font"
                        class="ovabrw_select2"
                        data-placeholder="<?php esc_attr_e( 'Select font', 'ova-brw' ); ?>"
                    >
                        <option value=""></option>
                        <?php if ( ! empty( $all_font ) && is_array( $all_font ) ):
                            // Font Weight
                            $data_font_weight = [];

                            foreach ( $all_font as $font ):
                                $data_font_weight[$font->family] = $font->variants;
                        ?>
                            <option
                                value="<?php echo esc_attr( $font->family ); ?>"
                                data-variants="<?php echo esc_attr( json_encode( $font->variants ) ); ?>"
                                <?php selected( $current_font, $font->family ); ?>>
                                <?php echo esc_html( $font->family ); ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="ovabrw_glb_primary_font_weight">
                        <?php esc_html_e( 'Load font weight', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-select">
                    <select
                        name="ovabrw_glb_primary_font_weight[]"
                        id="ovabrw_glb_primary_font_weight"
                        class="ovabrw_select2"
                        data-placeholder="<?php esc_attr_e( 'Select font weight', 'ova-brw' ); ?>"
                        multiple
                    >
                    <?php if ( isset( $data_font_weight[$current_font] ) && ! empty( $data_font_weight[$current_font] ) && is_array( $data_font_weight[$current_font] ) ):
                        foreach ( $data_font_weight[$current_font] as $weight ):
                            $selected = '';

                            if ( ! empty( $font_weight ) && is_array( $font_weight ) && in_array( $weight , $font_weight ) ) {
                                $selected = ' selected';
                            }
                    ?>
                            <option value="<?php echo esc_attr( $weight ); ?>"<?php echo esc_html( $selected ); ?>>
                                <?php echo esc_html( $weight ); ?>
                            </option>
                    <?php endforeach; endif; ?>
                    </select>
                    <button class="button ovabrw_glb_select_all_font_weight">
                        <?php esc_html_e( 'select all', 'ova-brw' ); ?>
                    </button>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="ovabrw_glb_custom_font">
                        <?php esc_html_e( 'Custom font', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-select">
                    <p class="description">
                        <?php printf( esc_html__( 'Step 1: Insert font-face in style.css file: Refer %s.', 'ova-brw' ), '<a href="https://www.w3schools.com/cssref/css3_pr_font-face_rule.asp" target="_blank">https://www.w3schools.com/cssref/css3_pr_font-face_rule.asp</a>' ); ?>
                    </p>
                    <p class="description">
                        <?php esc_html_e( 'Step 2: Insert font-family and font-weight like format: ["Perpetua", "Regular:Bold:Italic:Light"] | ["Name-Font", "Regular:Bold:Italic:Light"].', 'ova-brw' ); ?>
                    </p>
                    <p class="description">
                        <?php esc_html_e( 'Step 3: Refresh customize page to display new font in dropdown font field.', 'ova-brw' ); ?>
                    </p>
                    <br/>
                    <textarea name="ovabrw_glb_custom_font" id="ovabrw_glb_custom_font" rows="7"><?php echo esc_html( str_replace( '\"', '"', get_option( 'ovabrw_glb_custom_font' ) ) ); ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>
</div>