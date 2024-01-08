<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;
	if ( ! $product ) return;

	$product_id = $product->get_id();

	// Get all images product
	$image_ids 		= ovabrw_get_products_gallery_ids( $product_id );
	$data_gallery 	= array();

	if ( isset( $args['data_options'] ) ) {
	    $data_options = $args['data_options'];
	} else {
	    $data_options   = apply_filters( 'ovabrw_ft_wc_slideshow_options', array(
	        'items'                 => 1,
	        'slideBy'               => 1,
	        'margin'                => 1,
	        'autoplayHoverPause'    => true,
	        'loop'                  => true,
	        'autoplay'              => false,
	        'autoplayTimeout'       => 3000,
	        'smartSpeed'            => 500,
	        'autoWidth'             => false,
	        'center'                => false,
	        'lazyLoad'              => true,
	        'dots'                  => true,
	        'nav'                   => true,
	        'rtl'                   => is_rtl() ? true: false,
	        'nav_left'              => 'brwicon-left',
	        'nav_right'             => 'brwicon-right-1',
	        'responsive' 			=> array(
	        	'0' => array(
	        		'items' 	=> 1,
	        		'nav' 		=> false,
	        		'slideBy' 	=> 1,
	        	),
	        	'769' => array(
	        		'items' 	=> 1,
	        		'nav' 		=> true,
	        		'slideBy' 	=> 1,
	        	),
	        ),
	    ));
	}

	$card = ovabrw_get_card_template();
	$thumbnail_type = get_option( 'ovabrw_glb_'.$card.'_thumbnail_type', 'slider' );

	if ( $thumbnail_type === 'slider' ) {
		$is_slide = true;
	} else {
		$is_slide = false;
	}

	if ( isset( $args['thumbnail_type'] ) ) {
		if ( $args['thumbnail_type'] === 'slider' ) $is_slide = true;
		if ( $args['thumbnail_type'] === 'image' ) $is_slide = false;
	}

?>

<?php if ( $image_ids && is_array( $image_ids ) ): ?>
    <div class="ovabrw-gallery-popup">
    	<?php if ( $is_slide ): ?>
	        <div class="ovabrw-gallery-slideshow owl-carousel owl-theme" data-options="<?php echo esc_attr( json_encode( $data_options ) ); ?>">
	            <?php foreach ( $image_ids as $k => $img_id ):
	                $gallery_url = wp_get_attachment_url( $img_id );
	                $gallery_alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

	                if ( ! $gallery_alt ) {
	                    $gallery_alt = get_the_title( $img_id );
	                }

	                array_push( $data_gallery , array(
	                    'src'       => $gallery_url,
	                    'caption'   => $gallery_alt,
	                    'thumb'     => $gallery_url,
	                ));
	            ?>
	                <div class="item">
	                    <a class="gallery-fancybox" data-index="<?php echo esc_attr( $k ); ?>" href="javascript:void(0)">
	                        <?php echo wp_get_attachment_image( $img_id, 'woocommerce_thumbnail' ); ?>
	                    </a>
	                </div>
	            <?php endforeach; ?>
	        </div>
	        <input
	        	type="hidden"
	        	class="ovabrw-data-gallery"
	        	data-gallery="<?php echo esc_attr( json_encode( $data_gallery ) ); ?>"
	        />
	    <?php else: ?>
	    	<div class="ovabrw-product-img-feature">
	    		<?php $img_feature = reset( $image_ids ); ?>
	    		<?php echo wp_get_attachment_image( $img_feature, 'woocommerce_thumbnail' ); ?>
	    	</div>
	    <?php endif; ?>
    </div>
<?php endif; ?>