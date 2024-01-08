<?php
/*
This is COMMERCIAL SCRIPT
We are not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

// This function is called in page-gateways.php before showing payment forms and in wpbc-search-availability.php for search  results cost  hint
/**
 * Getting total cost  of the booking.
 * 
 * Example of function call:
        $total_cost_of_booking = wpbc_get_costs_i_hints__based_on_cost_calc( array(
              'form' => 'select-one^visitors'.$value->id.'^'.$min_free_items, 
              'days_input_format' => wpbc_get_comma_seprated_dates_from_to_day( date_i18n("d.m.Y", strtotime($bk_date_start) ), date_i18n("d.m.Y", strtotime($bk_date_finish) ) ), 
              'resource_id' => $value->id, 
              'booking_form_type' => apply_bk_filter('wpbc_get_default_custom_form', 'standard', $value->id )
          ) ) ;
 * 
 * @param $args
 *
 * @return array
 */
function wpbc_get_costs_i_hints__based_on_cost_calc( $args = array() ) {

	$defaults = array(
						'form'                          => '',              // text^cost_hint2^740.00~select-multiple^rangetime2[]^15:00 - 16:00~text^name2^~text^secondname2^~email^email2^~text^phone2^~select-one^accommodation_meals2^ ~select-one^visitors2^1~textarea^details2^~checkbox^term_and_condition2[]^
						'days_input_format'             => '',              // 'string' => "30.02.2014, 31.02.2014, 01.03.2014"
						'resource_id'                   => 1,               // ID of booking resource
						'booking_form_type'             => 'standard',      // Default custom  form  of booking resource
						'payment_cost'                  => 0.0,             // Amount to show in payment form NOW
						'is_check_additional_calendars' => true
					);
    $params = wp_parse_args( $args, $defaults );
            
    $_POST['booking_form_type'] = $params['booking_form_type'];                 // It's required for the correct calculation  of the Advanced Cost.
    
    make_bk_action('check_multiuser_params_for_client_side', $params[ 'resource_id' ] );


    $str_dates__dd_mm_yyyy = $params[ 'days_input_format' ];                    //FixIn: 5.4.5.2
    $booking_type          = $params[ 'resource_id' ];
    $booking_form_data     = $params[ 'form' ];

    $is_check_additional_calendars = $params[ 'is_check_additional_calendars' ];
    

    $dates_in_diff_formats = wpbc_get_dates_in_diff_formats( $str_dates__dd_mm_yyyy, $booking_type, $booking_form_data );

    $start_time         = $dates_in_diff_formats['start_time'];             // Array( [0] => 10, [1] => 00, [2] => 01 )
    $end_time           = $dates_in_diff_formats['end_time'];               // Array( [0] => 12, [1] => 00, [2] => 02 )
    $only_dates_array   = $dates_in_diff_formats['array'];                  // [0] => '2015-10-15', [1] => '2015-10-15'
    $dates              = $dates_in_diff_formats['string'];                 // '15.12.2015, 16.12.2015, 17.12.2015'


    // Get cost of main calendar with all rates discounts and  so on...
	$summ = wpbc_calc__booking_cost( array(
									  'resource_id'           => $booking_type                      // '2'
									, 'str_dates__dd_mm_yyyy' => $dates                             // '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023'
									, 'times_array' 	      => array( $start_time, $end_time )
									, 'form_data'             => $booking_form_data     		    // 'text^selected_short_timedates_hint4^06/11/2018 14:00...'
								));
    $summ = floatval( $summ );

    // Get only Original cost
	$summ_original = wpbc_calc__booking_cost( array(
										  'resource_id'           => $booking_type           			// '2'
										, 'str_dates__dd_mm_yyyy' => $dates     						// '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023'
										, 'times_array' 	      => array( $start_time, $end_time )
										, 'form_data'             => $booking_form_data     		 	// 'text^selected_short_timedates_hint4^06/11/2018 14:00...'
										, 'is_discount_calculate' => true
										, 'is_only_original_cost' => true
									));
    $summ_original = floatval( $summ_original );


    // Get description according coupons discount for main calendar if its exist
    $coupon_info_4_main_calendar = apply_bk_filter('wpdev_get_additional_description_about_coupons', '', $booking_type, $dates, array($start_time, $end_time ), $booking_form_data   );
	$coupon_discount_value		 = apply_bk_filter('wpbc_get_coupon_code_discount_value', '', $booking_type, $dates, array($start_time, $end_time ), $booking_form_data   );

    // Check additional cost based on several calendars inside of this form
    if ( $is_check_additional_calendars ) {
        $additional_calendars_cost = apply_bk_filter('wpbc_get_cost__in_additional_calendars', $summ, $booking_form_data, $booking_type,  array($start_time, $end_time)   ); // Apply cost according additional calendars

	    $summ_total       = $additional_calendars_cost['total_cost__in_all_calendars'];         // float
	    $summ_additional  = $additional_calendars_cost['cost_arr__in_extra_calendars'];         // [...]
	    $dates_additional = $additional_calendars_cost['dates_arr__in_extra_calendars'];        // [...]
    } else {
        $summ_total       = $summ;
        $summ_additional  = array();
        $dates_additional = array();
    }

	$additional_description = '';

	if ( count( $summ_additional ) > 0 ) {  // we have additional calendars inside of this form

            // Main calendar description and discount information
            $additional_description .= '<br />' . wpbc_get_resource_title($booking_type) . ': ' . wpbc_get_cost_with_currency_for_user( $summ, $booking_type );
            if ($coupon_info_4_main_calendar != '')
                $additional_description .=  ' '. $coupon_info_4_main_calendar . ' ' ;
            $coupon_info_4_main_calendar = '';
            $additional_description .= '<br />' ;

            // Additional calendars - information and discounts //
            foreach ($summ_additional as $key=>$ss) {

                $additional_description .= wpbc_get_resource_title($key) . ': ' . wpbc_get_cost_with_currency_for_user( $ss, $key );

                // Discounts
                $form_content_for_specific_calendar = wpbc_get__form_data__with_replaced_id( $booking_form_data, $key,  $booking_type );

                $dates_in_specific_calendar = $dates_additional[$key];
                $coupon_info_4_calendars = apply_bk_filter('wpdev_get_additional_description_about_coupons', '', $key , $dates_in_specific_calendar , array($start_time, $end_time ), $form_content_for_specific_calendar );
	            if ( $coupon_info_4_calendars != '' ) {
		            $additional_description .= ' ' . $coupon_info_4_calendars . ' ';
	            }
                $coupon_info_4_calendars = '';

                $additional_description .= '<br />' ;
            }
    }
 

	//FixIn: 8.8.3.15
	if ( 'On' === get_bk_option( 'booking_calc_deposit_on_original_cost_only' ) ) {
		$summ_deposit = apply_bk_filter( 'wpbc_calc__deposit_cost__if_enabled', $summ_original, $booking_type, $str_dates__dd_mm_yyyy ); // Apply fixed deposit
	} else {
		$summ_deposit = apply_bk_filter( 'wpbc_calc__deposit_cost__if_enabled', $summ_total, $booking_type, $str_dates__dd_mm_yyyy ); // Apply fixed deposit
	}


    $summ_balance = $summ_total - $summ_deposit;

	if ( $summ_balance < 0 ) {
		$summ_deposit = $summ_total;
		$summ_balance = 0;
	}

    $summ_additional_hint = $summ_total - $summ_original;
	$summ_additional_hint = ( $summ_additional_hint < 0 ) ? 0 : $summ_additional_hint;                                  //FixIn: 8.6.1.5


    return array(
        
        'additional_description' => $additional_description
            
        // N E W    
        , 'payment_cost'            => number_format( (float) $params['payment_cost'], wpbc_get_cost_decimals(), '.', '' )                  //FixIn: 8.3.2.1               //FixIn: 8.2.1.24

        , 'payment_cost_hint'       => wpbc_cost_show( $params['payment_cost'], array(  'currency' => 'CURRENCY_SYMBOL' ) )   
    
        , 'total_cost'              => number_format( (float) $summ_total, wpbc_get_cost_decimals(), '.', '' )                  //FixIn: 8.3.2.1
        , 'cost_hint'               => wpbc_cost_show( $summ_total, array(  'currency' => 'CURRENCY_SYMBOL' ) )                             // [cost_hint]            - TOTAL cost
        , 'total_cost_hint'         => wpbc_cost_show( $summ_total, array(  'currency' => 'CURRENCY_SYMBOL' ) )   
    
        , 'deposit_cost'            => number_format( (float) $summ_deposit, wpbc_get_cost_decimals(), '.', '' )                  //FixIn: 8.3.2.1
        , 'deposit_hint'            => wpbc_cost_show( $summ_deposit, array(  'currency' => 'CURRENCY_SYMBOL' ) )                           // [deposit_hint]         - Deposit cost
        , 'deposit_cost_hint'       => wpbc_cost_show( $summ_deposit, array(  'currency' => 'CURRENCY_SYMBOL' ) )   
        
        , 'balance_cost'            => number_format( (float) $summ_balance, wpbc_get_cost_decimals(), '.', '' )                  //FixIn: 8.3.2.1
        , 'balance_hint'            => wpbc_cost_show( $summ_balance, array(  'currency' => 'CURRENCY_SYMBOL' ) )                           // [balance_hint]         - Balance cost - difference between deposit and full cost
        , 'balance_cost_hint'       => wpbc_cost_show( $summ_balance, array(  'currency' => 'CURRENCY_SYMBOL' ) )   
    
        , 'original_cost'           => number_format( (float) $summ_original, wpbc_get_cost_decimals(), '.', '' )                  //FixIn: 8.3.2.1
        , 'original_cost_hint'      => wpbc_cost_show( $summ_original, array(  'currency' => 'CURRENCY_SYMBOL' ) )                          // [original_cost_hint]   - Cost of the booking only for selected dates only.
        
        , 'additional_cost'         => number_format( (float) $summ_additional_hint, wpbc_get_cost_decimals(), '.', '' )                  //FixIn: 8.3.2.1
        , 'additional_cost_hint'    => wpbc_cost_show( $summ_additional_hint, array(  'currency' => 'CURRENCY_SYMBOL' ) )                   // [additional_cost_hint] - Additional cost, which depends on the fields selection in the form. 
		
        , 'coupon_discount'         => number_format( (float) $coupon_discount_value, wpbc_get_cost_decimals(), '.', '' )                  //FixIn: 8.3.2.1
        , 'coupon_discount_hint'    => wpbc_cost_show( $coupon_discount_value, array(  'currency' => 'CURRENCY_SYMBOL' ) )
    );
}


