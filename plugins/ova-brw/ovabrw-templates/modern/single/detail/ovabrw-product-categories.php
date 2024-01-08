<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;

	if ( isset( $args['product_id'] ) && $args['product_id'] ) {
		$product = wc_get_product( $args['product_id'] );
	}

	if ( ! $product ) return;

	$categories = $product->get_category_ids();
?>
<?php if ( ! empty( $categories ) && is_array( $categories ) ): ?>
	<div class="ovabrw-product-categories">
		<span class="label"><?php esc_html_e( 'Categories:', 'ova-brw' ); ?></span>
		<?php foreach ( $categories as $k => $term_id ):
			$term = get_term( $term_id );

			if ( $term && is_object( $term ) ):
				$term_name = $term->name;
				$term_link = get_term_link( $term_id );
		?>
			<span class="name">
				<a href="<?php echo esc_url( $term_link ); ?>">
					<?php echo ( $k + 1 ) < count( $categories ) ? esc_html( $term_name ).', ' : esc_html( $term_name ); ?>
				</a>
			</span>
		<?php endif; endforeach; ?>
	</div>
<?php endif; ?>