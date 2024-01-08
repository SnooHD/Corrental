<?php if ( ! defined( 'ABSPATH' ) ) exit();

// Display Manage Booking
function ovabrw_create_order() {
	// Get all Products has Product Data: Rental
	$all_products = ovabrw_get_all_products();

    $html_list_option_product = '<option value="">'.esc_html__("Select Product", "ova-brw" ).'</option>';
	    while ( $all_products->have_posts() ) : $all_products->the_post();
	        global $product;

	        $html_list_option_product .= '<option value="'.get_the_id().'">'.get_the_title().'</option>';
	    endwhile; wp_reset_postdata(); wp_reset_query();

    $date_format 		=  ovabrw_get_date_format();
    $set_time_format 	= ovabrw_get_time_format_php();

    // Defautl country
    $country_setting 	= get_option( 'woocommerce_default_country', 'US:CA' );

	if ( strstr( $country_setting, ':' ) ) {
		$country_setting = explode( ':', $country_setting );
		$country         = current( $country_setting );
		$state           = end( $country_setting );
	} else {
		$country = $country_setting;
		$state   = '*';
	}

	?>
	<div class="wrap">
	    <form id="booking-filter" method="POST" enctype="multipart/form-data" action="<?php echo admin_url('/edit.php?post_type=product&page=ovabrw-create-order'); ?>">
	    	<h2><?php esc_html_e( 'Create Order', 'ova-brw' ); ?></h2>
	    	<div class="ovabrw-wrap">
	    		<?php
    			// Multi currency
    			$currencies = [];

    			if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) ) {
    				$setting 	= WOOMULTI_CURRENCY_F_Data::get_ins();
    				$currencies = $setting->get_list_currencies();
    			}

    			if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
    				// WPML multi currency
		    		global $woocommerce_wpml;

		    		if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
		    			if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

				        $currencies 	= $woocommerce_wpml->get_setting( 'currency_options' );
				        $current_lang 	= apply_filters( 'wpml_current_language', NULL );

				        if ( $current_lang != 'all' && ! empty( $currencies ) && is_array( $currencies ) ) {
				        	foreach ( $currencies as $k => $data ) {
				        		if ( isset( $currencies[$k]['languages'][$current_lang] ) && $currencies[$k]['languages'][$current_lang] ) {
				        			continue;
				        		} else {
				        			unset( $currencies[$k] );
				        		}
				        	}
				        }
				    }
    			}

    			if ( ! empty( $currencies ) && is_array( $currencies ) ):
					$current_currency = isset( $_GET['currency'] ) && $_GET['currency'] ? $_GET['currency'] : '';

					if ( ! $current_currency ) $current_currency = array_key_first( $currencies ); 
				?>
					<div class="ovabrw-row ovabrw-order-currency">
		    			<label for="ovabrw-currency"><?php esc_html_e( 'Currency', 'ova-brw' ); ?></label>
		    			<select name="currency" id="ovabrw-currency">
		    		<?php
					foreach ( $currencies as $currency => $rate ): ?>
	    				<option value="<?php echo esc_attr( $currency ); ?>"<?php selected( $currency, $current_currency ); ?>>
	    					<?php esc_html_e( $currency ); ?>
	    				</option>
			    	<?php endforeach; ?>
			    		</select>
			    		<input
			    			type="hidden"
			    			name="ovabrw-admin-url"
			    			value="<?php echo esc_url( get_admin_url() ); ?>"
			    		/>
		    		</div>
			    <?php endif; ?>
	    		<div class="ovabrw-row ovabrw-order-status">
	    			<label for="stattus-order"><?php esc_html_e( 'Status', 'ova-brw' ); ?></label>
	    			<select name="status_order" id="stattus-order" required>
	    				<option value="completed" selected ><?php esc_html_e( 'Completed', 'ova-brw' ); ?></option>
	    				<option value="processing"><?php esc_html_e( 'Processing', 'ova-brw' ); ?></option>
	    				<option value="pending"><?php esc_html_e( 'Pending payment', 'ova-brw' ); ?></option>
	    				<option value="on-hold"><?php esc_html_e( 'On hold', 'ova-brw' ); ?></option>
	    				<option value="cancelled"><?php esc_html_e( 'Cancelled', 'ova-brw' ); ?></option>
	    				<option value="refunded"><?php esc_html_e( 'Refunded', 'ova-brw' ); ?></option>
	    				<option value="failed"><?php esc_html_e( 'Failed', 'ova-brw' ); ?></option>
	    			</select>
	    		</div>
	            <div class="ovabrw-row ova-column-3 ovabrw-billing">
	            	<div class="item">
	            		<input
	            			type="text"
	            			name="ovabrw_first_name"
	            			placeholder="<?php esc_html_e( 'First Name', 'ova-brw' ); ?>"
	            			required
	            		/>
	            	</div>
	            	<div class="item">
	            		<input
	            			type="text"
	            			name="ovabrw_last_name"
	            			placeholder="<?php esc_html_e( 'Last Name', 'ova-brw' ); ?>"
	            			required
	            		/>
	            	</div>
	            	<div class="item">
	            		<input
	            			type="email"
	            			name="ovabrw_email"
	            			placeholder="<?php esc_html_e( 'Email', 'ova-brw' ); ?>"
	            			required
	            		/>
	            	</div>
	            	<div class="item">
	            		<input
	            			type="text"
	            			name="ovabrw_phone"
	            			placeholder="<?php esc_html_e( 'Phone', 'ova-brw' ); ?>"
	            			required
	            		/>
	            	</div>
	            	<div class="item">
	            		<input
	            			type="text"
	            			name="ovabrw_company"
	            			placeholder="<?php esc_html_e( 'Company', 'ova-brw' ); ?>"
	            		/>
	            	</div>
	            	<div class="item">
	            		<input
	            			type="text"
	            			name="ovabrw_address_1"
	            			placeholder="<?php esc_html_e( 'Address 1', 'ova-brw' ); ?>"
	            			required
	            		/>
	            	</div>
	            	<div class="item">
	            		<input
	            			type="text"
	            			name="ovabrw_address_2"
	            			placeholder="<?php esc_html_e( 'Address 2', 'ova-brw' ); ?>"
	            		/>
	            	</div>
	            	<div class="item">
	            		<input
	            			type="text"
	            			name="ovabrw_city"
	            			placeholder="<?php esc_html_e( 'City', 'ova-brw' ); ?>"
	            		/>
	            	</div>
	            	<div class="item">
	            		<select name="ovabrw_country" class="ovabrw_country" style="width: 100%;" required>
							<?php WC()->countries->country_dropdown_options( $country, $state ); ?>
						</select>
	            	</div>
	            </div>
	            <div class="ship-to-different-address">
	            	<label class="ship-to-different-address-label">
						<input
							id="ship-to-different-address-checkbox"
							class="ship-to-different-address-checkbox input-checkbox"
							type="checkbox"
							name="ship_to_different_address"
							value="1"
						/>
						<span><?php echo esc_html_e( 'Ship to a different address?', 'ova-brw' ); ?></span>
					</label>
	            </div>
	            <div class="ovabrw-row ova-column-3 ovabrw-shipping">
	            	<div class="item">
	            		<input
	            			type="text"
	            			name="ship_ovabrw_first_name"
	            			placeholder="<?php esc_html_e( 'First Name', 'ova-brw' ); ?>"
	            		/>
	            	</div>
	            	<div class="item">
	            		<input
	            			type="text"
	            			name="ship_ovabrw_last_name"
	            			placeholder="<?php esc_html_e( 'Last Name', 'ova-brw' ); ?>"
	            		/>
	            	</div>
	            	<div class="item">
	            		<input
	            			type="text"
	            			name="ship_ovabrw_phone"
	            			placeholder="<?php esc_html_e( 'Phone', 'ova-brw' ); ?>"
	            		/>
	            	</div>
	            	<div class="item">
	            		<input
	            			type="text"
	            			name="ship_ovabrw_company"
	            			placeholder="<?php esc_html_e( 'Company', 'ova-brw' ); ?>"
	            		/>
	            	</div>
	            	<div class="item">
	            		<input
	            			type="text"
	            			name="ship_ovabrw_address_1"
	            			placeholder="<?php esc_html_e( 'Address 1', 'ova-brw' ); ?>"
	            		/>
	            	</div>
	            	<div class="item">
	            		<input
	            			type="text"
	            			name="ship_ovabrw_address_2"
	            			placeholder="<?php esc_html_e( 'Address 2', 'ova-brw' ); ?>"
	            		/>
	            	</div>
	            	<div class="item">
	            		<input
	            			type="text"
	            			name="ship_ovabrw_city"
	            			placeholder="<?php esc_html_e( 'City', 'ova-brw' ); ?>"
	            		/>
	            	</div>
	            	<div class="item">
	            		<select name="ship_ovabrw_country" class="ovabrw_country" style="width: 100%;">
							<?php WC()->countries->country_dropdown_options( $country, $state ); ?>
						</select>
	            	</div>
	            </div>
	            <div class="wrap_item">
	            	<div class="ovabrw-order">
		            	<div class="item">
		            		<div class="sub-item">
		            			<h3 class="title"><?php esc_html_e('Product', 'ova-brw'); ?></h3>
		            			<div class="rental_item">
		            				<label for="ovabrw-name-product">
		            					<?php esc_html_e( 'Name Product', 'ova-brw' ); ?>
		            				</label>
		            				<select 
		            					id="ovabrw-name-product" 
		            					class="ovabrw_name_product" 
		            					name="ovabrw_name_product[]" 
		            					data-symbol="<?php echo get_woocommerce_currency_symbol(); ?>" 
		            					data-date_format="<?php echo $date_format.' '.$set_time_format; ?>" data-short_date_format="<?php echo $date_format; ?>" required>
		            					<?php echo $html_list_option_product; ?>
		            				</select>
		            			</div>
		            		</div>
		            		<div class="sub-item ovabrw-meta">
		            			<h3 class="title"><?php esc_html_e('Add Meta', 'ova-brw'); ?></h3>
		            			<div class="rental_item ovabrw-price-detial">
									<label for="ovabrw-price-detail">
										<?php esc_html_e( 'Price detail', 'ova-brw' ); ?>
									</label>
									<input
										id="ovabrw-price-detail"
										type="text"
										name="ovabrw_price_detail[]"
										class="required ovabrw_price_detail"
										readonly
									/>
								</div>
		            			<div class="rental_item show_pickup_loc">
									<label><?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?></label>
									<?php 
										echo ovabrw_get_locations_html( 'ovabrw_pickup_loc[]', 'required ovabrw_pickup_loc', '' );
									?>
								</div>
								<div class="rental_item show_pickoff_loc">
									<label><?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?></label>
									<?php 
										echo ovabrw_get_locations_html( 'ovabrw_pickoff_loc[]', 'required ovabrw_pickoff_loc', '' );
									?>
								</div>
								<div class="rental_item ovabrw-pickup">
									<label for="ovabrw-pickup-date">
										<?php esc_html_e( 'Pick-up Date *', 'ova-brw' ); ?>
									</label>
									<input
										id="ovabrw-pickup-date"
										type="text"
										name="ovabrw_pickup_date[]"
										class="required ovabrw_start_date ovabrw_datetimepicker"
										autocomplete="off"
										placeholder="<?php echo $date_format.' '.$set_time_format; ?>"
										value=""
										date_rent_full=""
										required
									/>
								</div>
								<div class="rental_item ovabrw-dropoff">
									<label><?php esc_html_e( 'Drop-off Date *', 'ova-brw' ); ?></label>
									<input
										type="text"
										name="ovabrw_pickoff_date[]"
										class="required ovabrw_end_date ovabrw_datetimepicker"
										autocomplete="off"
										placeholder="<?php echo $date_format.' '.$set_time_format; ?>"
										value=""
										date_rent_full=""
									/>
								</div>
								<div class="rental_item ovabrw-location-field">
									<label><?php esc_html_e( 'Pick-up Time *', 'ova-brw' ); ?></label>
									<input
										type="text"
										name="ovabrw_pickup_time[]"
										class="required ovabrw_timepicker"
										placeholder="<?php echo $set_time_format; ?>"
										value=""
										autocomplete="off"
									/>
								</div>
								<div class="rental_item ovabrw-location-field">
									<label><?php esc_html_e( 'Pick-up Location *', 'ova-brw' ); ?></label>
									<span class="location-field">
										<input
											type="text"
											name="ovabrw_pickup_location[]"
											class="required ovabrw_pickup_location"
											placeholder="<?php esc_html_e( 'Enter a location', 'ova-brw' ); ?>"
											value=""
											autocomplete="off"
										/>
										<input
											type="hidden"
											id="ovabrw_origin"
											name="ovabrw_origin"
											value=""
										/>
										<i class="flaticon-add btn-add-waypoint" aria-hidden="true"></i>
									</span>
								</div>
								<div class="rental_item ovabrw-location-field">
									<label><?php esc_html_e( 'Drop-off Location *', 'ova-brw' ); ?></label>
									<span class="location-field">
										<input
											type="text"
											name="ovabrw_dropoff_location[]"
											class="required ovabrw_dropoff_location"
											placeholder="<?php esc_html_e( 'Enter a location', 'ova-brw' ); ?>"
											value=""
											autocomplete="off"
										/>
										<input
											type="hidden"
											id="ovabrw_destination"
											name="ovabrw_destination"
											value=""
										/>
									</span>
								</div>
								<div class="rental_item ovabrw-extra-time">
									<label><?php esc_html_e( 'Extra Time', 'ova-brw' ); ?></label>
									<select name="ovabrw_extra_time[]">
										<option value=""><?php esc_html_e( 'Select Time', 'ova-brw' ); ?></option>
									</select>
								</div>
								<div class="rental_item show_number_vehicle">
									<label for="ovabrw_number_vehicle">
										<?php esc_html_e( 'Quantity', 'ova-brw' ); ?>
									</label>
									<input
										type="number"
										name="ovabrw_number_vehicle[]"
										class="required ovabrw_number_vehicle"
										autocomplete="off"
										value="1"
										min="1"
										max="1"
									/>
									<label class="ovabrw_number_available_vehicle" style="color:<?php echo ovabrw_get_setting( get_option( 'ova_brw_bg_calendar', '#c4c4c4' ) ); ?>; font-size:1.em; width: 180px;"></label>
								</div>
								<div class="rental_item rental_type">
									<label for="ovabrw-rental-type">
										<?php esc_html_e( 'Rental Type', 'ova-brw' ); ?>
									</label>
									<select id="ovabrw-rental-type" name="ovabrw_rental_type[]" >
										<option value="day"><?php esc_html_e( 'Day', 'ova-brw' ); ?></option>
										<option value="hour"><?php esc_html_e( 'Hour', 'ova-brw' ); ?></option>
										<option value="mixed"><?php esc_html_e( 'Mixed ', 'ova-brw' ); ?></option>
										<option value="period_time">
											<?php esc_html_e( 'Period of Time', 'ova-brw' ); ?>
										</option>
										<option value="transportation">
											<?php esc_html_e( 'Transportation', 'ova-brw' ); ?>
										</option>
										<option value="taxi">
											<?php esc_html_e( 'Taxi', 'ova-brw' ); ?>
										</option>
									</select>
								</div>
								<div class="rental_item rental_define_day">
									<label for="ovabrw_define_1_day">
										<?php esc_html_e( 'Charged by', 'ova-brw' ); ?>
									</label>
									<select id="ovabrw_define_1_day" name="ovabrw_define_1_day[]" >
										<option value="day"><?php esc_html_e( 'Day', 'ova-brw' ); ?></option>
										<option value="hotel"><?php esc_html_e( 'Hotel', 'ova-brw' ); ?></option>
										<option value="hour"><?php esc_html_e( 'Hour ', 'ova-brw' ); ?></option>
									</select>
								</div>
								<div class="rental_item ovabrw-package">
									<label for="ovabrw-package"><?php esc_html_e( 'Package', 'ova-brw' ); ?></label>
									<span class="ovabrw-package-span"></span>
								</div>
								<div class="rental_item ovabrw-custom_ckf"></div>
								<div class="rental_item ovabrw-resources">
									<label for="ovabrw-resources"><?php esc_html_e( 'Resources', 'ova-brw' ); ?></label>
									<span class="ovabrw-resources-span"></span>
								</div>
								<div class="rental_item ovabrw-services">
									<label for="ovabrw-services"><?php esc_html_e( 'Services', 'ova-brw' ); ?></label>
									<span class="ovabrw-services-span"></span>
								</div>
								<div class="rental_item ovabrw-id-vehicle">
									<label for="ovabrw-id-vehicle">
										<?php esc_html_e( 'ID Vehicle', 'ova-brw' ); ?>
									</label>
									<span class="ovabrw-id-vehicle-span"></span>
								</div>
								<div class="rental_item">
									<label for="ovabrw-amount-insurance">
										<?php esc_html_e( 'Amount of insurance', 'ova-brw' ); ?>
									</label>
									<input
										id="ovabrw-amount-insurance"
										type="text"
										name="ovabrw_amount_insurance[]"
										class="required ovabrw_amoun_insurance"
										placeholder="0"
										readonly
									/>
								</div>
								<div class="rental_item">
									<label for="ovabrw-amount-deposite">
										<?php esc_html_e( 'Deposit Amount', 'ova-brw' ); ?>
									</label>
									<input
										id="ovabrw-amount-deposite"
										type="text"
										name="ovabrw_amount_deposite[]"
										class="required ovabrw_amoun_deposite"
										placeholder="0"
									/>
								</div>
								<div class="rental_item ovabrw-amount-remaining">
									<label for="ovabrw-amount-remaining">
										<?php esc_html_e( 'Remaining Amount', 'ova-brw' ); ?>
									</label>
									<input
										id="ovabrw-amount-remaining"
										type="text"
										name="ovabrw_amount_remaining[]"
										class="required ovabrw_amount_remaining"
										placeholder="0"
										<?php echo apply_filters( 'ovabrw_create_order_edit_view_time', 'readonly' ); ?>
									/>
								</div>
								<div class="rental_item ovabrw-error">
									<span class="ovabrw-error-span"></span>
								</div>
								<div class="rental_item ovabrw-total-time">
									<label for="ovabrw-total-time">
										<?php esc_html_e( 'Total time', 'ova-brw' ); ?>
									</label>
									<input
										id="ovabrw-total-time"
										type="text"
										name="ovabrw_total_time[]"
										class="required ovabrw_total_time"
										<?php echo apply_filters( 'ovabrw_create_order_edit_view_time', 'readonly' ); ?>
									/>
								</div>
								<div class="rental_item ovabrw-total-cost">
		            				<label for="ovabrw-total-product">
		            					<?php esc_html_e( 'Cost', 'ova-brw' ); ?>
		            				</label>
		            				<input
		            					id="ovabrw-total-product"
		            					type="text"
		            					name="ovabrw_total_product[]"
		            					class="required ovabrw_total_product"
		            					<?php echo apply_filters( 'ovabrw_create_order_edit_cost', 'readonly' ); ?>
		            				/>
		            				<span class="ovabrw-current-currency">
		            					<?php echo get_woocommerce_currency_symbol(); ?>
		            				</span>
		            			</div>
		            			<div class="ovabrw-directions">
									<div id="ovabrw_map" class="ovabrw_map"></div>
									<div class="directions-info">
										<div class="distance-sum">
											<h3 class="label"><?php esc_html_e( 'Total Distance', 'ova-brw' ); ?></h3>
											<span class="distance-value">0</span>
											<span class="distance-unit"><?php esc_html_e( 'km', 'ova-brw' ); ?></span>
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
								<input
									type="hidden"
									name="ovabrw-data-location"
									data-waypoint-text="<?php esc_html_e( 'Waypoint', 'ova-brw' ); ?>"
									data-price-by=""
									data-types=""
									data-lat=""
									data-lng=""
									data-zoom=""
									data-bounds=""
									data-bounds-lat=""
									data-bounds-lng=""
									data-bounds-radius=""
									data-restrictions=""
								/>
								<input
									type="hidden"
									name="ovabrw-duration-map[]"
									value=""
								/>
								<input
									type="hidden"
									name="ovabrw-duration[]"
									value=""
								/>
								<input
									type="hidden"
									name="ovabrw-distance[]"
									value=""
								/>
		            		</div>
		            	</div>
		            	<a href="#" class="delete_order">x</a>
		            </div>
	            </div>
				<div class="ovabrw-row">
					<a href="#" class="button insert_wrap_item" data-row="
						<?php
							ob_start();
						?>
							<div class="ovabrw-order">
				            	<div class="item">
				            		<div class="sub-item">
				            			<h3 class="title"><?php esc_html_e('Product', 'ova-brw') ?></h3>
				            			<div class="rental_item">
				            				<label for="ovabrw-name-product">
				            					<?php esc_html_e( 'Name Product', 'ova-brw' ); ?>
				            				</label>
				            				<select 
				            					id="ovabrw-name-product" 
				            					class="ovabrw_name_product" 
				            					name="ovabrw_name_product[]" 
				            					data-symbol="<?php echo get_woocommerce_currency_symbol(); ?>" data-date_format="<?php echo $date_format.' '.$set_time_format; ?>" data-short_date_format="<?php echo $date_format; ?>" 
				            					required>
				            					<?php echo $html_list_option_product ?>
				            				</select>
				            			</div>
				            		</div>
				            		<div class="sub-item ovabrw-meta">
				            			<h3 class="title"><?php esc_html_e('Add Meta', 'ova-brw') ?></h3>
				            			<div class="rental_item ovabrw-price-detial">
											<label for="ovabrw-price-detail">
												<?php esc_html_e( 'Price detail', 'ova-brw' ); ?>
											</label>
											<input
												id="ovabrw-price-detail"
												type="text"
												name="ovabrw_price_detail[]"
												class="required ovabrw_price_detail"
												readonly
											/>
										</div>
				            			<div class="rental_item show_pickup_loc">
											<label><?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?></label>
											<?php 
												echo ovabrw_get_locations_html( $name = 'ovabrw_pickup_loc[]', $required = 'required ovabrw_pickup_loc', $selected = '' );
											?>
										</div>
										<div class="rental_item show_pickoff_loc">
											<label><?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?></label>
											<?php 
												echo ovabrw_get_locations_html( $name = 'ovabrw_pickoff_loc[]', $required = 'required ovabrw_pickoff_loc', $selected = '' );
											?>
										</div>
										<div class="rental_item ovabrw-pickup">
											<label for="ovabrw-pickup-date">
												<?php esc_html_e( 'Pick-up Date', 'ova-brw' ); ?>
											</label>
											<input
												id="ovabrw-pickup-date"
												type="text"
												name="ovabrw_pickup_date[]"
												class="required ovabrw_start_date ovabrw_datetimepicker"
												autocomplete="off"
												placeholder="<?php echo $date_format.' '.$set_time_format; ?>"
												value=""
												date_rent_full=""
												required
											/>
										</div>
										<div class="rental_item ovabrw-dropoff">
											<label><?php esc_html_e( 'Drop-off Date', 'ova-brw' ); ?></label>
											<input
												type="text"
												name="ovabrw_pickoff_date[]"
												class="required ovabrw_end_date ovabrw_datetimepicker"
												autocomplete="off"
												placeholder="<?php echo $date_format.' '.$set_time_format; ?>"
												value=""
												date_rent_full=""
											/>
										</div>
										<div class="rental_item ovabrw-location-field">
											<label><?php esc_html_e( 'Pick-up Time *', 'ova-brw' ); ?></label>
											<input
												type="text"
												name="ovabrw_pickup_time[]"
												class="required ovabrw_timepicker"
												placeholder="<?php echo $set_time_format; ?>"
												value=""
												autocomplete="off"
											/>
										</div>
										<div class="rental_item ovabrw-location-field">
											<label><?php esc_html_e( 'Pick-up Location *', 'ova-brw' ); ?></label>
											<span class="location-field">
												<input
													type="text"
													name="ovabrw_pickup_location[]"
													class="required ovabrw_pickup_location"
													placeholder="<?php esc_html_e( 'Enter a location', 'ova-brw' ); ?>"
													value=""
													autocomplete="off"
												/>
												<input
													type="hidden"
													id="ovabrw_origin"
													name="ovabrw_origin"
													value=""
												/>
												<i class="flaticon-add btn-add-waypoint" aria-hidden="true"></i>
											</span>
										</div>
										<div class="rental_item ovabrw-location-field">
											<label><?php esc_html_e( 'Drop-off Location *', 'ova-brw' ); ?></label>
											<span class="location-field">
												<input
													type="text"
													name="ovabrw_dropoff_location[]"
													class="required ovabrw_dropoff_location"
													placeholder="<?php esc_html_e( 'Enter a location', 'ova-brw' ); ?>"
													value=""
													autocomplete="off"
												/>
												<input
													type="hidden"
													id="ovabrw_destination"
													name="ovabrw_destination"
													value=""
												/>
											</span>
										</div>
										<div class="rental_item ovabrw-extra-time">
											<label><?php esc_html_e( 'Extra Time', 'ova-brw' ); ?></label>
											<select name="ovabrw_extra_time[]">
												<option value=""><?php esc_html_e( 'Select Time', 'ova-brw' ); ?></option>
											</select>
										</div>
										<div class="rental_item show_number_vehicle">
											<label for="ovabrw_number_vehicle">
												<?php esc_html_e( 'Quantity', 'ova-brw' ); ?>
											</label>
											<input
												type="number"
												name="ovabrw_number_vehicle[]"
												class="required ovabrw_number_vehicle"
												autocomplete="off"
												value="1"
												min="1"
												max="1"
											/>
											<label class="ovabrw_number_available_vehicle" style="color:<?php echo ovabrw_get_setting( get_option( 'ova_brw_bg_calendar', '#c4c4c4' ) ); ?>; font-size:1.em; width: 180px;"></label>
										</div>
										<div class="rental_item rental_type">
											<label for="ovabrw-rental-type">
												<?php esc_html_e( 'Rental Type', 'ova-brw' ); ?>
											</label>
											<select id="ovabrw-rental-type" name="ovabrw_rental_type[]" >
												<option value="day"><?php esc_html_e( 'Day', 'ova-brw' ); ?></option>
												<option value="hour"><?php esc_html_e( 'Hour', 'ova-brw' ); ?></option>
												<option value="mixed">
													<?php esc_html_e( 'Mixed ', 'ova-brw' ); ?>
												</option>
												<option value="period_time">
													<?php esc_html_e( 'Period of Time', 'ova-brw' ); ?>
												</option>
												<option value="transportation">
													<?php esc_html_e( 'Transportation', 'ova-brw' ); ?>
												</option>
												<option value="taxi">
													<?php esc_html_e( 'Taxi', 'ova-brw' ); ?>
												</option>
											</select>
										</div>
										<div class="rental_item rental_define_day">
											<label for="ovabrw_define_1_day">
												<?php esc_html_e( 'Charged by', 'ova-brw' ); ?>
											</label>
											<select id="ovabrw_define_1_day" name="ovabrw_define_1_day[]" >
												<option value="day"><?php esc_html_e( 'Day', 'ova-brw' ); ?></option>
												<option value="hotel"><?php esc_html_e( 'Hotel', 'ova-brw' ); ?></option>
												<option value="hour"><?php esc_html_e( 'Hour ', 'ova-brw' ); ?></option>
											</select>
										</div>
										<div class="rental_item ovabrw-package">
											<label for="ovabrw-package">
												<?php esc_html_e( 'Package', 'ova-brw' ); ?>
											</label>
											<span class="ovabrw-package-span"></span>
										</div>
										<div class="rental_item ovabrw-custom_ckf"></div>
										<div class="rental_item ovabrw-resources">
											<label for="ovabrw-resources">
												<?php esc_html_e( 'Resources', 'ova-brw' ); ?>
											</label>
											<span class="ovabrw-resources-span"></span>
										</div>
										<div class="rental_item ovabrw-services">
											<label for="ovabrw-services">
												<?php esc_html_e( 'Services', 'ova-brw' ); ?>
											</label>
											<span class="ovabrw-services-span"></span>
										</div>
										<div class="rental_item ovabrw-id-vehicle">
											<label for="ovabrw-id-vehicle">
												<?php esc_html_e( 'ID Vehicle', 'ova-brw' ); ?>
											</label>
											<span class="ovabrw-id-vehicle-span"></span>
										</div>
										<div class="rental_item">
											<label for="ovabrw-amount-insurance">
												<?php esc_html_e( 'Amount of insurance', 'ova-brw' ); ?>
											</label>
											<input
												id="ovabrw-amount-insurance"
												type="text"
												name="ovabrw_amount_insurance[]"
												class="required ovabrw_amoun_insurance"
												placeholder="0"
												readonly
											/>
										</div>
										<div class="rental_item">
											<label for="ovabrw-amount-deposite">
												<?php esc_html_e( 'Deposit Amount', 'ova-brw' ); ?>
											</label>
											<input
												id="ovabrw-amount-deposite"
												type="text"
												name="ovabrw_amount_deposite[]"
												class="required ovabrw_amoun_deposite"
												placeholder="0"
											/>
										</div>
										<div class="rental_item ovabrw-amount-remaining">
											<label for="ovabrw-amount-remaining">
												<?php esc_html_e( 'Remaining Amount', 'ova-brw' ); ?>
											</label>
											<input
												id="ovabrw-amount-remaining"
												type="text"
												name="ovabrw_amount_remaining[]"
												class="required ovabrw_amount_remaining"
												placeholder="0"
												<?php echo apply_filters( 'ovabrw_create_order_edit_view_time', 'readonly' ); ?>
											/>
										</div>
										<div class="rental_item ovabrw-error">
											<span class="ovabrw-error-span"></span>
										</div>
										<div class="rental_item ovabrw-total-time">
											<label for="ovabrw-total-time">
												<?php esc_html_e( 'Total time', 'ova-brw' ); ?>
											</label>
											<input
												id="ovabrw-total-time"
												type="text"
												name="ovabrw_total_time[]"
												class="required ovabrw_total_time"
												<?php echo apply_filters( 'ovabrw_create_order_edit_view_time', 'readonly' ); ?>
											/>
										</div>
										<div class="rental_item ovabrw-total-cost">
				            				<label for="ovabrw-total-product">
				            					<?php esc_html_e( 'Cost', 'ova-brw' ); ?>
				            				</label>
				            				<input
				            					id="ovabrw-total-product"
				            					type="text"
				            					name="ovabrw_total_product[]"
				            					class="required ovabrw_total_product"
				            					<?php echo apply_filters( 'ovabrw_create_order_edit_cost', 'readonly' ); ?>
				            				/>
				            				<span><?php echo get_woocommerce_currency_symbol(); ?></span>
				            			</div>
				            			<div class="ovabrw-directions">
											<div id="ovabrw_map" class="ovabrw_map"></div>
											<div class="directions-info">
												<div class="distance-sum">
													<h3 class="label"><?php esc_html_e( 'Total Distance', 'ova-brw' ); ?></h3>
													<span class="distance-value">0</span>
													<span class="distance-unit"><?php esc_html_e( 'km', 'ova-brw' ); ?></span>
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
				            			<input
											type="hidden"
											name="ovabrw-data-location"
											data-waypoint-text="<?php esc_html_e( 'Waypoint', 'ova-brw' ); ?>"
											data-types=""
											data-lat=""
											data-lng=""
											data-zoom=""
											data-bounds=""
											data-bounds-lat=""
											data-bounds-lng=""
											data-bounds-radius=""
											data-restrictions=""
										/>
										<input
											type="hidden"
											name="ovabrw-duration-map[]"
											value=""
										/>
										<input
											type="hidden"
											name="ovabrw-duration[]"
											value=""
										/>
										<input
											type="hidden"
											name="ovabrw-distance[]"
											value=""
										/>
				            		</div>
				            	</div>
				            	<a href="#" class="delete_order">x</a>
				            </div>
						<?php
							echo esc_attr( ob_get_clean() );
						?>
					">
					<?php esc_html_e( 'Add Item', 'ova-brw' ); ?></a>
					</a>
				</div>
				<button type="submit" class="button"><?php esc_html_e( 'Create Order', 'ova-brw' ); ?></button>
	    	</div>
	        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
	        <input type="hidden" name="post_type" value="product" />
	        <input type="hidden" name="ovabrw_create_order" value="create_order" />
	        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	        <!-- Now we can render the completed list table -->
	    </form>
	</div>
<?php }