<?php
/**
 * @version 1.0
 * @package Costs functions
 * @category Costs
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2016-06-26
 */
/*
This is COMMERCIAL SCRIPT
We are not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// ---------------------------------------------------------------------------------------------------------------------
// Calc   C O S T   of the booking
// ---------------------------------------------------------------------------------------------------------------------


// TODO: Need refactor
/**
 * Cost Calculate - based on Dates | Times including: - RATES - VALUATION_DAYS - ADVANCED_COST - EARLY_LATE_BOOKING_APPLY - COUPONS_DISCOUNT_APPLY
 *
 * Example: wpbc_calc__booking_cost( array( 'resource_id'=>2, 'str_dates__dd_mm_yyyy'=> '23.11.2023,24.11.2023,25.11.2023', 'times_array'=> array( array('14', '00', '02'), array('12', '00', '02')), 'form_data' => 'select-multiple^rangetime2[]^18:00 - 20:00~checkbox^fee2[]^true~text^name2^John~text^secondname2^Smith~email^email2^john.smith@server.com~select-one^visitors2^2~select-one^children2^0~textarea^details2^~text^starttime2^14:00~text^endtime2^12:00' ) );
 * Note 1: This function require correct definition  of  $_POST['booking_form_type']  for works of Advanced costs (relative custom  forms)!
 * Note 2: It makes only  calculation,  without updates of DB.
 *
 * @param $params [
 *                    'resource_id'           => 1                                                                      // '2'
 *    				, 'str_dates__dd_mm_yyyy' => ''                                                                     // '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023'
 *	                , 'times_array'           => [  ['00', '00', '01'], ['24', '00', '02']  ]
 *	                , 'form_data'             => ''                                                                     // 'text^selected_short_timedates_hint4^06/11/2018 14:00...'
 *					, 'is_discount_calculate' => true
 *					, 'is_only_original_cost' => false
 *               ]
 *
 * @return float 		216.45
 */
