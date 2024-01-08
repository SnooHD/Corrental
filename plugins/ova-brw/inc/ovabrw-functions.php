<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// var_dump
if ( ! function_exists( 'dd' ) ) {
    function dd( ...$args ) {
        echo '<pre>';
        var_dump( ...$args );
        echo '</pre>';
        die;
    }
}

// Return value of setting
if ( ! function_exists( 'ovabrw_get_setting' ) ) {
	function ovabrw_get_setting( $setting ) {
		if ( trim( $setting ) == '' ) return '';

		return $setting;
	}
}

// Get Date Format in Events Setting
if ( ! function_exists( 'ovabrw_get_date_format' ) ) {
	function ovabrw_get_date_format() {
		return apply_filters( 'ovabrw_get_date_format_hook', ovabrw_get_setting( get_option( 'ova_brw_booking_form_date_format', 'd-m-Y' ) ) );
	}
}

if ( ! function_exists( 'ovabrw_get_placeholder_date' ) ) {
	function ovabrw_get_placeholder_date() {
		$placeholder = '';
		$dateformat = ovabrw_get_date_format();

		if ( 'Y-m-d' === $dateformat ) {
			$placeholder = esc_html__( 'Y-m-d', 'ova-brw' );
		} elseif ( 'm/d/Y' === $dateformat ) {
			$placeholder = esc_html__( 'm/d/Y', 'ova-brw' );
		} elseif ( 'Y/m/d' === $dateformat ) {
			$placeholder = esc_html__( 'Y/m/d', 'ova-brw' );
		} else {
			$placeholder = esc_html__( 'd-m-Y', 'ova-brw' );
		}

		return $placeholder;
	}
}

// Get Time Format in Events Setting
if ( ! function_exists( 'ovabrw_get_time_format' ) ) {
	function ovabrw_get_time_format() {
		return apply_filters( 'ova_brw_calendar_time_format_hook', ovabrw_get_setting( get_option( 'ova_brw_calendar_time_format', '12' ) ) );
	}
}

if ( ! function_exists( 'ovabrw_get_placeholder_time' ) ) {
	function ovabrw_get_placeholder_time() {
		$placeholder = 'H:i';
		$time_format = ovabrw_get_time_format();

		if ( $time_format == '12' ) {
			$placeholder = esc_html__( 'g:i A', 'ova-brw' );
		} else {
			$placeholder = esc_html__( 'H:i', 'ova-brw' );
		}
		
		return $placeholder;
	}
}

// ISO 3166-1 alpha-2 codes
if ( ! function_exists( 'ovabrw_iso_alpha2' ) ) {
	function ovabrw_iso_alpha2() {
		$countries = [
		    'AD' => esc_html__('Andorra', 'ova-brw'),
		    'AE' => esc_html__('United Arab Emirates', 'ova-brw'),
		    'AF' => esc_html__('Afghanistan', 'ova-brw'),
		    'AG' => esc_html__('Antigua and Barbuda', 'ova-brw'),
		    'AI' => esc_html__('Anguilla', 'ova-brw'),
		    'AL' => esc_html__('Albania', 'ova-brw'),
		    'AM' => esc_html__('Armenia', 'ova-brw'),
		    'AO' => esc_html__('Angola', 'ova-brw'),
		    'AQ' => esc_html__('Antarctica', 'ova-brw'),
		    'AR' => esc_html__('Argentina', 'ova-brw'),
		    'AS' => esc_html__('American Samoa', 'ova-brw'),
		    'AT' => esc_html__('Austria', 'ova-brw'),
		    'AU' => esc_html__('Australia', 'ova-brw'),
		    'AW' => esc_html__('Aruba', 'ova-brw'),
		    'AX' => esc_html__('Åland Islands', 'ova-brw'),
		    'AZ' => esc_html__('Azerbaijan', 'ova-brw'),
		    'BA' => esc_html__('Bosnia and Herzegovina', 'ova-brw'),
		    'BB' => esc_html__('Barbados', 'ova-brw'),
		    'BD' => esc_html__('Bangladesh', 'ova-brw'),
		    'BE' => esc_html__('Belgium', 'ova-brw'),
		    'BF' => esc_html__('Burkina Faso', 'ova-brw'),
		    'BG' => esc_html__('Bulgaria', 'ova-brw'),
		    'BH' => esc_html__('Bahrain', 'ova-brw'),
		    'BI' => esc_html__('Burundi', 'ova-brw'),
		    'BJ' => esc_html__('Benin', 'ova-brw'),
		    'BL' => esc_html__('Saint Barthélemy', 'ova-brw'),
		    'BM' => esc_html__('Bermuda', 'ova-brw'),
		    'BN' => esc_html__('Brunei Darussalam', 'ova-brw'),
		    'BO' => esc_html__('Bolivia (Plurinational State of)', 'ova-brw'),
		    'BQ' => esc_html__('Bonaire, Sint Eustatius and Saba', 'ova-brw'),
		    'BR' => esc_html__('Brazil', 'ova-brw'),
		    'BS' => esc_html__('Bahamas', 'ova-brw'),
		    'BT' => esc_html__('Bhutan', 'ova-brw'),
		    'BV' => esc_html__('Bouvet Island', 'ova-brw'),
		    'BW' => esc_html__('Botswana', 'ova-brw'),
		    'BY' => esc_html__('Belarus', 'ova-brw'),
		    'BZ' => esc_html__('Belize', 'ova-brw'),
		    'CA' => esc_html__('Canada', 'ova-brw'),
		    'CC' => esc_html__('Cocos (Keeling) Islands', 'ova-brw'),
		    'CD' => esc_html__('Congo, Democratic Republic of the', 'ova-brw'),
		    'CF' => esc_html__('Central African Republic', 'ova-brw'),
		    'CG' => esc_html__('Congo', 'ova-brw'),
		    'CH' => esc_html__('Switzerland', 'ova-brw'),
		    'CI' => esc_html__('Côte d\'Ivoire', 'ova-brw'),
		    'CK' => esc_html__('Cook Islands', 'ova-brw'),
		    'CL' => esc_html__('Chile', 'ova-brw'),
		    'CM' => esc_html__('Cameroon', 'ova-brw'),
		    'CN' => esc_html__('China', 'ova-brw'),
		    'CO' => esc_html__('Colombia', 'ova-brw'),
		    'CR' => esc_html__('Costa Rica', 'ova-brw'),
		    'CU' => esc_html__('Cuba', 'ova-brw'),
		    'CV' => esc_html__('Cabo Verde', 'ova-brw'),
		    'CW' => esc_html__('Curaçao', 'ova-brw'),
		    'CX' => esc_html__('Christmas Island', 'ova-brw'),
		    'CY' => esc_html__('Cyprus', 'ova-brw'),
		    'CZ' => esc_html__('Czechia', 'ova-brw'),
		    'DE' => esc_html__('Germany', 'ova-brw'),
		    'DJ' => esc_html__('Djibouti', 'ova-brw'),
		    'DK' => esc_html__('Denmark', 'ova-brw'),
		    'DM' => esc_html__('Dominica', 'ova-brw'),
		    'DO' => esc_html__('Dominican Republic', 'ova-brw'),
		    'DZ' => esc_html__('Algeria', 'ova-brw'),
		    'EC' => esc_html__('Ecuador', 'ova-brw'),
		    'EE' => esc_html__('Estonia', 'ova-brw'),
		    'EG' => esc_html__('Egypt', 'ova-brw'),
		    'EH' => esc_html__('Western Sahara', 'ova-brw'),
		    'ER' => esc_html__('Eritrea', 'ova-brw'),
		    'ES' => esc_html__('Spain', 'ova-brw'),
		    'ET' => esc_html__('Ethiopia', 'ova-brw'),
		    'FI' => esc_html__('Finland', 'ova-brw'),
		    'FJ' => esc_html__('Fiji', 'ova-brw'),
		    'FK' => esc_html__('Falkland Islands (Malvinas)', 'ova-brw'),
		    'FM' => esc_html__('Micronesia (Federated States of)', 'ova-brw'),
		    'FO' => esc_html__('Faroe Islands', 'ova-brw'),
		    'FR' => esc_html__('France', 'ova-brw'),
		    'GA' => esc_html__('Gabon', 'ova-brw'),
		    'GB' => esc_html__('United Kingdom of Great Britain and Northern Ireland', 'ova-brw'),
		    'GD' => esc_html__('Grenada', 'ova-brw'),
		    'GE' => esc_html__('Georgia', 'ova-brw'),
		    'GF' => esc_html__('French Guiana', 'ova-brw'),
		    'GG' => esc_html__('Guernsey', 'ova-brw'),
		    'GH' => esc_html__('Ghana', 'ova-brw'),
		    'GI' => esc_html__('Gibraltar', 'ova-brw'),
		    'GL' => esc_html__('Greenland', 'ova-brw'),
		    'GM' => esc_html__('Gambia', 'ova-brw'),
		    'GN' => esc_html__('Guinea', 'ova-brw'),
		    'GP' => esc_html__('Guadeloupe', 'ova-brw'),
		    'GQ' => esc_html__('Equatorial Guinea', 'ova-brw'),
		    'GR' => esc_html__('Greece', 'ova-brw'),
		    'GS' => esc_html__('South Georgia and the South Sandwich Islands', 'ova-brw'),
		    'GT' => esc_html__('Guatemala', 'ova-brw'),
		    'GU' => esc_html__('Guam', 'ova-brw'),
		    'GW' => esc_html__('Guinea-Bissau', 'ova-brw'),
		    'GY' => esc_html__('Guyana', 'ova-brw'),
		    'HK' => esc_html__('Hong Kong', 'ova-brw'),
		    'HM' => esc_html__('Heard Island and McDonald Islands', 'ova-brw'),
		    'HN' => esc_html__('Honduras', 'ova-brw'),
		    'HR' => esc_html__('Croatia', 'ova-brw'),
		    'HT' => esc_html__('Haiti', 'ova-brw'),
		    'HU' => esc_html__('Hungary', 'ova-brw'),
		    'ID' => esc_html__('Indonesia', 'ova-brw'),
		    'IE' => esc_html__('Ireland', 'ova-brw'),
		    'IL' => esc_html__('Israel', 'ova-brw'),
		    'IM' => esc_html__('Isle of Man', 'ova-brw'),
		    'IN' => esc_html__('India', 'ova-brw'),
		    'IO' => esc_html__('British Indian Ocean Territory', 'ova-brw'),
		    'IQ' => esc_html__('Iraq', 'ova-brw'),
		    'IR' => esc_html__('Iran (Islamic Republic of)', 'ova-brw'),
		    'IS' => esc_html__('Iceland', 'ova-brw'),
		    'IT' => esc_html__('Italy', 'ova-brw'),
		    'JE' => esc_html__('Jersey', 'ova-brw'),
		    'JM' => esc_html__('Jamaica', 'ova-brw'),
		    'JO' => esc_html__('Jordan', 'ova-brw'),
		    'JP' => esc_html__('Japan', 'ova-brw'),
		    'KE' => esc_html__('Kenya', 'ova-brw'),
		    'KG' => esc_html__('Kyrgyzstan', 'ova-brw'),
		    'KH' => esc_html__('Cambodia', 'ova-brw'),
		    'KI' => esc_html__('Kiribati', 'ova-brw'),
		    'KM' => esc_html__('Comoros', 'ova-brw'),
		    'KN' => esc_html__('Saint Kitts and Nevis', 'ova-brw'),
		    'KP' => esc_html__('Korea (Democratic People\'s Republic of)', 'ova-brw'),
		    'KR' => esc_html__('Korea, Republic of', 'ova-brw'),
		    'KW' => esc_html__('Kuwait', 'ova-brw'),
		    'KY' => esc_html__('Cayman Islands', 'ova-brw'),
		    'KZ' => esc_html__('Kazakhstan', 'ova-brw'),
		    'LA' => esc_html__('Lao People\'s Democratic Republic', 'ova-brw'),
		    'LB' => esc_html__('Lebanon', 'ova-brw'),
		    'LC' => esc_html__('Saint Lucia', 'ova-brw'),
		    'LI' => esc_html__('Liechtenstein', 'ova-brw'),
		    'LK' => esc_html__('Sri Lanka', 'ova-brw'),
		    'LR' => esc_html__('Liberia', 'ova-brw'),
		    'LS' => esc_html__('Lesotho', 'ova-brw'),
		    'LT' => esc_html__('Lithuania', 'ova-brw'),
		    'LU' => esc_html__('Luxembourg', 'ova-brw'),
		    'LV' => esc_html__('Latvia', 'ova-brw'),
		    'LY' => esc_html__('Libya', 'ova-brw'),
		    'MA' => esc_html__('Morocco', 'ova-brw'),
		    'MC' => esc_html__('Monaco', 'ova-brw'),
		    'MD' => esc_html__('Moldova, Republic of', 'ova-brw'),
		    'ME' => esc_html__('Montenegro', 'ova-brw'),
		    'MF' => esc_html__('Saint Martin (French part)', 'ova-brw'),
		    'MG' => esc_html__('Madagascar', 'ova-brw'),
		    'MH' => esc_html__('Marshall Islands', 'ova-brw'),
		    'MK' => esc_html__('North Macedonia', 'ova-brw'),
		    'ML' => esc_html__('Mali', 'ova-brw'),
		    'MM' => esc_html__('Myanmar', 'ova-brw'),
		    'MN' => esc_html__('Mongolia', 'ova-brw'),
		    'MO' => esc_html__('Macao', 'ova-brw'),
		    'MP' => esc_html__('Northern Mariana Islands', 'ova-brw'),
		    'MQ' => esc_html__('Martinique', 'ova-brw'),
		    'MR' => esc_html__('Mauritania', 'ova-brw'),
		    'MS' => esc_html__('Montserrat', 'ova-brw'),
		    'MT' => esc_html__('Malta', 'ova-brw'),
		    'MU' => esc_html__('Mauritius', 'ova-brw'),
		    'MV' => esc_html__('Maldives', 'ova-brw'),
		    'MW' => esc_html__('Malawi', 'ova-brw'),
		    'MX' => esc_html__('Mexico', 'ova-brw'),
		    'MY' => esc_html__('Malaysia', 'ova-brw'),
		    'MZ' => esc_html__('Mozambique', 'ova-brw'),
		    'NA' => esc_html__('Namibia', 'ova-brw'),
		    'NC' => esc_html__('New Caledonia', 'ova-brw'),
		    'NE' => esc_html__('Niger', 'ova-brw'),
		    'NF' => esc_html__('Norfolk Island', 'ova-brw'),
		    'NG' => esc_html__('Nigeria', 'ova-brw'),
		    'NI' => esc_html__('Nicaragua', 'ova-brw'),
		    'NL' => esc_html__('Netherlands, Kingdom of the', 'ova-brw'),
		    'NO' => esc_html__('Norway', 'ova-brw'),
		    'NP' => esc_html__('Nepal', 'ova-brw'),
		    'NR' => esc_html__('Nauru', 'ova-brw'),
		    'NU' => esc_html__('Niue', 'ova-brw'),
		    'NZ' => esc_html__('New Zealand', 'ova-brw'),
		    'OM' => esc_html__('Oman', 'ova-brw'),
		    'PA' => esc_html__('Panama', 'ova-brw'),
		    'PE' => esc_html__('Peru', 'ova-brw'),
		    'PF' => esc_html__('French Polynesia', 'ova-brw'),
		    'PG' => esc_html__('Papua New Guinea', 'ova-brw'),
		    'PH' => esc_html__('Philippines', 'ova-brw'),
		    'PK' => esc_html__('Pakistan', 'ova-brw'),
		    'PL' => esc_html__('Poland', 'ova-brw'),
		    'PM' => esc_html__('Saint Pierre and Miquelon', 'ova-brw'),
		    'PN' => esc_html__('Pitcairn', 'ova-brw'),
		    'PR' => esc_html__('Puerto Rico', 'ova-brw'),
		    'PS' => esc_html__('Palestine, State of', 'ova-brw'),
		    'PT' => esc_html__('Portugal', 'ova-brw'),
		    'PW' => esc_html__('Palau', 'ova-brw'),
		    'PY' => esc_html__('Paraguay', 'ova-brw'),
		    'QA' => esc_html__('Qatar', 'ova-brw'),
		    'RE' => esc_html__('Réunion', 'ova-brw'),
		    'RO' => esc_html__('Romania', 'ova-brw'),
		    'RS' => esc_html__('Serbia', 'ova-brw'),
		    'RU' => esc_html__('Russian Federation', 'ova-brw'),
		    'RW' => esc_html__('Rwanda', 'ova-brw'),
		    'SA' => esc_html__('Saudi Arabia', 'ova-brw'),
		    'SB' => esc_html__('Solomon Islands', 'ova-brw'),
		    'SC' => esc_html__('Seychelles', 'ova-brw'),
		    'SD' => esc_html__('Sudan', 'ova-brw'),
		    'SE' => esc_html__('Sweden', 'ova-brw'),
		    'SG' => esc_html__('Singapore', 'ova-brw'),
		    'SH' => esc_html__('Saint Helena, Ascension and Tristan da Cunha', 'ova-brw'),
		    'SI' => esc_html__('Slovenia', 'ova-brw'),
		    'SJ' => esc_html__('Svalbard and Jan Mayen', 'ova-brw'),
		    'SK' => esc_html__('Slovakia', 'ova-brw'),
		    'SL' => esc_html__('Sierra Leone', 'ova-brw'),
		    'SM' => esc_html__('San Marino', 'ova-brw'),
		    'SN' => esc_html__('Senegal', 'ova-brw'),
		    'SO' => esc_html__('Somalia', 'ova-brw'),
		    'SR' => esc_html__('Suriname', 'ova-brw'),
		    'SS' => esc_html__('South Sudan', 'ova-brw'),
		    'ST' => esc_html__('Sao Tome and Principe', 'ova-brw'),
		    'SV' => esc_html__('El Salvador', 'ova-brw'),
		    'SX' => esc_html__('Sint Maarten (Dutch part)', 'ova-brw'),
		    'SY' => esc_html__('Syrian Arab Republic', 'ova-brw'),
		    'SZ' => esc_html__('Eswatini', 'ova-brw'),
		    'TC' => esc_html__('Turks and Caicos Islands', 'ova-brw'),
		    'TD' => esc_html__('Chad', 'ova-brw'),
		    'TF' => esc_html__('French Southern Territories', 'ova-brw'),
		    'TG' => esc_html__('Togo', 'ova-brw'),
		    'TH' => esc_html__('Thailand', 'ova-brw'),
		    'TJ' => esc_html__('Tajikistan', 'ova-brw'),
		    'TK' => esc_html__('Tokelau', 'ova-brw'),
		    'TL' => esc_html__('Timor-Leste', 'ova-brw'),
		    'TM' => esc_html__('Turkmenistan', 'ova-brw'),
		    'TN' => esc_html__('Tunisia', 'ova-brw'),
		    'TO' => esc_html__('Tonga', 'ova-brw'),
		    'TR' => esc_html__('Türkiye', 'ova-brw'),
		    'TT' => esc_html__('Trinidad and Tobago', 'ova-brw'),
		    'TV' => esc_html__('Tuvalu', 'ova-brw'),
		    'TW' => esc_html__('Taiwan, Province of China', 'ova-brw'),
		    'TZ' => esc_html__('Tanzania, United Republic of', 'ova-brw'),
		    'UA' => esc_html__('Ukraine', 'ova-brw'),
		    'UG' => esc_html__('Uganda', 'ova-brw'),
		    'UM' => esc_html__('United States Minor Outlying Islands', 'ova-brw'),
		    'US' => esc_html__('United States of America', 'ova-brw'),
		    'UY' => esc_html__('Uruguay', 'ova-brw'),
		    'UZ' => esc_html__('Uzbekistan', 'ova-brw'),
		    'VA' => esc_html__('Holy See', 'ova-brw'),
		    'VC' => esc_html__('Saint Vincent and the Grenadines', 'ova-brw'),
		    'VE' => esc_html__('Venezuela (Bolivarian Republic of)', 'ova-brw'),
		    'VG' => esc_html__('Virgin Islands (British)', 'ova-brw'),
		    'VI' => esc_html__('Virgin Islands (U.S.)', 'ova-brw'),
		    'VN' => esc_html__('Viet Nam', 'ova-brw'),
		    'VU' => esc_html__('Vanuatu', 'ova-brw'),
		    'WF' => esc_html__('Wallis and Futuna', 'ova-brw'),
		    'WS' => esc_html__('Samoa', 'ova-brw'),
		    'YE' => esc_html__('Yemen', 'ova-brw'),
		    'YT' => esc_html__('Mayotte', 'ova-brw'),
		    'ZA' => esc_html__('South Africa', 'ova-brw'),
		    'ZM' => esc_html__('Zambia', 'ova-brw'),
		    'ZW' => esc_html__('Zimbabwe', 'ova-brw'),
		];

		return $countries;
	}
}

