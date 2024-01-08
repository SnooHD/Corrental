<?php defined( 'ABSPATH' ) || exit();

if ( ! function_exists( 'ovabrw_woo_new_product_tab' ) ) {
    function ovabrw_woo_new_product_tab( $tabs ) {
        // Add Request Booking Tab
        $product_id = get_the_id();
        $product    = wc_get_product( $product_id );

        if ( $product ) {
            // Check product template
            $flag = true;
            $product_template = get_option( 'ova_brw_template_elementor_template', 'default' );

            if ( ovabrw_global_typography() && $product_template === 'modern' ) {
                $template_by_category = ovabrw_get_product_template( $product_id );

                if ( $template_by_category && $template_by_category !== 'modern' ) {
                    $flag = true;
                } else {
                    $flag = false;
                }
            }

            if ( ovabrw_get_setting( get_option( 'ova_brw_template_show_request_booking', 'yes' ) ) === 'yes' && $product->get_type() == 'ovabrw_car_rental' && apply_filters( 'ovabrw_ft_show_request_booking_in_product_tabs', $flag ) ) {
                $tabs['ovabrw_reqest_booking'] = array(
                    'title'     => esc_html__( 'Request for booking', 'ova-brw' ),
                    'priority'  => (int)ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_order_tab', 9 ) ),
                    'callback'  => 'ovabrw_woo_request_booking_tab_content'
                );
            }

            // Add Extra Tab
            $ovabrw_manage_extra_tab = get_post_meta( $product_id, 'ovabrw_manage_extra_tab', true );

            switch( $ovabrw_manage_extra_tab ) {
                case 'in_setting' : {
                    $short_code_form = ovabrw_get_setting( get_option('ova_brw_extra_tab_shortcode_form', '') );
                    break;
                }
                case 'new_form' : {
                    $short_code_form = get_post_meta( $product_id, 'ovabrw_extra_tab_shortcode', true );
                    break;
                }
                case 'no' : {
                    $short_code_form = '';
                    break;
                }
                default: {
                    $short_code_form = ovabrw_get_setting( get_option('ova_brw_extra_tab_shortcode_form', '') );
                    break;
                }
            }

            if ( ! $product || $product->get_type() !== 'ovabrw_car_rental' ) return $tabs;
            if ( ovabrw_get_setting( get_option( 'ova_brw_template_show_extra_tab', 'yes' ) ) === 'yes' && $short_code_form != ''   ) {
                $tabs['ovabrw_extra_tab'] = array(
                    'title'     => esc_html__( 'Extra Tab', 'ova-brw' ),
                    'priority'  => (int)ovabrw_get_setting( get_option( 'ova_brw_extra_tab_order_tab', 21 ) ),
                    'callback'  => 'ova_new_product_tab_extra_tab'
                );
            }
        }
        return $tabs;
    }
}

if ( ! function_exists( 'ova_new_product_tab_extra_tab' ) ) {
    function ova_new_product_tab_extra_tab() {
        return ovabrw_get_template( 'single/contact_form.php' );
    }
}

if ( ! function_exists( 'ovabrw_woo_request_booking_tab_content' ) ) {
    function ovabrw_woo_request_booking_tab_content() { 
	   return ovabrw_get_template( 'single/request_booking.php' );
    }
}

