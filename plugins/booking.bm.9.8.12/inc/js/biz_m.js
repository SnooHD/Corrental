/**
 *  >= Business Medium ...
 */

/**
 * Save initial values of days selection for later use in conditional day logic.   Load it,  when  start the page.
 *
 * @param resource_id
 */
function wpbc__conditions__SAVE_INITIAL__days_selection_params__bm(
  resource_id
) {
  // Save it only ONCE initial values of variables: bk_days_selection_mode, ...
  if (_wpbc.calendar__is_defined(resource_id)) {
    _wpbc.calendar__set_param_value(
      resource_id,
      "saved_variable___days_select_initial",
      {
        dynamic__days_min: _wpbc.calendar__get_param_value(
          resource_id,
          "dynamic__days_min"
        ),
        dynamic__days_max: _wpbc.calendar__get_param_value(
          resource_id,
          "dynamic__days_max"
        ),
        dynamic__days_specific: _wpbc.calendar__get_param_value(
          resource_id,
          "dynamic__days_specific"
        ),
        dynamic__week_days__start: _wpbc.calendar__get_param_value(
          resource_id,
          "dynamic__week_days__start"
        ),

        fixed__days_num: _wpbc.calendar__get_param_value(
          resource_id,
          "fixed__days_num"
        ),
        fixed__week_days__start: _wpbc.calendar__get_param_value(
          resource_id,
          "fixed__week_days__start"
        ),
      }
    );
  }
}

/**
 * Define First selected date for start conditional dates selection
 *
 * @param  all_dates				date object | string
 * @param resource_id				int | string  e.g. '1'
 * @returns {boolean}
 */
function wpbc__conditions__for_range_days__first_date__bm(
  all_dates,
  resource_id
) {
  if ("" == all_dates) {
    return false;
  } // If no days selections so then skip all.

  if (
    "dynamic" !=
      _wpbc.calendar__get_param_value(resource_id, "days_select_mode") &&
    "fixed" != _wpbc.calendar__get_param_value(resource_id, "days_select_mode")
  ) {
    return false;
  } // This conditional logic is possible only if the range days selection

  var selected_first_day = all_dates;

  if (typeof all_dates == "object") {
    // H I G H L I G H T   		// If this parameter is object (Date), then we  highlight the dates

    all_dates = document.getElementById("date_booking" + resource_id).value; // Get dates from the textarea date booking to ''
    if (all_dates != "") {
      // If some date is selected
      var first_date = get_first_day_of_selection(all_dates); // So we are NOT MAKE changing of highlighting if its was first click.
      var last_date = get_last_day_of_selection(all_dates);
      if (
        "fixed" !==
          _wpbc.calendar__get_param_value(resource_id, "days_select_mode") &&
        first_date == last_date
      ) {
        //FixIn: 8.4.4.8
        return false;
      }
    }
  } else {
    // S E L E C T
    var first_date = get_first_day_of_selection(all_dates);
    var last_date = get_last_day_of_selection(all_dates);
    if (first_date != last_date) {
      return false; // We are clicked second time
    }
    var date_sections = first_date.split(".");
    selected_first_day = new Date();
    selected_first_day.setFullYear(
      parseInt(date_sections[2] - 0),
      parseInt(date_sections[1] - 1),
      parseInt(date_sections[0] - 0)
    );
  }

  wpbc__conditions__set_NUMBER_OF_DAYS_TO_SELECT__depend_on_date__bm(
    selected_first_day,
    resource_id
  );
}

/**
 *  Update dates selection parameters ( NUMBER_OF_DAYS_TO_SELECT) depends on belong current date to specific week day or season
 *
 * @param selected_first_date		date, which  we check for conditions
 * @param resource_id				booking resource ID
 * @returns {boolean}
 */
