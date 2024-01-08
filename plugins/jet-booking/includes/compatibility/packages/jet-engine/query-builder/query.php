<?php
/**
 * JetEngine compatibility package Query Builder Query class.
 *
 * @package JET_ABAF\Compatibility\Packages\Jet_Engine\Query_Builder
 */

namespace JET_ABAF\Compatibility\Packages\Jet_Engine\Query_Builder;

use \Jet_Engine\Query_Builder\Queries\Base_Query;

class Query extends Base_Query {

	/**
	 * Get items.
	 *
	 * Returns queried items array of objects.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return array
	 */
	public function _get_items() {

		$bookings        = $this->get_booking_items();
		$booking_objects = [];

		foreach ( $bookings as $booking ) {
			$booking_objects[] = new \JET_ABAF\Resources\Booking( $booking );
		}

		return $booking_objects;

	}

	/**
	 * Get items total count.
	 *
	 * Returns total found items count.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return false|int|mixed
	 */
	public function get_items_total_count() {

		$cached = $this->get_cached_data( 'count' );

		if ( false !== $cached ) {
			return $cached;
		}

		$result = count( $this->get_booking_items( false ) );

		$this->update_query_cache( $result, 'count' );

		return $result;

	}

	/**
	 * Get items pages count.
	 *
	 * Returns queried items pages count.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return float|int
	 */
	public function get_items_pages_count() {

		$total    = count( $this->get_booking_items( false ) );
		$per_page = $this->get_items_per_page();

		if ( ! $total || ! $per_page ) {
			return 1;
		} else {
			return ceil( $total / $per_page );
		}

	}

	/**
	 * Get items page count.
	 *
	 * Returns queried items count per page.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return false|int|mixed
	 */
	public function get_items_page_count() {

		$result   = $this->get_items_total_count();
		$per_page = $this->get_items_per_page();

		if ( $per_page < $result ) {
			$page  = $this->get_current_items_page();
			$pages = $this->get_items_pages_count();

			if ( $page < $pages ) {
				$result = $per_page;
			} elseif ( absint( $page ) === absint( $pages ) ) {
				$offset = ! empty( $this->final_query['offset'] ) ? absint( $this->final_query['offset'] ) : 0;
				$result = $result - $offset;
			}
		}

		return $result;

	}

	/**
	 * Get current items page.
	 *
	 * Returns currently displayed page number.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return float|int
	 */
	public function get_current_items_page() {

		$offset   = ! empty( $this->final_query['offset'] ) ? absint( $this->final_query['offset'] ) : 0;
		$per_page = $this->get_items_per_page();

		if ( ! $offset || ! $per_page ) {
			return 1;
		} else {
			return ceil( $offset / $per_page ) + 1;
		}

	}

	/**
	 * Set filters prop.
	 *
	 * Set filtered prop in specific for current query type way.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param string $prop  Filter property name.
	 * @param mixed  $value Value to filter property.
	 *
	 * @return void
	 */
	public function set_filtered_prop( $prop = '', $value = null ) {
		switch ( $prop ) {
			case '_page':
				$page = absint( $value );

				if ( 0 < $page ) {
					$offset = ( $page - 1 ) * $this->get_items_per_page();

					$this->final_query['offset'] = $offset;
				}

				break;

			case 'orderby':
				if ( is_array( $value ) ) {
					foreach ( $value as $key => $order ) {
						$this->final_query['orderby'][] = [
							'orderby' => $key,
							'order'   => $order,
						];
					}
				}

				break;

			case 'meta_query':
				foreach ( $value as $row ) {
					$this->update_args_row( $row );
				}

				break;

			default:
				$this->update_args_row( [
					'key'     => $prop,
					'value'   => $value,
					'compare' => '=',
				] );

				break;
		}
	}

