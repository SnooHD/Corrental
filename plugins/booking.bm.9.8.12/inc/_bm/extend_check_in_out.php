<?php
/*
This is COMMERCIAL SCRIPT
We are not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// <editor-fold     defaultstate="collapsed"                        desc="  =  S e t t i n g s   -  Get Options  =  "  >

	//GOOD
	/**
	 * Get extra seconds    from the Settings   to   set  before and after bookings
	 *
	 * @return array [ int, int ]   :    array( $extra_seconds_in, $extra_seconds_out  )
	 */
	function wpbc_get_from_settings__extra_seconds__before_after(){

		$booking_unavailable_extra_in_out = get_bk_option( 'booking_unavailable_extra_in_out' );

	    // Get EXTRA seconds IN / OUT  to our dates --------------------------------------------------------------------

		$extra_seconds_in  = 0;
		$extra_seconds_out = 0;

		// Extra MINUTES -----------------------------------------------------------------------------------------------
		if ( $booking_unavailable_extra_in_out == 'm' ) {
			$extra_minutes_in  = str_replace( array( 'm', 'd' ), '', get_bk_option( 'booking_unavailable_extra_minutes_in' ) );         // 0
			$extra_minutes_out = str_replace( array( 'm', 'd' ), '', get_bk_option( 'booking_unavailable_extra_minutes_out' ) );        // 30

			$SEC_IN_MINUTE = 60;

			$extra_seconds_in  = intval($extra_minutes_in)  * $SEC_IN_MINUTE;
			$extra_seconds_out = intval($extra_minutes_out) * $SEC_IN_MINUTE;
		}

		// Extra DAYS --------------------------------------------------------------------------------------------------
		if ( $booking_unavailable_extra_in_out == 'd' ) {
			$extra_days_in  = str_replace( array( 'm', 'd' ), '', get_bk_option( 'booking_unavailable_extra_days_in' ) );              // 0
			$extra_days_out = str_replace( array( 'm', 'd' ), '', get_bk_option( 'booking_unavailable_extra_days_out' ) );             // 21

			$SEC_IN_DAY        = 60 * 60 * 24;
			$extra_seconds_in  = intval($extra_days_in)  * $SEC_IN_DAY;
			$extra_seconds_out = intval($extra_days_out) * $SEC_IN_DAY;
		}
		// -------------------------------------------------------------------------------------------------------------

		if ( empty( $extra_seconds_in ) ) {
			$extra_seconds_in = 0;
		}
		if ( empty( $extra_seconds_out ) ) {
			$extra_seconds_out = 0;
		}

		return array( $extra_seconds_in, $extra_seconds_out );
	}

// </editor-fold>

// CLASS BOOKING_DATE
/**
 * Override booking dates object  for extend dates on specific number of seconds IN / OUT
 */
class WPBC_BOOKING_DATE_EXTENDED extends WPBC_BOOKING_DATE{

	public $extended_dates_times_arr;
	public $debug_dates_times_arr;


	function __construct( $boking_dates_arr ) {

		parent::__construct( $boking_dates_arr );

		$this->extended_dates_times_arr = array();
		$this->debug_dates_times_arr    = array();
	}


	/**
	 * Extend   '$this->readable_dates'      array on specific number of seconds   by overriding it.
	 *
	 * @param int $extra_seconds_in
	 * @param int $extra_seconds_out
	 *
	 * @return void
	 */
	public function extend_readable_dates__on_extra_seconds__in_out( $extra_seconds_in, $extra_seconds_out ){

		$ext__time_ranges__arr = array();
		$debug_ext_only_times_arr = array();

		// Get unavailable extended time_ranges arr
		foreach ( $this->readable_dates as $only_date_sql => $readable_times_arr ) {

			foreach ( $this->readable_dates[ $only_date_sql ] as $readable_time_range ) {

				if ( ( $extra_seconds_in >= 0 ) && ( $this->is_time_check_in( $readable_time_range ) ) ) {

					$ext_only_times_arr = $this->extend_readable_time( $only_date_sql, $readable_time_range, -$extra_seconds_in );

					$debug_ext_only_times_arr[] = $ext_only_times_arr;                              // For debug only:  [ "0":"2023-08-10 12:00:01","1":"2023-08-11 00:00:00","3":"2023-08-12 13:59:52" ]

					$ext__time_ranges__arr[] = $this->transform__extended_sql_dates__into__time_ranges__by_dates( $ext_only_times_arr );        // <- Questions can  be only  here about 'anomalies'
				}

				if ( ( $extra_seconds_out >= 0 ) && ( $this->is_time_check_out( $readable_time_range ) ) ) {

					$ext_only_times_arr = $this->extend_readable_time( $only_date_sql, $readable_time_range, $extra_seconds_out );

					$debug_ext_only_times_arr[] = $ext_only_times_arr;                              // For debug only:  [ "0":"2023-08-10 12:00:01","1":"2023-08-11 00:00:00","3":"2023-08-12 13:59:52" ]

					$ext__time_ranges__arr[] = $this->transform__extended_sql_dates__into__time_ranges__by_dates( $ext_only_times_arr );        // <- Questions can  be only  here about 'anomalies'
				}
			}
		}


		/**
		 * Merge original       booked dates arr      and         extended times  arr:
		 *
		 * $all_unavailable_times__for_booking =
		 *      [
		 *          2023-08-10 = [
		 *                          0 = "12:00:01 - 14:00:02"
		 *                          1 = "12:00:01 - 14:00:02"
		 *                          2 = "12:00:01 - 24:00:00"
		 *                      ]
		 *          2023-08-11 = [  0 = "00:00:00 - 24:00:00" ]
		 *          2023-08-12 = [  0 = "00:00:00 - 14:00:02" ]
		 *      ]
		 */
		$all_unavailable_times__for_booking = $this->merge_booking_dates__and__extended_times( $this->readable_dates, $ext__time_ranges__arr );

		/**
		 * Check if time slots intersected and union them into one time_range:
		 *
		 * $united__time_ranges__by_dates =
		 *      [
		 *          2023-08-10 = [  0 = "12:00:01 - 24:00:00" ]
		 *          2023-08-11 = [  0 = "00:00:00 - 24:00:00" ]
		 *          2023-08-12 = [  0 = "00:00:00 - 14:00:02" ]
		 *      ]
		 */
		$united__time_ranges__by_dates = $this->merge_intersected__time_ranges__in_all_dates( $all_unavailable_times__for_booking );

		// For debug only
		if ( $this->is_debug ) {
			$this->debug_dates_times_arr['debug_ext_only_times_arr']    = $debug_ext_only_times_arr;
			$this->debug_dates_times_arr['debug_ext__time_ranges__arr'] = $ext__time_ranges__arr;
		}

		$this->readable_dates = $united__time_ranges__by_dates;
	}


