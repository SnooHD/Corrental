<?php 
/**
 * The template for displaying price content within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/price.php
 *
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( isset( $args['id'] ) && $args['id'] ) {
    $pid = $args['id'];
} else {
    $pid = get_the_id();
}

// Check product type: rental
$product = wc_get_product( $pid );

// if the product type isn't ovabrw_car_rental
if ( !$product || $product->get_type() !== 'ovabrw_car_rental' ) return;

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

$pid = $product->get_id();

$price_type = get_post_meta( $pid, 'ovabrw_price_type', true ) ? get_post_meta( $pid, 'ovabrw_price_type', true ) : 'day' ;
$price_hour = get_post_meta( $pid, 'ovabrw_regul_price_hour', true );
$price_day  = get_post_meta( $pid, '_regular_price', true );
$price_taxi = get_post_meta( $pid, 'ovabrw_regul_price_taxi', true );

// Get price
$petime_price   = get_post_meta( $pid, 'ovabrw_petime_price', true );
$price_location = get_post_meta( $pid, 'ovabrw_price_location', true );

$min = $max = 0;
if ( $price_type == 'period_time' && $petime_price && is_array( $petime_price ) ) {
    $min = min( $petime_price );
    $max = max( $petime_price );
}

if ( $price_type == 'transportation' && $price_location && is_array( $price_location ) ) {
    $min = min( $price_location );
    $max = max( $price_location );
}

if ( $price_type === 'hour' ) { ?>
    <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'ovabrw-price price' ) );?>">
        <span class="amount"><?php echo ovabrw_wc_price( $price_hour) ; ?> </span>
        <span class="label"><?php esc_html_e( '/ Hour', 'ova-brw' ); ?></span>
    </p>
<?php } elseif ( $price_type === 'day' ) { ?>
    <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'ovabrw-price price' ) );?>">
        <span class="amount"><?php echo ovabrw_wc_price( $price_day, [], false ); ?></span>
        <span class="label"><?php esc_html_e( '/ Day', 'ova-brw' ); ?></span>
    </p>
<?php } elseif ( $price_type === 'mixed' ) { ?>
    <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'ovabrw-price price' ) );?>">
        <span class="ovabrw_woo_price">
            <span class="amount"><?php echo ovabrw_wc_price( $price_hour ); ?></span>
            <span class="label"><?php esc_html_e( '/ Hour', 'ova-brw' ); ?></span>
        </span>
        <span class="ovabrw_woo_price">
            <span class="amount"><?php echo ovabrw_wc_price( $price_day ); ?></span>
            <span class="label"><?php esc_html_e( '/ Day', 'ova-brw' ); ?></span>
        </span>
    </p>
<?php } elseif ( $price_type === 'period_time' || $price_type === 'transportation' ) { ?>
    <?php if ( $min && $max && $min == $max ): ?>
        <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'ovabrw-price price' ) );?>">
            <span class="amount"><?php echo ovabrw_wc_price( $min ); ?></span>
        </p>
    <?php elseif ( $min && $max ): ?>
        <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'ovabrw-price price' ) );?>">
            <span class="amount"><?php echo ovabrw_wc_price( $min ). ' - ' .ovabrw_wc_price( $max ); ?></span>
        </p>
    <?php else: ?>
        <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'ovabrw-price price' ) );?>">
            <?php esc_html_e( 'Option Price', 'ova-brw' ); ?>
        </p>
    <?php endif; ?>
<?php } elseif ( $price_type === 'taxi' ) { ?>
    <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'ovabrw-price price' ) );?>">
        <span class="amount"><?php echo ovabrw_wc_price( $price_taxi ); ?></span>
        <span class="label"><?php esc_html_e( '/ Km', 'ova-brw' ); ?></span>
    </p>
<?php } else { ?>
    <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'ovabrw-price price' ) );?>">
        <?php esc_html_e( 'Option Price', 'ova-brw' ); ?>
    </p>
<?php }