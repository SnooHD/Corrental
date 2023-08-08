<?php
/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage Create pages Functions
 * @category Functions
 *
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2023-05-10
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/**
 * Create page for Booking Calendar
 *
 * @param array $page_params  = array(
								      'post_content'   => '[bookingedit]',
								      'post_name'      => 'wpbc-booking-edit',
								      'post_title'     => esc_html__('Booking edit','booking')
								)
 *
 * @return false|int - ID of the page.
 */
function wpbc_create_page( $page_params = array() ){                                                                    //FixIn: 9.6.2.10

	global $wp_rewrite;
	if ( is_null( $wp_rewrite ) ) {     //FixIn: 9.7.1.1
		return false;
	}

    // Create post object
    $defaults = array(
      //'menu_order'     => 0,                          // The order the post should be displayed in. Default 0. If new post is a page, it sets the order in which it should appear in the tabs.
      //'pinged'         => '',                         // Space or carriage return-separated list of URLs that have been pinged. Default empty.
      //'post_author'    => get_current_user_id(),      // The user ID number of the author.  The ID of the user who added the post. Default is the current user ID.
      //'post_category'  => array(),                    // Array of category IDs. Defaults to value of the 'default_category' option.   post_category no longer exists, try wp_set_post_terms() for setting a post's categories
      //'post_date'      => [ Y-m-d H:i:s ]             // The date of the post. Default is the current time. The time post was made.
      //'post_date_gmt'  => [ Y-m-d H:i:s ]             // The time post was made, in GMT.
      //'post_excerpt'   => ''                          //The post excerpt. Default empty. For all your post excerpt needs.
      //'post_parent'    => 0,                          // Set this for the post it belongs to, if any. Default 0. Sets the parent of the new post.
      //'post_password'  => '',                         //The password to access the post. Default empty. password for post?
      //'tags_input'     => '',                         // Array of tag names, slugs, or IDs. Default empty. [ '<tag>, <tag>, <...>' ] //For tags.
      //'to_ping'        => '',                         // Space or carriage return-separated list of URLs to ping. Default empty.  Space or carriage return-separated list of URLs to ping.  Default empty.
      //'tax_input'      => '',                         // Default empty. [ array( 'taxonomy_name' => array( 'term', 'term2', 'term3' ) ) ] // support for custom taxonomies.
      'ID'             => 0,                            // The post ID. If equal to something other than 0, the post with that ID will be updated. Default 0.
	  'post_type'      => 'page',                       // The post type. Default 'post'. [ 'post' | 'page' | 'link' | 'nav_menu_item' | 'custom_post_type' ] //You may want to insert a regular post, page, link, a menu item or some custom post type
      'post_status'    => 'publish',                    // [ 'draft' | 'publish' | 'pending'| 'future' | 'private' | 'custom_registered_status' ] // The post status. Default 'draft'. Set the status of the new post.
      'comment_status' => 'closed',                     // [ 'closed' | 'open' ] // 'closed' means no comments.
      'ping_status'    => 'closed',                     // [ 'closed' | 'open' ] // 'closed' means pingbacks or trackbacks turned off
      'post_content'   => '[bookingedit]',              // The post content. Default empty. The full text of the post.
      'post_name'      => 'wpbc-booking-edit',          // sanitize_title( $post_title ), -- By default, converts accent characters to ASCII characters and further limits the output to alphanumeric characters, underscore (_) and dash (-) // The post name. Default is the sanitized post title when creating a new post. The name (slug) for your post
      'post_title'     => esc_html__('Booking edit','booking')  // The post title. Default empty. The title of your post.
    );


	$my_post   = wp_parse_args( $page_params, $defaults );


	$post_id = wp_insert_post( $my_post );                         // Insert the post into the database

	if ( ( ! is_wp_error( $post_id ) ) && ( ! empty( $post_id ) ) ) {

		// Success

		// $post = get_post( $post_id );
		// $post_url = get_permalink( $post_id );

	} else {
		if ( is_wp_error( $post_id ) ) {
			// Error	    //  __( 'Sorry, the post could not be created.' )
		}
		if ( ! $post_id ) {
			// Error	    // __( 'Sorry, the post could not be created.' )
		}
		$post_id = false;
	}

	return $post_id;
}