	/**
	 * Update args row.
	 *
	 * Update args row in specific for current query type way.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param array $row Query row parameters list.
	 *
	 * @return void
	 */
	public function update_args_row( $row ) {
		if ( ! empty( $row['relation'] ) ) {
			unset( $row['relation'] );

			foreach ( $row as $inner_row ) {
				$this->update_args_row( $inner_row );
			}
		} else {
			$value = ! empty( $row['value'] ) ? $row['value'] : '';

			if ( in_array( $row['key'], jet_abaf()->db->get_default_fields() ) ) {
				if ( 'check_in_date' === $row['key'] || 'check_out_date' === $row['key'] ) {
					$this->final_query['date_query'][] = [
						'date'    => ! empty( $row['key'] ) ? $row['key'] : false,
						'compare' => ! empty( $row['compare'] ) ? $row['compare'] : '=',
						'value'   => $value,
					];
				} else {
					$this->final_query[ $row['key'] ] = is_array( $value ) ? $value : [ $value ];
				}
			} elseif ( in_array( $row['key'], jet_abaf()->settings->get_clean_columns() ) ) {
				$this->final_query['meta_query'][] = [
					'column'  => ! empty( $row['key'] ) ? $row['key'] : false,
					'compare' => ! empty( $row['compare'] ) ? $row['compare'] : '=',
					'value'   => $value,
				];
			}
		}
	}

	/**
	 * Get items per page.
	 *
	 * Returns count of the items visible per single page.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return int
	 */
	public function get_items_per_page() {

		if ( null === $this->final_query ) {
			$this->setup_query();
		}

		if ( ! empty( $this->final_query['limit'] ) ) {
			return absint( $this->final_query['limit'] );
		}

		return 0;

	}

	/**
	 * Add date range args.
	 *
	 * Adds date range query arguments to given query parameters.
	 *
	 * @since 3.1.0
	 *
	 * @param array $args        Initial arguments.
	 * @param array $dates_range Dates range list.
	 * @param array $settings    List of settings.
	 *
	 * @return array|mixed
	 */
	public function add_date_range_args( $args = [], $dates_range = [], $settings = [] ) {

		if ( 'jet_booking' !== $settings['group_by'] ) {
			return $args;
		}

		if ( isset( $settings['hide_past_events'] ) && filter_var( $settings['hide_past_events'], FILTER_VALIDATE_BOOLEAN ) ) {
			$date_query = [
				'date'    => 'check_in_date',
				'compare' => '>=',
				'value'   => date( 'Y-m-d', $dates_range['start'] ),
			];

			$args['date_query'][] = $date_query;
		}

		return $args;

	}