function wpbc__conditions__set_NUMBER_OF_DAYS_TO_SELECT__depend_on_date__bm(
  selected_first_date,
  resource_id
) {
  var is_condition_applied = false;

  var class_day = wpbc__get__td_class_date(selected_first_date);
  var sql_class_date = wpbc__get__sql_class_date(selected_first_date);

  if (
    jQuery(
      "#calendar_booking" +
        resource_id +
        " .datepick-days-cell.cal4date-" +
        class_day
    ).length <= 0
  ) {
    return false; // This date is not exist :( Why ?
  }
  var css_classes_date_arr = jQuery(
    "#calendar_booking" +
      resource_id +
      " .datepick-days-cell.cal4date-" +
      class_day
  )
    .attr("class")
    .split(/\s+/);

  /**
   * S E A S O N     C O N D I T I O N S    -    2.0
   *
   *	 		_wpbc.calendar__get_param_value(3,'conditions')['select-day']['season'] [0]['for'] = 'september_2023'	<- season name to check
   *	 		_wpbc.calendar__get_param_value(3,'conditions')['select-day']['season'] [0]['value'] = '3,4,5'			<- number of days to  select
   */
  var conditions = _wpbc.calendar__get_param_value(resource_id, "conditions");
  var single_css_date_class;
  var days_arr;
  if (
    null !== conditions &&
    "undefined" !== typeof conditions["select-day"] &&
    "undefined" !== typeof conditions["select-day"]["season"] &&
    conditions["select-day"]["season"].length > 0
  ) {
    for (var i = 0; i < css_classes_date_arr.length; i++) {
      single_css_date_class = css_classes_date_arr[i];

      // S E A S O N    F I L T E R    C O N D I T I O N S     - checking
      if (single_css_date_class.indexOf("wpdevbk_season_") >= 0) {
        single_css_date_class = single_css_date_class.replace(
          "wpdevbk_season_",
          ""
        );

        for (var j = 0; j < conditions["select-day"]["season"].length; j++) {
          if (
            single_css_date_class ===
            conditions["select-day"]["season"][j]["for"]
          ) {
            // Ok season in condition == season in a day

            days_arr =
              conditions["select-day"]["season"][j]["value"].split(",");

            // Update real vars
            _wpbc.calendar__set_param_value(
              resource_id,
              "dynamic__days_specific",
              days_arr
            );
            _wpbc.calendar__set_param_value(
              resource_id,
              "dynamic__days_min",
              days_arr[0]
            );
            _wpbc.calendar__set_param_value(
              resource_id,
              "dynamic__days_max",
              days_arr[days_arr.length - 1]
            );
            _wpbc.calendar__set_param_value(
              resource_id,
              "fixed__days_num",
              days_arr[0]
            );

            is_condition_applied = true;
          }
        }
      }
    }
  }

  /**
   * W E E K D A Y S     C O N D I T I O N S    -    2.0
   *
   *	 		_wpbc.calendar__get_param_value(3,'conditions')['select-day']['weekday'] [0]['for'] = '5'			<- number of Week day
   *	 		_wpbc.calendar__get_param_value(3,'conditions')['select-day']['weekday'] [0]['value'] = '3,7'		<- number of days to  select
   */
  if (
    null !== conditions &&
    "undefined" !== typeof conditions["select-day"] &&
    "undefined" !== typeof conditions["select-day"]["weekday"] &&
    conditions["select-day"]["weekday"].length > 0
  ) {
    for (var j = 0; j < conditions["select-day"]["weekday"].length; j++) {
      if (
        selected_first_date.getDay() ==
        conditions["select-day"]["weekday"][j]["for"]
      ) {
        // Ok weekday condition == a week day of day

        days_arr = conditions["select-day"]["weekday"][j]["value"].split(",");

        // Update real vars
        _wpbc.calendar__set_param_value(
          resource_id,
          "dynamic__days_specific",
          days_arr
        );
        _wpbc.calendar__set_param_value(
          resource_id,
          "dynamic__days_min",
          days_arr[0]
        );
        _wpbc.calendar__set_param_value(
          resource_id,
          "dynamic__days_max",
          days_arr[days_arr.length - 1]
        );
        _wpbc.calendar__set_param_value(
          resource_id,
          "fixed__days_num",
          days_arr[0]
        );

        is_condition_applied = true;
      }
    }
  }

  /**
   * D A T E S     C O N D I T I O N S    -    New
   *
   *	 		_wpbc.calendar__get_param_value(3,'conditions')['select-day']['date'][0]['for'] = '2023-08-22'		<- Date
   *	 		_wpbc.calendar__get_param_value(3,'conditions')['select-day']['date'][0]['value'] = '3,7'			<- number of days to  select
   */
  if (
    null !== conditions &&
    "undefined" !== typeof conditions["select-day"] &&
    "undefined" !== typeof conditions["select-day"]["date"] &&
    conditions["select-day"]["date"].length > 0
  ) {
    for (var j = 0; j < conditions["select-day"]["date"].length; j++) {
      if (sql_class_date == conditions["select-day"]["date"][j]["for"]) {
        // Ok date condition == a  date of selected day

        days_arr = conditions["select-day"]["date"][j]["value"].split(",");

        // Update real vars
        _wpbc.calendar__set_param_value(
          resource_id,
          "dynamic__days_specific",
          days_arr
        );
        _wpbc.calendar__set_param_value(
          resource_id,
          "dynamic__days_min",
          days_arr[0]
        );
        _wpbc.calendar__set_param_value(
          resource_id,
          "dynamic__days_max",
          days_arr[days_arr.length - 1]
        );
        _wpbc.calendar__set_param_value(
          resource_id,
          "fixed__days_num",
          days_arr[0]
        );

        is_condition_applied = true;
      }
    }
  }

  // Reset to the global,  if conditional parameters was not applied
  if (false === is_condition_applied) {
    var saved_variable___days_select_initial = _wpbc.calendar__get_param_value(
      resource_id,
      "saved_variable___days_select_initial"
    );

    // Update real vars
    _wpbc.calendar__set_param_value(
      resource_id,
      "dynamic__days_specific",
      saved_variable___days_select_initial["dynamic__days_specific"]
    );
    _wpbc.calendar__set_param_value(
      resource_id,
      "dynamic__days_min",
      saved_variable___days_select_initial["dynamic__days_min"]
    );
    _wpbc.calendar__set_param_value(
      resource_id,
      "dynamic__days_max",
      saved_variable___days_select_initial["dynamic__days_max"]
    );
    _wpbc.calendar__set_param_value(
      resource_id,
      "fixed__days_num",
      saved_variable___days_select_initial["fixed__days_num"]
    );
  }
}

