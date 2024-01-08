<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;
	if ( ! $product ) return;

	$product_id 	= $product->get_id();
	$show_phone 	= get_option('ova_brw_request_booking_form_show_number', 'yes');
	$show_address 	= get_option('ova_brw_request_booking_form_show_address', 'yes');
	$show_quantity 	= get_option( 'ova_brw_request_booking_form_show_number_vehicle', 'no' );
	$dropoff_date_setting = get_post_meta( $product_id, 'ovabrw_dropoff_date_by_setting', true );

	// Product settings
	$rental_type 		= get_post_meta( $product_id, 'ovabrw_price_type', true );
	$show_pickup_loc 	= ovabrw_rq_show_pick_location_product( $product_id, 'pickup' );
	$show_pickoff_loc 	= ovabrw_rq_show_pick_location_product( $product_id, 'dropoff' );
	$show_pickup_date 	= ovabrw_show_rq_pick_date_product( $product_id, 'pickup' );
	$show_pickoff_date 	= ovabrw_show_rq_pick_date_product( $product_id, 'dropoff' );
	$defined_one_day  	= defined_one_day( $product_id );

	$class_no_time_picker = '';
	if ( $defined_one_day == 'hotel' ) {
		$class_no_time_picker = 'no_time_picker';
	}

	$dateformat 		= ovabrw_get_date_format();
	$timeformat 		= ovabrw_get_time_format();
	$placeholder_date 	= ovabrw_get_placeholder_date();
	$placeholder_time 	= ovabrw_get_placeholder_time();

	// Get booked time
	$statuses 			= brw_list_order_status();
	$order_time 		= get_order_rent_time( $product_id, $statuses );
	$default_hour_start = ovabrw_get_default_time( $product_id, 'start' );
	$default_hour_end 	= ovabrw_get_default_time( $product_id, 'end' );
	$timepicker_start 	= ovabrw_timepicker_product( $product_id, 'start' );
	$timepicker_end 	= ovabrw_timepicker_product( $product_id, 'end' );
	$time_to_book_start = ovabrw_time_to_book( $product_id, 'start' );
	$time_to_book_end 	= ovabrw_time_to_book( $product_id, 'end' );

	$class_date_picker_period 	= $rental_type == 'period_time' ? 'no_time_picker' : '';
	$ovabrw_unfixed_time 		= get_post_meta( $product_id, 'ovabrw_unfixed_time', true );

	if ( $rental_type == 'period_time' ) {
		if ( $ovabrw_unfixed_time == 'yes' ) {
			$class_date_picker_period = '';
		} 
	}

	// Get Date, Location from URl
	$choose_hour_pickup 	= $class_date_picker_period == 'no_time_picker' || $class_no_time_picker == 'no_time_picker' ? 'no' : 'yes';
	$choose_hour_dropoff 	= $class_no_time_picker == 'no_time_picker' ? 'no' : 'yes';
	$pickup_date 			= ovabrw_get_current_date_from_search( $choose_hour_pickup, 'pickup_date', $product_id );
	$dropoff_date 			= ovabrw_get_current_date_from_search( $choose_hour_dropoff, 'dropoff_date', $product_id );
	$pickup_loc 			= isset( $_GET['pickup_loc'] ) ? $_GET['pickup_loc'] : '';
	$pickoff_loc 			= isset( $_GET['pickoff_loc'] ) ? $_GET['pickoff_loc'] : '';
	$st_pickup_loc   		= get_post_meta( $product_id, 'ovabrw_st_pickup_loc', true );
	$st_dropoff_loc  		= get_post_meta( $product_id, 'ovabrw_st_dropoff_loc', true );
	$pickup_time 			= strtotime( $pickup_date ) ? date( $timeformat, strtotime( $pickup_date ) ) : '';

	$class_pickup_date = '';
	if ( $class_date_picker_period ) $class_pickup_date .= ' '.$class_date_picker_period;
	if ( $class_no_time_picker ) $class_pickup_date .= ' '.$class_no_time_picker;