	//GOOD
	/**
	 *  Check if time slots intersected and merge intersected time_range    for all DATES       - basically  it's LOOP by dates
	 *
	 * @param $time_ranges__by_dates    =   [
	 *                                          [2023-08-01] => [
	 *                                                              [0] => 09:45:01 - 12:00:02
	 *                                                              [1] => 10:00:01 - 12:00:02
	 *                                                              [2] => 10:00:01 - 13:30:02
	 *                                                          ]
	 *                                          [2023-08-05] => [
	 *                                                              [0] => 09:45:01 - 12:00:02
	 *                                                              [1] => 10:00:01 - 12:00:02
	 *                                                              [2] => 10:00:01 - 13:30:02
	 *                                                          ]
     *                                      ]
	 *
	 * @return array
	 */
	protected function merge_intersected__time_ranges__in_all_dates( $time_ranges__by_dates ){

		$united__time_ranges__by_dates = array();

		foreach ( $time_ranges__by_dates as $only_date => $time_ranges_arr ) {
			// Union $time_ranges_arr
			$time_ranges__by_dates[ $only_date ] = $this->merge_intersected__time_ranges( $time_ranges_arr );
		}
		
		$united__time_ranges__by_dates = $time_ranges__by_dates;

		return $united__time_ranges__by_dates;
	}


		// GOOD
		/**
		 *   Merge   Intersected       Time_Ranges      [ '09:45:01 - 12:00:02', '10:00:01 - 12:00:02', '10:00:01 - 13:30:02' ]     -->     [  '09:45:01 - 13:30:02'  ]
		 *
		 * @param $time_ranges_arr      =>   [
		 *                                        '09:45:01 - 12:00:02'
		 *                                      , '10:00:01 - 12:00:02'
		 *                                      , '10:00:01 - 13:30:02'
		 *                                   ]
		 *
		 * @return array    [  '09:45:01 - 13:30:02'  ]
		 */
		public function merge_intersected__time_ranges( $time_ranges_arr ){

			// Get working array in seconds  ::  24 HOURS -->  SECONDS
			$working_seconds_arr = array();
			foreach ( $time_ranges_arr as $time_slot ) {

				$time_slot_arr = explode( ' - ', $time_slot );

				$working_seconds_arr[] = array(  $this->transform__24_hours__in__seconds( $time_slot_arr[0] ),  $this->transform__24_hours__in__seconds( $time_slot_arr[1] )  );
			}

			// Merge intersected intervals
			$merged_seconds_arr = wpbc_merge_intersected_intervals( $working_seconds_arr );

			// Backward composing  ::           SECONDS  -->  24 HOURS          time_ranges
			$fixed_24hours_arr = array();
			foreach ( $merged_seconds_arr as $time_slot ) {

				$fixed_24hours_arr[] = $this->transform__seconds__in__24_hours_his( $time_slot[0] ) . ' - ' . $this->transform__seconds__in__24_hours_his( $time_slot[1] );
			}

			return $fixed_24hours_arr;
		}


