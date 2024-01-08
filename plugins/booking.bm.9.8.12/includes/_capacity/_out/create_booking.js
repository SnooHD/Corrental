"use strict"; // ---------------------------------------------------------------------------------------------------------------------
//  A j a x    A d d    N e w    B o o k i n g
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Submit new booking
 *
 * @param params   =     {
                                'resource_id'        : resource_id,
                                'dates_ddmmyy_csv'   : document.getElementById( 'date_booking' + resource_id ).value,
                                'formdata'           : formdata,
                                'booking_hash'       : my_booking_hash,
                                'custom_form'        : my_booking_form,

                                'captcha_chalange'   : captcha_chalange,
                                'captcha_user_input' : user_captcha,

                                'is_emails_send'     : is_send_emeils,
                                'active_locale'      : wpdev_active_locale
						}
 *
 */

function _typeof(obj) {
  "@babel/helpers - typeof";
  return (
    (_typeof =
      "function" == typeof Symbol && "symbol" == typeof Symbol.iterator
        ? function (obj) {
            return typeof obj;
          }
        : function (obj) {
            return obj &&
              "function" == typeof Symbol &&
              obj.constructor === Symbol &&
              obj !== Symbol.prototype
              ? "symbol"
              : typeof obj;
          }),
    _typeof(obj)
  );
}

