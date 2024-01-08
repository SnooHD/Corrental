<?php if ( ! defined( 'ABSPATH' ) ) exit();
	if ( get_option( 'ova_brw_request_booking_form_show_service', 'yes' ) != 'yes' ) return;

	global $product;
	if ( ! $product ) return;

	$product_id 	= $product->get_id();
	$services_label = get_post_meta( $product_id, 'ovabrw_label_service', true );
?>
<?php if ( ! empty( $services_label ) && is_array( $services_label ) ):
	$services_required 	= get_post_meta( $product_id, 'ovabrw_service_required', true );
	$services_id 		= get_post_meta( $product_id, 'ovabrw_service_id', true );
	$services_name 		= get_post_meta( $product_id, 'ovabrw_service_name', true );
	$services_qty 		= get_post_meta( $product_id, 'ovabrw_service_qty', true );
?>
	<div class="ovabrw-services">
		<label><?php esc_html_e( 'Services', 'ova-brw' ); ?></label>
		<div class="ovabrw-service">
			<?php for ( $i = 0; $i < count( $services_label ); $i++ ):
				$label 		= $services_label[$i];
				$required 	= isset( $services_required[$i] ) ? $services_required[$i] : '';

				// Option
				$serv_ids = isset( $services_id[$i] ) ? $services_id[$i] : '';
			?>
				<div class="rental_item">
					<div class="error_item">
						<label><?php esc_html_e( 'This field is required', 'ova-brw' ) ?></label>
					</div>
					<div class="ovabrw-select">
						<select name="ovabrw_service[]" <?php if ( $required === 'yes' ) echo 'class="required"'; ?>>
							<option value="">
								<?php printf( esc_html__( 'Select %s', 'ova-brw' ), $label ); ?>
							</option>
							<?php if ( ! empty( $serv_ids ) && is_array( $serv_ids ) ): ?>
								<?php foreach ( $serv_ids as $k => $id ):
									$name = isset( $services_name[$i][$k] ) ? $services_name[$i][$k] : '';
								?>
									<option value="<?php echo esc_attr( $id ); ?>">
										<?php echo esc_html( $name ); ?>
									</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
						<?php if ( ! empty( $serv_ids ) && is_array( $serv_ids ) ): ?>
							<?php foreach ( $serv_ids as $k => $id ):?>
								<?php if ( isset( $services_qty[$i][$k] ) && absint( $services_qty[$i][$k] ) ): ?>
									<div class="ovabrw-qty-control">
										<div class="ovabrw-qty-input">
											<input
												type="number"
												name="<?php echo esc_attr( 'ovabrw_service_qty['.$id.']' ); ?>"
												class="qty-input"
												value="1"
												min="1"
												max="<?php echo esc_attr( absint( $services_qty[$i][$k] ) ); ?>"
												readonly
											/>
											<i aria-hidden="true" class="brwicon-down-arrow"></i>
										</div>
										<div class="ovabrw-qty-dropdown">
											<div class="qty-btn minus">
												<span class="flaticon-substract"></span>
											</div>
											<span class="qty">1</span>
											<div class="qty-btn plus">
												<span class="flaticon-add"></span>
											</div>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endfor; ?>
		</div>
	</div>
<?php endif; ?>