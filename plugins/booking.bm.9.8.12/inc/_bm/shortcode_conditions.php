<?php
/*
This is COMMERCIAL SCRIPT
We are not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

/**
 *  Parse conditions in Booking Calendar shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/**
 * [booking type=3 nummonths=3
 *      options='{calendar months_num_in_row=3 width=100% cell_height=50px}
 *              ,{select-day condition="weekday" for="1" value="4"}
 *              ,{select-day condition="weekday" for="5" value="3,7"}
 *              ,{select-day condition="weekday" for="6" value="2"}
 *              ,{start-day condition="season" for="September 2023" value="5,6,2"}
 *              ,{select-day condition="season" for="September 2023" value="3-5"}
 *  ']
 *
 * //TODO: may be create this simple conditions configuration ?
 * ?:
 * {start  in="season" "hight=5"}
 * {select in="weekday" "5=6,7..10"}
 * {select in="date" "2023-08-22 - 2023-08-29=7,14,21,29-99"}
 */

 /**
 * Parse option  parameter of Booking Calendar shortcode and set Conditions for Days Selection  depends on from  WeekDays / Seasons / Days
 *
 * @param $resource_id                  10
 * @param $shortcode_options            '{calendar months_num_in_row=3 width=100% cell_height=50px}
 *                                       ,{select-day condition="weekday" for="1" value="4"}
 *                                       ,{select-day condition="weekday" for="5" value="3,7"}
 *                                       ,{select-day condition="weekday" for="6" value="2"}
 *                                       ,{start-day condition="season" for="September 2023" value="5,6,2"}
 *                                       ,{select-day condition="season" for="September 2023" value="3-5"}'
 *
 * @return array
					[
					    conditions :[
										select-day:[
														weekday:[
																	0:[ ... ]
																	1:[ ... ]
																	2:[
																		for   : "6"
																		value : "2"
																	  ]
														season:[
																	0:[ ... ]
										start-day:[
														season:[
																	0:[
																		for    :    "september_2023"
																		value    :    "2,5,6"
																	  ]
					    seasons :[
										2023-09-01:[  "wpbc_season_september_2023", "wpbc_season_september_2024"  ]
										2023-09-02:[ ... ]
										2023-09-03:[ ... ]
										2023-09-04:[ ... ]
					]
 */
function wpbc_parse_shortcode_option__set_days_selection_conditions( $resource_id, $shortcode_options ) {

	$days_selection_conditions = array();

	if ( empty( $shortcode_options ) ) {    return $days_selection_conditions;    }

	if ( is_string( $shortcode_options ) ) {
		$shortcode_options = html_entity_decode( $shortcode_options );       //FixIn: 9.7.3.6.1
	}

	/**
	 * $matches found: [    [0] => {select-day condition="weekday" for="6" value="2,7"},
							[1] => select-day
							[2] => condition
							[3] => weekday
							[4] => for
							[5] => 6
							[6] => value
							[7] => 2,7
		            ]
    */
	$param = '\s*([condition|for|value]+)=[\'"]{1}([^\'"]+)[\'"]{1}\s*';
	$pattern_to_search = '%\s*{([^\s]+)' . $param . $param . $param . '}\s*[,]?\s*%';
	preg_match_all( $pattern_to_search, $shortcode_options, $matches, PREG_SET_ORDER );

	$season_names_arr = array();
	$conditions       = array();
	foreach ( $matches as $option ) {

		$cond = array(
						'full'               => $option[ 0 ],     // {select-day condition="weekday" for="6" value="2,7"},
						'type'               => $option[ 1 ],     //  select-day | start-day
						'condition_tag_name' => $option[ 2 ],     //  condition
						'condition_type'     => $option[ 3 ],     //  weekday     |   season          | date
						'for_tag_name'       => $option[ 4 ],     //  for
						'for_value'          => $option[ 5 ],     //      '5'     | 'September 2023'  | '2023-08-22' | '2023-08-22 - 2023-08-29'
						'value_tag_name'     => $option[ 6 ],     // value
						'value_value'        => $option[ 7 ]      // '2-7' | '5,6,2'
					);

		if ( ! isset( $conditions[ $cond['type'] ] ) ) { $conditions[ $cond['type'] ] = array(); }
		//                          select-day                season
		if ( ! isset( $conditions[ $cond['type'] ][ $cond['condition_type'] ] ) ) { $conditions[ $cond['type'] ][ $cond['condition_type'] ] = array(); }

		$conditions[ $cond['type'] ][ $cond['condition_type'] ][] = array();
		$i = count( $conditions[ $cond['type'] ][ $cond['condition_type'] ] ) - 1;                                      // Get index of the specific rule for the conditions.

		// We need to  save season names in [] for later getting dates for these seasons
		if ( 'season' === $cond['condition_type'] ) {
			$season_names_arr[] = $cond['for_value'];
		}
		$conditions[ $cond['type'] ][ $cond['condition_type'] ][ $i ][ 'for' ]   = wpbc_esc__season_filter_name( $cond['for_value'] );                       // [for]   => 'High season'     -> 'high_season'
		$conditions[ $cond['type'] ][ $cond['condition_type'] ][ $i ][ 'value' ] = wpbc_get_specific_range_dates__as_comma_list( $cond['value_value'] );     // [value] => '14' | '3,7-9,12' -> '3,7,8,9,12'
	}

	/**
	 * $conditions =     [    select-day :[    weekday :[   0 :[ for : "1",    value : "4" ], ...
														   1 :[
																for : "5"
																value : "3,7"
														   2 :[
																for : "6"
																value : "2"
											  season :[
														   0 :[
																for : "september_2023"
																value : "3,4,5"
							 start-day :[
											  season :[
														   0 :[
																for : "september_2023"
																value : "2,5,6"
					    ]
	 */

	$season_names_arr = array_unique( $season_names_arr );

	/**
	 * $season_names_arr =  [ "September 2023" ]
	 */

	$days_selection_conditions['conditions'] = $conditions;
	$days_selection_conditions['seasons']    = wpbc_set__season_filters_dates( $season_names_arr );

	return $days_selection_conditions;
}


