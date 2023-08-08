"use strict";
/**
 * Request Object
 * Here we can  define Search parameters and Update it later,  when  some parameter was changed
 *
 */

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

var wpbc_ajx_availability = function (obj, $) {
  // Secure parameters for Ajax	------------------------------------------------------------------------------------
  var p_secure = obj.security_obj = obj.security_obj || {
    user_id: 0,
    nonce: '',
    locale: ''
  };

  obj.set_secure_param = function (param_key, param_val) {
    p_secure[param_key] = param_val;
  };

  obj.get_secure_param = function (param_key) {
    return p_secure[param_key];
  }; // Listing Search parameters	------------------------------------------------------------------------------------


  var p_listing = obj.search_request_obj = obj.search_request_obj || {// sort            : "booking_id",
    // sort_type       : "DESC",
    // page_num        : 1,
    // page_items_count: 10,
    // create_date     : "",
    // keyword         : "",
    // source          : ""
  };

  obj.search_set_all_params = function (request_param_obj) {
    p_listing = request_param_obj;
  };

  obj.search_get_all_params = function () {
    return p_listing;
  };

  obj.search_get_param = function (param_key) {
    return p_listing[param_key];
  };

  obj.search_set_param = function (param_key, param_val) {
    // if ( Array.isArray( param_val ) ){
    // 	param_val = JSON.stringify( param_val );
    // }
    p_listing[param_key] = param_val;
  };

  obj.search_set_params_arr = function (params_arr) {
    _.each(params_arr, function (p_val, p_key, p_data) {
      // Define different Search  parameters for request
      this.search_set_param(p_key, p_val);
    });
  }; // Other parameters 			------------------------------------------------------------------------------------


  var p_other = obj.other_obj = obj.other_obj || {};

  obj.set_other_param = function (param_key, param_val) {
    p_other[param_key] = param_val;
  };

  obj.get_other_param = function (param_key) {
    return p_other[param_key];
  };

  return obj;
}(wpbc_ajx_availability || {}, jQuery);

var wpbc_ajx_bookings = [];
/**
 *   Show Content  ---------------------------------------------------------------------------------------------- */

/**
 * Show Content - Calendar and UI elements
 *
 * @param ajx_data_arr
 * @param ajx_search_params
 * @param ajx_cleaned_params
 */

function wpbc_ajx_availability__page_content__show(ajx_data_arr, ajx_search_params, ajx_cleaned_params) {
  var template__availability_main_page_content = wp.template('wpbc_ajx_availability_main_page_content'); // Content

  jQuery(wpbc_ajx_availability.get_other_param('listing_container')).html(template__availability_main_page_content({
    'ajx_data': ajx_data_arr,
    'ajx_search_params': ajx_search_params,
    // $_REQUEST[ 'search_params' ]
    'ajx_cleaned_params': ajx_cleaned_params
  }));
  jQuery('.wpbc_processing.wpbc_spin').parent().parent().parent().parent('[id^="wpbc_notice_"]').hide(); // Load calendar

  wpbc_ajx_availability__calendar__show({
    'resource_id': ajx_cleaned_params.resource_id,
    'ajx_nonce_calendar': ajx_data_arr.ajx_nonce_calendar,
    'ajx_data_arr': ajx_data_arr,
    'ajx_cleaned_params': ajx_cleaned_params
  });
  /**
   * Trigger for dates selection in the booking form
   *
   * jQuery( wpbc_ajx_availability.get_other_param( 'listing_container' ) ).on('wpbc_page_content_loaded', function(event, ajx_data_arr, ajx_search_params , ajx_cleaned_params) { ... } );
   */

  jQuery(wpbc_ajx_availability.get_other_param('listing_container')).trigger('wpbc_page_content_loaded', [ajx_data_arr, ajx_search_params, ajx_cleaned_params]);
}
/**
 * Show inline month view calendar              with all predefined CSS (sizes and check in/out,  times containers)
 * @param {obj} calendar_params_arr
			{
				'resource_id'       	: ajx_cleaned_params.resource_id,
				'ajx_nonce_calendar'	: ajx_data_arr.ajx_nonce_calendar,
				'ajx_data_arr'          : ajx_data_arr = { ajx_booking_resources:[], booked_dates: {}, resource_unavailable_dates:[], season_availability:{},.... }
				'ajx_cleaned_params'    : {
											calendar__days_selection_mode: "dynamic"
											calendar__start_week_day: "0"
											calendar__timeslot_day_bg_as_available: ""
											calendar__view__cell_height: ""
											calendar__view__months_in_row: 4
											calendar__view__visible_months: 12
											calendar__view__width: "100%"

											dates_availability: "unavailable"
											dates_selection: "2023-03-14 ~ 2023-03-16"
											do_action: "set_availability"
											resource_id: 1
											ui_clicked_element_id: "wpbc_availability_apply_btn"
											ui_usr__availability_selected_toolbar: "info"
								  		 }
			}
*/


function wpbc_ajx_availability__calendar__show(calendar_params_arr) {
  // Update nonce
  jQuery('#ajx_nonce_calendar_section').html(calendar_params_arr.ajx_nonce_calendar); //------------------------------------------------------------------------------------------------------------------
  // Update bookings

  if ('undefined' == typeof wpbc_ajx_bookings[calendar_params_arr.resource_id]) {
    wpbc_ajx_bookings[calendar_params_arr.resource_id] = [];
  }

  wpbc_ajx_bookings[calendar_params_arr.resource_id] = calendar_params_arr['ajx_data_arr']['booked_dates']; //------------------------------------------------------------------------------------------------------------------

  /**
   * Define showing mouse over tooltip on unavailable dates
   * It's defined, when calendar REFRESHED (change months or days selection) loaded in jquery.datepick.wpbc.9.0.js :
   * 		$( 'body' ).trigger( 'wpbc_datepick_inline_calendar_refresh', ...		//FixIn: 9.4.4.13
   */

  jQuery('body').on('wpbc_datepick_inline_calendar_refresh', function (event, resource_id, inst) {
    // inst.dpDiv  it's:  <div class="datepick-inline datepick-multi" style="width: 17712px;">....</div>
    inst.dpDiv.find('.season_unavailable,.before_after_unavailable,.weekdays_unavailable').on('mouseover', function (this_event) {
      // also available these vars: 	resource_id, jCalContainer, inst
      var jCell = jQuery(this_event.currentTarget);
      wpbc_avy__show_tooltip__for_element(jCell, calendar_params_arr['ajx_data_arr']['popover_hints']);
    });
  }); //------------------------------------------------------------------------------------------------------------------

  /**
   * Define height of the calendar  cells, 	and  mouse over tooltips at  some unavailable dates
   * It's defined, when calendar loaded in jquery.datepick.wpbc.9.0.js :
   * 		$( 'body' ).trigger( 'wpbc_datepick_inline_calendar_loaded', ...		//FixIn: 9.4.4.12
   */

  jQuery('body').on('wpbc_datepick_inline_calendar_loaded', function (event, resource_id, jCalContainer, inst) {
    // Remove highlight day for today  date
    jQuery('.datepick-days-cell.datepick-today.datepick-days-cell-over').removeClass('datepick-days-cell-over'); // Set height of calendar  cells if defined this option

    if ('' !== calendar_params_arr.ajx_cleaned_params.calendar__view__cell_height) {
      jQuery('head').append('<style type="text/css">' + '.hasDatepick .datepick-inline .datepick-title-row th, ' + '.hasDatepick .datepick-inline .datepick-days-cell {' + 'height: ' + calendar_params_arr.ajx_cleaned_params.calendar__view__cell_height + ' !important;' + '}' + '</style>');
    } // Define showing mouse over tooltip on unavailable dates


    jCalContainer.find('.season_unavailable,.before_after_unavailable,.weekdays_unavailable').on('mouseover', function (this_event) {
      // also available these vars: 	resource_id, jCalContainer, inst
      var jCell = jQuery(this_event.currentTarget);
      wpbc_avy__show_tooltip__for_element(jCell, calendar_params_arr['ajx_data_arr']['popover_hints']);
    });
  }); //------------------------------------------------------------------------------------------------------------------
  // Define width of entire calendar

  var width = 'width:' + calendar_params_arr.ajx_cleaned_params.calendar__view__width + ';'; // var width = 'width:100%;max-width:100%;';

  if (undefined != calendar_params_arr.ajx_cleaned_params.calendar__view__max_width && '' != calendar_params_arr.ajx_cleaned_params.calendar__view__max_width) {
    width += 'max-width:' + calendar_params_arr.ajx_cleaned_params.calendar__view__max_width + ';';
  } else {
    width += 'max-width:' + calendar_params_arr.ajx_cleaned_params.calendar__view__months_in_row * 284 + 'px;';
  } //------------------------------------------------------------------------------------------------------------------
  // Add calendar container: "Calendar is loading..."  and textarea


  jQuery('.wpbc_ajx_avy__calendar').html('<div class="' + ' bk_calendar_frame' + ' months_num_in_row_' + calendar_params_arr.ajx_cleaned_params.calendar__view__months_in_row + ' cal_month_num_' + calendar_params_arr.ajx_cleaned_params.calendar__view__visible_months + ' ' + calendar_params_arr.ajx_cleaned_params.calendar__timeslot_day_bg_as_available // 'wpbc_timeslot_day_bg_as_available' || ''
  + '" ' + 'style="' + width + '">' + '<div id="calendar_booking' + calendar_params_arr.resource_id + '">' + 'Calendar is loading...' + '</div>' + '</div>' + '<textarea      id="date_booking' + calendar_params_arr.resource_id + '"' + ' name="date_booking' + calendar_params_arr.resource_id + '"' + ' autocomplete="off"' + ' style="display:none;width:100%;height:10em;margin:2em 0 0;"></textarea>'); //------------------------------------------------------------------------------------------------------------------

  var cal_param_arr = {
    'html_id': 'calendar_booking' + calendar_params_arr.ajx_cleaned_params.resource_id,
    'text_id': 'date_booking' + calendar_params_arr.ajx_cleaned_params.resource_id,
    'calendar__start_week_day': calendar_params_arr.ajx_cleaned_params.calendar__start_week_day,
    'calendar__view__visible_months': calendar_params_arr.ajx_cleaned_params.calendar__view__visible_months,
    'calendar__days_selection_mode': calendar_params_arr.ajx_cleaned_params.calendar__days_selection_mode,
    'resource_id': calendar_params_arr.ajx_cleaned_params.resource_id,
    'ajx_nonce_calendar': calendar_params_arr.ajx_data_arr.ajx_nonce_calendar,
    'booked_dates': calendar_params_arr.ajx_data_arr.booked_dates,
    'season_availability': calendar_params_arr.ajx_data_arr.season_availability,
    'resource_unavailable_dates': calendar_params_arr.ajx_data_arr.resource_unavailable_dates,
    'popover_hints': calendar_params_arr['ajx_data_arr']['popover_hints'] // {'season_unavailable':'...','weekdays_unavailable':'...','before_after_unavailable':'...',}

  };
  wpbc_show_inline_booking_calendar(cal_param_arr); //------------------------------------------------------------------------------------------------------------------

  /**
   * On click AVAILABLE |  UNAVAILABLE button  in widget	-	need to  change help dates text
   */

  jQuery('.wpbc_radio__set_days_availability').on('change', function (event, resource_id, inst) {
    wpbc__inline_booking_calendar__on_days_select(jQuery('#' + cal_param_arr.text_id).val(), cal_param_arr);
  }); // Show 	'Select days  in calendar then select Available  /  Unavailable status and click Apply availability button.'

  jQuery('#wpbc_toolbar_dates_hint').html('<div class="ui_element"><span class="wpbc_ui_control wpbc_ui_addon wpbc_help_text" >' + cal_param_arr.popover_hints.toolbar_text + '</span></div>');
}
/**
 * 	Load Datepick Inline calendar
 *
 * @param calendar_params_arr		example:{
											'html_id'           : 'calendar_booking' + calendar_params_arr.ajx_cleaned_params.resource_id,
											'text_id'           : 'date_booking' + calendar_params_arr.ajx_cleaned_params.resource_id,

											'calendar__start_week_day': 	  calendar_params_arr.ajx_cleaned_params.calendar__start_week_day,
											'calendar__view__visible_months': calendar_params_arr.ajx_cleaned_params.calendar__view__visible_months,
											'calendar__days_selection_mode':  calendar_params_arr.ajx_cleaned_params.calendar__days_selection_mode,

											'resource_id'        : calendar_params_arr.ajx_cleaned_params.resource_id,
											'ajx_nonce_calendar' : calendar_params_arr.ajx_data_arr.ajx_nonce_calendar,
											'booked_dates'       : calendar_params_arr.ajx_data_arr.booked_dates,
											'season_availability': calendar_params_arr.ajx_data_arr.season_availability,

											'resource_unavailable_dates' : calendar_params_arr.ajx_data_arr.resource_unavailable_dates
										}
 * @returns {boolean}
 */


function wpbc_show_inline_booking_calendar(calendar_params_arr) {
  if (0 === jQuery('#' + calendar_params_arr.html_id).length // If calendar DOM element not exist then exist
  || true === jQuery('#' + calendar_params_arr.html_id).hasClass('hasDatepick') // If the calendar with the same Booking resource already  has been activated, then exist.
  ) {
    return false;
  } //------------------------------------------------------------------------------------------------------------------
  // Configure and show calendar


  jQuery('#' + calendar_params_arr.html_id).text('');
  jQuery('#' + calendar_params_arr.html_id).datepick({
    beforeShowDay: function beforeShowDay(date) {
      return wpbc__inline_booking_calendar__apply_css_to_days(date, calendar_params_arr, this);
    },
    onSelect: function onSelect(date) {
      jQuery('#' + calendar_params_arr.text_id).val(date); //wpbc_blink_element('.wpbc_widget_available_unavailable', 3, 220);

      return wpbc__inline_booking_calendar__on_days_select(date, calendar_params_arr, this);
    },
    onHover: function onHover(value, date) {
      //wpbc_avy__prepare_tooltip__in_calendar( value, date, calendar_params_arr, this );
      return wpbc__inline_booking_calendar__on_days_hover(value, date, calendar_params_arr, this);
    },
    onChangeMonthYear: null,
    showOn: 'both',
    numberOfMonths: calendar_params_arr.calendar__view__visible_months,
    stepMonths: 1,
    prevText: '&laquo;',
    nextText: '&raquo;',
    dateFormat: 'yy-mm-dd',
    // 'dd.mm.yy',
    changeMonth: false,
    changeYear: false,
    minDate: 0,
    //null,  //Scroll as long as you need
    maxDate: '10y',
    // minDate: new Date(2020, 2, 1), maxDate: new Date(2020, 9, 31), 	// Ability to set any  start and end date in calendar
    showStatus: false,
    closeAtTop: false,
    firstDay: calendar_params_arr.calendar__start_week_day,
    gotoCurrent: false,
    hideIfNoPrevNext: true,
    multiSeparator: ', ',
    multiSelect: 'dynamic' == calendar_params_arr.calendar__days_selection_mode ? 0 : 365,
    // Maximum number of selectable dates:	 Single day = 0,  multi days = 365
    rangeSelect: 'dynamic' == calendar_params_arr.calendar__days_selection_mode,
    rangeSeparator: ' ~ ',
    //' - ',
    // showWeeks: true,
    useThemeRoller: false
  });
  return true;
}
/**
 * Apply CSS to calendar date cells
 *
 * @param date					-  JavaScript Date Obj:  		Mon Dec 11 2023 00:00:00 GMT+0200 (Eastern European Standard Time)
 * @param calendar_params_arr	-  Calendar Settings Object:  	{
																  "html_id": "calendar_booking4",
																  "text_id": "date_booking4",
																  "calendar__start_week_day": 1,
																  "calendar__view__visible_months": 12,
																  "resource_id": 4,
																  "ajx_nonce_calendar": "<input type=\"hidden\" ... />",
																  "booked_dates": {
																	"12-28-2022": [
																	  {
																		"booking_date": "2022-12-28 00:00:00",
																		"approved": "1",
																		"booking_id": "26"
																	  }
																	], ...
																	}
																	'season_availability':{
																		"2023-01-09": true,
																		"2023-01-10": true,
																		"2023-01-11": true, ...
																	}
																  }
																}
 * @param datepick_this			- this of datepick Obj
 *
 * @returns [boolean,string]	- [ {true -available | false - unavailable}, 'CSS classes for calendar day cell' ]
 */


function wpbc__inline_booking_calendar__apply_css_to_days(date, calendar_params_arr, datepick_this) {
  var today_date = new Date(wpbc_today[0], parseInt(wpbc_today[1]) - 1, wpbc_today[2], 0, 0, 0);
  var class_day = date.getMonth() + 1 + '-' + date.getDate() + '-' + date.getFullYear(); // '1-9-2023'

  var sql_class_day = date.getFullYear() + '-';
  sql_class_day += date.getMonth() + 1 < 10 ? '0' : '';
  sql_class_day += date.getMonth() + 1 + '-';
  sql_class_day += date.getDate() < 10 ? '0' : '';
  sql_class_day += date.getDate(); // '2023-01-09'

  var css_date__standard = 'cal4date-' + class_day;
  var css_date__additional = ' wpbc_weekday_' + date.getDay() + ' '; //--------------------------------------------------------------------------------------------------------------
  // WEEKDAYS :: Set unavailable week days from - Settings General page in "Availability" section

  for (var i = 0; i < user_unavilable_days.length; i++) {
    if (date.getDay() == user_unavilable_days[i]) {
      return [!!false, css_date__standard + ' date_user_unavailable' + ' weekdays_unavailable'];
    }
  } // BEFORE_AFTER :: Set unavailable days Before / After the Today date


  if (days_between(date, today_date) < block_some_dates_from_today || typeof wpbc_available_days_num_from_today !== 'undefined' && parseInt('0' + wpbc_available_days_num_from_today) > 0 && days_between(date, today_date) > parseInt('0' + wpbc_available_days_num_from_today)) {
    return [!!false, css_date__standard + ' date_user_unavailable' + ' before_after_unavailable'];
  } // SEASONS ::  					Booking > Resources > Availability page


  var is_date_available = calendar_params_arr.season_availability[sql_class_day];

  if (false === is_date_available) {
    //FixIn: 9.5.4.4
    return [!!false, css_date__standard + ' date_user_unavailable' + ' season_unavailable'];
  } // RESOURCE_UNAVAILABLE ::   	Booking > Availability page


  if (wpdev_in_array(calendar_params_arr.resource_unavailable_dates, sql_class_day)) {
    is_date_available = false;
  }

  if (false === is_date_available) {
    //FixIn: 9.5.4.4
    return [!false, css_date__standard + ' date_user_unavailable' + ' resource_unavailable'];
  } //--------------------------------------------------------------------------------------------------------------


  css_date__additional += wpbc__inline_booking_calendar__days_css__get_rate(class_day, calendar_params_arr.resource_id); // ' rate_100'

  css_date__additional += wpbc__inline_booking_calendar__days_css__get_season_names(class_day, calendar_params_arr.resource_id); // ' weekend_season high_season'
  //--------------------------------------------------------------------------------------------------------------
  // Is any bookings in this date ?

  if ('undefined' !== typeof calendar_params_arr.booked_dates[class_day]) {
    var bookings_in_date = calendar_params_arr.booked_dates[class_day];

    if ('undefined' !== typeof bookings_in_date['sec_0']) {
      // "Full day" booking  -> (seconds == 0)
      css_date__additional += '0' === bookings_in_date['sec_0'].approved ? ' date2approve ' : ' date_approved '; // Pending = '0' |  Approved = '1'

      css_date__additional += ' full_day_booking';
      return [!false, css_date__standard + css_date__additional];
    } else if (Object.keys(bookings_in_date).length > 0) {
      // "Time slots" Bookings
      var is_approved = true;

      _.each(bookings_in_date, function (p_val, p_key, p_data) {
        if (!parseInt(p_val.approved)) {
          is_approved = false;
        }

        var ts = p_val.booking_date.substring(p_val.booking_date.length - 1);

        if (true === is_booking_used_check_in_out_time) {
          if (ts == '1') {
            css_date__additional += ' check_in_time' + (parseInt(p_val.approved) ? ' check_in_time_date_approved' : ' check_in_time_date2approve');
          }

          if (ts == '2') {
            css_date__additional += ' check_out_time' + (parseInt(p_val.approved) ? ' check_out_time_date_approved' : ' check_out_time_date2approve');
          }
        }
      });

      if (!is_approved) {
        css_date__additional += ' date2approve timespartly';
      } else {
        css_date__additional += ' date_approved timespartly';
      }

      if (!is_booking_used_check_in_out_time) {
        css_date__additional += ' times_clock';
      }
    }
  } //--------------------------------------------------------------------------------------------------------------


  return [true, css_date__standard + css_date__additional + ' date_available'];
}
/**
 * Apply some CSS classes, when we mouse over specific dates in calendar
 * @param value
 * @param date					-  JavaScript Date Obj:  		Mon Dec 11 2023 00:00:00 GMT+0200 (Eastern European Standard Time)
 * @param calendar_params_arr	-  Calendar Settings Object:  	{
																  "html_id": "calendar_booking4",
																  "text_id": "date_booking4",
																  "calendar__start_week_day": 1,
																  "calendar__view__visible_months": 12,
																  "resource_id": 4,
																  "ajx_nonce_calendar": "<input type=\"hidden\" ... />",
																  "booked_dates": {
																	"12-28-2022": [
																	  {
																		"booking_date": "2022-12-28 00:00:00",
																		"approved": "1",
																		"booking_id": "26"
																	  }
																	], ...
																	}
																	'season_availability':{
																		"2023-01-09": true,
																		"2023-01-10": true,
																		"2023-01-11": true, ...
																	}
																  }
																}
 * @param datepick_this			- this of datepick Obj
 *
 * @returns {boolean}
 */


function wpbc__inline_booking_calendar__on_days_hover(value, date, calendar_params_arr, datepick_this) {
  if (null === date) {
    jQuery('.datepick-days-cell-over').removeClass('datepick-days-cell-over'); // clear all highlight days selections

    return false;
  }

  var inst = jQuery.datepick._getInst(document.getElementById('calendar_booking' + calendar_params_arr.resource_id));

  if (1 == inst.dates.length // If we have one selected date
  && 'dynamic' === calendar_params_arr.calendar__days_selection_mode // while have range days selection mode
  ) {
    var td_class;
    var td_overs = [];
    var is_check = true;
    var selceted_first_day = new Date();
    selceted_first_day.setFullYear(inst.dates[0].getFullYear(), inst.dates[0].getMonth(), inst.dates[0].getDate()); //Get first Date

    while (is_check) {
      td_class = selceted_first_day.getMonth() + 1 + '-' + selceted_first_day.getDate() + '-' + selceted_first_day.getFullYear();
      td_overs[td_overs.length] = '#calendar_booking' + calendar_params_arr.resource_id + ' .cal4date-' + td_class; // add to array for later make selection by class

      if (date.getMonth() == selceted_first_day.getMonth() && date.getDate() == selceted_first_day.getDate() && date.getFullYear() == selceted_first_day.getFullYear() || selceted_first_day > date) {
        is_check = false;
      }

      selceted_first_day.setFullYear(selceted_first_day.getFullYear(), selceted_first_day.getMonth(), selceted_first_day.getDate() + 1);
    } // Highlight Days


    for (var i = 0; i < td_overs.length; i++) {
      // add class to all elements
      jQuery(td_overs[i]).addClass('datepick-days-cell-over');
    }

    return true;
  }

  return true;
}
/**
 * On DAYs selection in calendar
 *
 * @param dates_selection		-  string:			 '2023-03-07 ~ 2023-03-07' or '2023-04-10, 2023-04-12, 2023-04-02, 2023-04-04'
 * @param calendar_params_arr	-  Calendar Settings Object:  	{
																  "html_id": "calendar_booking4",
																  "text_id": "date_booking4",
																  "calendar__start_week_day": 1,
																  "calendar__view__visible_months": 12,
																  "resource_id": 4,
																  "ajx_nonce_calendar": "<input type=\"hidden\" ... />",
																  "booked_dates": {
																	"12-28-2022": [
																	  {
																		"booking_date": "2022-12-28 00:00:00",
																		"approved": "1",
																		"booking_id": "26"
																	  }
																	], ...
																	}
																	'season_availability':{
																		"2023-01-09": true,
																		"2023-01-10": true,
																		"2023-01-11": true, ...
																	}
																  }
																}
 * @param datepick_this			- this of datepick Obj
 *
 * @returns boolean
 */


function wpbc__inline_booking_calendar__on_days_select(dates_selection, calendar_params_arr) {
  var datepick_this = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;

  var inst = jQuery.datepick._getInst(document.getElementById('calendar_booking' + calendar_params_arr.resource_id));

  var dates_arr = []; //  [ "2023-04-09", "2023-04-10", "2023-04-11" ]

  if (-1 !== dates_selection.indexOf('~')) {
    // Range Days
    dates_arr = wpbc_get_dates_arr__from_dates_range_js({
      'dates_separator': ' ~ ',
      //  ' ~ '
      'dates': dates_selection // '2023-04-04 ~ 2023-04-07'

    });
  } else {
    // Multiple Days
    dates_arr = wpbc_get_dates_arr__from_dates_comma_separated_js({
      'dates_separator': ', ',
      //  ', '
      'dates': dates_selection // '2023-04-10, 2023-04-12, 2023-04-02, 2023-04-04'

    });
  }

  wpbc_avy_after_days_selection__show_help_info({
    'calendar__days_selection_mode': calendar_params_arr.calendar__days_selection_mode,
    'dates_arr': dates_arr,
    'dates_click_num': inst.dates.length,
    'popover_hints': calendar_params_arr.popover_hints
  });
  return true;
}
/**
 * Show help info at the top  toolbar about selected dates and future actions
 *
 * @param params
 * 					Example 1:  {
									calendar__days_selection_mode: "dynamic",
									dates_arr:  [ "2023-04-03" ],
									dates_click_num: 1
									'popover_hints'					: calendar_params_arr.popover_hints
								}
 * 					Example 2:  {
									calendar__days_selection_mode: "dynamic"
									dates_arr: Array(10) [ "2023-04-03", "2023-04-04", "2023-04-05", … ]
									dates_click_num: 2
									'popover_hints'					: calendar_params_arr.popover_hints
								}
 */


function wpbc_avy_after_days_selection__show_help_info(params) {
  // console.log( params );	//		[ "2023-04-09", "2023-04-10", "2023-04-11" ]
  var message, color;

  if (jQuery('#ui_btn_avy__set_days_availability__available').is(':checked')) {
    message = params.popover_hints.toolbar_text_available; //'Set dates _DATES_ as _HTML_ available.';

    color = '#11be4c';
  } else {
    message = params.popover_hints.toolbar_text_unavailable; //'Set dates _DATES_ as _HTML_ unavailable.';

    color = '#e43939';
  }

  message = '<span>' + message + '</span>';
  var first_date = params['dates_arr'][0];
  var last_date = 'dynamic' == params.calendar__days_selection_mode ? params['dates_arr'][params['dates_arr'].length - 1] : params['dates_arr'].length > 1 ? params['dates_arr'][1] : '';
  first_date = jQuery.datepick.formatDate('dd M, yy', new Date(first_date + 'T00:00:00'));
  last_date = jQuery.datepick.formatDate('dd M, yy', new Date(last_date + 'T00:00:00'));

  if ('dynamic' == params.calendar__days_selection_mode) {
    if (1 == params.dates_click_num) {
      last_date = '___________';
    } else {
      if ('first_time' == jQuery('.wpbc_ajx_availability_container').attr('wpbc_loaded')) {
        jQuery('.wpbc_ajx_availability_container').attr('wpbc_loaded', 'done');
        wpbc_blink_element('.wpbc_widget_available_unavailable', 3, 220);
      }
    }

    message = message.replace('_DATES_', '</span>' //+ '<div>' + 'from' + '</div>'
    + '<span class="wpbc_big_date">' + first_date + '</span>' + '<span>' + '-' + '</span>' + '<span class="wpbc_big_date">' + last_date + '</span>' + '<span>');
  } else {
    // if ( params[ 'dates_arr' ].length > 1 ){
    // 	last_date = ', ' + last_date;
    // 	last_date += ( params[ 'dates_arr' ].length > 2 ) ? ', ...' : '';
    // } else {
    // 	last_date='';
    // }
    var dates_arr = [];

    for (var i = 0; i < params['dates_arr'].length; i++) {
      dates_arr.push(jQuery.datepick.formatDate('dd M yy', new Date(params['dates_arr'][i] + 'T00:00:00')));
    }

    first_date = dates_arr.join(', ');
    message = message.replace('_DATES_', '</span>' + '<span class="wpbc_big_date">' + first_date + '</span>' + '<span>');
  }

  message = message.replace('_HTML_', '</span><span class="wpbc_big_text" style="color:' + color + ';">') + '<span>'; //message += ' <div style="margin-left: 1em;">' + ' Click on Apply button to apply availability.' + '</div>';

  message = '<div class="wpbc_toolbar_dates_hints">' + message + '</div>';
  jQuery('.wpbc_help_text').html(message);
}
/**
 *   Parse dates  ------------------------------------------------------------------------------------------- */

/**
 * Get dates array,  from comma separated dates
 *
 * @param params       = {
									* 'dates_separator' => ', ',                                        // Dates separator
									* 'dates'           => '2023-04-04, 2023-04-07, 2023-04-05'         // Dates in 'Y-m-d' format: '2023-01-31'
						 }
 *
 * @return array      = [
									* [0] => 2023-04-04
									* [1] => 2023-04-05
									* [2] => 2023-04-06
									* [3] => 2023-04-07
						]
 *
 * Example #1:  wpbc_get_dates_arr__from_dates_comma_separated_js(  {  'dates_separator' : ', ', 'dates' : '2023-04-04, 2023-04-07, 2023-04-05'  }  );
 */


function wpbc_get_dates_arr__from_dates_comma_separated_js(params) {
  var dates_arr = [];

  if ('' !== params['dates']) {
    dates_arr = params['dates'].split(params['dates_separator']);
    dates_arr.sort();
  }

  return dates_arr;
}
/**
 * Get dates array,  from range days selection
 *
 * @param params       =  {
									* 'dates_separator' => ' ~ ',                         // Dates separator
									* 'dates'           => '2023-04-04 ~ 2023-04-07'      // Dates in 'Y-m-d' format: '2023-01-31'
						  }
 *
 * @return array        = [
									* [0] => 2023-04-04
									* [1] => 2023-04-05
									* [2] => 2023-04-06
									* [3] => 2023-04-07
						  ]
 *
 * Example #1:  wpbc_get_dates_arr__from_dates_range_js(  {  'dates_separator' : ' ~ ', 'dates' : '2023-04-04 ~ 2023-04-07'  }  );
 * Example #2:  wpbc_get_dates_arr__from_dates_range_js(  {  'dates_separator' : ' - ', 'dates' : '2023-04-04 - 2023-04-07'  }  );
 */


