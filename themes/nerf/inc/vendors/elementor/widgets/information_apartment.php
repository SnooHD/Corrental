<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Information_Apartment extends Widget_Base {

	public function get_name() {
        return 'apus_element_information_apartment';
    }

	public function get_title() {
        return esc_html__( 'Apus Information Apartment', 'nerf' );
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
                'label' => esc_html__( 'Information', 'nerf' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => '',
            ]
        );

        $repeater->add_control(
            'value',
            [
                'label' => esc_html__( 'Value', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => '',

            ]
        );

        $this->add_control(
            'features',
            [
                'label' => esc_html__( 'List Information', 'nerf' ),
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

        if ( !empty($features) ) {
            ?>
            <div class="widget-information-apartment <?php echo esc_attr($el_class); ?>">
                <?php foreach ($features as $item): ?>
                    <div class="d-flex align-items-center list-info">
                        <?php if ( !empty($item['title']) ) { ?>
                            <div class="title-info"><?php echo trim($item['title']); ?></div>
                        <?php } ?>
                        <?php if ( !empty($item['value']) ) { ?>
                            <div class="value ms-auto"><?php echo trim($item['value']); ?></div>
                        <?php } ?>
                    </div>        
                <?php endforeach; ?>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Information_Apartment );
} else {
    Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Information_Apartment );
}