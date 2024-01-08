<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ovabrw_product_map extends Widget_Base {
	public function get_name() {		
		return 'ovabrw_product_map';
	}

	public function get_title() {
		return esc_html__( 'Product Map', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-google-maps';
	}

	public function get_categories() {
		return [ 'ovabrw-product' ];
	}

	public function get_script_depends() {
		// Map
		if ( get_option( 'ova_brw_google_key_map', false ) ) {
			wp_enqueue_script( 'google_map','https://maps.googleapis.com/maps/api/js?key='.get_option( 'ova_brw_google_key_map', '' ).'&callback=Function.prototype&libraries=places', false, true );
		} else {
			wp_enqueue_script( 'google_map','//maps.googleapis.com/maps/api/js?sensor=false&callback=Function.prototype&libraries=places', array('jquery'), false, true );
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
				'zoom',
				[
					'label' 	=> esc_html__( 'Zoom', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'default' 	=> 17,
				]
			);

			$this->add_control(
				'height',
				[
					'label' 	=> esc_html__( 'Height', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', 'custom' ],
					'range' => [
						'px' => [
							'min' 	=> 0,
							'max' 	=> 1000,
							'step' 	=> 5,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 500,
					],
					'selectors' => [
						'{{WRAPPER}} .ovabrw-product-map #ovabrw-show-map' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings 	= $this->get_settings();
		$product_id = $settings['product_id'];

		global $product;

		if ( ! $product ) {
			$product = wc_get_product( $product_id );
		}

		$product_id = $product->get_id();

    	if ( ! $product || ! $product->is_type('ovabrw_car_rental') ) {
			?>
			<div class="ovabrw_elementor_no_product">
				<span><?php echo $this->get_title(); ?></span>
			</div>
			<?php
			return;
		}

		ovabrw_get_template( 'elementor/ovabrw-product-map.php', ['id' => $product_id, 'zoom' => $settings['zoom']] );
	}
}