	// GOOD
	/**
	 * Merge original       booked dates arr      and         extended times  arr
	 *
	 * @param array $readable_dates   (original bookings)   = [
	 *                                                          [2023-09-01] => [ 00:00:01 - 24:00:00 ]
	 *                                                          [2023-09-03] => [ 00:00:00 - 24:00:00 ]
	 *                                                          [2023-09-14] => [ 00:00:00 - 24:00:02 ]
	 *                                                        ]
	 *
	 * @param array $extended_dates_time_ranges_arr   (extended times) = [
	 *
	 *                                                                      [0] => [
	 *                                                                                  [2023-08-31] => [   [0] => 23:45:01 - 24:00:00  ]
	 *                                                                                  [2023-09-01] => [   [0] => 00:00:00 - 24:00:00  ]
	 *                                                                              ]
	 *                                                                      [1] => [
	 *                                                                                  [2023-09-14] => [   [0] => 00:00:00 - 24:00:00  ]
	 *                                                                                  [2023-09-15] => [   [0] => 00:00:00 - 01:30:00  ]
	 *                                                                             ]
	 *                                                                   ]
	 *
	 * @return array    = [
	 *
	 *                      [2023-08-31] => [
	 *                                          [0] => 23:45:01 - 24:00:00
	 *                                      ]
	 *                      [2023-09-01] => [
     *                                          [0] => 00:00:00 - 24:00:00
     *                                          [1] => 00:00:01 - 24:00:00
     *                                      ]
	 *                      [2023-09-03] => [
	 *                                          [0] => 00:00:00 - 24:00:00
     *                                      ]
	 *                      [2023-09-14] => [
	 *                                          [0] => 00:00:00 - 24:00:00
	 *                                          [1] => 00:00:00 - 24:00:02
     *                                      ]
	 *                      [2023-09-15] => [
	 *                                          [0] => 00:00:00 - 01:30:00
     *                                      ]
	 *                     ]
	 */
	private function merge_booking_dates__and__extended_times( $readable_dates, $extended_dates_time_ranges_arr ) {

		$all_unavailable_times__for_booking = $readable_dates;

		foreach ( $extended_dates_time_ranges_arr as $dates_array ) {

			foreach ( $dates_array as $date_only => $time_ranges_arr ) {

				if ( ! isset( $all_unavailable_times__for_booking[ $date_only ] ) ) {
					$all_unavailable_times__for_booking[ $date_only ] = array();
				}
				foreach ( $time_ranges_arr as $time_range ) {
					$all_unavailable_times__for_booking[ $date_only ][] = $time_range;
				}
				sort( $all_unavailable_times__for_booking[ $date_only ] );      // Sort time ranges
			}
		}

		ksort( $all_unavailable_times__for_booking );                           // Sort dates

		return $all_unavailable_times__for_booking;
	}