/**
 * Create new page and get  URL  of this page
 *
 * @param $default_options_to_add
 *
 * @return void
 */
function wpbc_create_page_thank_you( $default_options_to_add ) {                                                        //FixIn: 9.6.2.11

	global $wp_rewrite;
	if ( is_null( $wp_rewrite ) ) {     //FixIn: 9.7.1.1
		return false;
	}

	$thank_you_page_url = get_bk_option( 'booking_thank_you_page_URL' );

	if (
		   ( ( empty( $thank_you_page_url ) ) || ( '/thank-you' == wpbc_make_link_relative( get_bk_option( $thank_you_page_url ) ) ) )
		&& ( empty( get_page_by_path( 'wpbc-booking-received' ) ) )
	){
		$page_params = array(
			'post_title'   => esc_html( __( 'Booking Received', 'booking' ) ),
			'post_content' => esc_html( __( 'Thank you for your booking. Your booking has been successfully received.', 'booking' ) ),
			'post_name'    => 'wpbc-booking-received'
		);

		$post_id = wpbc_create_page( $page_params );

		$post_url = '';

		if ( ! empty( $post_id ) ) {
			$post_url = get_permalink( $post_id );
		}

		$post_url_relative = str_replace( site_url(), '', $post_url );

		if ( ! empty( $post_url_relative ) ) {
			add_bk_option( 'booking_thank_you_page_URL', wpbc_make_link_relative( $post_url_relative ) );
		}
	}
}
add_bk_action( 'wpbc_before_activation__add_options', 'wpbc_create_page_thank_you' );


/**
 * Create pages for [bookingedit]  and [bookingcustomerlisting] shortcodes,
 *
 * if previously was not defined options
 * 'booking_url_bookings_edit_by_visitors' and 'booking_url_bookings_listing_by_customer'
 * to some pages different from homepage.
 *
 * @return void
 */
function wpbc_create_page_bookingedit(){                                                                                //FixIn: 9.6.2.10

	global $wp_rewrite;
	if ( is_null( $wp_rewrite ) ) {     //FixIn: 9.7.1.1
		return false;
	}

	// Booking Edit page - set  default page and URL ---------------------------------------------------------------
	$url_booking_edit = get_bk_option( 'booking_url_bookings_edit_by_visitors' );
	if (
		   ( site_url() == $url_booking_edit )
		&& ( empty( get_page_by_path( 'wpbc-my-booking' ) ) )
	){
		$page_params = array(
			'post_content' => '[bookingedit]',                          // The post content
			'post_name'    => 'wpbc-my-booking',                        // sanitize_title( $post_title )
			'post_title'   => esc_html__( 'My Booking', 'booking' )     // Title
		);

		$post_id = wpbc_create_page( $page_params );

		if ( ! empty( $post_id ) ) {
			$post_url = get_permalink( $post_id );
			update_bk_option( 'booking_url_bookings_edit_by_visitors', $post_url );
		}
	} else if (
				(
					( site_url() == $url_booking_edit )
				 || ( empty( $url_booking_edit ) )
				)
			    && ( ! empty( get_page_by_path( 'wpbc-my-booking' ) ) )
	){
		$wp_post = get_page_by_path( 'wpbc-my-booking' );
		if (
  			   ( ! empty( $wp_post ) )
			&& ( false !== strpos( $wp_post->post_content, '[bookingedit]' ) )
		){
			update_bk_option( 'booking_url_bookings_edit_by_visitors', get_permalink(  $wp_post->ID ) );
		}
	}
	// -------------------------------------------------------------------------------------------------------------

	// Booking Listing page - set  default page and URL ---------------------------------------------------------------
	$url_booking_edit = get_bk_option( 'booking_url_bookings_listing_by_customer' );
	if (
		   ( site_url() == $url_booking_edit )
		&& ( empty( get_page_by_path( 'wpbc-my-bookings-listing' ) ) )
	){

		$page_params = array(
			'post_content' => '[bookingcustomerlisting]',                           // The post content
			'post_name'    => 'wpbc-my-bookings-listing',                           // sanitize_title( $post_title )
			'post_title'   => esc_html__( 'My Bookings Listing', 'booking' )                // Title
		);

		$post_id = wpbc_create_page( $page_params );

		if ( ! empty( $post_id ) ) {
			$post_url = get_permalink( $post_id );
			update_bk_option( 'booking_url_bookings_listing_by_customer', $post_url );
		}
	} else if (
				(
					( site_url() == $url_booking_edit )
				 || ( empty( $url_booking_edit ) )
				)
			    && ( ! empty( get_page_by_path( 'wpbc-my-bookings-listing' ) ) )
	){
		$wp_post = get_page_by_path( 'wpbc-my-bookings-listing' );
		if (
  			   ( ! empty( $wp_post ) )
			&& ( false !== strpos( $wp_post->post_content, '[bookingcustomerlisting]' ) )
		){
			update_bk_option( 'booking_url_bookings_listing_by_customer', get_permalink(  $wp_post->ID ) );
		}
	}

}


