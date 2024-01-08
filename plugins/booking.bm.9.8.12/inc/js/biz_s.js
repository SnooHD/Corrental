/**
 *  >= Business Small ...
 */

/**
 * Hover dates in calendar, when mouse over
 *
 * @param sql_class_day
 * @param date
 * @param resource_id
 */
function wpbc__calendar__do_days_highlight__bs(
  sql_class_day,
  date,
  resource_id
) {
  var inst = jQuery.datepick._getInst(
    document.getElementById("calendar_booking" + resource_id)
  );

  if ("function" === typeof wpbc__conditions__for_range_days__first_date__bm) {
    wpbc__conditions__for_range_days__first_date__bm(date, resource_id); // Highlight dates based on the conditions
  }

  var td_overs = [];
  var i;
  var start_day_num;
  var working_date;

  // Fixed Days Selection mode - 1 mouse click
  if (
    "fixed" == _wpbc.calendar__get_param_value(resource_id, "days_select_mode")
  ) {
    wpbc_calendars__clear_days_highlighting(); // Clear days highlighting in all  calendar

    // Set START_WEEK_DAY 	depends on season
    if (
      "function" ===
      typeof wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm
    ) {
      wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm(
        resource_id,
        date,
        "start"
      );
    }

    // 1. Find the closest start WeekDay to the hover day
    if (
      -1 !=
      _wpbc.calendar__get_param_value(resource_id, "fixed__week_days__start")
    ) {
      start_day_num = wpbc_get_abs_closest_value_in_arr(
        date.getDay(),
        _wpbc.calendar__get_param_value(resource_id, "fixed__week_days__start")
      );
      date.setDate(date.getDate() - (date.getDay() - start_day_num));

      // Update NUMBER_OF_DAYS_TO_SELECT    depends on    week_day / season
      if (
        "function" ===
        typeof wpbc__conditions__set_NUMBER_OF_DAYS_TO_SELECT__depend_on_date__bm
      ) {
        wpbc__conditions__set_NUMBER_OF_DAYS_TO_SELECT__depend_on_date__bm(
          date,
          resource_id
        );
      }
    }

    // Go back of    START_WEEK_DAY 	to original shortcode
    if (
      "function" ===
      typeof wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm
    ) {
      wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm(
        resource_id,
        date,
        "end"
      );
    }

    // 2. When we are mouseover the date, that selected. Do not highlight it.
    if (wpbc_is_this_day_among_selected_days(date, inst.dates)) {
      return false;
    }

    for (
      i = 0;
      i < _wpbc.calendar__get_param_value(resource_id, "fixed__days_num");
      i++
    ) {
      // Recheck  if all days are available for the booking

      sql_class_day = wpbc__get__sql_class_date(date);

      if (!wpbc_is_this_day_selectable(resource_id, sql_class_day)) {
        // Check if day is selectable
        document.body.style.cursor = "default";
        return false;
      }

      td_overs[td_overs.length] =
        "#calendar_booking" +
        resource_id +
        " .cal4date-" +
        wpbc__get__td_class_date(date);

      date.setDate(date.getDate() + 1); // set next date
    }

    // Highlight days
    for (i = 0; i < td_overs.length; i++) {
      jQuery(td_overs[i]).addClass("datepick-days-cell-over");
    }
    return true;
  } // End Fixed days selection

  // Range Days Selection mode - 2 mouse clicks
  if (
    "dynamic" ==
    _wpbc.calendar__get_param_value(resource_id, "days_select_mode")
  ) {
    wpbc_calendars__clear_days_highlighting(); // Clear days highlighting in all  calendar

    // Highlight days before selection
    if (1 !== inst.dates.length) {
      // Situation, when we do not click 1 time. Not selected at all, or made selection.

      // Get this first mouse over date
      working_date = new Date();
      working_date.setFullYear(
        date.getFullYear(),
        date.getMonth(),
        date.getDate()
      );

      // We are mouseover selected date - do not highlight it
      if (wpbc_is_this_day_among_selected_days(working_date, inst.dates)) {
        return false;
      }

      // Set START_WEEK_DAY 	depends on season
      if (
        "function" ===
        typeof wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm
      ) {
        wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm(
          resource_id,
          date,
          "start"
        );
      }

      if (
        -1 !=
        _wpbc.calendar__get_param_value(
          resource_id,
          "dynamic__week_days__start"
        )
      ) {
        start_day_num = wpbc_get_abs_closest_value_in_arr(
          date.getDay(),
          _wpbc.calendar__get_param_value(
            resource_id,
            "dynamic__week_days__start"
          )
        );
        working_date.setDate(date.getDate() - (date.getDay() - start_day_num));

        // Update NUMBER_OF_DAYS_TO_SELECT    depends on    week_day / season
        if (
          "function" ===
          typeof wpbc__conditions__set_NUMBER_OF_DAYS_TO_SELECT__depend_on_date__bm
        ) {
          wpbc__conditions__set_NUMBER_OF_DAYS_TO_SELECT__depend_on_date__bm(
            working_date,
            resource_id
          );
        }
      }

      // Go back of    START_WEEK_DAY 	to original shortcode
      if (
        "function" ===
        typeof wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm
      ) {
        wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm(
          resource_id,
          date,
          "end"
        );
      }

      i = 0;

      // Get dates to select
      while (
        i < _wpbc.calendar__get_param_value(resource_id, "dynamic__days_min")
      ) {
        i++;
        sql_class_day = wpbc__get__sql_class_date(working_date);
        if (!wpbc_is_this_day_selectable(resource_id, sql_class_day)) {
          // Check if day is selectable
          document.body.style.cursor = "default";
          return false;
        }
        td_overs[td_overs.length] =
          "#calendar_booking" +
          resource_id +
          " .cal4date-" +
          wpbc__get__td_class_date(working_date);

        working_date.setDate(working_date.getDate() + 1); // set next date
      }
    } else {
      // First day clicked in calendar e.g. -- (inst.dates.length == 1)

      /**
       * If we already clicked 1st time on date in calendar, then we need to highlight dates starting from this first selected date,
       * that's why  we start operation  here from  first  selected date,  e.g.:   inst.dates[ 0 ]
       */

      working_date = new Date();
      working_date.setFullYear(
        inst.dates[0].getFullYear(),
        inst.dates[0].getMonth(),
        inst.dates[0].getDate()
      ); // Get "FIRST SELECTED DATE"
      var is_check = true;
      i = 0;

      while (
        is_check ||
        i < _wpbc.calendar__get_param_value(resource_id, "dynamic__days_min")
      ) {
        // Until rich MIN days number.
        i++;
        sql_class_day = wpbc__get__sql_class_date(working_date);

        if (
          !wpbc_is_this_day_selectable(resource_id, sql_class_day) ||
          (i !== 1 &&
            jQuery(".sql_date_" + sql_class_day).hasClass("check_out_time"))
        ) {
          // Check if day is selectable
          document.body.style.cursor = "default";
          return false;
        }

        td_overs[td_overs.length] =
          "#calendar_booking" +
          resource_id +
          " .cal4date-" +
          wpbc__get__td_class_date(working_date);

        // Check  for SEPARATE / DISCRETE DAYS, if we among them
        var is_discrete_ok = true;
        if (
          _wpbc.calendar__get_param_value(resource_id, "dynamic__days_specific")
            .length > 0
        ) {
          // check if we set some discrete dates
          is_discrete_ok = false;
          for (
            var di = 0;
            di <
            _wpbc.calendar__get_param_value(
              resource_id,
              "dynamic__days_specific"
            ).length;
            di++
          ) {
            // check if current number of days inside discrete one

            if (
              i ==
              _wpbc.calendar__get_param_value(
                resource_id,
                "dynamic__days_specific"
              )[di]
            ) {
              is_discrete_ok = true;
              break;
            }
          }
        }

        // Current Hovering date,  which  in [discrete]
        if (
          is_discrete_ok &&
          date.getMonth() == working_date.getMonth() &&
          date.getDate() == working_date.getDate() &&
          date.getFullYear() == working_date.getFullYear()
        ) {
          is_check = false;
        }

        // Inside  [min...max] and in [discrete]
        if (
          is_discrete_ok &&
          working_date > date &&
          i >=
            _wpbc.calendar__get_param_value(resource_id, "dynamic__days_min") &&
          i < _wpbc.calendar__get_param_value(resource_id, "dynamic__days_max")
        ) {
          is_check = false;
        }

        // = Max day
        if (
          i >= _wpbc.calendar__get_param_value(resource_id, "dynamic__days_max")
        ) {
          is_check = false;
        }

        working_date.setDate(working_date.getDate() + 1); // set next date
      }
    }

    // Highlight days
    for (i = 0; i < td_overs.length; i++) {
      jQuery(td_overs[i]).addClass("datepick-days-cell-over");
    }
    return true;
  } // End Range days selection
}

