<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * get_order_rent_time return all date available
 * @param  number $product_id   Product ID
 * @param  array  $order_status wc-completed, wc-processing
 * @return json               dates available
 */
if ( ! function_exists( 'get_order_rent_time' ) ) {
    function get_order_rent_time( $product_id = false, $order_status = array( 'wc-completed' ) ) {
        global $wpdb;

        $order_date = $dates_un_avaiable = $total_each_day = array();
        $count_car_in_store         = ovabrw_get_total_stock( $product_id );
        $background_color_calendar  = ovabrw_get_setting( get_option( 'ova_brw_bg_calendar', '#c4c4c4' ) );

        if ( ovabrw_global_typography() ) {
            $background_color_calendar = get_option( 'ovabrw_glb_primary_color', '#e56e00' );
        }
        
        // Get array product ids when use WPML
        $array_product_ids  = ovabrw_get_wpml_product_ids( $product_id );
        $orders_ids         = ovabrw_get_orders_by_product_id( $product_id, $order_status );
        $date_format        = ovabrw_get_date_format();

        foreach( $orders_ids as $key => $value ) {
            // Get Order Detail by Order ID
            $order = wc_get_order($value);

            // Get Meta Data type line_item of Order
            $order_line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
           
            // For Meta Data
            foreach ( $order_line_items as $item_id => $item ) {
                $push_date_unavailable      = array();
                $ovabrw_pickup_date_store   = $ovabrw_pickoff_date_store = $define_day = $rental_type = '';
                $ovabrw_number_vehicle      = 1;
                
                if ( in_array( $item->get_product_id(), $array_product_ids) ) {
                    $rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

                    // Get value of pickup date, pickoff date
                    if ( $item && is_object( $item ) ) {
                        $define_day                 = $item->get_meta('define_day');
                        $ovabrw_pickup_date_store   = strtotime( $item->get_meta('ovabrw_pickup_date_real') );
                        $ovabrw_pickoff_date_store  = strtotime( $item->get_meta('ovabrw_pickoff_date_real') );
                        $ovabrw_number_vehicle      = trim( $item->get_meta('ovabrw_number_vehicle') );
                    }
                }

                // $ovabrw_pickoff_date_store - $ovabrw_pickup_date_store
                if ( $ovabrw_pickoff_date_store >= current_time( 'timestamp' ) ) {
                    for( $i = 0; $i < $ovabrw_number_vehicle ; $i++ ) { 
                        $push_date_unavailable = push_date_unavailable( $ovabrw_pickup_date_store, $ovabrw_pickoff_date_store, $define_day, $rental_type, $product_id );

                        $total_each_day = array_merge_recursive( $total_each_day, $push_date_unavailable['total_each_day'] );
                        $dates_un_avaiable = array_merge_recursive( $dates_un_avaiable, $push_date_unavailable['dates_avaiable'] );
                    }
                }
            }
        }

        // Check Unavaiable Time in Product
        $ovabrw_untime_startdate    = get_post_meta( $product_id, 'ovabrw_untime_startdate', true );
        $ovabrw_untime_enddate      = get_post_meta( $product_id, 'ovabrw_untime_enddate', true );
        $define_day     = get_post_meta( $product_id, 'ovabrw_define_1_day', true );
        $rental_type    = get_post_meta( $product_id, 'ovabrw_price_type', true );

        if ( is_array( $ovabrw_untime_startdate ) && !empty( $ovabrw_untime_startdate ) ) {
            foreach( $ovabrw_untime_startdate as $key => $value ) {
                if ( !empty( $ovabrw_untime_startdate[$key] ) ) {
                    $push_date_unavailable_untime =  array();
                    $push_date_unavailable_untime = push_date_unavailable_time( strtotime( $ovabrw_untime_startdate[$key] ), strtotime( $ovabrw_untime_enddate[$key] ) );

                    for( $i = 0; $i < $count_car_in_store; $i++ ) {
                        $total_each_day     = array_merge_recursive( $total_each_day, $push_date_unavailable_untime['total_each_day'] );
                        $dates_un_avaiable  = array_merge_recursive( $dates_un_avaiable, $push_date_unavailable_untime['dates_avaiable'] );
                    }
                    
                    $total_each_day     = array_merge_recursive( $total_each_day, $push_date_unavailable_untime['total_each_day'] );
                    $dates_un_avaiable = array_merge_recursive( $dates_un_avaiable, $push_date_unavailable_untime['dates_avaiable'] );
                }
            }
        }

        // Check Unavaiable Time in each Vehicle ID
        $manage_store               = get_post_meta( $product_id, 'ovabrw_manage_store', true );
        $ovabrw_untime_vehicle_id   = get_post_meta( $product_id, 'ovabrw_id_vehicles', true );

        if ( is_array( $ovabrw_untime_vehicle_id ) && $manage_store == 'id_vehicle' ) {
            foreach( $ovabrw_untime_vehicle_id as $key => $value ) {
                $id_vehicle_untime  = array();
                $vehicle_avai       = ovabrw_get_vehicle_loc_title($value);

                $id_vehicle_untime_startday = !empty( $vehicle_avai['untime'] ) ? strtotime( $vehicle_avai['untime']['startdate'] ) : '';
                $id_vehicle_untime_enddate  = !empty( $vehicle_avai['untime'] ) ? strtotime( $vehicle_avai['untime']['enddate'] ) : ''; 

                if ( $id_vehicle_untime_startday != '' && $id_vehicle_untime_enddate != '' ) {
                    $id_vehicle_untime = push_date_unavailable_time( $id_vehicle_untime_startday, $id_vehicle_untime_enddate );    
                }

                if ( !empty($id_vehicle_untime) ) {
                    $total_each_day     = array_merge_recursive( $total_each_day, $id_vehicle_untime['total_each_day'] );
                    $dates_un_avaiable  = array_merge_recursive( $dates_un_avaiable, $id_vehicle_untime['dates_avaiable'] ) ;
                }
            }
        }

        $count_values = array_count_values( $total_each_day );

        // When Rental Type is Hotel: Check if  total day > vehicle in store
        $compared_array = array();

        if ( $define_day == 'hotel' ) {
            foreach ( $dates_un_avaiable as $k1 => $v1 ) {
                $time_up_check  = apply_filters( 'brw_real_pickup_time_hotel', '14:00' ). '-24:00';
                $time_off_check = '00:00-' . apply_filters( 'brw_real_dropoff_time_hotel', '11:00' );

                if ( trim( $v1['time'] ) === $time_up_check ) {
                    $start = date( 'Y-m-d', strtotime( $v1['start'] ) );
                    
                    if ( ! in_array( $start, $compared_array ) ) {
                        $k = 0;
                        array_push( $compared_array, $start );

                        foreach ( $dates_un_avaiable as $k2 => $v2 ) {
                            $start2 = date( 'Y-m-d', strtotime( $v2['start'] ) );

                            if ( $start2 === $start && trim( $v2['time'] ) === $time_off_check ) {
                                $k++;
                            }
                        }

                        if ( $k >= $count_car_in_store ) {
                            $count_values[$start] = $k;
                        }
                    }
                }
            }
        }

        foreach( $count_values as $key => $value ) {
            if ( $value >= $count_car_in_store ) {
                $item =  array(  
                    'start'             => $key,
                    'end'               => $key,
                    'start_v2'          => date( $date_format, strtotime( $key ) ) ,
                    'rendering'         => 'background',
                    'display'           => 'background',
                    'backgroundColor'   => $background_color_calendar,
                    'overlap'           => false,
                    'title'             => '',
                );
                array_push( $dates_un_avaiable, $item );
            }
        }

        return json_encode( $dates_un_avaiable );
    }
}

if ( ! function_exists( 'push_date_unavailable' ) ) {
    function push_date_unavailable( $ovabrw_pickup_date_store = '', $ovabrw_pickoff_date_store = '', $define_day = '', $rental_type = '', $product_id = '' ) {
        if ( $product_id && $rental_type != 'day' && $rental_type != 'transportation' ) {
            $prepare_time = get_post_meta( $product_id, 'ovabrw_prepare_vehicle', true ) ? get_post_meta( $product_id, 'ovabrw_prepare_vehicle', true ) * 60 : 0;
        } else {
            $prepare_time = get_post_meta( $product_id, 'ovabrw_prepare_vehicle_day', true ) ? get_post_meta( $product_id, 'ovabrw_prepare_vehicle_day', true ) * 86400 : 0;
        }

        $date_format = 'Y-m-d';
        $ovabrw_pickoff_date_store += $prepare_time;
        $date_time_pickup   = date( $date_format . ' H:i', $ovabrw_pickup_date_store );
        $date_time_pickoff  = date( $date_format . ' H:i', $ovabrw_pickoff_date_store );
        $date_pickup        = date( $date_format, $ovabrw_pickup_date_store );
        $date_pickoff       = date( $date_format, $ovabrw_pickoff_date_store );
        $time_pickup        = date( 'H:i', $ovabrw_pickup_date_store );
        $time_pickoff       = date( 'H:i', $ovabrw_pickoff_date_store );

        if ( $time_pickoff == '00:00' ) {
            $date_time_pickoff  = date( $date_format , $ovabrw_pickoff_date_store - 3600 * 24 ) . ' 24:00';
            $date_pickoff       = date( $date_format, $ovabrw_pickoff_date_store - 3600 * 24 );
            $time_pickoff       = '24:00';
        }

        // Check if define day is Hotel
        if ( $rental_type == 'day' && $define_day == 'hotel' ) {
            $time_pickup    = apply_filters( 'brw_real_pickup_time_hotel', '14:00' );
            $time_pickoff   = apply_filters( 'brw_real_dropoff_time_hotel', '11:00' );
        }

        $dates_avaiable = $total_each_day = array();
        $start_date     = $date_pickup;
        $end_date       = $date_pickoff;
        $total_between_2_days = total_between_2_days( $start_date, $end_date );

        if ( $total_between_2_days == 0 ) { // In a day
            $item = array(
                'start' => $date_time_pickup,
                'end'   => $date_time_pickoff,
                'title' => $time_pickup.'-'.$time_pickoff,
                'time'  => $time_pickup.'-'.$time_pickoff
            );
            array_push( $dates_avaiable, $item );

            if ( $time_pickup == '00:00' && $time_pickoff == '24:00' ) {
                array_push( $total_each_day, $date_pickup  );
            }
        } elseif ( $total_between_2_days == 1 ) { // 2 day beside
            $item = array(
                'start' => $date_time_pickup,
                'end'   => date( $date_format . ' 24:00', $ovabrw_pickup_date_store ),
                'title' => $time_pickup .' '. esc_html__('24:00', 'ova-brw'),
                'time'  => $time_pickup .'-'. '24:00',
            );
            array_push( $dates_avaiable, $item );

            if ( $time_pickup == '00:00' ) {
                array_push( $total_each_day, $date_pickup );
            }

            $item = array(
                'start' => $date_pickoff,
                'end'   => $date_time_pickoff,
                'title' => esc_html__('00:00', 'ova-brw').' '.$time_pickoff,
                'time'  => '00:00'.'-'.$time_pickoff,
            );
            array_push( $dates_avaiable, $item ); 

            if ( $time_pickoff == '24:00' ) {
                array_push( $total_each_day, $date_pickoff );
            }
        } else { // from 3 days 
            $item = array(
                'start' => $date_time_pickup,
                'end'   => date( $date_format . ' 24:00', $ovabrw_pickup_date_store ),
                'title' => $time_pickup.' '.esc_html__('24:00', 'ova-brw'),
                'time'  => $time_pickup.'-'.'24:00'
            );
            array_push( $dates_avaiable, $item ); 

            if ( $time_pickup == '00:00' ) {
                array_push( $total_each_day, $date_pickup );
            }

            $item = array(
                'start' => $date_pickoff .  esc_html__(' 00:00', 'ova-brw'),
                'end'   => $date_time_pickoff,
                'title' => esc_html__('00:00', 'ova-brw').' '.$time_pickoff,
                'time'  => '00:00'.'-'.$time_pickoff
            );
            array_push( $dates_avaiable, $item );

            if ( $time_pickoff == '24:00' ) {
                array_push( $total_each_day, $date_pickoff );
            }
            
            $date_between_start_end = ovabrw_createDatefull( strtotime( $start_date ), strtotime( $end_date ), $date_format );

            // Remove first and last array
            array_shift( $date_between_start_end ); 
            array_pop( $date_between_start_end );

            foreach( $date_between_start_end as $key => $value ) {
                $item = array(
                    'start' => $value,
                    'end'   => $value,
                    'title' => esc_html__('00:00 24:00 ', 'ova-brw'),
                    'time'  => '00:00-24:00'
                );
                array_push( $dates_avaiable, $item );
                array_push( $total_each_day, $value );
            }
        }
        
        return( array( 'dates_avaiable' => $dates_avaiable, 'total_each_day' => $total_each_day ) );
    }
}

/**
 * Get all dates from unvailable time
 */
