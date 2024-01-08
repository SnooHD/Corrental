<?php
/**
 * @version     1.0
 * @package     Booking Calendar
 * @category    Default Form Templates
 * @author      wpdevelop
 *
 * @web-site    https://wpbookingcalendar.com/
 * @email       info@wpbookingcalendar.com 
 * @modified    2016-02-28
 * 
 * This is COMMERCIAL SCRIPT
 * We are not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


////////////////////////////////////////////////////////////////////////////////
// Booking Form Templates
////////////////////////////////////////////////////////////////////////////////            

/**
	 * Get Default Booking Form during activation of plugin or get  this data for init creation of custom booking form
 * 
 * @return string
 */
function wpbc_get_default_booking_form() {    
    
    $is_demo = wpbc_is_this_demo();
     
    $booking_form = '[calendar] \n\
<div class="standard-form"> \n\
 <p>'.__('First Name (required)' ,'booking').':<br />[text* name] </p> \n\
 <p>'.__('Last Name (required)' ,'booking').':<br />[text* secondname] </p> \n\
 <p>'.__('Email (required)' ,'booking').':<br />[email* email] </p> \n\
 <p>'.__('Phone' ,'booking').':<br />[text phone] </p> \n\
 <p>'.__('Adults' ,'booking').':<br />[select visitors "1" "2" "3" "4"] </p> \n\
 <p>'.__('Children' ,'booking').':<br />[select children "0" "1" "2" "3"] </p> \n\
 <p>'.__('Details' ,'booking').':<br /> [textarea details] </p> \n\
 <p>[checkbox* term_and_condition use_label_element "'.__('I Accept term and conditions' ,'booking').'"] </p> \n\
 <p>[captcha]</p> \n\
 <p>[submit class:btn "'.__('Send' ,'booking').'"]</p> \n\
</div>';


	if ( class_exists( 'wpdev_bk_biz_s' ) ) {

        $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
<div class="wpbc__form__div" style="padding: 1em 0;"> \n\
    <r> \n\
		<c> <l>' . __( 'Select Date', 'booking' ) . ':</l><br />[calendar] </c> \n\
		<c> <l>' . __( 'Select Times', 'booking' ) . '*:</l><br /> \n\
			[select* rangetime "10:00 AM - 12:00 PM@@10:00 - 12:00" "12:00 PM - 02:00 PM@@12:00 - 14:00" "02:00 PM - 04:00 PM@@14:00 - 16:00" "04:00 PM - 06:00 PM@@16:00 - 18:00" "06:00 PM - 08:00 PM@@18:00 - 20:00"] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . __( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c> \n\
		<c> <l>' . __( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . __( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c> \n\
		<c> <l>' . __( 'Phone', 'booking' ) . ':</l><br />[text phone] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . __( 'Adults', 'booking' ) . ':</l><br />[select visitors "1" "2" "3" "4" "5"] </c> \n\
		<c> <l>' . __( 'Children', 'booking' ) . ':</l><br />[select children "0" "1" "2" "3"] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . __( 'Details', 'booking' ) . ':</l> <div style="clear:both;width:100%"></div> \n\
			[textarea details] </c> \n\
	</r> \n\
	<p>[submit "' . __( 'Send', 'booking' ) . '"]</p> \n\
</div>';
	}


	if ( ( class_exists( 'wpdev_bk_biz_m' ) ) ) {
        $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
[calendar]\n\
<div class="wpbc__form__div">\n\
	<r>\n\
		<c><div class="form-hints">\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>  \n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br>\n\
		</div></c>\n\
	</r>\n\
	<hr> \n\
	<r>\n\
		<c> <l>' . __( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c>\n\
		<c> <l>' . __( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c>\n\
		<c> <l>' . __( 'Phone', 'booking' ) . ':</l><br />[text phone] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Address (required)', 'booking' ) . ':</l><br />[text* address] </c>\n\
		<c> <l>' . __( 'City (required)', 'booking' ) . ':</l><br />[text* city] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Post code (required)', 'booking' ) . ':</l><br />[text* postcode] </c>\n\
		<c> <l>' . __( 'Country (required)', 'booking' ) . ':</l><br />[country] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Adults', 'booking' ) . ':</l><br />[select visitors "1" "2" "3" "4" "5"] </c>\n\
		<c> <l>' . __( 'Children', 'booking' ) . ':</l><br />[select children "0" "1" "2" "3"] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Details', 'booking' ) . ':</l><div style="clear:both;width:100%"></div>\n\
			[textarea details] </c>\n\
	</r>\n\
	<div style="margin-top:10px;clear:both;"></div>\n\
	<r>\n\
		<c> [checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"] </c>\n\
		<c> [captcha] </c>\n\
	</r>\n\
	<r>\n\
		<c><p>\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>\n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br>\n\
		</p></c>\n\
	</r> <hr> \n\
	<r>\n\
		<c> <p>[submit "Send"]</p> </c> \n\
	</r> \n\
</div>';

/*
			'[calendar] \n\
<div class="payment-form"> \n\
 <div class="form-hints"> \n\ 
      ' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br><br> \n\ 
      ' . __( 'Full cost of the booking', 'booking' ) . ': [cost_hint] <br> \n\ 
 </div><hr/> \n\ 
 <p>' . __( 'First Name (required)', 'booking' ) . ':<br />[text* name] </p> \n\
 <p>' . __( 'Last Name (required)', 'booking' ) . ':<br />[text* secondname] </p> \n\
 <p>' . __( 'Email (required)', 'booking' ) . ':<br />[email* email] </p> \n\
 <p>' . __( 'Phone', 'booking' ) . ':<br />[text phone] </p> \n\
 <p>' . __( 'Address (required)', 'booking' ) . ':<br />  [text* address] </p> \n\  
 <p>' . __( 'City (required)', 'booking' ) . ':<br />  [text* city] </p> \n\
 <p>' . __( 'Post code (required)', 'booking' ) . ':<br />  [text* postcode] </p> \n\  
 <p>' . __( 'Country (required)', 'booking' ) . ':<br />  [country] </p> \n\
 <p>' . __( 'Adults', 'booking' ) . ':<br />[select visitors "1" "2" "3" "4"] </p> \n\
 <p>' . __( 'Children', 'booking' ) . ':<br />[select children "0" "1" "2" "3"] </p> \n\
 <p>' . __( 'Details', 'booking' ) . ':<br /> [textarea details] </p> \n\
 <p>[checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"] </p> \n\
 <p>[captcha]</p> \n\
 <p>[submit class:btn "' . __( 'Send', 'booking' ) . '"]</p> \n\
</div>';
*/
	}

	if ( ( class_exists( 'wpdev_bk_biz_l' ) )  ) {
        $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
[calendar]\n\
<div class="wpbc__form__div">\n\
	<r>\n\
		<c><p>\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>  \n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> \n\
		</p></c>\n\ 
		<c><l>Availability:</l><spacer></spacer>[capacity_hint]</c> \n\   
	</r>\n\
	<hr>  \n\
	<r>\n\
		<c> <l>' . __( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c>\n\
		<c> <l>' . __( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c>\n\
		<c> <l>' . __( 'Phone', 'booking' ) . ':</l><br />[text phone] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Adults', 'booking' ) . ':</l><br />[select visitors "1" "2" "3" "4" "5"] </c>\n\
		<c> <l>' . __( 'Children', 'booking' ) . ':</l><br />[select children "0" "1" "2" "3"] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Details', 'booking' ) . ':</l><div style="clear:both;width:100%"></div>\n\
			[textarea details] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Coupon (required)', 'booking' ) . ':</l><br />[coupon coupon] </c>\n\
	</r>\n\
	<div style="margin-top:10px;clear:both;"></div>\n\
	<r>\n\
		<c> [checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"] </c>\n\
		<c> [captcha] </c>\n\
	</r>\n\
	<r>\n\
		<c><p>\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>\n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br>\n\
		</p></c>\n\
	</r> <hr>  \n\
	<r>\n\
		<c> <p>[submit "Send"]</p> </c> \n\
	</r> \n\
</div>';

/*
			'[calendar] \n\
<div class="payment-form"><br /> \n\
 <div class="form-hints"> \n\
      ' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]<br><br> \n\
      ' . __( 'Full cost of the booking', 'booking' ) . ': [cost_hint] <br> \n\
 </div><hr/> \n\
 <p>' . __( 'First Name (required)', 'booking' ) . ':<br />[text* name] </p> \n\
 <p>' . __( 'Last Name (required)', 'booking' ) . ':<br />[text* secondname] </p> \n\
 <p>' . __( 'Email (required)', 'booking' ) . ':<br />[email* email] </p> \n\
 <p>' . __( 'Phone', 'booking' ) . ':<br />[text phone] </p> \n\
 <p>' . __( 'Address (required)', 'booking' ) . ':<br />  [text* address] </p> \n\
 <p>' . __( 'City (required)', 'booking' ) . ':<br />  [text* city] </p> \n\
 <p>' . __( 'Post code (required)', 'booking' ) . ':<br />  [text* postcode] </p> \n\
 <p>' . __( 'Country (required)', 'booking' ) . ':<br />  [country] </p> \n\
 <p>' . __( 'Visitors', 'booking' ) . ':<br />  [select visitors "1" "2" "3" "4"] </p> \n\
 <p>' . __( 'Details', 'booking' ) . ':<br /> [textarea details] </p> \n\
 <p>' . __( 'Coupon', 'booking' ) . ':<br /> [coupon coupon] </p> \n\
 <p>[checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"] </p> \n\
 <p>[captcha]</p> \n\
 <p>[submit class:btn "' . __( 'Send', 'booking' ) . '"]</p> \n\
</div>';
 */
	}
    
    return $booking_form;    
}
          

/**
	 * Get Default Form to SHOW during activation of plugin or get  this data for init creation of custom booking form
 * 
 * @return string
 */
function wpbc_get_default_booking_form_show() {
    
    $is_demo = wpbc_is_this_demo();
    
    $booking_form = '<div class="standard-content-form"> \n\
    <b>'. __('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>'. __('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>'. __('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>'. __('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>'. __('Adults' ,'booking').'</b>:     <f>[visitors]</f><br/> \n\
    <b>'. __('Children' ,'booking').'</b>:   <f>[children]</f><br/> \n\
    <b>'. __('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
                
    if ( class_exists( 'wpdev_bk_biz_s' ) ) 
        $booking_form = '<div class="standard-content-form"> \n\
    <b>'. __('Times' ,'booking').'</b>:      <f>[rangetime]</f><br/> \n\
    <b>'. __('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>'. __('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>'. __('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>'. __('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>'. __('Adults' ,'booking').'</b>:     <f>[visitors]</f><br/> \n\
    <b>'. __('Children' ,'booking').'</b>:   <f>[children]</f><br/> \n\
    <b>'. __('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
    
    if ( ( class_exists( 'wpdev_bk_biz_m' ) )   )
        $booking_form = '<div class="standard-content-form"> \n\
    <b>'. __('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>'. __('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>'. __('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>'. __('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>'. __('Adults' ,'booking').'</b>:     <f>[visitors]</f><br/> \n\
    <b>'. __('Children' ,'booking').'</b>:   <f>[children]</f><br/> \n\
    <b>'. __('Address' ,'booking').'</b>:    <f>[address]</f><br/> \n\
    <b>'. __('City' ,'booking').'</b>:       <f>[city]</f><br/> \n\
    <b>'. __('Post code' ,'booking').'</b>:  <f>[postcode]</f><br/> \n\
    <b>'. __('Country' ,'booking').'</b>:    <f>[country]</f><br/> \n\
    <b>'. __('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
    
    if ( ( class_exists( 'wpdev_bk_biz_l' ) ) )
        $booking_form = '<div class="standard-content-form"> \n\
    <b>'. __('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>'. __('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>'. __('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>'. __('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>'. __('Adults' ,'booking').'</b>:     <f>[visitors]</f><br/> \n\
    <b>'. __('Children' ,'booking').'</b>:   <f>[children]</f><br/> \n\
    <b>'. __('Coupon' ,'booking').'</b>:     <f>[coupon]</f><br/> \n\
    <b>'. __('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
        
    return $booking_form;
}


////////////////////////////////////////////////////////////////////////////////
// Search Form Templates
////////////////////////////////////////////////////////////////////////////////            

/**
	 * Default Search Form templates
 * 
 * @param string $search_form_type
 * @return string
 */
function wpbc_get_default_search_form_template( $search_form_type = '' ){     //FixIn:6.1.0.1

  switch ( $search_form_type ) {

	  //FixIn: 9.1.3.1
	  //FixIn: 8.5.2.11
      case 'flex':
          return   '<div class="wpdevelop">' . '\n\r'
				. '  <div class="form-inline well search_container">' . '\n\r'
				. '		<div class="search_row">' . '\n\r'
				. '            <label>'.__('Check in' ,'booking').':</label> [search_check_in][search_check_in_icon]' . '\n\r'
//                . '			   <i style="width: 24px;height: 16px;margin-left: -24px;" class="glyphicon glyphicon-calendar"></i>'
//                . '            <a onclick="javascript:jQuery(\'#booking_search_check_in\').trigger(\'focus\');" href="javascript:void(0)" style="width: 24px;height: 16px;margin-left: -24px;z-index: 0;outline: none;text-decoration: none;color: #707070;" class="glyphicon glyphicon-calendar"></a>'
				. '		</div>' . '\n\r'
				. '		<div class="search_row">' . '\n\r'
				. '			<label>'.__('Check out' ,'booking').':</label> [search_check_out][search_check_out_icon]' . '\n\r'
//                . '			   <i style="width: 24px;height: 16px;margin-left: -24px;" class="glyphicon glyphicon-calendar"></i>'
//                . '            <a onclick="javascript:jQuery(\'#booking_search_check_out\').trigger(\'focus\');" href="javascript:void(0)" style="width: 24px;height: 16px;margin-left: -24px;z-index: 0;outline: none;text-decoration: none;color: #707070;" class="glyphicon glyphicon-calendar"></a>'              . '		</div>' . '\n\r'
				. '		</div>' . '\n\r'
				. '		<div class="search_row">' . '\n\r'
				. '			<label>'.__('Guests' ,'booking').':</label> [search_visitors]' . '\n\r'
				. '		</div>' . '\n\r'
				. '		<div class="search_row">' . '\n\r'
				. '			<label>[additional_search "3"] +/- 2 '.__('days' ,'booking').'</label>' . '\n\r'
				. '		</div>' . '\n\r'
				. '		<div class="search_row">' . '\n\r'
				. '			[search_button]' . '\n\r'
				. '		</div>' . '\n\r'
				. '  </div>' . '\n\r'
				. '</div>';
//	            . '<style type="text/css"> #booking_search_check_in.hasDatepick, #booking_search_check_out.hasDatepick { width: 120px; } </style>';

      case 'inline':
          return   '<div class="wpdevelop">' . '\n\r'
                 . '    <div class="form-inline well">' . '\n\r'
                 . '        <label>'.__('Check in' ,'booking').':</label> [search_check_in]' . '\n\r'
                 . '        <label>'.__('Check out' ,'booking').':</label> [search_check_out]' . '\n\r'
                 . '        <label>'.__('Guests' ,'booking').':</label> [search_visitors]' . '\n\r'
                 . '        [search_button]' . '\n\r'
                 . '    </div>' . '\n\r'
                 . '</div>';

      case 'horizontal':
          return   '<div class="wpdevelop">' . '\n\r'
                 . '    <div class="form-horizontal well">' . '\n\r'
                 . '        <label>'.__('Check in' ,'booking').':</label> [search_check_in]' . '\n\r'
                 . '        <label>'.__('Check out' ,'booking').':</label> [search_check_out]' . '\n\r'
                 . '        <label>'.__('Guests' ,'booking').':</label> [search_visitors]' . '\n\r'
                 . '        <hr/>\n\        [search_button]' . '\n\r'
                 . '    </div>' . '\n\r'
                 . '</div>';

      case 'advanced':                                            
          return   '<div class="wpdevelop">' . '\n\r'
                 . '    <div class="form-inline well">' . '\n\r'
                 . '        <label>'.__('Check in' ,'booking').':</label> [search_check_in]' . '\n\r'
                 . '        <label>'.__('Check out' ,'booking').':</label> [search_check_out]' . '\n\r'
                 . '        <label>'.__('Guests' ,'booking').':</label> [search_visitors]' . '\n\r'
                 . '        [search_button]' . '\n\r'
                 . '        <br/><label>[additional_search "3"] +/- 2 '.__('days' ,'booking').'</label>' . '\n\r'
                 . '    </div>' . '\n\r'
                 . '</div>';
      default:
          return 

                   ' <label>'.__('Check in' ,'booking').':</label> [search_check_in]' . '\n\r'
                 . ' <label>'.__('Check out' ,'booking').':</label> [search_check_out]' . '\n\r'
                 . ' <label>'.__('Guests' ,'booking').':</label> [search_visitors]' . '\n\r'
                 . ' [search_button] ';                        
  }

}


/**
	 * Default Search Results templates
 * 
 * @param string $search_form_type
 * @return string
 */
function wpbc_get_default_search_results_template( $search_form_type = '' ){     //FixIn:6.1.0.1

    switch ($search_form_type) {                    

	  //FixIn: 8.5.2.11
      case 'flex':
				   return '<div class="wpdevelop search_results_container">' . '\n\r'
                 . '  ' . '	  <div class="search_results_a">' . '\n\r'
                 . '  ' . '		  <div class="search_results_b">' . '\n\r'
                 . '  ' . '			  <a href="[book_now_link]" class="wpbc_book_now_link">' . '\n\r'
                 . '  ' . '				  [booking_resource_title]' . '\n\r'
                 . '  ' . '			  </a>' . '\n\r'
                 . '  ' . '		  </div>' . '\n\r'
                 . '  ' . '		  <div class="search_results_b">' . '\n\r'
                 . '  ' . '		  	  [booking_featured_image]' . '\n\r'
                 . '  ' . '		  </div>' . '\n\r'
                 . '  ' . '		  <div class="search_results_b">' . '\n\r'
                 . '  ' . '		  	  [booking_info]' . '\n\r'
                 . '  ' . '		  </div>' . '\n\r'
                 . '  ' . '		  <div class="search_results_b">' . '\n\r'
                 . '  ' . '			' . __('Availability' ,'booking').': [num_available_resources] item(s).' . '\n\r'
                 // . '  ' . '			' . __('Max. persons' ,'booking').': [max_visitors]' . '\n\r'
                 . '  ' . '			Check in/out: <strong>[search_check_in]</strong> -' . '\n\r'
                 . '  ' . '						  <strong>[search_check_out]</strong>' . '\n\r'
                 . '  ' . '		  </div>' . '\n\r'
                 . '  ' . '	  </div>' . '\n\r'
                 . '  ' . '	  <div class="search_results_a2">' . '\n\r'
                 . '  ' . '		<div class="search_results_b2">' . '\n\r'
                 . '  ' . '			Cost: <strong>[cost_hint]</strong>' . '\n\r'
                 . '  ' . '		</div>' . '\n\r'
                 . '  ' . '  	    <div class="search_results_b2">' . '\n\r'
                 . '  ' . '			[link_to_booking_resource "Book now"]' . '\n\r'
                 . '  ' . '		</div>' . '\n\r'
                 . '  ' . '	  </div>' . '\n\r'
                 . '  ' . '</div>';

      case 'advanced':
          return   '<div class="wpdevelop">' . '\n\r'
                 . '  ' . '<div style="float:right;"><div>Cost: <strong>[cost_hint]</strong></div>' . '\n\r'
                 . '  ' . '[link_to_booking_resource "Book now"]</div>' . '\n\r'
                 . '  ' . '<a href="[book_now_link]" class="wpbc_book_now_link">' . '\n\r'
                 . '  ' . '    ' .'[booking_resource_title]' . '\n\r'
                 . '  ' . '</a>' . '\n\r'
                 . '  ' . '[booking_featured_image]' . '\n\r'
                 . '  ' . '[booking_info]' . '\n\r'
                 . '  ' . '<div>' . '\n\r'
                 . '  ' . '  ' . __('Availability' ,'booking').': [num_available_resources] item(s).' . '\n\r'
                 // . '  ' . '  ' . __('Max. persons' ,'booking').': [max_visitors]' . '\n\r'
                 . '  ' . '  ' . 'Check in/out: <strong>[search_check_in]</strong> - ' . '\n\r'
                 . '  ' . '                ' . '<strong>[search_check_out]</strong>' . '\n\r'
                 . '  ' . '</div>' . '\n\r'
                 . '</div>';

      default:
          return   '<div class="wpdevelop">' . '\n\r'
                 . '    <div style="float:right;">' . '\n\r'
                 . '        ' . '<div>From [standard_cost]</div>' . '\n\r'
                 . '        ' . '[link_to_booking_resource "Book now"]' . '\n\r'
                 . '    </div>' . '\n\r'
                 . '    [booking_resource_title]' . '\n\r'
                 . '    [booking_featured_image]' . '\n\r'
                 . '    [booking_info]' . '\n\r'
                 . '    <div>' . '\n\r'
                 . '        ' . __('Availability' ,'booking').': [num_available_resources] item(s).' . '\n\r'
                 // . '        ' . __('Max. persons' ,'booking').': [max_visitors]' . '\n\r'
                 . '    </div>' . '\n\r'                            
                 . '</div>';
    }                      
}
