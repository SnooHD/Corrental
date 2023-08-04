<?php

//namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Schedule_Visit extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_schedule_visit';
    }

	public function get_title() {
        return esc_html__( 'Schedule Visit', 'nerf' );
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

        $contact_forms = array();
        if ( is_admin() ) {
            $args = array(
                'posts_per_page'   => -1,
                'post_type'        => 'wpcf7_contact_form',
                'post_status'      => 'publish'
            );
            $posts = get_posts( $args );
            foreach ( $posts as $post ) {
                $contact_forms[$post->post_name] = $post->post_title;
            }
        }

        $this->add_control(
            'contact_form',
            [
                'label' => esc_html__( 'Menu', 'nerf' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $contact_forms,
                'default' => ''
            ]
        );

        $this->add_control(
            'btn_text',
            [
                'label' => esc_html__( 'Button Text', 'nerf' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_html__( 'Enter your button text here', 'nerf' ),
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
            'section_button_style',
            [
                'label' => esc_html__( 'Button', 'nerf' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
            $this->start_controls_tabs(
                'style_tabs'
            );
                $this->start_controls_tab(
                    'button_normal_tab',
                        [
                            'label' => esc_html__( 'Normal', 'nerf' ),
                        ]
                    );
                    $this->add_control(
                        'btn_color',
                        [
                            'label' => esc_html__( 'Color', 'nerf' ),
                            'type' => Elementor\Controls_Manager::COLOR,
                            
                            'selectors' => [
                                '{{WRAPPER}} .btn' => 'color: {{VALUE}};',
                            ],
                        ]
                    );

                    $this->add_control(
                        'btn_bg_color',
                        [
                            'label' => esc_html__( 'Background', 'nerf' ),
                            'type' => Elementor\Controls_Manager::COLOR,
                            
                            'selectors' => [
                                '{{WRAPPER}} .btn' => 'background: {{VALUE}};',
                            ],
                        ]
                    );
                    $this->add_group_control(
                        Elementor\Group_Control_Border::get_type(),
                        [
                            'name' => 'border_button',
                            'label' => esc_html__( 'Border', 'nerf' ),
                            'selector' => '{{WRAPPER}} .btn',
                        ]
                    );

                $this->end_controls_tab();

                $this->start_controls_tab(
                    'button_hover_tab',
                        [
                            'label' => esc_html__( 'Hover', 'nerf' ),
                        ]
                    );
                    $this->add_control(
                        'btn_hover_color',
                        [
                            'label' => esc_html__( 'Color', 'nerf' ),
                            'type' => Elementor\Controls_Manager::COLOR,
                            
                            'selectors' => [
                                '{{WRAPPER}} .btn:hover' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .btn:focus' => 'color: {{VALUE}};',
                            ],
                        ]
                    );

                    $this->add_control(
                        'btn_hover_bg_color',
                        [
                            'label' => esc_html__( 'Background', 'nerf' ),
                            'type' => Elementor\Controls_Manager::COLOR,
                            
                            'selectors' => [
                                '{{WRAPPER}} .btn:hover' => 'background: {{VALUE}};',
                                '{{WRAPPER}} .btn:focus' => 'background: {{VALUE}};',
                            ],
                        ]
                    );
                    $this->add_control(
                        'btn_hover_br_color',
                        [
                            'label' => esc_html__( 'Border Color', 'nerf' ),
                            'type' => Elementor\Controls_Manager::COLOR,
                            
                            'selectors' => [
                                '{{WRAPPER}} .btn:hover, {{WRAPPER}} .btn:focus' => 'border-color: {{VALUE}};',
                            ],
                            'condition' => [
                                'border_button_border!' => '',
                            ],
                        ]
                    );

                $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_responsive_control(
                'button_padding',
                [
                    'label' => esc_html__( 'Padding', 'nerf' ),
                    'type' => Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'before',
                ]
            );
            $this->add_responsive_control(
                'button_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'nerf' ),
                    'type' => Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Typography::get_type(),
                [
                    'label' => esc_html__( 'Typography', 'nerf' ),
                    'name' => 'btn_typography',
                    'selector' => '{{WRAPPER}} .btn',
                ]
            );

        $this->end_controls_section();
    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );
        if(!empty($btn_text)){
        ?>
            <div class="apus-schedule-visit <?php echo esc_attr($el_class); ?>">
                <a href="#apus-schedule-visit-wrapper" class="schedule-visit-btn btn btn-theme"><?php echo esc_html( $btn_text ); ?></a>
                <div id="apus-schedule-visit-wrapper" class="schedule-visit-form-wrapper mfp-hide">
                    <?php
                    if ( $contact_form ) {
                        $args = array(
                          'name'        => $contact_form,
                          'post_type'   => 'wpcf7_contact_form',
                          'post_status' => 'publish',
                          'numberposts' => 1
                        );
                        $posts = get_posts($args);
                        if( $posts ) {
                            echo do_shortcode('[contact-form-7 id="'.$posts[0]->ID.'" title="'.$posts[0]->post_title.'"]');
                        }
                    }
                    ?>
                </div>
            </div>
        <?php }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Schedule_Visit );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Schedule_Visit );
}