	// <- Questions can  be only  here about 'anomalies'
	/**
	 * Transform SQL datetime arr. to by-dates time-ranges:    [ '2023-09-28 00:00:10', '2023-09-29 00:00:00', '2023-09-30 12:00:02' ] -> [ '2023-09-28' => ['00:00:10 - 24:00:00'], '2023-09-29' => ['00:00:00 - 24:00:00'], '2023-09-30' => ['00:00:00 - 12:00:02']  ]
	 *
	 * And make some exception to times corrections,  like   '23:59:50' to '24:00:00' ,  etc...
	 *
	 * @param $sql_date_time_arr    [
	 *                                  [0] => 2023-09-28 00:00:10                  <- Check Out
	 *                                  [1] => 2023-09-29 00:00:00
	 *                                  [3] => 2023-09-30 12:00:02
	 *                              ]
	 *
	 * @return array        [
	 *                          [2023-08-01] => [ 10:00:01 - 24:00:00 ]             <- timeslots
	 *                          [2023-08-02] => [ 00:00:00 - 24:00:00 ]
	 *                          [2023-08-03] => [ 00:00:00 - 12:00:02 ]
	 *                      ]
	 *                      ]
	 *                  OR
	 *                      [
	 *                          [2023-08-31] => [ 00:00:01 - 24:00:00 ]             <- FULL In                          corrected to    [00:00:00 - 24:00:00 ]
	 *                          [2023-09-15] => [ 00:00:00 - 24:00:00 ]
	 *                      ]
	 *                  OR
	 *                      [
	 *                          [2023-09-14] => [ 00:00:00 - 24:00:00 ]
	 *                          [2023-09-15] => [ 00:00:00 - 24:00:00 ]
	 *                          [2023-09-16] => [ 00:00:00 - 23:59:52 ]             <- FULL out                         corrected to    [00:00:00 - 24:00:00 ]
	 *                      ]
	 *                  OR
	 *                      [
	 *                          [2023-09-25] => [14:00:01 - 24:00:00 ]
	 *                          [2023-09-26] => [00:00:00 - 23:59:50 ]              <- Check In                         corrected to    [00:00:00 - 24:00:00 ]
	 *                      ]
	 *                  OR
	 *                      [
	 *                          [2023-09-28] => [00:00:10 - 24:00:00 ]              <- Check Out                        corrected to    [00:00:00 - 24:00:00 ]
	 *                          [2023-09-29] => [00:00:00 - 24:00:00 ]
	 *                          [2023-09-30] => [00:00:00 - 12:00:02 ]
	 *                      ]
     */
	private function transform__extended_sql_dates__into__time_ranges__by_dates( $sql_date_time_arr ){

		/**
		 * Here time ranges can  start  from  00:00:00 - TO SOME TIME,
		 * and it will  be time slot or check  out and not the full  day  booking.
		 *
		 *  Example: [  2023-09-14: [ "00:00:00 - 24:00:00"],       2023-09-15:     [ "00:00:00 - 04:00:00"  ]       ]
		 */

		$time_ranges__by_dates = array();

		foreach ( $sql_date_time_arr as $sql_date_time ) {

			list( $only_sql_date, $only_time_his ) = explode( ' ', $sql_date_time );

			if ( ! isset( $time_ranges__by_dates[ $only_sql_date ] ) ) {   $time_ranges__by_dates[ $only_sql_date ] = array();  }


//			// Make fixes of anomalies
			if( '00:00:10' === $only_time_his ) { $only_time_his = '00:00:00'; }                      // <- Check Out
			if( '00:00:01' === $only_time_his ) { $only_time_his = '00:00:00'; }                      // <- FULL In
			if( '23:59:50' === $only_time_his ) { $only_time_his = '24:00:00'; }                      // <- Check In
			if( '23:59:52' === $only_time_his ) { $only_time_his = '24:00:00'; }                      // <- FULL out
//			if ( '52' == substr( $only_time_his, - 2 ) ) {
//				$only_time_his = $this->transform__24_hours__in__seconds( $only_time_his ) + 10;
//				$only_time_his = $this->transform__seconds__in__24_hours_his( $only_time_his );
//			}


			$time_ranges__by_dates[ $only_sql_date ][] = $only_time_his;
		}

		/**
		 * 2023-08-01 =  [ "10:00:01 - 24:00:00" ], 2023-08-02 = [ "00:00:00" ],  2023-08-03 = [ "12:00:02" ]
		 *  OR
		 * 2023-08-01 = [  "10:00:01" ],   2023-08-02 = [  "00:00:00" ],   2023-08-03 = [  "12:00:02" ]
		 */

		foreach ( $time_ranges__by_dates as $only_sql_date => $time_ranges_arr ) {

			$time_ranges_arr = array_unique( $time_ranges_arr );

			// One time
			if ( 1 == count( $time_ranges_arr ) ) {

				$time_24_hours_slot = $time_ranges_arr[0];
				$is_check_in_out__or_full = substr( $time_24_hours_slot, -1 );                                          // '1', '2'  (not '0')
				$is_check_in_out_10s = substr( $time_24_hours_slot, -2 );                                               // '10', '50'


				if ( ( '00:00:00' == $time_24_hours_slot ) || ( '24:00:00' == $time_24_hours_slot ) ) {

					$time_range = '00:00:00 - 24:00:00';

				} else if ( ( '1' === $is_check_in_out__or_full ) || ( '00:00:10' === $time_24_hours_slot ) ) {

//					if( '00:00:10' === $time_24_hours_slot ) { $time_24_hours_slot = '00:00:00'; }                      // <- Check Out
//					if( '00:00:01' === $time_24_hours_slot ) { $time_24_hours_slot = '00:00:00'; }                      // <- FULL In

					$time_range = $time_24_hours_slot . ' - 24:00:00';

				} else if ( ( '2' === $is_check_in_out__or_full ) || ( '23:59:50' === $time_24_hours_slot ) ) {

//					if( '23:59:50' === $time_24_hours_slot ) { $time_24_hours_slot = '24:00:00'; }                      // <- Check In
//					if( '23:59:52' === $time_24_hours_slot ) { $time_24_hours_slot = '24:00:00'; }                      // <- FULL out
					if ( '52' == substr( $time_24_hours_slot, - 2 ) ) {
						$time_24_hours_slot = $this->transform__24_hours__in__seconds( $time_24_hours_slot ) + 10; //8;
						$time_24_hours_slot = $this->transform__seconds__in__24_hours_his( $time_24_hours_slot );
					}

					$time_range = '00:00:00 - ' . $time_24_hours_slot;

				} else {
					$time_range = $time_ranges_arr[0];                      // ?????
				}


				$time_ranges__by_dates[ $only_sql_date ] = array();
				$time_ranges__by_dates[ $only_sql_date ][] = $time_range;

			} else if ( 2 == count( $time_ranges_arr ) ) {

				// Time - Slot

				foreach ( $time_ranges_arr as $time_key => $time_24_hours_slot ) {
//					if( '00:00:10' === $time_24_hours_slot ) { $time_ranges_arr[$time_key] = '00:00:00'; }                      // <- Check Out
//					if( '00:00:01' === $time_24_hours_slot ) { $time_ranges_arr[$time_key] = '00:00:00'; }                      // <- FULL In
//					if( '23:59:50' === $time_24_hours_slot ) { $time_ranges_arr[$time_key] = '24:00:00'; }                      // <- Check In
//					if( '23:59:52' === $time_24_hours_slot ) { $time_ranges_arr[$time_key] = '24:00:00'; }                      // <- FULL out
					if ( '52' == substr( $time_24_hours_slot, - 2 ) ) {
						$time_ranges_arr[ $time_key ] = $this->transform__24_hours__in__seconds( $time_ranges_arr[ $time_key ] ) + 10; //8;
						$time_ranges_arr[ $time_key ] = $this->transform__seconds__in__24_hours_his( $time_ranges_arr[ $time_key ] );
					}
				}

				$time_ranges__by_dates[ $only_sql_date ] = array();
				$time_ranges__by_dates[ $only_sql_date ][] = implode( ' - ', $time_ranges_arr );                        // <- Time Slots

			} else {
				// Unexpected !!!!
				echo "WPBC. Unexpected situation for date: {$only_sql_date}. We have more than 2 time slots than expected.";                // It has never happened
				debuge( ' $time_ranges_arr', $time_ranges_arr );
			}

		}

		return $time_ranges__by_dates;
	}


