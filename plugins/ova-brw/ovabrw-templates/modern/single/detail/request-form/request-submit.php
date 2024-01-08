<?php if ( ! defined( 'ABSPATH' ) ) exit();
	if ( get_option( 'ova_brw_request_booking_form_show_service', 'yes' ) != 'yes' ) return;

	global $product;
	if ( ! $product ) return;

	$product_id 		= $product->get_id();
	$terms_conditions 	= get_option( 'ova_brw_request_booking_terms_conditions', '' );
	$terms_content 		= get_option( 'ova_brw_request_booking_terms_conditions_content', '' );
?>
<?php if ( $terms_conditions === 'yes' && $terms_content ): ?>
	<div class="terms-conditions">
		<label>
			<input
				type="checkbox"
				name="ovabrw-request_booking-terms-conditions"
				value="yes"
				data-error="<?php esc_attr_e( 'Please read and accept the terms and conditions to continue.', 'ova-brw' ); ?>"
			/>
			<span class="terms-conditions-content">
				<?php echo $terms_content; ?>
				<span class="terms-conditions-required">*</span>
			</span>
		</label>
	</div>
<?php endif; ?>
<div class="ovabrw-request-form-error"></div>
<?php if ( get_option( 'ova_brw_recapcha_enable', 'no' ) === 'yes' && ovabrw_get_recaptcha_form( 'request' ) ): ?>
    <div id="ovabrw-g-recaptcha-request"></div>
    <input
        type="hidden"
        id="ovabrw-recaptcha-request-token"
        value=""
        data-mess="<?php esc_attr_e( 'Requires reCAPTCHA', 'tripgo' ); ?>"
        data-error="<?php esc_attr_e( 'reCAPTCHA response error, please try again later', 'tripgo' ); ?>"
        data-expired="<?php esc_attr_e( 'reCAPTCHA response expires, you needs to re-verify', 'tripgo' ); ?>"
    />
<?php endif; ?>
<button type="submit" class="submit">
	<?php esc_html_e( 'Send', 'ova-brw' ); ?>
	<div class="ajax_loading">
		<div></div><div></div><div></div><div></div>
		<div></div><div></div><div></div><div></div>
		<div></div><div></div><div></div><div></div>
	</div>
</button>
<input type="hidden" name="product_name" value="<?php echo esc_attr( get_the_title() ); ?>" />
<input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>" />
<input type="hidden" name="request_booking" value="request_booking" />