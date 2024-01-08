<?php
/*
This is COMMERCIAL SCRIPT
We are not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

require_once( WPBC_PLUGIN_DIR . '/inc/_bs/maybe_payment.php' );           // Maybe get payment form for confirmation    // NEW 9.8

require_once(WPBC_PLUGIN_DIR. '/inc/_bs/lib_s.php' );
require_once(WPBC_PLUGIN_DIR. '/inc/_bs/wpbc-s-costs.php' );
require_once(WPBC_PLUGIN_DIR. '/inc/_bs/s-toolbar.php' );

require_once(WPBC_PLUGIN_DIR. '/inc/_bs/admin/api-settings-s.php' );            // Settings page
require_once(WPBC_PLUGIN_DIR. '/inc/_bs/admin/activation-s.php' );              // Activate / Deactivate

require_once( WPBC_PLUGIN_DIR . '/inc/_bs/admin/page-email-payment.php' );      // Email - Payment Request

require_once( WPBC_PLUGIN_DIR . '/inc/gateways/wpbc-class-gw-api.php' );        //Payment Gateways API - Abstract Class
require_once( WPBC_PLUGIN_DIR . '/inc/gateways/page-gateways.php' );            //Payment Gateways - General Settings Page


if ( file_exists( WPBC_PLUGIN_DIR . '/inc/_bm/biz_m.php' ) ) { require_once( WPBC_PLUGIN_DIR . '/inc/_bm/biz_m.php' ); }


class wpdev_bk_biz_s {

    var $wpdev_bk_biz_m;


    function __construct() {
         
        add_filter('wpdev_booking_form', array(&$this, 'add_paypal_form'));     										// Add DIV structure, where to show payment form

        add_action('wpbc_define_js_vars',   array(&$this, 'wpbc_define_js_vars') );
        add_action('wpbc_enqueue_js_files', array(&$this, 'wpbc_enqueue_js_files') );


	    add_filter( 'wpbc_booking_form_html__update__append_change_over_times', array( $this, 'wpbc_booking_form_html__update__append_change_over_times' ), 10, 2 );
		
        add_bk_filter('get_booking_cost_from_db', array(&$this, 'get_booking_cost_from_db'));
		
        add_bk_action('wpdev_change_payment_status', array(&$this, 'wpdev_change_payment_status'));          			// IPN

		// Delete all Pending Unpaid bookings, which older than N days
        add_bk_action('check_pending_unpaid_bookings__do_auto_cancel', array(&$this, 'check_pending_unpaid_bookings__do_auto_cancel'));



         if ( class_exists('wpdev_bk_biz_m')) {
                $this->wpdev_bk_biz_m = new wpdev_bk_biz_m();
        } else { $this->wpdev_bk_biz_m = false; } 

    }


	/**
	 * Get cost of booking from DB
	 *
	 * @param $booking_cost	= ''  always empty string
	 * @param $booking_id		  ID of booking
	 *
	 * @return string
	 */
	function get_booking_cost_from_db( $booking_cost, $booking_id ) {

	    return wpbc_db__get_booking__cost( $booking_id );
    }


    // Check and delete all Pending not paid bookings, which older than a 1-n days
    function check_pending_unpaid_bookings__do_auto_cancel($bk_type) {

	    // if ( defined('WP_ADMIN') ) if ( WP_ADMIN === true )  return;
	    $is_check_active = get_bk_option( 'booking_auto_cancel_pending_unpaid_bk_is_active' );   // Is this function Active
	    if ( $is_check_active != 'On' ) {
		    return;
	    }

	    global $wpdb;
	    $num_of_hours_ago = get_bk_option( 'booking_auto_cancel_pending_unpaid_bk_time' );        // Num of hours ago for specific booking

	    if ( strpos( $num_of_hours_ago, ':' ) === false ) {                 //FixIn: 7.0.1.25
		    $num_of_min_ago = '0';
	    } else {
		    $num_of_hours_ago = explode( ':', $num_of_hours_ago );
		    $num_of_min_ago   = intval( $num_of_hours_ago[1] );
		    $num_of_hours_ago = intval( $num_of_hours_ago[0] );
	    }


	    // Right now all bookings, which  have no successfully paid status or pending are canceled.
	    $labels_payment_status_ok = wpbc_get_payment_status_ok();
	    $labels_payment_status_ok = implode( "', '", $labels_payment_status_ok );
	    $labels_payment_status_ok = "'" . $labels_payment_status_ok;

	    $labels_payment_status_pending = wpbc_get_payment_status_pending();
	    $labels_payment_status_pending = implode( "', '", $labels_payment_status_pending );
	    $labels_payment_status_ok      .= "', '" . $labels_payment_status_pending . "'";

	    $trash_bookings = ' AND bk.trash != 1 ';                                //FixIn: 6.1.1.10  - check also  below usage of {$trash_bookings}

	    // We need to  use here gmdate,  because 'bk.modification_date' in DB saved in GMT - UTC time.
	    $since_date = gmdate( 'Y-m-d H:i:s' );

            // Cancell only Pending, Old (hours) and not Paid bookings
            $slct_sql = $wpdb->prepare("SELECT DISTINCT bk.booking_id as id, bk.modification_date as date,  dt.approved AS approved, bk.pay_status AS pay_status
                         FROM {$wpdb->prefix}booking AS bk

                         INNER JOIN {$wpdb->prefix}bookingdates as dt
                         ON    bk.booking_id = dt.booking_id

                          WHERE bk.pay_status NOT IN ( {$labels_payment_status_ok} ) {$trash_bookings} AND 
                                dt.approved=0 AND
                                bk.modification_date < ( %s - INTERVAL '%d:%d' HOUR_MINUTE ) ", $since_date , $num_of_hours_ago, $num_of_min_ago );          // //FixIn: 7.0.1.25	//FixIn: 8.8.3.1

                        // old: bk.modification_date < ( NOW() - INTERVAL %d HOUR ) ", $num_of_hours_ago );
            $pending_not_paid  = $wpdb->get_results( $slct_sql );

            $approved_id = array();
            foreach ($pending_not_paid as $value) {
               $approved_id []= $value->id;
            }
            $approved_id_str = join( ',', $approved_id);

			if ( count( $approved_id ) > 0 ) {

                // Send decline emails
                $auto_cancel_pending_unpaid_bk_is_send_email =  get_bk_option( 'booking_auto_cancel_pending_unpaid_bk_is_send_email' );
                if ($auto_cancel_pending_unpaid_bk_is_send_email == 'On') {
                    $auto_cancel_pending_unpaid_bk_email_reason  =  get_bk_option( 'booking_auto_cancel_pending_unpaid_bk_email_reason' );
                    foreach ($approved_id as $booking_id) {
						wpbc_db__add_log_info( explode( ',', $booking_id ), 'System Auto Cancellation.' . ' -- ' . $auto_cancel_pending_unpaid_bk_email_reason );
                        wpbc_send_email_trash( $booking_id, 1, $auto_cancel_pending_unpaid_bk_email_reason  );
                    }
                }

                if ( false === $wpdb->query( "UPDATE {$wpdb->prefix}booking AS bk SET bk.trash = 1 WHERE booking_id IN ({$approved_id_str})" ) ){

	                wp_die( new WP_Error( 'check_pending_unpaid_bookings__do_auto_cancel'
										, 'Error. Cancellation of pending bookings.' . '  FILE:' . __FILE__ . ' LINE:' . __LINE__
							) );

					//TODO: replace all such die(); functions to wp_die(...) with  error message.  2023-10-09 17:38
					/*
                    ?> <script type="text/javascript"> document.getElementById('submiting<?php echo $bk_type; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php echo 'Error during auto deleting booking at DB of pending bookings'; ?></div>'; </script> <?php
                    die();
					*/
                }
                
            }

    }


	// -----------------------------------------------------------------------------------------------------------------
 	//   C L I E N T     S I D E    //
	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Define JavaScripts Variables
	 *
	 * @param $where_to_load
	 *
	 * @return void
	 */
    function wpbc_define_js_vars( $where_to_load = 'both' ){

	    $specific_selected_dates    = get_bk_option( 'booking_range_selection_days_specific_num_dynamic' );
	    $js_specific_selected_dates = wpbc_get_specific_range_dates__as_comma_list( $specific_selected_dates );

        //FixIn: 9.4.3.1
        wp_localize_script('wpbc-global-vars', 'wpbc_global3', array(              
              'bk_1click_mode_days_num' => intval( get_bk_option('booking_range_selection_days_count') )            /* Number of days selection with 1 mouse click */
             ,'bk_1click_mode_days_start' => '['. get_bk_option('booking_range_start_day') .']'                     /* { -1 - Any | 0 - Su,  1 - Mo,  2 - Tu, 3 - We, 4 - Th, 5 - Fr, 6 - Sat } */
             ,'bk_2clicks_mode_days_min' => intval( get_bk_option('booking_range_selection_days_count_dynamic') )   /* Min. Number of days selection with 2 mouse clicks */
             ,'bk_2clicks_mode_days_max' => intval( get_bk_option('booking_range_selection_days_max_count_dynamic'))/* Max. Number of days selection with 2 mouse clicks */
             ,'bk_2clicks_mode_days_specific' => '['. $js_specific_selected_dates . ']'                             /* Exmaple [5,7] */
             ,'bk_2clicks_mode_days_start' => '[' . get_bk_option('booking_range_start_day_dynamic') . ']'          /* { -1 - Any | 0 - Su,  1 - Mo,  2 - Tu, 3 - We, 4 - Th, 5 - Fr, 6 - Sat } */
             ,'is_booking_recurrent_time' => ( ( get_bk_option( 'booking_recurrent_time' ) !== 'On')?'false':'true' )         
             ,'is_booking_used_check_in_out_time' => ( ( wpbc_is_booking_used_check_in_out_time() ) ? 'true' : 'false' )	//FixIn: 8.8.1.7
        ) );        
    }


	/**
	 * Load JavaScripts Files
	 *
	 * @param $where_to_load
	 *
	 * @return void
	 */
    function wpbc_enqueue_js_files( $where_to_load = 'both' ){
        wp_enqueue_script( 'wpbc-bs', WPBC_PLUGIN_URL . '/inc/js/biz_s.js', 		array( 'wpbc-global-vars' ), WP_BK_VERSION_NUM );
    }


	// -----------------------------------------------------------------------------------------------------------------
    //    A d d    E l e m e n t s     t o     B o o k  i n g     F o r m   //
	// -----------------------------------------------------------------------------------------------------------------

	/**
 * Add DIV structure, where to show payment form
	 *
	 * @param string $form_content  - booking form with  calendar
	 * @return string               - modified booking form
	 */
	function add_paypal_form($form_content) {

		// If all gateways OFF - then no payment form
		$is_turned_off = apply_bk_filter('is_all_payment_forms_off', true);
		if ( $is_turned_off )
			return $form_content ;


		// If we at adminpanel - then no payment form
		if ( strpos( $_SERVER['REQUEST_URI'], 'booking.php' ) !== false )
			return $form_content ;


		/* Get in booking form  in line like this
		* <form id="booking_form3" class="booking_form vertical" action="" method="post">
		* ID of booking resource here: booking_form3"
		*/

		$str_start = strpos( $form_content, 'booking_form');
		$str_fin   = strpos( $form_content, '"', $str_start);

		$booking_resource_id = substr($form_content,$str_start+12, ($str_fin-$str_start-12) );

		$form_content .= '<div  id="gateway_payment_forms' . $booking_resource_id . '"></div>';
		return $form_content;
	}


	/**
	 * Append Check IN/OUT time fields (change over) to  HTML booking form content,  if needed
	 *
	 * @param $html_booking_form	-html  booking form  content
	 * @param $resource_id			-resource ID
	 *
	 * @return string
	 */
	function wpbc_booking_form_html__update__append_change_over_times( $html_booking_form, $resource_id ) {

		if( wpbc_is_booking_used_check_in_out_time() )  {																//FixIn: 8.8.1.7

			$resource_id = intval( $resource_id );

			if ( strpos( $html_booking_form, 'name="starttime' ) !== false ) {
				$html_booking_form = str_replace( 'name="starttime', 'name="advanced_stime', $html_booking_form );
			}
			if ( strpos( $html_booking_form, 'name="endtime' ) !== false ) {
				$html_booking_form = str_replace( 'name="endtime', 'name="advanced_etime', $html_booking_form );
			}

			$html_booking_form .= '<input name="starttime' . $resource_id . '" id="starttime' . $resource_id . '" type="text" value="' . esc_attr( get_bk_option( 'booking_range_selection_start_time' ) ) . '" style="display:none;">';
			$html_booking_form .= '<input name="endtime'   . $resource_id . '" id="endtime'   . $resource_id . '" type="text" value="' . esc_attr( get_bk_option( 'booking_range_selection_end_time' ) )   . '" style="display:none;">';
		}
		return $html_booking_form;
	}


	/**
	 * Change the status of payment - Function  call  from  IPN
	 *
	 * @param $booking_id
	 * @param $payment_status
	 * @param $payment_status_show
	 *
	 * @return void
	 */
    function wpdev_change_payment_status($booking_id = '', $payment_status = '', $payment_status_show = false  ){ global $wpdb;

        if ($booking_id === '') {
            $booking_id      = $_POST[ "booking_id" ];
            $payment_status  = $_POST[ "payment_status" ];
            $payment_status_show  = $_POST[ "payment_status_show" ];
        }

        $sql =  $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}booking as bk WHERE bk.booking_id= %d ", $booking_id );
        $result_bk = $wpdb->get_results( $sql );

        if (  ( count($result_bk)>0 )  ) {

            $update_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}booking AS bk SET bk.pay_status= %s WHERE bk.booking_id= %d ", $payment_status, $booking_id );
            if ( false === $wpdb->query( $update_sql  ) ){
                 ?> <script type="text/javascript"> 
                        var my_message = '<?php echo html_entity_decode( esc_js( get_debuge_error('Error during updating wp_nonce status in BD' ,__FILE__,__LINE__) ),ENT_QUOTES) ; ?>';
                        wpbc_admin_show_message( my_message, 'error', 30000 );                                                                                                                                          
                    </script> <?php
                 die();
            }
            if ($payment_status_show !== false ) {
                ?><script type="text/javascript">                    
                    set_booking_row_payment_status('<?php echo $booking_id; ?>','<?php echo $payment_status; ?>','<?php echo $payment_status_show; ?>');
                    var my_message = '<?php echo html_entity_decode( esc_js( __('The payment status is changed successfully' ,'booking') ),ENT_QUOTES) ; ?>';
                    wpbc_admin_show_message( my_message, 'success', 3000 );                                 
                  </script><?php
            }
        } else {
            if ($payment_status_show !== false ) {
                ?> <script type="text/javascript"> 
                    var my_message = '<?php echo html_entity_decode( esc_js( __('The changing of payment status is failed' ,'booking') ),ENT_QUOTES) ; ?>';
                    wpbc_admin_show_message( my_message, 'error', 3000 );                                
                </script> <?php
            }
        }

    }

}