// ---------------------------------------------------------------------------------------------------------------------
// Deposit
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Calculate fixed deposit cost if deposit enabled for resource
 *
 * @param $base_cost_for_deposit 75.0
 * @param $resource_id           4
 * @param $dates_str__d_m_y      06.11.2023,07.11.2023,08.11.2023
 *
 * @return float|int|mixed|string
 *
 */
function wpbc_calc__deposit_cost__if_enabled( $base_cost_for_deposit, $resource_id, $dates_str__d_m_y = false ) {

    $fixed_deposit = wpbc_get_resource_meta( $resource_id, 'fixed_deposit' );

    if ( ! empty( $fixed_deposit ) ) {

		$fixed_deposit = maybe_unserialize( $fixed_deposit[0]->value );
    } else {
	    $fixed_deposit = array(
								'amount'           => '100',
								'type'             => '%',
								'active'           => 'Off',
								'apply_after_days' => '0',
								'season_filter'    => '0'
							);
    }

	if ( $fixed_deposit['active'] == 'On' ) {

		$resource_deposit_amount          = $fixed_deposit['amount'];
		$resource_deposit_amount_apply_to = $fixed_deposit['type'];

		$resource_deposit_apply_after_days = ( isset( $fixed_deposit['apply_after_days'] ) ) ? $fixed_deposit['apply_after_days'] : '0';
		$resource_deposit_season_filter    = ( isset( $fixed_deposit['season_filter'] ) )    ? $fixed_deposit['season_filter']    : '0';

		// Check if the difference between TODAY and Check In date is valid for the Apply of deposit.
		if ( $dates_str__d_m_y !== false ) {
			$sortedDates = wpbc_get_sorted_days_array( $dates_str__d_m_y );
			if ( ! empty( $sortedDates ) ) {
				$dates_diff = wpbc_get_difference_in_days( '+' . $resource_deposit_apply_after_days . ' days', $sortedDates[0] );

				if ( $dates_diff > 0 ) {
					return $base_cost_for_deposit;
				}
			}
		}

		if ( ! wpbc_is_check_in_day_in_season_filter( $resource_deposit_season_filter, $dates_str__d_m_y ) ) {
			return $base_cost_for_deposit;
		}

	    if ( $resource_deposit_amount_apply_to == '%' ) {
		    $base_cost_for_deposit = $base_cost_for_deposit * $resource_deposit_amount / 100;
	    } else {
		    $base_cost_for_deposit = $resource_deposit_amount;
	    }
    }

	return $base_cost_for_deposit;
}


