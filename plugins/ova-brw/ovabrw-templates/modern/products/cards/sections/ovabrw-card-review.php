<?php if ( ! defined( 'ABSPATH' ) ) exit();
    $card = ovabrw_get_card_template();
    if ( get_option( 'ovabrw_glb_'.$card.'_review' , 'yes' ) != 'yes' ) return;

	global $product;
    if ( ! $product ) return;

	$product_url 	= $product->get_permalink();
	$review_count   = $product->get_review_count();
	$rating         = $product->get_average_rating();
?>
<?php if ( wc_review_ratings_enabled() && $rating > 0 ): ?>
    <div class="ovabrw-review">
        <div class="ovabrw-star-rating" role="img" aria-label="<?php echo sprintf( __( 'Rated %s out of 5', 'ova-brw' ), $rating ); ?>">
        	<i aria-hidden="true" class="brwicon-star-3"></i>
        	<i aria-hidden="true" class="brwicon-star-3"></i>
        	<i aria-hidden="true" class="brwicon-star-3"></i>
        	<i aria-hidden="true" class="brwicon-star-3"></i>
        	<i aria-hidden="true" class="brwicon-star-3"></i>
            <span class="ovabrw-rating-percent" style="width: <?php echo esc_attr( ( $rating / 5 ) * 100 ).'%'; ?>;">
            	<i aria-hidden="true" class="brwicon-star-3"></i>
            	<i aria-hidden="true" class="brwicon-star-3"></i>
            	<i aria-hidden="true" class="brwicon-star-3"></i>
            	<i aria-hidden="true" class="brwicon-star-3"></i>
            	<i aria-hidden="true" class="brwicon-star-3"></i>
            </span>
        </div>
        <a href="<?php echo esc_url( $product_url.'#reviews' ); ?>" class="ovabrw-review-link" rel="nofollow">
            <?php printf( _n( '%s review', '%s reviews', $review_count, 'ova-brw' ), esc_html( $review_count ) ); ?>
        </a>
    </div>
<?php endif; ?>