/**
 * Update dates selection parameters ( START_WEEK_DAY)  depends on  belong current date to  specific   season
 *
 * @param resource_id				int
 * @param selected_first_date		JS date
 * @param is_start_or_end			'start' | 'end'
 * @returns {boolean}
 */
function wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm(
  resource_id,
  selected_first_date,
  is_start_or_end
) {
  if ("start" == is_start_or_end) {
    var class_day = wpbc__get__td_class_date(selected_first_date);
    // var sql_class_date = wpbc__get__sql_class_date( selected_first_date );

    if (
      jQuery(
        "#calendar_booking" +
          resource_id +
          " .datepick-days-cell.cal4date-" +
          class_day
      ).length <= 0
    ) {
      return false; // This date is not exist :( Why ?
    }
    var css_classes_date_arr = jQuery(
      "#calendar_booking" +
        resource_id +
        " .datepick-days-cell.cal4date-" +
        class_day
    )
      .attr("class")
      .split(/\s+/);

    /**
     * START_WEEK_DAY  from  S E A S O N    -    2.0
     *
     *	 		_wpbc.calendar__get_param_value(3,'conditions')['start-day']['season'][0]['for'] = 'september_2023'		<- season name to check
     *	 		_wpbc.calendar__get_param_value(3,'conditions')['start-day']['season'][0]['value']  = '2,5,6'			<- Week Days to  start  selection
     */
    var conditions = _wpbc.calendar__get_param_value(resource_id, "conditions");
    var single_css_date_class;
    var days_arr;
    if (
      null !== conditions &&
      "undefined" !== typeof conditions["start-day"] &&
      "undefined" !== typeof conditions["start-day"]["season"] &&
      conditions["start-day"]["season"].length > 0
    ) {
      for (var i = 0; i < css_classes_date_arr.length; i++) {
        single_css_date_class = css_classes_date_arr[i];

        // S E A S O N    F I L T E R    C O N D I T I O N S     - checking
        if (single_css_date_class.indexOf("wpdevbk_season_") >= 0) {
          single_css_date_class = single_css_date_class.replace(
            "wpdevbk_season_",
            ""
          );

          for (var j = 0; j < conditions["start-day"]["season"].length; j++) {
            if (
              single_css_date_class ===
              conditions["start-day"]["season"][j]["for"]
            ) {
              // Ok season in condition == season in a day

              days_arr =
                conditions["start-day"]["season"][j]["value"].split(",");

              // Update real vars
              _wpbc.calendar__set_param_value(
                resource_id,
                "dynamic__week_days__start",
                days_arr
              );
              _wpbc.calendar__set_param_value(
                resource_id,
                "fixed__week_days__start",
                days_arr
              );
            }
          }
        }
      }
    }
  }

  // START_WEEK_DAY  Back to INITIAL params
  if ("end" == is_start_or_end) {
    var saved_variable___days_select_initial = _wpbc.calendar__get_param_value(
      resource_id,
      "saved_variable___days_select_initial"
    );
    _wpbc.calendar__set_param_value(
      resource_id,
      "dynamic__week_days__start",
      saved_variable___days_select_initial["dynamic__week_days__start"]
    );
    _wpbc.calendar__set_param_value(
      resource_id,
      "fixed__week_days__start",
      saved_variable___days_select_initial["fixed__week_days__start"]
    );
  }
}