function wpbc_get_dates_arr__from_dates_range_js(params) {
  var dates_arr = [];

  if ('' !== params['dates']) {
    dates_arr = params['dates'].split(params['dates_separator']);
    var check_in_date_ymd = dates_arr[0];
    var check_out_date_ymd = dates_arr[1];

    if ('' !== check_in_date_ymd && '' !== check_out_date_ymd) {
      dates_arr = wpbc_get_dates_array_from_start_end_days_js(check_in_date_ymd, check_out_date_ymd);
    }
  }

  return dates_arr;
}
/**
 * Get dates array based on start and end dates.
 *
 * @param string sStartDate - start date: 2023-04-09
 * @param string sEndDate   - end date:   2023-04-11
 * @return array             - [ "2023-04-09", "2023-04-10", "2023-04-11" ]
 */


function wpbc_get_dates_array_from_start_end_days_js(sStartDate, sEndDate) {
  sStartDate = new Date(sStartDate + 'T00:00:00');
  sEndDate = new Date(sEndDate + 'T00:00:00');
  var aDays = []; // Start the variable off with the start date

  aDays.push(sStartDate.getTime()); // Set a 'temp' variable, sCurrentDate, with the start date - before beginning the loop

  var sCurrentDate = new Date(sStartDate.getTime());
  var one_day_duration = 24 * 60 * 60 * 1000; // While the current date is less than the end date

  while (sCurrentDate < sEndDate) {
    // Add a day to the current date "+1 day"
    sCurrentDate.setTime(sCurrentDate.getTime() + one_day_duration); // Add this new day to the aDays array

    aDays.push(sCurrentDate.getTime());
  }

  for (var i = 0; i < aDays.length; i++) {
    aDays[i] = new Date(aDays[i]);
    aDays[i] = aDays[i].getFullYear() + '-' + (aDays[i].getMonth() + 1 < 10 ? '0' : '') + (aDays[i].getMonth() + 1) + '-' + (aDays[i].getDate() < 10 ? '0' : '') + aDays[i].getDate();
  } // Once the loop has finished, return the array of days.


  return aDays;
}
/**
 *   Tooltips  ---------------------------------------------------------------------------------------------- */

/**
 * Define showing tooltip,  when  mouse over on  SELECTABLE (available, pending, approved, resource unavailable),  days
 * Can be called directly  from  datepick init function.
 *
 * @param value
 * @param date
 * @param calendar_params_arr
 * @param datepick_this
 * @returns {boolean}
 */


function wpbc_avy__prepare_tooltip__in_calendar(value, date, calendar_params_arr, datepick_this) {
  if (null == date) {
    return false;
  }

  var td_class = date.getMonth() + 1 + '-' + date.getDate() + '-' + date.getFullYear();
  var jCell = jQuery('#calendar_booking' + calendar_params_arr.resource_id + ' td.cal4date-' + td_class);
  wpbc_avy__show_tooltip__for_element(jCell, calendar_params_arr['popover_hints']);
  return true;
}
/**
 * Define tooltip  for showing on UNAVAILABLE days (season, weekday, today_depends unavailable)
 *
 * @param jCell					jQuery of specific day cell
 * @param popover_hints		    Array with tooltip hint texts	 : {'season_unavailable':'...','weekdays_unavailable':'...','before_after_unavailable':'...',}
 */


function wpbc_avy__show_tooltip__for_element(jCell, popover_hints) {
  var tooltip_time = '';

  if (jCell.hasClass('season_unavailable')) {
    tooltip_time = popover_hints['season_unavailable'];
  } else if (jCell.hasClass('weekdays_unavailable')) {
    tooltip_time = popover_hints['weekdays_unavailable'];
  } else if (jCell.hasClass('before_after_unavailable')) {
    tooltip_time = popover_hints['before_after_unavailable'];
  } else if (jCell.hasClass('date2approve')) {} else if (jCell.hasClass('date_approved')) {} else {}

  jCell.attr('data-content', tooltip_time);
  var td_el = jCell.get(0); //jQuery( '#calendar_booking' + calendar_params_arr.resource_id + ' td.cal4date-' + td_class ).get(0);

  if (undefined == td_el._tippy && '' != tooltip_time) {
    wpbc_tippy(td_el, {
      content: function content(reference) {
        var popover_content = reference.getAttribute('data-content');
        return '<div class="popover popover_tippy">' + '<div class="popover-content">' + popover_content + '</div>' + '</div>';
      },
      allowHTML: true,
      trigger: 'mouseenter focus',
      interactive: !true,
      hideOnClick: true,
      interactiveBorder: 10,
      maxWidth: 550,
      theme: 'wpbc-tippy-times',
      placement: 'top',
      delay: [400, 0],
      //FixIn: 9.4.2.2
      ignoreAttributes: true,
      touch: true,
      //['hold', 500], // 500ms delay			//FixIn: 9.2.1.5
      appendTo: function appendTo() {
        return document.body;
      }
    });
  }
}
/**
 *   Ajax  ------------------------------------------------------------------------------------------------------ */

/**
 * Send Ajax show request
 */


function wpbc_ajx_availability__ajax_request() {
  console.groupCollapsed('WPBC_AJX_AVAILABILITY');
  console.log(' == Before Ajax Send - search_get_all_params() == ', wpbc_ajx_availability.search_get_all_params());
  wpbc_availability_reload_button__spin_start(); // Start Ajax

  jQuery.post(wpbc_global1.wpbc_ajaxurl, {
    action: 'WPBC_AJX_AVAILABILITY',
    wpbc_ajx_user_id: wpbc_ajx_availability.get_secure_param('user_id'),
    nonce: wpbc_ajx_availability.get_secure_param('nonce'),
    wpbc_ajx_locale: wpbc_ajx_availability.get_secure_param('locale'),
    search_params: wpbc_ajx_availability.search_get_all_params()
  },
  /**
   * S u c c e s s
   *
   * @param response_data		-	its object returned from  Ajax - class-live-searcg.php
   * @param textStatus		-	'success'
   * @param jqXHR				-	Object
   */
  function (response_data, textStatus, jqXHR) {
    console.log(' == Response WPBC_AJX_AVAILABILITY == ', response_data);
    console.groupEnd(); // Probably Error

    if (_typeof(response_data) !== 'object' || response_data === null) {
      wpbc_ajx_availability__show_message(response_data);
      return;
    } // Reload page, after filter toolbar has been reset


    if (undefined != response_data['ajx_cleaned_params'] && 'reset_done' === response_data['ajx_cleaned_params']['do_action']) {
      location.reload();
      return;
    } // Show listing


    wpbc_ajx_availability__page_content__show(response_data['ajx_data'], response_data['ajx_search_params'], response_data['ajx_cleaned_params']); //wpbc_ajx_availability__define_ui_hooks();						// Redefine Hooks, because we show new DOM elements

    if ('' != response_data['ajx_data']['ajx_after_action_message'].replace(/\n/g, "<br />")) {
      wpbc_admin_show_message(response_data['ajx_data']['ajx_after_action_message'].replace(/\n/g, "<br />"), '1' == response_data['ajx_data']['ajx_after_action_result'] ? 'success' : 'error', 10000);
    }

    wpbc_availability_reload_button__spin_pause(); // Remove spin icon from  button and Enable this button.

    wpbc_button__remove_spin(response_data['ajx_cleaned_params']['ui_clicked_element_id']);
    jQuery('#ajax_respond').html(response_data); // For ability to show response, add such DIV element to page
  }).fail(function (jqXHR, textStatus, errorThrown) {
    if (window.console && window.console.log) {
      console.log('Ajax_Error', jqXHR, textStatus, errorThrown);
    }

    var error_message = '<strong>' + 'Error!' + '</strong> ' + errorThrown;

    if (jqXHR.status) {
      error_message += ' (<b>' + jqXHR.status + '</b>)';

      if (403 == jqXHR.status) {
        error_message += ' Probably nonce for this page has been expired. Please <a href="javascript:void(0)" onclick="javascript:location.reload();">reload the page</a>.';
      }
    }

    if (jqXHR.responseText) {
      error_message += ' ' + jqXHR.responseText;
    }

    error_message = error_message.replace(/\n/g, "<br />");
    wpbc_ajx_availability__show_message(error_message);
  }) // .done(   function ( data, textStatus, jqXHR ) {   if ( window.console && window.console.log ){ console.log( 'second success', data, textStatus, jqXHR ); }    })
  // .always( function ( data_jqXHR, textStatus, jqXHR_errorThrown ) {   if ( window.console && window.console.log ){ console.log( 'always finished', data_jqXHR, textStatus, jqXHR_errorThrown ); }     })
  ; // End Ajax
}
/**
 *   H o o k s  -  its Action/Times when need to re-Render Views  ----------------------------------------------- */

/**
 * Send Ajax Search Request after Updating search request parameters
 *
 * @param params_arr
 */


function wpbc_ajx_availability__send_request_with_params(params_arr) {
  // Define different Search  parameters for request
  _.each(params_arr, function (p_val, p_key, p_data) {
    //console.log( 'Request for: ', p_key, p_val );
    wpbc_ajx_availability.search_set_param(p_key, p_val);
  }); // Send Ajax Request


  wpbc_ajx_availability__ajax_request();
}
/**
 * Search request for "Page Number"
 * @param page_number	int
 */


function wpbc_ajx_availability__pagination_click(page_number) {
  wpbc_ajx_availability__send_request_with_params({
    'page_num': page_number
  });
}
/**
 *   Show / Hide Content  --------------------------------------------------------------------------------------- */

/**
 *  Show Listing Content 	- 	Sending Ajax Request	-	with parameters that  we early  defined
 */


function wpbc_ajx_availability__actual_content__show() {
  wpbc_ajx_availability__ajax_request(); // Send Ajax Request	-	with parameters that  we early  defined in "wpbc_ajx_booking_listing" Obj.
}
/**
 * Hide Listing Content
 */


function wpbc_ajx_availability__actual_content__hide() {
  jQuery(wpbc_ajx_availability.get_other_param('listing_container')).html('');
}
/**
 *   M e s s a g e  --------------------------------------------------------------------------------------------- */

/**
 * Show just message instead of content
 */


function wpbc_ajx_availability__show_message(message) {
  wpbc_ajx_availability__actual_content__hide();
  jQuery(wpbc_ajx_availability.get_other_param('listing_container')).html('<div class="wpbc-settings-notice notice-warning" style="text-align:left">' + message + '</div>');
}
/**
 *   Support Functions - Spin Icon in Buttons  ------------------------------------------------------------------ */

/**
 * Spin button in Filter toolbar  -  Start
 */


function wpbc_availability_reload_button__spin_start() {
  jQuery('#wpbc_availability_reload_button .menu_icon.wpbc_spin').removeClass('wpbc_animation_pause');
}
/**
 * Spin button in Filter toolbar  -  Pause
 */


function wpbc_availability_reload_button__spin_pause() {
  jQuery('#wpbc_availability_reload_button .menu_icon.wpbc_spin').addClass('wpbc_animation_pause');
}
/**
 * Spin button in Filter toolbar  -  is Spinning ?
 *
 * @returns {boolean}
 */


