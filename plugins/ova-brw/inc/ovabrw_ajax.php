<?php
if ( ! defined( 'ABSPATH' ) ) exit();

add_action( 'wp_ajax_ovabrw_load_name_product', 'ovabrw_load_name_product' );
add_action( 'wp_ajax_nopriv_ovabrw_load_name_product', 'ovabrw_load_name_product' );
if ( ! function_exists( 'ovabrw_load_name_product' ) ) {
	function ovabrw_load_name_product() {
		$keyword 	= isset($_POST['keyword']) ? sanitize_text_field( $_POST['keyword'] ) : '';
		$the_query 	= new WP_Query( array( 'post_type' => 'product' , 's' => $keyword, 'posts_per_page'=> '10') );

		if ( $the_query->have_posts() ) :
			while( $the_query->have_posts() ): $the_query->the_post();
				$title[] = get_the_title();
			endwhile;
			wp_reset_postdata();  
		endif;

		echo json_encode( $title );
		wp_die();
	}
}

add_action( 'wp_ajax_ovabrw_load_name_product_id', 'ovabrw_load_name_product_id' );
add_action( 'wp_ajax_nopriv_ovabrw_load_name_product_id', 'ovabrw_load_name_product_id' );
if ( ! function_exists( 'ovabrw_load_name_product_id' ) ) {
	function ovabrw_load_name_product_id() {
		$keyword 	= isset($_POST['keyword']) ? sanitize_text_field( $_POST['keyword'] ) : '';
		$the_query = new WP_Query( array( 'post_type' => 'product' , 's' => $keyword, 'posts_per_page'=> '10') );

		if ( $the_query->have_posts() ) :
			while( $the_query->have_posts() ): $the_query->the_post();
				$title[] = get_the_title() . '(#'.get_the_id().')';
			endwhile;
			wp_reset_postdata();  
		endif;

		echo json_encode($title);
		wp_die();
	}
}

add_action( 'wp_ajax_ovabrw_get_rental_type_product', 'ovabrw_get_rental_type_product' );
add_action( 'wp_ajax_nopriv_ovabrw_get_rental_type_product', 'ovabrw_get_rental_type_product' );
if ( ! function_exists( 'ovabrw_get_rental_type_product' ) ) {
	function ovabrw_get_rental_type_product() {
		$id_product 	= isset($_POST['id_product']) ? sanitize_text_field( $_POST['id_product'] ) : '';
		$rental_type 	= get_post_meta( $id_product, 'ovabrw_price_type', true );

		$data = [
			'rental_type' => $rental_type,
		];

		echo json_encode( $data );
		wp_die();
	}
}

add_action( 'wp_ajax_ovabrw_get_id_vehicle_rest', 'ovabrw_get_id_vehicle_rest' );
add_action( 'wp_ajax_nopriv_ovabrw_get_id_vehicle_rest', 'ovabrw_get_id_vehicle_rest' );
if ( ! function_exists( 'ovabrw_get_id_vehicle_rest' ) ) {
	function ovabrw_get_id_vehicle_rest() {
		$id_product 	= isset($_POST['id_product']) ? sanitize_text_field( $_POST['id_product'] ) : '';
		$pickup_date 	= isset($_POST['pickup_date']) ? strtotime(sanitize_text_field( $_POST['pickup_date'] )) : '';
		$dropp_off 		= isset($_POST['dropp_off']) ? strtotime(sanitize_text_field( $_POST['dropp_off'] )) : '';
		$pickup_loc 	= isset($_POST['pickup_loc']) ? sanitize_text_field( $_POST['pickup_loc'] ) : '';
		$dropoff_loc 	= isset($_POST['dropoff_loc']) ? sanitize_text_field( $_POST['dropoff_loc'] ) : '';
		$package_id 	= isset($_POST['package_id']) ? sanitize_text_field( $_POST['package_id'] ) : '';
		$new_input_date = ovabrw_new_input_date( $id_product, $pickup_date, $dropp_off, $package_id, $pickup_loc, $dropoff_loc );

		$pickup_date_new 	= $new_input_date['pickup_date_new'];
		$pickoff_date_new 	= $new_input_date['pickoff_date_new'];
		$data_vehicle 		= ova_validate_manage_store( $id_product, $pickup_date_new, $pickoff_date_new, $pickup_loc, $dropoff_loc, true, 'search' );
		if ( $data_vehicle ) {
			$data = $data_vehicle;
		} else {
			$data = [
				'error' => true,
				'start_date' => 'empty',
				'message' => esc_html__("Vehicle is not available for this time, Please book other time.", 'ova-brw'),
			];
		}
		
		echo json_encode( $data );
		wp_die();
	}
}

