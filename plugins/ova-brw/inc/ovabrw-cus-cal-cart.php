<?php defined( 'ABSPATH' ) || exit();

// Custom Calculate Total Add To Cart
add_action( 'woocommerce_before_calculate_totals',  'ovabrw_woocommerce_before_calculate_totals' , 10, 1 ); 
if ( ! function_exists( 'ovabrw_woocommerce_before_calculate_totals' ) ) {
    function ovabrw_woocommerce_before_calculate_totals( $cart_object ) {
        $remaining_amount = $deposit_amount = $total_amount_insurance = $remaining_tax_total = 0;
        $has_deposit = false;
        $type_deposit = 'full';

        $i = 0;
        foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item ) {
            // Check custom product type is ovabrw_car_rental
            if ( ! $cart_item['data']->is_type('ovabrw_car_rental') ) continue;

            $product_id = $cart_item['product_id'];

            // Calculate Rent Time
            $ovabrw_pickup_date  = isset( $cart_item['ovabrw_pickup_date'] ) ? $cart_item['ovabrw_pickup_date'] : '';
            $ovabrw_pickoff_date = isset( $cart_item['ovabrw_pickoff_date'] ) ? $cart_item['ovabrw_pickoff_date'] : '' ;
            $ovabrw_pickup_loc   = isset( $cart_item['ovabrw_pickup_loc'] ) ? $cart_item['ovabrw_pickup_loc'] : '';
            $ovabrw_pickoff_loc  = isset( $cart_item['ovabrw_pickoff_loc'] ) ? $cart_item['ovabrw_pickoff_loc'] : '';
            $ovabrw_package_id   = isset( $cart_item['period_package_id'] ) ? $cart_item['period_package_id'] : '';
            $ovabrw_rental_type  = get_post_meta( $product_id, 'ovabrw_price_type', true );

            $total = [
                'quantity'          => 0,
                'line_total'        => 0,
                'amount_insurance'  => 0,
            ];

            if ( $ovabrw_rental_type === 'taxi' ) {
                $total = get_price_by_distance( $product_id, strtotime( $ovabrw_pickup_date ), strtotime( $ovabrw_pickoff_date ), $cart_item );
            } else {
                $total = get_price_by_date( $product_id, strtotime( $ovabrw_pickup_date ), strtotime( $ovabrw_pickoff_date ), $cart_item, $ovabrw_pickup_loc, $ovabrw_pickoff_loc, $ovabrw_package_id );
            }

            if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
                $total['line_total'] = ovabrw_convert_price( $total['line_total'] );
                $total['amount_insurance'] = ovabrw_convert_price( $total['amount_insurance'] );
            }

            $line_total             = round( $total['line_total'], wc_get_price_decimals() );
            $amount_insurance       = $total['amount_insurance'];
            $deposit_enable         = get_post_meta ( $product_id, 'ovabrw_enable_deposit', true );
            $value_deposit          = get_post_meta ( $product_id, 'ovabrw_amount_deposit', true );
            $value_deposit          = $value_deposit ? floatval( $value_deposit ) : 0;
            $deposit_type_deposit   = get_post_meta ( $product_id, 'ovabrw_type_deposit', true );
            $sub_remaining_amount   = $deposit_amount = 0;
            $sub_deposit_amount     = $line_total;

            WC()->cart->deposit_info = array();

            if ( $deposit_enable === 'yes' ) {
                $has_deposit = true;

                if ( isset($cart_item['ova_type_deposit']) && $cart_item['ova_type_deposit'] === 'full' ) {
                    $sub_remaining_amount   = 0;
                    $sub_deposit_amount     = $line_total;
                } elseif ( isset( $cart_item['ova_type_deposit'] ) && $cart_item['ova_type_deposit'] === 'deposit' ) {
                    if ( $deposit_type_deposit === 'percent' ) {
                        $sub_deposit_amount     = ( $line_total * $value_deposit ) / 100;
                        $sub_remaining_amount   = $line_total - $sub_deposit_amount;
                    } elseif ($deposit_type_deposit === 'value') {
                        $sub_deposit_amount     = $value_deposit;
                        $sub_remaining_amount   = $line_total - $sub_deposit_amount;
                    }
                }
            }            

            $remaining_amount       += ovabrw_get_price_tax( $sub_remaining_amount, $cart_item );
            $deposit_amount         += $sub_deposit_amount;
            $total_amount_insurance += $amount_insurance;

            // Add tax total remanining
            if ( wc_tax_enabled() ) {
                $prices_include_tax   = wc_prices_include_tax() ? 'yes' : 'no';
                $remaining_tax_total += ovabrw_get_taxes_by_price( $sub_remaining_amount, $product_id, $prices_include_tax );
            }

            // Check order deposit
            if ( isset( $cart_item['ova_type_deposit'] ) && $cart_item['ova_type_deposit'] == 'deposit' ) {
                $type_deposit = 'deposit';
            }

            WC()->cart->deposit_info[ 'ova_deposit_amount' ]    = round( $deposit_amount, wc_get_price_decimals() );
            WC()->cart->deposit_info[ 'ova_remaining_amount' ]  = round( $remaining_amount, wc_get_price_decimals() );
            WC()->cart->deposit_info[ 'ova_type_deposit' ]      = $type_deposit;
            WC()->cart->deposit_info[ 'ova_insurance_amount' ]  = round( $total_amount_insurance, wc_get_price_decimals() );
            WC()->cart->deposit_info[ '_ova_has_deposit' ]      = $has_deposit;
            WC()->cart->deposit_info[ 'ova_remaining_taxes' ]   = $remaining_tax_total;

            $cart_item['data']->set_price( round( $line_total, wc_get_price_decimals() ) );
            $cart_item['data']->add_meta_data( 'pay_total', round( $line_total, wc_get_price_decimals() ), true );

            // Check deposit
            if ( $deposit_amount && $has_deposit && $deposit_enable === 'yes' ) {
                $cart_item['data']->set_price( $deposit_amount );
            }

            $cart_object->cart_contents[ $cart_item_key ]['quantity'] = 1;

        } // End foreach
    }
}

if ( ! function_exists( 'ovabrw_get_total_resources' ) ) {
    function ovabrw_get_total_resources( $product_id = false, $ovabrw_pickup_date = false, $ovabrw_pickoff_date = false, $resources = [], $resources_qty = [] ) {
        $total_price = 0;

        if ( empty( $resources ) || ! is_array( $resources ) ) return $total_price;
        if ( empty( $resources_qty ) ) $resources_qty = [];

        $rent_time      = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
        $resource_id    = get_post_meta( $product_id, 'ovabrw_resource_id', true );
        $resource_price = get_post_meta( $product_id, 'ovabrw_resource_price', true );
        $duration_type  = get_post_meta( $product_id, 'ovabrw_resource_duration_type', true );

        foreach ( $resources as $key_c_re => $value_c_re) {
            foreach ( $resource_id as $key_id => $value_id ) {
                if ( $key_c_re == $value_id ) {
                    $qty = isset( $resources_qty[$key_c_re] ) && $resources_qty[$key_c_re] ? absint( $resources_qty[$key_c_re] ) : 1;

                    if ( $duration_type[$key_id] == 'total' ) {
                        $total_price += $resource_price[$key_id] * $qty;
                    } elseif ( $duration_type[$key_id] == 'days' ) {
                        $total_price += $resource_price[$key_id] * $rent_time['rent_time_day'] * $qty;
                    } elseif ( $duration_type[$key_id] == 'hours' ) {
                        $total_price += $resource_price[$key_id] * $rent_time['rent_time_hour'] * $qty;
                    }
                }
            }
        }

        return $total_price;
    }
}