function wpbc_calc__booking_cost( $params ){

    $defaults = array(
                      'resource_id'           => 1                                                                      // '2'
    				, 'str_dates__dd_mm_yyyy' => ''                                                                     // '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023'
	                , 'times_array'           => array( array( '00', '00', '01' ), array( '24', '00', '02' ) )
	                , 'form_data'             => ''                                                                     // 'text^selected_short_timedates_hint4^06/11/2018 14:00...'
					, 'is_discount_calculate' => true
					, 'is_only_original_cost' => false
    			);
    $params   = wp_parse_args( $params, $defaults );


	// Times -----------------------------------------------------------------------------------------------------------
	$times_array = $params['times_array'];
	if (
		    ( 'Off' == get_bk_option( 'booking_is_time_apply_to_cost' ) )
		 && ( 'hour' != get_bk_option( 'booking_paypal_price_period' ) )
	){
		$times_array = array( array( '00', '00', '01' ), array( '24', '00', '02' ) );
	}
	if ( array( '00', '00', '00' ) == $times_array[1] ) {
		$times_array[1] = array( '24', '00', '02' );                                                                    //FixIn: 8.7.2.3
	}

	// Dates:  '27.11.2023,28.11.2023,29.11.2023,30.11.2023'  -> 	["27.11.2023", "28.11.2023", "29.11.2023", "30.11.2023"]
	$dates_arr__d_m_y = wpbc_convert_dates_arr__yyyy_mm_dd__to__dd_mm_yyyy(
																			explode( ',',
																					wpbc_convert_dates_str__dd_mm_yyyy__to__yyyy_mm_dd( $params['str_dates__dd_mm_yyyy'] )
																			)
																		);
	$days_count = count( $dates_arr__d_m_y );


	$resource__date_cost = wpbc_db__get_resource__cost( $params['resource_id'] );
    if (
			( get_bk_option( 'booking_recurrent_time' ) !== 'On')
		||	(	   ( $times_array[ 0 ][ 0 ] == '00' )
				&& ( $times_array[ 0 ][ 1 ] == '00' )
				&& ( $times_array[ 1 ][ 0 ] == '00' )
				&& ( $times_array[ 1 ][ 1 ] == '00' )
			)
		||  (	   ( get_bk_option( 'booking_recurrent_time' ) == 'On' )									            //FixIn:7.1.2.11
				&& ( get_bk_option( 'booking_paypal_price_period' ) == 'day' )
				&& ( get_bk_option( 'booking_is_time_apply_to_cost' ) != 'On' )
			)
	){
		if ( ! class_exists('wpdev_bk_biz_m') ) {

            $summ = wpbc_get_cost_for_period(
                                                get_bk_option( 'booking_paypal_price_period' ),
                                                wpbc_db__get_resource__cost( $params['resource_id'] ) ,
                                                $dates_arr__d_m_y,
                                                $times_array
                );

        } else  {

			$resource__date_cost = apply_bk_filter( 'wpdev_season_rates', $resource__date_cost, $dates_arr__d_m_y, $params['resource_id'], $times_array, $params['form_data'] );  // Its return array with day costs

			if ( is_array( $resource__date_cost ) ) {
				$summ = 0.0;
				for ( $ki = 0; $ki < count( $resource__date_cost ); $ki ++ ) {
					$summ += $resource__date_cost[ $ki ];
				}
			} else {
				$summ = ( 1 * $resource__date_cost * $days_count );
			}

        }

    } else { // Recurrent time in everyday calculation

        $final_summ = 0;
        $temp_days = $dates_arr__d_m_y;
        $temp_paypal_dayprice = $resource__date_cost;

        foreach ( $temp_days as $day_numb => $dates_arr__d_m_y ) {  // lOOP EACH DAY

            $dates_arr__d_m_y = array($dates_arr__d_m_y);
            $resource__date_cost = $temp_paypal_dayprice;

            if ( ! class_exists('wpdev_bk_biz_m') ) {

                $summ = wpbc_get_cost_for_period(
                                                    get_bk_option( 'booking_paypal_price_period' ),
                                                    wpbc_db__get_resource__cost( $params['resource_id'] ) ,
                                                    $dates_arr__d_m_y,
                                                    $times_array
                    );

                if (get_bk_option( 'booking_paypal_price_period' ) == 'fixed')          $final_summ = 0; // if we are have fixed cost calculation so we will not gathering all costs but get just last one.

                // Set first day as 0, if we have true all these conditions
                if (   (get_bk_option( 'booking_paypal_price_period' ) == 'night')
                    && (get_bk_option( 'booking_is_time_apply_to_cost' ) != 'On' )
                    && ( count($temp_days)>1 ) && ($final_summ == 0 ) && ($summ > 0) )
                {
                    $final_summ = -1*$summ + 0.000001;  // last number is need for definition its only for first day and make its little more than 0, then at final cost there is ROUND to the 2 nd number after comma.
                }


            } else  {

	            $resource__date_cost = apply_bk_filter( 'wpdev_season_rates', $resource__date_cost, $dates_arr__d_m_y, $params['resource_id'], $times_array, $params['form_data'] );  // Its return array with day costs

	            if ( is_array( $resource__date_cost ) ) {
                    $summ = 0.0;
                    for ($ki = 0; $ki < count($resource__date_cost); $ki++) { $summ += $resource__date_cost[$ki]; }
                } else {
	                $summ = ( 1 * $resource__date_cost * $days_count );
                }

            }

            $final_summ += $summ;
            $summ = 0.0;
        }

        $resource__date_cost = $temp_paypal_dayprice;
        $dates_arr__d_m_y = $temp_days;
        $summ = $final_summ;
    }

				if ( get_bk_option( 'booking_paypal_price_period' ) == 'fixed' ) {
					if ( is_array( $resource__date_cost ) ) {
						$summ = $resource__date_cost[0];
					} else {
						$summ = $resource__date_cost;
					}
				}


                $summ_original_without_additional = $summ ;

                if ($params['is_only_original_cost']) {
                    if ($params['is_discount_calculate']) {
                        $summ_original_without_additional = apply_bk_filter('coupons_discount_apply', $summ_original_without_additional, $params['form_data'], $params['resource_id'] ); // Apply discounts coupons
                    }
                    return add_coupon_discount($_POST['coupon'], $params['resource_id'], $summ_original_without_additional);
                }

	    		//FixIn: 8.8.2.9
                if ( $summ >= 0 ) {                                              // Apply additional  cost,  only  if the booking cost > 0

	                //FixIn: 8.7.2.2
                	$is_booking_coupon_code_directly_to_days = get_bk_option( 'booking_coupon_code_directly_to_days' );

                	if ( 'On' == $is_booking_coupon_code_directly_to_days ) {
						if ( $params['is_discount_calculate'] ) {
							$summ = apply_bk_filter( 'coupons_discount_apply', $summ, $params['form_data'], $params['resource_id'] ); 				// Apply discounts based on coupons
						}
					}

	                $summ = apply_bk_filter('advanced_cost_apply', $summ , $params['form_data'], $params['resource_id'], $dates_arr__d_m_y );    	// Apply advanced cost managemnt

					$summ = apply_bk_filter('early_late_booking_apply', $summ , $params['form_data'], $params['resource_id'], $dates_arr__d_m_y );   // Apply early_late_booking		//FixIn: 8.2.1.17

					if ( 'On' != $is_booking_coupon_code_directly_to_days ) {
						if ( $params['is_discount_calculate'] ) {
							$summ = apply_bk_filter( 'coupons_discount_apply', $summ, $params['form_data'], $params['resource_id'] );                // Apply discounts based on coupons
						}
					}
                }

                return add_coupon_discount($_POST['coupon'], $params['resource_id'], $summ);
}


	// TODO: Need refactor
	// Calculate the cost for specific days(times) based on base_cost for specific period
	function wpbc_get_cost_for_period( $period, $base_cost, $days, $times = array( array( '00', '00', '01' ), array( '24', '00', '02' ) ) ){


		if ( $times[1] == array( '23', '59', '02' ) ) {
			$times[1] = array( '24', '00', '00' );
		}
		$days_array  = $days;
		$times_array = $times;
		if ( count( $days_array ) == 1 ) {
			$d_day = $days_array[0];
			if ( ! empty( $d_day ) ) {
				$d_day            = explode( '.', $d_day );
				$day              = ( $d_day[0] + 0 );
				$month            = ( $d_day[1] + 0 );
				$year             = ( $d_day[2] + 0 );
				$start_time_in_ms = mktime( $times_array[0][0], $times_array[0][1], $times_array[0][2], $month, $day, $year );
				$end_time_in_ms   = mktime( $times_array[1][0], $times_array[1][1], $times_array[1][2], $month, $day, $year );
				if ( ( $end_time_in_ms - $start_time_in_ms ) < 0 ) {
					//We need to  add one extra day,  because the end time outside of 24:00 already
					$days[] = date( 'd.m.Y', mktime( 0, 0, 0, $month, ( $day + 1 ), $year ) );
				}
			}
		}



		$fin_cost = 0;

		if ( 'On' == get_bk_option( 'booking_is_time_apply_to_cost' ) ) {                           // Make some corrections if TIME IS APPLY TO THE COST
			if ( $period == 'day' ) {
				$period    = 'hour';
				$base_cost = $base_cost / 24;
			} else if ( $period == 'night' ) {
				$period    = 'hour';
				$base_cost = $base_cost / 24;
			} else if ( $period == 'hour' ) {                             // Skip here evrything fine
			} else {                                                    // Skip here evrything fine
			}
		}

		if ( $period == 'day' ) {

			$fin_cost = count( $days ) * $base_cost;

		} else if ( $period == 'night' ) {

			$night_count = ( count( $days ) > 1 ) ? ( count( $days ) - 1 ) : 1;
			$fin_cost    = $night_count * $base_cost;

		} else if ( $period == 'hour' ) {

			$start_time = $times[0];
			$end_time   = $times[1];
			if ( $end_time == array( '00', '00', '00' ) ) {
				$end_time = array( '24', '00', '00' );
			}

			if ( count( $days ) <= 1 ) {

				$m_dif    = ( $end_time[0] * 60 + intval( $end_time[1] ) ) - ( $start_time[0] * 60 + intval( $start_time[1] ) );
				$fin_cost = $m_dif * $base_cost / 60;

			} else {
				$full_days_count = count( $days ) - 2;

				$full_days_cost = $full_days_count * 24 * 60 * $base_cost / 60;
				$check_in_cost  = ( 24 * 60 - ( $start_time[0] * 60 + intval( $start_time[1] ) ) ) * $base_cost / 60;
				$check_out_cost = ( $end_time[0] * 60 + intval( $end_time[1] ) ) * $base_cost / 60;
				$fin_cost       = $check_in_cost + $full_days_cost + $check_out_cost;
			}

		} else { // Fixed

			$fin_cost = $base_cost;
		}

		return $fin_cost;
	}


	//Done.
	/**
	 * Get cost of booking resource from DB
	 *
	 * @param $resource_id
	 *
	 * @return int|string
	 */
	function wpbc_db__get_resource__cost( $resource_id ) {

		global $wpdb;
		// Can be value (string),  or null if not found
		$cost = $wpdb->get_var( $wpdb->prepare( "SELECT cost FROM {$wpdb->prefix}bookingtypes  WHERE booking_type_id = %d", $resource_id ) );

		return ( ! empty( $cost ) ) ? $cost : 0;
	}


	/**
	 * Get cost of booking from DB
	 *
	 * @param $resource_id
	 *
	 * @return string
	 */
	function wpbc_db__get_booking__cost( $booking_id ) {

		global $wpdb;
		// Can be value (string),  or null if not found
		$cost = $wpdb->get_var( $wpdb->prepare( "SELECT cost FROM {$wpdb->prefix}booking WHERE booking_id = %d", $booking_id ) );

		return ( ! empty( $cost ) ) ? $cost : '';
	}


	/**
	 * Update cost of the booking in DB
	 *
	 * @param $booking_id
	 * @param $cost
	 *
	 * @return true|WP_Error
	 */
	function wpbc_db__update_booking_cost( $booking_id, $cost ) {

		global $wpdb;

		$cost = floatval( $cost );

		$update_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}booking AS bk SET bk.cost=%f WHERE bk.booking_id= %d ", $cost, $booking_id );

		if ( false === $wpdb->query( $update_sql ) ) {
			return new WP_Error( 'wpbc_db__update_booking_cost', 'Error. UPDATE booking cost in DB.' . '  FILE:' . __FILE__ . ' LINE:' . __LINE__ . ' SQL:' . $update_sql );
		}

		return  true;
	}


	/**
	 * Update 'pay_status' of the booking in DB
	 *
	 * @param $booking_id
	 * @param $pay_status
	 *
	 * @return true|WP_Error
	 */
	function wpbc_db__update_booking_payment_status( $booking_id, $pay_status ) {

		global $wpdb;

		$update_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}booking AS bk SET bk.pay_status=%s WHERE bk.booking_id= %d", $pay_status, $booking_id );

		if ( false === $wpdb->query( $update_sql ) ) {
			return new WP_Error( 'wpbc_db__update_booking_payment_status', 'Error. UPDATE booking pay_status in DB.' . '  FILE:' . __FILE__ . ' LINE:' . __LINE__ . ' SQL:' . $update_sql );
		}

		return  true;
	}


