<?php
/**
 * The template for displaying extra fields in booking form within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/booking-form/extra_fields.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

// Get product_id from do_action - use when insert shortcode
if ( isset( $args['product_id'] ) && $args['product_id'] ){
	$pid = $args['product_id'];
} else {
	$pid = get_the_id();
}

$list_ckf_output 	= ovabrw_get_list_field_checkout( $pid );
$special_fields 	= [ 'textarea', 'select', 'radio', 'checkbox', 'file' ];

if ( ! empty( $list_ckf_output ) && is_array( $list_ckf_output ) ) {
	foreach ( $list_ckf_output as $key => $field ) {
		if ( array_key_exists( 'enabled', $field ) && $field['enabled'] == 'on' ) {
			if ( array_key_exists('required', $field) && $field['required'] == 'on' ) {
				$class_required = ' required';
			} else {
				$class_required = '';
			}
	?>
		<div class="rental_item">
			<label><?php echo esc_html( $field['label'] ); ?></label>
			<div class="error_item">
				<label><?php esc_html_e( 'This field is required', 'ova-brw' ); ?></label>
			</div>
			<?php if ( ! in_array( $field['type'], $special_fields ) ) { ?>
				<input 
					type="<?php echo esc_attr( $field['type'] ); ?>" 
					name="<?php echo esc_attr( $key ); ?>" 
					class="<?php echo esc_attr( $field['class'] ) . $class_required; ?>" 
					placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
					value="<?php echo esc_attr( $field['default'] ); ?>" />
			<?php } ?>

			<?php if ( $field['type'] === 'textarea' ) { ?>
				<textarea name="<?php echo esc_attr( $key ) ;?>" class="<?php echo esc_attr( $field['class'] ) . $class_required; ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" value="<?php echo $field['default']; ?>" cols="10" rows="5"></textarea>
			<?php } ?>

			<?php if ( $field['type'] === 'select' ) { 
				$ova_options_key = $ova_options_text = [];

				if ( array_key_exists( 'ova_options_key', $field ) ) {
					$ova_options_key = $field['ova_options_key'];
				}

				if ( array_key_exists( 'ova_options_text', $field ) ) {
					$ova_options_text = $field['ova_options_text'];
				}
			?>
				<select name="<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $field['class'] ) . $class_required; ?>">
				<?php 
					if ( ! empty( $ova_options_text ) && is_array( $ova_options_text ) ) { 
						if ( $field['required'] != 'on' ): ?>
							<option value="">
								<?php printf( esc_html__( 'Select %s', 'ova_brw' ), $field['label'] ); ?>
							</option>
						<?php
						endif;
						foreach ( $ova_options_text as $k => $value ) { 
							?>
							<option value="<?php echo esc_attr( $ova_options_key[$k] ); ?>"<?php selected( $field['default'], $ova_options_key[$k] ); ?>>
								<?php echo esc_html( $value ); ?>
							</option>
				<?php 	} //end foreach 
					}//end if
				?>
				</select>
			<?php } ?>
			<?php if ( $field['type'] === 'radio' ):
				$values 	= isset( $field['ova_values'] ) ? $field['ova_values'] : '';
				$default 	= isset( $field['default'] ) ? $field['default'] : '';

				if ( ! empty( $values ) && is_array( $values ) ):
					foreach ( $values as $k => $value ):
						$checked = '';

						if ( ! $default && $field['required'] === 'on' ) $default = $values[0];

						if ( $default === $value ) $checked = 'checked';
			?>			
					<div class="ovabrw-radio">
						<input 
							type="radio" 
							id="<?php echo 'ovabrw-radio'.esc_attr( $k ); ?>" 
							name="<?php echo esc_attr( $key ); ?>" 
							value="<?php echo esc_attr( $value ); ?>" <?php echo esc_html( $checked ); ?>/>
						<label for="<?php echo 'ovabrw-radio'.esc_attr( $k ); ?>"><?php echo esc_html( $value ); ?></label>
					</div>
			<?php endforeach; endif; endif; ?>
			<?php if ( $field['type'] === 'checkbox' ):
				$default 		= isset( $field['default'] ) ? $field['default'] : '';
				$checkbox_key 	= isset( $field['ova_checkbox_key'] ) ? $field['ova_checkbox_key'] : [];
				$checkbox_text 	= isset( $field['ova_checkbox_text'] ) ? $field['ova_checkbox_text'] : [];
				$checkbox_price = isset( $field['ova_checkbox_price'] ) ? $field['ova_checkbox_price'] : [];

				if ( ! empty( $checkbox_key ) && is_array( $checkbox_key ) ):
					foreach ( $checkbox_key as $k => $val ):
						$checked = '';

						if ( ! $default && $field['required'] === 'on' ) $default = $val;

						if ( $default === $val ) $checked = 'checked';
			?>
				<div class="ovabrw-checkbox">
					<input 
						type="checkbox" 
						id="<?php echo 'ovabrw-checkbox-'.esc_attr( $val ); ?>" 
						class="<?php echo esc_attr( $class_required ); ?>" 
						name="<?php echo esc_attr( $key ).'['.$val.']'; ?>" 
						value="<?php echo esc_attr( $val ); ?>" <?php echo esc_html( $checked ); ?>/>
					<label for="<?php echo 'ovabrw-checkbox-'.esc_attr( $val ); ?>">
						<?php echo isset( $checkbox_text[$k] ) ? esc_html( $checkbox_text[$k] ) : ''; ?>
					</label>
				</div>
			<?php endforeach; endif; endif; ?>
			<?php if ( $field['type'] === 'file' ):
				$mimes = apply_filters( 'ovabrw_ft_file_mimes', [
                    'jpg'   => 'image/jpeg',
                    'jpeg'  => 'image/pjpeg',
                    'png'   => 'image/png',
                    'pdf'   => 'application/pdf',
                    'doc'   => 'application/msword',
                ]);
			?>
				<div class="ovabrw-file">
					<label for="<?php echo 'ovabrw-file-'.esc_attr( $key ); ?>">
						<span class="ovabrw-file-chosen"><?php esc_html_e( 'Choose File', 'ova-brw' ); ?></span>
						<span class="ovabrw-file-name"></span>
					</label>
					<input 
						type="<?php echo esc_attr( $field['type'] ); ?>" 
						id="<?php echo 'ovabrw-file-'.esc_attr( $key ); ?>" 
						name="<?php echo esc_attr( $key ); ?>" 
						class="<?php echo esc_attr( $field['class'] ) . $class_required; ?>" 
						data-max-file-size="<?php echo esc_attr( $field['max_file_size'] ); ?>" 
						data-file-mimes="<?php echo esc_attr( json_encode( $mimes ) ); ?>" 
						data-max-file-size-msg="<?php printf( esc_html__( 'Max file size: %sMB', 'ova-brw' ), $field['max_file_size'] ); ?>" 
						data-formats="<?php esc_attr_e( 'Supported formats: .jpg, .jpeg, .png, .pdf, .doc', 'ova-brw' ); ?>"/>
				</div>
			<?php endif; ?>
		</div>
	<?php
		}//endif
	}//end foreach
	?>
	<input 
		type="hidden" 
		name="data_custom_ckf" 
		data-ckf="<?php echo esc_attr( json_encode( $list_ckf_output ) ); ?>" />
	<?php
}//end if