// Get Time book for pick-up date
if ( ! function_exists( 'ovabrw_group_time_pickup_date_global_setting' ) ) {
	function ovabrw_group_time_pickup_date_global_setting() {
		// Get from setting
		$group_time = ovabrw_get_setting( get_option( 'ova_brw_calendar_time_to_book', '07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00' ) );

		return apply_filters( 'ovabrw_group_time_pickup_date_global_setting', $group_time );
	}
}

// Get default hour for dropoff date
if ( ! function_exists( 'ovabrw_group_time_dropoff_date_global_setting' ) ) {
	function ovabrw_group_time_dropoff_date_global_setting() {
		// Get from setting
		$group_time = ovabrw_get_setting( get_option( 'ova_brw_calendar_time_to_book_for_end_date', '07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00' ) );

		return apply_filters( 'ovabrw_group_time_dropoff_date_global_setting', $group_time );
	}
}

// Get default hour for pick-up date
if ( ! function_exists( 'ovabrw_get_default_time' ) ) {
	function ovabrw_get_default_time( $id = false, $type = "start" ) {
		// Get from Setting
		if ( $type == 'start' ) {
			$time = ovabrw_get_setting( get_option( 'ova_brw_booking_form_default_hour', '07:00' ) );	
			// Get from Product
			$manage_default_hour_start = get_post_meta( $id, 'ovabrw_manage_default_hour_start', true );
			if ( $manage_default_hour_start == 'new_time' ) {
				$time = get_post_meta( $id, 'ovabrw_product_default_hour_start', true );
			}
		}else {
			// Get from Setting
			$time = ovabrw_get_setting( get_option( 'ova_brw_booking_form_default_hour_end_date', '07:00' ) );

			// Get from Product
			$manage_default_hour_start = get_post_meta( $id, 'ovabrw_manage_default_hour_end', true );
			if ( $manage_default_hour_start == 'new_time' ){
				$time = get_post_meta( $id, 'ovabrw_product_default_hour_end', true );
			}
		}
		
		$time = trim( sanitize_text_field( $time ) );
		$hour = (int)substr( $time, 0, strpos( $time, ':' ) );
		$minute = substr( $time, -2 );
		
		$time_format = ovabrw_get_time_format();

		if ( $time_format == '12' ) {
			if( $hour < 12 ) {
				$time = $hour . ':' . $minute . ' AM';
			} elseif( $hour == 12 ) {
				$time = $hour . ':' . $minute . ' PM';
			} elseif ( $hour > 12 ) {
				$time = ( $hour % 12) . ':' . $minute . ' PM';
			}
		}

		return $time;
	}
}

if ( ! function_exists( 'ovabrw_get_time_format_php' ) ) {
	function ovabrw_get_time_format_php() {
		$time_format = ovabrw_get_time_format();

		if ( $time_format == '12' ) {
			$set_time_format = 'g:i A';
		} else {
			$set_time_format = 'H:i';
		}
		
		return $set_time_format;
	}
}