/**
 * Get array  of seasons for each  sql_date:  [  '2023-09-01':[ 'wpdevbk_season_september_2023', 'wpdevbk_season_september_2024' ], ... ]
 *
 * @param $season_names_arr     [  "September 2023", "September 2024"  ]
 *
 * @return array                [
									2023-09-01:[  "wpdevbk_season_september_2023", "wpdevbk_season_september_2024"  ]
									2023-09-02:[ ... ]
									2023-09-03:[ ... ]
									2023-09-04:[ ... ]
 *                              ]
 */
function wpbc_set__season_filters_dates( $season_names_arr ) {

	global $wpdb;

	$seasons_y_m_d__esc_title = array();

	if ( count( $season_names_arr ) > 0 ) {

		$seasons_result = wpbc_db_get_season_data_arr__for_these_season_names( $season_names_arr );

		if ( ! empty( $seasons_result ) ) {

			$max_monthes_in_calendar = wpbc_get_max_visible_days_in_calendar();
			$sql_day_tag = date( 'Y-m-d' );                                                 // TODAY

			for ( $i = 0; $i < $max_monthes_in_calendar; $i ++ ) {                          // Days

				list( $year, $month, $day ) = explode( '-', $sql_day_tag );
				$day        = intval( $day );
				$month      = intval( $month );
				$year       = intval( $year );
				$my_day_tag = $month . '-' . $day . '-' . $year;            //  '8-22-2023'

				foreach ( $seasons_result as $filter_value ) {              // Season filters

					if ( ( isset( $filter_value->filter ) ) && ( isset( $filter_value->title ) ) ) {

						$is_day_inside_of_filter = wpbc_is_day_in_season_filter( $day, $month, $year, $filter_value->filter );
						if ( $is_day_inside_of_filter ) {

							if ( ! isset( $seasons_y_m_d__esc_title[ $sql_day_tag ] ) ) { $seasons_y_m_d__esc_title[ $sql_day_tag ] = array(); }

							$seasons_y_m_d__esc_title[ $sql_day_tag ][] = 'wpdevbk_season_' . wpbc_esc__season_filter_name( $filter_value->title );
						}
                    }
                }

				$sql_day_tag = date( 'Y-m-d', mktime( 0, 0, 0, $month, ( $day + 1 ), $year ) );
			}
		}
	}

	return $seasons_y_m_d__esc_title;
}


	/**
	 * Get season filters data from  DB,  with  specific names
	 *
	 * @param array $season_names_arr ["High season", "Weekend season"]           - Array of season names to retrive
	 *
	 * @return array|object|stdClass[]|null     [    0 =>  stdClass(object)(
	 *                                                                         'id' => '3',
	 *                                                                         'title' => 'High season',
	 *                                                                         'filter' => 'a:4:{s:8:"weekdays";a:7:{i:0;s:2:"On";i:1;s:2:"On";...',
	 *                                                                      ),
	 *                                                1 =>  stdClass(object)(
	 *                                                                         'id' => '4',
	 *                                                                         'title' => 'Weekend season',
	 *                                                                         'filter' => 'a:4:{s:8:"weekdays";a:7:{i:0;s:2:"On";i:1;...',
	 *                                                                        )
	 *                                            ]
	 */
	function wpbc_db_get_season_data_arr__for_these_season_names( $season_names_arr ) {

		$seasons_result_arr = array();

		if ( ! empty( $season_names_arr ) ) {

			global $wpdb;

			$season_filter_names = '';
			foreach ( $season_names_arr as $season_title ) {
				$season_filter_names .= $wpdb->prepare( "%s,", $season_title );
			}
			$season_filter_names = substr( $season_filter_names, 0, - 1 );                      // remove ','

			$my_sql =  "SELECT booking_filter_id as id, title, filter 
	                    FROM {$wpdb->prefix}booking_seasons 
	                    WHERE title IN ( {$season_filter_names} ) 
	                    ORDER BY booking_filter_id";

			$seasons_result_arr = $wpdb->get_results( $my_sql );                                    // SQL
		}

		return $seasons_result_arr;
	}