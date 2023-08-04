<?php

//namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Nav_Bar extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_nav_bar';
    }

	public function get_title() {
        return esc_html__( 'Apus Header NavBar', 'nerf' );
    }
    
	public function get_categories() {
        return [ 'uomo-header-elements' ];
    }

	protected function register_controls() {

        $custom_menus = array();
        $menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
        if ( is_array( $menus ) && ! empty( $menus ) ) {
            foreach ( $menus as $menu ) {
                if ( is_object( $menu ) && isset( $menu->name, $menu->slug ) ) {
                    $custom_menus[ $menu->slug ] = $menu->name;
                }
            }
        }

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'nerf' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'nerf' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => '',
            ]
        );

        $this->add_control(
            'nav_menu',
            [
                'label' => esc_html__( 'Menu', 'nerf' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $custom_menus,
                'default' => ''
            ]
        );

        $this->add_responsive_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'nerf' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__( 'White', 'nerf' ),
                    'st_dark' => esc_html__( 'Dark', 'nerf' ),
                ],
                'default' => ''
            ]
        );

   		$this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'nerf' ),
                'type'          => Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'nerf' ),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_icon_style',
            [
                'label' => esc_html__( 'Title', 'nerf' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Color Icon', 'nerf' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .vertical-icon::before' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .vertical-icon::after' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings );
        
        $menu_id = 0;
        if ($nav_menu) {
            $term = get_term_by( 'slug', $nav_menu, 'nav_menu' );
            if ( !empty($term) ) {
                $menu_id = $term->term_id;
            }
        }

        if ( empty($menu_id) )
            return;
        ?>
        <div class="navbar-wrapper <?php echo esc_attr($el_class); ?>">
            <span class="show-navbar-sidebar d-inline-flex align-items-center">
                <i class="vertical-icon"></i> 
                <?php if ( !empty($title) ) { ?>
                    <span class="title">
                        <?php echo esc_html($title); ?>
                    </span>
                <?php } ?>
            </span>
            <div class="navbar-sidebar-wrapper <?php echo esc_attr($style); ?>">
                <a href="javascript:void(0);" class="close-navbar-sidebar"><i class="ti-close"></i></a>
                <nav class="navbar navbar-offcanvas navbar-static" role="navigation">
                    <?php
                        $args = array(
                            'menu'        => $menu_id,
                            'container_class' => 'navbar-collapse navbar-offcanvas-collapse',
                            'menu_class' => 'nav navbar-nav main-mobile-menu',
                            'fallback_cb' => '',
                            'walker' => new Nerf_Mobile_Menu()
                        );
                        wp_nav_menu($args);
                    ?>
                </nav>
            </div>
            <div class="navbar-sidebar-overlay"></div>
        </div>
        <?php
    }

}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Nav_Bar );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Nav_Bar );
}