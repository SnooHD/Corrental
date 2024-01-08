/**
 * Convert seconds to  24 hour format   3600 -> '10:00'
 * @param time_in_seconds
 * @returns {string}
 */
function wpbc_js_convert__seconds__to_time_24( time_in_seconds ){

    var hours   = Math.floor( (   (time_in_seconds % 31536000) % 86400) / 3600 );
    if ( 86400 == time_in_seconds ){
        hours = 24;
    }
    var minutes = Math.floor( ( ( (time_in_seconds % 31536000) % 86400) % 3600 ) / 60 );

    if ( hours < 10 ){
        hours = '0' + hours.toString();
    }
    if ( minutes < 10 ){
        minutes = '0' + minutes.toString();
    }

    return hours + ':' + minutes;
}


/**
 * Convert seconds to  AM / PM time format   3600 -> '10:00 AM'
 *
 * @param time_in_seconds
 * @returns {string}
 */
function wpbc_js_convert__seconds__to_time_AMPM( time_in_seconds ){

    var hours   = Math.floor( (   (time_in_seconds % 31536000) % 86400) / 3600 );
    if ( 86400 == time_in_seconds ){
        hours = 24;
    }
    var minutes = Math.floor( ( ( (time_in_seconds % 31536000) % 86400) % 3600 ) / 60 );

    // American Heritage Dictionary of the English Language states "By convention, 12 AM denotes midnight and 12 PM denotes NOON    -  '12:00 MIDNIGHT' for 00:00 and  - '12:00 NOON' for '12:00'
    var am_pm = (parseInt( hours ) > 12) ? 'PM' : 'AM';
    am_pm = (12 == hours) ? 'PM' : am_pm;
    am_pm = (24 == hours) ? 'AM' : am_pm;

    if ( hours > 12 ){
        hours = hours - 12;
    }
    // if ( hours < 10 ){
    //     hours = '0' + hours.toString();
    // }

    if ( minutes < 10 ){
        minutes = '0' + minutes.toString();
    }

    return hours + ':' + minutes + ' ' + am_pm;
}


/**
 * Convert Time slot from  seconds to Readable Time Format:  24 | AM/PM         [ 0, 13*60*60]  ->   '00:00 AM - 01:00 PM'    |       '00:00 - 13:00'
 *
 * @param resource_id                   int ID of resource
 * @param timeslot_in_seconds_arr       [ 0, 13*60*60]
 * @returns {string}                    '00:00 AM - 01:00 PM'    |       '00:00 - 13:00'
 */
function wpbc_js_convert__seconds__to__readable_time( resource_id, timeslot_in_seconds_arr ){

    var readable_time_format;
    var is_use_24;
    if (
           ( _wpbc.calendar__get_param_value( resource_id, 'booking_time_format' ).indexOf( 'A' ) > 0 )
        || ( _wpbc.calendar__get_param_value( resource_id, 'booking_time_format' ).indexOf( 'a' ) > 0 )

    ) {
        is_use_24 = false;
    } else {
        is_use_24 = true;
    }

    if ( is_use_24 ){
        readable_time_format = wpbc_js_convert__seconds__to_time_24( timeslot_in_seconds_arr[ 0 ] )
                            + ' - '
                            + wpbc_js_convert__seconds__to_time_24( timeslot_in_seconds_arr[ 1 ] );
    } else {
        readable_time_format = wpbc_js_convert__seconds__to_time_AMPM( timeslot_in_seconds_arr[ 0 ] )
                            + ' - '
                            + wpbc_js_convert__seconds__to_time_AMPM( timeslot_in_seconds_arr[ 1 ] );
    }

    return readable_time_format;
}