if ( ! function_exists( 'push_date_unavailable_time' ) ) {
    function push_date_unavailable_time( $ovabrw_pickup_date_untime, $ovabrw_pickoff_date_untime ) {
        $date_format = 'Y-m-d';
        $date_time_pickup   = date( $date_format . ' H:i', $ovabrw_pickup_date_untime );
        $date_time_pickoff  = date( $date_format . ' H:i', $ovabrw_pickoff_date_untime );
        $start_date     = date( $date_format, $ovabrw_pickup_date_untime );
        $end_date       = date( $date_format, $ovabrw_pickoff_date_untime );
        $time_pickup    = date( 'H:i', $ovabrw_pickup_date_untime );
        $time_pickoff   = date( 'H:i', $ovabrw_pickoff_date_untime );

        if ( $time_pickoff == '00:00' ) {
            $date_time_pickoff  = date( $date_format , $ovabrw_pickoff_date_untime - 3600 * 24 ) . ' 24:00';
            $end_date           = date( $date_format, $ovabrw_pickoff_date_untime - 3600 * 24 );
            $time_pickoff       = '24:00';
        }

        $background_color_calendar  = ovabrw_get_setting( get_option( 'ova_brw_bg_calendar', '#c4c4c4' ) );

        if ( ovabrw_global_typography() ) {
            $background_color_calendar = get_option( 'ovabrw_glb_primary_color', '#e56e00' );
        }

        $dates_avaiable             = $total_each_day = array();
        $total_between_2_days       = total_between_2_days( $start_date, $end_date );

        if ( $total_between_2_days == 0 ) { // In a day
            $item = array(
                'start' => $date_time_pickup,
                'end'   => $date_time_pickoff,
                'title' => $time_pickup.'-'.$time_pickoff,
                'time'  => $time_pickup.'-'.$time_pickoff,
                'backgroundColor'   => $background_color_calendar,
                'borderColor'       => 'transparent',
            );
            array_push( $dates_avaiable, $item );

            if ( $time_pickup == '00:00' && $time_pickoff == '24:00' ) {
                array_push( $total_each_day, $start_date  );
            }
        } elseif ( $total_between_2_days == 1 ) { // 2 day beside
            $item = array(
                'start' => $date_time_pickup,
                'end'   => date( $date_format . ' 24:00', $ovabrw_pickup_date_untime ),
                'title' => $time_pickup .' '. esc_html__('24:00', 'ova-brw'),
                'time'  => $time_pickup .'-'. '24:00',
                'backgroundColor'   => $background_color_calendar,
                'borderColor'       => 'transparent',
            );
            array_push( $dates_avaiable, $item );

            if ( $time_pickup == '00:00' ) {
                array_push( $total_each_day, $start_date );
            }

            $item = array(
                'start' => $end_date,
                'end'   => $date_time_pickoff,
                'title' => esc_html__('00:00', 'ova-brw').' '.$time_pickoff,
                'time'  => '00:00'.'-'.$time_pickoff,
                'backgroundColor'   => $background_color_calendar,
                'borderColor'       => 'transparent',
            );
            array_push( $dates_avaiable, $item ); 

            if ( $time_pickoff == '24:00' ) {
                array_push( $total_each_day, $end_date );
            }
        } else { // from 3 days 
            $item = array(
                'start' => $date_time_pickup,
                'end'   => date( $date_format . ' 24:00', $ovabrw_pickup_date_untime ),
                'title' => $time_pickup.' '.esc_html__('24:00', 'ova-brw'),
                'time'  => $time_pickup.'-'.'24:00',
                'backgroundColor'   => $background_color_calendar,
                'borderColor'       => 'transparent',
            );
            array_push( $dates_avaiable, $item ); 

            if ( $time_pickup == '00:00' ) {
                array_push( $total_each_day, $start_date );
            }

            $item = array(
                'start' => $end_date .  esc_html__(' 00:00', 'ova-brw'),
                'end'   => $date_time_pickoff,
                'title' => esc_html__('00:00', 'ova-brw').' '.$time_pickoff,
                'time'  => '00:00'.'-'.$time_pickoff,
                'backgroundColor'   => $background_color_calendar,
                'borderColor'       => 'transparent',
            );
            array_push( $dates_avaiable, $item );

            if ( $time_pickoff == '24:00' ) {
                array_push( $total_each_day, $end_date );
            }
            
            $date_between_start_end = ovabrw_createDatefull( strtotime( $start_date ), strtotime( $end_date ), $format = $date_format );

            // Remove first and last array
            array_shift( $date_between_start_end ); 
            array_pop( $date_between_start_end );

            foreach( $date_between_start_end as $key => $value ) {
                $item = array(
                    'start' => $value,
                    'end'   => $value,
                    'title' => esc_html__('00:00 24:00 ', 'ova-brw'),
                    'time'  => '00:00-24:00',
                    'backgroundColor'   => $background_color_calendar,
                    'borderColor'       => 'transparent',
                );
                array_push( $dates_avaiable, $item );
                array_push( $total_each_day, $value  );
            }
        }
        
        return( array( 'dates_avaiable' => $dates_avaiable, 'total_each_day' => $total_each_day ) );
    }
}


/**
 * get all Order id of a product
 * @param  [number] $product_id   product id
 * @param  array  $order_status wc-completed, wc-processing
 * @return [array object]               all order id
 */
if ( ! function_exists( 'ovabrw_get_orders_by_product_id' ) ) {
    function ovabrw_get_orders_by_product_id( $product_id = false, $order_status = array( 'wc-completed' ) ){
        global $wpdb;
        $order_ids = array();

        // Get array product ids when use WPML
        $product_ids = ovabrw_get_wpml_product_ids( $product_id );

        if ( ovabrw_wc_custom_orders_table_enabled() ) {
            $order_ids = $wpdb->get_col("
                SELECT DISTINCT o.id
                FROM {$wpdb->prefix}wc_orders AS o
                LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oitems
                ON o.id = oitems.order_id
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                WHERE oitems.order_item_type = 'line_item'
                AND oitem_meta.meta_key = '_product_id'
                AND oitem_meta.meta_value IN ( ".implode( ',', $product_ids )." )
                AND o.status IN ( '" . implode( "','", $order_status ) . "' )
            ");
        } else {
            $order_ids = $wpdb->get_col("
                SELECT DISTINCT oitems.order_id
                FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitems.order_item_id
                LEFT JOIN {$wpdb->posts} AS posts ON oitems.order_id = posts.ID
                WHERE posts.post_type = 'shop_order'
                AND oitems.order_item_type = 'line_item'
                AND oitem_meta.meta_key = '_product_id'
                AND oitem_meta.meta_value IN ( ".implode( ',', $product_ids )." )
                AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
            ");
        }
        
        return $order_ids;
    }
}

/**
 * ovabrw_search_vehicle Search Product
 * @param  array $data_search more value
 * @return [array object]              false | array object wq_query
 */
if ( ! function_exists( 'ovabrw_search_vehicle' ) ) {
    function ovabrw_search_vehicle( $data_search ) {
        $name_product   = isset( $data_search['ovabrw_name_product'] ) ? sanitize_text_field( $data_search['ovabrw_name_product'] ) : '';
        $pickup_loc     = isset( $data_search['ovabrw_pickup_loc'] ) ? sanitize_text_field( $data_search['ovabrw_pickup_loc'] ) : '';
        $pickoff_loc    = isset( $data_search['ovabrw_pickoff_loc'] ) ? sanitize_text_field( $data_search['ovabrw_pickoff_loc'] ) : '';
        $pickup_date    = isset( $data_search['ovabrw_pickup_date'] )  ? strtotime( $data_search['ovabrw_pickup_date'] ) : '';
        $pickoff_date   = isset( $data_search['ovabrw_pickoff_date'] ) ? strtotime( $data_search['ovabrw_pickoff_date'] ) : '';
        $order          = isset( $data_search['order'] ) ? sanitize_text_field( $data_search['order'] ) : 'DESC';
        $orderby        = isset( $data_search['orderby'] ) ? sanitize_text_field( $data_search['orderby'] ) : 'ID';
        $name_attribute = isset( $data_search['ovabrw_attribute'] ) ? sanitize_text_field( $data_search['ovabrw_attribute'] ) : '';
        $value_attribute = isset( $data_search[$name_attribute] ) ? sanitize_text_field( $data_search[$name_attribute] ) : '';
        $category       = isset( $data_search['cat'] ) ? sanitize_text_field( $data_search['cat'] ) : '';
        $tag_product    = isset( $data_search['ovabrw_tag_product'] ) ? sanitize_text_field( $data_search['ovabrw_tag_product'] ) : '';
        $list_taxonomy  = ovabrw_create_type_taxonomies();
        $arg_taxonomy_arr = [];

        if ( !empty( $list_taxonomy ) ) {
            foreach( $list_taxonomy as $taxonomy ) {
                $taxonomy_get = isset( $data_search[$taxonomy['slug'].'_name'] ) ? sanitize_text_field( $data_search[$taxonomy['slug'].'_name'] ) : '';

                if ( $taxonomy_get != '' ) {
                    $arg_taxonomy_arr[] = array(
                        'taxonomy' => $taxonomy['slug'],
                        'field' => 'slug',
                        'terms' => $taxonomy_get
                    );
                } else {
                    $arg_taxonomy_arr[] = '';
                }
            }
        }

        $statuses   = brw_list_order_status();
        $error      = array();
        $items_id   = $args_cus_tax_custom = array();

        if ( $name_product == '' ) {
            $args_base = array(
                'post_type'         => 'product',
                'posts_per_page'    => '-1',
                'post_status'       => 'publish'
            );
        } else {
            $args_base = array(
                'post_type'         => 'product',
                'posts_per_page'    => '-1',
                's'                 => $name_product,
                'post_status'       => 'publish'
            );
        }
        if ( $category != '' ) {
            $arg_taxonomy_arr[] = [
                'taxonomy'  => 'product_cat',
                'field'     => 'slug',
                'terms'     => $category
            ];
        }
        if ( $name_attribute != '' ) {
            $arg_taxonomy_arr[] = [
                'taxonomy'  => 'pa_' . $name_attribute,
                'field'     => 'slug',
                'terms'     => [$value_attribute],
                'operator'  => 'IN',
            ];
        }
        if ( $tag_product != '' ) {
            $arg_taxonomy_arr[] = [
                'taxonomy'  => 'product_tag',
                'field'     => 'name',
                'terms'     => $tag_product
            ];
        }
        if ( !empty($arg_taxonomy_arr) ) {
            $args_cus_tax_custom = array(
                'tax_query'     => array(
                    'relation'  => 'AND',
                    $arg_taxonomy_arr
                )
            );
        }
       
        $args = array_merge_recursive( $args_base, $args_cus_tax_custom );

        // Get All products
        $items = new WP_Query( $args );

        if ( $items->have_posts() ) : while ( $items->have_posts() ) : $items->the_post();
            // Product ID
            $id = get_the_id();

            // Check location
            if ( ! ovabrw_check_location( $id, $pickup_loc, $pickoff_loc ) ) continue;

            // Set Pick-up, Drop-off Date again
            $new_input_date     = ovabrw_new_input_date( $id, $pickup_date, $pickoff_date, '', $pickup_loc, $pickoff_loc );
            $pickup_date_new    = $new_input_date['pickup_date_new'];
            $pickoff_date_new   = $new_input_date['pickoff_date_new'];

            $ova_validate_manage_store = ova_validate_manage_store( $id, $pickup_date_new, $pickoff_date_new, $pickup_loc, $pickoff_loc, $passed = false, $validate = 'search' ) ;
            
            if ( $ova_validate_manage_store && $ova_validate_manage_store['status'] ) {
                array_push($items_id, $id);
            }
        endwhile; else :
            return $items_id;
        endif; wp_reset_postdata();
        
        if ( $items_id ) {
            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
            $search_items_page = wc_get_default_products_per_row() * wc_get_default_product_rows_per_page();

            $args_product = array(
                'post_type'         => 'product',
                'posts_per_page'    => $search_items_page,
                'paged'             => $paged,
                'post_status'       => 'publish',
                'post__in'          => $items_id,
                'order'             => $order,
                'orderby'           => $orderby
            );
            $rental_products = new WP_Query( $args_product );

            return $rental_products;
        }

        return false;
    }
}

// Get Price Type
if ( ! function_exists( 'ovabrw_get_price_type' ) ) {
    function ovabrw_get_price_type( $post_id ) {
        return get_post_meta( $post_id, 'ovabrw_price_type', true );
    }
}

// Get Global Price of Rental Type - Day
if ( ! function_exists( 'ovabrw_get_price_day' ) ) {
    function ovabrw_get_price_day( $post_id ) {
        return ovabrw_wc_price( get_post_meta( $post_id, '_regular_price', true ) );
    }
}

// Get Global Price of Rental Type - Hour
if ( ! function_exists( 'ovabrw_get_price_hour' ) ) {
    function ovabrw_get_price_hour( $post_id ) {
        return ovabrw_wc_price( get_post_meta( $post_id, 'ovabrw_regul_price_hour', true ) );
    }
}

// Get All Rooms
if ( ! function_exists( 'get_all_rooms' ) ) {
    function get_all_rooms( $type = '', $exclude_type = false ) {
        $args = array(
            'post_type'         => 'product',
            'posts_per_page'    => '-1',
            'post_status'       => 'publish',
            'fields'            => 'ids', 
            'tax_query'         => array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'ovabrw_car_rental', 
                ),
            ),
        );

        if ( $type ) {
            $args['meta_query'] = array(
                array(
                    'key'     => 'ovabrw_price_type',
                    'value'   => $type,
                    'compare' => '=',
                ),
            );

            if ( $exclude_type ) {
                $args['meta_query'] = array(
                    array(
                        'key'     => 'ovabrw_price_type',
                        'value'   => $type,
                        'compare' => '!=',
                    ),
                );
            }
        }

        $rooms = new WP_Query( $args );

        return $rooms;
    }
}

// Get all location
if ( ! function_exists( 'ovabrw_get_locations' ) ) {
    function ovabrw_get_locations() {
        $locations = new WP_Query(
            array(
                'post_type'         => 'location',
                'post_status'       => 'publish',
                'posts_per_page'    => '-1'
            )
        );

        return $locations;
    }
}

// Get all Products has Product Data: Rental
if ( ! function_exists('ovabrw_get_all_products') ) {
    function ovabrw_get_all_products() {
        $args = array(
            'post_type'         => 'product',
            'fields'            => 'ids', 
            'post_status'       => 'publish',
            'posts_per_page'    => '-1',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'ovabrw_car_rental', 
                ),
            ),
        );
        $results = new WP_Query( $args );

        return $results;
    }
}

if ( ! function_exists( 'ovabrw_get_locations_array' ) ) {
    function ovabrw_get_locations_array() {
        $locations = get_posts(
            array(
                'post_type'         => 'location',
                'post_status'       => 'publish',
                'posts_per_page'    => '-1',
                'fields'            => 'id',
            )
        );
        $html = array();

        if ( $locations ) { 
            foreach( $locations as $location ) {
                $html[ trim( get_the_title( $location->ID  ) )] = trim( get_the_title( $location->ID ) );
            }
        }
        
        return $html;
    }
}

if ( ! function_exists( 'ovabrw_get_list_pickup_dropoff_loc_transport' ) ) {
    function ovabrw_get_list_pickup_dropoff_loc_transport( $id_product ) {
        if ( ! $id_product ) return [];

        $ovabrw_pickup_location     = get_post_meta( $id_product, 'ovabrw_pickup_location', 'false' );
        $ovabrw_dropoff_location    = get_post_meta( $id_product, 'ovabrw_dropoff_location', 'false' );
        $list_loc_pickup_dropoff    = [];

        if ( !empty( $ovabrw_pickup_location ) && !empty( $ovabrw_dropoff_location ) ) {
            foreach( $ovabrw_pickup_location as $key => $location ) {
                $dropoff_location = isset( $ovabrw_dropoff_location[$key] ) ? $ovabrw_dropoff_location[$key] : '';

                if ( $dropoff_location ) {
                    $list_loc_pickup_dropoff[$location][]   = $ovabrw_dropoff_location[$key];
                    $list_loc_pickup_dropoff[$location]     = array_unique( $list_loc_pickup_dropoff[$location] );
                }
            }
        }

        return $list_loc_pickup_dropoff;
    }
}