function wpbc_create_page_booking_payment_status(){                                                                     //FixIn: 9.6.2.13

	global $wp_rewrite;
	if ( is_null( $wp_rewrite ) ) {     //FixIn: 9.7.1.1
		return false;
	}

	// Successful Payment page options ---------------------------------------------------------------------------------

	$post_url_relative = false;
	$slug              = 'wpbc-booking-payment-successful';
	if ( empty( get_page_by_path( $slug ) ) ) {
		// Create page
		$page_params = array(
			'post_title'   => esc_html( __( 'Booking Payment Confirmation', 'booking' ) ),
			'post_content' => esc_html( __( 'Thank you for your booking. Your payment for the booking has been successfully received.', 'booking' ) ),
			'post_name'    => $slug
		);

		$post_id = wpbc_create_page( $page_params );

		if ( ! empty( $post_id ) ) {
			$post_url          = get_permalink( $post_id );
			$post_url_relative = str_replace( site_url(), '', $post_url );
		}
	}
	if ( ! empty( $post_url_relative ) ) {

		//$post_url_relative = wpbc_make_link_absolute( $post_url_relative );

		$payment_systems = array(
			'booking_stripe_v3_order_successful',
			'booking_paypal_return_url',
			'booking_authorizenet_order_successful',
			'booking_sage_order_successful',
			'booking_ideal_return_url',
			'booking_ipay88_return_url'
		);

		foreach ( $payment_systems as $payment_system ) {

			if (
				    ( false === get_bk_option( $payment_system ) )
			     || ( '/successful' == wpbc_make_link_relative( get_bk_option( $payment_system ) ) )
			) {
				update_bk_option( $payment_system, wpbc_make_link_relative( $post_url_relative ) );
			}
		}
	}

	// Failed Payment page options -------------------------------------------------------------------------------------

	$post_url_relative = false;
	$slug              = 'wpbc-booking-payment-failed';
	if ( empty( get_page_by_path( $slug ) ) ) {
		// Create page
		$page_params = array(
			'post_title'   => esc_html( __( 'Booking Payment Failed', 'booking' ) ),
			'post_content' => esc_html( __( 'Payment Unsuccessful. Please contact us for assistance.', 'booking' ) ),
			'post_name'    => $slug
		);

		$post_id = wpbc_create_page( $page_params );

		if ( ! empty( $post_id ) ) {
			$post_url          = get_permalink( $post_id );
			$post_url_relative = str_replace( site_url(), '', $post_url );
		}
	}
	if ( ! empty( $post_url_relative ) ) {

		//$post_url_relative = wpbc_make_link_absolute( $post_url_relative );

		$payment_systems = array(
			'booking_stripe_v3_order_failed',
			'booking_paypal_cancel_return_url',
			'booking_authorizenet_order_failed',
			'booking_sage_order_failed',
			'booking_ideal_cancel_return_url',
			'booking_ipay88_cancel_return_url'
		);

		foreach ( $payment_systems as $payment_system ) {

			if (
				    ( false === get_bk_option( $payment_system ) )
			     || ( '/failed' == wpbc_make_link_relative( get_bk_option( $payment_system ) ) )
			) {
				update_bk_option( $payment_system, wpbc_make_link_relative( $post_url_relative ) );
			}
		}
	}
}