function wpbc_ajx_booking__create(params) {
  console.groupCollapsed("WPBC_AJX_BOOKING__CREATE");
  console.groupCollapsed("== Before Ajax Send ==");
  console.log(params);
  console.groupEnd();
  params = wpbc_captcha__simple__maybe_remove_in_ajx_params(params); // Start Ajax

  const urlParams = new URLSearchParams(window.location.search);
  const couponParam = urlParams.get("coupon");

  jQuery
    .post(
      wpbc_global1.wpbc_ajaxurl,
      {
        action: "WPBC_AJX_BOOKING__CREATE",
        coupon: couponParam,
        wpbc_ajx_user_id: _wpbc.get_secure_param("user_id"),
        nonce: _wpbc.get_secure_param("nonce"),
        wpbc_ajx_locale: _wpbc.get_secure_param("locale"),
        calendar_request_params: params,
        /**
         *  Usually  params = { 'resource_id'        : resource_id,
         *						'dates_ddmmyy_csv'   : document.getElementById( 'date_booking' + resource_id ).value,
         *						'formdata'           : formdata,
         *						'booking_hash'       : my_booking_hash,
         *						'custom_form'        : my_booking_form,
         *
         *						'captcha_chalange'   : captcha_chalange,
         *						'user_captcha'       : user_captcha,
         *
         *						'is_emails_send'     : is_send_emeils,
         *						'active_locale'      : wpdev_active_locale
         *				}
         */
      },
      /**
       * S u c c e s s
       *
       * @param response_data		-	its object returned from  Ajax - class-live-searcg.php
       * @param textStatus		-	'success'
       * @param jqXHR				-	Object
       */
      function (response_data, textStatus, jqXHR) {
        console.log(" == Response WPBC_AJX_BOOKING__CREATE == ");

        for (var obj_key in response_data) {
          console.groupCollapsed("==" + obj_key + "==");
          console.log(" : " + obj_key + " : ", response_data[obj_key]);
          console.groupEnd();
        }

        console.groupEnd(); // <editor-fold     defaultstate="collapsed"     desc=" = Error Message! Server response with String.  ->  E_X_I_T  "  >
        // -------------------------------------------------------------------------------------------------
        // This section execute,  when server response with  String instead of Object -- Usually  it's because of mistake in code !
        // -------------------------------------------------------------------------------------------------

        if (_typeof(response_data) !== "object" || response_data === null) {
          var calendar_id = wpbc_get_resource_id__from_ajx_post_data_url(
            this.data
          );
          var jq_node = "#booking_form" + calendar_id;

          if ("" == response_data) {
            response_data =
              "<strong>" +
              "Error! Server respond with empty string!" +
              "</strong> ";
          } // Show Message

          wpbc_front_end__show_message(response_data, {
            type: "error",
            show_here: {
              jq_node: jq_node,
              where: "after",
            },
            is_append: true,
            style: "text-align:left;",
            delay: 0,
          }); // Enable Submit | Hide spin loader

          wpbc_booking_form__on_response__ui_elements_enable(calendar_id);
          return;
        } // </editor-fold>
        // <editor-fold     defaultstate="collapsed"     desc="  ==  This section execute,  when we have KNOWN errors from Booking Calendar.  ->  E_X_I_T  "  >
        // -------------------------------------------------------------------------------------------------
        // This section execute,  when we have KNOWN errors from Booking Calendar
        // -------------------------------------------------------------------------------------------------

        if ("ok" != response_data["ajx_data"]["status"]) {
          switch (response_data["ajx_data"]["status_error"]) {
            case "captcha_simple_wrong":
              wpbc_captcha__simple__update({
                resource_id: response_data["resource_id"],
                url: response_data["ajx_data"]["captcha__simple"]["url"],
                challenge:
                  response_data["ajx_data"]["captcha__simple"]["challenge"],
                message: response_data["ajx_data"][
                  "ajx_after_action_message"
                ].replace(/\n/g, "<br />"),
              });
              break;

            case "resource_id_incorrect":
              // Show Error Message - incorrect  booking resource ID during submit of booking.
              var message_id = wpbc_front_end__show_message(
                response_data["ajx_data"]["ajx_after_action_message"].replace(
                  /\n/g,
                  "<br />"
                ),
                {
                  type:
                    "undefined" !==
                    typeof response_data["ajx_data"][
                      "ajx_after_action_message_status"
                    ]
                      ? response_data["ajx_data"][
                          "ajx_after_action_message_status"
                        ]
                      : "warning",
                  delay: 0,
                  show_here: {
                    where: "after",
                    jq_node: "#booking_form" + params["resource_id"],
                  },
                }
              );
              break;

            case "booking_can_not_save":
              // We can not save booking, because dates are booked or can not save in same booking resource all the dates
              var message_id = wpbc_front_end__show_message(
                response_data["ajx_data"]["ajx_after_action_message"].replace(
                  /\n/g,
                  "<br />"
                ),
                {
                  type:
                    "undefined" !==
                    typeof response_data["ajx_data"][
                      "ajx_after_action_message_status"
                    ]
                      ? response_data["ajx_data"][
                          "ajx_after_action_message_status"
                        ]
                      : "warning",
                  delay: 0,
                  show_here: {
                    where: "after",
                    jq_node: "#booking_form" + params["resource_id"],
                  },
                }
              ); // Enable Submit | Hide spin loader

              wpbc_booking_form__on_response__ui_elements_enable(
                response_data["resource_id"]
              );
              break;

            default:
              // <editor-fold     defaultstate="collapsed"                        desc=" = For debug only ? --  Show Message under the form = "  >
              // --------------------------------------------------------------------------------------------------------------------------------
              if (
                "undefined" !==
                  typeof response_data["ajx_data"][
                    "ajx_after_action_message"
                  ] &&
                "" !=
                  response_data["ajx_data"]["ajx_after_action_message"].replace(
                    /\n/g,
                    "<br />"
                  )
              ) {
                var calendar_id = wpbc_get_resource_id__from_ajx_post_data_url(
                  this.data
                );
                var jq_node = "#booking_form" + calendar_id;
                var ajx_after_booking_message = response_data["ajx_data"][
                  "ajx_after_action_message"
                ].replace(/\n/g, "<br />");
                console.log(ajx_after_booking_message);
                /**
             * // Show Message
            	var ajx_after_action_message_id = wpbc_front_end__show_message( ajx_after_booking_message,
            								{
            									'type' : ('undefined' !== typeof (response_data[ 'ajx_data' ][ 'ajx_after_action_message_status' ]))
            											? response_data[ 'ajx_data' ][ 'ajx_after_action_message_status' ] : 'info',
            									'delay'    : 10000,
            									'show_here': {
            													'jq_node': jq_node,
            													'where'  : 'after'
            												 }
            								} );
             */
              }

            // </editor-fold>
          } // -------------------------------------------------------------------------------------------------
          // Reactivate calendar again ?
          // -------------------------------------------------------------------------------------------------
          // Enable Submit | Hide spin loader

          wpbc_booking_form__on_response__ui_elements_enable(
            response_data["resource_id"]
          ); // Unselect  dates

          wpbc_calendar__unselect_all_dates(response_data["resource_id"]); // 'resource_id'    => $params['resource_id'],
          // 'booking_hash'   => $booking_hash,
          // 'request_uri'    => $_SERVER['REQUEST_URI'],                                            // Is it the same as window.location.href or
          // 'custom_form'    => $params['custom_form'],                                             // Optional.
          // 'aggregate_resource_id_str' => implode( ',', $params['aggregate_resource_id_arr'] )     // Optional. Resource ID   from  aggregate parameter in shortcode.
          // Load new data in calendar.

          wpbc_calendar__load_data__ajx({
            resource_id: response_data["resource_id"], // It's from response ...AJX_BOOKING__CREATE of initial sent resource_id
            booking_hash: response_data["ajx_cleaned_params"]["booking_hash"], // ?? we can not use it,  because HASH chnaged in any  case!
            request_uri: response_data["ajx_cleaned_params"]["request_uri"],
            custom_form: response_data["ajx_cleaned_params"]["custom_form"], // Aggregate booking resources,  if any ?
            aggregate_resource_id_str: _wpbc
              .booking__get_param_value(
                response_data["resource_id"],
                "aggregate_resource_id_arr"
              )
              .join(","),
          }); // Exit

          return;
        } // </editor-fold>

        /*
    	// Show Calendar
    	wpbc_calendar__loading__stop( response_data[ 'resource_id' ] );
    
    	// -------------------------------------------------------------------------------------------------
    	// Bookings - Dates
    	_wpbc.bookings_in_calendar__set_dates(  response_data[ 'resource_id' ], response_data[ 'ajx_data' ]['dates']  );
    
    	// Bookings - Child or only single booking resource in dates
    	_wpbc.booking__set_param_value( response_data[ 'resource_id' ], 'resources_id_arr__in_dates', response_data[ 'ajx_data' ][ 'resources_id_arr__in_dates' ] );
    	// -------------------------------------------------------------------------------------------------
    
    	// Update calendar
    	wpbc_calendar__update_look( response_data[ 'resource_id' ] );
    */
        // Hide spin loader

        wpbc_booking_form__spin_loader__hide(response_data["resource_id"]); // Hide booking form

        wpbc_booking_form__animated__hide(response_data["resource_id"]); // Show Confirmation | Payment section

        wpbc_show_thank_you_message_after_booking(response_data);
        setTimeout(function () {
          wpbc_do_scroll(
            "#wpbc_scroll_point_" + response_data["resource_id"],
            10
          );
        }, 500);
      }
    )
    .fail(
      // <editor-fold     defaultstate="collapsed"                        desc=" = This section execute,  when  NONCE field was not passed or some error happened at  server! = "  >
      function (jqXHR, textStatus, errorThrown) {
        if (window.console && window.console.log) {
          console.log("Ajax_Error", jqXHR, textStatus, errorThrown);
        } // -------------------------------------------------------------------------------------------------
        // This section execute,  when  NONCE field was not passed or some error happened at  server!
        // -------------------------------------------------------------------------------------------------
        // Get Content of Error Message

        var error_message = "<strong>" + "Error!" + "</strong> " + errorThrown;

        if (jqXHR.status) {
          error_message += " (<b>" + jqXHR.status + "</b>)";

          if (403 == jqXHR.status) {
            error_message +=
              '<br> Probably nonce for this page has been expired. Please <a href="javascript:void(0)" onclick="javascript:location.reload();">reload the page</a>.';
            error_message +=
              '<br> Otherwise, please check this <a style="font-weight: 600;" href="https://wpbookingcalendar.com/faq/request-do-not-pass-security-check/">troubleshooting instruction</a>.<br>';
          }
        }

        if (jqXHR.responseText) {
          // Escape tags in Error message
          error_message +=
            '<br><strong>Response</strong><div style="padding: 0 10px;margin: 0 0 10px;border-radius:3px; box-shadow:0px 0px 1px #a3a3a3;">' +
            jqXHR.responseText
              .replace(/&/g, "&amp;")
              .replace(/</g, "&lt;")
              .replace(/>/g, "&gt;")
              .replace(/"/g, "&quot;")
              .replace(/'/g, "&#39;") +
            "</div>";
        }

        error_message = error_message.replace(/\n/g, "<br />");
        var calendar_id = wpbc_get_resource_id__from_ajx_post_data_url(
          this.data
        );
        var jq_node = "#booking_form" + calendar_id; // Show Message

        wpbc_front_end__show_message(error_message, {
          type: "error",
          show_here: {
            jq_node: jq_node,
            where: "after",
          },
          is_append: true,
          style: "text-align:left;",
          delay: 0,
        }); // Enable Submit | Hide spin loader

        wpbc_booking_form__on_response__ui_elements_enable(calendar_id);
      } // </editor-fold>
    ); // .done(   function ( data, textStatus, jqXHR ) {   if ( window.console && window.console.log ){ console.log( 'second success', data, textStatus, jqXHR ); }    })
  // .always( function ( data_jqXHR, textStatus, jqXHR_errorThrown ) {   if ( window.console && window.console.log ){ console.log( 'always finished', data_jqXHR, textStatus, jqXHR_errorThrown ); }     }) // End Ajax

  return true;
} // <editor-fold     defaultstate="collapsed"                        desc="  ==  CAPTCHA ==  "  >

/**
 * Update image in captcha and show warning message
 *
 * @param params
 *
 * Example of 'params' : {
 *							'resource_id': response_data[ 'resource_id' ],
 *							'url'        : response_data[ 'ajx_data' ][ 'captcha__simple' ][ 'url' ],
 *							'challenge'  : response_data[ 'ajx_data' ][ 'captcha__simple' ][ 'challenge' ],
 *							'message'    : response_data[ 'ajx_data' ][ 'ajx_after_action_message' ].replace( /\n/g, "<br />" )
 *						}
 */

function wpbc_captcha__simple__update(params) {
  document.getElementById("captcha_input" + params["resource_id"]).value = "";
  document.getElementById("captcha_img" + params["resource_id"]).src =
    params["url"];
  document.getElementById(
    "wpdev_captcha_challenge_" + params["resource_id"]
  ).value = params["challenge"]; // Show warning 		After CAPTCHA Img

  var message_id = wpbc_front_end__show_message__warning(
    "#captcha_input" + params["resource_id"] + " + img",
    params["message"]
  ); // Animate

  jQuery("#" + message_id + ", " + "#captcha_input" + params["resource_id"])
    .fadeOut(350)
    .fadeIn(300)
    .fadeOut(350)
    .fadeIn(400)
    .animate(
      {
        opacity: 1,
      },
      4000
    ); // Focus text  field

  jQuery("#captcha_input" + params["resource_id"]).trigger("focus"); //FixIn: 8.7.11.12
  // Enable Submit | Hide spin loader

  wpbc_booking_form__on_response__ui_elements_enable(params["resource_id"]);
}
/**
 * If the captcha elements not exist  in the booking form,  then  remove parameters relative captcha
 * @param params
 * @returns obj
 */

function wpbc_captcha__simple__maybe_remove_in_ajx_params(params) {
  if (!wpbc_captcha__simple__is_exist_in_form(params["resource_id"])) {
    delete params["captcha_chalange"];
    delete params["captcha_user_input"];
  }

  return params;
}
/**
 * Check if CAPTCHA exist in the booking form
 * @param resource_id
 * @returns {boolean}
 */

function wpbc_captcha__simple__is_exist_in_form(resource_id) {
  return (
    0 !== jQuery("#wpdev_captcha_challenge_" + resource_id).length ||
    0 !== jQuery("#captcha_input" + resource_id).length
  );
} // </editor-fold>
// <editor-fold     defaultstate="collapsed"                        desc="  ==  Send Button | Form Spin Loader  ==  "  >

/**
 * Disable Send button  |  Show Spin Loader
 *
 * @param resource_id
 */

function wpbc_booking_form__on_submit__ui_elements_disable(resource_id) {
  // Disable Submit
  wpbc_booking_form__send_button__disable(resource_id); // Show Spin loader in booking form

  wpbc_booking_form__spin_loader__show(resource_id);
}
/**
 * Enable Send button  |   Hide Spin Loader
 *
 * @param resource_id
 */

function wpbc_booking_form__on_response__ui_elements_enable(resource_id) {
  // Enable Submit
  wpbc_booking_form__send_button__enable(resource_id); // Hide Spin loader in booking form

  wpbc_booking_form__spin_loader__hide(resource_id);
}
/**
 * Enable Submit button
 * @param resource_id
 */

function wpbc_booking_form__send_button__enable(resource_id) {
  // Activate Send button
  jQuery("#booking_form_div" + resource_id + " input[type=button]").prop(
    "disabled",
    false
  );
  jQuery("#booking_form_div" + resource_id + " button").prop("disabled", false);
}
/**
 * Disable Submit button  and show  spin
 *
 * @param resource_id
 */

function wpbc_booking_form__send_button__disable(resource_id) {
  // Disable Send button
  jQuery("#booking_form_div" + resource_id + " input[type=button]").prop(
    "disabled",
    true
  );
  jQuery("#booking_form_div" + resource_id + " button").prop("disabled", true);
}
/**
 * Show booking form  Spin Loader
 * @param resource_id
 */

function wpbc_booking_form__spin_loader__show(resource_id) {
  // Show Spin Loader
  return;
}
/**
 * Remove / Hide booking form  Spin Loader
 * @param resource_id
 */

function wpbc_booking_form__spin_loader__hide(resource_id) {
  // Remove Spin Loader
  jQuery("#wpbc_booking_form_spin_loader" + resource_id).remove();
}
/**
 * Hide booking form wth animation
 *
 * @param resource_id
 */

function wpbc_booking_form__animated__hide(resource_id) {
  // jQuery( '#booking_form' + resource_id ).slideUp(  1000
  // 												, function (){
  //
  // 														// if ( document.getElementById( 'gateway_payment_forms' + response_data[ 'resource_id' ] ) != null ){
  // 														// 	wpbc_do_scroll( '#submiting' + resource_id );
  // 														// } else
  // 														if ( jQuery( '#booking_form' + resource_id ).parent().find( '.submiting_content' ).length > 0 ){
  // 															//wpbc_do_scroll( '#booking_form' + resource_id + ' + .submiting_content' );
  //
  // 															 var hideTimeout = setTimeout(function () {
  // 																				  wpbc_do_scroll( jQuery( '#booking_form' + resource_id ).parent().find( '.submiting_content' ).get( 0 ) );
  // 																				}, 100);
  //
  // 														}
  // 												  }
  // 										);
  jQuery("#booking_form" + resource_id).hide(); // var hideTimeout = setTimeout( function (){
  //
  // 	if ( jQuery( '#booking_form' + resource_id ).parent().find( '.submiting_content' ).length > 0 ){
  // 		var random_id = Math.floor( (Math.random() * 10000) + 1 );
  // 		jQuery( '#booking_form' + resource_id ).parent().before( '<div id="scroll_to' + random_id + '"></div>' );
  // 		console.log( jQuery( '#scroll_to' + random_id ) );
  //
  // 		wpbc_do_scroll( '#scroll_to' + random_id );
  // 		//wpbc_do_scroll( jQuery( '#booking_form' + resource_id ).parent().get( 0 ) );
  // 	}
  // }, 500 );
} // </editor-fold>
//TODO: what  about showing only  Thank you. message without payment forms.

/**
 * Show 'Thank you'. message and payment forms
 *
 * @param response_data
 */

function wpbc_show_thank_you_message_after_booking(response_data) {
  if (
    "undefined" !==
      typeof response_data["ajx_confirmation"]["ty_is_redirect"] &&
    "undefined" !== typeof response_data["ajx_confirmation"]["ty_url"] &&
    "page" == response_data["ajx_confirmation"]["ty_is_redirect"] &&
    "" != response_data["ajx_confirmation"]["ty_url"]
  ) {
    window.location.href = response_data["ajx_confirmation"]["ty_url"];
    return;
  }

  var resource_id = response_data["resource_id"];
  var confirm_content = "";

  if ("undefined" === typeof response_data["ajx_confirmation"]["ty_message"]) {
    response_data["ajx_confirmation"]["ty_message"] = "";
  }

  if (
    "undefined" ===
    typeof response_data["ajx_confirmation"]["ty_payment_payment_description"]
  ) {
    response_data["ajx_confirmation"]["ty_payment_payment_description"] = "";
  }

  if (
    "undefined" === typeof response_data["ajx_confirmation"]["payment_cost"]
  ) {
    response_data["ajx_confirmation"]["payment_cost"] = "";
  }

  if (
    "undefined" ===
    typeof response_data["ajx_confirmation"]["ty_payment_gateways"]
  ) {
    response_data["ajx_confirmation"]["ty_payment_gateways"] = "";
  }

  var ty_message_hide =
    "" == response_data["ajx_confirmation"]["ty_message"] ? "wpbc_ty_hide" : "";
  var ty_payment_payment_description_hide =
    "" ==
    response_data["ajx_confirmation"]["ty_payment_payment_description"].replace(
      /\\n/g,
      ""
    )
      ? "wpbc_ty_hide"
      : "";
  var ty_booking_costs_hide =
    "" == response_data["ajx_confirmation"]["payment_cost"]
      ? "wpbc_ty_hide"
      : "";
  var ty_payment_gateways_hide =
    "" ==
    response_data["ajx_confirmation"]["ty_payment_gateways"].replace(/\\n/g, "")
      ? "wpbc_ty_hide"
      : "";

  if ("wpbc_ty_hide" != ty_payment_gateways_hide) {
    jQuery(".wpbc_ty__content_text.wpbc_ty__content_gateways").html(""); // Reset  all  other possible gateways before showing new one.
  }

  confirm_content += '<div id="wpbc_scroll_point_'.concat(
    resource_id,
    '"></div>'
  );
  confirm_content += '  <div class="wpbc_after_booking_thank_you_section">';
  confirm_content += '    <div class="wpbc_ty__message '
    .concat(ty_message_hide, '">')
    .concat(response_data["ajx_confirmation"]["ty_message"], "</div>");
  confirm_content += '    <div class="wpbc_ty__container">';
  // confirm_content += '      <div class="wpbc_ty__header">'.concat(
  //   response_data["ajx_confirmation"]["ty_message_booking_id"],
  //   "</div>"
  // );
  confirm_content += '      <div class="wpbc_ty__content">';
  confirm_content +=
    '        <div class="wpbc_ty__content_text wpbc_ty__payment_description '
      .concat(ty_payment_payment_description_hide, '">')
      .concat(
        response_data["ajx_confirmation"][
          "ty_payment_payment_description"
        ].replace(/\\n/g, ""),
        "</div>"
      );
  confirm_content +=
    '        <div class="wpbc_ty__content_text wpbc_ty__content_costs '
      .concat(ty_booking_costs_hide, '">')
      .concat(response_data["ajx_confirmation"]["ty_booking_costs"], "</div>");
  confirm_content +=
    '        <div class="wpbc_ty__content_text wpbc_ty__content_gateways '
      .concat(ty_payment_gateways_hide, '">')
      .concat(
        response_data["ajx_confirmation"]["ty_payment_gateways"]
          .replace(/\\n/g, "")
          .replace(/ajax_script/gi, "script"),
        "</div>"
      );
  confirm_content += "      </div>";
  confirm_content += "    </div>";
  confirm_content += "</div>";
  jQuery("#booking_form" + resource_id).after(confirm_content);
}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImluY2x1ZGVzL19jYXBhY2l0eS9fc3JjL2NyZWF0ZV9ib29raW5nLmpzIl0sIm5hbWVzIjpbIndwYmNfYWp4X2Jvb2tpbmdfX2NyZWF0ZSIsInBhcmFtcyIsImNvbnNvbGUiLCJncm91cENvbGxhcHNlZCIsImxvZyIsImdyb3VwRW5kIiwid3BiY19jYXB0Y2hhX19zaW1wbGVfX21heWJlX3JlbW92ZV9pbl9hanhfcGFyYW1zIiwialF1ZXJ5IiwicG9zdCIsIndwYmNfZ2xvYmFsMSIsIndwYmNfYWpheHVybCIsImFjdGlvbiIsIndwYmNfYWp4X3VzZXJfaWQiLCJfd3BiYyIsImdldF9zZWN1cmVfcGFyYW0iLCJub25jZSIsIndwYmNfYWp4X2xvY2FsZSIsImNhbGVuZGFyX3JlcXVlc3RfcGFyYW1zIiwicmVzcG9uc2VfZGF0YSIsInRleHRTdGF0dXMiLCJqcVhIUiIsIm9ial9rZXkiLCJjYWxlbmRhcl9pZCIsIndwYmNfZ2V0X3Jlc291cmNlX2lkX19mcm9tX2FqeF9wb3N0X2RhdGFfdXJsIiwiZGF0YSIsImpxX25vZGUiLCJ3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlIiwid3BiY19ib29raW5nX2Zvcm1fX29uX3Jlc3BvbnNlX191aV9lbGVtZW50c19lbmFibGUiLCJ3cGJjX2NhcHRjaGFfX3NpbXBsZV9fdXBkYXRlIiwicmVwbGFjZSIsIm1lc3NhZ2VfaWQiLCJhanhfYWZ0ZXJfYm9va2luZ19tZXNzYWdlIiwid3BiY19jYWxlbmRhcl9fdW5zZWxlY3RfYWxsX2RhdGVzIiwid3BiY19jYWxlbmRhcl9fbG9hZF9kYXRhX19hangiLCJib29raW5nX19nZXRfcGFyYW1fdmFsdWUiLCJqb2luIiwid3BiY19ib29raW5nX2Zvcm1fX3NwaW5fbG9hZGVyX19oaWRlIiwid3BiY19ib29raW5nX2Zvcm1fX2FuaW1hdGVkX19oaWRlIiwid3BiY19zaG93X3RoYW5rX3lvdV9tZXNzYWdlX2FmdGVyX2Jvb2tpbmciLCJzZXRUaW1lb3V0Iiwid3BiY19kb19zY3JvbGwiLCJmYWlsIiwiZXJyb3JUaHJvd24iLCJ3aW5kb3ciLCJlcnJvcl9tZXNzYWdlIiwic3RhdHVzIiwicmVzcG9uc2VUZXh0IiwiZG9jdW1lbnQiLCJnZXRFbGVtZW50QnlJZCIsInZhbHVlIiwic3JjIiwid3BiY19mcm9udF9lbmRfX3Nob3dfbWVzc2FnZV9fd2FybmluZyIsImZhZGVPdXQiLCJmYWRlSW4iLCJhbmltYXRlIiwib3BhY2l0eSIsInRyaWdnZXIiLCJ3cGJjX2NhcHRjaGFfX3NpbXBsZV9faXNfZXhpc3RfaW5fZm9ybSIsInJlc291cmNlX2lkIiwibGVuZ3RoIiwid3BiY19ib29raW5nX2Zvcm1fX29uX3N1Ym1pdF9fdWlfZWxlbWVudHNfZGlzYWJsZSIsIndwYmNfYm9va2luZ19mb3JtX19zZW5kX2J1dHRvbl9fZGlzYWJsZSIsIndwYmNfYm9va2luZ19mb3JtX19zcGluX2xvYWRlcl9fc2hvdyIsIndwYmNfYm9va2luZ19mb3JtX19zZW5kX2J1dHRvbl9fZW5hYmxlIiwicHJvcCIsImFmdGVyIiwicmVtb3ZlIiwiaGlkZSIsImxvY2F0aW9uIiwiaHJlZiIsImNvbmZpcm1fY29udGVudCIsInR5X21lc3NhZ2VfaGlkZSIsInR5X3BheW1lbnRfcGF5bWVudF9kZXNjcmlwdGlvbl9oaWRlIiwidHlfYm9va2luZ19jb3N0c19oaWRlIiwidHlfcGF5bWVudF9nYXRld2F5c19oaWRlIiwiaHRtbCJdLCJtYXBwaW5ncyI6IkFBQUEsYSxDQUVBO0FBQ0E7QUFDQTs7QUFHQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7QUFDQSxTQUFTQSx3QkFBVCxDQUFtQ0MsTUFBbkMsRUFBMkM7QUFFM0NDLEVBQUFBLE9BQU8sQ0FBQ0MsY0FBUixDQUF3QiwwQkFBeEI7QUFDQUQsRUFBQUEsT0FBTyxDQUFDQyxjQUFSLENBQXdCLHdCQUF4QjtBQUNBRCxFQUFBQSxPQUFPLENBQUNFLEdBQVIs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a0JBQUQsQ0FBYixDQUFrQyx1QkFBbEMsQ0FBMUMsV0FBZjtBQUNBb0QsRUFBQUEsZUFBZSw0Q0FBZjtBQUNIQSxFQUFBQSxlQUFlLHNGQUE4RUUsbUNBQTlFLGdCQUFzSHRELGFBQWEsQ0FBRSxrQkFBRixDQUFiLENBQXFDLGdDQUFyQyxFQUF3RVcsT0FBeEUsQ0FBaUYsTUFBakYsRUFBeUYsRUFBekYsQ0FBdEgsV0FBZjtBQUNHeUMsRUFBQUEsZUFBZSx1RUFBNkRwRCxhQUFhLENBQUMsa0JBQUQsQ0FBYixDQUFrQyxxQkFBbEMsQ0FBN0QsV0FBZjtBQUNBb0QsRUFBQUEsZUFBZSx1RUFBNkRwRCxhQUFhLENBQUMsa0JBQUQsQ0FBYixDQUFrQyxvQkFBbEMsQ0FBN0QsV0FBZjtBQUNIb0QsRUFBQUEsZUFBZSxnRkFBd0VHLHFCQUF4RSxnQkFBa0d2RCxhQUFhLENBQUUsa0JBQUYsQ0FBYixDQUFxQyxrQkFBckMsQ0FBbEcsV0FBZjtBQUNBb0QsRUFBQUEsZUFBZSxtRkFBMkVJLHdCQUEzRSxnQkFBd0d4RCxhQUFhLENBQUUsa0JBQUYsQ0FBYixDQUFxQyxxQkFBckMsRUFBNkRXLE9BQTdELENBQXNFLE1BQXRFLEVBQThFLEVBQTlFLEVBQW1GQSxPQUFuRixDQUE0RixlQUE1RixFQUE2RyxRQUE3RyxDQUF4RyxXQUFmO0FBQ0d5QyxFQUFBQSxlQUFlLGtCQUFmO0FBQ0FBLEVBQUFBLGVBQWUsZ0JBQWY7QUFDSEEsRUFBQUEsZUFBZSxZQUFmO0FBRUMvRCxFQUFBQSxNQUFNLENBQUUsa0JBQWtCbUQsV0FBcEIsQ0FBTixDQUF3Q08sS0FBeEMsQ0FBK0NLLGVBQS9DO0FBQ0QiLCJzb3VyY2VzQ29udGVudCI6WyJcInVzZSBzdHJpY3RcIjtcclxuXHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4vLyAgQSBqIGEgeCAgICBBIGQgZCAgICBOIGUgdyAgICBCIG8gbyBrIGkgbiBnXHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHJcbi8qKlxyXG4gKiBTdWJtaXQgbmV3IGJvb2tpbmdcclxuICpcclxuICogQHBhcmFtIHBhcmFtcyAgID0gICAgIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAncmVzb3VyY2VfaWQnICAgICAgICA6IHJlc291cmNlX2lkLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdkYXRlc19kZG1teXlfY3N2JyAgIDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICdkYXRlX2Jvb2tpbmcnICsgcmVzb3VyY2VfaWQgKS52YWx1ZSxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnZm9ybWRhdGEnICAgICAgICAgICA6IGZvcm1kYXRhLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdib29raW5nX2hhc2gnICAgICAgIDogbXlfYm9va2luZ19oYXNoLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdjdXN0b21fZm9ybScgICAgICAgIDogbXlfYm9va2luZ19mb3JtLFxyXG5cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnY2FwdGNoYV9jaGFsYW5nZScgICA6IGNhcHRjaGFfY2hhbGFuZ2UsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2NhcHRjaGFfdXNlcl9pbnB1dCcgOiB1c2VyX2NhcHRjaGEsXHJcblxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdpc19lbWFpbHNfc2VuZCcgICAgIDogaXNfc2VuZF9lbWVpbHMsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2FjdGl2ZV9sb2NhbGUnICAgICAgOiB3cGRldl9hY3RpdmVfbG9jYWxlXHJcblx0XHRcdFx0XHRcdH1cclxuICpcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWp4X2Jvb2tpbmdfX2NyZWF0ZSggcGFyYW1zICl7XHJcblxyXG5jb25zb2xlLmdyb3VwQ29sbGFwc2VkKCAnV1BCQ19BSlhfQk9PS0lOR19fQ1JFQVRFJyApO1xyXG5jb25zb2xlLmdyb3VwQ29sbGFwc2VkKCAnPT0gQmVmb3JlIEFqYXggU2VuZCA9PScgKTtcclxuY29uc29sZS5sb2coIHBhcmFtcyApO1xyXG5jb25zb2xlLmdyb3VwRW5kKCk7XHJcblxyXG5cdHBhcmFtcyA9IHdwYmNfY2FwdGNoYV9fc2ltcGxlX19tYXliZV9yZW1vdmVfaW5fYWp4X3BhcmFtcyggcGFyYW1zICk7XHJcblxyXG5cdC8vIFN0YXJ0IEFqYXhcclxuXHRqUXVlcnkucG9zdCggd3BiY19nbG9iYWwxLndwYmNfYWpheHVybCxcclxuXHRcdFx0XHR7XHJcblx0XHRcdFx0XHRhY3Rpb24gICAgICAgICAgOiAnV1BCQ19BSlhfQk9PS0lOR19fQ1JFQVRFJyxcclxuXHRcdFx0XHRcdHdwYmNfYWp4X3VzZXJfaWQ6IF93cGJjLmdldF9zZWN1cmVfcGFyYW0oICd1c2VyX2lkJyApLFxyXG5cdFx0XHRcdFx0bm9uY2UgICAgICAgICAgIDogX3dwYmMuZ2V0X3NlY3VyZV9wYXJhbSggJ25vbmNlJyApLFxyXG5cdFx0XHRcdFx0d3BiY19hanhfbG9jYWxlIDogX3dwYmMuZ2V0X3NlY3VyZV9wYXJhbSggJ2xvY2FsZScgKSxcclxuXHJcblx0XHRcdFx0XHRjYWxlbmRhcl9yZXF1ZXN0X3BhcmFtcyA6IHBhcmFtc1xyXG5cclxuXHRcdFx0XHRcdC8qKlxyXG5cdFx0XHRcdFx0ICogIFVzdWFsbHkgIHBhcmFtcyA9IHsgJ3Jlc291cmNlX2lkJyAgICAgICAgOiByZXNvdXJjZV9pZCxcclxuXHRcdFx0XHRcdCAqXHRcdFx0XHRcdFx0J2RhdGVzX2RkbW15eV9jc3YnICAgOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggJ2RhdGVfYm9va2luZycgKyByZXNvdXJjZV9pZCApLnZhbHVlLFxyXG5cdFx0XHRcdFx0ICpcdFx0XHRcdFx0XHQnZm9ybWRhdGEnICAgICAgICAgICA6IGZvcm1kYXRhLFxyXG5cdFx0XHRcdFx0ICpcdFx0XHRcdFx0XHQnYm9va2luZ19oYXNoJyAgICAgICA6IG15X2Jvb2tpbmdfaGFzaCxcclxuXHRcdFx0XHRcdCAqXHRcdFx0XHRcdFx0J2N1c3RvbV9mb3JtJyAgICAgICAgOiBteV9ib29raW5nX2Zvcm0sXHJcblx0XHRcdFx0XHQgKlxyXG5cdFx0XHRcdFx0ICpcdFx0XHRcdFx0XHQnY2FwdGNoYV9jaGFsYW5nZScgICA6IGNhcHRjaGFfY2hhbGFuZ2UsXHJcblx0XHRcdFx0XHQgKlx0XHRcdFx0XHRcdCd1c2VyX2NhcHRjaGEnICAgICAgIDogdXNlcl9jYXB0Y2hhLFxyXG5cdFx0XHRcdFx0ICpcclxuXHRcdFx0XHRcdCAqXHRcdFx0XHRcdFx0J2lzX2VtYWlsc19zZW5kJyAgICAgOiBpc19zZW5kX2VtZWlscyxcclxuXHRcdFx0XHRcdCAqXHRcdFx0XHRcdFx0J2FjdGl2ZV9sb2NhbGUnICAgICAgOiB3cGRldl9hY3RpdmVfbG9jYWxlXHJcblx0XHRcdFx0XHQgKlx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0ICovXHJcblx0XHRcdFx0fSxcclxuXHJcblx0XHRcdFx0LyoqXHJcblx0XHRcdFx0ICogUyB1IGMgYyBlIHMgc1xyXG5cdFx0XHRcdCAqXHJcblx0XHRcdFx0ICogQHBhcmFtIHJlc3BvbnNlX2RhdGFcdFx0LVx0aXRzIG9iamVjdCByZXR1cm5lZCBmcm9tICBBamF4IC0gY2xhc3MtbGl2ZS1zZWFyY2cucGhwXHJcblx0XHRcdFx0ICogQHBhcmFtIHRleHRTdGF0dXNcdFx0LVx0J3N1Y2Nlc3MnXHJcblx0XHRcdFx0ICogQHBhcmFtIGpxWEhSXHRcdFx0XHQtXHRPYmplY3RcclxuXHRcdFx0XHQgKi9cclxuXHRcdFx0XHRmdW5jdGlvbiAoIHJlc3BvbnNlX2RhdGEsIHRleHRTdGF0dXMsIGpxWEhSICkge1xyXG5jb25zb2xlLmxvZyggJyA9PSBSZXNwb25zZSBXUEJDX0FKWF9CT09LSU5HX19DUkVBVEUgPT0gJyApO1xyXG5mb3IgKCB2YXIgb2JqX2tleSBpbiByZXNwb25zZV9kYXRhICl7XHJcblx0Y29uc29sZS5ncm91cENvbGxhcHNlZCggJz09JyArIG9ial9rZXkgKyAnPT0nICk7XHJcblx0Y29uc29sZS5sb2coICcgOiAnICsgb2JqX2tleSArICcgOiAnLCByZXNwb25zZV9kYXRhWyBvYmpfa2V5IF0gKTtcclxuXHRjb25zb2xlLmdyb3VwRW5kKCk7XHJcbn1cclxuY29uc29sZS5ncm91cEVuZCgpO1xyXG5cclxuXHJcblx0XHRcdFx0XHQvLyA8ZWRpdG9yLWZvbGQgICAgIGRlZmF1bHRzdGF0ZT1cImNvbGxhcHNlZFwiICAgICBkZXNjPVwiID0gRXJyb3IgTWVzc2FnZSEgU2VydmVyIHJlc3BvbnNlIHdpdGggU3RyaW5nLiAgLT4gIEVfWF9JX1QgIFwiICA+XHJcblx0XHRcdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdFx0XHQvLyBUaGlzIHNlY3Rpb24gZXhlY3V0ZSwgIHdoZW4gc2VydmVyIHJlc3BvbnNlIHdpdGggIFN0cmluZyBpbnN0ZWFkIG9mIE9iamVjdCAtLSBVc3VhbGx5ICBpdCdzIGJlY2F1c2Ugb2YgbWlzdGFrZSBpbiBjb2RlICFcclxuXHRcdFx0XHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdFx0XHRcdGlmICggKHR5cGVvZiByZXNwb25zZV9kYXRhICE9PSAnb2JqZWN0JykgfHwgKHJlc3BvbnNlX2RhdGEgPT09IG51bGwpICl7XHJcblxyXG5cdFx0XHRcdFx0XHR2YXIgY2FsZW5kYXJfaWQgPSB3cGJjX2dldF9yZXNvdXJjZV9pZF9fZnJvbV9hanhfcG9zdF9kYXRhX3VybCggdGhpcy5kYXRhICk7XHJcblx0XHRcdFx0XHRcdHZhciBqcV9ub2RlID0gJyNib29raW5nX2Zvcm0nICsgY2FsZW5kYXJfaWQ7XHJcblxyXG5cdFx0XHRcdFx0XHRpZiAoICcnID09IHJlc3BvbnNlX2RhdGEgKXtcclxuXHRcdFx0XHRcdFx0XHRyZXNwb25zZV9kYXRhID0gJzxzdHJvbmc+JyArICdFcnJvciEgU2VydmVyIHJlc3BvbmQgd2l0aCBlbXB0eSBzdHJpbmchJyArICc8L3N0cm9uZz4gJyA7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0Ly8gU2hvdyBNZXNzYWdlXHJcblx0XHRcdFx0XHRcdHdwYmNfZnJvbnRfZW5kX19zaG93X21lc3NhZ2UoIHJlc3BvbnNlX2RhdGEgLCB7ICd0eXBlJyAgICAgOiAnZXJyb3InLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnc2hvd19oZXJlJzogeydqcV9ub2RlJzoganFfbm9kZSwgJ3doZXJlJzogJ2FmdGVyJ30sXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdpc19hcHBlbmQnOiB0cnVlLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnc3R5bGUnICAgIDogJ3RleHQtYWxpZ246bGVmdDsnLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGVsYXknICAgIDogMFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG5cdFx0XHRcdFx0XHQvLyBFbmFibGUgU3VibWl0IHwgSGlkZSBzcGluIGxvYWRlclxyXG5cdFx0XHRcdFx0XHR3cGJjX2Jvb2tpbmdfZm9ybV9fb25fcmVzcG9uc2VfX3VpX2VsZW1lbnRzX2VuYWJsZSggY2FsZW5kYXJfaWQgKTtcclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0Ly8gPC9lZGl0b3ItZm9sZD5cclxuXHJcblxyXG5cdFx0XHRcdFx0Ly8gPGVkaXRvci1mb2xkICAgICBkZWZhdWx0c3RhdGU9XCJjb2xsYXBzZWRcIiAgICAgZGVzYz1cIiAgPT0gIFRoaXMgc2VjdGlvbiBleGVjdXRlLCAgd2hlbiB3ZSBoYXZlIEtOT1dOIGVycm9ycyBmcm9tIEJvb2tpbmcgQ2FsZW5kYXIuICAtPiAgRV9YX0lfVCAgXCIgID5cclxuXHRcdFx0XHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdFx0XHRcdC8vIFRoaXMgc2VjdGlvbiBleGVjdXRlLCAgd2hlbiB3ZSBoYXZlIEtOT1dOIGVycm9ycyBmcm9tIEJvb2tpbmcgQ2FsZW5kYXJcclxuXHRcdFx0XHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblx0XHRcdFx0XHRpZiAoICdvaycgIT0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnc3RhdHVzJyBdICkge1xyXG5cclxuXHRcdFx0XHRcdFx0c3dpdGNoICggcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnc3RhdHVzX2Vycm9yJyBdICl7XHJcblxyXG5cdFx0XHRcdFx0XHRcdGNhc2UgJ2NhcHRjaGFfc2ltcGxlX3dyb25nJzpcclxuXHRcdFx0XHRcdFx0XHRcdHdwYmNfY2FwdGNoYV9fc2ltcGxlX191cGRhdGUoIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQncmVzb3VyY2VfaWQnOiByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF0sXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3VybCcgICAgICAgIDogcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnY2FwdGNoYV9fc2ltcGxlJyBdWyAndXJsJyBdLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdjaGFsbGVuZ2UnICA6IHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2NhcHRjaGFfX3NpbXBsZScgXVsgJ2NoYWxsZW5nZScgXSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnbWVzc2FnZScgICAgOiByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2UnIF0ucmVwbGFjZSggL1xcbi9nLCBcIjxiciAvPlwiIClcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0KTtcclxuXHRcdFx0XHRcdFx0XHRcdGJyZWFrO1xyXG5cclxuXHRcdFx0XHRcdFx0XHRjYXNlICdyZXNvdXJjZV9pZF9pbmNvcnJlY3QnOlx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gU2hvdyBFcnJvciBNZXNzYWdlIC0gaW5jb3JyZWN0ICBib29raW5nIHJlc291cmNlIElEIGR1cmluZyBzdWJtaXQgb2YgYm9va2luZy5cclxuXHRcdFx0XHRcdFx0XHRcdHZhciBtZXNzYWdlX2lkID0gd3BiY19mcm9udF9lbmRfX3Nob3dfbWVzc2FnZSggcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnYWp4X2FmdGVyX2FjdGlvbl9tZXNzYWdlJyBdLnJlcGxhY2UoIC9cXG4vZywgXCI8YnIgLz5cIiApLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd0eXBlJyA6ICgndW5kZWZpbmVkJyAhPT0gdHlwZW9mIChyZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2Vfc3RhdHVzJyBdKSlcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ/IHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZV9zdGF0dXMnIF0gOiAnd2FybmluZycsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGVsYXknICAgIDogMCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzaG93X2hlcmUnOiB7ICd3aGVyZSc6ICdhZnRlcicsICdqcV9ub2RlJzogJyNib29raW5nX2Zvcm0nICsgcGFyYW1zWyAncmVzb3VyY2VfaWQnIF0gfVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0gKTtcclxuXHRcdFx0XHRcdFx0XHRcdGJyZWFrO1xyXG5cclxuXHRcdFx0XHRcdFx0XHRjYXNlICdib29raW5nX2Nhbl9ub3Rfc2F2ZSc6XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBXZSBjYW4gbm90IHNhdmUgYm9va2luZywgYmVjYXVzZSBkYXRlcyBhcmUgYm9va2VkIG9yIGNhbiBub3Qgc2F2ZSBpbiBzYW1lIGJvb2tpbmcgcmVzb3VyY2UgYWxsIHRoZSBkYXRlc1xyXG5cdFx0XHRcdFx0XHRcdFx0dmFyIG1lc3NhZ2VfaWQgPSB3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlKCByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2UnIF0ucmVwbGFjZSggL1xcbi9nLCBcIjxiciAvPlwiICksXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0e1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3R5cGUnIDogKCd1bmRlZmluZWQnICE9PSB0eXBlb2YgKHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZV9zdGF0dXMnIF0pKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdD8gcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnYWp4X2FmdGVyX2FjdGlvbl9tZXNzYWdlX3N0YXR1cycgXSA6ICd3YXJuaW5nJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdkZWxheScgICAgOiAwLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3Nob3dfaGVyZSc6IHsgJ3doZXJlJzogJ2FmdGVyJywgJ2pxX25vZGUnOiAnI2Jvb2tpbmdfZm9ybScgKyBwYXJhbXNbICdyZXNvdXJjZV9pZCcgXSB9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG5cclxuXHRcdFx0XHRcdFx0XHRcdC8vIEVuYWJsZSBTdWJtaXQgfCBIaWRlIHNwaW4gbG9hZGVyXHJcblx0XHRcdFx0XHRcdFx0XHR3cGJjX2Jvb2tpbmdfZm9ybV9fb25fcmVzcG9uc2VfX3VpX2VsZW1lbnRzX2VuYWJsZSggcmVzcG9uc2VfZGF0YVsgJ3Jlc291cmNlX2lkJyBdICk7XHJcblxyXG5cdFx0XHRcdFx0XHRcdFx0YnJlYWs7XHJcblxyXG5cclxuXHRcdFx0XHRcdFx0XHRkZWZhdWx0OlxyXG5cclxuXHRcdFx0XHRcdFx0XHRcdC8vIDxlZGl0b3ItZm9sZCAgICAgZGVmYXVsdHN0YXRlPVwiY29sbGFwc2VkXCIgICAgICAgICAgICAgICAgICAgICAgICBkZXNjPVwiID0gRm9yIGRlYnVnIG9ubHkgPyAtLSAgU2hvdyBNZXNzYWdlIHVuZGVyIHRoZSBmb3JtID0gXCIgID5cclxuXHRcdFx0XHRcdFx0XHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdFx0XHRcdFx0XHRpZiAoXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0KCAndW5kZWZpbmVkJyAhPT0gdHlwZW9mIChyZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2UnIF0pIClcclxuXHRcdFx0XHRcdFx0XHRcdFx0ICYmICggJycgIT0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnYWp4X2FmdGVyX2FjdGlvbl9tZXNzYWdlJyBdLnJlcGxhY2UoIC9cXG4vZywgXCI8YnIgLz5cIiApIClcclxuXHRcdFx0XHRcdFx0XHRcdCl7XHJcblxyXG5cdFx0XHRcdFx0XHRcdFx0XHR2YXIgY2FsZW5kYXJfaWQgPSB3cGJjX2dldF9yZXNvdXJjZV9pZF9fZnJvbV9hanhfcG9zdF9kYXRhX3VybCggdGhpcy5kYXRhICk7XHJcblx0XHRcdFx0XHRcdFx0XHRcdHZhciBqcV9ub2RlID0gJyNib29raW5nX2Zvcm0nICsgY2FsZW5kYXJfaWQ7XHJcblxyXG5cdFx0XHRcdFx0XHRcdFx0XHR2YXIgYWp4X2FmdGVyX2Jvb2tpbmdfbWVzc2FnZSA9IHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZScgXS5yZXBsYWNlKCAvXFxuL2csIFwiPGJyIC8+XCIgKTtcclxuXHJcblx0XHRcdFx0XHRcdFx0XHRcdGNvbnNvbGUubG9nKCBhanhfYWZ0ZXJfYm9va2luZ19tZXNzYWdlICk7XHJcblxyXG5cdFx0XHRcdFx0XHRcdFx0XHQvKipcclxuXHRcdFx0XHRcdFx0XHRcdFx0ICogLy8gU2hvdyBNZXNzYWdlXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0dmFyIGFqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZV9pZCA9IHdwYmNfZnJvbnRfZW5kX19zaG93X21lc3NhZ2UoIGFqeF9hZnRlcl9ib29raW5nX21lc3NhZ2UsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd0eXBlJyA6ICgndW5kZWZpbmVkJyAhPT0gdHlwZW9mIChyZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2Vfc3RhdHVzJyBdKSlcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdD8gcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnYWp4X2FmdGVyX2FjdGlvbl9tZXNzYWdlX3N0YXR1cycgXSA6ICdpbmZvJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2RlbGF5JyAgICA6IDEwMDAwLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnc2hvd19oZXJlJzoge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdqcV9ub2RlJzoganFfbm9kZSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnd2hlcmUnICA6ICdhZnRlcidcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0IH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0gKTtcclxuXHRcdFx0XHRcdFx0XHRcdFx0ICovXHJcblx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHQvLyA8L2VkaXRvci1mb2xkPlxyXG5cdFx0XHRcdFx0XHR9XHJcblxyXG5cclxuXHRcdFx0XHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHRcdFx0XHQvLyBSZWFjdGl2YXRlIGNhbGVuZGFyIGFnYWluID9cclxuXHRcdFx0XHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHRcdFx0XHQvLyBFbmFibGUgU3VibWl0IHwgSGlkZSBzcGluIGxvYWRlclxyXG5cdFx0XHRcdFx0XHR3cGJjX2Jvb2tpbmdfZm9ybV9fb25fcmVzcG9uc2VfX3VpX2VsZW1lbnRzX2VuYWJsZSggcmVzcG9uc2VfZGF0YVsgJ3Jlc291cmNlX2lkJyBdICk7XHJcblxyXG5cdFx0XHRcdFx0XHQvLyBVbnNlbGVjdCAgZGF0ZXNcclxuXHRcdFx0XHRcdFx0d3BiY19jYWxlbmRhcl9fdW5zZWxlY3RfYWxsX2RhdGVzKCByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF0gKTtcclxuXHJcblx0XHRcdFx0XHRcdC8vICdyZXNvdXJjZV9pZCcgICAgPT4gJHBhcmFtc1sncmVzb3VyY2VfaWQnXSxcclxuXHRcdFx0XHRcdFx0Ly8gJ2Jvb2tpbmdfaGFzaCcgICA9PiAkYm9va2luZ19oYXNoLFxyXG5cdFx0XHRcdFx0XHQvLyAncmVxdWVzdF91cmknICAgID0+ICRfU0VSVkVSWydSRVFVRVNUX1VSSSddLCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gSXMgaXQgdGhlIHNhbWUgYXMgd2luZG93LmxvY2F0aW9uLmhyZWYgb3JcclxuXHRcdFx0XHRcdFx0Ly8gJ2N1c3RvbV9mb3JtJyAgICA9PiAkcGFyYW1zWydjdXN0b21fZm9ybSddLCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIE9wdGlvbmFsLlxyXG5cdFx0XHRcdFx0XHQvLyAnYWdncmVnYXRlX3Jlc291cmNlX2lkX3N0cicgPT4gaW1wbG9kZSggJywnLCAkcGFyYW1zWydhZ2dyZWdhdGVfcmVzb3VyY2VfaWRfYXJyJ10gKSAgICAgLy8gT3B0aW9uYWwuIFJlc291cmNlIElEICAgZnJvbSAgYWdncmVnYXRlIHBhcmFtZXRlciBpbiBzaG9ydGNvZGUuXHJcblxyXG5cdFx0XHRcdFx0XHQvLyBMb2FkIG5ldyBkYXRhIGluIGNhbGVuZGFyLlxyXG5cdFx0XHRcdFx0XHR3cGJjX2NhbGVuZGFyX19sb2FkX2RhdGFfX2FqeCgge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICAncmVzb3VyY2VfaWQnIDogcmVzcG9uc2VfZGF0YVsgJ3Jlc291cmNlX2lkJyBdXHRcdFx0XHRcdFx0XHQvLyBJdCdzIGZyb20gcmVzcG9uc2UgLi4uQUpYX0JPT0tJTkdfX0NSRUFURSBvZiBpbml0aWFsIHNlbnQgcmVzb3VyY2VfaWRcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCwgJ2Jvb2tpbmdfaGFzaCc6IHJlc3BvbnNlX2RhdGFbICdhanhfY2xlYW5lZF9wYXJhbXMnIF1bJ2Jvb2tpbmdfaGFzaCddIFx0Ly8gPz8gd2UgY2FuIG5vdCB1c2UgaXQsICBiZWNhdXNlIEhBU0ggY2huYWdlZCBpbiBhbnkgIGNhc2UhXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQsICdyZXF1ZXN0X3VyaScgOiByZXNwb25zZV9kYXRhWyAnYWp4X2NsZWFuZWRfcGFyYW1zJyBdWydyZXF1ZXN0X3VyaSddXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQsICdjdXN0b21fZm9ybScgOiByZXNwb25zZV9kYXRhWyAnYWp4X2NsZWFuZWRfcGFyYW1zJyBdWydjdXN0b21fZm9ybSddXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gQWdncmVnYXRlIGJvb2tpbmcgcmVzb3VyY2VzLCAgaWYgYW55ID9cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCwgJ2FnZ3JlZ2F0ZV9yZXNvdXJjZV9pZF9zdHInIDogX3dwYmMuYm9va2luZ19fZ2V0X3BhcmFtX3ZhbHVlKCByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF0sICdhZ2dyZWdhdGVfcmVzb3VyY2VfaWRfYXJyJyApLmpvaW4oJywnKVxyXG5cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9ICk7XHJcblx0XHRcdFx0XHRcdC8vIEV4aXRcclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdC8vIDwvZWRpdG9yLWZvbGQ+XHJcblxyXG5cclxuLypcclxuXHQvLyBTaG93IENhbGVuZGFyXHJcblx0d3BiY19jYWxlbmRhcl9fbG9hZGluZ19fc3RvcCggcmVzcG9uc2VfZGF0YVsgJ3Jlc291cmNlX2lkJyBdICk7XHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBCb29raW5ncyAtIERhdGVzXHJcblx0X3dwYmMuYm9va2luZ3NfaW5fY2FsZW5kYXJfX3NldF9kYXRlcyggIHJlc3BvbnNlX2RhdGFbICdyZXNvdXJjZV9pZCcgXSwgcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWydkYXRlcyddICApO1xyXG5cclxuXHQvLyBCb29raW5ncyAtIENoaWxkIG9yIG9ubHkgc2luZ2xlIGJvb2tpbmcgcmVzb3VyY2UgaW4gZGF0ZXNcclxuXHRfd3BiYy5ib29raW5nX19zZXRfcGFyYW1fdmFsdWUoIHJlc3BvbnNlX2RhdGFbICdyZXNvdXJjZV9pZCcgXSwgJ3Jlc291cmNlc19pZF9hcnJfX2luX2RhdGVzJywgcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAncmVzb3VyY2VzX2lkX2Fycl9faW5fZGF0ZXMnIF0gKTtcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cdC8vIFVwZGF0ZSBjYWxlbmRhclxyXG5cdHdwYmNfY2FsZW5kYXJfX3VwZGF0ZV9sb29rKCByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF0gKTtcclxuKi9cclxuXHJcblx0XHRcdFx0XHQvLyBIaWRlIHNwaW4gbG9hZGVyXHJcblx0XHRcdFx0XHR3cGJjX2Jvb2tpbmdfZm9ybV9fc3Bpbl9sb2FkZXJfX2hpZGUoIHJlc3BvbnNlX2RhdGFbICdyZXNvdXJjZV9pZCcgXSApO1xyXG5cclxuXHRcdFx0XHRcdC8vIEhpZGUgYm9va2luZyBmb3JtXHJcblx0XHRcdFx0XHR3cGJjX2Jvb2tpbmdfZm9ybV9fYW5pbWF0ZWRfX2hpZGUoIHJlc3BvbnNlX2RhdGFbICdyZXNvdXJjZV9pZCcgXSApO1xyXG5cclxuXHRcdFx0XHRcdC8vIFNob3cgQ29uZmlybWF0aW9uIHwgUGF5bWVudCBzZWN0aW9uXHJcblx0XHRcdFx0XHR3cGJjX3Nob3dfdGhhbmtfeW91X21lc3NhZ2VfYWZ0ZXJfYm9va2luZyggcmVzcG9uc2VfZGF0YSApO1xyXG5cclxuXHRcdFx0XHRcdHNldFRpbWVvdXQoIGZ1bmN0aW9uICgpe1xyXG5cdFx0XHRcdFx0XHR3cGJjX2RvX3Njcm9sbCggJyN3cGJjX3Njcm9sbF9wb2ludF8nICsgcmVzcG9uc2VfZGF0YVsgJ3Jlc291cmNlX2lkJyBdLCAxMCApO1xyXG5cdFx0XHRcdFx0fSwgNTAwICk7XHJcblxyXG5cclxuXHJcblx0XHRcdFx0fVxyXG5cdFx0XHQgICkuZmFpbChcclxuXHRcdFx0XHQgIC8vIDxlZGl0b3ItZm9sZCAgICAgZGVmYXVsdHN0YXRlPVwiY29sbGFwc2VkXCIgICAgICAgICAgICAgICAgICAgICAgICBkZXNjPVwiID0gVGhpcyBzZWN0aW9uIGV4ZWN1dGUsICB3aGVuICBOT05DRSBmaWVsZCB3YXMgbm90IHBhc3NlZCBvciBzb21lIGVycm9yIGhhcHBlbmVkIGF0ICBzZXJ2ZXIhID0gXCIgID5cclxuXHRcdFx0XHQgIGZ1bmN0aW9uICgganFYSFIsIHRleHRTdGF0dXMsIGVycm9yVGhyb3duICkgeyAgICBpZiAoIHdpbmRvdy5jb25zb2xlICYmIHdpbmRvdy5jb25zb2xlLmxvZyApeyBjb25zb2xlLmxvZyggJ0FqYXhfRXJyb3InLCBqcVhIUiwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24gKTsgfVxyXG5cclxuXHRcdFx0XHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdFx0XHRcdC8vIFRoaXMgc2VjdGlvbiBleGVjdXRlLCAgd2hlbiAgTk9OQ0UgZmllbGQgd2FzIG5vdCBwYXNzZWQgb3Igc29tZSBlcnJvciBoYXBwZW5lZCBhdCAgc2VydmVyIVxyXG5cdFx0XHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHRcdFx0XHRcdC8vIEdldCBDb250ZW50IG9mIEVycm9yIE1lc3NhZ2VcclxuXHRcdFx0XHRcdHZhciBlcnJvcl9tZXNzYWdlID0gJzxzdHJvbmc+JyArICdFcnJvciEnICsgJzwvc3Ryb25nPiAnICsgZXJyb3JUaHJvd24gO1xyXG5cdFx0XHRcdFx0aWYgKCBqcVhIUi5zdGF0dXMgKXtcclxuXHRcdFx0XHRcdFx0ZXJyb3JfbWVzc2FnZSArPSAnICg8Yj4nICsganFYSFIuc3RhdHVzICsgJzwvYj4pJztcclxuXHRcdFx0XHRcdFx0aWYgKDQwMyA9PSBqcVhIUi5zdGF0dXMgKXtcclxuXHRcdFx0XHRcdFx0XHRlcnJvcl9tZXNzYWdlICs9ICc8YnI+IFByb2JhYmx5IG5vbmNlIGZvciB0aGlzIHBhZ2UgaGFzIGJlZW4gZXhwaXJlZC4gUGxlYXNlIDxhIGhyZWY9XCJqYXZhc2NyaXB0OnZvaWQoMClcIiBvbmNsaWNrPVwiamF2YXNjcmlwdDpsb2NhdGlvbi5yZWxvYWQoKTtcIj5yZWxvYWQgdGhlIHBhZ2U8L2E+Lic7XHJcblx0XHRcdFx0XHRcdFx0ZXJyb3JfbWVzc2FnZSArPSAnPGJyPiBPdGhlcndpc2UsIHBsZWFzZSBjaGVjayB0aGlzIDxhIHN0eWxlPVwiZm9udC13ZWlnaHQ6IDYwMDtcIiBocmVmPVwiaHR0cHM6Ly93cGJvb2tpbmdjYWxlbmRhci5jb20vZmFxL3JlcXVlc3QtZG8tbm90LXBhc3Mtc2VjdXJpdHktY2hlY2svXCI+dHJvdWJsZXNob290aW5nIGluc3RydWN0aW9uPC9hPi48YnI+J1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRpZiAoIGpxWEhSLnJlc3BvbnNlVGV4dCApe1xyXG5cdFx0XHRcdFx0XHQvLyBFc2NhcGUgdGFncyBpbiBFcnJvciBtZXNzYWdlXHJcblx0XHRcdFx0XHRcdGVycm9yX21lc3NhZ2UgKz0gJzxicj48c3Ryb25nPlJlc3BvbnNlPC9zdHJvbmc+PGRpdiBzdHlsZT1cInBhZGRpbmc6IDAgMTBweDttYXJnaW46IDAgMCAxMHB4O2JvcmRlci1yYWRpdXM6M3B4OyBib3gtc2hhZG93OjBweCAwcHggMXB4ICNhM2EzYTM7XCI+JyArIGpxWEhSLnJlc3BvbnNlVGV4dC5yZXBsYWNlKC8mL2csIFwiJmFtcDtcIilcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgLnJlcGxhY2UoLzwvZywgXCImbHQ7XCIpXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0IC5yZXBsYWNlKC8+L2csIFwiJmd0O1wiKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAucmVwbGFjZSgvXCIvZywgXCImcXVvdDtcIilcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgLnJlcGxhY2UoLycvZywgXCImIzM5O1wiKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdCsnPC9kaXY+JztcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdGVycm9yX21lc3NhZ2UgPSBlcnJvcl9tZXNzYWdlLnJlcGxhY2UoIC9cXG4vZywgXCI8YnIgLz5cIiApO1xyXG5cclxuXHRcdFx0XHRcdHZhciBjYWxlbmRhcl9pZCA9IHdwYmNfZ2V0X3Jlc291cmNlX2lkX19mcm9tX2FqeF9wb3N0X2RhdGFfdXJsKCB0aGlzLmRhdGEgKTtcclxuXHRcdFx0XHRcdHZhciBqcV9ub2RlID0gJyNib29raW5nX2Zvcm0nICsgY2FsZW5kYXJfaWQ7XHJcblxyXG5cdFx0XHRcdFx0Ly8gU2hvdyBNZXNzYWdlXHJcblx0XHRcdFx0XHR3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlKCBlcnJvcl9tZXNzYWdlICwgeyAndHlwZScgICAgIDogJ2Vycm9yJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzaG93X2hlcmUnOiB7J2pxX25vZGUnOiBqcV9ub2RlLCAnd2hlcmUnOiAnYWZ0ZXInfSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdpc19hcHBlbmQnOiB0cnVlLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3N0eWxlJyAgICA6ICd0ZXh0LWFsaWduOmxlZnQ7JyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdkZWxheScgICAgOiAwXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG5cdFx0XHRcdFx0Ly8gRW5hYmxlIFN1Ym1pdCB8IEhpZGUgc3BpbiBsb2FkZXJcclxuXHRcdFx0XHRcdHdwYmNfYm9va2luZ19mb3JtX19vbl9yZXNwb25zZV9fdWlfZWxlbWVudHNfZW5hYmxlKCBjYWxlbmRhcl9pZCApO1xyXG5cdFx0XHQgIFx0IH1cclxuXHRcdFx0XHQgLy8gPC9lZGl0b3ItZm9sZD5cclxuXHRcdFx0ICApXHJcblx0ICAgICAgICAgIC8vIC5kb25lKCAgIGZ1bmN0aW9uICggZGF0YSwgdGV4dFN0YXR1cywganFYSFIgKSB7ICAgaWYgKCB3aW5kb3cuY29uc29sZSAmJiB3aW5kb3cuY29uc29sZS5sb2cgKXsgY29uc29sZS5sb2coICdzZWNvbmQgc3VjY2VzcycsIGRhdGEsIHRleHRTdGF0dXMsIGpxWEhSICk7IH0gICAgfSlcclxuXHRcdFx0ICAvLyAuYWx3YXlzKCBmdW5jdGlvbiAoIGRhdGFfanFYSFIsIHRleHRTdGF0dXMsIGpxWEhSX2Vycm9yVGhyb3duICkgeyAgIGlmICggd2luZG93LmNvbnNvbGUgJiYgd2luZG93LmNvbnNvbGUubG9nICl7IGNvbnNvbGUubG9nKCAnYWx3YXlzIGZpbmlzaGVkJywgZGF0YV9qcVhIUiwgdGV4dFN0YXR1cywganFYSFJfZXJyb3JUaHJvd24gKTsgfSAgICAgfSlcclxuXHRcdFx0ICA7ICAvLyBFbmQgQWpheFxyXG5cclxuXHRyZXR1cm4gdHJ1ZTtcclxufVxyXG5cclxuXHJcblx0Ly8gPGVkaXRvci1mb2xkICAgICBkZWZhdWx0c3RhdGU9XCJjb2xsYXBzZWRcIiAgICAgICAgICAgICAgICAgICAgICAgIGRlc2M9XCIgID09ICBDQVBUQ0hBID09ICBcIiAgPlxyXG5cclxuXHQvKipcclxuXHQgKiBVcGRhdGUgaW1hZ2UgaW4gY2FwdGNoYSBhbmQgc2hvdyB3YXJuaW5nIG1lc3NhZ2VcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSBwYXJhbXNcclxuXHQgKlxyXG5cdCAqIEV4YW1wbGUgb2YgJ3BhcmFtcycgOiB7XHJcblx0ICpcdFx0XHRcdFx0XHRcdCdyZXNvdXJjZV9pZCc6IHJlc3BvbnNlX2RhdGFbICdyZXNvdXJjZV9pZCcgXSxcclxuXHQgKlx0XHRcdFx0XHRcdFx0J3VybCcgICAgICAgIDogcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnY2FwdGNoYV9fc2ltcGxlJyBdWyAndXJsJyBdLFxyXG5cdCAqXHRcdFx0XHRcdFx0XHQnY2hhbGxlbmdlJyAgOiByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdjYXB0Y2hhX19zaW1wbGUnIF1bICdjaGFsbGVuZ2UnIF0sXHJcblx0ICpcdFx0XHRcdFx0XHRcdCdtZXNzYWdlJyAgICA6IHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZScgXS5yZXBsYWNlKCAvXFxuL2csIFwiPGJyIC8+XCIgKVxyXG5cdCAqXHRcdFx0XHRcdFx0fVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfY2FwdGNoYV9fc2ltcGxlX191cGRhdGUoIHBhcmFtcyApe1xyXG5cclxuXHRcdGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAnY2FwdGNoYV9pbnB1dCcgKyBwYXJhbXNbICdyZXNvdXJjZV9pZCcgXSApLnZhbHVlID0gJyc7XHJcblx0XHRkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggJ2NhcHRjaGFfaW1nJyArIHBhcmFtc1sgJ3Jlc291cmNlX2lkJyBdICkuc3JjID0gcGFyYW1zWyAndXJsJyBdO1xyXG5cdFx0ZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICd3cGRldl9jYXB0Y2hhX2NoYWxsZW5nZV8nICsgcGFyYW1zWyAncmVzb3VyY2VfaWQnIF0gKS52YWx1ZSA9IHBhcmFtc1sgJ2NoYWxsZW5nZScgXTtcclxuXHJcblx0XHQvLyBTaG93IHdhcm5pbmcgXHRcdEFmdGVyIENBUFRDSEEgSW1nXHJcblx0XHR2YXIgbWVzc2FnZV9pZCA9IHdwYmNfZnJvbnRfZW5kX19zaG93X21lc3NhZ2VfX3dhcm5pbmcoICcjY2FwdGNoYV9pbnB1dCcgKyBwYXJhbXNbICdyZXNvdXJjZV9pZCcgXSArICcgKyBpbWcnLCBwYXJhbXNbICdtZXNzYWdlJyBdICk7XHJcblxyXG5cdFx0Ly8gQW5pbWF0ZVxyXG5cdFx0alF1ZXJ5KCAnIycgKyBtZXNzYWdlX2lkICsgJywgJyArICcjY2FwdGNoYV9pbnB1dCcgKyBwYXJhbXNbICdyZXNvdXJjZV9pZCcgXSApLmZhZGVPdXQoIDM1MCApLmZhZGVJbiggMzAwICkuZmFkZU91dCggMzUwICkuZmFkZUluKCA0MDAgKS5hbmltYXRlKCB7b3BhY2l0eTogMX0sIDQwMDAgKTtcclxuXHRcdC8vIEZvY3VzIHRleHQgIGZpZWxkXHJcblx0XHRqUXVlcnkoICcjY2FwdGNoYV9pbnB1dCcgKyBwYXJhbXNbICdyZXNvdXJjZV9pZCcgXSApLnRyaWdnZXIoICdmb2N1cycgKTsgICAgXHRcdFx0XHRcdFx0XHRcdFx0Ly9GaXhJbjogOC43LjExLjEyXHJcblxyXG5cclxuXHRcdC8vIEVuYWJsZSBTdWJtaXQgfCBIaWRlIHNwaW4gbG9hZGVyXHJcblx0XHR3cGJjX2Jvb2tpbmdfZm9ybV9fb25fcmVzcG9uc2VfX3VpX2VsZW1lbnRzX2VuYWJsZSggcGFyYW1zWyAncmVzb3VyY2VfaWQnIF0gKTtcclxuXHR9XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBJZiB0aGUgY2FwdGNoYSBlbGVtZW50cyBub3QgZXhpc3QgIGluIHRoZSBib29raW5nIGZvcm0sICB0aGVuICByZW1vdmUgcGFyYW1ldGVycyByZWxhdGl2ZSBjYXB0Y2hhXHJcblx0ICogQHBhcmFtIHBhcmFtc1xyXG5cdCAqIEByZXR1cm5zIG9ialxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfY2FwdGNoYV9fc2ltcGxlX19tYXliZV9yZW1vdmVfaW5fYWp4X3BhcmFtcyggcGFyYW1zICl7XHJcblxyXG5cdFx0aWYgKCAhIHdwYmNfY2FwdGNoYV9fc2ltcGxlX19pc19leGlzdF9pbl9mb3JtKCBwYXJhbXNbICdyZXNvdXJjZV9pZCcgXSApICl7XHJcblx0XHRcdGRlbGV0ZSBwYXJhbXNbICdjYXB0Y2hhX2NoYWxhbmdlJyBdO1xyXG5cdFx0XHRkZWxldGUgcGFyYW1zWyAnY2FwdGNoYV91c2VyX2lucHV0JyBdO1xyXG5cdFx0fVxyXG5cdFx0cmV0dXJuIHBhcmFtcztcclxuXHR9XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBDaGVjayBpZiBDQVBUQ0hBIGV4aXN0IGluIHRoZSBib29raW5nIGZvcm1cclxuXHQgKiBAcGFyYW0gcmVzb3VyY2VfaWRcclxuXHQgKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX2NhcHRjaGFfX3NpbXBsZV9faXNfZXhpc3RfaW5fZm9ybSggcmVzb3VyY2VfaWQgKXtcclxuXHJcblx0XHRyZXR1cm4gKFxyXG5cdFx0XHRcdFx0XHQoMCAhPT0galF1ZXJ5KCAnI3dwZGV2X2NhcHRjaGFfY2hhbGxlbmdlXycgKyByZXNvdXJjZV9pZCApLmxlbmd0aClcclxuXHRcdFx0XHRcdCB8fCAoMCAhPT0galF1ZXJ5KCAnI2NhcHRjaGFfaW5wdXQnICsgcmVzb3VyY2VfaWQgKS5sZW5ndGgpXHJcblx0XHRcdFx0KTtcclxuXHR9XHJcblxyXG5cdC8vIDwvZWRpdG9yLWZvbGQ+XHJcblxyXG5cclxuXHQvLyA8ZWRpdG9yLWZvbGQgICAgIGRlZmF1bHRzdGF0ZT1cImNvbGxhcHNlZFwiICAgICAgICAgICAgICAgICAgICAgICAgZGVzYz1cIiAgPT0gIFNlbmQgQnV0dG9uIHwgRm9ybSBTcGluIExvYWRlciAgPT0gIFwiICA+XHJcblxyXG5cdC8qKlxyXG5cdCAqIERpc2FibGUgU2VuZCBidXR0b24gIHwgIFNob3cgU3BpbiBMb2FkZXJcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSByZXNvdXJjZV9pZFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfYm9va2luZ19mb3JtX19vbl9zdWJtaXRfX3VpX2VsZW1lbnRzX2Rpc2FibGUoIHJlc291cmNlX2lkICl7XHJcblxyXG5cdFx0Ly8gRGlzYWJsZSBTdWJtaXRcclxuXHRcdHdwYmNfYm9va2luZ19mb3JtX19zZW5kX2J1dHRvbl9fZGlzYWJsZSggcmVzb3VyY2VfaWQgKTtcclxuXHJcblx0XHQvLyBTaG93IFNwaW4gbG9hZGVyIGluIGJvb2tpbmcgZm9ybVxyXG5cdFx0d3BiY19ib29raW5nX2Zvcm1fX3NwaW5fbG9hZGVyX19zaG93KCByZXNvdXJjZV9pZCApO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogRW5hYmxlIFNlbmQgYnV0dG9uICB8ICAgSGlkZSBTcGluIExvYWRlclxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHJlc291cmNlX2lkXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19ib29raW5nX2Zvcm1fX29uX3Jlc3BvbnNlX191aV9lbGVtZW50c19lbmFibGUocmVzb3VyY2VfaWQpe1xyXG5cclxuXHRcdC8vIEVuYWJsZSBTdWJtaXRcclxuXHRcdHdwYmNfYm9va2luZ19mb3JtX19zZW5kX2J1dHRvbl9fZW5hYmxlKCByZXNvdXJjZV9pZCApO1xyXG5cclxuXHRcdC8vIEhpZGUgU3BpbiBsb2FkZXIgaW4gYm9va2luZyBmb3JtXHJcblx0XHR3cGJjX2Jvb2tpbmdfZm9ybV9fc3Bpbl9sb2FkZXJfX2hpZGUoIHJlc291cmNlX2lkICk7XHJcblx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogRW5hYmxlIFN1Ym1pdCBidXR0b25cclxuXHRcdCAqIEBwYXJhbSByZXNvdXJjZV9pZFxyXG5cdFx0ICovXHJcblx0XHRmdW5jdGlvbiB3cGJjX2Jvb2tpbmdfZm9ybV9fc2VuZF9idXR0b25fX2VuYWJsZSggcmVzb3VyY2VfaWQgKXtcclxuXHJcblx0XHRcdC8vIEFjdGl2YXRlIFNlbmQgYnV0dG9uXHJcblx0XHRcdGpRdWVyeSggJyNib29raW5nX2Zvcm1fZGl2JyArIHJlc291cmNlX2lkICsgJyBpbnB1dFt0eXBlPWJ1dHRvbl0nICkucHJvcCggXCJkaXNhYmxlZFwiLCBmYWxzZSApO1xyXG5cdFx0XHRqUXVlcnkoICcjYm9va2luZ19mb3JtX2RpdicgKyByZXNvdXJjZV9pZCArICcgYnV0dG9uJyApLnByb3AoIFwiZGlzYWJsZWRcIiwgZmFsc2UgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIERpc2FibGUgU3VibWl0IGJ1dHRvbiAgYW5kIHNob3cgIHNwaW5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0gcmVzb3VyY2VfaWRcclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gd3BiY19ib29raW5nX2Zvcm1fX3NlbmRfYnV0dG9uX19kaXNhYmxlKCByZXNvdXJjZV9pZCApe1xyXG5cclxuXHRcdFx0Ly8gRGlzYWJsZSBTZW5kIGJ1dHRvblxyXG5cdFx0XHRqUXVlcnkoICcjYm9va2luZ19mb3JtX2RpdicgKyByZXNvdXJjZV9pZCArICcgaW5wdXRbdHlwZT1idXR0b25dJyApLnByb3AoIFwiZGlzYWJsZWRcIiwgdHJ1ZSApO1xyXG5cdFx0XHRqUXVlcnkoICcjYm9va2luZ19mb3JtX2RpdicgKyByZXNvdXJjZV9pZCArICcgYnV0dG9uJyApLnByb3AoIFwiZGlzYWJsZWRcIiwgdHJ1ZSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogU2hvdyBib29raW5nIGZvcm0gIFNwaW4gTG9hZGVyXHJcblx0XHQgKiBAcGFyYW0gcmVzb3VyY2VfaWRcclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gd3BiY19ib29raW5nX2Zvcm1fX3NwaW5fbG9hZGVyX19zaG93KCByZXNvdXJjZV9pZCApe1xyXG5cclxuXHRcdFx0Ly8gU2hvdyBTcGluIExvYWRlclxyXG5cdFx0XHRqUXVlcnkoICcjYm9va2luZ19mb3JtJyArIHJlc291cmNlX2lkICkuYWZ0ZXIoXHJcblx0XHRcdFx0JzxkaXYgaWQ9XCJ3cGJjX2Jvb2tpbmdfZm9ybV9zcGluX2xvYWRlcicgKyByZXNvdXJjZV9pZCArICdcIiBjbGFzcz1cIndwYmNfYm9va2luZ19mb3JtX3NwaW5fbG9hZGVyXCIgc3R5bGU9XCJwb3NpdGlvbjogcmVsYXRpdmU7XCI+PGRpdiBjbGFzcz1cIndwYmNfc3BpbnNfbG9hZGVyX3dyYXBwZXJcIj48ZGl2IGNsYXNzPVwid3BiY19zcGluc19sb2FkZXJfbWluaVwiPjwvZGl2PjwvZGl2PjwvZGl2PidcclxuXHRcdFx0KTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFJlbW92ZSAvIEhpZGUgYm9va2luZyBmb3JtICBTcGluIExvYWRlclxyXG5cdFx0ICogQHBhcmFtIHJlc291cmNlX2lkXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIHdwYmNfYm9va2luZ19mb3JtX19zcGluX2xvYWRlcl9faGlkZSggcmVzb3VyY2VfaWQgKXtcclxuXHJcblx0XHRcdC8vIFJlbW92ZSBTcGluIExvYWRlclxyXG5cdFx0XHRqUXVlcnkoICcjd3BiY19ib29raW5nX2Zvcm1fc3Bpbl9sb2FkZXInICsgcmVzb3VyY2VfaWQgKS5yZW1vdmUoKTtcclxuXHRcdH1cclxuXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBIaWRlIGJvb2tpbmcgZm9ybSB3dGggYW5pbWF0aW9uXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHJlc291cmNlX2lkXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIHdwYmNfYm9va2luZ19mb3JtX19hbmltYXRlZF9faGlkZSggcmVzb3VyY2VfaWQgKXtcclxuXHJcblx0XHRcdC8vIGpRdWVyeSggJyNib29raW5nX2Zvcm0nICsgcmVzb3VyY2VfaWQgKS5zbGlkZVVwKCAgMTAwMFxyXG5cdFx0XHQvLyBcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQsIGZ1bmN0aW9uICgpe1xyXG5cdFx0XHQvL1xyXG5cdFx0XHQvLyBcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gaWYgKCBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggJ2dhdGV3YXlfcGF5bWVudF9mb3JtcycgKyByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF0gKSAhPSBudWxsICl7XHJcblx0XHRcdC8vIFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBcdHdwYmNfZG9fc2Nyb2xsKCAnI3N1Ym1pdGluZycgKyByZXNvdXJjZV9pZCApO1xyXG5cdFx0XHQvLyBcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gfSBlbHNlXHJcblx0XHRcdC8vIFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRpZiAoIGpRdWVyeSggJyNib29raW5nX2Zvcm0nICsgcmVzb3VyY2VfaWQgKS5wYXJlbnQoKS5maW5kKCAnLnN1Ym1pdGluZ19jb250ZW50JyApLmxlbmd0aCA+IDAgKXtcclxuXHRcdFx0Ly8gXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly93cGJjX2RvX3Njcm9sbCggJyNib29raW5nX2Zvcm0nICsgcmVzb3VyY2VfaWQgKyAnICsgLnN1Ym1pdGluZ19jb250ZW50JyApO1xyXG5cdFx0XHQvL1xyXG5cdFx0XHQvLyBcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgdmFyIGhpZGVUaW1lb3V0ID0gc2V0VGltZW91dChmdW5jdGlvbiAoKSB7XHJcblx0XHRcdC8vIFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIHdwYmNfZG9fc2Nyb2xsKCBqUXVlcnkoICcjYm9va2luZ19mb3JtJyArIHJlc291cmNlX2lkICkucGFyZW50KCkuZmluZCggJy5zdWJtaXRpbmdfY29udGVudCcgKS5nZXQoIDAgKSApO1xyXG5cdFx0XHQvLyBcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSwgMTAwKTtcclxuXHRcdFx0Ly9cclxuXHRcdFx0Ly8gXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0Ly8gXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICB9XHJcblx0XHRcdC8vIFx0XHRcdFx0XHRcdFx0XHRcdFx0KTtcclxuXHJcblx0XHRcdGpRdWVyeSggJyNib29raW5nX2Zvcm0nICsgcmVzb3VyY2VfaWQgKS5oaWRlKCk7XHJcblxyXG5cdFx0XHQvLyB2YXIgaGlkZVRpbWVvdXQgPSBzZXRUaW1lb3V0KCBmdW5jdGlvbiAoKXtcclxuXHRcdFx0Ly9cclxuXHRcdFx0Ly8gXHRpZiAoIGpRdWVyeSggJyNib29raW5nX2Zvcm0nICsgcmVzb3VyY2VfaWQgKS5wYXJlbnQoKS5maW5kKCAnLnN1Ym1pdGluZ19jb250ZW50JyApLmxlbmd0aCA+IDAgKXtcclxuXHRcdFx0Ly8gXHRcdHZhciByYW5kb21faWQgPSBNYXRoLmZsb29yKCAoTWF0aC5yYW5kb20oKSAqIDEwMDAwKSArIDEgKTtcclxuXHRcdFx0Ly8gXHRcdGpRdWVyeSggJyNib29raW5nX2Zvcm0nICsgcmVzb3VyY2VfaWQgKS5wYXJlbnQoKS5iZWZvcmUoICc8ZGl2IGlkPVwic2Nyb2xsX3RvJyArIHJhbmRvbV9pZCArICdcIj48L2Rpdj4nICk7XHJcblx0XHRcdC8vIFx0XHRjb25zb2xlLmxvZyggalF1ZXJ5KCAnI3Njcm9sbF90bycgKyByYW5kb21faWQgKSApO1xyXG5cdFx0XHQvL1xyXG5cdFx0XHQvLyBcdFx0d3BiY19kb19zY3JvbGwoICcjc2Nyb2xsX3RvJyArIHJhbmRvbV9pZCApO1xyXG5cdFx0XHQvLyBcdFx0Ly93cGJjX2RvX3Njcm9sbCggalF1ZXJ5KCAnI2Jvb2tpbmdfZm9ybScgKyByZXNvdXJjZV9pZCApLnBhcmVudCgpLmdldCggMCApICk7XHJcblx0XHRcdC8vIFx0fVxyXG5cdFx0XHQvLyB9LCA1MDAgKTtcclxuXHRcdH1cclxuXHQvLyA8L2VkaXRvci1mb2xkPlxyXG5cclxuXHJcbi8vVE9ETzogd2hhdCAgYWJvdXQgc2hvd2luZyBvbmx5ICBUaGFuayB5b3UuIG1lc3NhZ2Ugd2l0aG91dCBwYXltZW50IGZvcm1zLlxyXG4vKipcclxuICogU2hvdyAnVGhhbmsgeW91Jy4gbWVzc2FnZSBhbmQgcGF5bWVudCBmb3Jtc1xyXG4gKlxyXG4gKiBAcGFyYW0gcmVzcG9uc2VfZGF0YVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19zaG93X3RoYW5rX3lvdV9tZXNzYWdlX2FmdGVyX2Jvb2tpbmcoIHJlc3BvbnNlX2RhdGEgKXtcclxuXHJcblx0aWYgKFxyXG4gXHRcdCAgICgndW5kZWZpbmVkJyAhPT0gdHlwZW9mIChyZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X2lzX3JlZGlyZWN0JyBdKSlcclxuXHRcdCYmICgndW5kZWZpbmVkJyAhPT0gdHlwZW9mIChyZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X3VybCcgXSkpXHJcblx0XHQmJiAoJ3BhZ2UnID09IHJlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAndHlfaXNfcmVkaXJlY3QnIF0pXHJcblx0XHQmJiAoJycgIT0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9jb25maXJtYXRpb24nIF1bICd0eV91cmwnIF0pXHJcblx0KXtcclxuXHRcdHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9jb25maXJtYXRpb24nIF1bICd0eV91cmwnIF07XHJcblx0XHRyZXR1cm47XHJcblx0fVxyXG5cclxuXHR2YXIgcmVzb3VyY2VfaWQgPSByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF1cclxuXHR2YXIgY29uZmlybV9jb250ZW50ID0nJztcclxuXHJcblx0aWYgKCAndW5kZWZpbmVkJyA9PT0gdHlwZW9mIChyZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X21lc3NhZ2UnIF0pICl7XHJcblx0XHRcdFx0XHQgIFx0XHRcdCByZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X21lc3NhZ2UnIF0gPSAnJztcclxuXHR9XHJcblx0aWYgKCAndW5kZWZpbmVkJyA9PT0gdHlwZW9mIChyZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X3BheW1lbnRfcGF5bWVudF9kZXNjcmlwdGlvbicgXSApICl7XHJcblx0XHQgXHRcdFx0ICBcdFx0XHQgcmVzcG9uc2VfZGF0YVsgJ2FqeF9jb25maXJtYXRpb24nIF1bICd0eV9wYXltZW50X3BheW1lbnRfZGVzY3JpcHRpb24nIF0gPSAnJztcclxuXHR9XHJcblx0aWYgKCAndW5kZWZpbmVkJyA9PT0gdHlwZW9mIChyZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3BheW1lbnRfY29zdCcgXSApICl7XHJcblx0XHRcdFx0XHQgIFx0XHRcdCByZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3BheW1lbnRfY29zdCcgXSA9ICcnO1xyXG5cdH1cclxuXHRpZiAoICd1bmRlZmluZWQnID09PSB0eXBlb2YgKHJlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAndHlfcGF5bWVudF9nYXRld2F5cycgXSApICl7XHJcblx0XHRcdFx0XHQgIFx0XHRcdCByZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X3BheW1lbnRfZ2F0ZXdheXMnIF0gPSAnJztcclxuXHR9XHJcblx0dmFyIHR5X21lc3NhZ2VfaGlkZSBcdFx0XHRcdFx0XHQ9ICgnJyA9PSByZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X21lc3NhZ2UnIF0pID8gJ3dwYmNfdHlfaGlkZScgOiAnJztcclxuXHR2YXIgdHlfcGF5bWVudF9wYXltZW50X2Rlc2NyaXB0aW9uX2hpZGUgXHQ9ICgnJyA9PSByZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X3BheW1lbnRfcGF5bWVudF9kZXNjcmlwdGlvbicgXS5yZXBsYWNlKCAvXFxcXG4vZywgJycgKSkgPyAnd3BiY190eV9oaWRlJyA6ICcnO1xyXG5cdHZhciB0eV9ib29raW5nX2Nvc3RzX2hpZGUgXHRcdFx0XHQ9ICgnJyA9PSByZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3BheW1lbnRfY29zdCcgXSkgPyAnd3BiY190eV9oaWRlJyA6ICcnO1xyXG5cdHZhciB0eV9wYXltZW50X2dhdGV3YXlzX2hpZGUgXHRcdFx0PSAoJycgPT0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9jb25maXJtYXRpb24nIF1bICd0eV9wYXltZW50X2dhdGV3YXlzJyBdLnJlcGxhY2UoIC9cXFxcbi9nLCAnJyApKSA/ICd3cGJjX3R5X2hpZGUnIDogJyc7XHJcblxyXG5cdGlmICggJ3dwYmNfdHlfaGlkZScgIT0gdHlfcGF5bWVudF9nYXRld2F5c19oaWRlICl7XHJcblx0XHRqUXVlcnkoICcud3BiY190eV9fY29udGVudF90ZXh0LndwYmNfdHlfX2NvbnRlbnRfZ2F0ZXdheXMnICkuaHRtbCggJycgKTtcdC8vIFJlc2V0ICBhbGwgIG90aGVyIHBvc3NpYmxlIGdhdGV3YXlzIGJlZm9yZSBzaG93aW5nIG5ldyBvbmUuXHJcblx0fVxyXG5cclxuXHRjb25maXJtX2NvbnRlbnQgKz0gYDxkaXYgaWQ9XCJ3cGJjX3Njcm9sbF9wb2ludF8ke3Jlc291cmNlX2lkfVwiPjwvZGl2PmA7XHJcblx0Y29uZmlybV9jb250ZW50ICs9IGAgIDxkaXYgY2xhc3M9XCJ3cGJjX2FmdGVyX2Jvb2tpbmdfdGhhbmtfeW91X3NlY3Rpb25cIj5gO1xyXG5cdGNvbmZpcm1fY29udGVudCArPSBgICAgIDxkaXYgY2xhc3M9XCJ3cGJjX3R5X19tZXNzYWdlICR7dHlfbWVzc2FnZV9oaWRlfVwiPiR7cmVzcG9uc2VfZGF0YVsgJ2FqeF9jb25maXJtYXRpb24nIF1bICd0eV9tZXNzYWdlJyBdfTwvZGl2PmA7XHJcbiAgICBjb25maXJtX2NvbnRlbnQgKz0gYCAgICA8ZGl2IGNsYXNzPVwid3BiY190eV9fY29udGFpbmVyXCI+YDtcclxuICAgIGNvbmZpcm1fY29udGVudCArPSBgICAgICAgPGRpdiBjbGFzcz1cIndwYmNfdHlfX2hlYWRlclwiPiR7cmVzcG9uc2VfZGF0YVsnYWp4X2NvbmZpcm1hdGlvbiddWyd0eV9tZXNzYWdlX2Jvb2tpbmdfaWQnXX08L2Rpdj5gO1xyXG4gICAgY29uZmlybV9jb250ZW50ICs9IGAgICAgICA8ZGl2IGNsYXNzPVwid3BiY190eV9fY29udGVudFwiPmA7XHJcblx0Y29uZmlybV9jb250ZW50ICs9IGAgICAgICAgIDxkaXYgY2xhc3M9XCJ3cGJjX3R5X19jb250ZW50X3RleHQgd3BiY190eV9fcGF5bWVudF9kZXNjcmlwdGlvbiAke3R5X3BheW1lbnRfcGF5bWVudF9kZXNjcmlwdGlvbl9oaWRlfVwiPiR7cmVzcG9uc2VfZGF0YVsgJ2FqeF9jb25maXJtYXRpb24nIF1bICd0eV9wYXltZW50X3BheW1lbnRfZGVzY3JpcHRpb24nIF0ucmVwbGFjZSggL1xcXFxuL2csICcnICl9PC9kaXY+YDtcclxuICAgIGNvbmZpcm1fY29udGVudCArPSBgICAgICAgXHQ8ZGl2IGNsYXNzPVwid3BiY190eV9fY29udGVudF90ZXh0IHdwYmNfY29sc18yXCI+JHtyZXNwb25zZV9kYXRhWydhanhfY29uZmlybWF0aW9uJ11bJ3R5X2N1c3RvbWVyX2RldGFpbHMnXX08L2Rpdj5gO1xyXG4gICAgY29uZmlybV9jb250ZW50ICs9IGAgICAgICBcdDxkaXYgY2xhc3M9XCJ3cGJjX3R5X19jb250ZW50X3RleHQgd3BiY19jb2xzXzJcIj4ke3Jlc3BvbnNlX2RhdGFbJ2FqeF9jb25maXJtYXRpb24nXVsndHlfYm9va2luZ19kZXRhaWxzJ119PC9kaXY+YDtcclxuXHRjb25maXJtX2NvbnRlbnQgKz0gYCAgICAgICAgPGRpdiBjbGFzcz1cIndwYmNfdHlfX2NvbnRlbnRfdGV4dCB3cGJjX3R5X19jb250ZW50X2Nvc3RzICR7dHlfYm9va2luZ19jb3N0c19oaWRlfVwiPiR7cmVzcG9uc2VfZGF0YVsgJ2FqeF9jb25maXJtYXRpb24nIF1bICd0eV9ib29raW5nX2Nvc3RzJyBdfTwvZGl2PmA7XHJcblx0Y29uZmlybV9jb250ZW50ICs9IGAgICAgICAgIDxkaXYgY2xhc3M9XCJ3cGJjX3R5X19jb250ZW50X3RleHQgd3BiY190eV9fY29udGVudF9nYXRld2F5cyAke3R5X3BheW1lbnRfZ2F0ZXdheXNfaGlkZX1cIj4ke3Jlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAndHlfcGF5bWVudF9nYXRld2F5cycgXS5yZXBsYWNlKCAvXFxcXG4vZywgJycgKS5yZXBsYWNlKCAvYWpheF9zY3JpcHQvZ2ksICdzY3JpcHQnICl9PC9kaXY+YDtcclxuICAgIGNvbmZpcm1fY29udGVudCArPSBgICAgICAgPC9kaXY+YDtcclxuICAgIGNvbmZpcm1fY29udGVudCArPSBgICAgIDwvZGl2PmA7XHJcblx0Y29uZmlybV9jb250ZW50ICs9IGA8L2Rpdj5gO1xyXG5cclxuIFx0alF1ZXJ5KCAnI2Jvb2tpbmdfZm9ybScgKyByZXNvdXJjZV9pZCApLmFmdGVyKCBjb25maXJtX2NvbnRlbnQgKTtcclxufVxyXG4iXSwiZmlsZSI6ImluY2x1ZGVzL19jYXBhY2l0eS9fb3V0L2NyZWF0ZV9ib29raW5nLmpzIn0=
