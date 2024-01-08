<?php

namespace JET_ABAF;

class Tools {

	/**
	 * Date format JS to PHP.
	 *
	 * Returns PHP date format from JavaScript format.
	 *
	 * @access public
	 *
	 * @param null  $format JS date format.
	 * @param array $mask   Provided transform mask.
	 *
	 * @return mixed|string|string[]
	 */
	public static function date_format_js_to_php( $format = null, $mask = [] ) {

		if ( ! $format ) {
			return '';
		}

		$mask = ! empty( $mask ) ? $mask : [
			'/HH{1}/'   => 'H',
			'/hh{1}/'   => 'h',
			'/YYYY{1}/' => 'Y',
			'/YY{1}/'   => 'y',
			'/MMMM{1}/' => 'F',
			'/MMM{1}/'  => 'M',
			'/MM{1}/'   => 'm',
			'/M{1}/'    => 'n',
			'/mm{1}/'   => 'i',
			'/DD{1}/'   => 'd',
			'/D{1}/'    => 'j',
			'/dddd{1}/' => 'l',
			'/ddd{1}/'  => 'D',
		];

		foreach ( $mask as $key => $value ) {
			$format = preg_replace( $key, $value, $format );
		}

		return $format;

	}

	/**
	 * Date format PHP to JS.
	 *
	 * Returns JavaScript date format from PHP format.
	 *
	 * @access public
	 *
	 * @param null  $format PHP date format.
	 * @param array $mask   Provided transform mask.
	 *
	 * @return mixed|string|string[]
	 */
	public static function date_format_php_to_js( $format = null, $mask = [] ) {

		if ( ! $format ) {
			return '';
		}

		$mask = ! empty( $mask ) ? $mask : [
			'/H{1}/' => 'HH',
			'/h{1}/' => 'hh',
			'/Y{1}/' => 'YYYY',
			'/y{1}/' => 'YY',
			'/M{1}/' => 'MMM',
			'/n{1}/' => 'M',
			'/m{1}/' => 'MM',
			'/F{1}/' => 'MMMM',
			'/d{1}/' => 'DD',
			'/D{1}/' => 'ddd',
			'/j{1}/' => 'D',
			'/l{1}/' => 'dddd',
			'/i{1}/' => 'mm',
			'/g{1}/' => 'hh',
		];

		foreach ( $mask as $key => $value ) {
			$format = preg_replace( $key, $value, $format );
		}

		return $format;

	}

	/**
	 * Get booking posts.
	 *
	 * Returns list of all created bookings posts.
	 *
	 * @since  2.6.1
	 * @access public
	 *
	 * @return array|int[]|\WP_Post[]
	 */
	public function get_booking_posts() {

		$post_type = jet_abaf()->settings->get( 'apartment_post_type' );

		if ( ! $post_type ) {
			return [];
		}

		$args = apply_filters( 'jet-booking/tools/post-type-args', [
			'post_type'      => $post_type,
			'posts_per_page' => - 1,
		] );

		$posts = get_posts( $args );

		if ( ! $posts ) {
			return [];
		}

		return $posts;

	}

