<?php
/**
 * The template for displaying booking form content within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/booking_form.php
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit();

// Get product_id from do_action - use when insert shortcode
if ( isset( $args['id'] ) && $args['id'] ) {
	$pid = $args['id'];
} else {
	$pid = get_the_id();
}

// Check product type: rental
$product = wc_get_product( $pid );
if ( !$product || !$product->is_type('ovabrw_car_rental') ) return;

$ovabrw_rental_type = get_post_meta( $pid, 'ovabrw_price_type', true );
$defined_one_day 	= defined_one_day( $pid );
$run_ajax 			= apply_filters( 'ovabrw_booking_form_run_ajax', 'false' );
$terms_conditions 	= get_option( 'ova_brw_booking_form_terms_conditions', '' );
$terms_content 		= get_option( 'ova_brw_booking_form_terms_conditions_content', '' );
?>
<div class="ovabrw_booking_form" id="ovabrw_booking_form">
	<h3 class="title"><?php esc_html_e( 'Booking Form', 'ova-brw' ); ?></h3>
	<form class="form ovabrw-form" id="booking_form" action="<?php home_url('/'); ?>" method="post" enctype="multipart/form-data" data-mesg_required="<?php esc_html_e( 'This field is required.', 'ova-brw' ); ?>" data-run_ajax="<?php esc_attr_e( $run_ajax ); ?>">
		<div class="ovabrw-container wrap_fields">
			<div class="ovabrw-row">
				<div class="wrap-item two_column">
					<!-- Display Booking Form -->
					<?php
					/**
					 * Hook: ovabrw_booking_form
					 * @hooked: ovabrw_booking_form_fields - 5
					 * @hooked: ovabrw_booking_form_extra_fields - 10
					 * @hooked: ovabrw_booking_form_resource - 15
					 * @hooked: ovabrw_booking_form_services - 20
					 * @hooked: ovabrw_booking_form_deposit - 25
					 * @hooked: ovabrw_booking_form_ajax_total - 30
					 */
						do_action( 'ovabrw_booking_form', $pid  );
					?>
				</div>
			</div>
		</div>
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
		<button type="submit" class="submit btn_tran">
			<?php esc_html_e( 'Booking', 'ova-brw' ); ?>
		</button>
		<input type="hidden" name="ovabrw_rental_type" value="<?php echo esc_attr($ovabrw_rental_type); ?>" />
		<input type="hidden" name="car_id" value="<?php echo $pid; ?>" />
		<input type="hidden" name="defined_one_day" value="<?php echo esc_attr($defined_one_day); ?>" />
		<input type="hidden" name="custom_product_type" value="ovabrw_car_rental" />
		<input type="hidden" name="add-to-cart" value="<?php echo $pid; ?>" />
		<input type="hidden" name="quantity" value="1" />
	</form>
</div>