/* Send mail in Request for booking */
if ( ! function_exists( 'ovabrw_request_booking' ) ) {
    function ovabrw_request_booking( $data ) {
        // Get subject setting
        $subject = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_subject', esc_html__('Request For Booking') ) );
        if ( empty( $subject ) ) {
            $subject = esc_html__('Request For Booking', 'ova-brw');
        }

        // Get email setting
        $mail_to_setting = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_from_email', get_option( 'admin_email' ) ) );

        if ( empty( $mail_to_setting ) ) {
            $mail_to_setting = get_option( 'admin_email' );
        }
        $mail_to = array( $mail_to_setting, $data['email'] );

        $body = '';
        $product_name   = isset( $data['product_name'] ) ? $data['product_name'] : '';
        $product_id     = isset( $data['product_id'] ) ? $data['product_id'] : '';
        $rental_type    = get_post_meta( $product_id, 'ovabrw_price_type', true );

        $name       = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
        $email      = isset( $data['email'] ) ? sanitize_text_field( $data['email'] ) : '';
        $number     = isset( $data['number'] ) ? sanitize_text_field( $data['number'] ) : '';
        $address    = isset( $data['address'] ) ? sanitize_text_field( $data['address'] ) : '';
        $ovabrw_pickup_loc  = isset( $data['ovabrw_pickup_loc'] ) ? sanitize_text_field( $data['ovabrw_pickup_loc'] ) : '';
        $ovabrw_pickoff_loc = isset( $data['ovabrw_pickoff_loc'] ) ? sanitize_text_field( $data['ovabrw_pickoff_loc'] ) : '';
        $pickup_date  = isset( $data['pickup_date'] ) ? sanitize_text_field( $data['pickup_date'] ) : '';
        $pickoff_date = isset( $data['pickoff_date'] ) ? sanitize_text_field( $data['pickoff_date'] ) : '';

        $extra = isset( $data['extra'] ) ? $data['extra'] : '';
        $ovabrw_period_package_id   = isset( $data['ovabrw_period_package_id'] ) ? sanitize_text_field( $data['ovabrw_period_package_id'] ) : '';
        $ovabrw_number_vehicle      = isset( $data['ovabrw_number_vehicle'] ) ? sanitize_text_field( $data['ovabrw_number_vehicle'] ) : '';
        $ovabrw_rental_type     = get_post_meta( $product_id, 'ovabrw_price_type', true );

        if ( $rental_type === 'taxi' ) {
            $date_format = ovabrw_get_date_format();
            $time_format = ovabrw_get_time_format_php();
        
            $date_time_format = $date_format . ' ' . $time_format;

            $ovabrw_pickup_loc  = isset( $data['ovabrw_req_pickup_loc'] ) ? trim( sanitize_text_field( $data['ovabrw_req_pickup_loc'] ) ) : '';
            $ovabrw_pickoff_loc = isset( $data['ovabrw_req_pickoff_loc'] ) ? trim( sanitize_text_field( $data['ovabrw_req_pickoff_loc'] ) ) : '';

            $ovabrw_waypoints   = isset( $data['ovabrw_req_waypoint_address'] ) ? $data['ovabrw_req_waypoint_address'] : [];
            $duration_map       = isset( $data['ovabrw-req-duration-map'] ) ? sanitize_text_field( $data['ovabrw-req-duration-map'] ) : '';
            $duration       = isset( $data['ovabrw-req-duration'] ) ? sanitize_text_field( $data['ovabrw-req-duration'] ) : '';
            $distance       = isset( $data['ovabrw-req-distance'] ) ? sanitize_text_field( $data['ovabrw-req-distance'] ) : '';
            $extra_time     = isset( $data['ovabrw_req_extra_time'] ) ? sanitize_text_field( $data['ovabrw_req_extra_time'] ) : '';
            $pickup_date    = isset( $data['pickup_date'] ) ? sanitize_text_field( $data['pickup_date'] ) : '';
            $pickup_time    = isset( $data['pickup_time'] ) ? sanitize_text_field( $data['pickup_time'] ) : '';
            $new_input_date = ovabrw_taxi_input_date( $pickup_date, $pickup_time, $duration );
            $pickup_date    = date( $date_time_format, $new_input_date['pickup_date_new'] );
            $pickoff_date   = date( $date_time_format, $new_input_date['pickoff_date_new'] );
        }

        // Resources
        $resources          = array();
        $resource_names     = array();
        $resource_checkboxs = isset( $data['ovabrw_resource_checkboxs'] ) ? $data['ovabrw_resource_checkboxs'] : [];
        $resource_qty       = isset( $data['ovabrw_resource_quantity'] ) ? $data['ovabrw_resource_quantity'] : [];

        if ( ! empty( $resource_checkboxs ) && is_array( $resource_checkboxs ) ) {
            $resource_ids    = get_post_meta( $product_id, 'ovabrw_resource_id', true );
            $resource_name   = get_post_meta( $product_id, 'ovabrw_resource_name', true );

            foreach( $resource_checkboxs as $rs_id ) {
                $rs_key = array_search( $rs_id, $resource_ids );
                
                if ( ! is_bool( $rs_key ) ) {
                    $rs_name = isset( $resource_name[$rs_key] ) ? $resource_name[$rs_key] : '';

                    if ( isset( $resource_qty[$rs_id] ) && absint( $resource_qty[$rs_id] ) ) {
                        $rs_name .= ' (x'.absint( $resource_qty[$rs_id] ).')';
                    }

                    $resources[$rs_id] = $rs_name;
                    array_push( $resource_names, $rs_name );
                }
            }
        }

        $data['resources']      = $resources;
        $data['resources_qty']  = $resource_qty;

        // Services
        $ovabrw_service         = isset( $data['ovabrw_service'] ) ? $data['ovabrw_service'] : [];
        $ser_qty                = isset( $data['ovabrw_service_qty'] ) ? $data['ovabrw_service_qty'] : [];
        $services_qty           = [];
        $ovabrw_service_label   = get_post_meta( $product_id, 'ovabrw_label_service', true );
        $ovabrw_service_id      = get_post_meta( $product_id, 'ovabrw_service_id', true );
        $ovabrw_service_name    = get_post_meta( $product_id, 'ovabrw_service_name', true );
        $ovabrw_service_str     = '';

        if ( ! empty( $ovabrw_service ) && is_array( $ovabrw_service ) ) {
            foreach( $ovabrw_service as $id_service ) {
                if ( isset( $ser_qty[$id_service] ) && absint( $ser_qty[$id_service] ) ) {
                    $services_qty[$id_service] = absint( $ser_qty[$id_service] );
                }

                if ( $id_service && $ovabrw_service_id && is_array( $ovabrw_service_id ) ) {
                    foreach ( $ovabrw_service_id as $key_id => $value_id_arr ) {
                        $ser_label = isset( $ovabrw_service_label[$key_id] ) ? $ovabrw_service_label[$key_id] : '';

                        if ( in_array( $id_service, $value_id_arr) ) {
                            foreach( $value_id_arr as $k => $v ) {
                                if ( $id_service == $v ) {
                                    $ser_name = $ovabrw_service_name[$key_id][$k];

                                    $ovabrw_service_str .= '<tr>';
                                    $ovabrw_service_str .= '<td>'.$ser_label.':</td>';
                                    if ( isset( $services_qty[$id_service] ) && absint( $services_qty[$id_service] ) ) {
                                        $ovabrw_service_str .= '<td>'.$ser_name.' (x'.absint( $services_qty[$id_service] ).')'.'</td>';
                                    } else {
                                        $ovabrw_service_str .= '<td>'.$ser_name.'</td>';
                                    }
                                    $ovabrw_service_str .= '</tr>';
                                }
                            }
                        }
                    }
                }
            }
        }
        $data['ovabrw_service_qty'] = $services_qty;

        $date_format = ovabrw_get_date_format();
        $time_format = ovabrw_get_time_format_php();

        if ( $ovabrw_rental_type === 'transportation' ) {
            $dropoff_date_by_setting = get_post_meta( $product_id, 'ovabrw_dropoff_date_by_setting', true );

            if ( $dropoff_date_by_setting != 'yes' && ! $pickoff_date ) {
                $time_dropoff = ovabrw_get_time_by_pick_up_off_loc_transport( $product_id, $ovabrw_pickup_loc, $ovabrw_pickoff_loc );
                $time_dropoff = 60 * $time_dropoff;
                $pickoff_date =  strtotime( $pickup_date ) + $time_dropoff;
                $pickoff_date = date( $date_format . ' ' . $time_format, $pickoff_date );
            }
        }

        if ( ! $pickoff_date ) {
            $pickoff_date = $pickup_date;
        }

        // get order
        $order = $product_id ? '<h2>'. esc_html__( 'Order details: ', 'ova-brw' ) . '</h2><table><tr><td style="width: 15%">' . esc_html__( 'Product: ', 'ova-brw' ).'</td><td style="width: 85%"><a href="'.get_permalink($product_id).'">'.$product_name.'</a><td></tr>' : '';
        $order .= $ovabrw_period_package_id ? esc_html__( 'Package: ', 'ova-brw' ).$ovabrw_period_package_id.'<br/>' : '';
        $order .= $name ? '<tr><td>' . esc_html__( 'Name: ', 'ova-brw' ) . '</td><td>' . $name . '</td></tr>' : '';
        $order .= $email ? '<tr><td>' . esc_html__( 'Email: ', 'ova-brw' ) . '</td><td>' . $email . '</td></tr>' : '';

        if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_number', 'yes' ) ) == 'yes' ) {
            $order .= $number ? '<tr><td>' . esc_html__( 'Phone: ', 'ova-brw' ) . '</td><td>' . $number . '</td></tr>' : '';
        }
        if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_address', 'yes' ) ) == 'yes' ) {
            $order .= $address ? '<tr><td>' . esc_html__( 'Address: ', 'ova-brw' ) . '</td><td>' . $address . '</td></tr>' : '';
        }
        if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_pickup_location', 'no' ) ) == 'yes' ) {
            $order .= $ovabrw_pickup_loc ? '<tr><td>' . esc_html__( 'Pick-up Location: ', 'ova-brw' ) . '</td><td>' . $ovabrw_pickup_loc . '</td></tr>' : '';
        }

        if ( $rental_type === 'taxi' ) {
            if ( ! empty( $ovabrw_waypoints ) && is_array( $ovabrw_waypoints ) ) {
                foreach ( $ovabrw_waypoints as $k => $val ) {
                    $order .= $val ? '<tr><td>' . sprintf( esc_html__( 'Waypoint %s', 'ova_brw' ), $k + 1 ) . '</td><td>' . $val . '</td></tr>' : '';
                }
            }
        }

        if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_pickoff_location', 'no' ) ) == 'yes' ) {
            $order .= $ovabrw_pickoff_loc ? '<tr><td>' . esc_html__( 'Drop-off Location: ', 'ova-brw' ) . '</td><td>' . $ovabrw_pickoff_loc . '</td></tr>' : '';
        }
        if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_number_vehicle', 'no' ) === 'yes' ) ) {
            $order .= $ovabrw_number_vehicle ? '<tr><td>' . esc_html__( 'Quantity: ', 'ova-brw' ) . '</td><td>' . $ovabrw_number_vehicle . '</td></tr>' : '';
        }
        if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_pickup_date', 'yes' ) ) == 'yes' ) {
            $order .= $pickup_date ? '<tr><td>' . esc_html__( 'Pick-up Date: ', 'ova-brw' ) . '</td><td>' . $pickup_date . '</td></tr>' : '';
        }
        if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_pickoff_date', 'yes' ) ) == 'yes' ) {
            $order .= $pickoff_date ? '<tr><td>' . esc_html__( 'Drop-off Date: ', 'ova-brw' ) . '</td><td>' . $pickoff_date . '</td></tr>' : '';
        }

        if ( $rental_type === 'taxi' ) {
            $order .= $distance ? '<tr><td>' . esc_html__( 'Distance: ', 'ova-brw' ) . '</td><td>' . ovarw_taxi_get_distance_text( $distance, $product_id ) . '</td></tr>' : '';
            $order .= $extra_time ? '<tr><td>' . esc_html__( 'Extra Time: ', 'ova-brw' ) . '</td><td>' . sprintf( esc_html__( '%s hour(s)', 'ova-brw' ), $extra_time ) . '</td></tr>' : '';
            $order .= $duration ? '<tr><td>' . esc_html__( 'Duration: ', 'ova-brw' ) . '</td><td>' . ovarw_taxi_get_duration_text( $duration ) . '</td></tr>' : '';
        }

        if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_service', 'yes' ) ) == 'yes' && ! empty( $resource_names ) ) {
            if ( count( $resource_names ) === 1 ) {
                $order .= '<tr><td>' . esc_html__( 'Resource: ', 'ova-brw' );
            } else {
                $order .= '<tr><td>' . esc_html__( 'Resources: ', 'ova-brw' );
            }
            
            $resource = $resource_names ? implode(', ', $resource_names) : '';
            $order .= '</td><td>' . $resource . '</td></tr>';
        }

        if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_service', 'yes' ) ) === 'yes' ) {
            $order .= $ovabrw_service_str;
        }

        // Custom Checkout Fields
        $list_fields        = ovabrw_get_list_field_checkout( $product_id );
        $custom_ckf         = array();
        $custom_ckf_qty     = [];
        $custom_ckf_save    = array();

        if ( ! empty( $list_fields ) && is_array( $list_fields ) ) {
            foreach ( $list_fields as $key => $field ) {
                if ( $field['type'] === 'file' ) {
                    $files = isset( $_FILES[$key] ) ? $_FILES[$key] : '';

                    if ( ! empty( $files ) ) {
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
                        
                        $order .= '<tr>
                                    <td>'.sprintf( '%s: ', esc_html( $field['label'] ) ).'</td>
                                    <td><a href="'.esc_url( $upload['url'] ).'" title="'.esc_attr( basename( $upload['file'] ) ).'" target="_blank">'.esc_attr( basename( $upload['file'] ) ).'</a></td>
                                </tr>';
                        $custom_ckf_save[$key] = '<a href="'.esc_url( $upload['url'] ).'" title="'.esc_attr( basename( $upload['file'] ) ).'" target="_blank">'.esc_attr( basename( $upload['file'] ) ).'</a>';
                    }
                } else {
                    $value = array_key_exists( $key, $data ) ? $data[$key] : '';

                    if ( ! empty( $value ) && 'on' === $field['enabled'] ) {
                        if ( 'select' === $field['type'] ) {
                            $custom_ckf[$key] = sanitize_text_field( $value );

                            if ( isset( $data[$key.'_qty'] ) && ! empty( $data[$key.'_qty'] ) ) {
                                if ( isset( $data[$key.'_qty'][$value] ) && absint( $data[$key.'_qty'][$value] ) ) {
                                    $custom_ckf_qty[$value] = absint( $data[$key.'_qty'][$value] );
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

                            $custom_ckf[$key] = $value;

                            if ( isset( $data[$key.'_qty'] ) && ! empty( $data[$key.'_qty'] ) && is_array( $data[$key.'_qty'] ) ) {
                                $custom_ckf_qty = $custom_ckf_qty + $data[$key.'_qty'];
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

                        if ( 'radio' === $field['type'] ) {
                            $custom_ckf[$key] = sanitize_text_field( $value );

                            if ( isset( $data[$key.'_qty'] ) && ! empty( $data[$key.'_qty'] ) ) {
                                $qty = isset( $data[$key.'_qty'][$custom_ckf[$key]] ) && absint( $data[$key.'_qty'][$custom_ckf[$key]] ) ? absint( $data[$key.'_qty'][$custom_ckf[$key]] ) : 1;
                                $custom_ckf_qty[$key] = $qty;

                                $value .= ' (x'.$qty.')';
                            }
                        }

                        $order .= '<tr><td>'.sprintf( '%s: ', esc_html( $field['label'] ) ).'</td><td>'.esc_html( $value ).'</td></tr>';

                        $custom_ckf_save[$key] = $value;
                    }
                }
            }
        }

        $data['custom_ckf']         = $custom_ckf;
        $data['custom_ckf_qty']     = $custom_ckf_qty;
        $data['custom_ckf_save']    = $custom_ckf_save;

        if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_info', 'yes' ) ) == 'yes' ) {
            $order .= $extra ? '<tr><td>' . esc_html__( 'Extra: ', 'ova-brw' ) . '</td><td>' . $extra . '</td></tr><table>' : '';
        }

        // Get Email Content
        $body = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_content', esc_html__( 'You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date] - [ovabrw_order_pickoff_date]. [ovabrw_order_details]', 'ova-brw' ) ) );
        if ( empty( $body ) ) {
            $body = esc_html__( 'You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date] - [ovabrw_order_pickoff_date]. [ovabrw_order_details]', 'ova-brw' );
        }
        $body = str_replace('[ovabrw_vehicle_name]', '<a href="'.get_permalink($product_id).'" target="_blank">'.$product_name.'</a>', $body);
        // Replace body
        $body = str_replace('[ovabrw_order_pickup_date]', $pickup_date, $body);
        $body = str_replace('[ovabrw_order_pickoff_date]', $pickoff_date, $body);
        $body = str_replace('[ovabrw_order_details]', $order, $body);
        $body = apply_filters( 'ovabrw_request_booking_content_mail', $body, $data );

        // Create Order
        $create_order = ovabrw_get_setting( get_option( 'ova_brw_request_booking_create_order', 'no' ) );

        if ( $create_order === 'yes' ) {
            $order_id = ovabrw_request_booking_create_new_order( $data );
        }

        return ovabrw_ovabrw_sendmail( $mail_to, $subject, $body );
    }
}