	/**
	 * Get unavailable apartments.
	 *
	 * @since 2.0.0
	 * @since 2.6.1 New handling.
	 * @since 3.1.0 Moved to tools class.
	 *
	 * @param string $from Range start date in timestamp.
	 * @param string $to   Range end date in timestamp.
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function get_unavailable_apartments( $from, $to ) {

		$posts = $this->get_booking_posts();

		if ( empty( $posts ) ) {
			return [];
		}

		$booked_apartments = [];

		foreach ( $posts as $post ) {
			$invalid_dates = $this->get_invalid_dates_in_range( $from, $to, $post->ID );

			if ( ! empty( $invalid_dates ) ) {
				$booked_apartments[] = $post->ID;
			}
		}

		return $booked_apartments;

	}

	/**
	 * Get invalid dates in range.
	 *
	 * Returns list of booked, disabled and off dates in defined range.
	 *
	 * @since  2.5.5
	 * @since  2.6.1 Added `$instance_id` parameter.
	 * @since  2.7.1 Checkout only compatibility.
	 * @access public
	 *
	 * @param string        $from        First date of range in timestamp.
	 * @param string        $to          Last date of range in timestamp.
	 * @param string|number $instance_id Booking instance ID.
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function get_invalid_dates_in_range( $from, $to, $instance_id ) {

		$start         = new \DateTime( date( 'Y-m-d', $from ) );
		$end           = new \DateTime( date( 'Y-m-d', $to ) );
		$period        = new \DatePeriod( $start, new \DateInterval( 'P1D' ), $end->modify( '+1 day' ) );
		$booked_dates  = jet_abaf()->engine_plugin->get_off_dates( $instance_id );
		$disabled_days = jet_abaf()->engine_plugin->get_days_by_rule( $instance_id );
		$booked_range  = [];

		foreach ( $period as $key => $value ) {
			if ( in_array( $value->format( 'Y-m-d' ), $booked_dates ) || in_array( $value->format( 'w' ), $disabled_days ) ) {
				$booked_range[] = $value->format( 'Y-m-d' );
			}
		}

		sort( $booked_range );

		$days_off = jet_abaf()->engine_plugin->get_booking_days_off( $instance_id );

		if ( jet_abaf()->settings->checkout_only_allowed() ) {
			if ( false !== ( $index = array_search( date( 'Y-m-d', $to ), $booked_range ) ) && 0 === $index && ! in_array( $booked_range[ $index ], $days_off ) && ! in_array( date( 'N', $to ), $disabled_days ) ) {
				unset( $booked_range[ $index ] );
			}
		}

		return array_values( $booked_range );

	}

	/**
	 * Get field default value.
	 *
	 * Returns check in check out field default values.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @param string     $value   Initial value.
	 * @param string     $format  Date format.
	 * @param string|int $post_id Queried post ID.
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function get_field_default_value( $value, $format, $post_id ) {

		$check_in_days  = jet_abaf()->engine_plugin->get_days_by_rule( $post_id, 'check_in' );
		$check_out_days = jet_abaf()->engine_plugin->get_days_by_rule( $post_id, 'check_out' );

		if ( ! empty( $check_in_days ) && 7 > count( $check_in_days ) || ! empty( $check_out_days ) && 7 > count( $check_out_days ) || jet_abaf()->settings->get( 'weekly_bookings' ) ) {
			return [];
		}

		$result         = [];
		$store_type     = jet_abaf()->settings->get( 'filters_store_type' );
		$searched_dates = jet_abaf()->stores->get_store( $store_type )->get( 'searched_dates' );
		$value          = $searched_dates ?: $value;

		if ( ! trim( $value ) ) {
			return $result;
		}

		$value = explode( ' - ', $value );

		if ( ! empty( $value[0] ) && \Jet_Engine_Tools::is_valid_timestamp( $value[0] ) && ! empty( $value[1] ) && \Jet_Engine_Tools::is_valid_timestamp( $value[1] ) ) {
			$checkin          = date( 'Y-m-d', $value[0] );
			$checkout         = date( 'Y-m-d', $value[1] );
			$period_start     = new \DateTime( $checkin );
			$period_end       = new \DateTime( $checkout );
			$period           = jet_abaf()->engine_plugin->is_per_nights_booking() ? $period_start->diff( $period_end ) : $period_start->diff( $period_end->modify( '+1 day' ) );
			$min_days         = jet_abaf()->engine_plugin->get_config_option( $post_id, 'min_days' );
			$max_days         = jet_abaf()->engine_plugin->get_config_option( $post_id, 'max_days' );
			$start_day_offset = jet_abaf()->engine_plugin->get_config_option( $post_id, 'start_day_offset' );

			if ( $min_days && $period->days < $min_days || $max_days && $period->days > $max_days || $period->days <= $start_day_offset ) {
				return $result;
			}

			$booked_range = $this->get_invalid_dates_in_range( $value[0], $value[1], $post_id );

			if ( $checkin >= date( 'Y-m-d' ) && ! ( in_array( $checkin, $booked_range ) && in_array( $checkout, $booked_range ) ) ) {
				if ( in_array( $checkin, $booked_range ) ) {
					$checkin = strtotime( end( $booked_range ) . ' + 1 day' );
					reset( $booked_range );
				} else {
					$checkin = $value[0];
				}

				if ( in_array( $checkout, $booked_range ) ) {
					$checkout = strtotime( $booked_range[0] . ' - 1 day' );
				} else {
					if ( ! empty( $booked_range ) && ! in_array( date( 'Y-m-d', $value[0] ), $booked_range ) ) {
						$checkout = strtotime( $booked_range[0] . ' - 1 day' );
					} else {
						$checkout = $value[1];
					}
				}

				$format = self::date_format_js_to_php( $format );

				$result['checkin']  = date( $format, $checkin );
				$result['checkout'] = jet_abaf()->settings->is_one_day_bookings( $post_id ) ? $result['checkin'] : date( $format, $checkout );
			}
		}

		return $result;

	}

}
