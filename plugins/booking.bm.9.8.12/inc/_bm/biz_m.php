<?php
/*
This is COMMERCIAL SCRIPT
We are not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


if ( ! defined( 'IS_USE_WPDEV_BK_CACHE' ) ) { define( 'IS_USE_WPDEV_BK_CACHE', true ); }

require_once(WPBC_PLUGIN_DIR. '/inc/_bm/booking_resources_m.php' ); 													//FixIn: 9.7.3.13
require_once(WPBC_PLUGIN_DIR. '/inc/_bm/wpbc-calc-string.php' );                                                        //FixIn: 8.1.3.17
require_once(WPBC_PLUGIN_DIR. '/inc/_bm/lib_m.php' );
require_once(WPBC_PLUGIN_DIR. '/inc/_bm/admin/wpbc-seasons-table.php' );
require_once(WPBC_PLUGIN_DIR. '/inc/_bm/admin/page-cost.php' );
require_once(WPBC_PLUGIN_DIR. '/inc/_bm/admin/page-cost-advanced.php' );
require_once(WPBC_PLUGIN_DIR. '/inc/_bm/admin/page-availability.php' );
require_once(WPBC_PLUGIN_DIR. '/inc/_bm/admin/page-seasons.php' );
require_once(WPBC_PLUGIN_DIR. '/inc/_bm/wpbc-m-costs.php' );
require_once(WPBC_PLUGIN_DIR. '/inc/_bm/m-toolbar.php' );
require_once(WPBC_PLUGIN_DIR. '/inc/_bm/form-conditions.php' );
require_once(WPBC_PLUGIN_DIR. '/inc/_bm/extend_check_in_out.php' );
require_once(WPBC_PLUGIN_DIR. '/inc/_bm/shortcode_conditions.php' );													// Parse conditions in Booking Calendar shortcodes

require_once(WPBC_PLUGIN_DIR. '/inc/_bm/admin/api-settings-m.php' );            										// Settings page
require_once(WPBC_PLUGIN_DIR. '/inc/_bm/admin/activation-m.php' );              										// Activate / Deactivate

if (file_exists(WPBC_PLUGIN_DIR. '/inc/_bl/biz_l.php')) { require_once(WPBC_PLUGIN_DIR. '/inc/_bl/biz_l.php' ); }


global $wpbc_cache_season_filters;

class wpdev_bk_biz_m {

    var $wpdev_bk_biz_l;

    function __construct(){

		add_bk_action('wpdev_ajax_show_cost', array($this, 'wpdev_ajax_show_cost'));

		add_bk_filter('wpbc_update_bookingform_content__after_load', array(&$this, 'wpbc_update_bookingform_content__after_load'));

		add_bk_filter('wpbc_is__cost_corrections__in_booking_form', array(&$this, 'wpbc_is__cost_corrections__in_booking_form'));

		add_bk_filter('wpbc_get_default_custom_form', array(&$this, 'wpbc_get_default_custom_form'));

		add_bk_filter('wpdev_season_rates', array(&$this, 'apply_season_rates'));
		add_bk_filter('get_available_days', array(&$this, 'get_available_days'));

	    add_bk_filter( 'wpbc_calc__deposit_cost__if_enabled', 'wpbc_calc__deposit_cost__if_enabled' );

		add_bk_filter('advanced_cost_apply', array(&$this, 'advanced_cost_apply'));

		add_bk_filter('early_late_booking_apply', array(&$this, 'early_late_booking_apply'));							//FixIn: 8.2.1.17

		add_bk_filter('reupdate_static_cost_hints_in_form', array(&$this, 'reupdate_static_cost_hints_in_form'));   	//FixIn: 5.4.5.5

		add_bk_filter('wpdev_get_booking_form', array(&$this, 'wpdev_get_booking_form'));

		add_bk_filter('wpdev_get_booking_form_content',  'wpbc_get_custom_booking_form' );								//FixIn: 8.1.3.19

		add_action('wpbc_define_js_vars', array(&$this, 'wpbc_define_js_vars') );
		add_action('wpbc_enqueue_js_files', array(&$this, 'wpbc_enqueue_js_files') );


		// Filter for getting daily  costs arr in calendar
	    add_filter( 'wpbc_get_calendar_dates_rates_arr', array( $this, 'wpbc_get_calendar_dates_rates_arr' ), 10, 2 );


		add_bk_filter('get_unavailbale_dates_of_season_filters', array(&$this, 'get_unavailbale_dates_of_season_filters'));

		 add_bk_filter('wpdev_check_for_additional_calendars_in_form', array(&$this, 'wpdev_check_for_additional_calendars_in_form'));
		 add_bk_filter('wpbc_get_cost__in_additional_calendars', array(&$this, 'wpbc_get_cost__in_additional_calendars'));

		if ( class_exists('wpdev_bk_biz_l')) {  $this->wpdev_bk_biz_l = new wpdev_bk_biz_l();
		} else {                                $this->wpdev_bk_biz_l = false; }

    }




	/**
	 * Possible to book many different items / rooms / facilties via. one form
	 *
	 * @param $form
	 * @param $my_boook_type
	 * @param $options
	 *
	 * @return array|string|string[]|null
	 *
	 *  Example:
	 *
	 *  $form = apply_bk_filter( 'wpdev_check_for_additional_calendars_in_form'
	 *                            , $form
	 *                            , $resource_id
	 *                            , array(
	 *                                    'booking_form' => $my_booking_form ,
	 *                                    'selected_dates' => $my_selected_dates_without_calendar ,
	 *                                    'cal_count' => $cal_count ,
	 *                                    'otions' => $bk_otions
	 *                                    )
	 *            );
	 */
	function wpdev_check_for_additional_calendars_in_form( $form, $my_boook_type, $options = false ) {

		$calendars            = array();
		$cal_num              = - 1;
		$additional_calendars = '';
		$form                 = preg_replace( '/\[calendar\*\s*\]/', '', $form );                                    	//FixIn: 6.1.1.17
		while ( strpos( $form, '[calendar ' ) !== false ) {
			$cal_num ++;
			$calendars[ $cal_num ] = array();                    														//FixIn: 9.4.3.7
			$cal_start = strpos( $form, '[calendar' );
			$cal_end = strpos( $form, ']', $cal_start + 1 );

			$new_cal = substr( $form, ( $cal_start + 9 ), ( $cal_end - $cal_start - 9 ) );
			$new_cal = trim( $new_cal );
			$params  = explode( ' ', $new_cal );
	        foreach ( $params as $param ) {
		        $param = explode( '=', $param );
		        $calendars[ $cal_num ][ $param[0] ] = $param[1];
	        }

	        if ( isset( $calendars[ $cal_num ]['id'] ) ) {

                 $resource_id = $calendars[$cal_num]['id'];

                 $my_selected_dates_without_calendar = '';
                 $my_boook_count =1;
                 $bk_otions = array();

                 if (! empty($options)) {
                     $my_booking_form = $options['booking_form' ];
                     $my_selected_dates_without_calendar = $options['selected_dates' ];
                     $my_boook_count = $options['cal_count'];
                     $bk_otions = $options['otions'];
                 }


	             $bk_cal =   '<a name="bklnk' . $resource_id . '"></a>'
						   . '<div id="booking_form_div' . $resource_id . '" class="booking_form_div">';
	             $additional_calendars .= $resource_id . ',';

	             $bk_cal .= apply_bk_filter( 'pre_get_calendar_html', $resource_id, $my_boook_count, $bk_otions );

	             $bk_cal .= '<input type="hidden" name="parent_of_additional_calendar' . $resource_id . '" id="parent_of_additional_calendar' . $resource_id . '" value="' . $my_boook_type . '" /> ';

	             $bk_cal .= '<div id="submiting' . $resource_id . '"></div><div class="form_bk_messages" id="form_bk_messages' . $resource_id . '" ></div>';
	             $bk_cal .= wp_nonce_field( 'CALCULATE_THE_COST', ( "wpbc_nonceCALCULATE_THE_COST" . $resource_id ), true, false );
	             $bk_cal .= '</div>';


	             if ( ( ! empty( $options ) ) && ( ! empty( $options['booking_form'] ) ) ) {
		             $custom_form = $options['booking_form'];
	             } else {
		             $custom_form = 'standard';
	             }

				 $start_script_code = wpbc__calendar__load( array(
												'resource_id'                     => $resource_id,
												'aggregate_resource_id_arr'       => array(),
												'selected_dates_without_calendar' => $my_selected_dates_without_calendar,
												'calendar_number_of_months'       => $my_boook_count,
					 							'custom_form'                     => $custom_form
											));

	             $form = substr_replace( $form, $bk_cal . $start_script_code, $cal_start, ( $cal_end - $cal_start + 1 ) );

             }
         }
	   if ( isset( $additional_calendars ) ) {
		   if ( $additional_calendars != '' ) {
			   $additional_calendars = substr( $additional_calendars, 0, - 1 );
			   $form                 .= ' <input type="hidden" name="additional_calendars' . $my_boook_type . '" id="additional_calendars' . $my_boook_type . '" value="' . $additional_calendars . '" /> ';

		   }
	   }
         return $form;
   }


	/**
	 * Get total and costs for each other calendars, which are inside of this form
	 *
	 * @param $initial_summ
	 * @param $form_data
	 * @param $resource_id
	 * @param $time_array
	 * @param $is_discount_calculate
	 *
	 * @return array    [
	 *						 'total_cost__in_all_calendars' => FLOAT,
	 *						 'cost_arr__in_extra_calendars' => [ ... ],
	 *						'dates_arr__in_extra_calendars' => [ ... ]
	 */
	function wpbc_get_cost__in_additional_calendars( $initial_summ, $form_data, $resource_id, $time_array, $is_discount_calculate = true ) {

        $summ_total = $initial_summ;

		// Check for additional calendars:
		$send_form_content = $form_data;
		$offset = 0;
		$summ_additional = array();
		$dates_additional = array();
		while ( strpos( $send_form_content , 'textarea^date_booking' , $offset) !== false ) {
			$offset      = strpos( $send_form_content, 'textarea^date_booking', $offset ) + 1;
			$offset_end  = strpos( $send_form_content, '^', $offset + 20 );
			$other_calendar_id = substr( $send_form_content, $offset + 20, $offset_end - $offset - 20 );                             	// ID

			$offset_end_dates_data = strpos( $send_form_content, '~', $offset_end );
			if ( $offset_end_dates_data === false ) {
				$offset_end_dates_data = strlen( $send_form_content );
			}
			$other_calendar_dates = substr( $send_form_content, $offset_end + 1, $offset_end_dates_data - $offset_end - 1 );         	// Dates

			// Replace inside of form old ID to the new correct ID
			$send_form_content = wpbc_get__form_data__with_replaced_id( $send_form_content, $other_calendar_id, $resource_id );   		//Form

			if ( empty( $other_calendar_dates ) ) {
				$summ_add = 0;
			} else {
				$summ_add = wpbc_calc__booking_cost( array(
												  'resource_id'           => $other_calendar_id           			// '2'
												, 'str_dates__dd_mm_yyyy' => $other_calendar_dates     						// '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023'
												, 'times_array' 	      => $time_array
												, 'form_data'             => $send_form_content     		 	// 'text^selected_short_timedates_hint4^06/11/2018 14:00...'
												, 'is_discount_calculate' => $is_discount_calculate
											));
			}

			$summ_add = floatval( $summ_add );

			$summ_additional[ $other_calendar_id ]  = $summ_add;
			$dates_additional[ $other_calendar_id ] = $other_calendar_dates;

			$send_form_content = $form_data;
		}

		foreach ( $summ_additional as $ss ) {
			$summ_total += $ss;
		}


		return array(
						 'total_cost__in_all_calendars' => $summ_total,
						 'cost_arr__in_extra_calendars' => $summ_additional,
						'dates_arr__in_extra_calendars' => $dates_additional
					);
	}


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  JavaScripts Variables | Files  ==  "  >

    function wpbc_define_js_vars( $where_to_load = 'both' ){

        wp_localize_script('wpbc-global-vars', 'wpbc_global4', array(
            'wpbc_available_days_num_from_today' => intval( get_bk_option('booking_available_days_num_from_today') )
        ) );                        
    }    


    function wpbc_enqueue_js_files( $where_to_load = 'both' ){
        wp_enqueue_script( 'wpbc-bm',         WPBC_PLUGIN_URL . '/inc/js/biz_m.js', array( 'wpbc-global-vars' ), WP_BK_VERSION_NUM );
        wp_enqueue_script( 'wpbc-conditions', WPBC_PLUGIN_URL . '/inc/js/form-conditions.js', array( 'wpbc-bm' ), WP_BK_VERSION_NUM );
    }

	// </editor-fold>


    // Just Get ALL booking types from DB
    function get_standard_cost_for_bk_resource($booking_type_id = 0) {

        $res = wpbc_get_booking_resources_bm__from_db__arr($booking_type_id);

        if (count($res)>0) {
            return $res[0]->cost;
        } else return 0;

    }


	/**
	 * Get available days depends from seaosn filter
	 *
	 * @param $type_id
	 *
	 * @return array
	 */
    function get_available_days( $type_id ){
        $filters = array(); global $wpdb;
        $return_result = array('available'=>true,'days'=> $filters ) ;

        $availability_res = wpbc_get_resource_meta( $type_id, 'availability' );
        if ( count($availability_res)>0 ) {
            if ( is_serialized( $availability_res[0]->value ) )   $availability = unserialize($availability_res[0]->value);
            else                                                  $availability = $availability_res[0]->value;

            $days_avalaibility = $availability['general'];
            $seasonfilter      = $availability['filter'];
            if (is_array($seasonfilter))
                foreach ($seasonfilter as $key => $value) {
                    if ($value == 'On') {


                        if ( IS_USE_WPDEV_BK_CACHE ) {
                            global $wpbc_cache_season_filters;
                            $filter_id = $key;
                            if (! isset($wpbc_cache_season_filters)) $wpbc_cache_season_filters = array();
                            if (! isset($wpbc_cache_season_filters[$filter_id])) {
                                $result = $wpdb->get_results( "SELECT booking_filter_id as id, filter FROM {$wpdb->prefix}booking_seasons" );

                                foreach ($result as $value) {
                                    $wpbc_cache_season_filters[$value->id] = array($value);
                                }
								
								if ( isset( $wpbc_cache_season_filters[ $filter_id ] ) ) {
									$result = $wpbc_cache_season_filters[$filter_id];
								} else {
									$result = array();
								}				
								
                            } else {
                                $result = $wpbc_cache_season_filters[$filter_id];
                            }
                        } else
                            $result = $wpdb->get_results( $wpdb->prepare( "SELECT filter FROM {$wpdb->prefix}booking_seasons WHERE booking_filter_id = %d" , $key ) );
                        if (! empty($result))
                        foreach($result as $filter) {

                            //FixIn:6.0.1.8
                            if ( is_serialized( $filter->filter ) ) $filter_data = unserialize($filter->filter);
                            else                                    $filter_data = $filter->filter;
                           
                            if ( isset($filter->id) ) $filters[$filter->id]=$filter_data;
                            else                      $filters[]=$filter_data;                            
                            
                        }
                    }
                }
        }
          else  $days_avalaibility = 'On';


        if ( $days_avalaibility == 'On' ) $return_result['available'] = true;
        else                              $return_result['available'] = false;
        $return_result['days'] = $filters;

        return $return_result;
    }


    function get_unavailbale_dates_of_season_filters($blank, $type_id ){
        $res_days = $this->get_available_days( $type_id );

        return($res_days);
    }


    // -----------------------------------------------------------------------------------------------------------------
    // B o o k i n g   F O R M S    customization
    // -----------------------------------------------------------------------------------------------------------------

	/**
	 * Get Content of CUSTOM Form  and CUSTOM Form Content data  -  Get Booking form Fields content
	 *
	 * @param $booking_form_def_value
	 * @param $my_booking_form_name
	 *
	 * @return string
	 */
    function wpdev_get_booking_form( $booking_form_def_value, $my_booking_form_name ){

    	$default_return_form_content = $booking_form_def_value;
		$custom_form_name 			 = $my_booking_form_name;
		$serialized_form_content 	 = false;
		$what_to_return 			 = 'form';
    	return wpbc_get_custom_booking_form( $default_return_form_content, $custom_form_name, $serialized_form_content, $what_to_return );
    }



	// -----------------------------------------------------------------------------------------------------------------
	// C O S T   CORRECTIONS
	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Check if existing shortcode [cost_corrections] in booking form at Booking > Add New page and return this entered cost, 	otherwise FALSE.
	 *
	 * Please note,  during generating of booking form, if in form  exist this shortcode 	[cost_corrections]
	 * then  in HTML booking form  will be this input with ID and NAME starting with  		'total_bk_cost' + resource_id
	 *
	 * @param $blank		''
	 * @param $form_data	'~checkbox^mymultiple4^~checkbox^rangetime4^ ~checkbox^ra...'
	 * @param $resource_id	1
	 *
	 * @return false		false if [cost_corrections] shortcode not in form,  Otherwise cost  from  this entered field
	 */
    function wpbc_is__cost_corrections__in_booking_form( $blank , $form_data, $resource_id ){

	    $form_elemnts = wpbc_get_parsed_booking_data_arr( $form_data, $resource_id, array( 'get' => 'value' ) );

	    if ( ! empty( $form_elemnts['total_bk_cost'] ) ) {

			$fin_cost_corrections_sum = str_replace( ' ', '', $form_elemnts['total_bk_cost'] );

		    $fin_cost_corrections_sum = floatval( $fin_cost_corrections_sum );

		    return $fin_cost_corrections_sum;

	    } else {

		    return false;

	    }
    }

    // Set fields inside of form for editing total cost
    function wpbc_update_booking_form_content__cost_correction($return_form, $bk_type){

        $my_form = '';

	    if ( wpbc_is_new_booking_page() ) {
		    $my_form =  '<div id="show_edit_cost_fields"><p><div class="legendspan">' . __( 'Standard booking resource cost', 'booking' ) . ':</div> '
					   		. '<input type="text" disabled="disabled" value="' . $this->get_standard_cost_for_bk_resource( $bk_type ) . '" id="standard_bk_cost' . $bk_type . '"  name="standard_bk_cost' . $bk_type . '" /></p>';
		    $my_form .= '<p><div class="legendspan">' . __( 'Total booking resource cost', 'booking' ) . ':</div>  '
							. '<input type="text" value="0" id="total_bk_cost' . $bk_type . '"  name="total_bk_cost' . $bk_type . '" /></p>';

		    if ( strpos( $_SERVER['REQUEST_URI'], 'booking_hash' ) !== false ) {
			    if ( isset( $_GET['booking_hash'] ) ) {

				    $my_booking_id_type = wpbc_hash__get_booking_id__resource_id( $_GET['booking_hash'] );
				    if ( $my_booking_id_type !== false ) {

					    $my_form .= '<script type="text/javascript">jQuery(document).ready( function(){ ';
					    $booking_id = $my_booking_id_type[0];

					    $cost = apply_bk_filter( 'get_booking_cost_from_db', '', $booking_id );
					    $this_booking_arr = wpbc_api_get_booking_by_id( $booking_id );                                    //FixIn: 9.4.4.2
					    $booking_data_arr = wpbc_get_parsed_booking_data_arr( $this_booking_arr['form'], $my_booking_id_type[1] );
					    if ( ! empty( $booking_data_arr['total_bk_cost'] ) ) {
						    $cost = strip_tags( $booking_data_arr['total_bk_cost']['value'] );
					    }
					    $my_form .= ' jQuery("#total_bk_cost' . $bk_type . '").val("' . $cost . '") ';
					    $my_form .= '});</script>';
				    }

			    }
		    } else {
			    // $my_form .= '<script type="text/javascript">jQuery(document).ready( function(){ if(typeof( showCostHintInsideBkForm ) == "function") { var show_cost_init=setTimeout(function(){ showCostHintInsideBkForm(' . $bk_type . '); },2500);  } });</script>';
		    }
			$my_form .= '</div>';
	    }
	    $return_form = str_replace( '[cost_corrections]', $my_form, $return_form );

        return $return_form ;
    }


	// -----------------------------------------------------------------------------------------------------------------
	// C O S T   H I N T
	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Check the form according show Hint and modification it
	 *
	 * @param $return_form
	 * @param $bk_type
	 * @param $my_booking_form
	 *
	 * @return array|string|string[]
	 */
    function wpbc_update_bookingform_content__after_load( $return_form, $bk_type, $my_booking_form = '' ){

        $cost_with_currency = wpbc_get_cost_with_currency_for_user( '0.00', $bk_type );

        $_POST['booking_form_type'] = $my_booking_form;                                                         		// It required for the correct calculation  of the Advanced Cost.
        $show_cost_hint = apply_bk_filter( 'advanced_cost_apply', 0, '', $bk_type, array(), true );             		// Get info  to show advanced cost.
        $return_form    = apply_bk_filter( 'reupdate_static_cost_hints_in_form', $return_form, $bk_type );      		//FixIn: 5.4.5.5


        $bk_title = wpbc_get_resource_title( $bk_type );


        foreach ( $show_cost_hint as $key_name => $value ) {

            if (  strpos( $return_form, '['.$key_name.']' ) !== false ) {
                $return_form = wpbc_replace_shortcode_hint( $return_form, array(
                                                      'shortcode'  => '['.$key_name.']'
                                                    , 'span_class' => 'bookinghint_' . $key_name . $bk_type , 'span_value' => $cost_with_currency
                                                    , 'input_name' => $key_name . $bk_type                  , 'input_data' => '0.00'    
                                        ) );
            
            }
        }
        
        $return_form = wpbc_replace_shortcode_hint( $return_form, array( 
                                              'shortcode'  => '[cost_hint]'
                                            , 'span_class' => 'booking_hint' . $bk_type , 'span_value' => $cost_with_currency
                                            , 'input_name' => 'cost_hint'    . $bk_type , 'input_data' => '0.00'    
                                ) );
        //FixIn: 8.4.2.1
        $return_form = wpbc_replace_shortcode_hint( $return_form, array( 
                                              'shortcode'  => '[estimate_day_cost_hint]'
                                            , 'span_class' => 'estimate_booking_day_cost_hint' . $bk_type , 'span_value' => $cost_with_currency
                                            , 'input_name' => 'estimate_day_cost_hint'    . $bk_type , 'input_data' => '0.00'
                                ) );
        //FixIn: 8.4.4.7
        $return_form = wpbc_replace_shortcode_hint( $return_form, array(
                                              'shortcode'  => '[estimate_night_cost_hint]'
                                            , 'span_class' => 'estimate_booking_night_cost_hint' . $bk_type , 'span_value' => $cost_with_currency
                                            , 'input_name' => 'estimate_night_cost_hint'    . $bk_type , 'input_data' => '0.00'
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array(
                                              'shortcode'  => '[original_cost_hint]'
                                            , 'span_class' => 'original_booking_hint' . $bk_type , 'span_value' => $cost_with_currency
                                            , 'input_name' => 'original_cost_hint'    . $bk_type , 'input_data' => '0.00'
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array(
                                              'shortcode'  => '[additional_cost_hint]'
                                            , 'span_class' => 'additional_booking_hint' . $bk_type , 'span_value' => $cost_with_currency
                                            , 'input_name' => 'additional_cost_hint'    . $bk_type , 'input_data' => '0.00'    
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array( 
                                              'shortcode'  => '[deposit_hint]'
                                            , 'span_class' => 'deposit_booking_hint' . $bk_type , 'span_value' => $cost_with_currency
                                            , 'input_name' => 'deposit_hint'         . $bk_type , 'input_data' => '0.00'    
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array( 
                                              'shortcode'  => '[coupon_discount_hint]'
                                            , 'span_class' => 'coupon_discount_booking_hint' . $bk_type , 'span_value' => $cost_with_currency
                                            , 'input_name' => 'coupon_discount_hint' . $bk_type , 'input_data' => '0.00'    
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array( 
                                              'shortcode'  => '[balance_hint]'
                                            , 'span_class' => 'balance_booking_hint' . $bk_type , 'span_value' => $cost_with_currency
                                            , 'input_name' => 'balance_hint'         . $bk_type , 'input_data' => '0.00'    
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array( 
                                              'shortcode'  => '[resource_title_hint]'
                                            , 'span_class' => 'resource_title_hint_tip' . $bk_type , 'span_value' => $bk_title
                                            , 'input_name' => 'resource_title_hint'     . $bk_type , 'input_data' => $bk_title
                                ) );
		//FixIn: 9.7.3.16
        $return_form = wpbc_replace_shortcode_hint( $return_form, array(                                                                         // Dates and Times Hints
                                              'shortcode'  => '[cancel_date_hint]'
                                            , 'span_class' => 'cancel_date_hint_tip' . $bk_type , 'span_value' => '...'
                                            , 'input_name' => 'cancel_date_hint'     . $bk_type , 'input_data' => '...'
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array(                                                                         // Dates and Times Hints
                                              'shortcode'  => '[check_in_date_hint]'
                                            , 'span_class' => 'check_in_date_hint_tip' . $bk_type , 'span_value' => '...'
                                            , 'input_name' => 'check_in_date_hint'     . $bk_type , 'input_data' => '...'
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array(
                                              'shortcode'  => '[check_out_date_hint]'
                                            , 'span_class' => 'check_out_date_hint_tip' . $bk_type , 'span_value' => '...'
                                            , 'input_name' => 'check_out_date_hint'     . $bk_type , 'input_data' => '...'    
                                ) );
        //FixIn: 8.0.2.12
        $return_form = wpbc_replace_shortcode_hint( $return_form, array(
                                              'shortcode'  => '[check_out_plus1day_hint]'
                                            , 'span_class' => 'check_out_plus1day_hint_tip' . $bk_type , 'span_value' => '...'
                                            , 'input_name' => 'check_out_plus1day_hint'     . $bk_type , 'input_data' => '...'
                                ) );

        $return_form = wpbc_replace_shortcode_hint( $return_form, array(
                                              'shortcode'  => '[start_time_hint]'
                                            , 'span_class' => 'start_time_hint_tip' . $bk_type , 'span_value' => '...'
                                            , 'input_name' => 'start_time_hint'     . $bk_type , 'input_data' => '...'    
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array( 
                                              'shortcode'  => '[end_time_hint]'
                                            , 'span_class' => 'end_time_hint_tip' . $bk_type , 'span_value' => '...'
                                            , 'input_name' => 'end_time_hint'     . $bk_type , 'input_data' => '...'    
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array( 
                                              'shortcode'  => '[selected_dates_hint]'
                                            , 'span_class' => 'selected_dates_hint_tip' . $bk_type , 'span_value' => '...'
                                            , 'input_name' => 'selected_dates_hint'     . $bk_type , 'input_data' => '...'    
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array( 
                                              'shortcode'  => '[selected_timedates_hint]'
                                            , 'span_class' => 'selected_timedates_hint_tip' . $bk_type , 'span_value' => '...'
                                            , 'input_name' => 'selected_timedates_hint'     . $bk_type , 'input_data' => '...'    
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array( 
                                              'shortcode'  => '[selected_short_dates_hint]'
                                            , 'span_class' => 'selected_short_dates_hint_tip' . $bk_type , 'span_value' => '...'
                                            , 'input_name' => 'selected_short_dates_hint'     . $bk_type , 'input_data' => '...'    
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array( 
                                              'shortcode'  => '[selected_short_timedates_hint]'
                                            , 'span_class' => 'selected_short_timedates_hint_tip' . $bk_type , 'span_value' => '...'
                                            , 'input_name' => 'selected_short_timedates_hint'     . $bk_type , 'input_data' => '...'    
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array( 
                                              'shortcode'  => '[days_number_hint]'
                                            , 'span_class' => 'days_number_hint_tip' . $bk_type , 'span_value' => '...'
                                            , 'input_name' => 'days_number_hint'     . $bk_type , 'input_data' => '...'    
                                ) );
        $return_form = wpbc_replace_shortcode_hint( $return_form, array( 
                                              'shortcode'  => '[nights_number_hint]'
                                            , 'span_class' => 'nights_number_hint_tip' . $bk_type , 'span_value' => '...'
                                            , 'input_name' => 'nights_number_hint'     . $bk_type , 'input_data' => '...'    
                                ) );
                
        $return_form = $this->wpbc_update_booking_form_content__cost_correction($return_form, $bk_type);

	    if ( function_exists( 'wpdev_bk_form_conditions_parsing' ) ) {
		    $return_form = wpdev_bk_form_conditions_parsing( $return_form, $bk_type );
	    }

	    return $return_form;
    }


	/**
	 * Ajax function call, for showing cost
	 * @return void
	 */
    function wpdev_ajax_show_cost(){

        make_bk_action('check_multiuser_params_for_client_side', $_POST[ "bk_type"] );

        $str_dates__dd_mm_yyyy = $_POST[ "all_dates" ];                         										//FixIn: 5.4.5.2
        $booking_type          = $_POST[ "bk_type"];
        $booking_form_data     = $_POST['form'];

        $dates_in_diff_formats = wpbc_get_dates_in_diff_formats( $str_dates__dd_mm_yyyy, $booking_type, $booking_form_data );

	    if ( 'On' === get_bk_option( 'booking_last_checkout_day_available' ) ) {
		    // Remove last  date from  the cost  calculation  function,  if this option last_checkout_day_available activated.				//FixIn: 8.2.1.28
		    array_pop( $dates_in_diff_formats['array'] );
		    $str_dates__dd_mm_yyyy = $dates_in_diff_formats['array'];
		    foreach ( $str_dates__dd_mm_yyyy as $d_k => $d_v ) {
			    $str_dates__dd_mm_yyyy[ $d_k ] = gmdate( "d.m.Y", strtotime( $d_v ) );
		    }
		    $str_dates__dd_mm_yyyy = implode( ', ', $str_dates__dd_mm_yyyy );
		    $dates_in_diff_formats = wpbc_get_dates_in_diff_formats( $str_dates__dd_mm_yyyy, $booking_type, $booking_form_data );
	    }

        $start_time         = $dates_in_diff_formats['start_time'];             // Array( [0] => 10, [1] => 00, [2] => 01 )
        $end_time           = $dates_in_diff_formats['end_time'];               // Array( [0] => 12, [1] => 00, [2] => 02 )
        $only_dates_array   = $dates_in_diff_formats['array'];                  // [0] => '2015-10-15', [1] => '2015-10-15'
        $dates              = $dates_in_diff_formats['string'];                 // '15.12.2015, 16.12.2015, 17.12.2015'


        // Get cost of main calendar with all rates discounts and  so on...
		$summ = wpbc_calc__booking_cost( array(
										  'resource_id'           => $booking_type           			// '2'
										, 'str_dates__dd_mm_yyyy' => $dates     						// '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023'
										, 'times_array' 	      => array( $start_time, $end_time )
										, 'form_data'             => $booking_form_data     		 	// 'text^selected_short_timedates_hint4^06/11/2018 14:00...'
									));
        $summ = floatval( $summ );

		$summ_original = wpbc_calc__booking_cost( array(
										  'resource_id'           => $booking_type           			// '2'
										, 'str_dates__dd_mm_yyyy' => $dates     						// '14.11.2023, 15.11.2023, 16.11.2023, 17.11.2023'
										, 'times_array' 	      => array( $start_time, $end_time )
										, 'form_data'             => $booking_form_data     		 	// 'text^selected_short_timedates_hint4^06/11/2018 14:00...'
										, 'is_discount_calculate' => true
										, 'is_only_original_cost' => true
									));
        $summ_original = floatval( $summ_original );

        //TODO: 10/03/2015 - Finish here
        $show_cost_hint = apply_bk_filter('advanced_cost_apply', $summ_original , $booking_form_data, $booking_type, explode(',', $dates) , true );    // Get info  to show advanced cost.

        // Get description according coupons discount for main calendar if its exist
        $coupon_info_4_main_calendar = apply_bk_filter('wpdev_get_additional_description_about_coupons', '', $booking_type, $dates, array($start_time, $end_time ), $booking_form_data   );
		$coupon_discount_value		 = apply_bk_filter('wpbc_get_coupon_code_discount_value', '', $booking_type, $dates, array($start_time, $end_time ), $booking_form_data   );

        // Check additional cost based on several calendars inside of this form //////////////////////////////////////////////////////////////
        $additional_calendars_cost = $this->wpbc_get_cost__in_additional_calendars( $summ, $booking_form_data, $booking_type,  array( $start_time, $end_time ) );
	    $summ_total       = $additional_calendars_cost['total_cost__in_all_calendars'];         // float
	    $summ_additional  = $additional_calendars_cost['cost_arr__in_extra_calendars'];         // [...]
	    $dates_additional = $additional_calendars_cost['dates_arr__in_extra_calendars'];        // [...]

        $additional_description = '';
        $additional_dates_description = '';    //FixIn: 8.3.3.3
		if ( ! empty( $dates_additional ) ) {  // we have additional calendars inside of this form
            // Additional calendars - dates
            foreach ( $dates_additional as $key => $ss) {
            	$dates_in_diff_formats_additional = wpbc_get_dates_in_diff_formats( $ss, $key, $booking_form_data );
 				$full_additional_days = array();		//FixIn: 8.7.1.7
	            foreach ( $dates_in_diff_formats_additional['array'] as $ful_add_day ) {
					$full_additional_days[]= $ful_add_day . ' 00:00:00';
 				}
	            $additional_dates_description .= '<br/>' . wpbc_get_resource_title( $key ) . ': ' .  wpbc_get_dates_short_format( implode( ',', $full_additional_days ) );
            }
		}

	    if ( count( $summ_additional ) > 0 ) {  					// we have additional calendars inside of this form

            // Main calendar description and discount info //
            $additional_description .= '<br />' . wpbc_get_resource_title( $booking_type ) . ': ' . wpbc_get_cost_with_currency_for_user( $summ, $booking_type );
            if ($coupon_info_4_main_calendar != '')
                $additional_description .=  ' ' . $coupon_info_4_main_calendar . ' ';
            $coupon_info_4_main_calendar = '';
            $additional_description .= '<br />' ;


            // Additional calendars - info and discounts //
            foreach ($summ_additional as $key=>$ss) {

                $additional_description .= wpbc_get_resource_title($key) . ': ' . wpbc_get_cost_with_currency_for_user( $ss, $key );

                // Discounts info ///////////////////////////////////////////////////////////////////////////////////////////////////////
                $form_content_for_specific_calendar = wpbc_get__form_data__with_replaced_id( $booking_form_data, $key ,  $booking_type );
                $dates_in_specific_calendar = $dates_additional[$key];
                $coupon_info_4_calendars = apply_bk_filter('wpdev_get_additional_description_about_coupons', '', $key , $dates_in_specific_calendar , array($start_time, $end_time ), $form_content_for_specific_calendar );
                if ($coupon_info_4_calendars != '')
                    $additional_description .= ' ' . $coupon_info_4_calendars . ' ' ;
                $coupon_info_4_calendars = '';
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $additional_description .= '<br />' ;
            }

        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	    //FixIn: 8.8.3.15
	    if ( 'On' === get_bk_option( 'booking_calc_deposit_on_original_cost_only' ) ) {
		    $summ_deposit = apply_bk_filter( 'wpbc_calc__deposit_cost__if_enabled', $summ_original, $booking_type, $dates ); // Apply fixed deposit
	    } else {
		    $summ_deposit = apply_bk_filter( 'wpbc_calc__deposit_cost__if_enabled', $summ_total, $booking_type, $dates ); // Apply fixed deposit
	    }

        $summ_balance = $summ_total - $summ_deposit;

		if ( $summ_balance < 0 ) {											//FixIn: 8.6.1.5
			$summ_deposit = $summ_total;
			$summ_balance = 0;
		}

        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $summ_additional_hint = $summ_total - $summ_original;
		$summ_additional_hint = ( $summ_additional_hint < 0 ) ? 0 : $summ_additional_hint;                              //FixIn: 8.6.1.5

        $summ_original         = wpbc_get_cost_with_currency_for_user( $summ_original, $booking_type );
        $summ_additional_hint  = wpbc_get_cost_with_currency_for_user( $summ_additional_hint, $booking_type );
        $summ_total_orig       = $summ_total;
        $summ_total            = wpbc_get_cost_with_currency_for_user( $summ_total, $booking_type );
        $summ_deposit          = wpbc_get_cost_with_currency_for_user( $summ_deposit, $booking_type );
		$coupon_discount_value_hint = wpbc_get_cost_with_currency_for_user( $coupon_discount_value, $booking_type );
        $summ_balance          = wpbc_get_cost_with_currency_for_user( $summ_balance, $booking_type );

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Dates and Times Hints: ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//FixIn: 9.7.3.16
		$cancel_date_hint				=
        $check_in_date_hint             =
        $check_out_date_hint            =
        $start_time_hint                =
        $end_time_hint                  =
        $selected_dates_hint            =
        $selected_timedates_hint        =
        $selected_short_dates_hint      =
        $selected_short_timedates_hint  =
        $days_number_hint               =
        $nights_number_hint             =  0;//'...';	//FixIn: 8.8.3.9

        if ( ! empty( $only_dates_array ) ) {

	        if ( ( ! isset( $start_time[0] ) ) || ( $start_time[0] == '' ) ) { $start_time[0] = '00'; }
	        if ( ( ! isset( $start_time[1] ) ) || ( $start_time[1] == '' ) ) { $start_time[1] = '00'; }
	        if ( ( ! isset( $end_time[0] ) ) || ( $end_time[0] == '' ) ) { $end_time[0] = '00'; }
	        if ( ( ! isset( $end_time[1] ) ) || ( $end_time[1] == '' ) ) { $end_time[1] = '00'; }

	        $selected_dates_hint     = array();
	        $selected_timedates_hint = array();

            $days_and_times = array();
            $only_full_days = array();

            foreach ( $only_dates_array as $day_num => $day ) {

	            if ( $day_num == 0 ) { //First  date
		            $days_and_times[] = $day . ' ' . $start_time[0] . ':' . $start_time[1] . ':' . $start_time[2];
	            } else if ( $day_num == ( count( $only_dates_array ) - 1 ) ) {  //Last date
		            $days_and_times[] = $day . ' ' . $end_time[0] . ':' . $end_time[1] . ':' . $end_time[2];
	            } else {
		            $days_and_times[] = $day . ' 00:00:00';
	            }
	            $only_full_days[] = $day . ' 00:00:00';

                // Wide Dates
                $selected_dates_hint[]      = wpbc_get_dates_comma_string_localized( $only_full_days[ (count($only_full_days)-1) ] ) ;
                $selected_timedates_hint[]  = wpbc_get_dates_comma_string_localized( $days_and_times[ (count($days_and_times)-1) ] ) ;
            }

            // Remove duplicated same dates, if we are selected only 1 date
            $selected_dates_hint     = array_values( array_unique( $selected_dates_hint ) );
            $selected_timedates_hint = array_values( array_unique( $selected_timedates_hint ) );

            // Number of days & nights
            $days_number_hint               = count( $selected_dates_hint );
            $nights_number_hint             = ($days_number_hint>1) ? ($days_number_hint-1) : $days_number_hint;

            // Wide Dates
            $selected_dates_hint            = implode(', ', $selected_dates_hint );
            $selected_timedates_hint        = implode(', ', $selected_timedates_hint );

            //Short Dates
            $selected_short_timedates_hint  = wpbc_get_dates_short_format(  implode(',', $days_and_times) );
            $only_full_days = array_values(array_unique($only_full_days));
            $selected_short_dates_hint      = wpbc_get_dates_short_format(  implode(',', $only_full_days) );

            $selected_short_timedates_hint 	.= $additional_dates_description;    //FixIn: 8.3.3.3
            $selected_short_dates_hint 		.= $additional_dates_description;    //FixIn: 8.3.3.3

			//FixIn: 9.7.3.16
			$cancel_date_hint				= wpbc_get_dates_comma_string_localized( date( 'Y-m-d H:i:s', strtotime( '-14 days', strtotime( $only_full_days[ 0 ] ) ) ) );
            // Check  In / Out Dates
            $check_in_date_hint             = wpbc_get_dates_comma_string_localized( $only_full_days[0]  );
            $check_out_date_hint            = wpbc_get_dates_comma_string_localized( $only_full_days[ (count($only_full_days)-1) ] );
            //FixIn: 8.0.2.12
            $check_out_plus1day_hint 		= wpbc_get_dates_comma_string_localized( date( 'Y-m-d H:i:s', strtotime( '+1 day', strtotime( $only_full_days[ (count($only_full_days)-1) ] ) ) ) );

            // Times:
			$start_time_hint = wpbc_time_localized( implode( ':', $start_time ) );
			$end_time_hint   = wpbc_time_localized( implode( ':', $end_time ) );

        } else {
        	$check_out_plus1day_hint = '';
        	$check_out_date_hint='';
		}


        ?> <script type="text/javascript">
                if ( jQuery('#booking_hint<?php echo $booking_type; ?>' ).length > 0 ) {
                    jQuery( '#booking_hint<?php echo $booking_type; ?>,.booking_hint<?php echo $booking_type; ?>' ).html( '<?php
                        echo (     $summ_total
								 . ( (! empty($coupon_info_4_main_calendar)) ?  ' '. $coupon_info_4_main_calendar .' ' : '' )
								 . $additional_description ) ; ?>' );
                    jQuery( '#cost_hint<?php echo $booking_type; ?>' ).val( '<?php
                        echo strip_tags( ( ( $summ_total  ) ) ); ?>' );
                }
            <?php
            foreach ( $show_cost_hint as $cost_hint_key => $cost_hint_value ) {
                ?>
                if ( jQuery('#bookinghint_<?php echo $cost_hint_key . $booking_type ; ?>' ).length > 0 ) {
                    jQuery( '#bookinghint_<?php echo $cost_hint_key . $booking_type; ?>,.bookinghint_<?php echo $cost_hint_key . $booking_type; ?>' ).html( '<?php
                        echo ( ( wpbc_get_cost_with_currency_for_user( $cost_hint_value, $booking_type ) ) ); ?>' );
                    jQuery( '#<?php echo $cost_hint_key . $booking_type; ?>' ).val( '<?php
                        echo strip_tags( ( ( wpbc_get_cost_with_currency_for_user( $cost_hint_value, $booking_type )  ) ) ); ?>' );
                }
                <?php
            }
            ?>
				<?php
				//FixIn: 8.4.2.1
				if (0 != $days_number_hint ) {
					$estimate_day_cost_hint  = wpbc_get_cost_with_currency_for_user( $summ_total_orig / $days_number_hint, $booking_type );
					$estimate_day_cost_hint_val  = strip_tags( $estimate_day_cost_hint );
				} else {
					$estimate_day_cost_hint = '...';
					$estimate_day_cost_hint_val = 0;
				}
				?>
                jQuery( '#estimate_booking_day_cost_hint<?php echo $booking_type;
                      ?>,.estimate_booking_day_cost_hint<?php echo $booking_type; ?>' ).html( '<?php   echo  ( $estimate_day_cost_hint ); ?>' );
                jQuery( '#estimate_day_cost_hint<?php    echo $booking_type; ?>' ).val( '<?php echo( $estimate_day_cost_hint_val ); ?>' );

				<?php
				//FixIn: 8.4.4.7
				if (0 != $nights_number_hint ) {
					$estimate_night_cost_hint     = wpbc_get_cost_with_currency_for_user( $summ_total_orig / $nights_number_hint, $booking_type );
					$estimate_night_cost_hint_val = strip_tags( $estimate_night_cost_hint );
				} else {
					$estimate_night_cost_hint = '...';
					$estimate_night_cost_hint_val = 0;
				}
				?>
                jQuery( '#estimate_booking_night_cost_hint<?php echo $booking_type;
                      ?>,.estimate_booking_night_cost_hint<?php echo $booking_type; ?>' ).html( '<?php   echo  ( $estimate_night_cost_hint ); ?>' );
                jQuery( '#estimate_night_cost_hint<?php    echo $booking_type; ?>' ).val( '<?php echo( $estimate_night_cost_hint_val ); ?>' );

                jQuery( '#additional_booking_hint<?php echo $booking_type;
                      ?>,.additional_booking_hint<?php echo $booking_type; ?>' ).html( '<?php            echo ( ( $summ_additional_hint ) ); ?>' );
                jQuery( '#additional_cost_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $summ_additional_hint ) ) ); ?>' ); 

                jQuery( '#original_booking_hint<?php echo $booking_type; 
                      ?>,.original_booking_hint<?php echo $booking_type; ?>' ).html( '<?php            echo ( ( $summ_original ) ); ?>' );
                jQuery( '#original_cost_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $summ_original ) ) ); ?>' );

                jQuery( '#deposit_booking_hint<?php echo $booking_type; 
                      ?>,.deposit_booking_hint<?php echo $booking_type; ?>' ).html( '<?php       echo ( ( $summ_deposit ) ); ?>' );
                jQuery( '#deposit_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $summ_deposit ) ) ); ?>' ); 
				
                jQuery( '#coupon_discount_booking_hint<?php echo $booking_type; 
                      ?>,.coupon_discount_booking_hint<?php echo $booking_type; ?>' ).html( '<?php       echo ( ( $coupon_discount_value_hint ) ); ?>' );
                jQuery( '#coupon_discount_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $coupon_discount_value ) ) ); ?>' ); 

                jQuery( '#balance_booking_hint<?php echo $booking_type; 
                      ?>,.balance_booking_hint<?php echo $booking_type; ?>' ).html( '<?php       echo ( ( $summ_balance ) ); ?>' );
                jQuery( '#balance_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $summ_balance ) ) ); ?>' ); 
                              
                if ( jQuery('#total_bk_cost<?php echo $booking_type; ?>' ).length > 0 ) {
                    if ( 
                             ( jQuery( '#total_bk_cost<?php echo $booking_type; ?>' ).val() == 0 )  
                          || ( location.href.indexOf('booking_hash') === -1 )                           //FixIn: 7.0.1.28
                        ) jQuery( '#total_bk_cost<?php echo $booking_type; ?>' ).val( '<?php 
                            echo strip_tags( ( ( $summ_total_orig  ) ) ); ?>' ); 
                }
                
                jQuery( '#coupon_discount_hint<?php echo $booking_type; 
                      ?>,.coupon_discount_hint<?php echo $booking_type; ?>' ).html( '<?php       echo ( ( $coupon_discount_value ) ); ?>' );
                jQuery( '#coupon_discount<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $coupon_discount_value ) ) ); ?>' ); 
				
                // Dates and Times shortcodes:
				//FixIn: 9.7.3.16
                jQuery( '#cancel_date_hint_tip<?php echo $booking_type;
                      ?>,.cancel_date_hint_tip<?php echo $booking_type; ?>' ).html( '<?php       echo ( ( $cancel_date_hint ) ); ?>' );
                jQuery( '#cancel_date_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $cancel_date_hint ) ) ); ?>' );

                jQuery( '#check_in_date_hint_tip<?php echo $booking_type;
                      ?>,.check_in_date_hint_tip<?php echo $booking_type; ?>' ).html( '<?php       echo ( ( $check_in_date_hint ) ); ?>' );
                jQuery( '#check_in_date_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $check_in_date_hint ) ) ); ?>' );

                jQuery( '#check_out_date_hint_tip<?php echo $booking_type;
                      ?>,.check_out_date_hint_tip<?php echo $booking_type; ?>' ).html( '<?php       echo ( ( $check_out_date_hint ) ); ?>' );
                jQuery( '#check_out_date_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $check_out_date_hint ) ) ); ?>' ); 
				//FixIn: 8.0.2.12
                jQuery( '#check_out_plus1day_hint_tip<?php echo $booking_type;
                      ?>,.check_out_plus1day_hint_tip<?php echo $booking_type; ?>' ).html( '<?php       echo ( ( $check_out_plus1day_hint ) ); ?>' );
                jQuery( '#check_out_plus1day_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $check_out_plus1day_hint ) ) ); ?>' );
				// End fix
                jQuery( '#start_time_hint_tip<?php echo $booking_type;
                      ?>,.start_time_hint_tip<?php echo $booking_type; ?>' ).html( '<?php       echo ( ( $start_time_hint ) ); ?>' );
                jQuery( '#start_time_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $start_time_hint ) ) ); ?>' ); 

                jQuery( '#end_time_hint_tip<?php echo $booking_type; 
                      ?>,.end_time_hint_tip<?php echo $booking_type; ?>' ).html( '<?php       echo ( ( $end_time_hint ) ); ?>' );
                jQuery( '#end_time_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $end_time_hint ) ) ); ?>' ); 

                jQuery( '#selected_dates_hint_tip<?php echo $booking_type; 
                      ?>,.selected_dates_hint_tip<?php echo $booking_type; ?>' ).html( '<?php       echo ( ( $selected_dates_hint ) ); ?>' );
                jQuery( '#selected_dates_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $selected_dates_hint ) ) ); ?>' ); 

                jQuery( '#selected_timedates_hint_tip<?php echo $booking_type; 
                      ?>,.selected_timedates_hint_tip<?php echo $booking_type; ?>' ).html( '<?php       echo ( ( $selected_timedates_hint ) ); ?>' );
                jQuery( '#selected_timedates_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $selected_timedates_hint ) ) ); ?>' ); 

                jQuery( '#selected_short_dates_hint_tip<?php echo $booking_type; 
                      ?>,.selected_short_dates_hint_tip<?php echo $booking_type; ?>' ).html( '<?php           echo ( ( $selected_short_dates_hint ) ); ?>' );
                jQuery( '#selected_short_dates_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $selected_short_dates_hint ) ) ); ?>' ); 

                jQuery( '#selected_short_timedates_hint_tip<?php echo $booking_type; 
                      ?>,.selected_short_timedates_hint_tip<?php echo $booking_type; ?>' ).html( '<?php           echo ( ( $selected_short_timedates_hint ) ); ?>' );
                jQuery( '#selected_short_timedates_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $selected_short_timedates_hint ) ) ); ?>' ); 

                jQuery( '#days_number_hint_tip<?php echo $booking_type; 
                      ?>,.days_number_hint_tip<?php echo $booking_type; ?>' ).html( '<?php           echo ( ( $days_number_hint ) ); ?>' );
                jQuery( '#days_number_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $days_number_hint ) ) ); ?>' ); 

                jQuery( '#nights_number_hint_tip<?php echo $booking_type; 
                      ?>,.nights_number_hint_tip<?php echo $booking_type; ?>' ).html( '<?php           echo ( ( $nights_number_hint ) ); ?>' );
                jQuery( '#nights_number_hint<?php    echo $booking_type; ?>' ).val( '<?php echo strip_tags( ( ( $nights_number_hint ) ) ); ?>' ); 
                
           </script> <?php
    }



	// -----------------------------------------------------------------------------------------------------------------
	// R A T E S
	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Get costs for each  specific dates in calendar
	 *
	 * @param $costs_return_arr		= array(
												'is_show_cost_in_tooltips'  => false,
												'is_show_cost_in_date_cell' => false,
												'cost_curency'             	=> __( 'Cost: ', 'booking' ),
												'wpbc_curency_symbol'      	=> '$',
												'prices_per_day' 			=> array()
											)
	 * @param $resource_id			= 1
	 *
	 * @return array				= Array (
												[is_show_cost_in_tooltips] => 1
												[is_show_cost_in_date_cell] => 1
												[cost_curency] => Day Cost:
												[wpbc_curency_symbol] => £
												[prices_per_day] => Array (
																			[1] => Array (
																					[7-15-2023] => 2 376.00
																					[7-16-2023] => 2 376.00
	                   							....
	 */
    function wpbc_get_calendar_dates_rates_arr( $costs_return_arr = array(), $resource_id = 1 ) {

	    $defaults = array(
							'is_show_cost_in_tooltips'  => ( 'On' == get_bk_option( 'booking_is_show_cost_in_tooltips' ) ) ? true : false,
							'is_show_cost_in_date_cell' => ( 'On' == get_bk_option( 'booking_is_show_cost_in_date_cell' ) ) ? true : false,
							'cost_curency'             	=> apply_bk_filter( 'wpdev_check_for_active_language', get_bk_option( 'booking_highlight_cost_word' ) ) . ' ' ,
							'wpbc_curency_symbol'      	=> get_bk_option( 'booking_cost_in_date_cell_currency' ) ,
							'prices_per_day' 			=> array()
						);
		$calendar_rates_arr   = wp_parse_args( $costs_return_arr, $defaults );


        // Get cost of 1 time unit
	    $cost   = 0;
	    $result = wpbc_get_booking_resources_bm__from_db__arr( $resource_id );
	    if ( count( $result ) > 0 ) {
		    $cost = $result[0]->cost;
	    }

        $prices_per_day = array();
        $prices_per_day[ $resource_id ] = array();

        $max_days_to_process = wpbc_get_max_visible_days_in_calendar();
	    $my_day = date( 'm.d.Y' );     
			
			// Start from TODAY

	    for ( $i = 0; $i < $max_days_to_process; $i ++ ) {

            $my_day_arr = explode('.',$my_day);

	        $day   = intval( $my_day_arr[1] );
	        $month = intval( $my_day_arr[0] );
	        $year  = intval( $my_day_arr[2] );

		    $fin_day_cost = wpbc_get_1_day_cost_apply_rates( $resource_id, $cost, $day, $month, $year );
		    $fin_day_cost = wpbc_cost_show( $fin_day_cost, array( 'currency' => '&nbsp;' ) );                           //FixIn: 7.2.1.11
		    $fin_day_cost = strip_tags( $fin_day_cost );

            $my_day_tag =   $month . '-' . $day . '-' . $year ;
            $prices_per_day[$resource_id][$my_day_tag] = $fin_day_cost;

		    $my_day = date( 'm.d.Y', mktime( 0, 0, 0, $month, ( $day + 1 ), $year ) );
        }

	    $calendar_rates_arr['prices_per_day'] = $prices_per_day;

        return $calendar_rates_arr;
    }



	/**
	 * Apply season rates to D A Y S array with/without $time_array   -   send from P A Y P A L form
	 *
	 * @param $paypal_dayprice
	 * @param $days_array				= array( 'dd.mm.yyyy', 'dd.mm.yyyy', ... )
	 * @param $booking_type
	 * @param $times_array
	 * @param $post_form
	 *
	 * @return array
	 */
	function apply_season_rates( $paypal_dayprice, $days_array, $booking_type, $times_array, $post_form ) {

		if ( 'On' === get_bk_option( 'booking_debug_valuation_days' ) ) { show_debug( 'clear', - 3 ); }

		if ( $times_array[0] == array( '00', '00', '00' ) ) { $times_array[0] = array( '00', '00', '01' ); }
		if ( $times_array[1] == array( '00', '00', '02' ) ) { $times_array[1] = array( '24', '00', '02' ); }

		$one_night           = 0;
		$paypal_price_period = get_bk_option( 'booking_paypal_price_period' );
		$costs_depends_from_selection_new = array();

		// -------------------------------------------------------------------------------------------------------------
		//  ==  V a l u a t i o n   d a y s  ==
		// -------------------------------------------------------------------------------------------------------------
		if (  ( 'day'   == $paypal_price_period ) || ( 'night' == $paypal_price_period )  ){

			$costs_depends_from_selection = wpbc_get_valuation_days_array( $booking_type, $days_array, $times_array );

			if ( 'On' === get_bk_option( 'booking_debug_valuation_days' ) ) { show_debug( '"Valuation days (cost per day)"', $costs_depends_from_selection ); }

			if ( $costs_depends_from_selection !== false ) {
				$costs_depends_from_selection[0] = 0;
				for ( $ii = 1; $ii < count( $costs_depends_from_selection ); $ii ++ ) {
					$costs_depends_from_selection_new[] = $costs_depends_from_selection[ $ii ];
				}
			}
		}

		if ( 'night' == $paypal_price_period ) {

			if (
				   ( count( $days_array ) > 1 )
				&& (
					    ( ( $times_array[0] == array( '00', '00', '01' ) ) && ( $times_array[1] == array( '00', '00', '00' ) ) )
				     || ( ( $times_array[0] == array( '00', '00', '01' ) ) && ( $times_array[1] == array( '24', '00', '02' ) ) )
				   )
			){
				$one_night = 1;
			}

			if ( $costs_depends_from_selection !== false ) {

				if ( count( $costs_depends_from_selection_new ) > 1 ) {
					$one_night = 0;
					// If we have default value for last day - "100%" - its means no setting of "Valuation days",  then  set cost  for this day  to 0, because of cost  per night
					if ( $costs_depends_from_selection_new[ ( count( $costs_depends_from_selection_new ) - 1 ) ] == '100%' ) {
						 $costs_depends_from_selection_new[ ( count( $costs_depends_from_selection_new ) - 1 ) ] = 0;
					}
				}
			}
		}
		// -------------------------------------------------------------------------------------------------------------



        $days_rates = array();

		if ( count( $days_array ) == 1 ) {
			$d_day = $days_array[0];
			if ( ! empty( $d_day ) ) {
				$d_day = explode( '.', $d_day );
				$day   = ( $d_day[0] + 0 );
				$month = ( $d_day[1] + 0 );
				$year  = ( $d_day[2] + 0 );
				$start_time_in_ms = mktime( $times_array[0][0], $times_array[0][1], $times_array[0][2], $month, $day, $year );
				$end_time_in_ms   = mktime( $times_array[1][0], $times_array[1][1], $times_array[1][2], $month, $day, $year );
				if ( ( $end_time_in_ms - $start_time_in_ms ) < 0 ) {

					$days_array[] = date( 'd.m.Y', mktime( 0, 0, 0, $month, ( $day + 1 ), $year ) );		//We need to  add one extra day,  because the end time outside 24:00 already
				}
			}
		}

		for ( $i = 0; $i < ( count( $days_array ) - $one_night ); $i ++ ) {

			$d_day = $days_array[ $i ];

			if ( ! empty( $d_day ) ) {

				$times_array_check = array( array( '00', '00', '01' ), array( '24', '00', '02' ) );
				if ( $i == 0 ) {
					$times_array_check[0] = $times_array[0];
				}
				if ( $i == ( count( $days_array ) - 1 - $one_night ) ) {
					$times_array_check[1] = $times_array[1];
				}
				//$times_array_check = array($times_array[0],$times_array[1]);  // It will make cost calculation only between entered times, even on multiple days
				$d_day        = explode( '.', $d_day );
				$day          = ( $d_day[0] + 0 );
				$month        = ( $d_day[1] + 0 );
				$year         = ( $d_day[2] + 0 );
				$week         = date( 'w', mktime( 0, 0, 0, $month, $day, $year ) );
				$days_rates[] = wpbc_get_1_day_cost_apply_rates( $booking_type, $paypal_dayprice, $day, $month, $year, $times_array_check, $post_form );
			}
		}


        // If fixed deposit so take only for first day cost
		if ( $paypal_price_period == 'fixed' ) {
			if ( count( $days_rates ) > 0 ) {
				$days_rates = array( $days_rates[0] );
			} else {
				$days_rates = array();
			}
		}


		if (
			 ( count( $costs_depends_from_selection_new ) > 0 ) &&
		     ( ! ( ( count( $days_array ) == 1 ) && ( empty( $days_array[0] ) ) ) )
		){
			$rates_with_procents = array();
			// check is some value of $costs_depends_from_selection_new consist % if its true so then apply this percents to days
			$is_rates_with_procents = false;
			for ( $iii = 0; $iii < count( $costs_depends_from_selection_new ); $iii ++ ) {
				if ( strpos( $costs_depends_from_selection_new[ $iii ], 'add' ) !== false ) {
					$my_vvalue              = floatval( str_replace( 'add', '', $costs_depends_from_selection_new[ $iii ] ) );
					$rates_with_procents[]  = $my_vvalue + $days_rates[ $iii ];
					$is_rates_with_procents = true;
				} elseif ( strpos( $costs_depends_from_selection_new[ $iii ], '%' ) !== false ) {
					$is_rates_with_procents = true;
					$proc                   = str_replace( '%', '', $costs_depends_from_selection_new[ $iii ] ) * 1;
					if ( isset( $days_rates[ $iii ] ) ) {
						$rates_with_procents[] = $proc * $days_rates[ $iii ] / 100;
					}
				} else {
					$rates_with_procents[] = floatval( $costs_depends_from_selection_new[ $iii ] );// $days_rates[$iii]; // just cost
				}
			}

			if ( $is_rates_with_procents ) {
				$final_daily_costs = $rates_with_procents;					// Rates with percents from cost depends on from number of days
			} else {
				$final_daily_costs = $costs_depends_from_selection_new;		// Cost depends on from number of days
			}

		} else {
			$final_daily_costs = $days_rates;								// Just pure rates
		}

		if ( 'On' === get_bk_option( 'booking_debug_valuation_days' ) ) { show_debug( 'Daily fixed costs:', $final_daily_costs ); }

		return $final_daily_costs;
    }



    /**
	 * Reupdate static cost hints in booking form. Showing standard additional  costs,  if selected specific option in selectbox or checkbox.
     * 
     * @param string $form - booking form
     * @param int $bktype - Id of booking resource
     * @return string - content of booking form.
     */
    function reupdate_static_cost_hints_in_form( $form , $bktype ) {     //FixIn: 5.4.5.5

	    $booking_form_name = '';
	    if ( isset( $_POST['booking_form_type'] ) ) {
		    if ( ! empty( $_POST['booking_form_type'] ) ) {
			    $booking_form_name = $_POST['booking_form_type'];
			    $booking_form_name = str_replace( "\'", '', $booking_form_name );
			    if ( $booking_form_name == 'standard' ) {
				    $booking_form_name = '';
			    }
		    }
	    }

	    $field__values = ( $booking_form_name === '' )
						? get_bk_option( 'booking_advanced_costs_values' )
						: get_bk_option( 'booking_advanced_costs_values_for' . $booking_form_name );

	    $field__values_unserilize = ( ! empty( $field__values ) )
									? maybe_unserialize( $field__values )
									: array();

	    if ( is_array( $field__values_unserilize ) ) {
			foreach ( $field__values_unserilize as $key => $value ) {

				$pattern = '\[(' . $key . ')_hint_static' . '([^\]]*)' . '\]';

				preg_match_all("/$pattern/", $form, $matches, PREG_SET_ORDER);

				if ( count($matches) > 0 ) {

					foreach ( $matches as $static_hint ) {

						$cost_for_insert = '';
						if ( isset( $field__values_unserilize[ $static_hint[1] ] ) ) {

							if ( isset( $field__values_unserilize[ $static_hint[1] ]['checkbox'] ) )             // Check additional cost in  standard checkbox, like this [checkbox some_name ""]
							{
								$cost_for_insert = $field__values_unserilize[ $static_hint[1] ]['checkbox'];
							}

							if ( isset( $field__values_unserilize[ $static_hint[1] ][ $static_hint[2] ] ) ) {
								$cost_for_insert = $field__values_unserilize[ $static_hint[1] ][ $static_hint[2] ];
							}

							$static_hint[2] = trim( str_replace( array( "'", '"' ), '', $static_hint[2] ) );

							if ( isset( $field__values_unserilize[ $static_hint[1] ][ $static_hint[2] ] ) ) {
								$cost_for_insert = $field__values_unserilize[ $static_hint[1] ][ $static_hint[2] ];
							}

							if ( strpos( $cost_for_insert, '%' ) === false ) {
								$cost_currency   = wpbc_get_currency_symbol_for_user( $bktype );
								$cost_for_insert = $cost_currency . ' ' . $cost_for_insert;
							} else {
								$cost_for_insert = '';												//Here we are have percents, then set  it to the empty
							}
							$form = str_replace( $static_hint[0], $cost_for_insert, $form );		// Replace staic cost  hint element to  the cost  value
						}
					}
				}
			}
        }
        return $form;
    }



	/**
	 * Apply "early booking discount" or "Last minute booking discount" to  the booking.
	 *
	 * @param $cost				=> 125
	 * @param $form				=> text^selected_short_timedates_hint4^06/09/2018 14:00 - 06/11/2018 12:00~text^nights_number_hint4^2~text^cost_hint4^$125.00~text^name4^~email^email4^~select-one^visitors4^1~select-one^children4^0~text^starttime4^14:00~text^endtime4^12:00
	 * @param $resource_id		=> 4
	 * @param $booking_days_arr	=> Array ( [0] => 09.06.2018 [1] => 10.06.2018 [2] => 11.06.2018 )
	 *
	 * @return mixed
	 */
    function early_late_booking_apply( $cost , $form , $resource_id , $booking_days_arr ){								//FixIn: 8.2.1.17

		$el_data = false;

		// Get Early / Late booking discount data for Resource
        $meta_data = wpbc_get_resource_meta( $resource_id, 'costs_early_late_booking' );
        if ( count( $meta_data ) > 0 ) {
	        $el_data = maybe_unserialize( $meta_data[0]->value );
        }


        if ( ! empty( $el_data ) ) {

	        /**
	         *  $el_data::

			    [early_booking_active] 				=> Off		|   	On
				[early_booking_amount] 				=> 100		|   	75
				[early_booking_type] 				=> %		|   	fixed
				[early_booking_days_condition] 		=> 0		|   	180
				[early_booking_season_filter] 		=> 0		|   	1

				[last_min_booking_active] 			=> Off		|   	On
				[last_min_booking_amount] 			=> 100		|   	25
				[last_min_booking_type] 			=> %		|   	%
				[last_min_booking_days_condition] 	=> 0		|   	7
				[last_min_booking_season_filter] 	=> 0		|   	3
			 */

			$booking_days_string = implode( ',', $booking_days_arr );

	        // Get sorted days
	        $sorted_dates = wpbc_get_sorted_days_array( $booking_days_string );

	        if ( ! empty( $sorted_dates ) ) {

	        	////////////////////////////////////////////////////////////////////////////////////////////////////////
	        	// E A R L Y  BOOKING
				////////////////////////////////////////////////////////////////////////////////////////////////////////
		        if ( $el_data['early_booking_active'] == 'On' ) {

			        $apply_after_days = intval( $el_data['early_booking_days_condition'] );

					$dates_diff = wpbc_get_difference_in_days( '+' . $apply_after_days . ' days', $sorted_dates[0] ); 	//debuge( '$dates_diff', $dates_diff, '+' . $apply_after_days . ' days', $sorted_dates[0] );

					// Check in  MORE  than XX days from  Today
					if ( $dates_diff <= 0 ){

						// Its inside season filter, or NO season filter to  apply in settings, (value = 0)
						if ( wpbc_is_check_in_day_in_season_filter( $el_data['early_booking_season_filter'], $booking_days_string ) ) {

							// Apply discount here
							$discount_val = intval( $el_data['early_booking_amount'] );

							if ( $el_data['early_booking_type'] == '%') {			// %
								$cost = $cost - $cost * $discount_val / 100 ;
							} else {												// fixed
								$cost = $cost - $discount_val;
							}

							if ( $cost < 0 ) { $cost = 0; }		// Check  negative
						}
					}
		        }

		        ////////////////////////////////////////////////////////////////////////////////////////////////////////
		        // LAST  MINUTE BOOKING
				////////////////////////////////////////////////////////////////////////////////////////////////////////
		        if ( $el_data['last_min_booking_active'] == 'On' ) {

			        $apply_after_days = intval( $el_data['last_min_booking_days_condition'] );

					$dates_diff = wpbc_get_difference_in_days( '+' . $apply_after_days . ' days', $sorted_dates[0] ); 	//debuge( '$dates_diff', $dates_diff, '+' . $apply_after_days . ' days', $sorted_dates[0] );

					// Check in  MORE  than XX days from  Today
					if ( $dates_diff > 0 ){

						// Its inside season filter, or NO season filter to  apply in settings, (value = 0)
						if ( wpbc_is_check_in_day_in_season_filter( $el_data['last_min_booking_season_filter'], $booking_days_string ) ) {

							// Apply discount here
							$discount_val = intval( $el_data['last_min_booking_amount'] );

							if ( $el_data['last_min_booking_type'] == '%') {		// %
								$cost = $cost - $cost * $discount_val / 100 ;
							} else {												// fixed
								$cost = $cost - $discount_val;
							}

							if ( $cost < 0 ) { $cost = 0; }		// Check  negative
						}
					}
		        }

	        }
        }

    	return $cost;
    }


    // Apply advanced cost to the cost from payment form
    function advanced_cost_apply( $summ , $form , $bktype , $days_array , $is_get_description = false ){

        $booking_form_name='';
        if (isset($_POST['booking_form_type']) ){
            if (! empty($_POST['booking_form_type'])) {
                $booking_form_name = $_POST['booking_form_type'];
                $booking_form_name = str_replace("\'",'',$booking_form_name);
                if (   ( $booking_form_name == 'standard' ) 
                    || ( get_bk_option( 'booking_advanced_costs_values_for' . $booking_form_name ) === false )          // Form does not exist
                 ) $booking_form_name = '';
            }
        }

	    $additional_cost   = 0;                                               		// advanced cost, which will apply
		// TODO: refactor this: 2023-11-17
	    $booking_form_show = wpbc__legacy__get_form_content_arr( $form, $bktype );
		$booking_form_show['_all_fields_'] = wpbc_get_parsed_booking_data_arr( $form, $bktype, array( 'get' => 'value' ) );		// FixIn: 9.8.7.1

	    if ( $booking_form_name === '' ) {
		    $field__values = get_bk_option( 'booking_advanced_costs_values' );		// Get saved advanced cost structure for STANDARD form
	    } else {
		    $field__values = get_bk_option( 'booking_advanced_costs_values_for' . $booking_form_name );
	    }
		// $field__values == {"rangetime":{"10:00_-_12:00":0,"12:00_-_14:00":0,"14:00_-_16:00":0,"16:00_-_18:00":0,"18:00_-_20:00":0},"visitors":{"1":"100%","2":"200%","3":"300%","4":"400%","5":0,"6":"500%","7":"700%","8":"800%","9":"900%"},"children":[0,"150%","200%","250%"],"tourist_tax":{"checkbox":"( [visitors] * 2 )"},"tourist_tax_deduction":{"checkbox":"(-1*( [visitors] * 2 ))"},"fixed_fee":{"checkbox":600}}
        $full_procents = 1;
        $advanced_cost_hint = array();
        if ( $field__values !== false ) {                                   // Its exist

	        $field__values_unserilize     = maybe_unserialize( $field__values );
	        $booking_form_show['content'] = '';

            if (! empty($field__values_unserilize)) {                       // Checking
                if (is_array($field__values_unserilize)) {
                    foreach ($field__values_unserilize as $key_name => $value) {    // repeat in format "visitors"  =>  array ("1"=>25, "2"=>"200%")
                        $key_name= trim($key_name);                         // Get trim visitors name (or some other)
                        
                        $advanced_cost_hint[$key_name] = array( 'value' => $value , 'fixed' => array(), 'percent' => array() );	// FixIn: 8.1.3.17.1

                        if (isset( $booking_form_show['_all_fields_'][$key_name] )) {       // Get value sending from booking form like this $booking_form_show["visitors"]
                            $selected_value = $booking_form_show['_all_fields_'][$key_name];


                            if ( is_array($selected_value) )  $selected_value_array = $selected_value;
                            else {
                                if ( strpos($selected_value,',')===false )
                                     $selected_value_array = array($selected_value);
                                else $selected_value_array = explode(',',$selected_value);
                            }


                            foreach ($selected_value_array as $selected_value ) {

								$selected_value = trim($selected_value);

	                            $selected_value = wpbc_replace_non_standard_symbols_for_advanced_costs( $selected_value );    	//FixIn: 8.6.1.7
	                            //FixIn: 9.8.4.2
								if (
										   ( $selected_value == '' )
										|| ( strtolower( $selected_value ) == 'true' )
//										|| ( strtolower( $selected_value ) == 'yes' )
//										|| ( strtolower( $selected_value ) == strtolower( __( 'yes', 'booking' ) ) )	// FixIn: 9.8.7.1
									) $selected_value = 'checkbox';

								if ( isset($value[$selected_value]) ) {         // check how its value for selected value in cash or percent

									$additional_single_cost = $value[$selected_value];
									$additional_single_cost = str_replace(',','.',$additional_single_cost);
									$full_additional_single_cost = 0;

									// Replace predefined shortcodes													//FixIn: 8.7.2.4
									$additional_single_cost = str_replace( '[days_count]' , count( $days_array ), $additional_single_cost );
									$nights_count = ( count( $days_array ) - 1 );
									$nights_count = ( 0 === $nights_count ) ? 1 : $nights_count;

									$additional_single_cost = str_replace( '[nights_count]' , $nights_count, $additional_single_cost );

									$additional_single_cost = str_replace( '[original_cost]', $summ, $additional_single_cost );            //FixIn: 9.4.3.8

									// FixIn: 8.1.3.17
									if(  ( substr( $additional_single_cost , -1 ) == '%' ) && ( substr( $additional_single_cost , 0, 1 ) == '+' )  ){
										$additional_single_cost = substr($additional_single_cost, 0, -1);
										$additional_single_cost = substr($additional_single_cost, 1 );
										// Calc
										$additional_single_cost = $this->wpbc_replace_shortcodes_to_values( $additional_single_cost, $booking_form_show['_all_fields_'] );
										$full_additional_single_cost              = floatval( $summ * ( $additional_single_cost / 100 ) );

										$advanced_cost_hint[ $key_name ]['fixed'][] = $full_additional_single_cost;		// FixIn: 8.1.3.17.1
										$additional_cost                          += $full_additional_single_cost;
									}
									else if ( substr( $additional_single_cost , -1 ) == '%' ) {
										$additional_single_cost = substr($additional_single_cost,0,-1);
										// Calc
										$additional_single_cost = $this->wpbc_replace_shortcodes_to_values( $additional_single_cost, $booking_form_show['_all_fields_'] );
										$advanced_cost_hint[ $key_name ]['percent'][] = ( ( $additional_single_cost * 1 / 100 ) );				// FixIn: 8.1.3.17.1
										$full_procents                              = ( ( $additional_single_cost * $full_procents / 100 ) );
									}
									else if ( substr( $additional_single_cost , -4 ) == '/day' ) {
										$additional_single_cost = str_replace( '/day', '', $additional_single_cost );
										//Calc
										$additional_single_cost = $this->wpbc_replace_shortcodes_to_values( $additional_single_cost, $booking_form_show['_all_fields_'] );
										$full_additional_single_cost              = floatval( $additional_single_cost ) * count( $days_array );
										$advanced_cost_hint[ $key_name ]['fixed'][] = $full_additional_single_cost;		// FixIn: 8.1.3.17.1
										$additional_cost                          += $full_additional_single_cost;
									}
									else if ( substr( $additional_single_cost , -6 ) == '/night' ) {
										$additional_single_cost = str_replace( '/night', '', $additional_single_cost );
										//Calc
										$additional_single_cost = $this->wpbc_replace_shortcodes_to_values( $additional_single_cost, $booking_form_show['_all_fields_'] );
										$nights_count           = ( count( $days_array ) - 1 );
										if ( $nights_count == 0 ) {
											$nights_count = 1;
										}
										$full_additional_single_cost              = floatval( $additional_single_cost ) * $nights_count;
										$advanced_cost_hint[ $key_name ]['fixed'][] = $full_additional_single_cost;		// FixIn: 8.1.3.17.1
										$additional_cost                          += $full_additional_single_cost;
									} else {                                                                      // cashe
										$additional_single_cost = $this->wpbc_replace_shortcodes_to_values( $additional_single_cost, $booking_form_show['_all_fields_'] );
										$full_additional_single_cost              =  $additional_single_cost;
										$advanced_cost_hint[ $key_name ]['fixed'][] = $full_additional_single_cost;		// FixIn: 8.1.3.17.1
										$additional_cost                         += $full_additional_single_cost;
									}
								}
                            }
                        }
                    }
                }
            }
        }


		if ( 'On' === get_bk_option( 'booking_debug_valuation_days' ) ) {                                            //FixIn: 8.8.3.18

			if ( get_bk_option( 'booking_advanced_costs_calc_fixed_cost_with_procents' ) == 'On' ) {

				show_debug( 'Advanced costs'
					, array( 'Fields configuration: ',  $field__values_unserilize )
					,  'Total cost of days: ' .    $summ
					,  'Additional FIXED cost: ' . $additional_cost
					,  'Percentage (X factor): ' . $full_procents
					,  'Final advanced cost: ' .   "( $summ + $additional_cost ) * $full_procents = " . ( ( $summ + $additional_cost ) * $full_procents )
				);

			} else {

				show_debug( 'Advanced costs'
					, array( 'Fields configuration: ',  $field__values_unserilize )
					,  'Total cost of days: ' .    $summ
					,  'Additional FIXED cost: ' . $additional_cost
					,  'Percentage (X factor): ' . $full_procents
					,  'Final advanced cost: ' .   "$summ * $full_procents + $additional_cost = " . ( $summ * $full_procents + $additional_cost )
				);

			}
		}


        if ( $is_get_description ) {

			/////////////////////////////////////////////////////////////////////////////////////////
	        //FixIn: 8.5.2.21
			/////////////////////////////////////////////////////////////////////////////////////////

			if ( get_bk_option( 'booking_advanced_costs_calc_fixed_cost_with_procents' ) == 'On' ) {
				$my_original_cost = $summ + $additional_cost;
			} else {
				$my_original_cost = $summ;
			}
	        /**
			 * Help Example:
			 *
	         * Initial params:  $158 * 106% * 106.75% + $15  ==  $193.78
 			 *
			 * 158 * (   106  / 100 )  *   ( 106.75  / 100  ) + 15  = 158 + x + y + 15
			 *                                             20.7849  = x + y
			 *
			 * 12,75%  = 20.7849
			 *     6%  = X
			 *
			 * X =>  6 * 20,7849 / 12,75 = 9,7811 = 9,78
			 *
			 *
			 * 12,75%  = 20.7849
			 *  6.75%  = Y
			 *
			 * Y =>  6.75 * 20,7849 / 12,75 = 11,004 = 11
			 *
			 *
			 * Total = 158 + 9,78 + 11 +  15 = 193,78
	         */

	        $summ_of_all_percenatage_values = $my_original_cost;		// $ 20.7849
	        $summ_of_all_percenatage        = 0;						//   12.75 %

            foreach ( $advanced_cost_hint as $key_name => $array_values ) {

            	if ( ! empty( $array_values['percent'] ) ) {
					$summ_of_all_percenatage_values = $summ_of_all_percenatage_values * array_sum( $advanced_cost_hint[$key_name]['percent'] );
		            $summ_of_all_percenatage += array_sum( $advanced_cost_hint[ $key_name ]['percent'] ) * 100 - 100;
				}
            }
            $summ_of_all_percenatage_values = $summ_of_all_percenatage_values - $my_original_cost;


            foreach ( $advanced_cost_hint as $key_name => $array_values ) {

                if (! isset($advanced_cost_hint[$key_name]['cost_hint']))
                    $advanced_cost_hint[$key_name]['cost_hint'] = '';

                if ( ! empty( $array_values['percent'] ) ) {

                	$this_addition_percent = array_sum( $advanced_cost_hint[ $key_name ]['percent'] ) * 100 - 100;

	                //FixIn: 8.7.3.13	- fix division by  zero
	                if ( $summ_of_all_percenatage > 0 ) {
                		$advanced_cost_hint[$key_name]['cost_hint'] = $this_addition_percent * $summ_of_all_percenatage_values / $summ_of_all_percenatage;
					}

                } else if ( ! empty($array_values['fixed'])) {

                    $advanced_cost_hint[$key_name]['cost_hint'] = array_sum( $advanced_cost_hint[$key_name]['fixed'] );	// FixIn: 8.1.3.17.1
                }
            }
			//FixIn: 8.5.2.21  End
            /////////////////////////////////////////////////////////////////////////////////////////

			$show_advanced_cost_hints = array();
			foreach ( $advanced_cost_hint as $key => $value ) {
				$show_advanced_cost_hints[$key . '_hint'] = ( $value['cost_hint'] === '' ? '0.00' : $value['cost_hint'] );            //FixIn: 8.8.3.2
			}


			return $show_advanced_cost_hints;
        }

        if ( get_bk_option( 'booking_advanced_costs_calc_fixed_cost_with_procents' ) == 'On' ) {
            return ( $summ + $additional_cost ) * $full_procents;
        } else {                                                                              
            return $summ * $full_procents + $additional_cost ;
        }
    }


    // FixIn: 8.1.3.17
    function wpbc_replace_shortcodes_to_values( $additional_single_cost, $booking_form_field_values ){

		// Replace form fields to  values,  if exist  some shortcodes.

		if ( strpos( $additional_single_cost, '[') !== false ) {

			foreach ( $booking_form_field_values as $field_key => $field_val ) {

				if ( strtolower( $field_val ) == 'yes' ) {
					$field_val = 1;
				}
				if ( strtolower( $field_val ) == 'no' ) {
					$field_val = 0;
				}
				$additional_single_cost = str_replace( '['.  $field_key .']' , $field_val, $additional_single_cost );
			}
		}

		$how_many = preg_match_all( '/[\+\-\*\/\(\)]/',  $additional_single_cost, $matches );

		if ( ! empty($how_many ) ) {
			$additional_single_cost = wpbc_str_calc( $additional_single_cost );
		}

    	return floatval( $additional_single_cost );
    }




	/**
	 *  R E S O U R C E     T A B L E     C O S T    C o l l  u m n
	 *
	 * @param $blank
	 * @param $booking_resource_id
	 *
	 * @return mixed
	 */
	function wpbc_get_default_custom_form( $blank, $booking_resource_id ) {

		global $wpdb;
		$types_list = $wpdb->get_results( $wpdb->prepare( "SELECT default_form FROM {$wpdb->prefix}bookingtypes  WHERE booking_type_id = %d ", $booking_resource_id ) );
		if ( $types_list ) {
			return $types_list[0]->default_form;
		} else {
			return $blank;
		}
	}

}