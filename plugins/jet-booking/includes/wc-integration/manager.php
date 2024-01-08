<?php

namespace JET_ABAF\WC_Integration;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Manager {

	/**
	 * Data key.
	 *
	 * Holds booking data key.
	 *
	 * @access public
	 *
	 * @var string
	 */
	public $data_key = 'booking_data';

	/**
	 * Form data key.
	 *
	 * Holds JetForms/JetEngine Forms data key.
	 *
	 * @access public
	 *
	 * @var string
	 */
	public $form_data_key = 'booking_form_data';

	/**
	 * Form ID key.
	 *
	 * Holds JetForms/JetEngine Forms ID key.
	 *
	 * @access public
	 *
	 * @var string
	 */
	public $form_id_key = 'booking_form_id';

	/**
	 * Mode.
	 *
	 * Current WooCommerce mode holder.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @var Modes\Based|Modes\Plain|null
	 */
	public $mode = null;

	public function __construct() {

		if ( ! $this->has_woocommerce() ) {
			$this->reset_wc_related_settings();

			return;
		}

		$this->mode = 'plain' === jet_abaf()->settings->get( 'booking_mode' ) ? new Modes\Plain() : new Modes\Based();

		// Cart related.
		add_filter( 'woocommerce_get_item_data', [ $this, 'add_custom_item_meta' ], 10, 2 );

		// Order related.
		add_action( 'woocommerce_checkout_order_processed', [ $this, 'process_order' ], 10, 3 );
		add_action( 'woocommerce_store_api_checkout_order_processed', [ $this, 'process_order_by_api' ] );
		add_action( 'woocommerce_order_status_changed', [ $this, 'update_status_on_order_update' ], 10, 4 );

		// Format booking price in admin add/edit popups.
		add_filter( 'jet-booking/booking-total-price', function ( $price ) {
			return wc_price( floatval( $price ) );
		} );

	}

	/**
	 * Has WooCommerce.
	 *
	 * Check if WooCommerce plugin is enabled.
	 *
	 * @since  2.8.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function has_woocommerce() {
		return class_exists( '\WooCommerce' );
	}

	/**
	 * Reset WC related settings.
	 *
	 * @since  3.0.0.
	 * @access public
	 *
	 * @return void
	 */
	public function reset_wc_related_settings() {
		if ( filter_var( jet_abaf()->settings->get( 'wc_integration' ), FILTER_VALIDATE_BOOLEAN ) ) {
			jet_abaf()->settings->update( 'wc_integration', false, false );
		}
	}

	/**
	 * Add custom item meta.
	 *
	 * Adding custom booking data into cart meta data.
	 *
	 * @since  3.0.0 Refactored.
	 * @access public
	 *
	 * @param array $item_data      Data for each item in the cart.
	 * @param array $cart_item_data List with stored custom values.
	 *
	 * @return mixed
	 */
	public function add_custom_item_meta( $item_data, $cart_item_data ) {

		if ( ! empty( $cart_item_data[ $this->data_key ] ) ) {
			$form_data = ! empty( $cart_item_data[ $this->form_data_key ] ) ? $cart_item_data[ $this->form_data_key ] : [];
			$form_id   = ! empty( $cart_item_data[ $this->form_id_key ] ) ? $cart_item_data[ $this->form_id_key ] : null;

			$item_data = array_merge( $item_data, $this->get_formatted_info( $cart_item_data[ $this->data_key ], $form_data, $form_id ) );
		}

		return $item_data;

	}

	/**
	 * Process order.
	 *
	 * Process new order creation.
	 *
	 * @param int    $order_id Process order ID.
	 * @param array  $data     Posted data from the checkout form.
	 * @param object $order    WC order object instance.
	 *
	 * @return void
	 */
	public function process_order( $order_id, $data, $order ) {
		foreach ( WC()->cart->get_cart_contents() as $item ) {
			if ( ! empty( $item[ $this->data_key ] ) ) {
				$this->set_order_data( $item[ $this->data_key ], $order_id, $order, $item );
			}
		}
	}

