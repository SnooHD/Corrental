<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;	
	if ( ! $product ) return;

	$product_id = $product->get_id();

	$list_ckf 		= ovabrw_get_list_field_checkout( $product_id );
	$special_fields = [ 'textarea', 'select', 'radio', 'checkbox', 'file' ];
?>
<?php if ( ! empty( $list_ckf ) && is_array( $list_ckf ) ):
	foreach ( $list_ckf as $key => $field ):
		if ( array_key_exists( 'enabled', $field ) && $field['enabled'] == 'on' ):
			if ( array_key_exists( 'required', $field ) && $field['required'] == 'on' ) {
				$required = ' required';
			} else {
				$required = '';
			}

			$type = '';
			if ( $field['type'] === 'textarea' ) $type = ' ovabrw-textarea-field';
			if ( $field['class'] ) $type .= ' '.$field['class'];
?>
			<div class="rental_item<?php echo esc_attr( $type ); ?>">
				<label><?php echo esc_html( $field['label'] ); ?></label>
				<div class="error_item">
					<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
				</div>
				<?php if ( isset( $field['description'] ) && $field['description'] ): ?>
					<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! in_array( $field['type'], $special_fields ) ): ?>
					<input 
						type="<?php echo esc_attr( $field['type'] ); ?>" 
						name="<?php echo esc_attr( $key ); ?>" 
						class="<?php echo esc_attr( $field['class'] ) . $required; ?>" 
						placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
						value="<?php echo esc_attr( $field['default'] ); ?>" />
				<?php endif; ?>
				<?php if ( $field['type'] === 'textarea' ): ?>
					<textarea
						name="<?php echo esc_attr( $key ) ;?>"
						class="<?php echo esc_attr( $field['class'] ) . $required; ?>"
						placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
						value="<?php echo $field['default']; ?>"></textarea>
				<?php endif; ?>
				<?php if ( $field['type'] === 'select' ): 
					$ova_options_key = $ova_options_text = $ova_options_qty = [];

					if ( array_key_exists( 'ova_options_key', $field ) ) {
						$ova_options_key = $field['ova_options_key'];
					}

					if ( array_key_exists( 'ova_options_text', $field ) ) {
						$ova_options_text = $field['ova_options_text'];
					}

					if ( array_key_exists( 'ova_options_qty', $field ) ) {
						$ova_options_qty = $field['ova_options_qty'];
					}
				?>
					<div class="ovabrw-select">
						<select
							name="<?php echo esc_attr( $key ); ?>"
							class="<?php echo esc_attr( $field['class'] ) . $required; ?>">
						<?php 
							if ( ! empty( $ova_options_text ) && is_array( $ova_options_text ) ):
								if ( $field['required'] != 'on' ):
							?>
									<option value="">
										<?php printf( esc_html__( 'Select %s', 'ova_brw' ), $field['label'] ); ?>
									</option>
							<?php endif;
								foreach ( $ova_options_text as $k => $value ): 
							?>
									<option
										value="<?php echo esc_attr( $ova_options_key[$k] ); ?>"
										<?php selected( $field['default'], $ova_options_key[$k] ); ?>>
										<?php echo esc_html( $value ); ?>
									</option>
							<?php endforeach; ?>
						<?php endif; ?>
						</select>
						<?php if ( ! empty( $ova_options_key ) && is_array( $ova_options_key ) ): ?>
							<?php foreach ( $ova_options_key as $k => $v ): ?>
								<?php if ( isset( $ova_options_qty[$k] ) && absint( $ova_options_qty[$k] ) ): ?>
									<div class="ovabrw-qty-control">
										<div class="ovabrw-qty-input">
											<input
												type="number"
												name="<?php echo esc_attr( $key ).'_qty['.$v.']'; ?>"
												class="qty-input"
												value="1"
												min="1"
												max="<?php echo esc_attr( absint( $ova_options_qty[$k] ) ); ?>"
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
				<?php endif; ?>
				<?php if ( $field['type'] === 'radio' ):
					$values 	= isset( $field['ova_values'] ) ? $field['ova_values'] : '';
					$qtys 		= isset( $field['ova_qtys'] ) ? $field['ova_qtys'] : '';
					$default 	= isset( $field['default'] ) ? $field['default'] : '';

					if ( ! empty( $values ) && is_array( $values ) ):
						foreach ( $values as $k => $value ):
							$checked = '';

							if ( ! $default && $field['required'] === 'on' ) $default = $values[0];
							if ( $default === $value ) $checked = 'checked';
				?>			
						<div class="ovabrw-radio">
							<label class="ovabrw-label-field">
								<?php echo esc_html( $value ); ?>
								<input 
									type="radio"
									name="<?php echo esc_attr( $key ); ?>"
									value="<?php echo esc_attr( $value ); ?>"
									<?php echo esc_html( $checked ); ?>
								/>
								<span class="checkmark"></span>
							</label>
							<?php if ( isset( $qtys[$k] ) && absint( $qtys[$k] ) ): ?>
								<div class="ovabrw-qty-control">
									<div class="ovabrw-qty-input">
										<input
											type="number"
											name="<?php echo esc_attr( $key ).'_qty['.$value.']'; ?>"
											class="qty-input"
											value="1"
											min="1"
											max="<?php echo esc_attr( absint( $qtys[$k] ) ); ?>"
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
						</div>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ( $field['type'] === 'checkbox' ):
					$default 		= isset( $field['default'] ) ? $field['default'] : '';
					$checkbox_key 	= isset( $field['ova_checkbox_key'] ) ? $field['ova_checkbox_key'] : [];
					$checkbox_text 	= isset( $field['ova_checkbox_text'] ) ? $field['ova_checkbox_text'] : [];
					$checkbox_price = isset( $field['ova_checkbox_price'] ) ? $field['ova_checkbox_price'] : [];
					$checkbox_qty 	= isset( $field['ova_checkbox_qty'] ) ? $field['ova_checkbox_qty'] : [];

					if ( ! empty( $checkbox_key ) && is_array( $checkbox_key ) ):
						foreach ( $checkbox_key as $k => $val ):
							$checked = '';

							if ( ! $default && $field['required'] === 'on' ) $default = $val;

							if ( $default === $val ) $checked = 'checked';
				?>
							<div class="ovabrw-checkbox">
								<label class="ovabrw-label-field">
									<?php echo isset( $checkbox_text[$k] ) ? esc_html( $checkbox_text[$k] ) : ''; ?>
									<input 
										type="checkbox" 
										class="<?php echo esc_attr( $required ); ?>" 
										name="<?php echo esc_attr( $key ).'['.$val.']'; ?>" 
										value="<?php echo esc_attr( $val ); ?>" <?php echo esc_html( $checked ); ?>
									/>
									<span class="checkmark"></span>
								</label>
								<?php if ( isset( $checkbox_qty[$k] ) && absint( $checkbox_qty[$k] ) ): ?>
									<div class="ovabrw-qty-control">
										<div class="ovabrw-qty-input">
											<input
												type="number"
												name="<?php echo esc_attr( $key ).'_qty['.$val.']'; ?>"
												class="qty-input"
												value="1"
												min="1"
												max="<?php echo esc_attr( absint( $checkbox_qty[$k] ) ); ?>"
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
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ( $field['type'] === 'file' ):
					$mimes = apply_filters( 'ovabrw_ft_file_mimes', [
	                    'jpg'   => 'image/jpeg',
	                    'jpeg'  => 'image/pjpeg',
	                    'png'   => 'image/png',
	                    'pdf'   => 'application/pdf',
	                    'doc'   => 'application/msword',
	                ]);
				?>
					<div class="ovabrw-modern-file">
						<label class="ovabrw-label-field">
							<div class="ovabrw-file-name">
								<span class="placeholder"><?php esc_html_e( 'Choose File', 'ova-brw' ); ?></span>
								<span class="name"></span>
							</div>
							<input 
								type="<?php echo esc_attr( $field['type'] ); ?>" 
								name="<?php echo esc_attr( $key ); ?>" 
								class="<?php echo esc_attr( $field['class'] ) . $required; ?>" 
								data-max-file-size="<?php echo esc_attr( $field['max_file_size'] ); ?>" 
								data-file-mimes="<?php echo esc_attr( json_encode( $mimes ) ); ?>" 
								data-max-file-size-msg="<?php printf( esc_html__( 'Max file size: %sMB', 'ova-brw' ), $field['max_file_size'] ); ?>" 
								data-formats="<?php esc_attr_e( 'Supported formats: .jpg, .jpeg, .png, .pdf, .doc', 'ova-brw' ); ?>"
							/>
							<i aria-hidden="true" class="brwicon-upload"></i>
						</label>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
	<input 
		type="hidden"
		name="data_custom_ckf"
		data-ckf="<?php echo esc_attr( json_encode( $list_ckf ) ); ?>"
	/>
<?php endif; ?>