?>
<div class="rental_item">
	<label><?php esc_html_e( 'Name', 'ova-brw' ); ?></label>
	<div class="error_item">
		<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
	</div>
	<input
		type="text"
		class="required"
		name="name"
		placeholder="<?php esc_html_e( 'Your name', 'ova-brw' ); ?>"
	/>
</div>
<div class="rental_item">
	<label><?php esc_html_e( 'Email', 'ova-brw' ); ?></label>
	<div class="error_item">
		<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
	</div>
	<input
		type="text"
		class="required"
		name="email"
		placeholder="<?php esc_html_e( 'your_email@gmail.com', 'ova-brw' ); ?>"
	/>	
</div>
<?php if ( $show_phone === 'yes' ): ?>
	<div class="rental_item">
		<label><?php esc_html_e( 'Phone', 'ova-brw' ); ?></label>
		<div class="error_item">
			<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
		</div>
		<input
			type="text"
			class="required"
			name="number"
			placeholder="<?php esc_html_e( 'Your phone', 'ova-brw' ); ?>"
		/>
	</div>
<?php endif; ?>
<?php if ( $show_address === 'yes' ): ?>
	<div class="rental_item">
		<label><?php esc_html_e( 'Address', 'ova-brw' ); ?></label>
		<div class="error_item">
		<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
	</div>
		<input
			type="text"
			class="required"
			name="address"
			placeholder="<?php esc_html_e( 'Your address', 'ova-brw' ); ?>"
		/>	
	</div>
<?php endif; ?>
<?php if ( $rental_type === 'day' || $rental_type === 'hour' || $rental_type === 'mixed'  ): ?>
	<!-- Day, Hour, Mixed -->
	<?php if ( $show_pickup_loc ): ?>
		<div class="rental_item">
			<label><?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?></label>
			<div class="error_item">
				<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
			</div>
			<?php
				if ( ! empty( $st_pickup_loc ) && ! empty( $st_dropoff_loc ) ) {
					echo ovabrw_get_setup_locations_html( 'ovabrw_pickup_loc', 'required', $pickup_loc, $product_id, 'pickup' );
				} else {
					echo ovabrw_get_locations_html( 'ovabrw_pickup_loc', 'required', $pickup_loc, $product_id, 'pickup' );
				}
			?>
			<div class="ovabrw-other-location"></div>
		</div>
	<?php endif; ?>
	<?php if ( $show_pickoff_loc ): ?>
		<div class="rental_item">
			<label><?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?></label>
			<div class="error_item">
				<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
			</div>
			<?php
				if ( ! empty( $st_pickup_loc ) && ! empty( $st_dropoff_loc ) ) {
					echo ovabrw_get_setup_locations_html( 'ovabrw_pickoff_loc', 'required', $pickoff_loc, $product_id, 'dropoff', $pickup_loc ); 
				} else {
					echo ovabrw_get_locations_html( 'ovabrw_pickoff_loc', 'required', $pickoff_loc, $product_id, 'dropoff' ); 
				}
			?>
			<div class="ovabrw-other-location"></div>
		</div>
	<?php endif; ?>
	<div class="rental_item">
		<label><?php echo esc_html( ovabrw_get_label_pickup_date( $product_id ) ); ?></label>
		<div class="error_item">
			<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
		</div>
		<input
			type="text"
			name="pickup_date"
			default_hour="<?php echo esc_attr( $default_hour_start ); ?>"
			time_to_book="<?php echo esc_attr( $time_to_book_start ); ?>"
			class="required ovabrw_start_date ovabrw_datetimepicker<?php echo esc_attr( $class_pickup_date ); ?>"
			placeholder="<?php echo esc_attr( $placeholder_date ); ?>"
			value="<?php echo esc_attr( $pickup_date ); ?>"
			order_time="<?php echo esc_attr( $order_time ); ?>"
			timepicker="<?php echo esc_attr( $timepicker_start ); ?>"
			autocomplete="off"
			onfocus="blur();"
		/>	
	</div>
	<?php if ( $show_pickoff_date ): ?>
		<div class="rental_item">
			<label><?php echo esc_html( ovabrw_get_label_pickoff_date( $product_id ) ); ?></label>
			<div class="error_item">
				<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
			</div>
			<input
				type="text"
				name="pickoff_date"
				default_hour="<?php echo esc_attr( $default_hour_end ); ?>"
				time_to_book="<?php echo esc_attr( $time_to_book_end ); ?>"
				class="required ovabrw_end_date ovabrw_datetimepicker <?php echo esc_attr( $class_no_time_picker ); ?>"
				placeholder="<?php echo esc_attr( $placeholder_date ); ?>"
				value="<?php echo esc_attr( $dropoff_date ); ?>"
				order_time="<?php echo esc_attr( $order_time ); ?>"
				timepicker="<?php echo esc_attr( $timepicker_end ); ?>"
				autocomplete="off"
				onfocus="blur();"
			/>	
		</div>
	<?php endif; ?>
	<!-- End Day, Hour, Mixed -->
