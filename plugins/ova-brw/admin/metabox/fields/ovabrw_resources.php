<div class="ovabrw_resources">
	<span style="display: block; margin-bottom: 10px;">
        <em><?php esc_html_e( 'Quantity: maximum per booking', 'ova-brw' ) ?></em>
    </span>
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
		<tbody class="wrap_resources">
		<!-- Append html here -->
		<?php
			$rental_type = get_post_meta( $post_id, 'ovabrw_price_type', true );
			$res_ids = get_post_meta( $post_id, 'ovabrw_resource_id', true );
			
			if ( ! empty( $res_ids ) && is_array( $res_ids ) ):
				$res_names 		= get_post_meta( $post_id, 'ovabrw_resource_name', true );
				$res_prices 	= get_post_meta( $post_id, 'ovabrw_resource_price', true );
				$res_durations 	= get_post_meta( $post_id, 'ovabrw_resource_duration_type', true );
				$res_quantity 	= get_post_meta( $post_id, 'ovabrw_resource_quantity', true );

				foreach ( $res_ids as $k => $id ):
					$name 		= isset( $res_names[$k] ) ? $res_names[$k] : '';
					$price 		= isset( $res_prices[$k] ) ? $res_prices[$k] : '';
					$duration 	= isset( $res_durations[$k] ) ? $res_durations[$k] : '';
					$quantity 	= isset( $res_quantity[$k] ) ? $res_quantity[$k] : '';
		?>
				<tr class="tr_rt_resource">
					<td width="15%">
				      	<input
				      		type="text"
				      		name="ovabrw_resource_id[]"
				      		value="<?php echo esc_attr( $id ); ?>"
				      		placeholder="<?php esc_html_e( 'Not space', 'ova-brw' ); ?>"
				      	/>
				    </td>
				    <td width="29%">
				      	<input
				      		type="text"
				      		name="ovabrw_resource_name[]"
				      		value="<?php echo esc_attr( $name ); ?>"
				      		placeholder="<?php esc_html_e( 'Name', 'ova-brw' ); ?>"
				      	/>
				    </td>
				    <td width="25%">
				      	<input
				      		type="text"
				      		name="ovabrw_resource_price[]"
				      		value="<?php echo esc_attr( $price ); ?>"
				      		placeholder="<?php esc_html_e( '10.5', 'ova-brw' ); ?>"
				      	/>
				    </td>
				    <td width="15%">
				      	<input
				      		type="number"
				      		name="ovabrw_resource_quantity[]"
				      		value="<?php echo esc_attr( $quantity ); ?>"
				      		placeholder="<?php esc_html_e( 'Number', 'ova-brw' ); ?>"
				      	/>
				    </td>
				    <td width="15%">
				    	<select name="ovabrw_resource_duration_type[]" class="short_dura">
						<?php
							if ( $rental_type === 'day' ): ?>
								<option value="days"<?php selected( $duration, 'days' ); ?>>
									<?php esc_html_e( '/Days', 'ova-brw' ); ?>
								</option>
					    		<option value="total"<?php selected( $duration, 'total' ); ?>>
					    			<?php esc_html_e( '/Total', 'ova-brw' ); ?>
					    		</option>
							<?php elseif ( $rental_type === 'hour' ): ?> 
								<option value="hours"<?php selected( $duration, 'hours' ); ?>>
									<?php esc_html_e( '/Hours', 'ova-brw' ); ?>
								</option>
					    		<option value="total"<?php selected( $duration, 'total' ); ?>>
					    			<?php esc_html_e( '/Total', 'ova-brw' ); ?>
					    		</option>
							<?php elseif ( $rental_type === 'mixed' || $rental_type == 'period_time' ): ?> 
								<option value="hours"<?php selected( $duration, 'hours' ); ?>>
									<?php esc_html_e( '/Hours', 'ova-brw' ); ?>
								</option>
								<option value="days"<?php selected( $duration, 'days' ); ?>>
									<?php esc_html_e( '/Days', 'ova-brw' ); ?>
								</option>
					    		<option value="total"<?php selected( $duration, 'total' ); ?>>
					    			<?php esc_html_e( '/Total', 'ova-brw' ); ?>
					    		</option>
							<?php elseif ( $rental_type === 'transportation' || $rental_type === 'taxi' ): ?> 
								<option value="total" selected>
									<?php esc_html_e( '/Total', 'ova-brw' ); ?>
								</option>
							<?php endif; ?>
				    	</select>
				    </td>
				    <td width="1%"><a href="#" class="button delete_resource">x</a></td>
				</tr>
			<?php endforeach; endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="6">
					<a href="#" class="button insert_resources" data-row="
						<?php
							ob_start();
							include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_resources_field.php' );
							echo esc_attr( ob_get_clean() );
						?>
					">
					<?php esc_html_e( 'Add Resources', 'ova-brw' ); ?></a>
					</a>
				</th>
			</tr>
		</tfoot>
	</table>
</div>