if ( ! function_exists( 'ovabrw_get_total_services' ) ) {
    function ovabrw_get_total_services( $product_id = false, $ovabrw_pickup_date = false, $ovabrw_pickoff_date = false, $ovabrw_service = [], $services_qty = [] ) {
        $total_service = 0;

        if ( empty( $ovabrw_service ) || ! is_array( $ovabrw_service ) ) return $total_service;
        if ( empty( $services_qty ) ) $services_qty = [];

        $rent_time      = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
        $rental_type    = get_post_meta( $product_id, 'ovabrw_price_type', true );
        $ovabrw_service_id      = get_post_meta( $product_id, 'ovabrw_service_id', true );  
        $ovabrw_service_price   = get_post_meta( $product_id, 'ovabrw_service_price', true );  
        $service_duration_type  = get_post_meta( $product_id, 'ovabrw_service_duration_type', true ); 

        foreach ( $ovabrw_service as $id_service ) {
            if ( $id_service && $ovabrw_service_id && is_array( $ovabrw_service_id ) ) {
                foreach( $ovabrw_service_id as $key_id => $value_id_arr ) {
                    if ( in_array($id_service, $value_id_arr) ) {
                        foreach( $value_id_arr as $k => $v ) {
                            if ( $id_service == $v ) {
                                $service_duration_type_str = $service_duration_type[$key_id][$k];
                                $price_service = floatval( $ovabrw_service_price[$key_id][$k] );

                                $qty = isset( $services_qty[$id_service] ) && absint( $services_qty[$id_service] ) ? absint( $services_qty[$id_service] ) : 1;

                                if ( $service_duration_type_str == 'total' ) {
                                    $total_service += $price_service * $qty;
                                } elseif ( $service_duration_type_str == 'days' ) {
                                    $total_service += $price_service * $rent_time['rent_time_day'] * $qty;
                                } elseif ( $service_duration_type_str == 'hours' ) {
                                    $total_service += $price_service * $rent_time['rent_time_hour'] * $qty;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $total_service;
    }
}

/**
 * Get Price a product with Pick-up date, Drop-off date
 * @param  [type] $product_id          [description]
 * @param  [strtotime] $ovabrw_pickup_date
 * @param  [strtotime] $ovabrw_pickoff_date
 * @return [array] quantity, line_total
 */
if ( ! function_exists( 'get_price_by_date' ) ) {
    function get_price_by_date( $product_id = false, $ovabrw_pickup_date = false, $ovabrw_pickoff_date = false, $cart_item = [], $ovabrw_pickup_loc = '', $ovabrw_pickoff_loc ='', $id_package = '' ) {
        if ( empty( $ovabrw_pickup_loc ) ) {
            $ovabrw_pickup_loc = isset( $cart_item['ovabrw_pickup_loc'] ) ? $cart_item['ovabrw_pickup_loc'] : '';
        }
        if ( empty( $ovabrw_pickoff_loc ) ) {
            $ovabrw_pickoff_loc = isset( $cart_item['ovabrw_pickoff_loc'] ) ? $cart_item['ovabrw_pickoff_loc'] : '';
        }
        if ( is_array( $cart_item ) && ! empty( $cart_item ) ) {
            $product_id = $cart_item['product_id'];
        }

        // Get New Date to match per product
        $new_date               = ovabrw_new_input_date( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date );
        $ovabrw_pickup_date     = $new_date['pickup_date_new'];
        $ovabrw_pickoff_date    = $new_date['pickoff_date_new'];
        $rent_time              = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
        
        // Get Range Time or ST
        $ovabrw_rt_startdate = get_post_meta( $product_id, 'ovabrw_rt_startdate', true );
        $ovabrw_rt_starttime = get_post_meta( $product_id, 'ovabrw_rt_starttime', true );

        $ovabrw_rt_enddate = get_post_meta( $product_id, 'ovabrw_rt_enddate', true );
        $ovabrw_rt_endtime = get_post_meta( $product_id, 'ovabrw_rt_endtime', true );
        
        /* Price calculate by Global - Priority 0 *******************************************/
        $gl_price   = $gl_price_total = ovabrw_price_global( $product_id, $rent_time );
        $quantity   = $gl_quantity = $gl_quantity_total = ovabrw_quantity_global( $product_id, $rent_time );
        $line_total = $gl_price * $gl_quantity;

        // Price calculate by Weekday - Priority 1 *******************************************/
        $rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

        if ( $rental_type == 'period_time' ) {
            if ( isset( $cart_item ) && is_array( $cart_item ) && ! empty( $cart_item ) ) {
                 $ovabrw_period_price = isset( $cart_item['period_price'] ) ? $cart_item['period_price'] : 0;
            } else {
                if ( $id_package ) {
                    $ovabrw_period_package_id = $id_package;
                } else {
                    $ovabrw_period_package_id = isset( $_POST['ovabrw_period_package_id'] ) ? sanitize_text_field( $_POST['ovabrw_period_package_id'] ) : '' ;
                }

                $rental_info_period     = get_rental_info_period( $product_id, $ovabrw_pickup_date, $rental_type, $ovabrw_period_package_id );
                $ovabrw_period_price    = $rental_info_period['period_price'];
            }

            $quantity = 1;
            $line_total = floatval( $ovabrw_period_price );
        } elseif ( $rental_type === "transportation" ) {
            $quantity = 1;
            $line_total = ovabrw_get_price_by_start_date_transport( $product_id, $ovabrw_pickup_loc, $ovabrw_pickoff_loc );
        } elseif ( $rental_type === "day" ) {
            $get_price_by_day   = get_price_by_rental_type_day( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date );
            $quantity           = $get_price_by_day['quantity'];
            $line_total         = $get_price_by_day['line_total'];
        } elseif ( $rental_type === "mixed" ) {
            $total_price_day    = get_price_by_rental_type_day( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date );
            $quantity_day       = floor($rent_time['rent_time_day_raw']);

            if ( $quantity_day <= 0 ) {
                $quantity_hour = $rent_time['rent_time_hour_raw'];
            } elseif ( $quantity_day > 0 ) {
                $quantity_hour = ($rent_time['rent_time_hour_raw'] - $quantity_day * 24);
            }

            $gl_price_hour  = get_regular_price_hour_mixed($product_id, $quantity_hour);
            $day_total      = (float)$total_price_day['line_total'];
            $hour_total     = $quantity_hour * $gl_price_hour;
            $line_total     = $day_total + $hour_total;
        }
       
        if ( $rental_type === "mixed" || $rental_type === "hour" ) {
            /* Price calculate by - Special Time ( Range Time) - Priority 2 *******************************************/
            $rt_quantity_total_4 = $rt_price_total_4 = 0;
            $rt_quantity_total_3 = $rt_price_total_3 = 0;
            $rt_quantity_total_2 = $rt_price_total_2 = 0;
            $rt_quantity_total_1 = $rt_price_total_1 = 0;

            $i = 0; $data_max = 0; $flag_max = false;

            if ( $ovabrw_rt_startdate ) {
                foreach( $ovabrw_rt_startdate as $key_rt => $value_rt ) {
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

                    $start_date_str = $value_rt . ' ' . $ovabrw_rt_starttime[$key_rt];
                    $end_date_str   = $ovabrw_rt_enddate[$key_rt] . ' ' . $ovabrw_rt_endtime[$key_rt];
                    $start_date     = strtotime( $start_date_str );
                    $end_date       = strtotime( $end_date_str );

                    // If Special_Time_Start_Date <= pickup_date && pickoff_date <= Special_Time_End_Date
                    if ( $ovabrw_pickup_date >= $start_date && $ovabrw_pickoff_date <= $end_date ) {
                        $rt_price       = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );
                        $rt_quantity    = ovabrw_quantity_global( $product_id, $rent_time );

                        $rt_quantity_total_1    += $rt_quantity;
                        $rt_price_total_1       += $rt_quantity * $rt_price;

                        $quantity   = $rt_quantity;
                        $line_total = $rt_price * $rt_quantity;

                        if ( $rental_type === "mixed" ) {
                            $ovabrw_rt_price_hour   = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
                            $rt_price_hour          = get_special_price_hour_mixed($product_id, $ovabrw_rt_price_hour[$key_rt],$key_rt, $quantity_hour);

                            // ovabrw_price_special_time
                            $line_total = $quantity_day * $rt_price + $quantity_hour * $rt_price_hour;

                            if ( $flag_max ) {
                                $hour_total = $quantity_hour * $rt_price_hour;
                            }
                        }
                    } elseif ( $ovabrw_pickup_date < $start_date && $ovabrw_pickoff_date <=  $end_date && $ovabrw_pickoff_date >=  $start_date ) {
                        $gl_quantity_array  = get_time_bew_2day( $ovabrw_pickup_date, $start_date, $product_id );
                        $gl_price           = ovabrw_price_global( $product_id, $gl_quantity_array );
                        $gl_quantity        = ovabrw_quantity_global( $product_id, $gl_quantity_array );
                        $rt_quantity_array  = get_time_bew_2day( $start_date, $ovabrw_pickoff_date, $product_id );
                        $rt_price           = ovabrw_price_special_time( $product_id, $rt_quantity_array, $key_rt );
                        $rt_quantity        = ovabrw_quantity_global( $product_id, $rt_quantity_array );

                        $rt_quantity_total_2    += $rt_quantity;
                        $rt_price_total_2       += $rt_quantity * $rt_price;

                        $quantity   = $gl_quantity + $rt_quantity;
                        $line_total = $gl_price * $gl_quantity + $rt_quantity * $rt_price;

                        if ( $rental_type === "mixed" ) {
                            $ovabrw_rt_price_hour   = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
                            $gl_quantity            = floor($gl_quantity_array['rent_time_day_raw']);
                            $rt_quantity            = $quantity_day - $gl_quantity;
                            $rt_price_hour          = get_special_price_hour_mixed($product_id, $ovabrw_rt_price_hour[$key_rt],$key_rt, $quantity_hour);
                            $line_total             = $gl_price * $gl_quantity + $rt_quantity * $rt_price + $quantity_hour * $rt_price_hour;

                            if ( $flag_max ) {
                                $hour_total = $quantity_hour * $rt_price_hour;
                            } 
                        }
                    } elseif ( $ovabrw_pickup_date >=  $start_date && $ovabrw_pickup_date <= $end_date && $ovabrw_pickoff_date >= $end_date ) {
                        $rt_quantity_array  = get_time_bew_2day( $ovabrw_pickup_date,  $end_date, $product_id );
                        $rt_price           = ovabrw_price_special_time( $product_id, $rt_quantity_array, $key_rt );
                        $rt_quantity        = ovabrw_quantity_global( $product_id, $rt_quantity_array );
                        $gl_quantity_array  = get_time_bew_2day( $end_date, $ovabrw_pickoff_date, $product_id );
                        $gl_price           = ovabrw_price_global( $product_id, $gl_quantity_array );
                        $gl_quantity        = ovabrw_quantity_global( $product_id, $gl_quantity_array );

                        $rt_quantity_total_3    += $rt_quantity;
                        $rt_price_total_3       += $rt_quantity * $rt_price;

                        $quantity   = $gl_quantity + $rt_quantity;
                        $line_total = $gl_price * $gl_quantity + $rt_quantity * $rt_price;

                        if ( $rental_type === "mixed" ) {
                            $ovabrw_rt_price_hour   = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
                            $rt_quantity            = ceil($rt_quantity_array['rent_time_day_raw']);
                            $gl_quantity            = $quantity_day - $rt_quantity;
                            $line_total             = $gl_price * $gl_quantity + $rt_quantity * $rt_price + $quantity_hour * $gl_price_hour;

                            if ( $flag_max ) {
                                $hour_total = $quantity_hour * $gl_price_hour;
                            }
                        }
                    } elseif ( $ovabrw_pickup_date < $start_date && $ovabrw_pickoff_date > $end_date ) {
                        $gl_rent_time_1 = get_time_bew_2day( $ovabrw_pickup_date, $start_date, $product_id );
                        $gl_quantity_1  = ovabrw_quantity_global( $product_id, $gl_rent_time_1 );
                        $gl_rent_time_3 = get_time_bew_2day( $end_date, $ovabrw_pickoff_date, $product_id );
                        $gl_quantity_3  = ovabrw_quantity_global( $product_id, $gl_rent_time_3 );
                        $gl_quantity    = $gl_quantity_1 + $gl_quantity_3;
                        $rt_rent_time_2 = get_time_bew_2day( $start_date, $end_date, $product_id );
                        $rt_quantity    = ovabrw_quantity_global( $product_id, $rt_rent_time_2 );
                        $gl_price = ovabrw_price_global( $product_id, $gl_rent_time_1 );
                        $rt_price = ovabrw_price_special_time( $product_id, $rt_rent_time_2, $key_rt );

                        $rt_quantity_total_4    += $rt_quantity;
                        $rt_price_total_4       += $rt_quantity * $rt_price;

                        $quantity   = $gl_quantity + $rt_quantity;
                        $line_total = $gl_price * $gl_quantity + $rt_quantity * $rt_price;

                        if ( $rental_type === "mixed" ) {
                            $ovabrw_rt_price_hour   = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
                            $rt_quantity            = ($rt_rent_time_2['rent_time_day_raw']);
                            $gl_quantity            = $quantity_day - $rt_quantity;
                            $line_total             = $gl_price * $gl_quantity + $rt_quantity * $rt_price + $quantity_hour * $gl_price_hour;

                            if ( $flag_max ) {
                                $hour_total = $quantity_hour * $gl_price_hour;
                            }
                        }
                    }
                }
            }

            $rt_quantity_total  = $rt_quantity_total_1 + $rt_quantity_total_2 + $rt_quantity_total_3 + $rt_quantity_total_4;
            $rt_price_total     = $rt_price_total_1 + $rt_price_total_2 + $rt_price_total_3 + $rt_price_total_4;

            if ( $rt_quantity_total ) {
                $gl_quantity    = $gl_quantity_total - $rt_quantity_total;
                $line_total     = $gl_quantity * $gl_price_total + $rt_price_total;
            }
            /* /Table Price - Special Time ( Range Time) *******************************************/
        }

        $ovabrw_amount_insurance = floatval(get_post_meta( $product_id, 'ovabrw_amount_insurance', true ));
        $line_total +=  $ovabrw_amount_insurance;

        $resources = $resources_qty = [];

        $resources      = isset( $cart_item['resources'] ) ? $cart_item['resources'] : [];
        $resources_qty  = isset( $cart_item['resources_qty'] ) ? $cart_item['resources_qty'] : [];

        // Total Resources
        $total_resource = ovabrw_get_total_resources( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $resources, $resources_qty );
        $line_total     += $total_resource;

        // Total Services
        $ovabrw_service = isset( $cart_item['ovabrw_service'] ) ? $cart_item['ovabrw_service'] : [];
        $services_qty   = isset( $cart_item['ovabrw_service_qty'] ) ? $cart_item['ovabrw_service_qty'] : [];
        $total_service  = ovabrw_get_total_services( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $ovabrw_service, $services_qty );
        $line_total += $total_service;

        $ovabrw_number_vehicle = isset( $cart_item['ovabrw_number_vehicle'] ) ? (int)$cart_item['ovabrw_number_vehicle'] : 1;

        $line_total *= $ovabrw_number_vehicle;
        $ovabrw_amount_insurance *= $ovabrw_number_vehicle;

        // Custom Checkout Fields
        if ( isset( $cart_item['custom_ckf'] ) && ! empty( $cart_item['custom_ckf'] ) ) {
            $custom_ckf_qty = isset( $cart_item['custom_ckf_qty'] ) && ! empty( $cart_item['custom_ckf_qty'] ) ? $cart_item['custom_ckf_qty'] : [];

            $price_ckf = ovabrw_get_price_ckf( $product_id, $cart_item['custom_ckf'], $custom_ckf_qty );

            if ( $price_ckf ) {
                $line_total += $price_ckf * $ovabrw_number_vehicle;
            }
        }

        return [ 'quantity' => $quantity, 'line_total' => $line_total, 'amount_insurance' => $ovabrw_amount_insurance ];
    }
}

// Get Price by Distance
if ( ! function_exists( 'get_price_by_distance' ) ) {
    function get_price_by_distance( $product_id = false, $pickup_date = false, $pickoff_date = false, $cart_item = [] ) {
        $result = [
            'quantity'          => 0,
            'line_total'        => 0,
            'amount_insurance'  => 0,
        ];

        if ( ! $product_id ) return $result;

        if ( ! empty( $cart_item ) && is_array( $cart_item ) ) {
            $qty = isset( $cart_item['ovabrw_number_vehicle'] ) ? absint( $cart_item['ovabrw_number_vehicle'] ) : 1;

            $regular_price  = floatval( get_post_meta( $product_id, 'ovabrw_regul_price_taxi', true ) );
            $insurance      = floatval( get_post_meta( $product_id, 'ovabrw_amount_insurance', true ) );
            $price_by       = get_post_meta( $product_id, 'ovabrw_map_price_by', true );
            $distance       = isset( $cart_item['distance'] ) ? floatval( $cart_item['distance'] ) : 0;

            if ( ! $price_by ) $price_by = 'km';

            if ( $distance ) {
                if ( $price_by === 'km' ) {
                    $distance = apply_filters( 'ovabrw_ft_distance', round( $distance / 1000, 2 ), $product_id );
                } else {
                    $distance = apply_filters( 'ovabrw_ft_distance', round( $distance / 1609.34, 2 ), $product_id );
                }
            }

            $subtotal = $regular_price * $distance;

            // Discount by Distance
            if ( get_price_by_global_discount_distance( $product_id, $distance, $subtotal ) ) {
                $subtotal = get_price_by_global_discount_distance( $product_id, $distance, $subtotal );
            }

            // Special Time
            if ( get_price_by_special_time_distance( $product_id, $distance, $subtotal, $pickup_date ) ) {
                $subtotal = get_price_by_special_time_distance( $product_id, $distance, $subtotal, $pickup_date );
            }

            // Insurance
            if ( $insurance ) $subtotal += $insurance;

            // Extra time
            if ( isset( $cart_item['extra_time'] ) && $cart_item['extra_time'] ) {
                $extra_time_hour    = get_post_meta( $product_id, 'ovabrw_extra_time_hour', true );
                $extra_time_price   = get_post_meta( $product_id, 'ovabrw_extra_time_price', true );

                if ( ! empty( $extra_time_hour ) && is_array( $extra_time_hour ) ) {
                    foreach ( $extra_time_hour as $k => $val ) {
                        $extt_price = isset( $extra_time_price[$k] ) ? floatval( $extra_time_price[$k] ) : 0;

                        if ( $val == $cart_item['extra_time'] ) {
                            $subtotal += $extt_price;
                            break;
                        }
                    }
                }
            }

            // Resources
            $resources      = isset( $cart_item['resources'] ) ? $cart_item['resources'] : [];
            $resources_qty  = isset( $cart_item['resources_qty'] ) ? $cart_item['resources_qty'] : [];
            $total_resource = ovabrw_get_total_resources( $product_id, $pickup_date, $pickoff_date, $resources, $resources_qty );
            $subtotal       += floatval( $total_resource );

            // Services
            $services       = isset( $cart_item['ovabrw_service'] ) ? $cart_item['ovabrw_service'] : [];
            $services_qty   = isset( $cart_item['ovabrw_service_qty'] ) ? $cart_item['ovabrw_service_qty'] : [];
            $total_service  = ovabrw_get_total_services( $product_id, $pickup_date, $pickoff_date, $services, $services_qty );
            $subtotal       += floatval( $total_service );

            // Total
            $total = $qty * $subtotal;

            // Custom Checkout Fields
            if ( isset( $cart_item['custom_ckf'] ) && ! empty( $cart_item['custom_ckf'] ) ) {
                $custom_ckf_qty = isset( $cart_item['custom_ckf_qty'] ) && ! empty( $cart_item['custom_ckf_qty'] ) ? $cart_item['custom_ckf_qty'] : [];

                $price_ckf = ovabrw_get_price_ckf( $product_id, $cart_item['custom_ckf'], $custom_ckf_qty );

                if ( $price_ckf ) {
                    $total += $price_ckf * $qty;
                }
            }

            $result['quantity']          = $qty;
            $result['line_total']        = $total;
            $result['amount_insurance']  = $qty * $insurance;
        }

        return $result;
    }
}

if ( ! function_exists( 'get_price_by_global_discount_distance' ) ) {
    function get_price_by_global_discount_distance( $product_id = false, $distance = 0, $subtotal = 0 ) {
        if ( ! $product_id || ! $distance ) return $subtotal;

        $regular_price  = floatval( get_post_meta( $product_id, 'ovabrw_regul_price_taxi', true ) );
        $distance_price = get_post_meta( $product_id, 'ovabrw_discount_distance_price', true );

        if ( ! empty( $distance_price ) && is_array( $distance_price ) ) {
            $distance_from  = get_post_meta( $product_id, 'ovabrw_discount_distance_from', true );
            $distance_to    = get_post_meta( $product_id, 'ovabrw_discount_distance_to', true );

            $total_discount     = 0;
            $remaining_distance = $distance;

            foreach ( $distance_price as $k => $price ) {
                $from   = isset( $distance_from[$k] ) ? $distance_from[$k] : '';
                $to     = isset( $distance_to[$k] ) ? $distance_to[$k] : '';

                $distance_discount = 0;

                if ( $from != '' && $to != '' && $from <= $to ) {
                    if ( $from < $distance && $to <= $distance ) {
                        $distance_discount  = $to - $from;
                        $total_discount     += floatval( $price ) * floatval( $distance_discount );
                        $remaining_distance -= $distance_discount;
                    } else if ( $from < $distance && $to >= $distance ) {
                        $distance_discount  = $distance - $from;
                        $total_discount     += floatval( $price ) * floatval( $distance_discount );
                        $remaining_distance -= $distance_discount;
                    } else {
                        continue;
                    }
                }
            }

            $subtotal = $regular_price * floatval( $remaining_distance ) + $total_discount;
        }

        return floatval( $subtotal );
    }
}

if ( ! function_exists( 'get_price_by_special_time_distance' ) ) {
    function get_price_by_special_time_distance( $product_id = false, $distance = 0, $subtotal = 0, $pickup_date = 0 ) {
        if ( ! $product_id || ! $distance || ! $pickup_date ) return $subtotal;

        $pickup_st = get_post_meta( $product_id, 'ovabrw_st_pickup_distance', true );

        if ( ! empty( $pickup_st ) && is_array( $pickup_st ) ) {
            $dropoff_st     = get_post_meta( $product_id, 'ovabrw_st_pickoff_distance', true );
            $price_st       = get_post_meta( $product_id, 'ovabrw_st_price_distance', true );
            $discount_st    = get_post_meta( $product_id, 'ovabrw_st_discount_distance', true );

            foreach ( $pickup_st as $k => $pickup ) {
                $dropoff    = isset( $dropoff_st[$k] ) ? $dropoff_st[$k] : '';
                $price      = isset( $price_st[$k] ) ? $price_st[$k] : '';
                $dsc_from   = isset( $discount_st[$k]['from'] ) ? $discount_st[$k]['from'] : '';
                $dsc_to     = isset( $discount_st[$k]['to'] ) ? $discount_st[$k]['to'] : '';
                $dsc_price  = isset( $discount_st[$k]['price'] ) ? $discount_st[$k]['price'] : '';

                if ( strtotime( $pickup ) && strtotime( $dropoff ) ) {
                    if ( strtotime( $pickup ) <= $pickup_date && $pickup_date <= strtotime( $dropoff ) ) {
                        $subtotal = floatval( $price ) * floatval( $distance );

                        if ( ! empty( $dsc_from ) && is_array( $dsc_from ) ) {
                            $total_discount     = 0;
                            $remaining_distance = $distance;

                            foreach ( $dsc_from as $k_dsc => $from ) {
                                $to         = isset( $dsc_to[$k_dsc] ) ? $dsc_to[$k_dsc] : '';
                                $disc_price = isset( $dsc_price[$k_dsc] ) ? $dsc_price[$k_dsc] : '';

                                $distance_discount = 0;

                                if ( $from != '' && $to != '' && $from <= $to ) {
                                    if ( $from < $distance && $to <= $distance ) {
                                        $distance_discount  = $to - $from;
                                        $total_discount     += floatval( $disc_price ) * floatval( $distance_discount );
                                        $remaining_distance -= $distance_discount;
                                    } else if ( $from < $distance && $to >= $distance ) {
                                        $distance_discount  = $distance - $from;
                                        $total_discount     += floatval( $disc_price ) * floatval( $distance_discount );
                                        $remaining_distance -= $distance_discount;
                                    } else {
                                        continue;
                                    }
                                }
                            }

                            $subtotal = floatval( $price ) * floatval( $remaining_distance ) + $total_discount;
                        }
                    }
                }
            }
        }

        return floatval( $subtotal );
    }
}

if ( ! function_exists( 'get_price_by_global_discount' ) ) {
    function get_price_by_global_discount( $product_id = false, $ovabrw_global_discount_duration_val_min = array(), $price_type = '', $rent_time = 0 ) {
        if ( $ovabrw_global_discount_duration_val_min ) {
            foreach ( $ovabrw_global_discount_duration_val_min as $key_dur_min => $value_dur_min ) {
               $ovabrw_global_discount_duration_val_max = get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_max', true );
               $ovabrw_global_discount_duration_type    = get_post_meta( $product_id, 'ovabrw_global_discount_duration_type', true );

               $type = isset( $ovabrw_global_discount_duration_type[$key_dur_min] ) ? $ovabrw_global_discount_duration_type[$key_dur_min] : '';
               $max = isset( $ovabrw_global_discount_duration_val_max[$key_dur_min] ) ? $ovabrw_global_discount_duration_val_max[$key_dur_min] : '';

               if ( $type == $price_type && $rent_time >= $value_dur_min && $max >= $rent_time ) {
                    $ovabrw_global_discount_price = get_post_meta( $product_id, 'ovabrw_global_discount_price', true );

                    return $ovabrw_global_discount_price[$key_dur_min];
                }
            }
        }

        return false;
    }
}

if ( ! function_exists( 'get_price_by_rt_discount' ) ) {
    function get_price_by_rt_discount( $ovabrw_rt_discount_duration_min = array(), $ovabrw_rt_discount_duration_max = array(), $ovabrw_rt_discount_duration_type = array(), $ovabrw_rt_discount_duration_price = array(), $price_type = '', $rent_time = 0 ) {
        if ( $ovabrw_rt_discount_duration_min ) {
            foreach( $ovabrw_rt_discount_duration_min as $key_dur => $value_dur ) {
               if ( $ovabrw_rt_discount_duration_type[$key_dur] == $price_type && $rent_time >=  $value_dur && $ovabrw_rt_discount_duration_max[$key_dur] >= $rent_time) {
                    return $ovabrw_rt_discount_duration_price[$key_dur];
                }
            }
        }

        return false;
    }
}

// Quantity in Global
if ( ! function_exists( 'ovabrw_quantity_global' ) ) {
    function ovabrw_quantity_global( $product_id, $rent_time ) {
        $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );
        
        if ( $price_type == 'day' ) {
            return (int)$rent_time['rent_time_day'];
        } elseif ( $price_type == 'hour' ) {
            return (int)$rent_time['rent_time_hour'];
        } else {
            if ( $rent_time['rent_time_day_raw'] < 1 ) {
                return (int)$rent_time['rent_time_hour'];
            } else {
                return (int)$rent_time['rent_time_day'];
            }
        }
    }
}

if ( ! function_exists( 'ovabrw_price_global_by_type_day' ) ) {
    function ovabrw_price_global_by_type_day( $product_id, $rent_time, $qty_special = 0 ) {
        $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

        $ovabrw_global_discount_duration_val_min = get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_min', true );
        if ( $ovabrw_global_discount_duration_val_min ) asort( $ovabrw_global_discount_duration_val_min );

        $gl_price = 0;

        if ( $price_type == 'day' ) {
            // Set Price by Global Discount
            $quantity = absint( $rent_time['rent_time_day'] ) - absint( $qty_special );

            if ( $ovabrw_global_discount_duration_val_min ) {
                $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type = 'days', $quantity );
                $gl_price = ( $gl_set_price != false ) ? $gl_set_price : $gl_price;
            }
        } elseif ( $price_type == 'mixed' ) {
            if ( $rent_time['rent_time_day_raw'] < 1 ) {
                $gl_price = get_post_meta( $product_id, 'ovabrw_regul_price_hour', true );

                $quantity = absint( $rent_time['rent_time_hour'] ) - absint( $qty_special );

                if ( $ovabrw_global_discount_duration_val_min ) {
                    $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type = 'hours', $quantity );
                    $gl_price = ( $gl_set_price != false ) ? $gl_set_price : $gl_price;
                }

                return $gl_price;
            } else {
                $gl_price = get_post_meta( $product_id, '_regular_price', true );

                $quantity = absint( floor( $rent_time['rent_time_day_raw'] ) ) - absint( $qty_special );

                if ( $ovabrw_global_discount_duration_val_min ) {
                    $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type = 'days', $quantity );
                    $gl_price = ( $gl_set_price != false ) ? $gl_set_price : $gl_price;
                }

                return $gl_price;
            }
        }

        return $gl_price;
    }
}

// Price in Global
if ( ! function_exists( 'ovabrw_price_global' ) ) {
    function ovabrw_price_global( $product_id, $rent_time, $special_qty = 0 ) {
        $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );
        $ovabrw_global_discount_duration_val_min = get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_min', true );

        if ( $ovabrw_global_discount_duration_val_min ){ asort( $ovabrw_global_discount_duration_val_min ); }
        if ( $price_type == 'day' ) {
            // Set Price by Global Discount
            $gl_price = get_post_meta( $product_id, '_regular_price', true );
            $quantity = absint( $rent_time['rent_time_day'] ) - absint( $special_qty );

            if ( $ovabrw_global_discount_duration_val_min ) {
                $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type = 'days', $quantity );
                $gl_price = ( $gl_set_price != false ) ? $gl_set_price : $gl_price;
            }

            return (float)$gl_price;
        } elseif ( $price_type == 'hour' ) {
            $gl_price = get_post_meta( $product_id, 'ovabrw_regul_price_hour', true );
            $quantity = absint( $rent_time['rent_time_hour'] ) - absint( $special_qty );

            if ( $ovabrw_global_discount_duration_val_min ) {
                $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type = 'hours', $quantity );
                $gl_price = ( $gl_set_price != false ) ? $gl_set_price : $gl_price;
            }

            return (float)$gl_price;
        } else { // Price type is Mixed
            if ( $rent_time['rent_time_day_raw'] < 1 ) {
                $gl_price = get_post_meta( $product_id, 'ovabrw_regul_price_hour', true );
                $quantity = absint( $rent_time['rent_time_hour'] ) - absint( $special_qty );

                if ( $ovabrw_global_discount_duration_val_min ) {
                    $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type = 'hours', $quantity );
                    $gl_price = ( $gl_set_price != false ) ? $gl_set_price : $gl_price;
                }

                return (float)$gl_price;
            } else {
                $gl_price = get_post_meta( $product_id, '_regular_price', true );
                $quantity = absint( floor( $rent_time['rent_time_day_raw'] ) ) - absint( $special_qty );

                if ( $ovabrw_global_discount_duration_val_min ) {
                    $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type = 'days', $quantity );
                    $gl_price = ( $gl_set_price != false ) ? $gl_set_price : $gl_price;
                }

                return (float)$gl_price;
            }
        }
    }
}