/**
 * Calendar Day Cell Bottom  -  Get daily cost for specific date
 *
 * usually  used for showing daily cost in bottom  of calendar date cell
 *
 * @param param_calendar_id         {string}    ID of calendar - booking resource
 * @param my_thisDateTime           {Date}      JavaScript date
 * @returns                         {string}    Cost  - formatted - like  "$ 95.99"
 */
function wpbc_show_day_cost_in_date_bottom(param_calendar_id, my_thisDateTime) {
  var resource_id = parseInt(param_calendar_id.replace("calendar_booking", ""));

  // console.log( _wpbc.bookings_in_calendar__get( resource_id ) );		// for debug

  // 1. Get child booking resources  or single booking resource  that  exist  in dates :	[1] | [1,14,15,17]
  // var child_resources_arr = wpbc_clone_obj( _wpbc.booking__get_param_value( resource_id, 'resources_id_arr__in_dates' ) );

  // '2023-08-21'
  var sql_date = wpbc__get__sql_class_date(new Date(my_thisDateTime));

  var hint__in_day__cost = "";

  var get_for_date_obj = _wpbc.bookings_in_calendar__get_for_date(
    resource_id,
    sql_date
  );

  if (false !== get_for_date_obj) {
    if (
      undefined != get_for_date_obj["summary"] &&
      undefined != get_for_date_obj["summary"].hint__in_day__cost
    ) {
      hint__in_day__cost = get_for_date_obj["summary"].hint__in_day__cost; // "25.00£"
    }
  }

  return hint__in_day__cost;
}

/**
 * Admin Panel - Booking > Settings > Form page -- Delete custom  form
 *
 * @param form_name
 * @param user_id
 * @returns {boolean}
 */
function wpbc_delete_custom_booking_form(form_name, user_id) {
  wpbc_admin_show_message_processing("deleting");

  jQuery.ajax({
    // Start Ajax Sending
    url: wpbc_ajaxurl,
    type: "POST",
    success: function (data, textStatus) {
      if (textStatus == "success") jQuery("#ajax_respond").html(data);
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      window.status = "Ajax sending Error status:" + textStatus;
      alert(XMLHttpRequest.status + " " + XMLHttpRequest.statusText);
      if (XMLHttpRequest.status == 500) {
        alert(
          "Please check at this page according this error:" +
            " https://wpbookingcalendar.com/faq/#ajax-sending-error"
        );
      }
    },
    // beforeSend: someFunction,
    data: {
      action: "DELETE_BK_FORM",
      formname: form_name,
      user_id: user_id,
      wpbc_nonce: document.getElementById("wpbc_admin_panel_nonce").value,
    },
  });
  return false;
}

/**
 * Admin Panel - Booking > Settings > Form page -- Change custom  form
 *
 * @param selectObj
 */
