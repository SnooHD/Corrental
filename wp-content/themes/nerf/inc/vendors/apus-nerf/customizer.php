<?php

function nerf_apartment_customize_register( $wp_customize ) {
    global $wp_registered_sidebars;
    $sidebars = array();

    if ( is_admin() && !empty($wp_registered_sidebars) ) {
        foreach ($wp_registered_sidebars as $sidebar) {
            $sidebars[$sidebar['id']] = $sidebar['name'];
        }
    }
    $columns = array( '1' => esc_html__('1 Column', 'nerf'),
        '2' => esc_html__('2 Columns', 'nerf'),
        '3' => esc_html__('3 Columns', 'nerf'),
        '4' => esc_html__('4 Columns', 'nerf'),
        '5' => esc_html__('5 Columns', 'nerf'),
        '6' => esc_html__('6 Columns', 'nerf'),
        '7' => esc_html__('7 Columns', 'nerf'),
        '8' => esc_html__('8 Columns', 'nerf'),
    );
    
    // Apartment Panel
    $wp_customize->add_panel( 'nerf_settings_apartment', array(
        'title' => esc_html__( 'Apartments Settings', 'nerf' ),
        'priority' => 4,
    ) );

    // General Section
    $wp_customize->add_section('nerf_settings_apartments_general', array(
        'title'    => esc_html__('General', 'nerf'),
        'priority' => 1,
        'panel' => 'nerf_settings_apartment',
    ));

    // Breadcrumbs
    $wp_customize->add_setting('nerf_theme_options[apartments_breadcrumbs]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('nerf_theme_options_apartments_breadcrumbs', array(
        'settings' => 'nerf_theme_options[apartments_breadcrumbs]',
        'label'    => esc_html__('Breadcrumbs', 'nerf'),
        'section'  => 'nerf_settings_apartments_general',
        'type'     => 'checkbox',
    ));

    // Breadcrumbs Background Color
    $wp_customize->add_setting('nerf_theme_options[apartments_breadcrumb_color]', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'apartments_breadcrumb_color', array(
        'label'    => esc_html__('Breadcrumbs Background Color', 'nerf'),
        'section'  => 'nerf_settings_apartments_general',
        'settings' => 'nerf_theme_options[apartments_breadcrumb_color]',
    )));

    // Breadcrumbs Background
    $wp_customize->add_setting('nerf_theme_options[apartments_breadcrumb_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'apartments_breadcrumb_image', array(
        'label'    => esc_html__('Breadcrumbs Background', 'nerf'),
        'section'  => 'nerf_settings_apartments_general',
        'settings' => 'nerf_theme_options[apartments_breadcrumb_image]',
    )));

    // Google Maps API
    $wp_customize->add_setting( 'nerf_theme_options[google_map_api_key]', array(
        'default'        => '',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'nerf_settings_apartments_archive_google_map_api_key', array(
        'label'   => esc_html__('Google Map API Key', 'nerf'),
        'section' => 'nerf_settings_apartments_general',
        'type'    => 'text',
        'settings' => 'nerf_theme_options[google_map_api_key]'
    ) );

    // Apartments Archives
    $wp_customize->add_section('nerf_settings_apartments_archive', array(
        'title'    => esc_html__('Apartments Archives', 'nerf'),
        'priority' => 2,
        'panel' => 'nerf_settings_apartment',
    ));

    // General Setting ?
    $wp_customize->add_setting('nerf_theme_options[apartments_general_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Nerf_WP_Customize_Heading_Control($wp_customize, 'apartments_general_setting', array(
        'label'    => esc_html__('General Settings', 'nerf'),
        'section'  => 'nerf_settings_apartments_archive',
        'settings' => 'nerf_theme_options[apartments_general_setting]',
    )));

    // Is Full Width
    $wp_customize->add_setting('nerf_theme_options[apartments_fullwidth]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('nerf_theme_options_apartments_fullwidth', array(
        'settings' => 'nerf_theme_options[apartments_fullwidth]',
        'label'    => esc_html__('Is Full Width', 'nerf'),
        'section'  => 'nerf_settings_apartments_archive',
        'type'     => 'checkbox',
    ));

    // layout
    $wp_customize->add_setting( 'nerf_theme_options[apartments_layout]', array(
        'default'        => 'main',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Nerf_WP_Customize_Radio_Image_Control( 
        $wp_customize, 
        'nerf_settings_apartments_archive_layout', 
        array(
            'label'   => esc_html__('Layout Type', 'nerf'),
            'section' => 'nerf_settings_apartments_archive',
            'type'    => 'select',
            'choices' => array(
                'main' => array(
                    'title' => esc_html__('Main Only', 'nerf'),
                    'img' => get_template_directory_uri() . '/inc/assets/images/screen1.png'
                ),
                'left-main' => array(
                    'title' => esc_html__('Left - Main Sidebar', 'nerf'),
                    'img' => get_template_directory_uri() . '/inc/assets/images/screen2.png'
                ),
                'main-right' => array(
                    'title' => esc_html__('Main - Right Sidebar', 'nerf'),
                    'img' => get_template_directory_uri() . '/inc/assets/images/screen3.png'
                ),
            ),
            'settings' => 'nerf_theme_options[apartments_layout]',
            'description' => esc_html__('Select the variation you want to apply on your apartment/archive page.', 'nerf'),
        ) 
    ));

    // Left Sidebar
    $wp_customize->add_setting( 'nerf_theme_options[apartments_left_sidebar]', array(
        'default'        => '',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'nerf_settings_apartments_archive_apartments_left_sidebar', array(
        'label'   => esc_html__('Archive Left Sidebar', 'nerf'),
        'section' => 'nerf_settings_apartments_archive',
        'type'    => 'select',
        'choices' => $sidebars,
        'settings' => 'nerf_theme_options[apartments_left_sidebar]',
        'description' => esc_html__('Choose a sidebar for left sidebar', 'nerf'),
    ) );

    // Right Sidebar
    $wp_customize->add_setting( 'nerf_theme_options[apartments_right_sidebar]', array(
        'default'        => '',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'nerf_settings_apartments_archive_apartments_right_sidebar', array(
        'label'   => esc_html__('Archive Right Sidebar', 'nerf'),
        'section' => 'nerf_settings_apartments_archive',
        'type'    => 'select',
        'choices' => $sidebars,
        'settings' => 'nerf_theme_options[apartments_right_sidebar]',
        'description' => esc_html__('Choose a sidebar for right sidebar', 'nerf'),
    ) );


    // Item Style
    $wp_customize->add_setting( 'nerf_theme_options[apartments_style]', array(
        'default'        => '',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'nerf_settings_apartments_archive_apartments_style', array(
        'label'   => esc_html__('Apartment Item Style', 'nerf'),
        'section' => 'nerf_settings_apartments_archive',
        'type'    => 'select',
        'choices' => array(
            '' => esc_html__('Default', 'nerf'),
            '-v1' => esc_html__('V1', 'nerf'),
            '-v2' => esc_html__('V2', 'nerf'),
        ),
        'settings' => 'nerf_theme_options[apartments_style]',
    ) );

    // apartments Columns
    $wp_customize->add_setting( 'nerf_theme_options[apartments_columns]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'nerf_settings_apartments_archive_apartments_columns', array(
        'label'   => esc_html__('Apartments Columns', 'nerf'),
        'section' => 'nerf_settings_apartments_archive',
        'type'    => 'select',
        'choices' => $columns,
        'settings' => 'nerf_theme_options[apartments_columns]',
    ) );

    // Number of Apartments Per Page
    $wp_customize->add_setting( 'nerf_theme_options[number_apartments_per_page]', array(
        'default'        => '12',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'nerf_settings_apartments_archive_number_apartments_per_page', array(
        'label'   => esc_html__('Number of Apartments Per Page', 'nerf'),
        'section' => 'nerf_settings_apartments_archive',
        'type'    => 'number',
        'settings' => 'nerf_theme_options[number_apartments_per_page]',
    ) );

}
add_action( 'customize_register', 'nerf_apartment_customize_register', 15 );