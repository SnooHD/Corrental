<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Popup_Video extends Widget_Base {

	public function get_name() {
        return 'apus_element_popup_video';
    }

	public function get_title() {
        return esc_html__( 'Apus Popup Video', 'nerf' );
    }

	public function get_icon() {
        return 'eicon-youtube';
    }

	public function get_categories() {
        return [ 'nerf-elements' ];
    }

	protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'nerf' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'img_src',
            [
                'name' => 'image',
                'label' => esc_html__( 'Image', 'nerf' ),
                'type' => Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Image Here', 'nerf' ),
            ]
        );

        $this->add_control(
            'video_link',
            [
                'label' => esc_html__( 'Youtube Video Link', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'input_type' => 'url',
            ]
        );

        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Style 1', 'nerf'),
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
                'label' => esc_html__( 'Icon', 'nerf' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

            $this->add_responsive_control(
                'sizes',
                [
                    'label' => esc_html__( 'Size', 'nerf' ),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 1000,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .popup-video' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'label' => esc_html__( 'Typography', 'nerf' ),
                    'name' => 'heading_typography',
                    'selector' => '{{WRAPPER}} .popup-video',
                ]
            );

            $this->start_controls_tabs( 'tabs_icon_style' );

                $this->start_controls_tab(
                    'tab_icon_normal',
                    [
                        'label' => esc_html__( 'Normal', 'nerf' ),
                    ]
                );

                $this->add_control(
                    'color_icon',
                    [
                        'label' => esc_html__( 'Color', 'nerf' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .popup-video' => 'color: {{VALUE}};',
                        ],
                    ]
                );

                $this->add_control(
                    'bg_icon',
                    [
                        'label' => esc_html__( 'Background Color', 'nerf' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .popup-video' => 'background-color: {{VALUE}};',
                        ],
                    ]
                );

                $this->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name' => 'border_icon',
                        'label' => esc_html__( 'Border', 'nerf' ),
                        'selector' => '{{WRAPPER}} .popup-video',
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
                    'color_hv_icon',
                    [
                        'label' => esc_html__( 'Color', 'nerf' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .popup-video:hover' => 'color: {{VALUE}};',
                        ],
                    ]
                );

                $this->add_control(
                    'bg_hv_icon',
                    [
                        'label' => esc_html__( 'Background Color', 'nerf' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .popup-video:hover' => 'background-color: {{VALUE}};',
                        ],
                    ]
                );

                $this->add_control(
                    'color_br_hv_icon',
                    [
                        'label' => esc_html__( 'Border Hover Color', 'nerf' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .popup-video:hover' => 'border-color: {{VALUE}};',
                        ],
                        'condition' => [
                            'border_icon_border!' => '',
                        ],
                    ]
                );

                $this->end_controls_tab();

            $this->end_controls_tabs();

        $this->end_controls_section();

    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        ?>
        <div class="widget-video <?php echo esc_attr($el_class);?>">
            <div class="video-wrapper-inner <?php echo esc_attr($style);?> <?php echo esc_attr ( ( !empty($img_src['id']) ) ? 'has-img':'' );?>">
                <?php
                    if ( !empty($img_src['id']) ) {
                    ?>
                    <div class="banner-image">
                        <?php echo nerf_get_attachment_thumbnail($img_src['id'], 'full'); ?>
                    </div>
                <?php } ?>
                <a class="popup-video d-inline-flex align-items-center justify-content-center" href="<?php echo esc_url($video_link); ?>">
                    <i class="flaticon-play"></i>
                </a>
            </div>
        </div>
        <?php
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Popup_Video );
} else {
    Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Popup_Video );
}