<?php
/**
 * Booking base class.
 *
 * @package JET_ABAF\Resources
 */

namespace JET_ABAF\Resources;

class Booking {

	/**
	 * Booking ID.
	 *
	 * @var int
	 */
	protected $ID = 0;

	/**
	 * Status.
	 *
	 * @var string
	 */
	protected $status = 'pending';

	/**
	 * Apartment ID.
	 *
	 * @var int
	 */
	protected $apartment_id = null;

	/**
	 * Apartment unit ID.
	 *
	 * @var int
	 */
	protected $apartment_unit = null;

	/**
	 * Check in date.
	 *
	 * @var int
	 */
	protected $check_in_date = null;

	/**
	 * Check out date.
	 *
	 * @var int
	 */
	protected $check_out_date = null;

	/**
	 * Related order ID.
	 *
	 * @var int
	 */
	protected $order_id = null;

	/**
	 * Calendar import ID.
	 *
	 * @var string
	 */
	protected $import_id = '';

	/**
	 * Additional database table columns.
	 *
	 * @var array
	 */
	protected $columns = [];

	public function __construct( $booking ) {

		$this->set_id( $booking['booking_id'] );
		$this->set_status( $booking['status'] );
		$this->set_apartment_id( $booking['apartment_id'] );
		$this->set_apartment_unit( $booking['apartment_unit'] );
		$this->set_check_in_date( $booking['check_in_date'] );
		$this->set_check_out_date( $booking['check_out_date'] );
		$this->set_order_id( $booking['order_id'] );
		$this->set_import_id( $booking['import_id'] );

		if ( ! empty( jet_abaf()->settings->get_clean_columns() ) ) {
			$columns = [];

			foreach ( jet_abaf()->settings->get_clean_columns() as $column ) {
				if ( isset( $booking[ $column ] ) ) {
					$columns[ $column ] = $booking[ $column ];
				}
			}

			$this->set_columns( $columns );
		}

	}

	/**
	 * Get ID.
	 *
	 * Returns booking ID.
	 *
	 * @since   3.1.0
	 * @access  public
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->ID;
	}

	/**
	 * Get status.
	 *
	 * Returns booking status.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Get apartment id.
	 *
	 * Returns booking instance post type ID.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return int|null
	 */
	public function get_apartment_id() {
		return $this->apartment_id;
	}

	/**
	 * Get apartment unit.
	 *
	 * Returns booking instance post type unit ID.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return int|null
	 */
	public function get_apartment_unit() {
		return $this->apartment_unit;
	}

	/**
	 * Get check-in date.
	 *
	 * Returns booking check-in date.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return int|null
	 */
	public function get_check_in_date() {
		return $this->check_in_date;
	}

	/**
	 * Get check-out date.
	 *
	 * Returns booking check-out date.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return int|null
	 */
	public function get_check_out_date() {
		return $this->check_out_date;
	}

	/**
	 * Get order ID.
	 *
	 * Returns booking related order ID.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return int|null
	 */
	public function get_order_id() {
		return $this->order_id;
	}

	/**
	 * Get import ID.
	 *
	 * Returns booking instance post type calendar import ID.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_import_id() {
		return $this->import_id;
	}

	/**
	 * Get columns.
	 *
	 * Return the list of additional database table columns.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_columns() {
		return $this->columns;
	}

	/**
	 * Get column.
	 *
	 * Returns the value from specified additional database table column.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param string $name Name of column to get.
	 *
	 * @return mixed
	 */
	public function get_column( $name ) {

		$value = null;

		if ( array_key_exists( $name, $this->columns ) ) {
			$value = $this->columns[ $name ];
		}

		return $value;

	}

	/**
	 * Set ID.
	 *
	 * Set booking ID.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param int $id Booking ID.
	 *
	 * @return void
	 */
	public function set_id( $id ) {
		$this->ID = $id;
	}

	/**
	 * Set status.
	 *
	 * Set booking status.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param string $status Booking status.
	 *
	 * @return void
	 */
	public function set_status( $status ) {
		$this->status = $status;
	}

	/**
	 * Set apartment ID.
	 *
	 * Set booking instance post type ID.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param int $apartment_id Booking instance post type ID.
	 *
	 * @return void
	 */
	public function set_apartment_id( $apartment_id ) {
		$this->apartment_id = $apartment_id;
	}

	/**
	 * Set apartment unit.
	 *
	 * Set booking instance post type unit ID.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param int $apartment_unit Booking instance post type unit ID.
	 *
	 * @return void
	 */
	public function set_apartment_unit( $apartment_unit ) {
		$this->apartment_unit = $apartment_unit;
	}

	/**
	 * Set check-in date.
	 *
	 * Set booking check-in date.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param int $check_in_date Booking check-in date.
	 *
	 * @return void
	 */
	public function set_check_in_date( $check_in_date ) {
		$this->check_in_date = $check_in_date;
	}

	/**
	 * Set check-out date.
	 *
	 * Set booking check-out date.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param int $check_out_date Booking check-out date.
	 *
	 * @return void
	 */
	public function set_check_out_date( $check_out_date ) {
		$this->check_out_date = $check_out_date;
	}

	/**
	 * Set order ID.
	 *
	 * Set booking related order ID.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param int $order_id Booking related order ID.
	 *
	 * @return void
	 */
	public function set_order_id( $order_id ) {
		$this->order_id = $order_id;
	}

	/**
	 * Set import ID.
	 *
	 * Set booking instance post type calendar import ID.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param string $import_id Booking instance post type calendar import ID.
	 *
	 * @return void
	 */
	public function set_import_id( $import_id ) {
		$this->import_id = $import_id;
	}

	/**
	 * Set columns.
	 *
	 * Set additional database table columns.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param array $columns Additional database table columns.
	 *
	 * @return void
	 */
	public function set_columns( $columns ) {
		$this->columns = $columns;
	}

}