if ( ! function_exists( 'ova_wp_mail_from' ) ) {
    function ova_wp_mail_from() {
        return $mail_to_setting = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_from_email', get_option( 'admin_email' ) ) );
    }
}

if ( ! function_exists( 'ova_wp_mail_from_name' ) ) {
    function ova_wp_mail_from_name(){
        $ova_wp_mail_from_name = ovabrw_get_setting( get_option( 'ova_brw_request_booking_mail_from_name', esc_html__( 'Request For Booking', 'ova-brw' ) ) );
        if ( empty( $ova_wp_mail_from_name ) ) {
            $ova_wp_mail_from_name = esc_html__( 'Request For Booking', 'ova-brw' );
        }

        return $ova_wp_mail_from_name;
    }
}

if ( ! function_exists( 'ovabrw_ovabrw_sendmail' ) ) {
    function ovabrw_ovabrw_sendmail( $mail_to, $subject, $body ) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=".get_bloginfo( 'charset' )."\r\n";
        
        add_filter( 'wp_mail_from', 'ova_wp_mail_from' );
        add_filter( 'wp_mail_from_name', 'ova_wp_mail_from_name' );

        if( wp_mail($mail_to, $subject, $body, $headers ) ){
            $result = true;
        }else{
            $result = false;
        }

        remove_filter( 'wp_mail_from', 'ova_wp_mail_from');
        remove_filter( 'wp_mail_from_name', 'ova_wp_mail_from_name' );

        return $result;
    }
}

