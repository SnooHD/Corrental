<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;
	if ( ! $product ) return;

	$product_id 	= $product->get_id();
	$rental_type 	= get_post_meta( $product_id, 'ovabrw_price_type', true );
?>
<?php if ( $rental_type === 'day' ):
	ovabrw_get_template( 'modern/single/detail/special-time/special-by-day.php' );
endif; ?>
<?php if ( $rental_type === 'hour' ):
	ovabrw_get_template( 'modern/single/detail/special-time/special-by-hour.php' );
endif; ?>
<?php if ( $rental_type === 'mixed' ):
	ovabrw_get_template( 'modern/single/detail/special-time/special-by-mixed.php' );
endif; ?>
<?php if ( $rental_type === 'taxi' ):
	ovabrw_get_template( 'modern/single/detail/special-time/special-by-taxi.php' );
endif; ?>