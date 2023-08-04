<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Mailchimp extends Widget_Base {

	public function get_name() {
        return 'apus_element_mailchimp';
    }

	public function get_title() {
        return esc_html__( 'Apus MailChimp Sign-Up Form', 'nerf' );
    }
    
	public function get_categories() {
        return [ 'nerf-elements' ];
    }

	protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'MailChimp Sign-Up Form', 'nerf' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'st1' => esc_html__('Show Text', 'nerf'),
                    'st2' => esc_html__('Show Icon', 'nerf'),
                ),
                'default' => 'st1'
            ]
        );

   		$this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'nerf' ),
                'type'          => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'nerf' ),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_box_style',
            [
                'label' => esc_html__( 'From', 'nerf' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_color',
            [
                'label' => esc_html__( 'Color', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                
                'selectors' => [
                    '{{WRAPPER}} form.mc4wp-form' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'form_bg_color',
            [
                'label' => esc_html__( 'Background', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                
                'selectors' => [
                    '{{WRAPPER}} form.mc4wp-form' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'label' => esc_html__( 'Border', 'nerf' ),
                'selector' => '{{WRAPPER}} form.mc4wp-form',
            ]
        );

        $this->add_responsive_control(
            'border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'nerf' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} form.mc4wp-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Input', 'nerf' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'input_color',
            [
                'label' => esc_html__( 'Input Color', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                
                'selectors' => [
                    '{{WRAPPER}} input' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_placeholder_color',
            [
                'label' => esc_html__( 'Input Placeholder Color', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                
                'selectors' => [
                    '{{WRAPPER}} input::-webkit-input-placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} input::-moz-placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} input:-ms-input-placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} input:-moz-placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_bg_color',
            [
                'label' => esc_html__( 'Input Background', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                
                'selectors' => [
                    '{{WRAPPER}} input' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'input_padding',
            [
                'label' => esc_html__( 'Padding Input', 'nerf' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_input',
                'label' => esc_html__( 'Border', 'nerf' ),
                'selector' => '{{WRAPPER}} input',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Typography Input', 'nerf' ),
                'name' => 'input_typography',
                'selector' => '{{WRAPPER}} input',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_button_style',
            [
                'label' => esc_html__( 'Button', 'nerf' ),
                'tab' => Controls_Manager::TAB_STYLE,
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
                            'label' => esc_html__( 'Button Color', 'nerf' ),
                            'type' => Controls_Manager::COLOR,
                            
                            'selectors' => [
                                '{{WRAPPER}} [type="submit"]' => 'color: {{VALUE}};',
                            ],
                        ]
                    );

                    $this->add_control(
                        'btn_bg_color',
                        [
                            'label' => esc_html__( 'Button Background', 'nerf' ),
                            'type' => Controls_Manager::COLOR,
                            
                            'selectors' => [
                                '{{WRAPPER}} [type="submit"]' => 'background: {{VALUE}};',
                            ],
                        ]
                    );
                    $this->add_control(
                        'btn_br_color',
                        [
                            'label' => esc_html__( 'Button Border', 'nerf' ),
                            'type' => Controls_Manager::COLOR,
                            
                            'selectors' => [
                                '{{WRAPPER}} [type="submit"]' => 'border-color: {{VALUE}};',
                            ],
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
                            'label' => esc_html__( 'Button Color', 'nerf' ),
                            'type' => Controls_Manager::COLOR,
                            
                            'selectors' => [
                                '{{WRAPPER}} [type="submit"]:hover' => 'color: {{VALUE}};',
                                '{{WRAPPER}} [type="submit"]:focus' => 'color: {{VALUE}};',
                            ],
                        ]
                    );

                    $this->add_control(
                        'btn_hover_bg_color',
                        [
                            'label' => esc_html__( 'Button Background', 'nerf' ),
                            'type' => Controls_Manager::COLOR,
                            
                            'selectors' => [
                                '{{WRAPPER}} [type="submit"]:hover' => 'background: {{VALUE}};',
                                '{{WRAPPER}} [type="submit"]:focus' => 'background: {{VALUE}};',
                            ],
                        ]
                    );
                    $this->add_control(
                        'btn_hover_br_color',
                        [
                            'label' => esc_html__( 'Button Border', 'nerf' ),
                            'type' => Controls_Manager::COLOR,
                            
                            'selectors' => [
                                '{{WRAPPER}} [type="submit"]:hover' => 'border-color: {{VALUE}};',
                                '{{WRAPPER}} [type="submit"]:focus' => 'border-color: {{VALUE}};',
                            ],
                        ]
                    );

                $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_responsive_control(
                'button_padding',
                [
                    'label' => esc_html__( 'Padding', 'nerf' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} [type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
            $this->add_responsive_control(
                'button_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'nerf' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} [type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'label' => esc_html__( 'Typography', 'nerf' ),
                    'name' => 'btn_typography',
                    'selector' => '{{WRAPPER}} [type="submit"]',
                ]
            );

        $this->end_controls_section();
        // end tab for button
    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        ?>
        <div class="widget-mailchimp <?php echo esc_attr($el_class.' '.$style); ?>">
            <?php mc4wp_show_form(''); ?>
        </div>
        <?php
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Mailchimp );
} else {
    Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Mailchimp );
}