function wpbc_change_custom_booking_form_in_url__and_reload(selectObj) {
  var idx = selectObj.selectedIndex;
  var my_form = selectObj.options[idx].value;

  var loc = location.href;
  if (loc.substr(loc.length - 1, 1) == "#") {
    loc = loc.substr(0, loc.length - 1);
  }

  if (loc.indexOf("booking_form=") == -1) {
    loc = loc + "&booking_form=" + my_form;
  } else {
    // Alredy have this paremeter at URL
    var start = loc.indexOf("&booking_form=");
    var fin = loc.indexOf("&", start + 15);
    if (fin == -1) {
      loc = loc.substr(0, start) + "&booking_form=" + my_form;
    } // at the end of row
    else {
      // at the middle of the row
      var loc1 = loc.substr(0, start) + "&booking_form=" + my_form; //alert(loc)
      loc = loc1 + loc.substr(fin);
    }
  }
  location.href = loc;
}

// TODO: refactor these 3 functions: (2023-09-01):
let AjaxCalculateCostCall = null;
function showCostHintInsideBkForm(bk_type) {
  // if ( ! jQuery( '#calendar_booking' + bk_type ).length )                      //FixIn:6.1.1.16    //FixIn: 8.2.1.13
  //     return  false;

  ////////////////////////////////////////////////////////////////////

  // Disable updating cost hint during first click, if range days selection with  2 mouse clicks is active
  // if ( jQuery('#booking_form_div'+bk_type+' input[type="button"]').prop('disabled' ) ) {
  //     return;
  // }

  ////////////////////////////////////////////////////////////////////

  // submit_bk_color = jQuery('#booking_form_div'+bk_type+' input[type="button"]').css('color');
  // jQuery('#booking_form_div'+bk_type+' input[type="button"]').attr('disabled', 'disabled'); // Disable the submit button
  // jQuery('#booking_form_div'+bk_type+' input[type="button"]').css('color', '#aaa');

  ////////////////////////////////////////////////////////////////////

  if (
    document.getElementById("parent_of_additional_calendar" + bk_type) != null
  ) {
    // Its mean that we get cost hint clicking at additional calendar
    bk_type = document.getElementById(
      "parent_of_additional_calendar" + bk_type
    ).value; // Get parent bk type from additional calendar
  }

  // if (document.getElementById('booking_hint' + bk_type) == null) return false;

  var all_dates = jQuery("#date_booking" + bk_type).val();

  const urlParams = new URLSearchParams(window.location.search);
  const couponParam = urlParams.get("coupon");

  var formdata = getBookingFormElements(bk_type);

  setBookingFormElementsWheelScroll(bk_type);

  var wpbc_loader_icon =
    '<span class="wpbc_ajax_loader"><img style="vertical-align:middle;box-shadow:none;width:14px;" src="' +
    wpdev_bk_plugin_url +
    '/assets/img/ajax-loader.gif"><//span>';
  //var wpbc_loader_icon = '<span class="wpbc_ajax_loader wpdevelop"><span class="wpbc_icn_rotate_left wpbc_spin wpbc_ajax_icon"  aria-hidden="true"><//span><//span>';

  // Calculation in process ...
  jQuery("#booking_hint" + bk_type + ",.booking_hint" + bk_type).html(
    wpbc_loader_icon
  );
  //FixIn: 8.4.2.1
  jQuery(
    "#estimate_booking_day_cost_hint" +
      bk_type +
      ",.estimate_booking_day_cost_hint" +
      bk_type
  ).html(wpbc_loader_icon);
  //FixIn: 8.4.4.7
  jQuery(
    "#estimate_booking_night_cost_hint" +
      bk_type +
      ",.estimate_booking_night_cost_hint" +
      bk_type
  ).html(wpbc_loader_icon);
  jQuery(
    "#additional_booking_hint" + bk_type + ",.additional_booking_hint" + bk_type
  ).html(wpbc_loader_icon);
  jQuery(
    "#original_booking_hint" + bk_type + ",.original_booking_hint" + bk_type
  ).html(wpbc_loader_icon);
  jQuery(
    "#deposit_booking_hint" + bk_type + ",.deposit_booking_hint" + bk_type
  ).html(wpbc_loader_icon);
  jQuery(
    "#coupon_discount_booking_hint" +
      bk_type +
      ",.coupon_discount_booking_hint" +
      bk_type
  ).html(wpbc_loader_icon);
  jQuery(
    "#balance_booking_hint" + bk_type + ",.balance_booking_hint" + bk_type
  ).html(wpbc_loader_icon);

  // Dates and Times shortcodes
  jQuery(
    "#cancel_date_hint_tip" + bk_type + ",.cancel_date_hint_tip" + bk_type
  ).html(wpbc_loader_icon); //FixIn: 9.7.3.16
  jQuery(
    "#check_in_date_hint_tip" + bk_type + ",.check_in_date_hint_tip" + bk_type
  ).html(wpbc_loader_icon);
  jQuery(
    "#check_out_date_hint_tip" + bk_type + ",.check_out_date_hint_tip" + bk_type
  ).html(wpbc_loader_icon);
  jQuery(
    "#check_out_plus1day_hint_tip" +
      bk_type +
      ",.check_out_plus1day_hint_tip" +
      bk_type
  ).html(wpbc_loader_icon); //FixIn: 8.0.2.12

  jQuery(
    "#start_time_hint_tip" + bk_type + ",.start_time_hint_tip" + bk_type
  ).html(wpbc_loader_icon);
  jQuery("#end_time_hint_tip" + bk_type + ",.end_time_hint_tip" + bk_type).html(
    wpbc_loader_icon
  );
  jQuery(
    "#selected_dates_hint_tip" + bk_type + ",.selected_dates_hint_tip" + bk_type
  ).html(wpbc_loader_icon);
  jQuery(
    "#selected_timedates_hint_tip" +
      bk_type +
      ",.selected_timedates_hint_tip" +
      bk_type
  ).html(wpbc_loader_icon);
  jQuery(
    "#selected_short_dates_hint_tip" +
      bk_type +
      ",.selected_short_dates_hint_tip" +
      bk_type
  ).html(wpbc_loader_icon);
  jQuery(
    "#selected_short_timedates_hint_tip" +
      bk_type +
      ",.selected_short_timedates_hint_tip" +
      bk_type
  ).html(wpbc_loader_icon);
  jQuery(
    "#days_number_hint_tip" + bk_type + ",.days_number_hint_tip" + bk_type
  ).html(wpbc_loader_icon);
  jQuery(
    "#nights_number_hint_tip" + bk_type + ",.nights_number_hint_tip" + bk_type
  ).html(wpbc_loader_icon);

  // Check  if calendar exist ( for booking form ONLY shortcode) //FixIn: 8.3.3.11
  if (undefined != document.getElementById("calendar_booking" + bk_type)) {
    // Prevent of showing any hints,  if selected only Check In day if we are using range days selection mode using 2 mouse clicks
    if (bk_days_selection_mode == "dynamic") {
      //FixIn: 5.4.3
      var inst = jQuery.datepick._getInst(
        document.getElementById("calendar_booking" + bk_type)
      );
      if (typeof inst !== "undefined")
        //FixIn: 6.1.1.16

        var is_show_cost_after_first_click; //FixIn: 8.4.2.6
      if (1 == bk_2clicks_mode_days_min) {
        //FixIn: 8.7.6.6
        is_show_cost_after_first_click = true;
      } else {
        is_show_cost_after_first_click = false;
      }

      if (inst.stayOpen == true) {
        // EDIT
        //&& ( ! is_show_cost_after_first_click ) ) {

        // Comment these 2 lines,  if we need to  show cost  hints,  if selected only 1 day
        jQuery(".wpbc_ajax_loader").html("...");
        //jQuery('#selected_short_timedates_hint_tip' + bk_type).html('Please click on check out day to finish days selection');
        return false;
      }
    }
  }

  var my_booking_form = "";
  if (document.getElementById("booking_form_type" + bk_type) != undefined) {
    my_booking_form = document.getElementById(
      "booking_form_type" + bk_type
    ).value;
  }
  var ajax_type_action = "CALCULATE_THE_COST";

  if (AjaxCalculateCostCall) {
    AjaxCalculateCostCall.abort();
  }

  AjaxCalculateCostCall = jQuery.ajax({
    // Start Ajax Sending
    url: wpbc_ajaxurl,
    type: "POST",
    success: function (data, textStatus) {
      if (textStatus == "success") {
        jQuery("#ajax_respond_insert" + bk_type).html(data);
        AjaxCalculateCostCall = null;
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      // EDIT::: fix race condition
      if (XMLHttpRequest.statusText === "abort") return;

      window.status = "Ajax sending Error status:" + textStatus;
      alert(XMLHttpRequest.status + " " + XMLHttpRequest.statusText);
      if (XMLHttpRequest.status == 500) {
        alert(
          "Please check at this page according this error:" +
            " https://wpbookingcalendar.com/faq/#ajax-sending-error"
        );
      }
      AjaxCalculateCostCall = null;
    },
    // beforeSend: someFunction,
    data: {
      action: ajax_type_action,
      form: formdata,
      all_dates: all_dates,
      coupon: couponParam,
      bk_type: bk_type,
      booking_form_type: my_booking_form,
      wpdev_active_locale: wpbc_active_locale,
      wpbc_nonce: document.getElementById(
        "wpbc_nonce" + ajax_type_action + bk_type
      ).value,
    },
  });
  jQuery(".booking_form_div").trigger("show_cost_hints", [bk_type]); //FixIn:7.0.1.53
  jQuery(".booking_form_div").trigger("wpbc_booking_date_or_option_selected", [
    bk_type,
  ]);
  return false;
}

function getBookingFormElements(bk_type) {
  var submit_form = document.getElementById("booking_form" + bk_type);
  var formdata = "";

  if (submit_form != null) {
    var count = submit_form.elements.length;
    var inp_value;
    var element;
    var el_type;
    // Serialize form here
    for (var i = 0; i < count; i++) {
      element = submit_form.elements[i];

      if (
        element.type !== "button" &&
        element.type !== "hidden" &&
        element.name !== "date_booking" + bk_type
      ) {
        // Skip buttons and hidden element - type

        // Get Element Value
        if (element.type == "checkbox") {
          if (element.value == "") {
            inp_value = element.checked;
          } else {
            if (element.checked) inp_value = element.value;
            else inp_value = "";
          }
        } else if (element.type == "radio") {
          if (element.checked) inp_value = element.value;
          else continue;
        } else {
          inp_value = element.value;
        }

        // Get value in selectbox of multiple selection
        if (element.type == "select-multiple") {
          inp_value = jQuery('[name="' + element.name + '"]').val();
          if (inp_value == null || inp_value.toString() == "") inp_value = "";
        }

        if (element.name !== "captcha_input" + bk_type) {
          if (formdata !== "") formdata += "~"; // next field element

          el_type = element.type;
          if (element.className.indexOf("wpdev-validates-as-email") !== -1)
            el_type = "email";
          if (element.className.indexOf("wpdev-validates-as-coupon") !== -1)
            el_type = "coupon";

          formdata += el_type + "^" + element.name + "^" + inp_value; // element attr
        }
      }
    }
  }

  return formdata;
}

function setBookingFormElementsWheelScroll(bk_type) {
  var submit_form = document.getElementById("booking_form" + bk_type);
  var element;
  var i;
  var count;
  var wpbc_loader_icon =
    '<span class="wpbc_ajax_loader"><img style="vertical-align:middle;box-shadow:none;width:14px;" src="' +
    wpdev_bk_plugin_url +
    '/assets/img/ajax-loader.gif"></span>';
  //var wpbc_loader_icon = '<span class="wpbc_ajax_loader wpdevelop"><span class="wpbc_icn_rotate_left wpbc_spin wpbc_ajax_icon"  aria-hidden="true"><//span><//span>';

  if (submit_form != null) {
    count = submit_form.elements.length;

    for (i = 0; i < count; i++) {
      element = submit_form.elements[i];
      // Calculation in process ...
      jQuery("#bookinghint_" + element.id + ",.bookinghint_" + element.id).html(
        wpbc_loader_icon
      );
    }
  }
}
