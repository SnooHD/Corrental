<?php
/*
This is COMMERCIAL SCRIPT
We are not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/



/**
 * Check if the system use check in/out times (change over days functionality) at this page
 *
 * @param $request_uri_init_param   usually  it's $_SERVER['REQUEST_URI']  but if used in Ajax request,  then  other parameter  here,
 *                                           by default, it = false,  it's the same as $_SERVER['REQUEST_URI']
 *
 * @return bool
 */
function wpbc_is_booking_used_check_in_out_time( $request_uri_init_param = false ){																		//FixIn: 8.9.4.10

	if ( false === $request_uri_init_param ) {
		$request_uri_init_param = $_SERVER['REQUEST_URI'];
	}
	$request_uri = $request_uri_init_param;


	$is_check_in_out_time = false;

	if ( get_bk_option( 'booking_range_selection_time_is_active' ) == 'On' ) {

		$is_check_in_out_time = true;

        $is_excerpt_on_pages = get_bk_option( 'booking_change_over__is_excerpt_on_pages'  );

        if ( 'On' == $is_excerpt_on_pages ) {

			/**
			 *  Array of pages with  relative paths, where we will NOT  use check in/out times
			 */
            $no_check_in_out__on_pages = get_bk_option( 'booking_change_over__excerpt_on_pages' );

            $no_check_in_out__on_pages = preg_split('/[\r\n]+/', $no_check_in_out__on_pages, -1, PREG_SPLIT_NO_EMPTY);

			/**
			 * Get request page URI
			 */
            if (
            	   ( strpos( $request_uri, 'booking_hash=') !== false )
                || ( strpos( $request_uri, 'check_in=') !== false )
            ) {
                $request_uri = parse_url($request_uri);
                if (  ( ! empty($request_uri ) ) && ( isset($request_uri['path'] ) )  ){
                    $request_uri = $request_uri['path'];
                } else {
                    $request_uri = $request_uri_init_param;
                }
            }

	        if (
					( ! empty( $no_check_in_out__on_pages ) )
				 && ( in_array( $request_uri, $no_check_in_out__on_pages ) )
			) {
		        $is_check_in_out_time = false;
	        }
        }
	}

	return $is_check_in_out_time;
}


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  S u p p o r t    f u n c t i o n s       ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Convert dates like this: '3,7-10,12' to this: '3,7,8,9,10,12
	 * 
	 * @param $specific_selected_dates	'3,7-10,12'
	 *
	 * @return string		'3,7,8,9,10,12
	 *
	 */
	function wpbc_get_specific_range_dates__as_comma_list( $specific_selected_dates ) {
	
		$specific_selected_dates    = explode( ',', $specific_selected_dates );
		$js_specific_selected_dates = array();
		
		foreach ( $specific_selected_dates as $value ) {
			if ( '' !== $value ) {
				$is_range = strpos( $value, '-' );
				if ( $is_range > 0 ) {
					$value     = explode( '-', $value );
					$max_value = ( $value[1] > 3650 ) ? 3650 : $value[1];        //FixIn: 8.7.3.4
					for ( $ii = $value[0]; $ii <= $max_value; $ii ++ ) {
						$js_specific_selected_dates[] = max( $ii, 1 );
					}
				} else {
					$js_specific_selected_dates[] = max( $value, 1 );
				}
			}
		}
		$js_specific_selected_dates = array_unique( $js_specific_selected_dates );        //FixIn: 9.7.3.8
		sort($js_specific_selected_dates);
		$js_specific_selected_dates = implode( ',', $js_specific_selected_dates );
	
		return $js_specific_selected_dates;
	}

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  Filters interface     Controll elements  ///////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//FixIn: 9.6.3.5


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  S Q L   Modifications  for  Booking Listing  ///////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Pay status
        function get_s_bklist_sql_paystatus($blank, $wh_pay_status ){
            $sql_where = '';

            if ( (isset($_REQUEST['wh_pay_status']) ) && ( $_REQUEST['wh_pay_status'] != 'all') ) {

                $sql_where .= " AND ( ";

                // Check  firstly if we are selected some goup of payment status
                if ($_REQUEST['wh_pay_status'] == 'group_ok' ) {                // SUCCESS

                   $payment_status = wpbc_get_payment_status_ok();

                   foreach ($payment_status as $label) {
                       $sql_where .= " ( bk.pay_status = '". $label ."' ) OR";
                   }
                   $sql_where = substr($sql_where, 0, -2);

                } else if ( ($_REQUEST['wh_pay_status'] == 'group_unknown' ) || (is_numeric($wh_pay_status)) || ($wh_pay_status == '') ) {     // UNKNOWN

                   $payment_status = wpbc_get_payment_status_unknown();
                   foreach ($payment_status as $label) {
                       $sql_where .= " ( bk.pay_status = '". $label ."' ) OR";
                   }
                   //$sql_where = substr($sql_where, 0, -2);
                   $sql_where .= " ( bk.pay_status = '' ) OR ( bk.pay_status regexp '^[0-9]') ";

                } else if ($_REQUEST['wh_pay_status'] == 'group_pending' ){     // Pending

                   $payment_status = wpbc_get_payment_status_pending();
                   foreach ($payment_status as $label) {
                       $sql_where .= " ( bk.pay_status = '". $label ."' ) OR";
                   }
                   $sql_where = substr($sql_where, 0, -2);

                } else if ($_REQUEST['wh_pay_status'] == 'group_failed' ) {     // Failed

                   $payment_status   = wpbc_get_payment_status_error();
                   foreach ($payment_status as $label) {
                       $sql_where .= " ( bk.pay_status = '". $label ."' ) OR";
                   }
                   $sql_where = substr($sql_where, 0, -2);

                } else {                                                        // CUSTOM Payment Status
                    $sql_where .= " bk.pay_status = '" . $wh_pay_status . "' ";
                }

                $sql_where .= " ) ";
            }

            return $sql_where;
        }
        add_bk_filter('get_bklist_sql_paystatus', 'get_s_bklist_sql_paystatus');

        // Cost
        function get_s_bklist_sql_cost($blank, $wh_cost, $wh_cost2  ){
            $sql_where = '';

            if ( $wh_cost   !== '' )    $sql_where.=   " AND (  bk.cost >= " . $wh_cost . " ) ";
            if ( $wh_cost2  !== '' )    $sql_where.=   " AND (  bk.cost <= " . $wh_cost2 . " ) ";

            return $sql_where;
        }
        add_bk_filter('get_bklist_sql_cost', 'get_s_bklist_sql_cost');



        function wpdev_bk_listing_show_payment_label(  $is_paid, $pay_print_status , $real_payment_status_label, $real_payment_css = '' ){	//FixIn: 8.7.7.13

        	if ( $pay_print_status == 'Completed' ) {            //FixIn: 8.4.7.11
        		$pay_print_status = __( 'Completed', 'booking' );
			}
	        $real_payment_css = empty( $real_payment_css ) ? $real_payment_status_label : $real_payment_css;			//FixIn: 8.7.7.13
	        $css_payment_label = 'payment-label-' . wpbc_check_payment_status( $real_payment_css );						//FixIn: 8.7.7.13
            if ($is_paid) { ?><span class="label label-default label-payment-status label-success <?php echo $css_payment_label; ?> "><?php echo '<span style="font-size:07px;">'.__('Payment' ,'booking') .'</span> '.$pay_print_status ; ?></span><?php     }
            else          {               
                ?><span class="label label-default label-payment-status <?php echo $css_payment_label; ?> "><?php  echo '<span style="font-size:07px;">'.__('Payment' ,'booking') .'</span> '. $pay_print_status; ; ?></span><?php
           }
        }
        add_bk_action( 'wpdev_bk_listing_show_payment_label', 'wpdev_bk_listing_show_payment_label');


        function wpdev_bk_get_payment_status_simple($bk_pay_status) {

            if ( wpbc_is_payment_status_ok( trim($bk_pay_status) ) ) $is_paid = 1 ;
            else $is_paid = 0 ;

            $payment_status_titles = get_payment_status_titles();
            $payment_status_titles_current = array_search($bk_pay_status, $payment_status_titles);
            if ($payment_status_titles_current === FALSE ) $payment_status_titles_current = $bk_pay_status ;

            $pay_print_status = '';

            if ($is_paid) {
                $pay_print_status = __('Paid OK' ,'booking');
                if ($payment_status_titles_current == 'Completed') $pay_print_status = $payment_status_titles_current;
            } else if ( (is_numeric($bk_pay_status)) || ($bk_pay_status == '') )        {
                $pay_print_status = __('Unknown' ,'booking');
            } else  {
                $pay_print_status = $payment_status_titles_current;
            }

            return $pay_print_status;

        }
        

    function get_payment_status_titles() {

        $payment_status_titles = array(
            __( 'Completed', 'booking' ) => 'Completed',
            __( 'In-Progress', 'booking' ) => 'In-Progress',
            __( 'Unknown', 'booking' ) => '1',
            __( 'Partially paid', 'booking' ) => 'partially',
            __( 'Cancelled', 'booking' ) => 'canceled',
            __( 'Failed', 'booking' ) => 'Failed',
            __( 'Refunded', 'booking' ) => 'Refunded',
            __( 'Fraud', 'booking' ) => 'fraud'
        );

        return $payment_status_titles;


        $payment_status_titles = array(
            __( '!Paid OK', 'booking' ) => 'OK',
            __( 'Unknown status', 'booking' ) => '1',
            __( 'Not Completed', 'booking' ) => 'Not_Completed',
            // PayPal statuses
            __( 'Completed', 'booking' ) => 'Completed',
            __( 'Pending', 'booking' ) => 'Pending',
            __( 'Processed', 'booking' ) => 'Processed',
            __( 'In-Progress', 'booking' ) => 'In-Progress',
            __( 'Canceled_Reversal', 'booking' ) => 'Canceled_Reversal',
            __( 'Denied', 'booking' ) => 'Denied',
            __( 'Expired', 'booking' ) => 'Expired',
            __( 'Failed', 'booking' ) => 'Failed',
            __( 'Partially_Refunded', 'booking' ) => 'Partially_Refunded',
            __( 'Refunded', 'booking' ) => 'Refunded',
            __( 'Reversed', 'booking' ) => 'Reversed',
            __( 'Voided', 'booking' ) => 'Voided',
            __( 'Created', 'booking' ) => 'Created',
            // Sage Statuses
            __( 'Not authed', 'booking' ) => 'not-authed',
            __( 'Malformed', 'booking' ) => 'malformed',
            __( 'Invalid', 'booking' ) => 'invalid',
            __( 'Abort', 'booking' ) => 'abort',
            __( 'Rejected', 'booking' ) => 'rejected',
            __( 'Error', 'booking' ) => 'error',
            __( 'Partially paid', 'booking' ) => 'partially',
            __( 'Cancelled', 'booking' ) => 'canceled',
            __( 'Fraud', 'booking' ) => 'fraud',
            __( 'Suspended', 'booking' ) => 'suspended'
        );
        return $payment_status_titles;
    }

