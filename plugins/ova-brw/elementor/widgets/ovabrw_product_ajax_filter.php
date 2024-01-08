<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ovabrw_product_ajax_filter extends Widget_Base {

	public function get_name() {
		return 'ovabrw_product_ajax_filter';
	}

	public function get_title() {
		return esc_html__( 'Category Filter', 'ova-brw' );
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
						'' 		=> esc_html__( 'Default', 'ova-brw' ),
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
		  	$category_args 		= array( 0 => esc_html__( 'All', 'ova-brw' ) );
		  	$default_category 	= array( 0 );
		  	
		  	if ( ! empty( $categories ) && is_array( $categories ) ) {
			  	foreach ( $categories as $k => $category ) {
				  	$category_args[$category->term_id] = $category->name;

				  	if ( $k < 3 ) array_push( $default_category, $category->term_id );
			  	}
		  	} else {
			  	$category_args[''] = esc_html__( 'Category not found' );
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

			$this->add_control(
				'category_filter',
				[
					'label' 		=> esc_html__( 'Show Filter', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
					'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
					'return_value' 	=> 'yes',
					'default' 		=> 'yes',
				]
			);

			$this->add_control(
				'pagination',
				[
					'label' 		=> esc_html__( 'Show Pagination', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
					'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
					'return_value' 	=> 'yes',
					'default' 		=> 'yes',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'caregory_section',
			[
				'label' => esc_html__( 'Category', 'ova-brw' ),
				'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' 		=> 'term_typography',
					'selector' 	=> '{{WRAPPER}} .ovabrw-product-ajax-filter .categories-filter .item-term',
				]
			);

			$this->start_controls_tabs(
				'term_tabs'
			);

				$this->start_controls_tab(
					'term_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'ova-brw' ),
					]
				);

					$this->add_control(
						'term_color',
						[
							'label' 	=> esc_html__( 'Color', 'ova-brw' ),
							'type' 		=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-product-ajax-filter .categories-filter .item-term' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'term_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'ova-brw' ),
					]
				);

					$this->add_control(
						'term_color_hover',
						[
							'label' 	=> esc_html__( 'Color', 'ova-brw' ),
							'type' 		=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-product-ajax-filter .categories-filter .item-term:hover' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'term_active_tab',
					[
						'label' => esc_html__( 'Active', 'ova-brw' ),
					]
				);

					$this->add_control(
						'term_color_active',
						[
							'label' 	=> esc_html__( 'Color', 'ova-brw' ),
							'type' 		=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-product-ajax-filter .categories-filter .item-term.active' => 'color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'item_term_margin',
				[
					'label' 		=> esc_html__( 'Item Margin', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ovabrw-product-ajax-filter .categories-filter .item-term' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'term_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ovabrw-product-ajax-filter .categories-filter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		
		$this->end_controls_section();

		$this->start_controls_section(
			'pagination_section',
			[
				'label' => esc_html__( 'Pagination', 'ova-brw' ),
				'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' 		=> 'page_typography',
					'selector' 	=> '{{WRAPPER}} .ovabrw-product-ajax-filter .ovabrw-pagination li .page-numbers',
				]
			);

			$this->add_control(
				'pagination_width',
				[
					'label' 		=> esc_html__( 'Width', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', 'custom' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 200,
							'step' => 5,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 45,
					],
					'selectors' => [
						'{{WRAPPER}} .ovabrw-product-ajax-filter .ovabrw-pagination li .page-numbers' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'pagination_height',
				[
					'label' 		=> esc_html__( 'Height', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', 'custom' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 200,
							'step' => 5,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 45,
					],
					'selectors' => [
						'{{WRAPPER}} .ovabrw-product-ajax-filter .ovabrw-pagination li .page-numbers' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs(
				'pagination_tabs'
			);

				$this->start_controls_tab(
					'pagination_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'ova-brw' ),
					]
				);

					$this->add_control(
						'pagination_color',
						[
							'label' 	=> esc_html__( 'Color', 'ova-brw' ),
							'type' 		=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-product-ajax-filter .ovabrw-pagination li .page-numbers' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'pagination_background',
						[
							'label' 	=> esc_html__( 'Background', 'ova-brw' ),
							'type' 		=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-product-ajax-filter .ovabrw-pagination li .page-numbers' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' 		=> 'pagination_border',
							'selector' 	=> '{{WRAPPER}} .ovabrw-product-ajax-filter .ovabrw-pagination li .page-numbers',
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'pagination_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'ova-brw' ),
					]
				);

					$this->add_control(
						'pagination_color_hover',
						[
							'label' 	=> esc_html__( 'Color', 'ova-brw' ),
							'type' 		=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-product-ajax-filter .ovabrw-pagination li .page-numbers:hover' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'pagination_background_hover',
						[
							'label' 	=> esc_html__( 'Background', 'ova-brw' ),
							'type' 		=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-product-ajax-filter .ovabrw-pagination li .page-numbers:hover' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' 		=> 'pagination_border_hover',
							'selector' 	=> '{{WRAPPER}} .ovabrw-product-ajax-filter .ovabrw-pagination li .page-numbers:hover',
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'pagination_active_tab',
					[
						'label' => esc_html__( 'Active', 'ova-brw' ),
					]
				);

					$this->add_control(
						'pagination_color_active',
						[
							'label' 	=> esc_html__( 'Color', 'ova-brw' ),
							'type' 		=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-product-ajax-filter .ovabrw-pagination li .page-numbers.current' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'pagination_background_active',
						[
							'label' 	=> esc_html__( 'Background', 'ova-brw' ),
							'type' 		=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-product-ajax-filter .ovabrw-pagination li .page-numbers.current' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' 		=> 'pagination_border_active',
							'selector' 	=> '{{WRAPPER}} .ovabrw-product-ajax-filter .ovabrw-pagination li .page-numbers.current',
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'item_pagination_margin',
				[
					'label' 		=> esc_html__( 'Item Margin', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ovabrw-product-ajax-filter .ovabrw-pagination li .page-numbers' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'pagination_margin',
				[
					'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ovabrw-product-ajax-filter .ovabrw-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

		// Data
		$args = [
			'template' 			=> $settings['card_template'],
			'categories' 		=> $settings['categories'],
			'posts_per_page' 	=> $settings['posts_per_page'],
			'orderby' 			=> $settings['orderby'],
			'order' 			=> $settings['order'],
			'pagination' 		=> $settings['pagination'],
			'category_filter' 	=> $settings['category_filter'],
		];

		ovabrw_get_template( 'elementor/ovabrw-product-ajax-filter.php', $args );
	}
}