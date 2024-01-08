<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;

	if ( isset( $args['product_id'] ) && $args['product_id'] ) {
		$product = wc_get_product( $args['product_id'] );
	}
	
	if ( ! $product ) return;

	$img_url 	= $img_alt = '';
	$img_id 	= $product->get_image_id();
	$data_gallery = array();

	if ( $img_id ) {
		$img_url = wp_get_attachment_url( $img_id );
		$img_alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

		if ( ! $img_alt ) {
			$img_alt = get_the_title( $img_id );
		}
	}

	$gallery_ids = $product->get_gallery_image_ids();

	// Carousel option
	$carousel_options = apply_filters( 'ovabrw_ft_glb_carousel_options', array(
		'items' 				=> 3,
		'slideBy' 				=> 1,
		'margin' 				=> 25,
		'autoplayHoverPause' 	=> true,
		'loop' 					=> true,
		'autoplay' 				=> true,
		'autoplayTimeout' 		=> 3000,
		'smartSpeed' 			=> 500,
		'rtl' 					=> is_rtl() ? true: false,
	));
?>
<?php if ( $img_url ): 
	array_push( $data_gallery , array(
		'src' 		=> $img_url,
		'caption' 	=> $img_alt,
		'thumb' 	=> $img_url,
	));
?>
	<div class="ovabrw-product-images">
		<div class="featured-img">
			<a class="gallery-fancybox" data-index='0' href="javascript:;">
					<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
				</a>
		</div>
		<?php if ( !empty( $gallery_ids ) && is_array( $gallery_ids ) ): ?>
		<div class="gallery">
			<div class="product-gallery owl-carousel owl-theme" 
				data-options="<?php echo esc_attr( json_encode( $carousel_options ) ); ?>">
				<?php foreach( $gallery_ids as $k => $gallery_id ): 
					$gallery_url = wp_get_attachment_url( $gallery_id );
					$gallery_alt = get_post_meta( $gallery_id, '_wp_attachment_image_alt', true );

					if ( !$gallery_alt ) {
						$gallery_alt = get_the_title( $gallery_id );
					}

					array_push( $data_gallery , array(
						'src' 		=> $gallery_url,
						'caption' 	=> $gallery_alt,
						'thumb' 	=> $gallery_url,
					));
				?>
					<div class="gallery-item">
						<a class="gallery-fancybox" data-index="<?php esc_attr_e( $k+1 ); ?>" href="javascript:;">
		  					<img src="<?php echo esc_url( $gallery_url ); ?>" alt="<?php echo esc_attr( $gallery_alt ); ?>">
		  				</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
		<div class="data-gallery" data-gallery="<?php echo esc_attr( json_encode( $data_gallery ) ); ?>"></div>
	</div>
<?php endif;