if ( ! function_exists( 'ovabrw_get_list_pickup_dropoff_setup_loctions' ) ) {
    function ovabrw_get_list_pickup_dropoff_setup_loctions( $product_id ) {
        if ( ! $product_id ) return [];
        $results = [];
        $ovabrw_st_pickup_loc   = get_post_meta( $product_id, 'ovabrw_st_pickup_loc', true );
        $ovabrw_st_dropoff_loc  = get_post_meta( $product_id, 'ovabrw_st_dropoff_loc', true );

        if ( ! empty( $ovabrw_st_pickup_loc ) && ! empty( $ovabrw_st_dropoff_loc ) ) {
            foreach( $ovabrw_st_pickup_loc as $key => $location ) {
                $dropoff_location = isset( $ovabrw_st_dropoff_loc[$key] ) ? $ovabrw_st_dropoff_loc[$key] : '';

                if ( $dropoff_location ) {
                    $results[$location][]   = $ovabrw_st_dropoff_loc[$key];
                    $results[$location]     = array_unique( $results[$location] );
                }
            }
        }

        return $results;
    }
}

if ( ! function_exists( 'ovabrw_get_locations_transport_html' ) ) {
    function ovabrw_get_locations_transport_html( $name = '', $required = 'required', $selected = '', $id_product = false, $type = 'pickup' ) {
        $list_loc_pickup_dropoff = ovabrw_get_list_pickup_dropoff_loc_transport( $id_product );

        $html = '<select name="'.$name.'" class="ovabrw-transport '.$required.'">';
        $html .= '<option value="">'. esc_html__( 'Select Location', 'ova-brw' ).'</option>';

        if ( $type == 'pickup' ) {
            if ( $list_loc_pickup_dropoff ) {
                foreach( $list_loc_pickup_dropoff as $loc => $item_loc ) {
                    $active = ( trim( $loc ) === trim( $selected ) ) ? 'selected="selected"' : '';
                    $html .= '<option data-item_loc="'.esc_attr(json_encode($item_loc)).'" value="'.trim( $loc ).'" '.$active.'>'.trim( $loc ).'</option>';
                }
            }
        }
        $html .= '</select>';

        return $html;
    }
}

if ( ! function_exists( 'ovabrw_get_locations_html' ) ) {
    function ovabrw_get_locations_html( $name = '', $required = 'required', $selected = '', $pid = '', $type = 'pickup' ) {
        $locations = get_posts( array(
            'post_type'         => 'location',
            'post_status'       => 'publish',
            'posts_per_page'    => '-1'
        ));

        $show_other_loc = true;

        if ( $pid ) {
            if( $type == 'pickup' ) {
                $show_other_loc = get_post_meta( $pid, 'ovabrw_show_other_location_pickup_product', true );
                $show_other_loc = ( $show_other_loc == 'no' ) ? false : true;
            } else {
                $show_other_loc = get_post_meta( $pid, 'ovabrw_show_other_location_dropoff_product', true );
                $show_other_loc = ( $show_other_loc == 'no' ) ? false : true;
            }
        }

        $html = '<select name="'.$name.'" class="'.$required.'">';
        $html .= '<option value="">'. esc_html__( 'Select Location', 'ova-brw' ).'</option>';

        if ( ! empty( $locations ) && is_array( $locations ) ) {
            foreach ( $locations as $location ) {
                if ( $location && is_object( $location ) ) {
                    $title  = $location->post_title;
                    $active = ( trim( $title ) === trim( $selected ) ) ? 'selected="selected"' : '';
                    $html .= '<option value="'.$title.'" '.$active.'>'.$title.'</option>';
                }
            }
        }
        
        if ( $show_other_loc ) {
            $active = ( 'other_location' === trim( $selected ) ) ? 'selected="selected"' : '';
            $html   .= '<option value="other_location" '.$active.'>'. esc_html__( 'Other Location', 'ova-brw' ).'</option>';
        }
        $html .= '</select>';

        return $html;
    }
}

if ( ! function_exists( 'ovabrw_get_setup_locations_html' ) ) {
    function ovabrw_get_setup_locations_html( $name = '', $required = 'required', $selected = '', $pid = '', $type = 'pickup', $validate = '' ) {
        $list_loc_pickup_dropoff = ovabrw_get_list_pickup_dropoff_setup_loctions( $pid );

        $html = '<select name="'.$name.'" class="ovabrw-transport '.$required.'">';
        $html .= '<option value="">'. esc_html__( 'Select Location', 'ova-brw' ).'</option>';

        if ( $type == 'pickup' ) {
            if ( $list_loc_pickup_dropoff ) {
                foreach( $list_loc_pickup_dropoff as $loc => $item_loc ) {
                    $active = ( trim( $loc ) === trim( $selected ) ) ? 'selected="selected"' : '';
                    $html .= '<option data-item_loc="'.esc_attr(json_encode($item_loc)).'" value="'.trim( $loc ).'" '.$active.'>'.trim( $loc ).'</option>';
                }
            }
        }

        if ( $type == 'dropoff' ) {
            $html = '<select name="'.$name.'" class="ovabrw-transport '.$required.'">';
            $html .= '<option value="">'. esc_html__( 'Select Location', 'ova-brw' ).'</option>';
            if ( $list_loc_pickup_dropoff ) {
                foreach( $list_loc_pickup_dropoff as $loc => $item_loc ) {
                    if ( trim( $loc ) === trim( $validate ) && ! empty( $item_loc ) && is_array( $item_loc ) ) {
                        foreach( $item_loc as $dropoff_loc ) {
                            $active = ( trim( $dropoff_loc ) === trim( $selected ) ) ? 'selected="selected"' : '';
                            $html .= '<option value="'.trim( $dropoff_loc ).'" '.$active.'>'.trim( $dropoff_loc ).'</option>';
                        }
                    }
                }
            }
        }

        $html .= '</select>';

        return $html;
    }
}

// Get all number plate
if ( ! function_exists( 'ovabrw_get_all_id_vehicles' ) ) {
    function ovabrw_get_all_id_vehicles() {
        $vehicle = new WP_Query(
            array(
                'post_type'         => 'vehicle',
                'post_status'       => 'publish',
                'posts_per_page'    => '-1'
            )
        );

        return $vehicle;
    }
}

if ( ! function_exists( 'ovabrw_get_vehicle_loc_title' ) ) {
    function ovabrw_get_vehicle_loc_title( $id_metabox ) {
        $vehicle_arr = array();

        $vehicle_ids = get_posts( array(
            'post_type'         => 'vehicle',
            'post_status'       => 'publish',
            'posts_per_page'    => 1,
            'fields'            => 'ids',
            'meta_query'        => array(
                array(
                    'key'     => 'ovabrw_id_vehicle',
                    'value'   => $id_metabox,
                    'compare' => '=',
                ),
            ),
        ));

        if ( ! empty( $vehicle_ids ) && is_array( $vehicle_ids ) ) {
            foreach ( $vehicle_ids as $id ) {
                $vehicle_arr['loc']         = get_post_meta( $id, 'ovabrw_id_vehicle_location', true );
                $vehicle_arr['require_loc'] = get_post_meta( $id, 'ovabrw_vehicle_require_location', true );
                $vehicle_arr['untime']      = get_post_meta( $id, 'ovabrw_id_vehicle_untime_from_day', true );
                $vehicle_arr['id_vehicle']  = get_post_meta( $id, 'ovabrw_id_vehicle', true );
                $vehicle_arr['title']       = get_the_title( $id );
            }
        }

        return $vehicle_arr;
    }
}

if ( ! function_exists( 'ovabrw_taxonomy_dropdown' ) ) {
    function ovabrw_taxonomy_dropdown( $selected, $required, $exclude_id, $slug_taxonomy, $name_taxonomy ) {
        $args = array(
            'show_option_all'    => '',
            'show_option_none'   => esc_html__( 'All ', 'ovabrw' ) . esc_html( $name_taxonomy ) ,
            'option_none_value'  => '',
            'orderby'            => 'ID',
            'order'              => 'ASC',
            'show_count'         => 0,
            'hide_empty'         => 0,
            'child_of'           => 0,
            'exclude'            => $exclude_id,
            'include'            => '',
            'echo'               => 0,
            'selected'           => $selected,
            'hierarchical'       => 1,
            'name'               => $slug_taxonomy.'_name',
            'id'                 => '',
            'class'              => 'postform '.$required,
            'depth'              => 0,
            'tab_index'          => 0,
            'taxonomy'           => $slug_taxonomy,
            'hide_if_empty'      => false,
            'value_field'        => 'slug',
        );

        return wp_dropdown_categories($args);
    }
}

/* Select html Category Rental */
if ( ! function_exists( 'ovabrw_cat_rental' ) ) {
    function ovabrw_cat_rental( $selected = '', $required = '', $exclude_id = '', $label = '' ) {
        if ( ! $label ) {
            $label = esc_html__( 'Select Category', 'ova-brw' );
        }
        
        $args = array(
            'show_option_all'    => '',
            'show_option_none'   => $label,
            'option_none_value'  => '',
            'orderby'            => 'ID',
            'order'              => 'ASC',
            'show_count'         => 0,
            'hide_empty'         => 0,
            'child_of'           => 0,
            'exclude'            => $exclude_id,
            'include'            => '',
            'echo'               => 0,
            'selected'           => $selected,
            'hierarchical'       => 1,
            'name'               => 'cat',
            'id'                 => '',
            'class'              => 'postform '.$required,
            'depth'              => 0,
            'tab_index'          => 0,
            'taxonomy'           => 'product_cat',
            'hide_if_empty'      => false,
            'value_field'        => 'slug',
        );

        return wp_dropdown_categories($args);
    }
}

// Return start time, end time when rental is period hour or time
if ( ! function_exists( 'get_rental_info_period' ) ) {
    function get_rental_info_period( $product_id, $start_date, $ovabrw_rental_type, $ovabrw_period_package_id ) {
        $start_date     = $start_date == '' ? null : $start_date;

        $ovabrw_unfixed = get_post_meta( $product_id, 'ovabrw_unfixed_time', true );
        if ( $ovabrw_unfixed != 'yes' ) {
            $start_date = date('Y-m-d', $start_date);
        } else {
            $start_date = date('Y-m-d H:i', $start_date);
        }

        $rental_start_time  = $rental_end_time = 0;
        $period_label       = '';
        $period_price       = 0;
        $start_date_totime  = strtotime($start_date);
        $package_type       = '';

        if ( trim( $ovabrw_rental_type ) == trim( 'period_time' ) ) {
            $ovabrw_petime_id           = get_post_meta( $product_id, 'ovabrw_petime_id', true );
            $ovabrw_petime_price        = get_post_meta( $product_id, 'ovabrw_petime_price', true );
            $ovabrw_petime_days         = get_post_meta( $product_id, 'ovabrw_petime_days', true );
            $ovabrw_petime_label        = get_post_meta( $product_id, 'ovabrw_petime_label', true );
            $ovabrw_petime_discount     = get_post_meta( $product_id, 'ovabrw_petime_discount', true );
            $ovabrw_package_type        = get_post_meta( $product_id, 'ovabrw_package_type', true );
            $ovabrw_pehour_unfixed      = get_post_meta( $product_id, 'ovabrw_pehour_unfixed', true );
            $ovabrw_pehour_start_time   = get_post_meta( $product_id, 'ovabrw_pehour_start_time', true );
            $ovabrw_pehour_end_time     = get_post_meta( $product_id, 'ovabrw_pehour_end_time', true );

            if ( $ovabrw_petime_id ) { 
                foreach( $ovabrw_petime_id as $key => $value ) {
                    if ( $ovabrw_petime_id[$key] ==  $ovabrw_period_package_id ) {
                        // Check pakage type
                        if ( $ovabrw_package_type[$key] == 'inday' ) {
                            $rental_start_time  = isset( $ovabrw_pehour_start_time[$key] ) ? strtotime( $start_date.' '.$ovabrw_pehour_start_time[$key] ) : 0;
                            $rental_end_time    = isset( $ovabrw_pehour_end_time[$key] ) ? strtotime( $start_date.' '.$ovabrw_pehour_end_time[$key] ) : 0;

                            if ( $ovabrw_unfixed == 'yes' ) {
                                $retal_pehour_unfixed   = isset( $ovabrw_pehour_unfixed[$key] ) ? (float)$ovabrw_pehour_unfixed[$key] : 0;
                                $rental_start_time      = $start_date_totime;
                                $rental_end_time        = $start_date_totime + $retal_pehour_unfixed * 3600;
                            }

                            $period_label = isset( $ovabrw_petime_label[$key] ) ? $ovabrw_petime_label[$key] : '';
                            $period_price = isset( $ovabrw_petime_price[$key] ) ? floatval( $ovabrw_petime_price[$key] ) : 0;
                            $package_type = 'inday';

                            if ( isset($ovabrw_petime_discount[$key]) && $ovabrw_petime_discount[$key]['price'] ) {
                                foreach( $ovabrw_petime_discount[$key]['price'] as $k => $v ) {
                                    // Start Time Discount < Rental Time < End Time Discount
                                    $start_time_dis = strtotime( $ovabrw_petime_discount[$key]['start_time'][$k] );
                                    $end_time_dis   = strtotime( $ovabrw_petime_discount[$key]['end_time'][$k] );

                                    if ( $start_time_dis <= $start_date_totime && $start_date_totime <= $end_time_dis ) {
                                        $period_price = floatval( $ovabrw_petime_discount[$key]['price'][$k] );
                                        break;
                                    }
                                }    
                            }
                        }elseif ( $ovabrw_package_type[$key] == 'other' ) {
                            if ( $ovabrw_unfixed == 'yes' ) {
                                $start_date_date = date( 'Y-m-d H:i', $start_date_totime ) ;
                            } else {
                                $start_date_date = date( 'Y-m-d', $start_date_totime ) ;
                            }
                            
                            $start_date_totime  = strtotime( $start_date_date );
                            $rental_start_time  = $start_date_totime;
                            $rental_end_time    = $start_date_totime + intval( $ovabrw_petime_days[$key] )*24*60*60;
                            $period_label       = isset( $ovabrw_petime_label[$key] ) ? $ovabrw_petime_label[$key] : '';
                            $period_price       = isset( $ovabrw_petime_price[$key] ) ? floatval( $ovabrw_petime_price[$key] ) : 0;
                            $package_type       = 'other';

                            if ( isset($ovabrw_petime_discount[$key]) && $ovabrw_petime_discount[$key]['price'] ) {
                                foreach( $ovabrw_petime_discount[$key]['price'] as $k => $v ) {
                                    // Start Time Discount < Rental Time < End Time Discount
                                    $start_time_dis = strtotime( $ovabrw_petime_discount[$key]['start_time'][$k] );
                                    $end_time_dis   = strtotime( $ovabrw_petime_discount[$key]['end_time'][$k] );

                                    if ( $start_time_dis <= $start_date_totime && $start_date_totime <= $end_time_dis ) {
                                        $period_price = floatval( $ovabrw_petime_discount[$key]['price'][$k] );
                                        break;
                                    }
                                }    
                            }
                        }
                        break;
                    }
                }
            }
        }

        return array( 'start_time' => $rental_start_time, 'end_time' => $rental_end_time, 'period_label' => $period_label, 'period_price' => $period_price, 'package_type' => $package_type );
    }
}

