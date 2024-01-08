<?php

namespace JET_ABAF\Rest_API;

class Endpoint_Update_Booking extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'update-booking';
	}

	public function callback( $request ) {

		$params  = $request->get_params();
		$item_id = ! empty( $params['id'] ) ? absint( $params['id'] ) : 0;
		$item    = ! empty( $params['item'] ) ? $params['item'] : [];

		if ( ! empty( $params['calculateTotals'] ) ) {
			wp_cache_set( 'calculate_booking_totals_' . $item_id, $params['calculateTotals'] );
		}

		$not_allowed = [
			'booking_id',
			'order_id',
			'check_in_date_timestamp',
			'check_out_date_timestamp',
		];

		if ( empty( $item['check_in_date'] ) || empty( $item['check_out_date'] ) ) {
			return rest_ensure_response( [
				'success' => false,
				'data'    => __( 'Incorrect item data', 'jet-booking' ),
			] );
		}

		foreach ( $not_allowed as $key ) {
			if ( isset( $item[ $key ] ) ) {
				unset( $item[ $key ] );
			}
		}

		if ( empty( $item ) ) {
			return rest_ensure_response( [
				'success' => false,
				'data'    => __( 'No data to update', 'jet-booking' ),
			] );
		}

		$item['check_in_date']  = strtotime( $item['check_in_date'] );
		$item['check_out_date'] = strtotime( $item['check_out_date'] );

		$apartment_units = jet_abaf()->db->get_apartment_units( $item['apartment_id'] );

		if ( ! empty( $apartment_units ) ) {
			$apartment_unit = jet_abaf()->db->get_apartment_unit( $item['apartment_id'], $item['apartment_unit'] );

			if ( empty( $apartment_unit ) ) {
				$item['apartment_unit'] = jet_abaf()->db->get_available_unit( $item );
			}
		}

		$is_available       = jet_abaf()->db->booking_availability( $item, $item_id );
		$is_dates_available = jet_abaf()->db->is_booking_dates_available( $item, $item_id );

		if ( ! $is_available && ! $is_dates_available ) {
			ob_start();

			echo __( 'Selected dates are not available.', 'jet-booking' ) . '<br>';

			if ( jet_abaf()->db->latest_result ) {
				echo __( 'Overlapping bookings: ', 'jet-booking' );

				$result = [];

				foreach ( jet_abaf()->db->latest_result as $ob ) {
					if ( absint( $ob['booking_id'] ) !== $item_id ) {
						if ( ! empty( $ob['order_id'] ) ) {
							$result[] = sprintf( '<a href="%s" target="_blank">#%d</a>', get_edit_post_link( $ob['order_id'] ), $ob['order_id'] );
						} else {
							$result[] = '#' . $ob['booking_id'];
						}
					}
				}

				echo implode( ', ', $result ) . '.';
			}

			return rest_ensure_response( [
				'success'              => false,
				'overlapping_bookings' => true,
				'html'                 => ob_get_clean(),
				'data'                 => __( 'Can`t update this item', 'jet-booking' ),
			] );

		}

		jet_abaf()->db->update_booking( $item_id, $item );

		return rest_ensure_response( [ 'success' => true ] );

	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELTE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'POST';
	}

	/**
	 * Get query param. Regex with query parameters
	 *
	 * @return string
	 */
	public function get_query_params() {
		return '(?P<id>[\d]+)';
	}

}