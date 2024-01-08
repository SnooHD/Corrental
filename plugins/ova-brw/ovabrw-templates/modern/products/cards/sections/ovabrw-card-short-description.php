<?php if ( ! defined( 'ABSPATH' ) ) exit();
	$card = ovabrw_get_card_template();
	if ( get_option( 'ovabrw_glb_'.$card.'_short_description' , 'yes' ) != 'yes' ) return;

	global $product;
	if ( ! $product ) return;

	$short_description = $product->get_short_description();
?>
<?php if ( $short_description ): ?>
	<p class="ovabrw-short-description">
		<?php echo esc_html( wp_strip_all_tags( $short_description ) ); ?>
	</p>
<?php endif; ?>