if ( ! function_exists( 'ovabrw_timepicker_product' ) ) {
	function ovabrw_timepicker_product( $product_id = false, $type = 'start' ) {
		if ( $type == 'start' ) {
			$manage_time_to_book = get_post_meta( $product_id, 'ovabrw_manage_time_book_start', true );
		} else {
			$manage_time_to_book = get_post_meta( $product_id, 'ovabrw_manage_time_book_end', true );
		}
		
		$ova_brw_calendar_time_to_book = ovabrw_group_time_pickup_date_global_setting();

		switch( $manage_time_to_book ) {
			case 'in_setting':
				if ( empty( $ova_brw_calendar_time_to_book ) ) {
					return 'false';
				}
				break;
			case 'new_time':
				if ( $type == 'start' ) {
					$time_to_book_new = get_post_meta( $product_id, 'ovabrw_product_time_to_book_start', true );
				} else {
					$time_to_book_new = get_post_meta( $product_id, 'ovabrw_product_time_to_book_end', true );
				}

				if( empty( $time_to_book_new ) ) {
					return 'false';
				}
				break;
			case 'no':
				return 'false';
				break;
		}

		return 'true';
	}
}

// Return real path template in Plugin or Theme
if ( ! function_exists( 'ovabrw_locate_template' ) ) {
	function ovabrw_locate_template( $template_name = '', $template_path = '', $default_path = '' ) {
		// Set variable to search in ovabrw-templates folder of theme.
		if ( ! $template_path ) :
			$template_path = 'ovabrw-templates/';
		endif;

		// Set default plugin templates path.
		if ( ! $default_path ) :
			$default_path = OVABRW_PLUGIN_PATH . 'ovabrw-templates/'; // Path to the template folder
		endif;

		// Search template file in theme folder.
		$template = locate_template( array(
			$template_path . $template_name
			// ,$template_name
		) );

		// Get plugins template file.
		if ( ! $template ) :
			$template = $default_path . $template_name;
		endif;

		return apply_filters( 'ovabrw_locate_template', $template, $template_name, $template_path, $default_path );
	}
}

// Include Template File
if ( ! function_exists( 'ovabrw_get_template' ) ) {
	function ovabrw_get_template( $template_name = '', $args = array(), $tempate_path = '', $default_path = '' ) {
		if ( is_array( $args ) && isset( $args ) ) {
			extract( $args );
		}

		$template_file = ovabrw_locate_template( $template_name, $tempate_path, $default_path );
		if ( ! file_exists( $template_file ) ) :
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
			return;
		endif;

		include $template_file;
	}
}

if ( ! function_exists( 'ovabrw_woo_wp_select_multiple' ) ) {
	function ovabrw_woo_wp_select_multiple( $field ) {
	    global $thepostid, $post, $woocommerce;

	    $thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	    $field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
	    $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	    $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	    $field['value']         = isset( $field['value'] ) ? $field['value'] : ( get_post_meta( $thepostid, $field['id'], true ) ? get_post_meta( $thepostid, $field['id'], true ) : array() );

	    echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" multiple="multiple">';

	    foreach( $field['options'] as $key => $value ) {
	        echo '<option value="' . esc_attr( $key ) . '" ' . ( in_array( $key, $field['value'] ) ? 'selected="selected"' : '' ) . '>' . esc_html( $value ) . '</option>';
	    }

	    echo '</select> ';

	    if ( ! empty( $field['description'] ) ) {
	        if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
	            echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
	        } else {
	            echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	        }
	    }

	    echo '</p>';
	}
}

if ( ! function_exists( 'ovabrw_get_timestamp_by_date_and_hour' ) ) {
	function ovabrw_get_timestamp_by_date_and_hour ( $date = 0, $time = 0 ) {
		$time_arr = explode(':', $time);
		$hour_time = 0;

		if ( !empty( $time_arr ) && is_array( $time_arr ) && count( $time_arr ) > 1 ) {
			$hour_time = (float) $time_arr[0];

			if ( strpos($time_arr[1], "AM") !== false )  {
				$time_arr[1] = str_replace('AM', '', $time_arr[1]);
				$hour_time = ($hour_time != 12) ? $hour_time : 0;

			}
			if ( strpos($time_arr[1], "PM") !== false )  {
				$time_arr[1] = str_replace('PM', '', $time_arr[1]);
				$hour_time = $hour_time + 12;
			}

			$min_time = (float) $time_arr[1];
			$hour_time = $hour_time + $min_time / 60;
		}

		$total_time = strtotime( $date ) + $hour_time * 3600;
		return $total_time;
	}
}

if ( ! function_exists( 'ovabrw_show_number_vehicle' ) ) {
	function ovabrw_show_number_vehicle( $product_id ) {
		$ovabrw_show_number_vehicle 		= get_post_meta( $product_id, 'ovabrw_show_number_vehicle', true );
		$ovabrw_show_number_vehicle_global 	= ovabrw_get_setting( get_option( 'ova_brw_booking_form_show_number_vehicle', 'yes' ) );
		
		switch( $ovabrw_show_number_vehicle ) {
			case 'in_setting':
				if ( $ovabrw_show_number_vehicle_global == 'yes' ) {
					return 'yes';
				} else {
					return 'no';
				}
				break;
			case 'yes':
				return 'yes';
				break;
			case 'no':
				return 'no';
				break;
			default:
				return 'yes';
		}
	}
}

if ( ! function_exists( 'ovabrw_show_pick_location_product' ) ) {
	function ovabrw_show_pick_location_product( $product_id = false, $type = 'pickup' ) {
		$rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

		if ( $rental_type === 'taxi' || $rental_type === 'transportation' ) return true;

		// Get custom checkout field by Category
		$product_cats 	= wp_get_post_terms( $product_id, 'product_cat' );
		$cat_id 		= isset( $product_cats[0] ) ? $product_cats[0]->term_id : '';
		$ovabrw_show_loc_booking_form = $cat_id ? get_term_meta($cat_id, 'ovabrw_show_loc_booking_form', true) : '';

		if ( $type == 'pickup' ) {
			$ovabrw_show_pick_location_product = get_post_meta( $product_id, 'ovabrw_show_pickup_location_product', true );
			
			if ( !empty( $ovabrw_show_loc_booking_form ) && in_array('pickup_loc', $ovabrw_show_loc_booking_form) ) {
				$ovabrw_booking_form_show_pick_location = 'yes';
			} elseif ( !empty( $ovabrw_show_loc_booking_form ) && !in_array('pickup_loc', $ovabrw_show_loc_booking_form) ) {
				$ovabrw_booking_form_show_pick_location = 'no';
			} else {
				$ovabrw_booking_form_show_pick_location = ovabrw_get_setting( get_option( 'ova_brw_booking_form_show_pickup_location', 'no' ) );	
			}
		} else {
			$ovabrw_show_pick_location_product = get_post_meta( $product_id, 'ovabrw_show_pickoff_location_product', true );
			
			if ( !empty( $ovabrw_show_loc_booking_form ) && in_array('dropoff_loc', $ovabrw_show_loc_booking_form) ) {
				$ovabrw_booking_form_show_pick_location = 'yes';
			} elseif ( !empty( $ovabrw_show_loc_booking_form ) && !in_array('dropoff_loc', $ovabrw_show_loc_booking_form) ) {
				$ovabrw_booking_form_show_pick_location = 'no';
			} else {
				$ovabrw_booking_form_show_pick_location = ovabrw_get_setting( get_option( 'ova_brw_booking_form_show_pickoff_location', 'no' ) );
			}
		}

		switch( $ovabrw_show_pick_location_product ) {
			case 'in_setting':
				if ( $ovabrw_booking_form_show_pick_location == 'no' ) {
					return false;
				} else {
					return true;
				}
				break;
			case 'yes':
				return true;
				break;
			case 'no':
				return false;
				break;
			default:
				if ( $ovabrw_booking_form_show_pick_location == 'no' ) {
					return false;
				} else {
					return true;
				}
		}
	}
}

if ( ! function_exists( 'ovabrw_rq_show_pick_location_product' ) ) {
	function ovabrw_rq_show_pick_location_product( $product_id = false, $type = 'pickup' ) {
		// Get custom checkout field by Category
		$product_cats 	= wp_get_post_terms( $product_id, 'product_cat' );
		$cat_id 		= isset( $product_cats[0] ) ? $product_cats[0]->term_id : '';
		$ovabrw_show_loc_booking_form = $cat_id ? get_term_meta($cat_id, 'ovabrw_show_loc_booking_form', true) : '';

		if ( $type == 'pickup' ) {
			$ovabrw_show_pick_location_product = get_post_meta( $product_id, 'ovabrw_show_pickup_location_product', true );

			if ( !empty( $ovabrw_show_loc_booking_form ) && in_array( 'pickup_loc', $ovabrw_show_loc_booking_form ) ) {
				$ovabrw_booking_form_show_pick_location = 'yes';
			} elseif ( !empty( $ovabrw_show_loc_booking_form ) && !in_array( 'pickup_loc', $ovabrw_show_loc_booking_form ) ) {
				$ovabrw_booking_form_show_pick_location = 'no';
			}else {
				$ovabrw_booking_form_show_pick_location = ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_pickup_location', 'no' ) );
			}
		} else {
			$ovabrw_show_pick_location_product = get_post_meta( $product_id, 'ovabrw_show_pickoff_location_product', true );

			if ( !empty( $ovabrw_show_loc_booking_form ) && in_array( 'dropoff_loc', $ovabrw_show_loc_booking_form ) ) {
				$ovabrw_booking_form_show_pick_location = 'yes';
			} elseif ( !empty( $ovabrw_show_loc_booking_form ) && !in_array( 'dropoff_loc', $ovabrw_show_loc_booking_form ) ) {
				$ovabrw_booking_form_show_pick_location = 'no';
			} else {
				$ovabrw_booking_form_show_pick_location = ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_pickoff_location', 'no' ) );
			}
		}

		switch( $ovabrw_show_pick_location_product ) {
			case 'in_setting':
				if ( $ovabrw_booking_form_show_pick_location == 'no' ) {
					return false;
				} else {
					return true;
				}
				break;
			case 'yes':
				return true;
				break;
			case 'no':
				return false;
				break;
			default:
				if ( $ovabrw_booking_form_show_pick_location == 'no' ) {
					return false;
				} else {
					return true;
				}
		}
	}
}

if ( ! function_exists( 'ovabrw_check_pickup_dropoff_loc_transport' ) ) {
	function ovabrw_check_pickup_dropoff_loc_transport( $product_id = false, $pick_loc = '', $type = 'pickup' ) {
		$list_loc_pickup_dropoff = ovabrw_get_list_pickup_dropoff_loc_transport( $product_id );

		if ( empty( $list_loc_pickup_dropoff ) || !is_array( $list_loc_pickup_dropoff ) ) return false;

		$flag = false;
		if ( $type == 'pickup' ) {
			foreach( $list_loc_pickup_dropoff as $pickup_loc => $dropoff_loc ) {
				if ( $pick_loc == $pickup_loc ) {
					$flag = true;
				}
			}
		} else {
			foreach( $list_loc_pickup_dropoff as $pickup_loc => $dropoff_loc ) {
				if ( is_array( $dropoff_loc ) && in_array( $pick_loc, $dropoff_loc ) ) {
					$flag = true;
				}
			}
		}

		return $flag;
	}
}

if ( ! function_exists( 'ovabrw_get_time_by_pick_up_off_loc_transport' ) ) {
	function ovabrw_get_time_by_pick_up_off_loc_transport( $product_id, $pickup_loc, $dropoff_loc ) {
		if ( ! $product_id ) return [];
		
		$list_time_pickup_dropoff_loc = ovabrw_get_list_time_loc_transport( $product_id );
		$time_complete = 0;

		if ( $list_time_pickup_dropoff_loc && is_array( $list_time_pickup_dropoff_loc ) ) {
			foreach( $list_time_pickup_dropoff_loc as $pickup => $dropoff_arr ) {
				if ( $pickup_loc == $pickup && is_array( $dropoff_arr ) ) {
					foreach( $dropoff_arr as $dropoff => $time ) {
						if ( $dropoff == $dropoff_loc ) {
							$time_complete = (float)$time;
						}
					}
				} 
			}
		}

		return $time_complete;
	}
}