	// GOOD
	/**
	 * Get extended arr. of dates  shifted on number of seconds from DATE and TIME_RANGE    ('2023-08-01', '10:00:01 - 12:00:02', 172800)  ->  [ '2023-08-01 10:00:01', '2023-08-02 00:00:00', '2023-08-03 12:00:02' ]
	 *
	 * @param $only_date_sql                '2023-08-01'
	 * @param $readable_time_range          '10:00:01 - 12:00:02'
	 * @param $extra_seconds                172800                      - (2 days)
	 *
	 * @return array                        = [
													0 = "2023-08-01 10:00:01"
													1 = "2023-08-02 00:00:00"
													3 = "2023-08-03 12:00:02"
	 *                                        ]
	 *
	 *
	 * Example #1:
	 *
	 * $this->extend_readable_time(  '2023-07-31', '10:00:01 - 12:00:02', 86400 )
	 *
	 *                                = [
     *									  0 = "2023-07-31 10:00:01"
	 *   								  2 = "2023-08-01 12:00:02"
	 *                                  ]
	 * Example #2:
	 *
	 * $this->extend_readable_time(  '2023-08-01', '10:00:01 - 12:00:02', -900 )
	 *                                = [
	 *                                     0 = "2023-08-01 09:45:01"
	 *                                     2 = "2023-08-01 12:00:02"
	 *                                  ]
	 */
	public function extend_readable_time( $only_date_sql, $readable_time_range, $extra_seconds ){

		/**
		 * Conception:
		 *    1) start  - Time-Slots     A  - B                    '10:00:01 - 12:00:02'                 '10:00:01 - 12:00:02'
		 *    2) end    - Time-Slots     A  - B                    '10:00:01 - 12:00:02'                 '10:00:01 - 12:00:02'
		 *
		 *    3)        - Check in       A  - ...                  '14:00:01 -  ...  '                   '14:00:01 - 24:00:00'
		 *    4)        - Check out      ... -  B                  '  ...  - 12:00:02'                   '00:00:00 - 12:00:02'
		 *
		 *    5)        - Full day       ... - ...                 '  ...  -  ...  '                     '00:00:00 - 24:00:00'
		 *    6)        - Full IN       ..1 - ...                  '00:00:01 -  ...  '                   '00:00:01 - 24:00:00'
		 *    7)        - Full OUT       ... - ..2                 '  ...  - 24:00:02'                   '00:00:00 - 24:00:02'
		 */

		// Extra seconds -----------------------------------------------------------------------------------------------
		$is_check_in = false;
		if ( $extra_seconds < 0 ) {
			$extra_seconds = - 1 * $extra_seconds;
			$is_check_in = true;
		}
		$SEC_IN_DAY = 60 * 60 * 24;
		$extra_seconds_arr = array();
		if ( $extra_seconds >= 0 ) {
			$extra_seconds_arr[] = $extra_seconds;                      // Maximum interval (it's START (for check IN) or END (for check OUT) point)
		}
		while ( $extra_seconds > $SEC_IN_DAY ) {
			$extra_seconds       = $extra_seconds - $SEC_IN_DAY;
			$extra_seconds_arr[] = $extra_seconds;                  	// Add full days
		}
		sort( $extra_seconds_arr );
		$extra_seconds = max( $extra_seconds_arr );
		// Get seconds for   'only Day'   and   'Time_Range' -----------------------------------------------------------

		$in_24hours__timerange__arr = explode( ' - ', $readable_time_range );

		$in_seconds__timerange__arr = array();
		$in_seconds__timerange__arr[0] = $this->transform__24_hours__in__seconds( $in_24hours__timerange__arr[0] );
		$in_seconds__timerange__arr[1] = $this->transform__24_hours__in__seconds( $in_24hours__timerange__arr[1] );

		$timestamp_seconds__only_date_ymd = $this->transform__only_date_ymd__in__timestamp( $only_date_sql );

		// -------------------------------------------------------------------------------------------------------------
		// Check  about possible situation,  relative to  our concept (check above)
		// -------------------------------------------------------------------------------------------------------------
		if ( '00:00:00 - 24:00:00' == $readable_time_range ) {
			$situation = 5;                  // FULL DAY

			return array();                                                                                             // Empty Array      Exit
		} else if ( '00:00:01 - 24:00:02' == $readable_time_range ) {
			$situation = 8;                 //  Full IN/OT                                                              // When we have only 1 single booked date
		} else if ( '00:00:00 - 24:00:02' == $readable_time_range ) {
			$situation = 7;                  // Full OUT
		} else if ( '00:00:01 - 24:00:00' == $readable_time_range ) {
			$situation = 6;                  // Full IN
		} else if ( ( '00:00:00' == $in_24hours__timerange__arr[0] ) && ( ! $is_check_in ) ) {
			$situation = 4;                 // Check Out Day
		} else if ( ( '24:00:00' == $in_24hours__timerange__arr[1] ) && ( $is_check_in ) ) {
			$situation = 3;                 // Check In Day
		} else if ( ! $is_check_in ) {
			$situation = 2;                 // Check Out  Time
		} else if ( $is_check_in ) {
			$situation = 1;                 // Check in  Time
		}


		// -------------------------------------------------------------------------------------------------------------
		// Get Start and End points for our situations:
		// -------------------------------------------------------------------------------------------------------------

		if ( 1 == $situation ) {                // Check in  Time  -----------------------------------------------------    '10:00:01 - NN:NN:02'                 '10:00:01 - 12:00:02'

			$start_date_sec = $timestamp_seconds__only_date_ymd + $in_seconds__timerange__arr[0] - $extra_seconds;
			$start_date_sql = $this->transform__timestamp__to_sql_date( $start_date_sec );                                // 2023-07-31 10:00:01

			$end_time_sec = $timestamp_seconds__only_date_ymd + $in_seconds__timerange__arr[1];
			$end_time_sql = $this->transform__timestamp__to_sql_date( $end_time_sec );                                    // 2023-08-01 12:00:02

		} else if ( 2 == $situation ) {         // Check out Time  -----------------------------------------------------    'NN:NN:01 - 12:00:02'                 '10:00:01 - 12:00:02'

			$start_date_sec = $timestamp_seconds__only_date_ymd + $in_seconds__timerange__arr[0];
			$start_date_sql = $this->transform__timestamp__to_sql_date( $start_date_sec );

			$end_time_sec = $timestamp_seconds__only_date_ymd + $in_seconds__timerange__arr[1] + $extra_seconds - 10;
			$end_time_sql = $this->transform__timestamp__to_sql_date( $end_time_sec );

		} else if ( 3 == $situation ) {           // Check In Day  -----------------------------------------------------    '14:00:01 -  ...  '                   '14:00:01 - 24:00:00'

			$start_date_sec = $timestamp_seconds__only_date_ymd + $in_seconds__timerange__arr[0] - $extra_seconds;
			$start_date_sql = $this->transform__timestamp__to_sql_date( $start_date_sec );                                // 2023-07-31 10:00:01

			$end_time_sec = $timestamp_seconds__only_date_ymd + $this->transform__24_hours__in__seconds( '23:59:50' );
			/**
			 * We do not use here this usual code:
			 *                                      $end_time_sec = $timestamp_seconds__only_date_ymd + $in_seconds__timerange__arr[1];
			 * Because:
			 *          $in_seconds__timerange__arr[1] = '24:00:00'
			 * and it's make next day
			 *
			 * For example, if we have '2023-09-26' and times: '14:00:01 - 24:00:00'
			 * then if we extend on 1 day = 86400 s.
			 * we will have: '2023-09-26 24:00:00' + 86400 s. = '2023-09-27 00:00:00'  instead of   '2023-09-26 24:00:00'
			 * that's why we set  here time '23:59:50'  and get  this:                              '2023-09-26 23:59:50'
			 */
			$end_time_sql = $this->transform__timestamp__to_sql_date( $end_time_sec );                                    // 2023-08-01 12:00:02

		} else if ( 4 == $situation ) {          // Check Out Day  -----------------------------------------------------    '  ...  - 12:00:02'                   '00:00:00 - 12:00:02'

			$start_date_sec = $timestamp_seconds__only_date_ymd + $this->transform__24_hours__in__seconds( '00:00:10' );
			$start_date_sql = $this->transform__timestamp__to_sql_date( $start_date_sec );

			$end_time_sec = $timestamp_seconds__only_date_ymd + $in_seconds__timerange__arr[1] + $extra_seconds;
			$end_time_sql = $this->transform__timestamp__to_sql_date( $end_time_sec );

		} else if ( 6 == $situation ) {           // Full IN  ----------------------------------------------------------    '00:00:01 -  ...  '                   '00:00:01 - 24:00:00'

			$start_date_sec = $timestamp_seconds__only_date_ymd + 1 - $extra_seconds;
			$start_date_sql = $this->transform__timestamp__to_sql_date( $start_date_sec );                                // 2023-08-31 00:00:01

			$end_time_sec = $timestamp_seconds__only_date_ymd + $this->transform__24_hours__in__seconds( '00:00:00' );  // This '... - 24:00:00' will be fully booked, simply set '2023-07-31 00:00:00'
			$end_time_sql = $this->transform__timestamp__to_sql_date( $end_time_sec );        // 2023-09-01 00:00:00

		} else if ( 7 == $situation ) {           // Full OUT  ---------------------------------------------------------    '  ...  - 24:00:02'                   '00:00:00 - 24:00:02'

			$start_date_sec = $timestamp_seconds__only_date_ymd + $this->transform__24_hours__in__seconds( '00:00:00' ); // This '00:00:00 - ...' will be fully booked, simply set '2023-07-31 00:00:00'
			$start_date_sql = $this->transform__timestamp__to_sql_date( $start_date_sec );

			$end_time_sec = $timestamp_seconds__only_date_ymd + $this->transform__24_hours__in__seconds( '23:59:52' ) + $extra_seconds;
			$end_time_sql = $this->transform__timestamp__to_sql_date( $end_time_sec );

		} else if ( 8 == $situation ) {           // Full IN | OUT  ---------------------------------------------------------    '00:00:01 - 24:00:02'            '00:00:01 - 24:00:02'

			if ( $is_check_in ) {
				$start_date_sec = $timestamp_seconds__only_date_ymd + $this->transform__24_hours__in__seconds( '00:00:01' ) - $extra_seconds;
			} else {
				$start_date_sec = $timestamp_seconds__only_date_ymd + $this->transform__24_hours__in__seconds( '00:00:00' );
			}
			$start_date_sql = $this->transform__timestamp__to_sql_date( $start_date_sec );

			if ( ! $is_check_in ) {
				$end_time_sec = $timestamp_seconds__only_date_ymd + $this->transform__24_hours__in__seconds( '23:59:52' ) + $extra_seconds;
			} else {
				$end_time_sec = $timestamp_seconds__only_date_ymd + $this->transform__24_hours__in__seconds( '00:00:00' );
			}
			$end_time_sql = $this->transform__timestamp__to_sql_date( $end_time_sec );                                    // 2023-08-01 12:00:02

		} else {
			echo "WPBC. Unexpected situation: {$situation}. We have checked already all variation.";                // It has never happened
			debuge( ' $only_date_sql, $readable_time_range, $extra_seconds', $only_date_sql, $readable_time_range, $extra_seconds );
		}


		// Fill dates between 2 points,  or simply  get  start  and end points
		$extended_time_arr = $this->fill_by_dates__between__start_end_points(   array(
																					  'start_date_sql' => $start_date_sql, 'start_date_sec' => $start_date_sec
																					, 'end_time_sql'   => $end_time_sql ,  'end_time_sec'   => $end_time_sec
																				)
																				, $extra_seconds_arr );

		return $extended_time_arr;
	}


