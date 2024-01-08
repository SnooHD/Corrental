<?php if ( ! defined( 'ABSPATH' ) ) exit();
	$card = ovabrw_get_card_template();
	if ( get_option( 'ovabrw_glb_'.$card.'_price' , 'yes' ) != 'yes' ) return;

	global $product;
	if ( ! $product ) return;

	$product_id 	= $product->get_id();
	$rental_type 	= get_post_meta( $product_id, 'ovabrw_price_type', true );
?>
<?php if ( $rental_type === 'day' ):
	$price 	= get_post_meta( $product_id, '_regular_price', true );
	$unit 	= esc_html__( '/ Day', 'ova-brw' );
	$define = get_post_meta( $product_id, 'ovabrw_define_1_day', true );

	if ( $define === 'hotel' ) $unit = esc_html__( '/ Night', 'ova-brw' );
?>
	<div class="ovabrw-price">
		<span class="amount"><?php echo ovabrw_wc_price( $price, [], false ); ?></span>
		<span class="unit"><?php echo esc_html( $unit ); ?></span>
	</div>
<?php elseif ( $rental_type === 'hour' ):
	$price = get_post_meta( $product_id, 'ovabrw_regul_price_hour', true );
?>
	<div class="ovabrw-price">
		<span class="amount"><?php echo ovabrw_wc_price( $price ); ?></span>
		<span class="unit"><?php esc_html_e( '/ Hour', 'ova-brw' ); ?></span>
	</div>
<?php elseif ( $rental_type === 'mixed' ):
	$price = get_post_meta( $product_id, 'ovabrw_regul_price_hour', true );
?>
	<div class="ovabrw-price">
		<span class="unit"><?php esc_html_e( 'From', 'ova-brw' ); ?></span>
		<span class="amount"><?php echo ovabrw_wc_price( $price ); ?></span>
		<span class="unit"><?php esc_html_e( '/ Hour', 'ova-brw' ); ?></span>
	</div>
<?php elseif ( $rental_type === 'period_time' ):
	$min = $max = 0;
	$petime_price = get_post_meta( $product_id, 'ovabrw_petime_price', true );

	if ( ! empty( $petime_price ) && is_array( $petime_price ) ) {
	    $min = min( $petime_price );
	    $max = max( $petime_price );
	}
?>
	<div class="ovabrw-price">
		<?php if ( $min && $max && $min == $max ): ?>
	        <span class="unit"><?php esc_html_e( 'From', 'ova-brw' ); ?></span>
			<span class="amount"><?php echo ovabrw_wc_price( $min ); ?></span>
    	<?php elseif ( $min && $max ): ?>
    		<span class="amount"><?php echo ovabrw_wc_price( $min ); ?></span>
	        <span class="unit"><?php esc_html_e( '-', 'ova-brw' ); ?></span>
			<span class="amount"><?php echo ovabrw_wc_price( $max ); ?></span>
    	<?php else: ?>
	        <span class="amount">
				<a href="<?php echo esc_url( get_the_permalink( $product_id ) ); ?>">
					<?php esc_html_e( 'Option Price', 'ova-brw' ); ?>
				</a>
			</span>
    	<?php endif; ?>
	</div>
<?php elseif ( $rental_type === 'transportation' ):
	$min = $max = 0;
	$price_location = get_post_meta( $product_id, 'ovabrw_price_location', true );

	if ( ! empty( $price_location ) && is_array( $price_location ) ) {
	    $min = min( $price_location );
	    $max = max( $price_location );
	}
?>
	<div class="ovabrw-price">
		<?php if ( $min && $max && $min == $max ): ?>
	        <span class="unit"><?php esc_html_e( 'From', 'ova-brw' ); ?></span>
			<span class="amount"><?php echo ovabrw_wc_price( $min ); ?></span>
    	<?php elseif ( $min && $max ): ?>
    		<span class="amount"><?php echo ovabrw_wc_price( $min ); ?></span>
	        <span class="unit"><?php esc_html_e( '-', 'ova-brw' ); ?></span>
			<span class="amount"><?php echo ovabrw_wc_price( $max ); ?></span>
    	<?php else: ?>
	        <span class="amount">
				<a href="<?php echo esc_url( get_the_permalink( $product_id ) ); ?>">
					<?php esc_html_e( 'Option Price', 'ova-brw' ); ?>
				</a>
			</span>
    	<?php endif; ?>
	</div>
<?php elseif ( $rental_type === 'taxi' ):
	$price 		= get_post_meta( $product_id, 'ovabrw_regul_price_taxi', true );
	$price_by 	= get_post_meta( $product_id, 'ovabrw_map_price_by', true );

	if ( ! $price_by ) $price_by = 'km';
?>
	<div class="ovabrw-price">
		<span class="amount"><?php echo ovabrw_wc_price( $price ); ?></span>
		<?php if ( $price_by === 'km' ): ?>
			<span class="unit"><?php esc_html_e( '/ Km', 'ova-brw' ); ?></span>
		<?php else: ?>
			<span class="unit"><?php esc_html_e( '/ Mi', 'ova-brw' ); ?></span>
		<?php endif; ?>
	</div>
<?php else: ?>
	<div class="ovabrw-price">
		<span class="amount">
			<a href="<?php echo esc_url( get_the_permalink( $product_id ) ); ?>">
				<?php esc_html_e( 'Option Price', 'ova-brw' ); ?>
			</a>
		</span>
	</div>
<?php endif; ?>