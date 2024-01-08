<?php if ( ! defined( 'ABSPATH' ) ) exit;
	$ovabrw_extra_time_hour 	= get_post_meta( $post_id, 'ovabrw_extra_time_hour', true );
	$ovabrw_extra_time_label 	= get_post_meta( $post_id, 'ovabrw_extra_time_label', true );
	$ovabrw_extra_time_price 	= get_post_meta( $post_id, 'ovabrw_extra_time_price', true );
?>
<div class="ovabrw_rt_extra_time">
	<table class="widefat">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Time (hour)', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Label', 'ova-brw' ); ?></th>
				<th><?php printf( esc_html__( 'Price (%s)', 'ova-brw' ), get_woocommerce_currency_symbol() ); ?></th>
			</tr>
		</thead>
		<tbody class="wrap_rt_extra_time">
			<?php 
			if ( ! empty( $ovabrw_extra_time_hour ) && ! empty( $ovabrw_extra_time_hour ) ):
				for ( $i = 0 ; $i < count( $ovabrw_extra_time_hour ); $i++ ):
					$label = isset( $ovabrw_extra_time_label[$i] ) ? $ovabrw_extra_time_label[$i] : '';
					$price = isset( $ovabrw_extra_time_price[$i] ) ? $ovabrw_extra_time_price[$i] : '';
			?>
				<tr class="tr_rt_extra_time">
				    <td width="20%">
				      	<input
				      		type="text"
				      		name="ovabrw_extra_time_hour[]"
					      	value="<?php echo esc_attr( $ovabrw_extra_time_hour[$i] ); ?>"
					      	placeholder="<?php esc_attr_e( 'Number', 'ova-brw' ); ?>"
					      	autocomplete="off"
					    />
				    </td>
				    <td width="20%">
				      	<input
				      		type="text"
				      		name="ovabrw_extra_time_label[]"
				      		value="<?php echo esc_attr( $label ); ?>"
				      		placeholder="<?php esc_attr_e( 'Text', 'ova-brw' ); ?>"
				      		autocomplete="off"
				      	/>
				    </td>
				    <td width="20%">
				      	<input
				      		type="text"
				      		name="ovabrw_extra_time_price[]"
				      		value="<?php echo esc_attr( $price ); ?>"
				      		placeholder="<?php esc_attr_e( 'Number', 'ova-brw' ); ?>"
				      		autocomplete="off"
				      	/>
				    </td>
				    <td width="1%"><a href="#" class="button remove_extra_time">x</a></td>
				</tr>
			<?php endfor; endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="6">
					<a href="#" class="button insert_extra_time" data-row="
						<?php
							ob_start();
							include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_extra_time_field.php' );
							echo esc_attr( ob_get_clean() );
						?>

					">
					<?php esc_html_e( 'Add Time', 'ova-brw' ); ?></a>
					</a>
				</th>
			</tr>
		</tfoot>
	</table>
</div>