// Get Price for Spceial Time
if ( ! function_exists( 'ovabrw_price_special_time' ) ) {
    function ovabrw_price_special_time( $product_id, $rent_time, $key_rt ) {
        // $ovabrw_start_timestamp = get_post_meta( $product_id, 'ovabrw_start_timestamp', true );
        $ovabrw_rt_string_start = get_post_meta( $product_id, 'ovabrw_rt_string_start', true );
        $ovabrw_rt_price        = get_post_meta( $product_id, 'ovabrw_rt_price', true );
        $ovabrw_rt_price_hour   = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
        $ovabrw_rt_discount     = get_post_meta( $product_id, 'ovabrw_rt_discount', true );
        $price_type             = get_post_meta( $product_id, 'ovabrw_price_type', true );

        if ( isset( $ovabrw_rt_string_start[$key_rt] ) && $ovabrw_rt_string_start[$key_rt] ) {
            // Check if PickUp Date bettwen date of Range Time
            if ( $price_type == 'day' ) {
                $rt_price = $ovabrw_rt_price[$key_rt];

                // Return ST Price Discount
                if ( isset( $ovabrw_rt_discount[$key_rt] ) ) {
                    $ovabrw_rt_discount_duration_min = $ovabrw_rt_discount[$key_rt]['min'];
                    $ovabrw_rt_discount_duration_max = $ovabrw_rt_discount[$key_rt]['max'];

                    if ( $ovabrw_rt_discount_duration_min && $ovabrw_rt_discount_duration_max ) {
                        arsort($ovabrw_rt_discount_duration_min);

                        $ovabrw_rt_discount_duration_type   = $ovabrw_rt_discount[$key_rt]['duration_type'];
                        $ovabrw_rt_discount_duration_price  = $ovabrw_rt_discount[$key_rt]['price'];
                        $st_set_price = get_price_by_rt_discount( $ovabrw_rt_discount_duration_min, $ovabrw_rt_discount_duration_max, $ovabrw_rt_discount_duration_type, $ovabrw_rt_discount_duration_price, $price_type = 'days', $rent_time['rent_time_day'] );
                        $rt_price = $st_set_price != false ? $st_set_price : $rt_price;
                    }
                }

                return $rt_price;
            } elseif ( $price_type == 'hour' ) {
                $rt_price = $ovabrw_rt_price_hour[$key_rt];

                // Set Price by RT(ST) Discount
                if ( isset( $ovabrw_rt_discount[$key_rt] ) ) {
                    $ovabrw_rt_discount_duration_min = $ovabrw_rt_discount[$key_rt]['min'];
                    $ovabrw_rt_discount_duration_max = $ovabrw_rt_discount[$key_rt]['max'];

                    if ( $ovabrw_rt_discount_duration_min && $ovabrw_rt_discount_duration_max ) {
                        arsort($ovabrw_rt_discount_duration_min);

                        $ovabrw_rt_discount_duration_type   = $ovabrw_rt_discount[$key_rt]['duration_type'];
                        $ovabrw_rt_discount_duration_price  = $ovabrw_rt_discount[$key_rt]['price'];
                        $st_set_price =  get_price_by_rt_discount( $ovabrw_rt_discount_duration_min, $ovabrw_rt_discount_duration_max, $ovabrw_rt_discount_duration_type, $ovabrw_rt_discount_duration_price, $price_type = 'hours', $rent_time['rent_time_hour'] );
                        $rt_price = $st_set_price != false ? $st_set_price : $rt_price;
                    }
                }

                return $rt_price;
            } else { // Price type is Mixed
                if ( $rent_time['rent_time_day_raw'] < 1 ) {
                    $rt_price = $ovabrw_rt_price_hour[$key_rt];

                    // Set Price by RT(ST) Discount
                    if ( isset( $ovabrw_rt_discount[$key_rt] ) ) {
                        $ovabrw_rt_discount_duration_min = $ovabrw_rt_discount[$key_rt]['min'];
                        $ovabrw_rt_discount_duration_max = $ovabrw_rt_discount[$key_rt]['max'];

                        if ( $ovabrw_rt_discount_duration_min && $ovabrw_rt_discount_duration_max ) {
                            arsort($ovabrw_rt_discount_duration_min);

                            $ovabrw_rt_discount_duration_type   = $ovabrw_rt_discount[$key_rt]['duration_type'];
                            $ovabrw_rt_discount_duration_price  = $ovabrw_rt_discount[$key_rt]['price'];
                            $st_set_price = get_price_by_rt_discount( $ovabrw_rt_discount_duration_min, $ovabrw_rt_discount_duration_max, $ovabrw_rt_discount_duration_type, $ovabrw_rt_discount_duration_price, $price_type = 'hours', $rent_time['rent_time_hour'] );
                            $rt_price = $st_set_price != false ? $st_set_price : $rt_price;
                        }
                    }

                    return $rt_price;
                } else {
                    $rt_price = $ovabrw_rt_price[$key_rt];

                    // Set Price by RT(ST) Discount
                    if ( isset( $ovabrw_rt_discount[$key_rt] ) ) {
                        $ovabrw_rt_discount_duration_min = $ovabrw_rt_discount[$key_rt]['min'];
                        $ovabrw_rt_discount_duration_max = $ovabrw_rt_discount[$key_rt]['max'];

                        if ( $ovabrw_rt_discount_duration_min && $ovabrw_rt_discount_duration_max ) {
                            arsort($ovabrw_rt_discount_duration_min);

                            $ovabrw_rt_discount_duration_type   = $ovabrw_rt_discount[$key_rt]['duration_type'];
                            $ovabrw_rt_discount_duration_price  = $ovabrw_rt_discount[$key_rt]['price'];
                            $st_set_price = get_price_by_rt_discount( $ovabrw_rt_discount_duration_min, $ovabrw_rt_discount_duration_max, $ovabrw_rt_discount_duration_type, $ovabrw_rt_discount_duration_price, $price_type = 'days', $rent_time['rent_time_day'] );
                            $rt_price = $st_set_price != false ? $st_set_price : $rt_price;
                        }
                    }

                    return $rt_price;
                }
            }
        } // endif
    }
}

