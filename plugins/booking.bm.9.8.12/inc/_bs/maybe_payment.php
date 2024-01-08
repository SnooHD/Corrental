<?php

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly            //FixIn: 9.8.0.4

// ---------------------------------------------------------------------------------------------------------------------
// == May be get Payment forms
// ---------------------------------------------------------------------------------------------------------------------

	/**
	 * Get payment form(s)          and             Update COST      of the booking
	 *
	 * @param $payment_params         = [    'booking_id'            => '2'
	 *                                'resource_id'           => '12'                                                     - calendar booking resource (probably  parent) for calculation cost
	 *                                'form_data'             => 'text^selected_short_timedates_hint12^06/11/2018 14:00...'
	 *                                'times_array'           => [ ["10","00","00"], ["12","00","00"] ]
	 *                                'str_dates__dd_mm_yyyy' => '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023'
	 *
	 *                              // -------  extra params  -------
	 *
	 *                              'initial_resource_id'   => 2                                                         - ? initial parent resource, in case if we used child resource for 'resource_id'
	 *                              'is_edit_booking'       => 0                                                         0 | int - ID of the booking
	 *                              'custom_form'           => ''                                                        '' | 'some_name'
	 *                              'is_duplicate_booking'  => 0                                                         0 | 1
	 *                              'is_from_admin_panel'   => false                                                     bool
	 *                              'is_show_payment_form'  => 1                                                         0 | 1
	 *                          ]
	 *
	 * @return array|false|string|string[]
	 *
	 * $response_payment_form_arr = [  'status'    => 'ok'
	 *                                 'costs_arr' => [                                                                 <- get from   wpbc_get__total_deposit_cost__mayb_update_db( [...] );
	 *                                                  'total_cost'           => 108,                                  - total cost saved to DB
	 *                                                  'deposit_cost'         => 10.8,                                 - if (deposit == false) than 108
	 *                                                  'form_data'             => '...'   -  if used [cost_correction] -- return  it also, because 'form_data' can  be overwritten
	 *                                                                                                                  with '~text^corrected_total_cost{$payment_params['resource_id']}^...'
	 *                                                ]
	 *                                 'gateways_output_arr' = [ ... ]
	 *                              ]
	 */
	function wpbc_maybe_get_payment_form( $payment_params ) {

		$payment_params['status'] = 'ok';

		if ( ! class_exists( 'wpdev_bk_biz_s' ) ) {
			return $payment_params;
		}

		$output_arr = array();

		// Make decision  Is show payment form
		$is_show_payment_form = wpbc_is_show_payment_form_this_time(	$payment_params['is_edit_booking'],
																		$payment_params['is_duplicate_booking'],
																		$payment_params['is_from_admin_panel'],
																		empty( $payment_params['costs_arr'] )
																	);
		$response_payment_form_arr = array( 'status' => 'ok' );

		if ( $is_show_payment_form ) {

			// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
			// Convert 'form_data' from possible 'child booking resource'  to 'parent booking resource',    and define this parent booking resource ID for correct  cost calculation
			if ( $payment_params['resource_id'] != $payment_params['initial_resource_id'] ) {
				$old_resource_id = $payment_params['resource_id'];
				$new_resource_id = $payment_params['initial_resource_id'];
				$payment_params['form_data']   = wpbc_get__form_data__with_replaced_id( $payment_params['form_data'], $new_resource_id, $old_resource_id );
				$payment_params['resource_id'] = $new_resource_id;
			} else {
				$old_resource_id = false;
				$new_resource_id = false;
			}
			// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^


			// If we provided this array,  then we do not need to calculate cost  again!
			if ( ! empty( $payment_params['costs_arr'] ) ) {
				echo 'LOLOLOLOLOL';
				$cost__deposit__arr = $payment_params['costs_arr'];

			} else {

				/**
				 *   Calc  'total cost'   and   'Update it in DB'
				 *   [
				 *		'total_cost'           => 108,                - Total cost saved to DB          // 108
				 *		'deposit_cost'         => 10.8,                                                 // 10.8             (if deposit falser than 108)
				 *   ]
				 */
				$cost__deposit__arr = wpbc_get__total_deposit_cost__mayb_update_db( array(
																	'booking_id'            => $payment_params['booking_id'],
																	'resource_id'           => $payment_params['resource_id'],
																	'custom_form'           => $payment_params['custom_form'],
																	'form_data'             => $payment_params['form_data'],
																	'str_dates__dd_mm_yyyy' => $payment_params['str_dates__dd_mm_yyyy'],
																	'times_array'           => array( $payment_params['times_array'][0], $payment_params['times_array'][1] )
																) );


				if ( ! empty( $cost__deposit__arr['form_data'] ) ) {                    // Because  - if used [cost_correction] - it can  be updated in DB
					$payment_params['form_data'] = $cost__deposit__arr['form_data'];
				}
			}

			$response_payment_form_arr['costs_arr'] = $cost__deposit__arr;


			// =========================================================================================================
		    // Get Payment forms
			// =========================================================================================================
			if (
					( ! empty( $payment_params['is_show_payment_form'] ) )
			     && ( 1 == $payment_params['is_show_payment_form'] )
			){

				$payment_params['cost__deposit__arr'] = $cost__deposit__arr;

				ob_start();
				ob_clean();


				if ( 'On' == get_bk_option( 'booking_super_admin_receive_regular_user_payments' ) ){ make_bk_action('make_force_using_this_user', -999 ); }		// '-999' - This ID "by default" is the ID of super booking admin user //FixIn: 9.2.3.8

				$output_arr = wpbc_get__payment_gateways__output_arr( $payment_params );                          // Is deposit,  then it update cost in DB

				if ( 'On' == get_bk_option( 'booking_super_admin_receive_regular_user_payments' ) ){ make_bk_action( 'finish_force_using_this_user' ); }


				$payment_systems_html = ob_get_contents();
				$payment_systems_html = str_replace( "\\n", '', $payment_systems_html );

				ob_end_clean();
			}


			// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
			// Backward Convert 'form_data' from possible 'parent booking resource' -> 'child booking resource'  and define booking resource ID for child resource
			if ( ( false !== $old_resource_id ) && ( false !== $new_resource_id ) ) {
				$payment_params['resource_id'] = $old_resource_id;
				$payment_params['form_data']   = wpbc_get__form_data__with_replaced_id( $payment_params['form_data'], $old_resource_id, $new_resource_id );
			}
			// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
		}


		$response_payment_form_arr['gateways_output_arr']    = $output_arr;

		$response_payment_form_arr['costs_arr']['form_data'] = $payment_params['form_data'];

		return $response_payment_form_arr;
	}


	/**
	 * Is show payment gateways or not
	 *
	 * @param $is_edit_booking          0 | 1
	 * @param $is_duplicate_booking     0 | 1
	 * @param $is_from_admin_panel      bool
	 * @param $is_empty_costs_arr       bool
	 *
	 * @return bool
	 */
	function wpbc_is_show_payment_form_this_time( $is_edit_booking, $is_duplicate_booking, $is_from_admin_panel , $is_empty_costs_arr ) {

		$is_new_booking               = ( 0 === $is_edit_booking );
		$is_update_cost_after_editing = ( 'On' == get_bk_option( 'booking_payment_update_cost_after_edit_in_bap' ) );

		if (
			   (  $is_new_booking )											                                                    // New booking
			|| (  1 === $is_duplicate_booking )										                        // Duplicate booking
			|| ( ( ! $is_new_booking ) && ( ! $is_from_admin_panel ) )			                            // Edit on Front-end
			|| ( ( ! $is_new_booking ) && (   $is_from_admin_panel ) && ( $is_update_cost_after_editing )   ) // Edit on Admin panel
			|| ( ! $is_empty_costs_arr )
		) {
			return  true;
		}

		return false;
	}

