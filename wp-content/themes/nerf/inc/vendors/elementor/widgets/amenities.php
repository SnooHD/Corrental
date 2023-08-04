<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Amenities extends Widget_Base {

    public function get_name() {
        return 'apus_element_amenities';
    }

    public function get_title() {
        return esc_html__( 'Apus Amenities', 'nerf' );
    }

    public function get_icon() {
        return 'eicon-price-list';
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


        $this->add_control(
            'amenities',
            [
                'label' => esc_html__( 'Amenities', 'nerf' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
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
        
        if ( !empty($amenities) ) {
            ?>
            <div class="widget-amenities <?php echo esc_attr($el_class); ?>">
                <div class="d-md-flex align-items-center">
                    <div class="wrapper-amenities-thumbnail d-none d-md-block col-md-6">
                        <div class="slick-carousel amenities-thumbnail m-0" data-items="1" data-large="1" data-medium="1" data-small="1" data-smallest="1" data-pagination="false" data-nav="false" data-asnavfor=".amenities-main" data-slidestoscroll="1" data-focusonselect="true" data-infinite="false">
                            <?php foreach ($amenities as $item) { ?>
                                <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                                <?php if ( $img_src ) { ?>
                                    <div class="amenities-banner p-0">
                                        <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr(!empty($item['name']) ? $item['name'] : ''); ?>">
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="slick-carousel amenities-main col-md-6 col-12 m-0" data-items="1" data-large="1" data-medium="1" data-small="1" data-smallest="1" data-pagination="false" data-nav="true" data-asnavfor=".amenities-thumbnail" data-slickparent="true">
                        <?php foreach ($amenities as $item) { ?>
                            <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                            
                            <div class="amenities-item p-0">
                                <?php if ( !empty($item['name']) ) {
                                    $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                    if ( ! empty( $item['link']['url'] ) ) {
                                        $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                    }
                                    echo trim($title);
                                ?>
                                <?php } ?>
                                <?php if ( !empty($item['content']) ) { ?>
                                    <div class="description"><?php echo trim($item['content']); ?></div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Amenities );
} else {
    Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Amenities );
}