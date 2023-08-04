<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Availability extends Widget_Base {

	public function get_name() {
        return 'apus_element_availability';
    }

	public function get_title() {
        return esc_html__( 'Apus Availability', 'nerf' );
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
            'title', [
                'label' => esc_html__( 'Title', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'bed_bath', [
                'label' => esc_html__( 'Bed/Bath', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'sqft', [
                'label' => esc_html__( 'SQFT', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'sale_price', [
                'label' => esc_html__( 'Sale Price', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'rent_price', [
                'label' => esc_html__( 'Rent Price', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'fllor_plan', [
                'label' => esc_html__( 'Floor Plan', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'items',
            [
                'label' => esc_html__( 'Items', 'nerf' ),
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
                    '' => esc_html__('Default', 'nerf'),
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

        if ( !empty($items) ) {
            ?>
            <div class="widget-availability <?php echo esc_attr($el_class.' '.$style); ?>">
                <div class="table-responsive">
                    <table class="table-availability">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Residence', 'nerf'); ?></th>
                                <th><?php esc_html_e('Bed/Bath', 'nerf'); ?></th>
                                <th><?php esc_html_e('Sqft', 'nerf'); ?></th>
                                <th><?php esc_html_e('Sale Price', 'nerf'); ?></th>
                                <th><?php esc_html_e('Rent Price', 'nerf'); ?></th>
                                <th><?php esc_html_e('Floor Plan', 'nerf'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item) { ?>
                                <tr>
                                    <td>
                                        <?php if( !empty($item['title']) ) { ?>
                                            <?php echo trim($item['title']); ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if( !empty($item['bed_bath']) ) { ?>
                                            <?php echo trim($item['bed_bath']); ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if( !empty($item['sqft']) ) { ?>
                                            <?php echo trim($item['sqft']); ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if( !empty($item['sale_price']) ) { ?>
                                            <?php echo trim($item['sale_price']); ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if( !empty($item['rent_price']) ) { ?>
                                            <?php echo trim($item['rent_price']); ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if( !empty($item['fllor_plan']) ) { ?>
                                            <a href="<?php esc_url($item['fllor_plan']); ?>" class="view-floor-plan"><?php esc_html_e('VIEW NOW', 'nerf'); ?><i class="flaticon-up-right-arrow"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Availability );
} else {
    Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Availability );
}