	/**
	 * Get booking items.
	 *
	 * Returns list of bookings based on set query parameters.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param boolean $limit True if limit query output.
	 *
	 * @return array|object|\stdClass[]
	 */
	public function get_booking_items( $limit = true ) {

		if ( null === $this->final_query ) {
			$this->setup_query();
		}

		$args         = $this->final_query;
		$query_clause = [];

		if ( ! empty( $args['status'] ) ) {
			$query_clause[] = sprintf( "status IN ( '%s' )", implode( "', '", $args['status'] ) );
		}

		if ( ! empty( $args['include'] ) ) {
			$query_clause[] = sprintf( "booking_id IN ( %s )", implode( ", ", $args['include'] ) );
		} else {
			if ( ! empty( $args['exclude'] ) ) {
				$query_clause[] = sprintf( "booking_id NOT IN ( %s )", implode( ", ", $args['exclude'] ) );
			}
		}

		if ( ! empty( $args['apartment_id'] ) ) {
			$query_clause[] = sprintf(
				"apartment_id IN ( %s )",
				is_array( $args['apartment_id'] ) ? implode( ", ", $args['apartment_id'] ) : $args['apartment_id']
			);

			if ( ! empty( $args['apartment_unit'] ) ) {
				$query_clause[] = sprintf( "apartment_unit IN ( %s )", implode( ", ", $args['apartment_unit'] ) );
			}
		}

		if ( ! empty( $args['order_id'] ) ) {
			$query_clause[] = sprintf( "order_id IN ( %s )", implode( ", ", $args['order_id'] ) );
		}

		if ( ! empty( $args['date_query'] ) ) {
			$query_date_clause = [];

			foreach ( $args['date_query'] as $date_query ) {
				if ( empty( $date_query['date'] ) ) {
					continue;
				}

				if ( is_array( $date_query['value'] ) ) {
					$date = $date_query['value'][0] . ' AND ' . $date_query['value'][1];
				} else {
					$date = strtotime( $date_query['value'] );
				}

				if ( ! $date ) {
					continue;
				}

				$compare = ! empty( $date_query['compare'] ) ? $date_query['compare'] : '=';

				if ( 'check_in_date' === $date_query['date'] ) {
					$date ++;
				}

				$query_date_clause[] = sprintf( "%s %s %s", $date_query['date'], $compare, $date );
			}

			if ( ! empty( $query_date_clause ) ) {
				$date_clause_rel = ! empty( $args['date_query_relation'] ) ? $args['date_query_relation'] : 'AND';
				$query_clause[]  = sprintf( "( %s )", implode( ' ' . $date_clause_rel . ' ', $query_date_clause ) );
			}
		}

		if ( ! empty( $args['meta_query'] ) ) {
			$query_meta_clause = [];

			foreach ( $args['meta_query'] as $meta_query ) {
				if ( empty( $meta_query['column'] ) ) {
					continue;
				}

				$value = ! empty( $meta_query['value'] ) ? $meta_query['value'] : '';

				if ( is_array( $value ) ) {
					$value = $value[0] . ' AND ' . $value[1];
				}

				$compare = ! empty( $meta_query['compare'] ) ? $meta_query['compare'] : '=';
				$format  = ! floatval( $value ) ? "%s %s '%s'" : "%s %s %s";

				$query_meta_clause[] = sprintf( $format, $meta_query['column'], $compare, $value );
			}

			if ( ! empty( $query_meta_clause ) ) {
				$meta_clause_rel = ! empty( $args['meta_query_relation'] ) ? $args['meta_query_relation'] : 'AND';
				$query_clause[]  = sprintf( "( %s )", implode( ' ' . $meta_clause_rel . ' ', $query_meta_clause ) );
			}
		}

		$query = sprintf( "SELECT * FROM %s", jet_abaf()->db::bookings_table() );

		if ( ! empty( $query_clause ) ) {
			$query .= sprintf( " WHERE %s", implode( ' AND ', $query_clause ) );
		}

		if ( ! empty( $args['orderby'] ) ) {
			$query_orderby_clause = [];

			foreach ( $args['orderby'] as $orderby ) {
				if ( empty( $orderby['orderby'] ) ) {
					continue;
				}

				$order = ! empty( $orderby['order'] ) ? $orderby['order'] : 'ASC';

				$query_orderby_clause[] = sprintf( "%s %s", $orderby['orderby'], $order );
			}

			if ( ! empty( $query_orderby_clause ) ) {
				$query .= sprintf( " ORDER BY %s", implode( ', ', $query_orderby_clause ) );
			}
		}

		if ( $limit && ! empty( $args['limit'] ) && intval( $args['limit'] ) > 0 ) {
			$query .= sprintf( " LIMIT %d", intval( $args['limit'] ) );

			if ( ! empty( $args['offset'] ) ) {
				$query .= sprintf( " OFFSET %d", intval( $args['offset'] ) );
			}
		}

		return jet_abaf()->db::wpdb()->get_results( $query, ARRAY_A );

	}

	/**
	 * Get args to explode.
	 *
	 * Returns list of arguments where string should be exploded into array.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return string[]
	 */
	public function get_args_to_explode() {
		return [
			'include',
			'exclude',
			'apartment_unit',
			'order_id'
		];
	}

}
