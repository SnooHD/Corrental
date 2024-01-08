<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;

	if ( isset( $args['product_id'] ) && $args['product_id'] ) {
		$product = wc_get_product( $args['product_id'] );
	}
	
	if ( ! $product ) return;
?>

<form
	class="form ovabrw-form"
	id="request_booking"
	action="<?php echo home_url('/'); ?>"
	method="post"
	enctype="multipart/form-data"
	data-mesg_required="<?php esc_html_e( 'This field is required.', 'ova-brw' ); ?>">
	<div class="ovabrw-product-fields">
		<?php ovabrw_get_template('modern/single/detail/request-form/request-fields.php'); ?>
		<?php ovabrw_get_template('modern/single/detail/request-form/request-custom-checkout-fields.php'); ?>
	</div>
	<?php ovabrw_get_template('modern/single/detail/request-form/request-resources.php'); ?>
	<?php ovabrw_get_template('modern/single/detail/request-form/request-services.php'); ?>
	<?php if ( get_option( 'ova_brw_request_booking_form_show_extra_info', 'yes' ) === 'yes' ): ?>
		<div class="ovabrw-request-extra">
			<label><?php esc_html_e( 'Extra Information', 'ova-brw' ); ?></label>
			<textarea name="extra"></textarea>
		</div>
	<?php endif; ?>
	<?php ovabrw_get_template('modern/single/detail/request-form/request-submit.php'); ?>
</form>