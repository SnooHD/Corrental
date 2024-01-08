<?php
/*
This is COMMERCIAL SCRIPT
We are not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly            //FixIn: 9.7.3.13

/**
 *  Get ALL booking resources from DB
 *
 * @param $resource_id
 * @param $where
 *
 * @return array|object|stdClass[]|null
 */
    // Just Get ALL booking types from DB
function wpbc_get_booking_resources_bm__from_db__arr( $resource_id = 0 ) {

	global $wpdb;

	if ( class_exists( 'wpdev_bk_biz_l' ) ) {

		$resources_as_linear_arr = wpbc_get_booking_resources_as_linear_arr();

		for ( $i = 0; $i < count( $resources_as_linear_arr ); $i ++ ) {
			$resources_as_linear_arr[ $i ]['obj']->count = $resources_as_linear_arr[ $i ]['count'];
			$resources_as_linear_arr[ $i ]               = $resources_as_linear_arr[ $i ]['obj'];
			if (
				    ( isset( $resource_id ) )
			     && ( isset( $resources_as_linear_arr[ $i ]->booking_type_id ) )
			     && ( $resource_id != 0 )
			     && ( $resource_id == $resources_as_linear_arr[ $i ]->booking_type_id )
			) {
				return $resources_as_linear_arr[ $i ];
			}
		}
		if ( $resource_id == 0 ) {
			return $resources_as_linear_arr;
		}
	}

	// Get booking resources only  as numbers                               //FixIn:5.4.3
	$booking_type_id_array = explode( ',', $resource_id );
	$resource_id           = array();
	foreach ( $booking_type_id_array as $bk_t ) {
		$bk_t = (int) $bk_t;
		if ( $bk_t > 0 ) {
			$resource_id[] = $bk_t;
		}
	}
	$resource_id = implode( ',', $resource_id );

	$order_type = 'title';

	if ( $resource_id == 0 ) {  // Normal getting
		$resources_db_arr = $wpdb->get_results( "SELECT booking_type_id as id, title, cost FROM {$wpdb->prefix}bookingtypes ORDER BY {$order_type}" );
	} else {
		$resources_db_arr = $wpdb->get_results( "SELECT booking_type_id as id, title, cost FROM {$wpdb->prefix}bookingtypes WHERE booking_type_id IN ( {$resource_id} )" );
	}

	return $resources_db_arr;
}
