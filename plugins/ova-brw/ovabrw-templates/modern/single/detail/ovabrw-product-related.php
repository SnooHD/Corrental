<?php if ( ! defined( 'ABSPATH' ) ) exit();

global $product;

if ( isset( $args['product_id'] ) && $args['product_id'] ) {
	$product = wc_get_product( $args['product_id'] );
}

if ( ! $product ) return;

$product_id = $product->get_id();
$categories = $product->get_category_ids();

$url_category = '';

if ( ! empty( $categories ) ) {
    $term_id = reset( $categories );

    if ( $term_id ) {
    	$url_category = get_term_link( $term_id );
    }
}

$args = array(
	'posts_per_page' => isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : 3,
	'columns'        => isset( $args['columns'] ) ? $args['columns'] : 3,
	'orderby'        => isset( $args['orderby'] ) ? $args['orderby'] : 'rand',
	'order'          => isset( $args['order'] ) ? $args['order'] : 'DESC',
);

// Get visible related products then sort them at random.
$args['related_products'] = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $args['posts_per_page'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );

// Handle orderby.
$related_products = wc_products_array_orderby( $args['related_products'], $args['orderby'], $args['order'] );

if ( $related_products ) : ?>
	<div class="ovabrw-related-products">
		<div class="head-related">
			<h2><?php esc_html_e( 'Related products', 'ova-brw' ); ?></h2>
			<?php if ( $url_category && apply_filters( 'ovabrw_ft_related_product_view_all', true ) ): ?>
				<a href="<?php echo esc_url( $url_category ); ?>"><?php esc_html_e( 'View All', 'ova-brw' ); ?></a>
			<?php endif; ?>
		</div>
		<?php
			if ( ovabrw_get_card_template() == 'card5' || ovabrw_get_card_template() == 'card6' ) { ?>
				<div class="related-products-list ovabrw-column-1">
			<?php } else { ?>
				<div class="related-products-list">
			<?php }
		?>
			<?php foreach ( $related_products as $related_product ):
				$post_object = get_post( $related_product->get_id() );
				setup_postdata( $GLOBALS['post'] =& $post_object );
				?><div class="related-item"><?php
					ovabrw_get_template( 'modern/products/cards/ovabrw-'.ovabrw_get_card_template().'.php' );
				?></div><?php
			endforeach; ?>
		</div>
	</div>
	<?php
endif;

wp_reset_postdata();
