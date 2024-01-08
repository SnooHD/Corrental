<?php if ( ! defined( 'ABSPATH' ) ) exit();
	// Get product_id from do_action - use when insert shortcode
	if ( isset( $args['id'] ) && $args['id'] ) {
		$pid = $args['id'];
	} else {
		$pid = get_the_id();
	}

	$product 	= wc_get_product( $pid );
	$template 	= ovabrw_get_product_template( $pid );

get_header( 'shop' ); ?>
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>

		<?php 
			// if the product type isn't ovabrw_car_rental
			if( ! $product || $product->get_type() !== 'ovabrw_car_rental' ){

				wc_get_template_part( 'content', 'single-product' );

			}else{  
				do_action( 'woocommerce_before_single_product' );
				echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template );
				do_action( 'woocommerce_after_single_product' );

			}

	endwhile; // end of the loop. ?>
<?php
get_footer( 'shop' );