// ---------------------------------------------------------------------------------------------------------------------
// Output of Payment Gateways
// ---------------------------------------------------------------------------------------------------------------------
/**
 * Output of Payment Gateways
 *
 * @param $params   [
 *							  'booking_id'            => 0          	                            //   REQUIRED  --    '2'
 *							, 'resource_id'           => 1           	                            //   REQUIRED  --    '2'
 *							, 'str_dates__dd_mm_yyyy' => ''                                         //   REQUIRED  --    '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023'
 *							, 'times_array' 	      => array( array(), array() )				    //   REQUIRED  --    [   [ '00', '00', '01' ],  [ '24', '00', '02' ]  ]
 *							, 'form_data'             => ''     		 	                        //   REQUIRED  --    'text^selected_short_timedates_hint4^06/11/2018 14:00...'
 *                  		, 'cost__deposit__arr'    => array()									//   REQUIRED  --    [ 'total_cost'=>..., 'deposit_cost'=> ...]
 *                  ]
 *
 * @return array $output_arr			and echo  payment form
 *
 * Example:
			$output_arr = wpbc_get__payment_gateways__output_arr( array(
							  'booking_id'            => 0          	                            //   REQUIRED  --    '2'
							, 'resource_id'           => 1           	                            //   REQUIRED  --    '2'
							, 'str_dates__dd_mm_yyyy' => ''                                         //   REQUIRED  --    '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023'
							, 'times_array' 	      => array( array(), array() )				    //   REQUIRED  --    [   [ '00', '00', '01' ],  [ '24', '00', '02' ]  ]
							, 'form_data'             => ''     		 	                        //   REQUIRED  --    'text^selected_short_timedates_hint4^06/11/2018 14:00...'

					 ));
 */
