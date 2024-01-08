<div class="ovabrw_service">
	<span style="display: block; margin-bottom: 10px;">
        <em><?php esc_html_e( 'Quantity: maximum per booking', 'ova-brw' ) ?></em>
    </span>
	<div class="wrap_ovabrw_service_group">
	<?php
		$rental_type 		= get_post_meta( $post_id, 'ovabrw_price_type', true );
		$service_labels 	= get_post_meta( $post_id, 'ovabrw_label_service', true );
		$service_required 	= get_post_meta( $post_id, 'ovabrw_service_required', true );
		$service_ids 		= get_post_meta( $post_id, 'ovabrw_service_id', true );
		$service_names 		= get_post_meta( $post_id, 'ovabrw_service_name', true );
		$service_prices 	= get_post_meta( $post_id, 'ovabrw_service_price', true );
		$service_qtys 		= get_post_meta( $post_id, 'ovabrw_service_qty', true );
		$duration_types 	= get_post_meta( $post_id, 'ovabrw_service_duration_type', true );
		
		if ( $service_labels ) {
			for ( $i = 0; $i < count( $service_labels ); $i++ ) {

		?>
		<div class="ovabrw_service_group">
			<div class="service_input">
				<div class="ovabrw_label_service">
					<span class="ovabrw_label_service">
						<?php esc_html_e( 'Label *', 'ova-brw' ); ?>
					</span>
					<input 
						type="text" 
						class="ovabrw_input_label" 
						name="ovabrw_label_service[]" 
						value="<?php echo esc_attr( $service_labels[$i] ); ?>" />
					<span>
						<?php esc_html_e( 'Required', 'ova-brw' ); ?>
					</span>
					<select name="ovabrw_service_required[]">
						<option value="yes" <?php if ( $service_required[$i] == 'yes' ) echo 'selected'; ?>>
							<?php esc_html_e( 'Yes', 'ova-brw' ); ?>
						</option>
						<option value="no" <?php if( $service_required[$i] == 'no' ) echo 'selected'; ?>>
							<?php esc_html_e( 'No', 'ova-brw' ); ?>
						</option>
					</select>
				</div>
				<a href="#" class="button delete_service_group">x</a>
			</div>
			<table class="widefat">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Unique ID *', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'Name *', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'Price *', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'Quantity', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'Duration', 'ova-brw' ); ?></th>
					</tr>
				</thead>
				<tbody class="wrap_service">
				<?php
					if ( isset( $service_ids[$i] ) && is_array( $service_ids[$i] ) && ! empty( $service_ids ) ) {
						foreach ( $service_ids[$i] as $key => $val ) {
				?>
						<tr class="tr_rt_service">
						    <td width="15%">
						      	<input 
						      		type="text" 
						      		name="ovabrw_service_id[<?php echo $i; ?>][]" 
						      		placeholder="<?php esc_html_e( 'Not space', 'ova-brw' ); ?>" 
						      		value="<?php echo esc_attr( $val ); ?>" />
						    </td>
						    <td width="34%">
						      	<input 
						      		type="text" 
						      		name="ovabrw_service_name[<?php echo $i; ?>][]" 
						      		value="<?php echo isset( $service_names[$i][$key] ) ? esc_attr( $service_names[$i][$key] ) : ''; ?>" />
						    </td>
						    <td width="20%">
						      	<input 
						      		type="text" 
						      		name="ovabrw_service_price[<?php echo $i; ?>][]" 
						      		value="<?php echo isset( $service_prices[$i][$key] ) ? esc_attr( $service_prices[$i][$key] ) : ''; ?>" 
						      		placeholder="<?php esc_html_e( 'Price', 'ova-brw' ); ?>" />
						    </td>
						    <td width="15%">
						      	<input 
						      		type="number" 
						      		name="ovabrw_service_qty[<?php echo $i; ?>][]" 
						      		value="<?php echo isset( $service_qtys[$i][$key] ) ? esc_attr( $service_qtys[$i][$key] ) : ''; ?>" 
						      		placeholder="<?php esc_html_e( 'Number', 'ova-brw' ); ?>" />
						    </td>
						    <td width="15%">
						      	<select name="ovabrw_service_duration_type[<?php echo $i; ?>][]" class="short_dura">
								<?php
									$selected_hour 	= isset( $duration_types[$i][$key] ) && $duration_types[$i][$key] == 'hours' ? 'selected' : '';
									$selected_day 	= isset( $duration_types[$i][$key] ) && $duration_types[$i][$key] == 'days' ? 'selected' : '';
									$selected_total = isset( $duration_types[$i][$key] ) && $duration_types[$i][$key] == 'total' ? 'selected' : '';

									if ( $rental_type === 'day' ) { ?>
										<option value="days" <?php echo esc_attr( $selected_day ); ?>>
											<?php esc_html_e( '/Days', 'ova-brw' ); ?>
										</option>
							    		<option value="total" <?php echo esc_attr( $selected_total ); ?>>
							    			<?php esc_html_e( '/Total', 'ova-brw' ); ?>
							    		</option>
									<?php } elseif ( $rental_type === 'hour' ) { ?> 
										<option value="hours" <?php echo esc_attr( $selected_hour ); ?>>
											<?php esc_html_e( '/Hours', 'ova-brw' ); ?>
										</option>
							    		<option value="total" <?php echo esc_attr( $selected_total ); ?>>
							    			<?php esc_html_e( '/Total', 'ova-brw' ); ?>
							    		</option>
									<?php } elseif ( $rental_type === 'mixed' || $rental_type == 'period_time' ) { ?> 
										<option value="hours" <?php echo esc_attr( $selected_hour ); ?>>
											<?php esc_html_e( '/Hours', 'ova-brw' ); ?>
										</option>
										<option value="days" <?php echo esc_attr( $selected_day ); ?>>
											<?php esc_html_e( '/Days', 'ova-brw' ); ?>
										</option>
							    		<option value="total" <?php echo esc_attr( $selected_total ); ?>>
							    			<?php esc_html_e( '/Total', 'ova-brw' ); ?>
							    		</option>
									<?php } elseif ( $rental_type === 'transportation' || $rental_type === 'taxi' ) {  ?> 
										<option value="total" selected>
											<?php esc_html_e( '/Total', 'ova-brw' ); ?>
										</option>
									<?php } ?>
						        </select>
						    </td>
						    <td width="1%"><a href="#" class="button delete_service">x</a></td>
						</tr>
				<?php } } ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6">
							<a href="#" class="button insert_service_option" >
							<?php esc_html_e( 'Add Option', 'ova-brw' ); ?>
							</a>
							<div class="ovabrw_content_service" style="display: none">
								<table>
									<?php include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_service_field.php' ); ?>
								</table>
							</div>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	<?php } } ?>
	</div>
	<div class="ovabrw-service-add">
		<a href="#" class="button insert_service_group" data-row="
			<?php
				ob_start();
				include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_service_group.php' );
				echo esc_attr( ob_get_clean() );
			?>
		">
		<?php esc_html_e( 'Add Service', 'ova-brw' ); ?></a>
		</a>
	</div>
</div>