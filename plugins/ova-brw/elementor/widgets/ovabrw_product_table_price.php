<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ovabrw_product_table_price extends Widget_Base {

	public function get_name() {		
		return 'ovabrw_product_table_price';
	}

	public function get_title() {
		return esc_html__( 'Product Table Price', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-price-table';
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
			'section_price_table_style',
			[
				'label' 	=> esc_html__( 'Title', 'ova-brw' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'product_template' => 'classic'
				]
			]
		);

			$this->add_control(
				'wc_style_warning',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'title_color',
				[
					'label'  => esc_html__( 'Color', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw-according' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'title_typography',
					'selector' 	=> '{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw-according',
				]
			);

			$this->add_responsive_control(
				'title_padding',
				[
					'label' 	 => esc_html__( 'Padding', 'ova-framework' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw-according' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'title_margin',
				[
					'label' 	 => esc_html__( 'Margin', 'ova-framework' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw-according' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			[
				'label' 	=> esc_html__( 'Content', 'ova-brw' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'product_template' => 'classic'
				]
			]
		);

			$this->add_control(
				'wc_style_warning_content',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'content_title_color',
				[
					'label'  => esc_html__( 'Color Title', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table label' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'content_title_typography',
					'selector' 	=> '{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table label',
				]
			);

			$this->add_responsive_control(
				'content_title_padding',
				[
					'label' 	 => esc_html__( 'Padding', 'ova-framework' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'content_title_margin',
				[
					'label' 	 => esc_html__( 'Margin', 'ova-framework' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_table_value_style',
			[
				'label' 	=> esc_html__( 'Table', 'ova-brw' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'product_template' => 'classic'
				]
			]
		);

			$this->add_control(
				'wc_style_warning_table',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'content_title_table_bg',
				[
					'label'  => esc_html__( 'Background', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'content_title_table_color',
				[
					'label'  => esc_html__( 'Color Title', 'ova-brw' ),
					'type' 	 => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead tr th' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'content_title_table_typography',
					'selector' 	=> '{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead tr th',
				]
			);

			$this->add_responsive_control(
				'content_title_table_padding',
				[
					'label' 	 => esc_html__( 'Padding', 'ova-framework' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead tr th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'content_title_table_margin',
				[
					'label' 	 => esc_html__( 'Margin', 'ova-framework' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead tr th' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_table_style',
			[
				'label' 	=> esc_html__( 'Table Value', 'ova-brw' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'product_template' => 'classic'
				]
			]
		);

			$this->add_control(
				'wc_style_warning_table_value',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'table_value_color',
				[
					'label'  	=> esc_html__( 'Color', 'ova-brw' ),
					'type' 	 	=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table tbody tr td' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'table_value_typography',
					'selector' 	=> '{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table tbody tr td',
				]
			);

			$this->add_responsive_control(
				'table_value_padding',
				[
					'label' 	 => esc_html__( 'Padding', 'ova-framework' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table tbody tr td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'table_value_margin',
				[
					'label' 	 => esc_html__( 'Margin', 'ova-framework' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table tbody tr td' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-table-price.php' ); ?>
			</div>
		<?php else:
			$product_id = $product->get_id();
		?>
			<div class="elementor-table-price">
				<?php ovabrw_get_template( 'single/table_price.php', array( 'id' => $product_id ) ); ?>
			</div>
		<?php endif;
	}
}