// ---------------------------------------------------------------------------------------------------------------------
// Cost of booking
// ---------------------------------------------------------------------------------------------------------------------

	/**
	 * Get / Calc  'Total Cost'  (maybe get 'Deposit Cost')    and     DB update:  'Total Cost' | ? 'form_data'
	 *
	 * @param $params  =  [
	 *                        'booking_id'            => 4234,
	 *                        'resource_id'           => 4,
	 *                        'custom_form'           => '',
	 *                        'form_data'             => 'text^selected_short_timedates_hint4^06/11/2018 14:00...',
	 *                        'str_dates__dd_mm_yyyy' => '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023',
	 *                        'times_array'           => [   [ '00', '00', '01' ],  [ '24', '00', '02' ]  ]
	 *
	 *                        'is_update_cost_to_db' => true,            // Optional   -   default true
	 *                    ]
	 *
	 * @return array|WP_Error  = [
	 *                                  'total_cost'           => 108
	 *                                  'deposit_cost'         => 10.8                      -  deposit (lower cost)   or   if not deposit then    deposit == total cost
	 *                                  'form_data'            => 'text^selected_ ...'      -  because it can  be updated
	 *                           ]
	 *           or  WP_Error
	 */
	function wpbc_get__total_deposit_cost__mayb_update_db( $params ){

		if ( ! class_exists( 'wpdev_bk_biz_s' ) ) {    return array( 'total_cost'   => 0, 'deposit_cost' => 0 );   }

		$defaults = array(
							'booking_id'            => 0,
							'resource_id'           => 1,
							'custom_form'           => '',                                  // Name of Custom form
							'form_data'             => '',                                  // 'text^selected_short_timedates_hint4^06/11/2018 14:00...'
							'str_dates__dd_mm_yyyy' => '',                                  // '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023'
							'times_array'           => array(),                             // [   [ '00', '00', '01' ],  [ '24', '00', '02' ]  ]
							'is_update_cost_to_db'  => true              // Optional
						);
		$payment_params = wp_parse_args( $params, $defaults );

		/**
		 * Trick for legacy code for correct cost calculation,  relative to "Advanced cost".
		 * Required in biz_m.php file in  function advanced_cost_apply( ... )
		 */
		$_POST['booking_form_type'] = $payment_params['custom_form'];


		// -------------------------------------------------------------------------------------------------------------
		// Get Total Cost
		// -------------------------------------------------------------------------------------------------------------

		// Check if total cost field exist and get cost from that field
		$fin_cost_corrections_sum = apply_bk_filter( 'wpbc_is__cost_corrections__in_booking_form', false, $payment_params['form_data'], $payment_params['resource_id'] );

	    if ( false !== $fin_cost_corrections_sum ) {

		    $total_booking_cost_db = $fin_cost_corrections_sum;                                                        // COST_CORRECTIONS

	    } else {

			/**
			 *  C A L C  :  RATES - VALUATION_DAYS - ADVANCED_COST - EARLY_LATE_BOOKING_APPLY - COUPONS_DISCOUNT_APPLY          :       only calc  - NO updates in DB
			 *
			 *              based on Dates | Times   It makes only  calculation,  without updates of DB.
			 *
			 *      Note 1: This function require correct definition  of  $_POST['booking_form_type']  for works of Advanced costs (relative custom  forms)!
			 */

			$total_booking_cost_db = wpbc_calc__booking_cost( array(
													  'resource_id'           => $payment_params['resource_id']           	    // '2'
													, 'str_dates__dd_mm_yyyy' => $payment_params['str_dates__dd_mm_yyyy']       // '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023'
													, 'times_array' 	      => $payment_params['times_array']
													, 'form_data'             => $payment_params['form_data']     		 	    // 'text^selected_short_timedates_hint4^06/11/2018 14:00...'
															, 'is_discount_calculate' => true                                   // Default
															, 'is_only_original_cost' => false                                  // Default
											));
	    }
		$total_booking_cost_db = floatval( $total_booking_cost_db );                                                    // from double  > float

		// -------------------------------------------------------------------------------------------------------------
		// Save in DB
		// -------------------------------------------------------------------------------------------------------------
		if ( $payment_params['is_update_cost_to_db'] ) {

			$is_updated = wpbc_db__update_booking_cost( $payment_params['booking_id'], $total_booking_cost_db );        // Before showing payment gateways - update DB cost again to  deposit

			if ( is_wp_error( $is_updated ) ) {
				wp_die( $is_updated );
			}

			// Log the cost  info.
			$is_add_timezone_offset = true;
			$booking_note = wpbc_date_localized( gmdate( 'Y-m-d H:i:s' ), '[Y-m-d H:i]', $is_add_timezone_offset ) . ' ';
			$booking_note .= ( false !== $fin_cost_corrections_sum ) ? __( 'Total cost manually entered', 'booking' ) : __( 'Automatically calculated cost', 'booking' );
			$booking_note .= ' ' . $total_booking_cost_db . "\n";
			make_bk_action( 'wpdev_make_update_of_remark',  $payment_params['booking_id'], $booking_note, true );
		}

		// -------------------------------------------------------------------------------------------------------------
		// Deposit                  -  deposit (lower cost)   or   if not deposit then    deposit == total cost
		// -------------------------------------------------------------------------------------------------------------
		$deposit_cost = wpbc_get_maybe_deposit_amount(  $total_booking_cost_db, array(
																'resource_id'           => $params['resource_id'],	            // 4
																'str_dates__dd_mm_yyyy' => $params['str_dates__dd_mm_yyyy'],    // '06.11.2023,07.11.2023,08.11.2023'
																'form_data'             => $payment_params['form_data'],	    // 'text^cost_hint4^€75.00~text^original_cost_hint4^€75.00~text^...'
																'times_array'           => $payment_params['times_array']	    // [ ['00', '00', '00'], ['24', '00', '00'] ]
															));

		// TODO: May be Check for Extra calendars in next  updates ?  ->   $extra_calendars__cost_arr = apply_bk_filter( 'wpbc_get_cost__in_additional_calendars', $summ, $params['form_data'], $params['resource_id'], $params['times_array'] ); 				// Apply cost according additional calendars

		// -------------------------------------------------------------------------------------------------------------
		// DB update    'form_data'    if cost_correction
		// -------------------------------------------------------------------------------------------------------------
		if ( false !== $fin_cost_corrections_sum ) {

			$payment_params['form_data'] = wpbc_db__if_cost_correction__update_costs__form_data(  $payment_params['form_data'],
																								$payment_params['booking_id'],
																								$payment_params['resource_id'],
																								$total_booking_cost_db,
																								$deposit_cost                   );
		}

		// -------------------------------------------------------------------------------------------------------------
		$booking_cost_array = array(
									'total_cost'   => $total_booking_cost_db,                                           // 108
									'deposit_cost' => $deposit_cost,                                                    // 10.8             (if deposit falser than 108)
									'form_data'    => $payment_params['form_data']                                      // It can be updated, because of [cost_correction]. Is it really need to  return ?
								);

		return $booking_cost_array;
	}


	/**
	 * Get Deposit Amount,  if it can  be applied
	 *
	 * @param float $total_booking_cost // 75.0
	 * @param array $params             [
	 *                              'resource_id'                => $params['resource_id'],                 // 4
	 *                              'str_dates__dd_mm_yyyy'      => $params['str_dates__dd_mm_yyyy'],       // '06.11.2023,07.11.2023,08.11.2023'
	 *                              'form_data'                  => $payment_params['form_data'],           // 'text^cost_hint4^€75.00~text^original_cost_hint4^€75.00~text^...'
	 *                              'times_array'                => $payment_params['times_array']          // [ ['00', '00', '00'], ['24', '00', '00'] ]
	 *                          ]
	 *
	 * @return float
	 */
	function wpbc_get_maybe_deposit_amount( $total_booking_cost, $params = array() ) {

		$total_booking_cost = floatval( $total_booking_cost );

		if ( 'On' === get_bk_option( 'booking_calc_deposit_on_original_cost_only' ) ) {

			// DEPOSIT based on ORIGINAL_COST
			$summ_original = wpbc_calc__booking_cost( array(
													  'resource_id'           => $params['resource_id']           	// '2'
													, 'str_dates__dd_mm_yyyy' => $params['str_dates__dd_mm_yyyy']   // '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023'
													, 'times_array' 	      => $params['times_array']
													, 'form_data'             => $params['form_data']     		 	// 'text^selected_short_timedates_hint4^06/11/2018 14:00...'
															, 'is_discount_calculate' => true       /* Default */
													, 'is_only_original_cost' => TRUE               /* CHANGED */
											) );

			$summ_original = floatval( $summ_original );

			$deposit_cost = apply_bk_filter( 'wpbc_calc__deposit_cost__if_enabled', $summ_original     , $params['resource_id'], $params['str_dates__dd_mm_yyyy'] );

			$is_deposit = ( $summ_original != $deposit_cost );

		} else {
			
			$deposit_cost = apply_bk_filter( 'wpbc_calc__deposit_cost__if_enabled', $total_booking_cost, $params['resource_id'], $params['str_dates__dd_mm_yyyy'] );

			$is_deposit = ( $total_booking_cost != $deposit_cost );
		}
		
		
		if ( $is_deposit ) {
			return $deposit_cost;
		} else {
			return $total_booking_cost;
		}
	}


	/**
	 * If used [cost_correction] then update [corrected_ ... _cost]  in  DB and return new 'form_data'
	 *
	 * Note. Shortcodes: [corrected_total_cost], [corrected_deposit_cost], [corrected_balance_cost]  we can use in emails.
	 *
	 * @param $form_data
	 * @param $booking_id
	 * @param $resource_id
	 * @param $total_cost
	 * @param $deposit_cost
	 *
	 * @return string
	 */
	function wpbc_db__if_cost_correction__update_costs__form_data( $form_data, $booking_id, $resource_id, $total_cost, $deposit_cost ) {

		// Was used [cost_corrections] shortcode, so need to re-update form_data with new: [corrected_total_cost], [corrected_deposit_cost], [corrected_balance_cost] we can use in emails.

		$cur_sym = wpbc_get_currency_symbol();

		$booking_cost_array['balance_cost'] = floatval($total_cost) - floatval($deposit_cost);

		// Shortcode [corrected_total_cost] --------------------------------------------------------------------
		$cost_hint_text = wpbc_formate_cost_hint__no_html( $total_cost, $cur_sym );
		$form_data .= "~text^corrected_total_cost{$resource_id}^" . $cost_hint_text;

		// Shortcode [corrected_deposit_cost] ------------------------------------------------------------------
		$deposit_hint_text = wpbc_formate_cost_hint__no_html( $deposit_cost, $cur_sym );
		$form_data .= "~text^corrected_deposit_cost{$resource_id}^" . $deposit_hint_text;

		//Shortcode [corrected_balance_cost] -------------------------------------------------------------------
		$balance_hint_text = wpbc_formate_cost_hint__no_html( $booking_cost_array['balance_cost'], $cur_sym );
		$form_data .= "~text^corrected_balance_cost{$resource_id}^" . $balance_hint_text;


		$booking_data_arr = wpbc_get_parsed_booking_data_arr( $form_data, $resource_id );						    //FixIn: 9.4.4.3

		if ( isset( $booking_data_arr['cost_hint'] ) ) {
			$booking_data_arr['cost_hint']['value'] = $cost_hint_text;
		}
		if ( isset( $booking_data_arr['deposit_hint'] ) ) {
			$booking_data_arr['deposit_hint']['value'] = $deposit_hint_text;
		}
		if ( isset( $booking_data_arr['balance_hint'] ) ) {
			$booking_data_arr['balance_hint']['value'] = $balance_hint_text;
		}
		$form_data = wpbc_encode_booking_data_to_string( $booking_data_arr, $resource_id );

	    // -------------------------------------------------------------------- --------------------------------
		// Update new form data for having it in emails.
		// -------------------------------------------------------------------- --------------------------------
		global $wpdb;

		$update_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}booking AS bk SET bk.form=%s WHERE bk.booking_id=%d;", $form_data, $booking_id );

		if ( false === $wpdb->query( $update_sql  ) ){

			// :: ERROR ::
			wp_die( new WP_Error( 'wpbc_db__update_payment_form_details', 'Error. UPDATE data in DB.' . '  FILE:' . __FILE__ . ' LINE:' . __LINE__ . ' SQL:' . $update_sql ) );
		}
		
		return $form_data;
	}


// ---------------------------------------------------------------------------------------------------------------------
// Approve booking if zero cost
// ---------------------------------------------------------------------------------------------------------------------

	/**
	 * Approve booking (Update booking dates in DB),  if the cost  of booking in DB == 0
	 *
	 * @param int $booking_id
	 *
	 * @return void
	 */
	function wpbc_if_zero_cost__approve_booking_dates( $booking_id ) {

		if(
			    ( 'On' != get_bk_option( 'booking_auto_approve_bookings_when_zero_cost' ) )
	      // && ( ! empty( $response__payment_form__arr['costs_arr']['total_cost'] ) )          //TODO: may be use this if. Check  if total_cost it's our final cost and it's available in biz_s version.
		){
			return false;
		}

		if ( ! class_exists( 'wpdev_bk_biz_s' ) ) {
			return false;
		}

		$booking_cost = wpbc_db__get_booking__cost( $booking_id );
		if ( empty( $booking_cost ) ) {
			// Approve booking
			wpbc_db__booking_approve( $booking_id );

			return true;
		}

		return false;
	}