add_action( 'wp_ajax_ovabrw_load_data_product_create_order', 'ovabrw_load_data_product_create_order' );
add_action( 'wp_ajax_nopriv_ovabrw_load_data_product_create_order', 'ovabrw_load_data_product_create_order' );
if ( ! function_exists( 'ovabrw_load_data_product_create_order' ) ) {
	function ovabrw_load_data_product_create_order() {
		$name_product 	= isset( $_POST['name_product'] ) ? sanitize_text_field( $_POST['name_product'] ) : '';
		$currency 		= isset( $_POST['currency'] ) ? sanitize_text_field( $_POST['currency'] ) : '';

		$id_product 	= trim( sanitize_text_field( $name_product ) );
		$rental_type 	= get_post_meta( $id_product, 'ovabrw_price_type', true );
		$petime_id 		= get_post_meta( $id_product, 'ovabrw_petime_id', true );
		$petime_label 	= get_post_meta( $id_product, 'ovabrw_petime_label', true );
		$product_price 	= get_post_meta( $id_product, '_regular_price', true );
		$product_price 	= ovabrw_convert_price_in_admin( $product_price, $currency );

		$ovabrw_amount_insurance 	= get_post_meta( $id_product, 'ovabrw_amount_insurance', true );
		$ovabrw_amount_insurance 	= ovabrw_convert_price( $ovabrw_amount_insurance, ['currency' => $currency] );
		$ovabrw_id_vehicles 		= get_post_meta( $id_product, 'ovabrw_id_vehicles', true );
		$ovabrw_define_1_day 		= get_post_meta( $id_product, 'ovabrw_define_1_day', true);
		$unfixed_time 				= get_post_meta( $id_product, 'ovabrw_unfixed_time', true);
		$show_pickoff_date_location = false;

		if ( $rental_type == 'transportation' ) {
			// Get html pick-up location
			$html_pickup_location 	= ovabrw_get_locations_transport_html( 'ovabrw_pickoff_loc', 'required', '', $id_product, 'pickup' );
			// Get html drop-off location
			$html_dropoff_location 	= ovabrw_get_locations_transport_html( 'ovabrw_pickoff_loc', 'required', '', $id_product, 'dropoff' );

			$dropoff_date_by_setting = get_post_meta( $id_product, 'ovabrw_dropoff_date_by_setting', true );
			if ( $dropoff_date_by_setting === 'yes' ) {
				$show_pickoff_date_location = true;
			}
		} else {
			// Get html pick-up location
			$html_pickup_location 	= ovabrw_get_locations_html( 'ovabrw_pickoff_loc', 'required', '', $id_product, 'pickup' );
			// Get html drop-off location
			$html_dropoff_location 	= ovabrw_get_locations_html( 'ovabrw_pickoff_loc', 'required', '', $id_product, 'dropoff' );
		}

		// Show/hide Pick-up date
		$show_pickup_date 		= ovabrw_show_pick_date_product( $id_product, $type = 'pickup' );

		// Show/hide Drop-off date
		$show_pickoff_date 		= ovabrw_show_pick_date_product( $id_product, $type = 'dropoff' );

		// Get show number vehicle
		$show_number_vehicle 	= get_post_meta( $id_product, 'ovabrw_show_number_vehicle', true);

		// Get show pick-up, drop-off date location
		$show_pickup_loc 	= ovabrw_show_pick_location_product( $id_product, $type = 'pickup' );
		$show_pickoff_loc 	= ovabrw_show_pick_location_product( $id_product, $type = 'dropoff' );

		// Taxi
		$show_pickup_time 	= $show_pickup_location = $show_pickoff_location = false;
		$data_location 		= [];

		if ( $rental_type === 'taxi' ) {
			$show_pickup_loc 	= $show_pickoff_loc = $show_pickoff_date = false;
			$show_pickup_time 	= $show_pickup_location = $show_pickoff_location = true;

			$extra_time_hour 	= get_post_meta( $id_product, 'ovabrw_extra_time_hour', true );
			$extra_time_label 	= get_post_meta( $id_product, 'ovabrw_extra_time_label', true );
			$extra_time_html 	= '';

			if ( ! empty( $extra_time_hour ) && ! empty( $extra_time_label ) ) {
				$extra_time_html .= '<option value="">'.esc_html__( 'Select Time', 'ova-brw' ).'</option>';

				foreach ( $extra_time_hour as $k => $val ) {
					$ext_label = isset( $extra_time_label[$k] ) ? $extra_time_label[$k] : '';
					$extra_time_html .= '<option value="'.esc_attr( $val ).'">'.$ext_label.'</option>';
				}
			}

			$ovabrw_zoom_map 	= get_post_meta( $id_product, 'ovabrw_zoom_map', true );
			$ovabrw_lat 		= get_post_meta( $id_product, 'ovabrw_latitude', true );
			$ovabrw_lng 		= get_post_meta( $id_product, 'ovabrw_longitude', true );

			if ( ! $ovabrw_lat ) {
				$ovabrw_lat = get_option( 'ova_brw_latitude_map_default', 39.177972 );
			}

			if ( ! $ovabrw_lng ) {
				$ovabrw_lng = get_option( 'ova_brw_longitude_map_default', -100.36375 );
			}

			$price_by 		= get_post_meta( $id_product, 'ovabrw_map_price_by', true );
			$map_types 		= get_post_meta( $id_product, 'ovabrw_map_types', true );
			$ovabrw_bounds 	= get_post_meta( $id_product, 'ovabrw_bounds', true );
			$bounds_lat 	= get_post_meta( $id_product, 'ovabrw_bounds_lat', true );
			$bounds_lng 	= get_post_meta( $id_product, 'ovabrw_bounds_lng', true );
			$bounds_radius 	= get_post_meta( $id_product, 'ovabrw_bounds_radius', true );
			$restrictions 	= get_post_meta( $id_product, 'ovabrw_restrictions', true );

			if ( ! $price_by ) $price_by = 'km';

			if ( ! $map_types ) $map_types = [ 'geocode' ];
			if ( ! $restrictions ) $restrictions = [];

			$data_location = [
				'price_by' 		=> $price_by,
				'types' 		=> json_encode( $map_types ),
				'lat' 			=> $ovabrw_lat,
				'lng' 			=> $ovabrw_lng,
				'zoom' 			=> $ovabrw_zoom_map,
				'bounds' 		=> $ovabrw_bounds,
				'bounds_lat' 	=> $bounds_lat,
				'bounds_lng' 	=> $bounds_lng,
				'bounds_radius' => $bounds_radius,
				'restrictions' 	=> json_encode( $restrictions ),
				'extra_time' 	=> $extra_time_html,
			];
		}

		// Get html custom checkout fields
		$html_custom_ckf = ovabrw_get_html_ckf_order( $id_product );

		// Get html resources
		$html_resources 	= ovabrw_get_html_resources_order( $id_product, $currency );

		// Get html resources
		$html_services	 	= ovabrw_get_html_services_order( $id_product );

		# Disable week day
		$disable_week_day 	= get_post_meta( $id_product, 'ovabrw_product_disable_week_day', true );

		$statuses 	= brw_list_order_status();
	    $order_date = get_order_rent_time( $id_product, $statuses );

		$data = [
			'rental_type' 				=> $rental_type,
			'petime_id' 				=> $petime_id,
			'petime_label' 				=> $petime_label,
			'ovabrw_amount_insurance' 	=> round( $ovabrw_amount_insurance, wc_get_price_decimals() ),
			'ovabrw_id_vehicles' 		=> $ovabrw_id_vehicles,
			'product_price' 			=> round( $product_price, wc_get_price_decimals() ),
			'ovabrw_define_1_day' 		=> $ovabrw_define_1_day,
			'order_date' 				=> $order_date,
			'show_number_vehicle' 		=> $show_number_vehicle,
			'show_pickup_loc' 	  		=> $show_pickup_loc ? "yes" : "no",
			'show_pickoff_loc' 	  		=> $show_pickoff_loc ? "yes" : "no",
			'show_pickup_date'			=> $show_pickup_date ? 'yes' : 'no',
			'show_pickoff_date'			=> $show_pickoff_date ? 'yes' : 'no',
			'html_pickup_location'		=> $html_pickup_location,
			'html_dropoff_location' 	=> $html_dropoff_location,
			'html_custom_ckf' 			=> $html_custom_ckf,
			'html_resources' 			=> $html_resources,
			'html_services' 			=> $html_services,
			'unfixed_time'				=> $unfixed_time,
			'show_pickoff_date_location'=> $show_pickoff_date_location,
			'disable_week_day' 			=> $disable_week_day,
			'show_pickup_location' 	  	=> $show_pickup_location ? "yes" : "no",
			'show_pickoff_location' 	=> $show_pickoff_location ? "yes" : "no",
			'show_pickup_time'			=> $show_pickup_time ? 'yes' : 'no',
			'data_location' 			=> json_encode( $data_location ),
		];

		echo json_encode( $data );
		wp_die();
	}
}

add_action( 'wp_ajax_ovabrw_load_tag_product', 'ovabrw_load_tag_product' );
add_action( 'wp_ajax_nopriv_ovabrw_load_tag_product', 'ovabrw_load_tag_product' );
if ( ! function_exists( 'ovabrw_load_tag_product' ) ) {
	function ovabrw_load_tag_product() {
		$keyword = isset($_POST['keyword']) ? sanitize_text_field( $_POST['keyword'] ) : '';
		$args = array(
	        'taxonomy'               => 'product_tag',
	        'search' => $keyword
	    );

	    $the_query = new WP_Term_Query($args);

	    $title = [];
	    if ( $the_query->terms ) {
	        foreach( $the_query->terms as $term ) {
	            $title[] =  $term->name;
	        }
	        wp_reset_postdata();  
	    }

		echo json_encode($title);
		wp_die();
	}
}

