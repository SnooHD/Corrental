<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ovabrw_product_features extends Widget_Base {
	public function get_name() {		
		return 'ovabrw_product_features';
	}

	public function get_title() {
		return esc_html__( 'Product Features', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-product-meta';
	}

	public function get_categories() {
		return [ 'ovabrw-product' ];
	}

	protected function register_controls() {
		
		$this->start_controls_section(
			'section_demo',
			[
				'label' => esc_html__( 'Demo', 'ova-brw' ),
			]
		);
			$arr_product 	= array( '0' => esc_html__( 'Choose Product', 'ova-brw' ) );
			$products 		= ovabrw_get_products_rental();

			if ( ! empty( $products ) && is_array( $products ) ) {
				foreach( $products as $product_id ) {
					$arr_product[$product_id] = get_the_title( $product_id );
				}
			} else {
				$arr_product[''] = esc_html__( 'There are no rental products', 'ova-brw' );
			}

			$this->add_control(
				'product_id',
				[
					'label' 	=> esc_html__( 'Choose Product', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> '0',
					'options' 	=> $arr_product,
				]
			);

			$this->add_control(
				'product_template',
				[
					'label' 	=> esc_html__( 'Choose Template', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'modern',
					'options' 	=> [
						'modern' 	=> esc_html__( 'Modern', 'ova-brw' ),
						'classic' 	=> esc_html__( 'Classic', 'ova-brw' )
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_features_style',
			[
				'label' 	=> esc_html__( 'Features', 'ova-brw' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'product_template' => 'classic'
				]
			]
		);

			$this->add_control(
				'features_display',
				[
					'label' => esc_html__( 'Display', 'ova-brw' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'block',
					'options' => [
						'block' 		=> esc_html__( 'Block', 'ova-brw' ),
						'inline-block' 	=> esc_html__( 'Auto', 'ova-brw' ),
					],
					'selectors' 	=> [
						'{{WRAPPER}} ul.ovabrw_woo_features' => 'display: {{UNIT}};',
					],
				]
			);

			$this->add_control(
				'features_background',
				[
					'label' 	=> esc_html__( 'Background', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ul.ovabrw_woo_features' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'features_padding',
				[
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} ul.ovabrw_woo_features' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'features_margin',
				[
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} ul.ovabrw_woo_features' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_features_items_style',
			[
				'label' 	=> esc_html__( 'Items', 'ova-brw' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'product_template' => 'classic'
				]
			]
		);

			$this->add_control(
				'features_items_background',
				[
					'label' 	=> esc_html__( 'Background', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ul.ovabrw_woo_features li' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' 		=> 'features_items_border',
					'label' 	=> esc_html__( 'Border', 'ova-brw' ),
					'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li',
				]
			);

			$this->add_control(
				'features_items_border_first_options',
				[
					'label' 	=> esc_html__( 'Border Items (first)', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' 		=> 'features_items_border_first',
					'label' 	=> esc_html__( 'Border', 'ova-brw' ),
					'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li:first-child',
				]
			);

			$this->add_control(
				'features_items_border_last_options',
				[
					'label' 	=> esc_html__( 'Border Items (last)', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' 		=> 'features_items_border_last',
					'label' 	=> esc_html__( 'Border', 'ova-brw' ),
					'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li:last-child',
				]
			);

			$this->add_responsive_control(
				'features_items_padding',
				[
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} ul.ovabrw_woo_features li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'features_items_margin',
				[
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} ul.ovabrw_woo_features li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_features_icons_style',
			[
				'label' 	=> esc_html__( 'Icons', 'ova-brw' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'product_template' => 'classic'
				]
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' 		=> 'icon_typography',
					'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li i:before',
				]
			);

			$this->add_control(
				'icon_color',
				[
					'label' 	=> esc_html__( 'Color', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ul.ovabrw_woo_features li i:before' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' 		=> 'icon_border',
					'label' 	=> esc_html__( 'Border', 'ova-brw' ),
					'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li i:before',
				]
			);

			$this->add_responsive_control(
				'icon_padding',
				[
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} ul.ovabrw_woo_features li i:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'icon_margin',
				[
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} ul.ovabrw_woo_features li i:before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_features_label_style',
			[
				'label' 	=> esc_html__( 'Label', 'ova-brw' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'product_template' => 'classic'
				]
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' 		=> 'label_typography',
					'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li label',
				]
			);

			$this->add_control(
				'label_color',
				[
					'label' 	=> esc_html__( 'Color', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ul.ovabrw_woo_features li label' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'label_min_width',
				[
					'label' 		=> esc_html__( 'Min Width', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' 	=> 0,
							'max' 	=> 1000,
							'step' 	=> 5,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} ul.ovabrw_woo_features li label' => 'min-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' 		=> 'label_border',
					'label' 	=> esc_html__( 'Border', 'ova-brw' ),
					'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li label',
				]
			);

			$this->add_responsive_control(
				'label_padding',
				[
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} ul.ovabrw_woo_features li label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'label_margin',
				[
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} ul.ovabrw_woo_features li label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_features_value_style',
			[
				'label' 	=> esc_html__( 'Value', 'ova-brw' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'product_template' => 'classic'
				]
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' 		=> 'value_typography',
					'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li span',
				]
			);

			$this->add_control(
				'value_color',
				[
					'label' 	=> esc_html__( 'Color', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} ul.ovabrw_woo_features li span' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' 		=> 'value_border',
					'label' 	=> esc_html__( 'Border', 'ova-brw' ),
					'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li span',
				]
			);

			$this->add_responsive_control(
				'value_padding',
				[
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} ul.ovabrw_woo_features li span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'value_margin',
				[
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} ul.ovabrw_woo_features li span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings 	= $this->get_settings();
		$product_id = $settings['product_id'];
		$template 	= $settings['product_template'];

		global $product;

		if ( ! $product ) {
			$product = wc_get_product( $product_id );
		}

    	if ( ! $product || ! $product->is_type('ovabrw_car_rental') ) {
			?>
			<div class="ovabrw_elementor_no_product">
				<span><?php echo $this->get_title(); ?></span>
			</div>
			<?php
			return;
		}

		if ( $template === 'modern' ): ?>
			<div class="ovabrw-modern-product">
				<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-features.php' ); ?>
			</div>
		<?php else:
			$product_id = $product->get_id();
		?>
			<div class="elementor-features">
				<?php ovabrw_get_template( 'single/features.php', array( 'id' => $product_id ) ); ?>
			</div>
		<?php endif;
	}
}