if ( ! function_exists( 'get_regular_price_hour_mixed' ) ) {
    function get_regular_price_hour_mixed ( $product_id = false, $quantity_hour = 0 ) {
        $gl_price_hour = get_post_meta( $product_id, 'ovabrw_regul_price_hour', true );
        $ovabrw_global_discount_duration_val_min = get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_min', true );
        if ( $ovabrw_global_discount_duration_val_min ){ asort( $ovabrw_global_discount_duration_val_min ); }

        if ( $ovabrw_global_discount_duration_val_min ) {
            $gl_set_price = get_price_by_global_discount( $product_id, $ovabrw_global_discount_duration_val_min, $price_type='hours', $quantity_hour );
            $gl_price_hour = ( $gl_set_price != false ) ? $gl_set_price : $gl_price_hour;
        }

        return $gl_price_hour;
    }
}

if ( ! function_exists( 'get_special_price_hour_mixed' ) ) {
    function get_special_price_hour_mixed ( $product_id, $ovabrw_rt_price_hour_key, $key_rt, $quantity_hour ) {
        $rt_price_hour      = $ovabrw_rt_price_hour_key;
        $ovabrw_rt_discount = get_post_meta( $product_id, 'ovabrw_rt_discount', true );

        // Set Price by RT(ST) Discount
        if ( isset( $ovabrw_rt_discount[$key_rt] ) ) {
            $ovabrw_rt_discount_duration_min = $ovabrw_rt_discount[$key_rt]['min'];
            $ovabrw_rt_discount_duration_max = $ovabrw_rt_discount[$key_rt]['max'];
            arsort($ovabrw_rt_discount_duration_min);
            $ovabrw_rt_discount_duration_type   = $ovabrw_rt_discount[$key_rt]['duration_type'];
            $ovabrw_rt_discount_duration_price  = $ovabrw_rt_discount[$key_rt]['price'];
            $st_set_price = get_price_by_rt_discount( $ovabrw_rt_discount_duration_min, $ovabrw_rt_discount_duration_max, $ovabrw_rt_discount_duration_type, $ovabrw_rt_discount_duration_price, $price_type='hours', $quantity_hour );
            $rt_price_hour = $st_set_price != false ? $st_set_price : $rt_price_hour;
        }

        return $rt_price_hour;
    }
}