add_action( 'wp_ajax_ovabrw_get_total_price_and_quantity', 'ovabrw_get_total_price_and_quantity' );
add_action( 'wp_ajax_nopriv_ovabrw_get_total_price_and_quantity', 'ovabrw_get_total_price_and_quantity' );
if ( ! function_exists( 'ovabrw_get_total_price_and_quantity' ) ) {
	function ovabrw_get_total_price_and_quantity() {
		$id_product 	= isset( $_POST['id_product'] ) ? trim( sanitize_text_field( $_POST['id_product'] ) ) : '';
		$currency 		= isset( $_POST['currency'] ) ? trim( sanitize_text_field( $_POST['currency'] ) ) : '';
		$product_price 	= get_post_meta( $id_product, '_regular_price', true );

		// Get rental type product
		$rental_type 	= get_post_meta( $id_product, 'ovabrw_price_type', true );

		// Check is Auto/Manual to make ID vehicle 
	    $manage_store 	= get_post_meta( $id_product, 'ovabrw_manage_store', true );
		$start_date 	= isset( $_POST['start_date'] ) ? trim( sanitize_text_field( $_POST['start_date'] ) ) : '';
		$end_date 		= isset( $_POST['end_date'] ) ? trim( sanitize_text_field( $_POST['end_date'] ) ) : '';
		$id_package 	= isset( $_POST['id_package'] ) ? trim( sanitize_text_field( $_POST['id_package'] ) ) : '';
		$number_vehicle = isset( $_POST['number_vehicle'] ) ? trim( sanitize_text_field( $_POST['number_vehicle'] ) ) : 1;
		$pickup_loc 	= isset( $_POST['pickup_loc'] ) ? trim( sanitize_text_field( $_POST['pickup_loc'] ) ) : '';
		$dropoff_loc 	= isset( $_POST['dropoff_loc'] ) ? trim( sanitize_text_field( $_POST['dropoff_loc'] ) ) : '';
		$resources 		= isset( $_POST['resources'] ) ? str_replace( '\\', '', $_POST['resources'] ) : '';
		$resources_data = (array)json_decode( $resources );
		$resources_qty 	= isset( $_POST['resources_qty'] ) ? str_replace( '\\', '', $_POST['resources_qty'] ) : [];
		$resources_qty 	= (array)json_decode( $resources_qty );
		$services 		= isset( $_POST['services'] ) ? str_replace( '\\', '', $_POST['services'] ) : '';
		$services_data 	= (array)json_decode( $services );
		$services_qty 	= isset( $_POST['services_qty'] ) ? str_replace( '\\', '', $_POST['services_qty'] ) : '';
		$services_qty 	= (array)json_decode( $services_qty );
		$custom_ckf 	= isset( $_POST['custom_ckf'] ) ? str_replace( '\\', '', $_POST['custom_ckf'] ) : '';
		$custom_ckf 	= (array) json_decode( $custom_ckf );
		$custom_ckf_qty = isset( $_POST['custom_ckf_qty'] ) ? str_replace( '\\', '', $_POST['custom_ckf_qty'] ) : '';
		$custom_ckf_qty = (array) json_decode( $custom_ckf_qty );

		// Taxi
		$pickup_time 		= isset( $_POST['pickup_time'] ) ? sanitize_text_field( $_POST['pickup_time'] ) : '';
		$pickup_location 	= isset( $_POST['pickup_location'] ) ? trim( sanitize_text_field( $_POST['pickup_location'] ) ) : '';
		$pickoff_location 	= isset( $_POST['dropoff_location'] ) ? trim( sanitize_text_field( $_POST['dropoff_location'] ) ) : '';
		$extra_time 		= isset( $_POST['extra_time'] ) ? sanitize_text_field( $_POST['extra_time'] ) : '';
		$duration_map 		= isset( $_POST['duration_map'] ) ? sanitize_text_field( $_POST['duration_map'] ) : '';
		$duration 			= isset( $_POST['duration'] ) ? sanitize_text_field( $_POST['duration'] ) : '';
		$distance 			= isset( $_POST['distance'] ) ? sanitize_text_field( $_POST['distance'] ) : '';

		$show_pickoff_date = ovabrw_show_pick_date_product( $id_product, 'dropoff' );

		if ( ! $show_pickoff_date ) {
			$end_date = $start_date;
		}

		$new_input_date = ovabrw_new_input_date( $id_product, strtotime( $start_date ), strtotime( $end_date ), $id_package, $pickup_loc, $dropoff_loc );

		if ( $rental_type === 'taxi' ) {
			$new_input_date = ovabrw_taxi_input_date( $start_date, $pickup_time, $duration );
		}

		$pickup_date_new 	= $new_input_date['pickup_date_new'];
		$pickoff_date_new 	= $new_input_date['pickoff_date_new'];
		
		// For Rental Type: Period Time
		$price_type 		= get_post_meta( $id_product, 'ovabrw_price_type', true );
		$rental_info_period = get_rental_info_period( $id_product, $pickup_date_new, $price_type, $id_package );

		$cart_item['product_id'] 			= $id_product;
		$cart_item['ovabrw_number_vehicle'] = 1;
		$cart_item['resources'] 			= $resources_data;
		$cart_item['resources_qty'] 		= $resources_qty;
		$cart_item['ovabrw_service'] 		= $services_data;
		$cart_item['ovabrw_service_qty'] 	= $services_qty;
		$cart_item['custom_ckf'] 			= $custom_ckf;
		$cart_item['custom_ckf_qty'] 		= $custom_ckf_qty;
		$cart_item['period_price'] 			= $rental_info_period['period_price'];	
		$cart_item['duration_map'] 			= $duration_map;
		$cart_item['duration'] 				= $duration;
		$cart_item['distance'] 				= $distance;
		$cart_item['extra_time'] 			= $extra_time;
		
		$product_detail = get_real_price_detail( $product_price, $id_product, $pickup_date_new, $pickoff_date_new );

		if ( $rental_type === 'period_time' ) {
			if ( empty( $start_date ) ) {
				$data = [ 
					'error' 		=> true,
					'start_date' 	=> 'empty',
					'message' 		=> esc_html__('Please Choose Pick-up Date', 'ova-brw'),
				];
			} elseif ( empty( $id_package ) ) {
				$data = [ 
					'error' 		=> true,
					'id_package' 	=> 'empty',
					'message' 		=> esc_html__('No Package', 'ova-brw'),
				];
			} else {
				$total = get_price_by_date( $id_product, $pickup_date_new, $pickoff_date_new, $cart_item, $pickup_loc, $dropoff_loc, $id_package );
				$total['line_total'] 	= $total['line_total'];
	            $total['product_price'] = $total['line_total'];
				$data 					= $total;
			}
		} elseif ( $rental_type === 'taxi' ) {
			if ( empty( $start_date ) ) {
				$data = [ 
					'error' 		=> true,
					'start_date' 	=> 'empty',
					'message' 		=> esc_html__('Please Choose Pick-up Date', 'ova-brw'),
				];
			} elseif ( empty( $pickup_time ) ) {
				$data = [ 
					'error' 		=> true,
					'pickup_time' 	=> 'empty',
					'message' 		=> esc_html__('Please Choose Pick-up Time', 'ova-brw'),
				];
			} elseif ( empty( $pickup_location ) ) {
				$data = [ 
					'error' 			=> true,
					'pickup_location' 	=> 'empty',
					'message' 			=> esc_html__('Please Enter Pick-up Location', 'ova-brw'),
				];
			} elseif ( empty( $pickoff_location ) ) {
				$data = [ 
					'error' 			=> true,
					'pickup_location' 	=> 'empty',
					'message' 			=> esc_html__('Please Enter Pick-off Location', 'ova-brw'),
				];
			} else {
				$total 	= get_price_by_distance( $id_product, $pickup_date_new, $pickoff_date_new, $cart_item );
				$data 	= $total;
			}
		} else {
			if ( empty( $start_date ) ) {
				$data = [ 
					'error' 		=> true,
					'start_date' 	=> 'empty',
					'message' 		=> esc_html__('Please Choose Pick-up Date', 'ova-brw'),
				];
			} elseif ( empty( $end_date ) ) {
				$data = [ 
					'error' 	=> true,
					'end_date' 	=> 'empty',
					'message' 	=> esc_html__('Please Choose Drop-off Date', 'ova-brw'),
				];
			} else {
				$total = get_price_by_date( $id_product, $pickup_date_new, $pickoff_date_new, $cart_item, $pickup_loc, $dropoff_loc, $id_package );

				if ( $rental_type == 'hour' ) {
	                $total['quantity'] = $total['quantity'] . ' Hours';
	            } elseif ( $rental_type != 'period_time' ) {
	                $total['quantity'] = $total['quantity'] . ' Days';
	            }

	            $total['line_total'] 	= $total['line_total'];
	            $total['product_price'] = $product_detail;
				$data 					= $total;
			}
		}

		if ( empty( $number_vehicle ) || $number_vehicle < 0 ) {
			$data = [ 
				'error' => true,
				'start_date' => 'empty',
				'message' => esc_html__('Quantity does not exist or is less than 0', 'ova-brw'),
			];
		}

		$data_vehicle = ova_validate_manage_store( $id_product, $pickup_date_new, $pickoff_date_new, $pickup_loc, $dropoff_loc, true, 'search');

		$number_vehicle_available = 0;
		if ( $data_vehicle ) {
			$number_vehicle_available = isset( $data_vehicle['number_vehicle_available'] ) ? $data_vehicle['number_vehicle_available'] : 0;
			if ( $number_vehicle_available && (int)$number_vehicle_available >= (int)$number_vehicle ) {
				$data['number_vehicle_available'] = (int)$number_vehicle_available;
			} else {
				$data = [
					'error' => true,
					'start_date' => 'empty',
					'message' => esc_html__( "Vehicle is not available for this time, Please book other time.", 'ova-brw' ),
				];
			}
		} else {
			$data = [
				'error' => true,
				'start_date' => 'empty',
				'message' => esc_html__( "Vehicle is not available for this time, Please book other time.", 'ova-brw' ),
			];
		}

		if ( isset( $data['line_total'] ) ) {
			$data['line_total'] *= $number_vehicle;
			$data['line_total'] = round( ovabrw_convert_price( $data['line_total'], [ 'currency' => $currency ] ), wc_get_price_decimals() );
		}

		if ( isset( $total['product_price'] ) ) {
			$data['product_price'] = round( ovabrw_convert_price_in_admin( $data['product_price'], $currency ), wc_get_price_decimals() );
		}
		
		echo json_encode( $data );
		wp_die();
	}
}

