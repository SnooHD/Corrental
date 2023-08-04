<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Gallery extends Widget_Base {

	public function get_name() {
        return 'apus_element_gallery';
    }

	public function get_title() {
        return esc_html__( 'Apus Gallery', 'nerf' );
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

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'image_title', [
                'label' => esc_html__( 'Title', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'img_src',
            [
                'name' => 'image',
                'label' => esc_html__( 'Image', 'nerf' ),
                'type' => Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Image', 'nerf' ),
            ]
        );

        $this->add_control(
            'images',
            [
                'label' => esc_html__( 'Images', 'nerf' ),
                'type' => Controls_Manager::REPEATER,
                'placeholder' => esc_html__( 'Enter your images here', 'nerf' ),
                'fields' => $repeater->get_controls(),
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
                    'style3' => esc_html__('Style 3', 'nerf'),
                    'style4' => esc_html__('Style 4', 'nerf'),
                ),
                'default' => 'style1'
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

        $this->add_responsive_control(
            'slides_to_scroll',
            [
                'label' => esc_html__( 'Slides to Scroll', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'nerf' ),
                'options' => $columns,
                'frontend_available' => true,
                'default' => 3,
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
            'section_nav_style',
            [
                'label' => esc_html__( 'Nav', 'nerf' ),
                'tab' => Controls_Manager::TAB_STYLE,
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
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-carousel .slick-arrow' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'nav_bg_color',
                [
                    'label' => esc_html__( 'Background Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-carousel .slick-arrow' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'nav_br_color',
                [
                    'label' => esc_html__( 'Border Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-carousel .slick-arrow' => 'border-color: {{VALUE}};',
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
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-carousel .slick-arrow:hover, {{WRAPPER}} .slick-carousel .slick-arrow:focus' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'nav_hv_bg_color',
                [
                    'label' => esc_html__( 'Background Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-carousel .slick-arrow:hover, {{WRAPPER}} .slick-carousel .slick-arrow:focus' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'nav_hv_br_color',
                [
                    'label' => esc_html__( 'Border Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-carousel .slick-arrow:hover, {{WRAPPER}} .slick-carousel .slick-arrow:focus' => 'border-color: {{VALUE}};',
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

        if ( !empty($images) ) {
            $columns = !empty($columns) ? $columns : 3;
            $columns_tablet = !empty($columns_tablet) ? $columns_tablet : 2;
            $columns_mobile = !empty($columns_mobile) ? $columns_mobile : 1;
            
            $slides_to_scroll = !empty($slides_to_scroll) ? $slides_to_scroll : $columns;
            $slides_to_scroll_tablet = !empty($slides_to_scroll_tablet) ? $slides_to_scroll_tablet : $slides_to_scroll;
            $slides_to_scroll_mobile = !empty($slides_to_scroll_mobile) ? $slides_to_scroll_mobile : 1;
            $rand = rand(0000,9999);
            ?>
            <div class="widget-gallery-images <?php echo esc_attr($el_class.' '.$style); ?>">
                
                <div class="slick-carousel <?php echo esc_attr( $fullscreen ? 'fullscreen' : 'nofullscreen' ); ?>"
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
                    <?php if($style == 'style3') { ?>
                        data-centerMode ="true"
                        data-infinite ="true"
                        data-centerPadding ="0px"
                    <?php } ?>
                    data-pagination="<?php echo esc_attr($show_pagination ? 'true' : 'false'); ?>" data-nav="<?php echo esc_attr($show_nav ? 'true' : 'false'); ?>">
                    <?php foreach ($images as $image) { ?>
                        <?php
                            $img_src = ( isset( $image['img_src']['id'] ) && $image['img_src']['id'] != 0 ) ? wp_get_attachment_url( $image['img_src']['id'] ) : '';
                            if ( $img_src ) {
                        ?>  
                            <div class="item">
                                <div class="image-item position-relative">
                                    
                                    <?php if( !empty($image['image_title']) ) { ?>
                                        <h4 class="title"><?php echo trim($image['image_title']); ?> </h4>
                                    <?php } ?>
                                    <a class="d-block position-relative action-img" href="<?php echo esc_url($img_src); ?>" data-elementor-lightbox-slideshow="nerf-gallery-<?php echo esc_attr($rand); ?>">
                                        <img src="<?php echo esc_url($img_src); ?>" <?php echo (!empty($image['image_title']) ? 'alt="'.esc_attr($image['image_title']).'"' : 'alt="'.esc_attr__('Image', 'nerf').'"'); ?>>
                                    </a>
                                    <?php if($style == 'style3') { ?>
                                        <span class="drag d-flex align-items-center justify-content-center"><?php esc_html_e('DRAG','nerf') ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Gallery );
} else {
    Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Gallery );
}