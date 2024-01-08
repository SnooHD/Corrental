<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ovabrw_product_tabs extends Widget_Base {

	public function get_name() {		
		return 'ovabrw_product_tabs';
	}

	public function get_title() {
		return esc_html__( 'Product Tabs', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-product-tabs';
	}

	public function get_categories() {
		return [ 'ovabrw-product' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'ova-brw' ),
			]
		);

			$this->add_control(
				'product_template',
				[
					'label' 		=> esc_html__( 'Choose Template', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SELECT,
					'default' 		=> 'modern',
					'description' 	=> esc_html__( 'This widget just works on the product detail page', 'ova-brw' ),
					'options' 		=> [
						'modern' 	=> esc_html__( 'Modern', 'ova-brw' ),
						'classic' 	=> esc_html__( 'Classic', 'ova-brw' )
					]
				]
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();
		$template = $settings['product_template'];

		global $product;

    	if ( ! $product || ! $product->is_type('ovabrw_car_rental') ) {
			?>
			<div class="ovabrw_elementor_no_product">
				<span><?php echo $this->get_title(); ?></span>
			</div>
			<?php
			return;
		}

		if ( $template === 'modern' ):
			add_filter( 'ovabrw_ft_show_request_booking_in_product_tabs', '__return_false' );
		?>
			<div class="ovabrw-modern-product">
				<?php wc_get_template( 'single-product/tabs/tabs.php' ); ?>
			</div>
		<?php else: ?>
			<div class="elementor-tabs">
				<?php wc_get_template( 'single-product/tabs/tabs.php' ); ?>
			</div>
		<?php endif;
	}
}