function wpbc_get__payment_gateways__output_arr( $params ) {

	$defaults = array(		  'booking_id'            => 0          	                            //   REQUIRED  --    '2'
							, 'resource_id'           => 1           	                            //   REQUIRED  --    '2'
							, 'str_dates__dd_mm_yyyy' => ''                                         //   REQUIRED  --    '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023'
							, 'times_array' 	      => array( array(), array() )				    //   REQUIRED  --    [   [ '00', '00', '01' ],  [ '24', '00', '02' ]  ]
							, 'form_data'             => ''     		 	                        //   REQUIRED  --    'text^selected_short_timedates_hint4^06/11/2018 14:00...'
							, 'cost__deposit__arr'    => array()									//   REQUIRED  --    [ 'total_cost'=>..., 'deposit_cost'=> ...]
					 );
	$params   = wp_parse_args( $params, $defaults );

	$cost__deposit__arr = $params['cost__deposit__arr'];

	$is_deposit = ( $cost__deposit__arr['total_cost'] != $cost__deposit__arr['deposit_cost'] );

	$payment_rows = array();
	if ( $is_deposit ) {

		if ( get_bk_option( 'booking_show_deposit_and_total_payment' ) == 'On' ) {
			$payment_rows[] = array(   'cost_in_gateway' => $cost__deposit__arr['total_cost'],    'is_deposit' => false );
		}
		$payment_rows[] = array(   'cost_in_gateway' => $cost__deposit__arr['deposit_cost'],      'is_deposit' => true  );

	} else {

		$payment_rows[] = array(   'cost_in_gateway' => $cost__deposit__arr['total_cost'],        'is_deposit' => false );
	}

	//TODO: Additionally here have to  be definition  for EXTRA CALENDARS, as well 'additional_calendars'			    2023-10-20

	// <editor-fold     defaultstate="collapsed"                        desc="  ==  Update bk.pay_status in DB   <- required before showing payment forms,  for using every time new nonce  ==  "  >
	$p_nonce = microtime( true ) * 100;

	$update_result = wpbc_db__update_booking_payment_status( $params['booking_id'], $p_nonce );

	if ( is_wp_error( $update_result ) ) {              																// E R R O R
		wp_die( $update_result );
	}
	// </editor-fold>

	$gateways_arr = array();
	foreach ( $payment_rows as $payment_row ) {

		if ( empty( $payment_row['cost_in_gateway'] ) ) {
			continue;   // skip  if cost = 0
		}

		// Get Payment Gateway forms
		$gateways_arr[] = wpbc_get_gateway_forms( array (
													  'booking_id'           => $params['booking_id']                   // 9
													, 'cost'                 => $payment_row['cost_in_gateway']         // 75.10
													, 'resource_id'          => $params['resource_id']                  // 4
													, 'form'                 => $params['form_data']                    // select-one^rangetime4^10:00 - 12:00~text^name4...
													, 'nonce' 				 =>	$p_nonce                                // nonce for identification of payment in response from PayPal IPN,  and some other gateways
													, 'is_deposit'           => $payment_row['is_deposit']              // true | false
													, 'booking_form_type'    => isset( $_POST['booking_form_type'] ) ? $_POST['booking_form_type'] : 'standard'  // If we are using custom booking form during creation  new booking,  so  transfer this parameter, for calculation  additional  cost  in biz_m version
										));
	}


	$output_arr = array(
						'gateway_rows'    => array(),
						'booking_summary' => ''
					);
	foreach ( $gateways_arr as $gateway_output ) {

		if ( ! empty( $gateway_output['booking_summary'] ) ) {
			$output_arr['booking_summary'] = $gateway_output['booking_summary'];
			unset( $gateway_output['booking_summary'] );
		}

		$output_arr['gateway_rows'][] = $gateway_output;
	}

	// -----------------------------------------------------------------------------------------------------------------

	// Update Cost in DB to Deposit amount
	if ( $is_deposit ) {
		wpbc_db__update_booking_cost( $params['booking_id'], $cost__deposit__arr['deposit_cost'] );

		$is_add_timezone_offset = true;             // Log the cost  info.
		$booking_note = wpbc_date_localized( gmdate( 'Y-m-d H:i:s' ), '[Y-m-d H:i]', $is_add_timezone_offset )
		                . ' ' . __( 'Automatically calculated deposit cost', 'booking' )
		                . ' ' . $cost__deposit__arr['deposit_cost']  . "\n";
		make_bk_action( 'wpdev_make_update_of_remark',  $params['booking_id'], $booking_note, true );
	}

	$is_turned_off = apply_bk_filter('is_all_payment_forms_off', true);
	if ( $is_turned_off ) {
		$output_arr['gateway_rows'] = array();
	}

	make_bk_action('wpbc_set_coupon_inactive', $params['booking_id'], $params['resource_id'], $params['str_dates__dd_mm_yyyy'], $params['times_array'] , $params['form_data'] );

	if (
  		   ( get_bk_option( 'booking_payment_form_in_request_only' ) == 'On' )
		&& ( empty( $params['booking_payment_form_in_request_only'] )        )
	) {                                            //FixIn: 8.8.1.9
		$output_arr['gateway_rows'] = array();
	}

	// -----------------------------------------------------------------------------------------------------------------

	$output_arr['booking_summary'] = wpbc_escaping_text_for_output( $output_arr['booking_summary'] );
	foreach ( $output_arr['gateway_rows'] as $row_num => $gateway_data ) {
		foreach ( $output_arr['gateway_rows'][ $row_num ]['gateways_arr'] as $gtw_num => $gtw_data ) {
			$output_arr['gateway_rows'][ $row_num ]['gateways_arr'][ $gtw_num ]['output'] = wpbc_escaping_text_for_output( $output_arr['gateway_rows'][ $row_num ]['gateways_arr'][ $gtw_num ]['output'] );
		}
	}
	// -------------------------------------------------------------------------------------------------------------


	// -------------------------------------------------------------------------------------------------------------
	// Currency Symbols Replacing
	// -------------------------------------------------------------------------------------------------------------
	$original_symbols = array( 'CURRENCY_SYMBOL' );
	make_bk_action( 'check_multiuser_params_for_client_side', $params['resource_id'] );                        		// MU        - Get correct currency of specific user at  front-end
	$temporary_symbols = array( wpbc_get_currency_symbol() );
	make_bk_action( 'finish_check_multiuser_params_for_client_side', $params['resource_id'] );                    	// MU

	foreach ( $output_arr['gateway_rows'] as $row_num => $gateway_data ) {
		foreach ( $output_arr['gateway_rows'][ $row_num ]['gateways_arr'] as $gtw_num => $gtw_data ) {
			$output_arr['gateway_rows'][ $row_num ]['gateways_arr'][ $gtw_num ]['output'] = str_replace( $original_symbols, $temporary_symbols, $output_arr['gateway_rows'][ $row_num ]['gateways_arr'][ $gtw_num ]['output'] );
		}
	}
	$output_arr['booking_summary'] = str_replace( $original_symbols, $temporary_symbols, $output_arr['booking_summary'] );

	// -------------------------------------------------------------------------------------------------------------

	$output_arr['booking_summary'] = wpbc_prepare_text_for_html( $output_arr['booking_summary'] );

	return $output_arr;
}



