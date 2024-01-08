<?php if ( ! defined( 'ABSPATH' ) ) exit();
	if ( ! $args['term_id'] ) return;

	$thumbnail_id 	= get_term_meta( $args['term_id'], 'thumbnail_id', true );
	$thumbnail_url 	= $args['image']['default'];
	$thumbnail_alt 	= '';

	if ( $thumbnail_id ) {
		$thumbnail_url 	= wp_get_attachment_url( $thumbnail_id );
		$thumbnail_alt 	= get_post_meta( $thumbnail_id , '_wp_attachment_image_alt', true );
	}

	if ( 'yes' === $args['custom_image'] ) {
		$thumbnail_url 	= $args['image']['url'];
		$thumbnail_alt 	= $args['image']['alt'];
	}

	$term_obj 	= get_term( $args['term_id'], 'product_cat' );
	$term_link 	= get_term_link( $args['term_id'] );

?>
<div class="ovabrw-el-product-category template2">
	<a href="<?php echo esc_url( $term_link ); ?>"<?php echo $args['target_link'] === 'yes' ? 'target="_blank"' : ''; ?>>
		<div class="content">
			<?php if ( 'yes' === $args['background_overlay'] ): ?>
				<div class="background-overlay"></div>
			<?php endif; ?>
			<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $thumbnail_alt ); ?>">
			<?php if ( 'yes' === $args['show_count'] ): ?>
				<?php if ( $term_obj->count === 1 ): ?>
					<span class="count"><?php printf( esc_html__( '%s Car', 'ova-brw' ), $term_obj->count ); ?></span>
				<?php else: ?>
					<span class="count"><?php printf( esc_html__( '%s Cars', 'ova-brw' ), $term_obj->count ); ?></span>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<div class="info">
			<?php if ( 'yes' === $args['show_name'] ): ?>
				<h2 class="name">
					<?php echo esc_html( $term_obj->name ); ?>
				</h2>
			<?php endif; ?>
			<?php if ( 'yes' === $args['show_review'] ):
				$review_average = ovabrw_get_average_product_review( $args['term_id'] );
			?>
				<span class="review-average">
					<i aria-hidden="true" class="brwicon-star-2"></i>
					<span class="average"><?php echo esc_html( $review_average ); ?></span>
				</span>
			<?php endif; ?>
		</div>
	</a>
</div>