function wpbc_availability_reload_button__is_spin() {
  if (jQuery('#wpbc_availability_reload_button .menu_icon.wpbc_spin').hasClass('wpbc_animation_pause')) {
    return true;
  } else {
    return false;
  }
}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImluY2x1ZGVzL3BhZ2UtYXZhaWxhYmlsaXR5L19zcmMvYXZhaWxhYmlsaXR5X3BhZ2UuanMiXSwibmFtZXMiOlsid3BiY19hanhfYXZhaWxhYmlsaXR5Iiwib2JqIiwiJCIsInBfc2VjdXJlIiwic2VjdXJpdHlfb2JqIiwidXNlcl9pZCIsIm5vbmNlIiwibG9jYWxlIiwic2V0X3NlY3VyZV9wYXJhbSIsInBhcmFtX2tleSIsInBhcmFtX3ZhbCIsImdldF9zZWN1cmVfcGFyYW0iLCJwX2xpc3RpbmciLCJzZWFyY2hfcmVxdWVzdF9vYmoiLCJzZWFyY2hfc2V0X2FsbF9wYXJhbXMiLCJyZXF1ZXN0X3BhcmFtX29iaiIsInNlYXJjaF9nZXRfYWxsX3BhcmFtcyIsInNlYXJjaF9nZXRfcGFyYW0iLCJzZWFyY2hfc2V0X3BhcmFtIiwic2VhcmNoX3NldF9wYXJhbXNfYXJyIiwicGFyYW1zX2FyciIsIl8iLCJlYWNoIiwicF92YWwiLCJwX2tleSIsInBfZGF0YSIsInBfb3RoZXIiLCJvdGhlcl9vYmoiLCJzZXRfb3RoZXJfcGFyYW0iLCJnZXRfb3RoZXJfcGFyYW0iLCJqUXVlcnkiLCJ3cGJjX2FqeF9ib29raW5ncyIsIndwYmNfYWp4X2F2YWlsYWJpbGl0eV9fcGFnZV9jb250ZW50X19zaG93IiwiYWp4X2RhdGFfYXJyIiwiYWp4X3NlYXJjaF9wYXJhbXMiLCJhanhfY2xlYW5lZF9wYXJhbXMiLCJ0ZW1wbGF0ZV9fYXZhaWxhYmlsaXR5X21haW5fcGFnZV9jb250ZW50Iiwid3AiLCJ0ZW1wbGF0ZSIsImh0bWwiLCJwYXJlbnQiLCJoaWRlIiwid3BiY19hanhfYXZhaWxhYmlsaXR5X19jYWxlbmRhcl9fc2hvdyIsInJlc291cmNlX2lkIiwiYWp4X25vbmNlX2NhbGVuZGFyIiwidHJpZ2dlciIsImNhbGVuZGFyX3BhcmFtc19hcnIiLCJvbiIsImV2ZW50IiwiaW5zdCIsImRwRGl2IiwiZmluZCIsInRoaXNfZXZlbnQiLCJqQ2VsbCIsImN1cnJlbnRUYXJnZXQiLCJ3cGJjX2F2eV9fc2hvd190b29sdGlwX19mb3JfZWxlbWVudCIsImpDYWxDb250YWluZXIiLCJyZW1vdmVDbGFzcyIsImNhbGVuZGFyX192aWV3X19jZWxsX2hlaWdodCIsImFwcGVuZCIsIndpZHRoIiwiY2FsZW5kYXJfX3ZpZXdfX3dpZHRoIiwidW5kZWZpbmVkIiwiY2FsZW5kYXJfX3ZpZXdfX21heF93aWR0aCIsImNhbGVuZGFyX192aWV3X19tb250aHNfaW5fcm93IiwiY2FsZW5kYXJfX3ZpZXdfX3Zpc2libGVfbW9udGhzIiwiY2FsZW5kYXJfX3RpbWVzbG90X2RheV9iZ19hc19hdmFpbGFibGUiLCJjYWxfcGFyYW1fYXJyIiwiY2FsZW5kYXJfX3N0YXJ0X3dlZWtfZGF5IiwiY2FsZW5kYXJfX2RheXNfc2VsZWN0aW9uX21vZGUiLCJib29rZWRfZGF0ZXMiLCJzZWFzb25fYXZhaWxhYmlsaXR5IiwicmVzb3VyY2VfdW5hdmFpbGFibGVfZGF0ZXMiLCJ3cGJjX3Nob3dfaW5saW5lX2Jvb2tpbmdfY2FsZW5kYXIiLCJ3cGJjX19pbmxpbmVfYm9va2luZ19jYWxlbmRhcl9fb25fZGF5c19zZWxlY3QiLCJ0ZXh0X2lkIiwidmFsIiwicG9wb3Zlcl9oaW50cyIsInRvb2xiYXJfdGV4dCIsImh0bWxfaWQiLCJsZW5ndGgiLCJoYXNDbGFzcyIsInRleHQiLCJkYXRlcGljayIsImJlZm9yZVNob3dEYXkiLCJkYXRlIiwid3BiY19faW5saW5lX2Jvb2tpbmdfY2FsZW5kYXJfX2FwcGx5X2Nzc190b19kYXlzIiwib25TZWxlY3QiLCJvbkhvdmVyIiwidmFsdWUiLCJ3cGJjX19pbmxpbmVfYm9va2luZ19jYWxlbmRhcl9fb25fZGF5c19ob3ZlciIsIm9uQ2hhbmdlTW9udGhZZWFyIiwic2hvd09uIiwibnVtYmVyT2ZNb250aHMiLCJzdGVwTW9udGhzIiwicHJldlRleHQiLCJuZXh0VGV4dCIsImRhdGVGb3JtYXQiLCJjaGFuZ2VNb250aCIsImNoYW5nZVllYXIiLCJtaW5EYXRlIiwibWF4RGF0ZSIsInNob3dTdGF0dXMiLCJjbG9zZUF0VG9wIiwiZmlyc3REYXkiLCJnb3RvQ3VycmVudCIsImhpZGVJZk5vUHJldk5leHQiLCJtdWx0aVNlcGFyYXRvciIsIm11bHRpU2VsZWN0IiwicmFuZ2VTZWxlY3QiLCJyYW5nZVNlcGFyYXRvciIsInVzZVRoZW1lUm9sbGVyIiwiZGF0ZXBpY2tfdGhpcyIsInRvZGF5X2RhdGUiLCJEYXRlIiwid3BiY190b2RheSIsInBhcnNlSW50IiwiY2xhc3NfZGF5IiwiZ2V0TW9udGgiLCJnZXREYXRlIiwiZ2V0RnVsbFllYXIiLCJzcWxfY2xhc3NfZGF5IiwiY3NzX2RhdGVfX3N0YW5kYXJkIiwiY3NzX2RhdGVfX2FkZGl0aW9uYWwiLCJnZXREYXkiLCJpIiwidXNlcl91bmF2aWxhYmxlX2RheXMiLCJkYXlzX2JldHdlZW4iLCJibG9ja19zb21lX2RhdGVzX2Zyb21fdG9kYXkiLCJ3cGJjX2F2YWlsYWJsZV9kYXlzX251bV9mcm9tX3RvZGF5IiwiaXNfZGF0ZV9hdmFpbGFibGUiLCJ3cGRldl9pbl9hcnJheSIsIndwYmNfX2lubGluZV9ib29raW5nX2NhbGVuZGFyX19kYXlzX2Nzc19fZ2V0X3JhdGUiLCJ3cGJjX19pbmxpbmVfYm9va2luZ19jYWxlbmRhcl9fZGF5c19jc3NfX2dldF9zZWFzb25fbmFtZXMiLCJib29raW5nc19pbl9kYXRlIiwiYXBwcm92ZWQiLCJPYmplY3QiLCJrZXlzIiwiaXNfYXBwcm92ZWQiLCJ0cyIsImJvb2tpbmdfZGF0ZSIsInN1YnN0cmluZyIsImlzX2Jvb2tpbmdfdXNlZF9jaGVja19pbl9vdXRfdGltZSIsIl9nZXRJbnN0IiwiZG9jdW1lbnQiLCJnZXRFbGVtZW50QnlJZCIsImRhdGVzIiwidGRfY2xhc3MiLCJ0ZF9vdmVycyIsImlzX2NoZWNrIiwic2VsY2V0ZWRfZmlyc3RfZGF5Iiwic2V0RnVsbFllYXIiLCJhZGRDbGFzcyIsImRhdGVzX3NlbGVjdGlvbiIsImRhdGVzX2FyciIsImluZGV4T2YiLCJ3cGJjX2dldF9kYXRlc19hcnJfX2Zyb21fZGF0ZXNfcmFuZ2VfanMiLCJ3cGJjX2dldF9kYXRlc19hcnJfX2Zyb21fZGF0ZXNfY29tbWFfc2VwYXJhdGVkX2pzIiwid3BiY19hdnlfYWZ0ZXJfZGF5c19zZWxlY3Rpb25fX3Nob3dfaGVscF9pbmZvIiwicGFyYW1zIiwibWVzc2FnZSIsImNvbG9yIiwiaXMiLCJ0b29sYmFyX3RleHRfYXZhaWxhYmxlIiwidG9vbGJhcl90ZXh0X3VuYXZhaWxhYmxlIiwiZmlyc3RfZGF0ZSIsImxhc3RfZGF0ZSIsImZvcm1hdERhdGUiLCJkYXRlc19jbGlja19udW0iLCJhdHRyIiwid3BiY19ibGlua19lbGVtZW50IiwicmVwbGFjZSIsInB1c2giLCJqb2luIiwic3BsaXQiLCJzb3J0IiwiY2hlY2tfaW5fZGF0ZV95bWQiLCJjaGVja19vdXRfZGF0ZV95bWQiLCJ3cGJjX2dldF9kYXRlc19hcnJheV9mcm9tX3N0YXJ0X2VuZF9kYXlzX2pzIiwic1N0YXJ0RGF0ZSIsInNFbmREYXRlIiwiYURheXMiLCJnZXRUaW1lIiwic0N1cnJlbnREYXRlIiwib25lX2RheV9kdXJhdGlvbiIsInNldFRpbWUiLCJ3cGJjX2F2eV9fcHJlcGFyZV90b29sdGlwX19pbl9jYWxlbmRhciIsInRvb2x0aXBfdGltZSIsInRkX2VsIiwiZ2V0IiwiX3RpcHB5Iiwid3BiY190aXBweSIsImNvbnRlbnQiLCJyZWZlcmVuY2UiLCJwb3BvdmVyX2NvbnRlbnQiLCJnZXRBdHRyaWJ1dGUiLCJhbGxvd0hUTUwiLCJpbnRlcmFjdGl2ZSIsImhpZGVPbkNsaWNrIiwiaW50ZXJhY3RpdmVCb3JkZXIiLCJtYXhXaWR0aCIsInRoZW1lIiwicGxhY2VtZW50IiwiZGVsYXkiLCJpZ25vcmVBdHRyaWJ1dGVzIiwidG91Y2giLCJhcHBlbmRUbyIsImJvZHkiLCJ3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX2FqYXhfcmVxdWVzdCIsImNvbnNvbGUiLCJncm91cENvbGxhcHNlZCIsImxvZyIsIndwYmNfYXZhaWxhYmlsaXR5X3JlbG9hZF9idXR0b25fX3NwaW5fc3RhcnQiLCJwb3N0Iiwid3BiY19nbG9iYWwxIiwid3BiY19hamF4dXJsIiwiYWN0aW9uIiwid3BiY19hanhfdXNlcl9pZCIsIndwYmNfYWp4X2xvY2FsZSIsInNlYXJjaF9wYXJhbXMiLCJyZXNwb25zZV9kYXRhIiwidGV4dFN0YXR1cyIsImpxWEhSIiwiZ3JvdXBFbmQiLCJ3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX3Nob3dfbWVzc2FnZSIsImxvY2F0aW9uIiwicmVsb2FkIiwid3BiY19hZG1pbl9zaG93X21lc3NhZ2UiLCJ3cGJjX2F2YWlsYWJpbGl0eV9yZWxvYWRfYnV0dG9uX19zcGluX3BhdXNlIiwid3BiY19idXR0b25fX3JlbW92ZV9zcGluIiwiZmFpbCIsImVycm9yVGhyb3duIiwid2luZG93IiwiZXJyb3JfbWVzc2FnZSIsInN0YXR1cyIsInJlc3BvbnNlVGV4dCIsIndwYmNfYWp4X2F2YWlsYWJpbGl0eV9fc2VuZF9yZXF1ZXN0X3dpdGhfcGFyYW1zIiwid3BiY19hanhfYXZhaWxhYmlsaXR5X19wYWdpbmF0aW9uX2NsaWNrIiwicGFnZV9udW1iZXIiLCJ3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX2FjdHVhbF9jb250ZW50X19zaG93Iiwid3BiY19hanhfYXZhaWxhYmlsaXR5X19hY3R1YWxfY29udGVudF9faGlkZSIsIndwYmNfYXZhaWxhYmlsaXR5X3JlbG9hZF9idXR0b25fX2lzX3NwaW4iXSwibWFwcGluZ3MiOiJBQUFBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7OztBQUVBLElBQUlBLHFCQUFxQixHQUFJLFVBQVdDLEdBQVgsRUFBZ0JDLENBQWhCLEVBQW1CO0FBRS9DO0FBQ0EsTUFBSUMsUUFBUSxHQUFHRixHQUFHLENBQUNHLFlBQUosR0FBbUJILEdBQUcsQ0FBQ0csWUFBSixJQUFvQjtBQUN4Q0MsSUFBQUEsT0FBTyxFQUFFLENBRCtCO0FBRXhDQyxJQUFBQSxLQUFLLEVBQUksRUFGK0I7QUFHeENDLElBQUFBLE1BQU0sRUFBRztBQUgrQixHQUF0RDs7QUFNQU4sRUFBQUEsR0FBRyxDQUFDTyxnQkFBSixHQUF1QixVQUFXQyxTQUFYLEVBQXNCQyxTQUF0QixFQUFrQztBQUN4RFAsSUFBQUEsUUFBUSxDQUFFTSxTQUFGLENBQVIsR0FBd0JDLFNBQXhCO0FBQ0EsR0FGRDs7QUFJQVQsRUFBQUEsR0FBRyxDQUFDVSxnQkFBSixHQUF1QixVQUFXRixTQUFYLEVBQXVCO0FBQzdDLFdBQU9OLFFBQVEsQ0FBRU0sU0FBRixDQUFmO0FBQ0EsR0FGRCxDQWIrQyxDQWtCL0M7OztBQUNBLE1BQUlHLFNBQVMsR0FBR1gsR0FBRyxDQUFDWSxrQkFBSixHQUF5QlosR0FBRyxDQUFDWSxrQkFBSixJQUEwQixDQUNsRDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQVBrRCxHQUFuRTs7QUFVQVosRUFBQUEsR0FBRyxDQUFDYSxxQkFBSixHQUE0QixVQUFXQyxpQkFBWCxFQUErQjtBQUMxREgsSUFBQUEsU0FBUyxHQUFHRyxpQkFBWjtBQUNBLEdBRkQ7O0FBSUFkLEVBQUFBLEdBQUcsQ0FBQ2UscUJBQUosR0FBNEIsWUFBWTtBQUN2QyxXQUFPSixTQUFQO0FBQ0EsR0FGRDs7QUFJQVgsRUFBQUEsR0FBRyxDQUFDZ0IsZ0JBQUosR0FBdUIsVUFBV1IsU0FBWCxFQUF1QjtBQUM3QyxXQUFPRyxTQUFTLENBQUVILFNBQUYsQ0FBaEI7QUFDQSxHQUZEOztBQUlBUixFQUFBQSxHQUFHLENBQUNpQixnQkFBSixHQUF1QixVQUFXVCxTQUFYLEVBQXNCQyxTQUF0QixFQUFrQztBQUN4RDtBQUNBO0FBQ0E7QUFDQUUsSUFBQUEsU0FBUyxDQUFFSCxTQUFGLENBQVQsR0FBeUJDLFNBQXpCO0FBQ0EsR0FMRDs7QUFPQVQsRUFBQUEsR0FBRyxDQUFDa0IscUJBQUosR0FBNEIsVUFBVUMsVUFBVixFQUFzQjtBQUNqREMsSUFBQUEsQ0FBQyxDQUFDQyxJQUFGLENBQVFGLFVBQVIsRUFBb0IsVUFBV0csS0FBWCxFQUFrQkMsS0FBbEIsRUFBeUJDLE1BQXpCLEVBQWlDO0FBQWdCO0FBQ3BFLFdBQUtQLGdCQUFMLENBQXVCTSxLQUF2QixFQUE4QkQsS0FBOUI7QUFDQSxLQUZEO0FBR0EsR0FKRCxDQWhEK0MsQ0F1RC9DOzs7QUFDQSxNQUFJRyxPQUFPLEdBQUd6QixHQUFHLENBQUMwQixTQUFKLEdBQWdCMUIsR0FBRyxDQUFDMEIsU0FBSixJQUFpQixFQUEvQzs7QUFFQTFCLEVBQUFBLEdBQUcsQ0FBQzJCLGVBQUosR0FBc0IsVUFBV25CLFNBQVgsRUFBc0JDLFNBQXRCLEVBQWtDO0FBQ3ZEZ0IsSUFBQUEsT0FBTyxDQUFFakIsU0FBRixDQUFQLEdBQXVCQyxTQUF2QjtBQUNBLEdBRkQ7O0FBSUFULEVBQUFBLEdBQUcsQ0FBQzRCLGVBQUosR0FBc0IsVUFBV3BCLFNBQVgsRUFBdUI7QUFDNUMsV0FBT2lCLE9BQU8sQ0FBRWpCLFNBQUYsQ0FBZDtBQUNBLEdBRkQ7O0FBS0EsU0FBT1IsR0FBUDtBQUNBLENBcEU0QixDQW9FMUJELHFCQUFxQixJQUFJLEVBcEVDLEVBb0VHOEIsTUFwRUgsQ0FBN0I7O0FBc0VBLElBQUlDLGlCQUFpQixHQUFHLEVBQXhCO0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFDQSxTQUFTQyx5Q0FBVCxDQUFvREMsWUFBcEQsRUFBa0VDLGlCQUFsRSxFQUFzRkMsa0JBQXRGLEVBQTBHO0FBRXpHLE1BQUlDLHdDQUF3QyxHQUFHQyxFQUFFLENBQUNDLFFBQUgsQ0FBYSx5Q0FBYixDQUEvQyxDQUZ5RyxDQUl6Rzs7QUFDQVIsRUFBQUEsTUFBTSxDQUFFOUIscUJBQXFCLENBQUM2QixlQUF0QixDQUF1QyxtQkFBdkMsQ0FBRixDQUFOLENBQXVFVSxJQUF2RSxDQUE2RUgsd0NBQXdDLENBQUU7QUFDeEcsZ0JBQTBCSCxZQUQ4RTtBQUV4Ryx5QkFBMEJDLGlCQUY4RTtBQUVwRDtBQUNwRCwwQkFBMEJDO0FBSDhFLEdBQUYsQ0FBckg7QUFNQUwsRUFBQUEsTUFBTSxDQUFFLDRCQUFGLENBQU4sQ0FBc0NVLE1BQXRDLEdBQStDQSxNQUEvQyxHQUF3REEsTUFBeEQsR0FBaUVBLE1BQWpFLENBQXlFLHNCQUF6RSxFQUFrR0MsSUFBbEcsR0FYeUcsQ0FZekc7O0FBQ0FDLEVBQUFBLHFDQUFxQyxDQUFFO0FBQzdCLG1CQUFzQlAsa0JBQWtCLENBQUNRLFdBRFo7QUFFN0IsMEJBQXNCVixZQUFZLENBQUNXLGtCQUZOO0FBRzdCLG9CQUEwQlgsWUFIRztBQUk3QiwwQkFBMEJFO0FBSkcsR0FBRixDQUFyQztBQVFBO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7O0FBQ0NMLEVBQUFBLE1BQU0sQ0FBRTlCLHFCQUFxQixDQUFDNkIsZUFBdEIsQ0FBdUMsbUJBQXZDLENBQUYsQ0FBTixDQUF1RWdCLE9BQXZFLENBQWdGLDBCQUFoRixFQUE0RyxDQUFFWixZQUFGLEVBQWdCQyxpQkFBaEIsRUFBb0NDLGtCQUFwQyxDQUE1RztBQUNBO0FBR0Q7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7OztBQUNBLFNBQVNPLHFDQUFULENBQWdESSxtQkFBaEQsRUFBcUU7QUFFcEU7QUFDQWhCLEVBQUFBLE1BQU0sQ0FBRSw2QkFBRixDQUFOLENBQXdDUyxJQUF4QyxDQUE4Q08sbUJBQW1CLENBQUNGLGtCQUFsRSxFQUhvRSxDQUtwRTtBQUNBOztBQUNBLE1BQUssZUFBZSxPQUFRYixpQkFBaUIsQ0FBRWUsbUJBQW1CLENBQUNILFdBQXRCLENBQTdDLEVBQW1GO0FBQUVaLElBQUFBLGlCQUFpQixDQUFFZSxtQkFBbUIsQ0FBQ0gsV0FBdEIsQ0FBakIsR0FBdUQsRUFBdkQ7QUFBNEQ7O0FBQ2pKWixFQUFBQSxpQkFBaUIsQ0FBRWUsbUJBQW1CLENBQUNILFdBQXRCLENBQWpCLEdBQXVERyxtQkFBbUIsQ0FBRSxjQUFGLENBQW5CLENBQXVDLGNBQXZDLENBQXZELENBUm9FLENBV3BFOztBQUNBO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7O0FBQ0NoQixFQUFBQSxNQUFNLENBQUUsTUFBRixDQUFOLENBQWlCaUIsRUFBakIsQ0FBcUIsdUNBQXJCLEVBQThELFVBQVdDLEtBQVgsRUFBa0JMLFdBQWxCLEVBQStCTSxJQUEvQixFQUFxQztBQUNsRztBQUNBQSxJQUFBQSxJQUFJLENBQUNDLEtBQUwsQ0FBV0MsSUFBWCxDQUFpQixxRUFBakIsRUFBeUZKLEVBQXpGLENBQTZGLFdBQTdGLEVBQTBHLFVBQVdLLFVBQVgsRUFBdUI7QUFDaEk7QUFDQSxVQUFJQyxLQUFLLEdBQUd2QixNQUFNLENBQUVzQixVQUFVLENBQUNFLGFBQWIsQ0FBbEI7QUFDQUMsTUFBQUEsbUNBQW1DLENBQUVGLEtBQUYsRUFBU1AsbUJBQW1CLENBQUUsY0FBRixDQUFuQixDQUFzQyxlQUF0QyxDQUFULENBQW5DO0FBQ0EsS0FKRDtBQU1BLEdBUkQsRUFqQm9FLENBMkJwRTs7QUFDQTtBQUNEO0FBQ0E7QUFDQTtBQUNBOztBQUNDaEIsRUFBQUEsTUFBTSxDQUFFLE1BQUYsQ0FBTixDQUFpQmlCLEVBQWpCLENBQXFCLHNDQUFyQixFQUE2RCxVQUFXQyxLQUFYLEVBQWtCTCxXQUFsQixFQUErQmEsYUFBL0IsRUFBOENQLElBQTlDLEVBQW9EO0FBRWhIO0FBQ0FuQixJQUFBQSxNQUFNLENBQUUsNERBQUYsQ0FBTixDQUF1RTJCLFdBQXZFLENBQW9GLHlCQUFwRixFQUhnSCxDQUtoSDs7QUFDQSxRQUFLLE9BQU9YLG1CQUFtQixDQUFDWCxrQkFBcEIsQ0FBdUN1QiwyQkFBbkQsRUFBZ0Y7QUFDL0U1QixNQUFBQSxNQUFNLENBQUUsTUFBRixDQUFOLENBQWlCNkIsTUFBakIsQ0FBeUIsNEJBQ2hCLHdEQURnQixHQUVoQixxREFGZ0IsR0FHZixVQUhlLEdBR0ZiLG1CQUFtQixDQUFDWCxrQkFBcEIsQ0FBdUN1QiwyQkFIckMsR0FHbUUsY0FIbkUsR0FJaEIsR0FKZ0IsR0FLbEIsVUFMUDtBQU1BLEtBYitHLENBZWhIOzs7QUFDQUYsSUFBQUEsYUFBYSxDQUFDTCxJQUFkLENBQW9CLHFFQUFwQixFQUE0RkosRUFBNUYsQ0FBZ0csV0FBaEcsRUFBNkcsVUFBV0ssVUFBWCxFQUF1QjtBQUNuSTtBQUNBLFVBQUlDLEtBQUssR0FBR3ZCLE1BQU0sQ0FBRXNCLFVBQVUsQ0FBQ0UsYUFBYixDQUFsQjtBQUNBQyxNQUFBQSxtQ0FBbUMsQ0FBRUYsS0FBRixFQUFTUCxtQkFBbUIsQ0FBRSxjQUFGLENBQW5CLENBQXNDLGVBQXRDLENBQVQsQ0FBbkM7QUFDQSxLQUpEO0FBS0EsR0FyQkQsRUFqQ29FLENBd0RwRTtBQUNBOztBQUNBLE1BQUljLEtBQUssR0FBSyxXQUFjZCxtQkFBbUIsQ0FBQ1gsa0JBQXBCLENBQXVDMEIscUJBQXJELEdBQTZFLEdBQTNGLENBMURvRSxDQTBEZ0M7O0FBRXBHLE1BQVNDLFNBQVMsSUFBSWhCLG1CQUFtQixDQUFDWCxrQkFBcEIsQ0FBdUM0Qix5QkFBdEQsSUFDRCxNQUFNakIsbUJBQW1CLENBQUNYLGtCQUFwQixDQUF1QzRCLHlCQURuRCxFQUVDO0FBQ0FILElBQUFBLEtBQUssSUFBSSxlQUFnQmQsbUJBQW1CLENBQUNYLGtCQUFwQixDQUF1QzRCLHlCQUF2RCxHQUFtRixHQUE1RjtBQUNBLEdBSkQsTUFJTztBQUNOSCxJQUFBQSxLQUFLLElBQUksZUFBa0JkLG1CQUFtQixDQUFDWCxrQkFBcEIsQ0FBdUM2Qiw2QkFBdkMsR0FBdUUsR0FBekYsR0FBaUcsS0FBMUc7QUFDQSxHQWxFbUUsQ0FvRXBFO0FBQ0E7OztBQUNBbEMsRUFBQUEsTUFBTSxDQUFFLHlCQUFGLENBQU4sQ0FBb0NTLElBQXBDLENBRUMsaUJBQWlCLG9CQUFqQixHQUNNLHFCQUROLEdBQzhCTyxtQkFBbUIsQ0FBQ1gsa0JBQXBCLENBQXVDNkIsNkJBRHJFLEdBRU0saUJBRk4sR0FFMkJsQixtQkFBbUIsQ0FBQ1gsa0JBQXBCLENBQXVDOEIsOEJBRmxFLEdBR00sR0FITixHQUdpQm5CLG1CQUFtQixDQUFDWCxrQkFBcEIsQ0FBdUMrQixzQ0FIeEQsQ0FHbUc7QUFIbkcsSUFJSSxJQUpKLEdBS0csU0FMSCxHQUtlTixLQUxmLEdBS3VCLElBTHZCLEdBT0ksMkJBUEosR0FPa0NkLG1CQUFtQixDQUFDSCxXQVB0RCxHQU9vRSxJQVBwRSxHQU8yRSx3QkFQM0UsR0FPc0csUUFQdEcsR0FTRSxRQVRGLEdBV0UsaUNBWEYsR0FXc0NHLG1CQUFtQixDQUFDSCxXQVgxRCxHQVd3RSxHQVh4RSxHQVlLLHFCQVpMLEdBWTZCRyxtQkFBbUIsQ0FBQ0gsV0FaakQsR0FZK0QsR0FaL0QsR0FhSyxxQkFiTCxHQWNLLDBFQWhCTixFQXRFb0UsQ0F5RnBFOztBQUNBLE1BQUl3QixhQUFhLEdBQUc7QUFDZCxlQUFzQixxQkFBcUJyQixtQkFBbUIsQ0FBQ1gsa0JBQXBCLENBQXVDUSxXQURwRTtBQUVkLGVBQXNCLGlCQUFpQkcsbUJBQW1CLENBQUNYLGtCQUFwQixDQUF1Q1EsV0FGaEU7QUFJZCxnQ0FBK0JHLG1CQUFtQixDQUFDWCxrQkFBcEIsQ0FBdUNpQyx3QkFKeEQ7QUFLZCxzQ0FBa0N0QixtQkFBbUIsQ0FBQ1gsa0JBQXBCLENBQXVDOEIsOEJBTDNEO0FBTWQscUNBQWtDbkIsbUJBQW1CLENBQUNYLGtCQUFwQixDQUF1Q2tDLDZCQU4zRDtBQVFkLG1CQUF1QnZCLG1CQUFtQixDQUFDWCxrQkFBcEIsQ0FBdUNRLFdBUmhEO0FBU2QsMEJBQXVCRyxtQkFBbUIsQ0FBQ2IsWUFBcEIsQ0FBaUNXLGtCQVQxQztBQVVkLG9CQUF1QkUsbUJBQW1CLENBQUNiLFlBQXBCLENBQWlDcUMsWUFWMUM7QUFXZCwyQkFBdUJ4QixtQkFBbUIsQ0FBQ2IsWUFBcEIsQ0FBaUNzQyxtQkFYMUM7QUFhZCxrQ0FBK0J6QixtQkFBbUIsQ0FBQ2IsWUFBcEIsQ0FBaUN1QywwQkFibEQ7QUFlZCxxQkFBaUIxQixtQkFBbUIsQ0FBRSxjQUFGLENBQW5CLENBQXNDLGVBQXRDLENBZkgsQ0FlMkQ7O0FBZjNELEdBQXBCO0FBaUJBMkIsRUFBQUEsaUNBQWlDLENBQUVOLGFBQUYsQ0FBakMsQ0EzR29FLENBNkdwRTs7QUFDQTtBQUNEO0FBQ0E7O0FBQ0NyQyxFQUFBQSxNQUFNLENBQUUsb0NBQUYsQ0FBTixDQUErQ2lCLEVBQS9DLENBQWtELFFBQWxELEVBQTRELFVBQVdDLEtBQVgsRUFBa0JMLFdBQWxCLEVBQStCTSxJQUEvQixFQUFxQztBQUNoR3lCLElBQUFBLDZDQUE2QyxDQUFFNUMsTUFBTSxDQUFFLE1BQU1xQyxhQUFhLENBQUNRLE9BQXRCLENBQU4sQ0FBc0NDLEdBQXRDLEVBQUYsRUFBZ0RULGFBQWhELENBQTdDO0FBQ0EsR0FGRCxFQWpIb0UsQ0FxSHBFOztBQUNBckMsRUFBQUEsTUFBTSxDQUFFLDBCQUFGLENBQU4sQ0FBb0NTLElBQXBDLENBQThDLHlGQUNoQzRCLGFBQWEsQ0FBQ1UsYUFBZCxDQUE0QkMsWUFESSxHQUVqQyxlQUZiO0FBSUE7QUFHRDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7QUFDQSxTQUFTTCxpQ0FBVCxDQUE0QzNCLG1CQUE1QyxFQUFpRTtBQUVoRSxNQUNNLE1BQU1oQixNQUFNLENBQUUsTUFBTWdCLG1CQUFtQixDQUFDaUMsT0FBNUIsQ0FBTixDQUE0Q0MsTUFBcEQsQ0FBbUU7QUFBbkUsS0FDRSxTQUFTbEQsTUFBTSxDQUFFLE1BQU1nQixtQkFBbUIsQ0FBQ2lDLE9BQTVCLENBQU4sQ0FBNENFLFFBQTVDLENBQXNELGFBQXRELENBRmYsQ0FFdUY7QUFGdkYsSUFHQztBQUNFLFdBQU8sS0FBUDtBQUNGLEdBUCtELENBU2hFO0FBQ0E7OztBQUNBbkQsRUFBQUEsTUFBTSxDQUFFLE1BQU1nQixtQkFBbUIsQ0FBQ2lDLE9BQTVCLENBQU4sQ0FBNENHLElBQTVDLENBQWtELEVBQWxEO0FBQ0FwRCxFQUFBQSxNQUFNLENBQUUsTUFBTWdCLG1CQUFtQixDQUFDaUMsT0FBNUIsQ0FBTixDQUE0Q0ksUUFBNUMsQ0FBcUQ7QUFDakRDLElBQUFBLGFBQWEsRUFBRyx1QkFBV0MsSUFBWCxFQUFpQjtBQUM1QixhQUFPQyxnREFBZ0QsQ0FBRUQsSUFBRixFQUFRdkMsbUJBQVIsRUFBNkIsSUFBN0IsQ0FBdkQ7QUFDQSxLQUg0QztBQUlsQ3lDLElBQUFBLFFBQVEsRUFBTSxrQkFBV0YsSUFBWCxFQUFpQjtBQUN6Q3ZELE1BQUFBLE1BQU0sQ0FBRSxNQUFNZ0IsbUJBQW1CLENBQUM2QixPQUE1QixDQUFOLENBQTRDQyxHQUE1QyxDQUFpRFMsSUFBakQsRUFEeUMsQ0FFekM7O0FBQ0EsYUFBT1gsNkNBQTZDLENBQUVXLElBQUYsRUFBUXZDLG1CQUFSLEVBQTZCLElBQTdCLENBQXBEO0FBQ0EsS0FSNEM7QUFTbEMwQyxJQUFBQSxPQUFPLEVBQUksaUJBQVdDLEtBQVgsRUFBa0JKLElBQWxCLEVBQXdCO0FBRTdDO0FBRUEsYUFBT0ssNENBQTRDLENBQUVELEtBQUYsRUFBU0osSUFBVCxFQUFldkMsbUJBQWYsRUFBb0MsSUFBcEMsQ0FBbkQ7QUFDQSxLQWQ0QztBQWVsQzZDLElBQUFBLGlCQUFpQixFQUFFLElBZmU7QUFnQmxDQyxJQUFBQSxNQUFNLEVBQUssTUFoQnVCO0FBaUJsQ0MsSUFBQUEsY0FBYyxFQUFHL0MsbUJBQW1CLENBQUNtQiw4QkFqQkg7QUFrQmxDNkIsSUFBQUEsVUFBVSxFQUFJLENBbEJvQjtBQW1CbENDLElBQUFBLFFBQVEsRUFBSyxTQW5CcUI7QUFvQmxDQyxJQUFBQSxRQUFRLEVBQUssU0FwQnFCO0FBcUJsQ0MsSUFBQUEsVUFBVSxFQUFJLFVBckJvQjtBQXFCVDtBQUN6QkMsSUFBQUEsV0FBVyxFQUFJLEtBdEJtQjtBQXVCbENDLElBQUFBLFVBQVUsRUFBSSxLQXZCb0I7QUF3QmxDQyxJQUFBQSxPQUFPLEVBQVEsQ0F4Qm1CO0FBd0JmO0FBQ2xDQyxJQUFBQSxPQUFPLEVBQU8sS0F6Qm1DO0FBeUI1QjtBQUNOQyxJQUFBQSxVQUFVLEVBQUksS0ExQm9CO0FBMkJsQ0MsSUFBQUEsVUFBVSxFQUFJLEtBM0JvQjtBQTRCbENDLElBQUFBLFFBQVEsRUFBSTFELG1CQUFtQixDQUFDc0Isd0JBNUJFO0FBNkJsQ3FDLElBQUFBLFdBQVcsRUFBSSxLQTdCbUI7QUE4QmxDQyxJQUFBQSxnQkFBZ0IsRUFBRSxJQTlCZ0I7QUErQmxDQyxJQUFBQSxjQUFjLEVBQUcsSUEvQmlCO0FBZ0NqREMsSUFBQUEsV0FBVyxFQUFJLGFBQWE5RCxtQkFBbUIsQ0FBQ3VCLDZCQUFsQyxHQUFtRSxDQUFuRSxHQUF1RSxHQWhDcEM7QUFnQzRDO0FBQzdGd0MsSUFBQUEsV0FBVyxFQUFJLGFBQWEvRCxtQkFBbUIsQ0FBQ3VCLDZCQWpDQztBQWtDakR5QyxJQUFBQSxjQUFjLEVBQUcsS0FsQ2dDO0FBa0NyQjtBQUNiO0FBQ0FDLElBQUFBLGNBQWMsRUFBRztBQXBDaUIsR0FBckQ7QUF3Q0EsU0FBUSxJQUFSO0FBQ0E7QUFHQTtBQUNEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7O0FBQ0MsU0FBU3pCLGdEQUFULENBQTJERCxJQUEzRCxFQUFpRXZDLG1CQUFqRSxFQUFzRmtFLGFBQXRGLEVBQXFHO0FBRXBHLE1BQUlDLFVBQVUsR0FBRyxJQUFJQyxJQUFKLENBQVVDLFVBQVUsQ0FBRSxDQUFGLENBQXBCLEVBQTRCQyxRQUFRLENBQUVELFVBQVUsQ0FBRSxDQUFGLENBQVosQ0FBUixHQUE4QixDQUExRCxFQUE4REEsVUFBVSxDQUFFLENBQUYsQ0FBeEUsRUFBK0UsQ0FBL0UsRUFBa0YsQ0FBbEYsRUFBcUYsQ0FBckYsQ0FBakI7QUFFQSxNQUFJRSxTQUFTLEdBQU1oQyxJQUFJLENBQUNpQyxRQUFMLEtBQWtCLENBQXBCLEdBQTBCLEdBQTFCLEdBQWdDakMsSUFBSSxDQUFDa0MsT0FBTCxFQUFoQyxHQUFpRCxHQUFqRCxHQUF1RGxDLElBQUksQ0FBQ21DLFdBQUwsRUFBeEUsQ0FKb0csQ0FJSDs7QUFDakcsTUFBSUMsYUFBYSxHQUFHcEMsSUFBSSxDQUFDbUMsV0FBTCxLQUFxQixHQUF6QztBQUNDQyxFQUFBQSxhQUFhLElBQU9wQyxJQUFJLENBQUNpQyxRQUFMLEtBQWtCLENBQW5CLEdBQXdCLEVBQTFCLEdBQWlDLEdBQWpDLEdBQXVDLEVBQXhEO0FBQ0FHLEVBQUFBLGFBQWEsSUFBS3BDLElBQUksQ0FBQ2lDLFFBQUwsS0FBa0IsQ0FBbkIsR0FBdUIsR0FBeEM7QUFDQUcsRUFBQUEsYUFBYSxJQUFNcEMsSUFBSSxDQUFDa0MsT0FBTCxLQUFpQixFQUFuQixHQUEwQixHQUExQixHQUFnQyxFQUFqRDtBQUNBRSxFQUFBQSxhQUFhLElBQUlwQyxJQUFJLENBQUNrQyxPQUFMLEVBQWpCLENBVG1HLENBU2hEOztBQUVwRCxNQUFJRyxrQkFBa0IsR0FBTSxjQUFjTCxTQUExQztBQUNBLE1BQUlNLG9CQUFvQixHQUFHLG1CQUFtQnRDLElBQUksQ0FBQ3VDLE1BQUwsRUFBbkIsR0FBbUMsR0FBOUQsQ0Fab0csQ0FjcEc7QUFFQTs7QUFDQSxPQUFNLElBQUlDLENBQUMsR0FBRyxDQUFkLEVBQWlCQSxDQUFDLEdBQUdDLG9CQUFvQixDQUFDOUMsTUFBMUMsRUFBa0Q2QyxDQUFDLEVBQW5ELEVBQXVEO0FBQ3RELFFBQUt4QyxJQUFJLENBQUN1QyxNQUFMLE1BQWlCRSxvQkFBb0IsQ0FBRUQsQ0FBRixDQUExQyxFQUFrRDtBQUNqRCxhQUFPLENBQUUsQ0FBQyxDQUFDLEtBQUosRUFBV0gsa0JBQWtCLEdBQUcsd0JBQXJCLEdBQWlELHVCQUE1RCxDQUFQO0FBQ0E7QUFDRCxHQXJCbUcsQ0F1QnBHOzs7QUFDQSxNQUFTSyxZQUFZLENBQUUxQyxJQUFGLEVBQVE0QixVQUFSLENBQWIsR0FBcUNlLDJCQUF2QyxJQUVDLE9BQVFDLGtDQUFSLEtBQWlELFdBQW5ELElBQ0ViLFFBQVEsQ0FBRSxNQUFNYSxrQ0FBUixDQUFSLEdBQXVELENBRHpELElBRUVGLFlBQVksQ0FBRTFDLElBQUYsRUFBUTRCLFVBQVIsQ0FBWixHQUFtQ0csUUFBUSxDQUFFLE1BQU1hLGtDQUFSLENBSmxELEVBTUM7QUFDQSxXQUFPLENBQUUsQ0FBQyxDQUFDLEtBQUosRUFBV1Asa0JBQWtCLEdBQUcsd0JBQXJCLEdBQWtELDJCQUE3RCxDQUFQO0FBQ0EsR0FoQ21HLENBa0NwRzs7O0FBQ0EsTUFBT1EsaUJBQWlCLEdBQUdwRixtQkFBbUIsQ0FBQ3lCLG1CQUFwQixDQUF5Q2tELGFBQXpDLENBQTNCOztBQUNBLE1BQUssVUFBVVMsaUJBQWYsRUFBa0M7QUFBcUI7QUFDdEQsV0FBTyxDQUFFLENBQUMsQ0FBQyxLQUFKLEVBQVdSLGtCQUFrQixHQUFHLHdCQUFyQixHQUFpRCxxQkFBNUQsQ0FBUDtBQUNBLEdBdENtRyxDQXdDcEc7OztBQUNBLE1BQUtTLGNBQWMsQ0FBQ3JGLG1CQUFtQixDQUFDMEIsMEJBQXJCLEVBQWlEaUQsYUFBakQsQ0FBbkIsRUFBcUY7QUFDcEZTLElBQUFBLGlCQUFpQixHQUFHLEtBQXBCO0FBQ0E7O0FBQ0QsTUFBTSxVQUFVQSxpQkFBaEIsRUFBbUM7QUFBb0I7QUFDdEQsV0FBTyxDQUFFLENBQUMsS0FBSCxFQUFVUixrQkFBa0IsR0FBRyx3QkFBckIsR0FBaUQsdUJBQTNELENBQVA7QUFDQSxHQTlDbUcsQ0FnRHBHOzs7QUFFQUMsRUFBQUEsb0JBQW9CLElBQUlTLGlEQUFpRCxDQUFFZixTQUFGLEVBQWF2RSxtQkFBbUIsQ0FBQ0gsV0FBakMsQ0FBekUsQ0FsRG9HLENBa0RvQzs7QUFDeElnRixFQUFBQSxvQkFBb0IsSUFBSVUseURBQXlELENBQUVoQixTQUFGLEVBQWF2RSxtQkFBbUIsQ0FBQ0gsV0FBakMsQ0FBakYsQ0FuRG9HLENBbURvQztBQUV4STtBQUdBOztBQUNBLE1BQUssZ0JBQWdCLE9BQVFHLG1CQUFtQixDQUFDd0IsWUFBcEIsQ0FBa0MrQyxTQUFsQyxDQUE3QixFQUErRTtBQUU5RSxRQUFJaUIsZ0JBQWdCLEdBQUd4RixtQkFBbUIsQ0FBQ3dCLFlBQXBCLENBQWtDK0MsU0FBbEMsQ0FBdkI7O0FBR0EsUUFBSyxnQkFBZ0IsT0FBUWlCLGdCQUFnQixDQUFFLE9BQUYsQ0FBN0MsRUFBNkQ7QUFBSTtBQUVoRVgsTUFBQUEsb0JBQW9CLElBQU0sUUFBUVcsZ0JBQWdCLENBQUUsT0FBRixDQUFoQixDQUE0QkMsUUFBdEMsR0FBbUQsZ0JBQW5ELEdBQXNFLGlCQUE5RixDQUY0RCxDQUV3RDs7QUFDcEhaLE1BQUFBLG9CQUFvQixJQUFJLG1CQUF4QjtBQUVBLGFBQU8sQ0FBRSxDQUFDLEtBQUgsRUFBVUQsa0JBQWtCLEdBQUdDLG9CQUEvQixDQUFQO0FBRUEsS0FQRCxNQU9PLElBQUthLE1BQU0sQ0FBQ0MsSUFBUCxDQUFhSCxnQkFBYixFQUFnQ3RELE1BQWhDLEdBQXlDLENBQTlDLEVBQWlEO0FBQUs7QUFFNUQsVUFBSTBELFdBQVcsR0FBRyxJQUFsQjs7QUFFQXJILE1BQUFBLENBQUMsQ0FBQ0MsSUFBRixDQUFRZ0gsZ0JBQVIsRUFBMEIsVUFBVy9HLEtBQVgsRUFBa0JDLEtBQWxCLEVBQXlCQyxNQUF6QixFQUFrQztBQUMzRCxZQUFLLENBQUMyRixRQUFRLENBQUU3RixLQUFLLENBQUNnSCxRQUFSLENBQWQsRUFBa0M7QUFDakNHLFVBQUFBLFdBQVcsR0FBRyxLQUFkO0FBQ0E7O0FBQ0QsWUFBSUMsRUFBRSxHQUFHcEgsS0FBSyxDQUFDcUgsWUFBTixDQUFtQkMsU0FBbkIsQ0FBOEJ0SCxLQUFLLENBQUNxSCxZQUFOLENBQW1CNUQsTUFBbkIsR0FBNEIsQ0FBMUQsQ0FBVDs7QUFDQSxZQUFLLFNBQVM4RCxpQ0FBZCxFQUFpRDtBQUNoRCxjQUFLSCxFQUFFLElBQUksR0FBWCxFQUFpQjtBQUFFaEIsWUFBQUEsb0JBQW9CLElBQUksb0JBQXFCUCxRQUFRLENBQUM3RixLQUFLLENBQUNnSCxRQUFQLENBQVQsR0FBNkIsOEJBQTdCLEdBQThELDZCQUFsRixDQUF4QjtBQUEySTs7QUFDOUosY0FBS0ksRUFBRSxJQUFJLEdBQVgsRUFBaUI7QUFBRWhCLFlBQUFBLG9CQUFvQixJQUFJLHFCQUFzQlAsUUFBUSxDQUFDN0YsS0FBSyxDQUFDZ0gsUUFBUCxDQUFULEdBQTZCLCtCQUE3QixHQUErRCw4QkFBcEYsQ0FBeEI7QUFBOEk7QUFDaks7QUFFRCxPQVZEOztBQVlBLFVBQUssQ0FBRUcsV0FBUCxFQUFvQjtBQUNuQmYsUUFBQUEsb0JBQW9CLElBQUksMkJBQXhCO0FBQ0EsT0FGRCxNQUVPO0FBQ05BLFFBQUFBLG9CQUFvQixJQUFJLDRCQUF4QjtBQUNBOztBQUVELFVBQUssQ0FBRW1CLGlDQUFQLEVBQTBDO0FBQ3pDbkIsUUFBQUEsb0JBQW9CLElBQUksY0FBeEI7QUFDQTtBQUVEO0FBRUQsR0FqR21HLENBbUdwRzs7O0FBRUEsU0FBTyxDQUFFLElBQUYsRUFBUUQsa0JBQWtCLEdBQUdDLG9CQUFyQixHQUE0QyxpQkFBcEQsQ0FBUDtBQUNBO0FBR0Q7QUFDRDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7OztBQUNDLFNBQVNqQyw0Q0FBVCxDQUF1REQsS0FBdkQsRUFBOERKLElBQTlELEVBQW9FdkMsbUJBQXBFLEVBQXlGa0UsYUFBekYsRUFBd0c7QUFFdkcsTUFBSyxTQUFTM0IsSUFBZCxFQUFvQjtBQUNuQnZELElBQUFBLE1BQU0sQ0FBRSwwQkFBRixDQUFOLENBQXFDMkIsV0FBckMsQ0FBa0QseUJBQWxELEVBRG1CLENBQ3VGOztBQUMxRyxXQUFPLEtBQVA7QUFDQTs7QUFFRCxNQUFJUixJQUFJLEdBQUduQixNQUFNLENBQUNxRCxRQUFQLENBQWdCNEQsUUFBaEIsQ0FBMEJDLFFBQVEsQ0FBQ0MsY0FBVCxDQUF5QixxQkFBcUJuRyxtQkFBbUIsQ0FBQ0gsV0FBbEUsQ0FBMUIsQ0FBWDs7QUFFQSxNQUNNLEtBQUtNLElBQUksQ0FBQ2lHLEtBQUwsQ0FBV2xFLE1BQWxCLENBQXdDO0FBQXhDLEtBQ0MsY0FBY2xDLG1CQUFtQixDQUFDdUIsNkJBRnZDLENBRTJFO0FBRjNFLElBR0M7QUFFQSxRQUFJOEUsUUFBSjtBQUNBLFFBQUlDLFFBQVEsR0FBRyxFQUFmO0FBQ0EsUUFBSUMsUUFBUSxHQUFHLElBQWY7QUFDUyxRQUFJQyxrQkFBa0IsR0FBRyxJQUFJcEMsSUFBSixFQUF6QjtBQUNBb0MsSUFBQUEsa0JBQWtCLENBQUNDLFdBQW5CLENBQStCdEcsSUFBSSxDQUFDaUcsS0FBTCxDQUFXLENBQVgsRUFBYzFCLFdBQWQsRUFBL0IsRUFBNER2RSxJQUFJLENBQUNpRyxLQUFMLENBQVcsQ0FBWCxFQUFjNUIsUUFBZCxFQUE1RCxFQUF3RnJFLElBQUksQ0FBQ2lHLEtBQUwsQ0FBVyxDQUFYLEVBQWMzQixPQUFkLEVBQXhGLEVBTlQsQ0FNOEg7O0FBRXJILFdBQVE4QixRQUFSLEVBQWtCO0FBRTFCRixNQUFBQSxRQUFRLEdBQUlHLGtCQUFrQixDQUFDaEMsUUFBbkIsS0FBZ0MsQ0FBakMsR0FBc0MsR0FBdEMsR0FBNENnQyxrQkFBa0IsQ0FBQy9CLE9BQW5CLEVBQTVDLEdBQTJFLEdBQTNFLEdBQWlGK0Isa0JBQWtCLENBQUM5QixXQUFuQixFQUE1RjtBQUVBNEIsTUFBQUEsUUFBUSxDQUFFQSxRQUFRLENBQUNwRSxNQUFYLENBQVIsR0FBOEIsc0JBQXNCbEMsbUJBQW1CLENBQUNILFdBQTFDLEdBQXdELGFBQXhELEdBQXdFd0csUUFBdEcsQ0FKMEIsQ0FJbUc7O0FBRWpILFVBQ045RCxJQUFJLENBQUNpQyxRQUFMLE1BQW1CZ0Msa0JBQWtCLENBQUNoQyxRQUFuQixFQUFyQixJQUNpQmpDLElBQUksQ0FBQ2tDLE9BQUwsTUFBa0IrQixrQkFBa0IsQ0FBQy9CLE9BQW5CLEVBRG5DLElBRWlCbEMsSUFBSSxDQUFDbUMsV0FBTCxNQUFzQjhCLGtCQUFrQixDQUFDOUIsV0FBbkIsRUFGMUMsSUFHTzhCLGtCQUFrQixHQUFHakUsSUFKakIsRUFLWDtBQUNBZ0UsUUFBQUEsUUFBUSxHQUFJLEtBQVo7QUFDQTs7QUFFREMsTUFBQUEsa0JBQWtCLENBQUNDLFdBQW5CLENBQWdDRCxrQkFBa0IsQ0FBQzlCLFdBQW5CLEVBQWhDLEVBQW1FOEIsa0JBQWtCLENBQUNoQyxRQUFuQixFQUFuRSxFQUFvR2dDLGtCQUFrQixDQUFDL0IsT0FBbkIsS0FBK0IsQ0FBbkk7QUFDQSxLQXhCRCxDQTBCQTs7O0FBQ0EsU0FBTSxJQUFJTSxDQUFDLEdBQUMsQ0FBWixFQUFlQSxDQUFDLEdBQUd1QixRQUFRLENBQUNwRSxNQUE1QixFQUFxQzZDLENBQUMsRUFBdEMsRUFBMEM7QUFBOEQ7QUFDdkcvRixNQUFBQSxNQUFNLENBQUVzSCxRQUFRLENBQUN2QixDQUFELENBQVYsQ0FBTixDQUFzQjJCLFFBQXRCLENBQStCLHlCQUEvQjtBQUNBOztBQUNELFdBQU8sSUFBUDtBQUVBOztBQUVFLFNBQU8sSUFBUDtBQUNIO0FBR0Q7QUFDRDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7OztBQUNDLFNBQVM5RSw2Q0FBVCxDQUF3RCtFLGVBQXhELEVBQXlFM0csbUJBQXpFLEVBQW9IO0FBQUEsTUFBdEJrRSxhQUFzQix1RUFBTixJQUFNOztBQUVuSCxNQUFJL0QsSUFBSSxHQUFHbkIsTUFBTSxDQUFDcUQsUUFBUCxDQUFnQjRELFFBQWhCLENBQTBCQyxRQUFRLENBQUNDLGNBQVQsQ0FBeUIscUJBQXFCbkcsbUJBQW1CLENBQUNILFdBQWxFLENBQTFCLENBQVg7O0FBRUEsTUFBSStHLFNBQVMsR0FBRyxFQUFoQixDQUptSCxDQUkvRjs7QUFFcEIsTUFBSyxDQUFDLENBQUQsS0FBT0QsZUFBZSxDQUFDRSxPQUFoQixDQUF5QixHQUF6QixDQUFaLEVBQTZDO0FBQXlDO0FBRXJGRCxJQUFBQSxTQUFTLEdBQUdFLHVDQUF1QyxDQUFFO0FBQ3ZDLHlCQUFvQixLQURtQjtBQUNZO0FBQ25ELGVBQW9CSCxlQUZtQixDQUVNOztBQUZOLEtBQUYsQ0FBbkQ7QUFLQSxHQVBELE1BT087QUFBaUY7QUFDdkZDLElBQUFBLFNBQVMsR0FBR0csaURBQWlELENBQUU7QUFDakQseUJBQW9CLElBRDZCO0FBQ0U7QUFDbkQsZUFBb0JKLGVBRjZCLENBRU47O0FBRk0sS0FBRixDQUE3RDtBQUlBOztBQUVESyxFQUFBQSw2Q0FBNkMsQ0FBQztBQUNsQyxxQ0FBaUNoSCxtQkFBbUIsQ0FBQ3VCLDZCQURuQjtBQUVsQyxpQkFBaUNxRixTQUZDO0FBR2xDLHVCQUFpQ3pHLElBQUksQ0FBQ2lHLEtBQUwsQ0FBV2xFLE1BSFY7QUFJbEMscUJBQXNCbEMsbUJBQW1CLENBQUMrQjtBQUpSLEdBQUQsQ0FBN0M7QUFNQSxTQUFPLElBQVA7QUFDQTtBQUVBO0FBQ0Y7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7OztBQUNFLFNBQVNpRiw2Q0FBVCxDQUF3REMsTUFBeEQsRUFBZ0U7QUFDbEU7QUFFRyxNQUFJQyxPQUFKLEVBQWFDLEtBQWI7O0FBQ0EsTUFBSW5JLE1BQU0sQ0FBRSwrQ0FBRixDQUFOLENBQXlEb0ksRUFBekQsQ0FBNEQsVUFBNUQsQ0FBSixFQUE0RTtBQUMxRUYsSUFBQUEsT0FBTyxHQUFHRCxNQUFNLENBQUNsRixhQUFQLENBQXFCc0Ysc0JBQS9CLENBRDBFLENBQ3BCOztBQUN0REYsSUFBQUEsS0FBSyxHQUFHLFNBQVI7QUFDRCxHQUhELE1BR087QUFDTkQsSUFBQUEsT0FBTyxHQUFHRCxNQUFNLENBQUNsRixhQUFQLENBQXFCdUYsd0JBQS9CLENBRE0sQ0FDa0Q7O0FBQ3hESCxJQUFBQSxLQUFLLEdBQUcsU0FBUjtBQUNBOztBQUVERCxFQUFBQSxPQUFPLEdBQUcsV0FBV0EsT0FBWCxHQUFxQixTQUEvQjtBQUVBLE1BQUlLLFVBQVUsR0FBR04sTUFBTSxDQUFFLFdBQUYsQ0FBTixDQUF1QixDQUF2QixDQUFqQjtBQUNBLE1BQUlPLFNBQVMsR0FBTSxhQUFhUCxNQUFNLENBQUMxRiw2QkFBdEIsR0FDWDBGLE1BQU0sQ0FBRSxXQUFGLENBQU4sQ0FBd0JBLE1BQU0sQ0FBRSxXQUFGLENBQU4sQ0FBc0IvRSxNQUF0QixHQUErQixDQUF2RCxDQURXLEdBRVQrRSxNQUFNLENBQUUsV0FBRixDQUFOLENBQXNCL0UsTUFBdEIsR0FBK0IsQ0FBakMsR0FBdUMrRSxNQUFNLENBQUUsV0FBRixDQUFOLENBQXVCLENBQXZCLENBQXZDLEdBQW9FLEVBRjFFO0FBSUFNLEVBQUFBLFVBQVUsR0FBR3ZJLE1BQU0sQ0FBQ3FELFFBQVAsQ0FBZ0JvRixVQUFoQixDQUE0QixVQUE1QixFQUF3QyxJQUFJckQsSUFBSixDQUFVbUQsVUFBVSxHQUFHLFdBQXZCLENBQXhDLENBQWI7QUFDQUMsRUFBQUEsU0FBUyxHQUFHeEksTUFBTSxDQUFDcUQsUUFBUCxDQUFnQm9GLFVBQWhCLENBQTRCLFVBQTVCLEVBQXlDLElBQUlyRCxJQUFKLENBQVVvRCxTQUFTLEdBQUcsV0FBdEIsQ0FBekMsQ0FBWjs7QUFHQSxNQUFLLGFBQWFQLE1BQU0sQ0FBQzFGLDZCQUF6QixFQUF3RDtBQUN2RCxRQUFLLEtBQUswRixNQUFNLENBQUNTLGVBQWpCLEVBQWtDO0FBQ2pDRixNQUFBQSxTQUFTLEdBQUcsYUFBWjtBQUNBLEtBRkQsTUFFTztBQUNOLFVBQUssZ0JBQWdCeEksTUFBTSxDQUFFLGtDQUFGLENBQU4sQ0FBNkMySSxJQUE3QyxDQUFtRCxhQUFuRCxDQUFyQixFQUF5RjtBQUN4RjNJLFFBQUFBLE1BQU0sQ0FBRSxrQ0FBRixDQUFOLENBQTZDMkksSUFBN0MsQ0FBbUQsYUFBbkQsRUFBa0UsTUFBbEU7QUFDQUMsUUFBQUEsa0JBQWtCLENBQUUsb0NBQUYsRUFBd0MsQ0FBeEMsRUFBMkMsR0FBM0MsQ0FBbEI7QUFDQTtBQUNEOztBQUNEVixJQUFBQSxPQUFPLEdBQUdBLE9BQU8sQ0FBQ1csT0FBUixDQUFpQixTQUFqQixFQUErQixVQUMvQjtBQUQrQixNQUU3Qiw4QkFGNkIsR0FFSU4sVUFGSixHQUVpQixTQUZqQixHQUc3QixRQUg2QixHQUdsQixHQUhrQixHQUdaLFNBSFksR0FJN0IsOEJBSjZCLEdBSUlDLFNBSkosR0FJZ0IsU0FKaEIsR0FLN0IsUUFMRixDQUFWO0FBTUEsR0FmRCxNQWVPO0FBQ047QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsUUFBSVosU0FBUyxHQUFHLEVBQWhCOztBQUNBLFNBQUssSUFBSTdCLENBQUMsR0FBRyxDQUFiLEVBQWdCQSxDQUFDLEdBQUdrQyxNQUFNLENBQUUsV0FBRixDQUFOLENBQXNCL0UsTUFBMUMsRUFBa0Q2QyxDQUFDLEVBQW5ELEVBQXVEO0FBQ3RENkIsTUFBQUEsU0FBUyxDQUFDa0IsSUFBVixDQUFpQjlJLE1BQU0sQ0FBQ3FELFFBQVAsQ0FBZ0JvRixVQUFoQixDQUE0QixTQUE1QixFQUF3QyxJQUFJckQsSUFBSixDQUFVNkMsTUFBTSxDQUFFLFdBQUYsQ0FBTixDQUF1QmxDLENBQXZCLElBQTZCLFdBQXZDLENBQXhDLENBQWpCO0FBQ0E7O0FBQ0R3QyxJQUFBQSxVQUFVLEdBQUdYLFNBQVMsQ0FBQ21CLElBQVYsQ0FBZ0IsSUFBaEIsQ0FBYjtBQUNBYixJQUFBQSxPQUFPLEdBQUdBLE9BQU8sQ0FBQ1csT0FBUixDQUFpQixTQUFqQixFQUErQixZQUM3Qiw4QkFENkIsR0FDSU4sVUFESixHQUNpQixTQURqQixHQUU3QixRQUZGLENBQVY7QUFHQTs7QUFDREwsRUFBQUEsT0FBTyxHQUFHQSxPQUFPLENBQUNXLE9BQVIsQ0FBaUIsUUFBakIsRUFBNEIscURBQW1EVixLQUFuRCxHQUF5RCxLQUFyRixJQUE4RixRQUF4RyxDQXREK0QsQ0F3RC9EOztBQUVBRCxFQUFBQSxPQUFPLEdBQUcsMkNBQTJDQSxPQUEzQyxHQUFxRCxRQUEvRDtBQUVBbEksRUFBQUEsTUFBTSxDQUFFLGlCQUFGLENBQU4sQ0FBNEJTLElBQTVCLENBQWtDeUgsT0FBbEM7QUFDQTtBQUVGO0FBQ0Q7O0FBRUU7QUFDRjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7O0FBQ0UsU0FBU0gsaURBQVQsQ0FBNERFLE1BQTVELEVBQW9FO0FBRW5FLE1BQUlMLFNBQVMsR0FBRyxFQUFoQjs7QUFFQSxNQUFLLE9BQU9LLE1BQU0sQ0FBRSxPQUFGLENBQWxCLEVBQStCO0FBRTlCTCxJQUFBQSxTQUFTLEdBQUdLLE1BQU0sQ0FBRSxPQUFGLENBQU4sQ0FBa0JlLEtBQWxCLENBQXlCZixNQUFNLENBQUUsaUJBQUYsQ0FBL0IsQ0FBWjtBQUVBTCxJQUFBQSxTQUFTLENBQUNxQixJQUFWO0FBQ0E7O0FBQ0QsU0FBT3JCLFNBQVA7QUFDQTtBQUVEO0FBQ0Y7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7O0FBQ0UsU0FBU0UsdUNBQVQsQ0FBa0RHLE1BQWxELEVBQTBEO0FBRXpELE1BQUlMLFNBQVMsR0FBRyxFQUFoQjs7QUFFQSxNQUFLLE9BQU9LLE1BQU0sQ0FBQyxPQUFELENBQWxCLEVBQThCO0FBRTdCTCxJQUFBQSxTQUFTLEdBQUdLLE1BQU0sQ0FBRSxPQUFGLENBQU4sQ0FBa0JlLEtBQWxCLENBQXlCZixNQUFNLENBQUUsaUJBQUYsQ0FBL0IsQ0FBWjtBQUNBLFFBQUlpQixpQkFBaUIsR0FBSXRCLFNBQVMsQ0FBQyxDQUFELENBQWxDO0FBQ0EsUUFBSXVCLGtCQUFrQixHQUFHdkIsU0FBUyxDQUFDLENBQUQsQ0FBbEM7O0FBRUEsUUFBTSxPQUFPc0IsaUJBQVIsSUFBK0IsT0FBT0Msa0JBQTNDLEVBQWdFO0FBRS9EdkIsTUFBQUEsU0FBUyxHQUFHd0IsMkNBQTJDLENBQUVGLGlCQUFGLEVBQXFCQyxrQkFBckIsQ0FBdkQ7QUFDQTtBQUNEOztBQUNELFNBQU92QixTQUFQO0FBQ0E7QUFFQTtBQUNIO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7O0FBQ0csU0FBU3dCLDJDQUFULENBQXNEQyxVQUF0RCxFQUFrRUMsUUFBbEUsRUFBNEU7QUFFM0VELEVBQUFBLFVBQVUsR0FBRyxJQUFJakUsSUFBSixDQUFVaUUsVUFBVSxHQUFHLFdBQXZCLENBQWI7QUFDQUMsRUFBQUEsUUFBUSxHQUFHLElBQUlsRSxJQUFKLENBQVVrRSxRQUFRLEdBQUcsV0FBckIsQ0FBWDtBQUVBLE1BQUlDLEtBQUssR0FBQyxFQUFWLENBTDJFLENBTzNFOztBQUNBQSxFQUFBQSxLQUFLLENBQUNULElBQU4sQ0FBWU8sVUFBVSxDQUFDRyxPQUFYLEVBQVosRUFSMkUsQ0FVM0U7O0FBQ0EsTUFBSUMsWUFBWSxHQUFHLElBQUlyRSxJQUFKLENBQVVpRSxVQUFVLENBQUNHLE9BQVgsRUFBVixDQUFuQjtBQUNBLE1BQUlFLGdCQUFnQixHQUFHLEtBQUcsRUFBSCxHQUFNLEVBQU4sR0FBUyxJQUFoQyxDQVoyRSxDQWMzRTs7QUFDQSxTQUFNRCxZQUFZLEdBQUdILFFBQXJCLEVBQThCO0FBQzdCO0FBQ0FHLElBQUFBLFlBQVksQ0FBQ0UsT0FBYixDQUFzQkYsWUFBWSxDQUFDRCxPQUFiLEtBQXlCRSxnQkFBL0MsRUFGNkIsQ0FJN0I7O0FBQ0FILElBQUFBLEtBQUssQ0FBQ1QsSUFBTixDQUFZVyxZQUFZLENBQUNELE9BQWIsRUFBWjtBQUNBOztBQUVELE9BQUssSUFBSXpELENBQUMsR0FBRyxDQUFiLEVBQWdCQSxDQUFDLEdBQUd3RCxLQUFLLENBQUNyRyxNQUExQixFQUFrQzZDLENBQUMsRUFBbkMsRUFBdUM7QUFDdEN3RCxJQUFBQSxLQUFLLENBQUV4RCxDQUFGLENBQUwsR0FBYSxJQUFJWCxJQUFKLENBQVVtRSxLQUFLLENBQUN4RCxDQUFELENBQWYsQ0FBYjtBQUNBd0QsSUFBQUEsS0FBSyxDQUFFeEQsQ0FBRixDQUFMLEdBQWF3RCxLQUFLLENBQUV4RCxDQUFGLENBQUwsQ0FBV0wsV0FBWCxLQUNSLEdBRFEsSUFDRTZELEtBQUssQ0FBRXhELENBQUYsQ0FBTCxDQUFXUCxRQUFYLEtBQXdCLENBQXpCLEdBQThCLEVBQWhDLEdBQXNDLEdBQXRDLEdBQTRDLEVBRDNDLEtBQ2tEK0QsS0FBSyxDQUFFeEQsQ0FBRixDQUFMLENBQVdQLFFBQVgsS0FBd0IsQ0FEMUUsSUFFUixHQUZRLElBRVErRCxLQUFLLENBQUV4RCxDQUFGLENBQUwsQ0FBV04sT0FBWCxLQUF1QixFQUFoQyxHQUFzQyxHQUF0QyxHQUE0QyxFQUYzQyxJQUVrRDhELEtBQUssQ0FBRXhELENBQUYsQ0FBTCxDQUFXTixPQUFYLEVBRi9EO0FBR0EsR0E1QjBFLENBNkIzRTs7O0FBQ0EsU0FBTzhELEtBQVA7QUFDQTtBQUlIO0FBQ0Q7O0FBRUM7QUFDRDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7OztBQUNDLFNBQVNLLHNDQUFULENBQWlEakcsS0FBakQsRUFBd0RKLElBQXhELEVBQThEdkMsbUJBQTlELEVBQW1Ga0UsYUFBbkYsRUFBa0c7QUFFakcsTUFBSyxRQUFRM0IsSUFBYixFQUFtQjtBQUFHLFdBQU8sS0FBUDtBQUFnQjs7QUFFdEMsTUFBSThELFFBQVEsR0FBSzlELElBQUksQ0FBQ2lDLFFBQUwsS0FBa0IsQ0FBcEIsR0FBMEIsR0FBMUIsR0FBZ0NqQyxJQUFJLENBQUNrQyxPQUFMLEVBQWhDLEdBQWlELEdBQWpELEdBQXVEbEMsSUFBSSxDQUFDbUMsV0FBTCxFQUF0RTtBQUVBLE1BQUluRSxLQUFLLEdBQUd2QixNQUFNLENBQUUsc0JBQXNCZ0IsbUJBQW1CLENBQUNILFdBQTFDLEdBQXdELGVBQXhELEdBQTBFd0csUUFBNUUsQ0FBbEI7QUFFQTVGLEVBQUFBLG1DQUFtQyxDQUFFRixLQUFGLEVBQVNQLG1CQUFtQixDQUFFLGVBQUYsQ0FBNUIsQ0FBbkM7QUFDQSxTQUFPLElBQVA7QUFDQTtBQUdEO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7O0FBQ0MsU0FBU1MsbUNBQVQsQ0FBOENGLEtBQTlDLEVBQXFEd0IsYUFBckQsRUFBb0U7QUFFbkUsTUFBSThHLFlBQVksR0FBRyxFQUFuQjs7QUFFQSxNQUFLdEksS0FBSyxDQUFDNEIsUUFBTixDQUFnQixvQkFBaEIsQ0FBTCxFQUE2QztBQUM1QzBHLElBQUFBLFlBQVksR0FBRzlHLGFBQWEsQ0FBRSxvQkFBRixDQUE1QjtBQUNBLEdBRkQsTUFFTyxJQUFLeEIsS0FBSyxDQUFDNEIsUUFBTixDQUFnQixzQkFBaEIsQ0FBTCxFQUErQztBQUNyRDBHLElBQUFBLFlBQVksR0FBRzlHLGFBQWEsQ0FBRSxzQkFBRixDQUE1QjtBQUNBLEdBRk0sTUFFQSxJQUFLeEIsS0FBSyxDQUFDNEIsUUFBTixDQUFnQiwwQkFBaEIsQ0FBTCxFQUFtRDtBQUN6RDBHLElBQUFBLFlBQVksR0FBRzlHLGFBQWEsQ0FBRSwwQkFBRixDQUE1QjtBQUNBLEdBRk0sTUFFQSxJQUFLeEIsS0FBSyxDQUFDNEIsUUFBTixDQUFnQixjQUFoQixDQUFMLEVBQXVDLENBRTdDLENBRk0sTUFFQSxJQUFLNUIsS0FBSyxDQUFDNEIsUUFBTixDQUFnQixlQUFoQixDQUFMLEVBQXdDLENBRTlDLENBRk0sTUFFQSxDQUVOOztBQUVENUIsRUFBQUEsS0FBSyxDQUFDb0gsSUFBTixDQUFZLGNBQVosRUFBNEJrQixZQUE1QjtBQUVBLE1BQUlDLEtBQUssR0FBR3ZJLEtBQUssQ0FBQ3dJLEdBQU4sQ0FBVSxDQUFWLENBQVosQ0FwQm1FLENBb0J6Qzs7QUFFMUIsTUFBTy9ILFNBQVMsSUFBSThILEtBQUssQ0FBQ0UsTUFBckIsSUFBbUMsTUFBTUgsWUFBOUMsRUFBOEQ7QUFFNURJLElBQUFBLFVBQVUsQ0FBRUgsS0FBRixFQUFVO0FBQ25CSSxNQUFBQSxPQURtQixtQkFDVkMsU0FEVSxFQUNDO0FBRW5CLFlBQUlDLGVBQWUsR0FBR0QsU0FBUyxDQUFDRSxZQUFWLENBQXdCLGNBQXhCLENBQXRCO0FBRUEsZUFBTyx3Q0FDRiwrQkFERSxHQUVERCxlQUZDLEdBR0YsUUFIRSxHQUlILFFBSko7QUFLQSxPQVZrQjtBQVduQkUsTUFBQUEsU0FBUyxFQUFVLElBWEE7QUFZbkJ2SixNQUFBQSxPQUFPLEVBQU0sa0JBWk07QUFhbkJ3SixNQUFBQSxXQUFXLEVBQVEsQ0FBRSxJQWJGO0FBY25CQyxNQUFBQSxXQUFXLEVBQVEsSUFkQTtBQWVuQkMsTUFBQUEsaUJBQWlCLEVBQUUsRUFmQTtBQWdCbkJDLE1BQUFBLFFBQVEsRUFBVyxHQWhCQTtBQWlCbkJDLE1BQUFBLEtBQUssRUFBYyxrQkFqQkE7QUFrQm5CQyxNQUFBQSxTQUFTLEVBQVUsS0FsQkE7QUFtQm5CQyxNQUFBQSxLQUFLLEVBQU0sQ0FBQyxHQUFELEVBQU0sQ0FBTixDQW5CUTtBQW1CSTtBQUN2QkMsTUFBQUEsZ0JBQWdCLEVBQUcsSUFwQkE7QUFxQm5CQyxNQUFBQSxLQUFLLEVBQU0sSUFyQlE7QUFxQkM7QUFDcEJDLE1BQUFBLFFBQVEsRUFBRTtBQUFBLGVBQU05RCxRQUFRLENBQUMrRCxJQUFmO0FBQUE7QUF0QlMsS0FBVixDQUFWO0FBd0JEO0FBQ0Q7QUFNRjtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBQ0EsU0FBU0MsbUNBQVQsR0FBOEM7QUFFOUNDLEVBQUFBLE9BQU8sQ0FBQ0MsY0FBUixDQUF3Qix1QkFBeEI7QUFBbURELEVBQUFBLE9BQU8sQ0FBQ0UsR0FBUixDQUFhLG9EQUFiLEVBQW9Fbk4scUJBQXFCLENBQUNnQixxQkFBdEIsRUFBcEU7QUFFbERvTSxFQUFBQSwyQ0FBMkMsR0FKRSxDQU03Qzs7QUFDQXRMLEVBQUFBLE1BQU0sQ0FBQ3VMLElBQVAsQ0FBYUMsWUFBWSxDQUFDQyxZQUExQixFQUNHO0FBQ0NDLElBQUFBLE1BQU0sRUFBWSx1QkFEbkI7QUFFQ0MsSUFBQUEsZ0JBQWdCLEVBQUV6TixxQkFBcUIsQ0FBQ1csZ0JBQXRCLENBQXdDLFNBQXhDLENBRm5CO0FBR0NMLElBQUFBLEtBQUssRUFBYU4scUJBQXFCLENBQUNXLGdCQUF0QixDQUF3QyxPQUF4QyxDQUhuQjtBQUlDK00sSUFBQUEsZUFBZSxFQUFHMU4scUJBQXFCLENBQUNXLGdCQUF0QixDQUF3QyxRQUF4QyxDQUpuQjtBQU1DZ04sSUFBQUEsYUFBYSxFQUFHM04scUJBQXFCLENBQUNnQixxQkFBdEI7QUFOakIsR0FESDtBQVNHO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0ksWUFBVzRNLGFBQVgsRUFBMEJDLFVBQTFCLEVBQXNDQyxLQUF0QyxFQUE4QztBQUVsRGIsSUFBQUEsT0FBTyxDQUFDRSxHQUFSLENBQWEsd0NBQWIsRUFBdURTLGFBQXZEO0FBQXdFWCxJQUFBQSxPQUFPLENBQUNjLFFBQVIsR0FGdEIsQ0FJN0M7O0FBQ0EsUUFBTSxRQUFPSCxhQUFQLE1BQXlCLFFBQTFCLElBQXdDQSxhQUFhLEtBQUssSUFBL0QsRUFBc0U7QUFFckVJLE1BQUFBLG1DQUFtQyxDQUFFSixhQUFGLENBQW5DO0FBRUE7QUFDQSxLQVY0QyxDQVk3Qzs7O0FBQ0EsUUFBaUI5SixTQUFTLElBQUk4SixhQUFhLENBQUUsb0JBQUYsQ0FBaEMsSUFDSixpQkFBaUJBLGFBQWEsQ0FBRSxvQkFBRixDQUFiLENBQXVDLFdBQXZDLENBRHhCLEVBRUM7QUFDQUssTUFBQUEsUUFBUSxDQUFDQyxNQUFUO0FBQ0E7QUFDQSxLQWxCNEMsQ0FvQjdDOzs7QUFDQWxNLElBQUFBLHlDQUF5QyxDQUFFNEwsYUFBYSxDQUFFLFVBQUYsQ0FBZixFQUErQkEsYUFBYSxDQUFFLG1CQUFGLENBQTVDLEVBQXNFQSxhQUFhLENBQUUsb0JBQUYsQ0FBbkYsQ0FBekMsQ0FyQjZDLENBdUI3Qzs7QUFDQSxRQUFLLE1BQU1BLGFBQWEsQ0FBRSxVQUFGLENBQWIsQ0FBNkIsMEJBQTdCLEVBQTBEakQsT0FBMUQsQ0FBbUUsS0FBbkUsRUFBMEUsUUFBMUUsQ0FBWCxFQUFpRztBQUNoR3dELE1BQUFBLHVCQUF1QixDQUNkUCxhQUFhLENBQUUsVUFBRixDQUFiLENBQTZCLDBCQUE3QixFQUEwRGpELE9BQTFELENBQW1FLEtBQW5FLEVBQTBFLFFBQTFFLENBRGMsRUFFWixPQUFPaUQsYUFBYSxDQUFFLFVBQUYsQ0FBYixDQUE2Qix5QkFBN0IsQ0FBVCxHQUFzRSxTQUF0RSxHQUFrRixPQUZwRSxFQUdkLEtBSGMsQ0FBdkI7QUFLQTs7QUFFRFEsSUFBQUEsMkNBQTJDLEdBaENFLENBaUM3Qzs7QUFDQUMsSUFBQUEsd0JBQXdCLENBQUVULGFBQWEsQ0FBRSxvQkFBRixDQUFiLENBQXVDLHVCQUF2QyxDQUFGLENBQXhCO0FBRUE5TCxJQUFBQSxNQUFNLENBQUUsZUFBRixDQUFOLENBQTBCUyxJQUExQixDQUFnQ3FMLGFBQWhDLEVBcEM2QyxDQW9DSztBQUNsRCxHQXJESixFQXNETVUsSUF0RE4sQ0FzRFksVUFBV1IsS0FBWCxFQUFrQkQsVUFBbEIsRUFBOEJVLFdBQTlCLEVBQTRDO0FBQUssUUFBS0MsTUFBTSxDQUFDdkIsT0FBUCxJQUFrQnVCLE1BQU0sQ0FBQ3ZCLE9BQVAsQ0FBZUUsR0FBdEMsRUFBMkM7QUFBRUYsTUFBQUEsT0FBTyxDQUFDRSxHQUFSLENBQWEsWUFBYixFQUEyQlcsS0FBM0IsRUFBa0NELFVBQWxDLEVBQThDVSxXQUE5QztBQUE4RDs7QUFFcEssUUFBSUUsYUFBYSxHQUFHLGFBQWEsUUFBYixHQUF3QixZQUF4QixHQUF1Q0YsV0FBM0Q7O0FBQ0EsUUFBS1QsS0FBSyxDQUFDWSxNQUFYLEVBQW1CO0FBQ2xCRCxNQUFBQSxhQUFhLElBQUksVUFBVVgsS0FBSyxDQUFDWSxNQUFoQixHQUF5QixPQUExQzs7QUFDQSxVQUFJLE9BQU9aLEtBQUssQ0FBQ1ksTUFBakIsRUFBeUI7QUFDeEJELFFBQUFBLGFBQWEsSUFBSSxrSkFBakI7QUFDQTtBQUNEOztBQUNELFFBQUtYLEtBQUssQ0FBQ2EsWUFBWCxFQUF5QjtBQUN4QkYsTUFBQUEsYUFBYSxJQUFJLE1BQU1YLEtBQUssQ0FBQ2EsWUFBN0I7QUFDQTs7QUFDREYsSUFBQUEsYUFBYSxHQUFHQSxhQUFhLENBQUM5RCxPQUFkLENBQXVCLEtBQXZCLEVBQThCLFFBQTlCLENBQWhCO0FBRUFxRCxJQUFBQSxtQ0FBbUMsQ0FBRVMsYUFBRixDQUFuQztBQUNDLEdBckVMLEVBc0VVO0FBQ047QUF2RUosR0FQNkMsQ0ErRXRDO0FBRVA7QUFJRDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7OztBQUNBLFNBQVNHLCtDQUFULENBQTJEeE4sVUFBM0QsRUFBdUU7QUFFdEU7QUFDQUMsRUFBQUEsQ0FBQyxDQUFDQyxJQUFGLENBQVFGLFVBQVIsRUFBb0IsVUFBV0csS0FBWCxFQUFrQkMsS0FBbEIsRUFBeUJDLE1BQXpCLEVBQWtDO0FBQ3JEO0FBQ0F6QixJQUFBQSxxQkFBcUIsQ0FBQ2tCLGdCQUF0QixDQUF3Q00sS0FBeEMsRUFBK0NELEtBQS9DO0FBQ0EsR0FIRCxFQUhzRSxDQVF0RTs7O0FBQ0F5TCxFQUFBQSxtQ0FBbUM7QUFDbkM7QUFHQTtBQUNEO0FBQ0E7QUFDQTs7O0FBQ0MsU0FBUzZCLHVDQUFULENBQWtEQyxXQUFsRCxFQUErRDtBQUU5REYsRUFBQUEsK0NBQStDLENBQUU7QUFDeEMsZ0JBQVlFO0FBRDRCLEdBQUYsQ0FBL0M7QUFHQTtBQUlGO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFDQSxTQUFTQywyQ0FBVCxHQUFzRDtBQUVyRC9CLEVBQUFBLG1DQUFtQyxHQUZrQixDQUVaO0FBQ3pDO0FBRUQ7QUFDQTtBQUNBOzs7QUFDQSxTQUFTZ0MsMkNBQVQsR0FBc0Q7QUFFckRsTixFQUFBQSxNQUFNLENBQUc5QixxQkFBcUIsQ0FBQzZCLGVBQXRCLENBQXVDLG1CQUF2QyxDQUFILENBQU4sQ0FBeUVVLElBQXpFLENBQStFLEVBQS9FO0FBQ0E7QUFJRDtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBQ0EsU0FBU3lMLG1DQUFULENBQThDaEUsT0FBOUMsRUFBdUQ7QUFFdERnRixFQUFBQSwyQ0FBMkM7QUFFM0NsTixFQUFBQSxNQUFNLENBQUU5QixxQkFBcUIsQ0FBQzZCLGVBQXRCLENBQXVDLG1CQUF2QyxDQUFGLENBQU4sQ0FBdUVVLElBQXZFLENBQ1csOEVBQ0N5SCxPQURELEdBRUEsUUFIWDtBQUtBO0FBSUQ7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUNBLFNBQVNvRCwyQ0FBVCxHQUFzRDtBQUNyRHRMLEVBQUFBLE1BQU0sQ0FBRSx1REFBRixDQUFOLENBQWlFMkIsV0FBakUsQ0FBOEUsc0JBQTlFO0FBQ0E7QUFFRDtBQUNBO0FBQ0E7OztBQUNBLFNBQVMySywyQ0FBVCxHQUFzRDtBQUNyRHRNLEVBQUFBLE1BQU0sQ0FBRSx1REFBRixDQUFOLENBQWtFMEgsUUFBbEUsQ0FBNEUsc0JBQTVFO0FBQ0E7QUFFRDtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7QUFDQSxTQUFTeUYsd0NBQVQsR0FBbUQ7QUFDL0MsTUFBS25OLE1BQU0sQ0FBRSx1REFBRixDQUFOLENBQWtFbUQsUUFBbEUsQ0FBNEUsc0JBQTVFLENBQUwsRUFBMkc7QUFDN0csV0FBTyxJQUFQO0FBQ0EsR0FGRSxNQUVJO0FBQ04sV0FBTyxLQUFQO0FBQ0E7QUFDRCIsInNvdXJjZXNDb250ZW50IjpbIlwidXNlIHN0cmljdFwiO1xyXG5cclxuLyoqXHJcbiAqIFJlcXVlc3QgT2JqZWN0XHJcbiAqIEhlcmUgd2UgY2FuICBkZWZpbmUgU2VhcmNoIHBhcmFtZXRlcnMgYW5kIFVwZGF0ZSBpdCBsYXRlciwgIHdoZW4gIHNvbWUgcGFyYW1ldGVyIHdhcyBjaGFuZ2VkXHJcbiAqXHJcbiAqL1xyXG5cclxudmFyIHdwYmNfYWp4X2F2YWlsYWJpbGl0eSA9IChmdW5jdGlvbiAoIG9iaiwgJCkge1xyXG5cclxuXHQvLyBTZWN1cmUgcGFyYW1ldGVycyBmb3IgQWpheFx0LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0dmFyIHBfc2VjdXJlID0gb2JqLnNlY3VyaXR5X29iaiA9IG9iai5zZWN1cml0eV9vYmogfHwge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR1c2VyX2lkOiAwLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRub25jZSAgOiAnJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0bG9jYWxlIDogJydcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgfTtcclxuXHJcblx0b2JqLnNldF9zZWN1cmVfcGFyYW0gPSBmdW5jdGlvbiAoIHBhcmFtX2tleSwgcGFyYW1fdmFsICkge1xyXG5cdFx0cF9zZWN1cmVbIHBhcmFtX2tleSBdID0gcGFyYW1fdmFsO1xyXG5cdH07XHJcblxyXG5cdG9iai5nZXRfc2VjdXJlX3BhcmFtID0gZnVuY3Rpb24gKCBwYXJhbV9rZXkgKSB7XHJcblx0XHRyZXR1cm4gcF9zZWN1cmVbIHBhcmFtX2tleSBdO1xyXG5cdH07XHJcblxyXG5cclxuXHQvLyBMaXN0aW5nIFNlYXJjaCBwYXJhbWV0ZXJzXHQtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHR2YXIgcF9saXN0aW5nID0gb2JqLnNlYXJjaF9yZXF1ZXN0X29iaiA9IG9iai5zZWFyY2hfcmVxdWVzdF9vYmogfHwge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBzb3J0ICAgICAgICAgICAgOiBcImJvb2tpbmdfaWRcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gc29ydF90eXBlICAgICAgIDogXCJERVNDXCIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIHBhZ2VfbnVtICAgICAgICA6IDEsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIHBhZ2VfaXRlbXNfY291bnQ6IDEwLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBjcmVhdGVfZGF0ZSAgICAgOiBcIlwiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBrZXl3b3JkICAgICAgICAgOiBcIlwiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBzb3VyY2UgICAgICAgICAgOiBcIlwiXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9O1xyXG5cclxuXHRvYmouc2VhcmNoX3NldF9hbGxfcGFyYW1zID0gZnVuY3Rpb24gKCByZXF1ZXN0X3BhcmFtX29iaiApIHtcclxuXHRcdHBfbGlzdGluZyA9IHJlcXVlc3RfcGFyYW1fb2JqO1xyXG5cdH07XHJcblxyXG5cdG9iai5zZWFyY2hfZ2V0X2FsbF9wYXJhbXMgPSBmdW5jdGlvbiAoKSB7XHJcblx0XHRyZXR1cm4gcF9saXN0aW5nO1xyXG5cdH07XHJcblxyXG5cdG9iai5zZWFyY2hfZ2V0X3BhcmFtID0gZnVuY3Rpb24gKCBwYXJhbV9rZXkgKSB7XHJcblx0XHRyZXR1cm4gcF9saXN0aW5nWyBwYXJhbV9rZXkgXTtcclxuXHR9O1xyXG5cclxuXHRvYmouc2VhcmNoX3NldF9wYXJhbSA9IGZ1bmN0aW9uICggcGFyYW1fa2V5LCBwYXJhbV92YWwgKSB7XHJcblx0XHQvLyBpZiAoIEFycmF5LmlzQXJyYXkoIHBhcmFtX3ZhbCApICl7XHJcblx0XHQvLyBcdHBhcmFtX3ZhbCA9IEpTT04uc3RyaW5naWZ5KCBwYXJhbV92YWwgKTtcclxuXHRcdC8vIH1cclxuXHRcdHBfbGlzdGluZ1sgcGFyYW1fa2V5IF0gPSBwYXJhbV92YWw7XHJcblx0fTtcclxuXHJcblx0b2JqLnNlYXJjaF9zZXRfcGFyYW1zX2FyciA9IGZ1bmN0aW9uKCBwYXJhbXNfYXJyICl7XHJcblx0XHRfLmVhY2goIHBhcmFtc19hcnIsIGZ1bmN0aW9uICggcF92YWwsIHBfa2V5LCBwX2RhdGEgKXtcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBEZWZpbmUgZGlmZmVyZW50IFNlYXJjaCAgcGFyYW1ldGVycyBmb3IgcmVxdWVzdFxyXG5cdFx0XHR0aGlzLnNlYXJjaF9zZXRfcGFyYW0oIHBfa2V5LCBwX3ZhbCApO1xyXG5cdFx0fSApO1xyXG5cdH1cclxuXHJcblxyXG5cdC8vIE90aGVyIHBhcmFtZXRlcnMgXHRcdFx0LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0dmFyIHBfb3RoZXIgPSBvYmoub3RoZXJfb2JqID0gb2JqLm90aGVyX29iaiB8fCB7IH07XHJcblxyXG5cdG9iai5zZXRfb3RoZXJfcGFyYW0gPSBmdW5jdGlvbiAoIHBhcmFtX2tleSwgcGFyYW1fdmFsICkge1xyXG5cdFx0cF9vdGhlclsgcGFyYW1fa2V5IF0gPSBwYXJhbV92YWw7XHJcblx0fTtcclxuXHJcblx0b2JqLmdldF9vdGhlcl9wYXJhbSA9IGZ1bmN0aW9uICggcGFyYW1fa2V5ICkge1xyXG5cdFx0cmV0dXJuIHBfb3RoZXJbIHBhcmFtX2tleSBdO1xyXG5cdH07XHJcblxyXG5cclxuXHRyZXR1cm4gb2JqO1xyXG59KCB3cGJjX2FqeF9hdmFpbGFiaWxpdHkgfHwge30sIGpRdWVyeSApKTtcclxuXHJcbnZhciB3cGJjX2FqeF9ib29raW5ncyA9IFtdO1xyXG5cclxuLyoqXHJcbiAqICAgU2hvdyBDb250ZW50ICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG4vKipcclxuICogU2hvdyBDb250ZW50IC0gQ2FsZW5kYXIgYW5kIFVJIGVsZW1lbnRzXHJcbiAqXHJcbiAqIEBwYXJhbSBhanhfZGF0YV9hcnJcclxuICogQHBhcmFtIGFqeF9zZWFyY2hfcGFyYW1zXHJcbiAqIEBwYXJhbSBhanhfY2xlYW5lZF9wYXJhbXNcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWp4X2F2YWlsYWJpbGl0eV9fcGFnZV9jb250ZW50X19zaG93KCBhanhfZGF0YV9hcnIsIGFqeF9zZWFyY2hfcGFyYW1zICwgYWp4X2NsZWFuZWRfcGFyYW1zICl7XHJcblxyXG5cdHZhciB0ZW1wbGF0ZV9fYXZhaWxhYmlsaXR5X21haW5fcGFnZV9jb250ZW50ID0gd3AudGVtcGxhdGUoICd3cGJjX2FqeF9hdmFpbGFiaWxpdHlfbWFpbl9wYWdlX2NvbnRlbnQnICk7XHJcblxyXG5cdC8vIENvbnRlbnRcclxuXHRqUXVlcnkoIHdwYmNfYWp4X2F2YWlsYWJpbGl0eS5nZXRfb3RoZXJfcGFyYW0oICdsaXN0aW5nX2NvbnRhaW5lcicgKSApLmh0bWwoIHRlbXBsYXRlX19hdmFpbGFiaWxpdHlfbWFpbl9wYWdlX2NvbnRlbnQoIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnYWp4X2RhdGEnICAgICAgICAgICAgICA6IGFqeF9kYXRhX2FycixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnYWp4X3NlYXJjaF9wYXJhbXMnICAgICA6IGFqeF9zZWFyY2hfcGFyYW1zLFx0XHRcdFx0XHRcdFx0XHQvLyAkX1JFUVVFU1RbICdzZWFyY2hfcGFyYW1zJyBdXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2FqeF9jbGVhbmVkX3BhcmFtcycgICAgOiBhanhfY2xlYW5lZF9wYXJhbXNcclxuXHRcdFx0XHRcdFx0XHRcdFx0fSApICk7XHJcblxyXG5cdGpRdWVyeSggJy53cGJjX3Byb2Nlc3Npbmcud3BiY19zcGluJykucGFyZW50KCkucGFyZW50KCkucGFyZW50KCkucGFyZW50KCAnW2lkXj1cIndwYmNfbm90aWNlX1wiXScgKS5oaWRlKCk7XHJcblx0Ly8gTG9hZCBjYWxlbmRhclxyXG5cdHdwYmNfYWp4X2F2YWlsYWJpbGl0eV9fY2FsZW5kYXJfX3Nob3coIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdyZXNvdXJjZV9pZCcgICAgICAgOiBhanhfY2xlYW5lZF9wYXJhbXMucmVzb3VyY2VfaWQsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnYWp4X25vbmNlX2NhbGVuZGFyJzogYWp4X2RhdGFfYXJyLmFqeF9ub25jZV9jYWxlbmRhcixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdhanhfZGF0YV9hcnInICAgICAgICAgIDogYWp4X2RhdGFfYXJyLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2FqeF9jbGVhbmVkX3BhcmFtcycgICAgOiBhanhfY2xlYW5lZF9wYXJhbXNcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHR9ICk7XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBUcmlnZ2VyIGZvciBkYXRlcyBzZWxlY3Rpb24gaW4gdGhlIGJvb2tpbmcgZm9ybVxyXG5cdCAqXHJcblx0ICogalF1ZXJ5KCB3cGJjX2FqeF9hdmFpbGFiaWxpdHkuZ2V0X290aGVyX3BhcmFtKCAnbGlzdGluZ19jb250YWluZXInICkgKS5vbignd3BiY19wYWdlX2NvbnRlbnRfbG9hZGVkJywgZnVuY3Rpb24oZXZlbnQsIGFqeF9kYXRhX2FyciwgYWp4X3NlYXJjaF9wYXJhbXMgLCBhanhfY2xlYW5lZF9wYXJhbXMpIHsgLi4uIH0gKTtcclxuXHQgKi9cclxuXHRqUXVlcnkoIHdwYmNfYWp4X2F2YWlsYWJpbGl0eS5nZXRfb3RoZXJfcGFyYW0oICdsaXN0aW5nX2NvbnRhaW5lcicgKSApLnRyaWdnZXIoICd3cGJjX3BhZ2VfY29udGVudF9sb2FkZWQnLCBbIGFqeF9kYXRhX2FyciwgYWp4X3NlYXJjaF9wYXJhbXMgLCBhanhfY2xlYW5lZF9wYXJhbXMgXSApO1xyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqIFNob3cgaW5saW5lIG1vbnRoIHZpZXcgY2FsZW5kYXIgICAgICAgICAgICAgIHdpdGggYWxsIHByZWRlZmluZWQgQ1NTIChzaXplcyBhbmQgY2hlY2sgaW4vb3V0LCAgdGltZXMgY29udGFpbmVycylcclxuICogQHBhcmFtIHtvYmp9IGNhbGVuZGFyX3BhcmFtc19hcnJcclxuXHRcdFx0e1xyXG5cdFx0XHRcdCdyZXNvdXJjZV9pZCcgICAgICAgXHQ6IGFqeF9jbGVhbmVkX3BhcmFtcy5yZXNvdXJjZV9pZCxcclxuXHRcdFx0XHQnYWp4X25vbmNlX2NhbGVuZGFyJ1x0OiBhanhfZGF0YV9hcnIuYWp4X25vbmNlX2NhbGVuZGFyLFxyXG5cdFx0XHRcdCdhanhfZGF0YV9hcnInICAgICAgICAgIDogYWp4X2RhdGFfYXJyID0geyBhanhfYm9va2luZ19yZXNvdXJjZXM6W10sIGJvb2tlZF9kYXRlczoge30sIHJlc291cmNlX3VuYXZhaWxhYmxlX2RhdGVzOltdLCBzZWFzb25fYXZhaWxhYmlsaXR5Ont9LC4uLi4gfVxyXG5cdFx0XHRcdCdhanhfY2xlYW5lZF9wYXJhbXMnICAgIDoge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0Y2FsZW5kYXJfX2RheXNfc2VsZWN0aW9uX21vZGU6IFwiZHluYW1pY1wiXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRjYWxlbmRhcl9fc3RhcnRfd2Vla19kYXk6IFwiMFwiXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRjYWxlbmRhcl9fdGltZXNsb3RfZGF5X2JnX2FzX2F2YWlsYWJsZTogXCJcIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0Y2FsZW5kYXJfX3ZpZXdfX2NlbGxfaGVpZ2h0OiBcIlwiXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRjYWxlbmRhcl9fdmlld19fbW9udGhzX2luX3JvdzogNFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0Y2FsZW5kYXJfX3ZpZXdfX3Zpc2libGVfbW9udGhzOiAxMlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0Y2FsZW5kYXJfX3ZpZXdfX3dpZHRoOiBcIjEwMCVcIlxyXG5cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdGRhdGVzX2F2YWlsYWJpbGl0eTogXCJ1bmF2YWlsYWJsZVwiXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRkYXRlc19zZWxlY3Rpb246IFwiMjAyMy0wMy0xNCB+IDIwMjMtMDMtMTZcIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0ZG9fYWN0aW9uOiBcInNldF9hdmFpbGFiaWxpdHlcIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0cmVzb3VyY2VfaWQ6IDFcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdHVpX2NsaWNrZWRfZWxlbWVudF9pZDogXCJ3cGJjX2F2YWlsYWJpbGl0eV9hcHBseV9idG5cIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0dWlfdXNyX19hdmFpbGFiaWxpdHlfc2VsZWN0ZWRfdG9vbGJhcjogXCJpbmZvXCJcclxuXHRcdFx0XHRcdFx0XHRcdCAgXHRcdCB9XHJcblx0XHRcdH1cclxuKi9cclxuZnVuY3Rpb24gd3BiY19hanhfYXZhaWxhYmlsaXR5X19jYWxlbmRhcl9fc2hvdyggY2FsZW5kYXJfcGFyYW1zX2FyciApe1xyXG5cclxuXHQvLyBVcGRhdGUgbm9uY2VcclxuXHRqUXVlcnkoICcjYWp4X25vbmNlX2NhbGVuZGFyX3NlY3Rpb24nICkuaHRtbCggY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfbm9uY2VfY2FsZW5kYXIgKTtcclxuXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBVcGRhdGUgYm9va2luZ3NcclxuXHRpZiAoICd1bmRlZmluZWQnID09IHR5cGVvZiAod3BiY19hanhfYm9va2luZ3NbIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgXSkgKXsgd3BiY19hanhfYm9va2luZ3NbIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgXSA9IFtdOyB9XHJcblx0d3BiY19hanhfYm9va2luZ3NbIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgXSA9IGNhbGVuZGFyX3BhcmFtc19hcnJbICdhanhfZGF0YV9hcnInIF1bICdib29rZWRfZGF0ZXMnIF07XHJcblxyXG5cclxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8qKlxyXG5cdCAqIERlZmluZSBzaG93aW5nIG1vdXNlIG92ZXIgdG9vbHRpcCBvbiB1bmF2YWlsYWJsZSBkYXRlc1xyXG5cdCAqIEl0J3MgZGVmaW5lZCwgd2hlbiBjYWxlbmRhciBSRUZSRVNIRUQgKGNoYW5nZSBtb250aHMgb3IgZGF5cyBzZWxlY3Rpb24pIGxvYWRlZCBpbiBqcXVlcnkuZGF0ZXBpY2sud3BiYy45LjAuanMgOlxyXG5cdCAqIFx0XHQkKCAnYm9keScgKS50cmlnZ2VyKCAnd3BiY19kYXRlcGlja19pbmxpbmVfY2FsZW5kYXJfcmVmcmVzaCcsIC4uLlx0XHQvL0ZpeEluOiA5LjQuNC4xM1xyXG5cdCAqL1xyXG5cdGpRdWVyeSggJ2JvZHknICkub24oICd3cGJjX2RhdGVwaWNrX2lubGluZV9jYWxlbmRhcl9yZWZyZXNoJywgZnVuY3Rpb24gKCBldmVudCwgcmVzb3VyY2VfaWQsIGluc3QgKXtcclxuXHRcdC8vIGluc3QuZHBEaXYgIGl0J3M6ICA8ZGl2IGNsYXNzPVwiZGF0ZXBpY2staW5saW5lIGRhdGVwaWNrLW11bHRpXCIgc3R5bGU9XCJ3aWR0aDogMTc3MTJweDtcIj4uLi4uPC9kaXY+XHJcblx0XHRpbnN0LmRwRGl2LmZpbmQoICcuc2Vhc29uX3VuYXZhaWxhYmxlLC5iZWZvcmVfYWZ0ZXJfdW5hdmFpbGFibGUsLndlZWtkYXlzX3VuYXZhaWxhYmxlJyApLm9uKCAnbW91c2VvdmVyJywgZnVuY3Rpb24gKCB0aGlzX2V2ZW50ICl7XHJcblx0XHRcdC8vIGFsc28gYXZhaWxhYmxlIHRoZXNlIHZhcnM6IFx0cmVzb3VyY2VfaWQsIGpDYWxDb250YWluZXIsIGluc3RcclxuXHRcdFx0dmFyIGpDZWxsID0galF1ZXJ5KCB0aGlzX2V2ZW50LmN1cnJlbnRUYXJnZXQgKTtcclxuXHRcdFx0d3BiY19hdnlfX3Nob3dfdG9vbHRpcF9fZm9yX2VsZW1lbnQoIGpDZWxsLCBjYWxlbmRhcl9wYXJhbXNfYXJyWyAnYWp4X2RhdGFfYXJyJyBdWydwb3BvdmVyX2hpbnRzJ10gKTtcclxuXHRcdH0pO1xyXG5cclxuXHR9XHQpO1xyXG5cclxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8qKlxyXG5cdCAqIERlZmluZSBoZWlnaHQgb2YgdGhlIGNhbGVuZGFyICBjZWxscywgXHRhbmQgIG1vdXNlIG92ZXIgdG9vbHRpcHMgYXQgIHNvbWUgdW5hdmFpbGFibGUgZGF0ZXNcclxuXHQgKiBJdCdzIGRlZmluZWQsIHdoZW4gY2FsZW5kYXIgbG9hZGVkIGluIGpxdWVyeS5kYXRlcGljay53cGJjLjkuMC5qcyA6XHJcblx0ICogXHRcdCQoICdib2R5JyApLnRyaWdnZXIoICd3cGJjX2RhdGVwaWNrX2lubGluZV9jYWxlbmRhcl9sb2FkZWQnLCAuLi5cdFx0Ly9GaXhJbjogOS40LjQuMTJcclxuXHQgKi9cclxuXHRqUXVlcnkoICdib2R5JyApLm9uKCAnd3BiY19kYXRlcGlja19pbmxpbmVfY2FsZW5kYXJfbG9hZGVkJywgZnVuY3Rpb24gKCBldmVudCwgcmVzb3VyY2VfaWQsIGpDYWxDb250YWluZXIsIGluc3QgKXtcclxuXHJcblx0XHQvLyBSZW1vdmUgaGlnaGxpZ2h0IGRheSBmb3IgdG9kYXkgIGRhdGVcclxuXHRcdGpRdWVyeSggJy5kYXRlcGljay1kYXlzLWNlbGwuZGF0ZXBpY2stdG9kYXkuZGF0ZXBpY2stZGF5cy1jZWxsLW92ZXInICkucmVtb3ZlQ2xhc3MoICdkYXRlcGljay1kYXlzLWNlbGwtb3ZlcicgKTtcclxuXHJcblx0XHQvLyBTZXQgaGVpZ2h0IG9mIGNhbGVuZGFyICBjZWxscyBpZiBkZWZpbmVkIHRoaXMgb3B0aW9uXHJcblx0XHRpZiAoICcnICE9PSBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9jbGVhbmVkX3BhcmFtcy5jYWxlbmRhcl9fdmlld19fY2VsbF9oZWlnaHQgKXtcclxuXHRcdFx0alF1ZXJ5KCAnaGVhZCcgKS5hcHBlbmQoICc8c3R5bGUgdHlwZT1cInRleHQvY3NzXCI+J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdCsgJy5oYXNEYXRlcGljayAuZGF0ZXBpY2staW5saW5lIC5kYXRlcGljay10aXRsZS1yb3cgdGgsICdcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHQrICcuaGFzRGF0ZXBpY2sgLmRhdGVwaWNrLWlubGluZSAuZGF0ZXBpY2stZGF5cy1jZWxsIHsnXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQrICdoZWlnaHQ6ICcgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9jbGVhbmVkX3BhcmFtcy5jYWxlbmRhcl9fdmlld19fY2VsbF9oZWlnaHQgKyAnICFpbXBvcnRhbnQ7J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdCsgJ30nXHJcblx0XHRcdFx0XHRcdFx0XHRcdCsnPC9zdHlsZT4nICk7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gRGVmaW5lIHNob3dpbmcgbW91c2Ugb3ZlciB0b29sdGlwIG9uIHVuYXZhaWxhYmxlIGRhdGVzXHJcblx0XHRqQ2FsQ29udGFpbmVyLmZpbmQoICcuc2Vhc29uX3VuYXZhaWxhYmxlLC5iZWZvcmVfYWZ0ZXJfdW5hdmFpbGFibGUsLndlZWtkYXlzX3VuYXZhaWxhYmxlJyApLm9uKCAnbW91c2VvdmVyJywgZnVuY3Rpb24gKCB0aGlzX2V2ZW50ICl7XHJcblx0XHRcdC8vIGFsc28gYXZhaWxhYmxlIHRoZXNlIHZhcnM6IFx0cmVzb3VyY2VfaWQsIGpDYWxDb250YWluZXIsIGluc3RcclxuXHRcdFx0dmFyIGpDZWxsID0galF1ZXJ5KCB0aGlzX2V2ZW50LmN1cnJlbnRUYXJnZXQgKTtcclxuXHRcdFx0d3BiY19hdnlfX3Nob3dfdG9vbHRpcF9fZm9yX2VsZW1lbnQoIGpDZWxsLCBjYWxlbmRhcl9wYXJhbXNfYXJyWyAnYWp4X2RhdGFfYXJyJyBdWydwb3BvdmVyX2hpbnRzJ10gKTtcclxuXHRcdH0pO1xyXG5cdH0gKTtcclxuXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBEZWZpbmUgd2lkdGggb2YgZW50aXJlIGNhbGVuZGFyXHJcblx0dmFyIHdpZHRoID0gICAnd2lkdGg6J1x0XHQrICAgY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfY2xlYW5lZF9wYXJhbXMuY2FsZW5kYXJfX3ZpZXdfX3dpZHRoICsgJzsnO1x0XHRcdFx0XHQvLyB2YXIgd2lkdGggPSAnd2lkdGg6MTAwJTttYXgtd2lkdGg6MTAwJTsnO1xyXG5cclxuXHRpZiAoICAgKCB1bmRlZmluZWQgIT0gY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfY2xlYW5lZF9wYXJhbXMuY2FsZW5kYXJfX3ZpZXdfX21heF93aWR0aCApXHJcblx0XHQmJiAoICcnICE9IGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLmNhbGVuZGFyX192aWV3X19tYXhfd2lkdGggKVxyXG5cdCl7XHJcblx0XHR3aWR0aCArPSAnbWF4LXdpZHRoOicgXHQrIGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLmNhbGVuZGFyX192aWV3X19tYXhfd2lkdGggKyAnOyc7XHJcblx0fSBlbHNlIHtcclxuXHRcdHdpZHRoICs9ICdtYXgtd2lkdGg6JyBcdCsgKCBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9jbGVhbmVkX3BhcmFtcy5jYWxlbmRhcl9fdmlld19fbW9udGhzX2luX3JvdyAqIDI4NCApICsgJ3B4Oyc7XHJcblx0fVxyXG5cclxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEFkZCBjYWxlbmRhciBjb250YWluZXI6IFwiQ2FsZW5kYXIgaXMgbG9hZGluZy4uLlwiICBhbmQgdGV4dGFyZWFcclxuXHRqUXVlcnkoICcud3BiY19hanhfYXZ5X19jYWxlbmRhcicgKS5odG1sKFxyXG5cclxuXHRcdCc8ZGl2IGNsYXNzPVwiJ1x0KyAnIGJrX2NhbGVuZGFyX2ZyYW1lJ1xyXG5cdFx0XHRcdFx0XHQrICcgbW9udGhzX251bV9pbl9yb3dfJyArIGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLmNhbGVuZGFyX192aWV3X19tb250aHNfaW5fcm93XHJcblx0XHRcdFx0XHRcdCsgJyBjYWxfbW9udGhfbnVtXycgXHQrIGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLmNhbGVuZGFyX192aWV3X192aXNpYmxlX21vbnRoc1xyXG5cdFx0XHRcdFx0XHQrICcgJyBcdFx0XHRcdFx0KyBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9jbGVhbmVkX3BhcmFtcy5jYWxlbmRhcl9fdGltZXNsb3RfZGF5X2JnX2FzX2F2YWlsYWJsZSBcdFx0XHRcdC8vICd3cGJjX3RpbWVzbG90X2RheV9iZ19hc19hdmFpbGFibGUnIHx8ICcnXHJcblx0XHRcdFx0KyAnXCIgJ1xyXG5cdFx0XHQrICdzdHlsZT1cIicgKyB3aWR0aCArICdcIj4nXHJcblxyXG5cdFx0XHRcdCsgJzxkaXYgaWQ9XCJjYWxlbmRhcl9ib29raW5nJyArIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgKyAnXCI+JyArICdDYWxlbmRhciBpcyBsb2FkaW5nLi4uJyArICc8L2Rpdj4nXHJcblxyXG5cdFx0KyAnPC9kaXY+J1xyXG5cclxuXHRcdCsgJzx0ZXh0YXJlYSAgICAgIGlkPVwiZGF0ZV9ib29raW5nJyArIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgKyAnXCInXHJcblx0XHRcdFx0XHQrICcgbmFtZT1cImRhdGVfYm9va2luZycgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLnJlc291cmNlX2lkICsgJ1wiJ1xyXG5cdFx0XHRcdFx0KyAnIGF1dG9jb21wbGV0ZT1cIm9mZlwiJ1xyXG5cdFx0XHRcdFx0KyAnIHN0eWxlPVwiZGlzcGxheTpub25lO3dpZHRoOjEwMCU7aGVpZ2h0OjEwZW07bWFyZ2luOjJlbSAwIDA7XCI+PC90ZXh0YXJlYT4nXHJcblx0KTtcclxuXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHR2YXIgY2FsX3BhcmFtX2FyciA9IHtcclxuXHRcdFx0XHRcdFx0XHQnaHRtbF9pZCcgICAgICAgICAgIDogJ2NhbGVuZGFyX2Jvb2tpbmcnICsgY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfY2xlYW5lZF9wYXJhbXMucmVzb3VyY2VfaWQsXHJcblx0XHRcdFx0XHRcdFx0J3RleHRfaWQnICAgICAgICAgICA6ICdkYXRlX2Jvb2tpbmcnICsgY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfY2xlYW5lZF9wYXJhbXMucmVzb3VyY2VfaWQsXHJcblxyXG5cdFx0XHRcdFx0XHRcdCdjYWxlbmRhcl9fc3RhcnRfd2Vla19kYXknOiBcdCAgY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfY2xlYW5lZF9wYXJhbXMuY2FsZW5kYXJfX3N0YXJ0X3dlZWtfZGF5LFxyXG5cdFx0XHRcdFx0XHRcdCdjYWxlbmRhcl9fdmlld19fdmlzaWJsZV9tb250aHMnOiBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9jbGVhbmVkX3BhcmFtcy5jYWxlbmRhcl9fdmlld19fdmlzaWJsZV9tb250aHMsXHJcblx0XHRcdFx0XHRcdFx0J2NhbGVuZGFyX19kYXlzX3NlbGVjdGlvbl9tb2RlJzogIGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLmNhbGVuZGFyX19kYXlzX3NlbGVjdGlvbl9tb2RlLFxyXG5cclxuXHRcdFx0XHRcdFx0XHQncmVzb3VyY2VfaWQnICAgICAgICA6IGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLnJlc291cmNlX2lkLFxyXG5cdFx0XHRcdFx0XHRcdCdhanhfbm9uY2VfY2FsZW5kYXInIDogY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfZGF0YV9hcnIuYWp4X25vbmNlX2NhbGVuZGFyLFxyXG5cdFx0XHRcdFx0XHRcdCdib29rZWRfZGF0ZXMnICAgICAgIDogY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfZGF0YV9hcnIuYm9va2VkX2RhdGVzLFxyXG5cdFx0XHRcdFx0XHRcdCdzZWFzb25fYXZhaWxhYmlsaXR5JzogY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfZGF0YV9hcnIuc2Vhc29uX2F2YWlsYWJpbGl0eSxcclxuXHJcblx0XHRcdFx0XHRcdFx0J3Jlc291cmNlX3VuYXZhaWxhYmxlX2RhdGVzJyA6IGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2RhdGFfYXJyLnJlc291cmNlX3VuYXZhaWxhYmxlX2RhdGVzLFxyXG5cclxuXHRcdFx0XHRcdFx0XHQncG9wb3Zlcl9oaW50cyc6IGNhbGVuZGFyX3BhcmFtc19hcnJbICdhanhfZGF0YV9hcnInIF1bJ3BvcG92ZXJfaGludHMnXVx0XHQvLyB7J3NlYXNvbl91bmF2YWlsYWJsZSc6Jy4uLicsJ3dlZWtkYXlzX3VuYXZhaWxhYmxlJzonLi4uJywnYmVmb3JlX2FmdGVyX3VuYXZhaWxhYmxlJzonLi4uJyx9XHJcblx0XHRcdFx0XHRcdH07XHJcblx0d3BiY19zaG93X2lubGluZV9ib29raW5nX2NhbGVuZGFyKCBjYWxfcGFyYW1fYXJyICk7XHJcblxyXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0LyoqXHJcblx0ICogT24gY2xpY2sgQVZBSUxBQkxFIHwgIFVOQVZBSUxBQkxFIGJ1dHRvbiAgaW4gd2lkZ2V0XHQtXHRuZWVkIHRvICBjaGFuZ2UgaGVscCBkYXRlcyB0ZXh0XHJcblx0ICovXHJcblx0alF1ZXJ5KCAnLndwYmNfcmFkaW9fX3NldF9kYXlzX2F2YWlsYWJpbGl0eScgKS5vbignY2hhbmdlJywgZnVuY3Rpb24gKCBldmVudCwgcmVzb3VyY2VfaWQsIGluc3QgKXtcclxuXHRcdHdwYmNfX2lubGluZV9ib29raW5nX2NhbGVuZGFyX19vbl9kYXlzX3NlbGVjdCggalF1ZXJ5KCAnIycgKyBjYWxfcGFyYW1fYXJyLnRleHRfaWQgKS52YWwoKSAsIGNhbF9wYXJhbV9hcnIgKTtcclxuXHR9KTtcclxuXHJcblx0Ly8gU2hvdyBcdCdTZWxlY3QgZGF5cyAgaW4gY2FsZW5kYXIgdGhlbiBzZWxlY3QgQXZhaWxhYmxlICAvICBVbmF2YWlsYWJsZSBzdGF0dXMgYW5kIGNsaWNrIEFwcGx5IGF2YWlsYWJpbGl0eSBidXR0b24uJ1xyXG5cdGpRdWVyeSggJyN3cGJjX3Rvb2xiYXJfZGF0ZXNfaGludCcpLmh0bWwoICAgICAnPGRpdiBjbGFzcz1cInVpX2VsZW1lbnRcIj48c3BhbiBjbGFzcz1cIndwYmNfdWlfY29udHJvbCB3cGJjX3VpX2FkZG9uIHdwYmNfaGVscF90ZXh0XCIgPidcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQrIGNhbF9wYXJhbV9hcnIucG9wb3Zlcl9oaW50cy50b29sYmFyX3RleHRcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0KyAnPC9zcGFuPjwvZGl2PidcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCk7XHJcbn1cclxuXHJcblxyXG4vKipcclxuICogXHRMb2FkIERhdGVwaWNrIElubGluZSBjYWxlbmRhclxyXG4gKlxyXG4gKiBAcGFyYW0gY2FsZW5kYXJfcGFyYW1zX2Fyclx0XHRleGFtcGxlOntcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdodG1sX2lkJyAgICAgICAgICAgOiAnY2FsZW5kYXJfYm9va2luZycgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9jbGVhbmVkX3BhcmFtcy5yZXNvdXJjZV9pZCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd0ZXh0X2lkJyAgICAgICAgICAgOiAnZGF0ZV9ib29raW5nJyArIGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLnJlc291cmNlX2lkLFxyXG5cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdjYWxlbmRhcl9fc3RhcnRfd2Vla19kYXknOiBcdCAgY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfY2xlYW5lZF9wYXJhbXMuY2FsZW5kYXJfX3N0YXJ0X3dlZWtfZGF5LFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2NhbGVuZGFyX192aWV3X192aXNpYmxlX21vbnRocyc6IGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLmNhbGVuZGFyX192aWV3X192aXNpYmxlX21vbnRocyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdjYWxlbmRhcl9fZGF5c19zZWxlY3Rpb25fbW9kZSc6ICBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9jbGVhbmVkX3BhcmFtcy5jYWxlbmRhcl9fZGF5c19zZWxlY3Rpb25fbW9kZSxcclxuXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQncmVzb3VyY2VfaWQnICAgICAgICA6IGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLnJlc291cmNlX2lkLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2FqeF9ub25jZV9jYWxlbmRhcicgOiBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9kYXRhX2Fyci5hanhfbm9uY2VfY2FsZW5kYXIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnYm9va2VkX2RhdGVzJyAgICAgICA6IGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2RhdGFfYXJyLmJvb2tlZF9kYXRlcyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzZWFzb25fYXZhaWxhYmlsaXR5JzogY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfZGF0YV9hcnIuc2Vhc29uX2F2YWlsYWJpbGl0eSxcclxuXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQncmVzb3VyY2VfdW5hdmFpbGFibGVfZGF0ZXMnIDogY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfZGF0YV9hcnIucmVzb3VyY2VfdW5hdmFpbGFibGVfZGF0ZXNcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcbiAqIEByZXR1cm5zIHtib29sZWFufVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19zaG93X2lubGluZV9ib29raW5nX2NhbGVuZGFyKCBjYWxlbmRhcl9wYXJhbXNfYXJyICl7XHJcblxyXG5cdGlmIChcclxuXHRcdCAgICggMCA9PT0galF1ZXJ5KCAnIycgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLmh0bWxfaWQgKS5sZW5ndGggKVx0XHRcdFx0XHRcdFx0Ly8gSWYgY2FsZW5kYXIgRE9NIGVsZW1lbnQgbm90IGV4aXN0IHRoZW4gZXhpc3RcclxuXHRcdHx8ICggdHJ1ZSA9PT0galF1ZXJ5KCAnIycgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLmh0bWxfaWQgKS5oYXNDbGFzcyggJ2hhc0RhdGVwaWNrJyApIClcdC8vIElmIHRoZSBjYWxlbmRhciB3aXRoIHRoZSBzYW1lIEJvb2tpbmcgcmVzb3VyY2UgYWxyZWFkeSAgaGFzIGJlZW4gYWN0aXZhdGVkLCB0aGVuIGV4aXN0LlxyXG5cdCl7XHJcblx0ICAgcmV0dXJuIGZhbHNlO1xyXG5cdH1cclxuXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBDb25maWd1cmUgYW5kIHNob3cgY2FsZW5kYXJcclxuXHRqUXVlcnkoICcjJyArIGNhbGVuZGFyX3BhcmFtc19hcnIuaHRtbF9pZCApLnRleHQoICcnICk7XHJcblx0alF1ZXJ5KCAnIycgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLmh0bWxfaWQgKS5kYXRlcGljayh7XHJcblx0XHRcdFx0XHRiZWZvcmVTaG93RGF5OiBcdGZ1bmN0aW9uICggZGF0ZSApe1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdHJldHVybiB3cGJjX19pbmxpbmVfYm9va2luZ19jYWxlbmRhcl9fYXBwbHlfY3NzX3RvX2RheXMoIGRhdGUsIGNhbGVuZGFyX3BhcmFtc19hcnIsIHRoaXMgKTtcclxuXHRcdFx0XHRcdFx0XHRcdFx0fSxcclxuICAgICAgICAgICAgICAgICAgICBvblNlbGVjdDogXHQgIFx0ZnVuY3Rpb24gKCBkYXRlICl7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0alF1ZXJ5KCAnIycgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLnRleHRfaWQgKS52YWwoIGRhdGUgKTtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHQvL3dwYmNfYmxpbmtfZWxlbWVudCgnLndwYmNfd2lkZ2V0X2F2YWlsYWJsZV91bmF2YWlsYWJsZScsIDMsIDIyMCk7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0cmV0dXJuIHdwYmNfX2lubGluZV9ib29raW5nX2NhbGVuZGFyX19vbl9kYXlzX3NlbGVjdCggZGF0ZSwgY2FsZW5kYXJfcGFyYW1zX2FyciwgdGhpcyApO1xyXG5cdFx0XHRcdFx0XHRcdFx0XHR9LFxyXG4gICAgICAgICAgICAgICAgICAgIG9uSG92ZXI6IFx0XHRmdW5jdGlvbiAoIHZhbHVlLCBkYXRlICl7XHJcblxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdC8vd3BiY19hdnlfX3ByZXBhcmVfdG9vbHRpcF9faW5fY2FsZW5kYXIoIHZhbHVlLCBkYXRlLCBjYWxlbmRhcl9wYXJhbXNfYXJyLCB0aGlzICk7XHJcblxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdHJldHVybiB3cGJjX19pbmxpbmVfYm9va2luZ19jYWxlbmRhcl9fb25fZGF5c19ob3ZlciggdmFsdWUsIGRhdGUsIGNhbGVuZGFyX3BhcmFtc19hcnIsIHRoaXMgKTtcclxuXHRcdFx0XHRcdFx0XHRcdFx0fSxcclxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZU1vbnRoWWVhcjpcdG51bGwsXHJcbiAgICAgICAgICAgICAgICAgICAgc2hvd09uOiBcdFx0XHQnYm90aCcsXHJcbiAgICAgICAgICAgICAgICAgICAgbnVtYmVyT2ZNb250aHM6IFx0Y2FsZW5kYXJfcGFyYW1zX2Fyci5jYWxlbmRhcl9fdmlld19fdmlzaWJsZV9tb250aHMsXHJcbiAgICAgICAgICAgICAgICAgICAgc3RlcE1vbnRoczpcdFx0XHQxLFxyXG4gICAgICAgICAgICAgICAgICAgIHByZXZUZXh0OiBcdFx0XHQnJmxhcXVvOycsXHJcbiAgICAgICAgICAgICAgICAgICAgbmV4dFRleHQ6IFx0XHRcdCcmcmFxdW87JyxcclxuICAgICAgICAgICAgICAgICAgICBkYXRlRm9ybWF0OiBcdFx0J3l5LW1tLWRkJywvLyAnZGQubW0ueXknLFxyXG4gICAgICAgICAgICAgICAgICAgIGNoYW5nZU1vbnRoOiBcdFx0ZmFsc2UsXHJcbiAgICAgICAgICAgICAgICAgICAgY2hhbmdlWWVhcjogXHRcdGZhbHNlLFxyXG4gICAgICAgICAgICAgICAgICAgIG1pbkRhdGU6IFx0XHRcdFx0XHQgMCxcdFx0Ly9udWxsLCAgLy9TY3JvbGwgYXMgbG9uZyBhcyB5b3UgbmVlZFxyXG5cdFx0XHRcdFx0bWF4RGF0ZTogXHRcdFx0XHRcdCcxMHknLFx0Ly8gbWluRGF0ZTogbmV3IERhdGUoMjAyMCwgMiwgMSksIG1heERhdGU6IG5ldyBEYXRlKDIwMjAsIDksIDMxKSwgXHQvLyBBYmlsaXR5IHRvIHNldCBhbnkgIHN0YXJ0IGFuZCBlbmQgZGF0ZSBpbiBjYWxlbmRhclxyXG4gICAgICAgICAgICAgICAgICAgIHNob3dTdGF0dXM6IFx0XHRmYWxzZSxcclxuICAgICAgICAgICAgICAgICAgICBjbG9zZUF0VG9wOiBcdFx0ZmFsc2UsXHJcbiAgICAgICAgICAgICAgICAgICAgZmlyc3REYXk6XHRcdFx0Y2FsZW5kYXJfcGFyYW1zX2Fyci5jYWxlbmRhcl9fc3RhcnRfd2Vla19kYXksXHJcbiAgICAgICAgICAgICAgICAgICAgZ290b0N1cnJlbnQ6IFx0XHRmYWxzZSxcclxuICAgICAgICAgICAgICAgICAgICBoaWRlSWZOb1ByZXZOZXh0Olx0dHJ1ZSxcclxuICAgICAgICAgICAgICAgICAgICBtdWx0aVNlcGFyYXRvcjogXHQnLCAnLFxyXG5cdFx0XHRcdFx0bXVsdGlTZWxlY3Q6ICgoJ2R5bmFtaWMnID09IGNhbGVuZGFyX3BhcmFtc19hcnIuY2FsZW5kYXJfX2RheXNfc2VsZWN0aW9uX21vZGUpID8gMCA6IDM2NSksXHRcdFx0Ly8gTWF4aW11bSBudW1iZXIgb2Ygc2VsZWN0YWJsZSBkYXRlczpcdCBTaW5nbGUgZGF5ID0gMCwgIG11bHRpIGRheXMgPSAzNjVcclxuXHRcdFx0XHRcdHJhbmdlU2VsZWN0OiAgKCdkeW5hbWljJyA9PSBjYWxlbmRhcl9wYXJhbXNfYXJyLmNhbGVuZGFyX19kYXlzX3NlbGVjdGlvbl9tb2RlKSxcclxuXHRcdFx0XHRcdHJhbmdlU2VwYXJhdG9yOiBcdCcgfiAnLFx0XHRcdFx0XHQvLycgLSAnLFxyXG4gICAgICAgICAgICAgICAgICAgIC8vIHNob3dXZWVrczogdHJ1ZSxcclxuICAgICAgICAgICAgICAgICAgICB1c2VUaGVtZVJvbGxlcjpcdFx0ZmFsc2VcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICApO1xyXG5cclxuXHRyZXR1cm4gIHRydWU7XHJcbn1cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIEFwcGx5IENTUyB0byBjYWxlbmRhciBkYXRlIGNlbGxzXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gZGF0ZVx0XHRcdFx0XHQtICBKYXZhU2NyaXB0IERhdGUgT2JqOiAgXHRcdE1vbiBEZWMgMTEgMjAyMyAwMDowMDowMCBHTVQrMDIwMCAoRWFzdGVybiBFdXJvcGVhbiBTdGFuZGFyZCBUaW1lKVxyXG5cdCAqIEBwYXJhbSBjYWxlbmRhcl9wYXJhbXNfYXJyXHQtICBDYWxlbmRhciBTZXR0aW5ncyBPYmplY3Q6ICBcdHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJodG1sX2lkXCI6IFwiY2FsZW5kYXJfYm9va2luZzRcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJ0ZXh0X2lkXCI6IFwiZGF0ZV9ib29raW5nNFwiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICBcImNhbGVuZGFyX19zdGFydF93ZWVrX2RheVwiOiAxLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICBcImNhbGVuZGFyX192aWV3X192aXNpYmxlX21vbnRoc1wiOiAxMixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJyZXNvdXJjZV9pZFwiOiA0LFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICBcImFqeF9ub25jZV9jYWxlbmRhclwiOiBcIjxpbnB1dCB0eXBlPVxcXCJoaWRkZW5cXFwiIC4uLiAvPlwiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICBcImJvb2tlZF9kYXRlc1wiOiB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFwiMTItMjgtMjAyMlwiOiBbXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFwiYm9va2luZ19kYXRlXCI6IFwiMjAyMi0xMi0yOCAwMDowMDowMFwiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFwiYXBwcm92ZWRcIjogXCIxXCIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCJib29raW5nX2lkXCI6IFwiMjZcIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XSwgLi4uXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3NlYXNvbl9hdmFpbGFiaWxpdHknOntcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcIjIwMjMtMDEtMDlcIjogdHJ1ZSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcIjIwMjMtMDEtMTBcIjogdHJ1ZSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcIjIwMjMtMDEtMTFcIjogdHJ1ZSwgLi4uXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgfVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fVxyXG5cdCAqIEBwYXJhbSBkYXRlcGlja190aGlzXHRcdFx0LSB0aGlzIG9mIGRhdGVwaWNrIE9ialxyXG5cdCAqXHJcblx0ICogQHJldHVybnMgW2Jvb2xlYW4sc3RyaW5nXVx0LSBbIHt0cnVlIC1hdmFpbGFibGUgfCBmYWxzZSAtIHVuYXZhaWxhYmxlfSwgJ0NTUyBjbGFzc2VzIGZvciBjYWxlbmRhciBkYXkgY2VsbCcgXVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfX2lubGluZV9ib29raW5nX2NhbGVuZGFyX19hcHBseV9jc3NfdG9fZGF5cyggZGF0ZSwgY2FsZW5kYXJfcGFyYW1zX2FyciwgZGF0ZXBpY2tfdGhpcyApe1xyXG5cclxuXHRcdHZhciB0b2RheV9kYXRlID0gbmV3IERhdGUoIHdwYmNfdG9kYXlbIDAgXSwgKHBhcnNlSW50KCB3cGJjX3RvZGF5WyAxIF0gKSAtIDEpLCB3cGJjX3RvZGF5WyAyIF0sIDAsIDAsIDAgKTtcclxuXHJcblx0XHR2YXIgY2xhc3NfZGF5ICA9ICggZGF0ZS5nZXRNb250aCgpICsgMSApICsgJy0nICsgZGF0ZS5nZXREYXRlKCkgKyAnLScgKyBkYXRlLmdldEZ1bGxZZWFyKCk7XHRcdFx0XHRcdFx0Ly8gJzEtOS0yMDIzJ1xyXG5cdFx0dmFyIHNxbF9jbGFzc19kYXkgPSBkYXRlLmdldEZ1bGxZZWFyKCkgKyAnLSc7XHJcblx0XHRcdHNxbF9jbGFzc19kYXkgKz0gKCAoZGF0ZS5nZXRNb250aCgpICsgMSkgPCAxMCApID8gJzAnIDogJyc7XHJcblx0XHRcdHNxbF9jbGFzc19kYXkgKz0gKGRhdGUuZ2V0TW9udGgoKSArIDEpKyAnLSdcclxuXHRcdFx0c3FsX2NsYXNzX2RheSArPSAoIGRhdGUuZ2V0RGF0ZSgpIDwgMTAgKSA/ICcwJyA6ICcnO1xyXG5cdFx0XHRzcWxfY2xhc3NfZGF5ICs9IGRhdGUuZ2V0RGF0ZSgpO1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gJzIwMjMtMDEtMDknXHJcblxyXG5cdFx0dmFyIGNzc19kYXRlX19zdGFuZGFyZCAgID0gICdjYWw0ZGF0ZS0nICsgY2xhc3NfZGF5O1xyXG5cdFx0dmFyIGNzc19kYXRlX19hZGRpdGlvbmFsID0gJyB3cGJjX3dlZWtkYXlfJyArIGRhdGUuZ2V0RGF5KCkgKyAnICc7XHJcblxyXG5cdFx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHRcdC8vIFdFRUtEQVlTIDo6IFNldCB1bmF2YWlsYWJsZSB3ZWVrIGRheXMgZnJvbSAtIFNldHRpbmdzIEdlbmVyYWwgcGFnZSBpbiBcIkF2YWlsYWJpbGl0eVwiIHNlY3Rpb25cclxuXHRcdGZvciAoIHZhciBpID0gMDsgaSA8IHVzZXJfdW5hdmlsYWJsZV9kYXlzLmxlbmd0aDsgaSsrICl7XHJcblx0XHRcdGlmICggZGF0ZS5nZXREYXkoKSA9PSB1c2VyX3VuYXZpbGFibGVfZGF5c1sgaSBdICkge1xyXG5cdFx0XHRcdHJldHVybiBbICEhZmFsc2UsIGNzc19kYXRlX19zdGFuZGFyZCArICcgZGF0ZV91c2VyX3VuYXZhaWxhYmxlJyBcdCsgJyB3ZWVrZGF5c191bmF2YWlsYWJsZScgXTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdC8vIEJFRk9SRV9BRlRFUiA6OiBTZXQgdW5hdmFpbGFibGUgZGF5cyBCZWZvcmUgLyBBZnRlciB0aGUgVG9kYXkgZGF0ZVxyXG5cdFx0aWYgKCBcdCggKGRheXNfYmV0d2VlbiggZGF0ZSwgdG9kYXlfZGF0ZSApKSA8IGJsb2NrX3NvbWVfZGF0ZXNfZnJvbV90b2RheSApXHJcblx0XHRcdCB8fCAoXHJcblx0XHRcdFx0ICAgKCB0eXBlb2YoIHdwYmNfYXZhaWxhYmxlX2RheXNfbnVtX2Zyb21fdG9kYXkgKSAhPT0gJ3VuZGVmaW5lZCcgKVxyXG5cdFx0XHRcdCYmICggcGFyc2VJbnQoICcwJyArIHdwYmNfYXZhaWxhYmxlX2RheXNfbnVtX2Zyb21fdG9kYXkgKSA+IDAgKVxyXG5cdFx0XHRcdCYmICggZGF5c19iZXR3ZWVuKCBkYXRlLCB0b2RheV9kYXRlICkgPiBwYXJzZUludCggJzAnICsgd3BiY19hdmFpbGFibGVfZGF5c19udW1fZnJvbV90b2RheSApIClcclxuXHRcdFx0XHQpXHJcblx0XHQpe1xyXG5cdFx0XHRyZXR1cm4gWyAhIWZhbHNlLCBjc3NfZGF0ZV9fc3RhbmRhcmQgKyAnIGRhdGVfdXNlcl91bmF2YWlsYWJsZScgXHRcdCsgJyBiZWZvcmVfYWZ0ZXJfdW5hdmFpbGFibGUnIF07XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gU0VBU09OUyA6OiAgXHRcdFx0XHRcdEJvb2tpbmcgPiBSZXNvdXJjZXMgPiBBdmFpbGFiaWxpdHkgcGFnZVxyXG5cdFx0dmFyICAgIGlzX2RhdGVfYXZhaWxhYmxlID0gY2FsZW5kYXJfcGFyYW1zX2Fyci5zZWFzb25fYXZhaWxhYmlsaXR5WyBzcWxfY2xhc3NfZGF5IF07XHJcblx0XHRpZiAoIGZhbHNlID09PSBpc19kYXRlX2F2YWlsYWJsZSApe1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvL0ZpeEluOiA5LjUuNC40XHJcblx0XHRcdHJldHVybiBbICEhZmFsc2UsIGNzc19kYXRlX19zdGFuZGFyZCArICcgZGF0ZV91c2VyX3VuYXZhaWxhYmxlJ1x0XHQrICcgc2Vhc29uX3VuYXZhaWxhYmxlJyBdO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIFJFU09VUkNFX1VOQVZBSUxBQkxFIDo6ICAgXHRCb29raW5nID4gQXZhaWxhYmlsaXR5IHBhZ2VcclxuXHRcdGlmICggd3BkZXZfaW5fYXJyYXkoY2FsZW5kYXJfcGFyYW1zX2Fyci5yZXNvdXJjZV91bmF2YWlsYWJsZV9kYXRlcywgc3FsX2NsYXNzX2RheSApICl7XHJcblx0XHRcdGlzX2RhdGVfYXZhaWxhYmxlID0gZmFsc2U7XHJcblx0XHR9XHJcblx0XHRpZiAoICBmYWxzZSA9PT0gaXNfZGF0ZV9hdmFpbGFibGUgKXtcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vRml4SW46IDkuNS40LjRcclxuXHRcdFx0cmV0dXJuIFsgIWZhbHNlLCBjc3NfZGF0ZV9fc3RhbmRhcmQgKyAnIGRhdGVfdXNlcl91bmF2YWlsYWJsZSdcdFx0KyAnIHJlc291cmNlX3VuYXZhaWxhYmxlJyBdO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblx0XHRjc3NfZGF0ZV9fYWRkaXRpb25hbCArPSB3cGJjX19pbmxpbmVfYm9va2luZ19jYWxlbmRhcl9fZGF5c19jc3NfX2dldF9yYXRlKCBjbGFzc19kYXksIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgKTsgICAgICAgICAgICAgICAgLy8gJyByYXRlXzEwMCdcclxuXHRcdGNzc19kYXRlX19hZGRpdGlvbmFsICs9IHdwYmNfX2lubGluZV9ib29raW5nX2NhbGVuZGFyX19kYXlzX2Nzc19fZ2V0X3NlYXNvbl9uYW1lcyggY2xhc3NfZGF5LCBjYWxlbmRhcl9wYXJhbXNfYXJyLnJlc291cmNlX2lkICk7ICAgICAgICAvLyAnIHdlZWtlbmRfc2Vhc29uIGhpZ2hfc2Vhc29uJ1xyXG5cclxuXHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblxyXG5cdFx0Ly8gSXMgYW55IGJvb2tpbmdzIGluIHRoaXMgZGF0ZSA/XHJcblx0XHRpZiAoICd1bmRlZmluZWQnICE9PSB0eXBlb2YoIGNhbGVuZGFyX3BhcmFtc19hcnIuYm9va2VkX2RhdGVzWyBjbGFzc19kYXkgXSApICkge1xyXG5cclxuXHRcdFx0dmFyIGJvb2tpbmdzX2luX2RhdGUgPSBjYWxlbmRhcl9wYXJhbXNfYXJyLmJvb2tlZF9kYXRlc1sgY2xhc3NfZGF5IF07XHJcblxyXG5cclxuXHRcdFx0aWYgKCAndW5kZWZpbmVkJyAhPT0gdHlwZW9mKCBib29raW5nc19pbl9kYXRlWyAnc2VjXzAnIF0gKSApIHtcdFx0XHQvLyBcIkZ1bGwgZGF5XCIgYm9va2luZyAgLT4gKHNlY29uZHMgPT0gMClcclxuXHJcblx0XHRcdFx0Y3NzX2RhdGVfX2FkZGl0aW9uYWwgKz0gKCAnMCcgPT09IGJvb2tpbmdzX2luX2RhdGVbICdzZWNfMCcgXS5hcHByb3ZlZCApID8gJyBkYXRlMmFwcHJvdmUgJyA6ICcgZGF0ZV9hcHByb3ZlZCAnO1x0XHRcdFx0Ly8gUGVuZGluZyA9ICcwJyB8ICBBcHByb3ZlZCA9ICcxJ1xyXG5cdFx0XHRcdGNzc19kYXRlX19hZGRpdGlvbmFsICs9ICcgZnVsbF9kYXlfYm9va2luZyc7XHJcblxyXG5cdFx0XHRcdHJldHVybiBbICFmYWxzZSwgY3NzX2RhdGVfX3N0YW5kYXJkICsgY3NzX2RhdGVfX2FkZGl0aW9uYWwgXTtcclxuXHJcblx0XHRcdH0gZWxzZSBpZiAoIE9iamVjdC5rZXlzKCBib29raW5nc19pbl9kYXRlICkubGVuZ3RoID4gMCApe1x0XHRcdFx0Ly8gXCJUaW1lIHNsb3RzXCIgQm9va2luZ3NcclxuXHJcblx0XHRcdFx0dmFyIGlzX2FwcHJvdmVkID0gdHJ1ZTtcclxuXHJcblx0XHRcdFx0Xy5lYWNoKCBib29raW5nc19pbl9kYXRlLCBmdW5jdGlvbiAoIHBfdmFsLCBwX2tleSwgcF9kYXRhICkge1xyXG5cdFx0XHRcdFx0aWYgKCAhcGFyc2VJbnQoIHBfdmFsLmFwcHJvdmVkICkgKXtcclxuXHRcdFx0XHRcdFx0aXNfYXBwcm92ZWQgPSBmYWxzZTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdHZhciB0cyA9IHBfdmFsLmJvb2tpbmdfZGF0ZS5zdWJzdHJpbmcoIHBfdmFsLmJvb2tpbmdfZGF0ZS5sZW5ndGggLSAxICk7XHJcblx0XHRcdFx0XHRpZiAoIHRydWUgPT09IGlzX2Jvb2tpbmdfdXNlZF9jaGVja19pbl9vdXRfdGltZSApe1xyXG5cdFx0XHRcdFx0XHRpZiAoIHRzID09ICcxJyApIHsgY3NzX2RhdGVfX2FkZGl0aW9uYWwgKz0gJyBjaGVja19pbl90aW1lJyArICgocGFyc2VJbnQocF92YWwuYXBwcm92ZWQpKSA/ICcgY2hlY2tfaW5fdGltZV9kYXRlX2FwcHJvdmVkJyA6ICcgY2hlY2tfaW5fdGltZV9kYXRlMmFwcHJvdmUnKTsgfVxyXG5cdFx0XHRcdFx0XHRpZiAoIHRzID09ICcyJyApIHsgY3NzX2RhdGVfX2FkZGl0aW9uYWwgKz0gJyBjaGVja19vdXRfdGltZScgKyAoKHBhcnNlSW50KHBfdmFsLmFwcHJvdmVkKSkgPyAnIGNoZWNrX291dF90aW1lX2RhdGVfYXBwcm92ZWQnIDogJyBjaGVja19vdXRfdGltZV9kYXRlMmFwcHJvdmUnKTsgfVxyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHR9KTtcclxuXHJcblx0XHRcdFx0aWYgKCAhIGlzX2FwcHJvdmVkICl7XHJcblx0XHRcdFx0XHRjc3NfZGF0ZV9fYWRkaXRpb25hbCArPSAnIGRhdGUyYXBwcm92ZSB0aW1lc3BhcnRseSdcclxuXHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0Y3NzX2RhdGVfX2FkZGl0aW9uYWwgKz0gJyBkYXRlX2FwcHJvdmVkIHRpbWVzcGFydGx5J1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0aWYgKCAhIGlzX2Jvb2tpbmdfdXNlZF9jaGVja19pbl9vdXRfdGltZSApe1xyXG5cdFx0XHRcdFx0Y3NzX2RhdGVfX2FkZGl0aW9uYWwgKz0gJyB0aW1lc19jbG9jaydcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHR9XHJcblxyXG5cdFx0fVxyXG5cclxuXHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblx0XHRyZXR1cm4gWyB0cnVlLCBjc3NfZGF0ZV9fc3RhbmRhcmQgKyBjc3NfZGF0ZV9fYWRkaXRpb25hbCArICcgZGF0ZV9hdmFpbGFibGUnIF07XHJcblx0fVxyXG5cclxuXHJcblx0LyoqXHJcblx0ICogQXBwbHkgc29tZSBDU1MgY2xhc3Nlcywgd2hlbiB3ZSBtb3VzZSBvdmVyIHNwZWNpZmljIGRhdGVzIGluIGNhbGVuZGFyXHJcblx0ICogQHBhcmFtIHZhbHVlXHJcblx0ICogQHBhcmFtIGRhdGVcdFx0XHRcdFx0LSAgSmF2YVNjcmlwdCBEYXRlIE9iajogIFx0XHRNb24gRGVjIDExIDIwMjMgMDA6MDA6MDAgR01UKzAyMDAgKEVhc3Rlcm4gRXVyb3BlYW4gU3RhbmRhcmQgVGltZSlcclxuXHQgKiBAcGFyYW0gY2FsZW5kYXJfcGFyYW1zX2Fyclx0LSAgQ2FsZW5kYXIgU2V0dGluZ3MgT2JqZWN0OiAgXHR7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwiaHRtbF9pZFwiOiBcImNhbGVuZGFyX2Jvb2tpbmc0XCIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwidGV4dF9pZFwiOiBcImRhdGVfYm9va2luZzRcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJjYWxlbmRhcl9fc3RhcnRfd2Vla19kYXlcIjogMSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJjYWxlbmRhcl9fdmlld19fdmlzaWJsZV9tb250aHNcIjogMTIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwicmVzb3VyY2VfaWRcIjogNCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJhanhfbm9uY2VfY2FsZW5kYXJcIjogXCI8aW5wdXQgdHlwZT1cXFwiaGlkZGVuXFxcIiAuLi4gLz5cIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJib29rZWRfZGF0ZXNcIjoge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcIjEyLTI4LTIwMjJcIjogW1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcImJvb2tpbmdfZGF0ZVwiOiBcIjIwMjItMTItMjggMDA6MDA6MDBcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcImFwcHJvdmVkXCI6IFwiMVwiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFwiYm9va2luZ19pZFwiOiBcIjI2XCJcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICB9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdF0sIC4uLlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzZWFzb25fYXZhaWxhYmlsaXR5Jzp7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTA5XCI6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTEwXCI6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTExXCI6IHRydWUsIC4uLlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH1cclxuXHQgKiBAcGFyYW0gZGF0ZXBpY2tfdGhpc1x0XHRcdC0gdGhpcyBvZiBkYXRlcGljayBPYmpcclxuXHQgKlxyXG5cdCAqIEByZXR1cm5zIHtib29sZWFufVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfX2lubGluZV9ib29raW5nX2NhbGVuZGFyX19vbl9kYXlzX2hvdmVyKCB2YWx1ZSwgZGF0ZSwgY2FsZW5kYXJfcGFyYW1zX2FyciwgZGF0ZXBpY2tfdGhpcyApe1xyXG5cclxuXHRcdGlmICggbnVsbCA9PT0gZGF0ZSApe1xyXG5cdFx0XHRqUXVlcnkoICcuZGF0ZXBpY2stZGF5cy1jZWxsLW92ZXInICkucmVtb3ZlQ2xhc3MoICdkYXRlcGljay1kYXlzLWNlbGwtb3ZlcicgKTsgICBcdCAgICAgICAgICAgICAgICAgICAgICAgIC8vIGNsZWFyIGFsbCBoaWdobGlnaHQgZGF5cyBzZWxlY3Rpb25zXHJcblx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdH1cclxuXHJcblx0XHR2YXIgaW5zdCA9IGpRdWVyeS5kYXRlcGljay5fZ2V0SW5zdCggZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICdjYWxlbmRhcl9ib29raW5nJyArIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgKSApO1xyXG5cclxuXHRcdGlmIChcclxuXHRcdFx0ICAgKCAxID09IGluc3QuZGF0ZXMubGVuZ3RoKVx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIElmIHdlIGhhdmUgb25lIHNlbGVjdGVkIGRhdGVcclxuXHRcdFx0JiYgKCdkeW5hbWljJyA9PT0gY2FsZW5kYXJfcGFyYW1zX2Fyci5jYWxlbmRhcl9fZGF5c19zZWxlY3Rpb25fbW9kZSkgXHRcdFx0XHRcdC8vIHdoaWxlIGhhdmUgcmFuZ2UgZGF5cyBzZWxlY3Rpb24gbW9kZVxyXG5cdFx0KXtcclxuXHJcblx0XHRcdHZhciB0ZF9jbGFzcztcclxuXHRcdFx0dmFyIHRkX292ZXJzID0gW107XHJcblx0XHRcdHZhciBpc19jaGVjayA9IHRydWU7XHJcbiAgICAgICAgICAgIHZhciBzZWxjZXRlZF9maXJzdF9kYXkgPSBuZXcgRGF0ZSgpO1xyXG4gICAgICAgICAgICBzZWxjZXRlZF9maXJzdF9kYXkuc2V0RnVsbFllYXIoaW5zdC5kYXRlc1swXS5nZXRGdWxsWWVhcigpLChpbnN0LmRhdGVzWzBdLmdldE1vbnRoKCkpLCAoaW5zdC5kYXRlc1swXS5nZXREYXRlKCkgKSApOyAvL0dldCBmaXJzdCBEYXRlXHJcblxyXG4gICAgICAgICAgICB3aGlsZSggIGlzX2NoZWNrICl7XHJcblxyXG5cdFx0XHRcdHRkX2NsYXNzID0gKHNlbGNldGVkX2ZpcnN0X2RheS5nZXRNb250aCgpICsgMSkgKyAnLScgKyBzZWxjZXRlZF9maXJzdF9kYXkuZ2V0RGF0ZSgpICsgJy0nICsgc2VsY2V0ZWRfZmlyc3RfZGF5LmdldEZ1bGxZZWFyKCk7XHJcblxyXG5cdFx0XHRcdHRkX292ZXJzWyB0ZF9vdmVycy5sZW5ndGggXSA9ICcjY2FsZW5kYXJfYm9va2luZycgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLnJlc291cmNlX2lkICsgJyAuY2FsNGRhdGUtJyArIHRkX2NsYXNzOyAgICAgICAgICAgICAgLy8gYWRkIHRvIGFycmF5IGZvciBsYXRlciBtYWtlIHNlbGVjdGlvbiBieSBjbGFzc1xyXG5cclxuICAgICAgICAgICAgICAgIGlmIChcclxuXHRcdFx0XHRcdCggICggZGF0ZS5nZXRNb250aCgpID09IHNlbGNldGVkX2ZpcnN0X2RheS5nZXRNb250aCgpICkgICYmXHJcbiAgICAgICAgICAgICAgICAgICAgICAgKCBkYXRlLmdldERhdGUoKSA9PSBzZWxjZXRlZF9maXJzdF9kYXkuZ2V0RGF0ZSgpICkgICYmXHJcbiAgICAgICAgICAgICAgICAgICAgICAgKCBkYXRlLmdldEZ1bGxZZWFyKCkgPT0gc2VsY2V0ZWRfZmlyc3RfZGF5LmdldEZ1bGxZZWFyKCkgKVxyXG5cdFx0XHRcdFx0KSB8fCAoIHNlbGNldGVkX2ZpcnN0X2RheSA+IGRhdGUgKVxyXG5cdFx0XHRcdCl7XHJcblx0XHRcdFx0XHRpc19jaGVjayA9ICBmYWxzZTtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdHNlbGNldGVkX2ZpcnN0X2RheS5zZXRGdWxsWWVhciggc2VsY2V0ZWRfZmlyc3RfZGF5LmdldEZ1bGxZZWFyKCksIChzZWxjZXRlZF9maXJzdF9kYXkuZ2V0TW9udGgoKSksIChzZWxjZXRlZF9maXJzdF9kYXkuZ2V0RGF0ZSgpICsgMSkgKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gSGlnaGxpZ2h0IERheXNcclxuXHRcdFx0Zm9yICggdmFyIGk9MDsgaSA8IHRkX292ZXJzLmxlbmd0aCA7IGkrKykgeyAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBhZGQgY2xhc3MgdG8gYWxsIGVsZW1lbnRzXHJcblx0XHRcdFx0alF1ZXJ5KCB0ZF9vdmVyc1tpXSApLmFkZENsYXNzKCdkYXRlcGljay1kYXlzLWNlbGwtb3ZlcicpO1xyXG5cdFx0XHR9XHJcblx0XHRcdHJldHVybiB0cnVlO1xyXG5cclxuXHRcdH1cclxuXHJcblx0ICAgIHJldHVybiB0cnVlO1xyXG5cdH1cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIE9uIERBWXMgc2VsZWN0aW9uIGluIGNhbGVuZGFyXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gZGF0ZXNfc2VsZWN0aW9uXHRcdC0gIHN0cmluZzpcdFx0XHQgJzIwMjMtMDMtMDcgfiAyMDIzLTAzLTA3JyBvciAnMjAyMy0wNC0xMCwgMjAyMy0wNC0xMiwgMjAyMy0wNC0wMiwgMjAyMy0wNC0wNCdcclxuXHQgKiBAcGFyYW0gY2FsZW5kYXJfcGFyYW1zX2Fyclx0LSAgQ2FsZW5kYXIgU2V0dGluZ3MgT2JqZWN0OiAgXHR7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwiaHRtbF9pZFwiOiBcImNhbGVuZGFyX2Jvb2tpbmc0XCIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwidGV4dF9pZFwiOiBcImRhdGVfYm9va2luZzRcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJjYWxlbmRhcl9fc3RhcnRfd2Vla19kYXlcIjogMSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJjYWxlbmRhcl9fdmlld19fdmlzaWJsZV9tb250aHNcIjogMTIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwicmVzb3VyY2VfaWRcIjogNCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJhanhfbm9uY2VfY2FsZW5kYXJcIjogXCI8aW5wdXQgdHlwZT1cXFwiaGlkZGVuXFxcIiAuLi4gLz5cIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJib29rZWRfZGF0ZXNcIjoge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcIjEyLTI4LTIwMjJcIjogW1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcImJvb2tpbmdfZGF0ZVwiOiBcIjIwMjItMTItMjggMDA6MDA6MDBcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcImFwcHJvdmVkXCI6IFwiMVwiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFwiYm9va2luZ19pZFwiOiBcIjI2XCJcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICB9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdF0sIC4uLlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzZWFzb25fYXZhaWxhYmlsaXR5Jzp7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTA5XCI6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTEwXCI6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTExXCI6IHRydWUsIC4uLlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH1cclxuXHQgKiBAcGFyYW0gZGF0ZXBpY2tfdGhpc1x0XHRcdC0gdGhpcyBvZiBkYXRlcGljayBPYmpcclxuXHQgKlxyXG5cdCAqIEByZXR1cm5zIGJvb2xlYW5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX19pbmxpbmVfYm9va2luZ19jYWxlbmRhcl9fb25fZGF5c19zZWxlY3QoIGRhdGVzX3NlbGVjdGlvbiwgY2FsZW5kYXJfcGFyYW1zX2FyciwgZGF0ZXBpY2tfdGhpcyA9IG51bGwgKXtcclxuXHJcblx0XHR2YXIgaW5zdCA9IGpRdWVyeS5kYXRlcGljay5fZ2V0SW5zdCggZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICdjYWxlbmRhcl9ib29raW5nJyArIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgKSApO1xyXG5cclxuXHRcdHZhciBkYXRlc19hcnIgPSBbXTtcdC8vICBbIFwiMjAyMy0wNC0wOVwiLCBcIjIwMjMtMDQtMTBcIiwgXCIyMDIzLTA0LTExXCIgXVxyXG5cclxuXHRcdGlmICggLTEgIT09IGRhdGVzX3NlbGVjdGlvbi5pbmRleE9mKCAnficgKSApIHsgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gUmFuZ2UgRGF5c1xyXG5cclxuXHRcdFx0ZGF0ZXNfYXJyID0gd3BiY19nZXRfZGF0ZXNfYXJyX19mcm9tX2RhdGVzX3JhbmdlX2pzKCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGF0ZXNfc2VwYXJhdG9yJyA6ICcgfiAnLCAgICAgICAgICAgICAgICAgICAgICAgICAvLyAgJyB+ICdcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdkYXRlcycgICAgICAgICAgIDogZGF0ZXNfc2VsZWN0aW9uLCAgICBcdFx0ICAgLy8gJzIwMjMtMDQtMDQgfiAyMDIzLTA0LTA3J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0gKTtcclxuXHJcblx0XHR9IGVsc2UgeyAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gTXVsdGlwbGUgRGF5c1xyXG5cdFx0XHRkYXRlc19hcnIgPSB3cGJjX2dldF9kYXRlc19hcnJfX2Zyb21fZGF0ZXNfY29tbWFfc2VwYXJhdGVkX2pzKCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGF0ZXNfc2VwYXJhdG9yJyA6ICcsICcsICAgICAgICAgICAgICAgICAgICAgICAgIFx0Ly8gICcsICdcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdkYXRlcycgICAgICAgICAgIDogZGF0ZXNfc2VsZWN0aW9uLCAgICBcdFx0XHQvLyAnMjAyMy0wNC0xMCwgMjAyMy0wNC0xMiwgMjAyMy0wNC0wMiwgMjAyMy0wNC0wNCdcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9ICk7XHJcblx0XHR9XHJcblxyXG5cdFx0d3BiY19hdnlfYWZ0ZXJfZGF5c19zZWxlY3Rpb25fX3Nob3dfaGVscF9pbmZvKHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdjYWxlbmRhcl9fZGF5c19zZWxlY3Rpb25fbW9kZSc6IGNhbGVuZGFyX3BhcmFtc19hcnIuY2FsZW5kYXJfX2RheXNfc2VsZWN0aW9uX21vZGUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGF0ZXNfYXJyJyAgICAgICAgICAgICAgICAgICAgOiBkYXRlc19hcnIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGF0ZXNfY2xpY2tfbnVtJyAgICAgICAgICAgICAgOiBpbnN0LmRhdGVzLmxlbmd0aCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdwb3BvdmVyX2hpbnRzJ1x0XHRcdFx0XHQ6IGNhbGVuZGFyX3BhcmFtc19hcnIucG9wb3Zlcl9oaW50c1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0gKTtcclxuXHRcdHJldHVybiB0cnVlO1xyXG5cdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFNob3cgaGVscCBpbmZvIGF0IHRoZSB0b3AgIHRvb2xiYXIgYWJvdXQgc2VsZWN0ZWQgZGF0ZXMgYW5kIGZ1dHVyZSBhY3Rpb25zXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHBhcmFtc1xyXG5cdFx0ICogXHRcdFx0XHRcdEV4YW1wbGUgMTogIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdGNhbGVuZGFyX19kYXlzX3NlbGVjdGlvbl9tb2RlOiBcImR5bmFtaWNcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdGRhdGVzX2FycjogIFsgXCIyMDIzLTA0LTAzXCIgXSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdGRhdGVzX2NsaWNrX251bTogMVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3BvcG92ZXJfaGludHMnXHRcdFx0XHRcdDogY2FsZW5kYXJfcGFyYW1zX2Fyci5wb3BvdmVyX2hpbnRzXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0fVxyXG5cdFx0ICogXHRcdFx0XHRcdEV4YW1wbGUgMjogIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdGNhbGVuZGFyX19kYXlzX3NlbGVjdGlvbl9tb2RlOiBcImR5bmFtaWNcIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0ZGF0ZXNfYXJyOiBBcnJheSgxMCkgWyBcIjIwMjMtMDQtMDNcIiwgXCIyMDIzLTA0LTA0XCIsIFwiMjAyMy0wNC0wNVwiLCDigKYgXVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0ZGF0ZXNfY2xpY2tfbnVtOiAyXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQncG9wb3Zlcl9oaW50cydcdFx0XHRcdFx0OiBjYWxlbmRhcl9wYXJhbXNfYXJyLnBvcG92ZXJfaGludHNcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIHdwYmNfYXZ5X2FmdGVyX2RheXNfc2VsZWN0aW9uX19zaG93X2hlbHBfaW5mbyggcGFyYW1zICl7XHJcbi8vIGNvbnNvbGUubG9nKCBwYXJhbXMgKTtcdC8vXHRcdFsgXCIyMDIzLTA0LTA5XCIsIFwiMjAyMy0wNC0xMFwiLCBcIjIwMjMtMDQtMTFcIiBdXHJcblxyXG5cdFx0XHR2YXIgbWVzc2FnZSwgY29sb3I7XHJcblx0XHRcdGlmIChqUXVlcnkoICcjdWlfYnRuX2F2eV9fc2V0X2RheXNfYXZhaWxhYmlsaXR5X19hdmFpbGFibGUnKS5pcygnOmNoZWNrZWQnKSl7XHJcblx0XHRcdFx0IG1lc3NhZ2UgPSBwYXJhbXMucG9wb3Zlcl9oaW50cy50b29sYmFyX3RleHRfYXZhaWxhYmxlOy8vJ1NldCBkYXRlcyBfREFURVNfIGFzIF9IVE1MXyBhdmFpbGFibGUuJztcclxuXHRcdFx0XHQgY29sb3IgPSAnIzExYmU0Yyc7XHJcblx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0bWVzc2FnZSA9IHBhcmFtcy5wb3BvdmVyX2hpbnRzLnRvb2xiYXJfdGV4dF91bmF2YWlsYWJsZTsvLydTZXQgZGF0ZXMgX0RBVEVTXyBhcyBfSFRNTF8gdW5hdmFpbGFibGUuJztcclxuXHRcdFx0XHRjb2xvciA9ICcjZTQzOTM5JztcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0bWVzc2FnZSA9ICc8c3Bhbj4nICsgbWVzc2FnZSArICc8L3NwYW4+JztcclxuXHJcblx0XHRcdHZhciBmaXJzdF9kYXRlID0gcGFyYW1zWyAnZGF0ZXNfYXJyJyBdWyAwIF07XHJcblx0XHRcdHZhciBsYXN0X2RhdGUgID0gKCAnZHluYW1pYycgPT0gcGFyYW1zLmNhbGVuZGFyX19kYXlzX3NlbGVjdGlvbl9tb2RlIClcclxuXHRcdFx0XHRcdFx0XHQ/IHBhcmFtc1sgJ2RhdGVzX2FycicgXVsgKHBhcmFtc1sgJ2RhdGVzX2FycicgXS5sZW5ndGggLSAxKSBdXHJcblx0XHRcdFx0XHRcdFx0OiAoIHBhcmFtc1sgJ2RhdGVzX2FycicgXS5sZW5ndGggPiAxICkgPyBwYXJhbXNbICdkYXRlc19hcnInIF1bIDEgXSA6ICcnO1xyXG5cclxuXHRcdFx0Zmlyc3RfZGF0ZSA9IGpRdWVyeS5kYXRlcGljay5mb3JtYXREYXRlKCAnZGQgTSwgeXknLCBuZXcgRGF0ZSggZmlyc3RfZGF0ZSArICdUMDA6MDA6MDAnICkgKTtcclxuXHRcdFx0bGFzdF9kYXRlID0galF1ZXJ5LmRhdGVwaWNrLmZvcm1hdERhdGUoICdkZCBNLCB5eScsICBuZXcgRGF0ZSggbGFzdF9kYXRlICsgJ1QwMDowMDowMCcgKSApO1xyXG5cclxuXHJcblx0XHRcdGlmICggJ2R5bmFtaWMnID09IHBhcmFtcy5jYWxlbmRhcl9fZGF5c19zZWxlY3Rpb25fbW9kZSApe1xyXG5cdFx0XHRcdGlmICggMSA9PSBwYXJhbXMuZGF0ZXNfY2xpY2tfbnVtICl7XHJcblx0XHRcdFx0XHRsYXN0X2RhdGUgPSAnX19fX19fX19fX18nXHJcblx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdGlmICggJ2ZpcnN0X3RpbWUnID09IGpRdWVyeSggJy53cGJjX2FqeF9hdmFpbGFiaWxpdHlfY29udGFpbmVyJyApLmF0dHIoICd3cGJjX2xvYWRlZCcgKSApe1xyXG5cdFx0XHRcdFx0XHRqUXVlcnkoICcud3BiY19hanhfYXZhaWxhYmlsaXR5X2NvbnRhaW5lcicgKS5hdHRyKCAnd3BiY19sb2FkZWQnLCAnZG9uZScgKVxyXG5cdFx0XHRcdFx0XHR3cGJjX2JsaW5rX2VsZW1lbnQoICcud3BiY193aWRnZXRfYXZhaWxhYmxlX3VuYXZhaWxhYmxlJywgMywgMjIwICk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdG1lc3NhZ2UgPSBtZXNzYWdlLnJlcGxhY2UoICdfREFURVNfJywgICAgJzwvc3Bhbj4nXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLysgJzxkaXY+JyArICdmcm9tJyArICc8L2Rpdj4nXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQrICc8c3BhbiBjbGFzcz1cIndwYmNfYmlnX2RhdGVcIj4nICsgZmlyc3RfZGF0ZSArICc8L3NwYW4+J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0KyAnPHNwYW4+JyArICctJyArICc8L3NwYW4+J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0KyAnPHNwYW4gY2xhc3M9XCJ3cGJjX2JpZ19kYXRlXCI+JyArIGxhc3RfZGF0ZSArICc8L3NwYW4+J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0KyAnPHNwYW4+JyApO1xyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdC8vIGlmICggcGFyYW1zWyAnZGF0ZXNfYXJyJyBdLmxlbmd0aCA+IDEgKXtcclxuXHRcdFx0XHQvLyBcdGxhc3RfZGF0ZSA9ICcsICcgKyBsYXN0X2RhdGU7XHJcblx0XHRcdFx0Ly8gXHRsYXN0X2RhdGUgKz0gKCBwYXJhbXNbICdkYXRlc19hcnInIF0ubGVuZ3RoID4gMiApID8gJywgLi4uJyA6ICcnO1xyXG5cdFx0XHRcdC8vIH0gZWxzZSB7XHJcblx0XHRcdFx0Ly8gXHRsYXN0X2RhdGU9Jyc7XHJcblx0XHRcdFx0Ly8gfVxyXG5cdFx0XHRcdHZhciBkYXRlc19hcnIgPSBbXTtcclxuXHRcdFx0XHRmb3IoIHZhciBpID0gMDsgaSA8IHBhcmFtc1sgJ2RhdGVzX2FycicgXS5sZW5ndGg7IGkrKyApe1xyXG5cdFx0XHRcdFx0ZGF0ZXNfYXJyLnB1c2goICBqUXVlcnkuZGF0ZXBpY2suZm9ybWF0RGF0ZSggJ2RkIE0geXknLCAgbmV3IERhdGUoIHBhcmFtc1sgJ2RhdGVzX2FycicgXVsgaSBdICsgJ1QwMDowMDowMCcgKSApICApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRmaXJzdF9kYXRlID0gZGF0ZXNfYXJyLmpvaW4oICcsICcgKTtcclxuXHRcdFx0XHRtZXNzYWdlID0gbWVzc2FnZS5yZXBsYWNlKCAnX0RBVEVTXycsICAgICc8L3NwYW4+J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0KyAnPHNwYW4gY2xhc3M9XCJ3cGJjX2JpZ19kYXRlXCI+JyArIGZpcnN0X2RhdGUgKyAnPC9zcGFuPidcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCsgJzxzcGFuPicgKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRtZXNzYWdlID0gbWVzc2FnZS5yZXBsYWNlKCAnX0hUTUxfJyAsICc8L3NwYW4+PHNwYW4gY2xhc3M9XCJ3cGJjX2JpZ190ZXh0XCIgc3R5bGU9XCJjb2xvcjonK2NvbG9yKyc7XCI+JykgKyAnPHNwYW4+JztcclxuXHJcblx0XHRcdC8vbWVzc2FnZSArPSAnIDxkaXYgc3R5bGU9XCJtYXJnaW4tbGVmdDogMWVtO1wiPicgKyAnIENsaWNrIG9uIEFwcGx5IGJ1dHRvbiB0byBhcHBseSBhdmFpbGFiaWxpdHkuJyArICc8L2Rpdj4nO1xyXG5cclxuXHRcdFx0bWVzc2FnZSA9ICc8ZGl2IGNsYXNzPVwid3BiY190b29sYmFyX2RhdGVzX2hpbnRzXCI+JyArIG1lc3NhZ2UgKyAnPC9kaXY+JztcclxuXHJcblx0XHRcdGpRdWVyeSggJy53cGJjX2hlbHBfdGV4dCcgKS5odG1sKFx0bWVzc2FnZSApO1xyXG5cdFx0fVxyXG5cclxuXHQvKipcclxuXHQgKiAgIFBhcnNlIGRhdGVzICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBHZXQgZGF0ZXMgYXJyYXksICBmcm9tIGNvbW1hIHNlcGFyYXRlZCBkYXRlc1xyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSBwYXJhbXMgICAgICAgPSB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQqICdkYXRlc19zZXBhcmF0b3InID0+ICcsICcsICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIERhdGVzIHNlcGFyYXRvclxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0KiAnZGF0ZXMnICAgICAgICAgICA9PiAnMjAyMy0wNC0wNCwgMjAyMy0wNC0wNywgMjAyMy0wNC0wNScgICAgICAgICAvLyBEYXRlcyBpbiAnWS1tLWQnIGZvcm1hdDogJzIwMjMtMDEtMzEnXHJcblx0XHRcdFx0XHRcdFx0XHQgfVxyXG5cdFx0ICpcclxuXHRcdCAqIEByZXR1cm4gYXJyYXkgICAgICA9IFtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCogWzBdID0+IDIwMjMtMDQtMDRcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCogWzFdID0+IDIwMjMtMDQtMDVcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCogWzJdID0+IDIwMjMtMDQtMDZcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCogWzNdID0+IDIwMjMtMDQtMDdcclxuXHRcdFx0XHRcdFx0XHRcdF1cclxuXHRcdCAqXHJcblx0XHQgKiBFeGFtcGxlICMxOiAgd3BiY19nZXRfZGF0ZXNfYXJyX19mcm9tX2RhdGVzX2NvbW1hX3NlcGFyYXRlZF9qcyggIHsgICdkYXRlc19zZXBhcmF0b3InIDogJywgJywgJ2RhdGVzJyA6ICcyMDIzLTA0LTA0LCAyMDIzLTA0LTA3LCAyMDIzLTA0LTA1JyAgfSAgKTtcclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gd3BiY19nZXRfZGF0ZXNfYXJyX19mcm9tX2RhdGVzX2NvbW1hX3NlcGFyYXRlZF9qcyggcGFyYW1zICl7XHJcblxyXG5cdFx0XHR2YXIgZGF0ZXNfYXJyID0gW107XHJcblxyXG5cdFx0XHRpZiAoICcnICE9PSBwYXJhbXNbICdkYXRlcycgXSApe1xyXG5cclxuXHRcdFx0XHRkYXRlc19hcnIgPSBwYXJhbXNbICdkYXRlcycgXS5zcGxpdCggcGFyYW1zWyAnZGF0ZXNfc2VwYXJhdG9yJyBdICk7XHJcblxyXG5cdFx0XHRcdGRhdGVzX2Fyci5zb3J0KCk7XHJcblx0XHRcdH1cclxuXHRcdFx0cmV0dXJuIGRhdGVzX2FycjtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEdldCBkYXRlcyBhcnJheSwgIGZyb20gcmFuZ2UgZGF5cyBzZWxlY3Rpb25cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0gcGFyYW1zICAgICAgID0gIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCogJ2RhdGVzX3NlcGFyYXRvcicgPT4gJyB+ICcsICAgICAgICAgICAgICAgICAgICAgICAgIC8vIERhdGVzIHNlcGFyYXRvclxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0KiAnZGF0ZXMnICAgICAgICAgICA9PiAnMjAyMy0wNC0wNCB+IDIwMjMtMDQtMDcnICAgICAgLy8gRGF0ZXMgaW4gJ1ktbS1kJyBmb3JtYXQ6ICcyMDIzLTAxLTMxJ1xyXG5cdFx0XHRcdFx0XHRcdFx0ICB9XHJcblx0XHQgKlxyXG5cdFx0ICogQHJldHVybiBhcnJheSAgICAgICAgPSBbXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQqIFswXSA9PiAyMDIzLTA0LTA0XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQqIFsxXSA9PiAyMDIzLTA0LTA1XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQqIFsyXSA9PiAyMDIzLTA0LTA2XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQqIFszXSA9PiAyMDIzLTA0LTA3XHJcblx0XHRcdFx0XHRcdFx0XHQgIF1cclxuXHRcdCAqXHJcblx0XHQgKiBFeGFtcGxlICMxOiAgd3BiY19nZXRfZGF0ZXNfYXJyX19mcm9tX2RhdGVzX3JhbmdlX2pzKCAgeyAgJ2RhdGVzX3NlcGFyYXRvcicgOiAnIH4gJywgJ2RhdGVzJyA6ICcyMDIzLTA0LTA0IH4gMjAyMy0wNC0wNycgIH0gICk7XHJcblx0XHQgKiBFeGFtcGxlICMyOiAgd3BiY19nZXRfZGF0ZXNfYXJyX19mcm9tX2RhdGVzX3JhbmdlX2pzKCAgeyAgJ2RhdGVzX3NlcGFyYXRvcicgOiAnIC0gJywgJ2RhdGVzJyA6ICcyMDIzLTA0LTA0IC0gMjAyMy0wNC0wNycgIH0gICk7XHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIHdwYmNfZ2V0X2RhdGVzX2Fycl9fZnJvbV9kYXRlc19yYW5nZV9qcyggcGFyYW1zICl7XHJcblxyXG5cdFx0XHR2YXIgZGF0ZXNfYXJyID0gW107XHJcblxyXG5cdFx0XHRpZiAoICcnICE9PSBwYXJhbXNbJ2RhdGVzJ10gKSB7XHJcblxyXG5cdFx0XHRcdGRhdGVzX2FyciA9IHBhcmFtc1sgJ2RhdGVzJyBdLnNwbGl0KCBwYXJhbXNbICdkYXRlc19zZXBhcmF0b3InIF0gKTtcclxuXHRcdFx0XHR2YXIgY2hlY2tfaW5fZGF0ZV95bWQgID0gZGF0ZXNfYXJyWzBdO1xyXG5cdFx0XHRcdHZhciBjaGVja19vdXRfZGF0ZV95bWQgPSBkYXRlc19hcnJbMV07XHJcblxyXG5cdFx0XHRcdGlmICggKCcnICE9PSBjaGVja19pbl9kYXRlX3ltZCkgJiYgKCcnICE9PSBjaGVja19vdXRfZGF0ZV95bWQpICl7XHJcblxyXG5cdFx0XHRcdFx0ZGF0ZXNfYXJyID0gd3BiY19nZXRfZGF0ZXNfYXJyYXlfZnJvbV9zdGFydF9lbmRfZGF5c19qcyggY2hlY2tfaW5fZGF0ZV95bWQsIGNoZWNrX291dF9kYXRlX3ltZCApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gZGF0ZXNfYXJyO1xyXG5cdFx0fVxyXG5cclxuXHRcdFx0LyoqXHJcblx0XHRcdCAqIEdldCBkYXRlcyBhcnJheSBiYXNlZCBvbiBzdGFydCBhbmQgZW5kIGRhdGVzLlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcGFyYW0gc3RyaW5nIHNTdGFydERhdGUgLSBzdGFydCBkYXRlOiAyMDIzLTA0LTA5XHJcblx0XHRcdCAqIEBwYXJhbSBzdHJpbmcgc0VuZERhdGUgICAtIGVuZCBkYXRlOiAgIDIwMjMtMDQtMTFcclxuXHRcdFx0ICogQHJldHVybiBhcnJheSAgICAgICAgICAgICAtIFsgXCIyMDIzLTA0LTA5XCIsIFwiMjAyMy0wNC0xMFwiLCBcIjIwMjMtMDQtMTFcIiBdXHJcblx0XHRcdCAqL1xyXG5cdFx0XHRmdW5jdGlvbiB3cGJjX2dldF9kYXRlc19hcnJheV9mcm9tX3N0YXJ0X2VuZF9kYXlzX2pzKCBzU3RhcnREYXRlLCBzRW5kRGF0ZSApe1xyXG5cclxuXHRcdFx0XHRzU3RhcnREYXRlID0gbmV3IERhdGUoIHNTdGFydERhdGUgKyAnVDAwOjAwOjAwJyApO1xyXG5cdFx0XHRcdHNFbmREYXRlID0gbmV3IERhdGUoIHNFbmREYXRlICsgJ1QwMDowMDowMCcgKTtcclxuXHJcblx0XHRcdFx0dmFyIGFEYXlzPVtdO1xyXG5cclxuXHRcdFx0XHQvLyBTdGFydCB0aGUgdmFyaWFibGUgb2ZmIHdpdGggdGhlIHN0YXJ0IGRhdGVcclxuXHRcdFx0XHRhRGF5cy5wdXNoKCBzU3RhcnREYXRlLmdldFRpbWUoKSApO1xyXG5cclxuXHRcdFx0XHQvLyBTZXQgYSAndGVtcCcgdmFyaWFibGUsIHNDdXJyZW50RGF0ZSwgd2l0aCB0aGUgc3RhcnQgZGF0ZSAtIGJlZm9yZSBiZWdpbm5pbmcgdGhlIGxvb3BcclxuXHRcdFx0XHR2YXIgc0N1cnJlbnREYXRlID0gbmV3IERhdGUoIHNTdGFydERhdGUuZ2V0VGltZSgpICk7XHJcblx0XHRcdFx0dmFyIG9uZV9kYXlfZHVyYXRpb24gPSAyNCo2MCo2MCoxMDAwO1xyXG5cclxuXHRcdFx0XHQvLyBXaGlsZSB0aGUgY3VycmVudCBkYXRlIGlzIGxlc3MgdGhhbiB0aGUgZW5kIGRhdGVcclxuXHRcdFx0XHR3aGlsZShzQ3VycmVudERhdGUgPCBzRW5kRGF0ZSl7XHJcblx0XHRcdFx0XHQvLyBBZGQgYSBkYXkgdG8gdGhlIGN1cnJlbnQgZGF0ZSBcIisxIGRheVwiXHJcblx0XHRcdFx0XHRzQ3VycmVudERhdGUuc2V0VGltZSggc0N1cnJlbnREYXRlLmdldFRpbWUoKSArIG9uZV9kYXlfZHVyYXRpb24gKTtcclxuXHJcblx0XHRcdFx0XHQvLyBBZGQgdGhpcyBuZXcgZGF5IHRvIHRoZSBhRGF5cyBhcnJheVxyXG5cdFx0XHRcdFx0YURheXMucHVzaCggc0N1cnJlbnREYXRlLmdldFRpbWUoKSApO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0Zm9yIChsZXQgaSA9IDA7IGkgPCBhRGF5cy5sZW5ndGg7IGkrKykge1xyXG5cdFx0XHRcdFx0YURheXNbIGkgXSA9IG5ldyBEYXRlKCBhRGF5c1tpXSApO1xyXG5cdFx0XHRcdFx0YURheXNbIGkgXSA9IGFEYXlzWyBpIF0uZ2V0RnVsbFllYXIoKVxyXG5cdFx0XHRcdFx0XHRcdFx0KyAnLScgKyAoKCAoYURheXNbIGkgXS5nZXRNb250aCgpICsgMSkgPCAxMCkgPyAnMCcgOiAnJykgKyAoYURheXNbIGkgXS5nZXRNb250aCgpICsgMSlcclxuXHRcdFx0XHRcdFx0XHRcdCsgJy0nICsgKCggICAgICAgIGFEYXlzWyBpIF0uZ2V0RGF0ZSgpIDwgMTApID8gJzAnIDogJycpICsgIGFEYXlzWyBpIF0uZ2V0RGF0ZSgpO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHQvLyBPbmNlIHRoZSBsb29wIGhhcyBmaW5pc2hlZCwgcmV0dXJuIHRoZSBhcnJheSBvZiBkYXlzLlxyXG5cdFx0XHRcdHJldHVybiBhRGF5cztcclxuXHRcdFx0fVxyXG5cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqICAgVG9vbHRpcHMgIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuXHJcblx0LyoqXHJcblx0ICogRGVmaW5lIHNob3dpbmcgdG9vbHRpcCwgIHdoZW4gIG1vdXNlIG92ZXIgb24gIFNFTEVDVEFCTEUgKGF2YWlsYWJsZSwgcGVuZGluZywgYXBwcm92ZWQsIHJlc291cmNlIHVuYXZhaWxhYmxlKSwgIGRheXNcclxuXHQgKiBDYW4gYmUgY2FsbGVkIGRpcmVjdGx5ICBmcm9tICBkYXRlcGljayBpbml0IGZ1bmN0aW9uLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHZhbHVlXHJcblx0ICogQHBhcmFtIGRhdGVcclxuXHQgKiBAcGFyYW0gY2FsZW5kYXJfcGFyYW1zX2FyclxyXG5cdCAqIEBwYXJhbSBkYXRlcGlja190aGlzXHJcblx0ICogQHJldHVybnMge2Jvb2xlYW59XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19hdnlfX3ByZXBhcmVfdG9vbHRpcF9faW5fY2FsZW5kYXIoIHZhbHVlLCBkYXRlLCBjYWxlbmRhcl9wYXJhbXNfYXJyLCBkYXRlcGlja190aGlzICl7XHJcblxyXG5cdFx0aWYgKCBudWxsID09IGRhdGUgKXsgIHJldHVybiBmYWxzZTsgIH1cclxuXHJcblx0XHR2YXIgdGRfY2xhc3MgPSAoIGRhdGUuZ2V0TW9udGgoKSArIDEgKSArICctJyArIGRhdGUuZ2V0RGF0ZSgpICsgJy0nICsgZGF0ZS5nZXRGdWxsWWVhcigpO1xyXG5cclxuXHRcdHZhciBqQ2VsbCA9IGpRdWVyeSggJyNjYWxlbmRhcl9ib29raW5nJyArIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgKyAnIHRkLmNhbDRkYXRlLScgKyB0ZF9jbGFzcyApO1xyXG5cclxuXHRcdHdwYmNfYXZ5X19zaG93X3Rvb2x0aXBfX2Zvcl9lbGVtZW50KCBqQ2VsbCwgY2FsZW5kYXJfcGFyYW1zX2FyclsgJ3BvcG92ZXJfaGludHMnIF0gKTtcclxuXHRcdHJldHVybiB0cnVlO1xyXG5cdH1cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIERlZmluZSB0b29sdGlwICBmb3Igc2hvd2luZyBvbiBVTkFWQUlMQUJMRSBkYXlzIChzZWFzb24sIHdlZWtkYXksIHRvZGF5X2RlcGVuZHMgdW5hdmFpbGFibGUpXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gakNlbGxcdFx0XHRcdFx0alF1ZXJ5IG9mIHNwZWNpZmljIGRheSBjZWxsXHJcblx0ICogQHBhcmFtIHBvcG92ZXJfaGludHNcdFx0ICAgIEFycmF5IHdpdGggdG9vbHRpcCBoaW50IHRleHRzXHQgOiB7J3NlYXNvbl91bmF2YWlsYWJsZSc6Jy4uLicsJ3dlZWtkYXlzX3VuYXZhaWxhYmxlJzonLi4uJywnYmVmb3JlX2FmdGVyX3VuYXZhaWxhYmxlJzonLi4uJyx9XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19hdnlfX3Nob3dfdG9vbHRpcF9fZm9yX2VsZW1lbnQoIGpDZWxsLCBwb3BvdmVyX2hpbnRzICl7XHJcblxyXG5cdFx0dmFyIHRvb2x0aXBfdGltZSA9ICcnO1xyXG5cclxuXHRcdGlmICggakNlbGwuaGFzQ2xhc3MoICdzZWFzb25fdW5hdmFpbGFibGUnICkgKXtcclxuXHRcdFx0dG9vbHRpcF90aW1lID0gcG9wb3Zlcl9oaW50c1sgJ3NlYXNvbl91bmF2YWlsYWJsZScgXTtcclxuXHRcdH0gZWxzZSBpZiAoIGpDZWxsLmhhc0NsYXNzKCAnd2Vla2RheXNfdW5hdmFpbGFibGUnICkgKXtcclxuXHRcdFx0dG9vbHRpcF90aW1lID0gcG9wb3Zlcl9oaW50c1sgJ3dlZWtkYXlzX3VuYXZhaWxhYmxlJyBdO1xyXG5cdFx0fSBlbHNlIGlmICggakNlbGwuaGFzQ2xhc3MoICdiZWZvcmVfYWZ0ZXJfdW5hdmFpbGFibGUnICkgKXtcclxuXHRcdFx0dG9vbHRpcF90aW1lID0gcG9wb3Zlcl9oaW50c1sgJ2JlZm9yZV9hZnRlcl91bmF2YWlsYWJsZScgXTtcclxuXHRcdH0gZWxzZSBpZiAoIGpDZWxsLmhhc0NsYXNzKCAnZGF0ZTJhcHByb3ZlJyApICl7XHJcblxyXG5cdFx0fSBlbHNlIGlmICggakNlbGwuaGFzQ2xhc3MoICdkYXRlX2FwcHJvdmVkJyApICl7XHJcblxyXG5cdFx0fSBlbHNlIHtcclxuXHJcblx0XHR9XHJcblxyXG5cdFx0akNlbGwuYXR0ciggJ2RhdGEtY29udGVudCcsIHRvb2x0aXBfdGltZSApO1xyXG5cclxuXHRcdHZhciB0ZF9lbCA9IGpDZWxsLmdldCgwKTtcdC8valF1ZXJ5KCAnI2NhbGVuZGFyX2Jvb2tpbmcnICsgY2FsZW5kYXJfcGFyYW1zX2Fyci5yZXNvdXJjZV9pZCArICcgdGQuY2FsNGRhdGUtJyArIHRkX2NsYXNzICkuZ2V0KDApO1xyXG5cclxuXHRcdGlmICggKCB1bmRlZmluZWQgPT0gdGRfZWwuX3RpcHB5ICkgJiYgKCAnJyAhPSB0b29sdGlwX3RpbWUgKSApe1xyXG5cclxuXHRcdFx0XHR3cGJjX3RpcHB5KCB0ZF9lbCAsIHtcclxuXHRcdFx0XHRcdGNvbnRlbnQoIHJlZmVyZW5jZSApe1xyXG5cclxuXHRcdFx0XHRcdFx0dmFyIHBvcG92ZXJfY29udGVudCA9IHJlZmVyZW5jZS5nZXRBdHRyaWJ1dGUoICdkYXRhLWNvbnRlbnQnICk7XHJcblxyXG5cdFx0XHRcdFx0XHRyZXR1cm4gJzxkaXYgY2xhc3M9XCJwb3BvdmVyIHBvcG92ZXJfdGlwcHlcIj4nXHJcblx0XHRcdFx0XHRcdFx0XHRcdCsgJzxkaXYgY2xhc3M9XCJwb3BvdmVyLWNvbnRlbnRcIj4nXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0KyBwb3BvdmVyX2NvbnRlbnRcclxuXHRcdFx0XHRcdFx0XHRcdFx0KyAnPC9kaXY+J1xyXG5cdFx0XHRcdFx0XHRcdCArICc8L2Rpdj4nO1xyXG5cdFx0XHRcdFx0fSxcclxuXHRcdFx0XHRcdGFsbG93SFRNTCAgICAgICAgOiB0cnVlLFxyXG5cdFx0XHRcdFx0dHJpZ2dlclx0XHRcdCA6ICdtb3VzZWVudGVyIGZvY3VzJyxcclxuXHRcdFx0XHRcdGludGVyYWN0aXZlICAgICAgOiAhIHRydWUsXHJcblx0XHRcdFx0XHRoaWRlT25DbGljayAgICAgIDogdHJ1ZSxcclxuXHRcdFx0XHRcdGludGVyYWN0aXZlQm9yZGVyOiAxMCxcclxuXHRcdFx0XHRcdG1heFdpZHRoICAgICAgICAgOiA1NTAsXHJcblx0XHRcdFx0XHR0aGVtZSAgICAgICAgICAgIDogJ3dwYmMtdGlwcHktdGltZXMnLFxyXG5cdFx0XHRcdFx0cGxhY2VtZW50ICAgICAgICA6ICd0b3AnLFxyXG5cdFx0XHRcdFx0ZGVsYXlcdFx0XHQgOiBbNDAwLCAwXSxcdFx0XHQvL0ZpeEluOiA5LjQuMi4yXHJcblx0XHRcdFx0XHRpZ25vcmVBdHRyaWJ1dGVzIDogdHJ1ZSxcclxuXHRcdFx0XHRcdHRvdWNoXHRcdFx0IDogdHJ1ZSxcdFx0XHRcdC8vWydob2xkJywgNTAwXSwgLy8gNTAwbXMgZGVsYXlcdFx0XHQvL0ZpeEluOiA5LjIuMS41XHJcblx0XHRcdFx0XHRhcHBlbmRUbzogKCkgPT4gZG9jdW1lbnQuYm9keSxcclxuXHRcdFx0XHR9KTtcclxuXHRcdH1cclxuXHR9XHJcblxyXG5cclxuXHJcblxyXG5cclxuLyoqXHJcbiAqICAgQWpheCAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG4vKipcclxuICogU2VuZCBBamF4IHNob3cgcmVxdWVzdFxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19hanhfYXZhaWxhYmlsaXR5X19hamF4X3JlcXVlc3QoKXtcclxuXHJcbmNvbnNvbGUuZ3JvdXBDb2xsYXBzZWQoICdXUEJDX0FKWF9BVkFJTEFCSUxJVFknICk7IGNvbnNvbGUubG9nKCAnID09IEJlZm9yZSBBamF4IFNlbmQgLSBzZWFyY2hfZ2V0X2FsbF9wYXJhbXMoKSA9PSAnICwgd3BiY19hanhfYXZhaWxhYmlsaXR5LnNlYXJjaF9nZXRfYWxsX3BhcmFtcygpICk7XHJcblxyXG5cdHdwYmNfYXZhaWxhYmlsaXR5X3JlbG9hZF9idXR0b25fX3NwaW5fc3RhcnQoKTtcclxuXHJcblx0Ly8gU3RhcnQgQWpheFxyXG5cdGpRdWVyeS5wb3N0KCB3cGJjX2dsb2JhbDEud3BiY19hamF4dXJsLFxyXG5cdFx0XHRcdHtcclxuXHRcdFx0XHRcdGFjdGlvbiAgICAgICAgICA6ICdXUEJDX0FKWF9BVkFJTEFCSUxJVFknLFxyXG5cdFx0XHRcdFx0d3BiY19hanhfdXNlcl9pZDogd3BiY19hanhfYXZhaWxhYmlsaXR5LmdldF9zZWN1cmVfcGFyYW0oICd1c2VyX2lkJyApLFxyXG5cdFx0XHRcdFx0bm9uY2UgICAgICAgICAgIDogd3BiY19hanhfYXZhaWxhYmlsaXR5LmdldF9zZWN1cmVfcGFyYW0oICdub25jZScgKSxcclxuXHRcdFx0XHRcdHdwYmNfYWp4X2xvY2FsZSA6IHdwYmNfYWp4X2F2YWlsYWJpbGl0eS5nZXRfc2VjdXJlX3BhcmFtKCAnbG9jYWxlJyApLFxyXG5cclxuXHRcdFx0XHRcdHNlYXJjaF9wYXJhbXNcdDogd3BiY19hanhfYXZhaWxhYmlsaXR5LnNlYXJjaF9nZXRfYWxsX3BhcmFtcygpXHJcblx0XHRcdFx0fSxcclxuXHRcdFx0XHQvKipcclxuXHRcdFx0XHQgKiBTIHUgYyBjIGUgcyBzXHJcblx0XHRcdFx0ICpcclxuXHRcdFx0XHQgKiBAcGFyYW0gcmVzcG9uc2VfZGF0YVx0XHQtXHRpdHMgb2JqZWN0IHJldHVybmVkIGZyb20gIEFqYXggLSBjbGFzcy1saXZlLXNlYXJjZy5waHBcclxuXHRcdFx0XHQgKiBAcGFyYW0gdGV4dFN0YXR1c1x0XHQtXHQnc3VjY2VzcydcclxuXHRcdFx0XHQgKiBAcGFyYW0ganFYSFJcdFx0XHRcdC1cdE9iamVjdFxyXG5cdFx0XHRcdCAqL1xyXG5cdFx0XHRcdGZ1bmN0aW9uICggcmVzcG9uc2VfZGF0YSwgdGV4dFN0YXR1cywganFYSFIgKSB7XHJcblxyXG5jb25zb2xlLmxvZyggJyA9PSBSZXNwb25zZSBXUEJDX0FKWF9BVkFJTEFCSUxJVFkgPT0gJywgcmVzcG9uc2VfZGF0YSApOyBjb25zb2xlLmdyb3VwRW5kKCk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gUHJvYmFibHkgRXJyb3JcclxuXHRcdFx0XHRcdGlmICggKHR5cGVvZiByZXNwb25zZV9kYXRhICE9PSAnb2JqZWN0JykgfHwgKHJlc3BvbnNlX2RhdGEgPT09IG51bGwpICl7XHJcblxyXG5cdFx0XHRcdFx0XHR3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX3Nob3dfbWVzc2FnZSggcmVzcG9uc2VfZGF0YSApO1xyXG5cclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdC8vIFJlbG9hZCBwYWdlLCBhZnRlciBmaWx0ZXIgdG9vbGJhciBoYXMgYmVlbiByZXNldFxyXG5cdFx0XHRcdFx0aWYgKCAgICAgICAoICAgICB1bmRlZmluZWQgIT0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9jbGVhbmVkX3BhcmFtcycgXSlcclxuXHRcdFx0XHRcdFx0XHQmJiAoICdyZXNldF9kb25lJyA9PT0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9jbGVhbmVkX3BhcmFtcycgXVsgJ2RvX2FjdGlvbicgXSlcclxuXHRcdFx0XHRcdCl7XHJcblx0XHRcdFx0XHRcdGxvY2F0aW9uLnJlbG9hZCgpO1xyXG5cdFx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0Ly8gU2hvdyBsaXN0aW5nXHJcblx0XHRcdFx0XHR3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX3BhZ2VfY29udGVudF9fc2hvdyggcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdLCByZXNwb25zZV9kYXRhWyAnYWp4X3NlYXJjaF9wYXJhbXMnIF0gLCByZXNwb25zZV9kYXRhWyAnYWp4X2NsZWFuZWRfcGFyYW1zJyBdICk7XHJcblxyXG5cdFx0XHRcdFx0Ly93cGJjX2FqeF9hdmFpbGFiaWxpdHlfX2RlZmluZV91aV9ob29rcygpO1x0XHRcdFx0XHRcdC8vIFJlZGVmaW5lIEhvb2tzLCBiZWNhdXNlIHdlIHNob3cgbmV3IERPTSBlbGVtZW50c1xyXG5cdFx0XHRcdFx0aWYgKCAnJyAhPSByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2UnIF0ucmVwbGFjZSggL1xcbi9nLCBcIjxiciAvPlwiICkgKXtcclxuXHRcdFx0XHRcdFx0d3BiY19hZG1pbl9zaG93X21lc3NhZ2UoXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2UnIF0ucmVwbGFjZSggL1xcbi9nLCBcIjxiciAvPlwiIClcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQsICggJzEnID09IHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fcmVzdWx0JyBdICkgPyAnc3VjY2VzcycgOiAnZXJyb3InXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0LCAxMDAwMFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQpO1xyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdHdwYmNfYXZhaWxhYmlsaXR5X3JlbG9hZF9idXR0b25fX3NwaW5fcGF1c2UoKTtcclxuXHRcdFx0XHRcdC8vIFJlbW92ZSBzcGluIGljb24gZnJvbSAgYnV0dG9uIGFuZCBFbmFibGUgdGhpcyBidXR0b24uXHJcblx0XHRcdFx0XHR3cGJjX2J1dHRvbl9fcmVtb3ZlX3NwaW4oIHJlc3BvbnNlX2RhdGFbICdhanhfY2xlYW5lZF9wYXJhbXMnIF1bICd1aV9jbGlja2VkX2VsZW1lbnRfaWQnIF0gKVxyXG5cclxuXHRcdFx0XHRcdGpRdWVyeSggJyNhamF4X3Jlc3BvbmQnICkuaHRtbCggcmVzcG9uc2VfZGF0YSApO1x0XHQvLyBGb3IgYWJpbGl0eSB0byBzaG93IHJlc3BvbnNlLCBhZGQgc3VjaCBESVYgZWxlbWVudCB0byBwYWdlXHJcblx0XHRcdFx0fVxyXG5cdFx0XHQgICkuZmFpbCggZnVuY3Rpb24gKCBqcVhIUiwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24gKSB7ICAgIGlmICggd2luZG93LmNvbnNvbGUgJiYgd2luZG93LmNvbnNvbGUubG9nICl7IGNvbnNvbGUubG9nKCAnQWpheF9FcnJvcicsIGpxWEhSLCB0ZXh0U3RhdHVzLCBlcnJvclRocm93biApOyB9XHJcblxyXG5cdFx0XHRcdFx0dmFyIGVycm9yX21lc3NhZ2UgPSAnPHN0cm9uZz4nICsgJ0Vycm9yIScgKyAnPC9zdHJvbmc+ICcgKyBlcnJvclRocm93biA7XHJcblx0XHRcdFx0XHRpZiAoIGpxWEhSLnN0YXR1cyApe1xyXG5cdFx0XHRcdFx0XHRlcnJvcl9tZXNzYWdlICs9ICcgKDxiPicgKyBqcVhIUi5zdGF0dXMgKyAnPC9iPiknO1xyXG5cdFx0XHRcdFx0XHRpZiAoNDAzID09IGpxWEhSLnN0YXR1cyApe1xyXG5cdFx0XHRcdFx0XHRcdGVycm9yX21lc3NhZ2UgKz0gJyBQcm9iYWJseSBub25jZSBmb3IgdGhpcyBwYWdlIGhhcyBiZWVuIGV4cGlyZWQuIFBsZWFzZSA8YSBocmVmPVwiamF2YXNjcmlwdDp2b2lkKDApXCIgb25jbGljaz1cImphdmFzY3JpcHQ6bG9jYXRpb24ucmVsb2FkKCk7XCI+cmVsb2FkIHRoZSBwYWdlPC9hPi4nO1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRpZiAoIGpxWEhSLnJlc3BvbnNlVGV4dCApe1xyXG5cdFx0XHRcdFx0XHRlcnJvcl9tZXNzYWdlICs9ICcgJyArIGpxWEhSLnJlc3BvbnNlVGV4dDtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdGVycm9yX21lc3NhZ2UgPSBlcnJvcl9tZXNzYWdlLnJlcGxhY2UoIC9cXG4vZywgXCI8YnIgLz5cIiApO1xyXG5cclxuXHRcdFx0XHRcdHdwYmNfYWp4X2F2YWlsYWJpbGl0eV9fc2hvd19tZXNzYWdlKCBlcnJvcl9tZXNzYWdlICk7XHJcblx0XHRcdCAgfSlcclxuXHQgICAgICAgICAgLy8gLmRvbmUoICAgZnVuY3Rpb24gKCBkYXRhLCB0ZXh0U3RhdHVzLCBqcVhIUiApIHsgICBpZiAoIHdpbmRvdy5jb25zb2xlICYmIHdpbmRvdy5jb25zb2xlLmxvZyApeyBjb25zb2xlLmxvZyggJ3NlY29uZCBzdWNjZXNzJywgZGF0YSwgdGV4dFN0YXR1cywganFYSFIgKTsgfSAgICB9KVxyXG5cdFx0XHQgIC8vIC5hbHdheXMoIGZ1bmN0aW9uICggZGF0YV9qcVhIUiwgdGV4dFN0YXR1cywganFYSFJfZXJyb3JUaHJvd24gKSB7ICAgaWYgKCB3aW5kb3cuY29uc29sZSAmJiB3aW5kb3cuY29uc29sZS5sb2cgKXsgY29uc29sZS5sb2coICdhbHdheXMgZmluaXNoZWQnLCBkYXRhX2pxWEhSLCB0ZXh0U3RhdHVzLCBqcVhIUl9lcnJvclRocm93biApOyB9ICAgICB9KVxyXG5cdFx0XHQgIDsgIC8vIEVuZCBBamF4XHJcblxyXG59XHJcblxyXG5cclxuXHJcbi8qKlxyXG4gKiAgIEggbyBvIGsgcyAgLSAgaXRzIEFjdGlvbi9UaW1lcyB3aGVuIG5lZWQgdG8gcmUtUmVuZGVyIFZpZXdzICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAqL1xyXG5cclxuLyoqXHJcbiAqIFNlbmQgQWpheCBTZWFyY2ggUmVxdWVzdCBhZnRlciBVcGRhdGluZyBzZWFyY2ggcmVxdWVzdCBwYXJhbWV0ZXJzXHJcbiAqXHJcbiAqIEBwYXJhbSBwYXJhbXNfYXJyXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX3NlbmRfcmVxdWVzdF93aXRoX3BhcmFtcyAoIHBhcmFtc19hcnIgKXtcclxuXHJcblx0Ly8gRGVmaW5lIGRpZmZlcmVudCBTZWFyY2ggIHBhcmFtZXRlcnMgZm9yIHJlcXVlc3RcclxuXHRfLmVhY2goIHBhcmFtc19hcnIsIGZ1bmN0aW9uICggcF92YWwsIHBfa2V5LCBwX2RhdGEgKSB7XHJcblx0XHQvL2NvbnNvbGUubG9nKCAnUmVxdWVzdCBmb3I6ICcsIHBfa2V5LCBwX3ZhbCApO1xyXG5cdFx0d3BiY19hanhfYXZhaWxhYmlsaXR5LnNlYXJjaF9zZXRfcGFyYW0oIHBfa2V5LCBwX3ZhbCApO1xyXG5cdH0pO1xyXG5cclxuXHQvLyBTZW5kIEFqYXggUmVxdWVzdFxyXG5cdHdwYmNfYWp4X2F2YWlsYWJpbGl0eV9fYWpheF9yZXF1ZXN0KCk7XHJcbn1cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIFNlYXJjaCByZXF1ZXN0IGZvciBcIlBhZ2UgTnVtYmVyXCJcclxuXHQgKiBAcGFyYW0gcGFnZV9udW1iZXJcdGludFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfYWp4X2F2YWlsYWJpbGl0eV9fcGFnaW5hdGlvbl9jbGljayggcGFnZV9udW1iZXIgKXtcclxuXHJcblx0XHR3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX3NlbmRfcmVxdWVzdF93aXRoX3BhcmFtcygge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3BhZ2VfbnVtJzogcGFnZV9udW1iZXJcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHR9ICk7XHJcblx0fVxyXG5cclxuXHJcblxyXG4vKipcclxuICogICBTaG93IC8gSGlkZSBDb250ZW50ICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuXHJcbi8qKlxyXG4gKiAgU2hvdyBMaXN0aW5nIENvbnRlbnQgXHQtIFx0U2VuZGluZyBBamF4IFJlcXVlc3RcdC1cdHdpdGggcGFyYW1ldGVycyB0aGF0ICB3ZSBlYXJseSAgZGVmaW5lZFxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19hanhfYXZhaWxhYmlsaXR5X19hY3R1YWxfY29udGVudF9fc2hvdygpe1xyXG5cclxuXHR3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX2FqYXhfcmVxdWVzdCgpO1x0XHRcdC8vIFNlbmQgQWpheCBSZXF1ZXN0XHQtXHR3aXRoIHBhcmFtZXRlcnMgdGhhdCAgd2UgZWFybHkgIGRlZmluZWQgaW4gXCJ3cGJjX2FqeF9ib29raW5nX2xpc3RpbmdcIiBPYmouXHJcbn1cclxuXHJcbi8qKlxyXG4gKiBIaWRlIExpc3RpbmcgQ29udGVudFxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19hanhfYXZhaWxhYmlsaXR5X19hY3R1YWxfY29udGVudF9faGlkZSgpe1xyXG5cclxuXHRqUXVlcnkoICB3cGJjX2FqeF9hdmFpbGFiaWxpdHkuZ2V0X290aGVyX3BhcmFtKCAnbGlzdGluZ19jb250YWluZXInICkgICkuaHRtbCggJycgKTtcclxufVxyXG5cclxuXHJcblxyXG4vKipcclxuICogICBNIGUgcyBzIGEgZyBlICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuXHJcbi8qKlxyXG4gKiBTaG93IGp1c3QgbWVzc2FnZSBpbnN0ZWFkIG9mIGNvbnRlbnRcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWp4X2F2YWlsYWJpbGl0eV9fc2hvd19tZXNzYWdlKCBtZXNzYWdlICl7XHJcblxyXG5cdHdwYmNfYWp4X2F2YWlsYWJpbGl0eV9fYWN0dWFsX2NvbnRlbnRfX2hpZGUoKTtcclxuXHJcblx0alF1ZXJ5KCB3cGJjX2FqeF9hdmFpbGFiaWxpdHkuZ2V0X290aGVyX3BhcmFtKCAnbGlzdGluZ19jb250YWluZXInICkgKS5odG1sKFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnPGRpdiBjbGFzcz1cIndwYmMtc2V0dGluZ3Mtbm90aWNlIG5vdGljZS13YXJuaW5nXCIgc3R5bGU9XCJ0ZXh0LWFsaWduOmxlZnRcIj4nICtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRtZXNzYWdlICtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JzwvZGl2PidcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHQpO1xyXG59XHJcblxyXG5cclxuXHJcbi8qKlxyXG4gKiAgIFN1cHBvcnQgRnVuY3Rpb25zIC0gU3BpbiBJY29uIGluIEJ1dHRvbnMgIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAqL1xyXG5cclxuLyoqXHJcbiAqIFNwaW4gYnV0dG9uIGluIEZpbHRlciB0b29sYmFyICAtICBTdGFydFxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19hdmFpbGFiaWxpdHlfcmVsb2FkX2J1dHRvbl9fc3Bpbl9zdGFydCgpe1xyXG5cdGpRdWVyeSggJyN3cGJjX2F2YWlsYWJpbGl0eV9yZWxvYWRfYnV0dG9uIC5tZW51X2ljb24ud3BiY19zcGluJykucmVtb3ZlQ2xhc3MoICd3cGJjX2FuaW1hdGlvbl9wYXVzZScgKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIFNwaW4gYnV0dG9uIGluIEZpbHRlciB0b29sYmFyICAtICBQYXVzZVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19hdmFpbGFiaWxpdHlfcmVsb2FkX2J1dHRvbl9fc3Bpbl9wYXVzZSgpe1xyXG5cdGpRdWVyeSggJyN3cGJjX2F2YWlsYWJpbGl0eV9yZWxvYWRfYnV0dG9uIC5tZW51X2ljb24ud3BiY19zcGluJyApLmFkZENsYXNzKCAnd3BiY19hbmltYXRpb25fcGF1c2UnICk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBTcGluIGJ1dHRvbiBpbiBGaWx0ZXIgdG9vbGJhciAgLSAgaXMgU3Bpbm5pbmcgP1xyXG4gKlxyXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYXZhaWxhYmlsaXR5X3JlbG9hZF9idXR0b25fX2lzX3NwaW4oKXtcclxuICAgIGlmICggalF1ZXJ5KCAnI3dwYmNfYXZhaWxhYmlsaXR5X3JlbG9hZF9idXR0b24gLm1lbnVfaWNvbi53cGJjX3NwaW4nICkuaGFzQ2xhc3MoICd3cGJjX2FuaW1hdGlvbl9wYXVzZScgKSApe1xyXG5cdFx0cmV0dXJuIHRydWU7XHJcblx0fSBlbHNlIHtcclxuXHRcdHJldHVybiBmYWxzZTtcclxuXHR9XHJcbn1cclxuIl0sImZpbGUiOiJpbmNsdWRlcy9wYWdlLWF2YWlsYWJpbGl0eS9fb3V0L2F2YWlsYWJpbGl0eV9wYWdlLmpzIn0=
