<?php if ( ! defined( 'ABSPATH' ) ) exit();
	extract( $args );

	if ( ovabrw_global_typography() ) {
		$class .= ' ovabrw-modern-product';
	} else {
		$card = '';
	}

	if ( $categories ) $categories = explode( ',', $categories );

	if ( in_array( $card , ['card5', 'card6'] ) ) $column = 1;

	$args_query = array(
		'post_type'      	=> 'product',
		'post_status'    	=> 'publish',
		'posts_per_page' 	=> $posts_per_page,
		'order' 			=> $order,
		'orderby' 			=> $orderby,
		'tax_query' => array(
			'relation'  => 'AND',
	        array(
	            'taxonomy' => 'product_type',
	            'field'    => 'slug',
	            'terms'    => 'ovabrw_car_rental'
	        )
	    ),
	);

	if ( $categories ) {
		$args_query['tax_query'][] = array(
	        'taxonomy' => 'product_cat',
	        'field'    => 'term_id',
	        'terms'    => $categories
	    );
	}

	$products = new WP_Query( $args_query );
?>
<div class="ovabrw-list-product <?php echo esc_attr( $class ); ?>">
	<ul class="products ovabrw-column<?php echo esc_attr( $column ); ?>">
		<?php
			if ( $products->have_posts() ) : while ( $products->have_posts() ) : $products->the_post();
				if ( $card ) {
					$thumbnail_type = get_option( 'ovabrw_glb_'.$card.'_thumbnail_type', 'slider' );
					?>
					<li class="item">
						<?php ovabrw_get_template( 'modern/products/cards/ovabrw-'.$card.'.php', [ 'thumbnail_type' => $thumbnail_type ] ); ?>
					</li>
					<?php
				} else {
					wc_get_template_part( 'content', 'product' );
				}
			endwhile; else :
			?>
				<div class="not-found">
					<?php esc_html_e( 'Product not found', 'ova-brw' ); ?>
				</div>
			<?php
			endif; wp_reset_postdata();
		?>
	</ul>
</div>