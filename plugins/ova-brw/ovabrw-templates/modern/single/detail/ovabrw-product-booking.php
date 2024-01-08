<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;

	if ( isset( $args['product_id'] ) && $args['product_id'] ) {
		$product = wc_get_product( $args['product_id'] );
	}
	
	if ( ! $product ) return;
?>
<form
	action=""
	method="POST"
	enctype="multipart/form-data"
	id="booking_form"
	class="ovabrw-form"
	data-mesg_required="<?php esc_html_e( 'This field is required.', 'ova-brw' ); ?>"
	data-run_ajax="<?php esc_attr_e( apply_filters( 'ovabrw_booking_form_run_ajax', 'false' ) ); ?>">
	<div class="ovabrw-product-fields">
		<?php ovabrw_get_template('modern/single/detail/booking-form/booking-fields.php'); ?>
		<?php ovabrw_get_template('modern/single/detail/booking-form/booking-custom-checkout-fields.php'); ?>
	</div>
	<?php ovabrw_get_template('modern/single/detail/booking-form/booking-resources.php'); ?>
	<?php ovabrw_get_template('modern/single/detail/booking-form/booking-services.php'); ?>
	<?php ovabrw_get_template('modern/single/detail/booking-form/booking-deposit.php'); ?>
	<?php ovabrw_get_template('modern/single/detail/booking-form/booking-show-total.php'); ?>
	<?php ovabrw_get_template('modern/single/detail/booking-form/booking-submit.php'); ?>
</form>