/**
 * Do dates selection in calendar
 *
 * @param selected_dates
 * @param resource_id
 */
function wpbc__calendar__do_days_select__bs(selected_dates, resource_id) {
  if (typeof wpbc__conditions__for_range_days__first_date__bm == "function") {
    wpbc__conditions__for_range_days__first_date__bm(
      selected_dates,
      resource_id
    );
  }

  wpbc__calendar_range_days_select__bs(selected_dates, resource_id);

  /**
   * Conditional showing form elements,     basically  can be SKIPed in REFACTOR,  but better  to  REFACTOR  it too
   *
   * We are need to  get the dates from  the textarea and not from  all_dates variable, because in the range days selection  the dates can be changed
   */
  if (typeof check_condition_sections_in_bkform == "function") {
    check_condition_sections_in_bkform(
      jQuery("#date_booking" + resource_id).val(),
      resource_id
    );
  }

  //TODO:  update it,  because here exist  such  variables: bk_2clicks_mode_days_min
  // if (typeof showCostHintInsideBkForm == "function") {
  //   showCostHintInsideBkForm(resource_id);
  // } //Calculate the cost and show inside of form
}

//TODO: this function  need to  R E F A C T O R  and make it simpler

/**
 * Force range days selection functionality  for FIXED and DYNAMIC days selection
 *
 * @param selected_dates	DYNAMIC :: '19.08.2023 - 19.08.2023' / '19.08.2023 - 27.08.2023'  		FIXED :: '19.08.2023'
 * @param resource_id		'1'
 * @returns {boolean}
 */
