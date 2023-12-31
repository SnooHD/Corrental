<?php if ( ! defined( 'ABSPATH' ) ) exit();
	if ( empty( $args['categories'] ) ) return;

	// Get products
	$products = ovabrw_get_product_ajax_filter( array(
		'paged' 			=> 1,
		'posts_per_page' 	=> $args['posts_per_page'],
		'orderby' 			=> $args['orderby'],
		'order' 			=> $args['order'],
		'term_id' 			=> reset( $args['categories'] ),
	));
?>

<div class="ovabrw-product-ajax-filter">
	<?php if ( 'yes' === $args['category_filter'] ): ?>
		<ul class="categories-filter">
			<?php foreach ( $args['categories'] as $k => $term_id ):
				$term_name = esc_html__( 'All', 'ova-brw' );

				if ( $term_id ) {
					$term_obj 	= get_term( $term_id, 'product_cat' );
					if($term_obj) {
						$term_name 	= $term_obj->name;
					}
				}
			?>
				<li
					class="item-term<?php echo $k == 0 ? ' active' : ''; ?>"
					data-term-id="<?php echo $term_id ? esc_attr( $term_id ) : '0'; ?>">
					<?php echo esc_html( $term_name ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<div class="ovabrw-result">
		<ul class="products ovabrw-product-list<?php echo in_array( $args['template'] , ['card5', 'card6'] ) ? ' ovabrw-column1' : ''; ?>">
			<?php
				if ( $products->have_posts() ) : while ( $products->have_posts() ) : $products->the_post();
					if ( $args['template'] ) {
						$thumbnail_type = get_option( 'ovabrw_glb_'.$args['template'].'_thumbnail_type', 'slider' );
						?>
						<li class="item">
							<?php ovabrw_get_template( 'modern/products/cards/ovabrw-'.$args['template'].'.php', [ 'thumbnail_type' => $thumbnail_type ] ); ?>
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
		<span class="ovabrw-loader"></span>
		<input
			type="hidden"
			name="ovabrw-data-ajax-filter"
			data-template="<?php echo esc_attr( $args['template'] ); ?>"
			data-posts-per-page="<?php echo esc_attr( $args['posts_per_page'] ); ?>"
			data-orderby="<?php echo esc_attr( $args['orderby'] ); ?>"
			data-order="<?php echo esc_attr( $args['order'] ); ?>"
			data-pagination="<?php echo esc_attr( $args['pagination'] ); ?>"
		/>
	</div>
	<?php if ( 'yes' === $args['pagination'] ): ?>
		<?php
			$pages 		= $products->max_num_pages;
			$limit 		= $products->query_vars['posts_per_page'];
			$current 	= 1;

			if ( $pages > 1 ):
		?>
				<ul class="ovabrw-pagination">
					<?php for ( $i = 1; $i <= $pages; $i++ ): ?>
						<li>
							<span
								class="page-numbers<?php echo $i == $current ? ' current' : ''; ?>"
								data-paged="<?php echo esc_attr( $i ); ?>">
								<?php echo esc_html( $i ); ?>
							</span>
						</li>
					<?php endfor; ?>
				</ul>
		<?php endif; ?>
	<?php endif; ?>
</div>