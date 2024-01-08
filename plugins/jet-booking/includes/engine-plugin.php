<?php

namespace JET_ABAF;

use JET_ABAF\Form_Fields\Check_In_Out_Render;
use JET_ABAF\Vendor\Actions_Core\Smart_Notification_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Engine_Plugin {

	use Apartment_Booking_Trait;
	use Smart_Notification_Trait;

	private $done = false;
	private $deps_added = false;
	private $booked_dates = [];
	public $default = false;
	public $config = false;
	public $namespace = 'jet-form';

	public function __construct() {

		add_action( 'wp_ajax_jet_booking_check_available_units_count', [ $this, 'check_available_units_count' ] );
		add_action( 'wp_ajax_nopriv_jet_booking_check_available_units_count', [ $this, 'check_available_units_count' ] );

		// Price calculation and display after date selection in admin area for add & edit popups.
		add_action( 'wp_ajax_jet_booking_product_get_total_price', [ $this, 'get_booking_total_price' ] );

		add_filter( 'jet-engine/listings/macros-list', [ $this, 'register_macros' ] );
		add_filter( 'jet-engine/listing/current-object-title', [ $this, 'get_current_booking_object_title' ], 10, 2 );
		add_filter( 'jet-engine/macros/current-meta', [ $this, 'get_current_booking_meta' ], 10, 3 );

		if ( 'plain' === jet_abaf()->settings->get( 'booking_mode' ) ) {
			// Register field for booking form.
			add_filter( 'jet-engine/forms/booking/field-types', [ $this, 'register_dates_field' ] );
			add_action( 'jet-engine/forms/edit-field/before', [ $this, 'edit_fields' ] );
			add_action( 'jet-engine/forms/editor/macros-list', [ $this, 'add_macros_list' ] );

			// Register notification for booking form.
			add_filter( 'jet-engine/forms/booking/notification-types', [ $this, 'register_booking_notification' ] );
			add_action( 'jet-engine/forms/booking/notifications/fields-after', [ $this, 'notification_fields' ] );

			add_filter( 'jet-engine/calculated-data/ADVANCED_PRICE', [ $this, 'macros_advanced_price' ] );
			add_filter( 'jet-engine/forms/gateways/notifications-before', [ $this, 'before_form_gateway' ], 1, 2 );
			add_filter( 'jet-engine/forms/handler/query-args', [ $this, 'handler_query_args' ], 10, 3 );
			add_action( 'jet-engine/forms/gateways/on-payment-success', [ $this, 'on_gateway_success' ], 10, 3 );

			$check_in_out = new Check_In_Out_Render();
			// Add form field template.
			add_action( 'jet-engine/forms/booking/field-template/check_in_out', [ $check_in_out, 'getFieldTemplate' ], 10, 3 );

			// Register notification handler.
			add_filter( 'jet-engine/forms/booking/notification/apartment_booking', [ $this, 'do_action' ], 1, 2 );
		}

	}

	/**
	 * Check available units count.
	 *
	 * Check available units count for passed/selected dates.
	 *
	 * @since  2.5.2
	 * @access public
	 *
	 * @return void
	 */
	public function check_available_units_count() {

		$booking   = $_POST['booking'] ?? [];
		$all_units = jet_abaf()->db->get_apartment_units( $booking['apartment_id'] );

		if ( empty( $all_units ) || empty( $fields['check_out_date'] ) || empty( $fields['check_in_date'] ) ) {
			wp_send_json_error();
		}

		$booked_units = jet_abaf()->db->get_booked_units( $booking );

		if ( empty( $booked_units ) ) {
			wp_send_json_success( [ 'count' => count( $all_units ) ] );
		}

		$available_units = jet_abaf()->db->get_available_units( $booking );

		wp_send_json_success( [ 'count' => count( $available_units ) ] );

	}

	/**
	 * Get booking total price.
	 *
	 * Price calculation and display after date selection in admin area for add & edit popups.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function get_booking_total_price() {

		if ( empty( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'jet-abaf-bookings' ) ) {
			wp_send_json_error();
		}

		$booking = $_POST['booking'] ?? [];

		if ( empty( $booking ) ) {
			wp_send_json_error();
		}

		$booking['check_in_date']  = strtotime( $booking['check_in_date'] );
		$booking['check_out_date'] = strtotime( $booking['check_out_date'] );

		$price = new Price( $booking['apartment_id'] );

		$response['price'] = apply_filters( 'jet-booking/booking-total-price', $price->get_booking_price( $booking ) );

		wp_send_json_success( $response );

	}

	/**
	 * Register dates fields.
	 *
	 * Register specific booking field type for JetEngine forms.
	 *
	 * @access public
	 *
	 * @param array $fields Fields types list.
	 *
	 * @return mixed
	 */
	public function register_dates_field( $fields ) {
		$fields['check_in_out'] = __( 'Check-in/check-out dates', 'jet-booking' );

		return $fields;
	}

	/**
	 * Edit fields.
	 *
	 * Render additional edit field for dates field.
	 *
	 * @since  3.0.0 Moved to the separate template.
	 * @access public
	 *
	 * @return void
	 */
	public function edit_fields() {
		require_once JET_ABAF_PATH . 'templates/admin/jet-engine-forms/field-edit.php';
	}

	/**
	 * Add macros list.
	 *
	 * Adds a macro description to the calculator field.
	 *
	 * @access public
	 *
	 * @return void
	 */
	function add_macros_list() {
		?>
		<br>
		<div><b><?php _e( 'Booking macros:', 'jet-booking' ); ?></b></div>
		<div>
			<i>%ADVANCED_PRICE::_check_in_out%</i> - <?php _e( 'The macro will return the advanced rate times the number of days booked.', 'jet-booking' ); ?>
			<br>
			<b>_check_in_out</b> <?php _e( ' - is the name of the field that returns the number of days booked.', 'jet-booking' ); ?>
		</div><br>
		<div>
			<i>%META::_apartment_price%</i> - <?php esc_html_e( 'Macro returns price per 1 day / night', 'jet-booking' ); ?>
		</div>
		<?php
	}

	/**
	 * Register booking notifications.
	 *
	 * Register specific booking notification type for JetEngine forms.
	 *
	 * @param array $notifications Forms notifications list.
	 *
	 * @return mixed
	 */
	public function register_booking_notification( $notifications ) {
		$notifications['apartment_booking'] = __( 'Apartment booking', 'jet-booking' );

		return $notifications;
	}

	/**
	 * Notification fields.
	 *
	 * Render additional JetEngine forms notification fields.
	 *
	 * @since  3.0.0 Moved to the separate template.
	 * @access public
	 *
	 * @return void
	 */
	public function notification_fields() {
		require_once JET_ABAF_PATH . 'templates/admin/jet-engine-forms/notification-edit.php';
	}

	/**
	 * Macros advanced price.
	 *
	 * Macros %ADVANCED_PRICE% processing in the calculator field
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function macros_advanced_price( $macros ) {
		return $macros;
	}

	/**
	 * Before form gateway.
	 *
	 * Set booking notification before gateway.
	 *
	 * @since  3.0.0 New naming.
	 * @access public
	 *
	 * @param array $stored_notifications List of stored notifications.
	 * @param array $notifications        List of all notifications.
	 *
	 * @return array
	 */
	public function before_form_gateway( $stored_notifications, $notifications ) {

		foreach ( $notifications as $index => $notification ) {
			if ( 'apartment_booking' === $notification['type'] && ! in_array( $index, $stored_notifications ) ) {
				$stored_notifications[] = $index;
			}
		}

		return $stored_notifications;

	}

	/**
	 * Handle query args.
	 *
	 * @since  3.0.0 New naming.
	 * @access public
	 *
	 * @param array  $query_args List of query arguments.
	 * @param array  $args       List of handler arguments.
	 * @param object $handler    Handler instance.
	 *
	 * @return mixed
	 */
	public function handler_query_args( $query_args, $args, $handler ) {

		$field_name = false;

		foreach ( $handler->form_fields as $field ) {
			if ( 'check_in_out' === $field['type'] ) {
				$field_name = $field['name'];
			}
		}

		if ( $field_name ) {
			$query_args['new_date'] = $handler->notifcations->data[ $field_name ];
		}

		return $query_args;

	}

	/**
	 * On gateway success.
	 *
	 * Finalize booking on internal JetEngine form gateway success.
	 *
	 * @access public
	 *
	 * @param string|int $form_id   Form ID.
	 * @param array      $settings  Settings array.
	 * @param array      $form_data Form data array.
	 *
	 * @return void
	 */
	public function on_gateway_success( $form_id, $settings, $form_data ) {
		if ( ! empty( $form_data['booking_id'] ) ) {
			jet_abaf()->db->update_booking( $form_data['booking_id'], [ 'status' => 'completed' ] );
		}
	}

	/**
	 * Is per night bookings.
	 *
	 * Check if per nights booking mode enable.
	 *
	 * @since  2.8.0 Optimization.
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_per_nights_booking() {

		$period = jet_abaf()->settings->get( 'booking_period' );

		if ( ! $period || 'per_nights' === $period ) {
			return true;
		}

		return false;

	}

	public function enqueue_deps( $post_id ) {

		if ( ! $post_id || $this->deps_added ) {
			return;
		}

		do_action( 'jet-booking/assets/before' );

		ob_start();

		include JET_ABAF_PATH . 'assets/js/booking-init.js';

		$init_datepicker = ob_get_clean();
		$handle          = 'jquery-date-range-picker';

		wp_register_script(
			'jet-plugins',
			JET_ABAF_URL . 'assets/lib/jet-plugins/jet-plugins.js',
			[ 'jquery' ],
			'1.1.0',
			true
		);

		wp_register_script(
			'moment-js',
			JET_ABAF_URL . 'assets/lib/moment/js/moment.js',
			array(),
			'2.4.0',
			true
		);

		wp_enqueue_script(
			$handle,
			JET_ABAF_URL . 'assets/lib/jquery-date-range-picker/js/daterangepicker.min.js',
			[ 'jquery', 'moment-js', 'jet-plugins' ],
			JET_ABAF_VERSION,
			true
		);

		wp_add_inline_script( $handle, $init_datepicker );

		$weekly_bookings  = jet_abaf()->settings->is_weekly_bookings( $post_id );
		$week_offset      = false;
		$one_day_bookings = false;

		if ( ! $weekly_bookings ) {
			$one_day_bookings = Plugin::instance()->settings->is_one_day_bookings( $post_id );
		} else {
			$week_offset = $this->get_config_option( $post_id, 'week_offset' );
		}

		if ( $weekly_bookings || $one_day_bookings ) {
			$this->default = false;
		}

		$css_url = add_query_arg(
			[ 'v' => JET_ABAF_VERSION ],
			JET_ABAF_URL . 'assets/lib/jquery-date-range-picker/css/daterangepicker.css'
		);

		$booked_dates    = $this->get_off_dates( $post_id );
		$apartment_price = new Price( $post_id );

		wp_localize_script( $handle, 'JetABAFData', apply_filters( 'jet-booking/assets/config', [
			'css_url'          => $css_url,
			'booked_dates'     => $booked_dates,
			'booked_next'      => $this->get_next_booked_dates( $booked_dates ),
			'checkout_only'    => Plugin::instance()->settings->checkout_only_allowed(),
			'days_off'         => $this->get_booking_days_off( $post_id ),
			'disabled_days'    => $this->get_days_by_rule( $post_id ),
			'check_in_days'    => $this->get_days_by_rule( $post_id, 'check_in' ),
			'check_out_days'   => $this->get_days_by_rule( $post_id, 'check_out' ),
			'custom_labels'    => jet_abaf()->settings->get( 'use_custom_labels' ),
			'labels'           => apply_filters( 'jet-booking/compatibility/translate-labels', jet_abaf()->settings->get_labels() ),
			'weekly_bookings'  => $weekly_bookings,
			'week_offset'      => $week_offset,
			'one_day_bookings' => $one_day_bookings,
			'per_nights'       => $this->is_per_nights_booking(),
			'start_day_offset' => $this->get_config_option( $post_id, 'start_day_offset' ),
			'min_days'         => ! empty( $this->get_config_option( $post_id, 'min_days' ) ) ? $this->get_config_option( $post_id, 'min_days' ) : ( $this->is_per_nights_booking() ? 1 : '' ),
			'max_days'         => $this->get_config_option( $post_id, 'max_days' ),
			'bace_price'       => [
				'price'         => $apartment_price->get_default_price(),
				'price_rates'   => $apartment_price->rates_price->get_rates(),
				'weekend_price' => $apartment_price->weekend_price->get_price(),
			],
			'seasonal_price'   => $apartment_price->seasonal_price->get_price(),
			'post_id'          => $post_id,
			'ajax_url'         => esc_url( admin_url( 'admin-ajax.php' ) ),
		] ) );

		do_action( 'jet-booking/assets/after' );

		$this->deps_added = true;

	}

	/**
	 * Get config option.
	 *
	 * Returns option for date range picker configuration.
	 *
	 * @since  2.6.0
	 * @access public
	 *
	 * @param integer $post_id Post ID.
	 * @param string  $key     Options name key.
	 *
	 * @return mixed
	 */
	public function get_config_option( $post_id, $key ) {

		$option = Plugin::instance()->settings->get( $key );

		if ( ! $this->config ) {
			$this->config = get_post_meta( $post_id, 'jet_abaf_configuration', true );
		}

		if ( isset( $this->config['config'] ) && $this->config['config']['enable_config'] ) {
			$option = $this->config['config'][ $key ] ?? $option;
		}

		return $option;

	}

	public function get_next_booked_dates( $booked_dates ) {

		$result = [];

		if ( ! Plugin::instance()->settings->checkout_only_allowed() ) {
			return $result;
		}

		foreach ( $booked_dates as $index => $date ) {
			$next_date = date( 'Y-m-d', strtotime( $date ) + DAY_IN_SECONDS );
			$prev_date = date( 'Y-m-d', strtotime( $date ) - DAY_IN_SECONDS );

			if ( ! in_array( $next_date, $booked_dates ) && ! in_array( $prev_date, $booked_dates ) ) {
				$result[] = $next_date;
			}
		}

		return $result;

	}

	public function ensure_ajax_js() {
		if ( wp_doing_ajax() ) {
			wp_scripts()->done[] = 'jquery';
			wp_scripts()->print_scripts( 'jquery-date-range-picker' );
		}
	}

	/**
	 * Schedule settings.
	 *
	 * Return custom schedule settings list for specific post or global.
	 *
	 * @since  2.5.0
	 * @since  2.8.0 Code refactor.
	 * @access public
	 *
	 * @param null $post_id       Booking post type ID.
	 * @param null $default_value Default schedule value.
	 * @param null $meta_key      Post type meta value key.
	 *
	 * @return mixed|void
	 */
	public function get_schedule_settings( $post_id = null, $default_value = null, $meta_key = null ) {

		$schedule         = null;
		$post_schedule    = get_post_meta( $post_id, 'jet_abaf_custom_schedule', true );
		$general_schedule = Plugin::instance()->settings->get( $meta_key ) ?? $default_value;

		if ( ! isset( $post_schedule['custom_schedule'] ) || ! $post_schedule['custom_schedule']['use_custom_schedule'] ) {
			$schedule = $general_schedule;
		} elseif ( isset( $post_schedule['custom_schedule'][ $meta_key ] ) ) {
			$schedule = $post_schedule['custom_schedule'][ $meta_key ] ?? $general_schedule;
		}

		return apply_filters( 'jet-abaf/calendar/custom-schedule', $schedule, $meta_key, $default_value, $post_id );

	}

	/**
	 * Get days by rule.
	 *
	 * Returns list of days by passed rule.
	 *
	 * @since  2.8.0
	 * @access public
	 *
	 * @param string|number $post_id Booking post type id.
	 * @param string        $type    Rule type.
	 *
	 * @return array
	 */
	public function get_days_by_rule( $post_id = null, $type = 'disable' ) {

		if ( ! $post_id ) {
			return [];
		}

		$days          = [];
		$post_schedule = get_post_meta( $post_id, 'jet_abaf_custom_schedule', true );

		$rules = [
			$type . '_weekend_2',
			$type . '_weekday_1',
			$type . '_weekday_2',
			$type . '_weekday_3',
			$type . '_weekday_4',
			$type . '_weekday_5',
			$type . '_weekend_1',
		];

		if ( ! isset( $post_schedule['custom_schedule'] ) || ! $post_schedule['custom_schedule']['use_custom_schedule'] ) {
			foreach ( $rules as $key => $value ) {
				if ( Plugin::instance()->settings->get( $value ) ) {
					$days[] = $key;
				}
			}
		} else {
			foreach ( $rules as $key => $value ) {
				if ( isset( $post_schedule['custom_schedule'][ $value ] ) && filter_var( $post_schedule['custom_schedule'][ $value ], FILTER_VALIDATE_BOOLEAN ) ) {
					$days[] = $key;
				}
			}
		}

		if ( 'disable' === $type ) {
			return $days;
		}

		$disabled_days = $this->get_days_by_rule( $post_id );

		if ( empty( $disabled_days ) ) {
			return $days;
		}

		foreach ( $disabled_days as $day ) {
			if ( ( $key = array_search( $day, $days ) ) !== false ) {
				unset( $days[ $key ] );
			}
		}

		return array_values( $days );

	}

	/**
	 * Booking days off.
	 *
	 * Returns booking days off - official days off.
	 *
	 * @since  2.5.0
	 * @access public
	 *
	 * @param int $post_id Booking post type ID.
	 *
	 * @throws \Exception
	 * @return array List of days off.
	 */
	public function get_booking_days_off( $post_id ) {

		$days_off = $this->get_schedule_settings( $post_id, null, 'days_off' );
		$dates    = [];

		if ( empty( $days_off ) ) {
			return $dates;
		}

		foreach ( $days_off as $day ) {
			$from = new \DateTime( date( 'F d, Y', $day['startTimeStamp'] ) );
			$to   = new \DateTime( date( 'F d, Y', $day['endTimeStamp'] ) );

			if ( $to->format( 'Y-m-d' ) === $from->format( 'Y-m-d' ) ) {
				$dates[] = $from->format( 'Y-m-d' );
			} else {
				$to     = $to->modify( '+1 day' );
				$period = new \DatePeriod( $from, new \DateInterval( 'P1D' ), $to );

				foreach ( $period as $key => $value ) {
					$dates[] = $value->format( 'Y-m-d' );
				}
			}
		}

		return $dates;

	}

	/**
	 * Booked dates.
	 *
	 * Returns booked dates list.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @throws \Exception
	 * @return array List of booked dates.
	 */
	public function get_booked_dates( $post_id ) {

		$bookings = Plugin::instance()->db->get_future_bookings( $post_id );

		if ( empty( $bookings ) ) {
			return [];
		}

		$units           = Plugin::instance()->db->get_apartment_units( $post_id );
		$units_num       = ! empty( $units ) ? count( $units ) : 0;
		$weekly_bookings = jet_abaf()->settings->is_weekly_bookings( $post_id );
		$week_offset     = $this->get_config_option( $post_id, 'week_offset' );
		$skip_statuses   = Plugin::instance()->statuses->invalid_statuses();
		$skip_statuses[] = Plugin::instance()->statuses->temporary_status();
		$dates           = [];

		if ( ! $units_num || 1 === $units_num ) {
			foreach ( $bookings as $booking ) {
				if ( ! empty( $booking['status'] ) && in_array( $booking['status'], $skip_statuses ) ) {
					continue;
				}

				$from = new \DateTime( date( 'F d, Y', $booking['check_in_date'] ) );
				$to   = new \DateTime( date( 'F d, Y', $booking['check_out_date'] ) );

				if ( $weekly_bookings && ! $week_offset || ! $this->is_per_nights_booking() ) {
					$to = $to->modify( '+1 day' );
				}

				if ( $to->format( 'Y-m-d' ) === $from->format( 'Y-m-d' ) ) {
					$dates[] = $from->format( 'Y-m-d' );
				} else {
					$period = new \DatePeriod( $from, new \DateInterval( 'P1D' ), $to );

					foreach ( $period as $key => $value ) {
						$dates[] = $value->format( 'Y-m-d' );
					}
				}
			}
		} else {
			$booked_units = [];

			foreach ( $bookings as $booking ) {
				if ( ! empty( $booking['status'] ) && in_array( $booking['status'], $skip_statuses ) ) {
					continue;
				}

				$from = new \DateTime( date( 'F d, Y', $booking['check_in_date'] ) );
				$to   = new \DateTime( date( 'F d, Y', $booking['check_out_date'] ) );

				if ( $weekly_bookings && ! $week_offset || ! $this->is_per_nights_booking() ) {
					$to = $to->modify( '+1 day' );
				}

				if ( $to->format( 'Y-m-d' ) === $from->format( 'Y-m-d' ) ) {
					if ( empty( $booked_units[ $from->format( 'Y-m-d' ) ] ) ) {
						$booked_units[ $from->format( 'Y-m-d' ) ] = 1;
					} else {
						$booked_units[ $from->format( 'Y-m-d' ) ] ++;
					}
				} else {
					$period = new \DatePeriod( $from, new \DateInterval( 'P1D' ), $to );

					foreach ( $period as $key => $value ) {
						if ( empty( $booked_units[ $value->format( 'Y-m-d' ) ] ) ) {
							$booked_units[ $value->format( 'Y-m-d' ) ] = 1;
						} else {
							$booked_units[ $value->format( 'Y-m-d' ) ] ++;
						}
					}
				}
			}

			foreach ( $booked_units as $date => $booked_units_num ) {
				if ( $units_num <= $booked_units_num ) {
					$dates[] = $date;
				}
			}
		}

		return $dates;

	}

	/**
	 * Off dates.
	 *
	 * Returns off dates - official days off and booked dates.
	 *
	 * @since  2.5.0
	 * @since  2.5.5 Added additional `$post_id` handling.
	 * @access public
	 *
	 * @param int $post_id Booking post type ID.
	 *
	 * @throws \Exception
	 * @return array|mixed
	 */
	public function get_off_dates( $post_id ) {

		$post_id = Plugin::instance()->db->get_initial_booking_item_id( $post_id );

		if ( isset( $this->booked_dates[ $post_id ] ) ) {
			return $this->booked_dates[ $post_id ];
		}

		$booked_dates = $this->get_booked_dates( $post_id );
		$days_off     = $this->get_booking_days_off( $post_id );

		if ( empty( $booked_dates ) && empty( $days_off ) ) {
			$this->booked_dates[ $post_id ] = [];

			return [];
		}

		$dates = array_merge( $booked_dates, $days_off );

		$this->booked_dates[ $post_id ] = $dates;

		return $dates;

	}

	/**
	 * Register macros.
	 *
	 * Registers and returns specific macros list for booking functionality.
	 *
	 * @since  2.7.0
	 * @access public
	 *
	 * @return array
	 */
	public function register_macros() {

		$macros_list['booking_unit_title'] = [
			'label' => __( 'JetBooking: Unit Title', 'jet-engine' ),
			'cb'    => [ $this, 'get_unit_title' ],
		];

		$macros_list['booking_status'] = [
			'label' => __( 'JetBooking: Status', 'jet-engine' ),
			'cb'    => [ $this, 'get_booking_status' ],
		];

		return $macros_list;

	}

	/**
	 * Get unit title.
	 *
	 * Returns unit name in units is set.
	 *
	 * @since  2.7.0
	 * @access public
	 *
	 * @return mixed|string
	 */
	public function get_unit_title() {

		$booking = jet_engine()->listings->data->get_current_object();

		if ( ! $booking ) {
			return '';
		}

		$apartment_id = ! empty( $booking->apartment_id ) ? absint( $booking->apartment_id ) : null;
		$unit_id      = ! empty( $booking->apartment_unit ) ? absint( $booking->apartment_unit ) : null;

		if ( ! $apartment_id || ! $unit_id ) {
			return '';
		}

		$unit = Plugin::instance()->db->get_apartment_unit( $apartment_id, $unit_id );

		if ( empty( $unit ) ) {
			return '';
		}

		return ! empty( $unit[0]['unit_title'] ) ? $unit[0]['unit_title'] : 'Unit-' . $unit_id;

	}

	/**
	 * Get booking status.
	 *
	 * Return current status of booking instance.
	 *
	 * @since  2.7.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_booking_status() {

		$booking = jet_engine()->listings->data->get_current_object();

		if ( ! $booking ) {
			return '';
		}

		return ! empty( $booking->status ) ? $booking->status : Plugin::instance()->statuses->temporary_status();

	}

	/**
	 * Get current booking object title.
	 *
	 * Returns booking instance title of current booking item.
	 *
	 * @since  2.7.0
	 * @access public
	 *
	 * @param string $title  Current object title.
	 * @param object $object Current object instance.
	 *
	 * @return string
	 */
	public function get_current_booking_object_title( $title, $object ) {

		if ( ! $object || empty( $object->apartment_id ) ) {
			return $title;
		}

		return get_the_title( $object->apartment_id );

	}

	/**
	 * Get current booking meta.
	 *
	 * Returns meta value of booking instance for current booking item.
	 *
	 * @since  2.7.0
	 * @access public
	 *
	 * @param boolean $meta_value Initial value.
	 * @param object  $object     Current object instance.
	 * @param string  $meta_key   Meta field key name.
	 *
	 * @return mixed
	 */
	public function get_current_booking_meta( $meta_value, $object, $meta_key ) {

		if ( ! $object || empty( $object->apartment_id ) ) {
			return $meta_value;
		}

		return get_post_meta( $object->apartment_id, $meta_key, true );

	}

}
