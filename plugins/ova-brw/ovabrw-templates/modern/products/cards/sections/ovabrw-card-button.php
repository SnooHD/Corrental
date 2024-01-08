<?php if ( ! defined( 'ABSPATH' ) ) exit();
	$card = ovabrw_get_card_template();
	if ( get_option( 'ovabrw_glb_'.$card.'_button' , 'yes' ) != 'yes' ) return;

	global $product;
	if ( ! $product ) return;

	$product_url = $product->get_permalink();
?>
<?php if ( $product_url ): ?>
	<a href="<?php echo esc_url( $product_url ); ?>" class="ovabrw-button">
		<?php esc_html_e( 'Book Now', 'ova-brw' ); ?>
		<i aria-hidden="true" class="brwicon-right"></i>
	</a>
<?php endif; ?>