add_action( 'wp_ajax_ovabrw_get_package_by_time', 'ovabrw_get_package_by_time_cus' );
add_action( 'wp_ajax_nopriv_ovabrw_get_package_by_time', 'ovabrw_get_package_by_time_cus' );
if ( ! function_exists( 'ovabrw_get_package_by_time_cus' ) ) {
	function ovabrw_get_package_by_time_cus() {
		$id 		= isset($_POST['post_id']) ? sanitize_text_field( $_POST['post_id'] ) : '';
		$start_date = isset($_POST['startdate']) ? strtotime( $_POST['startdate'] ) : '';

	    // Get list package of period time
		$ovabrw_petime_id		= get_post_meta( $id, 'ovabrw_petime_id', true );
		$ovabrw_petime_label 	= get_post_meta( $id, 'ovabrw_petime_label', true );

		// Package available
	    $package_available 	= array( '0' => esc_html__( 'Select Package', 'ova-brw' ) );

		if ( ! empty( $ovabrw_petime_id ) && is_array( $ovabrw_petime_id ) ) {
			foreach ( $ovabrw_petime_id as $k => $petime_id ) {
				$new_input_date = ovabrw_new_input_date( $id, $start_date, '', $petime_id, '', '' );

				$pickup_date_new    = $new_input_date['pickup_date_new'];
        		$pickoff_date_new   = $new_input_date['pickoff_date_new'];
        		$check_vehicle 		= ova_validate_manage_store( $id, $pickup_date_new, $pickoff_date_new, '', '', false, 'search', 1 );
			
				if ( $check_vehicle && $check_vehicle['status'] && ( $check_vehicle['vehicle_availables'] || $check_vehicle['number_vehicle_available'] ) ) {
					$package_available[$petime_id] = $ovabrw_petime_label[$k];
				}
			}
		}

		if ( empty( $package_available ) || count( $package_available ) <= 1 ) {
			$package_available = array( esc_html__( 'There are no packages', 'ova-brw' ) );
		}
		
		echo json_encode( $package_available );
		wp_die();
	}
}

