<?php if ( ! defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw_st_locations">
	<table class="widefat">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?></th>
			</tr>
		</thead>
		<tbody class="wrap_resources">
			<!-- Append html here -->
			<?php 

			if ( class_exists('OVABRW') && class_exists('woocommerce') ) {
			  	$locaitons = ovabrw_get_locations_array();
			} else {
			  	$locaitons = array();
			}

			$pickup_locations 	= get_post_meta( $post_id, 'ovabrw_st_pickup_loc', 'false' );
			$dropoff_locations 	= get_post_meta( $post_id, 'ovabrw_st_dropoff_loc', 'false' );

			if ( $pickup_locations && ! empty( $locaitons ) ) { 
				for( $i = 0 ; $i < count( $pickup_locations ); $i++ ) {
			?>
				<tr class="tr_setup_loc">
				    <td width="50%">
				      	<select name="ovabrw_st_pickup_loc[]" data-number_loc="<?php echo esc_attr( count( $locaitons ) ); ?>">
				      		<option value="" ><?php echo esc_html__( 'Select Location', 'ova-brw' ); ?></option>
							<?php 
							if ( $locaitons ):
								foreach( $locaitons as $location ):
									$selected = isset( $pickup_locations[$i] ) && $pickup_locations[$i] == $location ? 'selected' : '';
								?>
									<option value="<?php echo esc_attr( $location ); ?>" <?php echo esc_attr( $selected ); ?>>
										<?php echo esc_html( $location ); ?>
									</option>
							<?php endforeach; endif ?>
					    </select>
				    </td>
				    <td width="50%">
				      <select name="ovabrw_st_dropoff_loc[]">
				      		<option value=""><?php echo esc_html__( 'Select Location', 'ova-brw' ); ?></option>
							<?php 
							if ( $locaitons ):
								foreach( $locaitons as $location ):
									$selected = isset( $dropoff_locations[$i] ) && $dropoff_locations[$i] == $location ? 'selected' : '';
								?>
									<option value="<?php echo esc_attr( $location ); ?>" <?php echo esc_attr( $selected ) ?>>
										<?php echo esc_html( $location ); ?>
									</option>
							<?php endforeach; endif ?>
					    </select>
				    </td>
				    <td width="1%"><a href="#" class="button delete_st_location">x</a></td>
				</tr>
			<?php } } ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="6">
					<a href="#" class="button insert_st_location" data-row="
						<?php
							ob_start();
							include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_choose_location-field.php' );
							echo esc_attr( ob_get_clean() );
						?>
					">
					<?php esc_html_e( 'Add Location', 'ova-brw' ); ?></a>
					</a>
				</th>
			</tr>
		</tfoot>
	</table>
</div>