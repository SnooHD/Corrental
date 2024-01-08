<?php if ( ! defined( 'ABSPATH' ) ) exit();

if ( ! class_exists( 'Ovabrw_Model' ) ) {
    class Ovabrw_Model {
        public function __construct() {
            // Create new order manually
            add_action( 'admin_init', array( $this, 'ovabrw_create_new_order_manully' ) );

            // Import Locations
            add_action( 'admin_init', array( $this, 'ovabrw_import_locations_manully' ) );
        }

        public function ovabrw_get_address() {
            $data_address = array();

            $first_name         = isset( $_POST['ovabrw_first_name'] )  ? sanitize_text_field( $_POST['ovabrw_first_name'] )    : '';
            $last_name          = isset( $_POST['ovabrw_last_name'] )   ? sanitize_text_field( $_POST['ovabrw_last_name'] )     : '';
            $company            = isset( $_POST['ovabrw_company'] )     ? sanitize_text_field( $_POST['ovabrw_company'] )       : '';
            $email              = isset( $_POST['ovabrw_email'] )       ? sanitize_text_field( $_POST['ovabrw_email'] )         : '';
            $phone              = isset( $_POST['ovabrw_phone'] )       ? sanitize_text_field( $_POST['ovabrw_phone'] )         : '';
            $address_1          = isset( $_POST['ovabrw_address_1'] )   ? sanitize_text_field( $_POST['ovabrw_address_1'] )     : '';
            $address_2          = isset( $_POST['ovabrw_address_2'] )   ? sanitize_text_field( $_POST['ovabrw_address_2'] )     : '';
            $city               = isset( $_POST['ovabrw_city'] )        ? sanitize_text_field( $_POST['ovabrw_city'] )          : '';
            $country_setting    = isset( $_POST['ovabrw_country'] )     ? sanitize_text_field( $_POST['ovabrw_country'] )       : 'US';

            if ( strstr( $country_setting, ':' ) ) {
                $country_setting = explode( ':', $country_setting );
                $country         = current( $country_setting );
                $state           = end( $country_setting );
            } else {
                $country = $country_setting;
                $state   = '*';
            }

            $data_address = array(
                'billing' => array(
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                    'company'    => $company,
                    'email'      => $email,
                    'phone'      => $phone,
                    'address_1'  => $address_1,
                    'address_2'  => $address_2,
                    'city'       => $city,
                    'country'    => $country,
                ),
                'shipping' => array(),
            );

            $ship_to_different_address = isset( $_POST['ship_to_different_address'] ) ? sanitize_text_field( $_POST['ship_to_different_address'] ) : '';

            if ( $ship_to_different_address ) {
                $ship_first_name        = isset( $_POST['ship_ovabrw_first_name'] ) ? sanitize_text_field( $_POST['ship_ovabrw_first_name'] ) : '';
                $ship_last_name         = isset( $_POST['ship_ovabrw_last_name'] ) ? sanitize_text_field( $_POST['ship_ovabrw_last_name'] ) : '';
                $ship_company           = isset( $_POST['ship_ovabrw_company'] ) ? sanitize_text_field( $_POST['ship_ovabrw_company'] ) : '';
                $ship_phone             = isset( $_POST['ship_ovabrw_phone'] ) ? sanitize_text_field( $_POST['ship_ovabrw_phone'] ) : '';
                $ship_address_1         = isset( $_POST['ship_ovabrw_address_1'] ) ? sanitize_text_field( $_POST['ship_ovabrw_address_1'] ) : '';
                $ship_address_2         = isset( $_POST['ship_ovabrw_address_2'] ) ? sanitize_text_field( $_POST['ship_ovabrw_address_2'] ) : '';
                $ship_city              = isset( $_POST['ship_ovabrw_city'] ) ? sanitize_text_field( $_POST['ship_ovabrw_city'] ) : '';
                $ship_country_setting   = isset( $_POST['ship_ovabrw_country'] ) ? sanitize_text_field( $_POST['ship_ovabrw_country'] ) : 'US';

                if ( strstr( $ship_country_setting, ':' ) ) {
                    $ship_country_setting = explode( ':', $ship_country_setting );
                    $ship_country         = current( $ship_country_setting );
                    $ship_state           = end( $ship_country_setting );
                } else {
                    $ship_country = $ship_country_setting;
                    $ship_state   = '*';
                }

                $data_address['shipping'] = array(
                    'first_name' => $ship_first_name,
                    'last_name'  => $ship_last_name,
                    'company'    => $ship_company,
                    'email'      => $email,
                    'phone'      => $ship_phone,
                    'address_1'  => $ship_address_1,
                    'address_2'  => $ship_address_2,
                    'city'       => $ship_city,
                    'country'    => $ship_country,
                );
            }

            return apply_filters( 'ovabrw_ft_get_address', $data_address );
        }

        public function ovabrw_get_tax_default() {
            $tax_rates = array();

            if ( wc_tax_enabled() ) {

                $country_setting = isset( $_POST['ovabrw_country'] ) ? sanitize_text_field( $_POST['ovabrw_country'] ) : 'US';

                if ( strstr( $country_setting, ':' ) ) {
                    $country_setting = explode( ':', $country_setting );
                    $country         = current( $country_setting );
                    $state           = end( $country_setting );
                } else {
                    $country = $country_setting;
                    $state   = '*';
                }

                $tax_rates = WC_Tax::find_rates(
                    array(
                        'country'   => $country,
                        'state'     => $state,
                        'postcode'  => '',
                        'city'      => '',
                        'tax_class' => '',
                    )
                );
            }

            return $tax_rates;
        }

        protected function ovabrw_get_total_taxed_enable( $products ,$price ) {
            if ( empty( $products ) || ! $price ) {
                return 0;
            }

            $tax_rates = $this->ovabrw_get_tax_default();

            if ( empty( $tax_rates ) ) {
                
                $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );

                if ( empty( $tax_rates ) ) {
                    return apply_filters( 'ovabrw_ft_get_total_taxed_enable', $price );
                }
            }

            if ( wc_tax_enabled() ) {

                if ( ! wc_prices_include_tax() ) {

                    $excl_tax = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                    $price   += round( array_sum( $excl_tax ), wc_get_price_decimals() );

                }
            }

            return apply_filters( 'ovabrw_ft_get_total_taxed_enable', $price );
        }

        protected function ovabrw_get_item_total_taxed_enable( $products ,$price ) {
            if ( empty( $products ) || ! $price ) {
                return 0;
            }

            $tax_rates = $this->ovabrw_get_tax_default();

            if ( empty( $tax_rates ) ) {
                
                $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );

                if ( empty( $tax_rates ) ) {
                    return apply_filters( 'ovabrw_ft_get_item_total_taxed_enable', $price );
                }
            }

            if ( wc_tax_enabled() ) {

                if ( wc_prices_include_tax() ) {

                    if ( get_option( 'woocommerce_tax_display_cart' ) != 'incl' ) {
                        $incl_tax = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
                        $price   -= round( array_sum( $incl_tax ), wc_get_price_decimals() );
                    }

                } else {

                    if ( get_option( 'woocommerce_tax_display_cart' ) === 'incl' ) {
                        $excl_tax = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                        $price   += round( array_sum( $excl_tax ), wc_get_price_decimals() );
                    }

                }

            }

            return apply_filters( 'ovabrw_ft_get_item_total_taxed_enable', $price );
        }

        protected function ovabrw_get_product_price_taxed_enable( $products ,$price ) {

            if ( empty( $products ) || ! $price ) {
                return 0;
            }

            $tax_rates = $this->ovabrw_get_tax_default();

            if ( empty( $tax_rates ) ) {

                $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );

                if ( empty( $tax_rates ) ) {
                    return apply_filters( 'ovabrw_ft_get_product_price_taxed_enable', $price );
                }
            }

            if ( wc_tax_enabled() ) {

                if ( wc_prices_include_tax() ) {

                    $incl_tax = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
                    $price   -= round( array_sum( $incl_tax ), wc_get_price_decimals() );

                }
            }

            return apply_filters( 'ovabrw_ft_get_product_price_taxed_enable', $price );
        }

        protected function ovabrw_get_tax_amount_taxed_enable( $products ,$price ) {
            $tax_amount = 0;

            if ( ! $products || ! $price ) {
                return $tax_amount;
            }

            $tax_rates = $this->ovabrw_get_tax_default();

            if ( empty( $tax_rates ) ) {
                
                $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );

                if ( empty( $tax_rates ) ) {
                    return apply_filters( 'ovabrw_ft_get_tax_amount_taxed_enable', $tax_amount );
                }
            }

            if ( wc_tax_enabled() ) {

                if ( wc_prices_include_tax() ) {

                    $incl_tax   = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
                    $tax_amount = round( array_sum( $incl_tax ), wc_get_price_decimals() );

                } else {

                    $excl_tax   = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                    $tax_amount = round( array_sum( $excl_tax ), wc_get_price_decimals() );
                }

            }

            return apply_filters( 'ovabrw_ft_get_tax_amount_taxed_enable', $tax_amount );
        }

        public function ovabrw_add_order_item( $order_id ) {
            // order data
            $data_order = array();

            if ( ! $order_id ) {
                return $data_order;
            }

            $order = wc_get_order( $order_id ); // Get order

            // Get order items
            $has_deposit = $item_has_deposit = false;
            $order_items = $order->get_items(); 
            $order_total = $total_remaining = $total_deposite = $total_insurance = 0;
            $item_total_remaining = $item_total_deposite = $item_total_insurance = $deposit_full_amount = 0;

            // Tax
            $tax_display_cart   = false;
            $remaining_taxes    = 0;
            if ( wc_tax_enabled() && get_option( 'woocommerce_tax_display_cart' ) === 'incl' ) {
                $tax_display_cart = true;
            }

            $date_format = ovabrw_get_date_format();
            $time_format = ovabrw_get_time_format_php();
            $ovabrw_date_time_format = $date_format . ' ' . $time_format;

            // Init $i
            $i = 0;

            // Loop order items
            foreach ( $order_items as $item_id => $product ) {
                $item_total = 0;
                $line_tax   = $data_item = array();

                // Tax
                $tax_rate_id = '';
                $tax_amount  = 0;

                $item = WC_Order_Factory::get_order_item( absint( $item_id ) );

                if ( ! $item ) {
                    continue;
                }

                $product_id = $product['product_id'];
                $products   = wc_get_product( $product_id );

                $ovabrw_pickup_loc       = isset( $_POST['ovabrw_pickup_loc'][$i] )       ? sanitize_text_field( $_POST['ovabrw_pickup_loc'][$i] )     : "";
                $ovabrw_pickoff_loc      = isset( $_POST['ovabrw_pickoff_loc'][$i] )      ? sanitize_text_field( $_POST['ovabrw_pickoff_loc'][$i] )    : "";

                $ovabrw_pickup_date      = isset( $_POST['ovabrw_pickup_date'][$i] )      ? sanitize_text_field( $_POST['ovabrw_pickup_date'][$i] )    : "";
                $ovabrw_pickoff_date     = isset( $_POST['ovabrw_pickoff_date'][$i] )     ? sanitize_text_field( $_POST['ovabrw_pickoff_date'][$i] )   : "";

                $ovabrw_number_vehicle   = isset( $_POST['ovabrw_number_vehicle'][$i] )   ? sanitize_text_field( $_POST['ovabrw_number_vehicle'][$i] ) : 1;
                $ovabrw_total_product    = isset( $_POST['ovabrw_total_product'][$i] )    ? floatval( $_POST['ovabrw_total_product'][$i] )             : 0;

                $ovabrw_id_vehicle       = isset( $_POST['ovabrw_id_vehicle'][$i] )       ? sanitize_text_field( $_POST['ovabrw_id_vehicle'][$i] )     : "";

                $ovabrw_resources       = isset( $_POST['ovabrw_resource_checkboxs'][$product_id] ) ? $_POST['ovabrw_resource_checkboxs'][$product_id]  : [];
                $ovabrw_resources_qty   = isset( $_POST['ovabrw_resource_quantity'][$product_id] ) ? $_POST['ovabrw_resource_quantity'][$product_id]  : [];
                $ovabrw_services        = isset( $_POST['ovabrw_service'][$product_id] ) ? $_POST['ovabrw_service'][$product_id] : [];
                $ovabrw_services_qty    = isset( $_POST['ovabrw_service_quantity'][$product_id] ) ? $_POST['ovabrw_service_quantity'][$product_id] : [];

                // Custom Checkout Fields
                $list_fields = get_option( 'ovabrw_booking_form', array() );

                $ovabrw_rental_type      = isset( $_POST['ovabrw_rental_type'][$i] )      ? sanitize_text_field( $_POST['ovabrw_rental_type'][$i] )    : "";
                $ovabrw_define_1_day     = isset( $_POST['ovabrw_define_1_day'][$i] )     ? sanitize_text_field( $_POST['ovabrw_define_1_day'][$i] )   : "";
                $ovabrw_package          = isset( $_POST['ovabrw_package'][$i] )          ? sanitize_text_field( $_POST['ovabrw_package'][$i] )        : "";
                $ovabrw_total_time       = isset( $_POST['ovabrw_total_time'][$i] )       ? sanitize_text_field( $_POST['ovabrw_total_time'][$i] )     : "";
                $ovabrw_price_detail     = isset( $_POST['ovabrw_price_detail'][$i] )     ? sanitize_text_field( $_POST['ovabrw_price_detail'][$i] )   : 0;

                $item_total_insurance    = isset( $_POST['ovabrw_amount_insurance'][$i] ) ? floatval( $_POST['ovabrw_amount_insurance'][$i] )          : 0;
                $item_total_deposite     = isset( $_POST['ovabrw_amount_deposite'][$i] )  ? floatval( $_POST['ovabrw_amount_deposite'][$i] )           : 0;
                $item_total_remaining    = isset( $_POST['ovabrw_amount_remaining'][$i] ) ? floatval( $_POST['ovabrw_amount_remaining'][$i] )          : 0;

                $ovabrw_waypoints = [];
                $distance = $extra_time = $duration = '';

                if ( $ovabrw_rental_type === 'taxi' ) {
                    $ovabrw_pickup_loc  = isset( $_POST['ovabrw_pickup_location'][$i] ) ? trim( sanitize_text_field( $_POST['ovabrw_pickup_location'][$i] ) ) : '';
                    $ovabrw_pickoff_loc = isset( $_POST['ovabrw_dropoff_location'][$i] ) ? trim( sanitize_text_field( $_POST['ovabrw_dropoff_location'][$i] ) ) : '';

                    $ovabrw_waypoints   = isset( $_POST['ovabrw_waypoint_address'][$product_id] ) ? $_POST['ovabrw_waypoint_address'][$product_id] : [];
                    $duration_map       = isset( $_POST['ovabrw-duration-map'][$i] ) ? sanitize_text_field( $_POST['ovabrw-duration-map'][$i] ) : '';
                    $duration       = isset( $_POST['ovabrw-duration'][$i] ) ? sanitize_text_field( $_POST['ovabrw-duration'][$i] ) : '';
                    $distance       = isset( $_POST['ovabrw-distance'][$i] ) ? sanitize_text_field( $_POST['ovabrw-distance'][$i] ) : '';
                    $extra_time     = isset( $_POST['ovabrw_extra_time'][$i] ) ? sanitize_text_field( $_POST['ovabrw_extra_time'][$i] ) : '';
                    $pickup_time    = isset( $_POST['ovabrw_pickup_time'][$i] ) ? sanitize_text_field( $_POST['ovabrw_pickup_time'][$i] ) : '';
                    $new_input_date = ovabrw_taxi_input_date( $ovabrw_pickup_date, $pickup_time, $duration );

                    $ovabrw_pickup_date     = date( $ovabrw_date_time_format, $new_input_date['pickup_date_new'] );
                    $ovabrw_pickoff_date    = date( $ovabrw_date_time_format, $new_input_date['pickoff_date_new'] );
                }

                if ( ! $ovabrw_pickoff_date ) {
                    $ovabrw_pickoff_date = $ovabrw_pickup_date;
                }

                $total_insurance        += $item_total_insurance;

                $total_deposite         += $this->ovabrw_get_total_taxed_enable( $products, $item_total_deposite );
                $total_remaining        += $this->ovabrw_get_item_total_taxed_enable( $products, $item_total_remaining );

                if ( $item_total_deposite && floatval( $item_total_deposite ) > 0 ) {
                    $has_deposit = $item_has_deposit = true;
                } else {
                    $item_has_deposit = false;
                }

                if ( $item_has_deposit ) {
                    $order_total += $total_deposite;
                    $item_total   = $item_total_deposite;

                    // Deposit full amount
                    $deposit_full_amount = $this->ovabrw_get_item_total_taxed_enable( $products, $ovabrw_total_product );
                } else {
                    $order_total += $this->ovabrw_get_total_taxed_enable( $products, $ovabrw_total_product );
                    $item_total   = $ovabrw_total_product;

                    $deposit_full_amount = 0;
                    $no_has_deposite     = $ovabrw_total_product;
                }
                
                if ( isset( $no_has_deposite ) && $has_deposit ) {
                    $total_deposite += $no_has_deposite;
                }

                // Get remaining taxes
                if ( wc_tax_enabled() ) {

                    $remaining_taxes += $this->ovabrw_get_tax_amount_taxed_enable( $products, $item_total_remaining );

                    // item data
                    $item_total_deposite  = $this->ovabrw_get_item_total_taxed_enable( $products, $item_total_deposite );
                    $item_total_remaining = $this->ovabrw_get_item_total_taxed_enable( $products, $item_total_remaining );

                    $tax_rates = $this->ovabrw_get_tax_default();

                    if ( empty( $tax_rates ) ) {
                        $tax_rates = WC_Tax::get_rates( $products->get_tax_class() );
                    }

                    if ( ! empty( $tax_rates ) ) {
                        $tax_item_id = key($tax_rates);

                        if ( $tax_item_id ) {

                            $tax_amount_item        = $this->ovabrw_get_tax_amount_taxed_enable( $products, $item_total );
                            $line_tax[$tax_item_id] = $tax_amount_item;
                            $tax_rate_id            = $tax_item_id;
                            $tax_amount            += $tax_amount_item;
                            $item_total             = $this->ovabrw_get_product_price_taxed_enable( $products, $item_total );
                        }
                    }
                    
                }

                $defined_one_day = defined_one_day( $product_id );

                //add real date
                $ovabrw_pickup_date_real    = $ovabrw_pickup_date;
                $ovabrw_dropoff_date_real   = $ovabrw_pickoff_date;
                $ovabrw_period_label        = '';
                
                if ( $ovabrw_rental_type === 'transportation' ) {
                    $ovabrw_pickup_date_timestamp   = strtotime( $ovabrw_pickup_date );
                    $ovabrw_pickup_date_real        = date( $date_format, $ovabrw_pickup_date_timestamp ) . ' 00:00';
                    $ovabrw_dropoff_date_real       = date( $date_format, $ovabrw_pickup_date_timestamp ) . ' 24:00';
                } elseif ( $ovabrw_rental_type === 'period_time' ) {
                    $rental_info_period     = get_rental_info_period( $product_id, strtotime( $ovabrw_pickup_date ), $ovabrw_rental_type, $ovabrw_package );
                    $ovabrw_pickup_date     = date( $ovabrw_date_time_format, $rental_info_period['start_time'] );
                    $ovabrw_pickoff_date    = date( $ovabrw_date_time_format, $rental_info_period['end_time'] );
                    $ovabrw_period_label    = $rental_info_period['period_label'];
                }
               
                if ( $defined_one_day == 'hotel' ) {
                    $ovabrw_pickup_date_timestamp   = strtotime( $ovabrw_pickup_date );
                    $ovabrw_pickoff_date_timestamp  = strtotime( $ovabrw_pickoff_date );

                    $ovabrw_pickup_date_real        = date( $date_format, $ovabrw_pickup_date_timestamp ) . ' ' .apply_filters( 'brw_real_pickup_time_hotel', '14:00' );
                    $ovabrw_dropoff_date_real       = date( $date_format, $ovabrw_pickoff_date_timestamp ) . ' ' .apply_filters( 'brw_real_dropoff_time_hotel', '11:00' );
                } elseif ( $defined_one_day == 'day' ) {
                    $ovabrw_pickup_date_timestamp   = strtotime( $ovabrw_pickup_date );
                    $ovabrw_pickoff_date_timestamp  = strtotime( $ovabrw_pickoff_date );

                    $ovabrw_pickup_date_real        = date( $date_format, $ovabrw_pickup_date_timestamp ) . ' 00:00';
                    $ovabrw_dropoff_date_real       = date( $date_format, $ovabrw_pickoff_date_timestamp ) . ' 24:00';
                } elseif ( $defined_one_day == 'hour' ) {
                    //fixed later
                    $ovabrw_pickup_date_real    = $ovabrw_pickup_date;
                    $ovabrw_dropoff_date_real   = $ovabrw_pickoff_date;
                }

                if ( $ovabrw_rental_type === 'hour' || $ovabrw_rental_type === 'mixed' || $ovabrw_rental_type === 'period_time' || $ovabrw_rental_type === 'taxi' ) {
                    $ovabrw_pickup_date_real    = $ovabrw_pickup_date;
                    $ovabrw_dropoff_date_real   = $ovabrw_pickoff_date;
                }

                $data_item[ 'ovabrw_pickup_loc' ] = $ovabrw_pickup_loc;

                if ( ! empty( $ovabrw_waypoints ) && is_array( $ovabrw_waypoints ) ) {
                    foreach ( $ovabrw_waypoints as $k => $val ) {
                        $data_item[sprintf( esc_html__( 'Waypoint %s', 'ova_brw' ), $k + 1 )] = $val;
                    }
                }

                $data_item[ 'ovabrw_pickoff_loc' ]          = $ovabrw_pickoff_loc;
                $data_item[ 'ovabrw_pickup_date' ]          = $ovabrw_pickup_date;
                $data_item[ 'ovabrw_pickoff_date' ]         = $ovabrw_pickoff_date;
                $data_item[ 'ovabrw_number_vehicle' ]       = $ovabrw_number_vehicle;
                $data_item[ 'ovabrw_pickup_date_real' ]     = $ovabrw_pickup_date_real;
                $data_item[ 'ovabrw_pickoff_date_real' ]    = $ovabrw_dropoff_date_real;
                $data_item[ 'rental_type' ]                 = $ovabrw_rental_type;
                $data_item[ 'period_label' ]                = $ovabrw_period_label;
                $data_item[ 'ovabrw_total_days' ]           = $ovabrw_total_time;
                $data_item[ 'ovabrw_price_detail' ]         = $ovabrw_price_detail;

                if ( $item_total_insurance ) {
                    $data_item[ 'ovabrw_amount_insurance_product' ] = $item_total_insurance;
                }

                if ( $item_has_deposit ) {
                    $data_item[ 'ovabrw_deposit_amount_product' ]   = $item_total_deposite;
                    $data_item[ 'ovabrw_remaining_amount_product' ] = $item_total_remaining;
                }

                if ( $deposit_full_amount ) {
                    $data_item['ovabrw_deposit_full_amount'] = $deposit_full_amount;
                }

                if ( $ovabrw_id_vehicle ) {
                    $data_item[ 'id_vehicle' ] = $ovabrw_id_vehicle;   
                }
                
                if ( $ovabrw_rental_type == 'day' ) {
                    $data_item[ 'define_day' ] = $defined_one_day;
                }

                if ( $distance ) {
                    $data_item['ovabrw_distance'] = ovarw_taxi_get_distance_text( $distance, $product_id );
                }

                if ( $extra_time ) {
                    $data_item['ovabrw_extra_time'] = sprintf( esc_html__( '%s hour(s)', 'ova-brw' ), $extra_time );
                }

                if ( $duration ) {
                    $data_item['ovabrw_duration'] = ovarw_taxi_get_duration_text( $duration );
                }

                // Add resource
                if ( ! empty( $ovabrw_resources ) && is_array( $ovabrw_resources ) ) {
                    $data_resources = $name_resources = [];

                    $resource_ids    = get_post_meta( $product_id, 'ovabrw_resource_id', true );
                    $resource_names  = get_post_meta( $product_id, 'ovabrw_resource_name', true );

                    foreach ( $ovabrw_resources as $k => $rs_id ) {
                        $rs_k = array_search( $rs_id, $resource_ids );

                        if ( ! is_bool( $rs_k ) ) {
                            if ( ovabrw_check_array( $resource_names, $rs_k ) ) {
                                $res_name = $resource_names[$rs_k];

                                $data_resources[$rs_id] = $res_name;

                                if ( isset( $ovabrw_resources_qty[$rs_id] ) && absint( $ovabrw_resources_qty[$rs_id] ) ) {
                                    $res_name .= ' (x'.absint( $ovabrw_resources_qty[$rs_id] ).')';
                                }

                                array_push( $name_resources, $res_name );
                            }
                        }
                    }

                    $data_item['ovabrw_resources']      = $data_resources;
                    $data_item['ovabrw_resources_qty']  = $ovabrw_resources_qty;

                    if ( count( $name_resources ) == 1 ) {
                        $data_item[ esc_html__( 'Resource', 'ova-brw' ) ] = join( ', ', $name_resources );
                    } else {
                        $data_item[ esc_html__( 'Resources', 'ova-brw' ) ] = join( ', ', $name_resources );
                    } 
                }

                if ( ! empty( $ovabrw_services ) ) {
                    $data_item['ovabrw_services'] = $ovabrw_services;
                    $services_qty = [];
                    
                    $services_id      = get_post_meta( $product_id, 'ovabrw_service_id', true ); 
                    $services_name    = get_post_meta( $product_id, 'ovabrw_service_name', true );
                    $services_label   = get_post_meta( $product_id, 'ovabrw_label_service', true ); 

                    foreach ( $ovabrw_services as $val_ser ) {
                        if ( isset( $ovabrw_services_qty[$val_ser] ) && absint( $ovabrw_services_qty[$val_ser] ) ) {
                            $services_qty[$val_ser] = $ovabrw_services_qty[$val_ser];
                        }

                        if ( ! empty( $services_id ) && is_array( $services_id ) ) {
                            foreach ( $services_id as $key => $value ) {
                                if ( is_array( $value ) && ! empty( $value ) ) {
                                    foreach ( $value as $k => $val ) {
                                        if ( $val_ser == $val && ! empty( $val ) ) {
                                            $service_name = $services_name[$key][$k];

                                            if ( isset( $services_qty[$val_ser] ) && absint( $services_qty[$val_ser] ) ) {
                                                $service_name .= ' (x'.absint( $services_qty[$val_ser] ).')';
                                            }

                                            $service_label = $services_label[$key];
                                            $data_item[$service_label] = $service_name;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $data_item['ovabrw_services_qty'] = $services_qty;
                }

                // Custom Checkout Fields
                $list_fields    = ovabrw_get_list_field_checkout( $product_id );;
                $custom_ckf     = array();
                $custom_ckf_qty = [];

                if ( ! empty( $list_fields ) && is_array( $list_fields ) ) {
                    foreach ( $list_fields as $key => $field ) {
                        if ( $field['type'] === 'file' ) {
                            $data_file = isset( $_FILES[$key] ) ? $_FILES[$key] : '';

                            if ( $data_file ) {
                                $files = array();

                                if ( isset( $data_file['name'][$product_id] ) ) {
                                    $files['name'] = $data_file['name'][$product_id];
                                }
                                if ( isset( $data_file['full_path'][$product_id] ) ) {
                                    $files['full_path'] = $data_file['full_path'][$product_id];
                                }
                                if ( isset( $data_file['type'][$product_id] ) ) {
                                    $files['type'] = $data_file['type'][$product_id];
                                }
                                if ( isset( $data_file['tmp_name'][$product_id] ) ) {
                                    $files['tmp_name'] = $data_file['tmp_name'][$product_id];
                                }
                                if ( isset( $data_file['error'][$product_id] ) ) {
                                    $files['error'] = $data_file['error'][$product_id];
                                }
                                if ( isset( $data_file['size'][$product_id] ) ) {
                                    $files['size'] = $data_file['size'][$product_id];
                                }

                                if ( isset( $files['size'] ) && $files['size'] ) {
                                    $mb = absint( $files['size'] ) / 1048576;

                                    if ( $mb > $field['max_file_size'] ) {
                                        continue;
                                    }
                                }

                                $overrides = [
                                    'test_form' => false,
                                    'mimes'     => apply_filters( 'ovabrw_ft_file_mimes', [
                                        'jpg'   => 'image/jpeg',
                                        'jpeg'  => 'image/pjpeg',
                                        'png'   => 'image/png',
                                        'pdf'   => 'application/pdf',
                                        'doc'   => 'application/msword',
                                    ]),
                                ];

                                require_once( ABSPATH . 'wp-admin/includes/admin.php' );

                                $upload = wp_handle_upload( $files, $overrides );

                                if ( isset( $upload['error'] ) ) {
                                    continue;
                                }

                                $data_item[$key] = '<a href="'.esc_url( $upload['url'] ).'" title="'.esc_attr( basename( $upload['file'] ) ).'" target="_blank">'.esc_attr( basename( $upload['file'] ) ).'</a>';
                            }
                        } else {
                            $value = isset( $_POST[$key][$product_id] ) ? $_POST[$key][$product_id] : '';

                            if ( ! empty( $value ) && 'on' === $field['enabled'] ) {
                                if ( 'select' === $field['type'] ) {
                                    $custom_ckf[$key] = sanitize_text_field( $value );

                                    if ( isset( $_POST[$key.'_qty'] ) && ! empty( $_POST[$key.'_qty'] ) ) {
                                        if ( isset( $_POST[$key.'_qty'][$product_id][$value] ) && absint( $_POST[$key.'_qty'][$product_id][$value] ) ) {
                                            $custom_ckf_qty[$value] = absint( $_POST[$key.'_qty'][$product_id][$value] );
                                        }
                                    }

                                    $options_key = $options_text = array();
                                    if ( ovabrw_check_array( $field, 'ova_options_key' ) ) {
                                        $options_key = $field['ova_options_key'];
                                    }

                                    if ( ovabrw_check_array( $field, 'ova_options_text' ) ) {
                                        $options_text = $field['ova_options_text'];
                                    }

                                    $key_op = array_search( $value, $options_key );

                                    if ( ! is_bool( $key_op ) ) {
                                        if ( ovabrw_check_array( $options_text, $key_op ) ) {
                                            if ( isset( $custom_ckf_qty[$value] ) && absint( $custom_ckf_qty[$value] ) ) {
                                                $value = $options_text[$key_op] . ' (x'.absint( $custom_ckf_qty[$value] ).')';
                                            } else {
                                                $value = $options_text[$key_op];
                                            }
                                        }
                                    }
                                }

                                if ( 'checkbox' === $field['type'] ) {
                                    $checkbox_val = $checkbox_key = $checkbox_text = array();

                                    if ( ! empty( $value ) && is_array( $value ) ) {
                                        $custom_ckf[$key] = $value;

                                        if ( isset( $_POST[$key.'_qty'][$product_id] ) && ! empty( $_POST[$key.'_qty'][$product_id] ) && is_array( $_POST[$key.'_qty'][$product_id] ) ) {
                                            $custom_ckf_qty = $custom_ckf_qty + $_POST[$key.'_qty'][$product_id];
                                        }

                                        if ( ovabrw_check_array( $field, 'ova_checkbox_key' ) ) {
                                            $checkbox_key = $field['ova_checkbox_key'];
                                        }

                                        if ( ovabrw_check_array( $field, 'ova_checkbox_text' ) ) {
                                            $checkbox_text = $field['ova_checkbox_text'];
                                        }

                                        foreach ( $value as $val_cb ) {
                                            $key_cb = array_search( $val_cb, $checkbox_key );
                                            $qty    = isset( $custom_ckf_qty[$val_cb] ) && absint( $custom_ckf_qty[$val_cb] ) ? absint( $custom_ckf_qty[$val_cb] ) : '';

                                            if ( ! is_bool( $key_cb ) ) {
                                                if ( ovabrw_check_array( $checkbox_text, $key_cb ) ) {

                                                    if ( $qty ) {
                                                        array_push( $checkbox_val , $checkbox_text[$key_cb] . ' (x'.$qty.')' );
                                                    } else {
                                                        array_push( $checkbox_val , $checkbox_text[$key_cb] );
                                                    }
                                                }
                                            }
                                        }

                                        if ( ! empty( $checkbox_val ) && is_array( $checkbox_val ) ) {
                                            $value = join( ", ", $checkbox_val );
                                        }
                                    }
                                }

                                if ( 'radio' === $field['type'] ) {
                                    $custom_ckf[$key] = sanitize_text_field( $value );

                                    if ( $custom_ckf[$key] && isset( $_POST[$key.'_qty'][$product_id] ) && ! empty( $_POST[$key.'_qty'][$product_id] ) ) {
                                        $qty = isset( $_POST[$key.'_qty'][$product_id][$custom_ckf[$key]] ) && absint( $_POST[$key.'_qty'][$product_id][$custom_ckf[$key]] ) ? absint( $_POST[$key.'_qty'][$product_id][$custom_ckf[$key]] ) : 1;
                                        $custom_ckf_qty[$key] = $qty;

                                        $value .= ' (x'.$qty.')';
                                    }
                                }

                                $data_item[$key] = $value;
                            }
                        }
                    }
                }

                if ( ! empty( $custom_ckf ) ) {
                    $data_item[ 'ovabrw_custom_ckf' ] = $custom_ckf;
                }

                if ( ! empty( $custom_ckf_qty ) ) {
                    $data_item[ 'ovabrw_custom_ckf_qty' ] = $custom_ckf_qty;
                }

                // Add item
                foreach ( $data_item as $meta_key => $meta_value ) {
                    wc_add_order_item_meta( $item_id, $meta_key, $meta_value );
                }

                // Update item meta
                $item->set_props(
                    array(
                        'total'     => $item_total,
                        'subtotal'  => $item_total,
                        'taxes'     => array(
                            'total'    => $line_tax,
                            'subtotal' => $line_tax,
                        ),
                    )
                );

                $item->save();
                // End update item meta

                $i++;
            }

            $data_order = [
                'order_total'       => $order_total,
                'total_remaining'   => $total_remaining,
                'total_deposit'     => $total_deposite,
                'total_insurance'   => $total_insurance,
                'has_deposit'       => $has_deposit,
                'tax_display_cart'  => $tax_display_cart,
                'remaining_taxes'   => $remaining_taxes,
                'tax_rate_id'       => $tax_rate_id,
                'tax_amount'        => $tax_amount,
            ];

            return apply_filters( 'ovabrw_ft_add_order_item', $data_order, $order_id );
        }

        public function ovabrw_create_new_order_manully() {
            if ( isset( $_POST['ovabrw_create_order'] ) && $_POST['ovabrw_create_order'] === 'create_order' ) {

                // Check Permission
                if( !current_user_can( apply_filters( 'ovabrw_create_order' ,'publish_posts' ) ) ){

                    echo    '<div class="notice notice-error is-dismissible">
                                <h2>'.esc_html__( 'You don\'t have permission to create order', 'ova-brw' ).'</h2>
                            </div>';
                    return;
                }

                $data_order_keys = array();

                $order      = wc_create_order(); // Create new order
                $order_id   = $order->get_id(); // Get order id

                // Add product -> Get Total
                // Get array product ids
                $products = isset( $_POST['ovabrw_name_product'] ) ? $_POST['ovabrw_name_product'] : [];
                $currency = isset( $_POST['currency'] ) && $_POST['currency'] ? $_POST['currency'] : '';

                if ( $currency ) $order->set_currency( $currency );

                // Check order deposit
                $has_deposit = false;

                if ( ! empty( $products ) ) {

                    foreach( $products as $key => $product ) {

                        $item_total = 0;

                        $product    = trim( sanitize_text_field( $product ) );
                        $id_product = substr( $product, strpos( $product, '(#' ) );
                        $id_product = str_replace('(#', '', $id_product);
                        $id_product = str_replace(')', '', $id_product);

                        $ovabrw_number_vehicle  = isset( $_POST['ovabrw_number_vehicle'][$key] )  ? floatval( $_POST['ovabrw_number_vehicle'][$key] )  : 1;
                        $item_total_deposite    = isset( $_POST['ovabrw_amount_deposite'][$key] ) ? floatval( $_POST['ovabrw_amount_deposite'][$key] ) : 0;

                        $item_total             = isset( $_POST['ovabrw_total_product'][$key] )   ? floatval( $_POST['ovabrw_total_product'][$key] )   : 0;

                        if ( $item_total_deposite && floatval( $item_total_deposite ) > 0 ) {
                            $item_total = $item_total_deposite;
                        }

                        if ( wc_tax_enabled() ) {
                            $obj_products   = wc_get_product( $id_product );
                            $item_total     = $this->ovabrw_get_product_price_taxed_enable( $obj_products, $item_total );
                        }

                        $order->add_product( wc_get_product( $id_product ), $ovabrw_number_vehicle, array( 'total' => $item_total ) );
                    }
                }

                $order_data  = $this->ovabrw_add_order_item( $order_id ); // Get order data

                // Get data total
                $order_total = $order_data['order_total'];
                
                if ( $order_data['has_deposit'] ) {

                    $data_order_keys['_ova_remaining_amount'] = $order_data['total_remaining'];
                    $data_order_keys['_ova_deposit_amount']   = $order_data['total_deposit'];
                    $data_order_keys['_ova_has_deposit']      = 1;
                    $data_order_keys['_ova_remaining_taxes']  = 0;
                }

                if ( $order_data['tax_display_cart'] ) {
                    $data_order_keys['_ova_tax_display_cart'] = 1;
                }

                if ( $order_data['remaining_taxes'] ) {
                    $data_order_keys['_ova_remaining_taxes']  = $order_data['remaining_taxes'];
                }

                $data_order_keys['_ova_insurance_amount'] = $order_data['total_insurance'];
                $data_order_keys['_ova_original_total']   = $order_total;

                foreach ( $data_order_keys as $key => $update ) {
                    $order->update_meta_data( $key , $update );
                }

                $data_address = $this->ovabrw_get_address();

                $order->set_total( $order_total );
                $order->set_address( $data_address['billing'], 'billing' );

                $ship_to_different_address = isset( $_POST['ship_to_different_address'] ) ? sanitize_text_field( $_POST['ship_to_different_address'] ) : '';
                if ( $ship_to_different_address ) {
                    $order->set_address( $data_address['shipping'], 'shipping' );
                } else {
                    $order->set_address( $data_address['billing'], 'shipping' );
                }

                $status_order = isset( $_POST['status_order'] ) ? sanitize_text_field( $_POST['status_order'] ) : '';

                // Tax
                if ( wc_tax_enabled() && $order_data['tax_rate_id'] && $order_data['tax_amount'] ) {

                    $tax_rate_id = $order_data['tax_rate_id'];
                    $tax_amount  = $order_data['tax_amount'];

                    $item = new WC_Order_Item_Tax();
                    $item->set_props(
                        array(
                            'rate_id'            => $tax_rate_id,
                            'tax_total'          => $tax_amount,
                            'shipping_tax_total' => 0,
                            'rate_code'          => WC_Tax::get_rate_code( $tax_rate_id ),
                            'label'              => WC_Tax::get_rate_label( $tax_rate_id ),
                            'compound'           => WC_Tax::is_compound( $tax_rate_id ),
                            'rate_percent'       => WC_Tax::get_rate_percent_value( $tax_rate_id ),
                        )
                    );

                    // Add item to order and save.
                    $order->add_item( $item );
                }

                if ( $order_data['tax_amount'] ) {
                    $order->set_cart_tax( $order_data['tax_amount'] );
                }

                if ( $status_order ) {
                    $order->update_status( $status_order );
                }

                $order->save();
            }
        }

        public function ovabrw_import_locations_manully() {

            $action = isset( $_POST['action_import'] ) && $_POST['action_import'] ? sanitize_text_field( $_POST['action_import'] ) : '';

            if ( $action === 'import_locations' ) {
                $product_id = isset( $_POST['product_id'] ) && $_POST['product_id'] ? absint( $_POST['product_id'] ) : '';

                // Check Permission
                if( !current_user_can( apply_filters( 'ovabrw_import_locations' ,'publish_posts' ) ) ){

                    echo    '<div class="notice notice-error is-dismissible" style="margin-left:200px;">
                                <h2>'.esc_html__( 'You don\'t have permission to import locations', 'ova-brw' ).'</h2>
                            </div>';
                    return;
                }

                if ( !$product_id ) {
                    $_POST['error'] = esc_html__( 'Please select a product', 'ova-brw' );
                    add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_error' ) );
                    return;
                }

                $file = isset( $_FILES['location_file'] ) && $_FILES['location_file'] ? $_FILES['location_file'] : '';
                    $upload_overrides = array(
                    'test_form' => false,
                    'mimes'     => array(
                        'csv' => 'text/csv',
                        'txt' => 'text/plain',
                    ),
                );

                try {
                    $upload = wp_handle_upload( $file, $upload_overrides );
                } catch ( Exception $e ) {
                    $_POST['error'] = $e->getMessage();
                    add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_error' ) );
                    return;
                }
                
                if ( isset( $upload['error'] ) && $upload['error'] ) {
                    $_POST['error'] = $upload['error'];
                    add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_error' ) );
                    return;
                } else {
                    // Construct the object array.
                    $object = array(
                        'post_title'     => basename( $upload['file'] ),
                        'post_content'   => $upload['url'],
                        'post_mime_type' => $upload['type'],
                        'guid'           => $upload['url'],
                        'context'        => 'import',
                        'post_status'    => 'private',
                    );

                    // Save the data.
                    $id = wp_insert_attachment( $object, $upload['file'] );
                    wp_schedule_single_event( time() + DAY_IN_SECONDS, 'importer_scheduled_cleanup', array( $id ) );

                    $file_url = $upload['file'];
                    $handle = fopen($file_url,"r");

                    while (!feof($handle) ) {
                        $data[] = fgetcsv($handle);
                    }
                    fclose($handle);

                    if ( !empty( $data ) && is_array( $data ) ) {
                        if ( count( $data ) === 1 ) {
                            $_POST['error'] = esc_html__( 'File empty!', 'ova-brw' );
                            add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_error' ) );
                            return;
                        } else {
                            array_shift($data);

                            $pickup_location    = array();
                            $dropoff_location   = array();
                            $location_time      = array();
                            $price_location     = array();

                            foreach( $data as $items ) {
                                $pickup_loc = isset( $items[0] ) && $items[0] ? $items[0] : '';
                                if ( !$pickup_loc ) {
                                    continue;
                                }
                                
                                $dropoff_loc = isset( $items[1] ) && $items[1] ? $items[1] : '';
                                if ( !$dropoff_loc ) {
                                    continue;
                                }
                                
                                $time_loc = isset( $items[2] ) && $items[2] ? floatval( $items[2] ) : '';
                                if ( !$time_loc ) {
                                    continue;
                                }

                                $price_loc = isset( $items[3] ) && $items[3] ? floatval( $items[3] ) : '';
                                if ( !$price_loc ) {
                                    continue;
                                }

                                array_push( $pickup_location , $pickup_loc );
                                array_push( $dropoff_location , $dropoff_loc );
                                array_push( $location_time , $time_loc );
                                array_push( $price_location , $price_loc );
                            }

                            if ( !empty( $pickup_location ) && !empty( $dropoff_location ) && !empty( $location_time ) && !empty( $price_location ) ) {
                                $update_pickup_location     = array();
                                $update_dropoff_location    = array();
                                $update_location_time       = array();
                                $update_price_location      = array();

                                $current_pickup_location    = get_post_meta( $product_id, 'ovabrw_pickup_location', true );
                                if ( !empty( $current_pickup_location ) && is_array( $current_pickup_location ) ) {
                                    $update_pickup_location = array_merge( $current_pickup_location, $pickup_location );
                                } else {
                                    $update_pickup_location = $pickup_location;
                                }

                                $current_dropoff_location = get_post_meta( $product_id, 'ovabrw_dropoff_location', true );
                                if ( !empty( $current_dropoff_location ) && is_array( $current_dropoff_location ) ) {
                                    $update_dropoff_location = array_merge( $current_dropoff_location, $dropoff_location );
                                } else {
                                    $update_dropoff_location = $dropoff_location;
                                }
                                
                                $current_location_time = get_post_meta( $product_id, 'ovabrw_location_time', true );
                                if ( !empty( $current_location_time ) && is_array( $current_location_time ) ) {
                                    $update_location_time = array_merge( $current_location_time, $location_time );
                                } else {
                                    $update_location_time = $location_time;
                                }

                                $current_price_location = get_post_meta( $product_id, 'ovabrw_price_location', true );
                                if ( !empty( $current_price_location ) && is_array( $current_price_location ) ) {
                                    $update_price_location = array_merge( $current_price_location, $price_location );
                                } else {
                                    $update_price_location = $price_location;
                                }

                                update_post_meta( $product_id, 'ovabrw_pickup_location', $update_pickup_location );
                                update_post_meta( $product_id, 'ovabrw_dropoff_location', $update_dropoff_location );
                                update_post_meta( $product_id, 'ovabrw_location_time', $update_location_time );
                                update_post_meta( $product_id, 'ovabrw_price_location', $update_price_location );

                                $_POST['success'] = esc_html__( 'Imported locations success!', 'ova-brw' );
                                add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_success' ) );
                                return;
                            } else {
                                $_POST['error'] = esc_html__( 'Imported locations fails!', 'ova-brw' );
                                add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_error' ) );
                                return;
                            }
                        }
                    } else {
                        $_POST['error'] = esc_html__( 'File empty!', 'ova-brw' );
                        add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_error' ) );
                        return;
                    }
                }
            }

            if ( $action === 'import_setup_locations' ) {

                $product_id = isset( $_POST['product_id'] ) && $_POST['product_id'] ? absint( $_POST['product_id'] ) : '';
                
                // Check Permission
                if( !current_user_can( apply_filters( 'ovabrw_import_locations' ,'publish_posts' ) ) ){

                    echo    '<div class="notice notice-error is-dismissible" style="margin-left:200px;">
                                <h2>'.esc_html__( 'You don\'t have permission to import locations', 'ova-brw' ).'</h2>
                            </div>';
                    return;
                }

                if ( !$product_id ) {
                    $_POST['error'] = esc_html__( 'Please select a product', 'ova-brw' );
                    add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_error' ) );
                    return;
                }

                $file = isset( $_FILES['location_file'] ) && $_FILES['location_file'] ? $_FILES['location_file'] : '';
                    $upload_overrides = array(
                    'test_form' => false,
                    'mimes'     => array(
                        'csv' => 'text/csv',
                        'txt' => 'text/plain',
                    ),
                );

                try {
                    $upload = wp_handle_upload( $file, $upload_overrides );
                } catch ( Exception $e ) {
                    $_POST['error'] = $e->getMessage();
                    add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_error' ) );
                    return;
                }
                
                if ( isset( $upload['error'] ) && $upload['error'] ) {
                    $_POST['error'] = $upload['error'];
                    add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_error' ) );
                    return;
                } else {
                    // Construct the object array.
                    $object = array(
                        'post_title'     => basename( $upload['file'] ),
                        'post_content'   => $upload['url'],
                        'post_mime_type' => $upload['type'],
                        'guid'           => $upload['url'],
                        'context'        => 'import',
                        'post_status'    => 'private',
                    );

                    // Save the data.
                    $id = wp_insert_attachment( $object, $upload['file'] );
                    wp_schedule_single_event( time() + DAY_IN_SECONDS, 'importer_scheduled_cleanup', array( $id ) );

                    $file_url = $upload['file'];
                    $handle = fopen($file_url,"r");

                    while (!feof($handle) ) {
                        $data[] = fgetcsv($handle);
                    }
                    fclose($handle);

                    if ( !empty( $data ) && is_array( $data ) ) {
                        if ( count( $data ) === 1 ) {
                            $_POST['error'] = esc_html__( 'File empty!', 'ova-brw' );
                            add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_error' ) );
                            return;
                        } else {
                            array_shift($data);

                            $st_pickup_loc  = array();
                            $st_dropoff_loc = array();

                            foreach( $data as $items ) {
                                $pickup_loc = isset( $items[0] ) && $items[0] ? $items[0] : '';
                                if ( !$pickup_loc ) {
                                    continue;
                                }
                                
                                $dropoff_loc = isset( $items[1] ) && $items[1] ? $items[1] : '';
                                if ( !$dropoff_loc ) {
                                    continue;
                                }

                                array_push( $st_pickup_loc , $pickup_loc );
                                array_push( $st_dropoff_loc , $dropoff_loc );
                            }

                            if ( !empty( $st_pickup_loc ) && !empty( $st_dropoff_loc ) ) {
                                $update_pickup_location     = array();
                                $update_dropoff_location    = array();

                                $current_pickup_location    = get_post_meta( $product_id, 'ovabrw_st_pickup_loc', true );
                                if ( !empty( $current_pickup_location ) && is_array( $current_pickup_location ) ) {
                                    $update_pickup_location = array_merge( $current_pickup_location, $st_pickup_loc );
                                } else {
                                    $update_pickup_location = $st_pickup_loc;
                                }

                                $current_dropoff_location = get_post_meta( $product_id, 'ovabrw_st_dropoff_loc', true );
                                if ( !empty( $current_dropoff_location ) && is_array( $current_dropoff_location ) ) {
                                    $update_dropoff_location = array_merge( $current_dropoff_location, $st_dropoff_loc );
                                } else {
                                    $update_dropoff_location = $st_dropoff_loc;
                                }
                                
                                update_post_meta( $product_id, 'ovabrw_st_pickup_loc', $update_pickup_location );
                                update_post_meta( $product_id, 'ovabrw_st_dropoff_loc', $update_dropoff_location );
                                
                                $_POST['success'] = esc_html__( 'Imported locations success!', 'ova-brw' );
                                add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_success' ) );
                                return;
                            } else {
                                $_POST['error'] = esc_html__( 'Imported locations fails!', 'ova-brw' );
                                add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_error' ) );
                                return;
                            }
                        }
                    } else {
                        $_POST['error'] = esc_html__( 'File empty!', 'ova-brw' );
                        add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_error' ) );
                        return;
                    }
                }
            }

            if ( $action === 'remove_locations' ) {

                // Check Permission
                if( !current_user_can( apply_filters( 'ovabrw_import_locations' ,'publish_posts' ) ) ){

                    echo    '<div class="notice notice-error is-dismissible" style="margin-left:200px;">
                                <h2>'.esc_html__( 'You don\'t have permission to remove locations', 'ova-brw' ).'</h2>
                            </div>';
                    return;
                }

                $product_id = isset( $_POST['product_id'] ) && $_POST['product_id'] ? absint( $_POST['product_id'] ) : '';
                if ( !$product_id ) {
                    $_POST['error'] = esc_html__( 'Please select a product', 'ova-brw' );
                    add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_error' ) );
                    return;
                }

                $product_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

                if ( $product_type != 'transportation' ) {
                    update_post_meta( $product_id, 'ovabrw_st_pickup_loc', '' );
                    update_post_meta( $product_id, 'ovabrw_st_dropoff_loc', '' );
                } else {
                    update_post_meta( $product_id, 'ovabrw_pickup_location', '' );
                    update_post_meta( $product_id, 'ovabrw_dropoff_location', '' );
                    update_post_meta( $product_id, 'ovabrw_location_time', '' );
                    update_post_meta( $product_id, 'ovabrw_price_location', '' );
                }

                $_POST['success'] = esc_html__( 'Removed locations success!', 'ova-brw' );
                add_action( 'admin_notices', array( $this, 'ovabrw_show_admin_notice_success' ) );
                return;
            }
        }

        public function ovabrw_show_admin_notice_success() {
            if ( isset( $_POST['success'] ) && $_POST['success'] ) {
                ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e( $_POST['success'] ); ?></p>
                </div>
                <?php
            }
        }

        public function ovabrw_show_admin_notice_error() {
            if ( isset( $_POST['error'] ) && $_POST['error'] ) {
                ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php esc_html_e( $_POST['error'] ); ?></p>
                </div>
                <?php
            }
        }
    }
}

new Ovabrw_Model();