// Get Real Quantity
if ( ! function_exists( 'get_real_quantity' ) ) {
    function get_real_quantity( $product_quantity, $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date ) {
        if ( ! is_int( $ovabrw_pickup_date ) ) {
            $ovabrw_pickup_date = strtotime( $ovabrw_pickup_date );
        }
        if ( ! is_int( $ovabrw_pickoff_date ) ) {
            $ovabrw_pickoff_date = strtotime( $ovabrw_pickoff_date );
        }

        $quantity_hour  = 0;
        $rent_time      = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
        $price_type     = get_post_meta( $product_id, 'ovabrw_price_type', true );

        /* Global */
        $gl_quantity        = ovabrw_quantity_global( $product_id, $rent_time );
        $product_quantity   = $gl_quantity.' '.ovabrw_text_time( $price_type , $rent_time);

        if ( $price_type == 'mixed' && $rent_time['rent_time_day_raw'] >= 1 ) {
            $day_mix    = floor( $rent_time["rent_time_day_raw"] );
            $hour_mix   = $rent_time["rent_time_hour_raw"] - $day_mix * 24;
            $product_quantity = $day_mix .' '. esc_html__( 'Day(s)', 'ova-brw' ) . '<br>' . $hour_mix .' '. esc_html__( 'Hour(s)', 'ova-brw' ) ;
        }

        // Display for case: PickUp/PickOff in Special Time
        $ovabrw_rt_startdate    = get_post_meta( $product_id, 'ovabrw_rt_startdate', true );
        $ovabrw_rt_starttime    = get_post_meta( $product_id, 'ovabrw_rt_starttime', true );
        $ovabrw_rt_enddate      = get_post_meta( $product_id, 'ovabrw_rt_enddate', true );
        $ovabrw_rt_endtime      = get_post_meta( $product_id, 'ovabrw_rt_endtime', true );
        $gl_quantity_total      = ovabrw_quantity_global( $product_id, $rent_time );
        $price_type             = get_post_meta( $product_id, 'ovabrw_price_type', true );
        $hour_total_text        = '';

        if ( $price_type === "mixed" ) {
            $gl_quantity_total = floor($rent_time['rent_time_day_raw']);
            if ( $rent_time['rent_time_day_raw'] < 1 ) {
                $gl_quantity_total = (int)$rent_time['rent_time_hour'];
            }

            $gl_quantity_total = floor( $rent_time['rent_time_day_raw'] );
            if ( $gl_quantity_total <= 0 ) { 
                $quantity_hour = $rent_time['rent_time_hour_raw'];
            } elseif ( $gl_quantity_total > 0 ) {
                $quantity_hour = ($rent_time['rent_time_hour_raw'] - $gl_quantity_total * 24);
            }

            if ( $quantity_hour > 0 ) {
                $hour_total_text = $quantity_hour .' '.esc_html__( 'Hour(s)', 'ova-brw' );
            }
        }

        $i = 0; $data_max = 0; $flag_max = false;
        $product_quantity = '';
        $rt_quantity_total = 0;

        if ( $ovabrw_rt_startdate && apply_filters( 'ovabrw_ft_show_special_time', true ) ) {
            foreach ( $ovabrw_rt_startdate as $key_rt => $value_rt ) {
                $i++;

                if ( $i === 1 ) {
                    $data_max = strtotime( $ovabrw_rt_enddate[$key_rt] . ' ' .  $ovabrw_rt_endtime[$key_rt]);
                    $flag_max = true;
                } elseif ( $data_max < strtotime( $ovabrw_rt_enddate[$key_rt] . ' ' .  $ovabrw_rt_endtime[$key_rt] ) ) {
                    $data_max = strtotime( $ovabrw_rt_enddate[$key_rt] . ' ' .  $ovabrw_rt_endtime[$key_rt] );
                    $flag_max = true;
                } else {
                    $flag_max = false;
                }

                $start_date_str = $value_rt  . ' ' . $ovabrw_rt_starttime[$key_rt];
                $end_date_str   = $ovabrw_rt_enddate[$key_rt]  . ' ' . $ovabrw_rt_endtime[$key_rt];

                // If Special_Time_Start_Date <= pickup_date && pickoff_date <= Special_Time_End_Date
                if ( $ovabrw_pickup_date >= strtotime( $start_date_str ) && $ovabrw_pickoff_date <= strtotime( $end_date_str ) ) {
                    $rt_quantity        = ovabrw_quantity_global( $product_id, $rent_time );
                    $product_quantity   = $rt_quantity.' '.ovabrw_text_time_rt( $price_type, $rent_time );
                    $rt_quantity_total  = $gl_quantity_total;

                    if ( $flag_max && $quantity_hour > 0 ) {
                        $hour_total_text = $quantity_hour . esc_html__( ' Special Hour(s)', 'ova-brw' );
                    }
                } elseif ( $ovabrw_pickup_date < strtotime( $start_date_str ) && $ovabrw_pickoff_date <= strtotime( $end_date_str ) && $ovabrw_pickoff_date >= strtotime( $start_date_str ) ) {
                    $gl_quantity_array  = get_time_bew_2day( $ovabrw_pickup_date, strtotime( $start_date_str ), $product_id );
                    $gl_quantity        = ovabrw_quantity_global( $product_id, $gl_quantity_array );
                    $rt_quantity_array  = get_time_bew_2day( strtotime( $start_date_str ), $ovabrw_pickoff_date );

                    $rt_quantity = 0;
                    if ( $price_type == 'mixed' ) {
                        $rt_quantity = floor( $rt_quantity_array['rent_time_day_raw'] );
                    } elseif ( $price_type == 'hour' ) {
                        $rt_quantity = (int)$rt_quantity_array['rent_time_hour'];
                    } else {
                        $rt_quantity = (int)$rt_quantity_array['rent_time_day'];
                    }

                    $rt_quantity_total += $rt_quantity;
                    $product_quantity .= $rt_quantity.' '.ovabrw_text_time_rt( $price_type , $rent_time) . '<br/>';

                    if ( $flag_max && $quantity_hour > 0 ) {
                        $hour_total_text = $quantity_hour . esc_html__( ' Special Hour(s)', 'ova-brw' );
                    }
                } elseif ( $ovabrw_pickup_date >= strtotime( $start_date_str ) && $ovabrw_pickup_date <= strtotime( $end_date_str ) && $ovabrw_pickoff_date >= strtotime( $end_date_str ) ) {
                    $gl_quantity_array  = get_time_bew_2day( strtotime( $end_date_str ), $ovabrw_pickoff_date, $product_id );
                    $gl_quantity        = ovabrw_quantity_global( $product_id, $gl_quantity_array );
                    $rt_quantity_array  = get_time_bew_2day( $ovabrw_pickup_date, strtotime( $end_date_str ) );

                    $rt_quantity = 0;
                    if ( $price_type == 'mixed' ) {
                        $rt_quantity = floor( $rt_quantity_array['rent_time_day_raw'] );
                    } elseif( $price_type == 'hour' ) {
                        $rt_quantity = (int)$rt_quantity_array['rent_time_hour'];
                    } else {
                        $rt_quantity = (int)$rt_quantity_array['rent_time_day'];
                    }

                    $rt_quantity_total += $rt_quantity;
                    $product_quantity   .= $rt_quantity.' '.ovabrw_text_time_rt( $price_type , $rent_time) . '<br/>';

                    if ( $flag_max && $quantity_hour > 0 ) {
                        $hour_total_text = $quantity_hour . esc_html__( ' Special Hour(s)', 'ova-brw' );
                    }
                } elseif ( $ovabrw_pickup_date < strtotime( $start_date_str ) && $ovabrw_pickoff_date > strtotime( $end_date_str ) ) {
                    // Time section 1
                    $gl_rent_time_1 = get_time_bew_2day( $ovabrw_pickup_date, strtotime( $start_date_str ) );
                    $gl_quantity_1  = ovabrw_quantity_global( $product_id, $gl_rent_time_1 );
                    $gl_rent_time_3 = get_time_bew_2day( strtotime( $end_date_str ), $ovabrw_pickoff_date );
                    $gl_quantity_3  = ovabrw_quantity_global( $product_id, $gl_rent_time_3 );
                    $rt_rent_time_2 = get_time_bew_2day( strtotime( $start_date_str ), strtotime( $end_date_str ) );

                    $rt_quantity_2 = 0;
                    if ( $price_type == 'mixed' ) {
                        $rt_quantity_2 = floor( $rt_rent_time_2['rent_time_day_raw'] );
                    } elseif ( $price_type == 'hour' ) {
                        $rt_quantity_2 = (int)$rt_rent_time_2['rent_time_hour'];
                    } else {
                        $rt_quantity_2 = (int)$rt_rent_time_2['rent_time_day'];
                    }
                    
                    $rt_quantity_total  += $rt_quantity_2;
                    $gl_quantity        = $gl_quantity_1 + $gl_quantity_3;
                    $product_quantity   .= $rt_quantity_2.' '.ovabrw_text_time_rt( $price_type, $rent_time ).'<br/> ';

                    if ( $flag_max && $quantity_hour > 0 ) {
                        $hour_total_text = $quantity_hour . esc_html__( ' Special Hour(s)', 'ova-brw' );
                    }
                }
            }
        }

        $gl_quantity = $gl_quantity_total - $rt_quantity_total;

        if ( $gl_quantity > 0 ) {
            $product_quantity .= $gl_quantity.' '.ovabrw_text_time_gl( $price_type , $rent_time);
        }
        if ( $hour_total_text ) {
            if ( $product_quantity ) {
                $product_quantity .= '<br>' . $hour_total_text;
            } else {
                $product_quantity .= $hour_total_text;
            }
        }
        
        return $product_quantity; 
    }
}

