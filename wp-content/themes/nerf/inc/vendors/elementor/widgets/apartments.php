<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Apartments extends Elementor\Widget_Base {

    public function get_name() {
        return 'apus_element_apartments';
    }

    public function get_title() {
        return esc_html__( 'Apus Apartments', 'nerf' );
    }
    
    public function get_categories() {
        return [ 'nerf-elements' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Apartments', 'nerf' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'nerf' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_html__( 'Enter your title here', 'nerf' ),
            ]
        );

        $this->add_control(
            'number',
            [
                'label' => esc_html__( 'Number', 'nerf' ),
                'type' => Elementor\Controls_Manager::NUMBER,
                'input_type' => 'number',
                'description' => esc_html__( 'Number posts to display', 'nerf' ),
                'default' => 4
            ]
        );
        
        $this->add_control(
            'order_by',
            [
                'label' => esc_html__( 'Order by', 'nerf' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'nerf'),
                    'date' => esc_html__('Date', 'nerf'),
                    'ID' => esc_html__('ID', 'nerf'),
                    'author' => esc_html__('Author', 'nerf'),
                    'title' => esc_html__('Title', 'nerf'),
                    'modified' => esc_html__('Modified', 'nerf'),
                    'rand' => esc_html__('Random', 'nerf'),
                    'comment_count' => esc_html__('Comment count', 'nerf'),
                    'menu_order' => esc_html__('Menu order', 'nerf'),
                ),
                'default' => ''
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => esc_html__( 'Sort order', 'nerf' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'nerf'),
                    'ASC' => esc_html__('Ascending', 'nerf'),
                    'DESC' => esc_html__('Descending', 'nerf'),
                ),
                'default' => ''
            ]
        );

        $this->add_control(
            'item_style',
            [
                'label' => esc_html__( 'Item Style', 'nerf' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'inner-grid' => esc_html__('Default', 'nerf'),
                    'inner-grid-v1' => esc_html__('V1', 'nerf'),
                    'inner-grid-v2' => esc_html__('V2', 'nerf'),
                    'inner-grid-v3' => esc_html__('V3', 'nerf'),
                ),
                'default' => 'inner-grid',
                'condition' => [
                    'layout_type' => ['grid', 'carousel'],
                ]
            ]
        );
        

        $this->add_control(
            'layout_type',
            [
                'label' => esc_html__( 'Layout', 'nerf' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'carousel' => esc_html__('Carousel', 'nerf'),
                    'grid' => esc_html__('Grid', 'nerf'),
                ),
                'default' => 'grid'
            ]
        );

        $this->add_control(
            'fullscreen',
            [
                'label'         => esc_html__( 'Full Screen', 'nerf' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Yes', 'nerf' ),
                'label_off'     => esc_html__( 'No', 'nerf' ),
                'return_value'  => true,
                'default'       => false,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $columns = range( 1, 12 );
        $columns = array_combine( $columns, $columns );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__( 'Columns', 'nerf' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $columns,
                'frontend_available' => true,
                'default' => 3,
                'condition' => [
                    'layout_type' => ['grid', 'carousel'],
                ],
            ]
        );

        $this->add_responsive_control(
            'slides_to_scroll',
            [
                'label' => esc_html__( 'Slides to Scroll', 'nerf' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'nerf' ),
                'options' => $columns,
                'condition' => [
                    'columns!' => '1',
                    'layout_type' => 'carousel',
                ],
                'frontend_available' => true,
                'default' => 3,
            ]
        );

        $this->add_control(
            'show_nav',
            [
                'label' => esc_html__( 'Show Nav', 'nerf' ),
                'type' => Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'nerf' ),
                'label_off' => esc_html__( 'Show', 'nerf' ),
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__( 'Show Pagination', 'nerf' ),
                'type' => Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'nerf' ),
                'label_off' => esc_html__( 'Show', 'nerf' ),
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'nav_style',
            [
                'label' => esc_html__( 'Nav Style', 'nerf' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'default' => esc_html__('Default', 'nerf'),
                    'style1' => esc_html__('Style 1', 'nerf'),
                    'style2' => esc_html__('Style 2', 'nerf'),
                ),
                'default' => 'style1',
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Image_Size::get_type(),
            [
                'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
                'default' => 'large',
                'separator' => 'none',
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

        $this->start_controls_tabs( 'tabs_title_style' );

            $this->start_controls_tab(
                'tab_title_normal',
                [
                    'label' => esc_html__( 'Normal', 'nerf' ),
                ]
            );

            $this->add_control(
                'post_title_color',
                [
                    'label' => esc_html__( 'Title Color', 'nerf' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .entry-title a' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Typography::get_type(),
                [
                    'label' => esc_html__( 'Title Typography', 'nerf' ),
                    'name' => 'post_title_typography',
                    'selector' => '{{WRAPPER}} .entry-title a',
                ]
            );

            $this->add_control(
                'post_area_color',
                [
                    'label' => esc_html__( 'Area Color', 'nerf' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .area' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Typography::get_type(),
                [
                    'label' => esc_html__( 'Area Typography', 'nerf' ),
                    'name' => 'post_area_typography',
                    'selector' => '{{WRAPPER}} .area',
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_title_hover',
                [
                    'label' => esc_html__( 'Hover', 'nerf' ),
                ]
            );

            $this->add_control(
                'post_title_hv_color',
                [
                    'label' => esc_html__( 'Title Color', 'nerf' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .entry-title a:hover,{{WRAPPER}} .entry-title a:focus' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Typography::get_type(),
                [
                    'label' => esc_html__( 'Title Typography', 'nerf' ),
                    'name' => 'post_title_hv_typography',
                    'selector' => '{{WRAPPER}} .entry-title a:hover, {{WRAPPER}} .entry-title a:focus',
                ]
            );


            $this->add_control(
                'apartment_post_area_color',
                [
                    'label' => esc_html__( 'Area Color', 'nerf' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .apartment-layout:hover .area' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Typography::get_type(),
                [
                    'label' => esc_html__( 'Area Typography', 'nerf' ),
                    'name' => 'apartment_post_area_typography',
                    'selector' => '{{WRAPPER}} .apartment-layout:hover .area',
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover


        $this->end_controls_section();


        $this->start_controls_section(
            'section_nav_style',
            [
                'label' => esc_html__( 'Nav', 'nerf' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // tab normal and hover

        $this->start_controls_tabs( 'tabs_nav_style' );

            $this->start_controls_tab(
                'tab_nav_normal',
                [
                    'label' => esc_html__( 'Normal', 'nerf' ),
                ]
            );

            $this->add_control(
                'nav_color',
                [
                    'label' => esc_html__( 'Color', 'nerf' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-arrow' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'nav_bg_color',
                [
                    'label' => esc_html__( 'Background Color', 'nerf' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-arrow' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'nav_br_color',
                [
                    'label' => esc_html__( 'Border Color', 'nerf' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-arrow' => 'border-color: {{VALUE}};',
                    ],
                ]
            );


            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_nav_hover',
                [
                    'label' => esc_html__( 'Hover', 'nerf' ),
                ]
            );

            $this->add_control(
                'nav_hv_color',
                [
                    'label' => esc_html__( 'Color', 'nerf' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-arrow:hover, {{WRAPPER}} .slick-arrow:focus' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'nav_hv_bg_color',
                [
                    'label' => esc_html__( 'Background Color', 'nerf' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-arrow:hover, {{WRAPPER}} .slick-arrow:focus' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'nav_hv_br_color',
                [
                    'label' => esc_html__( 'Border Color', 'nerf' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-arrow:hover, {{WRAPPER}} .slick-arrow:focus' => 'border-color: {{VALUE}};',
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

        $args = array(
            'post_type' => 'apartment',
            'post_status' => 'publish',
            'posts_per_page' => $number,
            'orderby' => $order_by,
            'order' => $order,
        );
        $loop = new WP_Query($args);
        if ( $loop->have_posts() ) {
            if ( $image_size == 'custom' ) {
                if ( $image_custom_dimension['width'] && $image_custom_dimension['height'] ) {
                    $thumbsize = $image_custom_dimension['width'].'x'.$image_custom_dimension['height'];
                } else {
                    $thumbsize = 'full';
                }
            } else {
                $thumbsize = $image_size;
            }

            set_query_var( 'thumbsize', $thumbsize );

            $columns = !empty($columns) ? $columns : 3;
            $columns_tablet = !empty($columns_tablet) ? $columns_tablet : 2;
            $columns_mobile = !empty($columns_mobile) ? $columns_mobile : 1;
            
            $slides_to_scroll = !empty($slides_to_scroll) ? $slides_to_scroll : $columns;
            $slides_to_scroll_tablet = !empty($slides_to_scroll_tablet) ? $slides_to_scroll_tablet : $slides_to_scroll;
            $slides_to_scroll_mobile = !empty($slides_to_scroll_mobile) ? $slides_to_scroll_mobile : 1;

            ?>
            <div class="widget-apartments <?php echo esc_attr($el_class); ?>">
                <?php if ( $title ) { ?>
                    <h2 class="widget-title"><?php echo esc_html($title); ?></h2>
                <?php } ?>
                <div class="widget-content">

                    <?php if ( $layout_type == 'carousel' ): ?>
                        <div class="slick-carousel <?php echo esc_attr( $fullscreen ? 'fullscreen' : 'nofullscreen' ); ?> <?php echo esc_attr($columns < $loop->post_count?'':'hidden-dots'); echo esc_attr($nav_style); ?>"
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

                            data-pagination="<?php echo esc_attr($show_pagination ? 'true' : 'false'); ?>" data-nav="<?php echo esc_attr($show_nav ? 'true' : 'false'); ?>">
                            <?php while ( $loop->have_posts() ): $loop->the_post(); ?>
                                <div class="item">
                                    <?php get_template_part( 'template-apartment/loop/'.$item_style, null, array('thumbsize' => $thumbsize)); ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php elseif ( $layout_type == 'grid' ): ?>
                            <div class="row">
                                <?php
                                    $mdcol = 12/$columns;
                                    $smcol = 12/$columns_tablet;
                                    $xscol = 12/$columns_mobile;
                                    while ( $loop->have_posts() ) : $loop->the_post();
                                ?>
                                    <div class="col-xl-<?php echo esc_attr($mdcol); ?> col-md-<?php echo esc_attr($smcol); ?> col-<?php echo esc_attr($xscol); ?>">
                                        <?php get_template_part( 'template-apartment/loop/'.$item_style, null, array('thumbsize' => $thumbsize) ); ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Apartments );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Apartments );
}