if ( ! function_exists( 'ovabrw_get_price_by_start_date_transport' ) ) {
    function ovabrw_get_price_by_start_date_transport( $product_id, $ovabrw_pickup_loc, $ovabrw_pickoff_loc ) {
        $list_price_transport   = ovabrw_get_list_price_loc_transport($product_id);
        $price_transport        = 0;

        if ( ! empty( $list_price_transport ) && is_array( $list_price_transport ) ) {
            foreach( $list_price_transport as $pickup => $dropoff_price_arr ) {
                if ( $ovabrw_pickup_loc == $pickup && ! empty( $dropoff_price_arr ) && is_array( $dropoff_price_arr ) ) {
                    foreach( $dropoff_price_arr as $dropoff => $price ) {
                        if ( $ovabrw_pickoff_loc ==  $dropoff ) {
                            $price_transport = $price;
                        }
                    }
                }
            }
        }

        return $price_transport;
    }
}

//total price if have discount
if ( ! function_exists( 'get_total_price_special_by_type_price_day' ) ) {
    function get_total_price_special_by_type_price_day( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date ) {
        $ovabrw_rt_startdate    = get_post_meta( $product_id, 'ovabrw_rt_startdate', true );
        $ovabrw_rt_starttime    = get_post_meta( $product_id, 'ovabrw_rt_starttime', true );
        $ovabrw_rt_enddate      = get_post_meta( $product_id, 'ovabrw_rt_enddate', true );
        $ovabrw_rt_endtime      = get_post_meta( $product_id, 'ovabrw_rt_endtime', true );
        $price_type             = get_post_meta( $product_id, 'ovabrw_price_type', true );
        $total_price_special    = 0;
        $rent_time_day          = 0;

        if ( $ovabrw_rt_startdate ) {
            foreach( $ovabrw_rt_startdate as $key_rt => $value_rt ) {
                $start_date_str = $value_rt  . ' ' . $ovabrw_rt_starttime[$key_rt];
                $end_date_str   = $ovabrw_rt_enddate[$key_rt]  . ' ' . $ovabrw_rt_endtime[$key_rt];
                $start_date     = strtotime( $start_date_str );
                $end_date       = strtotime( $end_date_str );

                if ( $ovabrw_pickup_date >= $start_date && $ovabrw_pickoff_date <=  $end_date ) {
                    $rent_time      = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
                    $price_special  = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );

                    if ( $price_type == 'mixed' ) {
                        $rent_time_day = floor( $rent_time['rent_time_day_raw'] );
                    } else {
                        $rent_time_day = $rent_time['rent_time_day'];
                    }

                    $total_price_special += $price_special * $rent_time_day;
                } elseif ( $ovabrw_pickup_date < $start_date && $ovabrw_pickoff_date <=  $end_date && $ovabrw_pickoff_date >= $start_date ) {
                    $rent_time      = get_time_bew_2day( $start_date, $ovabrw_pickoff_date, $product_id );
                    $price_special  = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );

                    if ( $price_type == 'mixed' ) {
                        $rent_time_day = floor( $rent_time['rent_time_day_raw'] );
                    } else {
                        $rent_time_day = $rent_time['rent_time_day'];
                    }

                    $total_price_special += $price_special * $rent_time_day;
                } elseif ( $ovabrw_pickup_date >= $start_date && $ovabrw_pickup_date <= $end_date && $ovabrw_pickoff_date >= $end_date ) {
                    $rent_time      = get_time_bew_2day( $ovabrw_pickup_date, $end_date, $product_id );
                    $price_special  = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );

                    if ( $price_type == 'mixed' ) {
                        $rent_time_day = floor( $rent_time['rent_time_day_raw'] );
                    } else {
                        $rent_time_day = $rent_time['rent_time_day'] ;
                    }

                    $total_price_special += $price_special * $rent_time_day;
                } elseif ( $ovabrw_pickup_date < $start_date && $ovabrw_pickoff_date > $end_date ) {
                    $rent_time      = get_time_bew_2day( $start_date, $end_date, $product_id );
                    $price_special  = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );

                    if ( $price_type == 'mixed' ) {
                        $rent_time_day = floor( $rent_time['rent_time_day_raw']);
                    } else {
                        $rent_time_day = $rent_time['rent_time_day'];
                    }

                    $total_price_special += $price_special * $rent_time_day;
                }
            }
        }

        return [
            'total_price_special'   => $total_price_special,
            'quantity_special'      => $rent_time_day,
        ];
    }
}

