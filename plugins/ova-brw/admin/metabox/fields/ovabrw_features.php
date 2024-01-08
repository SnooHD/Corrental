<div class="ovabrw_features">
	<table class="widefat">
		<thead>
			<tr>
				<?php if ( apply_filters( 'ovabrw_show_icon_features', 'true' ) ): ?>
					<th><?php esc_html_e( 'Icon Class', 'ova-brw' ); ?></th>
				<?php endif; ?>
				<th><?php esc_html_e( 'Label', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Description', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Feature', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Special', 'ova-brw' ); ?></th>
			</tr>
		</thead>
		<tbody class="wrap_rt_features">
			<!-- Append html here -->
			<?php
				$features_desc = get_post_meta( $post_id, 'ovabrw_features_desc', true );

				if ( $features_desc ):
					$features_label 	= get_post_meta( $post_id, 'ovabrw_features_label', true );
					$features_icons 	= get_post_meta( $post_id, 'ovabrw_features_icons', true );
					$features_special 	= get_post_meta( $post_id, 'ovabrw_features_special', true );
					$features_featured 	= get_post_meta( $post_id, 'ovabrw_features_featured', true );

					for ( $i = 0 ; $i < count( $features_desc ); $i++ ):
						$special = isset( $features_special[$i] ) ? $features_special[$i] : 'yes';
						$feature = isset( $features_featured[$i] ) ? $features_featured[$i] : 'no';
			?>
				<tr class="tr_rt_feature">
					<?php if ( apply_filters( 'ovabrw_show_icon_features', 'true' ) ): ?>
					    <td width="30%">
					      	<input
					      		type="text"
					      		name="ovabrw_features_icons[]"
					      		placeholder="<?php esc_html_e( 'Icon-Class', 'ova-brw' ); ?>"
					      		value="<?php echo isset( $features_icons[$i] ) ? $features_icons[$i] : ''; ?>"
					      	/>
					    </td>
					<?php endif; ?>
				    <td width="20%">
				      	<input
				      		type="text"
				      		name="ovabrw_features_label[]"
				      		placeholder="<?php esc_html_e( 'Label', 'ova-brw' ); ?>"
				      		value="<?php echo isset( $features_label[$i] ) ? $features_label[$i] : ''; ?>"
				      	/>
				    </td>
				    <?php $cols = apply_filters( 'ovabrw_show_icon_features', 'true' ) ? '29%' : '59%'; ?>
				    <td width="<?php echo $cols; ?>">
				      	<input
				      		type="text"
				      		name="ovabrw_features_desc[]"
				      		placeholder="<?php esc_html_e( 'Description', 'ova-brw' ); ?>"
				      		value="<?php echo isset( $features_desc[$i] ) ? $features_desc[$i] : '' ; ?>"
				      	/>
				    </td>
				    <td width="10%">
				      	<select name="ovabrw_features_special[]" class="short_dura">
				    		<option value="yes"<?php selected( $special, 'yes' ); ?>>
				    			<?php esc_html_e( 'Yes', 'ova-brw' ); ?>
				    		</option>
				    		<option value="no"<?php selected( $special, 'no' ); ?>>
				    			<?php esc_html_e( 'No', 'ova-brw' ); ?>
				    		</option>
				    	</select>
				    </td>
				    <td width="10%">
				      	<select name="ovabrw_features_featured[]" class="short_dura">
				    		<option value="yes"<?php selected( $feature, 'yes' ); ?>>
				    			<?php esc_html_e( 'Yes', 'ova-brw' ); ?>
				    		</option>
				    		<option value="no"<?php selected( $feature, 'no' ); ?>>
				    			<?php esc_html_e( 'No', 'ova-brw' ); ?>
				    		</option>
				    	</select>
				    </td>
				    <td width="1%"><a href="#" class="button delete_feature">x</a></td>
				</tr>
			<?php endfor; endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="6">
					<a href="#" class="button insert_rt_feature" data-row="
						<?php
							ob_start();
							include( OVABRW_PLUGIN_PATH.'/admin/metabox/fields/ovabrw_feature_field.php' );
							echo esc_attr( ob_get_clean() );
						?>
					">
					<?php esc_html_e( 'Add Feature', 'ova-brw' ); ?></a>
					</a>
				</th>
			</tr>
		</tfoot>
	</table>
</div>