// Get Real Price
if ( ! function_exists( 'get_real_price' ) ) {
    function get_real_price( $product_price, $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date ) {
        if ( ! is_int( $ovabrw_pickup_date ) ) {
            $ovabrw_pickup_date = strtotime( $ovabrw_pickup_date );
        }
        if ( ! is_int( $ovabrw_pickoff_date ) ) {
            $ovabrw_pickoff_date = strtotime( $ovabrw_pickoff_date );
        }

        $rent_time  = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
        $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

        // Global
        $gl_price_total     = ovabrw_price_global( $product_id, $rent_time );
        $gl_quantity_total  = ovabrw_quantity_global( $product_id, $rent_time );
        $gl_price           = ovabrw_price_global( $product_id, $rent_time );
        $product_price      = ovabrw_wc_price( $gl_price );

        $ovabrw_rt_startdate    = get_post_meta( $product_id, 'ovabrw_rt_startdate', true );
        $ovabrw_rt_starttime    = get_post_meta( $product_id, 'ovabrw_rt_starttime', true );
        $ovabrw_rt_enddate      = get_post_meta( $product_id, 'ovabrw_rt_enddate', true );
        $ovabrw_rt_endtime      = get_post_meta( $product_id, 'ovabrw_rt_endtime', true );

        $price_hour     = '';
        $quantity_hour  = $rt_quantity_total = 0;

        if ( $price_type === "mixed" ) {
            $total_price_day    = get_price_by_rental_type_day( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date );
            $quantity_day       = floor($rent_time['rent_time_day_raw']);

            if ( $quantity_day <= 0 ) { 
                $quantity_hour = $rent_time['rent_time_hour_raw'];
            } elseif ( $quantity_day > 0 ) {
                $quantity_hour = ($rent_time['rent_time_hour_raw'] - $quantity_day * 24);
            }

            if ( $quantity_hour > 0 ) {
                $price_hour = ovabrw_wc_price( get_regular_price_hour_mixed( $product_id, $quantity_hour) ) .' '. esc_html__( 'Hour(s)', 'ova-brw' );
            }
        }
        
        $product_price = '';
        $flag = true;

        $i = 0; $data_max = 0; $flag_max = false;

        if ( $ovabrw_rt_startdate && ( $price_type === 'day' || $price_type === 'hour' || $price_type === 'mixed' ) ) {
            foreach ( $ovabrw_rt_startdate as $key_rt => $value_rt ) {
                $i++;

                if ( $i === 1 ) {
                    $data_max = strtotime( $ovabrw_rt_enddate[$key_rt]  . ' ' .  $ovabrw_rt_endtime[$key_rt]);
                    $flag_max = true;
                } elseif ( $data_max < strtotime( $ovabrw_rt_enddate[$key_rt]  . ' ' .  $ovabrw_rt_endtime[$key_rt] ) ) {
                    $data_max = strtotime( $ovabrw_rt_enddate[$key_rt]  . ' ' .  $ovabrw_rt_endtime[$key_rt] );
                    $flag_max = true;
                } else {
                    $flag_max = false;
                }

                $start_date_str = strtotime( $value_rt . ' ' . $ovabrw_rt_starttime[$key_rt] );
                $end_date_str   = strtotime( $ovabrw_rt_enddate[$key_rt] . ' ' . $ovabrw_rt_endtime[$key_rt] );

                // If Special_Time_Start_Date <= pickup_date && pickoff_date <= Special_Time_End_Date
                if ( $ovabrw_pickup_date >= $start_date_str && $ovabrw_pickoff_date <= $end_date_str ) {
                    $rt_price           = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );
                    $product_price      .= ovabrw_wc_price( ovabrw_get_price_include_tax( $product_id, $rt_price ) ).' '.ovabrw_text_time_rt( $price_type, $rent_time );
                    $flag               = false;
                    $rt_quantity_total  = $gl_quantity_total;

                    if ( $flag_max && $quantity_hour > 0 ) {
                        $ovabrw_rt_price_hour   = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
                        $rt_price_hour          = get_special_price_hour_mixed($product_id, $ovabrw_rt_price_hour[$key_rt], $key_rt, $quantity_hour);
                        $price_hour             = ovabrw_wc_price( ovabrw_get_price_include_tax( $product_id, $rt_price_hour ) ) . esc_html__( ' Special Hour(s)', 'ova-brw' );
                    }
                } elseif ( $ovabrw_pickup_date < $start_date_str && $ovabrw_pickoff_date <= $end_date_str && $ovabrw_pickoff_date >= $start_date_str ) {
                    $gl_quantity_array  = get_time_bew_2day( $ovabrw_pickup_date, $start_date_str, $product_id );
                    $gl_price           = ovabrw_price_global( $product_id, $gl_quantity_array );
                    $rt_quantity_array  = get_time_bew_2day( $start_date_str, $ovabrw_pickoff_date, $product_id );
                    $rt_price           = ovabrw_price_special_time( $product_id, $rt_quantity_array, $key_rt );
                    $product_price      .= ovabrw_wc_price( ovabrw_get_price_include_tax( $product_id, $rt_price ) ).' '.ovabrw_text_time_rt( $price_type, $rent_time ).'<br/>';

                    $rt_quantity = 0;
                    if ( $price_type == 'mixed' ) {
                        $rt_quantity = floor( $rt_quantity_array['rent_time_day_raw'] );
                    } elseif ( $price_type == 'hour' ) {
                        $rt_quantity = (int)$rt_quantity_array['rent_time_hour'];
                    } else {
                        $rt_quantity = (int)$rt_quantity_array['rent_time_day'];
                    }

                    $rt_quantity_total += $rt_quantity;

                    if ( $flag_max && $quantity_hour > 0 ) {
                        $ovabrw_rt_price_hour   = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
                        $rt_price_hour          = get_special_price_hour_mixed($product_id, $ovabrw_rt_price_hour[$key_rt], $key_rt, $quantity_hour);
                        $price_hour             = ovabrw_wc_price( ovabrw_get_price_include_tax( $product_id, $rt_price_hour ) ) . esc_html__( ' Special Hour(s)', 'ova-brw' );
                    }
                } elseif ( $ovabrw_pickup_date >= $start_date_str && $ovabrw_pickup_date <= $end_date_str && $ovabrw_pickoff_date >= $end_date_str ) {
                    $rt_quantity_array  = get_time_bew_2day( $ovabrw_pickup_date, $end_date_str );
                    $rt_price           = ovabrw_price_special_time( $product_id, $rt_quantity_array, $key_rt );
                    $gl_quantity_array  = get_time_bew_2day( $end_date_str, $ovabrw_pickoff_date, $product_id );
                    $gl_price           = ovabrw_price_global( $product_id, $gl_quantity_array );
                    $product_price      .= ovabrw_wc_price( ovabrw_get_price_include_tax( $product_id, $rt_price ) ).' '.ovabrw_text_time_rt( $price_type, $rent_time ).'<br>';

                    $rt_quantity = 0;
                    if ( $price_type == 'mixed' ) {
                        $rt_quantity = floor( $rt_quantity_array['rent_time_day_raw'] );
                    } elseif( $price_type == 'hour' ) {
                        $rt_quantity = (int)$rt_quantity_array['rent_time_hour'];
                    } else {
                        $rt_quantity = (int)$rt_quantity_array['rent_time_day'];
                    }

                    $rt_quantity_total += $rt_quantity;

                    if ( $flag_max && $quantity_hour > 0 ) {
                        $ovabrw_rt_price_hour   = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
                        $rt_price_hour          = get_special_price_hour_mixed($product_id, $ovabrw_rt_price_hour[$key_rt], $key_rt, $quantity_hour);
                        $price_hour             = ovabrw_wc_price( ovabrw_get_price_include_tax( $product_id, $rt_price_hour ) ) . esc_html__( ' Special Hour(s)', 'ova-brw' );
                    }
                } elseif ( $ovabrw_pickup_date < $start_date_str && $ovabrw_pickoff_date > $end_date_str ) {
                    // Time section 1
                    $gl_quantity_array  = get_time_bew_2day( $ovabrw_pickup_date, $start_date_str, $product_id );
                    $gl_price           = ovabrw_price_global( $product_id, $gl_quantity_array );
                    $rent_time_2        = get_time_bew_2day( $start_date_str, $end_date_str, $product_id );
                    $rt_price           = ovabrw_price_special_time( $product_id, $rent_time_2, $key_rt );
                    $product_price      .= ovabrw_wc_price( ovabrw_get_price_include_tax( $product_id, $rt_price ) ).' '.ovabrw_text_time_rt( $price_type, $rent_time ).'<br/>';

                    $rt_quantity_2 = 0;
                    if ( $price_type == 'mixed' ) {
                        $rt_quantity_2 = floor( $rent_time_2['rent_time_day_raw'] );
                    } elseif ( $price_type == 'hour' ) {
                        $rt_quantity_2 = (int)$rent_time_2['rent_time_hour'];
                    } else {
                        $rt_quantity_2 = (int)$rent_time_2['rent_time_day'];
                    }
                    
                    $rt_quantity_total += $rt_quantity_2;

                    if ( $flag_max && $quantity_hour > 0 ) {
                        $ovabrw_rt_price_hour   = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
                        $rt_price_hour          = get_special_price_hour_mixed($product_id, $ovabrw_rt_price_hour[$key_rt], $key_rt, $quantity_hour);
                        $price_hour             = ovabrw_wc_price( ovabrw_get_price_include_tax( $product_id, $rt_price_hour ) ) . esc_html__( ' Special Hour(s)', 'ova-brw' );
                    }
                }
            }
        }

        $gl_price_total = ovabrw_price_global( $product_id, $rent_time, $rt_quantity_total );

        if ( $product_price === '' ) {
            if ( $price_type === 'day' ) {
                $product_price = ovabrw_wc_price( ovabrw_get_price_include_tax( $product_id, $gl_price_total ), [], false );
            } else {
                $product_price = ovabrw_wc_price( ovabrw_get_price_include_tax( $product_id, $gl_price_total ) );
            }
            
        } elseif ( ! $flag ) {
            $product_price = $product_price;
        } else {
            if ( $price_hour ) {
                $product_price .= ovabrw_wc_price( ovabrw_get_price_include_tax( $product_id, $gl_price_total ) ).' '.ovabrw_text_time_gl( $price_type, $rent_time ) . '<br>' . $price_hour ;
            } else {
                $product_price .= ovabrw_wc_price( ovabrw_get_price_include_tax( $product_id, $gl_price_total ) ).' '.ovabrw_text_time_gl( $price_type, $rent_time );
            }
        }

        return $product_price; 
    }
}