if ( ! function_exists( 'ovabrw_get_list_time_loc_transport' ) ) {
	function ovabrw_get_list_time_loc_transport( $product_id ) {
		if ( ! $product_id ) return [];

		$ovabrw_pickup_location 	= get_post_meta( $product_id, 'ovabrw_pickup_location', 'false' );
	    $ovabrw_dropoff_location 	= get_post_meta( $product_id, 'ovabrw_dropoff_location', 'false' );
	    $ovabrw_location_time 		= get_post_meta( $product_id, 'ovabrw_location_time', 'false' );
	    $list_time_pickup_dropoff_loc = [];

	    if ( !empty( $ovabrw_pickup_location ) ) {
	        foreach( $ovabrw_pickup_location as $key => $location ) {
	            $list_time_pickup_dropoff_loc[$location][$ovabrw_dropoff_location[$key]] = $ovabrw_location_time[$key];
	        }
	    }

	    return $list_time_pickup_dropoff_loc;
	}
}

if ( ! function_exists( 'ovabrw_get_list_price_loc_transport' ) ) {
	function ovabrw_get_list_price_loc_transport( $product_id ) {
		if ( ! $product_id ) return [];

		$ovabrw_pickup_location 	= get_post_meta( $product_id, 'ovabrw_pickup_location', 'false' );
	    $ovabrw_dropoff_location 	= get_post_meta( $product_id, 'ovabrw_dropoff_location', 'false' );
	    $ovabrw_price_location 		= get_post_meta( $product_id, 'ovabrw_price_location', 'false' );
	    $list_price_pickup_dropoff_loc = [];

	    if ( !empty( $ovabrw_pickup_location ) ) {
	        foreach( $ovabrw_pickup_location as $key => $location ) {
	            $list_price_pickup_dropoff_loc[$location][$ovabrw_dropoff_location[$key]] = $ovabrw_price_location[$key];
	        }
	    }

	    return $list_price_pickup_dropoff_loc;
	}
}

if ( ! function_exists( 'ovabrw_show_pick_date_product' ) ) {
	function ovabrw_show_pick_date_product( $product_id = false, $type = 'pickup' ) {
		if ( $type == 'pickup' ) {
			$ovabrw_show_pick_date_product 		= get_post_meta( $product_id, 'ovabrw_show_pickup_date_product', true );
			$ovabrw_booking_form_show_pick_date = ovabrw_get_setting( get_option( 'ova_brw_booking_form_show_pickup_date', 'yes' ) );
		} else {
			$ovabrw_rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true ); 

			if ( $ovabrw_rental_type == 'transportation' ) {
				$ovabrw_show_pick_date_product = 'no';
				$dropoff_date_by_setting = get_post_meta( $product_id, 'ovabrw_dropoff_date_by_setting', true );
				if ( $dropoff_date_by_setting === 'yes' ) {
					$ovabrw_show_pick_date_product 		= get_post_meta( $product_id, 'ovabrw_show_pickoff_date_product', true );
					$ovabrw_booking_form_show_pick_date = ovabrw_get_setting( get_option( 'ova_brw_booking_form_show_dropoff_date', 'yes' ) );
				}
			} else {
				$ovabrw_show_pick_date_product 		= get_post_meta( $product_id, 'ovabrw_show_pickoff_date_product', true );
				$ovabrw_booking_form_show_pick_date = ovabrw_get_setting( get_option( 'ova_brw_booking_form_show_dropoff_date', 'yes' ) );
			}
		}
		
		switch( $ovabrw_show_pick_date_product ) {
			case 'in_setting':
				if ( $ovabrw_booking_form_show_pick_date == 'yes' ) {
					return true;
				} else {
					return false;
				}
				break;
			case 'yes':
				return true;
				break;
			case 'no':
				return false;
				break;
			default:
				return true;
		}
	}
}

if ( ! function_exists( 'ovabrw_show_rq_pick_date_product' ) ) {
	function ovabrw_show_rq_pick_date_product( $product_id = false, $type = 'pickup' ) {
		if ( $type == 'pickup' ) {
			$ovabrw_show_pick_date_product 		= get_post_meta( $product_id, 'ovabrw_show_pickup_date_product', true );
			$ovabrw_booking_form_show_pick_date = ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_pickup_date', 'yes' ) );
		} else {
			$ovabrw_show_pick_date_product = get_post_meta( $product_id, 'ovabrw_show_pickoff_date_product', true );
			$ovabrw_booking_form_show_pick_date = ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_pickoff_date', 'yes' ) );
		}
		
		switch( $ovabrw_show_pick_date_product ) {
			case 'in_setting':
				if ( $ovabrw_booking_form_show_pick_date == 'yes' ) {
					return true;
				} else {
					return false;
				}
				break;
			case 'yes':
				return true;
				break;
			case 'no':
				return false;
				break;
			default:
				return true;
		}
	}
}

if ( ! function_exists( 'ovabrw_check_time_to_book' ) ) {
	function ovabrw_check_time_to_book( $product_id = false, $type = 'start' ) {
		if ( $type == 'start' ) {
			$manage_time_to_book = get_post_meta( $product_id, 'ovabrw_manage_time_book_start', true );
		} else {
			$manage_time_to_book = get_post_meta( $product_id, 'ovabrw_manage_time_book_end', true );
		}
		
		switch( $manage_time_to_book ) {
			case 'in_setting':
				if ( $type = 'start' ) {
					$time_to_book = ovabrw_group_time_pickup_date_global_setting();
				} else {
					$time_to_book = ovabrw_group_time_dropoff_date_global_setting();
				}
				break;
			case 'new_time':
				if ( $type = 'start' ) {
					$time_to_book = get_post_meta( $product_id, 'ovabrw_product_time_to_book_start', true );
				} else {
					$time_to_book = get_post_meta( $product_id, 'ovabrw_product_time_to_book_end', true );
				}
				break;
			case 'no':
				$time_to_book = '';
				break;
			default:
				if ( $type = 'start' ) {
					$time_to_book = ovabrw_group_time_pickup_date_global_setting();
				} else {
					$time_to_book = ovabrw_group_time_dropoff_date_global_setting();
				}
		}

		return $time_to_book;
	}
}

if ( ! function_exists( 'ovabrw_time_to_book' ) ) {
	function ovabrw_time_to_book( $product_id = false, $type = 'start' ) {
		if ( $type == 'start' ) {
			$manage_time_to_book_start = get_post_meta( $product_id, 'ovabrw_manage_time_book_start', true );

			switch( $manage_time_to_book_start ) {
				case 'in_setting':
					$time_to_book = ovabrw_group_time_pickup_date_global_setting();
					break;
				case 'new_time':
					$time_to_book = get_post_meta( $product_id, 'ovabrw_product_time_to_book_start', true );
					break;
				case 'no':
					$time_to_book = 'no';
					break;
				default:
					$time_to_book = ovabrw_group_time_pickup_date_global_setting();
			}
		} else {
			$ovabrw_manage_time_book_end = get_post_meta( $product_id, 'ovabrw_manage_time_book_end', true );

			switch( $ovabrw_manage_time_book_end ) {
				case 'in_setting':
					$time_to_book = ovabrw_group_time_dropoff_date_global_setting();
					break;
				case 'new_time':
					$time_to_book = get_post_meta( $product_id, 'ovabrw_product_time_to_book_end', true );
					break;
				case 'no':
					$time_to_book = 'no';
					break;
				default:
					$time_to_book = ovabrw_group_time_dropoff_date_global_setting();
			}
		}

		return $time_to_book;
	}
}

// Get Defined 1 day of product
if ( ! function_exists( 'defined_one_day' ) ) {
	function defined_one_day( $product_id ) {
		$ovabrw_price_type 		= get_post_meta( $product_id, 'ovabrw_price_type', true );
		$ovabrw_define_1_day 	= get_post_meta( $product_id, 'ovabrw_define_1_day', true );

		if ( $ovabrw_price_type === 'day' && $ovabrw_define_1_day === 'hotel' ) {
			return 'hotel';
		} elseif ( $ovabrw_price_type === 'day' && $ovabrw_define_1_day === 'day' ) {
			return 'day';
		} elseif ( $ovabrw_price_type === 'day' && $ovabrw_define_1_day === 'hour' ) {
			return 'hour';
		}

		return false;
	}
}

// Get Text Time
if ( ! function_exists( 'ovabrw_text_time' ) ) {
	function ovabrw_text_time( $price_type, $rent_time ) {
		if ( $price_type == 'day' ) {
			$text = esc_html__( 'Day(s)', 'ova-brw' );
		} elseif ( $price_type == 'hour' ) {
			$text = esc_html__( 'Hour(s)', 'ova-brw' );
		} elseif ( $price_type == 'mixed' && $rent_time['rent_time_day_raw'] < 1 ) {
			$text = esc_html__( 'Hour(s)', 'ova-brw' );
		} elseif ( $price_type == 'mixed' && $rent_time['rent_time_day_raw'] >= 1 ) {
			$text = esc_html__( 'Day(s)', 'ova-brw' );
		} else {
	        $text = '';
	    }

	    return $text;
	}
}

// Get Text Time for Global
if ( ! function_exists( 'ovabrw_text_time_gl' ) ) {
	function ovabrw_text_time_gl( $price_type, $rent_time ) {
		if ( $price_type == 'day' ) {
			$text = esc_html__( 'Day(s)', 'ova-brw' );
		} elseif ( $price_type == 'hour' ) {
			$text = esc_html__( 'Hour(s)', 'ova-brw' );
		} elseif ( $price_type == 'mixed' && $rent_time['rent_time_day_raw'] < 1 ) {
			$text = esc_html__( 'Hour(s)', 'ova-brw' );
		} elseif( $price_type == 'mixed' && $rent_time['rent_time_day_raw'] >= 1 ) {
			$text = esc_html__( 'Day(s)', 'ova-brw' );
		} else {
	        $text = '';
	    }

	    return $text;
	}
}

// Get Text Time for range time
if ( ! function_exists( 'ovabrw_text_time_rt' ) ) {
	function ovabrw_text_time_rt( $price_type, $rent_time ) {
		if ( $price_type == 'day' ) {
			$text = esc_html__( 'Special Day(s)', 'ova-brw' );
		} elseif ( $price_type == 'hour' ) {
			$text = esc_html__( 'Special Hour(s)', 'ova-brw' );
		} elseif ( $price_type == 'mixed' && $rent_time['rent_time_day_raw'] < 1 ) {
			$text = esc_html__( 'Special Hour(s)', 'ova-brw' );
		} elseif ( $price_type == 'mixed' && $rent_time['rent_time_day_raw'] >= 1 ) {
			$text = esc_html__( 'Special Day(s)', 'ova-brw' );
		} else {
	        $text = '';
	    }

	    return $text;
	}
}

