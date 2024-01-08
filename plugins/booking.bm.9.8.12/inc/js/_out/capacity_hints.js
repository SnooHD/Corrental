"use strict";

/**
 * Convert seconds to  24 hour format   3600 -> '10:00'
 * @param time_in_seconds
 * @returns {string}
 */
function wpbc_js_convert__seconds__to_time_24(time_in_seconds) {
  var hours = Math.floor(time_in_seconds % 31536000 % 86400 / 3600);

  if (86400 == time_in_seconds) {
    hours = 24;
  }

  var minutes = Math.floor(time_in_seconds % 31536000 % 86400 % 3600 / 60);

  if (hours < 10) {
    hours = '0' + hours.toString();
  }

  if (minutes < 10) {
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


function wpbc_js_convert__seconds__to_time_AMPM(time_in_seconds) {
  var hours = Math.floor(time_in_seconds % 31536000 % 86400 / 3600);

  if (86400 == time_in_seconds) {
    hours = 24;
  }

  var minutes = Math.floor(time_in_seconds % 31536000 % 86400 % 3600 / 60); // American Heritage Dictionary of the English Language states "By convention, 12 AM denotes midnight and 12 PM denotes NOON    -  '12:00 MIDNIGHT' for 00:00 and  - '12:00 NOON' for '12:00'

  var am_pm = parseInt(hours) > 12 ? 'PM' : 'AM';
  am_pm = 12 == hours ? 'PM' : am_pm;
  am_pm = 24 == hours ? 'AM' : am_pm;

  if (hours > 12) {
    hours = hours - 12;
  } // if ( hours < 10 ){
  //     hours = '0' + hours.toString();
  // }


  if (minutes < 10) {
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


function wpbc_js_convert__seconds__to__readable_time(resource_id, timeslot_in_seconds_arr) {
  var readable_time_format;
  var is_use_24;

  if (_wpbc.calendar__get_param_value(resource_id, 'booking_time_format').indexOf('A') > 0 || _wpbc.calendar__get_param_value(resource_id, 'booking_time_format').indexOf('a') > 0) {
    is_use_24 = false;
  } else {
    is_use_24 = true;
  }

  if (is_use_24) {
    readable_time_format = wpbc_js_convert__seconds__to_time_24(timeslot_in_seconds_arr[0]) + ' - ' + wpbc_js_convert__seconds__to_time_24(timeslot_in_seconds_arr[1]);
  } else {
    readable_time_format = wpbc_js_convert__seconds__to_time_AMPM(timeslot_in_seconds_arr[0]) + ' - ' + wpbc_js_convert__seconds__to_time_AMPM(timeslot_in_seconds_arr[1]);
  }

  return readable_time_format;
} // =====================================================================================================================
// [capacity_hint]
// =====================================================================================================================

/**
 *  Convert times seconds arr [ 21600, 23400 ] to redable obj  {}
 *
 * @param times_as_seconds_arr      [ 21600, 23400 ]
 *
 * @returns {{value_option_24h: string[], times_as_seconds_arr, readable_time: string}}
 */


function wpbc_convert_seconds_arr__to_readable_obj(resource_id, times_as_seconds_arr) {
  var readable_time_format = wpbc_js_convert__seconds__to__readable_time(resource_id, times_as_seconds_arr);
  var obj = {
    'times_as_seconds': wpbc_clone_obj(times_as_seconds_arr),
    'value_option_24h': [wpbc_js_convert__seconds__to_time_24(times_as_seconds_arr[0]), wpbc_js_convert__seconds__to_time_24(times_as_seconds_arr[1])],
    'readable_time': readable_time_format
  };
  return obj;
}

function wpbc_get_start_end_times_sec_arr__for_all_rangetime_slots_in_booking_form(resource_id) {
  // [ {jquery_option: {}, name: "rangetime2", times_as_seconds:[ 36000, 43200 ], value_option_24h: "10:00 - 12:00"} , ... ]
  var is_only_selected_time = false;
  var all_time_fields = wpbc_get__selected_time_fields__in_booking_form__as_arr(resource_id, is_only_selected_time);
  var time_as_seconds_arr = [];

  for (var t_key in all_time_fields) {
    if (all_time_fields[t_key]['name'].indexOf('rangetime') > -1) {
      time_as_seconds_arr.push(wpbc_convert_seconds_arr__to_readable_obj(resource_id, all_time_fields[t_key].times_as_seconds // { times_as_seconds: [ 21600, 23400 ], value_option_24h: '06:00 - 06:30', name: 'rangetime2[]', jquery_option: jQuery_Object {}}
      ));
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


function wpbc_get__available_items_for_selected_datetime(resource_id) {
  var selected_time_fields = []; // -------------------------------------------------------------------------------------------------------------
  // This is current selected / entered  ONE time slot  (if not entred time,  then  full date)
  // -------------------------------------------------------------------------------------------------------------
  // [ 0 , 24 * 60 * 60 ]  |  [ 12*60*60 , 14*60*60 ]    This is selected,  entered times. So  we will  show available slots only  for selected times

  var time_to_book__as_seconds_arr = wpbc_get_start_end_times__in_booking_form__as_seconds(resource_id); // [ 12*60*60 , 14*60*60 ]

  selected_time_fields.push(wpbc_convert_seconds_arr__to_readable_obj(resource_id, time_to_book__as_seconds_arr)); // -------------------------------------------------------------------------------------------------------------
  // This is all  time-slots from  range-time,  if any

  var all_rangetime_slots_arr = wpbc_get_start_end_times_sec_arr__for_all_rangetime_slots_in_booking_form(resource_id); // -------------------------------------------------------------------------------------------------------------

  var work_times_array = all_rangetime_slots_arr.length > 0 ? wpbc_clone_obj(all_rangetime_slots_arr) : wpbc_clone_obj(selected_time_fields);
  var capacity_dates_times = [];

  for (var obj_key in work_times_array) {
    // Object { name: "rangetime2", value_option_24h: "10:00 - 12:00", jquery_option: {…}, name: "rangetime2", times_as_seconds: Array [ 36000, 43200 ], value_option_24h: "10:00 - 12:00" }
    var one_times_readable_obj = work_times_array[obj_key]; // '43200_50400'

    var time_key = '' + one_times_readable_obj['times_as_seconds'][0] + '_' + one_times_readable_obj['times_as_seconds'][1];
    /**
     *  [   "2024-05-16": [  0: Object { resource_id: 2,  is_available: true, booked__seconds: [], … }
     *                       1: Object { resource_id: 10, is_available: true, booked__seconds: [], … }
     *                       2: Object { resource_id: 11, is_available: true, booked__seconds: [], … }
     *   ]
     */

    var available_slots_by_dates = wpbc__get_available_slots__for_selected_dates_times__bl(resource_id, wpbc_clone_obj(one_times_readable_obj['times_as_seconds'])); //console.log( 'available_slots_by_dates==',available_slots_by_dates);
    // Loop Dates

    for (var date_sql_key in available_slots_by_dates) {
      var available_slots_in_one_date = available_slots_by_dates[date_sql_key];
      var count_available_slots = 0;
      var time2book_in_sec_per_each_date = wpbc_clone_obj(one_times_readable_obj['times_as_seconds']); // Loop Available Slots in Date

      for (var i = 0; i < available_slots_in_one_date.length; i++) {
        if (available_slots_in_one_date[i]['is_available']) {
          count_available_slots++;
        } // Ovveride that  time by  times,  that  can  be different for several  dates,  if deactivated this option: 'Use selected times for each booking date'
        // For example if slecte time 10:00 - 11:00 and selected 3 dates, then  booked times here will be  10:00 - 24:00,   00:00 - 24:00,   00:00 - 11:00


        time2book_in_sec_per_each_date = wpbc_clone_obj(available_slots_in_one_date[i]['time_to_book__seconds']);
      } // Save info


      if ('undefined' === typeof capacity_dates_times[date_sql_key]) {
        capacity_dates_times[date_sql_key] = [];
      }

      var css_class = '';

      if (selected_time_fields.length > 0) {
        if (selected_time_fields[0]['times_as_seconds'][0] == time2book_in_sec_per_each_date[0] && selected_time_fields[0]['times_as_seconds'][1] == time2book_in_sec_per_each_date[1]) {
          css_class += ' wpbc_selected_timeslot';
        }
      } // -----------------------------------------------------------------------------------------------------
      // Readable Time Format:  24 | AM/PM
      // -----------------------------------------------------------------------------------------------------


      var readable_time_format = wpbc_js_convert__seconds__to__readable_time(resource_id, time2book_in_sec_per_each_date);
      capacity_dates_times[date_sql_key][time_key] = {
        // 'value_option_24h':one_times_readable_obj[ 'value_option_24h' ],
        'available_items': count_available_slots,
        'times_as_seconds': time2book_in_sec_per_each_date,
        'date_sql_key': date_sql_key,
        'readable_time': readable_time_format,
        'css_class': css_class
      };
    }
  }

  return capacity_dates_times;
} // ---------------------------------------------------------------------------------------------------------------------
// Template for shortcode hint
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Update time hint shortcode content in booking form
 *
 * @param resource_id
 */


function wpbc_update_capacity_hint(resource_id) {
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
  var available_items_arr = wpbc_get__available_items_for_selected_datetime(resource_id);
  var is_full_day_booking = true;

  for (var obj_date_tag in available_items_arr) {
    if (Object.keys(available_items_arr[obj_date_tag]).length > 1) {
      is_full_day_booking = false;
      break;
    }

    for (var time_key in available_items_arr[obj_date_tag]) {
      if (available_items_arr[obj_date_tag][time_key]['times_as_seconds'][0] > 0 && available_items_arr[obj_date_tag][time_key]['times_as_seconds'][1] < 86400) {
        is_full_day_booking = false;
        break;
      }
    }

    if (!is_full_day_booking) {
      break;
    }
  }

  var css_is_full_day_booking = is_full_day_booking ? ' wpbc_chint__full_day_bookings' : '';
  var tooltip_hint = '<div class="wpbc_capacity_hint_container' + css_is_full_day_booking + '">';

  for (var obj_date_tag in available_items_arr) {
    var timeslots_in_day = available_items_arr[obj_date_tag];
    tooltip_hint += '<div class="wpbc_chint__datetime_container">'; // JSON.stringify(available_items_arr).match(/[^\\]":/g).length

    if (Object.keys(available_items_arr).length > 1 || is_full_day_booking) {
      tooltip_hint += '<div class="wpbc_chint__date_container">';
      tooltip_hint += '<div class="wpbc_chint__date">' + obj_date_tag + '</div> ';
      tooltip_hint += '<div class="wpbc_chint__date_divider">:</div> ';
      tooltip_hint += '</div> ';
    }

    for (var time_key in timeslots_in_day) {
      tooltip_hint += '<div class="wpbc_chint__time_container">'; // If not full day booking: e.g  00:00 - 24:00
      //if ( (timeslots_in_day[ time_key ][ 'times_as_seconds' ][ 0 ] > 0) && (timeslots_in_day[ time_key ][ 'times_as_seconds' ][ 1 ] < 86400) ){

      tooltip_hint += '<div class="wpbc_chint__timeslot ' + timeslots_in_day[time_key]['css_class'] + '">' + timeslots_in_day[time_key]['readable_time'] + '</div> ';
      tooltip_hint += '<div class="wpbc_chint__timeslot_divider">: </div> '; //}

      tooltip_hint += '<div class="wpbc_chint__availability availability_num_' + timeslots_in_day[time_key]['available_items'] + '">' + timeslots_in_day[time_key]['available_items'] + '</div> ';
      tooltip_hint += '</div> ';
    }

    tooltip_hint += '</div> ';
  }

  tooltip_hint += '</div> '; //console.log( ':: available_items_arr ::', available_items_arr );

  jQuery('.capacity_hint_' + resource_id).html(tooltip_hint);
  jQuery('.capacity_hint_' + resource_id).removeClass('wpbc_chin_newline');

  if (Object.keys(available_items_arr).length > 1) {
    jQuery('.capacity_hint_' + resource_id).addClass('wpbc_chin_newline');
  }
} // Run shortcode changing after  dates selection,  and options selection.


jQuery(document).ready(function () {
  jQuery('.booking_form_div').on('wpbc_booking_date_or_option_selected', function (event, resource_id) {
    wpbc_update_capacity_hint(resource_id);
  });
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImluYy9qcy9fc3JjL2NhcGFjaXR5X2hpbnRzLmpzIl0sIm5hbWVzIjpbIndwYmNfanNfY29udmVydF9fc2Vjb25kc19fdG9fdGltZV8yNCIsInRpbWVfaW5fc2Vjb25kcyIsImhvdXJzIiwiTWF0aCIsImZsb29yIiwibWludXRlcyIsInRvU3RyaW5nIiwid3BiY19qc19jb252ZXJ0X19zZWNvbmRzX190b190aW1lX0FNUE0iLCJhbV9wbSIsInBhcnNlSW50Iiwid3BiY19qc19jb252ZXJ0X19zZWNvbmRzX190b19fcmVhZGFibGVfdGltZSIsInJlc291cmNlX2lkIiwidGltZXNsb3RfaW5fc2Vjb25kc19hcnIiLCJyZWFkYWJsZV90aW1lX2Zvcm1hdCIsImlzX3VzZV8yNCIsIl93cGJjIiwiY2FsZW5kYXJfX2dldF9wYXJhbV92YWx1ZSIsImluZGV4T2YiLCJ3cGJjX2NvbnZlcnRfc2Vjb25kc19hcnJfX3RvX3JlYWRhYmxlX29iaiIsInRpbWVzX2FzX3NlY29uZHNfYXJyIiwib2JqIiwid3BiY19jbG9uZV9vYmoiLCJ3cGJjX2dldF9zdGFydF9lbmRfdGltZXNfc2VjX2Fycl9fZm9yX2FsbF9yYW5nZXRpbWVfc2xvdHNfaW5fYm9va2luZ19mb3JtIiwiaXNfb25seV9zZWxlY3RlZF90aW1lIiwiYWxsX3RpbWVfZmllbGRzIiwid3BiY19nZXRfX3NlbGVjdGVkX3RpbWVfZmllbGRzX19pbl9ib29raW5nX2Zvcm1fX2FzX2FyciIsInRpbWVfYXNfc2Vjb25kc19hcnIiLCJ0X2tleSIsInB1c2giLCJ0aW1lc19hc19zZWNvbmRzIiwid3BiY19nZXRfX2F2YWlsYWJsZV9pdGVtc19mb3Jfc2VsZWN0ZWRfZGF0ZXRpbWUiLCJzZWxlY3RlZF90aW1lX2ZpZWxkcyIsInRpbWVfdG9fYm9va19fYXNfc2Vjb25kc19hcnIiLCJ3cGJjX2dldF9zdGFydF9lbmRfdGltZXNfX2luX2Jvb2tpbmdfZm9ybV9fYXNfc2Vjb25kcyIsImFsbF9yYW5nZXRpbWVfc2xvdHNfYXJyIiwid29ya190aW1lc19hcnJheSIsImxlbmd0aCIsImNhcGFjaXR5X2RhdGVzX3RpbWVzIiwib2JqX2tleSIsIm9uZV90aW1lc19yZWFkYWJsZV9vYmoiLCJ0aW1lX2tleSIsImF2YWlsYWJsZV9zbG90c19ieV9kYXRlcyIsIndwYmNfX2dldF9hdmFpbGFibGVfc2xvdHNfX2Zvcl9zZWxlY3RlZF9kYXRlc190aW1lc19fYmwiLCJkYXRlX3NxbF9rZXkiLCJhdmFpbGFibGVfc2xvdHNfaW5fb25lX2RhdGUiLCJjb3VudF9hdmFpbGFibGVfc2xvdHMiLCJ0aW1lMmJvb2tfaW5fc2VjX3Blcl9lYWNoX2RhdGUiLCJpIiwiY3NzX2NsYXNzIiwid3BiY191cGRhdGVfY2FwYWNpdHlfaGludCIsImF2YWlsYWJsZV9pdGVtc19hcnIiLCJpc19mdWxsX2RheV9ib29raW5nIiwib2JqX2RhdGVfdGFnIiwiT2JqZWN0Iiwia2V5cyIsImNzc19pc19mdWxsX2RheV9ib29raW5nIiwidG9vbHRpcF9oaW50IiwidGltZXNsb3RzX2luX2RheSIsImpRdWVyeSIsImh0bWwiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiZG9jdW1lbnQiLCJyZWFkeSIsIm9uIiwiZXZlbnQiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLFNBQVNBLG9DQUFULENBQStDQyxlQUEvQyxFQUFnRTtBQUU1RCxNQUFJQyxLQUFLLEdBQUtDLElBQUksQ0FBQ0MsS0FBTCxDQUFpQkgsZUFBZSxHQUFHLFFBQW5CLEdBQStCLEtBQW5DLEdBQTRDLElBQXhELENBQWQ7O0FBQ0EsTUFBSyxTQUFTQSxlQUFkLEVBQStCO0FBQzNCQyxJQUFBQSxLQUFLLEdBQUcsRUFBUjtBQUNIOztBQUNELE1BQUlHLE9BQU8sR0FBR0YsSUFBSSxDQUFDQyxLQUFMLENBQWlCSCxlQUFlLEdBQUcsUUFBbkIsR0FBK0IsS0FBakMsR0FBMEMsSUFBNUMsR0FBcUQsRUFBakUsQ0FBZDs7QUFFQSxNQUFLQyxLQUFLLEdBQUcsRUFBYixFQUFp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SmdCLE1BQUFBLFlBQVksSUFBSSxTQUFoQjtBQUNQOztBQUNEQSxJQUFBQSxZQUFZLElBQUksU0FBaEI7QUFDSDs7QUFFREEsRUFBQUEsWUFBWSxJQUFJLFNBQWhCLENBOUU2QyxDQWlGakQ7O0FBR0lFLEVBQUFBLE1BQU0sQ0FBRSxvQkFBb0IvQyxXQUF0QixDQUFOLENBQTBDZ0QsSUFBMUMsQ0FBZ0RILFlBQWhEO0FBRUFFLEVBQUFBLE1BQU0sQ0FBRSxvQkFBb0IvQyxXQUF0QixDQUFOLENBQTBDaUQsV0FBMUMsQ0FBdUQsbUJBQXZEOztBQUNBLE1BQUtQLE1BQU0sQ0FBQ0MsSUFBUCxDQUFhSixtQkFBYixFQUFtQ2QsTUFBbkMsR0FBNEMsQ0FBakQsRUFBb0Q7QUFDaERzQixJQUFBQSxNQUFNLENBQUUsb0JBQW9CL0MsV0FBdEIsQ0FBTixDQUEwQ2tELFFBQTFDLENBQW9ELG1CQUFwRDtBQUNIO0FBQ0osQyxDQUdHOzs7QUFDQUgsTUFBTSxDQUFFSSxRQUFGLENBQU4sQ0FBbUJDLEtBQW5CLENBQTBCLFlBQVc7QUFDakNMLEVBQUFBLE1BQU0sQ0FBRSxtQkFBRixDQUFOLENBQThCTSxFQUE5QixDQUFrQyxzQ0FBbEMsRUFBMEUsVUFBV0MsS0FBWCxFQUFrQnRELFdBQWxCLEVBQStCO0FBQ3JHc0MsSUFBQUEseUJBQXlCLENBQUV0QyxXQUFGLENBQXpCO0FBQ0gsR0FGRDtBQUlILENBTEQiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcclxuICogQ29udmVydCBzZWNvbmRzIHRvICAyNCBob3VyIGZvcm1hdCAgIDM2MDAgLT4gJzEwOjAwJ1xyXG4gKiBAcGFyYW0gdGltZV9pbl9zZWNvbmRzXHJcbiAqIEByZXR1cm5zIHtzdHJpbmd9XHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2pzX2NvbnZlcnRfX3NlY29uZHNfX3RvX3RpbWVfMjQoIHRpbWVfaW5fc2Vjb25kcyApe1xyXG5cclxuICAgIHZhciBob3VycyAgID0gTWF0aC5mbG9vciggKCAgICh0aW1lX2luX3NlY29uZHMgJSAzMTUzNjAwMCkgJSA4NjQwMCkgLyAzNjAwICk7XHJcbiAgICBpZiAoIDg2NDAwID09IHRpbWVfaW5fc2Vjb25kcyApe1xyXG4gICAgICAgIGhvdXJzID0gMjQ7XHJcbiAgICB9XHJcbiAgICB2YXIgbWludXRlcyA9IE1hdGguZmxvb3IoICggKCAodGltZV9pbl9zZWNvbmRzICUgMzE1MzYwMDApICUgODY0MDApICUgMzYwMCApIC8gNjAgKTtcclxuXHJcbiAgICBpZiAoIGhvdXJzIDwgMTAgKXtcclxuICAgICAgICBob3VycyA9ICcwJyArIGhvdXJzLnRvU3RyaW5nKCk7XHJcbiAgICB9XHJcbiAgICBpZiAoIG1pbnV0ZXMgPCAxMCApe1xyXG4gICAgICAgIG1pbnV0ZXMgPSAnMCcgKyBtaW51dGVzLnRvU3RyaW5nKCk7XHJcbiAgICB9XHJcblxyXG4gICAgcmV0dXJuIGhvdXJzICsgJzonICsgbWludXRlcztcclxufVxyXG5cclxuXHJcbi8qKlxyXG4gKiBDb252ZXJ0IHNlY29uZHMgdG8gIEFNIC8gUE0gdGltZSBmb3JtYXQgICAzNjAwIC0+ICcxMDowMCBBTSdcclxuICpcclxuICogQHBhcmFtIHRpbWVfaW5fc2Vjb25kc1xyXG4gKiBAcmV0dXJucyB7c3RyaW5nfVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19qc19jb252ZXJ0X19zZWNvbmRzX190b190aW1lX0FNUE0oIHRpbWVfaW5fc2Vjb25kcyApe1xyXG5cclxuICAgIHZhciBob3VycyAgID0gTWF0aC5mbG9vciggKCAgICh0aW1lX2luX3NlY29uZHMgJSAzMTUzNjAwMCkgJSA4NjQwMCkgLyAzNjAwICk7XHJcbiAgICBpZiAoIDg2NDAwID09IHRpbWVfaW5fc2Vjb25kcyApe1xyXG4gICAgICAgIGhvdXJzID0gMjQ7XHJcbiAgICB9XHJcbiAgICB2YXIgbWludXRlcyA9IE1hdGguZmxvb3IoICggKCAodGltZV9pbl9zZWNvbmRzICUgMzE1MzYwMDApICUgODY0MDApICUgMzYwMCApIC8gNjAgKTtcclxuXHJcbiAgICAvLyBBbWVyaWNhbiBIZXJpdGFnZSBEaWN0aW9uYXJ5IG9mIHRoZSBFbmdsaXNoIExhbmd1YWdlIHN0YXRlcyBcIkJ5IGNvbnZlbnRpb24sIDEyIEFNIGRlbm90ZXMgbWlkbmlnaHQgYW5kIDEyIFBNIGRlbm90ZXMgTk9PTiAgICAtICAnMTI6MDAgTUlETklHSFQnIGZvciAwMDowMCBhbmQgIC0gJzEyOjAwIE5PT04nIGZvciAnMTI6MDAnXHJcbiAgICB2YXIgYW1fcG0gPSAocGFyc2VJbnQoIGhvdXJzICkgPiAxMikgPyAnUE0nIDogJ0FNJztcclxuICAgIGFtX3BtID0gKDEyID09IGhvdXJzKSA/ICdQTScgOiBhbV9wbTtcclxuICAgIGFtX3BtID0gKDI0ID09IGhvdXJzKSA/ICdBTScgOiBhbV9wbTtcclxuXHJcbiAgICBpZiAoIGhvdXJzID4gMTIgKXtcclxuICAgICAgICBob3VycyA9IGhvdXJzIC0gMTI7XHJcbiAgICB9XHJcbiAgICAvLyBpZiAoIGhvdXJzIDwgMTAgKXtcclxuICAgIC8vICAgICBob3VycyA9ICcwJyArIGhvdXJzLnRvU3RyaW5nKCk7XHJcbiAgICAvLyB9XHJcblxyXG4gICAgaWYgKCBtaW51dGVzIDwgMTAgKXtcclxuICAgICAgICBtaW51dGVzID0gJzAnICsgbWludXRlcy50b1N0cmluZygpO1xyXG4gICAgfVxyXG5cclxuICAgIHJldHVybiBob3VycyArICc6JyArIG1pbnV0ZXMgKyAnICcgKyBhbV9wbTtcclxufVxyXG5cclxuXHJcbi8qKlxyXG4gKiBDb252ZXJ0IFRpbWUgc2xvdCBmcm9tICBzZWNvbmRzIHRvIFJlYWRhYmxlIFRpbWUgRm9ybWF0OiAgMjQgfCBBTS9QTSAgICAgICAgIFsgMCwgMTMqNjAqNjBdICAtPiAgICcwMDowMCBBTSAtIDAxOjAwIFBNJyAgICB8ICAgICAgICcwMDowMCAtIDEzOjAwJ1xyXG4gKlxyXG4gKiBAcGFyYW0gcmVzb3VyY2VfaWQgICAgICAgICAgICAgICAgICAgaW50IElEIG9mIHJlc291cmNlXHJcbiAqIEBwYXJhbSB0aW1lc2xvdF9pbl9zZWNvbmRzX2FyciAgICAgICBbIDAsIDEzKjYwKjYwXVxyXG4gKiBAcmV0dXJucyB7c3RyaW5nfSAgICAgICAgICAgICAgICAgICAgJzAwOjAwIEFNIC0gMDE6MDAgUE0nICAgIHwgICAgICAgJzAwOjAwIC0gMTM6MDAnXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2pzX2NvbnZlcnRfX3NlY29uZHNfX3RvX19yZWFkYWJsZV90aW1lKCByZXNvdXJjZV9pZCwgdGltZXNsb3RfaW5fc2Vjb25kc19hcnIgKXtcclxuXHJcbiAgICB2YXIgcmVhZGFibGVfdGltZV9mb3JtYXQ7XHJcbiAgICB2YXIgaXNfdXNlXzI0O1xyXG4gICAgaWYgKFxyXG4gICAgICAgICAgICggX3dwYmMuY2FsZW5kYXJfX2dldF9wYXJhbV92YWx1ZSggcmVzb3VyY2VfaWQsICdib29raW5nX3RpbWVfZm9ybWF0JyApLmluZGV4T2YoICdBJyApID4gMCApXHJcbiAgICAgICAgfHwgKCBfd3BiYy5jYWxlbmRhcl9fZ2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2Jvb2tpbmdfdGltZV9mb3JtYXQnICkuaW5kZXhPZiggJ2EnICkgPiAwIClcclxuXHJcbiAgICApIHtcclxuICAgICAgICBpc191c2VfMjQgPSBmYWxzZTtcclxuICAgIH0gZWxzZSB7XHJcbiAgICAgICAgaXNfdXNlXzI0ID0gdHJ1ZTtcclxuICAgIH1cclxuXHJcbiAgICBpZiAoIGlzX3VzZV8yNCApe1xyXG4gICAgICAgIHJlYWRhYmxlX3RpbWVfZm9ybWF0ID0gd3BiY19qc19jb252ZXJ0X19zZWNvbmRzX190b190aW1lXzI0KCB0aW1lc2xvdF9pbl9zZWNvbmRzX2FyclsgMCBdIClcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICsgJyAtICdcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICsgd3BiY19qc19jb252ZXJ0X19zZWNvbmRzX190b190aW1lXzI0KCB0aW1lc2xvdF9pbl9zZWNvbmRzX2FyclsgMSBdICk7XHJcbiAgICB9IGVsc2Uge1xyXG4gICAgICAgIHJlYWRhYmxlX3RpbWVfZm9ybWF0ID0gd3BiY19qc19jb252ZXJ0X19zZWNvbmRzX190b190aW1lX0FNUE0oIHRpbWVzbG90X2luX3NlY29uZHNfYXJyWyAwIF0gKVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgKyAnIC0gJ1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgKyB3cGJjX2pzX2NvbnZlcnRfX3NlY29uZHNfX3RvX3RpbWVfQU1QTSggdGltZXNsb3RfaW5fc2Vjb25kc19hcnJbIDEgXSApO1xyXG4gICAgfVxyXG5cclxuICAgIHJldHVybiByZWFkYWJsZV90aW1lX2Zvcm1hdDtcclxufVxyXG5cclxuXHJcblxyXG4vLyA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuLy8gW2NhcGFjaXR5X2hpbnRdXHJcbi8vID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogIENvbnZlcnQgdGltZXMgc2Vjb25kcyBhcnIgWyAyMTYwMCwgMjM0MDAgXSB0byByZWRhYmxlIG9iaiAge31cclxuICAgICAqXHJcbiAgICAgKiBAcGFyYW0gdGltZXNfYXNfc2Vjb25kc19hcnIgICAgICBbIDIxNjAwLCAyMzQwMCBdXHJcbiAgICAgKlxyXG4gICAgICogQHJldHVybnMge3t2YWx1ZV9vcHRpb25fMjRoOiBzdHJpbmdbXSwgdGltZXNfYXNfc2Vjb25kc19hcnIsIHJlYWRhYmxlX3RpbWU6IHN0cmluZ319XHJcbiAgICAgKi9cclxuICAgIGZ1bmN0aW9uIHdwYmNfY29udmVydF9zZWNvbmRzX2Fycl9fdG9fcmVhZGFibGVfb2JqKCByZXNvdXJjZV9pZCwgdGltZXNfYXNfc2Vjb25kc19hcnIgKXtcclxuXHJcblxyXG4gICAgICAgIHZhciByZWFkYWJsZV90aW1lX2Zvcm1hdCA9IHdwYmNfanNfY29udmVydF9fc2Vjb25kc19fdG9fX3JlYWRhYmxlX3RpbWUoIHJlc291cmNlX2lkLCB0aW1lc19hc19zZWNvbmRzX2FyciApO1xyXG5cclxuICAgICAgICB2YXIgb2JqID0ge1xyXG4gICAgICAgICAgICAndGltZXNfYXNfc2Vjb25kcyc6IHdwYmNfY2xvbmVfb2JqKCB0aW1lc19hc19zZWNvbmRzX2FyciApLFxyXG4gICAgICAgICAgICAndmFsdWVfb3B0aW9uXzI0aCc6IFtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgd3BiY19qc19jb252ZXJ0X19zZWNvbmRzX190b190aW1lXzI0KCB0aW1lc19hc19zZWNvbmRzX2FyclsgMCBdICksXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHdwYmNfanNfY29udmVydF9fc2Vjb25kc19fdG9fdGltZV8yNCggdGltZXNfYXNfc2Vjb25kc19hcnJbIDEgXSApXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgXSxcclxuICAgICAgICAgICAgJ3JlYWRhYmxlX3RpbWUnICAgOiByZWFkYWJsZV90aW1lX2Zvcm1hdFxyXG4gICAgICAgIH07XHJcbiAgICAgICAgcmV0dXJuIG9iajtcclxuICAgIH1cclxuXHJcblxyXG4gICAgZnVuY3Rpb24gd3BiY19nZXRfc3RhcnRfZW5kX3RpbWVzX3NlY19hcnJfX2Zvcl9hbGxfcmFuZ2V0aW1lX3Nsb3RzX2luX2Jvb2tpbmdfZm9ybSggcmVzb3VyY2VfaWQgKXtcclxuXHJcbiAgICAgICAgLy8gWyB7anF1ZXJ5X29wdGlvbjoge30sIG5hbWU6IFwicmFuZ2V0aW1lMlwiLCB0aW1lc19hc19zZWNvbmRzOlsgMzYwMDAsIDQzMjAwIF0sIHZhbHVlX29wdGlvbl8yNGg6IFwiMTA6MDAgLSAxMjowMFwifSAsIC4uLiBdXHJcbiAgICAgICAgdmFyIGlzX29ubHlfc2VsZWN0ZWRfdGltZSA9IGZhbHNlO1xyXG4gICAgICAgIHZhciBhbGxfdGltZV9maWVsZHMgPSB3cGJjX2dldF9fc2VsZWN0ZWRfdGltZV9maWVsZHNfX2luX2Jvb2tpbmdfZm9ybV9fYXNfYXJyKCByZXNvdXJjZV9pZCAsIGlzX29ubHlfc2VsZWN0ZWRfdGltZSApO1xyXG5cclxuICAgICAgICB2YXIgdGltZV9hc19zZWNvbmRzX2FyciA9IFtdO1xyXG5cclxuICAgICAgICBmb3IgKCB2YXIgdF9rZXkgaW4gYWxsX3RpbWVfZmllbGRzICl7XHJcblxyXG4gICAgICAgICAgICBpZiAoIGFsbF90aW1lX2ZpZWxkc1sgdF9rZXkgXVsgJ25hbWUnIF0uaW5kZXhPZiggJ3JhbmdldGltZScgKSA+IC0xICl7XHJcblxyXG4gICAgICAgICAgICAgICAgdGltZV9hc19zZWNvbmRzX2Fyci5wdXNoKFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHdwYmNfY29udmVydF9zZWNvbmRzX2Fycl9fdG9fcmVhZGFibGVfb2JqKCAgcmVzb3VyY2VfaWQsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBhbGxfdGltZV9maWVsZHNbIHRfa2V5IF0udGltZXNfYXNfc2Vjb25kcyAgICAgICAgICAgICAgIC8vIHsgdGltZXNfYXNfc2Vjb25kczogWyAyMTYwMCwgMjM0MDAgXSwgdmFsdWVfb3B0aW9uXzI0aDogJzA2OjAwIC0gMDY6MzAnLCBuYW1lOiAncmFuZ2V0aW1lMltdJywganF1ZXJ5X29wdGlvbjogalF1ZXJ5X09iamVjdCB7fX1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICApXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICApO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICByZXR1cm4gdGltZV9hc19zZWNvbmRzX2FycjtcclxuICAgIH1cclxuXHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBHZXQgYXJyYXkgIG9mIGF2YWlsYWJsZSBpdGVtcyBmb3IgZWFjaCAgc2VlbGN0ZWQgZGF0ZSBhbmQgdGltZSBzbG90IGluIGJvb2tpbmcgZm9ybVxyXG4gICAgICpcclxuICAgICAqIEBwYXJhbSBpbnQgcmVzb3VyY2VfaWRcclxuICAgICAqIEByZXR1cm5zIFtcclxuICAgICAqXHJcbiAgICAgKiAgICAgICAgICAgICAgXCIyMDI0LTA1LTE3XCI6IFtcclxuICAgICAqICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgMF84NjQwMCAgICA6IE9iamVjdCB7IGF2YWlsYWJsZV9pdGVtczogNCwgdmFsdWVfb3B0aW9uXzI0aDogXCIwMDowMCAtIDI0OjAwXCIsIGRhdGVfc3FsX2tleTogXCIyMDI0LTA1LTE3XCIsIOKApiB9XHJcbiAgICAgKiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDM2MDAwXzQzMjAwOiBPYmplY3QgeyBhdmFpbGFibGVfaXRlbXM6IDQsIHZhbHVlX29wdGlvbl8yNGg6IFwiMTA6MDAgLSAxMjowMFwiLCBkYXRlX3NxbF9rZXk6IFwiMjAyNC0wNS0xN1wiLCDigKYgfVxyXG4gICAgICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICA0MzIwMF81MDQwMDogT2JqZWN0IHsgYXZhaWxhYmxlX2l0ZW1zOiA0LCB2YWx1ZV9vcHRpb25fMjRoOiBcIjEyOjAwIC0gMTQ6MDBcIiwgZGF0ZV9zcWxfa2V5OiBcIjIwMjQtMDUtMTdcIiwg4oCmIH1cclxuICAgICAqICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgNTA0MDBfNTc2MDA6IE9iamVjdCB7IGF2YWlsYWJsZV9pdGVtczogNCwgdmFsdWVfb3B0aW9uXzI0aDogXCIxNDowMCAtIDE2OjAwXCIsIGRhdGVfc3FsX2tleTogXCIyMDI0LTA1LTE3XCIsIOKApiB9XHJcbiAgICAgKiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDU3NjAwXzY0ODAwOiBPYmplY3QgeyBhdmFpbGFibGVfaXRlbXM6IDQsIHZhbHVlX29wdGlvbl8yNGg6IFwiMTY6MDAgLSAxODowMFwiLCBkYXRlX3NxbF9rZXk6IFwiMjAyNC0wNS0xN1wiLCDigKYgfVxyXG4gICAgICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICA2NDgwMF83MjAwMDogT2JqZWN0IHsgYXZhaWxhYmxlX2l0ZW1zOiA0LCB2YWx1ZV9vcHRpb25fMjRoOiBcIjE4OjAwIC0gMjA6MDBcIiwgZGF0ZV9zcWxfa2V5OiBcIjIwMjQtMDUtMTdcIiwg4oCmIH1cclxuICAgICAqICAgICAgICAgICAgICAgICAgICAgICAgICAgIF1cclxuICAgICAqICAgICAgICAgICAgICBcIjIwMjQtMDUtMTlcIjogW1xyXG4gICAgICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAwXzg2NDAwICAgIDogT2JqZWN0IHsgYXZhaWxhYmxlX2l0ZW1zOiA0LCB2YWx1ZV9vcHRpb25fMjRoOiBcIjAwOjAwIC0gMjQ6MDBcIiwgZGF0ZV9zcWxfa2V5OiBcIjIwMjQtMDUtMTlcIiwg4oCmIH1cclxuICAgICAqICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgMzYwMDBfNDMyMDA6IE9iamVjdCB7IGF2YWlsYWJsZV9pdGVtczogNCwgdmFsdWVfb3B0aW9uXzI0aDogXCIxMDowMCAtIDEyOjAwXCIsIGRhdGVfc3FsX2tleTogXCIyMDI0LTA1LTE5XCIsIOKApiB9XHJcbiAgICAgKiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDQzMjAwXzUwNDAwOiBPYmplY3QgeyBhdmFpbGFibGVfaXRlbXM6IDQsIHZhbHVlX29wdGlvbl8yNGg6IFwiMTI6MDAgLSAxNDowMFwiLCBkYXRlX3NxbF9rZXk6IFwiMjAyNC0wNS0xOVwiLCDigKYgfVxyXG4gICAgICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICA1MDQwMF81NzYwMDogT2JqZWN0IHsgYXZhaWxhYmxlX2l0ZW1zOiA0LCB2YWx1ZV9vcHRpb25fMjRoOiBcIjE0OjAwIC0gMTY6MDBcIiwgZGF0ZV9zcWxfa2V5OiBcIjIwMjQtMDUtMTlcIiwg4oCmIH1cclxuICAgICAqICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgNTc2MDBfNjQ4MDA6IE9iamVjdCB7IGF2YWlsYWJsZV9pdGVtczogNCwgdmFsdWVfb3B0aW9uXzI0aDogXCIxNjowMCAtIDE4OjAwXCIsIGRhdGVfc3FsX2tleTogXCIyMDI0LTA1LTE5XCIsIOKApiB9XHJcbiAgICAgKiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDY0ODAwXzcyMDAwOiBPYmplY3QgeyBhdmFpbGEuLi5cclxuICAgICAqICAgICAgICAgICAgICAgICAgICAgICAgICAgIF1cclxuICAgICAqICAgICAgICAgIF1cclxuICAgICAqL1xyXG4gICAgZnVuY3Rpb24gd3BiY19nZXRfX2F2YWlsYWJsZV9pdGVtc19mb3Jfc2VsZWN0ZWRfZGF0ZXRpbWUoIHJlc291cmNlX2lkICl7XHJcblxyXG4gICAgICAgICB2YXIgc2VsZWN0ZWRfdGltZV9maWVsZHMgPSBbXTtcclxuXHJcbiAgICAgICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gICAgICAgIC8vIFRoaXMgaXMgY3VycmVudCBzZWxlY3RlZCAvIGVudGVyZWQgIE9ORSB0aW1lIHNsb3QgIChpZiBub3QgZW50cmVkIHRpbWUsICB0aGVuICBmdWxsIGRhdGUpXHJcbiAgICAgICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gICAgICAgIC8vIFsgMCAsIDI0ICogNjAgKiA2MCBdICB8ICBbIDEyKjYwKjYwICwgMTQqNjAqNjAgXSAgICBUaGlzIGlzIHNlbGVjdGVkLCAgZW50ZXJlZCB0aW1lcy4gU28gIHdlIHdpbGwgIHNob3cgYXZhaWxhYmxlIHNsb3RzIG9ubHkgIGZvciBzZWxlY3RlZCB0aW1lc1xyXG4gICAgICAgIHZhciB0aW1lX3RvX2Jvb2tfX2FzX3NlY29uZHNfYXJyID0gd3BiY19nZXRfc3RhcnRfZW5kX3RpbWVzX19pbl9ib29raW5nX2Zvcm1fX2FzX3NlY29uZHMoIHJlc291cmNlX2lkICk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gWyAxMio2MCo2MCAsIDE0KjYwKjYwIF1cclxuICAgICAgICBzZWxlY3RlZF90aW1lX2ZpZWxkcy5wdXNoKCAgd3BiY19jb252ZXJ0X3NlY29uZHNfYXJyX190b19yZWFkYWJsZV9vYmooIHJlc291cmNlX2lkLCB0aW1lX3RvX2Jvb2tfX2FzX3NlY29uZHNfYXJyICkgKTtcclxuXHJcbiAgICAgICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gICAgICAgIC8vIFRoaXMgaXMgYWxsICB0aW1lLXNsb3RzIGZyb20gIHJhbmdlLXRpbWUsICBpZiBhbnlcclxuICAgICAgICB2YXIgYWxsX3JhbmdldGltZV9zbG90c19hcnIgPSB3cGJjX2dldF9zdGFydF9lbmRfdGltZXNfc2VjX2Fycl9fZm9yX2FsbF9yYW5nZXRpbWVfc2xvdHNfaW5fYm9va2luZ19mb3JtKCByZXNvdXJjZV9pZCApO1xyXG4gICAgICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblxyXG4gICAgICAgIHZhciB3b3JrX3RpbWVzX2FycmF5ID0gKGFsbF9yYW5nZXRpbWVfc2xvdHNfYXJyLmxlbmd0aCA+IDApXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgID8gd3BiY19jbG9uZV9vYmooIGFsbF9yYW5nZXRpbWVfc2xvdHNfYXJyIClcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgOiB3cGJjX2Nsb25lX29iaiggc2VsZWN0ZWRfdGltZV9maWVsZHMgKTtcclxuXHJcbiAgICAgICAgdmFyIGNhcGFjaXR5X2RhdGVzX3RpbWVzID0gW107XHJcblxyXG4gICAgICAgIGZvciAoIHZhciBvYmpfa2V5IGluIHdvcmtfdGltZXNfYXJyYXkgKXtcclxuXHJcbiAgICAgICAgICAgIC8vIE9iamVjdCB7IG5hbWU6IFwicmFuZ2V0aW1lMlwiLCB2YWx1ZV9vcHRpb25fMjRoOiBcIjEwOjAwIC0gMTI6MDBcIiwganF1ZXJ5X29wdGlvbjoge+KApn0sIG5hbWU6IFwicmFuZ2V0aW1lMlwiLCB0aW1lc19hc19zZWNvbmRzOiBBcnJheSBbIDM2MDAwLCA0MzIwMCBdLCB2YWx1ZV9vcHRpb25fMjRoOiBcIjEwOjAwIC0gMTI6MDBcIiB9XHJcbiAgICAgICAgICAgIHZhciBvbmVfdGltZXNfcmVhZGFibGVfb2JqID0gd29ya190aW1lc19hcnJheVsgb2JqX2tleSBdO1xyXG5cclxuICAgICAgICAgICAgLy8gJzQzMjAwXzUwNDAwJ1xyXG4gICAgICAgICAgICB2YXIgdGltZV9rZXkgPSAnJyArIG9uZV90aW1lc19yZWFkYWJsZV9vYmpbICd0aW1lc19hc19zZWNvbmRzJyBdWyAwIF0gKyAnXycgKyBvbmVfdGltZXNfcmVhZGFibGVfb2JqWyAndGltZXNfYXNfc2Vjb25kcycgXVsgMSBdO1xyXG5cclxuXHJcbiAgICAgICAgICAgIC8qKlxyXG4gICAgICAgICAgICAgKiAgWyAgIFwiMjAyNC0wNS0xNlwiOiBbICAwOiBPYmplY3QgeyByZXNvdXJjZV9pZDogMiwgIGlzX2F2YWlsYWJsZTogdHJ1ZSwgYm9va2VkX19zZWNvbmRzOiBbXSwg4oCmIH1cclxuICAgICAgICAgICAgICogICAgICAgICAgICAgICAgICAgICAgIDE6IE9iamVjdCB7IHJlc291cmNlX2lkOiAxMCwgaXNfYXZhaWxhYmxlOiB0cnVlLCBib29rZWRfX3NlY29uZHM6IFtdLCDigKYgfVxyXG4gICAgICAgICAgICAgKiAgICAgICAgICAgICAgICAgICAgICAgMjogT2JqZWN0IHsgcmVzb3VyY2VfaWQ6IDExLCBpc19hdmFpbGFibGU6IHRydWUsIGJvb2tlZF9fc2Vjb25kczogW10sIOKApiB9XHJcbiAgICAgICAgICAgICAqICAgXVxyXG4gICAgICAgICAgICAgKi9cclxuICAgICAgICAgICAgdmFyIGF2YWlsYWJsZV9zbG90c19ieV9kYXRlcyA9IHdwYmNfX2dldF9hdmFpbGFibGVfc2xvdHNfX2Zvcl9zZWxlY3RlZF9kYXRlc190aW1lc19fYmwoIHJlc291cmNlX2lkLCB3cGJjX2Nsb25lX29iaiggb25lX3RpbWVzX3JlYWRhYmxlX29ialsgJ3RpbWVzX2FzX3NlY29uZHMnIF0gKSApO1xyXG4vL2NvbnNvbGUubG9nKCAnYXZhaWxhYmxlX3Nsb3RzX2J5X2RhdGVzPT0nLGF2YWlsYWJsZV9zbG90c19ieV9kYXRlcyk7XHJcblxyXG4gICAgICAgICAgICAvLyBMb29wIERhdGVzXHJcbiAgICAgICAgICAgIGZvciAoIHZhciBkYXRlX3NxbF9rZXkgaW4gYXZhaWxhYmxlX3Nsb3RzX2J5X2RhdGVzICl7XHJcblxyXG4gICAgICAgICAgICAgICAgdmFyIGF2YWlsYWJsZV9zbG90c19pbl9vbmVfZGF0ZSA9IGF2YWlsYWJsZV9zbG90c19ieV9kYXRlc1sgZGF0ZV9zcWxfa2V5IF07XHJcblxyXG4gICAgICAgICAgICAgICAgdmFyIGNvdW50X2F2YWlsYWJsZV9zbG90cyA9IDBcclxuXHJcbiAgICAgICAgICAgICAgICB2YXIgdGltZTJib29rX2luX3NlY19wZXJfZWFjaF9kYXRlID0gd3BiY19jbG9uZV9vYmooIG9uZV90aW1lc19yZWFkYWJsZV9vYmpbICd0aW1lc19hc19zZWNvbmRzJyBdICk7XHJcblxyXG4gICAgICAgICAgICAgICAgLy8gTG9vcCBBdmFpbGFibGUgU2xvdHMgaW4gRGF0ZVxyXG4gICAgICAgICAgICAgICAgZm9yICggdmFyIGkgPSAwOyBpIDwgYXZhaWxhYmxlX3Nsb3RzX2luX29uZV9kYXRlLmxlbmd0aDsgaSsrICl7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYgKCBhdmFpbGFibGVfc2xvdHNfaW5fb25lX2RhdGVbIGkgXVsgJ2lzX2F2YWlsYWJsZScgXSApe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBjb3VudF9hdmFpbGFibGVfc2xvdHMrKztcclxuICAgICAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgICAgIC8vIE92dmVyaWRlIHRoYXQgIHRpbWUgYnkgIHRpbWVzLCAgdGhhdCAgY2FuICBiZSBkaWZmZXJlbnQgZm9yIHNldmVyYWwgIGRhdGVzLCAgaWYgZGVhY3RpdmF0ZWQgdGhpcyBvcHRpb246ICdVc2Ugc2VsZWN0ZWQgdGltZXMgZm9yIGVhY2ggYm9va2luZyBkYXRlJ1xyXG4gICAgICAgICAgICAgICAgICAgIC8vIEZvciBleGFtcGxlIGlmIHNsZWN0ZSB0aW1lIDEwOjAwIC0gMTE6MDAgYW5kIHNlbGVjdGVkIDMgZGF0ZXMsIHRoZW4gIGJvb2tlZCB0aW1lcyBoZXJlIHdpbGwgYmUgIDEwOjAwIC0gMjQ6MDAsICAgMDA6MDAgLSAyNDowMCwgICAwMDowMCAtIDExOjAwXHJcbiAgICAgICAgICAgICAgICAgICAgdGltZTJib29rX2luX3NlY19wZXJfZWFjaF9kYXRlID0gd3BiY19jbG9uZV9vYmooIGF2YWlsYWJsZV9zbG90c19pbl9vbmVfZGF0ZVsgaSBdWyd0aW1lX3RvX2Jvb2tfX3NlY29uZHMnXSApO1xyXG4gICAgICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgICAgIC8vIFNhdmUgaW5mb1xyXG4gICAgICAgICAgICAgICAgaWYgKCAndW5kZWZpbmVkJyA9PT0gdHlwZW9mIChjYXBhY2l0eV9kYXRlc190aW1lc1sgZGF0ZV9zcWxfa2V5IF0pICl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FwYWNpdHlfZGF0ZXNfdGltZXNbIGRhdGVfc3FsX2tleSBdID0gW107XHJcbiAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgdmFyIGNzc19jbGFzcyA9ICcnO1xyXG4gICAgICAgICAgICAgICAgaWYgKCBzZWxlY3RlZF90aW1lX2ZpZWxkcy5sZW5ndGggPiAwICl7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYgKCAgIChzZWxlY3RlZF90aW1lX2ZpZWxkc1sgMCBdWyAndGltZXNfYXNfc2Vjb25kcycgXVsgMCBdID09IHRpbWUyYm9va19pbl9zZWNfcGVyX2VhY2hfZGF0ZVsgMCBdKVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAmJiAoc2VsZWN0ZWRfdGltZV9maWVsZHNbIDAgXVsgJ3RpbWVzX2FzX3NlY29uZHMnIF1bIDEgXSA9PSB0aW1lMmJvb2tfaW5fc2VjX3Blcl9lYWNoX2RhdGVbIDEgXSkgKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgY3NzX2NsYXNzICs9ICcgd3BiY19zZWxlY3RlZF90aW1lc2xvdCdcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICAgICAgICAgICAgICAgIC8vIFJlYWRhYmxlIFRpbWUgRm9ybWF0OiAgMjQgfCBBTS9QTVxyXG4gICAgICAgICAgICAgICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICAgICAgICAgICAgICAgIHZhciByZWFkYWJsZV90aW1lX2Zvcm1hdCA9IHdwYmNfanNfY29udmVydF9fc2Vjb25kc19fdG9fX3JlYWRhYmxlX3RpbWUoIHJlc291cmNlX2lkLCB0aW1lMmJvb2tfaW5fc2VjX3Blcl9lYWNoX2RhdGUgKVxyXG5cclxuICAgICAgICAgICAgICAgIGNhcGFjaXR5X2RhdGVzX3RpbWVzWyBkYXRlX3NxbF9rZXkgXVsgdGltZV9rZXkgXSA9IHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyAndmFsdWVfb3B0aW9uXzI0aCc6b25lX3RpbWVzX3JlYWRhYmxlX29ialsgJ3ZhbHVlX29wdGlvbl8yNGgnIF0sXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2F2YWlsYWJsZV9pdGVtcyc6IGNvdW50X2F2YWlsYWJsZV9zbG90cyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAndGltZXNfYXNfc2Vjb25kcyc6IHRpbWUyYm9va19pbl9zZWNfcGVyX2VhY2hfZGF0ZSxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnZGF0ZV9zcWxfa2V5JyAgICA6IGRhdGVfc3FsX2tleSxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAncmVhZGFibGVfdGltZScgICA6IHJlYWRhYmxlX3RpbWVfZm9ybWF0LFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdjc3NfY2xhc3MnICAgICAgIDogY3NzX2NsYXNzXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9O1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICByZXR1cm4gY2FwYWNpdHlfZGF0ZXNfdGltZXM7XHJcblxyXG4gICAgfVxyXG5cclxuXHJcblxyXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuLy8gVGVtcGxhdGUgZm9yIHNob3J0Y29kZSBoaW50XHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuLyoqXHJcbiAqIFVwZGF0ZSB0aW1lIGhpbnQgc2hvcnRjb2RlIGNvbnRlbnQgaW4gYm9va2luZyBmb3JtXHJcbiAqXHJcbiAqIEBwYXJhbSByZXNvdXJjZV9pZFxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY191cGRhdGVfY2FwYWNpdHlfaGludCggcmVzb3VyY2VfaWQgKXtcclxuXHJcbiAgICAgLyoqXHJcbiAgICAgKiAgWyAgICAgICAgICBcIjIwMjQtMDUtMTdcIjogW1xyXG4gICAgICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAwXzg2NDAwICAgIDogT2JqZWN0IHsgYXZhaWxhYmxlX2l0ZW1zOiA0LCB2YWx1ZV9vcHRpb25fMjRoOiBcIjAwOjAwIC0gMjQ6MDBcIiwgZGF0ZV9zcWxfa2V5OiBcIjIwMjQtMDUtMTdcIiwg4oCmIH1cclxuICAgICAqICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgMzYwMDBfNDMyMDA6IE9iamVjdCB7IGF2YWlsYWJsZV9pdGVtczogNCwgdmFsdWVfb3B0aW9uXzI0aDogXCIxMDowMCAtIDEyOjAwXCIsIGRhdGVfc3FsX2tleTogXCIyMDI0LTA1LTE3XCIsIOKApiB9XHJcbiAgICAgKiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDQzMjAwXzUwNDAwOiBPYmplY3QgeyBhdmFpbGFibGVfaXRlbXM6IDQsIHZhbHVlX29wdGlvbl8yNGg6IFwiMTI6MDAgLSAxNDowMFwiLCBkYXRlX3NxbF9rZXk6IFwiMjAyNC0wNS0xN1wiLCDigKYgfVxyXG4gICAgICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICA1MDQwMF81NzYwMDogT2JqZWN0IHsgYXZhaWxhYmxlX2l0ZW1zOiA0LCB2YWx1ZV9vcHRpb25fMjRoOiBcIjE0OjAwIC0gMTY6MDBcIiwgZGF0ZV9zcWxfa2V5OiBcIjIwMjQtMDUtMTdcIiwg4oCmIH1cclxuICAgICAqICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgNTc2MDBfNjQ4MDA6IE9iamVjdCB7IGF2YWlsYWJsZV9pdGVtczogNCwgdmFsdWVfb3B0aW9uXzI0aDogXCIxNjowMCAtIDE4OjAwXCIsIGRhdGVfc3FsX2tleTogXCIyMDI0LTA1LTE3XCIsIOKApiB9XHJcbiAgICAgKiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDY0ODAwXzcyMDAwOiBPYmplY3QgeyBhdmFpbGFibGVfaXRlbXM6IDQsIHZhbHVlX29wdGlvbl8yNGg6IFwiMTg6MDAgLSAyMDowMFwiLCBkYXRlX3NxbF9rZXk6IFwiMjAyNC0wNS0xN1wiLCDigKYgfVxyXG4gICAgICogICAgICAgICAgICAgICAgICAgICAgICAgICAgXVxyXG4gICAgICogICAgICAgICAgICAgIFwiMjAyNC0wNS0xOVwiOiBbXHJcbiAgICAgKiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDBfODY0MDAgICAgOiBPYmplY3QgeyBhdmFpbGFibGVfaXRlbXM6IDQsIHZhbHVlX29wdGlvbl8yNGg6IFwiMDA6MDAgLSAyNDowMFwiLCBkYXRlX3NxbF9rZXk6IFwiMjAyNC0wNS0xOVwiLCDigKYgfVxyXG4gICAgICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAzNjAwMF80MzIwMDogT2JqZWN0IHsgYXZhaWxhYmxlX2l0ZW1zOiA0LCB2YWx1ZV9vcHRpb25fMjRoOiBcIjEwOjAwIC0gMTI6MDBcIiwgZGF0ZV9zcWxfa2V5OiBcIjIwMjQtMDUtMTlcIiwg4oCmIH1cclxuICAgICAqICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgNDMyMDBfNTA0MDA6IE9iamVjdCB7IGF2YWlsYWJsZV9pdGVtczogNCwgdmFsdWVfb3B0aW9uXzI0aDogXCIxMjowMCAtIDE0OjAwXCIsIGRhdGVfc3FsX2tleTogXCIyMDI0LTA1LTE5XCIsIOKApiB9XHJcbiAgICAgKiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDUwNDAwXzU3NjAwOiBPYmplY3QgeyBhdmFpbGFibGVfaXRlbXM6IDQsIHZhbHVlX29wdGlvbl8yNGg6IFwiMTQ6MDAgLSAxNjowMFwiLCBkYXRlX3NxbF9rZXk6IFwiMjAyNC0wNS0xOVwiLCDigKYgfVxyXG4gICAgICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICA1NzYwMF82NDgwMDogT2JqZWN0IHsgYXZhaWxhYmxlX2l0ZW1zOiA0LCB2YWx1ZV9vcHRpb25fMjRoOiBcIjE2OjAwIC0gMTg6MDBcIiwgZGF0ZV9zcWxfa2V5OiBcIjIwMjQtMDUtMTlcIiwg4oCmIH1cclxuICAgICAqICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgNjQ4MDBfNzIwMDA6IE9iamVjdCB7IGF2YWlsYS4uLlxyXG4gICAgICogICAgICAgICAgICAgICAgICAgICAgICAgICAgXVxyXG4gICAgICogICAgICAgICAgXVxyXG4gICAgICovXHJcbiAgICB2YXIgYXZhaWxhYmxlX2l0ZW1zX2FyciA9IHdwYmNfZ2V0X19hdmFpbGFibGVfaXRlbXNfZm9yX3NlbGVjdGVkX2RhdGV0aW1lKCByZXNvdXJjZV9pZCApO1xyXG5cclxuICAgIHZhciBpc19mdWxsX2RheV9ib29raW5nID0gdHJ1ZTtcclxuICAgIGZvciAoIHZhciBvYmpfZGF0ZV90YWcgaW4gYXZhaWxhYmxlX2l0ZW1zX2FyciApe1xyXG5cclxuICAgICAgICBpZiAoIE9iamVjdC5rZXlzKCBhdmFpbGFibGVfaXRlbXNfYXJyWyBvYmpfZGF0ZV90YWcgXSApLmxlbmd0aCA+IDEgKXtcclxuICAgICAgICAgICAgaXNfZnVsbF9kYXlfYm9va2luZyA9IGZhbHNlO1xyXG4gICAgICAgICAgICBicmVhaztcclxuICAgICAgICB9XHJcbiAgICAgICAgZm9yICggdmFyIHRpbWVfa2V5IGluIGF2YWlsYWJsZV9pdGVtc19hcnJbIG9ial9kYXRlX3RhZyBdICl7XHJcbiAgICAgICAgICAgIGlmICggKGF2YWlsYWJsZV9pdGVtc19hcnJbIG9ial9kYXRlX3RhZyBdWyB0aW1lX2tleSBdWyAndGltZXNfYXNfc2Vjb25kcycgXVsgMCBdID4gMCkgJiYgKGF2YWlsYWJsZV9pdGVtc19hcnJbIG9ial9kYXRlX3RhZyBdWyB0aW1lX2tleSBdWyAndGltZXNfYXNfc2Vjb25kcycgXVsgMSBdIDwgODY0MDApICl7XHJcbiAgICAgICAgICAgICAgICBpc19mdWxsX2RheV9ib29raW5nID0gZmFsc2U7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICBpZiAoICFpc19mdWxsX2RheV9ib29raW5nICl7XHJcbiAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuICAgIHZhciBjc3NfaXNfZnVsbF9kYXlfYm9va2luZyA9IChpc19mdWxsX2RheV9ib29raW5nKSA/ICcgd3BiY19jaGludF9fZnVsbF9kYXlfYm9va2luZ3MnIDogJyc7XHJcblxyXG4gICAgdmFyIHRvb2x0aXBfaGludCA9ICc8ZGl2IGNsYXNzPVwid3BiY19jYXBhY2l0eV9oaW50X2NvbnRhaW5lcicgKyBjc3NfaXNfZnVsbF9kYXlfYm9va2luZyArICdcIj4nO1xyXG5cclxuICAgIGZvciAoIHZhciBvYmpfZGF0ZV90YWcgaW4gYXZhaWxhYmxlX2l0ZW1zX2FyciApe1xyXG5cclxuICAgICAgICB2YXIgdGltZXNsb3RzX2luX2RheSA9IGF2YWlsYWJsZV9pdGVtc19hcnJbIG9ial9kYXRlX3RhZyBdXHJcblxyXG4gICAgICAgIHRvb2x0aXBfaGludCArPSAnPGRpdiBjbGFzcz1cIndwYmNfY2hpbnRfX2RhdGV0aW1lX2NvbnRhaW5lclwiPic7XHJcblxyXG4gICAgICAgIC8vIEpTT04uc3RyaW5naWZ5KGF2YWlsYWJsZV9pdGVtc19hcnIpLm1hdGNoKC9bXlxcXFxdXCI6L2cpLmxlbmd0aFxyXG4gICAgICAgIGlmICggKE9iamVjdC5rZXlzKCBhdmFpbGFibGVfaXRlbXNfYXJyICkubGVuZ3RoID4gMSkgfHwgKGlzX2Z1bGxfZGF5X2Jvb2tpbmcpICl7XHJcbiAgICAgICAgICAgIHRvb2x0aXBfaGludCArPSAnPGRpdiBjbGFzcz1cIndwYmNfY2hpbnRfX2RhdGVfY29udGFpbmVyXCI+JztcclxuICAgICAgICAgICAgICAgIHRvb2x0aXBfaGludCArPSAnPGRpdiBjbGFzcz1cIndwYmNfY2hpbnRfX2RhdGVcIj4nICsgb2JqX2RhdGVfdGFnICsgJzwvZGl2PiAnO1xyXG4gICAgICAgICAgICAgICAgdG9vbHRpcF9oaW50ICs9ICc8ZGl2IGNsYXNzPVwid3BiY19jaGludF9fZGF0ZV9kaXZpZGVyXCI+OjwvZGl2PiAnO1xyXG4gICAgICAgICAgICB0b29sdGlwX2hpbnQgKz0gJzwvZGl2PiAnO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgZm9yICggdmFyIHRpbWVfa2V5IGluIHRpbWVzbG90c19pbl9kYXkgKXtcclxuICAgICAgICAgICAgICAgIHRvb2x0aXBfaGludCArPSAnPGRpdiBjbGFzcz1cIndwYmNfY2hpbnRfX3RpbWVfY29udGFpbmVyXCI+JztcclxuXHJcbiAgICAgICAgICAgICAgICAvLyBJZiBub3QgZnVsbCBkYXkgYm9va2luZzogZS5nICAwMDowMCAtIDI0OjAwXHJcbiAgICAgICAgICAgICAgICAvL2lmICggKHRpbWVzbG90c19pbl9kYXlbIHRpbWVfa2V5IF1bICd0aW1lc19hc19zZWNvbmRzJyBdWyAwIF0gPiAwKSAmJiAodGltZXNsb3RzX2luX2RheVsgdGltZV9rZXkgXVsgJ3RpbWVzX2FzX3NlY29uZHMnIF1bIDEgXSA8IDg2NDAwKSApe1xyXG5cclxuICAgICAgICAgICAgICAgICAgICAgdG9vbHRpcF9oaW50ICs9ICc8ZGl2IGNsYXNzPVwid3BiY19jaGludF9fdGltZXNsb3QgJyArIHRpbWVzbG90c19pbl9kYXlbIHRpbWVfa2V5IF1bICdjc3NfY2xhc3MnIF0gKyAnXCI+J1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgKyB0aW1lc2xvdHNfaW5fZGF5WyB0aW1lX2tleSBdWyAncmVhZGFibGVfdGltZScgXVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICsgJzwvZGl2PiAnO1xyXG4gICAgICAgICAgICAgICAgICAgIHRvb2x0aXBfaGludCArPSAnPGRpdiBjbGFzcz1cIndwYmNfY2hpbnRfX3RpbWVzbG90X2RpdmlkZXJcIj46IDwvZGl2PiAnO1xyXG4gICAgICAgICAgICAgICAgLy99XHJcblxyXG4gICAgICAgICAgICAgICAgICAgIHRvb2x0aXBfaGludCArPSAnPGRpdiBjbGFzcz1cIndwYmNfY2hpbnRfX2F2YWlsYWJpbGl0eSBhdmFpbGFiaWxpdHlfbnVtXycgKyB0aW1lc2xvdHNfaW5fZGF5WyB0aW1lX2tleSBdWyAnYXZhaWxhYmxlX2l0ZW1zJyBdICsgJ1wiPidcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICsgdGltZXNsb3RzX2luX2RheVsgdGltZV9rZXkgXVsgJ2F2YWlsYWJsZV9pdGVtcycgXVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgKyAnPC9kaXY+ICc7XHJcbiAgICAgICAgICAgICAgICB0b29sdGlwX2hpbnQgKz0gJzwvZGl2PiAnO1xyXG4gICAgICAgIH1cclxuICAgICAgICB0b29sdGlwX2hpbnQgKz0gJzwvZGl2PiAnO1xyXG4gICAgfVxyXG5cclxuICAgIHRvb2x0aXBfaGludCArPSAnPC9kaXY+ICc7XHJcblxyXG5cclxuLy9jb25zb2xlLmxvZyggJzo6IGF2YWlsYWJsZV9pdGVtc19hcnIgOjonLCBhdmFpbGFibGVfaXRlbXNfYXJyICk7XHJcblxyXG5cclxuICAgIGpRdWVyeSggJy5jYXBhY2l0eV9oaW50XycgKyByZXNvdXJjZV9pZCApLmh0bWwoIHRvb2x0aXBfaGludCApO1xyXG5cclxuICAgIGpRdWVyeSggJy5jYXBhY2l0eV9oaW50XycgKyByZXNvdXJjZV9pZCApLnJlbW92ZUNsYXNzKCAnd3BiY19jaGluX25ld2xpbmUnICk7XHJcbiAgICBpZiAoIE9iamVjdC5rZXlzKCBhdmFpbGFibGVfaXRlbXNfYXJyICkubGVuZ3RoID4gMSApe1xyXG4gICAgICAgIGpRdWVyeSggJy5jYXBhY2l0eV9oaW50XycgKyByZXNvdXJjZV9pZCApLmFkZENsYXNzKCAnd3BiY19jaGluX25ld2xpbmUnICk7XHJcbiAgICB9XHJcbn1cclxuXHJcblxyXG4gICAgLy8gUnVuIHNob3J0Y29kZSBjaGFuZ2luZyBhZnRlciAgZGF0ZXMgc2VsZWN0aW9uLCAgYW5kIG9wdGlvbnMgc2VsZWN0aW9uLlxyXG4gICAgalF1ZXJ5KCBkb2N1bWVudCApLnJlYWR5KCBmdW5jdGlvbiAoKXtcclxuICAgICAgICBqUXVlcnkoICcuYm9va2luZ19mb3JtX2RpdicgKS5vbiggJ3dwYmNfYm9va2luZ19kYXRlX29yX29wdGlvbl9zZWxlY3RlZCcsIGZ1bmN0aW9uICggZXZlbnQsIHJlc291cmNlX2lkICl7XHJcbiAgICAgICAgICAgIHdwYmNfdXBkYXRlX2NhcGFjaXR5X2hpbnQoIHJlc291cmNlX2lkICk7XHJcbiAgICAgICAgfSApO1xyXG5cclxuICAgIH0gKTtcclxuIl0sImZpbGUiOiJpbmMvanMvX291dC9jYXBhY2l0eV9oaW50cy5qcyJ9