	/**
	 * Process order by API.
	 *
	 * Process new order creation with new checkout block API.
	 *
	 * @since  2.7.1
	 * @access public
	 *
	 * @param object $order WC order instance.
	 *
	 * @return void
	 */
	public function process_order_by_api( $order ) {
		$this->process_order( $order->get_id(), [], $order );
	}

	/**
	 * Update status on order update.
	 *
	 * Update an booking status on related WC order update.
	 *
	 * @since  3.0.0 Refactored.
	 * @access public
	 *
	 * @param int    $order_id   WC order ID.
	 * @param string $old_status Old status name.
	 * @param string $new_status New status name.
	 * @param object $order      WC order object instance.
	 *
	 * @return void
	 */
	public function update_status_on_order_update( $order_id, $old_status, $new_status, $order ) {

		$bookings = jet_abaf()->db->query( [ 'order_id' => $order_id ] );

		if ( ! empty( $bookings ) ) {
			foreach ( $bookings as $booking ) {
				$this->set_order_data( $booking, $order_id, $order );
			}
		}

	}

	/**
	 * Get formatted info.
	 *
	 * Get formatted booking information.
	 *
	 * @since  3.0.0 Refactored.
	 * @access public
	 *
	 * @param array      $data      Booking data list.
	 * @param array      $form_data Submitted form data list.
	 * @param string|int $form_id   Submitted form id.
	 *
	 * @return array
	 */
	public function get_formatted_info( $data = [], $form_data = [], $form_id = null ) {

		$pre_cart_info = apply_filters( 'jet-booking/wc-integration/pre-cart-info', false, $data, $form_data, $form_id );

		if ( $pre_cart_info ) {
			return $pre_cart_info;
		}

		$from = ! empty( $data['check_in_date'] ) ? absint( $data['check_in_date'] ) : false;
		$to   = ! empty( $data['check_out_date'] ) ? absint( $data['check_out_date'] ) : false;

		if ( ! $from || ! $to ) {
			return [];
		}

		$result[] = [
			'key'     => __( 'Check In', 'jet-booking' ),
			'display' => date_i18n( get_option( 'date_format' ), $from ),
		];

		$result[] = [
			'key'     => __( 'Check Out', 'jet-booking' ),
			'display' => date_i18n( get_option( 'date_format' ), $to ),
		];

		return apply_filters( 'jet-booking/wc-integration/cart-info', $result, $data, $form_data, $form_id );

	}

	/**
	 * Setup order data.
	 *
	 * @since  2.8.0 Added `wc_sync_orders` option handling.
	 * @access public
	 *
	 * @param array      $data      Posted booking data from the checkout form.
	 * @param string|int $order_id  Process order ID.
	 * @param object     $order     WC order object instance.
	 * @param array      $cart_item Processed cart item data list.
	 *
	 * @return void
	 */
	public function set_order_data( $data, $order_id, $order, $cart_item = [] ) {

		$booking_id = ! empty( $data['booking_id'] ) ? absint( $data['booking_id'] ) : false;

		if ( ! $booking_id ) {
			return;
		}

		do_action( 'jet-booking/wc-integration/before-set-order-data' );

		$booking_statuses = jet_abaf()->statuses->get_statuses();

		jet_abaf()->db->update_booking( $booking_id, [
			'order_id' => $order_id,
			'status'   => isset( $booking_statuses[ $order->get_status() ] ) ? $order->get_status() : 'created',
		] );

		do_action( 'jet-booking/wc-integration/process-order', $order_id, $order, $cart_item );

	}

	/**
	 * Get formatted price.
	 *
	 * Returns a formatted string representation for a numeric price value.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @param string|int $price Price value.
	 *
	 * @return string
	 */
	public function get_formatted_price( $price ) {
		return sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), number_format( floatval( $price ), wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() ) );
	}

}