// List custom checkout fields array
if ( ! function_exists( 'ovabrw_get_list_field_checkout' ) ) {
	function ovabrw_get_list_field_checkout( $post_id ) {
		if ( ! $post_id ) return [];

		$list_ckf_output = [];
		$ovabrw_manage_custom_checkout_field = get_post_meta( $post_id, 'ovabrw_manage_custom_checkout_field', true );
		$list_field_checkout = get_option( 'ovabrw_booking_form', array() );

		// Get custom checkout field by Category
		$product_cats 	= wp_get_post_terms( $post_id, 'product_cat' );
		$cat_id 		= isset( $product_cats[0] ) ? $product_cats[0]->term_id : '';
		$ovabrw_custom_checkout_field = $cat_id ? get_term_meta($cat_id, 'ovabrw_custom_checkout_field', true) : '';
		$ovabrw_choose_custom_checkout_field = $cat_id ? get_term_meta($cat_id, 'ovabrw_choose_custom_checkout_field', true) : '';
		
		if ( $ovabrw_manage_custom_checkout_field === 'new' ) {
			$list_field_checkout_in_product = get_post_meta( $post_id, 'ovabrw_product_custom_checkout_field', true );
			$list_field_checkout_in_product_arr = explode( ',', $list_field_checkout_in_product );
			$list_field_checkout_in_product_arr = array_map( 'trim', $list_field_checkout_in_product_arr );

			$list_ckf_output = [];
			if ( !empty( $list_field_checkout_in_product_arr ) && is_array( $list_field_checkout_in_product_arr ) ) {
				foreach( $list_field_checkout_in_product_arr as $field_name ) {
					if ( array_key_exists( $field_name, $list_field_checkout ) ) {
						$list_ckf_output[$field_name] = $list_field_checkout[$field_name];
					}
				}
			} 
		} elseif ( $ovabrw_choose_custom_checkout_field == 'all' ) {
			$list_ckf_output = $list_field_checkout;
		} elseif( $ovabrw_choose_custom_checkout_field == 'special' ) {
			if ( $ovabrw_custom_checkout_field ) {
				foreach( $ovabrw_custom_checkout_field as $field_name ) {
					if ( array_key_exists( $field_name, $list_field_checkout ) ) {
						$list_ckf_output[$field_name] = $list_field_checkout[$field_name];
					}
				}
			} else {
				$list_ckf_output = [];
			}
		} else {
			$list_ckf_output = $list_field_checkout;
		}

		return $list_ckf_output;
	}
}

// Render value in product to javascript at frontend
if ( ! function_exists( 'ovabrw_create_order_time_calendar' ) ) {
	function ovabrw_create_order_time_calendar( $post_id ) {
		if ( ! $post_id ) return [];

		$statuses 		= brw_list_order_status();
		$order_date 	= get_order_rent_time( $post_id, $statuses );
		$price_type 	= get_post_meta( $post_id, 'ovabrw_price_type', true );
		$price_calendar = [];

		if ( $price_type === 'day' ) {
			$regular_price 			= get_post_meta( $post_id, '_regular_price', true );
		    $ovabrw_daily_monday 	= !empty( get_post_meta( $post_id, 'ovabrw_daily_monday', true ) ) ? (float)get_post_meta( $post_id, 'ovabrw_daily_monday', true ) : (float)$regular_price;
		    $ovabrw_daily_tuesday 	= !empty( get_post_meta( $post_id, 'ovabrw_daily_tuesday', true ) ) ? (float)get_post_meta( $post_id, 'ovabrw_daily_tuesday', true ) : (float)$regular_price;
		    $ovabrw_daily_wednesday = !empty( get_post_meta( $post_id, 'ovabrw_daily_wednesday', true ) ) ? (float)get_post_meta( $post_id, 'ovabrw_daily_wednesday', true ) : (float)$regular_price;
		    $ovabrw_daily_thursday 	= !empty( get_post_meta( $post_id, 'ovabrw_daily_thursday', true ) ) ? (float)get_post_meta( $post_id, 'ovabrw_daily_thursday', true ) : (float)$regular_price;
		    $ovabrw_daily_friday 	= !empty( get_post_meta( $post_id, 'ovabrw_daily_friday', true ) ) ?  (float)get_post_meta( $post_id, 'ovabrw_daily_friday', true ) : (float)$regular_price;
		    $ovabrw_daily_saturday 	= !empty( get_post_meta( $post_id, 'ovabrw_daily_saturday', true ) ) ? (float)get_post_meta( $post_id, 'ovabrw_daily_saturday', true ) : (float)$regular_price;
		    $ovabrw_daily_sunday 	= !empty( get_post_meta( $post_id, 'ovabrw_daily_sunday', true ) ) ? (float)get_post_meta( $post_id, 'ovabrw_daily_sunday', true ) : (float)$regular_price;

		    $price_calendar = [
		    	[
		    		'type_price' => 'day',
		    	],
		    	[
		    		'ovabrw_daily_monday' 		=> wc_price_calendar( $ovabrw_daily_monday ),
		    		'ovabrw_daily_tuesday' 		=> wc_price_calendar( $ovabrw_daily_tuesday ),
		    		'ovabrw_daily_wednesday' 	=> wc_price_calendar( $ovabrw_daily_wednesday ),
		    		'ovabrw_daily_thursday' 	=> wc_price_calendar( $ovabrw_daily_thursday ),
		    		'ovabrw_daily_friday' 		=> wc_price_calendar( $ovabrw_daily_friday ),
		    		'ovabrw_daily_saturday' 	=> wc_price_calendar( $ovabrw_daily_saturday ),
		    		'ovabrw_daily_sunday' 		=> wc_price_calendar( $ovabrw_daily_sunday ),
		    	],
		    ];
		} elseif ( $price_type === 'hour' ) {
			$regular_price_hour = get_post_meta( $post_id, 'ovabrw_regul_price_hour', true );

			$price_calendar = [
		    	[
		    		'type_price' => 'hour',
		    	],
		    	[
		    		'ovabrw_price_hour' => wc_price_calendar( $regular_price_hour ),
		    	],
		    ];
		} elseif ( $price_type === 'mixed' ) {
			$regular_price_day  	= get_post_meta( $post_id, '_regular_price', true );
			$regular_price_hour 	= get_post_meta( $post_id, 'ovabrw_regul_price_hour', true );
			$ovabrw_daily_monday 	= !empty( get_post_meta( $post_id, 'ovabrw_daily_monday', true ) ) ? (float)get_post_meta( $post_id, 'ovabrw_daily_monday', true ) : (float)$regular_price_day;
		    $ovabrw_daily_tuesday 	= !empty( get_post_meta( $post_id, 'ovabrw_daily_tuesday', true ) ) ? (float)get_post_meta( $post_id, 'ovabrw_daily_tuesday', true ) : (float)$regular_price_day;
		    $ovabrw_daily_wednesday = !empty( get_post_meta( $post_id, 'ovabrw_daily_wednesday', true ) ) ? (float)get_post_meta( $post_id, 'ovabrw_daily_wednesday', true ) : (float)$regular_price_day;
		    $ovabrw_daily_thursday 	= !empty( get_post_meta( $post_id, 'ovabrw_daily_thursday', true ) ) ? (float)get_post_meta( $post_id, 'ovabrw_daily_thursday', true ) : (float)$regular_price_day;
		    $ovabrw_daily_friday 	= !empty( get_post_meta( $post_id, 'ovabrw_daily_friday', true ) ) ?  (float)get_post_meta( $post_id, 'ovabrw_daily_friday', true ) : (float)$regular_price_day;
		    $ovabrw_daily_saturday 	= !empty( get_post_meta( $post_id, 'ovabrw_daily_saturday', true ) ) ? (float)get_post_meta( $post_id, 'ovabrw_daily_saturday', true ) : (float)$regular_price_day;
		    $ovabrw_daily_sunday 	= !empty( get_post_meta( $post_id, 'ovabrw_daily_sunday', true ) ) ? (float)get_post_meta( $post_id, 'ovabrw_daily_sunday', true ) : (float)$regular_price_day;

			$price_calendar = [
		    	[
		    		'type_price' => 'mixed',
		    	],
		    	[
		    		'ovabrw_daily_monday' 		=> wc_price_calendar( $regular_price_hour ) . '<br>' . wc_price_calendar( $ovabrw_daily_monday ),
		    		'ovabrw_daily_tuesday' 		=> wc_price_calendar( $regular_price_hour )  . '<br>' . wc_price_calendar( $ovabrw_daily_tuesday ),
		    		'ovabrw_daily_wednesday' 	=> wc_price_calendar( $regular_price_hour ) . '<br>' . wc_price_calendar( $ovabrw_daily_wednesday ),
		    		'ovabrw_daily_thursday' 	=> wc_price_calendar( $regular_price_hour ) . '<br>' . wc_price_calendar( $ovabrw_daily_thursday ),
		    		'ovabrw_daily_friday' 		=> wc_price_calendar( $regular_price_hour ) . '<br>' . wc_price_calendar( $ovabrw_daily_friday ),
		    		'ovabrw_daily_saturday' 	=> wc_price_calendar( $regular_price_hour ) . '<br>' . wc_price_calendar( $ovabrw_daily_saturday ),
		    		'ovabrw_daily_sunday' 		=> wc_price_calendar( $regular_price_hour ) . '<br>' . wc_price_calendar( $ovabrw_daily_sunday ),
		    	],
		    ];
		} elseif ( $price_type === 'taxi' ) {
			$regular_price_taxi = get_post_meta( $post_id, 'ovabrw_regul_price_taxi', true );

			$price_calendar = [
		    	[
		    		'type_price' => 'taxi',
		    	],
		    	[
		    		'ovabrw_price_taxi' => wc_price_calendar( $regular_price_taxi ),
		    	],
		    ];
		}
		
		if ( apply_filters( 'ovabrw_show_price_calendar', true ) == false ) {
			$price_calendar = [];
		}
		
		if ( $order_date && $order_date != '[]' ) {
			return array( 'order_time' =>  $order_date, 'price_calendar' => json_encode( $price_calendar ) );
		} else {
			return array( 'order_time' => '', 'price_calendar' => json_encode( $price_calendar ) );
		}
	}
}

// Get Deposit Type HTML in Product
if ( ! function_exists( 'ovabrw_p_deposit_type' ) ) {
	function ovabrw_p_deposit_type( $product_id ) {
		$deposit_type 	= get_post_meta ( $product_id, 'ovabrw_type_deposit', true );
		$deposit_value 	= get_post_meta ( $product_id, 'ovabrw_amount_deposit', true );

		if ( $deposit_type === 'percent' ) {
			return '<span>'.esc_html($deposit_value).'%</span>';
		} elseif ($deposit_type === 'value') {
			return '<span>'.ovabrw_wc_price($deposit_value).'</span>';
		}
	}
}

// Get price array of weekdays
if ( ! function_exists( 'ovabrw_p_weekdays' ) ) {
	function ovabrw_p_weekdays( $product_id ) {
		$rental_type 	= get_post_meta( $product_id, 'ovabrw_price_type', true );
		$monday     	= get_post_meta( $product_id, 'ovabrw_daily_monday', true );
        $tuesday    	= get_post_meta( $product_id, 'ovabrw_daily_tuesday', true );
        $wednesday  	= get_post_meta( $product_id, 'ovabrw_daily_wednesday', true );
        $thursday   	= get_post_meta( $product_id, 'ovabrw_daily_thursday', true );
        $friday     	= get_post_meta( $product_id, 'ovabrw_daily_friday', true );
        $saturday   	= get_post_meta( $product_id, 'ovabrw_daily_saturday', true );
        $sunday     	= get_post_meta( $product_id, 'ovabrw_daily_sunday', true );

        if ( ( $rental_type == 'day' || $rental_type == 'mixed' ) && $monday && $tuesday && $wednesday && $thursday && $friday && $saturday && $sunday ) { 

			$daily = array(
				'monday' 	=> $monday, 
				'tuesday' 	=> $tuesday, 
				'wednesday' => $wednesday, 
				'thursday' 	=> $thursday, 
				'friday' 	=> $friday, 
				'saturday' 	=> $saturday, 
				'sunday' 	=> $sunday
			);
		} else {
			return false;
		}

		return $daily;
	}
}

// List Order Status 
if ( ! function_exists( 'brw_list_order_status' ) ) {
	function brw_list_order_status() {
		return apply_filters( 'brw_list_order_status', array( 'wc-completed', 'wc-processing' ) );
	}
}

if ( ! function_exists( 'ovabrw_get_total_stock' ) ) {
	function ovabrw_get_total_stock( $product_id ) {
		$ovabrw_manage_store 	= get_post_meta( $product_id, 'ovabrw_manage_store', true );
	    $number_stock 			= 1;

		if ( $ovabrw_manage_store == 'store' ) {
			$number_stock = (int)get_post_meta( $product_id, 'ovabrw_car_count', true );
			
			return $number_stock;
		} elseif ( $ovabrw_manage_store == 'id_vehicle' ) {
			$ovabrw_id_vehicles = get_post_meta( $product_id, 'ovabrw_id_vehicles', true );
			$number_stock 		=  $ovabrw_id_vehicles ? count( $ovabrw_id_vehicles ) : 0;

			return $number_stock;
		}

		return $number_stock;
	}
}