// ---------------------------------------------------------------------------------------------------------------------
// Cost Format
// ---------------------------------------------------------------------------------------------------------------------
/**
 * Get formated cost with  currency symbol
 *
 * @param $cost
 * @param $cur_sym		if empty then  get it from  DB
 *
 * @return array|string|string[]
 */
function wpbc_formate_cost_hint__no_html( $cost, $cur_sym = '' ){

	if ( empty( $cur_sym ) ) {
		$cur_sym = wpbc_get_currency_symbol();
	}

	$cost_text = $cost;

	$cost_text = number_format( floatval( $cost_text ), wpbc_get_cost_decimals(), '.', '' );

	$cost_text = strip_tags( wpbc_cost_show( $cost_text, array( 'currency' => 'CURRENCY_SYMBOL' ) ) );

	$cost_text = str_replace( array( 'CURRENCY_SYMBOL', '&' ), array( $cur_sym, '&amp;' ), $cost_text );

	return $cost_text;
}


/**
	 * Format booking cost with a currency symbol
 *
 * @param float $cost
 * @param array $args (default: array())
 * @return string
 * 
 * Exmaple of usage:
   wpbc_cost_show( $subtotal, array(  'currency' => wpbc_get_currency() ) );
 */
function wpbc_cost_show( $cost, $args = array() ) {

    extract( apply_filters( 'wpbc_cost_args', wp_parse_args( $args, array(
            'currency'           => '',
            'decimals'           => wpbc_get_cost_decimals(),
            'decimal_separator'  => wpbc_get_cost_decimal_separator(),
            'thousand_separator' => wpbc_get_cost_thousand_separator(),
            'cost_format'        => wpbc_get_cost_format()
    ) ) ) );

    $cost       = floatval( $cost );                                        // Convert possible string cost to Float
    $cost       = apply_filters( 'wpbc_formatted_cost'
                                        , number_format( $cost, $decimals, $decimal_separator, $thousand_separator )
                                        , $cost
                                        , $decimals
                                        , $decimal_separator
                                        , $thousand_separator );

    $formatted_cost = sprintf( $cost_format, '<span class="wpbc-currency-symbol">' . wpbc_get_currency_symbol( $currency ) . '</span>', $cost );
    $return         = '<span class="wpbc-cost-amount">' . $formatted_cost . '</span>';

    return apply_filters( 'wpbc_cost_show', $return, $cost, $args );
}


/**
	 * Get cost format depending on the currency position.
 * 
 * @param string $currency_pos - default '' (load from  DB) | 'left' | 'right' | 'left_space' | 'right_space'
 * @return string
 */
function wpbc_get_cost_format( $currency_pos = '' ) {

    if ( empty( $currency_pos ) )
        $currency_pos = get_bk_option( 'booking_currency_pos' );
    
    $format = '%1$s%2$s';

    switch ( $currency_pos ) {

        case 'left' :
                $format = '%1$s%2$s';
                break;
        case 'right' :
                $format = '%2$s%1$s';
                break;
        case 'left_space' :
                $format = '%1$s&nbsp;%2$s';
                break;
        case 'right_space' :
                $format = '%2$s&nbsp;%1$s';
                break;
    }

    return apply_filters( 'wpbc_get_cost_format', $format, $currency_pos );
}


