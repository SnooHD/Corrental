<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Countdown extends Widget_Base {

	public function get_name() {
        return 'apus_element_countdown';
    }

	public function get_title() {
        return esc_html__( 'Apus Countdown', 'nerf' );
    }
    
	public function get_categories() {
        return [ 'nerf-elements' ];
    }

	protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Countdown', 'nerf' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'end_date', [
                'label' => esc_html__( 'End Date', 'nerf' ),
                'type' => Controls_Manager::DATE_TIME,
                'picker_options' => [
                    'enableTime' => false
                ]
            ]
        );

        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'style1' => esc_html__('Style 1', 'nerf'),
                ),
                'default' => 'style1'
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
            'section_title_style',
            [
                'label' => esc_html__( 'Style', 'nerf' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'box_color',
            [
                'label' => esc_html__( 'Color', 'nerf' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .times' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );
        $end_date = !empty($end_date) ? strtotime($end_date) : '';
        if ( $end_date ) {
            ?>
            <div class="widget-countdown <?php echo esc_attr($el_class.' '.$style); ?>">
                <div class="time-wrapper">
                    <div class="apus-countdown clearfix" data-time="timmer-elementor"
                        data-date="<?php echo date('m', $end_date).'-'.date('d', $end_date).'-'.date('Y', $end_date).'-'. date('H', $end_date) . '-' . date('i', $end_date) . '-' .  date('s', $end_date) ; ?>">
                    </div>
                </div>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Countdown );
} else {
    Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Countdown );
}