add_action( 'wp_ajax_ovabrw_calculate_total', 'ovabrw_calculate_total', 10, 0 );
add_action( 'wp_ajax_nopriv_ovabrw_calculate_total', 'ovabrw_calculate_total', 10, 0 );
if ( ! function_exists( 'ovabrw_calculate_total' ) ) {
	function ovabrw_calculate_total() {
		$id = isset($_POST['id']) ? sanitize_text_field( $_POST['id'] ) : '';
		$pickup_loc 	= isset( $_POST['pickup_loc'] ) ? sanitize_text_field( $_POST['pickup_loc'] ) : '';
		$dropoff_loc 	= isset( $_POST['dropoff_loc'] ) ? sanitize_text_field( $_POST['dropoff_loc'] ) : '';
		$pickup_date 	= isset( $_POST['pickup_date'] ) ? sanitize_text_field( $_POST['pickup_date'] ) : '';
		$dropoff_date 	= isset( $_POST['dropoff_date'] ) ? sanitize_text_field( $_POST['dropoff_date'] ) : '';
		$package_id 	= isset( $_POST['package_id'] ) ? sanitize_text_field( $_POST['package_id'] ) : '';
		$quantity 		= isset( $_POST['quantity'] ) ? sanitize_text_field( $_POST['quantity'] ) : 1;
		$deposit 		= isset( $_POST['deposit'] ) ? sanitize_text_field( $_POST['deposit'] ) : '';
		$resources 		= isset( $_POST['resources'] ) ? str_replace( '\\', '', $_POST['resources'] ) : '';
		$resources_qty 	= isset( $_POST['resources_qty'] ) ? str_replace( '\\', '', $_POST['resources_qty'] ) : '';
		$services 		= isset( $_POST['services'] ) ? str_replace( '\\', '', $_POST['services'] ) : '';
		$services_qty 	= isset( $_POST['services_qty'] ) ? str_replace( '\\', '', $_POST['services_qty'] ) : '';
		$custom_ckf 	= isset( $_POST['custom_ckf'] ) ? str_replace( '\\', '', $_POST['custom_ckf'] ) : '';
		$custom_ckf_qty = isset( $_POST['custom_ckf_qty'] ) ? str_replace( '\\', '', $_POST['custom_ckf_qty'] ) : '';
		$custom_ckf 	= (array) json_decode( $custom_ckf );
		$custom_ckf_qty = (array) json_decode( $custom_ckf_qty );
		$services 		= (array) json_decode( $services );
		$services_qty 	= (array) json_decode( $services_qty );
		$resources 		= (array) json_decode( $resources );
		$resources_qty 	= (array) json_decode( $resources_qty );

		// Taxi
		$duration_map 	= isset( $_POST['duration_map'] ) ? sanitize_text_field( $_POST['duration_map'] ) : '';
		$duration 		= isset( $_POST['duration'] ) ? sanitize_text_field( $_POST['duration'] ) : '';
		$distance 		= isset( $_POST['distance'] ) ? sanitize_text_field( $_POST['distance'] ) : '';
		$pickup_time 	= isset( $_POST['pickup_time'] ) ? sanitize_text_field( $_POST['pickup_time'] ) : '';
		$extra_time 	= isset( $_POST['extra_time'] ) ? sanitize_text_field( $_POST['extra_time'] ) : '';

		// Rental Type
		$price_type = get_post_meta( $id, 'ovabrw_price_type', true );

		// Get new date
		$new_date = ovabrw_new_input_date( $id, strtotime( $pickup_date ), strtotime( $dropoff_date ), $package_id, $pickup_loc, $dropoff_loc );

		if ( $price_type === 'taxi' ) {
			$new_date = ovabrw_taxi_input_date( $pickup_date, $pickup_time, $duration );
		}

		if ( ! $new_date['pickup_date_new'] || ! $new_date['pickoff_date_new'] ) {
			echo 0;
			wp_die();
		}

		// For Rental Type: Period Time
		$rental_info_period = get_rental_info_period( $id, $new_date['pickup_date_new'], $price_type, $package_id );

		$cart_item['product_id'] 			= $id;
		$cart_item['ovabrw_number_vehicle'] = $quantity;
		$cart_item['resources'] 			= $resources;
		$cart_item['resources_qty'] 		= $resources_qty;
		$cart_item['ovabrw_service'] 		= $services;
		$cart_item['ovabrw_service_qty'] 	= $services_qty;
		$cart_item['custom_ckf'] 			= $custom_ckf;
		$cart_item['custom_ckf_qty'] 		= $custom_ckf_qty;
		$cart_item['period_price'] 			= $rental_info_period['period_price'];
		$cart_item['ova_type_deposit'] 		= $deposit;
		$cart_item['duration_map'] 			= $duration_map;
		$cart_item['duration'] 				= $duration;
		$cart_item['distance'] 				= $distance;
		$cart_item['extra_time'] 			= $extra_time;

		$total = [
			'quantity' 			=> 0,
			'line_total' 		=> 0,
			'amount_insurance' 	=> 0,
		];

		if ( $price_type === 'taxi' ) {
			$total = get_price_by_distance( $id, $new_date['pickup_date_new'], $new_date['pickoff_date_new'], $cart_item );
		} else {
			$total = get_price_by_date( $id, $new_date['pickup_date_new'], $new_date['pickoff_date_new'], $cart_item, $pickup_loc, $dropoff_loc, $package_id );
		}
		
		$line_total = $total['line_total'];

		// Calculate When choose Deposit or Full Amonunt at fronend
		$sub_deposit_amount = $line_total;

		/**
		 * Check Vehicle availables
		 */
		// Check Unavilable time
		$unavilable_time = ovabrw_check_unavilable_time( $id, $new_date['pickup_date_new'], $new_date['pickoff_date_new'] );
		if ( $unavilable_time ) {
			$total['error'] = esc_html__( 'This time is not available for renting', 'ova-brw' );
		}

		// Check product in order
		$store_vehicle_rented = ovabrw_vehicle_rented_in_order( $id, $new_date['pickup_date_new'], $new_date['pickoff_date_new'] );

		// Check product in cart
		$cart_vehicle_rented  = ovabrw_vehicle_rented_in_cart( $id, 'cart', $new_date['pickup_date_new'], $new_date['pickoff_date_new'] );

		// Get manage store
		$manage_store = get_post_meta( $id, 'ovabrw_manage_store', true );

		// Get array vehicle available
	    $data_vehicle = ovabrw_get_vehicle_available( $id, $store_vehicle_rented, $cart_vehicle_rented, $quantity, $manage_store, $new_date['pickup_date_new'], $new_date['pickoff_date_new'], $pickup_loc, $dropoff_loc, false, $validate = 'cart' );

	    // Number vehicle available
	    $total['number_vehicle_available'] = $data_vehicle['number_vehicle_available'];

	    if ( absint( $total['number_vehicle_available'] ) === 0 ) {
	    	$total['error'] = esc_html__( 'Out stock!', 'ova-brw' );
	    } elseif ( absint( $quantity ) > absint( $total['number_vehicle_available'] ) ) {
	    	$total['error'] = sprintf( esc_html__( 'Maximum Quantity: %s', 'ova-brw' ), $total['number_vehicle_available'] );
	    }
	    // Deposit
	    $deposit_enable = get_post_meta ( $id, 'ovabrw_enable_deposit', true );

	    if ( $deposit_enable === 'yes' && apply_filters( 'ovabrw_ajax_deposit_enable', true ) ) {
	    	$value_deposit = ! empty( get_post_meta ( $id, 'ovabrw_amount_deposit', true ) ) ? floatval( get_post_meta ( $id, 'ovabrw_amount_deposit', true ) ) : 0;
	    	$deposit_type  = get_post_meta ( $id, 'ovabrw_type_deposit', true );

	    	if ( $deposit === 'deposit' ) {
	    		if ( $deposit_type === 'percent' ) {
	    			$line_total = ( $line_total * $value_deposit ) / 100;
	    		} else {
	    			$line_total = $value_deposit;
	    		}
	    	}
	    }
		
		if ( $total['line_total'] <= 0 ) {
			echo 0;	
		} else {
			if ( isset( $total['amount_insurance'] ) && $total['amount_insurance'] > 0 ) {
				$amount_total = $total['line_total'] - $total['amount_insurance'];
				if ( $amount_total > 0 ) {
					$total['line_total'] = ovabrw_wc_price( apply_filters( 'ovabrw_ajax_total_filter', $line_total, $id ) );
					echo json_encode($total);
				} else {
					echo 0;
				}
			} else {
				$total['line_total'] = ovabrw_wc_price( apply_filters( 'ovabrw_ajax_total_filter', $line_total, $id ) );
				echo json_encode($total);
			}
		}

		wp_die();
	}
}