// Request for Booking create new order
if ( ! function_exists( 'ovabrw_request_booking_create_new_order' ) ) {
    function ovabrw_request_booking_create_new_order( $data ) {
        $product_id = isset( $data['product_id'] ) ? $data['product_id'] : '';

        if ( ! $product_id ) return false;
        $rental_type    = get_post_meta( $product_id, 'ovabrw_price_type', true );
        $name           = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
        $email          = isset( $data['email'] ) ? sanitize_text_field( $data['email'] ) : '';
        $phone          = isset( $data['number'] ) ? sanitize_text_field( $data['number'] ) : '';
        $address        = isset( $data['address'] ) ? sanitize_text_field( $data['address'] ) : '';
        $note           = isset( $data['extra'] ) ? sanitize_text_field( $data['extra'] ) : '';
        $date_format    = ovabrw_get_date_format();
        $time_format    = ovabrw_get_time_format_php();
        $rental_type    = get_post_meta( $product_id, 'ovabrw_price_type', true );
        $pickup_loc     = isset( $data['ovabrw_pickup_loc'] ) ? sanitize_text_field( $data['ovabrw_pickup_loc'] ) : '';
        $pickoff_loc    = isset( $data['ovabrw_pickoff_loc'] ) ? sanitize_text_field( $data['ovabrw_pickoff_loc'] ) : '';
        $pickup_date    = isset( $data['pickup_date'] ) ? sanitize_text_field( $data['pickup_date'] ) : '';
        $pickoff_date   = isset( $data['pickoff_date'] ) ? sanitize_text_field( $data['pickoff_date'] ) : '';

        if ( $rental_type === 'taxi' ) {
            $date_time_format = $date_format . ' ' . $time_format;

            $pickup_loc  = isset( $data['ovabrw_req_pickup_loc'] ) ? trim( sanitize_text_field( $data['ovabrw_req_pickup_loc'] ) ) : '';
            $pickoff_loc = isset( $data['ovabrw_req_pickoff_loc'] ) ? trim( sanitize_text_field( $data['ovabrw_req_pickoff_loc'] ) ) : '';

            $ovabrw_waypoints   = isset( $data['ovabrw_req_waypoint_address'] ) ? $data['ovabrw_req_waypoint_address'] : [];
            $duration_map       = isset( $data['ovabrw-req-duration-map'] ) ? sanitize_text_field( $data['ovabrw-req-duration-map'] ) : '';
            $duration       = isset( $data['ovabrw-req-duration'] ) ? sanitize_text_field( $data['ovabrw-req-duration'] ) : '';
            $distance       = isset( $data['ovabrw-req-distance'] ) ? sanitize_text_field( $data['ovabrw-req-distance'] ) : '';
            $extra_time     = isset( $data['ovabrw_req_extra_time'] ) ? sanitize_text_field( $data['ovabrw_req_extra_time'] ) : '';
            $pickup_date    = isset( $data['pickup_date'] ) ? sanitize_text_field( $data['pickup_date'] ) : '';
            $pickup_time    = isset( $data['pickup_time'] ) ? sanitize_text_field( $data['pickup_time'] ) : '';
            $new_input_date = ovabrw_taxi_input_date( $pickup_date, $pickup_time, $duration );
            $pickup_date    = date( $date_time_format, $new_input_date['pickup_date_new'] );
            $pickoff_date   = date( $date_time_format, $new_input_date['pickoff_date_new'] );
        }

        $resources      = isset( $data['resources'] ) ? $data['resources'] : [];
        $resources_qty  = isset( $data['resources_qty'] ) ? $data['resources_qty'] : [];
        $services       = isset( $data['ovabrw_service'] ) ? $data['ovabrw_service'] : [];
        $services_qty   = isset( $data['ovabrw_service_qty'] ) ? $data['ovabrw_service_qty'] : [];
        $custom_ckf     = isset( $data['custom_ckf'] ) ? $data['custom_ckf'] : [];
        $custom_ckf_qty = isset( $data['custom_ckf_qty'] ) ? $data['custom_ckf_qty'] : [];
        $package_id     = isset( $data['ovabrw_period_package_id'] ) ? sanitize_text_field( $data['ovabrw_period_package_id'] ) : '';
        $quantity       = isset( $data['ovabrw_number_vehicle'] ) ? sanitize_text_field( $data['ovabrw_number_vehicle'] ) : 1;

        if ( ! $pickoff_date ) {
            $pickoff_date = $pickup_date;
        }

        $new_date       = ovabrw_new_input_date( $product_id, strtotime( $pickup_date ), strtotime( $pickoff_date ), $package_id, $pickup_loc, $pickoff_loc );
        $rental_period  = get_rental_info_period( $product_id, $new_date['pickup_date_new'], $rental_type, $package_id );

        $cart_item['product_id']            = $product_id;
        $cart_item['ovabrw_number_vehicle'] = $quantity;
        $cart_item['resources']             = $resources;
        $cart_item['resources_qty']         = $resources_qty;
        $cart_item['ovabrw_service']        = $services;
        $cart_item['ovabrw_service_qty']    = $services_qty;
        $cart_item['custom_ckf']            = $custom_ckf;
        $cart_item['custom_ckf_qty']        = $custom_ckf_qty;
        $cart_item['period_price']          = $rental_period['period_price'];

        if ( $rental_type === 'taxi' ) {
            $cart_item['duration_map']  = $duration_map;
            $cart_item['duration']      = $duration;
            $cart_item['distance']      = $distance;
            $cart_item['extra_time']    = $extra_time;
        }

        $price_by_date = [
            'line_total'        => 0,
            'amount_insurance'  => 0,
            'quantity'          => 0,
        ];

        if ( $rental_type === 'taxi' ) {
            $price_by_date = get_price_by_distance( $product_id, $new_date['pickup_date_new'], $new_date['pickoff_date_new'], $cart_item );
        } else {
            $price_by_date = get_price_by_date( $product_id, $new_date['pickup_date_new'], $new_date['pickoff_date_new'], $cart_item, $pickup_loc, $pickoff_loc, $package_id );
        }
        
        $line_total = ovabrw_convert_price( $price_by_date['line_total'] );
        $insurance  = ovabrw_convert_price( $price_by_date['amount_insurance'] );

        // Billing
        $order_address = array(
            'billing' => array(
                'first_name' => $name,
                'last_name'  => '',
                'company'    => '',
                'email'      => $email,
                'phone'      => $phone,
                'address_1'  => $address,
                'address_2'  => '',
                'city'       => '',
                'country'    => '',
            ),
            'shipping' => array(),
        );

        $args = array(
            'status'        => '',
            'customer_note' => $note,
        );
        
        $order      = wc_create_order( $args ); // Create new order
        $order_id   = $order->get_id(); // Get order id
        $products   = wc_get_product( $product_id );

        // Add product to order
        $order->add_product( wc_get_product( $product_id ), $quantity, array( 'total' => $line_total ) );

        // Order Item
        $order_items = $order->get_items(); 

        // Loop order items
        foreach ( $order_items as $item_id => $product ) {
            $item = WC_Order_Factory::get_order_item( absint( $item_id ) );

            if ( ! $item ) {
                continue;
            }

            $data_item  = array();
            $product_id = $product['product_id'];
            $defined_one_day = defined_one_day( $product_id );
            $total_days      = 1;

            $pickup_date_real    = $pickup_date;
            $dropoff_date_real   = $pickoff_date;

            if ( $rental_type === 'transportation' ) {
                $dropoff_date_by_setting = get_post_meta( $product_id, 'ovabrw_dropoff_date_by_setting', true );
                if ( $dropoff_date_by_setting != 'yes' ) {
                    $time_dropoff = ovabrw_get_time_by_pick_up_off_loc_transport( $product_id, $pickup_loc, $pickoff_loc );

                    // Get Second
                    $time_dropoff_seconds   = 60 * $time_dropoff;
                    $dropoff_date_timestamp = strtotime( $pickup_date ) + $time_dropoff_seconds;
                    $pickoff_date           = date( $date_format . ' ' . $time_format, $dropoff_date_timestamp );
                }

                $pickup_date_real   = date( $date_format, strtotime( $pickup_date ) ) . ' 00:00';
                $dropoff_date_real  = date( $date_format, strtotime( $pickup_date ) ) . ' 24:00';
            } elseif ( $rental_type === 'period_time' ) {
                $ovabrw_unfixed = get_post_meta( $product_id, 'ovabrw_unfixed_time', true );

                $date_time_format = $date_format . ' ' . $time_format;

                if ( $ovabrw_unfixed != 'yes' ) {
                    $date_time_format = $date_format;

                    if ( $rental_period['package_type'] == 'inday' ) {
                        $date_time_format = $date_format . ' ' . $time_format;   
                    }
                }

                $pickup_date  = $rental_period['start_time'] ? date( $date_time_format, $rental_period['start_time'] ) : '';
                $pickoff_date = $rental_period['end_time'] ? date( $date_time_format, $rental_period['end_time'] ) : '';
            } else {
                $total_days = get_real_quantity( 1, $product_id, $new_date['pickup_date_new'], $new_date['pickoff_date_new'] );
            }
           
            if ( $defined_one_day == 'hotel' ) {
                $pickup_date_real   = date( $date_format, $new_date['pickup_date_new'] ) . ' 00:00';
                $dropoff_date_real  = date( $date_format, ( $new_date['pickoff_date_new'] - ( 3600 * 24 ) ) ) . ' 24:00';
            } elseif ( $defined_one_day == 'day' ) {
                $pickup_date_real   = date( $date_format, $new_date['pickup_date_new'] ) . ' 00:00';
                $dropoff_date_real  = date( $date_format, $new_date['pickoff_date_new'] ) . ' 24:00';
            } elseif ( $defined_one_day == 'hour' ) {
                $pickup_date_real   = $pickup_date;
                $dropoff_date_real  = $pickoff_date;
            } else {
                $pickup_date_real   = $pickup_date;
                $dropoff_date_real  = $pickoff_date;
            }

            $data_item[ 'ovabrw_pickup_loc' ] = $pickup_loc;

            if ( $rental_type === 'taxi' ) {
                if ( ! empty( $ovabrw_waypoints ) && is_array( $ovabrw_waypoints ) ) {
                    foreach ( $ovabrw_waypoints as $k => $val ) {
                        $data_item[sprintf( esc_html__( 'Waypoint %s', 'ova_brw' ), $k + 1 )] = $val;
                    }
                }
            }

            $data_item[ 'ovabrw_pickoff_loc' ]          = $pickoff_loc;
            $data_item[ 'ovabrw_pickup_date' ]          = $pickup_date;
            $data_item[ 'ovabrw_pickoff_date' ]         = $pickoff_date;
            $data_item[ 'ovabrw_number_vehicle' ]       = $quantity;
            $data_item[ 'ovabrw_pickup_date_real' ]     = $pickup_date_real;
            $data_item[ 'ovabrw_pickoff_date_real' ]    = $dropoff_date_real;

            if ( $rental_type === 'taxi' ) {
                if ( $distance ) {
                    $data_item[ 'distance' ] = ovarw_taxi_get_distance_text( $distance, $product_id );
                }

                if ( $extra_time ) {
                    $data_item['ovabrw_extra_time'] = sprintf( esc_html__( '%s hour(s)', 'ova-brw' ), $extra_time );
                }

                if ( $duration ) {
                    $data_item['ovabrw_duration'] = ovarw_taxi_get_duration_text( $duration );
                }
            }

            // Services
            if ( ! empty( $services ) ) {
                $data_item['ovabrw_services']       = $services;
                $data_item['ovabrw_services_qty']   = $services_qty;

                $services_id      = get_post_meta( $product_id, 'ovabrw_service_id', true ); 
                $services_name    = get_post_meta( $product_id, 'ovabrw_service_name', true );
                $services_label   = get_post_meta( $product_id, 'ovabrw_label_service', true ); 

                foreach ( $services as $val_ser ) {
                    if ( ! empty( $services_id ) && is_array( $services_id ) ) {
                        foreach ( $services_id as $key => $value ) {
                            if ( is_array( $value ) && ! empty( $value ) ) {
                                foreach ( $value as $k => $val ) {
                                    if ( $val_ser == $val && ! empty( $val ) ){
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
            }

            // Resources
            if ( ! empty( $resources ) ) {
                $data_item['ovabrw_resources']      = $resources;
                $data_item['ovabrw_resources_qty']  = $resources_qty;

                $res_name = [];

                foreach ( $resources as $res_id => $val ) {
                    array_push( $res_name, $val );
                }

                if ( count( $resources ) == 1 ) {
                    $data_item[esc_html__( 'Resource', 'ova-brw' )] = join( ', ', $res_name );
                } else {
                    $data_item[esc_html__( 'Resources', 'ova-brw' )] = join( ', ', $res_name );
                }
            }

            if ( $custom_ckf ) {
                $data_item[ 'ovabrw_custom_ckf' ]       = $custom_ckf;
                $data_item[ 'ovabrw_custom_ckf_qty' ]   = $custom_ckf_qty;
            }

            if ( isset( $data['custom_ckf_save'] ) && $data['custom_ckf_save'] ) {
                foreach ( $data['custom_ckf_save'] as $k => $val ) {
                    $data_item[$k] = $val;
                }
            }

            $data_item[ 'rental_type' ]         = $rental_type;
            $data_item[ 'period_label' ]        = $package_id;
            $data_item[ 'ovabrw_total_days' ]   = $total_days;
            $data_item[ 'ovabrw_price_detail' ] = $line_total;

            if ( $insurance ) {
                $data_item[ 'ovabrw_amount_insurance_product' ] = $insurance;
            }

            if ( $rental_type == 'day' ) {
                $data_item[ 'define_day' ] = $defined_one_day;
            }

            // Add item
            foreach ( $data_item as $meta_key => $meta_value ) {
                wc_add_order_item_meta( $item_id, $meta_key, $meta_value );
            }

            // Update item meta
            $item->set_props(
                array(
                    'total'     => $line_total,
                    'subtotal'  => $line_total,
                )
            );

            $item->save();
        }

        $status_order = ovabrw_get_setting( get_option( 'ova_brw_request_booking_order_status', 'wc-on-hold' ) );
        wp_update_post( array( 'ID' => $order_id, 'post_status' => $status_order ) );

        $order->update_meta_data( '_ova_insurance_amount' , $insurance );
        $order->set_address( $order_address['billing'], 'billing' );
        $order->set_date_created( date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) );
        $order->calculate_totals( wc_tax_enabled() );

        $order->save();
    }
}