if ( ! function_exists( 'ovabrw_createDatefull' ) ) {
	function ovabrw_createDatefull( $start = '', $end = '', $format = "Y-m-d H:i" ){
	    $dates = array();

	    while( $start <= $end ) {
	        array_push( $dates, date( $format, $start) );
	        $start += 86400;
	    }

	    return $dates;
	} 
}

if ( ! function_exists( 'total_between_2_days' ) ) {
	function total_between_2_days( $start, $end ) {
	    return floor( abs( strtotime( $end ) - strtotime( $start ) ) / ( 60*60*24 ) );
	}
}

// You have to insert unistamp time
if ( ! function_exists( 'get_time_bew_2day' ) ) {
	function get_time_bew_2day( $start = '', $end = '', $product_id = '' ) {
		$start 	= $start == '' ? null : $start;
		$end 	= $end == '' ? null : $end;

	    $defined_one_day = $product_id != '' ? defined_one_day( $product_id ) : '';    

	    if ( $defined_one_day === 'day' ) {
	        $start = strtotime( date( 'Y-m-d', $start ) );
	        $end = strtotime( date('Y-m-d', $end ) ) + 24*60*60 - 1  ;
	    } elseif ( $defined_one_day === 'hotel' ) {
	    	$start = strtotime( date( 'Y-m-d', $start ) );
	        $end = strtotime( date('Y-m-d', $end ) ) ;
	    }

	    $rent_time_day_raw 	= ( $end - $start )/(24*60*60);
	    $rent_time_hour_raw = ( $end - $start )/(60*60);
	    $rent_time_day 		= ceil( $rent_time_day_raw );
	    $rent_time_hour 	= ceil( $rent_time_hour_raw );

	    return array( 'rent_time_day_raw' => $rent_time_day_raw, 'rent_time_hour_raw' => $rent_time_hour_raw, 'rent_time_day' => $rent_time_day, 'rent_time_hour' => $rent_time_hour );
	}
}

if ( ! function_exists( 'ovabrw_array_flatten' ) ) {
	function ovabrw_array_flatten( $array ) {
	    if ( ! is_array( $array ) ) { 
	        return FALSE; 
	    } 

	    $result = array(); 
	    foreach( $array as $key => $value ) { 
	        if ( is_array( $value ) ) { 
	          	$result = array_merge($result, ovabrw_array_flatten($value)); 
	      	} else { 
	          $result[$key] = $value; 
	      	} 
	    } 

	    return $result; 
	}
}

if ( ! function_exists( 'ovabrw_pagination_theme' ) ) {
	function ovabrw_pagination_theme( $ovabrw_query = null ) {
	    /** Stop execution if there's only 1 page */
	    if ( $ovabrw_query != null ) {
	        if ( $ovabrw_query->max_num_pages <= 1 ) return; 
	    } elseif ( $wp_query->max_num_pages <= 1 ) {
	    	return;
	    }

	    $paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;

	    if ( $ovabrw_query != null ) {
	        $max = intval( $ovabrw_query->max_num_pages );
	    } else {
	        $max = intval( $wp_query->max_num_pages );    
	    }
	    
	    /** Add current page to the array */
	    if ( $paged >= 1 ) $links[] = $paged;

	    /** Add the pages around the current page to the array */
	    if ( $paged >= 3 ) {
	        $links[] = $paged - 1;
	        $links[] = $paged - 2;
	    }

	    if ( ( $paged + 2 ) <= $max ) {
	        $links[] = $paged + 2;
	        $links[] = $paged + 1;
	    }

	    echo '<nav class="woocommerce-pagination"><ul class="page-numbers">' . "\n";

	    /** Previous Post Link */
	    if ( get_previous_posts_link() )
	        printf( '<li class="prev page-numbers">%s</li>' . "\n", get_previous_posts_link('<i class="arrow_carrot-left"></i>') );

	    /** Link to first page, plus ellipses if necessary */
	    if ( !in_array( 1, $links ) ) {
	        $class = $paged == 1 ? ' class="current"' : 'page-numbers';
	        printf( '<li><a href="%s" %s>%s</a></li>' . "\n",  esc_url( get_pagenum_link( 1 ) ), $class, '1' );

	        if ( !in_array( 2, $links ) )
	            echo '<li>...</li>';
	    }

	    /** Link to current page, plus 2 pages in either direction if necessary */
	    sort( $links );
	    foreach( (array)$links as $link ) {
	        $class = $paged == $link ? ' class="current"' : '';
	        printf( '<li><a href="%s" %s>%s</a></li>' . "\n",  esc_url( get_pagenum_link( $link ) ), $class, $link );
	    }

	    /** Link to last page, plus ellipses if necessary */
	    if ( !in_array( $max, $links ) ) {
	        if ( !in_array( $max - 1, $links ) )
	            echo '<li>...</li>' . "\n";

	        $class = $paged == $max ? ' class="current"' : '';
	        printf( '<li><a href="%s" %s>%s</a></li>' . "\n",  esc_url( get_pagenum_link( $max ) ), $class, $max );
	    }

	    /** Next Post Link */
	    if ( get_next_posts_link() )
	        printf( '<li class="next page-numbers">%s</li>' . "\n", get_next_posts_link('<i class="arrow_carrot-right"></i>') );
	    echo '</ul></nav>' . "\n";
	}
}

if ( ! function_exists( 'ovabrw_get_real_date' ) ) {
	function ovabrw_get_real_date( $product_id, $rental_type, $defined_one_day, $ovabrw_pickup_date, $ovabrw_pickoff_date ) {
		$date_format = ovabrw_get_date_format();

		if ( $rental_type === 'transportation' ) {
			$dropoff_date_by_setting = get_post_meta( $product_id, 'ovabrw_dropoff_date_by_setting', true );

			if ( $dropoff_date_by_setting != 'yes' ) {
				$ovabrw_pickup_date_timestamp 	= strtotime( $ovabrw_pickup_date );
	        	$ovabrw_pickup_date_real 		= date( $date_format, $ovabrw_pickup_date_timestamp ) . ' 00:00';
	        	$ovabrw_dropoff_date_real 		= date( $date_format, $ovabrw_pickup_date_timestamp ) . ' 24:00';
			} else {
				$ovabrw_pickup_date_real 	= $ovabrw_pickup_date;
	        	$ovabrw_dropoff_date_real 	= $ovabrw_pickoff_date;
			}
	    }
	    
	    if ( $defined_one_day == 'hotel' ) {
	        $ovabrw_pickup_date_timestamp 	= strtotime( $ovabrw_pickup_date );
	        $ovabrw_pickoff_date_timestamp 	= strtotime( $ovabrw_pickoff_date );
	        $ovabrw_pickup_date 			= date($date_format, $ovabrw_pickup_date_timestamp);
	        $ovabrw_pickoff_date 			= date($date_format, $ovabrw_pickoff_date_timestamp);
	        $ovabrw_pickup_date_timestamp 	= strtotime( $ovabrw_pickup_date );
	        $ovabrw_pickoff_date_timestamp 	= strtotime( $ovabrw_pickoff_date );
	        $ovabrw_pickup_date_real 		= date( $date_format, $ovabrw_pickup_date_timestamp ) .' '. apply_filters( 'brw_real_pickup_time_hotel', '14:00' );
	        $ovabrw_dropoff_date_real 		= date(  $date_format, $ovabrw_pickoff_date_timestamp ) .' '. apply_filters( 'brw_real_dropoff_time_hotel', '11:00' );
	    } elseif ( $defined_one_day == 'day' ) {
	        $ovabrw_pickup_date_timestamp 	= strtotime( $ovabrw_pickup_date );
	        $ovabrw_pickoff_date_timestamp 	= strtotime( $ovabrw_pickoff_date );
	        $ovabrw_pickup_date_real 		= date( $date_format, $ovabrw_pickup_date_timestamp ) . ' 00:00';
	        $ovabrw_dropoff_date_real 		= date( $date_format, $ovabrw_pickoff_date_timestamp ) . ' 24:00';
	    } elseif ( $defined_one_day == 'hour' ) {
	        //fixed later
	        $ovabrw_pickup_date_real 	= $ovabrw_pickup_date;
	        $ovabrw_dropoff_date_real 	= $ovabrw_pickoff_date;
	    } else {
	    	$ovabrw_pickup_date_real 	= $ovabrw_pickup_date;
        	$ovabrw_dropoff_date_real 	= $ovabrw_pickoff_date;
	    }

	    if ( $rental_type === 'hour' || $rental_type === 'mixed' || $rental_type === 'period_time' ) {
	        $ovabrw_pickup_date_real 	= $ovabrw_pickup_date;
	        $ovabrw_dropoff_date_real 	= $ovabrw_pickoff_date;
	    }

	    return array( 'pickup_date_real' => $ovabrw_pickup_date_real, 'pickoff_date_real' => $ovabrw_dropoff_date_real );
	}
}

// Display price in calendar
if ( ! function_exists( 'wc_price_calendar' ) ) {
	function wc_price_calendar( $price ) {
		return ovabrw_wc_price( 
			$price, 
			apply_filters( 
				'wc_price_calendar_args', 
					array(
		                'ex_tax_label'       => false,
		                'currency'           => '',
		                'decimal_separator'  => wc_get_price_decimal_separator(),
		                'thousand_separator' => wc_get_price_thousand_separator(),
		                'decimals'           => wc_get_price_decimals(),
		                'price_format'       => get_woocommerce_price_format(),
           			) 
			) 
		);
	}
}

// Get Array Product ID with WPML
if ( ! function_exists( 'ovabrw_get_wpml_product_ids' ) ) {
	function ovabrw_get_wpml_product_ids( $product_id_original ) {
		$translated_ids = array();

		// get plugin active
		$active_plugins = get_option('active_plugins');

		if ( in_array ( 'polylang/polylang.php', $active_plugins ) || in_array ( 'polylang-pro/polylang.php', $active_plugins ) ) {
				$languages = pll_languages_list();
				if ( !isset( $languages ) ) return;
				foreach ($languages as $lang) {
					$translated_ids[] = pll_get_post($product_id_original, $lang);
				}
		} elseif ( in_array ( 'sitepress-multilingual-cms/sitepress.php', $active_plugins ) ) {
			global $sitepress;
		
			if ( !isset( $sitepress ) ) return;
			
			$trid 			= $sitepress->get_element_trid($product_id_original, 'post_product');
			$translations 	= $sitepress->get_element_translations($trid, 'product');

			foreach( $translations as $lang => $translation ) {
			    $translated_ids[] = $translation->element_id;
			}
		} else {
			$translated_ids[] = $product_id_original;
		}

		if ( empty( $translated_ids ) ) {
			$translated_ids[] = $product_id_original;
		}

		return apply_filters( 'ovabrw_multiple_languages', $translated_ids );
	}
}

// Get Pick up date from URL in Product detail
if ( ! function_exists( 'ovabrw_get_current_date_from_search' ) ) {
	function ovabrw_get_current_date_from_search( $choose_hour = 'yes', $type = 'pickup_date', $product_id = false ) {
		// Get date from URL
		if ( $type == 'pickup_date' ) {
			$time = isset( $_GET['pickup_date'] ) ? strtotime( $_GET['pickup_date'] ) : '';
		} elseif ( $type == 'dropoff_date' ) {
			$time = ( isset( $_GET['dropoff_date'] ) ) ? strtotime( $_GET['dropoff_date'] ) : '';
		}

		$dateformat 	= ovabrw_get_date_format();
		$time_format 	= ovabrw_get_time_format_php();

		if ( $time && $choose_hour == 'yes' ) {
			return date( $dateformat.' '.$time_format, $time );
		} elseif ( $time && $choose_hour == 'no' ) {
			return date( $dateformat, $time );		
		}

		return '';
	}
}

