<?php if ( !defined( 'ABSPATH' ) ) exit();

//Product Cat Create page
add_action( 'product_cat_add_form_fields', 'ovabrw_taxonomy_add_new_meta_field', 10, 1 );
if ( ! function_exists( 'ovabrw_taxonomy_add_new_meta_field' ) ) {
    function ovabrw_taxonomy_add_new_meta_field( $array ) {
    ?>
        <!-- Display category -->
        <div class="form-field">
            <label for="Display"><?php esc_html_e('Display', 'ova-brw'); ?></label>
            <select name="ovabrw_cat_dis">
                <option value="rental"><?php esc_html_e( 'Rental', 'ova-brw' ); ?></option>
                <option value="shop"><?php esc_html_e( 'Shop', 'ova-brw' ); ?></option>
            </select>
        </div>
        <!-- Custom Taxonomy -->
        <div class="form-field">
            <label>
                <?php esc_html_e('Choose Taxonomies', 'ova-brw'); ?>
            </label>
            <select name="ovabrw_custom_tax[]" multiple="multiple" class="ovabrw_custom_tax_with_cat">
                <?php 
                    $list_fields = get_option( 'ovabrw_custom_taxonomy', array() );

                    if ( ! empty( $list_fields ) ) {
                        foreach( $list_fields as $slug => $field ) {
                            $name           = array_key_exists( 'name', $field ) ? $field['name'] : '';
                            $singular_name  = array_key_exists( 'singular_name', $field ) ? $field['singular_name'] : '';
                            ?>
                                <option value="<?php echo $slug; ?>">
                                    <?php echo $name; ?>
                                </option>
                            <?php
                        }
                    }
                ?>
            </select>
        </div>
        <!-- Custom Checkout Field -->
        <div class="form-field">
            <div class="choose_custom_checkout_field">
                <label>
                    <?php esc_html_e('Choose Custom Checkout Field in booking form', 'ova-brw'); ?>
                </label>
                <select name="ovabrw_choose_custom_checkout_field" id="">
                    <option value="all"><?php esc_html_e( 'All', 'ova-brw' ); ?></option>
                    <option value="special"><?php esc_html_e( 'Special', 'ova-brw' ); ?></option>
                </select>
            </div>
            <div id="special_cus_fields" class="show_special_checkout_field">
                <br>
                <label>
                    <?php esc_html_e('Choose Special Checkout Field in booking form', 'ova-brw'); ?>
                </label>
                <select name="ovabrw_custom_checkout_field[]" multiple="multiple" class="ovabrw_custom_tax_with_cat">
                    <?php 
                        $list_fields = get_option( 'ovabrw_booking_form', array() );

                        if ( !empty( $list_fields ) ) {
                            foreach( $list_fields as $slug => $field ) {
                                $label = array_key_exists( 'label', $field ) ? $field['label'] : '';
                                ?>
                                    <option value="<?php echo $slug; ?>">
                                        <?php echo $label; ?>
                                    </option>
                                <?php
                            }
                        }
                    ?>
                </select>
            </div>
        </div>
        <!-- Show Location in booking form -->
        <div class="form-field">
            <label>
                <?php esc_html_e('Show Location in booking form', 'ova-brw'); ?>
            </label>
            <select name="ovabrw_show_loc_booking_form[]" multiple="multiple" class="ovabrw_custom_tax_with_cat">
                <?php 
                    $list_fields = array( 'pickup_loc' => esc_html__( 'Pick-up Location', 'ova-brw' ), 'dropoff_loc' => esc_html__( 'Drop-off Location', 'ova-brw' ) );

                    if ( ! empty( $list_fields ) ) {
                        foreach( $list_fields as $slug => $value ) {
                            ?>
                                <option value="<?php echo $slug; ?>">
                                    <?php echo $value; ?>
                                </option>
                            <?php
                        }
                    }
                ?>
            </select>
            <br/>
            <span>
                <?php esc_html_e( 'If empty will get value in WooCommerce >> Settings >> Booking & Rental >> Booking Form', 'ova-brw' ); ?>
            </span>
        </div>
        <!-- Label Pick-up Date -->
        <div class="form-field">
            <label><?php esc_html_e('Label Pick-up Date', 'ova-brw'); ?></label>
            <input name="ovabrw_lable_pickup_date" id="ovabrw_lable_pickup_date" type="text" value="" size="40">
            <p><?php esc_html_e( 'Insert label Pick-up Date. Example: Pick-up Date, Check-in', 'ova-brw' ); ?></p>
        </div>
        <!-- Label Drop-off Date -->
        <div class="form-field">
            <label><?php esc_html_e('Label Drop-off Date', 'ova-brw'); ?></label>
            <input name="ovabrw_lable_dropoff_date" id="ovabrw_lable_dropoff_date" type="text" value="" size="40">
            <p><?php esc_html_e( 'Insert label Drop-off Date. Example: Drop-off Date, Check-out', 'ova-brw' ); ?></p>
        </div>
        <!-- Product Templates -->
        <div class="form-field">
            <label><?php esc_html_e('Product template', 'ova-brw'); ?></label>
            <?php
                // Get templates from elementor
                $global_template    = get_option( 'ova_brw_template_elementor_template', 'default' );
                $templates          = get_posts( array('post_type' => 'elementor_library', 'meta_key' => '_elementor_template_type', 'meta_value' => 'page' ) );
                $list_templates     = '<option value="global" selected="selected">' . esc_html__( 'Global', 'ova-brw' ) . '</option>';

                if ( !empty( $templates ) ) {
                    foreach( $templates as $template ) {
                        $id_template    = $template->ID;

                        if ( $global_template == $id_template ) {
                            continue;
                        }

                        $title_template = $template->post_title;
                        $list_templates .= '<option value="' . $id_template . '">' . $title_template . '</option>';
                    }
                }
            ?>
            <select id="ovabrw_product_templates" name="ovabrw_product_templates">
                <?php echo $list_templates; ?>
            </select>
            <p><?php esc_html_e( 'Global (WooCommerce >> Settings >> Booking & Rental >> Product Template) or Other (made in Templates of Elementor )', 'ova-brw' ); ?></p>
        </div>
        <?php
        // Card
        if ( ovabrw_global_typography() ): ?>
            <div class="form-field">
                <label><?php esc_html_e('Card template', 'ova-brw'); ?></label>
                <select name="ovabrw_card_template">
                    <option value="">
                        <?php esc_html_e( 'Select card', 'ova-brw' ); ?>
                    </option>
                    <option value="card1">
                        <?php esc_html_e( 'Card 1', 'ova-brw' ); ?>
                    </option>
                    <option value="card2">
                        <?php esc_html_e( 'Card 2', 'ova-brw' ); ?>
                    </option>
                    <option value="card3">
                        <?php esc_html_e( 'Card 3', 'ova-brw' ); ?>
                    </option>
                    <option value="card4">
                        <?php esc_html_e( 'Card 4', 'ova-brw' ); ?>
                    </option>
                    <option value="card5">
                        <?php esc_html_e( 'Card 5', 'ova-brw' ); ?>
                    </option>
                    <option value="card6">
                        <?php esc_html_e( 'Card 6', 'ova-brw' ); ?>
                    </option>
                </select>
            </div>
        <?php endif;
    }
}

