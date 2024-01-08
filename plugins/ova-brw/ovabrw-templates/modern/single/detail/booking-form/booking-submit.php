<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;
	if ( ! $product ) return;

	$product_id = $product->get_id();

	$rental_type 		= get_post_meta( $product_id, 'ovabrw_price_type', true );
	$defined_one_day 	= defined_one_day( $product_id );
	$terms_conditions 	= get_option( 'ova_brw_booking_form_terms_conditions', '' );
	$terms_content 		= get_option( 'ova_brw_booking_form_terms_conditions_content', '' );
?>
<?php if ( $terms_conditions === 'yes' && $terms_content ): ?>
	<div class="terms-conditions">
		<label>
			<input
				type="checkbox"
				name="ovabrw-booking-terms-conditions"
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
<?php if ( get_option( 'ova_brw_recapcha_enable', 'no' ) === 'yes' && ovabrw_get_recaptcha_form( 'booking' ) ): ?>
    <div id="ovabrw-g-recaptcha-booking"></div>
    <input
        type="hidden"
        id="ovabrw-recaptcha-booking-token"
        value=""
        data-mess="<?php esc_attr_e( 'Requires reCAPTCHA', 'tripgo' ); ?>"
        data-error="<?php esc_attr_e( 'reCAPTCHA response error, please try again later', 'tripgo' ); ?>"
        data-expired="<?php esc_attr_e( 'reCAPTCHA response expires, you needs to re-verify', 'tripgo' ); ?>"
    />
<?php endif; ?>
<button type="submit" class="submit">
	<?php esc_html_e( 'Booking', 'ova-brw' ); ?>
	<div class="ajax_loading">
		<div></div><div></div><div></div><div></div>
		<div></div><div></div><div></div><div></div>
		<div></div><div></div><div></div><div></div>
	</div>
</button>
<input type="hidden" name="ovabrw_rental_type" value="<?php echo esc_attr( $rental_type ); ?>" />
<input type="hidden" name="car_id" value="<?php echo esc_attr( $product_id ); ?>" />
<input type="hidden" name="defined_one_day" value="<?php echo esc_attr( $defined_one_day ); ?>" />
<input type="hidden" name="custom_product_type" value="ovabrw_car_rental" />
<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product_id ); ?>" />
<input type="hidden" name="quantity" value="1" />