		// GOOD
		/**
		 * Get extra dates between 2 points
		 *
		 * @param array $params                   = array(
		 *                                                              start_date_sec =                        1690884001
		 *                                                            , start_date_sql = "2023-08-01 10:00:01"
		 *                                                            , end_time_sec =                          1691064002
		 *                                                            , end_time_sql =   "2023-08-03 12:00:02"
		 *                                               )
		 * @param array $extra_seconds_arr        [ 86400, 172800 ]
		 *
		 * @return array                        = [
		 * 		    								0 = "2023-08-01 10:00:01"
		 * 	    									1 = "2023-08-02 00:00:00"
		 * `										3 = "2023-08-03 12:00:02"
		 *                                        ]
		 *
		 *
		 * Example #1:
		 *
		 * $this->fill_by_dates__between__start_end_points(  array(
		 *                                                              start_date_sec =                        1690797601
		 *                                                            , start_date_sql = "2023-07-31 10:00:01"
		 *                                                            , end_time_sec =                          1690891202
		 *                                                            , end_time_sql =   "2023-08-01 12:00:02"
		 *                                                 ),  [ 86400 ] )
		 *                                = [
	     *									  0 = "2023-07-31 10:00:01"
		 *   								  2 = "2023-08-01 12:00:02"
		 *                                  ]
		 * Example #2:
		 *
		 * $this->fill_by_dates__between__start_end_points(  array(
		 *                                                              start_date_sec =                        1690883101
		 *                                                            , start_date_sql = "2023-08-01 09:45:01"
		 *                                                            , end_time_sec =                          1690891202
		 *                                                            , end_time_sql =   "2023-08-01 12:00:02"
		 *                                                 ),  [ 900 ] )
		 *                                = [
		 *                                     0 = "2023-08-01 09:45:01"
		 *                                     2 = "2023-08-01 12:00:02"
		 *                                  ]
		 */
		private function fill_by_dates__between__start_end_points( $params , $extra_seconds_arr ){

			$extended_time_arr = array();

			// 1. Start date      ------------------------------------------------------------------------------------------
			$extended_time_arr[] = $params[ 'start_date_sql' ];                                                             // 2023-07-31 10:00:01

			// 2. Between dates   ------------------------------------------------------------------------------------------

			foreach ( $extra_seconds_arr as $extra_seconds_int ) {                                                          // [ 38600 ]

				$possible_next_full_day =  $this->transform__timestamp__to_sql_date(  $params[ 'start_date_sec' ] + $extra_seconds_int
																				    , 'Y-m-d 00:00:00' );
				$possible_next_full_day_seconds = $this->transform__sql_date_ymd_his__in__timestamp( $possible_next_full_day );

				if (
					  ( $params[ 'start_date_sec' ] < $possible_next_full_day_seconds )
					&&( ( $params[ 'start_date_sec' ] + $extra_seconds_int ) < $params[ 'end_time_sec' ] )
				){
					// FULL dates adding  				     																// [ 2023-07-31 10:00:01, 2023-08-01 00:00:00 ]
					$extended_time_arr[] = $this->transform__timestamp__to_sql_date(
	                                                                                $params[ 'start_date_sec' ] + $extra_seconds_int
																				  , 'Y-m-d 00:00:00' );
				}
			}

			$last_added_date = $extended_time_arr[ count( $extended_time_arr ) - 1 ];                                       // Get last added       2023-08-01 00:00:00
			if (
				    ( substr( $last_added_date, 0, 10 ) == substr( $params[ 'end_time_sql' ], 0, 10 ) )                     // "2023-08-01" 00:00:00      = DAY =         "2023-08-01" 12:00:02
			     && ( count( $extended_time_arr ) > 1 )                                                                     // if it's count = 1,  then  here only start day
			) {
				unset( $extended_time_arr[ count( $extended_time_arr ) - 1 ] );
			}

			// 3. End date        ------------------------------------------------------------------------------------------
			$extended_time_arr[] = $params[ 'end_time_sql' ];

			$extended_time_arr = array_unique( $extended_time_arr );

			return $extended_time_arr;
		}
}



