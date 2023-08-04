<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Brands extends Widget_Base {

	public function get_name() {
        return 'apus_element_brands';
    }

	public function get_title() {
        return esc_html__( 'Apus Brands', 'nerf' );
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
            'title',
            [
                'label' => esc_html__( 'Title', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_html__( 'Enter your title here', 'nerf' ),
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'list_title', [
                'label' => esc_html__( 'Title', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Brand Title' , 'nerf' ),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'img_src',
            [
                'name' => 'image',
                'label' => esc_html__( 'Brand Image', 'nerf' ),
                'type' => Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Brand Image', 'nerf' ),
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => esc_html__( 'Link', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => esc_html__( 'Enter your brand link here', 'nerf' ),
            ]
        );

        $this->add_control(
            'brands',
            [
                'label' => esc_html__( 'Brands', 'nerf' ),
                'type' => Controls_Manager::REPEATER,
                'placeholder' => esc_html__( 'Enter your brands here', 'nerf' ),
                'fields' => $repeater->get_controls(),
            ]
        );
        
        $this->add_control(
            'layout',
            [
                'label' => esc_html__( 'Layout', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'grid' => esc_html__('Grid', 'nerf'),
                    'carousel' => esc_html__('Carousel', 'nerf'),
                ),
                'default' => 'carousel'
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
                'default' => 6,
            ]
        );

        $this->add_responsive_control(
            'slides_to_scroll',
            [
                'label' => esc_html__( 'Slides to Scroll', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'nerf' ),
                'options' => $columns,
                'condition' => [
                    'columns!' => '1',
                    'layout' => 'carousel',
                ],
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
                'condition' => [
                    'layout' => 'carousel',
                ],
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
                'condition' => [
                    'layout' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Style 1', 'nerf'),
                    'st2' => esc_html__('Style 2', 'nerf'),
                    'st3' => esc_html__('Style 3', 'nerf'),
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

    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        if ( !empty($brands) ) {
            
            ?>
            <div class="widget-brand <?php echo esc_attr($el_class.' '.$layout.' '.$style); ?>">
                <?php if ( $title ) { ?>
                    <div class="top-info">
                        <h2 class="title"><?php echo esc_html($title); ?></h2>
                    </div>
                <?php } ?>
                <div class="content-inner">
                    <?php if ( $layout == 'grid' ) {
                        $mdcol = 12/$columns;
                        $smcol = 12/$columns_tablet;
                        $xscol = 12/$columns_mobile;
                    ?>
                        <div class="row">
                            <?php foreach ($brands as $brand) { ?>
                                <?php
                                    $img_src = ( isset( $brand['img_src']['id'] ) && $brand['img_src']['id'] != 0 ) ? wp_get_attachment_url( $brand['img_src']['id'] ) : '';
                                    if ( $img_src ) {
                                ?>
                                        <div class="col-xl-<?php echo esc_attr($mdcol); ?> col-md-<?php echo esc_attr($smcol); ?> col-<?php echo esc_attr($xscol); ?>">
                                            <div class="brand-item d-flex align-items-center justify-content-center">
                                                <?php if ( !empty($brand['link']) ) { ?>
                                                    <a href="<?php echo esc_url($brand['link']); ?>" <?php echo (!empty($brand['title']) ? 'title="'.esc_attr($brand['title']).'"' : ''); ?>>
                                                <?php } ?>
                                                    <img src="<?php echo esc_url($img_src); ?>" <?php echo (!empty($brand['title']) ? 'alt="'.esc_attr($brand['title']).'"' : 'alt="'.esc_attr__('Image', 'nerf').'"'); ?>>
                                                <?php if ( !empty($brand['link']) ) { ?>
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } else {
                        $columns = !empty($columns) ? $columns : 3;
                        $columns_tablet = !empty($columns_tablet) ? $columns_tablet : 2;
                        $columns_mobile = !empty($columns_mobile) ? $columns_mobile : 1;
                        
                        $slides_to_scroll = !empty($slides_to_scroll) ? $slides_to_scroll : $columns;
                        $slides_to_scroll_tablet = !empty($slides_to_scroll_tablet) ? $slides_to_scroll_tablet : $slides_to_scroll;
                        $slides_to_scroll_mobile = !empty($slides_to_scroll_mobile) ? $slides_to_scroll_mobile : 1;
                    ?>
                        <div class="slick-carousel"
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

                            data-pagination="<?php echo esc_attr($show_pagination ? 'true' : 'false'); ?>" data-nav="<?php echo esc_attr($show_nav ? 'true' : 'false'); ?>"

                            >
                            <?php foreach ($brands as $brand) { ?>
                                <?php
                                    $img_src = ( isset( $brand['img_src']['id'] ) && $brand['img_src']['id'] != 0 ) ? wp_get_attachment_url( $brand['img_src']['id'] ) : '';
                                    if ( $img_src ) {
                                ?>  
                                    <div class="item">
                                        <div class="brand-item d-flex align-items-center justify-content-center">
                                            <?php if ( !empty($brand['link']) ) { ?>
                                                <a href="<?php echo esc_url($brand['link']); ?>" <?php echo (!empty($brand['title']) ? 'title="'.esc_attr($brand['title']).'"' : ''); ?>>
                                            <?php } ?>
                                                <img src="<?php echo esc_url($img_src); ?>" <?php echo (!empty($brand['title']) ? 'alt="'.esc_attr($brand['title']).'"' : 'alt="'.esc_attr__('Image', 'nerf').'"'); ?>>
                                            <?php if ( !empty($brand['link']) ) { ?>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Brands );
} else {
    Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Brands );
}