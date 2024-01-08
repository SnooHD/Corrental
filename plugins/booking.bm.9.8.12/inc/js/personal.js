jQuery(document).ready( function(){
   if( jQuery('.wpdev-validates-as-time').length > 0 ) {
       jQuery('.wpdev-validates-as-time').attr('alt','time');
       jQuery('.wpdev-validates-as-time').setMask();
   }
});

// ---------------------------------------------------------------------------------------------------------------------

// Send booking Cancel by visitor
function wpbc_customer_action__booking_cancel( booking_hash, bk_type, wpdev_active_locale ){
    
    if (booking_hash!='') {
        
        document.getElementById('submiting' + bk_type).innerHTML =
            '<div style="height:20px;width:100%;text-align:center;margin:15px auto;"><img  style="vertical-align:middle;box-shadow:none;width:14px;" src="'+wpdev_bk_plugin_url+'/assets/img/ajax-loader.gif"><//div>';

        var wpdev_ajax_path = wpdev_bk_plugin_url+'/' + wpdev_bk_plugin_filename;
        var ajax_type_action='DELETE_BY_VISITOR';

        jQuery.ajax({                                           // Start Ajax Sending
            // url: wpdev_ajax_path,
            url: wpbc_ajaxurl,
            type:'POST',
            success: function (data, textStatus){if( textStatus == 'success')   jQuery('#ajax_respond_insert' + bk_type).html( data ) ;},
            error:function (XMLHttpRequest, textStatus, errorThrown){window.status = 'Ajax sending Error status:'+ textStatus;alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText);if (XMLHttpRequest.status == 500) {alert('Please check at this page according this error:' + ' https://wpbookingcalendar.com/faq/#ajax-sending-error');}},
            // beforeSend: someFunction,
            data:{
                // ajax_action : ajax_type_action,
                action : ajax_type_action,
                booking_hash : booking_hash,
                bk_type : bk_type,
                wpdev_active_locale:wpdev_active_locale,
                wpbc_nonce: document.getElementById('wpbc_nonce_delete'+bk_type).value 
            }
        });
        return false;
    }
    return true;
}

// ---------------------------------------------------------------------------------------------------------------------

/**
 * Set checkbox in booking form Exclusive on click
 *
 * @param element
 */
function wpbc_in_form__make_exclusive_checkbox( element ){

    jQuery( '[name="' + element.name + '"]' ).prop( "checked", false );             // Uncheck  all checkboxes with  this name

    element.checked = true;
}

/**
 * Set select-box with multiple selections - Exclusive
 * @param element
 */
function wpbc_in_form__make_exclusive_selectbox( element ){

    // Get all selected elements.
    var selectedOptions = jQuery.find( '[name="' + element.name + '"] option:selected' );

    // Check if we have more than 1 selection
    if ( selectedOptions.length > 1 ){

        var ind = selectedOptions[ 0 ].index;                                             // Get index of the first  selected element
        jQuery( '[name="' + element.name + '"] option' ).prop( "selected", false );             // Uncheck  all checkboxes with  this name
        jQuery( '[name="' + element.name + '"] option:eq(' + ind + ')' ).prop( "selected", true );  // Set the first element selected
    }
}

// ---------------------------------------------------------------------------------------------------------------------
                                                                                                                        //FixIn: 9.6.3.5

function wpbc_reset__form_configuration(type) {

    var editor_textarea_id = 'booking_form';
    var editor_textarea_content = wpbc_reset__get_form_configuration( type );

    //FixIn: 8.4.7.18
    if  ( (typeof WPBC_CM !== 'undefined') && ( WPBC_CM.is_defined( '#' + editor_textarea_id ) ) ){

        WPBC_CM.set_codemirror_value( '#' + editor_textarea_id , editor_textarea_content );

    } else {

        if ( typeof tinymce != "undefined" ){
            var editor = tinymce.get( editor_textarea_id );
            if ( editor && editor instanceof tinymce.Editor ){
                editor.setContent( editor_textarea_content );
                editor.save( {no_events: true} );
            } else {
                jQuery( '#' + editor_textarea_id ).val( editor_textarea_content );
            }
        } else {
            jQuery( '#' + editor_textarea_id ).val( editor_textarea_content );
        }
    }
}

function wpbc_reset__form_data(type) {

    var editor_textarea_id = 'booking_form_show';
    var editor_textarea_content = wpbc_reset__get_form_data(type);

    //FixIn: 8.4.7.18
    if  ( (typeof WPBC_CM !== 'undefined') && ( WPBC_CM.is_defined( '#' + editor_textarea_id ) ) ){

        WPBC_CM.set_codemirror_value( '#' + editor_textarea_id , editor_textarea_content );
    } else {


        if( typeof tinymce != "undefined" ) {
            var editor = tinymce.get( editor_textarea_id );
            if( editor && editor instanceof tinymce.Editor ) {
                editor.setContent( editor_textarea_content );
                editor.save( { no_events: true } );
            } else {
                jQuery( '#' + editor_textarea_id ).val( editor_textarea_content );
            }
        } else {
            jQuery( '#' + editor_textarea_id ).val( editor_textarea_content );
        }
    }
}