// =====================================================================================================================
// [capacity_hint]
// =====================================================================================================================

    /**
     *  Convert times seconds arr [ 21600, 23400 ] to redable obj  {}
     *
     * @param times_as_seconds_arr      [ 21600, 23400 ]
     *
     * @returns {{value_option_24h: string[], times_as_seconds_arr, readable_time: string}}
     */
    function wpbc_convert_seconds_arr__to_readable_obj( resource_id, times_as_seconds_arr ){


        var readable_time_format = wpbc_js_convert__seconds__to__readable_time( resource_id, times_as_seconds_arr );

        var obj = {
            'times_as_seconds': wpbc_clone_obj( times_as_seconds_arr ),
            'value_option_24h': [
                                    wpbc_js_convert__seconds__to_time_24( times_as_seconds_arr[ 0 ] ),
                                    wpbc_js_convert__seconds__to_time_24( times_as_seconds_arr[ 1 ] )
                                ],
            'readable_time'   : readable_time_format
        };
        return obj;
    }


    function wpbc_get_start_end_times_sec_arr__for_all_rangetime_slots_in_booking_form( resource_id ){

        // [ {jquery_option: {}, name: "rangetime2", times_as_seconds:[ 36000, 43200 ], value_option_24h: "10:00 - 12:00"} , ... ]
        var is_only_selected_time = false;
        var all_time_fields = wpbc_get__selected_time_fields__in_booking_form__as_arr( resource_id , is_only_selected_time );

        var time_as_seconds_arr = [];

        for ( var t_key in all_time_fields ){

            if ( all_time_fields[ t_key ][ 'name' ].indexOf( 'rangetime' ) > -1 ){

                time_as_seconds_arr.push(
                                            wpbc_convert_seconds_arr__to_readable_obj(  resource_id,
                                                                all_time_fields[ t_key ].times_as_seconds               // { times_as_seconds: [ 21600, 23400 ], value_option_24h: '06:00 - 06:30', name: 'rangetime2[]', jquery_option: jQuery_Object {}}
                                                        )
                                        );
            }
        }

        return time_as_seconds_arr;
    }


    /**
     * Get array  of available items for each  seelcted date and time slot in booking form
     *
     * @param int resource_id
     * @returns [
     *
     *              "2024-05-17": [
     *                              0_86400    : Object { available_items: 4, value_option_24h: "00:00 - 24:00", date_sql_key: "2024-05-17", … }
     *                              36000_43200: Object { available_items: 4, value_option_24h: "10:00 - 12:00", date_sql_key: "2024-05-17", … }
     *                              43200_50400: Object { available_items: 4, value_option_24h: "12:00 - 14:00", date_sql_key: "2024-05-17", … }
     *                              50400_57600: Object { available_items: 4, value_option_24h: "14:00 - 16:00", date_sql_key: "2024-05-17", … }
     *                              57600_64800: Object { available_items: 4, value_option_24h: "16:00 - 18:00", date_sql_key: "2024-05-17", … }
     *                              64800_72000: Object { available_items: 4, value_option_24h: "18:00 - 20:00", date_sql_key: "2024-05-17", … }
     *                            ]
     *              "2024-05-19": [
     *                              0_86400    : Object { available_items: 4, value_option_24h: "00:00 - 24:00", date_sql_key: "2024-05-19", … }
     *                              36000_43200: Object { available_items: 4, value_option_24h: "10:00 - 12:00", date_sql_key: "2024-05-19", … }
     *                              43200_50400: Object { available_items: 4, value_option_24h: "12:00 - 14:00", date_sql_key: "2024-05-19", … }
     *                              50400_57600: Object { available_items: 4, value_option_24h: "14:00 - 16:00", date_sql_key: "2024-05-19", … }
     *                              57600_64800: Object { available_items: 4, value_option_24h: "16:00 - 18:00", date_sql_key: "2024-05-19", … }
     *                              64800_72000: Object { availa...
     *                            ]
     *          ]
     */
    function wpbc_get__available_items_for_selected_datetime( resource_id ){

         var selected_time_fields = [];

        // -------------------------------------------------------------------------------------------------------------
        // This is current selected / entered  ONE time slot  (if not entred time,  then  full date)
        // -------------------------------------------------------------------------------------------------------------
        // [ 0 , 24 * 60 * 60 ]  |  [ 12*60*60 , 14*60*60 ]    This is selected,  entered times. So  we will  show available slots only  for selected times
        var time_to_book__as_seconds_arr = wpbc_get_start_end_times__in_booking_form__as_seconds( resource_id );
                                                                                // [ 12*60*60 , 14*60*60 ]
        selected_time_fields.push(  wpbc_convert_seconds_arr__to_readable_obj( resource_id, time_to_book__as_seconds_arr ) );

        // -------------------------------------------------------------------------------------------------------------
        // This is all  time-slots from  range-time,  if any
        var all_rangetime_slots_arr = wpbc_get_start_end_times_sec_arr__for_all_rangetime_slots_in_booking_form( resource_id );
        // -------------------------------------------------------------------------------------------------------------


        var work_times_array = (all_rangetime_slots_arr.length > 0)
                                    ? wpbc_clone_obj( all_rangetime_slots_arr )
                                    : wpbc_clone_obj( selected_time_fields );

        var capacity_dates_times = [];

        for ( var obj_key in work_times_array ){

            // Object { name: "rangetime2", value_option_24h: "10:00 - 12:00", jquery_option: {…}, name: "rangetime2", times_as_seconds: Array [ 36000, 43200 ], value_option_24h: "10:00 - 12:00" }
            var one_times_readable_obj = work_times_array[ obj_key ];

            // '43200_50400'
            var time_key = '' + one_times_readable_obj[ 'times_as_seconds' ][ 0 ] + '_' + one_times_readable_obj[ 'times_as_seconds' ][ 1 ];


            /**
             *  [   "2024-05-16": [  0: Object { resource_id: 2,  is_available: true, booked__seconds: [], … }
             *                       1: Object { resource_id: 10, is_available: true, booked__seconds: [], … }
             *                       2: Object { resource_id: 11, is_available: true, booked__seconds: [], … }
             *   ]
             */
            var available_slots_by_dates = wpbc__get_available_slots__for_selected_dates_times__bl( resource_id, wpbc_clone_obj( one_times_readable_obj[ 'times_as_seconds' ] ) );
//console.log( 'available_slots_by_dates==',available_slots_by_dates);

            // Loop Dates
            for ( var date_sql_key in available_slots_by_dates ){

                var available_slots_in_one_date = available_slots_by_dates[ date_sql_key ];

                var count_available_slots = 0

                var time2book_in_sec_per_each_date = wpbc_clone_obj( one_times_readable_obj[ 'times_as_seconds' ] );

                // Loop Available Slots in Date
                for ( var i = 0; i < available_slots_in_one_date.length; i++ ){
                    if ( available_slots_in_one_date[ i ][ 'is_available' ] ){
                        count_available_slots++;
                    }

                    // Ovveride that  time by  times,  that  can  be different for several  dates,  if deactivated this option: 'Use selected times for each booking date'
                    // For example if slecte time 10:00 - 11:00 and selected 3 dates, then  booked times here will be  10:00 - 24:00,   00:00 - 24:00,   00:00 - 11:00
                    time2book_in_sec_per_each_date = wpbc_clone_obj( available_slots_in_one_date[ i ]['time_to_book__seconds'] );
                }

                // Save info
                if ( 'undefined' === typeof (capacity_dates_times[ date_sql_key ]) ){
                    capacity_dates_times[ date_sql_key ] = [];
                }

                var css_class = '';
                if ( selected_time_fields.length > 0 ){
                    if (   (selected_time_fields[ 0 ][ 'times_as_seconds' ][ 0 ] == time2book_in_sec_per_each_date[ 0 ])
                        && (selected_time_fields[ 0 ][ 'times_as_seconds' ][ 1 ] == time2book_in_sec_per_each_date[ 1 ]) ){
                        css_class += ' wpbc_selected_timeslot'
                    }
                }

                // -----------------------------------------------------------------------------------------------------
                // Readable Time Format:  24 | AM/PM
                // -----------------------------------------------------------------------------------------------------
                var readable_time_format = wpbc_js_convert__seconds__to__readable_time( resource_id, time2book_in_sec_per_each_date )

                capacity_dates_times[ date_sql_key ][ time_key ] = {
                                                                    // 'value_option_24h':one_times_readable_obj[ 'value_option_24h' ],
                                                                    'available_items': count_available_slots,
                                                                    'times_as_seconds': time2book_in_sec_per_each_date,
                                                                    'date_sql_key'    : date_sql_key,
                                                                    'readable_time'   : readable_time_format,
                                                                    'css_class'       : css_class
                                                                };
            }
        }

        return capacity_dates_times;

    }



