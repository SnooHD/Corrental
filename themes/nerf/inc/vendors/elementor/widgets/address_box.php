<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Address_Box extends Widget_Base {

	public function get_name() {
        return 'apus_element_address_box';
    }

	public function get_title() {
        return esc_html__( 'Apus Box', 'nerf' );
    }

	public function get_icon() {
        return 'eicon-image-box';
    }

	public function get_categories() {
        return [ 'nerf-elements' ];
    }

	protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Apus Box', 'nerf' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'icon',
            [
                'label' => esc_html__( 'Icon', 'nerf' ),
                'type' => Controls_Manager::ICON,
                'default' => 'fa fa-star',
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => '',
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => esc_html__( 'Content', 'nerf' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => '',
                'placeholder' => '',

            ]
        );

        $this->add_control(
            'btn_text',
            [
                'label' => esc_html__( 'Button Text', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_html__( 'Enter your button text here', 'nerf' ),
            ]
        );

        $this->add_control(
            'btn_link',
            [
                'label' => esc_html__( 'Button Link to', 'nerf' ),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://your-link.com', 'nerf' ),
            ]
        );

        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Style1', 'nerf'),
                ),
                'default' => ''
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
            'section_icon_style',
            [
                'label' => esc_html__( 'Icon Style', 'nerf' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

            // tab normal and hover

        $this->start_controls_tabs( 'tabs_icon_style' );

            $this->start_controls_tab(
                'tab_icon_normal',
                [
                    'label' => esc_html__( 'Normal', 'nerf' ),
                ]
            );

            $this->add_control(
                'icon_color',
                [
                    'label' => esc_html__( 'Icon Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .features-box-image ' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'icon_bgcolor',
                [
                    'label' => esc_html__( 'Icon Background Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .features-box-image ' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'icon_shadow',
                    'label' => esc_html__( 'Box Shadow', 'nerf' ),
                    'selector' => '{{WRAPPER}} .features-box-image',
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_icon_hover',
                [
                    'label' => esc_html__( 'Hover', 'nerf' ),
                ]
            );

            $this->add_control(
                'icon_hv_color',
                [
                    'label' => esc_html__( 'Icon Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .item-box:hover .features-box-image ' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'icon_hv_bgcolor',
                [
                    'label' => esc_html__( 'Icon Background Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .item-box:hover .features-box-image ' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'icon_hover_shadow',
                    'label' => esc_html__( 'Box Shadow', 'nerf' ),
                    'selector' => '{{WRAPPER}} .item-box:hover .features-box-image',
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Icon Typography', 'nerf' ),
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .features-box-image',
            ]
        );

        $this->add_control(
            'count_color',
            [
                'label' => esc_html__( 'Number Color', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .number' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'count_bg_color',
            [
                'label' => esc_html__( 'Number Background Color', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .number' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_info_style',
            [
                'label' => esc_html__( 'Information Style', 'nerf' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        // tab normal and hover

        $this->start_controls_tabs( 'tabs_info_style' );

            $this->start_controls_tab(
                'tab_info_normal',
                [
                    'label' => esc_html__( 'Normal', 'nerf' ),
                ]
            );

                $this->add_control(
                    'title_color',
                    [
                        'label' => esc_html__( 'Title Color', 'nerf' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .title' => 'color: {{VALUE}};',
                        ],
                    ]
                );

                $this->add_control(
                    'des_color',
                    [
                        'label' => esc_html__( 'Description Color', 'nerf' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .description' => 'color: {{VALUE}};',
                        ],
                    ]
                );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_info_hover',
                [
                    'label' => esc_html__( 'Hover', 'nerf' ),
                ]
            );

            $this->add_control(
                'title_hv_color',
                [
                    'label' => esc_html__( 'Title Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .item-box:hover .title' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'des_hv_color',
                [
                    'label' => esc_html__( 'Description Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .item-box:hover .description' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Title Typography', 'nerf' ),
                'name' => 'title2_typography',
                'selector' => '{{WRAPPER}} .title',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Description Typography', 'nerf' ),
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .description',
            ]
        );

        $this->end_controls_section();

    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings ); ?>
        <div class="apus-box position-relative">
            <?php if ( !empty($title) ) { ?>
                <h2 class="title">
                    <?php echo esc_html($title); ?>
                </h2>
            <?php } ?>
            <?php if ( !empty($description) ) { ?>
                <div class="description">
                    <?php echo trim($description); ?>
                </div>
            <?php } ?>
            <?php if( !empty($btn_link) && !empty($btn_text) ) { ?>
                <a class="btn-readmore d-inline-flex align-items-center flex-wrap" href="<?php echo esc_attr($btn_link['url']); ?>"> <i class="direction-circle d-flex align-items-center justify-content-center flaticon-up-right-arrow"></i><?php echo esc_html( $btn_text ); ?></a>
            <?php } ?>
            <?php if ( !empty($icon) ) { ?>
                <span class="bg-icon">
                    <i class="<?php echo esc_attr( $icon ); ?>"></i>
                </span>
            <?php } ?>
        </div>
        <?php
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Address_Box );
} else {
    Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Address_Box );
}