/**
	 * Get number of decimals after the decimal point
 * 
 * @return int
 */
function wpbc_get_cost_decimals() {
    return absint( get_bk_option( 'booking_cost_currency_format_decimal_number', 2 ) );
}


/**
	 * Get decimal separator for costs.
 * 
 * @return string
 */
function wpbc_get_cost_decimal_separator() {
    $separator = stripslashes( get_bk_option( 'booking_cost_currency_format_decimal_separator' ) );
    $separator = str_replace( 'space', ' ', $separator );
    return $separator ? $separator : '.';
}


/**
	 * Get thousand separator for costs.
 *
 * @return string
 */
function wpbc_get_cost_thousand_separator() {
    $separator = stripslashes( get_bk_option( 'booking_cost_currency_format_thousands_separator' ) );
    $separator = str_replace( 'space', ' ', $separator );
    return $separator;
}



////////////////////////////////////////////////////////////////////////////////
// Currencies
////////////////////////////////////////////////////////////////////////////////
/**
	 * Get active Currency
 * 
 * @return string
 */
function wpbc_get_currency(){
    
    $currency = apply_filters( 'wpbc_booking_currency', get_bk_option('booking_currency') );
    
    if ( empty( $currency ) ) 
        $currency = get_bk_option( 'booking_paypal_curency' );
    
    if ( empty( $currency ) ) 
        $currency = 'USD';
    
    return $currency;
}

/**
	 * Get full list of currency codes.
 *
 * @return array
 */