<?php endif; ?>
<?php if ( $rental_type === 'period_time' ): ?>
	<!-- Period of Time -->
	<?php if ( $show_pickup_loc ): ?>
		<div class="rental_item">
			<label><?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?></label>
			<div class="error_item">
				<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
			</div>
			<?php
				if ( ! empty( $st_pickup_loc ) && ! empty( $st_dropoff_loc ) ) {
					echo ovabrw_get_setup_locations_html( 'ovabrw_pickup_loc', 'required', $pickup_loc, $product_id, 'pickup' );
				} else {
					echo ovabrw_get_locations_html( 'ovabrw_pickup_loc', 'required', $pickup_loc, $product_id, 'pickup' );
				}
			?>
			<div class="ovabrw-other-location"></div>
		</div>
	<?php endif; ?>
	<?php if ( $show_pickoff_loc ): ?>
		<div class="rental_item">
			<label><?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?></label>
			<div class="error_item">
				<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
			</div>
			<?php
				if ( ! empty( $st_pickup_loc ) && !empty( $st_dropoff_loc ) ) {
					echo ovabrw_get_setup_locations_html( 'ovabrw_pickoff_loc', 'required', $pickoff_loc, $product_id, 'dropoff', $pickup_loc ); 
				} else {
					echo ovabrw_get_locations_html( 'ovabrw_pickoff_loc', 'required', $pickoff_loc, $product_id, 'dropoff' ); 
				}
			?>
			<div class="ovabrw-other-location"></div>
		</div>
	<?php endif; ?>
	<div class="rental_item">
		<label><?php echo esc_html( ovabrw_get_label_pickup_date( $product_id ) ); ?></label>
		<div class="error_item">
			<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
		</div>
		<input
			type="text"
			name="pickup_date"
			default_hour="<?php echo esc_attr( $default_hour_start ); ?>"
			time_to_book="<?php echo esc_attr( $time_to_book_start ); ?>"
			class="required ovabrw_start_date ovabrw_datetimepicker<?php echo esc_attr( $class_pickup_date ); ?>"
			placeholder="<?php echo esc_attr( $placeholder_date ); ?>"
			value="<?php echo esc_attr( $pickup_date ); ?>"
			order_time="<?php echo esc_attr( $order_time ); ?>"
			timepicker="<?php echo esc_attr( $timepicker_start ); ?>"
			autocomplete="off"
			onfocus="blur();"
		/>	
	</div>
	<?php
		$ovabrw_petime_id 		= get_post_meta( $product_id, 'ovabrw_petime_id', true );
		$ovabrw_petime_label 	= get_post_meta( $product_id, 'ovabrw_petime_label', true );

		if ( ! empty( $ovabrw_petime_id ) && ! empty( $ovabrw_petime_label ) ):
	?>
		<div class="rental_item">
			<label><?php esc_html_e( 'Choose Package', 'ova-brw' ); ?></label>
			<div class="error_item">
				<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
			</div>
			<div class="period_package">
				<select name="ovabrw_period_package_id" class="required">
					<option value=""><?php esc_html_e( 'Select Package', 'ova-brw' ); ?></option>
					<?php foreach ( $ovabrw_petime_id as $key => $value ): ?>
						<option value="<?php echo esc_attr( trim( $ovabrw_petime_label[$key] ) ); ?>">
							<?php echo esc_html( $ovabrw_petime_label[$key] ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	<?php endif; ?>
	<!-- End Period of Time -->
<?php endif; ?>
<?php if ( $rental_type === 'transportation' ): ?>
	<!-- Location -->
	<div class="rental_item">
		<label><?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?></label>
		<div class="error_item">
			<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
		</div>
		<?php
			echo ovabrw_get_locations_transport_html( 'ovabrw_pickup_loc', 'required', $pickup_loc, $product_id, 'pickup' );
		?>
		<div class="ovabrw-other-location"></div>
	</div>
	<div class="rental_item">
		<label><?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?></label>
		<div class="error_item">
			<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
		</div>
		<?php
			echo ovabrw_get_locations_transport_html( 'ovabrw_pickoff_loc', 'required', $pickoff_loc, $product_id, 'dropoff' );
		?>
		<div class="ovabrw-other-location"></div>
	</div>
	<div class="rental_item">
		<label><?php echo esc_html( ovabrw_get_label_pickup_date( $product_id ) ); ?></label>
		<div class="error_item">
			<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
		</div>
		<input
			type="text"
			name="pickup_date"
			default_hour="<?php echo esc_attr( $default_hour_start ); ?>"
			time_to_book="<?php echo esc_attr( $time_to_book_start ); ?>"
			class="required ovabrw_start_date ovabrw_datetimepicker<?php echo esc_attr( $class_pickup_date ); ?>"
			placeholder="<?php echo esc_attr( $placeholder_date ); ?>"
			value="<?php echo esc_attr( $pickup_date ); ?>"
			order_time="<?php echo esc_attr( $order_time ); ?>"
			timepicker="<?php echo esc_attr( $timepicker_start ); ?>"
			autocomplete="off"
			onfocus="blur();"
		/>	
	</div>
	<?php if ( $show_pickoff_date && $dropoff_date_setting === 'yes' ): ?>
		<div class="rental_item">
			<label><?php echo esc_html( ovabrw_get_label_pickoff_date( $product_id ) ); ?></label>
			<div class="error_item">
				<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
			</div>
			<input
				type="text"
				name="pickoff_date"
				default_hour="<?php echo esc_attr( $default_hour_end ); ?>"
				time_to_book="<?php echo esc_attr( $time_to_book_end ); ?>"
				class="required ovabrw_end_date ovabrw_datetimepicker <?php echo esc_attr( $class_no_time_picker ); ?>"
				placeholder="<?php echo esc_attr( $placeholder_date ); ?>"
				value="<?php echo esc_attr( $dropoff_date ); ?>"
				order_time="<?php echo esc_attr( $order_time ); ?>"
				timepicker="<?php echo esc_attr( $timepicker_end ); ?>"
				autocomplete="off"
				onfocus="blur();"
			/>	
		</div>
	<?php endif; ?>
	<!-- End Location -->
<?php endif; ?>
<?php if ( $rental_type === 'taxi' ):
	$ovabrw_price_by 	= get_post_meta( $product_id, 'ovabrw_map_price_by', true );
	$ovabrw_waypoint 	= get_post_meta( $product_id, 'ovabrw_waypoint', true );
	$ovabrw_zoom_map 	= get_post_meta( $product_id, 'ovabrw_zoom_map', true );
	$extra_time_hour 	= get_post_meta( $product_id, 'ovabrw_extra_time_hour', true );
	$extra_time_label 	= get_post_meta( $product_id, 'ovabrw_extra_time_label', true );
	$ovabrw_lat 		= get_post_meta( $product_id, 'ovabrw_latitude', true );
	$ovabrw_lng 		= get_post_meta( $product_id, 'ovabrw_longitude', true );

	if ( ! $ovabrw_price_by ) $ovabrw_price_by = 'km';

	if ( ! $ovabrw_lat ) {
		$ovabrw_lat = get_option( 'ova_brw_latitude_map_default', 39.177972 );
	}

	if ( ! $ovabrw_lng ) {
		$ovabrw_lng = get_option( 'ova_brw_longitude_map_default', -100.36375 );
	}

	$max_waypoint 	= get_post_meta( $product_id, 'ovabrw_max_waypoint', true );
	$map_types 		= get_post_meta( $product_id, 'ovabrw_map_types', true );
	$ovabrw_bounds 	= get_post_meta( $product_id, 'ovabrw_bounds', true );
	$bounds_lat 	= get_post_meta( $product_id, 'ovabrw_bounds_lat', true );
	$bounds_lng 	= get_post_meta( $product_id, 'ovabrw_bounds_lng', true );
	$bounds_radius 	= get_post_meta( $product_id, 'ovabrw_bounds_radius', true );
	$restrictions 	= get_post_meta( $product_id, 'ovabrw_restrictions', true );

	if ( ! $map_types ) $map_types = [ 'geocode' ];
	if ( ! $restrictions ) $restrictions = [];
?>
	<div class="rental_item">
		<label><?php esc_html_e( 'Pick-up Date', 'ova-brw' ); ?></label>
		<div class="error_item">
			<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
		</div>
		<input 
			type="text"
			name="pickup_date"
			class="required ovabrw_datetimepicker ovabrw_start_date"
			placeholder="<?php echo esc_attr( $placeholder_date ); ?>"
			value="<?php echo esc_attr( $pickup_date ); ?>"
			order_time="<?php echo esc_attr( $order_time ); ?>"
			data-pid="<?php echo esc_attr( $product_id ); ?>"
			timepicker="false"
			onfocus="blur();"
			autocomplete="off"
		/>
	</div>
	<div class="rental_item">
		<label>
			<?php esc_html_e( 'Pick-up Time' ); ?>
		</label>
		<div class="error_item">
			<label>
				<?php esc_html_e( 'This field is required', 'ova-brw' ); ?>
			</label>
		</div>
		<input 
			type="text"
			name="pickup_time"
			default_hour="<?php echo esc_attr( $default_hour_start ); ?>"
			time_to_book="<?php echo esc_attr( $time_to_book_start ); ?>"
			class="required ovabrw_timepicker ovabrw_start_time"
			placeholder="<?php echo esc_attr( $placeholder_time ); ?>"
			autocomplete="off"
			value="<?php echo esc_attr( $pickup_time ); ?>"
			data-pid="<?php echo esc_attr( $product_id ); ?>"
			onfocus="blur();"
		/>
	</div>
	<div class="rental_item form-location-field">
		<label>
			<?php esc_html_e( 'Pick-up Location' ); ?>
		</label>
		<div class="error_item">
			<label>
				<?php esc_html_e( 'This field is required', 'ova-brw' ); ?>
			</label>
		</div>
		<input 
			type="text"
			id="ovabrw_req_pickup_loc"
			class="required" 
			name="ovabrw_req_pickup_loc"
			autocomplete="off"
		/>
		<input
			type="hidden"
			id="ovabrw_req_origin"
			name="ovabrw_req_origin"
			value=""
		/>
		<?php if ( $ovabrw_waypoint === 'on' ): ?>
			<i aria-hidden="true" class="flaticon-add btn-req-add-waypoint"></i>
		<?php endif; ?>
	</div>
	<div class="rental_item form-location-field">
		<label>
			<?php esc_html_e( 'Drop-off Location' ); ?>
		</label>
		<div class="error_item">
			<label>
				<?php esc_html_e( 'This field is required', 'ova-brw' ); ?>
			</label>
		</div>
		<input 
			type="text"
			id="ovabrw_req_pickoff_loc"
			class="required"
			name="ovabrw_req_pickoff_loc"
			autocomplete="off"
		/>
		<input
			type="hidden"
			id="ovabrw_req_destination"
			name="ovabrw_req_destination"
			value=""
		/>
	</div>
	<div class="rental_item">
		<label>
			<?php esc_html_e( 'Extra Time', 'ova-brw' ); ?>
		</label>
		<?php if ( ! empty( $extra_time_hour ) && ! empty( $extra_time_label ) ): ?>
			<select name="ovabrw_req_extra_time">
				<option value=""><?php esc_html_e( 'Select Time', 'ova-brw' ); ?></option>
				<?php foreach ( $extra_time_hour as $k => $time ):
					$ext_label = isset( $extra_time_label[$k] ) ? $extra_time_label[$k] : '';
				?>
					<option value="<?php echo esc_attr( $time ); ?>">
						<?php echo esc_html( $ext_label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		<?php endif; ?>
	</div>
	<input
		type="hidden"
		name="ovabrw-req-data-location"
		data-price-by="<?php echo esc_attr( $ovabrw_price_by ); ?>"
		data-waypoint-text="<?php esc_html_e( 'Waypoint', 'ova-brw' ); ?>"
		data-max-waypoint="<?php echo esc_attr( $max_waypoint ); ?>"
		data-map-types="<?php echo esc_attr( json_encode( $map_types ) ); ?>"
		data-lat="<?php echo esc_attr( $ovabrw_lat ); ?>"
		data-lng="<?php echo esc_attr( $ovabrw_lng ); ?>"
		data-zoom="<?php echo esc_attr( $ovabrw_zoom_map ); ?>"
		data-bounds="<?php echo esc_attr( $ovabrw_bounds ); ?>"
		data-bounds-lat="<?php echo esc_attr( $bounds_lat ); ?>"
		data-bounds-lng="<?php echo esc_attr( $bounds_lng ); ?>"
		data-bounds-radius="<?php echo esc_attr( $bounds_radius ); ?>"
		data-restrictions="<?php echo esc_attr( json_encode( $restrictions ) ); ?>"
	/>
	<input
		type="hidden"
		name="ovabrw-req-duration-map"
		value=""
	/>
	<input
		type="hidden"
		name="ovabrw-req-duration"
		value=""
	/>
	<input
		type="hidden"
		name="ovabrw-req-distance"
		value=""
	/>
<?php endif; ?>
<?php if ( $show_quantity === 'yes' ): ?>
	<div class="rental_item">
		<label><?php esc_html_e( 'Quantity', 'ova-brw' ); ?></label>
		<div class="error_item">
			<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
		</div>
		<input
			type="number"
			class="required"
			name="ovabrw_number_vehicle"
			value="1"
			min="1"
		/>
	</div>
<?php endif; ?>
<?php if ( $rental_type === 'taxi' ):
	$price_by = get_post_meta( $product_id, 'ovabrw_map_price_by', true );

	if ( ! $price_by ) $price_by = 'km';
?>
	<div class="ovabrw-req-directions">
		<div id="ovabrw_req_map" class="ovabrw_req_map"></div>
		<div class="directions-info">
			<div class="distance-sum">
				<h3 class="label"><?php esc_html_e( 'Total Distance', 'ova-brw' ); ?></h3>
				<span class="distance-value">0</span>
				<?php if ( $price_by === 'km' ): ?>
					<span class="distance-unit"><?php esc_html_e( 'km', 'ova-brw' ); ?></span>
				<?php else: ?>
					<span class="distance-unit"><?php esc_html_e( 'mi', 'ova-brw' ); ?></span>
				<?php endif; ?>
			</div>
			<div class="duration-sum">
				<h3 class="label"><?php esc_html_e( 'Total Time', 'ova-brw' ); ?></h3>
				<span class="hour">0</span>
				<span class="unit"><?php esc_html_e( 'h', 'ova-brw' ); ?></span>
				<span class="minute">0</span>
				<span class="unit"><?php esc_html_e( 'm', 'ova-brw' ); ?></span>
			</div>
		</div>
	</div>
<?php endif; ?>