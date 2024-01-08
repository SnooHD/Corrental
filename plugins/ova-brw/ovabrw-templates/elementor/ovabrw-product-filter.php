<?php if ( ! defined( 'ABSPATH' ) ) exit();

	// Get products
	$products = ovabrw_get_product_filter( array(
		'posts_per_page' 	=> $args['posts_per_page'],
		'orderby' 			=> $args['orderby'],
		'order' 			=> $args['order'],
		'categories' 		=> $args['categories'],
	));

	$slide_options = $args['slide_options'];
?>
<?php if ( $products->have_posts() ): ?>
	<div class="ovabrw-product-filter">
		<div class="ovabrw-product-filter-slide owl-carousel owl-theme" data-options="<?php echo esc_attr( json_encode( $slide_options ) ); ?>">
			<?php while ( $products->have_posts() ):
				$products->the_post();
			?>
				<div class="item">
					<?php ovabrw_get_template( 'modern/products/cards/ovabrw-'.$args['template'].'.php', [ 'thumbnail_type' => 'image' ] ); ?>
				</div>
			<?php endwhile; ?>
		</div>
	</div>
<?php else: ?>
	<div class="not-found">
		<?php esc_html_e( 'Product not found', 'ova-brw' ); ?>
	</div>
<?php endif; wp_reset_postdata(); ?>