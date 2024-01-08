<?php if ( ! defined( 'ABSPATH' ) ) exit;
	$discount_distance_from 	= get_post_meta( $post_id, 'ovabrw_discount_distance_from', true );
	$discount_distance_to 		= get_post_meta( $post_id, 'ovabrw_discount_distance_to', true );
	$discount_distance_price 	= get_post_meta( $post_id, 'ovabrw_discount_distance_price', true );
	$price_by 					= get_post_meta( $post_id, 'ovabrw_map_price_by', true );

	if ( ! $price_by ) $price_by = 'km';
?>
<div class="ovabrw_rt_discount_distance">
	<table class="widefat">
		<thead>
			<tr>
				<th><?php printf( esc_html__( 'From (%s)', 'ova-brw' ), $price_by ); ?></th>
				<th><?php printf( esc_html__( 'To (%s)', 'ova-brw' ), $price_by ); ?></th>
				<th><?php printf( esc_html__( 'Price (%s)', 'ova-brw' ), get_woocommerce_currency_symbol() ); ?></th>
			</tr>
		</thead>
		<tbody class="wrap_rt_discount_distance">
			<?php 
			if ( ! empty( $discount_distance_from ) && ! empty( $discount_distance_to ) && ! empty( $discount_distance_price ) ):
				for ( $i = 0 ; $i < count( $discount_distance_price ); $i++ ):
					$from 	= isset( $discount_distance_from[$i] ) ? $discount_distance_from[$i] : '';
					$to 	= isset( $discount_distance_to[$i] ) ? $discount_distance_to[$i] : '';
					$price 	= isset( $discount_distance_price[$i] ) ? $discount_distance_price[$i] : '';
			?>
				<tr class="tr_rt_discount_distance">
				    <td width="30%">
				      	<input
				      		type="text"
				      		name="ovabrw_discount_distance_from[]"
					      	value="<?php echo esc_attr( $from ); ?>"
					      	placeholder="<?php esc_attr_e( 'Number', 'ova-brw' ); ?>"
					      	autocomplete="off"
					    />
				    </td>
				    <td width="30%">
				      	<input
				      		type="text"
				      		name="ovabrw_discount_distance_to[]"
				      		value="<?php echo esc_attr( $to ); ?>"
				      		placeholder="<?php esc_attr_e( 'Number', 'ova-brw' ); ?>"
				      		autocomplete="off"
				      	/>
				    </td>
				    <td width="30%">
				      	<input
				      		type="text"
				      		name="ovabrw_discount_distance_price[]"
				      		value="<?php echo esc_attr( $price ); ?>"
				      		placeholder="<?php esc_attr_e( 'Number', 'ova-brw' ); ?>"
				      		autocomplete="off"
				      	/>
				    </td>
				    <td width="1%"><a href="#" class="button remove_discount_distance">x</a></td>
				</tr>
			<?php endfor; endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="6">
					<a href="#" class="button insert_discount_distance" data-row="
						<?php
							ob_start();
							include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_discount_distance_field.php' );
							echo esc_attr( ob_get_clean() );
						?>

					">
					<?php esc_html_e( 'Add Discount', 'ova-brw' ); ?></a>
					</a>
				</th>
			</tr>
		</tfoot>
	</table>
</div>