// ---------------------------------------------------------------------------------------------------------------------
// V a l u a t i o n    Days
// ---------------------------------------------------------------------------------------------------------------------

/**
	 * Get array of "Valuation days"
 * 
 * @param int $booking_type
 * @param array $days_array         - array( [0] => '10.10.2016', [1] => ' 11.10.2016', [2] => ' 12.10.2016', ...
 * @param array $times_array        - array ([0] => Array( [0] => 00, [1] => 00, [2] => 01 ),  [1] => Array ( [0] => 24, [1] => 00, [2] => 02 )  )
 * @return bool | array             - array ( [1] => 100 , [2] => add5, [3] => add5, [4] => add5, [5] => add5, [6] => 95%, [7] => 0, ... )
 */
function wpbc_get_valuation_days_array( $booking_type, $days_array, $times_array ){

    $days_costs = array();
    $maximum_days_count = count( $days_array );
    
    $costs_depends = wpbc_get_resource_meta( $booking_type, 'costs_depends' );

    if ( count( $costs_depends ) > 0 )        
        $costs_depends = maybe_unserialize( $costs_depends[0]->value );        
    else
        return false;

    if ( empty( $costs_depends ) ) return false;                                //FixIn: 7.0.1.4


    $sortedDates = wpbc_get_sorted_days_array( implode( ',', $days_array ) );
    if ( !empty( $sortedDates ) ) {
        $check_in_date = $sortedDates[0];
        $check_in_date = explode( ' ', $check_in_date );
        $check_in_date = $check_in_date[0];
        $check_in_date = explode( '-', $check_in_date );
        $check_in = array();
        $check_in['year'] =  intval( $check_in_date[0] );
        $check_in['month'] = intval( $check_in_date[1] );
        $check_in['day'] =   intval( $check_in_date[2] );
    } else
        return false;

    $is_togather_applied = false;                                               //FixIn: 7.0.1.7
//debuge('$costs_depends',$costs_depends, $days_array);    
    foreach ( $costs_depends as $value ) {                        // Loop all items of "Valuation days" cost settings.    
        
        if ( $value['active'] == 'On' ) {                         // Only Active
            
            // Season filters //////////////////////////////////////////////////
            
            $is_can_continue_with_this_item = true;

            if ( ! empty( $value['season_filter'] ) ) {                         // Check  if this day inside of filter
                $is_day_inside_of_filter = wpbc_is_day_inside_of_filter( $check_in['day'], $check_in['month'], $check_in['year'], $value['season_filter'] );
                if ( ! $is_day_inside_of_filter )
                    $is_can_continue_with_this_item = false;
            }
            if ( ! $is_can_continue_with_this_item )
                continue;
            
            ////////////////////////////////////////////////////////////////////

            if ( $value['type'] == 'summ' ) {

                // Check  situation, when the "Together" date alredy set  by some other setting ///////
                if ( isset( $days_costs[$value['from']] ) ) {
                    $is_can_continue = false;
                    for ( $ii = 1; $ii < $value['from']; $ii++ ) { // Recheck if all previous dates are also set - its mean that was set "Together" option
                        if ( !isset( $days_costs[$ii] ) ) {
                            $is_can_continue = true;            // We have one date not set, its mean the previousl was set For or From selecttors, and we can apply Together
                        }
                    }
                    if ( !$is_can_continue )
                        continue;                    // Aleready set this option
                } //////////////////////////////////////////////////////////////

                if ( $value['from'] <= ($maximum_days_count) ) {                // We will apply Togather       //FixIn: 7.0.1.7
                    $is_togather_applied = true;                                    
                }
                
                if ( $value['cost_apply_to'] == '%' )
                    $value['cost'] .= '%';

                if ( $value['from'] == ($maximum_days_count) ) {

                    $days_costs[$value['from']] = $value['cost'];

                    if ( strpos( $value['cost'], '%' ) !== false )
                        $assign_value = $value['cost'];
                    else
                        $assign_value = 0;

                    for ( $ii = 1; $ii < $value['from']; $ii++ ) {
                        $days_costs[$ii] = $assign_value;
                    }

                // return $days_costs;                                          //FixIn:6.2.1.3
                } elseif ( $value['from'] < ($maximum_days_count) ) {

                    $days_costs[$value['from']] = $value['cost'];
                    if ( strpos( $value['cost'], '%' ) !== false )
                        $assign_value = $value['cost'];
                    else
                        $assign_value = 0;
                    for ( $ii = 1; $ii < $value['from']; $ii++ ) {
                        $days_costs[$ii] = $assign_value;
                    }
                }
                
            } elseif ( $value['type'] == '=' ) {
//debuge($days_costs,$value, (int) $is_togather_applied , $maximum_days_count);   				
                if ( strtolower( $value['from'] ) == 'last' ) {                 //FixIn: 7.0.1.7
                 
                    //if ( $is_togather_applied ) {                               // Previously  we was applied TOGETHER,  so no need to  apply LAST,  because its have to  be already calculated in togather term (//FixIn: 7.0.1.7)
					
					//FixIn: 7.2.1.20  - Apply For = LAST,  event if previoys TOGATHER = NN% settings was applyied
					if (	( $is_togather_applied ) 
						 && ( ( isset( $days_costs[ $maximum_days_count ] ) ) && ( strpos( $days_costs[ $maximum_days_count ], '%' ) === false ) ) 
					){ 
                        continue;;      
                    }
                    $value['from'] = $maximum_days_count;
                }
                // if ( isset( $days_costs[ $value['from'] ] ) ) continue;      // Aleready set this option      //FixIn:6.2.1.3
                
                if ( $value['from'] <= $maximum_days_count ) {

                    if ( $value['cost_apply_to'] == 'add' )
                        $days_costs[$value['from']] = 'add' . $value['cost'];
                    elseif ( $value['cost_apply_to'] == '%' )
                        $days_costs[$value['from']] = $value['cost'] . '%';
                    elseif ( $value['cost_apply_to'] == 'fixed' )
                        $days_costs[$value['from']] = $value['cost'];
                    else
                        $days_costs[$value['from']] = $value['cost'];
                }

            } elseif ( $value['type'] == '>' ) {
                for ( $i = $value['from']; $i <= $value['to']; $i++ ) {
                    if ( $i <= $maximum_days_count )
                        if ( !isset( $days_costs[$i] ) ) {

                            if ( $value['cost_apply_to'] == 'add' )         $days_costs[$i] = 'add' . $value['cost'];
                            elseif ( $value['cost_apply_to'] == '%' )       $days_costs[$i] = $value['cost'] . '%';
                            elseif ( $value['cost_apply_to'] == 'fixed' )   $days_costs[$i] = $value['cost'];
                            else                                            $days_costs[$i] = $value['cost'];
                        }
                }
            }
        }
    }



    for ( $i = 1; $i <= $maximum_days_count; $i++ ) {
        if ( !isset( $days_costs[$i] ) ) {
            $days_costs[$i] = '100%';
        }
    }
    ksort( $days_costs );
//debuge($days_costs);
    return $days_costs;
}

