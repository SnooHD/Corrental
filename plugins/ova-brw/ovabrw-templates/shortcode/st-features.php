<?php if ( ! defined( 'ABSPATH' ) ) exit();
	if ( ovabrw_global_typography() ) {
		$args['class'] .= ' ovabrw-modern-product';
	}
?>

<div class="<?php echo esc_attr( $args['class'] ); ?>">
	<?php
		if ( ovabrw_global_typography() ) {
			ovabrw_get_template( 'modern/single/detail/ovabrw-product-features.php', [ 'product_id' => $args['id'] ] );
		} else {
			ovabrw_get_template( 'single/features.php', array( 'id' => $args['id'] ) );
		}
	?>	
</div>