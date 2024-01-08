<?php if ( ! defined( 'ABSPATH' ) ) exit(); ?>

<div class="wcst-title">
    <h2><?php esc_html_e( 'Card', 'ova-brw' ); ?></h2>
    <span class="dashicons dashicons-plus-alt2 ovabrw-more"></span>
    <span class="dashicons dashicons-minus ovabrw-less"></span>
</div>
<div class="ovabrw-wcst-fields ovabrw-wcst-card">
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="ovabrw_glb_card_template">
                        <?php esc_html_e( 'Template', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-select">
                    <select
                        name="ovabrw_glb_card_template"
                        id="ovabrw_glb_card_template"
                        class="ovabrw_select2"
                        data-placeholder="">
                        <option value="card1"<?php selected( get_option( 'ovabrw_glb_card_template' ), 'card1' ); ?>>
                            <?php esc_html_e( 'Card 1', 'ova-brw' ); ?>
                        </option>
                        <option value="card2"<?php selected( get_option( 'ovabrw_glb_card_template' ), 'card2' ); ?>>
                            <?php esc_html_e( 'Card 2', 'ova-brw' ); ?>
                        </option>
                        <option value="card3"<?php selected( get_option( 'ovabrw_glb_card_template' ), 'card3' ); ?>>
                            <?php esc_html_e( 'Card 3', 'ova-brw' ); ?>
                        </option>
                        <option value="card4"<?php selected( get_option( 'ovabrw_glb_card_template' ), 'card4' ); ?>>
                            <?php esc_html_e( 'Card 4', 'ova-brw' ); ?>
                        </option>
                        <option value="card5"<?php selected( get_option( 'ovabrw_glb_card_template' ), 'card5' ); ?>>
                            <?php esc_html_e( 'Card 5', 'ova-brw' ); ?>
                        </option>
                        <option value="card6"<?php selected( get_option( 'ovabrw_glb_card_template' ), 'card6' ); ?>>
                            <?php esc_html_e( 'Card 6', 'ova-brw' ); ?>
                        </option>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="ovabrw-wcst-tabs">
        <div class="ovabrw-wcst-tab-nav">
            <a href="#card1" data-id="card1" class="ovabrw-wcst-tab-btn active">
                <?php esc_html_e( 'Card 1', 'ova-brw' ); ?>
            </a>
            <a href="#card2" data-id="card2" class="ovabrw-wcst-tab-btn">
                <?php esc_html_e( 'Card 2', 'ova-brw' ); ?>
            </a>
            <a href="#card3" data-id="card3" class="ovabrw-wcst-tab-btn">
                <?php esc_html_e( 'Card 3', 'ova-brw' ); ?>
            </a>
            <a href="#card4" data-id="card4" class="ovabrw-wcst-tab-btn">
                <?php esc_html_e( 'Card 4', 'ova-brw' ); ?>
            </a>
            <a href="#card5" data-id="card5" class="ovabrw-wcst-tab-btn">
                <?php esc_html_e( 'Card 5', 'ova-brw' ); ?>
            </a>
            <a href="#card6" data-id="card6" class="ovabrw-wcst-tab-btn">
                <?php esc_html_e( 'Card 6', 'ova-brw' ); ?>
            </a>
        </div>
        <div class="ovabrw-wcst-tab-content">
            <?php for ( $i = 1; $i <= 6; $i++ ): $id = 'card'.$i; ?>
                <table class="form-table<?php echo $i == 1 ? ' active': ''; ?>" data-id="<?php echo esc_attr( $id ); ?>">
                    <tbody>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="ovabrw_glb_<?php echo esc_attr( $id ); ?>_featured">
                                    <?php esc_html_e( 'Show highlight', 'ova-brw' ); ?>
                                </label>
                            </th>
                            <td class="forminp forminp-select">
                                <select
                                    name="ovabrw_glb_<?php echo esc_attr( $id ); ?>_featured"
                                    id="ovabrw_glb_<?php echo esc_attr( $id ); ?>_featured">
                                    <option value="yes"<?php selected( get_option( 'ovabrw_glb_'.$id.'_featured', 'yes' ), 'yes' ); ?>>
                                        <?php esc_html_e( 'Yes', 'ova-brw' ); ?>
                                    </option>
                                    <option value="no"<?php selected( get_option( 'ovabrw_glb_'.$id.'_featured', 'yes' ), 'no' ); ?>>
                                        <?php esc_html_e( 'No', 'ova-brw' ); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="ovabrw_glb_<?php echo esc_attr( $id ); ?>_feature_featured">
                                    <?php esc_html_e( 'Show special', 'ova-brw' ); ?>
                                </label>
                            </th>
                            <td class="forminp forminp-select">
                                <select
                                    name="ovabrw_glb_<?php echo esc_attr( $id ); ?>_feature_featured"
                                    id="ovabrw_glb_<?php echo esc_attr( $id ); ?>_feature_featured">
                                    <option value="yes"<?php selected( get_option( 'ovabrw_glb_'.$id.'_feature_featured', 'yes' ), 'yes' ); ?>>
                                        <?php esc_html_e( 'Yes', 'ova-brw' ); ?>
                                    </option>
                                    <option value="no"<?php selected( get_option( 'ovabrw_glb_'.$id.'_feature_featured', 'yes' ), 'no' ); ?>>
                                        <?php esc_html_e( 'No', 'ova-brw' ); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="ovabrw_glb_<?php echo esc_attr( $id ); ?>_thumbnail_type">
                                    <?php esc_html_e( 'Thumbnail Type', 'ova-brw' ); ?>
                                </label>
                            </th>
                            <td class="forminp forminp-select">
                                <select
                                    name="ovabrw_glb_<?php echo esc_attr( $id ); ?>_thumbnail_type"
                                    id="ovabrw_glb_<?php echo esc_attr( $id ); ?>_thumbnail_type">
                                    <option value="slider"<?php selected( get_option( 'ovabrw_glb_'.$id.'_thumbnail_type', 'slider' ), 'slider' ); ?>>
                                        <?php esc_html_e( 'Slider', 'ova-brw' ); ?>
                                    </option>
                                    <option value="image"<?php selected( get_option( 'ovabrw_glb_'.$id.'_thumbnail_type', 'slider' ), 'image' ); ?>>
                                        <?php esc_html_e( 'Image', 'ova-brw' ); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="ovabrw_glb_<?php echo esc_attr( $id ); ?>_price">
                                    <?php esc_html_e( 'Show price', 'ova-brw' ); ?>
                                </label>
                            </th>
                            <td class="forminp forminp-select">
                                <select
                                    name="ovabrw_glb_<?php echo esc_attr( $id ); ?>_price"
                                    id="ovabrw_glb_<?php echo esc_attr( $id ); ?>_price">
                                    <option value="yes"<?php selected( get_option( 'ovabrw_glb_'.$id.'_price', 'yes' ), 'yes' ); ?>>
                                        <?php esc_html_e( 'Yes', 'ova-brw' ); ?>
                                    </option>
                                    <option value="no"<?php selected( get_option( 'ovabrw_glb_'.$id.'_price', 'yes' ), 'no' ); ?>>
                                        <?php esc_html_e( 'No', 'ova-brw' ); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="ovabrw_glb_<?php echo esc_attr( $id ); ?>_features">
                                    <?php esc_html_e( 'Show features', 'ova-brw' ); ?>
                                </label>
                            </th>
                            <td class="forminp forminp-select">
                                <select
                                    name="ovabrw_glb_<?php echo esc_attr( $id ); ?>_features"
                                    id="ovabrw_glb_<?php echo esc_attr( $id ); ?>_features">
                                    <option value="yes"<?php selected( get_option( 'ovabrw_glb_'.$id.'_features', 'yes' ), 'yes' ); ?>>
                                        <?php esc_html_e( 'Yes', 'ova-brw' ); ?>
                                    </option>
                                    <option value="no"<?php selected( get_option( 'ovabrw_glb_'.$id.'_features', 'yes' ), 'no' ); ?>>
                                        <?php esc_html_e( 'No', 'ova-brw' ); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="ovabrw_glb_<?php echo esc_attr( $id ); ?>_custom_taxonomy">
                                    <?php esc_html_e( 'Show Custom Taxonomy', 'ova-brw' ); ?>
                                </label>
                            </th>
                            <td class="forminp forminp-select">
                                <select
                                    name="ovabrw_glb_<?php echo esc_attr( $id ); ?>_custom_taxonomy"
                                    id="ovabrw_glb_<?php echo esc_attr( $id ); ?>_custom_taxonomy">
                                    <option value="yes"<?php selected( get_option( 'ovabrw_glb_'.$id.'_custom_taxonomy', 'yes' ), 'yes' ); ?>>
                                        <?php esc_html_e( 'Yes', 'ova-brw' ); ?>
                                    </option>
                                    <option value="no"<?php selected( get_option( 'ovabrw_glb_'.$id.'_custom_taxonomy', 'yes' ), 'no' ); ?>>
                                        <?php esc_html_e( 'No', 'ova-brw' ); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="ovabrw_glb_<?php echo esc_attr( $id ); ?>_attribute">
                                    <?php esc_html_e( 'Show attribute', 'ova-brw' ); ?>
                                </label>
                            </th>
                            <td class="forminp forminp-select">
                                <select
                                    name="ovabrw_glb_<?php echo esc_attr( $id ); ?>_attribute"
                                    id="ovabrw_glb_<?php echo esc_attr( $id ); ?>_attribute">
                                    <option value="yes"<?php selected( get_option( 'ovabrw_glb_'.$id.'_attribute', 'yes' ), 'yes' ); ?>>
                                        <?php esc_html_e( 'Yes', 'ova-brw' ); ?>
                                    </option>
                                    <option value="no"<?php selected( get_option( 'ovabrw_glb_'.$id.'_attribute', 'yes' ), 'no' ); ?>>
                                        <?php esc_html_e( 'No', 'ova-brw' ); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="ovabrw_glb_<?php echo esc_attr( $id ); ?>_short_description">
                                    <?php esc_html_e( 'Show short description', 'ova-brw' ); ?>
                                </label>
                            </th>
                            <td class="forminp forminp-select">
                                <select
                                    name="ovabrw_glb_<?php echo esc_attr( $id ); ?>_short_description"
                                    id="ovabrw_glb_<?php echo esc_attr( $id ); ?>_short_description">
                                    <option value="yes"<?php selected( get_option( 'ovabrw_glb_'.$id.'_short_description', 'yes' ), 'yes' ); ?>>
                                        <?php esc_html_e( 'Yes', 'ova-brw' ); ?>
                                    </option>
                                    <option value="no"<?php selected( get_option( 'ovabrw_glb_'.$id.'_short_description', 'yes' ), 'no' ); ?>>
                                        <?php esc_html_e( 'No', 'ova-brw' ); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="ovabrw_glb_<?php echo esc_attr( $id ); ?>_review">
                                    <?php esc_html_e( 'Show review', 'ova-brw' ); ?>
                                </label>
                            </th>
                            <td class="forminp forminp-select">
                                <select
                                    name="ovabrw_glb_<?php echo esc_attr( $id ); ?>_review"
                                    id="ovabrw_glb_<?php echo esc_attr( $id ); ?>_review">
                                    <option value="yes"<?php selected( get_option( 'ovabrw_glb_'.$id.'_review', 'yes' ), 'yes' ); ?>>
                                        <?php esc_html_e( 'Yes', 'ova-brw' ); ?>
                                    </option>
                                    <option value="no"<?php selected( get_option( 'ovabrw_glb_'.$id.'_review', 'yes' ), 'no' ); ?>>
                                        <?php esc_html_e( 'No', 'ova-brw' ); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="ovabrw_glb_<?php echo esc_attr( $id ); ?>_button">
                                    <?php esc_html_e( 'Show button', 'ova-brw' ); ?>
                                </label>
                            </th>
                            <td class="forminp forminp-select">
                                <select
                                    name="ovabrw_glb_<?php echo esc_attr( $id ); ?>_button"
                                    id="ovabrw_glb_<?php echo esc_attr( $id ); ?>_button">
                                    <option value="yes"<?php selected( get_option( 'ovabrw_glb_'.$id.'_button', 'yes' ), 'yes' ); ?>>
                                        <?php esc_html_e( 'Yes', 'ova-brw' ); ?>
                                    </option>
                                    <option value="no"<?php selected( get_option( 'ovabrw_glb_'.$id.'_button', 'yes' ), 'no' ); ?>>
                                        <?php esc_html_e( 'No', 'ova-brw' ); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php endfor; ?>
        </div>
    </div>
</div>