function wpbc__calendar_range_days_select__bs(selected_dates, resource_id) {
  if (
    "fixed" !==
      _wpbc.calendar__get_param_value(resource_id, "days_select_mode") &&
    "dynamic" !==
      _wpbc.calendar__get_param_value(resource_id, "days_select_mode")
  ) {
    return false;
  }

  var td_class;
  var sql_class_day;
  var inst = jQuery.datepick._getInst(
    document.getElementById("calendar_booking" + resource_id)
  );
  var internal__days_num__to_select = _wpbc.calendar__get_param_value(
    resource_id,
    "fixed__days_num"
  );

  /**
   * If 2 clicks,  then  dates can  be '19.08.2023 - 19.08.2023'  or "19.08.2023 - 27.08.2023" and then  we need to  check if it's possible to  finish  selection on 27/08/2023
   * If 1 click    then  we have       '19.08.2023',  and after that  we need to  count how many  days to  select
   */

  // Dynamic for 2 mouse clicks ======================================================================================
  if (-1 != selected_dates.indexOf(" - ")) {
    var start_end_date = selected_dates.split(" - ");

    var is_start_same_as_last_day__for_dynamic = true;
    if (
      inst.dates.length > 1 && // inst.date = [ Date('26 Aug.') ]
      "dynamic" ===
        _wpbc.calendar__get_param_value(resource_id, "days_select_mode")
    ) {
      is_start_same_as_last_day__for_dynamic = false;
    }

    var is_only_first_click =
      start_end_date[0] == start_end_date[1] &&
      is_start_same_as_last_day__for_dynamic === true;

    // FIRST_DAY_CLICK  --------------------------------------------------------------------------------------------
    if (is_only_first_click) {
      var start_dynamic_date = start_end_date[0].split(".");
      var date_js__check_in = new Date(
        parseInt(start_dynamic_date[2]),
        parseInt(start_dynamic_date[1]) - 1,
        parseInt(start_dynamic_date[0])
      );

      if (
        typeof wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm ==
        "function"
      ) {
        wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm(
          resource_id,
          date_js__check_in,
          "start"
        );
      }

      // START selection on SPECIFIC WEEK DAYS
      if (
        -1 !=
        _wpbc.calendar__get_param_value(
          resource_id,
          "dynamic__week_days__start"
        )
      ) {
        if (
          date_js__check_in.getDay() !=
          _wpbc.calendar__get_param_value(
            resource_id,
            "dynamic__week_days__start"
          )
        ) {
          var startDay = wpbc_get_abs_closest_value_in_arr(
            date_js__check_in.getDay(),
            _wpbc.calendar__get_param_value(
              resource_id,
              "dynamic__week_days__start"
            )
          );
          date_js__check_in.setDate(
            date_js__check_in.getDate() -
              (date_js__check_in.getDay() - startDay)
          );

          selected_dates = jQuery.datepick._formatDate(inst, date_js__check_in);
          selected_dates += " - " + selected_dates;
          jQuery("#date_booking" + resource_id).val(selected_dates); // Fill the input box

          if (
            typeof wpbc__conditions__for_range_days__first_date__bm ==
            "function"
          ) {
            wpbc__conditions__for_range_days__first_date__bm(
              selected_dates,
              resource_id
            );
          } // Highlight dates based on the conditions

          // Check this day for already booked
          var selceted_first_day = new Date();
          selceted_first_day.setFullYear(
            date_js__check_in.getFullYear(),
            date_js__check_in.getMonth(),
            date_js__check_in.getDate()
          );
          i = 0;
          while (
            i <
            _wpbc.calendar__get_param_value(resource_id, "dynamic__days_min")
          ) {
            sql_class_day = wpbc__get__sql_class_date(selceted_first_day);
            td_class = wpbc__get__td_class_date(selceted_first_day);

            if (!wpbc_is_this_day_selectable(resource_id, sql_class_day)) {
              wpbc_calendar__unselect_all_dates(resource_id); // Unselect all dates and set  properties of Datepick

              return false;
            }

            selceted_first_day.setFullYear(
              selceted_first_day.getFullYear(),
              selceted_first_day.getMonth(),
              selceted_first_day.getDate() + 1
            );
            i++;
          }

          // Selection of the day
          inst.cursorDate.setFullYear(
            date_js__check_in.getFullYear(),
            date_js__check_in.getMonth(),
            date_js__check_in.getDate()
          );
          inst.dates = [inst.cursorDate];
          jQuery.datepick._updateDatepick(inst);
        }
      } else {
        // Set correct date, if only single date is selected, and possible press send button then.

        inst.cursorDate.setFullYear(
          date_js__check_in.getFullYear(),
          date_js__check_in.getMonth(),
          date_js__check_in.getDate()
        );
        inst.dates = [inst.cursorDate];
        jQuery.datepick._updateDatepick(inst);
        jQuery("#date_booking" + resource_id).val(start_end_date[0]);
      }

      if (
        typeof wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm ==
        "function"
      ) {
        wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm(
          resource_id,
          "",
          "end"
        );
      }

      // Disable the submit button
      if (
        _wpbc.calendar__get_param_value(resource_id, "dynamic__days_min") > 1
      ) {
        wpbc_disable_submit_button_in_booking_form(resource_id);
      }
      setTimeout(function () {
        jQuery(
          "#calendar_booking" +
            resource_id +
            " .datepick-unselectable.timespartly.check_out_time" +
            ",#calendar_booking" +
            resource_id +
            " .datepick-unselectable.timespartly.check_in_time"
        ).removeClass("datepick-unselectable");
      }, 500);

      return false;
    }

    // LAST_DAY_CLICK  ---------------------------------------------------------------------------------------------
    if (!is_only_first_click) {
      wpbc_restore_submit_button_in_booking_form(resource_id);

      var start_dynamic_date = start_end_date[0].split(".");
      var date_js__check_in = new Date();
      date_js__check_in.setFullYear(
        start_dynamic_date[2],
        start_dynamic_date[1] - 1,
        start_dynamic_date[0]
      ); // get date

      var end_dynamic_date = start_end_date[1].split(".");
      var real_end_dynamic_date = new Date();
      real_end_dynamic_date.setFullYear(
        end_dynamic_date[2],
        end_dynamic_date[1] - 1,
        end_dynamic_date[0]
      ); // get date

      internal__days_num__to_select = 1; // need to count how many days right now

      var temp_date_for_count = new Date();

      //FixIn: 8.8.2.7
      for (var j1 = 0; j1 < 3 * 365; j1++) {
        temp_date_for_count = new Date();
        temp_date_for_count.setFullYear(
          date_js__check_in.getFullYear(),
          date_js__check_in.getMonth(),
          date_js__check_in.getDate() + j1
        );

        if (
          temp_date_for_count.getFullYear() ==
            real_end_dynamic_date.getFullYear() &&
          temp_date_for_count.getMonth() == real_end_dynamic_date.getMonth() &&
          temp_date_for_count.getDate() == real_end_dynamic_date.getDate()
        ) {
          internal__days_num__to_select = j1;
          j1 = 1000;
        }
      }
      internal__days_num__to_select++;
      selected_dates = start_end_date[0];
      if (
        internal__days_num__to_select <
        _wpbc.calendar__get_param_value(resource_id, "dynamic__days_min")
      ) {
        internal__days_num__to_select = _wpbc.calendar__get_param_value(
          resource_id,
          "dynamic__days_min"
        );
      }

      var is_backward_direction = false;
      if (
        _wpbc.calendar__get_param_value(resource_id, "dynamic__days_specific")
          .length > 0
      ) {
        // check if we set some discreet dates

        var is_discreet_ok = false;
        while (is_discreet_ok === false) {
          for (
            var di = 0;
            di <
            _wpbc.calendar__get_param_value(
              resource_id,
              "dynamic__days_specific"
            ).length;
            di++
          ) {
            // check if current number of days inside of discreet one
            if (
              internal__days_num__to_select ==
                _wpbc.calendar__get_param_value(
                  resource_id,
                  "dynamic__days_specific"
                )[di] &&
              internal__days_num__to_select <=
                _wpbc.calendar__get_param_value(
                  resource_id,
                  "dynamic__days_max"
                )
            ) {
              is_discreet_ok = true;
              di =
                _wpbc.calendar__get_param_value(
                  resource_id,
                  "dynamic__days_specific"
                ).length + 1;
            }
          }
          if (is_backward_direction === false) {
            if (is_discreet_ok === false) {
              internal__days_num__to_select++;
            }
          }

          // BackWard directions, if we set more than maximum days
          if (
            internal__days_num__to_select >=
            _wpbc.calendar__get_param_value(resource_id, "dynamic__days_max")
          ) {
            is_backward_direction = true;
          }

          if (is_backward_direction === true) {
            if (is_discreet_ok === false) {
              internal__days_num__to_select--;
            }
          }

          if (
            internal__days_num__to_select <
            _wpbc.calendar__get_param_value(resource_id, "dynamic__days_min")
          ) {
            is_discreet_ok = true;
          }
        }
      } else {
        if (
          internal__days_num__to_select >
          _wpbc.calendar__get_param_value(resource_id, "dynamic__days_max")
        ) {
          internal__days_num__to_select = _wpbc.calendar__get_param_value(
            resource_id,
            "dynamic__days_max"
          );
        }
      }
    }
  }
  // Dynamic for 2 mouse clicks  E N D  ==============================================================================

  // DO DAYS SELECTION INTENTIONALLY +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

  var temp_saved__days_selection_mode = _wpbc.calendar__get_param_value(
    resource_id,
    "days_select_mode"
  );
  _wpbc.calendar__set_param_value(resource_id, "days_select_mode", "multiple");

  inst.dates = []; // Empty dates in datepicker
  var all_dates_array;
  var date_array;
  var date;
  var date_to_ins;

  // Get array of dates
  if (selected_dates.indexOf(",") == -1) {
    all_dates_array = [selected_dates];
  } else {
    all_dates_array = selected_dates.split(",");
  }

  var original_array = [];
  var isMakeSelection = false;

  // F I X E D
  if ("dynamic" != temp_saved__days_selection_mode) {
    // Gathering original (already selected dates) date array
    for (var j = 0; j < all_dates_array.length; j++) {
      //loop array of dates
      all_dates_array[j] = all_dates_array[j].replace(/(^\s+)|(\s+$)/g, ""); // trim white spaces in date string

      date_array = all_dates_array[j].split("."); // get single date array

      date = new Date();
      date.setFullYear(date_array[2], date_array[1] - 1, date_array[0]); // get date

      if (
        date.getFullYear() == inst.cursorDate.getFullYear() &&
        date.getMonth() == inst.cursorDate.getMonth() &&
        date.getDate() == inst.cursorDate.getDate()
      ) {
        isMakeSelection = true;

        if (
          typeof wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm ==
          "function"
        ) {
          wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm(
            resource_id,
            inst.cursorDate,
            "start"
          );
        }

        if (
          -1 !=
          _wpbc.calendar__get_param_value(
            resource_id,
            "fixed__week_days__start"
          )
        ) {
          var startDay = wpbc_get_abs_closest_value_in_arr(
            inst.cursorDate.getDay(),
            _wpbc.calendar__get_param_value(
              resource_id,
              "fixed__week_days__start"
            )
          );

          inst.cursorDate.setDate(
            inst.cursorDate.getDate() - (inst.cursorDate.getDay() - startDay)
          );

          _wpbc.calendar__set_param_value(
            resource_id,
            "days_select_mode",
            temp_saved__days_selection_mode
          );

          if (
            typeof wpbc__conditions__set_NUMBER_OF_DAYS_TO_SELECT__depend_on_date__bm ==
            "function"
          ) {
            wpbc__conditions__set_NUMBER_OF_DAYS_TO_SELECT__depend_on_date__bm(
              inst.cursorDate,
              resource_id
            );
          } // Highlight dates based on the conditions

          temp_saved__days_selection_mode = _wpbc.calendar__get_param_value(
            resource_id,
            "days_select_mode"
          );
          _wpbc.calendar__set_param_value(
            resource_id,
            "days_select_mode",
            "multiple"
          );

          internal__days_num__to_select = _wpbc.calendar__get_param_value(
            resource_id,
            "fixed__days_num"
          );
        }

        if (
          typeof wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm ==
          "function"
        ) {
          wpbc__conditions__set_START_WEEK_DAY__depend_on_season__bm(
            resource_id,
            inst.cursorDate,
            "end"
          );
        }
      }
    }
  } else {
    isMakeSelection = true; // Dynamic range selection
  }

  var isEmptySelection = false;
  // Fix auto setting cur date when clearing calendar
  var isClearing = inst.clear;
  if (isMakeSelection && !isClearing) {
    var date_start_range = inst.cursorDate;

    if ("dynamic" != temp_saved__days_selection_mode) {
      original_array.push(
        jQuery.datepick._restrictMinMax(
          inst,
          jQuery.datepick._determineDate(inst, inst.cursorDate, null)
        )
      ); //add date
    } else {
      original_array.push(
        jQuery.datepick._restrictMinMax(
          inst,
          jQuery.datepick._determineDate(inst, date_js__check_in, null)
        )
      ); //set 1st date from dynamic range
      date_start_range = date_js__check_in;
    }
    var dates_array = [];
    var range_array = [];
    var td;

    // Add dates to the range array
    for (var i = 1; i < internal__days_num__to_select; i++) {
      dates_array[i] = new Date();

      dates_array[i].setFullYear(
        date_start_range.getFullYear(),
        date_start_range.getMonth(),
        date_start_range.getDate() + i
      );

      td_class =
        dates_array[i].getMonth() +
        1 +
        "-" +
        dates_array[i].getDate() +
        "-" +
        dates_array[i].getFullYear();
      sql_class_day = wpbc__get__sql_class_date(dates_array[i]);

      td = "#calendar_booking" + resource_id + " .cal4date-" + td_class;

      if (
        jQuery(td).hasClass("datepick-unselectable") ||
        (i !== 1 && jQuery(td).hasClass("check_out_time"))
      ) {
        // If we find some unselect option so then make no selection at all in this range
        jQuery(td).removeClass("datepick-current-day");
        isEmptySelection = true;
      }

      //Check if in selection range are reserved days, if so then do not make selection
      if (!wpbc_is_this_day_selectable(resource_id, sql_class_day)) {
        isEmptySelection = true;
      }

      /////////////////////////////////////////////////////////////////////////////////////

      date_to_ins = jQuery.datepick._restrictMinMax(
        inst,
        jQuery.datepick._determineDate(inst, dates_array[i], null)
      );

      range_array.push(date_to_ins);
    }

    // check if some dates are the same in the arrays so the remove them from both
    for (i = 0; i < range_array.length; i++) {
      for (j = 0; j < original_array.length; j++) {
        //loop array of dates

        if (original_array[j] != -1 && range_array[i] != -1)
          if (
            range_array[i].getFullYear() == original_array[j].getFullYear() &&
            range_array[i].getMonth() == original_array[j].getMonth() &&
            range_array[i].getDate() == original_array[j].getDate()
          ) {
            range_array[i] = -1;
            original_array[j] = -1;
          }
      }
    }

    // Add to the dates array
    for (j = 0; j < original_array.length; j++) {
      //loop array of dates
      if (original_array[j] != -1) {
        inst.dates.push(original_array[j]);
      }
    }
    for (i = 0; i < range_array.length; i++) {
      if (range_array[i] != -1) {
        inst.dates.push(range_array[i]);
      }
    }
  }

  if (isClearing) {
    inst.clear = false;
  }

  if (!isEmptySelection) {
    for (j = 0; j < inst.dates.length; j++) {
      sql_class_day = wpbc__get__sql_class_date(inst.dates[j]);
      if (!wpbc_is_this_day_selectable(resource_id, sql_class_day)) {
        isEmptySelection = true;
        break;
      }
    }
  }

  if (isEmptySelection) {
    inst.dates = [];
  }

  if ("dynamic" != temp_saved__days_selection_mode) {
    jQuery.datepick._updateInput("#calendar_booking" + resource_id);
  } else {
    if (isEmptySelection) {
      jQuery.datepick._updateInput("#calendar_booking" + resource_id);
    } else {
      // Dynamic range selections, transform days from jQuery.datepick
      var dateStr =
        inst.dates.length == 0
          ? ""
          : jQuery.datepick._formatDate(inst, inst.dates[0]); // Get first date
      for (i = 1; i < inst.dates.length; i++) {
        dateStr +=
          jQuery.datepick._get(inst, "multiSeparator") +
          jQuery.datepick._formatDate(inst, inst.dates[i]);
      }
      jQuery("#date_booking" + resource_id).val(dateStr); // Fill the input box
    }
  }

  if (
    is_start_same_as_last_day__for_dynamic === false &&
    start_end_date[0] == start_end_date[1]
  ) {
    if (inst.dates.length == 1) {
      inst.dates.push(inst.dates[0]);
    }
  }
  jQuery.datepick._notifyChange(inst);

  // Call  this 'onChangeMonthYear' from  datepicker.
  jQuery.datepick._adjustInstDate(inst);
  jQuery.datepick._showDate(inst); // Update the input field with the current date(s)

  _wpbc.calendar__set_param_value(
    resource_id,
    "days_select_mode",
    temp_saved__days_selection_mode
  );
}