// Get all order has pickup date larger current time
if ( ! function_exists( 'ovabrw_get_orders_feature' ) ) {
    function ovabrw_get_orders_feature() {
        global $wpdb;

        $order_ids      = [];
        $order_status   = brw_list_order_status();

        if ( ovabrw_wc_custom_orders_table_enabled() ) {
            $order_ids = $wpdb->get_col("
                SELECT DISTINCT o.id
                FROM {$wpdb->prefix}wc_orders AS o
                WHERE o.status IN ( '" . implode( "','", $order_status ) . "' )
            ");
        } else {
            $order_ids = $wpdb->get_col("
                SELECT DISTINCT oitems.order_id
                FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                LEFT JOIN {$wpdb->posts} AS posts
                ON oitems.order_id = posts.ID
                WHERE posts.post_type = 'shop_order'
                AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
            ");
        }

        return $order_ids;
    }
}

// Get orders that have not yet created a remaining invoice
if ( ! function_exists( 'ovabrw_get_orders_not_remaining_invoice' ) ) {
    function ovabrw_get_orders_not_remaining_invoice() {
        global $wpdb;

        $order_ids      = [];
        $order_status   = brw_list_order_status();

        if ( ovabrw_wc_custom_orders_table_enabled() ) {
            $order_ids = $wpdb->get_col("
                SELECT DISTINCT o.id
                FROM {$wpdb->prefix}wc_orders AS o
                LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oitems
                ON o.id = oitems.order_id
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                WHERE oitem_meta.meta_key = 'ovabrw_remaining_amount_product'
                AND oitem_meta.meta_value != 0
                AND o.status IN ( '" . implode( "','", $order_status ) . "' )
            ");
        } else {
            $order_ids = $wpdb->get_col("
                SELECT DISTINCT oitems.order_id
                FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                ON oitems.order_item_id = oitem_meta.order_item_id
                LEFT JOIN {$wpdb->posts} AS posts
                ON oitems.order_id = posts.ID
                WHERE posts.post_type = 'shop_order'
                AND oitem_meta.meta_key = 'ovabrw_remaining_amount_product'
                AND oitem_meta.meta_value != 0
                AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
            ");
        }

        return $order_ids;
    }
}

/**
 * Get price include tax
 */
if ( ! function_exists( 'ovabrw_get_price_include_tax' ) ) {
    function ovabrw_get_price_include_tax( $product_id, $product_price ) {
        $display_price_cart = get_option( 'woocommerce_tax_display_cart', 'incl' );

        if ( wc_tax_enabled() && wc_prices_include_tax() && $product_price && $display_price_cart === 'excl' ) {
            $product_data   = wc_get_product($product_id);
            $tax_rates_data = WC_Tax::get_rates( $product_data->get_tax_class() );
            $rate_data      = reset($tax_rates_data);

            if ( $rate_data && isset( $rate_data['rate'] ) ) {

                $rate           = $rate_data['rate'];
                $tax_price      = $product_price - ( $product_price / ( ( $rate / 100 ) + 1 ) );
                $product_price  = $product_price - round( $tax_price, wc_get_price_decimals() );
            }
        }
        
        return apply_filters( 'ovabrw_get_price_include_tax', $product_price );;
    }
}

/**
 * Get html Resources
 */
if ( ! function_exists( 'ovabrw_get_html_resources' ) ) {
    function ovabrw_get_html_resources( $product_id = false, $resources = [], $start_date = false, $end_date = false, $qty = 1, $order_id = false, $resources_qty = [] ) {
        $html = '';
        if ( get_option( 'ova_brw_booking_form_show_extra', 'no' ) == 'no' ) {
            return $html;
        }

        $currency = '';

        if ( $order_id ) {
            $order = wc_get_order( $order_id );

            if ( ! empty( $order ) && is_object( $order ) ) {
                $currency = $order->get_currency();
            }
        }

        if ( ! empty( $resources ) && is_array( $resources ) ) {
            foreach ( $resources as $key => $value ) {
                $rs_data   = [];
                $rs_data   = [
                    $key => $value
                ];
                $rs_price   = ovabrw_convert_price_in_admin( ovabrw_get_total_resources( $product_id, $start_date, $end_date, $rs_data ), $currency );

                $res_qty = isset( $resources_qty[$key] ) && absint( $resources_qty[$key] ) ? ' (x'.absint( $resources_qty[$key] ).')' : '';
                $html   .=  '<dt>' . $value . esc_html__( ': ', 'ova-brw' ) . '</dt><dd>' . ovabrw_wc_price( $rs_price * $qty, ['currency' => $currency] ) . $res_qty . '</dd>';
            }
        }

        return $html;
    }
}

/**
 * Get html Services
 */
if ( ! function_exists( 'ovabrw_get_html_services' ) ) {
    function ovabrw_get_html_services( $product_id = false, $services = [], $start_date = false, $end_date = false, $qty = 1, $order_id = false, $services_qty = [] ) {
        $html = '';

        if ( get_option( 'ova_brw_booking_form_show_extra', 'no' ) == 'no' ) {
            return $html;
        }

        $currency = '';

        if ( $order_id ) {
            $order = wc_get_order( $order_id );

            if ( ! empty( $order ) && is_object( $order ) ) {
                $currency = $order->get_currency();
            }
        }

        $service_id             = get_post_meta( $product_id, 'ovabrw_service_id', true );
        $service_name           = get_post_meta( $product_id, 'ovabrw_service_name', true );
        $service_price          = get_post_meta( $product_id, 'ovabrw_service_price', true );
        $service_duration_type  = get_post_meta( $product_id, 'ovabrw_service_duration_type', true );

        if ( ! empty( $services ) && is_array( $services ) ) {
            foreach ( $services as $value ) {
                if ( ! empty( $value ) ) {
                    $sv_data = [];
                    array_push( $sv_data, $value );
                    foreach ( $service_id as $sv_key => $sv_value ) {
                        if ( in_array( $value, $sv_value ) ) {
                            foreach ( $sv_value as $id_key => $id_value ) {
                                if ( $value == $id_value ) {
                                    $sv_price = ovabrw_convert_price_in_admin( ovabrw_get_total_services( $product_id, $start_date, $end_date, $sv_data ), $currency );
                                    $sv_qty = isset( $services_qty[$value] ) && absint( $services_qty[$value] ) ? ' (x'.absint( $services_qty[$value] ).')' : '';

                                    $html .= '<dt>' . $service_name[$sv_key][$id_key] . esc_html__( ': ', 'ova-brw' ) . '</dt><dd>' . ovabrw_wc_price( $sv_price * $qty, ['currency' => $currency] ) . $sv_qty . '</dd>';
                                }
                            }
                        }
                    }
                }
            }
        }

        return $html;
    }
}

/**
 * Get html Custom checkout fields
 */
if ( ! function_exists( 'ovabrw_get_html_ckf' ) ) {
    function ovabrw_get_html_ckf( $custom_ckf = [] , $qty = 1, $order_id = false, $custom_ckf_qty = [] ) {
        $html = '';

        $currency = '';

        if ( $order_id ) {
            $order = wc_get_order( $order_id );

            if ( ! empty( $order ) && is_object( $order ) ) {
                $currency = $order->get_currency();
            }
        }

        if ( ! empty( $custom_ckf ) && is_array( $custom_ckf ) ) {
            $list_fields = get_option( 'ovabrw_booking_form', array() );

            foreach ( $custom_ckf as $k => $val ) {
                if ( isset( $list_fields[$k] ) && ! empty( $list_fields[$k] ) ) {
                    $type = $list_fields[$k]['type'];

                    if ( $type === 'radio' ) {
                        $val_key = array_search( $val, $list_fields[$k]['ova_values'] );
                        $val_qty = isset( $custom_ckf_qty[$k] ) && absint( $custom_ckf_qty[$k] ) ? absint( $custom_ckf_qty[$k] ) : '';

                        if ( ! is_bool( $val_key ) ) {
                            $price = ovabrw_convert_price_in_admin( $list_fields[$k]['ova_prices'][$val_key], $currency );

                            if ( $price ) {
                                if ( $val_qty ) {
                                    $html .= '<dt>' . $val . esc_html__( ': ', 'ova-brw' ) . '</dt><dd>' . ovabrw_wc_price( $price * $qty, ['currency' => $currency] ) . ' (x'.$val_qty.')</dd>';
                                } else {
                                    $html .= '<dt>' . $val . esc_html__( ': ', 'ova-brw' ) . '</dt><dd>' . ovabrw_wc_price( $price * $qty, ['currency' => $currency] ) . '</dd>';
                                }
                            }
                        }
                    }

                    if ( $type === 'checkbox' ) {
                        if ( ! empty( $val ) && is_array( $val ) ) {
                            foreach ( $val as $val_cb ) {
                                $val_key = array_search( $val_cb, $list_fields[$k]['ova_checkbox_key'] );
                                $val_qty = isset( $custom_ckf_qty[$val_cb] ) && absint( $custom_ckf_qty[$val_cb] ) ? absint( $custom_ckf_qty[$val_cb] ) : '';

                                if ( ! is_bool( $val_key ) ) {
                                    $label = $list_fields[$k]['ova_checkbox_text'][$val_key];
                                    $price = ovabrw_convert_price_in_admin( $list_fields[$k]['ova_checkbox_price'][$val_key], $currency );

                                    if ( $price ) {
                                        if ( $val_qty ) {
                                            $html .= '<dt>' . $label . esc_html__( ': ', 'ova-brw' ) . '</dt><dd>' . ovabrw_wc_price( $price * $qty, ['currency' => $currency] ) . ' (x'.$val_qty.')</dd>';
                                        } else {
                                            $html .= '<dt>' . $label . esc_html__( ': ', 'ova-brw' ) . '</dt><dd>' . ovabrw_wc_price( $price * $qty, ['currency' => $currency] ) . '</dd>';
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ( $type === 'select' ) {
                        $val_key = array_search( $val, $list_fields[$k]['ova_options_key'] );
                        $val_qty = isset( $custom_ckf_qty[$val] ) && absint( $custom_ckf_qty[$val] ) ? absint( $custom_ckf_qty[$val] ) : '';

                        if ( ! is_bool( $val_key ) ) {
                            $label = $list_fields[$k]['ova_options_text'][$val_key];
                            $price = ovabrw_convert_price_in_admin( $list_fields[$k]['ova_options_price'][$val_key], $currency );

                            if ( $price ) {
                                if ( $val_qty ) {
                                    $html .= '<dt>' . $label . esc_html__( ': ', 'ova-brw' ) . '</dt><dd>' . ovabrw_wc_price( $price * $qty, ['currency' => $currency] ) . ' (x'.$val_qty.')</dd>';
                                } else {
                                    $html .= '<dt>' . $label . esc_html__( ': ', 'ova-brw' ) . '</dt><dd>' . ovabrw_wc_price( $price * $qty, ['currency' => $currency] ) . '</dd>';
                                }
                            }
                        }
                    }
                }
            }
        }

        return $html;
    }
}

/**
 * Get html Resources + Services
 */
if ( ! function_exists( 'ovabrw_get_html_extra' ) ) {
    function ovabrw_get_html_extra( $resource_html= '', $service_html = '', $ckf_html = '' ) {
        $html = '';

        if ( ! empty( $resource_html ) || ! empty( $service_html ) || ! empty( $ckf_html ) ) {
            $html .= '<dl class="variation ovabrw_extra_item">';
            $html .= $resource_html;
            $html .= $service_html;
            $html .= $ckf_html;
            $html .= '</dl>';
        }

        return apply_filters( 'ovabrw_ft_get_html_extra', $html );
    }
}

/**
 * Get html total pay when wc_tax_enabled()
 */
if ( ! function_exists( 'ovabrw_get_html_total_pay' ) ) {
    function ovabrw_get_html_total_pay( $total, $cart_item ) {
        $html = '';

        if ( ! $total || ! $cart_item ) {
            return $html;
        }

        $product_id = $cart_item['product_id'];
        $product    = wc_get_product( $product_id );
        $tax_rates  = WC_Tax::get_rates( $product->get_tax_class() );

        if ( wc_tax_enabled() ) {
            if ( wc_prices_include_tax() ) {
                if ( ! WC()->cart->display_prices_including_tax() ) {
                    $incl_tax = WC_Tax::calc_inclusive_tax( $total, $tax_rates );
                    $total   -= array_sum( $incl_tax );
                }
            } else {
                if ( WC()->cart->display_prices_including_tax() ) {
                    $excl_tax = WC_Tax::calc_exclusive_tax( $total, $tax_rates );
                    $total   += array_sum( $excl_tax ); 
                }
            }
        }
        $html .= '<br/><small>' . sprintf( __( '%s payable in total', 'ova-brw' ), ovabrw_wc_price( $total, [], false ) ) . '</small>';

        return apply_filters( 'ovabrw_ft_get_html_total_pay', $html );
    }
}

/**
 * Get price when wc_tax_enabled()
 */
if ( ! function_exists( 'ovabrw_get_price_tax' ) ) {
    function ovabrw_get_price_tax( $price, $cart_item ) {
        if ( ! $price || ! $cart_item ) {
            return 0;
        }

        $product_id = $cart_item['product_id'];
        $product    = wc_get_product( $product_id );
        $tax_rates  = WC_Tax::get_rates( $product->get_tax_class() );

        if ( wc_tax_enabled() ) {
            if ( wc_prices_include_tax() ) {
                if ( ! WC()->cart->display_prices_including_tax() ) {
                    $incl_tax = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
                    $price   -= round( array_sum( $incl_tax ), wc_get_price_decimals() ); 
                }
            } else {
                if ( WC()->cart->display_prices_including_tax() ) {
                    $excl_tax = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                    $price   += round( array_sum( $excl_tax ), wc_get_price_decimals() ); 
                }
            }
        }

        return apply_filters( 'ovabrw_ft_get_price_tax', $price );
    }
}

/**
 * Get taxes when wc_tax_enabled()
 */
if ( ! function_exists( 'ovabrw_get_taxes_by_price' ) ) {
    function ovabrw_get_taxes_by_price( $price, $product_id, $prices_include_tax ) {
        $taxes = 0;

        if ( ! $price || ! $product_id || ! $prices_include_tax ) {
            return $taxes;
        }

        $product    = wc_get_product( $product_id );
        $tax_rates  = WC_Tax::get_rates( $product->get_tax_class() );

        if ( wc_tax_enabled() ) {
            if ( $prices_include_tax == 'yes' ) {
                $incl_tax = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
                $taxes    = round( array_sum( $incl_tax ), wc_get_price_decimals() );
            } else {
                $excl_tax = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                $taxes    = round( array_sum( $excl_tax ), wc_get_price_decimals() );
            }
        }

        return apply_filters( 'ovabrw_ft_get_taxes_by_price', $taxes );
    }
}

/**
 * Get tax_amount by price and tax rates
 */
if ( ! function_exists( 'ovabrw_get_tax_amount_by_tax_rates' ) ) {
    function ovabrw_get_tax_amount_by_tax_rates( $price, $tax_rates, $prices_include_tax ) {
        if ( ! $price || ! $tax_rates || ! $prices_include_tax ) {
            return 0;
        }

        if ( wc_tax_enabled() ) {
            if ( $prices_include_tax == 'yes' ) {
                $tax_amount = round( $price - ( $price / ( ( $tax_rates / 100 ) + 1 ) ), wc_get_price_decimals() );
            } else {
                $tax_amount = round( $price * ( $tax_rates / 100 ), wc_get_price_decimals() );
            }
        }

        return apply_filters( 'ovabrw_ft_get_tax_amount_by_tax_rates', $tax_amount );
    }
}

/**
 * Get html custom checkout fields
 */
if ( ! function_exists( 'ovabrw_get_html_ckf_order' ) ) {
    function ovabrw_get_html_ckf_order( $id ) {
        if ( ! $id ) return '';

        $list_ckf_output    = ovabrw_get_list_field_checkout( $id );
        $special_fields     = [ 'textarea', 'select', 'radio', 'checkbox', 'file' ];

        ob_start();
        if ( ! empty( $list_ckf_output ) && is_array( $list_ckf_output ) ):
            foreach ( $list_ckf_output as $key => $field ):
                if ( array_key_exists( 'enabled', $field ) && $field['enabled'] == 'on' ):
                    if ( array_key_exists('required', $field) && $field['required'] == 'on' ) {
                        $class_required = 'required';
                    } else {
                        $class_required = '';
                    }
        ?>
                <div class="ovabrw-ckf ovabrw-ckf-<?php echo esc_attr( $key ); ?>">
                    <label><?php echo esc_html( $field['label'] ); ?></label>
                    <?php if ( $field['type'] === 'checkbox' ): ?>
                        <span class="ovabrw-ckf-span ovabrw-ckf-checkbox <?php echo 'ovabrw-'.esc_attr( $class_required ); ?>">
                    <?php else: ?>
                        <span class="ovabrw-ckf-span">
                    <?php endif; ?>
                        <?php if ( ! in_array( $field['type'], $special_fields ) ): ?>
                            <input 
                                type="<?php echo esc_attr( $field['type'] ); ?>" 
                                name="<?php echo esc_attr( $key ).'['.$id.']'; ?>" 
                                class="<?php echo esc_attr( $field['class'] ); ?>" 
                                placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
                                value="<?php echo esc_attr( $field['default'] ); ?>" 
                                <?php echo esc_attr( $class_required ); ?>/>
                        <?php endif; ?>
                        <?php if ( $field['type'] === 'textarea' ): ?>
                            <textarea name="<?php echo esc_attr( $key ) ;?>" class="<?php echo esc_attr( $field['class'] ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" value="<?php echo $field['default']; ?>" cols="10" rows="5" <?php echo esc_attr( $class_required ); ?>></textarea>
                        <?php endif; ?>
                        <?php if ( $field['type'] === 'select' ):
                            $ova_options_key = $ova_options_text = $ova_options_qty = [];

                            if ( array_key_exists( 'ova_options_key', $field ) ) {
                                $ova_options_key = $field['ova_options_key'];
                            }

                            if ( array_key_exists( 'ova_options_text', $field ) ) {
                                $ova_options_text = $field['ova_options_text'];
                            }

                            if ( array_key_exists( 'ova_options_qty', $field ) ) {
                                $ova_options_qty = $field['ova_options_qty'];
                            }
                        ?>
                            <div class="ovabrw-select">
                                <select 
                                    name="<?php echo esc_attr( $key ).'['.$id.']'; ?>" 
                                    class="ovabrw-ckf-price <?php echo esc_attr( $field['class'] ); ?>" 
                                    <?php echo esc_attr( $class_required ); ?>
                                    data-key="<?php echo esc_attr( $key ); ?>"
                                    data-product-id="<?php echo esc_attr( $id ); ?>">
                                    <?php 
                                    if ( ! empty( $ova_options_text ) && is_array( $ova_options_text ) ):
                                        if ( $field['required'] != 'on' ): ?>
                                            <option value="">
                                                <?php printf( esc_html__( 'Select %s', 'ova_brw' ), $field['label'] ); ?>
                                            </option>
                                        <?php
                                        endif;
                                        foreach ( $ova_options_text as $k => $value ):
                                            ?>
                                            <option value="<?php echo esc_attr( $ova_options_key[$k] ); ?>"<?php selected( $field['default'], $ova_options_key[$k] ); ?>>
                                                <?php echo esc_html( $value ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if ( ! empty( $ova_options_key ) && is_array( $ova_options_key ) ): ?>
                                    <?php foreach ( $ova_options_key as $k => $v ): ?>
                                        <?php if ( isset( $ova_options_qty[$k] ) && absint( $ova_options_qty[$k] ) ): ?>
                                            <div class="ovabrw-qty-control">
                                                <div class="ovabrw-qty-input">
                                                    <input
                                                        type="number"
                                                        name="<?php echo esc_attr( $key ).'_qty['.$id.']['.$v.']'; ?>"
                                                        class="qty-input"
                                                        value="1"
                                                        min="1"
                                                        max="<?php echo esc_attr( absint( $ova_options_qty[$k] ) ); ?>"
                                                        readonly
                                                    />
                                                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                                                </div>
                                                <div class="ovabrw-qty-dropdown">
                                                    <div class="qty-btn minus">
                                                        <span class="dashicons dashicons-minus"></span>
                                                    </div>
                                                    <span class="qty">1</span>
                                                    <div class="qty-btn plus">
                                                        <span class="dashicons dashicons-plus"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( $field['type'] === 'radio' ):
                            $values     = isset( $field['ova_values'] ) ? $field['ova_values'] : '';
                            $qtys       = isset( $field['ova_qtys'] ) ? $field['ova_qtys'] : '';
                            $default    = isset( $field['default'] ) ? $field['default'] : '';

                            if ( ! empty( $values ) && is_array( $values ) ):
                                foreach ( $values as $k => $value ):
                                    $checked = '';

                                    if ( ! $default && $field['required'] === 'on' ) $default = $values[0];

                                    if ( $default === $value ) $checked = 'checked';
                        ?>          
                                <div class="ovabrw-radio ovabrw-ckf-price">
                                    <input 
                                        type="radio" 
                                        id="<?php echo 'ovabrw-radio'.esc_attr( $k ); ?>" 
                                        name="<?php echo esc_attr( $key ).'['.$id.']'; ?>" 
                                        value="<?php echo esc_attr( $value ); ?>" <?php echo esc_html( $checked ); ?>/>
                                    <label for="<?php echo 'ovabrw-radio'.esc_attr( $k ); ?>"><?php echo esc_html( $value ); ?></label>
                                    <?php if ( isset( $qtys[$k] ) && absint( $qtys[$k] ) ): ?>
                                        <div class="ovabrw-qty-control">
                                            <div class="ovabrw-qty-input">
                                                <input
                                                    type="number"
                                                    name="<?php echo esc_attr( $key ).'_qty['.$id.']['.$value.']'; ?>"
                                                    class="qty-input"
                                                    value="1"
                                                    min="1"
                                                    max="<?php echo esc_attr( absint( $qtys[$k] ) ); ?>"
                                                    readonly
                                                />
                                                <span class="dashicons dashicons-arrow-down-alt2"></span>
                                            </div>
                                            <div class="ovabrw-qty-dropdown">
                                                <div class="qty-btn minus">
                                                    <span class="dashicons dashicons-minus"></span>
                                                </div>
                                                <span class="qty">1</span>
                                                <div class="qty-btn plus">
                                                    <span class="dashicons dashicons-plus"></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ( $field['type'] === 'checkbox' ):
                            $default        = isset( $field['default'] ) ? $field['default'] : '';
                            $checkbox_key   = isset( $field['ova_checkbox_key'] ) ? $field['ova_checkbox_key'] : [];
                            $checkbox_text  = isset( $field['ova_checkbox_text'] ) ? $field['ova_checkbox_text'] : [];
                            $checkbox_price = isset( $field['ova_checkbox_price'] ) ? $field['ova_checkbox_price'] : [];
                            $checkbox_qty   = isset( $field['ova_checkbox_qty'] ) ? $field['ova_checkbox_qty'] : [];

                            if ( ! empty( $checkbox_key ) && is_array( $checkbox_key ) ):
                                foreach ( $checkbox_key as $k => $val ):
                                    $checked = '';

                                    if ( ! $default && $field['required'] === 'on' ) $default = $val;

                                    if ( $default === $val ) $checked = 'checked';
                        ?>
                            <div class="ovabrw-checkbox ovabrw-ckf-price">
                                <input 
                                    type="checkbox" 
                                    id="<?php echo 'ovabrw-checkbox-'.esc_attr( $val ).'['.$id.']'; ?>" 
                                    class="" 
                                    name="<?php echo esc_attr( $key ).'['.$id.'][]'; ?>" 
                                    value="<?php echo esc_attr( $val ); ?>" <?php echo esc_html( $checked ); ?>/>
                                <label for="<?php echo 'ovabrw-checkbox-'.esc_attr( $val ).'['.$id.']'; ?>">
                                    <?php echo isset( $checkbox_text[$k] ) ? esc_html( $checkbox_text[$k] ) : ''; ?>
                                </label>
                                <?php if ( isset( $checkbox_qty[$k] ) && absint( $checkbox_qty[$k] ) ): ?>
                                    <div class="ovabrw-qty-control">
                                        <div class="ovabrw-qty-input">
                                            <input
                                                type="number"
                                                name="<?php echo esc_attr( $key ).'_qty['.$id.']['.$val.']'; ?>"
                                                class="qty-input"
                                                value="1"
                                                min="1"
                                                max="<?php echo esc_attr( absint( $checkbox_qty[$k] ) ); ?>"
                                                readonly
                                            />
                                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                                        </div>
                                        <div class="ovabrw-qty-dropdown">
                                            <div class="qty-btn minus">
                                                <span class="dashicons dashicons-minus"></span>
                                            </div>
                                            <span class="qty">1</span>
                                            <div class="qty-btn plus">
                                                <span class="dashicons dashicons-plus"></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach;?>
                            <span class="ovabrw-error">
                                <?php printf( esc_html__( '%s field is required', 'ova-brw' ), $field['label'] ); ?>
                            </span>
                        <?php endif; endif; ?>
                        <?php if ( $field['type'] === 'file' ):
                            $mimes = apply_filters( 'ovabrw_ft_file_mimes', [
                                'jpg'   => 'image/jpeg',
                                'jpeg'  => 'image/pjpeg',
                                'png'   => 'image/png',
                                'pdf'   => 'application/pdf',
                                'doc'   => 'application/msword',
                            ]);
                        ?>
                            <div class="ovabrw-file">
                                <label for="<?php echo 'ovabrw-file-'.esc_attr( $key ); ?>">
                                    <span class="ovabrw-file-chosen">
                                        <?php esc_html_e( 'Choose File', 'ova-brw' ); ?>
                                    </span>
                                    <span class="ovabrw-file-name"></span>
                                </label>
                                <input 
                                    type="<?php echo esc_attr( $field['type'] ); ?>" 
                                    id="<?php echo 'ovabrw-file-'.esc_attr( $key ); ?>" 
                                    name="<?php echo esc_attr( $key ).'['.$id.']'; ?>" 
                                    class="<?php echo esc_attr( $field['class'] ); ?>" 
                                    data-max-file-size="<?php echo esc_attr( $field['max_file_size'] ); ?>" 
                                    data-file-mimes="<?php echo esc_attr( json_encode( $mimes ) ); ?>" 
                                    data-max-file-size-msg="<?php printf( esc_html__( 'Max file size: %sMB', 'ova-brw' ), $field['max_file_size'] ); ?>" 
                                    data-formats="<?php esc_attr_e( 'Supported formats: .jpg, .jpeg, .png, .pdf, .doc', 'ova-brw' ); ?>" <?php echo esc_attr( $class_required ); ?> 
                                    data-required="<?php esc_attr_e( 'This field is required', 'ova-brw' ); ?>"/>
                            </div>
                        <?php endif; ?>
                    </span>
                </div>
        <?php
                endif;
            endforeach;
        ?>
            <input 
                type="hidden" 
                name="data_custom_ckf" 
                data-ckf="<?php echo esc_attr( json_encode( $list_ckf_output ) ); ?>" />
        <?php
        endif;

        $html = ob_get_contents(); 
        ob_end_clean();

        return $html;
    }
}

/**
 * Get html resources when created order in admin
 */
if ( ! function_exists( 'ovabrw_get_html_resources_order' ) ) {
    function ovabrw_get_html_resources_order( $id = false, $currency = '' ) {
        $html = '';

        if ( ! $id ) return $html;

        $resources  = get_post_meta( $id, 'ovabrw_resource_name', true );

        if ( $resources ) {
            $resources_id       = get_post_meta( $id, 'ovabrw_resource_id', true );
            $resources_price    = get_post_meta( $id, 'ovabrw_resource_price', true );
            $duration_val       = get_post_meta( $id, 'ovabrw_resource_duration_val', true );
            $duration_type      = get_post_meta( $id, 'ovabrw_resource_duration_type', true );
            $resources_quantity = get_post_meta( $id, 'ovabrw_resource_quantity', true );

            $html .= '<div class="resources_order">';
                foreach ( $resources as $key => $value) {
                    $rs_id              = isset( $resources_id[$key] ) ? $resources_id[$key] : 0;
                    $rs_name            = isset( $resources[$key] ) ? $resources[$key] : '';
                    $rs_price           = isset( $resources_price[$key] ) ? $resources_price[$key] : 0;
                    $rs_duration_val    = isset( $duration_val[$key] ) ? $duration_val[$key] : '';
                    $rs_duration_type   = isset( $duration_type[$key] ) ? $duration_type[$key] : '';
                    $qty                = isset( $resources_quantity[$key] ) ? $resources_quantity[$key] : '';

                    switch ( $rs_duration_type ) {
                        case 'hours':
                            $rs_duration_type_text = esc_html__( 'Hour', 'ova-brw' );
                            break;
                        case 'days':
                            $rs_duration_type_text = esc_html__( 'Day', 'ova-brw' );
                            break;
                        case 'total':
                            $rs_duration_type_text = esc_html__( 'Total', 'ova-brw' );
                            break;
                        default:
                            $rs_duration_type_text = esc_html__( 'Total', 'ova-brw' );
                            break;
                    }

                    $html .= '<div class="res-item">';
                        $html .= '<div class="left">';
                            $html .= '<input 
                                        type="checkbox" 
                                        id="ovabrw_resource_checkboxs_bk_'. esc_html( $key ) .'" 
                                        data-resource_key="'. $rs_id .'" 
                                        name="ovabrw_resource_checkboxs['. $id .'][]" 
                                        value="'. esc_attr( $rs_id ) .'" 
                                        class="ovabrw_resource_checkboxs" />';
                            $html .= '<label for="ovabrw_resource_checkboxs_bk_'. esc_html( $key ) .'">'. $rs_name .'</label>';
                        $html .= '</div>';
                        $html .= '<div class="right">';
                            $html .= '<div class="unit">';
                                $html .= '<span class="dur_price">'. ovabrw_wc_price( $rs_price, ['currency' => $currency] ) .'</span>';
                                $html .= '<span class="slash">/</span>';

                                if ( $rs_duration_val != '' ) {
                                    $html .= '<span class="dur_val">'. $rs_duration_val .'</span>';
                                }

                                $html .= '<span class="dur_type">'. $rs_duration_type_text .'</span>';
                            $html .= '</div>';

                            if ( absint( $qty ) ) {
                                $html .= '<div class="res-qty ovabrw-qty-control">';
                                    $html .= '<div class="ovabrw-qty-input">';
                                        $html .= '<input
                                            type="number"
                                            class="qty-input"
                                            name="ovabrw_resource_quantity['. $id .']['.$rs_id.']"
                                            value="1"
                                            min="1"
                                            max="'.absint( $qty ).'"
                                            readonly />';
                                        $html .= '<span class="dashicons dashicons-arrow-down-alt2"></span>';
                                    $html .= '</div>';
                                    $html .= '<div class="ovabrw-qty-dropdown">';
                                        $html .= '<div class="qty-btn minus">';
                                            $html .= '<span class="dashicons dashicons-minus"></span>';
                                        $html .= '</div>';
                                        $html .= '<span class="qty">1</span>';
                                        $html .= '<div class="qty-btn plus">';
                                            $html .= '<span class="dashicons dashicons-plus"></span>';
                                        $html .= '</div>';
                                    $html .= '</div>';
                                $html .= '</div>';
                            }

                        $html .= '</div>';
                    $html .= '</div>';
                }
            $html .= '</div>';
        }

        return $html;
    }
}

/**
 * Get html services when created order in admin
 */
if ( ! function_exists( 'ovabrw_get_html_services_order' ) ) {
    function ovabrw_get_html_services_order( $id = false ) {
        $html = '';

        if ( ! $id ) return $html;

        $services  = get_post_meta( $id, 'ovabrw_label_service', true );

        if ( $services ) {
            $services_id            = get_post_meta( $id, 'ovabrw_service_id', true );
            $services_name          = get_post_meta( $id, 'ovabrw_service_name', true );
            $services_price         = get_post_meta( $id, 'ovabrw_service_price', true );
            $services_qty           = get_post_meta( $id, 'ovabrw_service_qty', true );
            $services_required      = get_post_meta( $id, 'ovabrw_service_required', true );
            $services_duration_type = get_post_meta( $id, 'ovabrw_service_duration_type', true );

            $html .= '<div class="services_order">';
                for ( $i = 0; $i < count( $services ); $i++ ) {
                    $sv_ids             = isset( $services_id[$i] ) ? $services_id[$i] : [];
                    $sv_name            = isset( $services_name[$i] ) ? $services_name[$i] : [];
                    $sv_price           = isset( $services_price[$i] ) ? $services_price[$i] : [];
                    $sv_qty             = isset( $services_qty[$i] ) ? $services_qty[$i] : [];
                    $sv_required        = isset( $services_required[$i] ) ? $services_required[$i] : '';
                    $sv_duration_type   = isset( $services_duration_type[$i] ) ? $services_duration_type[$i] : '';

                    if ( $sv_required == 'yes' ) {
                        $requires = ' class="required"';
                    } else {
                        $requires = '';
                    }

                    $html .= '<div class="service-item">';
                        $html .= '<div class="error_item"><label>'. esc_html__( 'This field is required', 'ova-brw' ) .'</label></div>';

                        $html .= '<div class="ovabrw-select">';
                            $html .= '<select name="ovabrw_service['. $id .'][]"'. $requires .' data-product-id="'.esc_attr( $id ).'">';
                                $html .= '<option value="">'. sprintf( esc_html__( 'Select %s', 'ova-brw' ), $services[$i] ) .'</option>';

                                if ( ! empty( $sv_ids ) && is_array( $sv_ids ) ) {
                                    foreach( $sv_ids as $key => $value ) {
                                        $html .= '<option value="'. esc_attr( $value ) .'">'. esc_html( $sv_name[$key] ) .'</option>';
                                    }
                                }
                            $html .= '</select>';
                            if ( ! empty( $sv_ids ) && is_array( $sv_ids ) ) {
                                foreach ( $sv_ids as $k => $v ) {
                                    if ( isset( $sv_qty[$k] ) && absint( $sv_qty[$k] ) ) {
                                        $html .= '<div class="ovabrw-qty-control">';
                                            $html .= '<div class="ovabrw-qty-input">';
                                                $html .= '<input
                                                    type="number"
                                                    name="ovabrw_service_quantity['. $id .']['.$v.']"
                                                    class="qty-input"
                                                    value="1"
                                                    min="1"
                                                    max="'.absint( $sv_qty[$k] ).'"
                                                    readonly
                                                />';
                                                $html .= '<span class="dashicons dashicons-arrow-down-alt2"></span>';
                                            $html .= '</div>';
                                            $html .= '<div class="ovabrw-qty-dropdown">';
                                                $html .= '<div class="qty-btn minus">';
                                                    $html .= '<span class="dashicons dashicons-minus"></span>';
                                                $html .= '</div>';
                                                $html .= '<span class="qty">1</span>';
                                                $html .= '<div class="qty-btn plus">';
                                                    $html .= '<span class="dashicons dashicons-plus"></span>';
                                                $html .= '</div>';
                                            $html .= '</div>';
                                        $html .= '</div>';
                                    }
                                }
                            }
                        $html .= '</div>';
                    $html .= '</div>';
                }
            $html .= '</div>';
        }

        return $html;
    }
}

/**
 *  HTML Dropdown Attributes
 */
if ( ! function_exists( 'ovabrw_dropdown_attributes' ) ) {
    function ovabrw_dropdown_attributes( $label = '' ) {
        $args       = array(); 
        $html       = $html_attr_value = '';
        $attributes = wc_get_attribute_taxonomies();

        if ( ! $label ) {
            $label = esc_html__( 'Select Attribute', 'ova-brw' );
        }

        if ( ! empty( $attributes ) ) {
            $html .= '<select name="ovabrw_attribute" class="ovabrw_attribute"><option value="">'. $label .'</option>';

            foreach ( $attributes as $obj_attr ) {
                if ( taxonomy_exists( wc_attribute_taxonomy_name( $obj_attr->attribute_name ) ) ) {
                    $html .= "<option value='". $obj_attr->attribute_name ."'>". $obj_attr->attribute_label ."</option>";

                    $term_attributes = get_terms( wc_attribute_taxonomy_name( $obj_attr->attribute_name ), 'orderby=name&hide_empty=0' );
                    if ( ! empty( $term_attributes ) ) {
                        $html_attr_value .= '<div class="label_search s_field ovabrw-value-attribute" id="'. $obj_attr->attribute_name .'"><select name="ovabrw_attribute_value" >';

                        foreach ( $term_attributes as $obj_attr_value ) {
                            $html_attr_value .= '<option value="'.$obj_attr_value->slug.'">'.$obj_attr_value->name.'</option>';
                        }

                        $html_attr_value .= '</select></div>';
                    }
                }
            }
            $html .= '</select>';
        }

        $args['html_attr']         = $html;
        $args['html_attr_value']   = $html_attr_value;

        return $args;
    }
}

/**
 *  Get html taxonomy search map
 */
if ( ! function_exists( 'ovabrw_search_taxonomy_dropdown' ) ) {
    function ovabrw_search_taxonomy_dropdown( $slug_taxonomy, $name_taxonomy ) {
        $args = array(
            'show_option_all'    => '',
            'show_option_none'   => esc_html__( 'Select ', 'ovabrw' ) . esc_html( $name_taxonomy ) ,
            'option_none_value'  => '',
            'orderby'            => 'ID',
            'order'              => 'ASC',
            'show_count'         => 0,
            'hide_empty'         => 0,
            'child_of'           => 0,
            'exclude'            => '',
            'include'            => '',
            'echo'               => 0,
            'selected'           => '',
            'hierarchical'       => 1,
            'name'               => $slug_taxonomy.'_name',
            'id'                 => '',
            'class'              => 'custom_taxonomy',
            'depth'              => 0,
            'tab_index'          => 0,
            'taxonomy'           => $slug_taxonomy,
            'hide_if_empty'      => false,
            'value_field'        => 'slug',
        );

        return wp_dropdown_categories($args);
    }
}

/**
 *  Get product search map
 */
if ( ! function_exists( 'ovabrw_search_products' ) ) {
    function ovabrw_search_products( $data ) {
        $number     = $data['posts_per_page']   ? $data['posts_per_page']   : 12;
        $orderby    = $data['orderby']          ? $data['orderby']          : 'date';
        $order      = $data['order']            ? $data['order']            : 'DESC';

        $args = array(
            'post_type'         => 'product',
            'post_status'       => 'publish',
            'posts_per_page'    => $number,
            'orderby'           => $orderby,
            'order'             => $order,
        );

        $products = new WP_Query( $args );

        return $products;
    }
}

/**
 * Pagination ajax
 */
if ( ! function_exists( 'ovabrw_pagination_ajax' ) ) {
    function ovabrw_pagination_ajax( $total, $limit, $current  ) {
        $html   = '';
        $pages  = ceil( $total / $limit );

        if ( $pages > 1 ) {
            $html .= '<ul>';

            if ( $current > 1 ) {
                $html .=    '<li><span data-paged="'. ( $current - 1 ) .'" class="prev page-numbers" >'
                                . esc_html__( 'Previous', 'ova-brw' ) .
                            '</span></li>';
            }

            for ( $i = 1; $i <= $pages; $i++ ) {
                if ( $current == $i ) {
                    $html .=    '<li><span data-paged="'. $i .'" class="prev page-numbers current" >'. esc_html( $i ) .'</span></li>';
                } else {
                    $html .=    '<li><span data-paged="'. $i .'" class="prev page-numbers" >'. esc_html( $i ) .'</span></li>';
                }
            }

            if ( $current < $pages ) {
                $html .=    '<li><span data-paged="'. ( $current + 1 ) .'" class="next page-numbers" >'
                                . esc_html__( 'Next', 'ova-brw' ) .
                            '</span></li>';
            }
        }

        return $html;
    }
}

/**
 * Get html price
 */
if ( ! function_exists( 'ovabrw_get_html_price' ) ) {
    function ovabrw_get_html_price( $id ) {
        $html = '';
        if ( $id ) {
            $min = $max = 0;
            $rental_type    = get_post_meta( $id, 'ovabrw_price_type', true ) ? get_post_meta( $id, 'ovabrw_price_type', true ) : 'day' ;
            $price_hour     = get_post_meta( $id, 'ovabrw_regul_price_hour', true );
            $price_day      = get_post_meta( $id, '_regular_price', true );

            // Get price
            $petime_price   = get_post_meta( $id, 'ovabrw_petime_price', true );
            $price_location = get_post_meta( $id, 'ovabrw_price_location', true );

            if ( $rental_type == 'period_time' && $petime_price && is_array( $petime_price ) ) {
                $min = min( $petime_price );
                $max = max( $petime_price );
            }

            if ( $rental_type == 'transportation' && $price_location && is_array( $price_location ) ) {
                $min = min( $price_location );
                $max = max( $price_location );
            }

            if ( $rental_type == 'period_time' || $rental_type == 'transportation' ) {
                if ( $min && $max && $min == $max ) {
                    $html .= '<div class="amount">'. ovabrw_wc_price( $min ) .'</div>';
                } elseif ( $min && $max ) {
                    $html .= '<div class="amount">'. ovabrw_wc_price( $min ) .' - '. ovabrw_wc_price( $max ) .'</div>';
                } else {
                    $html .= '';
                }
            } elseif ( $rental_type == 'hour' ) {
                $html .= '<div class="amount">'. ovabrw_wc_price( $price_hour ) .'</div>';
            } elseif ( $rental_type == 'day' ) {
                $html .= '<div class="amount">'. ovabrw_wc_price( $price_day, [], false ) .'</div>';
            } elseif ( $rental_type == 'mixed' ) {
                $html .= '<div class="amount">'. ovabrw_wc_price( $price_hour ) . esc_html__('/hour - ', 'ova-brw') . ovabrw_wc_price( $price_day, [], false ) . esc_html__('/day', 'ova-brw') .'</div>';
            } else {
                $html = '';
            }
        }

        return apply_filters( 'ovabrw_ft_get_html_price', $html, $id );
    }
}

/**
 * Recursive array replace \\
 */
if ( ! function_exists( 'recursive_array_replace' ) ) {
    function recursive_array_replace( $find, $replace, $array ) {
        if ( ! is_array( $array ) ) {
            return str_replace( $find, $replace, $array );
        }

        foreach ( $array as $key => $value ) {
            $array[$key] = recursive_array_replace( $find, $replace, $value );
        }

        return $array;
    }
}

/**
 * Check Global Discount By Hour
 */
if ( ! function_exists( 'ovabrw_check_table_price_by_hour' ) ) {
    function ovabrw_check_table_price_by_hour( $product_id ) {
        $flag = false;

        $gd_duration_val_min = get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_min', true );
        $gd_duration_val_max = get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_max', true );

        if ( $gd_duration_val_min ) asort( $gd_duration_val_min );
        if ( $gd_duration_val_max ) asort( $gd_duration_val_max );

        if ( !empty( $gd_duration_val_min ) || !empty( $gd_duration_val_max ) ) $flag = true;

        $rt_price_hour = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );

        if ( !empty( $rt_price_hour ) ) $flag = true;

        return $flag;
    }
}

/**
 * Check Global Discount By Day
 */
if ( ! function_exists( 'ovabrw_check_table_price_by_day' ) ) {
    function ovabrw_check_table_price_by_day( $product_id ) {
        $flag   = false;
        $daily  = ovabrw_p_weekdays( $product_id );

        if ( ! empty( $daily ) ) { 
            $flag = true;
        }

        $gd_duration_val_min = get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_min', true );
        $gd_duration_val_max = get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_max', true );

        if ( $gd_duration_val_min ) asort( $gd_duration_val_min );
        if ( $gd_duration_val_max ) asort( $gd_duration_val_max );
        if ( !empty( $gd_duration_val_min ) || !empty( $gd_duration_val_max ) ) $flag = true;

        $rt_price = get_post_meta( $product_id, 'ovabrw_rt_price', true );
        if ( !empty( $rt_price ) ) $flag = true;

        return $flag;
    }
}

/**
 * Check Global Discount Period Time
 */
if ( ! function_exists( 'ovabrw_check_table_price_by_period_time' ) ) {
    function ovabrw_check_table_price_by_period_time( $product_id ) {
        $flag = false;

        $petime_label = get_post_meta( $product_id, 'ovabrw_petime_label', true );
        if ( !empty( $petime_label ) ) $flag = true;

        return $flag;
    }
}

/**
 * Get Product Rental
 */
if ( ! function_exists( 'ovabrw_get_products_rental' ) ) {
    function ovabrw_get_products_rental() {
        $args = array(
            'post_type'         => 'product',
            'fields'            => 'ids', 
            'post_status'       => 'publish',
            'posts_per_page'    => '-1',
            'orderby'           => 'ID',
            'order'             => 'ASC',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'ovabrw_car_rental', 
                ),
            ),
        );

        $products = get_posts( $args );

        return $products;
    }
}

/**
 * Get Product Gallery IDs
 */
if ( ! function_exists( 'ovabrw_get_products_gallery_ids' ) ) {
    function ovabrw_get_products_gallery_ids( $product_id = null ) {
        if ( ! $product_id ) return false;

        $product = wc_get_product( $product_id );

        if ( $product ) {
            $image_ids = array();

            $product_image_id = $product->get_image_id();
            if ( $product_image_id ) {
                array_push( $image_ids, $product_image_id );
            }

            $product_gallery_ids = $product->get_gallery_image_ids();
            if ( $product_gallery_ids && is_array( $product_gallery_ids ) ) {
                $image_ids = array_merge( $image_ids, $product_gallery_ids );
            }

            return $image_ids;
        }

        return false;
    }
}

/**
 * Get Label Pickup Date
 */
if ( ! function_exists( 'ovabrw_get_label_pickup_date' ) ) {
    function ovabrw_get_label_pickup_date( $product_id = null ) {
        $label_pickup_date = esc_html__( 'Pick-up Date', 'ova-brw' );

        if ( ! $product_id ) return $label_pickup_date;

        $setting_lable_pickup_date = get_post_meta( $product_id, 'ovabrw_label_pickup_date_product', true );

        if ( $setting_lable_pickup_date == 'new' ) {
            $label_pickup_date = get_post_meta( $product_id, 'ovabrw_new_pickup_date_product', true );
        } elseif ( $setting_lable_pickup_date == 'category' ) {
            $terms = wp_get_post_terms( $product_id, 'product_cat', array( 'fields'=>'ids' ));
            if ( $terms && is_array( $terms ) ) {
                $term_id = reset($terms);
            }

            $label_pickup_date  = isset( $term_id ) ? get_term_meta( $term_id, 'ovabrw_lable_pickup_date', true ) : esc_html__( 'Pick-up Date', 'ova-brw' );
        }

        if ( $label_pickup_date == '' ) {
            $label_pickup_date = esc_html__( 'Pick-up Date', 'ova-brw' );
        }

        return $label_pickup_date;
    }
}

/**
 * Get Label Pickoff Date
 */
if ( ! function_exists( 'ovabrw_get_label_pickoff_date' ) ) {
    function ovabrw_get_label_pickoff_date( $product_id = null ) {
        $label_dropoff_date = esc_html__( 'Drop-off Date', 'ova-brw' );

        if ( ! $product_id ) return $label_dropoff_date;

        $setting_lable_dropoff_date = get_post_meta( $product_id, 'ovabrw_label_dropoff_date_product', true );

        if ( $setting_lable_dropoff_date == 'new' ) {
            $label_dropoff_date = get_post_meta( $product_id, 'ovabrw_new_dropoff_date_product', true );
        } elseif ( $setting_lable_dropoff_date == 'category' ) {
            $terms = wp_get_post_terms( $product_id, 'product_cat', array( 'fields'=>'ids' ));
            if ( $terms && is_array( $terms ) ) {
                $term_id = reset($terms);
            }
            
            $label_dropoff_date = isset( $term_id ) ? get_term_meta( $term_id, 'ovabrw_lable_dropoff_date', true ) : esc_html__( 'Drop-off Date', 'ova-brw');
        } else {
            $label_dropoff_date = esc_html__( 'Drop-off Date', 'ova-brw');
        }

        if ( $label_dropoff_date == '' ) {
            $label_dropoff_date = esc_html__( 'Drop-off Date', 'ova-brw' );
        }

        return $label_dropoff_date;
    }
}

/**
 * Get average review product by category
 */
if ( ! function_exists( 'ovabrw_get_average_product_review' ) ) {
    function ovabrw_get_average_product_review( $category_id = null ) {
        if ( ! $category_id ) return;

        $average = $count = $total_rating = 0;

        $args = [
            'post_type'         => 'product',
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
            'orderby'           => 'date',
            'order'             => 'DESC',
            'fields'            => 'ids',
            'tax_query'         => array(
                array(
                    'taxonomy'  => 'product_cat',
                    'field'     => 'term_id',
                    'terms'     => $category_id,
                    'operator'  => 'IN',
                ),
            )
        ];

        $product_ids = get_posts( $args );

        if ( ! empty( $product_ids ) && is_array( $product_ids ) ) {
            foreach ( $product_ids as $product_id ) {
                $product    = wc_get_product( $product_id );
                $rating     = $product->get_average_rating();

                if ( $rating ) {
                    $total_rating += floatval( $rating );
                    $count += 1;
                }
            }
        }

        if ( floatval( $total_rating ) > 0 && absint( $count ) > 0 ) {
            $average = number_format( $total_rating / $count, 1, '.', ',' );
        }
        
        return $average;
    }
}

if ( ! function_exists( 'ovabrw_get_product_ajax_filter' ) ) {
    function ovabrw_get_product_ajax_filter( $args = [] ) {
        if ( empty( $args ) ) return;

        $base_query = [
            'post_type'         => 'product',
            'post_status'       => 'publish',
            'paged'             => $args['paged'],
            'posts_per_page'    => $args['posts_per_page'],
            'orderby'           => $args['orderby'],
            'order'             => $args['order'],
        ];

        if ( $args['term_id'] ) {
            $base_query['tax_query'] = [
                array(
                    'taxonomy'  => 'product_cat',
                    'field'     => 'term_id',
                    'terms'     => $args['term_id'],
                    'operator'  => 'IN',
                ),
            ];
        }

        $products = new WP_Query( $base_query );
        
        return $products;
    }
}

if ( ! function_exists( 'ovabrw_get_product_filter' ) ) {
    function ovabrw_get_product_filter( $args = [] ) {
        if ( empty( $args ) ) return;

        $base_query = [
            'post_type'         => 'product',
            'post_status'       => 'publish',
            'posts_per_page'    => $args['posts_per_page'],
            'orderby'           => $args['orderby'],
            'order'             => $args['order'],
        ];

        if ( ! empty( $args['categories'] ) && is_array( $args['categories'] ) ) {
            $base_query['tax_query'] = [
                array(
                    'taxonomy'  => 'product_cat',
                    'field'     => 'term_id',
                    'terms'     => $args['categories'],
                    'operator'  => 'IN',
                ),
            ];
        }

        $products = new WP_Query( $base_query );
        
        return $products;
    }
}