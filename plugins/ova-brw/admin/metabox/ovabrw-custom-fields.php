<?php if ( ! defined( 'ABSPATH' ) ) exit();

// Check product if with wcfm plugin
global $wp;

if ( ! empty($wp->query_vars) ) {
	$post_id = $wp->query_vars['wcfm-products-manage'];
} else {
	$post_id = false;
}
	
if ( !$post_id ) {
	$post_id = get_the_ID();
}

global $woocommerce, $post;

if ( ! function_exists( 'woocommerce_wp_text_input' ) && ! is_admin() ) {
	include_once WC()->plugin_path() . '/includes/admin/wc-meta-box-functions.php';
}
?>

<div class="options_group show_if_ovabrw_car_rental ovabrw_metabox_car_rental">
	<!-- Price Type -->
	<?php woocommerce_wp_select(
	  	array(
			'id' 			=> 'ovabrw_price_type',
			'label' 		=> esc_html__( 'Rental Type', 'ova-brw' ),
			'placeholder' 	=> '',
			'desc_tip' 		=> 'true',
			'description' 	=> esc_html__( 'Some fields will show/hide when change this field', 'ova-brw' ),
			'options' 		=> array(
				'day'				=> esc_html__( '1: Day', 'ova-brw' ),
				'hour'				=> esc_html__( '2: Hour', 'ova-brw' ),
				'mixed'				=> esc_html__( '3: Mixed (Day and Hour)', 'ova-brw' ),
				'period_time' 		=> esc_html__( '4: Period of Time ( 05:00 am - 10:00 am, 1 day, 2 days, 1 month, 6 months, 1 year... )', 'ova-brw' ),
				'transportation' 	=> esc_html__( '5: Location', 'ova-brw' ),
				'taxi' 				=> esc_html__( '6: Taxi', 'ova-brw' ),
			),
			'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_price_type', true ) : 'day',
	   	));
	?>

	<!-- Price hour -->
	<?php woocommerce_wp_text_input(
	  	array(
			'id' 			=> 'ovabrw_regul_price_hour',
			'class' 		=> 'short wc_input_price',
			'label' 		=> esc_html__( 'Regular price / Hour', 'ova-brw' ),
			'placeholder' 	=> '',
			'desc_tip'    	=> 'true',
			'description' 	=> esc_html__( 'Regular price by hour', 'ova-brw' ),
			'type' 			=> 'text',
			'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_regul_price_hour', true ) : '',
	   	));
	?>

	<!-- Price Taxi -->
	<?php woocommerce_wp_text_input(
	  	array(
			'id' 			=> 'ovabrw_regul_price_taxi',
			'class' 		=> 'short wc_input_price',
			'label' 		=> esc_html__( 'Price', 'ova-brw' ),
			'placeholder' 	=> '',
			'desc_tip'    	=> 'true',
			'description' 	=> esc_html__( 'Price', 'ova-brw' ),
			'type' 			=> 'text',
			'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_regul_price_taxi', true ) : '',
	   	));
	?>

	<!-- Unfixed time -->
	<?php woocommerce_wp_select(
	  	array(
			'id' 			=> 'ovabrw_unfixed_time',
			'label' 		=> __( 'Unfixed time', 'ova-crs' ),
			'placeholder' 	=> '',
			'options' 		=> array(
				'no' 	=> esc_html__( 'No', 'ova-brw' ),
				'yes' 	=> esc_html__( 'Yes', 'ova-brw' ),
			),
			'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_unfixed_time', true ) : 'no',
	   	));
	?>

	<!-- Define 1 day -->
	<?php woocommerce_wp_select(
	  	array(
			'id' 			=> 'ovabrw_define_1_day',
			'label' 		=> esc_html__( 'Charged by', 'ova-brw' ),
			'placeholder' 	=> '',
			'desc_tip'    	=> 'true',
			'options' 		=> array(
				'day'	=> esc_html__( 'Day', 'ova-brw' ),
				'hotel'	=> esc_html__( 'Night', 'ova-brw' ),
				'hour'	=> esc_html__( 'Hour', 'ova-brw' ),
			),
			'description' 	=> esc_html__( 'Calculate rental period:<br/> <strong>- Day</strong>: (Drop-off date) - (Pick-up date) + 1 <br/> <strong>- Night</strong>: (Drop-off date) - (Pick-up date) <br/> <strong>- Hour</strong>: (Drop-off date) - (Pick-up date) + X <br/> X = 1:  if (Drop-off Time) - (Pick-up Time) > 0 <br/>X = 0:  if (Drop-off Time) - (Pick-up Time) < 0', 'ova-brw' ),
			'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_define_1_day', true ) : 'day',
	   	));
	?>

	<!-- Regular Price / Day -->
	<?php
		$product = wc_get_product( $post_id );
		// Edit product in backend and WCFM plugin
		if ( isset( $wp->query_vars['wcfm-products-manage'] ) && $product && $product->get_type() == 'ovabrw_car_rental' ) {
			woocommerce_wp_text_input(
				array(
				   'id' 			=> 'regular_price',
				   'class' 			=> 'short',
				   'label' 			=> esc_html__( 'Regular Price / Day', 'ova-brw' ),
				   'placeholder' 	=> '0.00',
				   'type' 			=> 'number',
				   'value' 			=> $post_id ? get_post_meta( $post_id, '_regular_price', true ) : '',
		   	));
		}
	?>

	<!-- Total Vehicle -->
	<?php woocommerce_wp_text_input(
	  	array(
			'id' 			=> 'ovabrw_amount_insurance',
			'class' 		=> 'short',
			'label' 		=> esc_html__( 'Amount of insurance', 'ova-brw' ),
			'desc_tip' 		=> 'true',
			'description' 	=> esc_html__( 'This amount will be added to the cart. Decimal use dot (.)', 'ova-brw' ),
			'placeholder' 	=> '10.5',
			'type' 			=> 'text',
			'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_amount_insurance', true ) : '',
	   	));
	?>

	<?php if ( apply_filters( 'ovabrw_show_backend_deposit', true ) ): ?>

		<!-- Enable deposit -->
		<?php woocommerce_wp_select(
		  	array(
				'id' 			=> 'ovabrw_enable_deposit',
				'label' 		=> esc_html__( 'Enable deposit', 'ova-brw' ),
				'placeholder' 	=> '',
				'options' 		=> array(
					'no' 	=> esc_html__( 'No', 'ova-brw' ),
					'yes' 	=> esc_html__( 'Yes', 'ova-brw' ),
				),
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_enable_deposit', true ) : 'no',
		   	));
		?>

		<!-- Force deposit -->
		<?php woocommerce_wp_select(
		  	array(
				'id' 			=> 'ovabrw_force_deposit',
				'label' 		=> esc_html__( 'Show Full Payment', 'ova-brw' ),
				'desc_tip' 		=> 'true',
				'description' 	=> esc_html__( 'Yes: Allow pay Full Payment, No: Only Deposit', 'ova-brw' ),
				'placeholder' 	=> '',
				'options' 		=> array(
					'no' 	=> esc_html__( 'No', 'ova-brw' ),
					'yes' 	=> esc_html__( 'Yes', 'ova-brw' ),
				),
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_force_deposit', true ) : 'no',
		   	));
		?>

		<!-- Type deposit -->
		<?php woocommerce_wp_select(
		  	array(
				'id' 			=> 'ovabrw_type_deposit',
				'label' 		=> esc_html__( 'Deposit type', 'ova-brw' ),
				'placeholder' 	=> '',
				'options' 		=> array(
					'percent'	=> esc_html__( 'Percentage of price', 'ova-brw' ),
					'value'		=> esc_html__( 'Fixed value', 'ova-brw' ),
				),
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_type_deposit', true ) : 'percent',
		   	));
		?>
		
		<!-- amount deposit -->
		<?php woocommerce_wp_text_input(
			array(
				'id' 			=> 'ovabrw_amount_deposit',
				'class' 		=> 'short',
				'label' 		=> esc_html__( 'Deposit amount', 'ova-brw' ),
				'placeholder' 	=> '50',
				'desc_tip' 		=> 'true',
				'description' 	=> esc_html__( 'decimal use dot (.)', 'ova-brw' ),
				'type' 			=> 'text',
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_amount_deposit', true ) : '',
		   	));
		?>
	<?php endif; ?>	

	<!-- Manage Vehicles -->
	<?php if( apply_filters( 'ovabrw_manage_store_show_manual_setting_product', true ) ) {
			woocommerce_wp_select(
		  		array(
					'id' 			=> 'ovabrw_manage_store',
					'label' 		=> esc_html__( 'Manage Vehicles', 'ova-brw' ),
					'placeholder' 	=> '',
					'options' 		=> array(
						'store'		=> esc_html__( 'Automatic', 'ova-brw' ),
						'id_vehicle' 	=> esc_html__( 'Manual', 'ova-brw' ),
					),
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_store', true ) : 'store',
		   	));
		} else {
			woocommerce_wp_select(
			  	array(
					'id' 			=> 'ovabrw_manage_store',
					'label' 		=> esc_html__( 'Manage Vehicles', 'ova-brw' ),
					'placeholder' 	=> '',
					'options' 		=> array(
						'store'	=> esc_html__( 'Automatic', 'ova-brw' )
					),
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_store', true ) : 'store',
		   	));
		} 
	?>

	<!-- Total Vehicle -->
	<?php $value = !empty(get_post_meta( $post_id, 'ovabrw_car_count', true )) ? get_post_meta( $post_id, 'ovabrw_car_count', true ) : 1;
		woocommerce_wp_text_input(
		  	array(
				'id' 			=> 'ovabrw_car_count',
				'class' 		=> 'short',
				'label' 		=> esc_html__( 'Stock Quantity', 'ova-brw' ),
				'placeholder' 	=> '10',
				'value' 		=> $value,
				'type' 			=> 'number'
	   	));
	?>

	<!-- ID Vehicles -->
	<!-- Feature -->
	<div class="ovabrw-form-field ovabrw_id_vehicle_wrap">
  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_id_vehicle.php' ); ?>
	</div>
	
	<?php woocommerce_wp_text_input(
	  		array(
				'id' 			=> 'ovabrw_prepare_vehicle',
				'class' 		=> 'short',
				'label' 		=> esc_html__( 'Time between 2 leases (Minutes)', 'ova-brw' ),
				'desc_tip' 		=> 'true',
				'description' 	=> esc_html__( ' For example: if the car is delivered at 9:00, I set 60 minutes to prepare the car so the car is available again to book at 10:00', 'ova-brw' ),
				'placeholder' 	=> '60',
				'type' 			=> 'number',
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_prepare_vehicle', true ) : '',
	   	));
	?>

	<?php woocommerce_wp_text_input(
		  	array(
				'id' 			=> 'ovabrw_prepare_vehicle_day',
				'class' 		=> 'short',
				'label' 		=> esc_html__( 'Time between 2 leases (Day)', 'ova-brw' ),
				'desc_tip' 		=> 'true',
				'description' 	=> esc_html__( ' For example: if the car is delivered on 01/01/2021, I set 1 day to prepare the car so the car is available again to book on 03/01/2021', 'ova-brw' ),
				'placeholder' 	=> '0',
				'type' 			=> 'number',
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_prepare_vehicle_day', true ) : '',
		));
	?>
	
	<?php if ( apply_filters( 'ovabrw_show_backend_custom_order', true ) ) {
	 	$value = ! empty( get_post_meta( $post_id, 'ovabrw_car_order', true ) ) ? get_post_meta( $post_id, 'ovabrw_car_order', true ) : 1;
	 	woocommerce_wp_text_input(
	  		array(
				'id' 			=> 'ovabrw_car_order',
				'class' 		=> 'short ',
				'label' 		=> esc_html__( 'Order at frontend', 'ova-brw' ),
				'placeholder' 	=> '1',
				'desc_tip' 		=> 'true',
				'description' 	=> esc_html__( 'Use in some elements', 'ova-brw' ),
				'type' 			=> 'number',
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_car_order', true ) : '',
	   	));
	}
	?>

	<!-- Taxi config -->
	<div class="ovabrw-form-field ovabrw_taxi_map">
		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Location Setup', 'ova-brw'); ?></strong>
		<div class="ovabrw-taxi-setup">
			<!-- Types -->
			<?php
				$ovabrw_map_price_by = get_post_meta( $post_id, 'ovabrw_map_price_by', true ) ? get_post_meta( $post_id, 'ovabrw_map_price_by', true ) : 'km';
			?>
			<p class="form-field ovabrw_map_price_by_field">
				<label for="ovabrw_map_price_by">
					<?php esc_html_e( 'Price by per', 'ova-brw' ); ?>
				</label>
				<span class="map-price-by">
					<span class="map-price-by-input">
						<input
							type="radio"
							id="map-price-km"
							name="ovabrw_map_price_by"
							value="km"
							<?php checked( $ovabrw_map_price_by, 'km' ); ?>
						/>
						<label class="label" for="map-price-km">
							<?php esc_html_e( 'km', 'ova-brw' ); ?>
						</label>
					</span>
					<span class="map-price-by-input">
						<input
							type="radio"
							id="map-price-mi"
							name="ovabrw_map_price_by"
							value="mi"
							<?php checked( $ovabrw_map_price_by, 'mi' ); ?>
						/>
						<label class="label" for="map-price-mi">
							<?php esc_html_e( 'mi', 'ova-brw' ); ?>
						</label>
					</span>
				</span>
			</p>
			<?php
				$waypoint = get_post_meta( $post_id, 'ovabrw_waypoint', true ) ? get_post_meta( $post_id, 'ovabrw_waypoint', true ) : 'on';
				$max_waypoint = get_post_meta( $post_id, 'ovabrw_max_waypoint', true ) ? get_post_meta( $post_id, 'ovabrw_max_waypoint', true ) : '';
			?>
			<p class="form-field ovabrw_waypoint_field ">
				<label for="ovabrw_waypoint">
					<?php esc_html_e( 'Waypoints', 'ova-brw' ); ?>
				</label>
				<input 
					type="checkbox"
					name="ovabrw_waypoint"
					id="ovabrw_waypoint"
					<?php echo $waypoint === 'on' ? 'checked="checked"' : ''; ?>
				>
				<span class="max_waypoint">
					<span class="label"><?php esc_html_e( 'Max Waypoint' ); ?></span>
					<input
						type="number"
						name="ovabrw_max_waypoint"
						value="<?php echo esc_attr( $max_waypoint ); ?>"
						placeholder="0"
						autocomplete="off"
					/>
				</span>
			</p>
			<?php
				$ovabrw_zoom_map = get_post_meta( $post_id, 'ovabrw_zoom_map', true ) ? get_post_meta( $post_id, 'ovabrw_zoom_map', true ) : 4;
			 	woocommerce_wp_text_input(
			  		array(
						'id' 			=> 'ovabrw_zoom_map',
						'class' 		=> 'short ',
						'label' 		=> esc_html__( 'Zoom Map', 'ova-brw' ),
						'placeholder' 	=> '4',
						'type' 			=> 'number',
						'value' 		=> $ovabrw_zoom_map,
			   	));
			?>

			<!-- Types -->
			<?php
				$ovabrw_map_types = get_post_meta( $post_id, 'ovabrw_map_types', true ) ? get_post_meta( $post_id, 'ovabrw_map_types', true ) : '';

				if ( ! $ovabrw_map_types ) $ovabrw_map_types = [ 'geocode' ];
			?>
			<p class="form-field ovabrw_map_types_field ">
				<label for="ovabrw_map_types">
					<?php esc_html_e( 'Types', 'ova-brw' ); ?>
				</label>
				<span class="map-types">
					<span class="map-type-input">
						<input
							type="radio"
							id="map-geocode"
							name="ovabrw_map_types[]"
							value="geocode"
							<?php echo in_array( 'geocode', $ovabrw_map_types ) ? 'checked' : ''; ?>
						/>
						<label class="label" for="map-geocode">
							<?php esc_html_e( 'Geocode', 'ova-brw' ); ?>
						</label>
					</span>
					<span class="map-type-input">
						<input
							type="radio"
							id="map-address"
							name="ovabrw_map_types[]"
							value="address"
							<?php echo in_array( 'address', $ovabrw_map_types ) ? 'checked' : ''; ?>
						/>
						<label class="label" for="map-address">
							<?php esc_html_e( 'Address', 'ova-brw' ); ?>
						</label>
					</span>
					<span class="map-type-input">
						<input
							type="radio"
							id="map-establishment"
							name="ovabrw_map_types[]"
							value="establishment"
							<?php echo in_array( 'establishment', $ovabrw_map_types ) ? 'checked' : ''; ?>
						/>
						<label class="label" for="map-establishment">
							<?php esc_html_e( 'Establishment', 'ova-brw' ); ?>
						</label>
					</span>
				</span>
			</p>
			<?php
				$ovabrw_bounds 	= get_post_meta( $post_id, 'ovabrw_bounds', true ) ? get_post_meta( $post_id, 'ovabrw_bounds', true ) : '';
				$bounds_lat 	= get_post_meta( $post_id, 'ovabrw_bounds_lat', true ) ? get_post_meta( $post_id, 'ovabrw_bounds_lat', true ) : '';
				$bounds_lng 	= get_post_meta( $post_id, 'ovabrw_bounds_lng', true ) ? get_post_meta( $post_id, 'ovabrw_bounds_lng', true ) : '';
				$bounds_radius 	= get_post_meta( $post_id, 'ovabrw_bounds_radius', true ) ? get_post_meta( $post_id, 'ovabrw_bounds_radius', true ) : '';
			?>
			<p class="form-field ovabrw_bounds_field ">
				<label for="ovabrw_bounds">
					<?php esc_html_e( 'Bounds', 'ova-brw' ); ?>
				</label>
				<input 
					type="checkbox"
					name="ovabrw_bounds"
					id="ovabrw_bounds"
					<?php echo $ovabrw_bounds === 'on' ? 'checked="checked"' : ''; ?>
				>
				<span class="coordinates">
					<span class="bounds-lat">
						<span class="label"><?php esc_html_e( 'Latitude', 'ova-brw' ); ?></span>
						<input
							type="text"
							name="ovabrw_bounds_lat"
							value="<?php echo esc_attr( $bounds_lat ); ?>"
						/>
					</span>
					<span class="bounds-lng">
						<span class="label"><?php esc_html_e( 'Longitude', 'ova-brw' ); ?></span>
						<input
							type="text"
							name="ovabrw_bounds_lng"
							value="<?php echo esc_attr( $bounds_lng ); ?>"
						/>
					</span>
					<span class="bounds-radius">
						<span class="label"><?php esc_html_e( 'Radius(meters)', 'ova-brw' ); ?></span>
						<input
							type="text"
							name="ovabrw_bounds_radius"
							value="<?php echo esc_attr( $bounds_radius ); ?>"
						/>
					</span>
				</span>
			</p>

			<!-- Component Restrictions -->
			<?php
				$countries 		= ovabrw_iso_alpha2();
				$restrictions 	= get_post_meta( $post_id, 'ovabrw_restrictions', true ) ? get_post_meta( $post_id, 'ovabrw_restrictions', true ) : [];
			?>
			<p class="form-field ovabrw_restrictions_field ">
				<label for="ovabrw_restrictions">
					<?php esc_html_e( 'Restrictions', 'ova-brw' ); ?>
				</label>
				<select name="ovabrw_restrictions[]" id="ovabrw_restrictions" data-placeholder="<?php esc_html_e( 'Select country', 'ova-brw' ); ?>" multiple>
					<?php foreach ( $countries as $country_code => $country_name ):
						$selected = '';

						if ( ! empty( $restrictions ) && in_array( $country_code, $restrictions ) ) {
							$selected = ' selected';
						}
					?>
						<option value="<?php echo esc_attr( $country_code ); ?>"<?php echo esc_attr( $selected ); ?>>
							<?php echo esc_html( $country_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>

			<!-- Extra Time -->
			<div class="ovabrw-form-field ovabrw_extra_time">
				<br/><strong class="ovabrw_heading_section">
					<?php esc_html_e( 'Extra Time', 'ova-brw' ); ?>
				</strong>
				<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_extra_time.php' ); ?>
			</div>
			<!-- End Extra Time -->
			<!-- Discount by Distance -->
			<div class="ovabrw-form-field ovabrw_discount_distance">
				<br/><strong class="ovabrw_heading_section">
					<?php esc_html_e( 'Discount by Distance', 'ova-brw' ); ?>
				</strong>
				<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_discount_distance.php' ); ?>
			</div>
			<!-- End Discount by Distance -->
			<!-- Special Time by Distance -->
			<div class="ovabrw-form-field ovabrw_st_distance">
				<br/><strong class="ovabrw_heading_section">
					<?php esc_html_e( 'Special Time', 'ova-brw' ); ?>
				</strong>
				<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_st_distance.php' ); ?>
			</div>
			<!-- End Special Time by Distance -->
		</div>
	</div>

	<!-- Daily Price -->
	<div class="ovabrw-form-field ovabrw_price_daily">
		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Daily Price', 'ova-brw'); ?></strong>
		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_dailys.php' ); ?>
	</div>

	<!-- Location Price -->
	<div class="ovabrw-form-field ovabrw_local_price_wrap">
		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Location Price', 'ova-brw'); ?></strong>
		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_local_price.php' ); ?>
	</div>

	<!-- Choose Location -->
	<div class="ovabrw-form-field ovabrw_choose_location">
		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Setup Locations', 'ova-brw'); ?></strong>
		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_choose_location.php' ); ?>
	</div>

	<!-- Price by Period Time -->
	<div class="ovabrw-form-field price_period_time">
		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Setup Price for Rental Type: Period of time', 'ova-brw'); ?></strong>
		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_period_time.php' ); ?>
	</div>
	
	<!-- Feature -->
	<div class="ovabrw-form-field ovabrw_features_wrap">
  		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Features', 'ova-brw'); ?></strong>
  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_features.php' ); ?>
	</div>

	<!-- Global Discount -->
	<div class="ovabrw-form-field price_not_period_time">
  		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Global Discount (GD)', 'ova-brw'); ?></strong>
  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_global_discount.php' ); ?>
	</div>

	<!-- Price by range time -->
	<div class="ovabrw-form-field price_not_period_time">
		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Special Time (ST)', 'ova-brw'); ?></strong>
		<span class="ovabrw_right"><?php esc_html_e( 'Note: ST doesnt use GD, it will use DST', 'ova-brw' ); ?></span>
		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_rt.php' ); ?>
	</div>
	
	<!-- Resources -->
	<div class="ovabrw-form-field ovabrw_resources_wrap">
  		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Resources', 'ova-brw'); ?></strong>
  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_resources.php' ); ?>
	</div>

	<!-- Service -->
	<div class="ovabrw-form-field ovabrw_service_wrap">
  		<br/><strong class="ovabrw_heading_section"><?php esc_html_e('Services', 'ova-brw'); ?></strong>
  		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_service.php' ); ?>
	</div>

	<!-- Unavailable Date -->
	<div class="ovabrw-form-field ovabrw_untime_wrap">
		<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Unavailable Time (UT)', 'ova-brw' ); ?></strong>
		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_untime.php' ); ?>
	</div>
	<!-- End Unavailable Date -->
	<?php  
	$ovabrw_product_disable_week_day = get_post_meta( $post_id, 'ovabrw_product_disable_week_day', true ) != '' ? get_post_meta( $post_id, 'ovabrw_product_disable_week_day', true ) : '';
	woocommerce_wp_text_input(
	  	array(
			'id' 			=> 'ovabrw_product_disable_week_day',
			'class' 		=> 'short',
			'label' 		=> esc_html__( 'Disable Weekdays', 'ova-brw' ),
			'placeholder' 	=> esc_html__( '0,6', 'ova-brw' ),
			'value' 		=> $ovabrw_product_disable_week_day,
			'type' 			=> 'text',
			'desc_tip' 		=> 'true',
			'description' 	=> esc_html__( '0: Sunday, 1: Monday, 2: Tuesday, 3: Wednesday, 4: Thursday, 5: Friday, 6: Saturday . Example: 0,6', 'ova-brw' ),
	));
	?>

	<!-- Rent min -->
	<div class="ovabrw-form-field ovabrw_rent_time_min_wrap">
		<br/><strong class="ovabrw_heading_section ovabrw_min_rental_period">
			<?php esc_html_e( 'Min Rental Period', 'ova-brw' ); ?>
		</strong>
		<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_rent_time_min.php' ); ?>
	</div>	

	<!-- Contact form 7 shortcode -->
	<?php if( apply_filters( 'ovabrw_show_extra_tab_setting_product', true ) ): ?>
		<div class="ovabrw-form-field">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Extra Tab (shortcode)', 'ova-brw' ); ?></strong>
			<?php 
				woocommerce_wp_select(
			  		array(
						'id' 			=> 'ovabrw_manage_extra_tab',
						'label' 		=> esc_html__( 'Extra Tab (shortcode)', 'ova-brw' ),
						'placeholder' 	=> '',
						'options' 		=> array(
							'in_setting' 	=> esc_html__( 'Default', 'ova-brw' ),
							'new_form' 		=> esc_html__( 'New Form', 'ova-brw' ),
							'no' 			=> esc_html__( 'No', 'ova-brw' ),
						),
						'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_extra_tab', true ) : 'in_setting',
			   	));
			?>
			<?php
				woocommerce_wp_textarea_input(
				    array(
				        'id' 			=> 'ovabrw_extra_tab_shortcode',
				        'placeholder' 	=> esc_html__( '[contact-form-7 id="205" title="Contact form 1"]', 'ova-brw' ),
				        'label' 		=> esc_html__('Shortcode', 'ova-brw'),
				        'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_extra_tab_shortcode', true ) : '',
				    )
				);
			?>
		</div>
	<?php endif; ?>

	<div class="ovabrw-form-field none-day-hotel manage-time-book-start-field">
		<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Start Date For Booking', 'ova-brw' ); ?></strong>
		<?php 
		woocommerce_wp_select(
		  	array(
				'id' 			=> 'ovabrw_manage_time_book_start',
				'label' 		=> esc_html__( 'Group Time', 'ova-brw' ),
				'placeholder' 	=> '',
				'options' 		=> array(
					'in_setting' 	=> esc_html__( 'Default', 'ova-brw' ),
					'new_time'		=> esc_html__( 'New Time', 'ova-brw' ),
					'no'			=> esc_html__( 'No', 'ova-brw' ),
				),
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_time_book_start', true ) : 'in_setting',
		   	));
		?>
		<?php
			woocommerce_wp_textarea_input(
			    array(
			        'id' 			=> 'ovabrw_product_time_to_book_start',
			        'placeholder' 	=> esc_html__( '07:00, 07:30, 13:00, 18:00', 'ova-brw' ),
			        'label' 		=> '',
			        'description' 	=> esc_html__('Insert time format: 24hour. Like 07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00', 'ova-brw'),
			        'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_product_time_to_book_start', true ) : '',
			    )
			);
		?>

		<?php 
			woocommerce_wp_select(
		  		array(
					'id' 			=> 'ovabrw_manage_default_hour_start',
					'label' 		=> esc_html__( 'Defaul Time', 'ova-brw' ),
					'placeholder' 	=> '',
					'options' 		=> array(
						'in_setting' 	=> esc_html__( 'Default', 'ova-brw' ),
						'new_time'		=> esc_html__( 'New Time', 'ova-brw' ),
					),
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_default_hour_start', true ) : 'in_setting',
		   	));
		?>
		<?php
			woocommerce_wp_text_input(
			    array(
			        'id' 			=> 'ovabrw_product_default_hour_start',
			        'placeholder' 	=> esc_html__( '09:00', 'ova-brw' ),
			        'label' 		=> '',
			        'description' 	=> esc_html__('Insert time format 24hour. Example: 09:00', 'ova-brw'),
			        'value'			=> $post_id ? get_post_meta( $post_id, 'ovabrw_product_default_hour_start', true ) : '',
			    )
			);
		?>
	</div>
	<div class="ovabrw-form-field none-day-hotel manage-time-book-end-field">
		<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'End Date for booking', 'ova-brw' ); ?></strong>
		<?php 
		woocommerce_wp_select(
		  	array(
				'id' 			=> 'ovabrw_manage_time_book_end',
				'label' 		=> esc_html__( 'Group Time', 'ova-brw' ),
				'placeholder' 	=> '',
				'options' 		=> array(
					'in_setting' 	=> esc_html__( 'Default', 'ova-brw' ),
					'new_time'		=> esc_html__( 'New Time', 'ova-brw' ),
					'no'			=> esc_html__( 'No', 'ova-brw' ),
				),
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_time_book_end', true ) : 'in_setting',
		   	));
		?>
		<?php
			woocommerce_wp_textarea_input(
			    array(
			        'id' 			=> 'ovabrw_product_time_to_book_end',
			        'placeholder' 	=> esc_html__( '07:00, 07:30, 13:00, 18:00', 'ova-brw' ),
			        'label' 		=> esc_html__('', 'ova-brw'),
			        'description' 	=> esc_html__('Insert time format: 24hour. Like 07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00', 'ova-brw'),
			        'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_product_time_to_book_end', true ) : '',
			    )
			);
		?>
		<?php 
		woocommerce_wp_select(
		  	array(
				'id' 			=> 'ovabrw_manage_default_hour_end',
				'label' 		=> esc_html__( 'Default Time', 'ova-brw' ),
				'placeholder' 	=> '',
				'options' 		=> array(
					'in_setting' 	=> esc_html__( 'Default', 'ova-brw' ),
					'new_time' 		=> esc_html__( 'New Time', 'ova-brw' ),
				),
				'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_default_hour_end', true ) : 'in_setting',
		   	));
		?>
		<?php
			woocommerce_wp_text_input(
			    array(
			        'id' 			=> 'ovabrw_product_default_hour_end',
			        'placeholder' 	=> esc_html__( '09:00', 'ova-brw' ),
			        'label' 		=> esc_html__('', 'ova-brw'),
			        'description' 	=> esc_html__('Insert time format 24hour. Example: 09:00', 'ova-brw'),
			        'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_product_default_hour_end', true ) : '',
			    )
			);
		?>
	</div>

	<?php if ( apply_filters( 'ovabrw_show_checkout_field_setting_product', true ) ): ?>
		<div class="ovabrw-form-field">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Custom Checkout Field', 'ova-brw' ); ?></strong>
			<?php 
				woocommerce_wp_select(
			  		array(
						'id' 			=> 'ovabrw_manage_custom_checkout_field',
						'label' 		=> esc_html__( 'Custom Checkout Field', 'ova-brw' ),
						'placeholder' 	=> '',
						'options' 		=> array(
							'all'		=> esc_html__( 'All', 'ova-brw' ),
							'new'		=> esc_html__( 'New', 'ova-brw' ),
						),
						'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_manage_custom_checkout_field', true ) : 'all',
			   	));
			?>
			<br/>
			<?php
				woocommerce_wp_textarea_input(
				    array(
				        'id' 			=> 'ovabrw_product_custom_checkout_field',
				        'placeholder' 	=> esc_html__( '', 'ova-brw' ),
				        'label' 		=> esc_html__('Custom Checkout Field', 'ova-brw'),
				        'description' 	=> esc_html__('Insert name in general custom checkout field. Example: ova_email_field, ova_address_field', 'ova-brw'),
				        'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_product_custom_checkout_field', true ) : '',
				    )
				);
			?>
		</div>
	<?php endif; ?>
	<?php if ( apply_filters( 'ovabrw_show_pickup_loc_setting_product', true ) ): ?>
		<div class="ovabrw-form-field manager-show-pickup-location">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Show Pick-up Location', 'ova-brw' ); ?></strong>
			<?php 
				woocommerce_wp_select(
				  	array(
						'id' 			=> 'ovabrw_show_pickup_location_product',
						'label' 		=> esc_html__( 'Show Pick-up Location', 'ova-brw' ),
						'placeholder' 	=> '',
						'options' 		=> array(
							'in_setting' 	=> esc_html__( 'Default', 'ova-brw' ),
							'yes' 			=> esc_html__( 'Yes', 'ova-brw' ),
							'no' 			=> esc_html__( 'No', 'ova-brw' ),
						),
						'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_show_pickup_location_product', true ) : 'in_setting',
			   	)); 
				woocommerce_wp_select(
				  	array(
						'id' 			=> 'ovabrw_show_other_location_pickup_product',
						'label' 		=> esc_html__( 'Allow customers to enter the other location', 'ova-brw' ),
						'placeholder' 	=> '',
						'options' 		=> array(
							'yes' 	=> esc_html__( 'Yes', 'ova-brw' ),
							'no' 	=> esc_html__( 'No', 'ova-brw' ),
						),
						'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_show_other_location_pickup_product', true ) : 'yes',
			   	));
			?>
		</div>
	<?php endif; ?>	

	<?php if ( apply_filters( 'ovabrw_show_dropoff_loc_setting_product', true ) ): ?>
		<div class="ovabrw-form-field manager-show-dropoff-location">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Show Drop-off Location', 'ova-brw' ); ?></strong>
			<?php 
				woocommerce_wp_select(
			  		array(
						'id'          	=> 'ovabrw_show_pickoff_location_product',
						'label'       	=> esc_html__( 'Show Drop-off Location', 'ova-brw' ),
						'placeholder' 	=> '',
						'options'		=> array(
							'in_setting' 	=> esc_html__( 'Default', 'ova-brw' ),
							'yes' 			=> esc_html__( 'Yes', 'ova-brw' ),
							'no' 			=> esc_html__( 'No', 'ova-brw' ),
						),
						'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_show_pickoff_location_product', true ) : 'in_setting',
			  	));
				woocommerce_wp_select(
				  	array(
						'id' 			=> 'ovabrw_show_other_location_dropoff_product',
						'label' 		=> esc_html__( 'Allow customers to enter the other location', 'ova-brw' ),
						'placeholder'	=> '',
						'options' 		=> array(
							'yes'	=> esc_html__( 'Yes', 'ova-brw' ),
							'no'	=> esc_html__( 'No', 'ova-brw' ),
						),
						'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_show_other_location_dropoff_product', true ) : 'yes',
			   	));
			?>
		</div>
	<?php endif; ?>

	<?php if( apply_filters( 'ovabrw_show_pickup_date_setting_product', true ) ): ?>
		<div class="ovabrw-form-field manager-show-pickup-date">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Show Pick-up Date', 'ova-brw' ); ?></strong>
			<?php 
				woocommerce_wp_select(
			  		array(
						'id' 			=> 'ovabrw_show_pickup_date_product',
						'label' 		=> esc_html__( 'Show Pick-up Date', 'ova-brw' ),
						'placeholder' 	=> '',
						'options'		=> array(
							'in_setting' 	=> esc_html__( 'Default', 'ova-brw' ),
							'yes'		 	=> esc_html__( 'Yes', 'ova-brw' ),
							'no'		 	=> esc_html__( 'No', 'ova-brw' ),
						),
						'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_show_pickup_date_product', true ) : 'in_setting',
			   	));
			?>
			<?php 
				woocommerce_wp_select(
			  		array(
						'id'          	=> 'ovabrw_label_pickup_date_product',
						'label'       	=> esc_html__( 'Get Label By ', 'ova-brw' ),
						'placeholder' 	=> '',
						'options' 	 	=> array(
							'category' 	=> esc_html__( 'Category', 'ova-brw' ),
							'new' 		=> esc_html__( 'New', 'ova-brw' ),
						),
						'description' 	=> esc_html__('Show label Pick-up Date outside Frontend', 'ova-brw'),
						'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_label_pickup_date_product', true ) : 'category',
			   	));
				woocommerce_wp_text_input(
				    array(
				        'id' 			=> 'ovabrw_new_pickup_date_product',
				        'placeholder' 	=> esc_html__( 'Your Label', 'ova-brw' ),
				        'label' 		=> esc_html__('Add Label', 'ova-brw'),
				        'description' 	=> esc_html__('Insert label Pick-up Date. Example: Pick-up Date, Check-in', 'ova-brw'),
				        'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_new_pickup_date_product', true ) : '',
				));
			?>
		</div>
	<?php endif; ?>
	<?php if ( apply_filters( 'ovabrw_show_dropoff_date_setting_product', true ) ): ?>
		<div class="ovabrw-form-field manager-show-dropoff-date">
			<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Show Drop-off Date', 'ova-brw' ); ?></strong>
			<?php
				woocommerce_wp_checkbox( 
					array( 
						'id' 	=> 'ovabrw_dropoff_date_by_setting', 
						'label' => esc_html__('Use by Settings', 'ova-brw' ),
						'value' => $post_id ? get_post_meta( $post_id, 'ovabrw_dropoff_date_by_setting', true ) : 'no',
				));
				woocommerce_wp_select(
					array(
						'id' 			=> 'ovabrw_show_pickoff_date_product',
						'label' 		=> esc_html__( 'Show Drop-off Date', 'ova-brw' ),
						'placeholder' 	=> '',
						'options'		=> array(
							'in_setting' 	=> esc_html__( 'Default', 'ova-brw' ),
							'yes' 			=> esc_html__( 'Yes', 'ova-brw' ),
							'no'			=> esc_html__( 'No', 'ova-brw' ),
						),
						'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_show_pickoff_date_product', true ) : 'in_setting',
			   	));
				woocommerce_wp_select(
					array(
						'id'          	=> 'ovabrw_label_dropoff_date_product',
						'label'       	=> esc_html__( 'Get Label By ', 'ova-brw' ),
						'placeholder' 	=> '',
						'options' 	 	=> array(
							'category' 	=> esc_html__( 'Category', 'ova-brw' ),
							'new' 		=> esc_html__( 'New', 'ova-brw' ),
						),
						'description' 	=> esc_html__('Show label Drop-off Date outside Frontend', 'ova-brw'),
						'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_label_dropoff_date_product', true ) : 'category',
			   	));
				woocommerce_wp_text_input(
				    array(
				        'id' 			=> 'ovabrw_new_dropoff_date_product',
				        'placeholder' 	=> esc_html__( 'Your Label', 'ova-brw' ),
				        'label' 		=> esc_html__('Add Label', 'ova-brw'),
				        'description' 	=> esc_html__('Insert label Drop-off Date. Example: Drop-off Date, Check-out', 'ova-brw'),
				        'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_new_dropoff_date_product', true ) : '',
				    )
				);
			?>
		</div>
	<?php endif; ?>
	<?php if( apply_filters( 'ovabrw_show_quantity_setting_product', true ) ): ?>
		<div class="ovabrw-form-field ovabrw_show_number_vehicle_wrap">
			<br/>
			<strong class="ovabrw_heading_section">
				<?php esc_html_e( 'Show Quantity', 'ova-brw' ); ?>
			</strong>
			<?php woocommerce_wp_select(
			  	array(
					'id' 			=> 'ovabrw_show_number_vehicle',
					'label' 		=> esc_html__( 'Input quantity in booking form', 'ova-brw' ),
					'placeholder' 	=> '',
					'options' 		=> array(
						'in_setting' 	=> esc_html__( 'Default', 'ova-brw' ),
						'yes'			=> esc_html__( 'Yes', 'ova-brw' ),
						'no'			=> esc_html__( 'No', 'ova-brw' ),
					),
					'value' 		=> $post_id ? get_post_meta( $post_id, 'ovabrw_show_number_vehicle', true ) : 'in_setting',
			   	));
			?>
		</div>
	<?php endif; ?>

	<!-- Map -->
	<div class="ovabrw-form-field ovabrw_map_wrap">
		<br/><strong class="ovabrw_heading_section"><?php esc_html_e( 'Map', 'ova-brw' ); ?></strong>
		<?php
			$ovabrw_map_name = $post_id ? get_post_meta( $post->ID ,'ovaev_map_name', true ) : esc_html__('New York', 'ova-brw');
			$ovabrw_address  = $post_id ? get_post_meta( $post_id, 'ovabrw_address', true ) : esc_html__( 'New York, NY, USA', 'ova-brw' );

			if ( ! $ovabrw_address ) {
				$ovabrw_address = esc_html__( 'New York, NY, USA', 'ova-brw' );
			}
			// Address
			woocommerce_wp_text_input(
				array(
					'id' 				=> 'pac-input',
					'class' 			=> 'controls',
					'label'				=> esc_html__( '', 'ova-brw' ),
					'placeholder'		=> esc_html__( 'Enter a venue', 'ova-brw' ),
					'type' 				=> 'text',
					'value' 			=> $ovabrw_address,
					'custom_attributes' => array(
						'autocomplete' 	=> 'off',
						'autocorrect'	=> 'off',
						'autocapitalize'=> 'none'
					),
			));
		?>
		<div id="admin_show_map"></div>
		<div id="infowindow-content">
			<span id="place-name" class="title"><?php echo esc_attr( $ovabrw_map_name ); ?></span><br>
			<span id="place-address"><?php echo esc_attr( $ovabrw_address ); ?></span>
		</div>
		<div id="map_info">
			<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_product_map.php' ); ?>
		</div>
	</div>	
</div>