/**
 * Disable submit button  in booking form
 * @param resource_id		resource ID
 */
function wpbc_disable_submit_button_in_booking_form(resource_id) {
  jQuery("#booking_form_div" + resource_id + ' input[type="button"]').attr(
    "disabled",
    "disabled"
  );
  jQuery("#booking_form_div" + resource_id + ' input[type="button"]').attr(
    "saved-button-color",
    jQuery("#booking_form_div" + resource_id + ' input[type="button"]').css(
      "color"
    )
  );
  jQuery("#booking_form_div" + resource_id + ' input[type="button"]').css(
    "color",
    "#aaa"
  );
}

/**
 * Restore disabled submit button  in booking form
 * @param resource_id		resource ID
 */
function wpbc_restore_submit_button_in_booking_form(resource_id) {
  jQuery("#booking_form_div" + resource_id + ' input[type="button"]').prop(
    "disabled",
    false
  );

  if (
    undefined !=
    jQuery("#booking_form_div" + resource_id + ' input[type="button"]').attr(
      "saved-button-color"
    )
  ) {
    //FixIn: 9.5.1.2
    jQuery("#booking_form_div" + resource_id + ' input[type="button"]').css(
      "color",
      jQuery("#booking_form_div" + resource_id + ' input[type="button"]').attr(
        "saved-button-color"
      )
    );
  }
}

/**
 * Tests :
 * 			var inst= wpbc_calendar__get_inst(3); wpbc__calendar_range_days_select__bs('20.08.2023 - 27.08.2023' , 3 );  jQuery.datepick._updateDatepick( inst );
 *
 * 		    var inst= wpbc_calendar__get_inst(3); inst.dates=[]; wpbc__calendar_range_days_select__bs('20.09.2023 - 20.09.2023' , 3 );  jQuery.datepick._updateDatepick( inst );
 */
