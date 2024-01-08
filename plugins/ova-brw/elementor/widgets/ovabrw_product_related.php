<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ovabrw_product_related extends Widget_Base {

	public function get_name() {		
		return 'ovabrw_product_related';
	}

	public function get_title() {
		return esc_html__( 'Product Related', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-product-related';
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

			$this->add_control(
				'posts_per_page',
				[
					'label' 	=> esc_html__( 'Products Per Page', 'ova-brw' ),
					'type' 		=> Controls_Manager::NUMBER,
					'default' 	=> 3,
				]
			);

			$this->add_responsive_control(
				'columns',
				[
					'label' 	=> esc_html__( 'Columns', 'ova-brw' ),
					'type' 		=> Controls_Manager::NUMBER,
					'default' 	=> 3,
				]
			);

			$this->add_control(
				'orderby',
				[
					'label' => esc_html__( 'Order By', 'ova-brw' ),
					'type' 	=> Controls_Manager::SELECT,
					'default' => 'date',
					'options' => [
						'date' 			=> esc_html__( 'Date', 'ova-brw' ),
						'title' 		=> esc_html__( 'Title', 'ova-brw' ),
						'price' 		=> esc_html__( 'Price', 'ova-brw' ),
						'popularity' 	=> esc_html__( 'Popularity', 'ova-brw' ),
						'rating' 		=> esc_html__( 'Rating', 'ova-brw' ),
						'rand' 			=> esc_html__( 'Random', 'ova-brw' ),
						'menu_order' 	=> esc_html__( 'Menu Order', 'ova-brw' ),
					],
				]
			);

			$this->add_control(
				'order',
				[
					'label' => esc_html__( 'Order', 'ova-brw' ),
					'type' 	=> Controls_Manager::SELECT,
					'default' => 'desc',
					'options' => [
						'asc' 	=> esc_html__( 'ASC', 'ova-brw' ),
						'desc' 	=> esc_html__( 'DESC', 'ova-brw' ),
					],
				]
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings 	= $this->get_settings();
		$product_id = $settings['product_id'];
		$template 	= $settings['product_template'];

		$args = [
			'posts_per_page' 	=> $settings['posts_per_page'],
			'columns' 			=> $settings['columns'],
			'orderby' 			=> $settings['orderby'],
			'order' 			=> $settings['order'],
		];

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
			<div class="ovabrw-modern-product ovabrw-product-related-widget">
				<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-related.php', $args ); ?>
			</div>
		<?php else:
			setup_postdata( $product->get_id() );

			if ( ! empty( $settings['posts_per_page'] ) ) {
				$args['posts_per_page'] = $settings['posts_per_page'];
			}

			if ( ! empty( $settings['columns'] ) ) {
				$args['columns'] = $settings['columns'];
			}

			// Get visible related products then sort them at random.
			$args['related_products'] = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $args['posts_per_page'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );

			// Handle orderby.
			$args['related_products'] = wc_products_array_orderby( $args['related_products'], $args['orderby'], $args['order'] );
		?>
			<div class="elementor-ralated">
				<?php wc_get_template( 'single-product/related.php', $args ); ?>
			</div>
		<?php endif;
	}
}