//total price real if no discount
if ( ! function_exists( 'get_total_price_special_by_type_price_day_sub' ) ) {
    function get_total_price_special_by_type_price_day_sub( $product_id = false, $ovabrw_pickup_date = false, $ovabrw_pickoff_date = false, $array_price_week = [], $weekday_start = null ) {
        $ovabrw_rt_startdate    = get_post_meta( $product_id, 'ovabrw_rt_startdate', true );
        $ovabrw_rt_starttime    = get_post_meta( $product_id, 'ovabrw_rt_starttime', true );
        $ovabrw_rt_enddate      = get_post_meta( $product_id, 'ovabrw_rt_enddate', true );
        $ovabrw_rt_endtime      = get_post_meta( $product_id, 'ovabrw_rt_endtime', true );
        $price_type             = get_post_meta( $product_id, 'ovabrw_price_type', true );
        $total_price_sub        = 0;

        if ( $ovabrw_rt_startdate ) {
            foreach( $ovabrw_rt_startdate as $key_rt => $value_rt ) {
                $start_date_str = $value_rt  . ' ' . $ovabrw_rt_starttime[$key_rt];
                $end_date_str   = $ovabrw_rt_enddate[$key_rt] . ' ' . $ovabrw_rt_endtime[$key_rt];
                $start_date     = strtotime( $start_date_str );
                $end_date       = strtotime( $end_date_str );

                if ( $ovabrw_pickup_date >= $start_date && $ovabrw_pickoff_date <= $end_date ) {
                    $rent_time      = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
                    $weekday_start  = date('N', $ovabrw_pickup_date);
                    $price_special  = ovabrw_price_special_time( $product_id, $rent_time, $key_rt  );

                    if ( $price_type == 'mixed' ) {
                        $rent_time_day = floor( $rent_time['rent_time_day_raw'] );
                    } else {
                        $rent_time_day = $rent_time['rent_time_day'];
                    }

                    $total_price_sub += get_price_day_by_weekday_start($weekday_start, $rent_time_day, $array_price_week);
                } elseif ( $ovabrw_pickup_date < $start_date && $ovabrw_pickoff_date <= $end_date && $ovabrw_pickoff_date >= $start_date ){
                    $rent_time      = get_time_bew_2day( $start_date, $ovabrw_pickoff_date, $product_id );
                    $weekday_start  = date('N', $start_date);
                    $price_special  = ovabrw_price_special_time( $product_id, $rent_time, $key_rt  );

                    if ( $price_type == 'mixed' ) {
                        $rent_time_day = floor( $rent_time['rent_time_day_raw'] );
                    } else {
                        $rent_time_day = $rent_time['rent_time_day'];
                    }

                    $total_price_sub += get_price_day_by_weekday_start($weekday_start, $rent_time_day, $array_price_week);
                } elseif ( $ovabrw_pickup_date >= $start_date && $ovabrw_pickup_date <= $end_date && $ovabrw_pickoff_date >= $end_date ) {
                    $rent_time      = get_time_bew_2day( $ovabrw_pickup_date, $end_date, $product_id );
                    $weekday_start  = date('N', $ovabrw_pickup_date );
                    $price_special  = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );
                    
                    if ( $price_type == 'mixed' ) {
                        $rent_time_day = floor( $rent_time['rent_time_day_raw']);
                    } else {
                        $rent_time_day = $rent_time['rent_time_day'];
                    }

                    $total_price_sub += get_price_day_by_weekday_start($weekday_start, $rent_time_day, $array_price_week);
                } elseif ( $ovabrw_pickup_date < $start_date && $ovabrw_pickoff_date > $end_date ) {
                    $rent_time      = get_time_bew_2day( $start_date, $end_date, $product_id );
                    $weekday_start  = date('N', $start_date);
                    $price_special  = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );

                    if ( $price_type == 'mixed' ) {
                        $rent_time_day = floor( $rent_time['rent_time_day_raw'] );
                    } else {
                        $rent_time_day = $rent_time['rent_time_day'];
                    }

                    $total_price_sub += get_price_day_by_weekday_start($weekday_start, $rent_time_day, $array_price_week);
                }    
            }
        }

        return $total_price_sub;
    }
}

if ( ! function_exists( 'get_price_by_rental_type_day' ) ) {
    function get_price_by_rental_type_day( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date ) {
        $ovabrw_pickup_date     = $ovabrw_pickup_date == '' ? null : $ovabrw_pickup_date;
        $ovabrw_pickoff_date    = $ovabrw_pickoff_date == '' ? null : $ovabrw_pickoff_date;

        $rent_time  = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
        $weekstart  = date('N', $ovabrw_pickup_date);
        $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

        if ( $price_type == 'mixed' ) {
            $number_day = floor( $rent_time['rent_time_day_raw'] );
        } else {
            $number_day = $rent_time['rent_time_day'];
        }

        $regular_price = ! empty( get_post_meta( $product_id, '_regular_price', true ) ) ? get_post_meta( $product_id, '_regular_price', true ) : 0;

        $ovabrw_daily_monday    =  get_post_meta( $product_id, 'ovabrw_daily_monday', true ) ? (float)get_post_meta( $product_id, 'ovabrw_daily_monday', true ) : $regular_price;
        $ovabrw_daily_tuesday   = get_post_meta( $product_id, 'ovabrw_daily_tuesday', true ) ? (float)get_post_meta( $product_id, 'ovabrw_daily_tuesday', true ) : $regular_price;
        $ovabrw_daily_wednesday = get_post_meta( $product_id, 'ovabrw_daily_wednesday', true ) ? (float)get_post_meta( $product_id, 'ovabrw_daily_wednesday', true ) : $regular_price;
        $ovabrw_daily_thursday  = get_post_meta( $product_id, 'ovabrw_daily_thursday', true ) ? (float)get_post_meta( $product_id, 'ovabrw_daily_thursday', true ) : $regular_price;
        $ovabrw_daily_friday    = get_post_meta( $product_id, 'ovabrw_daily_friday', true ) ?  (float)get_post_meta( $product_id, 'ovabrw_daily_friday', true ) : $regular_price;
        $ovabrw_daily_saturday  = get_post_meta( $product_id, 'ovabrw_daily_saturday', true ) ? (float)get_post_meta( $product_id, 'ovabrw_daily_saturday', true ) : $regular_price;
        $ovabrw_daily_sunday    = get_post_meta( $product_id, 'ovabrw_daily_sunday', true ) ? (float)get_post_meta( $product_id, 'ovabrw_daily_sunday', true ) : $regular_price;

        $price_global = ovabrw_price_global_by_type_day( $product_id, $rent_time );

        // Get total cost in special time
        $data_special_day   = get_total_price_special_by_type_price_day( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date );
        $price_special_day  = floatval( $data_special_day['total_price_special'] );
        $quantity_special   = absint( $data_special_day['quantity_special'] );

        if ( $quantity_special ) {
            $price_global = ovabrw_price_global_by_type_day( $product_id, $rent_time, $quantity_special );
        }

        if ( ! empty ( $price_global ) ) {
            $ovabrw_daily_monday = $ovabrw_daily_tuesday = $ovabrw_daily_wednesday = $ovabrw_daily_thursday = $ovabrw_daily_friday = $ovabrw_daily_saturday = $ovabrw_daily_sunday = $price_global;
        }

        $array_price_week = [$ovabrw_daily_monday, $ovabrw_daily_tuesday, $ovabrw_daily_wednesday, $ovabrw_daily_thursday, $ovabrw_daily_friday, $ovabrw_daily_saturday, $ovabrw_daily_sunday];

        // Get price total from monday to sunday
        $price_total_by_weekday = get_price_day_by_weekday_start($weekstart, $number_day, $array_price_week);
        
        // Get total cost
        $price_special_day_sub = get_total_price_special_by_type_price_day_sub( $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date, $array_price_week, $weekstart );

        $price_total = $price_total_by_weekday + $price_special_day - $price_special_day_sub;

        return array( 'quantity' => $number_day, 'line_total' => $price_total );
    }
}