function wpbc_reset__get_form_configuration( form_type ) {
    var form_content = '';
    
    if ( (form_type == 'times') || (form_type == 'times30')  || (form_type == 'times15') ){
        form_content = '';
        form_content +='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
        form_content +='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
        form_content +='<div class="wpbc__form__div">  \n';
        form_content +='	<r>  \n';
        form_content +='		<c> [calendar]  </c> \n';
        form_content +='		<c>  <l>Select Times (required):</l><br /> \n';
        if ( form_type == 'times' ) {
            form_content +='			[select* rangetime multiple "10:00 AM - 12:00 PM@@10:00 - 12:00" "12:00 PM - 02:00 PM@@12:00 - 14:00" "02:00 PM - 04:00 PM@@14:00 - 16:00" "04:00 PM - 06:00 PM@@16:00 - 18:00" "06:00 PM - 08:00 PM@@18:00 - 20:00"] \n';
        }
        if ( form_type == 'times30' ) {
            form_content +='			[select rangetime "06:00 - 06:30" "06:30 - 07:00" "07:00 - 07:30" "07:30 - 08:00" "08:00 - 08:30" "08:30 - 09:00" "09:00 - 09:30" "09:30 - 10:00" "10:00 - 10:30" "10:30 - 11:00" "11:00 - 11:30" "11:30 - 12:00" "12:00 - 12:30" "12:30 - 13:00" "13:00 - 13:30" "13:30 - 14:00" "14:00 - 14:30" "14:30 - 15:00" "15:00 - 15:30" "15:30 - 16:00" "16:00 - 16:30" "16:30 - 17:00" "17:00 - 17:30" "17:30 - 18:00" "18:00 - 18:30" "18:30 - 19:00" "19:00 - 19:30" "19:30 - 20:00" "20:00 - 20:30" "20:30 - 21:00" "21:00 - 21:30"] \n';
        }
        if ( form_type == 'times15' ) {
            form_content +='			[select rangetime "8:00 AM - 8:15 AM@@08:00 - 08:15" "8:15 AM - 8:30 AM@@08:15 - 08:30" "8:30 AM - 8:45 AM@@08:30 - 08:45" "8:45 AM - 9:00 AM@@08:45 - 09:00" "9:00 AM - 9:15 AM@@09:00 - 09:15" "9:15 AM - 9:30 AM@@09:15 - 09:30" "9:30 AM - 9:45 AM@@09:30 - 09:45" "9:45 AM - 10:00 AM@@09:45 - 10:00" "10:00 AM - 10:15 AM@@10:00 - 10:15" "10:15 AM - 10:30 AM@@10:15 - 10:30" "10:30 AM - 10:45 AM@@10:30 - 10:45" "10:45 AM - 11:00 AM@@10:45 - 11:00" "11:00 AM - 11:15 AM@@11:00 - 11:15" "11:15 AM - 11:30 AM@@11:15 - 11:30" "11:30 AM - 11:45 AM@@11:30 - 11:45" "11:45 AM - 12:00 AM@@11:45 - 12:00" "12:00 AM - 12:15 AM@@12:00 - 12:15" "12:15 AM - 12:30 AM@@12:15 - 12:30" "12:30 AM - 12:45 AM@@12:30 - 12:45" "12:45 AM - 1:00 PM@@12:45 - 13:00" "1:00 PM - 1:15 PM@@13:00 - 13:15" "1:15 PM - 1:30 PM@@13:15 - 13:30" "1:30 PM - 1:45 PM@@13:30 - 13:45" "1:45 PM - 2:00 PM@@13:45 - 14:00" "2:00 PM - 2:15 PM@@14:00 - 14:15" "2:15 PM - 2:30 PM@@14:15 - 14:30" "2:30 PM - 2:45 PM@@14:30 - 14:45" "2:45 PM - 3:00 PM@@14:45 - 15:00" "3:00 PM - 3:15 PM@@15:00 - 15:15" "3:15 PM - 3:30 PM@@15:15 - 15:30" "3:30 PM - 3:45 PM@@15:30 - 15:45" "3:45 PM - 4:00 PM@@15:45 - 16:00" "4:00 PM - 4:15 PM@@16:00 - 16:15" "4:15 PM - 4:30 PM@@16:15 - 16:30" "4:30 PM - 4:45 PM@@16:30 - 16:45" "4:45 PM - 5:00 PM@@16:45 - 17:00" "5:00 PM - 5:15 PM@@17:00 - 17:15" "5:15 PM - 5:30 PM@@17:15 - 17:30" "5:30 PM - 5:45 PM@@17:30 - 17:45" "5:45 PM - 6:00 PM@@17:45 - 18:00" "6:00 PM - 6:15 PM@@18:00 - 18:15" "6:15 PM - 6:30 PM@@18:15 - 18:30" "6:30 PM - 6:45 PM@@18:30 - 18:45" "6:45 PM - 7:00 PM@@18:45 - 19:00" "7:00 PM - 7:15 PM@@19:00 - 19:15" "7:15 PM - 7:30 PM@@19:15 - 19:30" "7:30 PM - 7:45 PM@@19:30 - 19:45" "7:45 PM - 8:00 PM@@19:45 - 20:00" "8:00 PM - 8:15 PM@@20:00 - 20:15" "8:15 PM - 8:30 PM@@20:15 - 20:30" "8:30 PM - 8:45 PM@@20:30 - 20:45" "8:45 PM - 9:00 PM@@20:45 - 21:00" "9:00 PM - 9:15 PM@@21:00 - 21:15" "9:15 PM - 9:30 PM@@21:15 - 21:30" "9:30 PM - 9:45 PM@@21:30 - 21:45"] \n';
        }
        if ( 'function' === typeof( wpbc_update_capacity_hint ) ){                                                      // >= biz_l
            form_content +='		<spacer>height:1em;</spacer> \n';
            form_content +='		<l>Availability:</l><spacer></spacer> \n';
            form_content +='		[capacity_hint] \n';
        }
        form_content +='		</c> \n';
        form_content +='	</r> \n';
        form_content +='	<hr> \n';
        form_content +='	<r> \n';
        form_content +='		<c> <l>First Name (required):</l><br />[text* name] </c> \n';
        form_content +='		<c> <l>Last Name (required):</l><br />[text* secondname] </c> \n';
        form_content +='	</r> \n';
        form_content +='	<r> \n';
        form_content +='		<c> <l>Email (required):</l><br />[email* email] </c> \n';
        form_content +='		<c> <l>Phone:</l><br />[text phone] </c> \n';
        form_content +='	</r> \n';
        form_content +='	<r> \n';
        form_content +='		<c> <l>Adults:</l><br />[select visitors "1" "2" "3" "4" "5"] </c> \n';
        form_content +='		<c> <l>Children:</l><br />[select children "0" "1" "2" "3"] </c> \n';
        form_content +='	</r> \n';
        form_content +='	<r> \n';
        form_content +='		<c> <l>Details:</l><spacer></spacer> \n';
        form_content +='			[textarea details] </c> \n';
        form_content +='	</r> \n';
        form_content +='	<spacer>height:10px;</spacer> \n';
        form_content +='	<r> \n';
        form_content +='		<c> [checkbox* term_and_condition use_label_element "I Accept term and conditions"] </c> \n';
        form_content +='		<c> [captcha] </c> \n';
        form_content +='	</r> \n';
        if ( 'function' === typeof( wpbc_show_day_cost_in_date_bottom ) ){                                              // >= biz_m
            form_content += '	<r> \n';
            form_content += '		<c><p> \n';
            form_content += '			Dates: <strong>[selected_short_timedates_hint]</strong> \n';
            form_content += '			([nights_number_hint] - night(s))<br> \n';
            form_content += '			Full cost of the booking: <strong>[cost_hint]</strong> <br> \n';
            form_content += '		</p></c> \n';
            form_content += '	</r> \n';
        }
        form_content +='	<hr> \n';
        form_content +='	<r> \n';
        form_content +='		<c> <p>[submit "Send"]</p> </c> \n';
        form_content +='	</r> \n';
        form_content +='</div> \n';
    }

    if (form_type == 'timesweek'){
           form_content = '';
           form_content +='[calendar] \n'; 
           form_content +='<div class="times-form"> \n';
           form_content +='<p> \n';
           form_content +='    [condition name="weekday-condition" type="weekday" value="*"] \n';
           form_content +='        Select Time Slot:<br> [select rangetime multiple "10:00 - 12:00" "12:00 - 14:00" "14:00 - 16:00" "16:00 - 18:00" "18:00 - 20:00"] \n';
           form_content +='    [/condition] \n';
           form_content +='    [condition name="weekday-condition" type="weekday" value="1,2"] \n';
           form_content +='        Select Time Slot available on Monday, Tuesday:<br>    [select rangetime multiple "10:00 - 12:00" "12:00 - 14:00"] \n';
           form_content +='    [/condition] \n';
           form_content +='    [condition name="weekday-condition" type="weekday" value="3,4"] \n';
           form_content +='        Select Time Slot available on Wednesday, Thursday:<br>  [select rangetime multiple "14:00 - 16:00" "16:00 - 18:00" "18:00 - 20:00"] \n';
           form_content +='    [/condition] \n';
           form_content +='    [condition name="weekday-condition" type="weekday" value="5,6,0"] \n';
           form_content +='        Select Time Slot available on Friday, Saturday, Sunday:<br> [select rangetime multiple "12:00 - 14:00" "14:00 - 16:00" "16:00 - 18:00"] \n';
           form_content +='    [/condition] \n';
           form_content +='</p> \n';
           form_content +='     <p>First Name (required):<br />[text* name] </p> \n';
           form_content +='     <p>Last Name (required):<br />[text* secondname] </p> \n';
           form_content +='     <p>Email (required):<br />[email* email] </p>   \n';
           form_content +='     <p>Phone:<br />[text phone] </p> \n';
           form_content +='     <p>Adults:<br />[select visitors "1" "2" "3" "4"]</p> \n';
           form_content +='     <p>Children:<br />[select children "0" "1" "2" "3"]</p> \n';
           form_content +='     <p>Details:<br /> [textarea details] </p> \n';
           form_content +='     <p>[checkbox* term_and_condition use_label_element "I Accept term and conditions"] </p>\n';
           form_content +='     <p>[captcha]</p> \n';
           form_content +='     <p>[submit class:btn "Send"]</p> \n';
           form_content +='</div>';
    }

    if (form_type == 'hints'){
           form_content = '';
           form_content +='[calendar] \n'; 
           form_content +='<div class="standard-form"> \n';
           if ( 'function' === typeof( wpbc_show_day_cost_in_date_bottom ) ){                                              // >= biz_m
               form_content += '     <div class="form-hints"> \n';
               form_content += '          Dates:[selected_short_timedates_hint]  ([nights_number_hint] - night(s))<br><br> \n';
               form_content += '          Full cost of the booking: [cost_hint] <br> \n';
               form_content += '     </div><hr/> \n';
           }
           form_content +='     <p>First Name (required):<br />[text* name] </p> \n';
           form_content +='     <p>Last Name (required):<br />[text* secondname] </p> \n';
           form_content +='     <p>Email (required):<br />[email* email] </p>   \n';
           form_content +='     <p>Phone:<br />[text phone] </p> \n';
           form_content +='     <p>Adults:<br />[select visitors "1" "2" "3" "4"]</p> \n';
           form_content +='     <p>Children:<br />[select children "0" "1" "2" "3"]</p> \n';
           form_content +='     <p>Details:<br /> [textarea details] </p> \n';
           form_content +='     <p>[checkbox* term_and_condition use_label_element "I Accept term and conditions"] </p>\n';
           form_content +='     <p>[captcha]</p> \n';
           form_content +='     <p>[submit class:btn "Send"]</p> \n';
           form_content +='</div>';
    }

    //FixIn: 8.7.3.5
    if ( 'hints-dev' == form_type ){
            form_content = '';
            form_content +='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
            form_content +='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
            form_content +='[calendar] \n';
            form_content +='<div class="wpbc__form__div"> \n';
            if ( 'function' === typeof( wpbc_update_capacity_hint ) ){                                                      // >= biz_l
                form_content +='	<r> \n';
                form_content +='		<c><l>Availability:</l><spacer></spacer>[capacity_hint]</c> \n';
                form_content +='	</r> \n';
            }
            if ( 'function' === typeof( wpbc_show_day_cost_in_date_bottom ) ){                                              // >= biz_m
                form_content +='	<r> \n';
                form_content +='		<c><p> \n';
                form_content +='			Dates: <strong>[selected_short_timedates_hint]</strong> \n';
                form_content +='			([nights_number_hint] - night(s))<br> \n';
                form_content +='			Full cost of the booking: <strong>[cost_hint]</strong> <br> \n';
                form_content +='		</p></c> \n';
                form_content +='	</r> <hr> \n';
            }
            form_content +='	<r> \n';
            form_content +='		<c> <l>First Name (required):</l><br />[text* name] </c> \n';
            form_content +='		<c> <l>Last Name (required):</l><br />[text* secondname] </c> \n';
            form_content +='	</r> \n';
            form_content +='	<r> \n';
            form_content +='		<c> <l>Email (required):</l><br />[email* email] </c> \n';
            form_content +='		<c> <l>Phone:</l><br />[text phone] </c> \n';
            form_content +='	</r> \n';
            form_content +='	<r> \n';
            form_content +='		<c> <l>Adults:</l><br />[select visitors "1" "2" "3" "4" "5"] </c> \n';
            form_content +='		<c> <l>Children:</l><br />[select children "0" "1" "2" "3"] </c> \n';
            form_content +='	</r> \n';
            form_content +='	<r> \n';
            form_content +='		<c> <l>Details:</l><spacer></spacer> \n';
            form_content +='			[textarea details] </c> \n';
            form_content +='	</r> \n';
            form_content +='	<spacer>height:10px;</spacer> \n';
            form_content +='	<r> \n';
            form_content +='		<c> [checkbox* term_and_condition use_label_element "I Accept term and conditions"] </c> \n';
            form_content +='		<c> [captcha] </c> \n';
            form_content +='	</r> \n';
            if ( 'function' === typeof( wpbc_show_day_cost_in_date_bottom ) ){                                              // >= biz_m
                form_content += '	<r> \n';
                form_content += '		<c><p> \n';
                form_content += '			Dates: <strong>[selected_short_timedates_hint]</strong> \n';
                form_content += '			([nights_number_hint] - night(s))<br> \n';
                form_content += '			Full cost of the booking: <strong>[cost_hint]</strong> <br> \n';
                form_content += '		</p></c> \n';
                form_content += '	</r> \n';
            }
            form_content +='	<hr> \n';
            form_content +='	<r> \n';
            form_content +='		<c> <p>[submit "Send"]</p> </c> \n';
            form_content +='	</r> \n';
            form_content +='</div> \n';
    }

	if ( (form_type == 'payment') || (form_type == 'paymentUS') ){
        form_content = '';
        form_content +='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
        form_content +='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
        form_content +='[calendar] \n';
        form_content +='<div class="payment-form"> \n';
        form_content +='     <p>First Name (required):<br />[text* name] </p> \n';
        form_content +='     <p>Last Name (required):<br />[text* secondname] </p> \n';
        form_content +='     <p>Email (required):<br />[email* email] </p> \n';
        form_content +='     <p>Phone:<br />[text phone] </p> \n';
        form_content +='     <p>Address (required):<br />  [text* address] </p> \n';
        form_content +='     <p>City (required):<br />  [text* city] </p> \n';
        form_content +='     <p>Post code (required):<br />  [text* postcode] </p> \n';
        if ( form_type == 'paymentUS' ){                                                                                //FixIn: 8.1.1.5
            form_content +='     <p>Country (required):<br />  [country "US"] </p> \n';
            form_content +='     <p>State:<br /> [select state "" "Alabama@@AL" "Alaska@@AK" "Arizona@@AZ" "Arkansas@@AR" "California@@CA" "Colorado@@CO" "Connecticut@@CT" "Delaware@@DE" "Florida@@FL" "Georgia@@GA" "Hawaii@@HI" "Idaho@@ID" "Illinois@@IL" "Indiana@@IN" "Iowa@@IA" "Kansas@@KS" "Kentucky@@KY" "Louisiana@@LA" "Maine@@ME" "Maryland@@MD" "Massachusetts@@MA" "Michigan@@MI" "Minnesota@@MN" "Mississippi@@MS" "Missouri@@MO" "Montana@@MT" "Nebraska@@NE" "Nevada@@NV" "New Hampshire@@NH" "New Jersey@@NJ" "New Mexico@@NM" "New York@@NY" "North Carolina@@NC" "North Dakota@@ND" "Ohio@@OH" "Oklahoma@@OK" "Oregon@@OR" "Pennsylvania@@PA" "Rhode Island@@RI" "South Carolina@@SC" "South Dakota@@SD" "Tennessee@@TN" "Texas@@TX" "Utah@@UT" "Vermont@@VT" "Virginia@@VA" "Washington@@WA" "West Virginia@@WV" "Wisconsin@@WI" "Wyoming@@WY"] </p> \n';
		} else {
			form_content += '     <p>Country (required):<br />  [country] </p> \n';
		}
        form_content +='     <p>Adults:<br />[select visitors "1" "2" "3" "4"]</p> \n';
        form_content +='     <p>Children:<br />[select children "0" "1" "2" "3"]</p> \n';
        form_content +='     <p>Details:<br /> [textarea details] </p> \n';
        form_content +='     <p>[checkbox* term_and_condition use_label_element "I Accept term and conditions"] </p> \n';
        form_content +='     <p>[captcha]</p> \n';
        form_content +='     <p>[submit class:btn "Send"]</p> \n';
        form_content +='</div>';
    }

    if (form_type == 'wizard')  {
        //FixIn: 8.6.1.15
		form_content = '';
        if ( location.href.indexOf( 'wpbookingcalendar.com' ) !== -1 ){
            form_content += '<!-- In our Public Demo, JavaScript is restricted in the form. --> \n';
            form_content += '<!-- As a result, the "Wizard Form Template" is unable to function here. --> \n';
        }
        form_content +='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
        form_content +='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
        form_content +='<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step1"> \n';
        form_content +='		<r> \n';
        form_content +='			<c> [calendar] </c> \n';
        if ( 'function' === typeof( wpbc_show_day_cost_in_date_bottom ) ){                                              // >= biz_m
            form_content += '			<c><p> \n';
            form_content += '				Dates: <strong>[selected_short_timedates_hint]</strong> \n';
            form_content += '				([nights_number_hint] - night(s))<br> \n';
            form_content += '				Full cost of the booking: <strong>[cost_hint]</strong> <br> \n';
            form_content += '			</p></c> \n';
        }
        form_content +='		</r> <hr> \n';
        form_content +='		<r> \n';
        form_content +='			<div class="wpbc__field" style="justify-content: flex-end;"> \n';
        form_content +='     			<a class="wpbc_button_light" href="javascript:void(0);" \n';
        form_content +='				   onclick="javascript:wpbc_wizard_step(this ,2);"> \n';
        form_content +='				Continue to step 2 \n';
        form_content +='				</a> \n';
        form_content +='			</div> \n';
        form_content +='		</r> \n';
        form_content +='</div> \n';
        form_content +='<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step2" style="display:none;clear:both;"> \n';
        form_content +='	<r> \n';
        form_content +='		<c> <l>First Name (required):</l><br />[text* name] </c> \n';
        form_content +='		<c> <l>Last Name (required):</l><br />[text* secondname] </c> \n';
        form_content +='	</r> \n';
        form_content +='	<r> \n';
        form_content +='		<c> <l>Email (required):</l><br />[email* email] </c> \n';
        form_content +='		<c> <l>Phone:</l><br />[text phone] </c> \n';
        form_content +='	</r> \n';
        form_content +='	<r> \n';
        form_content +='		<c> <l>Adults:</l><br />[select visitors "1" "2" "3" "4" "5"] </c> \n';
        form_content +='		<c> <l>Children:</l><br />[select children "0" "1" "2" "3"] </c> \n';
        form_content +='	</r> \n';
        form_content +='	<r> \n';
        form_content +='		<c> <l>Details:</l><spacer></spacer> \n';
        form_content +='			[textarea details] </c> \n';
        form_content +='	</r> \n';
        form_content +='	<spacer>height:10px;</spacer> \n';
        form_content +='	<r> \n';
        form_content +='		<c> [checkbox* term_and_condition use_label_element "I Accept term and conditions"] </c> \n';
        form_content +='		<c> [captcha] </c> \n';
        form_content +='	</r> \n';
        if ( 'function' === typeof( wpbc_show_day_cost_in_date_bottom ) ){                                              // >= biz_m
            form_content += '	<r> \n';
            form_content += '		<c><div class="form-hints"> \n';
            form_content += '			Dates: <strong>[selected_short_timedates_hint]</strong> \n';
            form_content += '			([nights_number_hint] - night(s))<br> \n';
            form_content += '			Full cost of the booking: <strong>[cost_hint]</strong> <br> \n';
            form_content += '		</div></c> \n';
            form_content += '	</r> \n';
        }
        form_content +='	<hr> \n';
        form_content +='	<r> \n';
        form_content +='		<div class="wpbc__field" style="justify-content: flex-end;"> \n';
        form_content +='			<a href="javascript:void(0);" \n';
        form_content +='				  onclick="javascript:wpbc_wizard_step(this, 1);" \n';
        form_content +='				  class="wpbc_button_light">Back to step 1</a>&nbsp;&nbsp;&nbsp; \n';
        form_content +='			[submit "Send"] \n';
        form_content +='		</div> \n';
        form_content +='	</r> \n';
        form_content +='</div> \n';

    }

    if (form_type == '2collumns')  { // calendar next to  form
        form_content = '';

        form_content +='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
        form_content +='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
        form_content +='<r> \n';
        form_content +='	<c>[calendar]</c> \n';
        form_content +='	<spacer>width:40px;</spacer> \n';
        form_content +='	<c> \n';
        form_content +='		<r><c> <l>First Name (required):</l> <br>[text* name]</c></r> \n';
        form_content +='		<r><c> <l>Last Name (required):</l>  <br>[text* secondname] </c></r> \n';
        form_content +='		<r><c> <l>Email (required):</l>      <br>[email* email] </c></r> \n';
        form_content +='		<r><c> <l>Phone:</l>                 <br>[text phone] </c></r> \n';
        form_content +='		<r><c> <l>Adults:</l>                <br>[select visitors "1" "2" "3" "4"] </c></r> \n';
        form_content +='		<r><c> <l>Children:</l>              <br>[select children "0" "1" "2" "3"] </c></r> \n';
        form_content +='		<r><c> <l>Details:</l><br> [textarea details]</c></r> \n';
        form_content +='		<r> \n';
        form_content +='			<c>[checkbox* term_and_condition use_label_element "I Accept term and conditions"]</c> \n';
        form_content +='			<c>[captcha]</c> \n';
        form_content +='		</r> \n';
        form_content +='		<hr/> \n';
        form_content +='		<r><c>[submit class:btn "Send"]</c></r> \n';
        form_content +='	</c> \n';
        form_content +='</r> \n';


        // form_content +='<r> \n';
        // form_content +='	<c>[calendar]</c> \n';
        // form_content +='	<c> \n';
        // form_content +='		<r><c> <l>First Name (required):</l><br>[text* name]</c></r> \n';
        // form_content +='		<r><c> <l>Last Name (required):</l><br>[text* secondname]</c></r> \n';
        // form_content +='		<r><c> <l>Email (required):</l><br>[email* email]</c></r> \n';
        // form_content +='		<r><c> <l>Phone:</l><br>[text phone]</c></r> \n';
        // form_content +='		<r><c> <l>Adults:</l><br>[select visitors "1" "2" "3" "4"]</c></r> \n';
        // form_content +='		<r><c> <l>Children:</l><br>[select children "0" "1" "2" "3"]</c></r> \n';
        // form_content +='		<r><c> <l>Details:</l><br> [textarea details]</c></r> \n';
        // form_content +='		<r> \n';
        // form_content +='			<c>[checkbox* term_and_condition use_label_element "I Accept term and conditions"]</c> \n';
        // form_content +='			<c>[captcha]</c> \n';
        // form_content +='		</r> \n';
        // form_content +='		<hr/> \n';
        // form_content +='		<r><c>[submit class:btn "Send"]</c></r> \n';
        // form_content +='	</c> \n';
        // form_content +='</r> \n';

        // form_content +='<div class="wpbc_sections"> \n';
        // form_content +='	<div class="wpbc_section_50"> \n';
        // form_content +='		[calendar] \n';
        // form_content +='	</div> \n';
        // form_content +='	<div class="wpbc_section_spacer"></div> \n';
        // form_content +='	<div class="wpbc_section_50"> \n';
        // form_content +='		<p>First Name (required):<br />[text* name] </p> \n';
        // form_content +='		<p>Last Name (required):<br />[text* secondname] </p>  \n';
        // form_content +='		<p>Email (required):<br />[email* email] </p>  \n';
        // form_content +='		<p>Phone:<br />[text phone] </p>  \n';
        // form_content +='		<p>Adults:<br />[select visitors "1" "2" "3" "4"]</p> \n';
        // form_content +='		<p>Children:<br />[select children "0" "1" "2" "3"]</p>  \n';
        // form_content +='	</div> \n';
        // form_content +='	<div class="wpbc_section_100"> \n';
        // form_content +='		<p>Details:<br /> [textarea details]</p>  \n';
        // form_content +='		[captcha] \n';
        // form_content +='		<p>[checkbox* term_and_condition use_label_element "I Accept term and conditions"]</p> \n';
        // form_content +='		<hr/> \n';
        // form_content +='		<p>[submit class:btn "Send"] </p> \n';
        // form_content +='	</div> \n';
        // form_content +='</div> \n';
    }

    //FixIn: 8.7.7.15
    if (form_type == 'fields2columns')  { // 2 columns form
        form_content = '';
        form_content +='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
        form_content +='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
        form_content +='[calendar]\n';
        form_content +='<div class="wpbc__form__div">\n';
        form_content +='	<r>\n';
        form_content +='		<c> <l>First Name (required):</l><br>[text* name] </c>\n';
        form_content +='		<c> <l>Last Name (required):</l><br>[text* secondname] </c>\n';
        form_content +='	</r>\n';
        form_content +='	<r>\n';
        form_content +='		<c> <l>Email (required):</l><br>[email* email] </c>\n';
        form_content +='		<c> <l>Phone:</l><br>[text phone] </c>\n';
        form_content +='	</r>\n';
        form_content +='	<r>\n';
        form_content +='		<c> <l>Address (required):</l><br>[text* address] </c>\n';
        form_content +='		<c> <l>City (required):</l><br>[text* city] </c>\n';
        form_content +='	</r>\n';
        form_content +='	<r>\n';
        form_content +='		<c> <l>Post code (required):</l><br>[text* postcode] </c>\n';
        form_content +='		<c> <l>Country (required):</l><br>[country] </c>\n';
        form_content +='	</r>\n';
        form_content +='	<r>\n';
        form_content +='		<c> <l>Adults:</l><br>[select visitors "1" "2" "3" "4" "5"] </c>\n';
        form_content +='		<c> <l>Children:</l><br>[select children "0" "1" "2" "3"] </c>\n';
        form_content +='	</r>\n';
        form_content +='	<r>\n';
        form_content +='		<c> <l>Details:</l><spacer></spacer> \n';
        form_content +='			[textarea details] </c>\n';
        form_content +='	</r><br>\n';
        form_content +='	<p>[submit class:btn "Send"]</p>\n';
        form_content +='</div>';
    }

    //FixIn: 8.8.2.6
    if (form_type == 'fields3columns')  { // 3 columns form
        form_content = '';
        form_content +='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
        form_content +='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
        form_content +='<r>\n';
        form_content +='    <c> [calendar] </c>\n';
        form_content +='    <c>\n';
        form_content +='        <div class="wpbc__form__div">\n';
        form_content +='            <r>\n';
        form_content +='                <c> <l>First Name (required):</l><br>[text* name] </c>\n';
        form_content +='                <c> <l>Last Name (required):</l><br>[text* secondname] </c>\n';
        form_content +='            </r>\n';
        form_content +='            <r>\n';
        form_content +='                <c> <l>Email (required):</l><br>[email* email] </c>\n';
        form_content +='                <c> <l>Phone:</l><br>[text phone] </c>\n';
        form_content +='            </r>\n';
        form_content +='            <r>\n';
        form_content +='                <c> <l>Address (required):</l><br>[text* address] </c>\n';
        form_content +='                <c> <l>City (required):</l><br>[text* city] </c>\n';
        form_content +='            </r>\n';
        form_content +='            <r>\n';
        form_content +='                <c> <l>Post code (required):</l><br>[text* postcode] </c>\n';
        form_content +='                <c> <l>Country (required):</l><br>[country] </c>\n';
        form_content +='            </r>\n';
        form_content +='            <r>\n';
        form_content +='                <c> <l>Adults:</l><br>[select visitors "1" "2" "3" "4" "5"] </c>\n';
        form_content +='                <c> <l>Children:</l><br>[select children "0" "1" "2" "3"] </c>\n';
        form_content +='            </r>\n';
        form_content +='            <r>\n';
        form_content +='                <c> <l>Details:</l><spacer></spacer> \n';
        form_content +='                    [textarea details] </c>\n';
        form_content +='            </r>\n';
        form_content +='            <p>[submit class:btn "Send"]</p>\n';
        form_content +='        </div>\n';
        form_content +='    </c>\n';
        form_content +='</r>\n';
    }


    //FixIn: 8.7.11.14
    if (form_type == 'fields2columnstimes')  { // 2 columns form
        form_content  = '';
        form_content +='<div class="wpbc__form__div"> \n';
        form_content +='    <r> \n';
        form_content +='		<c> <l>Select Date:</l><br />[calendar] </c> \n';
        form_content +='		<c> <l>Select Times*:</l><br /> \n';
        form_content +='			[select* rangetime "10:00 AM - 12:00 PM@@10:00 - 12:00" "12:00 PM - 02:00 PM@@12:00 - 14:00" "02:00 PM - 04:00 PM@@14:00 - 16:00" "04:00 PM - 06:00 PM@@16:00 - 18:00" "06:00 PM - 08:00 PM@@18:00 - 20:00"] </c> \n';
        form_content +='	</r> \n';
        form_content +='	<r> \n';
        form_content +='		<c> <l>First Name (required):</l><br />[text* name] </c> \n';
        form_content +='		<c> <l>Last Name (required):</l><br />[text* secondname] </c> \n';
        form_content +='	</r> \n';
        form_content +='	<r> \n';
        form_content +='		<c> <l>Email (required):</l><br />[email* email] </c> \n';
        form_content +='		<c> <l>Phone:</l><br />[text phone] </c> \n';
        form_content +='	</r> \n';
        form_content +='	<r> \n';
        form_content +='		<c> <l>Adults:</l><br />[select visitors "1" "2" "3" "4" "5"] </c> \n';
        form_content +='		<c> <l>Children:</l><br />[select children "0" "1" "2" "3"] </c> \n';
        form_content +='	</r> \n';
        form_content +='	<r> \n';
        form_content +='		<c> <l>Details:</l><spacer></spacer> \n';
        form_content +='			[textarea details] </c> \n';
        form_content +='	</r><br /> \n';
        form_content +='	<p>[submit "Send"]</p>\n';
        form_content +='</div>\n';
    }

    if (form_content == '') { // Default Form.
           form_content = '';
           form_content +='[calendar] \n'; 
           form_content +='<div class="standard-form"> \n';
           form_content +='     <p>First Name (required):<br />[text* name] </p> \n';
           form_content +='     <p>Last Name (required):<br />[text* secondname] </p> \n';
           form_content +='     <p>Email (required):<br />[email* email] </p>   \n';
           form_content +='     <p>Phone:<br />[text phone] </p> \n';
           form_content +='     <p>Adults:<br />[select visitors "1" "2" "3" "4"]</p> \n';
           form_content +='     <p>Children:<br />[select children "0" "1" "2" "3"]</p> \n';
           form_content +='     <p>Details:<br /> [textarea details] </p> \n';
           form_content +='     <p>[checkbox* term_and_condition use_label_element "I Accept term and conditions"] </p>\n';
           form_content +='     <p>[captcha]</p> \n';
           form_content +='     <p>[submit class:btn "Send"]</p> \n';
           form_content +='</div>';
    }
    
    return form_content;
}

