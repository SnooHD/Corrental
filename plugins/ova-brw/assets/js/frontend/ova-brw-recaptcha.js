"use strict";

// reCAPTCHA functions
var ovabrwLoadingReCAPTCHAv2 = function() {
    if ( document.getElementById('ovabrw-g-recaptcha-booking') ) {
        grecaptcha.render(document.getElementById('ovabrw-g-recaptcha-booking'), {
            'sitekey' : ovabrw_recaptcha.site_key,
            'callback': ovabrwBookingFormVerify,
            'expired-callback': ovabrwBookingFormExpired,
            'error-callback': ovabrwBookingFormError
        });
    }

    if ( document.getElementById('ovabrw-g-recaptcha-request') ) {
        grecaptcha.render(document.getElementById('ovabrw-g-recaptcha-request'), {
            'sitekey' : ovabrw_recaptcha.site_key,
            'callback': ovabrwRequestFormVerify,
            'expired-callback': ovabrwRequestFormExpired,
            'error-callback': ovabrwRequestFormError
        });
    }
};

var ovabrwLoadingReCAPTCHAv3 = function() {
    if ( document.getElementById('ovabrw-g-recaptcha-booking') ) {
        grecaptcha.ready(function() {
            grecaptcha.execute(ovabrw_recaptcha.site_key, {action: 'submit'}).then(function(token) {
                var tokenInput = document.getElementById("ovabrw-recaptcha-booking-token");

                if ( tokenInput && token ) {
                    tokenInput.setAttribute('value', token);
                    tokenInput.setAttribute('data-mess', '');
                }
            });
        });
    }

    if ( document.getElementById('ovabrw-g-recaptcha-request') ) {
        grecaptcha.ready(function() {
            grecaptcha.execute(ovabrw_recaptcha.site_key, {action: 'submit'}).then(function(token) {
                var tokenInput = document.getElementById("ovabrw-recaptcha-request-token");

                if ( tokenInput && token ) {
                    tokenInput.setAttribute('value', token);
                    tokenInput.setAttribute('data-mess', '');
                }
            });
        });
    }
};

// Booking Form Verify
var ovabrwBookingFormVerify = function( response ) {
    var tokenInput = document.getElementById("ovabrw-recaptcha-booking-token");

    if ( tokenInput && response ) {
        tokenInput.setAttribute('value', response);
        tokenInput.setAttribute('data-mess', '');
    }
}

var ovabrwBookingFormExpired = function() {
    var tokenInput = document.getElementById("ovabrw-recaptcha-booking-token");

    if ( tokenInput ) {
        var expiresError = tokenInput.getAttribute('data-expired');

        tokenInput.setAttribute('value', '');
        tokenInput.setAttribute('data-mess', expiresError);
    }
}

var ovabrwBookingFormError = function() {
    var tokenInput = document.getElementById("ovabrw-recaptcha-booking-token");

    if ( tokenInput ) {
        var error = tokenInput.getAttribute('data-error');

        tokenInput.setAttribute('value', '');
        tokenInput.setAttribute('data-mess', error);
    }
}

// Request Form Verify
var ovabrwRequestFormVerify = function( response ) {
    var tokenInput = document.getElementById("ovabrw-recaptcha-request-token");

    if ( tokenInput && response ) {
        tokenInput.setAttribute('value', response);
        tokenInput.setAttribute('data-mess', '');
    }
}

var ovabrwRequestFormExpired = function() {
    var tokenInput = document.getElementById("ovabrw-recaptcha-request-token");

    if ( tokenInput ) {
        var expiresError = tokenInput.getAttribute('data-expired');

        tokenInput.setAttribute('value', '');
        tokenInput.setAttribute('data-mess', expiresError);
    }
}

var ovabrwRequestFormError = function() {
    var tokenInput = document.getElementById("ovabrw-recaptcha-request-token");

    if ( tokenInput ) {
        var error = tokenInput.getAttribute('data-error');

        tokenInput.setAttribute('value', '');
        tokenInput.setAttribute('data-mess', error);
    }
}