//Product Cat Edit page
add_action( 'product_cat_edit_form_fields', 'ovabrw_taxonomy_edit_meta_field', 10, 1 );
if ( ! function_exists( 'ovabrw_taxonomy_edit_meta_field' ) ) {
    function ovabrw_taxonomy_edit_meta_field( $term ) {
        //getting term ID
        $term_id = $term->term_id;

        // retrieve the existing value(s) for this meta field.
        $ovabrw_cat_dis = get_term_meta($term_id, 'ovabrw_cat_dis', true);

        $ovabrw_custom_tax = get_term_meta($term_id, 'ovabrw_custom_tax', true);
        if ( empty( $ovabrw_custom_tax ) ) {
            $ovabrw_custom_tax = array();
        }

        $ovabrw_custom_checkout_field = get_term_meta($term_id, 'ovabrw_custom_checkout_field', true);
        if ( empty( $ovabrw_custom_checkout_field ) ) {
            $ovabrw_custom_checkout_field = array();
        }
        
        $ovabrw_show_loc_booking_form = get_term_meta($term_id, 'ovabrw_show_loc_booking_form', true);
        if ( empty( $ovabrw_show_loc_booking_form ) ) {
            $ovabrw_show_loc_booking_form = array();
        }

        $ovabrw_choose_custom_checkout_field = get_term_meta($term_id, 'ovabrw_choose_custom_checkout_field', true);

        // Get pick-up, drop-off date
        $ovabrw_lable_pickup_date   = get_term_meta( $term_id, 'ovabrw_lable_pickup_date', true );
        $ovabrw_lable_dropoff_date  = get_term_meta( $term_id, 'ovabrw_lable_dropoff_date', true );

        // Get product template
        $global_template            = get_option( 'ova_brw_template_elementor_template', 'default' );
        $product_template           = get_term_meta( $term_id, 'ovabrw_product_templates', true );
        $templates                  = get_posts( array('post_type' => 'elementor_library', 'meta_key' => '_elementor_template_type', 'meta_value' => 'page' ) );
        $list_templates             = array();
        $list_templates['global']  = esc_html__( 'Global', 'ova-brw' );

        if ( !empty( $templates ) ) {
            foreach( $templates as $template ) {
                $id_template = $template->ID;

                if ( $global_template == $id_template ) {
                    continue;
                }

                $title_template = $template->post_title;
                $list_templates[$id_template] = $title_template;
            }
        }

        $html_templates = '';
        foreach( $list_templates as $id => $title ) {
            if ( $id == $product_template ) {
                $html_templates .= '<option value="' . $id . '" selected="selected">' . $title . '</option>';
            } else {
                $html_templates .= '<option value="' . $id . '">' . $title . '</option>';
            }
        }

        // Card
        $card_template = get_term_meta( $term_id, 'ovabrw_card_template', true );
        ?>
        <!-- Display category -->
        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="ovabrw_cat_dis">
                    <?php esc_html_e('Display', 'ova-brw'); ?>
                </label>
            </th>
            <td>
                <select name="ovabrw_cat_dis">
                    <option value="rental" <?php echo ( esc_attr($ovabrw_cat_dis) == 'rental' ) ? 'selected' : ''; ?>>
                        <?php esc_html_e( 'Rental', 'ova-brw' ); ?>
                    </option>
                    <option value="shop" <?php echo ( esc_attr($ovabrw_cat_dis) == 'shop' ) ? 'selected' : ''; ?>>
                        <?php esc_html_e( 'Shop', 'ova-brw' ); ?>
                    </option>
                </select>
            </td>
        </tr>
        <!-- Custom Taxonomy -->
        <tr class="form-field">
            <th scope="row" valign="top">
                <label>
                    <?php esc_html_e('Choose Taxonomies', 'ova-brw'); ?>
                </label>
            </th>
            <td>
                <select name="ovabrw_custom_tax[]" multiple="multiple" class="ovabrw_custom_tax_with_cat">
                    <?php 
                        $list_fields = get_option( 'ovabrw_custom_taxonomy', array() );

                        if ( ! empty( $list_fields ) ) {
                            foreach( $list_fields as $slug => $field ) {
                                $name           = array_key_exists( 'name', $field ) ? $field['name'] : '';
                                $singular_name  = array_key_exists( 'singular_name', $field ) ? $field['singular_name'] : '';
                                $selected       = in_array( $slug, $ovabrw_custom_tax) ? ' selected' : '';
                                ?>
                                    <option value="<?php echo $slug; ?>" <?php echo ' '.$selected; ?>>
                                        <?php echo $name; ?>
                                    </option>
                                <?php

                            }
                        }

                    ?>
                </select>
            </td>
        </tr>
        <!-- Custom Checkout Field -->
        <tr class="form-field choose_custom_checkout_field">
            <th scope="row" valign="top">
              <label>
                <?php esc_html_e('Choose Custom Checkout Field in booking form', 'ova-brw'); ?>
            </label>
            </th>
            <td>
                <select name="ovabrw_choose_custom_checkout_field" id="">
                    <option value="all" <?php echo ( $ovabrw_choose_custom_checkout_field == 'all' ) ? 'selected' : '' ?> ><?php esc_html_e( 'All', 'ova-brw' ); ?></option>
                    <option value="special" <?php echo ( $ovabrw_choose_custom_checkout_field == 'special' ) ? 'selected' : '' ?>><?php esc_html_e( 'Special', 'ova-brw' ); ?></option>
                </select>
            </td>
        </tr>
        <tr class="form-field show_special_checkout_field">
            <th scope="row" valign="top">
                <label>
                    <?php esc_html_e('Choose Special Checkout Field in booking form', 'ova-brw'); ?>
                </label>
            </th>
            <td>
                <select name="ovabrw_custom_checkout_field[]" multiple="multiple" class="ovabrw_custom_tax_with_cat">
                    <?php 
                        $list_fields = get_option( 'ovabrw_booking_form', array() );

                        if ( ! empty( $list_fields ) ) {
                            foreach( $list_fields as $slug => $field ) {
                                $label      = array_key_exists( 'label', $field ) ? $field['label'] : '';
                                $selected   = in_array( $slug, $ovabrw_custom_checkout_field) ? ' selected' : '';
                            ?>
                                <option value="<?php echo $slug; ?>" <?php echo ' '.$selected; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>
        <!-- Show Location in booking form -->
        <tr class="form-field">
            <th scope="row" valign="top">
                <label>
                    <?php esc_html_e('Show Location in booking form', 'ova-brw'); ?>
                </label>

            </th>
            <td>
                <select name="ovabrw_show_loc_booking_form[]" multiple="multiple" class="ovabrw_custom_tax_with_cat">
                    <?php 
                        $list_fields = array( 'pickup_loc' => esc_html__( 'Pick-up Location', 'ova-brw' ), 'dropoff_loc' => esc_html__( 'Drop-off Location', 'ova-brw' ) );

                        if ( ! empty( $list_fields ) ) {
                            foreach( $list_fields as $slug => $value ) {
                                 $selected = in_array( $slug, $ovabrw_show_loc_booking_form) ? ' selected' : '';
                                ?>
                                    <option value="<?php echo $slug; ?>" <?php echo ' '.$selected; ?>>
                                        <?php echo $value; ?>
                                    </option>
                                <?php
                            }
                        }
                    ?>
                </select>
                <br>
                <span>
                    <?php esc_html_e( 'If empty will get value in WooCommerce >> Settings >> Booking & Rental >> Booking Form', 'ova-brw' ); ?>
                </span>
            </td>
        </tr>
        <!-- Label Pick-up Date -->
        <tr class="form-field ovabrw_lable_pickup_date">
            <th>
                <label><?php esc_html_e('Label Pick-up Date', 'ova-brw'); ?></label>
            </th>
            <td>
                <input name="ovabrw_lable_pickup_date" id="ovabrw_lable_pickup_date" type="text" value="<?php esc_html_e( $ovabrw_lable_pickup_date, 'ova-brw' ); ?>" size="40">
                <p><?php esc_html_e( 'Insert label Pick-up Date. Example: Pick-up Date, Check-in', 'ova-brw' ); ?></p>
            </td>
        </tr>
        <!-- Label Drop-off Date -->
        <tr class="form-field ovabrw_lable_dropoff_date">
            <th>
                <label><?php esc_html_e('Label Drop-off Date', 'ova-brw'); ?></label>
            </th>
            <td>
                <input name="ovabrw_lable_dropoff_date" id="ovabrw_lable_dropoff_date" type="text" value="<?php esc_html_e( $ovabrw_lable_dropoff_date, 'ova-brw' ); ?>" size="40">
                <p><?php esc_html_e( 'Insert label Drop-off Date. Example: Drop-off Date, Check-out', 'ova-brw' ); ?></p>
            </td>
        </tr>
        <!-- Product template -->
        <tr class="form-field ovabrw_product_templates">
            <th>
                <label><?php esc_html_e('Product template', 'ova-brw'); ?></label>
            </th>
            <td>
                <select id="ovabrw_product_templates" name="ovabrw_product_templates">
                    <?php echo $html_templates; ?>
                </select>
                <p><?php esc_html_e( 'Global (WooCommerce >> Settings >> Booking & Rental >> Product Template) or Other (made in Templates of Elementor )', 'ova-brw' ); ?></p>
            </td>
        </tr>
        <?php
        if ( ovabrw_global_typography() ): ?>
           <tr class="form-field ovabrw_card_template">
                <th>
                    <label><?php esc_html_e('Card template', 'ova-brw'); ?></label>
                </th>
                <td>
                    <select name="ovabrw_card_template">
                        <option value="">
                            <?php esc_html_e( 'Select card', 'ova-brw' ); ?>
                        </option>
                        <option value="card1"<?php selected( $card_template, 'card1' ); ?>>
                            <?php esc_html_e( 'Card 1', 'ova-brw' ); ?>
                        </option>
                        <option value="card2"<?php selected( $card_template, 'card2' ); ?>>
                            <?php esc_html_e( 'Card 2', 'ova-brw' ); ?>
                        </option>
                        <option value="card3"<?php selected( $card_template, 'card3' ); ?>>
                            <?php esc_html_e( 'Card 3', 'ova-brw' ); ?>
                        </option>
                        <option value="card4"<?php selected( $card_template, 'card4' ); ?>>
                            <?php esc_html_e( 'Card 4', 'ova-brw' ); ?>
                        </option>
                        <option value="card5"<?php selected( $card_template, 'card5' ); ?>>
                            <?php esc_html_e( 'Card 5', 'ova-brw' ); ?>
                        </option>
                        <option value="card6"<?php selected( $card_template, 'card6' ); ?>>
                            <?php esc_html_e( 'Card 6', 'ova-brw' ); ?>
                        </option>
                    </select>
                </td>
            </tr>
        <?php endif;
    }
}

// Save extra taxonomy fields callback function.
add_action( 'edited_product_cat', 'ovabrw_save_taxonomy_custom_meta', 10, 1 );
add_action( 'create_product_cat', 'ovabrw_save_taxonomy_custom_meta', 10, 1 );
if ( ! function_exists( 'ovabrw_save_taxonomy_custom_meta' ) ) {
    function ovabrw_save_taxonomy_custom_meta( $term_id ) {
        $ovabrw_cat_dis     = filter_input(INPUT_POST, 'ovabrw_cat_dis');
        $ovabrw_custom_tax  = isset( $_REQUEST['ovabrw_custom_tax'] ) ? $_REQUEST['ovabrw_custom_tax'] : '';

        $ovabrw_custom_checkout_field = isset($_REQUEST['ovabrw_custom_checkout_field']) ? $_REQUEST['ovabrw_custom_checkout_field'] : '';
        $ovabrw_show_loc_booking_form = isset($_REQUEST['ovabrw_show_loc_booking_form']) ? $_REQUEST['ovabrw_show_loc_booking_form'] : '';
        $ovabrw_choose_custom_checkout_field = isset( $_REQUEST['ovabrw_choose_custom_checkout_field'] ) ? $_REQUEST['ovabrw_choose_custom_checkout_field'] : '';

        // Get pick-up, drop-off date
        $ovabrw_lable_pickup_date   = isset( $_REQUEST['ovabrw_lable_pickup_date'] ) ? $_REQUEST['ovabrw_lable_pickup_date'] : '';
        $ovabrw_lable_dropoff_date  = isset( $_REQUEST['ovabrw_lable_dropoff_date'] ) ? $_REQUEST['ovabrw_lable_dropoff_date'] : '';

        // Get product template
        $product_template = isset( $_REQUEST['ovabrw_product_templates'] ) ? $_REQUEST['ovabrw_product_templates'] : '';

        // Card
        $card_template = isset( $_REQUEST['ovabrw_card_template'] ) ? $_REQUEST['ovabrw_card_template'] : '';

        update_term_meta($term_id, 'ovabrw_cat_dis', $ovabrw_cat_dis);
        update_term_meta($term_id, 'ovabrw_custom_tax', $ovabrw_custom_tax);
        update_term_meta($term_id, 'ovabrw_custom_checkout_field', $ovabrw_custom_checkout_field);
        update_term_meta($term_id, 'ovabrw_show_loc_booking_form', $ovabrw_show_loc_booking_form);
        update_term_meta($term_id, 'ovabrw_choose_custom_checkout_field', $ovabrw_choose_custom_checkout_field);

        // Save pick-up, drop-off date
        update_term_meta($term_id, 'ovabrw_lable_pickup_date', $ovabrw_lable_pickup_date);
        update_term_meta($term_id, 'ovabrw_lable_dropoff_date', $ovabrw_lable_dropoff_date);

        // Save product template
        update_term_meta($term_id, 'ovabrw_product_templates', $product_template);

        // Save card template
        update_term_meta( $term_id, 'ovabrw_card_template', $card_template );
    }
}