function wpbc_get_currency_list() {
    return array_unique(
                apply_filters( 'wpbc_currency_list',
			array(
				'AED' =>  'United Arab Emirates dirham', 
				'AFN' =>  'Afghan afghani',
				'ALL' =>  'Albanian lek',
				'AMD' =>  'Armenian dram',
				'ANG' =>  'Netherlands Antillean guilder',
				'AOA' =>  'Angolan kwanza',
				'ARS' =>  'Argentine peso',
				'AUD' =>  'Australian dollar',
				'AWG' =>  'Aruban florin',
				'AZN' =>  'Azerbaijani manat',
				'BAM' =>  'Bosnia and Herzegovina convertible mark',
				'BBD' =>  'Barbadian dollar',
				'BDT' =>  'Bangladeshi taka',
				'BGN' =>  'Bulgarian lev',
				'BHD' =>  'Bahraini dinar',
				'BIF' =>  'Burundian franc',
				'BMD' =>  'Bermudian dollar',
				'BND' =>  'Brunei dollar',
				'BOB' =>  'Bolivian boliviano',
				'BRL' =>  'Brazilian real',
				'BSD' =>  'Bahamian dollar',
				'BTC' =>  'Bitcoin',
				'BTN' =>  'Bhutanese ngultrum',
				'BWP' =>  'Botswana pula',
				'BYR' =>  'Belarusian ruble',
				'BZD' =>  'Belize dollar',
				'CAD' =>  'Canadian dollar',
				'CDF' =>  'Congolese franc',
				'CHF' =>  'Swiss franc',
				'CLP' =>  'Chilean peso',
				'CNY' =>  'Chinese yuan',
				'COP' =>  'Colombian peso',
				'CRC' =>  'Costa Rican col&oacute;n',
				'CUC' =>  'Cuban convertible peso',
				'CUP' =>  'Cuban peso',
				'CVE' =>  'Cape Verdean escudo',
				'CZK' =>  'Czech koruna',
				'DJF' =>  'Djiboutian franc',
				'DKK' =>  'Danish krone',
				'DOP' =>  'Dominican peso',
				'DZD' =>  'Algerian dinar',
				'EGP' =>  'Egyptian pound',
				'ERN' =>  'Eritrean nakfa',
				'ETB' =>  'Ethiopian birr',
				'EUR' =>  'Euro',
				'FJD' =>  'Fijian dollar',
				'FKP' =>  'Falkland Islands pound',
				'GBP' =>  'Pound sterling',
				'GEL' =>  'Georgian lari',
				'GGP' =>  'Guernsey pound',
				'GHS' =>  'Ghana cedi',
				'GIP' =>  'Gibraltar pound',
				'GMD' =>  'Gambian dalasi',
				'GNF' =>  'Guinean franc',
				'GTQ' =>  'Guatemalan quetzal',
				'GYD' =>  'Guyanese dollar',
				'HKD' =>  'Hong Kong dollar',
				'HNL' =>  'Honduran lempira',
				'HRK' =>  'Croatian kuna',
				'HTG' =>  'Haitian gourde',
				'HUF' =>  'Hungarian forint',
				'IDR' =>  'Indonesian rupiah',
				'ILS' =>  'Israeli new shekel',
				'IMP' =>  'Manx pound',
				'INR' =>  'Indian rupee',
				'IQD' =>  'Iraqi dinar',
				'IRR' =>  'Iranian rial',
				'ISK' =>  'Icelandic kr&oacute;na',
				'JEP' =>  'Jersey pound',
				'JMD' =>  'Jamaican dollar',
				'JOD' =>  'Jordanian dinar',
				'JPY' =>  'Japanese yen',
				'KES' =>  'Kenyan shilling',
				'KGS' =>  'Kyrgyzstani som',
				'KHR' =>  'Cambodian riel',
				'KMF' =>  'Comorian franc',
				'KPW' =>  'North Korean won',
				'KRW' =>  'South Korean won',
				'KWD' =>  'Kuwaiti dinar',
				'KYD' =>  'Cayman Islands dollar',
				'KZT' =>  'Kazakhstani tenge',
				'LAK' =>  'Lao kip',
				'LBP' =>  'Lebanese pound',
				'LKR' =>  'Sri Lankan rupee',
				'LRD' =>  'Liberian dollar',
				'LSL' =>  'Lesotho loti',
				'LYD' =>  'Libyan dinar',
				'MAD' =>  'Moroccan dirham',
				'MDL' =>  'Moldovan leu',
				'MGA' =>  'Malagasy ariary',
				'MKD' =>  'Macedonian denar',
				'MMK' =>  'Burmese kyat',
				'MNT' =>  'Mongolian t&ouml;gr&ouml;g',
				'MOP' =>  'Macanese pataca',
				'MRO' =>  'Mauritanian ouguiya',
				'MUR' =>  'Mauritian rupee',
				'MVR' =>  'Maldivian rufiyaa',
				'MWK' =>  'Malawian kwacha',
				'MXN' =>  'Mexican peso',
				'MYR' =>  'Malaysian ringgit',
				'MZN' =>  'Mozambican metical',
				'NAD' =>  'Namibian dollar',
				'NGN' =>  'Nigerian naira',
				'NIO' =>  'Nicaraguan c&oacute;rdoba',
				'NOK' =>  'Norwegian krone',
				'NPR' =>  'Nepalese rupee',
				'NZD' =>  'New Zealand dollar',
				'OMR' =>  'Omani rial',
				'PAB' =>  'Panamanian balboa',
				'PEN' =>  'Peruvian nuevo sol',
				'PGK' =>  'Papua New Guinean kina',
				'PHP' =>  'Philippine peso',
				'PKR' =>  'Pakistani rupee',
				'PLN' =>  'Polish z&#x142;oty',
				'PRB' =>  'Transnistrian ruble',
				'PYG' =>  'Paraguayan guaran&iacute;',
				'QAR' =>  'Qatari riyal',
				'RON' =>  'Romanian leu',
				'RSD' =>  'Serbian dinar',
				'RUB' =>  'Russian ruble',
				'RWF' =>  'Rwandan franc',
				'SAR' =>  'Saudi riyal',
				'SBD' =>  'Solomon Islands dollar',
				'SCR' =>  'Seychellois rupee',
				'SDG' =>  'Sudanese pound',
				'SEK' =>  'Swedish krona',
				'SGD' =>  'Singapore dollar',
				'SHP' =>  'Saint Helena pound',
				'SLL' =>  'Sierra Leonean leone',
				'SOS' => 'Somali shilling',
				'SRD' =>  'Surinamese dollar',
				'SSP' =>  'South Sudanese pound',
				'STD' =>  'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra',
				'SYP' =>  'Syrian pound',
				'SZL' =>  'Swazi lilangeni',
				'THB' =>  'Thai baht',
				'TJS' =>  'Tajikistani somoni',
				'TMT' =>  'Turkmenistan manat',
				'TND' =>  'Tunisian dinar',
				'TOP' =>  'Tongan pa&#x2bb;anga',
				'TRY' =>  'Turkish lira',
				'TTD' =>  'Trinidad and Tobago dollar',
				'TWD' =>  'New Taiwan dollar',
				'TZS' =>  'Tanzanian shilling',
				'UAH' =>  'Ukrainian hryvnia',
				'UGX' =>  'Ugandan shilling',
				'USD' =>  'United States dollar',
				'UYU' =>  'Uruguayan peso',
				'UZS' =>  'Uzbekistani som',
				'VEF' =>  'Venezuelan bol&iacute;var',
				'VND' =>  'Vietnamese &#x111;&#x1ed3;ng',
				'VUV' =>  'Vanuatu vatu',
				'WST' =>  'Samoan t&#x101;l&#x101;',
				'XAF' =>  'Central African CFA franc',
				'XCD' =>  'East Caribbean dollar',
				'XOF' =>  'West African CFA franc',
				'XPF' =>  'CFP franc',
				'YER' =>  'Yemeni rial',
				'ZAR' =>  'South African rand',
				'ZMW' =>  'Zambian kwacha'
			)
		)
	);
}

/**
	 * Get Currency symbol.
 *
 * @param string $currency (default: ''), if skipped "Currency Code"  then, load active currency from DB
 * @return string
 */