/**
 *  Extend unavailable interval to extra days/hours/minutes  -- cleaning time, or any other service time
 *
 * @param $resources__booking_id__obj     =   [ > resource_id < ][ > booking_id < ] => obj( ... )
 *
 * @return mixed
 */
function wpbc__extend_availability__before_after__check_in_out( $resources__booking_id__obj ){

	list( $extra_seconds_in, $extra_seconds_out ) = wpbc_get_from_settings__extra_seconds__before_after();

	// None
	if ( ( empty( $extra_seconds_in ) ) && ( empty( $extra_seconds_out ) ) ) {
		return $resources__booking_id__obj;
	}


	// [ > resource_id < ][ > booking_id < ] => obj(  ['__summary__booking']['sql__booking_dates__arr'] [  ... ,  [ ' >seconds< '] => ' > SQL DATE < ' , ...  ]  )
	foreach ( $resources__booking_id__obj as $resource_id => $bookings_arr ) {

		foreach ( $bookings_arr as $booking_id => $booking_obj ) {

			$readable_dates = $resources__booking_id__obj[ $resource_id ][ $booking_id ]->__summary__booking['__dates_obj']->readable_dates;

			$booking_date_extended_obj = new WPBC_BOOKING_DATE_EXTENDED( $readable_dates );
			$booking_date_extended_obj->extend_readable_dates__on_extra_seconds__in_out( $extra_seconds_in, $extra_seconds_out );


			$resources__booking_id__obj[$resource_id][$booking_id]->__summary__booking['__dates_obj__bm__extended'] = $booking_date_extended_obj;
			$resources__booking_id__obj[$resource_id][$booking_id]->__summary__booking['sql__booking_dates__arr__extended'] = $booking_date_extended_obj->convert_readable_dates__into__sql_dates__arr();
		}
	}

	return $resources__booking_id__obj;
}



