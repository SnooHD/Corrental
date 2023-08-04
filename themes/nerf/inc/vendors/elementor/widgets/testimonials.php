<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Testimonials extends Widget_Base {

    public function get_name() {
        return 'apus_element_testimonials';
    }

    public function get_title() {
        return esc_html__( 'Apus Testimonials', 'nerf' );
    }

    public function get_icon() {
        return 'eicon-testimonial';
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

        $repeater = new Repeater();

        $repeater->add_control(
            'img_src',
            [
                'name' => 'image',
                'label' => esc_html__( 'Choose Image', 'nerf' ),
                'type' => Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Brand Image', 'nerf' ),
            ]
        );

        $repeater->add_control(
            'name',
            [
                'label' => esc_html__( 'Name', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );


        $repeater->add_control(
            'content', [
                'label' => esc_html__( 'Content', 'nerf' ),
                'type' => Controls_Manager::TEXTAREA
            ]
        );

        $repeater->add_control(
            'job',
            [
                'label' => esc_html__( 'Job', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );


        $repeater->add_control(
            'link',
            [
                'label' => esc_html__( 'Link To', 'nerf' ),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'Enter your social link here', 'nerf' ),
                'placeholder' => esc_html__( 'https://your-link.com', 'nerf' ),
            ]
        );

        $this->add_control(
            'testimonials',
            [
                'label' => esc_html__( 'Testimonials', 'nerf' ),
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
                'default' => 1,
            ]
        );

        $this->add_control(
            'show_nav',
            [
                'label' => esc_html__( 'Show Nav', 'nerf' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'nerf' ),
                'label_off' => esc_html__( 'Show', 'nerf' ),
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__( 'Show Pagination', 'nerf' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'nerf' ),
                'label_off' => esc_html__( 'Show', 'nerf' ),
            ]
        );

        $this->add_control(
            'layout_type',
            [
                'label' => esc_html__( 'Layout', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'style1' => esc_html__('Style 1', 'nerf'),
                    'style2' => esc_html__('Style 2', 'nerf'),
                    'style3' => esc_html__('Style 3', 'nerf'),
                ),
                'default' => 'style1'
            ]
        );

        $this->add_control(
            'fullscreen',
            [
                'label'         => esc_html__( 'Full Screen', 'nerf' ),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Yes', 'nerf' ),
                'label_off'     => esc_html__( 'No', 'nerf' ),
                'return_value'  => true,
                'default'       => false,
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
                'label' => esc_html__( 'Style Box', 'nerf' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_box',
                'label' => esc_html__( 'Border Box', 'nerf' ),
                'selector' => '{{WRAPPER}} [class*="testimonials-item"]',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Style Info', 'nerf' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'test_title_color',
            [
                'label' => esc_html__( 'Name Color', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .name-client' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .name-client a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Name Typography', 'nerf' ),
                'name' => 'test_title_typography',
                'selector' => '{{WRAPPER}} .name-client',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => esc_html__( 'Content Color', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Content Typography', 'nerf' ),
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .description',
            ]
        );

        $this->add_control(
            'job_color',
            [
                'label' => esc_html__( 'Job Color', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .job' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Job Typography', 'nerf' ),
                'name' => 'listing_typography',
                'selector' => '{{WRAPPER}} .job',
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'section_dots_style',
            [
                'label' => esc_html__( 'Style Dots', 'nerf' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'dots_color',
            [
                'label' => esc_html__( 'Color', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-carousel .slick-dots li button' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .slick-carousel .slick-dots li button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'dots_active_color',
            [
                'label' => esc_html__( 'Color Active', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-carousel .slick-dots .slick-active button' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .slick-carousel .slick-dots li.slick-active button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        $columns = !empty($columns) ? $columns : 1;
        $columns_tablet = !empty($columns_tablet) ? $columns_tablet : 1;
        $columns_mobile = !empty($columns_mobile) ? $columns_mobile : 1;
        
        if ( !empty($testimonials) ) {
            ?>
            <div class="widget-testimonials <?php echo esc_attr($el_class.' '.$layout_type); ?> <?php echo esc_attr($show_nav ? 'show_nav' : ''); ?> <?php echo esc_attr( $fullscreen ? 'fullscreen' : 'nofullscreen' ); ?>">
                

                <?php if($layout_type == 'style1' ) { ?>

                    <div class="slick-carousel testimonial-main <?php echo trim( ($columns >= count($testimonials))?'hidden-dots':'' ); ?>" data-items="<?php echo esc_attr($columns); ?>" data-large="<?php echo esc_attr( $columns_tablet ); ?>" data-medium="<?php echo esc_attr( $columns_tablet ); ?>" data-small="<?php echo esc_attr($columns_mobile); ?>" data-smallest="<?php echo esc_attr($columns_mobile); ?>" data-pagination="<?php echo esc_attr($show_pagination ? 'true' : 'false'); ?>" data-nav="<?php echo esc_attr($show_nav ? 'true' : 'false'); ?>" data-infinite="true">
                        <?php foreach ($testimonials as $item) { ?>
                        <div class="item">

                            <div class="testimonials-item">

                                <div class="inner-bottom">
                                    <div class="d-sm-flex row">
                                        <?php if ( isset( $item['img_src']['id'] ) && !empty($item['img_src']['id']) ) { ?>
                                        <div class="wrapper-avarta col-12 col-sm-4">
                                            <div class="avarta d-flex align-items-center">
                                                <?php echo nerf_get_attachment_thumbnail($item['img_src']['id'], 'full'); ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        
                                        <div class="info-testimonials col-12 col-sm-8">
                                            <?php if ( !empty($item['content']) ) { ?>
                                                <div class="description"><?php echo trim($item['content']); ?></div>
                                            <?php } ?>
                                            <?php if ( !empty($item['name']) ) {

                                                $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                                if ( ! empty( $item['link']['url'] ) ) {
                                                    $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                                }
                                                echo trim($title);
                                            ?>
                                            <?php } ?>

                                            <?php if ( !empty($item['job']) ) { ?>
                                                <div class="job"><?php echo esc_html($item['job']); ?></div>
                                            <?php } ?>
                                            <?php if ( !empty($item['star']) ) { ?>
                                                <div class="star d-flex align-items-center">
                                                    <span class="text"><?php echo number_format($item['star'], 1, '.', ''); ?> </span>
                                                    <div class="inner">
                                                        <div class="w-percent" style="width: <?php echo trim($item['star']*20)?>%;"></div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <?php } ?>
                    </div>
                <?php } elseif($layout_type == 'style2' ) {
                    $slides_to_scroll = !empty($slides_to_scroll) ? $slides_to_scroll : $columns;
                    $slides_to_scroll_tablet = !empty($slides_to_scroll_tablet) ? $slides_to_scroll_tablet : $slides_to_scroll;
                    $slides_to_scroll_mobile = !empty($slides_to_scroll_mobile) ? $slides_to_scroll_mobile : 1;
                ?>

                    <div class="slick-carousel show-text <?php echo trim( ($columns >= count($testimonials))?'hidden-dots':'' ); ?>" 
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
                        data-pagination="<?php echo esc_attr($show_pagination ? 'true' : 'false'); ?>" data-nav="<?php echo esc_attr($show_nav ? 'true' : 'false'); ?>" data-infinite="true">
                        <?php foreach ($testimonials as $item) { ?>
                        <div class="item">

                            <div class="testimonials-item2">
                                    <div class="info-testimonials flex-grow-1">
                                        <?php if ( !empty($item['star']) ) { ?>
                                            <div class="star d-flex align-items-center">
                                                <span class="text"><?php echo number_format($item['star'], 1, '.', ''); ?> </span>
                                                <div class="inner">
                                                    <div class="w-percent" style="width: <?php echo trim($item['star']*20)?>%;"></div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <?php if ( !empty($item['content']) ) { ?>
                                            <div class="description"><?php echo trim($item['content']); ?></div>
                                        <?php } ?>

                                        <?php if ( isset( $item['img_src']['id'] ) && !empty($item['img_src']['id']) ) { ?>
                                            <div class="wrapper-avarta">
                                                <div class="avarta d-inline-flex justify-content-center align-items-center">
                                                    <?php echo nerf_get_attachment_thumbnail($item['img_src']['id'], 'full'); ?>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <?php if ( !empty($item['name']) ) {
                                            $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                            if ( ! empty( $item['link']['url'] ) ) {
                                                $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                            }
                                            echo trim($title);
                                        ?>
                                        <?php } ?>
                                        <?php if ( !empty($item['job']) ) { ?>
                                            <div class="job"><?php echo esc_html($item['job']); ?></div>
                                        <?php } ?>

                                    </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                <?php } elseif($layout_type == 'style3' ) { ?>
                    <div class="slick-carousel testimonial-main" data-items="1" data-large="1" data-medium="1" data-small="1" data-smallest="1" data-pagination="false" data-nav="false" data-asnavfor=".testimonial-thumbnail" data-slickparent="true">
                        <?php foreach ($testimonials as $item) { ?>
                            <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                            
                            <div class="testimonials-item2 text-center">
                                <?php if ( !empty($item['content']) ) { ?>
                                    <div class="description"><?php echo trim($item['content']); ?></div>
                                <?php } ?>
                                <?php if ( !empty($item['name']) ) {
                                    $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                    if ( ! empty( $item['link']['url'] ) ) {
                                        $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                    }
                                    echo trim($title);
                                ?>
                                <?php } ?>
                                <?php if ( !empty($item['job']) ) { ?>
                                    <div class="job"><?php echo esc_html($item['job']); ?></div>
                                <?php } ?>

                            </div>
                        <?php } ?>
                    </div>

                    <div class="wrapper-testimonial-thumbnail">
                        <div class="slick-carousel testimonial-thumbnail" data-centerpadding='0px' data-centermode="true" data-items="5" data-large="5" data-medium="5" data-small="3" data-smallest="3" data-pagination="false" data-nav="false" data-asnavfor=".testimonial-main" data-slidestoscroll="1" data-focusonselect="true" data-infinite="true">
                            <?php foreach ($testimonials as $item) { ?>
                                <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                                <?php if ( $img_src ) { ?>
                                    <div class="wrapper-avarta">
                                        <div class="avarta">
                                            <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr(!empty($item['name']) ? $item['name'] : ''); ?>">
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>                    
                <?php } ?>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Testimonials );
} else {
    Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Testimonials );
}