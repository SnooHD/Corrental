<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ovabrw_product_filter extends Widget_Base {

	public function get_name() {
		return 'ovabrw_product_filter';
	}

	public function get_title() {
		return esc_html__( 'Products Slider', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
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
			'content_section',
			[
				'label' => esc_html__( 'Content', 'ova-brw' ),
				'tab' 	=> \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'card_template',
				[
					'label' 	=> esc_html__( 'Card template', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'card1',
					'options' 	=> [
						'card1' => esc_html__( 'Card 1', 'ova-brw' ),
						'card2' => esc_html__( 'Card 2', 'ova-brw' ),
						'card3' => esc_html__( 'Card 3', 'ova-brw' ),
						'card4' => esc_html__( 'Card 4', 'ova-brw' ),
						'card5' => esc_html__( 'Card 5', 'ova-brw' ),
						'card6' => esc_html__( 'Card 6', 'ova-brw' ),
					],
				]
			);

			$args = array(
				'taxonomy' 	=> 'product_cat',
				'orderby' 	=> 'name',
				'order' 	=> 'ASC'
			);
  
		  	$categories 		= get_categories( $args );
		  	$category_args 		= [];
		  	$default_category 	= [];
		  	
		  	if ( ! empty( $categories ) && is_array( $categories ) ) {
			  	foreach ( $categories as $k => $category ) {
				  	$category_args[$category->term_id] = $category->name;

				  	if ( $k <= 3 ) array_push( $default_category, $category->term_id );
			  	}
		  	} else {
			  	$category_args[''] = esc_html__( 'Category not found', 'ova-brw' );
		  	}

		  	$this->add_control(
				'categories',
				[
					'label' 		=> esc_html__( 'Select Category', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SELECT2,
					'label_block' 	=> true,
					'multiple' 		=> true,
					'options' 		=> $category_args,
					'default' 		=> $default_category,
				]
			);

			$this->add_control(
				'posts_per_page',
				[
					'label'   => esc_html__( 'Posts per page', 'ova-brw' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'min'     => -1,
					'default' => 6,
				]
			);

			$this->add_control(
				'orderby',
				[
					'label' 	=> esc_html__( 'Order By', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'ID',
					'options' 	=> [
						'ID'  			=> esc_html__( 'ID', 'ova-brw' ),
						'title' 		=> esc_html__( 'Title', 'ova-brw' ),
						'date' 			=> esc_html__( 'Date', 'ova-brw' ),
						'modified' 		=> esc_html__('Modified', 'ova-brw'),
						'rand' 			=> esc_html__('Random', 'ova-brw'),
						'menu_order' 	=> esc_html__( 'Menu Order', 'ova-brw' )
					],
				]
			);

			$this->add_control(
				'order',
				[
					'label' 	=> esc_html__( 'Order', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'default' 	=> 'DESC',
					'options' 	=> [
						'ASC'  	=> esc_html__( 'Ascending', 'ova-brw' ),
						'DESC'  => esc_html__( 'Descending', 'ova-brw' ),
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_options',
			[
				'label' => esc_html__( 'Additional Options Slider', 'ova-brw' ),
			]
		);

			$this->add_control(
				'item_number',
				[
					'label' 	=> esc_html__( 'Item number', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'default' 	=> 3,
				]
			);

			$this->add_control(
				'slides_to_scroll',
				[
					'label'       => esc_html__( 'Slides to Scroll', 'ova-brw' ),
					'type'        => \Elementor\Controls_Manager::NUMBER,
					'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'ova-brw' ),
					'default'     => 1,
				]
			);

			$this->add_control(
				'margin_item',
				[
					'label' 	=> esc_html__( 'Margin item', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'default' 	=> 25,
				]
			);

			$this->add_control(
				'pause_on_hover',
				[
					'label'   => esc_html__( 'Pause on Hover', 'ova-brw' ),
					'type'    => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'ova-brw' ),
						'no'  => esc_html__( 'No', 'ova-brw' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'infinite',
				[
					'label'   => esc_html__( 'Infinite Loop', 'ova-brw' ),
					'type'    => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'false',
					'options' => [
						'yes' => esc_html__( 'Yes', 'ova-brw' ),
						'no'  => esc_html__( 'No', 'ova-brw' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay',
				[
					'label'   => esc_html__( 'Autoplay', 'ova-brw' ),
					'type'    => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'false',
					'options' => [
						'yes' => esc_html__( 'Yes', 'ova-brw' ),
						'no'  => esc_html__( 'No', 'ova-brw' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay_speed',
				[
					'label'     => esc_html__( 'Autoplay Speed', 'ova-brw' ),
					'type'      => \Elementor\Controls_Manager::NUMBER,
					'default'   => 3000,
					'step'      => 500,
					'condition' => [
						'autoplay' => 'yes',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'smartspeed',
				[
					'label'   => esc_html__( 'Smart Speed', 'ova-brw' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'default' => 500,
				]
			);

			$this->add_control(
				'nav_control',
				[
					'label'   => esc_html__( 'Show Nav', 'ova-brw' ),
					'type'    => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'ova-brw' ),
						'no'  => esc_html__( 'No', 'ova-brw' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'dots_control',
				[
					'label'   => esc_html__( 'Show Dots', 'ova-brw' ),
					'type'    => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'yes',
					'options' => [
						'yes' => esc_html__( 'Yes', 'ova-brw' ),
						'no'  => esc_html__( 'No', 'ova-brw' ),
					],
					'frontend_available' => true,
				]
			);


		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

		// Responsive
		$responsive = [
			'0' => array(
        		'items' 	=> 1,
        		'nav' 		=> false,
        		'slideBy' 	=> 1,
        	),
        	'769' => array(
        		'items' 	=> 2,
        		'nav' 		=> false,
        		'slideBy' 	=> 1,
        	),
        	'1024' => array(
        		'items' 	=> 3,
        		'nav' 		=> false,
        		'slideBy' 	=> 1,
        	),
        	'1200' => array(
        		'items' 	=> $settings['item_number'],
        		'nav' 		=> true,
        		'slideBy' 	=> 1,
        	),
		];

		if ( in_array( $settings['card_template'] , ['card5', 'card6'] ) ) {
			$responsive = [
				'0' => array(
	        		'items' 	=> 1,
	        		'nav' 		=> false,
	        		'slideBy' 	=> 1,
	        	),
	        	'769' => array(
	        		'items' 	=> 1,
	        		'nav' 		=> false,
	        		'slideBy' 	=> 1,
	        	),
	        	'1024' => array(
	        		'items' 	=> 1,
	        		'nav' 		=> false,
	        		'slideBy' 	=> 1,
	        	),
			];
		}

		// Data
		$args = [
			'template' 			=> $settings['card_template'],
			'categories' 		=> $settings['categories'],
			'posts_per_page' 	=> $settings['posts_per_page'],
			'orderby' 			=> $settings['orderby'],
			'order' 			=> $settings['order'],
			'slide_options' 	=> [
				'items' 				=> $settings['item_number'],
				'slideBy' 				=> $settings['slides_to_scroll'],
				'margin' 				=> $settings['margin_item'],
				'autoplayTimeout' 		=> $settings['autoplay_speed'],
				'smartSpeed' 			=> $settings['smartspeed'],
				'autoplayHoverPause' 	=> $settings['pause_on_hover'] === 'yes' ? true : false,
				'loop' 					=> $settings['infinite'] === 'yes' ? true : false,
				'autoplay' 				=> $settings['autoplay'] === 'yes' ? true : false,
				'nav' 					=> $settings['nav_control'] === 'yes' ? true : false,
				'dots' 					=> $settings['dots_control'] === 'yes' ? true : false,
				'rtl' 					=> is_rtl() ? true : false,
				'nav_left'              => 'brwicon-left',
	        	'nav_right'             => 'brwicon-right-1',
				'responsive' 			=> $responsive,
			],
		];

		ovabrw_get_template( 'elementor/ovabrw-product-filter.php', $args );
	}
}