<?php

// Location //////////////////////////////////////////////////////////////////////////////////
add_action( 'init', 'ovabrw_location',0 );
function ovabrw_location() {
    $labels = array(
        'name'               => esc_html__( 'Location', 'post type general name', 'ova-brw' ),
        'singular_name'      => esc_html__( 'Location', 'post type singular name', 'ova-brw' ),
        'menu_name'          => esc_html__( 'Location', 'admin menu', 'ova-brw' ),
        'name_admin_bar'     => esc_html__( 'Location', 'add new on admin bar', 'ova-brw' ),
        'add_new'            => esc_html__( 'Add New Location', 'Location', 'ova-brw' ),
        'add_new_item'       => esc_html__( 'Add New Location', 'ova-brw' ),
        'new_item'           => esc_html__( 'New Location', 'ova-brw' ),
        'edit_item'          => esc_html__( 'Edit Location', 'ova-brw' ),
        'view_item'          => esc_html__( 'View Location', 'ova-brw' ),
        'all_items'          => esc_html__( 'All Location', 'ova-brw' ),
        'search_items'       => esc_html__( 'Search Location', 'ova-brw' ),
        'parent_item_colon'  => esc_html__( 'Parent Location:', 'ova-brw' ),
        'not_found'          => esc_html__( 'No Location found.', 'ova-brw' ),
        'not_found_in_trash' => esc_html__( 'No Location found in Trash.', 'ova-brw' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'menu_icon'          => 'dashicons-format-gallery',
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'location' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'author', 'thumbnail', ),
    );

    register_post_type( 'location', $args );
}


// Vehicle
add_action( 'init', 'ovabrw_vehicle',0 );
function ovabrw_vehicle() {
    $labels = array(
        'name'               => esc_html__( 'Vehicle', 'post type general name', 'ova-brw' ),
        'singular_name'      => esc_html__( 'Vehicle', 'post type singular name', 'ova-brw' ),
        'menu_name'          => esc_html__( 'Manage Vehicle', 'ova-brw' ),
        'name_admin_bar'     => esc_html__( 'Vehicle', 'ova-brw' ),
        'add_new'            => esc_html__( 'Add New Vehicle', 'ova-brw' ),
        'add_new_item'       => esc_html__( 'Add New Vehicle', 'ova-brw' ),
        'new_item'           => esc_html__( 'New Vehicle', 'ova-brw' ),
        'edit_item'          => esc_html__( 'Edit Vehicle', 'ova-brw' ),
        'view_item'          => esc_html__( 'View Vehicle', 'ova-brw' ),
        'all_items'          => esc_html__( 'All Vehicle', 'ova-brw' ),
        'search_items'       => esc_html__( 'Search Vehicle', 'ova-brw' ),
        'parent_item_colon'  => esc_html__( 'Parent Vehicle:', 'ova-brw' ),
        'not_found'          => esc_html__( 'No Vehicle found.', 'ova-brw' ),
        'not_found_in_trash' => esc_html__( 'No Vehicle found in Trash.', 'ova-brw' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'menu_icon'          => 'dashicons-format-gallery',
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'vehicle' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'author' ),
    );

    register_post_type( 'vehicle', $args );
}

// Add Closed status in WooCommerce
add_action( 'init', 'register_wc_closed_order_statuses' );
function register_wc_closed_order_statuses() {
    register_post_status( 'wc-closed', array(
        'label'                     => _x( 'Closed', 'Order status', 'ova-brw' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Closed <span class="count">(%s)</span>', 'Closed<span class="count">(%s)</span>', 'ova-brw' )
    ) );
}