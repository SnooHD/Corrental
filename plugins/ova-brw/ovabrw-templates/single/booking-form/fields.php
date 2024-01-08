<?php
/**
 * The template for displaying fields in booking form within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/booking-form/fields.php
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit();

// Get product_id from do_action - use when insert shortcode
if ( isset( $args['product_id'] ) && $args['product_id'] ){
	$pid = $args['product_id'];
} else {
	$pid = get_the_id();
}

$ovabrw_rental_type 	= get_post_meta( $pid, 'ovabrw_price_type', true );
$defined_one_day 		= defined_one_day( $pid );
$class_no_time_picker 	= $defined_one_day == 'hotel' ? 'no_time_picker' : '';
$time_to_book_start 	= ovabrw_time_to_book( $pid, 'start' );
$time_to_book_end 		= ovabrw_time_to_book( $pid, 'end' );
$default_hour_start 	= ovabrw_get_default_time( $pid, 'start' );
$default_hour_end 		= ovabrw_get_default_time( $pid, 'end' );
$timepicker_start 		= ovabrw_timepicker_product( $pid, 'start' );
$timepicker_end 		= ovabrw_timepicker_product( $pid, 'end' );
$show_pickup_location 	= ovabrw_show_pick_location_product( $pid, $type = 'pickup' );
$show_pickoff_location 	= ovabrw_show_pick_location_product( $pid, $type = 'dropoff' );
$show_pickup_date 		= ovabrw_show_pick_date_product( $pid, $type = 'pickup' );
$show_pickoff_date 		= ovabrw_show_pick_date_product( $pid, $type = 'dropoff' );
$show_number_vehicle 	= ovabrw_show_number_vehicle( $pid );
$disable_week_day 		= get_post_meta( $pid, 'ovabrw_product_disable_week_day', true );
$dropoff_date_by_setting = get_post_meta( $pid, 'ovabrw_dropoff_date_by_setting', true );

// Get booked time
$statuses 	= brw_list_order_status();
$order_time = get_order_rent_time( $pid, $statuses );
$dateformat = ovabrw_get_date_format();
$timeformat = ovabrw_get_time_format();
$placeholder_date 			= ovabrw_get_placeholder_date();
$placeholder_time 			= ovabrw_get_placeholder_time();
$class_date_picker_period 	= $ovabrw_rental_type == 'period_time' ? 'no_time_picker' : '';
$ovabrw_unfixed_time 		= get_post_meta( $pid, 'ovabrw_unfixed_time', true );

$startdate_perido_time = '';

if ( ( $ovabrw_rental_type == 'period_time' ) ) {
	$startdate_perido_time = 'startdate_perido_time';

	if ( $ovabrw_unfixed_time == 'yes' ) {
		$class_date_picker_period = '';
	} 
}

// Get Date, Location from URl
$choose_hour_pickup 	= $class_date_picker_period == 'no_time_picker' || $class_no_time_picker == 'no_time_picker' ? 'no' : 'yes';
$choose_hour_dropoff 	= $class_no_time_picker == 'no_time_picker' ? 'no' : 'yes';
$pickup_date 	= ovabrw_get_current_date_from_search( $choose_hour_pickup, 'pickup_date', $pid );
$dropoff_date 	= ovabrw_get_current_date_from_search( $choose_hour_dropoff, 'dropoff_date', $pid );
$pickup_loc 	= isset( $_GET['pickup_loc'] ) ? $_GET['pickup_loc'] : '';
$pickoff_loc 	= isset( $_GET['pickoff_loc'] ) ? $_GET['pickoff_loc'] : '';
$pickup_time 	= strtotime( $pickup_date ) ? date( $timeformat, strtotime( $pickup_date ) ) : '';

// Get first day in week
$first_day = get_option( 'ova_brw_calendar_first_day', '0' );

if ( empty( $first_day ) ) {
	$first_day = 0;
}

// Get categories
$terms = wp_get_post_terms( $pid,'product_cat',array('fields'=>'ids') );
if ( $terms && is_array( $terms ) ) {
	$term_id = reset($terms);
}

// Get label pick-up and pick-off date
$label_pickup_date 			= esc_html__( 'Pick-up Date', 'ova-brw');
$setting_lable_pickup_date 	= get_post_meta( $pid, 'ovabrw_label_pickup_date_product', true );

if ( $setting_lable_pickup_date == 'new' ) {
	$label_pickup_date = get_post_meta( $pid, 'ovabrw_new_pickup_date_product', true );
} elseif ( $setting_lable_pickup_date == 'category' ) {
	$label_pickup_date 	= isset( $term_id ) ? get_term_meta( $term_id, 'ovabrw_lable_pickup_date', true ) : esc_html__( 'Pick-up Date', 'ova-brw');
} else {
	$label_pickup_date = esc_html__( 'Pick-up Date', 'ova-brw');
}

$label_dropoff_date 		= esc_html__( 'Drop-off Date', 'ova-brw');
$setting_lable_dropoff_date = get_post_meta( $pid, 'ovabrw_label_dropoff_date_product', true );

if ( $setting_lable_dropoff_date == 'new' ) {
	$label_dropoff_date = get_post_meta( $pid, 'ovabrw_new_dropoff_date_product', true );
} elseif ( $setting_lable_dropoff_date == 'category' ) {
	$label_dropoff_date = isset( $term_id ) ? get_term_meta( $term_id, 'ovabrw_lable_dropoff_date', true ) : esc_html__( 'Drop-off Date', 'ova-brw');
} else {
	$label_dropoff_date = esc_html__( 'Drop-off Date', 'ova-brw');
}

if ( $label_pickup_date == '' ) {
	$label_pickup_date = esc_html__( 'Pick-up Date', 'ova-brw');
}
if ( $label_dropoff_date == '' ) {
	$label_dropoff_date = esc_html__( 'Drop-off Date', 'ova-brw');
}

$ovabrw_st_pickup_loc   = get_post_meta( $pid, 'ovabrw_st_pickup_loc', true );
$ovabrw_st_dropoff_loc  = get_post_meta( $pid, 'ovabrw_st_dropoff_loc', true );

// Taxi
$ovabrw_price_by 	= get_post_meta( $pid, 'ovabrw_map_price_by', true );
$ovabrw_waypoint 	= get_post_meta( $pid, 'ovabrw_waypoint', true );
$ovabrw_zoom_map 	= get_post_meta( $pid, 'ovabrw_zoom_map', true );
$extra_time_hour 	= get_post_meta( $pid, 'ovabrw_extra_time_hour', true );
$extra_time_label 	= get_post_meta( $pid, 'ovabrw_extra_time_label', true );
$ovabrw_lat 		= get_post_meta( $pid, 'ovabrw_latitude', true );
$ovabrw_lng 		= get_post_meta( $pid, 'ovabrw_longitude', true );

if ( ! $ovabrw_price_by ) $ovabrw_price_by = 'km';

if ( ! $ovabrw_lat ) {
	$ovabrw_lat = get_option( 'ova_brw_latitude_map_default', 39.177972 );
}

if ( ! $ovabrw_lng ) {
	$ovabrw_lng = get_option( 'ova_brw_longitude_map_default', -100.36375 );
}


?>

<?php if ( $ovabrw_rental_type != 'taxi' ): ?>
	<?php if ( $show_pickup_location ): ?>
		<div class="rental_item">
			<label><?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?></label>
			<div class="error_item">
				<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
			</div>
			<?php
				if ( $ovabrw_rental_type !== 'transportation' ) {
					if ( !empty( $ovabrw_st_pickup_loc ) && !empty( $ovabrw_st_dropoff_loc ) ) {
						echo ovabrw_get_setup_locations_html( $class = 'ovabrw_pickup_loc', $required = 'required', $selected = $pickup_loc, $pid, 'pickup' );
					} else {
						echo ovabrw_get_locations_html( $class = 'ovabrw_pickup_loc', $required = 'required', $selected = $pickup_loc, $pid, 'pickup' );
					}
				} else {
					echo ovabrw_get_locations_transport_html( $class = 'ovabrw_pickup_loc', $required = 'required', $selected = $pickup_loc, $pid, 'pickup' );
				}
			?>
			<div class="ovabrw-other-location"></div>
		</div>
	<?php endif; ?>
	<?php if ( $show_pickoff_location ): ?>
		<div class="rental_item">
			<label><?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?></label>
			<div class="error_item">
				<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
			</div>
			<?php
				if ( $ovabrw_rental_type !== 'transportation' ) {
					if ( !empty( $ovabrw_st_pickup_loc ) && !empty( $ovabrw_st_dropoff_loc ) ) {
						echo ovabrw_get_setup_locations_html( $class = 'ovabrw_pickoff_loc', $required = 'required', $selected = $pickoff_loc, $pid, 'dropoff', $pickup_loc ); 
					} else {
						echo ovabrw_get_locations_html( $class = 'ovabrw_pickoff_loc', $required = 'required', $selected = $pickoff_loc, $pid, 'dropoff' ); 
					}
				} else {
					echo ovabrw_get_locations_transport_html( $class = 'ovabrw_pickoff_loc', $required = 'required', $selected = $pickoff_loc, $pid, 'dropoff' );
				}
			?>
			<div class="ovabrw-other-location"></div>
		</div>
	<?php endif; ?>
	<?php if ( $show_pickup_date ): ?>
		<div class="rental_item">
			<label>
				<?php echo esc_html( $label_pickup_date ); ?>
			</label>
			<div class="error_item">
				<label>
					<?php esc_html_e( 'This field is required', 'ova-brw' ); ?>
				</label>
			</div>
			<input 
				type="text" 
				name="ovabrw_pickup_date"  
				default_hour="<?php echo $default_hour_start; ?>"  
				time_to_book="<?php echo $time_to_book_start; ?>" 
				class="required ovabrw_datetimepicker ovabrw_start_date <?php echo esc_attr( $class_date_picker_period ) ?> <?php echo esc_attr( $class_no_time_picker ) ?> <?php echo esc_attr( $startdate_perido_time ); ?>" 
				placeholder="<?php echo esc_attr( $placeholder_date ); ?>" 
				autocomplete="off" 
				value="<?php echo $pickup_date; ?>" 
				order_time='<?php echo $order_time; ?>' 
				data-pid="<?php echo $pid; ?>"
				timepicker='<?php echo $timepicker_start; ?>' 
				data-firstday='<?php echo esc_attr( $first_day ); ?>' 
				data-disable-week-day='<?php echo esc_attr( $disable_week_day ); ?>' 
				onfocus="blur();"
			/>
		</div>
	<?php endif; ?>
	<!-- Check Rental type -->
	<?php if ( $ovabrw_rental_type == 'period_time'):
		$ovabrw_petime_id 		= get_post_meta( $pid, 'ovabrw_petime_id', true );
		$ovabrw_petime_label 	= get_post_meta( $pid, 'ovabrw_petime_label', true );
	?>
		<div class="rental_item">
			<label>
				<?php esc_html_e( 'Choose Package', 'ova-brw' ); ?>
			</label>
			<div class="error_item">
				<label>
					<?php esc_html_e( 'This field is required', 'ova-brw' ); ?>
				</label>
			</div>
			<div class="period_package">
				<select name="ovabrw_period_package_id" class="required">
					<option value=""><?php esc_html_e( 'Select Package', 'ova-brw' ); ?></option>
					<?php if ( $ovabrw_petime_id ) {
						foreach ( $ovabrw_petime_id as $key => $value ) { ?>
							<?php if ( isset( $ovabrw_petime_id[$key] ) && isset( $ovabrw_petime_label[$key] ) ) { ?>
								<option value="<?php echo esc_attr(trim( $ovabrw_petime_id[$key] ) ); ?>" > 
									<?php echo esc_html( $ovabrw_petime_label[$key] ); ?> 
								</option>
					<?php } } } ?>
				</select>
			</div>
		</div>
	<?php elseif ( $ovabrw_rental_type != 'transportation' ): ?>
		<?php if ( $show_pickoff_date ): ?>
			<div class="rental_item">
				<label>
					<?php echo esc_html( $label_dropoff_date ); ?>
				</label>
				<div class="error_item">
					<label>
						<?php esc_html_e( 'This field is required', 'ova-brw' ); ?>
					</label>
				</div>
				<input 
					type="text" 
					name="ovabrw_pickoff_date" 
					default_hour="<?php echo $default_hour_end; ?>"  
					time_to_book="<?php echo $time_to_book_end; ?>"  
					class="required ovabrw_datetimepicker ovabrw_end_date <?php echo esc_attr( $class_no_time_picker ); ?>" 
					placeholder="<?php echo esc_attr( $placeholder_date ); ?>"   
					autocomplete="off" 
					value="<?php echo $dropoff_date; ?>"   
					order_time='<?php echo $order_time; ?>' 
					timepicker='<?php echo $timepicker_end; ?>' 
					data-firstday='<?php echo esc_attr( $first_day ); ?>' 
					data-disable-week-day='<?php echo esc_attr( $disable_week_day ); ?>' 
					onfocus="blur();" 
				/>
			</div>
		<?php endif; ?>
	<?php elseif ( $ovabrw_rental_type === 'transportation' ): ?>
		<?php if ( $show_pickoff_date && $dropoff_date_by_setting === 'yes' ): ?>
			<div class="rental_item">
				<label>
					<?php echo esc_html( $label_dropoff_date ); ?>
				</label>
				<div class="error_item">
					<label>
						<?php esc_html_e( 'This field is required', 'ova-brw' ); ?>
					</label>
				</div>
				<input 
					type="text" 
					name="ovabrw_pickoff_date" 
					default_hour="<?php echo $default_hour_end; ?>"  
					time_to_book="<?php echo $time_to_book_end; ?>"  
					class="required ovabrw_datetimepicker ovabrw_end_date <?php echo esc_attr( $class_no_time_picker ); ?>" 
					placeholder="<?php echo esc_attr( $placeholder_date ); ?>"   
					autocomplete="off" 
					value="<?php echo $dropoff_date; ?>"   
					order_time='<?php echo $order_time; ?>' 
					timepicker='<?php echo $timepicker_end; ?>' 
					data-firstday='<?php echo esc_attr( $first_day ); ?>' 
					data-disable-week-day='<?php echo esc_attr( $disable_week_day ); ?>' 
					onfocus="blur();" 
				/>
			</div>
		<?php endif; ?>
	<?php endif; ?>
<?php else:
	$max_waypoint 	= get_post_meta( $pid, 'ovabrw_max_waypoint', true );
	$map_types 		= get_post_meta( $pid, 'ovabrw_map_types', true );
	$ovabrw_bounds 	= get_post_meta( $pid, 'ovabrw_bounds', true );
	$bounds_lat 	= get_post_meta( $pid, 'ovabrw_bounds_lat', true );
	$bounds_lng 	= get_post_meta( $pid, 'ovabrw_bounds_lng', true );
	$bounds_radius 	= get_post_meta( $pid, 'ovabrw_bounds_radius', true );
	$restrictions 	= get_post_meta( $pid, 'ovabrw_restrictions', true );

	if ( ! $map_types ) $map_types = [ 'geocode' ];
	if ( ! $restrictions ) $restrictions = [];
?>
	<div class="rental_item">
		<label>
			<?php echo $label_pickup_date; ?>
		</label>
		<div class="error_item">
			<label>
				<?php esc_html_e( 'This field is required', 'ova-brw' ); ?>
			</label>
		</div>
		<input 
			type="text"
			name="ovabrw_pickup_date"
			class="required ovabrw_datetimepicker ovabrw_start_date"
			placeholder="<?php echo esc_attr( $placeholder_date ); ?>"
			autocomplete="off"
			value="<?php echo $pickup_date; ?>"
			order_time='<?php echo $order_time; ?>'
			data-pid="<?php echo $pid; ?>"
			timepicker='false' 
			data-firstday='<?php echo esc_attr( $first_day ); ?>'
			data-disable-week-day='<?php echo esc_attr( $disable_week_day ); ?>'
			onfocus="blur();"
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
			name="ovabrw_pickup_time"
			default_hour="<?php echo $default_hour_start; ?>"
			time_to_book="<?php echo $time_to_book_start; ?>"
			class="required ovabrw_timepicker ovabrw_start_time"
			placeholder="<?php echo esc_attr( $placeholder_time ); ?>"
			autocomplete="off"
			value="<?php echo esc_attr( $pickup_time ); ?>"
			data-pid="<?php echo esc_attr( $pid ); ?>"
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
			id="ovabrw_pickup_loc"
			class="required" 
			name="ovabrw_pickup_loc"
			autocomplete="off"
		/>
		<input
			type="hidden"
			id="ovabrw_origin"
			name="ovabrw_origin"
			value=""
		/>
		<?php if ( $ovabrw_waypoint === 'on' ): ?>
			<i aria-hidden="true" class="flaticon-add btn-add-waypoint"></i>
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
			id="ovabrw_pickoff_loc"
			class="required"
			name="ovabrw_pickoff_loc"
			autocomplete="off"
		/>
		<input
			type="hidden"
			id="ovabrw_destination"
			name="ovabrw_destination"
			value=""
		/>
	</div>
	<div class="rental_item">
		<label>
			<?php esc_html_e( 'Extra Time', 'ova-brw' ); ?>
		</label>
		<?php if ( ! empty( $extra_time_hour ) && ! empty( $extra_time_label ) ): ?>
			<select name="ovabrw_extra_time">
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
		name="ovabrw-data-location"
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
		name="ovabrw-duration-map"
		value=""
	/>
	<input
		type="hidden"
		name="ovabrw-duration"
		value=""
	/>
	<input
		type="hidden"
		name="ovabrw-distance"
		value=""
	/>
<?php endif; ?>
<?php if ( $show_number_vehicle === 'yes' ): 
	$total_number_vehicle = ovabrw_get_total_stock( $pid );
?>
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
			max="<?php echo esc_attr( $total_number_vehicle ); ?>"
		/>
	</div>
<?php endif; ?>
<?php if ( $ovabrw_rental_type === 'taxi' ):
	$price_by = get_post_meta( $pid, 'ovabrw_map_price_by', true );

	if ( ! $price_by ) $price_by = 'km';
?>
	<div class="ovabrw-directions">
		<div id="ovabrw_map" class="ovabrw_map"></div>
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