add_action( 'wp_ajax_ovabrw_get_tax_in_cat', 'ovabrw_get_tax_in_cat', 10, 0 );
add_action( 'wp_ajax_nopriv_ovabrw_get_tax_in_cat', 'ovabrw_get_tax_in_cat', 10, 0 );
if ( ! function_exists( 'ovabrw_get_tax_in_cat' ) ) {
	function ovabrw_get_tax_in_cat(){
		$cat_val 			= isset( $_POST['cat_val'] ) ?  $_POST['cat_val'] : '';
		$list_tax_values 	= array();
		$get_term 			= get_term_by( 'slug', $cat_val, 'product_cat' );
		$term_id 			= $get_term->term_id;
		$ovabrw_custom_tax 	= get_term_meta($term_id, 'ovabrw_custom_tax', true);
				
		if ( $ovabrw_custom_tax ) {
			foreach( $ovabrw_custom_tax as $key => $value ) {
				if ( !in_array( $value, $list_tax_values ) ) {
					if ( $value ) {
						array_push( $list_tax_values, $value);		
					}
				}
			}
		}

		echo implode(",", $list_tax_values ); 
		wp_die();
	}
}

add_action( 'wp_ajax_ovabrw_get_dropoff_date_transportation', 'ovabrw_get_dropoff_date_transportation' );
add_action( 'wp_ajax_nopriv_ovabrw_get_dropoff_date_transportation', 'ovabrw_get_dropoff_date_transportation' );
if ( ! function_exists( 'ovabrw_get_dropoff_date_transportation' ) ) {
	function ovabrw_get_dropoff_date_transportation() {
		$date_format 	= ovabrw_get_date_format();
	    $time_format 	= ovabrw_get_time_format_php();
		$id_product 	= isset($_POST['id_product']) ? sanitize_text_field( $_POST['id_product'] ) : '';
		$pickup_date 	= isset($_POST['pickup_date']) ? strtotime(sanitize_text_field( $_POST['pickup_date'] )) : '';
		$pickup_loc 	= isset($_POST['pickup_loc']) ? sanitize_text_field( $_POST['pickup_loc'] ) : '';
		$dropoff_loc 	= isset($_POST['dropoff_loc']) ? sanitize_text_field( $_POST['dropoff_loc'] ) : '';
		$time_dropoff 	= ovabrw_get_time_by_pick_up_off_loc_transport( $id_product, $pickup_loc, $dropoff_loc );

	    // Get Second
	    $time_dropoff_seconds = 60 * $time_dropoff;
	    
	    $dropoff_date_timestamp =  $pickup_date + $time_dropoff_seconds;
	    $pickoff_date = date( $date_format . ' ' . $time_format, $dropoff_date_timestamp );
		
		if ( $pickoff_date && $pickup_loc && $dropoff_loc ) {
			$data = [
				'error' => false,
				'dropoff' => $pickoff_date
			];
		} else {
			$data = [
				'error' => true,
				'start_date' => 'empty',
				'message' => esc_html__("Please insert Pick-up, Drop-off Location", 'ova-brw'),
			];
		}
		
		echo json_encode( $data );
		wp_die();
	}
}

/**
 * ajax search map
 */