function wpbc_reset__get_form_data( form_type ){
    var form_content = '';
        
    if ( (form_type == 'payment')  || (form_type == 'paymentUS') || ( 'fields2columns' == form_type ) || ( 'fields3columns' == form_type ) ) {               //FixIn: 8.7.7.15      //FixIn: 8.8.2.6
        form_content = '';
        form_content +='<div class="standard-content-form"> \n';
        form_content += '    <b>First Name</b>: <f>[name]</f><br>\n';
        form_content += '    <b>Last Name</b>:  <f>[secondname]</f><br>\n';
        form_content += '    <b>Email</b>:      <f>[email]</f><br>\n';
        form_content += '    <b>Phone</b>:      <f>[phone]</f><br>\n';
        form_content += '    <b>Address</b>:    <f>[address]</f><br>\n';
        form_content += '    <b>City</b>:       <f>[city]</f><br>\n';
        form_content += '    <b>Post code</b>:  <f>[postcode]</f><br>\n';
        form_content += '    <b>Country</b>:    <f>[country]</f><br>\n';
        if ( form_type == 'paymentUS' ) {
        form_content += '    <b>State</b>:      <f>[state]</f><br>\n';
        }
        form_content += '    <b>Adults</b>:     <f>[visitors]</f><br>\n';
        form_content += '    <b>Children</b>:   <f>[children]</f><br>\n';
        form_content += '    <b>Details</b>:    <f>[details]</f>\n';
        form_content += '</div>';
    }

    if ( (form_type == 'times') || (form_type == 'times30')  || (form_type == 'times15')  || ( form_type == 'timesweek') || ( 'fields2columnstimes' == form_type ) ){      //FixIn: 7.1.2.6       //FixIn: 8.7.11.14
        form_content = '';
        form_content +='<div class="standard-content-form"> \n';
        form_content += '    <b>Times</b>:      <f>[rangetime]</f><br>\n';
        form_content += '    <b>First Name</b>: <f>[name]</f><br>\n';
        form_content += '    <b>Last Name</b>:  <f>[secondname]</f><br>\n';
        form_content += '    <b>Email</b>:      <f>[email]</f><br>\n';
        form_content += '    <b>Phone</b>:      <f>[phone]</f><br>\n';
        form_content += '    <b>Adults</b>:     <f>[visitors]</f><br>\n';
        form_content += '    <b>Children</b>:   <f>[children]</f><br>\n';
        form_content += '    <b>Details</b>:    <f>[details]</f>\n';
        form_content +='</div>';
    }

    //FixIn: 8.7.3.5
    if ( 'hints-dev' == form_type ){
        form_content = '';
        form_content +='<div class="standard-content-form"> \n';
        form_content += '    <b>First Name</b>: <f>[name]</f><br>\n';
        form_content += '    <b>Last Name</b>:  <f>[secondname]</f><br>\n';
        form_content += '    <b>Email</b>:      <f>[email]</f><br>\n';
        form_content += '    <b>Adults</b>:     <f>[visitors]</f><br>\n';
        form_content += '    <b>Children</b>:   <f>[children]</f><br>\n';
        form_content += '    <b>Details</b>:    <f>[details]</f>\n';
        form_content +='</div>';
    }

    if (  (form_type == 'wizard') || (form_type == '2collumns') || (form_content == 'hints') || (form_content == '') ){
        form_content = '';
        form_content +='<div class="standard-content-form"> \n';
        form_content += '    <b>First Name</b>: <f>[name]</f><br>\n';
        form_content += '    <b>Last Name</b>:  <f>[secondname]</f><br>\n';
        form_content += '    <b>Email</b>:      <f>[email]</f><br>\n';
        form_content += '    <b>Phone</b>:      <f>[phone]</f><br>\n';
        form_content += '    <b>Adults</b>:     <f>[visitors]</f><br>\n';
        form_content += '    <b>Children</b>:   <f>[children]</f><br>\n';
        form_content += '    <b>Details</b>:    <f>[details]</f>\n';
        form_content +='</div>';
    }
    return form_content;
}