/**
 * Is day available relative to 'Limit available days from today: '
 *
 * @param string $my_day_tag       '2023-08-03'
 *
 * @return true
 */
function wpbc_is_day_available__relative__limit_available_from_today($my_day_tag){

	$wpbc_available_days_num_from_today = intval( get_bk_option( 'booking_available_days_num_from_today' ) );

	if ( $wpbc_available_days_num_from_today > 0 ) {

		$days_number = ( strtotime( '+' . $wpbc_available_days_num_from_today . ' days' ) - strtotime( $my_day_tag ) ) / 86400;
		if ( ( $days_number  ) > 0 ) {
			return true;
		} else {
			return false;
		}
	}

	return true;
}


/**
 * EXTEND_CHECKING_DATES_RANGE__IF_USED__EXTRA_SECONDS__BEFORE_AFTER
 *
 * @param $sql_dates_arr__to_extend     ["2023-11-05","2023-11-06","2023-11-07"]
 *
 * @return array
 */
function wpbc__extend_checking_dates_range__if_used__extra_seconds__before_after( $sql_dates_arr__to_extend ) {

	list( $extra_seconds_in, $extra_seconds_out ) = wpbc_get_from_settings__extra_seconds__before_after();

	if ( ( ! empty( $extra_seconds_in ) ) || ( ! empty( $extra_seconds_out ) ) ) {

		/**
		 * Prepare our dates in specific format,  which  is required for WPBC_BOOKING_DATE_EXTENDED
		 */
		// [ '2023-08-01': [  0: "10:00:01 - 12:00:02" ], '2023-08-05': [ 0:"10:00:01 - 12:00:02" ] ...]
		$readable_dates = array();
		foreach ( $sql_dates_arr__to_extend as $date_num => $sql_date_to_check ) {
			if ( 0 == $date_num ) {
				$readable_dates[ $sql_date_to_check ] = array( '00:00:01 - 24:00:00' );
			} else if ( $date_num == ( count( $sql_dates_arr__to_extend ) - 1 ) ) {
				$readable_dates[ $sql_date_to_check ] = array( '00:00:00 - 23:59:52' );
			} else {
				$readable_dates[ $sql_date_to_check ] = array( '00:00:00 - 24:00:00' );
			}
			if ( 1 == count( $sql_dates_arr__to_extend ) ) {
				$readable_dates[ $sql_date_to_check ] = array( '00:00:01 - 23:59:52' );
			}
		}

		$booking_date_extended_obj = new WPBC_BOOKING_DATE_EXTENDED( $readable_dates );

		// Important we need to  switch  the    $extra_seconds_out <-> $extra_seconds_in  in arguments here
		$booking_date_extended_obj->extend_readable_dates__on_extra_seconds__in_out( $extra_seconds_out, $extra_seconds_in );

		// ["2023-09-26 00:00:00", "2023-09-27 00:0...", "2023-09-28 00:0...", "2023-09-30 00:0..."]
		$sql_dates_arr__to_extend = $booking_date_extended_obj->convert_readable_dates__into__sql_dates__arr();

		// ["2023-09-26", "2023-09-27", "2023-09-28", "2023-09-30"]
		$sql_dates_arr__to_extend = array_keys( $booking_date_extended_obj->readable_dates );


		// Get new extended check IN/OUT dates
		$sqldate__check_in  = $sql_dates_arr__to_extend[0];
		$sqldate__check_out = $sql_dates_arr__to_extend[ count( $sql_dates_arr__to_extend ) - 1 ];


		// Fill  all  dates from  $sqldate__check_in to  $sqldate__check_out  ----------------------------------
		$sql_dates_arr__to_extend   = array();
		$sql_dates_arr__to_extend[] = $sqldate__check_in;

		$work_date_timestamp = strtotime( $sqldate__check_in . ' 00:00:00' ) + 24 * 60 * 60;
		while ( strtotime( $sqldate__check_out . ' 00:00:00' ) >= $work_date_timestamp ) {
			$sql_dates_arr__to_extend[] = date( 'Y-m-d', $work_date_timestamp );
			$work_date_timestamp        = $work_date_timestamp + 24 * 60 * 60;
		}
	}

	return $sql_dates_arr__to_extend;
}