if ( ! function_exists( 'get_price_day_by_weekday_start' ) ) {
    function get_price_day_by_weekday_start ( $weekstart, $number_day, $arry_price ) {
        $number_weeks           = floor( $number_day / 7 );
        $rent_day_of_week       = $number_day % 7;
        $ovabrw_daily_monday    = $arry_price[0];
        $ovabrw_daily_tuesday   = $arry_price[1];
        $ovabrw_daily_wednesday = $arry_price[2];
        $ovabrw_daily_thursday  = $arry_price[3];
        $ovabrw_daily_friday    = $arry_price[4];
        $ovabrw_daily_saturday  = $arry_price[5];
        $ovabrw_daily_sunday    = $arry_price[6];

        $price_total = $price_number_week = $price_rent_day_of_week = 0;

        switch( $weekstart ) {
            //monday
            case 1: {
                $price_number_week = 0;

                if ( $number_weeks > 0 ) {
                    $price_mondays      = $ovabrw_daily_monday * $number_weeks;
                    $price_tuesday      = $ovabrw_daily_tuesday * $number_weeks;
                    $price_wednesday    = $ovabrw_daily_wednesday * $number_weeks;
                    $price_thursday     = $ovabrw_daily_thursday * $number_weeks;
                    $price_friday       = $ovabrw_daily_friday * $number_weeks;
                    $price_saturday     = $ovabrw_daily_saturday * $number_weeks;
                    $price_sunday       = $ovabrw_daily_sunday * $number_weeks;
                    $price_number_week = $price_mondays + $price_tuesday + $price_wednesday + $price_thursday + $price_friday + $price_saturday + $price_sunday;
                }

                if ( $rent_day_of_week > 0 ) {
                    if ( $rent_day_of_week === 1 ) {
                        $price_rent_day_of_week = $ovabrw_daily_monday;
                    } elseif ( $rent_day_of_week === 2 ) {
                        $price_rent_day_of_week = $ovabrw_daily_monday + $ovabrw_daily_tuesday;
                    } elseif ( $rent_day_of_week === 3 ) {
                        $price_rent_day_of_week = $ovabrw_daily_monday + $ovabrw_daily_tuesday + $ovabrw_daily_wednesday;
                    } elseif ( $rent_day_of_week === 4 ) {
                        $price_rent_day_of_week = $ovabrw_daily_monday + $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday;
                    } elseif ( $rent_day_of_week === 5 ) {
                        $price_rent_day_of_week = $ovabrw_daily_monday + $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday;
                    } elseif ( $rent_day_of_week === 6 ) {
                        $price_rent_day_of_week = $ovabrw_daily_monday + $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday;
                    }
                }

                $price_total = $price_number_week + $price_rent_day_of_week;
                break;
            }
            //tuesday
            case 2: {
                if ( $number_weeks > 0 ) {
                    $price_mondays      = $ovabrw_daily_monday * $number_weeks;
                    $price_tuesday      = $ovabrw_daily_tuesday * $number_weeks;
                    $price_wednesday    = $ovabrw_daily_wednesday * $number_weeks;
                    $price_thursday     = $ovabrw_daily_thursday * $number_weeks;
                    $price_friday       = $ovabrw_daily_friday * $number_weeks;
                    $price_saturday     = $ovabrw_daily_saturday * $number_weeks;
                    $price_sunday       = $ovabrw_daily_sunday * $number_weeks;
                    $price_number_week  = $price_mondays + $price_tuesday + $price_wednesday + $price_thursday + $price_friday + $price_saturday + $price_sunday;
                }

                if ( $rent_day_of_week > 0 ) {
                    if ( $rent_day_of_week === 1 ) {
                        $price_rent_day_of_week = $ovabrw_daily_tuesday;
                    } elseif ( $rent_day_of_week === 2 ) {
                        $price_rent_day_of_week = $ovabrw_daily_tuesday + $ovabrw_daily_wednesday;
                    } elseif ( $rent_day_of_week === 3 ) {
                        $price_rent_day_of_week = $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday;
                    } elseif ( $rent_day_of_week === 4 ) {
                        $price_rent_day_of_week = $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday;
                    } elseif ( $rent_day_of_week === 5 ) {
                        $price_rent_day_of_week = $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday;
                    } elseif ( $rent_day_of_week === 6 ) {
                        $price_rent_day_of_week = $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday;
                    }
                }

                $price_total = $price_number_week + $price_rent_day_of_week;
                break;
            }

            //wecnesday
            case 3: {
                if ( $number_weeks > 0 ) {
                    $price_mondays      = $ovabrw_daily_monday * $number_weeks;
                    $price_tuesday      = $ovabrw_daily_tuesday * $number_weeks;
                    $price_wednesday    = $ovabrw_daily_wednesday * $number_weeks;
                    $price_thursday     = $ovabrw_daily_thursday * $number_weeks;
                    $price_friday       = $ovabrw_daily_friday * $number_weeks;
                    $price_saturday     = $ovabrw_daily_saturday * $number_weeks;
                    $price_sunday       = $ovabrw_daily_sunday * $number_weeks;
                    $price_number_week  = $price_mondays + $price_tuesday + $price_wednesday + $price_thursday + $price_friday + $price_saturday + $price_sunday;
                }

                if ( $rent_day_of_week > 0 ) {
                    if ( $rent_day_of_week === 1 ) {
                        $price_rent_day_of_week = $ovabrw_daily_wednesday;
                    } elseif ( $rent_day_of_week === 2 ) {
                        $price_rent_day_of_week = $ovabrw_daily_wednesday + $ovabrw_daily_thursday;
                    } elseif ( $rent_day_of_week === 3 ) {
                        $price_rent_day_of_week = $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday;
                    } elseif ( $rent_day_of_week === 4 ) {
                        $price_rent_day_of_week = $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday;
                    } elseif ( $rent_day_of_week === 5 ) {
                        $price_rent_day_of_week = $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday;
                    } elseif ( $rent_day_of_week === 6 ) {
                        $price_rent_day_of_week = $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday;
                    }
                }

                $price_total = $price_number_week + $price_rent_day_of_week;
                break;
            }
            //thursday
            case 4: {
                if ( $number_weeks > 0 ) {
                    $price_mondays      = $ovabrw_daily_monday * $number_weeks;
                    $price_tuesday      = $ovabrw_daily_tuesday * $number_weeks;
                    $price_wednesday    = $ovabrw_daily_wednesday * $number_weeks;
                    $price_thursday     = $ovabrw_daily_thursday * $number_weeks;
                    $price_friday       = $ovabrw_daily_friday * $number_weeks;
                    $price_saturday     = $ovabrw_daily_saturday * $number_weeks;
                    $price_sunday       = $ovabrw_daily_sunday * $number_weeks;
                    $price_number_week  = $price_mondays + $price_tuesday + $price_wednesday + $price_thursday + $price_friday + $price_saturday + $price_sunday;
                }

                if ( $rent_day_of_week > 0 ) {
                    if ( $rent_day_of_week === 1 ) {
                        $price_rent_day_of_week = $ovabrw_daily_thursday;
                    } elseif ( $rent_day_of_week === 2 ) {
                        $price_rent_day_of_week = $ovabrw_daily_thursday + $ovabrw_daily_friday;
                    } elseif ( $rent_day_of_week === 3 ) {
                        $price_rent_day_of_week = $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday;
                    } elseif ( $rent_day_of_week === 4 ) {
                        $price_rent_day_of_week = $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday;
                    } elseif ( $rent_day_of_week === 5 ) {
                        $price_rent_day_of_week = $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday;
                    } elseif ( $rent_day_of_week === 6 ) {
                        $price_rent_day_of_week = $ovabrw_daily_thursday + $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday;
                    }
                }

                $price_total = $price_number_week + $price_rent_day_of_week;
                break;
            }
            //friday
            case 5 : {
                if ( $number_weeks > 0 ) {
                    $price_mondays      = $ovabrw_daily_monday * $number_weeks;
                    $price_tuesday      = $ovabrw_daily_tuesday * $number_weeks;
                    $price_wednesday    = $ovabrw_daily_wednesday * $number_weeks;
                    $price_thursday     = $ovabrw_daily_thursday * $number_weeks;
                    $price_friday       = $ovabrw_daily_friday * $number_weeks;
                    $price_saturday     = $ovabrw_daily_saturday * $number_weeks;
                    $price_sunday       = $ovabrw_daily_sunday * $number_weeks;
                    $price_number_week  = $price_mondays + $price_tuesday + $price_wednesday + $price_thursday + $price_friday + $price_saturday + $price_sunday;
                }

                if ( $rent_day_of_week > 0 ) {
                    if ( $rent_day_of_week === 1 ) {
                        $price_rent_day_of_week = $ovabrw_daily_friday;
                    } elseif ( $rent_day_of_week === 2 ) {
                        $price_rent_day_of_week = $ovabrw_daily_friday + $ovabrw_daily_saturday;
                    } elseif ( $rent_day_of_week === 3 ) {
                        $price_rent_day_of_week = $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday;
                    } elseif ( $rent_day_of_week === 4 ) {
                        $price_rent_day_of_week = $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday;
                    } elseif ( $rent_day_of_week === 5 ) {
                        $price_rent_day_of_week = $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday;
                    } elseif ( $rent_day_of_week === 6 ) {
                        $price_rent_day_of_week = $ovabrw_daily_friday + $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday + $ovabrw_daily_wednesday;
                    }
                }

                $price_total = $price_number_week + $price_rent_day_of_week;
                break;
            }
            //saturday
            case 6: {
                if ( $number_weeks > 0 ) {
                    $price_mondays      = $ovabrw_daily_monday * $number_weeks;
                    $price_tuesday      = $ovabrw_daily_tuesday * $number_weeks;
                    $price_wednesday    = $ovabrw_daily_wednesday * $number_weeks;
                    $price_thursday     = $ovabrw_daily_thursday * $number_weeks;
                    $price_friday       = $ovabrw_daily_friday * $number_weeks;
                    $price_saturday     = $ovabrw_daily_saturday * $number_weeks;
                    $price_sunday       = $ovabrw_daily_sunday * $number_weeks;
                    $price_number_week  = $price_mondays + $price_tuesday + $price_wednesday + $price_thursday + $price_friday + $price_saturday + $price_sunday;
                }

                if ( $rent_day_of_week > 0 ) {
                    if ( $rent_day_of_week === 1 ) {
                        $price_rent_day_of_week = $ovabrw_daily_saturday;
                    } elseif ( $rent_day_of_week === 2 ) {
                        $price_rent_day_of_week = $ovabrw_daily_saturday + $ovabrw_daily_sunday;
                    } elseif ( $rent_day_of_week === 3 ) {
                        $price_rent_day_of_week = $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday;
                    } elseif ( $rent_day_of_week === 4 ) {
                        $price_rent_day_of_week = $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday;
                    } elseif ( $rent_day_of_week === 5 ) {
                        $price_rent_day_of_week = $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday + $ovabrw_daily_wednesday;
                    } elseif ( $rent_day_of_week === 6 ) {
                        $price_rent_day_of_week = $ovabrw_daily_saturday + $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday;
                    }
                }

                $price_total = $price_number_week + $price_rent_day_of_week;
                break;
            }
            //sunday
            case 7: {
                if ( $number_weeks > 0 ) {
                    $price_mondays      = $ovabrw_daily_monday * $number_weeks;
                    $price_tuesday      = $ovabrw_daily_tuesday * $number_weeks;
                    $price_wednesday    = $ovabrw_daily_wednesday * $number_weeks;
                    $price_thursday     = $ovabrw_daily_thursday * $number_weeks;
                    $price_friday       = $ovabrw_daily_friday * $number_weeks;
                    $price_saturday     = $ovabrw_daily_saturday * $number_weeks;
                    $price_sunday       = $ovabrw_daily_sunday * $number_weeks;
                    $price_number_week  = $price_mondays + $price_tuesday + $price_wednesday + $price_thursday + $price_friday + $price_saturday + $price_sunday;
                }

                if ( $rent_day_of_week > 0 ) {
                    if ( $rent_day_of_week === 1 ) {
                        $price_rent_day_of_week = $ovabrw_daily_sunday;
                    } elseif ( $rent_day_of_week === 2 ) {
                        $price_rent_day_of_week = $ovabrw_daily_sunday + $ovabrw_daily_monday;
                    } elseif ( $rent_day_of_week === 3 ) {
                        $price_rent_day_of_week = $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday;
                    } elseif ( $rent_day_of_week === 4 ) {
                        $price_rent_day_of_week = $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday + $ovabrw_daily_wednesday;
                    } elseif ( $rent_day_of_week === 5 ) {
                        $price_rent_day_of_week = $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday;
                    } elseif ( $rent_day_of_week === 6 ) {
                        $price_rent_day_of_week = $ovabrw_daily_sunday + $ovabrw_daily_monday +  $ovabrw_daily_tuesday + $ovabrw_daily_wednesday + $ovabrw_daily_thursday + $ovabrw_daily_friday;
                    }
                }

                $price_total = $price_number_week + $price_rent_day_of_week;
                break;
            }
        }

        return $price_total;
    }
}