// ---------------------------------------------------------------------------------------------------------------------
// Template for shortcode hint
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Update time hint shortcode content in booking form
 *
 * @param resource_id
 */
function wpbc_update_capacity_hint( resource_id ){

     /**
     *  [          "2024-05-17": [
     *                              0_86400    : Object { available_items: 4, value_option_24h: "00:00 - 24:00", date_sql_key: "2024-05-17", … }
     *                              36000_43200: Object { available_items: 4, value_option_24h: "10:00 - 12:00", date_sql_key: "2024-05-17", … }
     *                              43200_50400: Object { available_items: 4, value_option_24h: "12:00 - 14:00", date_sql_key: "2024-05-17", … }
     *                              50400_57600: Object { available_items: 4, value_option_24h: "14:00 - 16:00", date_sql_key: "2024-05-17", … }
     *                              57600_64800: Object { available_items: 4, value_option_24h: "16:00 - 18:00", date_sql_key: "2024-05-17", … }
     *                              64800_72000: Object { available_items: 4, value_option_24h: "18:00 - 20:00", date_sql_key: "2024-05-17", … }
     *                            ]
     *              "2024-05-19": [
     *                              0_86400    : Object { available_items: 4, value_option_24h: "00:00 - 24:00", date_sql_key: "2024-05-19", … }
     *                              36000_43200: Object { available_items: 4, value_option_24h: "10:00 - 12:00", date_sql_key: "2024-05-19", … }
     *                              43200_50400: Object { available_items: 4, value_option_24h: "12:00 - 14:00", date_sql_key: "2024-05-19", … }
     *                              50400_57600: Object { available_items: 4, value_option_24h: "14:00 - 16:00", date_sql_key: "2024-05-19", … }
     *                              57600_64800: Object { available_items: 4, value_option_24h: "16:00 - 18:00", date_sql_key: "2024-05-19", … }
     *                              64800_72000: Object { availa...
     *                            ]
     *          ]
     */
    var available_items_arr = wpbc_get__available_items_for_selected_datetime( resource_id );

    var is_full_day_booking = true;
    for ( var obj_date_tag in available_items_arr ){

        if ( Object.keys( available_items_arr[ obj_date_tag ] ).length > 1 ){
            is_full_day_booking = false;
            break;
        }
        for ( var time_key in available_items_arr[ obj_date_tag ] ){
            if ( (available_items_arr[ obj_date_tag ][ time_key ][ 'times_as_seconds' ][ 0 ] > 0) && (available_items_arr[ obj_date_tag ][ time_key ][ 'times_as_seconds' ][ 1 ] < 86400) ){
                is_full_day_booking = false;
                break;
            }
        }
        if ( !is_full_day_booking ){
            break;
        }
    }
    var css_is_full_day_booking = (is_full_day_booking) ? ' wpbc_chint__full_day_bookings' : '';

    var tooltip_hint = '<div class="wpbc_capacity_hint_container' + css_is_full_day_booking + '">';

    for ( var obj_date_tag in available_items_arr ){

        var timeslots_in_day = available_items_arr[ obj_date_tag ]

        tooltip_hint += '<div class="wpbc_chint__datetime_container">';

        // JSON.stringify(available_items_arr).match(/[^\\]":/g).length
        if ( (Object.keys( available_items_arr ).length > 1) || (is_full_day_booking) ){
            tooltip_hint += '<div class="wpbc_chint__date_container">';
                tooltip_hint += '<div class="wpbc_chint__date">' + obj_date_tag + '</div> ';
                tooltip_hint += '<div class="wpbc_chint__date_divider">:</div> ';
            tooltip_hint += '</div> ';
        }

        for ( var time_key in timeslots_in_day ){
                tooltip_hint += '<div class="wpbc_chint__time_container">';

                // If not full day booking: e.g  00:00 - 24:00
                //if ( (timeslots_in_day[ time_key ][ 'times_as_seconds' ][ 0 ] > 0) && (timeslots_in_day[ time_key ][ 'times_as_seconds' ][ 1 ] < 86400) ){

                     tooltip_hint += '<div class="wpbc_chint__timeslot ' + timeslots_in_day[ time_key ][ 'css_class' ] + '">'
                                        + timeslots_in_day[ time_key ][ 'readable_time' ]
                                   + '</div> ';
                    tooltip_hint += '<div class="wpbc_chint__timeslot_divider">: </div> ';
                //}

                    tooltip_hint += '<div class="wpbc_chint__availability availability_num_' + timeslots_in_day[ time_key ][ 'available_items' ] + '">'
                                        + timeslots_in_day[ time_key ][ 'available_items' ]
                                  + '</div> ';
                tooltip_hint += '</div> ';
        }
        tooltip_hint += '</div> ';
    }

    tooltip_hint += '</div> ';


//console.log( ':: available_items_arr ::', available_items_arr );


    jQuery( '.capacity_hint_' + resource_id ).html( tooltip_hint );

    jQuery( '.capacity_hint_' + resource_id ).removeClass( 'wpbc_chin_newline' );
    if ( Object.keys( available_items_arr ).length > 1 ){
        jQuery( '.capacity_hint_' + resource_id ).addClass( 'wpbc_chin_newline' );
    }
}


    // Run shortcode changing after  dates selection,  and options selection.
    jQuery( document ).ready( function (){
        jQuery( '.booking_form_div' ).on( 'wpbc_booking_date_or_option_selected', function ( event, resource_id ){
            wpbc_update_capacity_hint( resource_id );
        } );

    } );
