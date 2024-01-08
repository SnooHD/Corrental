<?php
/**
 * JetEngine compatibility package Listings manager.
 *
 * @package JET_ABAF\Compatibility\Packages\Jet_Engine\Listings
 */

namespace JET_ABAF\Compatibility\Packages\Jet_Engine\Listings;

class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @var object
	 */
	private static $instance = null;

	public function __construct() {

		// Register JetBooking object fields group.
		add_filter( 'jet-engine/listing/data/object-fields-groups', [ $this, 'add_object_fields_groups' ] );
		// Handle dynamic field property output.
		add_filter( 'jet-engine/listings/data/prop-not-found', [ $this, 'get_dynamic_field_prop' ], 10, 3 );
		// Register additional columns object fields groups.
		add_filter( 'jet-engine/listing/data/jet-booking-query/object-fields-groups', [ $this, 'add_additional_column_object_fields' ] );
		// Register additional calendar widget group keys.
		add_filter( 'jet-engine/listing/calendar/group-keys', [ $this, 'register_calendar_group_keys' ] );
		// Handle calendar widget date key.
		add_filter( 'jet-engine/listing/calendar/date-key', [ $this, 'prepare_booking_date_key' ], 10, 4 );
		// Set multidays parameters for booking group date key.
		add_action( 'jet-engine/listing/calendar/before', [ $this, 'set_booking_multiday_params' ], 10, 2 );
		// Set listing custom post type id.
		add_filter( 'jet-engine/listing/custom-post-id', [ $this, 'set_queried_booking_id' ], 10, 2 );
		// Handle custom values in dynamic field for Booking object.
		add_filter( 'jet-engine/listings/dynamic-field/custom-value', [ $this, 'dynamic_field_custom_value_handle' ], 10, 3 );

	}

	/**
	 * Add object fields groups.
	 *
	 * Add JetBooking related fields into dynamic object fields groups.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param array $groups List of objects groups.
	 *
	 * @return array
	 */
	public function add_object_fields_groups( $groups ) {

		$groups[] = [
			'label'   => __( 'JetBooking', 'jet-booking' ),
			'options' => apply_filters( 'jet-engine/listing/data/jet-booking-query/object-fields-groups', [
				'jet_abaf_get_id'             => __( 'Booking ID', 'jet-booking' ),
				'jet_abaf_get_status'         => __( 'Status', 'jet-booking' ),
				'jet_abaf_get_apartment_id'   => __( 'Booking Instance ID', 'jet-booking' ),
				'jet_abaf_get_apartment_unit' => __( 'Booking Instance Unit ID', 'jet-booking' ),
				'jet_abaf_get_check_in_date'  => __( 'Check In Date', 'jet-booking' ),
				'jet_abaf_get_check_out_date' => __( 'Check Out Date', 'jet-booking' ),
				'jet_abaf_get_order_id'       => __( 'Related Order ID', 'jet-booking' ),
			] ),
		];

		return $groups;

	}

	/**
	 * Get dynamic field prop.
	 *
	 * Returns dynamic property values.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param mixed  $result   Dynamic field property value.
	 * @param string $property Property name.
	 * @param object $object   Current listing object.
	 *
	 * @return mixed
	 */
	public function get_dynamic_field_prop( $result, $property, $object ) {

		if ( false !== strpos( $property, 'jet_abaf_' ) ) {
			$property = str_replace( 'jet_abaf_', '', $property );
		}

		if ( is_callable( [ $object, $property ] ) ) {
			return call_user_func( [ $object, $property ] );
		} elseif ( false !== strpos( $property, 'jet_abaf::' ) && is_callable( [ $object, 'get_column' ] ) ) {
			$result = $object->get_column( str_replace( 'jet_abaf::', '', $property ) );
		}

		return $result;

	}

	/**
	 * Add additional column object fields.
	 *
	 * Add JetBooking additional columns source fields into the dynamic field widget.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param array $fields List of field.
	 *
	 * @return array
	 */
	public function add_additional_column_object_fields( $fields ) {

		$columns = jet_abaf()->settings->get_clean_columns();

		if ( empty( $columns ) ) {
			return $fields;
		}

		foreach ( $columns as $column ) {
			$fields[ 'jet_abaf::' . $column ] = __( 'Additional column: ', 'jet-booking' ) . $column;
		}

		return $fields;

	}

	/**
	 * Register calendar group keys.
	 *
	 * Register additional booking key for the calendar widget.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param array $keys List of initial groups keys.
	 *
	 * @return array
	 */
	public function register_calendar_group_keys( $keys ) {

		$keys['jet_booking'] = __( 'Booking date', 'jet-booking' );

		return $keys;

	}

	/**
	 * Prepare booking date key.
	 *
	 * Return calendar date key for appointments in calendar
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param string                       $key      Timestamp of required month day.
	 * @param object                       $item     Listing item object.
	 * @param string                       $group_by Items group by key.
	 * @param \Jet_Listing_Render_Calendar $calendar Calendar widget render object.
	 *
	 * @return mixed
	 */
	public function prepare_booking_date_key( $key, $item, $group_by, $calendar ) {

		if ( 'jet_booking' === $group_by ) {

			$allowed_range = $calendar->get_date_period_for_query( $calendar->get_settings() );

			$key = $item->get_check_in_date();

			if ( $key < $allowed_range['start'] || $key > $allowed_range['end'] ) {
				return false;
			}

		}

		return $key;

	}

	/**
	 * Set booking multidays params.
	 *
	 * Handle booking groups key and set appropriate parameters for multiday output.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param array                        $settings List of Calendar widget settings.
	 * @param \Jet_Listing_Render_Calendar $calendar Calendar widget render object.
	 *
	 * @throws \Exception
	 */
	public function set_booking_multiday_params( $settings, $calendar ) {

		if ( 'jet_booking' !== $settings['group_by'] ) {
			return;
		}

		$query_id = null;

		if ( isset( $settings['custom_query'] ) && filter_var( $settings['custom_query'], FILTER_VALIDATE_BOOLEAN ) ) {
			$query_id = absint( $settings['custom_query_id'] );
		} else {
			$listing          = jet_engine()->listings->data->get_listing();
			$listing_settings = $listing->get_settings();

			if ( 'query' === $listing_settings['listing_source'] ) {
				$query_id = \Jet_Engine\Query_Builder\Manager::instance()->listings->get_query_id( $listing->get_main_id(), $listing_settings );
			}
		}

		if ( ! $query_id ) {
			return;
		}

		$query = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $query_id );

		if ( ! $query ) {
			return;
		}

		$query->setup_query();

		$month = [
			'start' => $calendar->get_current_month(),
			'end'   => $calendar->get_current_month( true ),
		];

		foreach ( $query->get_items() as $item ) {
			$start_date      = new \DateTime( date( 'Y-m-d', $item->get_check_in_date() ) );
			$end_date        = new \DateTime( date( 'Y-m-d', $item->get_check_out_date() ) );
			$period          = $start_date->diff( $end_date );
			$calendar_period = $calendar->get_date_period_for_query( $settings );

			for ( $i = 1; $i <= $period->days; $i ++ ) {
				$day = strtotime( date( 'Y-m-d', $item->get_check_in_date() ) . '+ ' . $i . ' days' );

				if ( $day < $calendar_period['start'] || $day > $calendar_period['end'] ) {
					continue;
				}

				$j = absint( date( 'j', $day ) );

				if ( $day < $month['start'] ) {
					if ( empty( $calendar->prev_month_posts[ $j ] ) ) {
						$calendar->prev_month_posts[ $j ] = [ $item ];
					} else {
						$calendar->prev_month_posts[ $j ][] = $item;
					}

					continue;
				}

				if ( $day > $month['end'] ) {
					if ( empty( $calendar->next_month_posts[ $j ] ) ) {
						$calendar->next_month_posts[ $j ] = [ $item ];
					} else {
						$calendar->next_month_posts[ $j ][] = $item;
					}

					continue;
				}

				if ( empty( $calendar->multiday_events[ $j ] ) ) {
					$calendar->multiday_events[ $j ] = array( $item );
				} else {
					$calendar->multiday_events[ $j ][] = $item;
				}

				$calendar->posts_cache[ jet_engine()->listings->data->get_current_object_id( $item ) ] = false;
			}
		}

	}

	/**
	 * Set queried booking id.
	 *
	 * Set correct booking id  for listing output.
	 *
	 * @since 3.1.0
	 *
	 * @param int|string $id     Queried object id.
	 * @param object     $object Listing queried object.
	 *
	 * @return mixed
	 */
	public function set_queried_booking_id( $id, $object ) {

		if ( $object && is_object( $object ) && is_a( $object, '\JET_ABAF\Resources\Booking' ) ) {
			$id = $object->get_id();
		}

		return $id;

	}

	/**
	 * Dynamic field custom value handle.
	 *
	 * Handle custom values in dynamic field for Booking object.
	 *
	 * @since 3.1.0
	 *
	 * @param boolean                          $value         Initial custom field value.
	 * @param array                            $settings      List of dynamic field settings.
	 * @param \Jet_Engine_Render_Dynamic_Field $dynamic_field Dynamic field class instance.
	 *
	 * @return mixed|string
	 */
	public function dynamic_field_custom_value_handle( $value, $settings, $dynamic_field ) {

		if ( 'object' !== $dynamic_field->get( 'dynamic_field_source' ) ) {
			return $value;
		}

		$object_context = $dynamic_field->get( 'object_context' );
		$post_object    = jet_engine()->listings->data->get_object_by_context( $object_context );

		if ( ! $post_object ) {
			$post_object = jet_engine()->listings->data->get_current_object();
		}

		if ( $post_object && is_object( $post_object ) && is_a( $post_object, '\JET_ABAF\Resources\Booking' ) ) {
			$field = $dynamic_field->get( 'dynamic_field_post_object' );
			$auto  = $dynamic_field->get( 'dynamic_field_wp_excerpt', '' );

			if ( 'post_excerpt' === $field && ! empty( $auto ) ) {
				$value = get_the_excerpt( $post_object->get_apartment_id() );
			}
		}

		return $value;

	}

	/**
	 * Returns the instance.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return object
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

}