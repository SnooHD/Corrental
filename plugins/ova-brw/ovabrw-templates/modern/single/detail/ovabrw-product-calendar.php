<?php if ( ! defined( 'ABSPATH' ) ) exit();
	if ( get_option( 'ova_brw_template_show_calendar' ) != 'yes' ) return;

	global $product;

	if ( isset( $args['product_id'] ) && $args['product_id'] ) {
		$product = wc_get_product( $args['product_id'] );
	}

	if ( ! $product ) return;

	$product_id = $product->get_id();
	$background = get_option( 'ovabrw_glb_primary_color', '#e56e00' );

	// Disable week day
	$disable_week_day 			= get_option( 'ova_brw_calendar_disable_week_day', '' );
	$product_disable_week_day 	= get_post_meta( $product_id, 'ovabrw_product_disable_week_day', true );

	if ( $product_disable_week_day != '' ) {
		$disable_week_day = $product_disable_week_day;
	}

	$data_disable_week_day = $disable_week_day != '' ? json_encode( explode( ',', $disable_week_day ) ) : '';
	// End
	
	$booked 	= ovabrw_create_order_time_calendar( $product_id );
	$toolbar[] 	= ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_month', 'yes' ) ) == 'yes' ? 'dayGridMonth' : '';
	$toolbar[] 	= ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_week', 'yes' ) ) == 'yes' ? 'timeGridWeek' : '';
	$toolbar[] 	= ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_day', 'yes' ) ) == 'yes' ? 'timeGridDay' : '';
	$toolbar[] 	= ovabrw_get_setting( get_option( 'ova_brw_calendar_show_nav_list', 'yes' ) ) == 'yes' ? 'listWeek' : '';
	$show_time 	= ovabrw_get_setting( get_option( 'ova_brw_template_show_time_in_calendar', 'yes' ) ) == 'yes' ? '' : 'ova-hide-time-calendar';
	$nav 		= implode(',', array_filter( $toolbar ) );

	$language = ovabrw_get_setting( get_option( 'ova_brw_calendar_language_general', 'en' ) );

	if ( function_exists('pll_current_language') ) {
	    $language = pll_current_language();
	}

	$default_view = ovabrw_get_setting( get_option( 'ova_brw_calendar_default_view', 'month' ) );

	// Get first day in week
	$first_day 			= ovabrw_get_setting( get_option( 'ova_brw_calendar_first_day', '0' ) );
	$manage_store 		= get_post_meta( $product_id, 'ovabrw_manage_store', true );
	$stock_qty 			= absint( get_post_meta( $product_id, 'ovabrw_car_count', true ) );
	$number_limit 		= $manage_store === 'store' ? $stock_qty : 1;
	$define_day 		= defined_one_day( $product_id );
	$data_define_day 	= $define_day ? 'data-define_day='.$define_day : '';
	$default_hour_start = ovabrw_get_default_time( $product_id, 'start' );
	$time_to_book_start = ovabrw_time_to_book( $product_id, 'start' );
	$rental_type 		= get_post_meta( $product_id, 'ovabrw_price_type', true );
	$special_time 		= ovabrw_get_special_time( $product_id, $rental_type );
	$data_special_time 	= json_encode( $special_time );
?>

<div class="ovabrw-product-calendar">
	<h2 class="title"><?php esc_html_e( 'Calendar', 'ova-brw' ); ?></h2>
	<div class="wrap_calendar">
		<div <?php echo esc_attr( $data_define_day ) ?> 
			class="ovabrw__product_calendar <?php echo esc_attr( $show_time ); ?>" 
			data-number_limit="<?php echo esc_attr( $number_limit ); ?>" 
			data-lang="<?php echo esc_attr( $language ); ?>" 
			data-nav="<?php echo esc_attr( $nav ); ?>" 
			data-default_view="<?php echo esc_attr( $default_view ); ?>" 
			data-first-day="<?php echo esc_attr( $first_day ); ?>" 
			data-special-time='<?php echo esc_attr( $data_special_time ); ?>' 
			data-background-day='<?php echo esc_attr( $background ); ?>' 
			data-disable_week_day='<?php echo esc_attr( $data_disable_week_day ); ?>' 
			default_hour_start="<?php echo esc_attr( $default_hour_start ); ?>"  
			time_to_book_start="<?php echo esc_attr( $time_to_book_start ); ?>" 
			price_calendar='<?php echo esc_attr( $booked['price_calendar'] ); ?>' 
			type_price='<?php echo esc_attr( $rental_type ); ?>' 
			order_time='<?php echo esc_attr( $booked['order_time'] ); ?>'
			data_event_number="<?php echo apply_filters( 'ovabrw_event_number_cell', 2 ); ?>">
		</div>
	</div>
	<ul class="intruction_calendar">
		<li>
			<span class="ovabrw-box"></span>
			<span><?php esc_html_e( 'Available','ova-brw' ); ?></span>		
		</li>
		<li>
			<span class="ovabrw-box" style="background: <?php echo esc_attr( $background ); ?>; border-color: <?php echo esc_attr( $background ) ?>; "></span>
			<span><?php esc_html_e( 'Unavailable','ova-brw' ); ?></span>
		</li>
	</ul>
</div>