function add_coupon_discount($coupon_code, $type_id,$price){
		if($coupon_code){
			$coupon_meta = wpbc_get_resource_meta( $type_id, 'coupons' );

			if(count( $coupon_meta ) > 0){
				$coupon_meta_data = maybe_unserialize( $coupon_meta[0]->value );
				if( count ( $coupon_meta_data ) > 0 ){
					$cur_coupon = false;
					foreach($coupon_meta_data as &$coupon){
						if($coupon['coupon_code'] == $coupon_code){
							$cur_coupon = $coupon;
						}
					}

					if( $cur_coupon ) { 
						if($cur_coupon['coupon_type'] == '%'){
							$price = $price * ( 1 - ($cur_coupon['coupon_amount'] / 100));
						}else{
							$price = $price - $cur_coupon['coupon_amount'];
						}
					}
				}
			}
		}			
		
		return $price;
}

//FixIn: 9.8.0.4

	/**
	 * Get 1 DAY cost OR cost from time to  time at  $times_array
	 *
	 * @param $type_id          "3"
	 * @param $base_cost        "25"        it's cost saved in DB for booking resource
	 * @param int $day          28
	 * @param int $month        8
	 * @param int $year         2023
	 * @param $times_array
	 * @param $post_form
	 *
	 * @return false|float|int|mixed|string|string[]|null
	 */
	function wpbc_get_1_day_cost_apply_rates( $type_id, $base_cost, $day, $month, $year, $times_array = false, $post_form = '' ) {

		$price_period = get_bk_option( 'booking_paypal_price_period' );             // Get cost period and set multiplier for it.

		if ( $price_period == 'day' ) {             $cost_multiplier = 1;
		} elseif ( $price_period == 'night' ) {     $cost_multiplier = 1;
		} elseif ( $price_period == 'hour' ) {      $cost_multiplier = 24;          // Day have 24 hours
		} else {                                    $cost_multiplier = 1;           // fixed  // return $base_cost;
		}

		$rate_meta_res = wpbc_get_resource_meta( $type_id, 'rates' );               // Get all RATES for this bk resource

		if ( count( $rate_meta_res ) > 0 ) {
			if ( is_serialized( $rate_meta_res[0]->value ) ) {
				$rate_meta = unserialize( $rate_meta_res[0]->value );
			} else {
				$rate_meta = $rate_meta_res[0]->value;
			}

			$rate         = $rate_meta['rate'];                         // Rate values                           (key -> ID)
			$seasonfilter = $rate_meta['filter'];                       // If this filter assign to rate On/Off  (key -> ID)

			if ( isset( $rate_meta['rate_type'] ) ) {
				$rate_type = $rate_meta['rate_type'];                   // is rate curency or %
			} else {
				$rate_type = array();
			}


			/////////////////////////////////////////////////////////////////////////////////////////////////////////
			// Get    B A S E    C O S T   with   Rates  and get    H O U R L Y   R a t e s
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
			$base_cost_with_rates = $base_cost;
			$hourly_rates         = array();
			////////////////////////////////////////////////////////////////
			// Get here Cost of the day with rates - $base_cost_with_rates, If curency rate is assing for this day so then just assign it and stop
			// also get all hour filters rates
			foreach ( $seasonfilter as $filter_id => $is_filter_ON ) {  // Id_filter => On  || Id_filter => Off
				if ( $is_filter_ON == 'On' ) {                                       // Only activated filters
					$is_day_inside_of_filter = wpbc_is_day_inside_of_filter( $day, $month, $year, $filter_id );  // Check  if this day inside of filter

					if ( $is_day_inside_of_filter === true ) {              // If return true then Only D A Y filters here
						if ( isset( $rate_type[ $filter_id ] ) ) {                    // It Can be situation that in previos version is not set rate_type so need to check its
							if ( $rate_type[ $filter_id ] == '%' ) {
								$base_cost_with_rates = ( ( $base_cost_with_rates * $rate[ $filter_id ] / 100 ) );      // %
							} else {                                            // Here is the place where we need in future create the priority of rates according direct curency value
								$base_cost_with_rates = $rate[ $filter_id ];
								break;                                          // Here rate_type  == 'curency so we return direct value and break all other rates
							}
						} else {
							$base_cost_with_rates = ( ( $base_cost_with_rates * $rate[ $filter_id ] / 100 ) );
						} // Default - %
					}

					if ( is_array( $is_day_inside_of_filter ) ) {              // Its HOURLY filter, save them for future work
						if ( $is_day_inside_of_filter[0] == 'hour' ) {
							$hourly_rates[ $filter_id ] = array(
								'rate'      => $rate[ $filter_id ],
								'rate_type' => $rate_type[ $filter_id ],
								'time'      => array(
									$is_day_inside_of_filter[1],
									$is_day_inside_of_filter[2]
								)
							);
						}
					}

				} // close ON if
			}  // close foreach


					// Customization for the  Joao ///////////////////////////////////////////////////////////////////////
					/**
					 * Different Rates of the dates,  depends on from  selection  of options in selectboxes.
					 * -----------------------------------------------------------------------------------------
					 * Configuration  like:  [visitors=1:270;2:300;3:380;4:450]                                         //FixIn: 9.8.0.5
					 * can  be definition  of the rates at  the Booking > Resources > Cost and rates > Rates page.
					 * It is means that instead of 270 USD for specific rate we can use  [visitors=1:270;2:300;3:380;4:450]
					 * (where different selection of visitors define the different rate for specific season
					 * this rate configuration (which  can  be assigned to the specific season  filter - its mean to the specific dates in calendar)
					 * //TODO: improve UI for this functionality
		             */
					if ( $post_form != '' ) {
						$booking_form_show = wpbc__legacy__get_form_content_arr( $post_form, $type_id );
						$booking_form_show = $booking_form_show['_all_'];;
					}
					if ( strpos( $base_cost_with_rates, '=' ) ) {

						$base_cost_with_rates = str_replace( '[', '', $base_cost_with_rates );  // [visitors=1:140;2:150]
						$base_cost_with_rates = str_replace( ']', '', $base_cost_with_rates );
						$base_cost_with_rates = explode( '=', $base_cost_with_rates );

						$my_field_name = $base_cost_with_rates[0];                  // visitors

						$my_temp_field_values = explode( ';', $base_cost_with_rates[1] );
						$my_field_values      = array();
						foreach ( $my_temp_field_values as $m_value ) {
							$m_value                        = explode( ':', $m_value );
							$my_field_values[ $m_value[0] ] = $m_value[1];
						}
						/*[1] => Array
								(
									[1] => 140
									[2] => 150
								)*/
						if ( $post_form != '' ) {
							foreach ( $booking_form_show as $bk_ft_key => $bk_ft_value ) {
								if ( $bk_ft_key == ( $my_field_name . $type_id ) ) {
									if ( isset( $my_field_values[ $bk_ft_value ] ) ) {
										$base_cost_with_rates = $my_field_values[ $bk_ft_value ];
										break;
									}
								}
							}
						} else {
							$my_field_values_a    = array_values( $my_field_values );
							$base_cost_with_rates = array_shift( $my_field_values_a );
						}
						if ( is_array( $base_cost_with_rates ) ) {
							$my_field_values_a    = array_values( $my_field_values );
							$base_cost_with_rates = array_shift( $my_field_values_a );
						}
					}
					// Customization for the  Joao ///////////////////////////////////////////////////////////////////////


			if ( ( count( $hourly_rates ) == 0 ) && ( $price_period == 'fixed' ) ) {
				return $base_cost_with_rates;
			}

			// H O U R s ///////////////////////////////////////////////////
			$general_hours_arr = array();

			/////////////////////////////////////////////////////////////////////////////////////////////////////////
			// Get   S T A R T  and   E N D   T i m e   for this day (or 0-24 or from function params $starttime)
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
			if ( $times_array === false ) {                                   // Time is not pass to the function
				$global_start_time = array( '00', '00', '00' );
				$global_finis_time = array( '24', '00', '00' );
			} else {                                                        // Time is set and we need calculate cost between it
				$global_start_time = $times_array[0];
				if ( count( $times_array ) > 1 ) {
					$global_finis_time = $times_array[1];
				} else {
					$global_finis_time = array( '24', '00', '00' );
				}
				if ( is_string( $global_start_time ) ) {
					$global_start_time    = explode( ':', $global_start_time );
					$global_start_time[2] = '00';
				}
				if ( is_string( $global_finis_time ) ) {
					$global_finis_time    = explode( ':', $global_finis_time );
					$global_finis_time[2] = '00';
				}
				if ( $global_finis_time == array( '00', '00', '00' ) ) {
					$global_finis_time = array( '24', '00', '00' );
				}
			}
			$general_hours_arr[ wpbc_get_minutes_num_from_time( $global_start_time ) * 1000000 ] = array(
				'start',
				$base_cost_with_rates,
				''
			);  // start glob work times array
			$general_hours_arr[ wpbc_get_minutes_num_from_time( $global_finis_time ) * 1000000 ] = array(
				'end',
				$base_cost_with_rates,
				''
			);  // end glob work times array
			/////////////////////////////////////////////////////////////////////////////////////////////////////////


			/////////////////////////////////////////////////////////////////////////////////////////////////////////
			// Get   all   H O U R L Y    R A T E S    in    S o r t e d    by  Minutes*100   array
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
			foreach ( $hourly_rates as $hour_filter_id => $hour_rate ) {
				if ( ! isset( $hour_rate['rate_type'] ) ) {
					$hour_rate['rate_type'] = '%';
				}

				$r__start = 1000000 * wpbc_get_minutes_num_from_time( $hour_rate['time'][0] );
				$r__fin   = 1000000 * wpbc_get_minutes_num_from_time( $hour_rate['time'][1] );
				while ( isset( $general_hours_arr[ $r__start ] ) ) {
					$r__start --;
				}
				while ( isset( $general_hours_arr[ $r__fin ] ) ) {
					$r__fin --;
				}

				$general_hours_arr[ $r__start ] = array( 'rate_start', $hour_rate['rate'], $hour_rate['rate_type'] );
				$general_hours_arr[ $r__fin ]   = array( 'rate_end', $hour_rate['rate'], $hour_rate['rate_type'] );
			}
			ksort( $general_hours_arr );                                    // SORT time(rate) arrays with start/end time
			/////////////////////////////////////////////////////////////////////////////////////////////////////////


			if ( ( $price_period == 'hour' ) ||                            // Get hour rates, already based on cost with applying rates for days not hours
			     ( ( $price_period == 'fixed' ) && ( count( $hourly_rates ) > 0 ) )
			) {
				$base_hour_cost = $base_cost_with_rates;
			} else {
				$base_hour_cost = $base_cost_with_rates / 24;
			}


			$is_continue        = false;                                           // Calculate cost for our times in array segments
			$general_summ_array = array();
			$cur_rate           = $base_hour_cost;
			$cur_type           = 'curency';
			foreach ( $general_hours_arr as $minute_time => $rate_value ) {

				if ( $is_continue ) {                                         // Calculation
					if ( $cur_type == 'curency' ) {
						if ( $price_period == 'fixed' ) {
							$general_summ_array[] = $cur_rate;
						} else {
							$general_summ_array[] = wpbc_get_cost_between_times( array(
								$previos_time[0],
								$minute_time
							), $cur_rate );
						}
					} else {
						$procent_base         = wpbc_get_cost_between_times( array(
							$previos_time[0],
							$minute_time
						), $base_hour_cost );
						$general_summ_array[] = ( ( $procent_base * $cur_rate / 100 ) ); // %
					}
				}

				if ( $rate_value[0] == 'start' ) {
					$is_continue = true;
				}   // start calculate from this time
				if ( $rate_value[0] == 'end' ) {
					break;
				}                 // Finish calculation

				$previos_time = array( $minute_time, $rate_value );           // Save previos time and rate

				if ( $rate_value[0] == 'rate_start' ) {                              // RATE start so get type and value of rate
					$cur_type = $rate_value[2];
					if ( ( $price_period == 'hour' ) || ( $price_period == 'fixed' ) ) {
						$cur_rate = $rate_value[1];
					} else {
						if ( $cur_type == 'curency' ) {
							$cur_rate = $rate_value[1] / 24;
						} else {
							$cur_rate = $rate_value[1];
						}
					}

				}
				if ( $rate_value[0] == 'rate_end' ) {                              // Rate end so set standard  type and rate
					$cur_rate = $base_hour_cost;
					$cur_type = 'curency';
				}
			} // close foreach time cost array


			if ( count( $general_hours_arr ) > 0 ) {                          // summ all costs into one variable - its 1 day cost ( or cost between times), with already aplly day rates filters
				if ( $price_period == 'fixed' ) {
					$return_cost = $general_summ_array[0];
				} else {
					$return_cost = 0;
					foreach ( $general_summ_array as $vv ) {
						$return_cost += $vv;
					}
				}
			} else {
				$return_cost = $base_cost_with_rates;
			}


			return $return_cost;   // Everything is calculated based on hours


		} // Finish R A T E S  work


		// There    N o    R A T E S  at all
		if ( $times_array === false ) {
			return $cost_multiplier * $base_cost;
		}      // No times, cost for 1 day
		else { // Also need to check according times hour
			if ( $price_period == 'hour' ) {
				$hour_cost = $base_cost;
			} else {
				if ( $price_period == 'fixed' ) {
					return $base_cost;
				} else {
					$hour_cost = $base_cost / 24;
				}
			}

			return wpbc_get_cost_between_times( $times_array, $hour_cost );                     // Cost for some time interval
		}

	}


		/**
		 * Get COST based on hourly rate - $hour_cost and start and end time during 1 day
		 *
		 * @param $times_array (start_minutes, end minutes) | ("12:00", "17:30") | (array("12","00","00"), array("22", "00", "00"))
		 * @param $hour_cost
		 *
		 * @return float|int
		 */
		function wpbc_get_cost_between_times( $times_array, $hour_cost ) {

			$start_time = $times_array[0];      // Get Times
			if ( count( $times_array ) > 1 ) {
				$end_time = $times_array[1];
			} else {
				$end_time = array( '24', '00', '00' );
			}

			if ( $end_time == array( '23', '59', '02' ) ) {
				$end_time = array( '24', '00', '00' );
			}                      //FixIn: 6.2.3.1

			if ( is_string( $start_time ) ) {
				$start_time    = explode( ':', $start_time );
				$start_time[2] = '00';
			}
			if ( is_string( $end_time ) ) {
				$end_time    = explode( ':', $end_time );
				$end_time[2] = '00';
			}

			if ( ( is_int( $end_time ) ) && ( is_int( $start_time ) ) ) {   // 1000000 correction need to make.

				if ( $end_time > 1000000 ) {
					$ostatok = $end_time % 1000000;
					if ( $ostatok == 0 ) {
						$end_time = $end_time / 1000000;
					} else {
						$end_time = ( $end_time + ( 1000000 - $ostatok ) ) / 1000000;
					}
				}

				if ( $start_time > 1000000 ) {
					$ostatok = $start_time % 1000000;
					if ( $ostatok == 0 ) {
						$start_time = $start_time / 1000000;
					} else {
						$start_time = ( $start_time + ( 1000000 - $ostatok ) ) / 1000000;
					}
				}

				//return round(  ( ($end_time - $start_time) * ($hour_cost / 60 ) ) , 2 );
				return ( ( $end_time - $start_time ) * ( $hour_cost / 60 ) );                             //FixIn: 7.0.1.44
			}

			if ( empty( $start_time[0] ) ) {
				$start_time[0] = '00';
			}
			if ( empty( $end_time[0] ) ) {
				$end_time[0] = '00';
			}

			if ( ! isset( $start_time[1] ) ) {
				$start_time[1] = '00';
			}
			if ( ! isset( $end_time[1] ) ) {
				$end_time[1] = '00';
			}


			if ( ( $end_time[0] == '00' ) && ( $end_time[1] == '00' ) ) {
				$end_time[0] = '24';
			}


			$m_dif = ( $end_time[0] * 60 + intval( $end_time[1] ) ) - ( $start_time[0] * 60 + intval( $start_time[1] ) );
			$h_dif = intval( $m_dif / 60 );
			$m_dif = ( $m_dif - ( $h_dif * 60 ) ) / 60;

			//$summ = round( ( 1 * $h_dif * $hour_cost ) + ( 1 * $m_dif * $hour_cost ) , 2);
			$summ = ( 1 * $h_dif * $hour_cost ) + ( 1 * $m_dif * $hour_cost );                          //FixIn: 7.0.1.44

			return $summ;
		}


		/**
		 * Get count of MINUTES from time in format "17:20" or array(17, 20)
		 *
		 * @param $time_array       "17:20" | array(17, 20)
		 *
		 * @return false|float|int|mixed
		 */
		function wpbc_get_minutes_num_from_time( $time_array ) {

			if ( is_string( $time_array ) ) {
				$time_array = explode( ':', $time_array );
			}

			if ( is_array( $time_array ) ) {
				return ( $time_array[0] * 60 + intval( $time_array[1] ) );
			}

			return $time_array;
		}