// Get Real Price
if ( ! function_exists( 'get_real_price_detail' ) ) {
    function get_real_price_detail( $product_price, $product_id, $ovabrw_pickup_date, $ovabrw_pickoff_date ) {
        $rent_time  = get_time_bew_2day( $ovabrw_pickup_date, $ovabrw_pickoff_date, $product_id );
        $price_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

        $ovabrw_rt_startdate    = get_post_meta( $product_id, 'ovabrw_rt_startdate', true );
        $ovabrw_rt_enddate      = get_post_meta( $product_id, 'ovabrw_rt_enddate', true );

        // Global
        $gl_price       = ovabrw_price_global( $product_id, $rent_time );
        $product_price  =  $gl_price ;

        if ( $ovabrw_rt_startdate ) {
            foreach( $ovabrw_rt_startdate as $key_rt => $value_rt ) {
                // If Special_Time_Start_Date <= pickup_date && pickoff_date <= Special_Time_End_Date
                if ( $ovabrw_pickup_date >= strtotime( $ovabrw_rt_startdate[$key_rt] ) && $ovabrw_pickoff_date <= strtotime( $ovabrw_rt_enddate[$key_rt] ) ) {
                    $rt_price       = ovabrw_price_special_time( $product_id, $rent_time, $key_rt );
                    $product_price  =  $rt_price.' '.ovabrw_text_time_rt( $price_type, $rent_time );
                    break;
                } elseif ( $ovabrw_pickup_date < strtotime( $ovabrw_rt_startdate[$key_rt] ) && $ovabrw_pickoff_date <= strtotime( $ovabrw_rt_enddate[$key_rt] ) && $ovabrw_pickoff_date >= strtotime( $ovabrw_rt_startdate[$key_rt] ) ) {
                    $gl_quantity_array  = get_time_bew_2day( $ovabrw_pickup_date, strtotime( $ovabrw_rt_startdate[$key_rt] ), $product_id );
                    $gl_price           = ovabrw_price_global( $product_id, $gl_quantity_array );
                    $rt_quantity_array  = get_time_bew_2day( strtotime( $ovabrw_rt_startdate[$key_rt] ), $ovabrw_pickoff_date, $product_id );
                    $rt_price           = ovabrw_price_special_time( $product_id, $rt_quantity_array, $key_rt );
                    $product_price      =  $gl_price .' '.ovabrw_text_time_gl( $price_type, $rent_time ).' ' . $rt_price  .' '.ovabrw_text_time_rt( $price_type, $rent_time );
                    break;
                } elseif ( $ovabrw_pickup_date >= strtotime( $ovabrw_rt_startdate[$key_rt] ) && $ovabrw_pickup_date <= strtotime( $ovabrw_rt_enddate[$key_rt] ) && $ovabrw_pickoff_date >= strtotime( $ovabrw_rt_enddate[$key_rt] ) ) {
                    $rt_quantity_array  = get_time_bew_2day( $ovabrw_pickup_date, strtotime( $ovabrw_rt_enddate[$key_rt] ), $product_id );
                    $rt_price           = ovabrw_price_special_time( $product_id, $rt_quantity_array, $key_rt );
                    $gl_quantity_array  = get_time_bew_2day( strtotime( $ovabrw_rt_enddate[$key_rt] ), $ovabrw_pickoff_date, $product_id );
                    $gl_price           = ovabrw_price_global( $product_id, $gl_quantity_array );
                    $product_price      =  $gl_price . ' ' . ovabrw_text_time_gl( $price_type, $rent_time ) . ' ' . $rt_price . ' ' . ovabrw_text_time_rt( $price_type, $rent_time );
                    break;
                } elseif ( $ovabrw_pickup_date < strtotime( $ovabrw_rt_startdate[$key_rt] ) && $ovabrw_pickoff_date > strtotime( $ovabrw_rt_enddate[$key_rt] ) ) {
                    // Time section 1
                    $gl_quantity_array  = get_time_bew_2day( $ovabrw_pickup_date, strtotime( $ovabrw_rt_startdate[$key_rt] ), $product_id );
                    $gl_price           = ovabrw_price_global( $product_id, $gl_quantity_array );
                    $rent_time_2        = get_time_bew_2day( strtotime( $ovabrw_rt_startdate[$key_rt] ), strtotime( $ovabrw_rt_enddate[$key_rt] ), $product_id );
                    $rt_price           = ovabrw_price_special_time( $product_id, $rent_time_2, $key_rt );
                    $product_price      = $rt_price . ' ' . ovabrw_text_time_rt( $price_type, $rent_time ) . ' ' . $gl_price . ' ' . ovabrw_text_time_gl( $price_type, $rent_time );
                    break;
                }
            }
        }

        return $product_price; 
    }
}

// Get Price Custom Checkout Fields
if ( ! function_exists( 'ovabrw_get_price_ckf' ) ) {
    function ovabrw_get_price_ckf( $product_id = false, $args_ckf = [], $args_ckf_qty = [] ) {
        if ( ! $product_id || empty( $args_ckf ) ) return 0;

        $price = 0;
        $list_custom_ckf = ovabrw_get_list_field_checkout( $product_id );

        foreach ( $args_ckf as $key => $val ) {
            if ( isset( $list_custom_ckf[$key] ) && ! empty( $list_custom_ckf[$key] ) ) {
                $type = $list_custom_ckf[$key]['type'];

                if ( ! $type || ! in_array( $type, array( 'radio', 'select', 'checkbox' ) ) ) continue;

                if ( $type === 'radio' && isset( $list_custom_ckf[$key]['ova_values'] ) && $list_custom_ckf[$key]['ova_values'] ) {
                    foreach ( $list_custom_ckf[$key]['ova_values'] as $k => $v ) {
                        if ( $val === $v && isset( $list_custom_ckf[$key]['ova_prices'][$k] ) ) {
                            $qty = isset( $args_ckf_qty[$key] ) && absint( $args_ckf_qty[$key] ) ? absint( $args_ckf_qty[$key] ) : 1;
                            $price += floatval( $list_custom_ckf[$key]['ova_prices'][$k] ) * $qty;

                            break;
                        }
                    }
                }

                if ( $type === 'select' && isset( $list_custom_ckf[$key]['ova_options_key'] ) && $list_custom_ckf[$key]['ova_options_key'] ) {
                    foreach ( $list_custom_ckf[$key]['ova_options_key'] as $k => $v ) {
                        if ( $val === $v && isset( $list_custom_ckf[$key]['ova_options_price'][$k] ) ) {
                            $qty = isset( $args_ckf_qty[$val] ) && absint( $args_ckf_qty[$val] ) ? absint( $args_ckf_qty[$val] ) : 1;

                            $price += floatval( $list_custom_ckf[$key]['ova_options_price'][$k] ) * $qty;

                            break;
                        }
                    }
                }

                if ( $type === 'checkbox' && ! empty( $val ) && is_array( $val ) ) {
                    $checkbox_key   = isset( $list_custom_ckf[$key]['ova_checkbox_key'] ) ? $list_custom_ckf[$key]['ova_checkbox_key'] : '';
                    $checkbox_price = isset( $list_custom_ckf[$key]['ova_checkbox_price'] ) ? $list_custom_ckf[$key]['ova_checkbox_price'] : '';
                    if ( ! empty( $checkbox_key ) && ! empty( $checkbox_price ) ) {
                        foreach ( $val as $val_cb ) {
                            $key_cb = array_search( $val_cb, $checkbox_key );

                            if ( ! is_bool( $key_cb ) ) {
                                if ( ovabrw_check_array( $checkbox_price, $key_cb ) ) {
                                    $qty = isset( $args_ckf_qty[$val_cb] ) && absint( $args_ckf_qty[$val_cb] ) ? absint( $args_ckf_qty[$val_cb] ) : 1;

                                    $price += floatval( $checkbox_price[$key_cb] ) * $qty;
                                }
                            }
                        }
                    }
                }
            }
        }

        return floatval( $price );
    }
}