// Get All custom taxonomy display in listing of product
if ( ! function_exists( 'get_all_cus_tax_dis_listing' ) ) {
	function get_all_cus_tax_dis_listing( $pid ) {
		$all_cus_choosed 		= array();
		$all_cus_choosed_tmp 	= array();

		// Get All Categories of this product
		$categories = get_the_terms( $pid, 'product_cat' );
		if ( $categories ) {
			foreach( $categories as $key => $value ) {
				$cat_id = $value->term_id;

				// Get custom tax display in category
				$ovabrw_custom_tax = get_term_meta($cat_id, 'ovabrw_custom_tax', true);

				if ( $ovabrw_custom_tax ) {
					foreach( $ovabrw_custom_tax as $slug_tax ) {
					
						// Get value of terms in product
						$terms = get_the_terms( $pid, $slug_tax );

						// Get option: custom taxonomy
						$ovabrw_custom_taxonomy =  get_option( 'ovabrw_custom_taxonomy', '' );
						$show_listing_status 	= 'no';

						if ( $ovabrw_custom_taxonomy ) {
							foreach( $ovabrw_custom_taxonomy as $slug => $value ) {
								if ( $slug_tax == $slug && isset( $value['show_listing'] ) && $value['show_listing'] == 'on' ) {
									$show_listing_status = 'yes';
									break;
								}
							}
						}
						if ( $terms && $show_listing_status == 'yes' ) {
							foreach( $terms as $term ) {
								if ( !in_array( $slug_tax, $all_cus_choosed_tmp ) ) {
									// Assign array temp to check exist
									array_push($all_cus_choosed_tmp, $slug_tax);
									array_push($all_cus_choosed, array( 'slug' => $slug_tax, 'name' => $term->name) );
								}
							}
						}
					}
				}
			}
		}

		return $all_cus_choosed;
	}
}

// Get custom taxonomy of an product
if ( ! function_exists( 'ovabrw_get_taxonomy_choosed_product' ) ) {
	function ovabrw_get_taxonomy_choosed_product( $pid, $show_archive = '' ) {
		// Custom taxonomies choosed in post
		$all_cus_tax 	= array();
		$exist_cus_tax 	= array();
		
		// Get Category of product
		$cats = get_the_terms( $pid, 'product_cat' );
		$show_taxonomy_depend_category = ovabrw_get_setting( get_option( 'ova_brw_search_show_tax_depend_cat', 'yes' ) );

		if ( 'yes' == $show_taxonomy_depend_category ) {
			if ( $cats ) {
				foreach ( $cats as $key => $cat ) {
					// Get custom taxonomy display in category
					$ovabrw_custom_tax = get_term_meta($cat->term_id, 'ovabrw_custom_tax', true);	
					
					if ( $ovabrw_custom_tax ) {
						foreach( $ovabrw_custom_tax as $key => $value ) {
							array_push( $exist_cus_tax, $value );
						}	
					}
				}
			}

			if ( $exist_cus_tax ) {
				foreach( $exist_cus_tax as $key => $value ) {
					$cus_tax_terms = get_the_terms( $pid, $value );

					if ( $cus_tax_terms ) {
						foreach( $cus_tax_terms as $key => $value ) {
							$list_fields = get_option( 'ovabrw_custom_taxonomy', array() );

							if ( ! empty( $list_fields ) ):
			                    foreach( $list_fields as $key => $field ):
			                    	$enabled 		= isset( $field['enabled'] ) ? $field['enabled'] : '';
			                    	$show_listing 	= isset( $field['show_listing'] ) ? $field['show_listing'] : '';

			                    	if ( $enabled != 'on' ) continue;
			                    	if ( $show_archive && $show_archive != $show_listing ) continue;

			                    	if ( is_object( $value ) && $value->taxonomy == $key ) {
			                    		if ( array_key_exists($key, $all_cus_tax) ) {
			                    			if ( ! in_array( $value->name, $all_cus_tax[$key]['value'] ) ) {
			                    				array_push( $all_cus_tax[$key]['value'], $value->name );
			                    				array_push( $all_cus_tax[$key]['link'], get_term_link( $value->term_id ) );
			                    			}
			                    		} else {
		                    				if ( isset( $field['label_frontend'] ) && $field['label_frontend'] ) {
		                    					$all_cus_tax[$key]['name'] = $field['label_frontend'];	
		                    				} else {
		                    					$all_cus_tax[$key]['name'] = $field['name'];	
		                    				}

		                    				$all_cus_tax[$key]['value'] = array( $value->name );
		                    				$all_cus_tax[$key]['link'] 	= array( get_term_link( $value->term_id ) );
			                    		}

			                    		break;
			                    	}
			                    endforeach;
			                endif;
						}
					}
				}
			}
		} else {
			$list_fields = get_option( 'ovabrw_custom_taxonomy', array() );

			if ( ! empty( $list_fields ) ) {
				foreach( $list_fields as $key => $field ) {
					$enabled 		= isset( $field['enabled'] ) ? $field['enabled'] : '';
					$show_listing 	= isset( $field['show_listing'] ) ? $field['show_listing'] : '';

			        if ( $enabled != 'on' ) continue;
			        if ( $show_archive && $show_archive != $show_listing ) continue;

					$terms = get_the_terms( $pid, $key );

					if ( $terms && !isset( $terms->errors ) ) {
						foreach( $terms as $value ) {
							if ( is_object( $value ) ) {
								if ( array_key_exists( $key, $all_cus_tax ) ) {
									if ( ! in_array( $value->name, $all_cus_tax[$key]['value'] ) ) {
			            				array_push( $all_cus_tax[$key]['value'], $value->name );
			            				array_push( $all_cus_tax[$key]['link'], get_term_link( $value->term_id ) );
			            			}
								} else {
									if ( isset( $field['label_frontend'] ) && $field['label_frontend'] ) {
			        					$all_cus_tax[$key]['name'] = $field['label_frontend'];	
			        				} else {
			        					$all_cus_tax[$key]['name'] = $field['name'];
			        				}

									$all_cus_tax[$key]['value'] = array( $value->name );
									$all_cus_tax[$key]['link'] 	= array( get_term_link( $value->term_id ) );
								}
							}
						}
					}
				}
			}
		}

		return $all_cus_tax;
	}
}

// Get Special Time Product
if ( ! function_exists( 'ovabrw_get_special_time' ) ) {
	function ovabrw_get_special_time( $product_id, $price_type ) {
		// init array special time
		$special_time = [];
		$prices = [];

		// get special price
		if ( $price_type == 'day' ) {
			$ovabrw_rt_price = get_post_meta( $product_id, 'ovabrw_rt_price', true );
		} elseif ( $price_type == 'hour' ) {
			$ovabrw_rt_price = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
		} elseif ( $price_type == 'mixed' ) {
			$ovabrw_rt_price  = get_post_meta( $product_id, 'ovabrw_rt_price', true );
			$ovabrw_rt_price_hour = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
		}

		// get timestamp
		$ovabrw_start_timestamp = get_post_meta( $product_id, 'ovabrw_start_timestamp', true );
		$ovabrw_end_timestamp 	= get_post_meta( $product_id, 'ovabrw_end_timestamp', true );

		if ( !empty( $ovabrw_rt_price ) ) {
			foreach( $ovabrw_rt_price as $key => $value ) {
				// start timestamp
				$start_timestamp = array_key_exists( $key, $ovabrw_start_timestamp ) ? strtotime( gmdate( 'Y-m-d', $ovabrw_start_timestamp[$key] ) ) : 0;

				// end timestamp
				$end_timestamp 	= array_key_exists( $key, $ovabrw_end_timestamp ) ? strtotime( gmdate( 'Y-m-d', $ovabrw_end_timestamp[$key] ) ) : 0;

				// check price type
				if ( $price_type == 'mixed' ) {
					$price_mixed = array_key_exists( $key, $ovabrw_rt_price_hour ) ? ( wc_price_calendar($ovabrw_rt_price_hour[$key]) . '<br>' . wc_price_calendar($value) ) : ( wc_price_calendar(0) . '<br>' . wc_price_calendar($value) );

					if ( in_array( $value, $prices ) ) {
						$special_time[$price_mixed.'<span hidden>'.$key.'</span>'] = [ $start_timestamp, $end_timestamp ];
					} else {
						$special_time[$price_mixed] = [ $start_timestamp, $end_timestamp ];
						array_push($prices, $value);
					}
				} else {
					if ( in_array( $value, $prices ) ) {
						$special_time[wc_price_calendar($value).'<span hidden>'.$key.'</span>'] = [ $start_timestamp, $end_timestamp ];
					} else {
						$special_time[wc_price_calendar($value)] = [ $start_timestamp, $end_timestamp ];
						array_push($prices, $value);
					}
				}
			}
		}
		
		return $special_time;
	}
}

// Get product template
if ( ! function_exists( 'ovabrw_get_product_template' ) ) {
	function ovabrw_get_product_template( $id ) {
		$template = get_option( 'ova_brw_template_elementor_template', 'default' );

		if ( empty( $id ) ) {
			return $template;
		}

		$products = wc_get_product( $id );

		if ( $products ) {
			$categories = $products->get_category_ids();

			if ( ! empty( $categories ) ) {
		        $term_id 	= reset( $categories );
		        $template_by_category = get_term_meta( $term_id, 'ovabrw_product_templates', true );

		        if ( $template_by_category && $template_by_category != 'global' ) {
		        	$template = $template_by_category;
		        }
		    }
		}

		return $template;
	}
}

// Check locations
if ( ! function_exists( 'ovabrw_check_location' ) ) {
	function ovabrw_check_location( $product_id = null, $pickup_loc = '', $dropoff_loc = '' ) {
		if ( ! $product_id ) return false;

		$data_pickup_loc 	= get_post_meta( $product_id, 'ovabrw_st_pickup_loc', true );
		$data_dropoff_loc 	= get_post_meta( $product_id, 'ovabrw_st_dropoff_loc', true );

		if ( ! empty( $data_pickup_loc ) && is_array( $data_pickup_loc ) ) {
			foreach( $data_pickup_loc as $k => $loc ) {
				if ( $loc === $pickup_loc && isset( $data_dropoff_loc[$k] ) && $data_dropoff_loc[$k] === $dropoff_loc ) {
					return true;
				}
			}
		} else {
			return true;
		}

		return false;
	}
}

// Check key in array
if ( ! function_exists( 'ovabrw_check_array' ) ) {
	function ovabrw_check_array( $args, $key ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			if ( isset( $args[$key] ) && $args[$key] ) {
				return true;
			}
		}

		return false;
	}
}

// Get input date for rental by distance
if ( ! function_exists( 'ovabrw_taxi_input_date' ) ) {
	function ovabrw_taxi_input_date( $date = '', $hour = '', $duration = 0 ) {
		$result = [
			'pickup_date_new' 	=> '',
			'pickoff_date_new' 	=> '',
		];

		if ( ! $date ) return $result;

		if ( $hour ) {
			$date .= ' ' . $hour;
		}

		if ( ! $duration ) $duration = 0;

		if ( strtotime( $date ) ) {
			$result['pickup_date_new'] 	= strtotime( $date );
			$result['pickoff_date_new'] = strtotime( $date ) + $duration;
		}

		return $result;
	}
}

// Get text distance
if ( ! function_exists( 'ovarw_taxi_get_distance_text' ) ) {
	function ovarw_taxi_get_distance_text( $distance = 0, $product_id = false ) {
		$price_by = 'km';

		if ( $product_id ) {
			$price_by = get_post_meta( $product_id, 'ovabrw_map_price_by', true );

			if ( ! $price_by ) $price_by = 'km';
		}

        if ( $distance ) {
        	if ( $price_by === 'km' ) {
        		$distance = apply_filters( 'ovabrw_ft_distance_text', round( $distance / 1000, 2 ), $product_id );
        	} else {
        		$distance = apply_filters( 'ovabrw_ft_distance_text', round( $distance / 1609.34, 2 ), $product_id );
        	}
        } else {
            $distance = 0;
        }

        return sprintf( esc_html__( '%s %s', 'ova-brw' ), $distance, $price_by );
	}
}

