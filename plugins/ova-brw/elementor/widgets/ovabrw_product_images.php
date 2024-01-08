<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ovabrw_product_images extends Widget_Base {
	public function get_name() {		
		return 'ovabrw_product_images';
	}

	public function get_title() {
		return esc_html__( 'Product Images', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-product-images';
	}

	public function get_categories() {
		return [ 'ovabrw-product' ];
	}

	public function get_script_depends() {
		// Fancybox
		wp_enqueue_script('fancybox', OVABRW_PLUGIN_URI.'/assets/libs/fancybox/fancybox.umd.js', array('jquery'),null,true);
		wp_enqueue_style('fancybox', OVABRW_PLUGIN_URI.'/assets/libs/fancybox/fancybox.css', array(), null);

		// Carosel
		wp_enqueue_style( 'carousel', OVABRW_PLUGIN_URI.'assets/libs/carousel/owl.carousel.min.css' );
		wp_enqueue_script( 'carousel', OVABRW_PLUGIN_URI.'assets/libs/carousel/owl.carousel.min.js', array('jquery'), false, true );

		// BRW icon
	    if ( apply_filters( 'ovabrw_use_brwicon', true ) ) {
	    	wp_enqueue_style( 'brwicon', OVABRW_PLUGIN_URI.'assets/libs/flaticon/brwicon/font/flaticon_brw.css', array(), null);
	    }
		    
		return [ 'ova-script-elementor' ];
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
			'section_product_gallery_style',
			[
				'label' 	=> esc_html__( 'Style', 'ova-brw' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'product_template' => 'classic'
				]
			]
		);

			$this->add_control(
				'wc_style_warning',
				[
					'type' 	=> Controls_Manager::RAW_HTML,
					'raw' 	=> esc_html__( 'The style of this widget is often affected by your theme and <p></p>lugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'image_border',
					'selector' 	=> '.woocommerce {{WRAPPER}} .woocommerce-product-gallery .flex-viewport',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'image_border_radius',
				[
					'label' 	 => esc_html__( 'Border Radius', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .woocommerce-product-gallery .flex-viewport' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'spacing',
				[
					'label' 	 => esc_html__( 'Spacing Image', 'ova-brw' ),
					'type' 		 => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .woocommerce-product-gallery .flex-viewport' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'heading_thumbs_style',
				[
					'label' 	=> esc_html__( 'Thumbnails', 'ova-brw' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'thumbs_border',
					'selector' 	=> '.woocommerce {{WRAPPER}} .flex-control-thumbs img',
				]
			);

			$this->add_responsive_control(
				'thumbs_border_radius',
				[
					'label' 	 => esc_html__( 'Border Radius', 'ova-brw' ),
					'type' 		 => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .flex-control-thumbs img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'spacing_thumbs',
				[
					'label' 	 => esc_html__( 'Spacing Thumbnails', 'ova-brw' ),
					'type' 		 => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em' ],
					'selectors'  => [
						'.woocommerce {{WRAPPER}} .flex-control-thumbs li' => 'padding-right: calc({{SIZE}}{{UNIT}} / 2); padding-left: calc({{SIZE}}{{UNIT}} / 2); padding-bottom: {{SIZE}}{{UNIT}}',
						'.woocommerce {{WRAPPER}} .flex-control-thumbs' => 'margin-right: calc(-{{SIZE}}{{UNIT}} / 2); margin-left: calc(-{{SIZE}}{{UNIT}} / 2)',
					],
				]
			);

			$this->add_responsive_control(
				'thumbnails_align',
				[
					'label' => esc_html__( 'Alignment', 'ova-brw' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'flex-start' => [
							'title' => esc_html__( 'Left', 'ova-brw' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'ova-brw' ),
							'icon' => 'eicon-text-align-center',
						],
						'flex-end' => [
							'title' => esc_html__( 'Right', 'ova-brw' ),
							'icon' => 'eicon-text-align-right',
						],
						'space-between' => [
							'title' => esc_html__( 'Justified', 'ova-brw' ),
							'icon' 	=> 'eicon-text-align-justify',
						],
					],
					'selectors' => [
						'.woocommerce {{WRAPPER}} .woocommerce-product-gallery .flex-control-nav.flex-control-thumbs' => 'justify-content: {{VALUE}}',
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
		
		if ( ! $product || ! $product->is_type('ovabrw_car_rental') || ( ! is_product() && $template == 'classic' ) ) {
			?>
			<div class="ovabrw_elementor_no_product">
				<span><?php echo $this->get_title(); ?></span>
			</div>
			<?php
			return;
		}

		if ( $template === 'modern' ): ?>
			<div class="elementor-product-image ovabrw-modern-product">
				<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-images.php' ); ?>
				<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-is-featured.php' );	 ?>
			</div>
		<?php else: ?>
			<div class="elementor-product-image">
				<?php wc_get_template( 'single-product/product-image.php' ); ?>
			</div>
		<?php endif;
	}
}