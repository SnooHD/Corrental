<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;
	if ( ! $product ) return;

	$show_booking = get_option( 'ova_brw_template_show_booking_form', 'yes' );
	$show_request = get_option( 'ova_brw_template_show_request_booking', 'yes' );
?>
<?php if ( $show_booking === 'yes' || $show_request === 'yes' ): ?>
	<div class="ovabrw-product-form-tabs">
		<div class="ovabrw-tab-head">
			<?php if ( $show_booking === 'yes' && $show_request === 'yes' ): ?>
				<div class="item-tab modern-booking-tab active" data-id="modern-booking">
					<?php esc_html_e( 'Booking Form', 'ova-brw' ); ?>
				</div>
				<div class="item-tab modern-request-tab" data-id="modern-request">
					<?php esc_html_e( 'Request Booking', 'ova-brw' ); ?>
				</div>
			<?php elseif ( $show_booking === 'yes' && $show_request != 'yes' ): ?>
				<div class="item-tab modern-booking-tab active ovabrw-center">
					<?php esc_html_e( 'Booking Form', 'ova-brw' ); ?>
				</div>
			<?php elseif ( $show_booking != 'yes' && $show_request === 'yes' ): ?>
				<div class="item-tab modern-request-tab active ovabrw-center">
					<?php esc_html_e( 'Request Booking', 'ova-brw' ); ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="ovabrw-tab-content">
			<?php if ( $show_booking === 'yes' && $show_request === 'yes' ): ?>
				<div class="item-content active" id="modern-booking">
					<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-booking.php' ); ?>
				</div>
				<div class="item-content" id="modern-request">
					<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-request.php' ); ?>
				</div>
			<?php elseif ( $show_booking === 'yes' && $show_request != 'yes' ): ?>
				<div class="item-content active" id="modern-booking">
					<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-booking.php' ); ?>
				</div>
			<?php elseif ( $show_booking != 'yes' && $show_request === 'yes' ): ?>
				<div class="item-content active" id="modern-request">
					<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-request.php' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>