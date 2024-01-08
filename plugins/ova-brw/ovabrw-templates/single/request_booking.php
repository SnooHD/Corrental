<?php 
/**
 * The template for displaying request booking form content within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/request_booking.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

// Get id from do_action - use when insert shortcode
if ( isset( $args['id'] ) && $args['id'] ) {
	$pid = $args['id'];
} else {
	$pid = get_the_id();
}

// Check product type: rental
$product = wc_get_product( $pid );
if ( !$product || $product->get_type() !== 'ovabrw_car_rental' ) return;

$ovabrw_rental_type 	= get_post_meta( $pid, 'ovabrw_price_type', true );
$show_pickup_location 	= ovabrw_rq_show_pick_location_product( $pid, $type = 'pickup' );
$show_pickoff_location 	= ovabrw_rq_show_pick_location_product( $pid, $type = 'dropoff' );
$show_pickup_date 		= ovabrw_show_rq_pick_date_product( $pid, $type = 'pickup' );
$show_pickoff_date 		= ovabrw_show_rq_pick_date_product( $pid, $type = 'dropoff' );
$defined_one_day 		= defined_one_day( $pid );

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
$order_time 		= get_order_rent_time( $pid, $statuses );
$default_hour_start = ovabrw_get_default_time( $pid, 'start' );
$default_hour_end 	= ovabrw_get_default_time( $pid, 'end' );
$timepicker_start 	= ovabrw_timepicker_product( $pid, 'start' );
$timepicker_end 	= ovabrw_timepicker_product( $pid, 'end' );
$time_to_book_start = ovabrw_time_to_book( $pid, 'start' );
$time_to_book_end 	= ovabrw_time_to_book( $pid, 'end' );

$class_date_picker_period 	= $ovabrw_rental_type == 'period_time' ? 'no_time_picker' : '';
$ovabrw_unfixed_time 		= get_post_meta( $pid, 'ovabrw_unfixed_time', true );

if ( $ovabrw_rental_type == 'period_time' ) {
	if ( $ovabrw_unfixed_time == 'yes' ) {
		$class_date_picker_period = '';
	} 
}

// Get Date, Location from URl
$choose_hour_pickup 	= $class_date_picker_period == 'no_time_picker' || $class_no_time_picker == 'no_time_picker' ? 'no' : 'yes';
$choose_hour_dropoff 	= $class_no_time_picker == 'no_time_picker' ? 'no' : 'yes';
$pickup_date 			= ovabrw_get_current_date_from_search( $choose_hour_pickup, 'pickup_date', $pid );
$dropoff_date 			= ovabrw_get_current_date_from_search( $choose_hour_dropoff, 'dropoff_date', $pid );
$pickup_loc 			= isset( $_GET['pickup_loc'] ) ? $_GET['pickup_loc'] : '';
$pickoff_loc 			= isset( $_GET['pickoff_loc'] ) ? $_GET['pickoff_loc'] : '';
$ovabrw_st_pickup_loc   = get_post_meta( $pid, 'ovabrw_st_pickup_loc', true );
$ovabrw_st_dropoff_loc  = get_post_meta( $pid, 'ovabrw_st_dropoff_loc', true );
$pickup_time 			= strtotime( $pickup_date ) ? date( $timeformat, strtotime( $pickup_date ) ) : '';
$terms_conditions 		= get_option( 'ova_brw_request_booking_terms_conditions', '' );
$terms_content 			= get_option( 'ova_brw_request_booking_terms_conditions_content', '' );

$column_class = 'three_column';

if ( $ovabrw_rental_type ) $column_class = 'two_column';

?>
<div class="request_booking">
	<h3>
		<?php esc_html_e( 'Send your requirement to us. We will check email and contact you soon.', 'ova-brw' ); ?>
	</h3>
	<form class="form ovabrw-form" id="request_booking" action="<?php echo home_url('/'); ?>" method="post" enctype="multipart/form-data" data-mesg_required="<?php esc_html_e( 'This field is required.', 'ova-brw' ); ?>">
	<div class="ovabrw-container">
		<div class="ovabrw-row">
			<div class="wrap-item <?php echo esc_attr( $column_class ); ?>">
				<div class="rental_item">
					<label><?php esc_html_e( 'Name', 'ova-brw' ); ?></label>
					<div class="error_item">
						<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
					</div>
					<input 
						type="text" 
						class="required" 
						name="name" 
						placeholder="<?php esc_html_e( 'Your Name', 'ova-brw' ); ?>" />	
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
						placeholder="<?php esc_html_e( 'your_email@gmail.com', 'ova-brw' ); ?>" />	
				</div>
				<?php 
					if ( ovabrw_get_setting( get_option('ova_brw_request_booking_form_show_number', 'yes') ) === 'yes' ):
				?>
				<div class="rental_item">
					<label><?php esc_html_e( 'Number', 'ova-brw' ); ?></label>
					<div class="error_item">
						<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
					</div>
					<input 
						type="text" 
						class="required" 
						name="number" 
						placeholder="<?php esc_html_e( '+66-4545688', 'ova-brw' ); ?>" />
				</div>
				<?php endif; ?>
				<?php 
					if ( ovabrw_get_setting( get_option('ova_brw_request_booking_form_show_address', 'yes') ) === 'yes' ):
				?>
					<div class="rental_item">
						<label><?php esc_html_e( 'Address', 'ova-brw' ); ?></label>
						<div class="error_item">
						<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
					</div>
						<input 
							type="text" 
							class="required" 
							name="address" 
							placeholder="<?php esc_html_e( 'Address', 'ova-brw' ); ?>" />	
					</div>
				<?php endif; ?>
				<?php if ( $ovabrw_rental_type === 'taxi' ):
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
							data-pid="<?php echo esc_attr( $pid ); ?>"
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
				<?php if ( $ovabrw_rental_type != 'taxi' ): ?>
					<?php if ( $show_pickup_location ): ?>
						<div class="rental_item">
							<label><?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?></label>
							<div class="error_item">
								<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
							</div>
							<?php
								if ( $ovabrw_rental_type !== 'transportation' ) {
									if ( ! empty( $ovabrw_st_pickup_loc ) && !empty( $ovabrw_st_dropoff_loc ) ) {
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
							<label><?php esc_html_e( 'Pick-up Date', 'ova-brw' ); ?></label>
							<div class="error_item">
								<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
							</div>
							<input 
								type="text" 
								onkeydown="return false" 
								name="pickup_date" 
								default_hour="<?php echo $default_hour_start; ?>" 
								time_to_book="<?php echo $time_to_book_start; ?>" 
								class="required ovabrw_start_date ovabrw_datetimepicker <?php echo esc_attr( $class_date_picker_period ); ?> <?php echo esc_attr( $class_no_time_picker ); ?>" 
								placeholder="<?php echo esc_attr( $placeholder_date ); ?> " 
								autocomplete="off" 
								value="<?php echo $pickup_date; ?>" 
								onfocus="blur();" 
								order_time='<?php echo $order_time; ?>'
								timepicker='<?php echo $timepicker_start; ?>' />	
						</div>
					<?php endif; ?>
					<?php if ( $ovabrw_rental_type == 'period_time' ):
						$ovabrw_petime_id 		= get_post_meta( $pid, 'ovabrw_petime_id', true );
						$ovabrw_petime_label 	= get_post_meta( $pid, 'ovabrw_petime_label', true );
					?>
						<div class="rental_item">
							<label><?php esc_html_e( 'Choose Package', 'ova-brw' ); ?></label>
							<div class="error_item">
								<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
							</div>
							<div class="period_package">
								<select name="ovabrw_period_package_id" class="required">
									<option value=""><?php esc_html_e( 'Select Package', 'ova-brw' ); ?></option>
									<?php if ( $ovabrw_petime_id ){ 
										foreach( $ovabrw_petime_id as $key => $value ) { ?>
										<option value="<?php echo esc_attr( $ovabrw_petime_label[$key] ); ?>">
											<?php echo esc_html( $ovabrw_petime_label[$key] ); ?>
										</option>
									<?php } } ?>
								</select>
							</div>
						</div>
					<?php elseif ( $ovabrw_rental_type != 'transportation' ):
						if ( $show_pickoff_date ): ?>
						<div class="rental_item">
							<label><?php esc_html_e( 'Drop-off Date', 'ova-brw' ); ?></label>
							<div class="error_item">
								<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
							</div>
							<input 
								type="text" 
								name="pickoff_date" 
								onkeydown="return false" 
								default_hour="<?php echo $default_hour_end; ?>"  
								time_to_book="<?php echo $time_to_book_end; ?>"  
								class="required ovabrw_end_date ovabrw_datetimepicker <?php echo esc_attr( $class_no_time_picker ); ?>" 
								placeholder="<?php echo esc_attr( $placeholder_date ); ?>" 
								autocomplete="off" 
								value="<?php echo $dropoff_date; ?>" 
								onfocus="blur();" 
								order_time='<?php echo $order_time; ?>'
								timepicker='<?php echo $timepicker_end; ?>'
							>	
						</div>
					<?php endif; endif; ?>
				<?php endif; ?>
				<?php
					$show_number_vehicle = ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_number_vehicle', 'no' ) );
					if ( $show_number_vehicle === 'yes' ):
				?>
						<div class="rental_item">
							<label><?php esc_html_e( 'Quantity', 'ova-brw' ); ?></label>
							<div class="error_item">
								<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
							</div>
							<input type="number" class="required" name="ovabrw_number_vehicle" value="1" min="1" />
						</div>
				<?php endif; ?>
				<?php
					$list_ckf_output = [];
					$ovabrw_manage_custom_checkout_field 	= get_post_meta( $pid, 'ovabrw_manage_custom_checkout_field', true );
					$list_field_checkout 					= get_option( 'ovabrw_booking_form', array() );

					// Get custom checkout field by Category
					$product_cats 	= wp_get_post_terms( $pid, 'product_cat' );
					$cat_id 		= isset( $product_cats[0] ) ? $product_cats[0]->term_id : '';
					$ovabrw_custom_checkout_field 			= $cat_id ? get_term_meta($cat_id, 'ovabrw_custom_checkout_field', true) : '';
					$ovabrw_choose_custom_checkout_field 	= $cat_id ? get_term_meta($cat_id, 'ovabrw_choose_custom_checkout_field', true) : '';

					if ( $ovabrw_manage_custom_checkout_field === 'new' ) {
						$list_field_checkout_in_product 	= get_post_meta( $pid, 'ovabrw_product_custom_checkout_field', true );
						$list_field_checkout_in_product_arr = explode( ',', $list_field_checkout_in_product );
						$list_field_checkout_in_product_arr = array_map( 'trim', $list_field_checkout_in_product_arr );

						$list_ckf_output = [];
						if ( !empty( $list_field_checkout_in_product_arr ) && is_array( $list_field_checkout_in_product_arr ) ) {
							foreach( $list_field_checkout_in_product_arr as $field_name ) {
								if ( array_key_exists( $field_name, $list_field_checkout ) ) {
									$list_ckf_output[$field_name] = $list_field_checkout[$field_name];
								}
							}
						} 
					} elseif ( $ovabrw_choose_custom_checkout_field == 'all' ){
						$list_ckf_output = $list_field_checkout;
					} elseif ( $ovabrw_choose_custom_checkout_field == 'special' ){
						if ( $ovabrw_custom_checkout_field ) {
							foreach( $ovabrw_custom_checkout_field as $field_name ) {
								if ( array_key_exists( $field_name, $list_field_checkout ) ) {
									$list_ckf_output[$field_name] = $list_field_checkout[$field_name];
								}
							}
						} else {
							$list_ckf_output = [];
						}
					} else {
						$list_ckf_output = $list_field_checkout;
					}

					if ( is_array( $list_ckf_output ) && !empty( $list_ckf_output ) ) {
						$special_fields = [ 'textarea', 'select', 'radio', 'checkbox', 'file' ];

						foreach( $list_ckf_output as $key => $field ) {
							if ( array_key_exists('enabled', $field) &&  $field['enabled'] == 'on' ) {
								if ( array_key_exists('required', $field) &&  $field['required'] == 'on' ) {
									$class_required = 'required';
								} else {
									$class_required = '';
								}
						?>
							<div class="rental_item">
								<label><?php echo esc_html( $field['label'] ); ?></label>
								<div class="error_item">
									<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
								</div>
								<?php if ( ! in_array( $field['type'], $special_fields ) ) { ?>
									<input 
										type="<?php echo esc_attr( $field['type'] ); ?>" 
										name="<?php echo esc_attr( $key ); ?>" 
										class=" <?php echo esc_attr( $field['class'] ) . ' ' . $class_required ?>" 
										placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
										value="<?php echo $field['default']; ?>" />
								<?php } ?>
								<?php if ( $field['type'] === 'textarea' ) { ?>
									<textarea name="<?php echo esc_attr( $key ); ?>" class=" <?php echo esc_attr( $field['class'] ) . ' ' . $class_required ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"   value="<?php echo $field['default']; ?>" cols="10" rows="5"></textarea>
								<?php } ?>
								<?php if ( $field['type'] === 'select' ) { 
									$ova_options_key = $ova_options_text = [];

									if ( array_key_exists( 'ova_options_key', $field ) ) {
										$ova_options_key = $field['ova_options_key'];
									}

									if ( array_key_exists( 'ova_options_text', $field ) ) {
										$ova_options_text = $field['ova_options_text'];
									}

									?>
									<select name="<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $field['class'] ) . ' ' . $class_required; ?>" >
										<?php 
										if ( !empty( $ova_options_text ) && is_array( $ova_options_text ) ) { 
											if ( $field['required'] != 'on' ): ?>
												<option value="">
													<?php printf( esc_html__( 'Select %s', 'ova_brw' ), $field['label'] ); ?>
												</option>
											<?php
											endif;
											foreach( $ova_options_text as $key => $value ) {
										?>
												<option value="<?php echo esc_attr( $ova_options_key[$key] ); ?>"<?php selected( $field['default'], $ova_options_key[$key] ); ?>>
													<?php echo esc_html( $value ); ?>
												</option>
										<?php 
											} //end foreach
										}//end if
									?>
									</select>
								<?php } ?>
								<?php if ( $field['type'] === 'radio' ):
									$values 	= isset( $field['ova_values'] ) ? $field['ova_values'] : '';
									$default 	= isset( $field['default'] ) ? $field['default'] : '';

									if ( ! empty( $values ) && is_array( $values ) ):
										foreach ( $values as $k => $value ):
											$checked = '';

											if ( ! $default && $field['required'] === 'on' ) $default = $values[0];

											if ( $default === $value ) $checked = 'checked';
								?>			
										<div class="ovabrw-radio">
											<input 
												type="radio" 
												id="<?php echo 'ovabrw-req-radio'.esc_attr( $k ); ?>" 
												name="<?php echo esc_attr( $key ); ?>" 
												value="<?php echo esc_attr( $value ); ?>" <?php echo esc_html( $checked ); ?>/>
											<label for="<?php echo 'ovabrw-req-radio'.esc_attr( $k ); ?>"><?php echo esc_html( $value ); ?></label>
										</div>
								<?php endforeach; endif; endif; ?>
								<?php if ( $field['type'] === 'checkbox' ):
									$default 		= isset( $field['default'] ) ? $field['default'] : '';
									$checkbox_key 	= isset( $field['ova_checkbox_key'] ) ? $field['ova_checkbox_key'] : [];
									$checkbox_text 	= isset( $field['ova_checkbox_text'] ) ? $field['ova_checkbox_text'] : [];
									$checkbox_price = isset( $field['ova_checkbox_price'] ) ? $field['ova_checkbox_price'] : [];

									if ( ! empty( $checkbox_key ) && is_array( $checkbox_key ) ):
										foreach ( $checkbox_key as $k => $val ):
											$checked = '';

											if ( ! $default && $field['required'] === 'on' ) $default = $val;

											if ( $default === $val ) $checked = 'checked';
								?>
									<div class="ovabrw-checkbox">
										<input 
											type="checkbox" 
											id="<?php echo 'ovabrw-req-checkbox-'.esc_attr( $val ); ?>" 
											class="<?php echo esc_attr( $class_required ); ?>" 
											name="<?php echo esc_attr( $key ).'['.$val.']'; ?>" 
											value="<?php echo esc_attr( $val ); ?>" <?php echo esc_html( $checked ); ?>/>
										<label for="<?php echo 'ovabrw-req-checkbox-'.esc_attr( $val ); ?>">
											<?php echo isset( $checkbox_text[$k] ) ? esc_html( $checkbox_text[$k] ) : ''; ?>
										</label>
									</div>
								<?php endforeach; endif; endif; ?>
								<?php if ( $field['type'] === 'file' ):
									$mimes = apply_filters( 'ovabrw_ft_file_mimes', [
					                    'jpg'   => 'image/jpeg',
					                    'jpeg'  => 'image/pjpeg',
					                    'png'   => 'image/png',
					                    'pdf'   => 'application/pdf',
					                    'doc'   => 'application/msword',
					                ]);
								?>
									<div class="ovabrw-file">
										<label for="<?php echo 'ovabrw-req-file-'.esc_attr( $key ); ?>">
											<span class="ovabrw-file-chosen"><?php esc_html_e( 'Choose File', 'ova-brw' ); ?></span>
											<span class="ovabrw-file-name"></span>
										</label>
										<input 
											type="<?php echo esc_attr( $field['type'] ); ?>" 
											id="<?php echo 'ovabrw-req-file-'.esc_attr( $key ); ?>" 
											name="<?php echo esc_attr( $key ); ?>" 
											class="<?php echo esc_attr( $field['class'] ) . $class_required; ?>" 
											data-max-file-size="<?php echo esc_attr( $field['max_file_size'] ); ?>" 
											data-file-mimes="<?php echo esc_attr( json_encode( $mimes ) ); ?>" 
											data-max-file-size-msg="<?php printf( esc_html__( 'Max file size: %sMB', 'ova-brw' ), $field['max_file_size'] ); ?>" 
											data-formats="<?php esc_attr_e( 'Supported formats: .jpg, .jpeg, .png, .pdf, .doc', 'ova-brw' ); ?>"/>
									</div>
								<?php endif; ?>
							</div>
						<?php
							}//endif
						}//end foreach
					}//end if
				?>
			</div>
		</div>
	</div>
	<?php if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_extra_service', 'yes' ) ) === 'yes' ) { ?>
		<?php $ovabrw_resource_name = get_post_meta( $pid, 'ovabrw_resource_name', true ); 
			if ( $ovabrw_resource_name ) {
				$ovabrw_resource_price 			= get_post_meta( $pid, 'ovabrw_resource_price', true ); 
				$ovabrw_resource_duration_val 	= get_post_meta( $pid, 'ovabrw_resource_duration_val', true ); 
				$ovabrw_resource_duration_type 	= get_post_meta( $pid, 'ovabrw_resource_duration_type', true ); 
				$ovabrw_resource_id 			= get_post_meta( $pid, 'ovabrw_resource_id', true ); 
		?>
		<div class="ovabrw_extra_service">
			<label><?php esc_html_e( 'Extra Service', 'ova-brw' ); ?></label>
			<div class="row-fluid ovabrw_resource">
				<div class="row">
				<?php foreach( $ovabrw_resource_name as $key => $value ) { ?>
					<div class="item">
						<div class="left">
							<?php $ovabrw_resource_key = $ovabrw_resource_id[$key]; ?>
							<input 
								type="checkbox" 
								id="ovabrw_resource_checkboxs_<?php echo esc_html($key); ?>" 
								name="ovabrw_resource_checkboxs[]" 
								value="<?php echo esc_attr( $ovabrw_resource_key ); ?>" />
							<label for="ovabrw_resource_checkboxs_<?php echo esc_html($key) ?>">
								<?php echo esc_html( $ovabrw_resource_name[$key] ); ?>
							</label>
						</div>
						<div class="right">
							<div class="resource">
								<span class="dur_price"><?php echo ovabrw_wc_price( $ovabrw_resource_price[$key] ); ?></span>
								<span class="slash">/</span>
								<?php if ( isset( $ovabrw_resource_duration_val[$key] ) ) : ?>
								<span class="dur_val">
									<?php echo esc_html( $ovabrw_resource_duration_val[$key] ); ?>
								</span>
								<?php endif; ?>
								<span class="dur_type">
									<?php
										if ( $ovabrw_resource_duration_type[$key] == 'hours' ) {
											esc_html_e( 'Hour(s)', 'ova-brw' );
										} elseif ( $ovabrw_resource_duration_type[$key] == 'days' ) {
											esc_html_e( 'Day(s)', 'ova-brw' );
										} elseif ( $ovabrw_resource_duration_type[$key] == 'total' ){
											esc_html_e( 'Total', 'ova-brw' );
										}
									?>
								</span>
							</div>
						</div>
					</div>
				<?php } ?>
				</div>
			</div>
		</div>
	<?php } } ?>
	<?php
		if ( ovabrw_get_setting( get_option( 'ova_brw_request_booking_form_show_service', 'yes' ) ) === 'yes' ) { 
			$ovabrw_label_service 	= get_post_meta( $pid, 'ovabrw_label_service', true );
			$ovabrw_service_id 		= get_post_meta( $pid, 'ovabrw_service_id', true );
			$ovabrw_service_name 	= get_post_meta( $pid, 'ovabrw_service_name', true );
			$ovabrw_service_price 	= get_post_meta( $pid, 'ovabrw_service_price', true );
			$ovabrw_service_duration_type = get_post_meta( $pid, 'ovabrw_service_duration_type', true );
			?>
			<?php
				if ( $ovabrw_label_service ) {
			?>
			<div class="ovabrw_service_wrap">
				<label><?php esc_html_e( 'Service', 'ova-brw' ); ?></label>
				<div class="row ovabrw_service">
					<?php
					for( $i = 0; $i < count( $ovabrw_label_service ); $i++ ) {
						$label_group_service = $ovabrw_label_service[$i];
						?>
						<div class="ovabrw_service_select">
							<select name="ovabrw_service[]">
								<option value="">
									<?php printf( esc_html__( 'Select %s', 'ova-brw' ), $label_group_service ); ?>
								</option>
								<?php
								if ( isset( $ovabrw_service_id[$i] ) && is_array( $ovabrw_service_id[$i] ) && !empty( $ovabrw_service_id ) ) {
									foreach( $ovabrw_service_id[$i] as $key => $val ) {
										?>
											<option value="<?php echo esc_attr( $val ) ?>">
												<?php echo esc_html( $ovabrw_service_name[$i][$key] ); ?>
											</option>
										<?php
									}
								}
								?>
							</select>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php
			}
		}
	?>
	<?php if ( ovabrw_get_setting( get_option('ova_brw_request_booking_form_show_extra_info', 'yes') ) === 'yes' ): ?>
		<div class="extra">
			<textarea name="extra" cols="50" rows="5" placeholder="<?php esc_html_e( 'Extra Information', 'ova-brw' ); ?>"></textarea>
		</div>
	<?php endif; ?>
	<?php if ( $ovabrw_rental_type === 'taxi' ):
		$price_by = get_post_meta( $pid, 'ovabrw_map_price_by', true );

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
	<?php if ( $terms_conditions === 'yes' && $terms_content ): ?>
		<div class="terms-conditions">
			<label>
				<input
					type="checkbox"
					name="ovabrw-request_booking-terms-conditions"
					value="yes"
					data-error="<?php esc_attr_e( 'Please read and accept the terms and conditions to continue.', 'ova-brw' ); ?>"
				/>
				<span class="terms-conditions-content">
					<?php echo $terms_content; ?>
					<span class="terms-conditions-required">*</span>
				</span>
			</label>
		</div>
	<?php endif; ?>
	<div class="ovabrw-request-form-error"></div>
	<?php if ( get_option( 'ova_brw_recapcha_enable', 'no' ) === 'yes' && ovabrw_get_recaptcha_form( 'request' ) ): ?>
	    <div id="ovabrw-g-recaptcha-request"></div>
	    <input
	        type="hidden"
	        id="ovabrw-recaptcha-request-token"
	        value=""
	        data-mess="<?php esc_attr_e( 'Requires reCAPTCHA', 'tripgo' ); ?>"
	        data-error="<?php esc_attr_e( 'reCAPTCHA response error, please try again later', 'tripgo' ); ?>"
	        data-expired="<?php esc_attr_e( 'reCAPTCHA response expires, you needs to re-verify', 'tripgo' ); ?>"
	    />
	<?php endif; ?>
	<input type="hidden" name="product_name" value="<?php echo get_the_title(); ?>" />
	<input type="hidden" name="product_id" value="<?php echo $pid; ?>" />
	<input type="hidden" name="request_booking" value="request_booking" />
	<button type="submit" class="submit btn_tran">
		<?php esc_html_e( 'Send', 'ova-brw' ); ?>
		<div class="ajax_loading"></div>
	</button>
	</form>
</div>