<?php

//namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Primary_Menu extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_primary_menu';
    }

	public function get_title() {
        return esc_html__( 'Apus Header Primary Menu', 'nerf' );
    }
    
	public function get_categories() {
        return [ 'nerf-header-elements' ];
    }

	protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'nerf' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'effect',
            [
                'label' => esc_html__( 'Effect Dropdown Menu', 'nerf' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'effect1' => esc_html__('Effect 1', 'nerf'),
                    'effect2' => esc_html__('Effect 2', 'nerf'),
                    'effect3' => esc_html__('Effect 3', 'nerf'),
                ),
                'default' => 'effect1'
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
            'section_title_style',
            [
                'label' => esc_html__( 'Style', 'nerf' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        // tab normal and hover

        $this->start_controls_tabs( 'tabs_menu_style' );

            $this->start_controls_tab(
                'tab_menu_normal',
                [
                    'label' => esc_html__( 'Normal', 'nerf' ),
                ]
            );
            
            $this->add_control(
                'menu_color',
                [
                    'label' => esc_html__( 'Color Menu', 'nerf' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .navbar-nav.megamenu > li > a' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Typography::get_type(),
                [
                    'label' => esc_html__( 'Typography', 'nerf' ),
                    'name' => 'menu_typography',
                    'selector' => '{{WRAPPER}} .megamenu > li > a',
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_menu_hover',
                [
                    'label' => esc_html__( 'Hover', 'nerf' ),
                ]
            );

            $this->add_control(
                'menu_hover_color',
                [
                    'label' => esc_html__( 'Color Hover Menu', 'nerf' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .navbar-nav.megamenu > li:hover > a,{{WRAPPER}} .navbar-nav.megamenu > li.active > a' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Typography::get_type(),
                [
                    'label' => esc_html__( 'Typography', 'nerf' ),
                    'name' => 'menu_hv_typography',
                    'selector' => '{{WRAPPER}} .navbar-nav.megamenu > li:hover > a, {{WRAPPER}} .navbar-nav.megamenu > li.active > a',
                ]
            );


            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => esc_html__( 'Padding Menu', 'nerf' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .megamenu > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );
    
        $this->add_control(
            'dp_color',
            [
                'label' => esc_html__( 'Color Dropdown Menu', 'nerf' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .navbar-nav.megamenu .dropdown-menu li > a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'dp_hover_color',
            [
                'label' => esc_html__( 'Color Hover Dropdown Menu', 'nerf' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .navbar-nav.megamenu .dropdown-menu li.current-menu-item > a,{{WRAPPER}} .navbar-nav.megamenu .dropdown-menu li.open > a,{{WRAPPER}}  .navbar-nav.megamenu .dropdown-menu li.active > a,{{WRAPPER}} .navbar-nav.megamenu .dropdown-menu li:hover > a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'bg_dp_color',
            [
                'label' => esc_html__( 'Background Color Dropdown Menu', 'nerf' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .navbar-nav.megamenu .dropdown-menu' => 'background-color: {{VALUE}};',
                ],
            ]
        );
    

        $this->end_controls_section();

    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        if ( has_nav_menu( 'primary' ) ) {
            $add_class = '';
            if ( !empty($align) ) {
                $add_class = 'menu-'.$align;
            }
            ?>
            <div class="main-menu <?php echo esc_attr($add_class.' '.$el_class); ?>">
                <nav data-duration="400" class="apus-megamenu animate navbar navbar-expand-lg" role="navigation">
                <?php
                    $args = array(
                        'theme_location' => 'primary',
                        'container_class' => 'collapse navbar-collapse no-padding',
                        'menu_class' => 'nav navbar-nav megamenu '.$effect,
                        'fallback_cb' => '',
                        'menu_id' => 'primary-menu',
                        'walker' => new Nerf_Nav_Menu()
                    );
                    wp_nav_menu($args);
                ?>
                </nav>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Primary_Menu );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Primary_Menu );
}