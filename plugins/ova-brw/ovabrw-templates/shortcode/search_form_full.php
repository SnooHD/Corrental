<?php

extract(  $args );
$first_day = get_option( 'ova_brw_calendar_first_day', '0' );
if ( empty( $first_day ) ) {
    $first_day = 0;
}

$class_modern = ovabrw_global_typography() ? ' ovabrw_search_modern' : '';

?>
<div class="ovabrw_wd_search">
    <form 
        action="<?php echo esc_url( home_url() ); ?>"
        class="ovabrw_search form_ovabrw row <?php echo esc_attr( $class.$class_modern ); ?>"
        enctype="multipart/form-data"
        data-mesg_required="<?php esc_html_e( 'This field is required.', 'ova-brw' ); ?>">
        <div class="wrap_content <?php echo esc_attr( $column ); ?>">
            <?php if ( $show_name_product === 'yes' ): ?>
                <div class="s_field">
                    <div class="content">
                        <label><?php esc_html_e( 'Name Product', 'ova-brw' ); ?></label>
                        <div class="wrap-error"></div>
                        <input
                            type="text"
                            name="ovabrw_name_product"
                            class="<?php echo esc_attr( $name_product_required ); ?>"
                            value="<?php echo esc_attr( $name_product ); ?>"
                            placeholder="<?php esc_html_e( 'Name Product', 'ova-brw' ); ?>"
                            autocomplete="off"
                        />
                    </div>
                </div>
            <?php endif; ?>
            <?php if ( $show_cat === 'yes' ): ?>
                <div class="s_field">
                    <div class="content">
                        <label><?php esc_html_e( 'Category', 'ova-brw' ); ?></label>
                        <div class="wrap-error"></div>
                        <?php echo ovabrw_cat_rental( $cat, $category_required, $remove_cats_id ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ( $show_pickup_loc === 'yes' ): ?>
                <div class="s_field">
                    <div class="content">
                        <label><?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?></label>
                        <div class="wrap-error"></div>
                        <?php
                            echo ovabrw_get_locations_html( 'ovabrw_pickup_loc', $pickup_loc_required, $pickup_loc );
                        ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ( $show_dropoff_loc === 'yes' ): ?>
                <div class="s_field">
                    <div class="content">
                        <label><?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?></label>
                        <div class="wrap-error"></div>
                        <?php
                            echo ovabrw_get_locations_html( 'ovabrw_pickoff_loc', $dropoff_loc_required, $pickoff_loc );
                        ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ( $show_pickup_date === 'yes' ): ?>
                <div class="s_field">
                    <div class="content">
                        <label><?php esc_html_e( 'Pick-up Date', 'ova-brw' ); ?></label>
                        <div class="wrap-error"></div>
                        <input
                            type="text"
                            name="ovabrw_pickup_date"
                            class="ovabrw_datetimepicker ovabrw_start_date <?php echo esc_attr( $pickup_date_required ); ?>"
                            value="<?php echo esc_attr( $pickup_date ); ?>"
                            placeholder="<?php echo esc_attr( ovabrw_get_date_format() ); ?>"
                            data-hour_default="<?php echo esc_attr( $hour_default ); ?>"
                            data-time_step="<?php echo esc_attr( $time_step ); ?>"
                            data-dateformat="<?php echo esc_attr( $dateformat ); ?>"
                            data-firstday="<?php echo esc_attr( $first_day ); ?>"
                            timepicker="<?php echo esc_attr( $timepicker ); ?>"
                            data-error=".ovabrw_pickup_date"
                            autocomplete="off"
                            onfocus="blur();"
                        />
                    </div>
                </div>
            <?php endif; ?>
            <?php if ( $show_dropoff_date === 'yes' ): ?>
                <div class="s_field">
                    <div class="content">
                        <label><?php esc_html_e( 'Drop-off Date', 'ova-brw' ); ?></label>
                        <div class="wrap-error"></div>
                        <input
                            type="text"
                            name="ovabrw_pickoff_date"
                            class="ovabrw_datetimepicker ovabrw_end_date <?php echo esc_attr( $dropoff_date_required ); ?>"
                            value="<?php echo esc_attr( $pickoff_date ); ?>"
                            placeholder="<?php echo esc_attr( ovabrw_get_date_format() ); ?>"
                            data-hour_default="<?php echo esc_attr( $hour_default ); ?>"
                            data-time_step="<?php echo esc_attr( $time_step ); ?>"
                            data-dateformat="<?php echo esc_attr( $dateformat ); ?>"
                            data-firstday="<?php echo esc_attr( $first_day ); ?>"
                            timepicker="<?php echo esc_attr( $timepicker ); ?>"
                            data-error=".ovabrw_pickoff_date"
                            autocomplete="off"
                            onfocus="blur();"
                        />
                    </div>
                </div>
            <?php endif; ?>
            <?php if ( $show_attribute === 'yes' ): ?>
                <div class="s_field">
                    <div class="content">
                        <label><?php esc_html_e( 'Name Attribute', 'ova-brw' ); ?></label>
                        <div class="wrap-error"></div>
                        <?php echo $html_select_attribute; ?>
                    </div>
                </div>
                <?php echo $html_select_value_attribute; ?>
            <?php endif; ?>
            <?php if ( $show_tag_product == 'yes' ): ?>
                <div class="s_field">
                    <div class="content">
                        <label><?php esc_html_e( 'Tag Product', 'ova-brw' ); ?></label>
                        <div class="wrap-error"></div>
                        <input
                            type="text"
                            name="ovabrw_tag_product"
                            class="<?php echo esc_attr( $tag_product_required ); ?>"
                            value="<?php echo esc_attr( $tag_product ); ?>"
                            placeholder="<?php esc_html_e( 'Tag Product', 'ova-brw' ); ?>"
                            autocomplete="off"
                        />
                    </div>
                </div>
            <?php endif; ?>
            <?php
                $list_taxonomy              = array_key_exists( 'taxonomy_list_all' , $taxonomy_list_wrap) ? $taxonomy_list_wrap['taxonomy_list_all'] : [];
                $arr_require_taxonomy_key   = array_key_exists( 'taxonomy_require' , $taxonomy_list_wrap) ? $taxonomy_list_wrap['taxonomy_require'] : [];
                $arr_hide_taxonomy_key      = array_key_exists( 'taxonomy_hide' , $taxonomy_list_wrap) ? $taxonomy_list_wrap['taxonomy_hide'] : [];
                $list_get_taxonomy          = array_key_exists( 'taxonomy_get' , $taxonomy_list_wrap) ? $taxonomy_list_wrap['taxonomy_get'] : [];

                if ( ! empty( $list_taxonomy ) && $show_tax == 'yes' ) {
                    foreach( $list_taxonomy as $taxonomy ) {
                        
                        $required = '';
                        if ( is_array( $arr_require_taxonomy_key ) && array_key_exists( $taxonomy['slug'], $arr_require_taxonomy_key ) && $arr_require_taxonomy_key[$taxonomy['slug']] == 'require' ) {
                            $required = 'required';
                        } else {
                            $required = '';
                        }

                        if ( is_array( $arr_hide_taxonomy_key ) && array_key_exists( $taxonomy['slug'], $arr_hide_taxonomy_key ) && $arr_hide_taxonomy_key[$taxonomy['slug']] == 'hide' ) {
                            //
                        } else { ?>
                            <div class="s_field s_field_cus_tax <?php echo esc_attr( $column. ' '. $taxonomy['slug'] ); ?>">
                                <div class="content">
                                    <label><?php echo esc_html( $taxonomy['name'] ); ?></label>
                                    <div class="wrap-error"></div>
                                    <?php echo ovabrw_taxonomy_dropdown( $list_get_taxonomy[$taxonomy['slug']], $required , '',$taxonomy['slug'], $taxonomy['name'] ); ?>
                                </div>
                            </div>
                        <?php }
                    }
                }
            ?>
            <input type="hidden" name="order" value="<?php echo esc_attr( $order ); ?>">
            <input type="hidden" name="orderby" value="<?php echo esc_attr( $orderby ); ?>">
        </div>
        <div class="s_submit">
            <button class="ovabrw_btn_submit" type="submit"><?php esc_html_e( 'Search', 'ova-brw' ); ?></button>
        </div>
        <input type="hidden" name="ovabrw_search_product" value="ovabrw_search_product">
        <input type="hidden" name="ovabrw_search" value="search_item">
        <input type="hidden" name="post_type" value="product">
        <?php if ( defined( 'ICL_LANGUAGE_CODE' ) ): ?>
            <input type="hidden" name="lang" value="<?php echo esc_attr( ICL_LANGUAGE_CODE ); ?>">
        <?php endif; ?>
    </form>
</div>