// Get text duration
if ( ! function_exists( 'ovarw_taxi_get_duration_text' ) ) {
	function ovarw_taxi_get_duration_text( $duration ) {
		$hour = $minute = 0;

        if ( $duration ) {
            $hour 	= absint( $duration / 3600 );
            $minute = round( ( $duration % 3600 ) / 60 );
        }

        return sprintf( esc_html__( '%sh%sm', 'ova-brw' ), $hour, $minute );
	}
}

// Get all font
if ( ! function_exists( 'ovabrw_get_all_font' ) ) {
	function ovabrw_get_all_font() {
		$font_file 	= OVABRW_PLUGIN_URI . 'assets/libs/google_font/api/google-fonts-alphabetical.json';
        $request 	= wp_remote_get( $font_file );

        if ( is_wp_error( $request ) ) {
            return "";
        }

        $body       = wp_remote_retrieve_body( $request );
        $content    = json_decode( $body );
        $all_fonts 	= $content->items;

        if ( get_option('ovabrw_glb_custom_font', '') != '' ) {

        	$glb_custom_font 	= str_replace( '\"', '"', get_option( 'ovabrw_glb_custom_font' ) );
            $list_custom_font 	= explode( '|', $glb_custom_font );

            foreach ( $list_custom_font as $key => $font ) {
                $cus_font = json_decode( $font );
                $cus_font_family = $cus_font['0'];
                $cus_font_weight = explode( ':', $cus_font['1'] );

                $all_fonts[] = json_decode(json_encode( array(
                    "kind"      => "webfonts#webfont",
                    "family"    => $cus_font_family,
                    "category"  => "sans-serif",
                    "variants"  => $cus_font_weight,
                ) ) );
            }
        }
        
        return $all_fonts;
	}
}

// Enable global typography
if ( ! function_exists( 'ovabrw_global_typography' ) ) {
	function ovabrw_global_typography() {
		$global_typography = get_option( 'ovabrw_enable_global_typography', 'yes' );

		if ( 'yes' === $global_typography ) return true;

		return false;
	}
}

// Is archive product
if ( ! function_exists( 'ovabrw_is_archive_product' ) ) {
	function ovabrw_is_archive_product() {
		if ( ovabrw_global_typography() ) {
			if ( is_product_category() ) {
				$terms = get_queried_object();

				if ( ! empty( $terms ) && is_object( $terms ) ) {
					$term_id = $terms->term_id;

					if ( $term_id ) {
						$display = get_term_meta( $term_id, 'ovabrw_cat_dis', true );

						if ( $display === 'rental' ) {
							return true;
						} else {
							return false;
						}
					}
				}
			}

			if ( is_product_taxonomy() ) {
				$display = get_option( 'ova_brw_display_product_taxonomy', 'rental' );

				if ( $display === 'rental' ) {
					return true;
				} else {
					return false;
				}
			}

			if ( is_shop() ) {
				$display = get_option( 'ova_brw_display_shop_page', 'rental' );

				if ( $display === 'rental' ) {
					return true;
				} else {
					return false;
				}
			}
		}

		return false;
	}
}

// Is rental product
if ( ! function_exists( 'ovabrw_is_rental_product' ) ) {
	function ovabrw_is_rental_product() {
		global $product;

		if ( $product && $product->is_type( 'ovabrw_car_rental' ) ) return true;

		return false;
	}
}

// Get Card Template
if ( ! function_exists( 'ovabrw_get_card_template' ) ) {
	function ovabrw_get_card_template() {
		$card_template = get_option( 'ovabrw_glb_card_template', 'card1' );

		if ( get_queried_object_id() ) {
			$term_card_template = get_term_meta( get_queried_object_id(), 'ovabrw_card_template', true );

			if ( $term_card_template ) $card_template = $term_card_template;
		}

		return apply_filters( 'ovabrw-ft-cart-template', $card_template );
	}
}

// Allow booking when booking time include date disable
if ( ! function_exists( 'ovabrw_allow_boooking_incl_disable_week_day' ) ) {
	function ovabrw_allow_boooking_incl_disable_week_day() {
		$allow = get_option( 'ova_brw_booking_form_disable_week_day', 'yes' );

		return $allow === 'yes' ? true : false;
	}
}

// Check table price
if ( ! function_exists( 'ovabrw_check_table_price' ) ) {
	function ovabrw_check_table_price( $product_id = null ) {
		if ( ! $product_id ) return false;

		$daily = ovabrw_p_weekdays( $product_id );
		if ( ! empty( $daily ) && is_array( $daily ) ) return true;

		$rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

		// Discount
		if ( 'day' === $rental_type || 'hour' === $rental_type || 'mixed' === $rental_type ) {
			$discount_min = get_post_meta( $product_id, 'ovabrw_global_discount_duration_val_min', true );
			if ( ! empty( $discount_min ) && is_array( $discount_min ) ) return true;
		}
		if ( 'period_time' === $rental_type ) {
			$peoftime_discount = get_post_meta( $product_id, 'ovabrw_petime_discount', true );
			if ( ! empty( $peoftime_discount ) && is_array( $peoftime_discount ) ) return true;
		}
		if ( 'taxi' === $rental_type ) {
			$discount_price = get_post_meta( $product_id, 'ovabrw_discount_distance_price', true );
			if ( ! empty( $discount_price ) && is_array( $discount_price ) ) return true;
		}
		
		// Special Time
		if ( 'day' === $rental_type || 'mixed' === $rental_type ) {
			$special_price = get_post_meta( $product_id, 'ovabrw_rt_price', true );
			if ( ! empty( $special_price ) && is_array( $special_price ) ) return true;
		}
		if ( 'hour' === $rental_type || 'mixed' === $rental_type ) {
			$special_price = get_post_meta( $product_id, 'ovabrw_rt_price_hour', true );
			if ( ! empty( $special_price ) && is_array( $special_price ) ) return true;
		}
		if ( 'taxi' === $rental_type ) {
			$special_price = get_post_meta( $product_id, 'ovabrw_st_price_distance', true );
			if ( ! empty( $special_price ) && is_array( $special_price ) ) return true;
		}
	}
}

// Get Price - Multi Currency
if ( ! function_exists( 'ovabrw_wc_price' ) ) {
	function ovabrw_wc_price( $price = null, $args = array(), $convert = true ) {
		$new_price = $price;

		if ( ! $price ) $new_price = 0;

		do_action( 'ovabrw_wc_price_before', $price, $args, $convert );

		$current_currency = isset( $args['currency'] ) && $args['currency'] ? $args['currency'] : false;

		// CURCY - Multi Currency for WooCommerce
		// WooCommerce Multilingual & Multicurrency
		if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) ) {
			$new_price = wmc_get_price( $price, $current_currency );
		} elseif ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
			if ( $convert ) {
				// WPML multi currency
	    		global $woocommerce_wpml;

	    		if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
	    			if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

			        $multi_currency     = $woocommerce_wpml->get_multi_currency();
			        $currency_options   = $woocommerce_wpml->get_setting( 'currency_options' );
			        $WMCP   			= new WCML_Multi_Currency_Prices( $multi_currency, $currency_options );
			        $new_price  		= $WMCP->convert_price_amount( $price, $current_currency );
			    }
			}
		} else {
			// nothing
		}

		do_action( 'ovabrw_wc_price_after', $price, $args, $convert );
		
		return apply_filters( 'ovabrw_wc_price', wc_price( $new_price, $args ), $price, $args, $convert );
	}
}

if ( ! function_exists( 'ovabrw_convert_price' ) ) {
	function ovabrw_convert_price( $price = null, $args = array(), $convert = true ) {
		$new_price = $price;

		if ( ! $price ) $new_price = 0;

		do_action( 'ovabrw_convert_price_before', $price, $args, $convert );

		$current_currency = isset( $args['currency'] ) && $args['currency'] ? $args['currency'] : false;

		// CURCY - Multi Currency for WooCommerce
		// WooCommerce Multilingual & Multicurrency
		if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) ) {
			$new_price = wmc_get_price( $price, $current_currency );
		} elseif ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
			if ( $convert ) {
				// WPML multi currency
	    		global $woocommerce_wpml;

	    		if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
	    			if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

			        $multi_currency     = $woocommerce_wpml->get_multi_currency();
			        $currency_options   = $woocommerce_wpml->get_setting( 'currency_options' );
			        $WMCP   			= new WCML_Multi_Currency_Prices( $multi_currency, $currency_options );
			        $new_price  		= $WMCP->convert_price_amount( $price, $current_currency );
			    }
			}
		} else {
			// nothing
		}

		do_action( 'ovabrw_convert_price_after', $price, $args, $convert );
		
		return apply_filters( 'ovabrw_convert_price', $new_price, $price, $args, $convert );
	}
}

if ( ! function_exists( 'ovabrw_convert_price_in_admin' ) ) {
	function ovabrw_convert_price_in_admin( $price = null, $currency_code = '' ) {
		$new_price = $price;

		if ( ! $price ) $new_price = 0;

		if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) ) {
			$setting = WOOMULTI_CURRENCY_F_Data::get_ins();

			/*Check currency*/
			$selected_currencies = $setting->get_list_currencies();
			$current_currency    = $setting->get_current_currency();

			if ( ! $currency_code || $currency_code === $current_currency ) {
				return $new_price;
			}

			if ( $new_price ) {
				if ( $currency_code && isset( $selected_currencies[ $currency_code ] ) ) {
					$new_price = $price * (float) $selected_currencies[ $currency_code ]['rate'];
				} else {
					$new_price = $price * (float) $selected_currencies[ $current_currency ]['rate'];
				}
			}
		} elseif ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
			// WPML multi currency
    		global $woocommerce_wpml;

    		if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
    			if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

		        $multi_currency     = $woocommerce_wpml->get_multi_currency();
		        $currency_options   = $woocommerce_wpml->get_setting( 'currency_options' );
		        $WMCP   			= new WCML_Multi_Currency_Prices( $multi_currency, $currency_options );
		        $new_price  		= $WMCP->convert_price_amount( $price, $currency_code );
		    }
		} else {
			// nothing
		}

		return apply_filters( 'ovabrw_convert_price_in_admin', $new_price, $price, $currency_code );
	}
}

// Check High-Performance Order Storage for Woocommerce
if ( ! function_exists( 'ovabrw_wc_custom_orders_table_enabled' ) ) {
	function ovabrw_wc_custom_orders_table_enabled() {
		if ( get_option( 'woocommerce_custom_orders_table_enabled', 'no' ) === 'yes' ) {
			return true;
		}

		return false;
	}
}

// reCAPTCHA type
if ( ! function_exists( 'ovabrw_get_recaptcha_type' ) ) {
	function ovabrw_get_recaptcha_type() {
		return get_option( 'ova_brw_recapcha_type', 'v3' );
	}
}

// reCAPTCHA site key
if ( ! function_exists( 'ovabrw_get_recaptcha_site_key' ) ) {
	function ovabrw_get_recaptcha_site_key() {
		if ( ovabrw_get_recaptcha_type() === 'v3' ) {
			return get_option( 'ova_brw_recapcha_v3_site_key', '' );
		} else {
			return get_option( 'ova_brw_recapcha_v2_site_key', '' );
		}
	}
}

// reCAPTCHA secret key
if ( ! function_exists( 'ovabrw_get_recaptcha_secret_key' ) ) {
	function ovabrw_get_recaptcha_secret_key() {
		if ( ovabrw_get_recaptcha_type() === 'v3' ) {
			return get_option( 'ova_brw_recapcha_v3_secret_key', '' );
		} else {
			return get_option( 'ova_brw_recapcha_v2_secret_key', '' );
		}
	}
}

// reCAPTCHA form
if ( ! function_exists( 'ovabrw_get_recaptcha_form' ) ) {
	function ovabrw_get_recaptcha_form( $form = '' ) {
		if ( get_option( 'ova_brw_recapcha_form', '' ) === 'both' ) return true;
		if ( get_option( 'ova_brw_recapcha_form', '' ) === $form ) return true;

		return false;
	}
}