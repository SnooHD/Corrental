<?php

// Rent Day max
woocommerce_wp_text_input( array(
    'id'            => 'ovabrw_rent_day_max',
    'class'         => 'short ',
    'label'         => esc_html__( 'Max Rental Dates', 'ova-brw' ),
    'placeholder'   => esc_html__( '1', 'ova-brw' ),
    'desc_tip'      => 'true',
    'type'          => 'text',
));

// Rent Hour max
woocommerce_wp_text_input( array(
    'id'            => 'ovabrw_rent_hour_max',
    'class'         => 'short ',
    'label'         => esc_html__( 'Max Rental Hours', 'ova-brw' ),
    'placeholder'   => esc_html__( '1', 'ova-brw' ),
    'desc_tip'      => 'true',
    'type'          => 'text',
));

// Rent Day min
woocommerce_wp_text_input( array(
    'id'            => 'ovabrw_rent_day_min',
    'class'         => 'short ',
    'label'         => esc_html__( 'Min Rental Dates', 'ova-brw' ),
    'placeholder'   => esc_html__( '1', 'ova-brw' ),
    'desc_tip'      => 'true',
    'type'          => 'text',
));

// Rent Hour min
woocommerce_wp_text_input( array(
    'id'            => 'ovabrw_rent_hour_min',
    'class'         => 'short ',
    'label'         => esc_html__( 'Min Rental Hours', 'ova-brw' ),
    'placeholder'   => esc_html__( '1', 'ova-brw' ),
    'desc_tip'      => 'true',
    'type'          => 'text',
));