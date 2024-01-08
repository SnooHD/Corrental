<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/search.php
 *
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

?>
<header class="woocommerce-products-header">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="woocommerce-products-header__title page-title">
			<?php esc_html_e( 'Search Result', 'ova-brw' ); ?>
		</h1>
	<?php endif; ?>

	<?php
	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );
	?>
</header>

<?php


$products 	= ovabrw_search_vehicle( $_GET );
$card 		= ovabrw_get_card_template();
$column 	= apply_filters( 'ovabrw_ft_search_result_column' , 3 );

if ( in_array( $card , ['card5', 'card6'] ) ) $column = 1;

if ( $products != false ) { ?>
	<div class="ovabrw-list-product">
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
	<nav class="woocommerce-pagination">
	<?php 
		$big = 999999999; // need an unlikely integer

		$args = array(
			'base'               	=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'             	=> '?paged=%#%',
			'current' 				=> max( 1, get_query_var('paged') ),
			'total' 				=> $products->max_num_pages,
			'show_all'           	=> false,
			'end_size'           	=> 1,
			'mid_size'           	=> 2,
			'prev_next'          	=> true,
			'prev_text'          	=> '<i class="arrow_carrot-left"></i>',
			'next_text'          	=> '<i class="arrow_carrot-right"></i>',
			'type'               	=> 'list',
			'add_args'           	=> false,
			'add_fragment'       	=> '',
			'before_page_number' 	=> '',
			'after_page_number'  	=> ''
		);

		echo paginate_links( $args );
	?>
	</nav>

	<?php
} else {
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
}

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