function wpbc_get_currency_symbol( $currency = '' ) {
    
	if ( ! $currency ) {
		$currency = wpbc_get_currency();
	}

	$symbols = apply_filters( 'wpbc_currency_symbols', array(
		'AED' => '&#x62f;.&#x625;',
		'AFN' => '&#x60b;',
		'ALL' => 'L',
		'AMD' => 'AMD',
		'ANG' => '&fnof;',
		'AOA' => 'Kz',
		'ARS' => '&#36;',
		'AUD' => '&#36;',
		'AWG' => '&fnof;',
		'AZN' => 'AZN',
		'BAM' => 'KM',
		'BBD' => '&#36;',
		'BDT' => '&#2547;&nbsp;',
		'BGN' => '&#1083;&#1074;.',
		'BHD' => '.&#x62f;.&#x628;',
		'BIF' => 'Fr',
		'BMD' => '&#36;',
		'BND' => '&#36;',
		'BOB' => 'Bs.',
		'BRL' => '&#82;&#36;',
		'BSD' => '&#36;',
		'BTC' => '&#3647;',
		'BTN' => 'Nu.',
		'BWP' => 'P',
		'BYR' => 'Br',
		'BZD' => '&#36;',
		'CAD' => '&#36;',
		'CDF' => 'Fr',
		'CHF' => '&#67;&#72;&#70;',
		'CLP' => '&#36;',
		'CNY' => '&yen;',
		'COP' => '&#36;',
		'CRC' => '&#x20a1;',
		'CUC' => '&#36;',
		'CUP' => '&#36;',
		'CVE' => '&#36;',
		'CZK' => '&#75;&#269;',
		'DJF' => 'Fr',
		'DKK' => 'DKK',
		'DOP' => 'RD&#36;',
		'DZD' => '&#x62f;.&#x62c;',
		'EGP' => 'EGP',
		'ERN' => 'Nfk',
		'ETB' => 'Br',
		'EUR' => '&euro;',
		'FJD' => '&#36;',
		'FKP' => '&pound;',
		'GBP' => '&pound;',
		'GEL' => '&#x10da;',
		'GGP' => '&pound;',
		'GHS' => '&#x20b5;',
		'GIP' => '&pound;',
		'GMD' => 'D',
		'GNF' => 'Fr',
		'GTQ' => 'Q',
		'GYD' => '&#36;',
		'HKD' => '&#36;',
		'HNL' => 'L',
		'HRK' => 'Kn',
		'HTG' => 'G',
		'HUF' => '&#70;&#116;',
		'IDR' => 'Rp',
		'ILS' => '&#8362;',
		'IMP' => '&pound;',
		'INR' => '&#8377;',
		'IQD' => '&#x639;.&#x62f;',
		'IRR' => '&#xfdfc;',
		'ISK' => 'Kr.',
		'JEP' => '&pound;',
		'JMD' => '&#36;',
		'JOD' => '&#x62f;.&#x627;',
		'JPY' => '&yen;',
		'KES' => 'KSh',
		'KGS' => '&#x43b;&#x432;',
		'KHR' => '&#x17db;',
		'KMF' => 'Fr',
		'KPW' => '&#x20a9;',
		'KRW' => '&#8361;',
		'KWD' => '&#x62f;.&#x643;',
		'KYD' => '&#36;',
		'KZT' => 'KZT',
		'LAK' => '&#8365;',
		'LBP' => '&#x644;.&#x644;',
		'LKR' => '&#xdbb;&#xdd4;',
		'LRD' => '&#36;',
		'LSL' => 'L',
		'LYD' => '&#x644;.&#x62f;',
		'MAD' => '&#x62f;. &#x645;.',
		'MAD' => '&#x62f;.&#x645;.',
		'MDL' => 'L',
		'MGA' => 'Ar',
		'MKD' => '&#x434;&#x435;&#x43d;',
		'MMK' => 'Ks',
		'MNT' => '&#x20ae;',
		'MOP' => 'P',
		'MRO' => 'UM',
		'MUR' => '&#x20a8;',
		'MVR' => '.&#x783;',
		'MWK' => 'MK',
		'MXN' => '&#36;',
		'MYR' => '&#82;&#77;',
		'MZN' => 'MT',
		'NAD' => '&#36;',
		'NGN' => '&#8358;',
		'NIO' => 'C&#36;',
		'NOK' => '&#107;&#114;',
		'NPR' => '&#8360;',
		'NZD' => '&#36;',
		'OMR' => '&#x631;.&#x639;.',
		'PAB' => 'B/.',
		'PEN' => 'S/.',
		'PGK' => 'K',
		'PHP' => '&#8369;',
		'PKR' => '&#8360;',
		'PLN' => '&#122;&#322;',
		'PRB' => '&#x440;.',
		'PYG' => '&#8370;',
		'QAR' => '&#x631;.&#x642;',
		'RMB' => '&yen;',
		'RON' => 'lei',
		'RSD' => '&#x434;&#x438;&#x43d;.',
		'RUB' => '&#8381;',
		'RWF' => 'Fr',
		'SAR' => '&#x631;.&#x633;',
		'SBD' => '&#36;',
		'SCR' => '&#x20a8;',
		'SDG' => '&#x62c;.&#x633;.',
		'SEK' => '&#107;&#114;',
		'SGD' => '&#36;',
		'SHP' => '&pound;',
		'SLL' => 'Le',
		'SOS' => 'Sh',
		'SRD' => '&#36;',
		'SSP' => '&pound;',
		'STD' => 'Db',
		'SYP' => '&#x644;.&#x633;',
		'SZL' => 'L',
		'THB' => '&#3647;',
		'TJS' => '&#x405;&#x41c;',
		'TMT' => 'm',
		'TND' => '&#x62f;.&#x62a;',
		'TOP' => 'T&#36;',
		'TRY' => '&#8378;',
		'TTD' => '&#36;',
		'TWD' => '&#78;&#84;&#36;',
		'TZS' => 'Sh',
		'UAH' => '&#8372;',
		'UGX' => 'UGX',
		'USD' => '&#36;',
		'UYU' => '&#36;',
		'UZS' => 'UZS',
		'VEF' => 'Bs F',
		'VND' => '&#8363;',
		'VUV' => 'Vt',
		'WST' => 'T',
		'XAF' => 'Fr',
		'XCD' => '&#36;',
		'XOF' => 'Fr',
		'XPF' => 'Fr',
		'YER' => '&#xfdfc;',
		'ZAR' => '&#82;',
		'ZMW' => 'ZK',
            
                'CURRENCY_SYMBOL' => 'CURRENCY_SYMBOL'                          // System term - usually  used for later  replacing.
	) );

	$currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';

	return apply_filters( 'wpbc_currency_symbol', $currency_symbol, $currency );
}