add_action( 'wp_ajax_ovabrw_search_map', 'ovabrw_search_map' );
add_action( 'wp_ajax_nopriv_ovabrw_search_map', 'ovabrw_search_map' );
if ( ! function_exists( 'ovabrw_search_map' ) ) {
	function ovabrw_search_map() {
		$data = $_POST;
		$map_lat 	= isset( $data['map_lat'] ) ? floatval( $data['map_lat'] ) 	: '';
		$map_lng 	= isset( $data['map_lng'] ) ? floatval( $data['map_lng'] ) 	: '';
		$radius 	= isset( $data['radius'] ) 	? floatval( $data['radius'] ) 	: '50';

		/***** Query Radius *****/
		$args_query_radius = array(
			'post_type' 		=> 'product',
			'posts_per_page' 	=> -1,
		);

		$the_query 	= new WP_Query( $args_query_radius);
		$post_in 	= $arr_distance = array();
		$posts 		= $the_query->get_posts();

		if ( $map_lat != '' || $map_lng != '' ) {
			foreach ( $posts as $post ) {
				/* Latitude Longitude Search */
				$lat_search = deg2rad($map_lat);
				$lng_search = deg2rad($map_lng);

				/* Latitude Longitude Post */
				$lat_post = deg2rad( get_post_meta( $post->ID, 'ovabrw_latitude', true ));
				$lng_post = deg2rad( get_post_meta( $post->ID, 'ovabrw_longitude', true ));

				$lat_delta = $lat_post - $lat_search;
				$lon_delta = $lng_post - $lng_search;

				// $angle = 2 * asin(sqrt(pow(sin($lat_delta / 2), 2) + cos($lat_search) * cos($lat_post) * pow(sin($lon_delta / 2), 2)));
				$angle = acos(sin($lat_search) * sin($lat_post) + cos($lat_search) * cos($lat_post) * cos($lng_search - $lng_post));

				/* 6371 = the earth's radius in km */
				/* 3959 = the earth's radius in mi */
				$distance =  6371 * $angle;

				if ( $distance <= $radius || !$map_lat ) {
					array_push( $arr_distance, $distance );
					array_push( $post_in, $post->ID );
				}
			}

			wp_reset_postdata();
			array_multisort($arr_distance, $post_in);
		} else {
			foreach( $posts as $post )  {
				array_push( $post_in, $post->ID );
			}
		}

		if ( $map_lat && ! $post_in ) {
			$post_in = array('');
		}
		/***** End Query Radius *****/

		$sort 			= isset( $data['sort'] ) 		? sanitize_text_field( $data['sort'] ) 			: '';
		$order 			= isset( $data['order'] ) 		? sanitize_text_field( $data['order'] ) 		: '';
		$orderby 		= isset( $data['orderby'] ) 	? sanitize_text_field( $data['orderby'] ) 		: '';
		$per_page 		= isset( $data['per_page'] ) 	? sanitize_text_field( $data['per_page'] ) 		: '';
		$paged 			= isset( $data['paged'] ) 		? (int)$data['paged']  							: 1;
		$name 			= isset( $data['name'] ) 		? sanitize_text_field( $data['name'] ) 			: '';
	    $pickup_loc 	= isset( $data['pickup_loc'] ) 	? sanitize_text_field( $data['pickup_loc'] ) 	: '';
	    $pickoff_loc 	= isset( $data['dropoff_loc'] ) ? sanitize_text_field( $data['dropoff_loc'] ) 	: '';
	    $pickup_date 	= isset( $data['start_date'] )  ? strtotime( $data['start_date'] ) 				: '';
	    $pickoff_date 	= isset( $data['end_date'] ) 	? strtotime( $data['end_date'] ) 				: '';
	    $category 		= isset( $data['cat'] ) 		? sanitize_text_field( $data['cat'] ) 			: '';
	    $card 			= isset( $data['card'] ) 		? sanitize_text_field( $data['card'] ) 			: '';
	    $column 		= isset( $data['column'] ) 		? sanitize_text_field( $data['column'] ) 		: '';
	    $attribute 		= isset( $data['attribute'] ) 	? sanitize_text_field( $data['attribute'] ) 	: '';
	    $attr_value 	= isset( $data['attr_value'] ) 	? sanitize_text_field( $data['attr_value'] ) 	: '';
	    $tags 			= isset( $data['tags'] ) 		? sanitize_text_field( $data['tags'] ) 			: '';
	    $taxonomies 	= isset( $data['taxonomies'] ) 	? sanitize_text_field( $data['taxonomies'] )	: '';

	    $taxonomies 	= str_replace( '\\', '',  $taxonomies);
		if ( $taxonomies ) {
			$taxonomies = json_decode( $taxonomies, true );
		}

	    $args_base = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => -1
		);

	    // sort
	    $args_order 	= ! empty( $order ) 	? array( 'order' => $order ) 		: array( 'order' => 'DESC' );
	    $args_orderby 	= ! empty( $orderby ) 	? array( 'orderby' => $orderby ) 	: array( 'orderby' => 'title' );

	    switch ( $sort ) {
			case 'date-desc':
			$args_orderby =  array( 'orderby' => 'date' );
			break;

			case 'date-asc':
			$args_orderby 	= array( 'orderby' => 'date' );
			$args_order 	= array( 'order' => 'ASC' );
			break;

			case 'near':
			$args_orderby 	= array( 'orderby' => 'post__in');
			$args_order 	= array( 'order' => 'ASC' );
			break;

			case 'a-z':
			$args_orderby 	= array( 'orderby' => 'title');
			$args_order 	= array( 'order' => 'ASC' );
			break;

			case 'z-a':
			$args_orderby 	= array( 'orderby' => 'title');
			break;

			default:
			break;
		}

		$args_basic 	= array_merge_recursive( $args_base, $args_order, $args_orderby );
		$args_radius 	= $args_name = $args_taxonomy = $args_tax_attr = $item_ids = array();

		// Query Result
		if ( $post_in ) {
			$args_radius = array( 'post__in' => $post_in );
		}

		// Query Name
		if ( $name ){
			$args_name = array( 's' => $name );
		}

		// Query Categories
		if ( $category ){
			$args_tax_attr[] = [
	            'taxonomy' 	=> 'product_cat',
	            'field' 	=> 'slug',
	            'terms' 	=> $category
	        ];
		}

		// Query Attribute
		if ( $attribute ) {
	        $args_tax_attr[] = [
	            'taxonomy' 	=> 'pa_' . $attribute,
	            'field' 	=> 'slug',
	            'terms' 	=> [$attr_value],
	            'operator'  => 'IN',
	        ];
	    }

	    // Query Tags
		if ( $tags ) {
			$args_tax_attr[] = [
	            'taxonomy' 	=> 'product_tag',
	            'field' 	=> 'name',
	            'terms' 	=> $tags
	        ];
		}

		// Query taxonomy custom
	    if ( $taxonomies && is_array( $taxonomies ) ) {
	    	foreach ( $taxonomies as $slug => $value) {
	    		$taxo_name = isset( $data[$slug] ) ? sanitize_text_field( $data[$slug] ) : '';
	    		if ( $taxo_name ) {
	    			$args_tax_attr[] = [
			            'taxonomy' 	=> $slug,
			            'field' 	=> 'name',
			            'terms' 	=> $taxo_name
			        ];
	    		}
	    	}
	    }

		// Query taxonomy
		if ( ! empty( $args_tax_attr )) {
	        $args_taxonomy = array(
	            'tax_query' => array(
	                'relation'  => 'AND',
	                $args_tax_attr
	            )
	        );
	    }

		$args = array_merge_recursive( $args_basic, $args_radius, $args_name, $args_taxonomy );

		// Get All products
	    $items = new WP_Query( apply_filters( 'ovabrw_ft_items_query_search_map', $args, $data ));

	    if ( $items->have_posts() ) : while ( $items->have_posts() ) : $items->the_post();
	        // Product ID
	        $id = get_the_id();

	        // Set Pick-up, Drop-off Date again
	        $new_input_date 	= ovabrw_new_input_date( $id, $pickup_date, $pickoff_date, '', $pickup_loc, $pickoff_loc );
	        $pickup_date_new 	= $new_input_date['pickup_date_new'];
	        $pickoff_date_new 	= $new_input_date['pickoff_date_new'];

	        $ova_validate_manage_store = ova_validate_manage_store( $id, $pickup_date_new, $pickoff_date_new, $pickup_loc, $pickoff_loc, $passed = false, $validate = 'search' ) ;
	        
	        if ( $ova_validate_manage_store && $ova_validate_manage_store['status'] ){
	            array_push( $item_ids, $id );
	        }
	    endwhile; else :
	        $result = '<div class="not_found_product">'. esc_html( 'Product not found', 'ova-brw' ) .'</div>';
	    	$results_found = '<div class="results_found"><span>'. esc_html__( '0 Result Found', 'ova-brw' ) .'</span></div>';
	    endif; wp_reset_postdata();

	    if ( $item_ids ) {
	        $args_product = array(
	            'post_type' 		=> 'product',
	            'posts_per_page' 	=> $per_page,
	            'paged' 			=> $paged,
	            'post_status' 		=> 'publish',
	            'post__in' 			=> $item_ids,
	            'orderby' 			=> 'post__in',
	            'order' 			=> $order ? $order : 'DESC',
	        );

	        $products = new WP_Query( apply_filters( 'ovabrw_ft_query_search_map', $args_product, $data ));

	        // Card
	        if ( $card === 'card5' || $card === 'card6' ) $column = 'one-column';

	        ob_start();
	        ?>
	        <div class="ovabrw_product_archive <?php echo esc_attr( $column ); ?>">
				<?php
					woocommerce_product_loop_start();
					if ( $products->have_posts() ) : while ( $products->have_posts() ) : $products->the_post();
						$id 			= get_the_id();
						$product 		= wc_get_product( $id );
						$html_price 	= ovabrw_get_html_price( $id );

						if ( $html_price ) {
							$data_html_price = htmlentities( $html_price );
						} else {
							$data_html_price = '';
						}

						$lat_product 	= get_post_meta( $id, 'ovabrw_latitude', true );
						$lng_product 	= get_post_meta( $id, 'ovabrw_longitude', true );

						if ( $card ) {
							$thumbnail_type = get_option( 'ovabrw_glb_'.$card.'_thumbnail_type', 'slider' );

							ovabrw_get_template( 'modern/products/cards/ovabrw-'.$card.'.php', [ 'product_id' => $id, 'thumbnail_type' => $thumbnail_type ] );
						} else {
							wc_get_template_part( 'content', 'product' );
						}
						?>
						<input
							type="hidden"
							class="data_product"
							data-link_product="<?php echo esc_attr( get_the_permalink() ); ?>"
							data-title_product="<?php echo esc_attr( get_the_title() ); ?>"
							data-average_rating="<?php echo $product->get_average_rating(); ?>"
							data-number_comment="<?php echo get_comments_number( $id ); ?>"
							data-map_lat_product="<?php echo $lat_product; ?>"
							data-map_lng_product="<?php echo $lng_product; ?>"
							data-thumbnail_product="<?php echo wp_get_attachment_image_url( get_post_thumbnail_id() , 'thumbnail' ); ?>"
							data-html_price="<?php echo $data_html_price; ?>"
						/>
						<?php
					endwhile; else :
					?>
						<div class="not_found_product"><?php esc_html_e( 'Product not found', 'ova-brw' ); ?></div>
					<?php
					endif; wp_reset_postdata();
					woocommerce_product_loop_end();
				?>
			</div>
	        <?php
	        $total = $products->max_num_pages;

			if (  $total > 1 ): ?>
				<div class="ovabrw_pagination_ajax">
				<?php
					echo ovabrw_pagination_ajax( $products->found_posts, $products->query_vars['posts_per_page'], $paged );
				?>
				</div>
				<?php
			endif;

			$result = ob_get_contents(); 
			ob_end_clean();

			ob_start();
			?>
				<div class="results_found">
					<?php if ( $products->found_posts == 1 ): ?>
					<span>
						<?php echo sprintf( esc_html__( '%s Result Found', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
					</span>
					<?php else: ?>
					<span>
						<?php echo sprintf( esc_html__( '%s Results Found', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
					</span>
					<?php endif; ?>

					<?php if ( 1 == ceil( $products->found_posts/ $products->query_vars['posts_per_page']) && $products->have_posts() ): ?>
						<span>
							<?php echo sprintf( esc_html__( '(Showing 1-%s)', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
						</span>
					<?php elseif ( !$products->have_posts() ): ?>
						<span></span>
					<?php else: ?>
						<span>
							<?php echo sprintf( esc_html__( '(Showing 1-%s)', 'ova-brw' ), esc_html( $products->query_vars['posts_per_page'] ) ); ?>
						</span>
					<?php endif; ?>
				</div>

			<?php
			$results_found = ob_get_contents();
			ob_end_clean();

			echo json_encode( array( "result" => $result, "results_found" => $results_found ));
			wp_die();
	    } else {
	    	echo json_encode( array( "result" => $result, "results_found" => $results_found ));
	    	wp_die();
	    }
	}
}

// Product ajax filter
add_action( 'wp_ajax_ovabrw_product_ajax_filter', 'ovabrw_product_ajax_filter' );
add_action( 'wp_ajax_nopriv_ovabrw_product_ajax_filter', 'ovabrw_product_ajax_filter' );
if ( ! function_exists( 'ovabrw_product_ajax_filter' ) ) {
	function ovabrw_product_ajax_filter() {
		$term_id 		= isset( $_POST['term_id'] ) ? $_POST['term_id'] : '';
		$template 		= isset( $_POST['template'] ) ? $_POST['template'] : 'card1';
		$posts_per_page = isset( $_POST['posts_per_page'] ) ? $_POST['posts_per_page'] : 6;
		$orderby 		= isset( $_POST['orderby'] ) ? $_POST['orderby'] : 'ID';
		$order 			= isset( $_POST['order'] ) ? $_POST['order'] : 'DESC';
		$pagination 	= isset( $_POST['pagination'] ) ? $_POST['pagination'] : '';
		$paged 			= isset( $_POST['paged'] ) ? $_POST['paged'] : 1;

		$products = ovabrw_get_product_ajax_filter( array(
			'paged' 			=> $paged,
			'posts_per_page' 	=> $posts_per_page,
			'orderby' 			=> $orderby,
			'order' 			=> $order,
			'term_id' 			=> $term_id,
		));

		ob_start();
		if ( $products->have_posts() ) : while ( $products->have_posts() ) : $products->the_post();
			if ( $template ) {
				?>
				<li class="item">
					<?php ovabrw_get_template( 'modern/products/cards/ovabrw-'.$template.'.php' ); ?>
				</li>
				<?php
			} else {
				wc_get_template_part( 'content', 'product' );
			}
		endwhile; else :
		?>
			<div class="not-found">
				<?php esc_html_e( 'Product not found', 'ova-brw' ); ?>
			</div>
		<?php
		endif; wp_reset_postdata();

		$result = ob_get_contents();
		ob_end_clean();

		ob_start();
		if ( 'yes' === $pagination ) {
			$pages 		= $products->max_num_pages;
			$limit 		= $products->query_vars['posts_per_page'];
			$current 	= $paged;

			if ( $pages > 1 ):
				for ( $i = 1; $i <= $pages; $i++ ): ?>
				<li>
					<span
						class="page-numbers<?php echo $i == $current ? ' current' : ''; ?>"
						data-paged="<?php echo esc_attr( $i ); ?>">
						<?php echo esc_html( $i ); ?>
					</span>
				</li>
			<?php endfor; endif;
		}

		$pagination = ob_get_contents();
		ob_end_clean();

		echo json_encode( array( 'result' => $result, 'pagination' => $pagination ) );
		wp_die();
	}
}

// reCAPTCHA
add_action( 'wp_ajax_ovabrw_verify_reCAPTCHA', 'ovabrw_verify_reCAPTCHA' );
add_action( 'wp_ajax_nopriv_ovabrw_verify_reCAPTCHA', 'ovabrw_verify_reCAPTCHA' );
if ( ! function_exists( 'ovabrw_verify_reCAPTCHA' ) ) {
	function ovabrw_verify_reCAPTCHA() {
		$mess = esc_html__( 'reCAPTCHA response error, please try again later', 'ova-brw' );

		if ( ! isset( $_POST ) && empty( $_POST ) ) {
			echo $mess;
			wp_die();
		}

		$token = isset( $_POST['token'] ) ? $_POST['token'] : '';

		if ( ! $token ) {
			echo $mess;
			wp_die();
		} else {
			$secret_key = ovabrw_get_recaptcha_secret_key();

			$url 	= 'https://www.google.com/recaptcha/api/siteverify';
			$data 	= [
			    'secret' 	=> $secret_key,
			    'response' 	=> $token,
			];

			$options = [
			    'http' => [
			        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
			        'method' => 'POST',
			        'content' => http_build_query($data),
			    ],
			];

			$context 	= stream_context_create( $options );
			$result 	= file_get_contents( $url, false, $context );
			$result 	= json_decode( $result, true );

			if ( $result['success'] ) {
			    $mess = '';
			} else {
			    // reCAPTCHA verification failed
			    $mess = esc_html__( 'reCAPTCHA verification failed!', 'ova-brw' );
			}
		}

		echo $mess;
		wp_die();
	}
}
?>