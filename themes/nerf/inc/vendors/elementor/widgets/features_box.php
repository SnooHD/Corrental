<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Features_Box extends Widget_Base {

	public function get_name() {
        return 'apus_element_features_box';
    }

	public function get_title() {
        return esc_html__( 'Apus Features Box', 'nerf' );
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
                'label' => esc_html__( 'Features Box', 'nerf' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'image_icon',
            [
                'label' => esc_html__( 'Image or Icon', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'icon' => esc_html__('Icon', 'nerf'),
                    'image' => esc_html__('Image', 'nerf'),
                ),
                'default' => 'image'
            ]
        );

        $repeater->add_control(
            'icon',
            [
                'label' => esc_html__( 'Icon', 'nerf' ),
                'type' => Controls_Manager::ICON,
                'default' => 'fa fa-star',
                'condition' => [
                    'image_icon' => 'icon',
                ],
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => esc_html__( 'Choose Image', 'nerf' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'image_icon' => 'image',
                ],
            ]
        );

        $repeater->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                'default' => 'full',
                'separator' => 'none',
                'condition' => [
                    'image_icon' => 'image',
                ],
            ]
        );
        $repeater->add_control(
            'title_text',
            [
                'label' => esc_html__( 'Title & Description', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'This is the heading', 'nerf' ),
                'placeholder' => esc_html__( 'Enter your title', 'nerf' ),
            ]
        );

        $repeater->add_control(
            'description_text',
            [
                'label' => esc_html__( 'Content', 'nerf' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'Description', 'nerf' ),
                'placeholder' => esc_html__( 'Enter your description', 'nerf' ),
                'separator' => 'none',
                'rows' => 10,
                'show_label' => false,
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => esc_html__( 'Link to', 'nerf' ),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://your-link.com', 'nerf' ),
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'features',
            [
                'label' => esc_html__( 'Features Box', 'nerf' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );
        
        $columns = range( 1, 12 );
        $columns = array_combine( $columns, $columns );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__( 'Columns', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'options' => $columns,
                'frontend_available' => true,
                'default' => 3,
            ]
        );
        

        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'style1' => esc_html__('Style 1', 'nerf'),
                    'style2' => esc_html__('Style 2', 'nerf'),
                ),
                'default' => 'style1'
            ]
        );
        $this->add_control(
            'layout',
            [
                'label' => esc_html__( 'Layout', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'carousel' => esc_html__('Carousel', 'nerf'),
                    'grid' => esc_html__('Grid', 'nerf'),
                ),
                'default' => 'carousel',
            ]
        );

        $this->add_responsive_control(
            'slides_to_scroll',
            [
                'label' => esc_html__( 'Slides to Scroll', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'nerf' ),
                'options' => $columns,
                'frontend_available' => true,
                'default' => 3,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_responsive_control(
            'alignment',
            [
                'label' => esc_html__( 'Alignment', 'nerf' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'nerf' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'nerf' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'nerf' ),
                        'icon' => 'fa fa-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__( 'Justified', 'nerf' ),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .item-inner-features' => 'text-align: {{VALUE}};',
                ],
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
                'label' => esc_html__( 'Box Style', 'nerf' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'padding-box',
            [
                'label' => esc_html__( 'Padding', 'nerf' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .item-inner-features' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // tab normal and hover

        $this->start_controls_tabs( 'tabs_box_style' );

            $this->start_controls_tab(
                'tab_box_normal',
                [
                    'label' => esc_html__( 'Normal', 'nerf' ),
                ]
            );

            $this->add_control(
                'color',
                [
                    'label' => esc_html__( 'Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .item-inner-features' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'box_color',
                    'selector' => '{{WRAPPER}} .item-inner-features',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'border_box',
                    'label' => esc_html__( 'Border', 'nerf' ),
                    'selector' => '{{WRAPPER}} .item-inner-features',
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_box_hover',
                [
                    'label' => esc_html__( 'Hover', 'nerf' ),
                ]
            );

            $this->add_control(
                'color_hover',
                [
                    'label' => esc_html__( 'Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .item-inner-features:hover' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'box_color_hover',
                    'selector' => '{{WRAPPER}} .item-inner-features:hover',
                ]
            );

            $this->add_control(
                'color_br_hv_box',
                [
                    'label' => esc_html__( 'Border Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .item-inner-features:hover' => 'border-color: {{VALUE}};',
                    ],
                    'condition' => [
                        'border_box_border!' => '',
                    ],
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover

        $this->end_controls_section();


        $this->start_controls_section(
            'section_heading_style',
            [
                'label' => esc_html__( 'Information Style', 'nerf' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Heading Color', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'title_color_hover',
            [
                'label' => esc_html__( 'Heading Color Hover', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .item-inner-features:hover .title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'margin-title',
            [
                'label' => esc_html__( 'Heading Margin', 'nerf' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Heading Typography', 'nerf' ),
                'name' => 'heading_typography',
                'selector' => '{{WRAPPER}} .title',
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

        // tab for icon
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
                    'label' => esc_html__( 'Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .features-box-image' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'icon_bg_color',
                    'selector' => '{{WRAPPER}} .features-box-image',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'border_icon',
                    'label' => esc_html__( 'Border', 'nerf' ),
                    'selector' => '{{WRAPPER}} .features-box-image',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'tab_icon_hover',
                [
                    'label' => esc_html__( 'Hover', 'nerf' ),
                ]
            );

            $this->add_control(
                'icon_hover_color',
                [
                    'label' => esc_html__( 'Hover Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .item-inner-features:hover .features-box-image' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'icon_bg_hover_color',
                    'selector' => '{{WRAPPER}}  .item-inner-features:hover .features-box-image',
                ]
            );

            $this->add_control(
                'color_br_hv_icon',
                [
                    'label' => esc_html__( 'Border Hover Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .item-inner-features:hover .features-box-image' => 'border-color: {{VALUE}};',
                    ],
                    'condition' => [
                        'border_icon_border!' => '',
                    ],
                ]
            );
            
            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover

        $this->end_controls_section();


    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings );


        $columns = !empty($columns) ? $columns : 3;
        $columns_tablet = !empty($columns_tablet) ? $columns_tablet : 2;
        $columns_mobile = !empty($columns_mobile) ? $columns_mobile : 1;
        
        $slides_to_scroll = !empty($slides_to_scroll) ? $slides_to_scroll : $columns;
        $slides_to_scroll_tablet = !empty($slides_to_scroll_tablet) ? $slides_to_scroll_tablet : $slides_to_scroll;
        $slides_to_scroll_mobile = !empty($slides_to_scroll_mobile) ? $slides_to_scroll_mobile : 1;

        if ( !empty($features) ) {
            ?>
            <div class="widget-features-box <?php echo esc_attr($el_class.' '.$alignment); ?>">
                <?php if($layout == 'carousel') {?>
                    <div class="slick-carousel <?php echo esc_attr( (count($features) <= $columns )?'hidden-dots':'' ); ?>" 
                    data-items="<?php echo esc_attr($columns); ?>"
                    data-large="<?php echo esc_attr( $columns_tablet ); ?>"
                    data-medium="<?php echo esc_attr( $columns_tablet ); ?>"
                    data-small="<?php echo esc_attr($columns_mobile); ?>"
                    data-smallest="<?php echo esc_attr($columns_mobile); ?>"

                    data-slidestoscroll="<?php echo esc_attr($slides_to_scroll); ?>"
                    data-slidestoscroll_large="<?php echo esc_attr( $slides_to_scroll_tablet ); ?>"
                    data-slidestoscroll_medium="<?php echo esc_attr( $slides_to_scroll_tablet ); ?>"
                    data-slidestoscroll_small="<?php echo esc_attr($slides_to_scroll_mobile); ?>"
                    data-slidestoscroll_smallest="<?php echo esc_attr($slides_to_scroll_mobile); ?>"
                    data-pagination="true" data-nav="false">
                        <?php foreach ($features as $item): ?>
                            <div class="item">
                                <div class="item-inner-features <?php echo trim($style); ?>">
                                    <div class="top-inner">
                                        <?php if(!empty($item['number'])) {?>
                                            <div class="number">
                                                <?php echo (int)$item['number']; ?>
                                            </div>
                                        <?php } ?>
                                        <?php
                                        $has_content = ! empty( $item['title_text'] ) || ! empty( $item['description_text'] );
                                        $html = '';

                                        if ( $item['image_icon'] == 'image' ) {
                                            if ( ! empty( $item['image']['url'] ) ) {
                                                $this->add_render_attribute( 'image', 'src', $item['image']['url'] );
                                                $this->add_render_attribute( 'image', 'alt', Control_Media::get_image_alt( $item['image'] ) );
                                                $this->add_render_attribute( 'image', 'title', Control_Media::get_image_title( $item['image'] ) );


                                                $image_html = Group_Control_Image_Size::get_attachment_image_html( $item, 'thumbnail', 'image' );

                                                if ( ! empty( $item['link']['url'] ) ) {
                                                    $image_html = '<a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>' . $image_html . '</a>';
                                                }

                                                $html .= '<div class="features-box-image d-inline-flex align-items-center justify-content-center img">' . $image_html . '</div>';
                                            }
                                        } elseif ( $item['image_icon'] == 'icon' && !empty($item['icon'])) {
                                            $html .= '<div class="features-box-image d-inline-flex align-items-center justify-content-center icon"><i class="'.$item['icon'].'"></i></div>';
                                        }
                                    $html .= '</div>';
                                    if ( $has_content ) {
                                        $html .= '<div class="features-box-content">';

                                        if ( ! empty( $item['title_text'] ) ) {
                                            
                                            $title_html = $item['title_text'];

                                            if ( ! empty( $item['link']['url'] ) ) {
                                                $html .= '<a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'><h3 class="title">'.$title_html.'</h3></a>';
                                            } else {
                                                $html .= sprintf( '<h3 class="title">%1$s</h3>', $title_html );
                                            }
                                        }
                                        
                                        

                                        if ( ! empty( $item['description_text'] ) ) {
                                            $html .= sprintf( '<div class="description">%1$s</div>', $item['description_text'] );
                                        }


                                        $html .= '</div>';
                                    }

                                    echo trim($html);
                                    ?>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php }elseif($layout == 'grid'){  $item['number'] =1;?>  
                    <div class="row">
                        <?php 
                        $mdcol = 12/$columns;
                        $smcol = 12/$columns_tablet;
                        $xscol = 12/$columns_mobile;
                        foreach ($features as $item): ?>
                            <div class="item col-xl-<?php echo esc_attr($mdcol); ?> col-md-<?php echo esc_attr($smcol); ?> col-<?php echo esc_attr($xscol); ?>">
                                <div class="item-inner-features <?php echo trim($style); ?>">
                                    <div class="top-inner">
                                        <?php if(!empty($item['number'])) {?>
                                            <div class="number">
                                                <?php echo (int)$item['number']; ?>
                                            </div>
                                        <?php } ?>
                                        <?php
                                        $has_content = ! empty( $item['title_text'] ) || ! empty( $item['description_text'] );
                                        $html = '';

                                        if ( $item['image_icon'] == 'image' ) {
                                            if ( ! empty( $item['image']['url'] ) ) {
                                                $this->add_render_attribute( 'image', 'src', $item['image']['url'] );
                                                $this->add_render_attribute( 'image', 'alt', Control_Media::get_image_alt( $item['image'] ) );
                                                $this->add_render_attribute( 'image', 'title', Control_Media::get_image_title( $item['image'] ) );


                                                $image_html = Group_Control_Image_Size::get_attachment_image_html( $item, 'thumbnail', 'image' );

                                                if ( ! empty( $item['link']['url'] ) ) {
                                                    $image_html = '<a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>' . $image_html . '</a>';
                                                }

                                                $html .= '<div class="features-box-image d-inline-flex align-items-center justify-content-center img">' . $image_html . '</div>';
                                            }
                                        } elseif ( $item['image_icon'] == 'icon' && !empty($item['icon'])) {
                                            $html .= '<div class="features-box-image d-inline-flex align-items-center justify-content-center icon"><i class="'.$item['icon'].'"></i></div>';
                                        }
                                    $html .= '</div>';
                                    if ( $has_content ) {
                                        $html .= '<div class="features-box-content">';

                                        if ( ! empty( $item['title_text'] ) ) {
                                            
                                            $title_html = $item['title_text'];

                                            if ( ! empty( $item['link']['url'] ) ) {
                                                $html .= '<a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'><h3 class="title">'.$title_html.'</h3></a>';
                                            } else {
                                                $html .= sprintf( '<h3 class="title">%1$s</h3>', $title_html );
                                            }
                                        }

                                        if ( ! empty( $item['description_text'] ) ) {
                                            $html .= sprintf( '<div class="description">%1$s</div>', $item['description_text'] );
                                        }


                                        $html .= '</div>';
                                    }

                                    echo trim($html);
                                    ?>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php } ?>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Features_Box );
} else {
    Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Features_Box );
}