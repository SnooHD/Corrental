<?php
namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ovabrw_search_map extends Widget_Base {

	public function get_name() {		
		return 'ovabrw_search_map';
	}

	public function get_title() {
		return esc_html__( 'Search Map', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-google-maps';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	public function get_script_depends() {

		/* Google Maps */
		if ( get_option( 'ova_brw_google_key_map', false ) ) {
			wp_enqueue_script( 'google_map','https://maps.googleapis.com/maps/api/js?key='.get_option( 'ova_brw_google_key_map', '' ).'&callback=Function.prototype&libraries=places', false, true );
		} else {
			wp_enqueue_script( 'google_map','//maps.googleapis.com/maps/api/js?sensor=false&callback=Function.prototype&libraries=places', array('jquery'), false, true);
		}
		wp_enqueue_script( 'google-marker', OVABRW_PLUGIN_URI.'assets/libs/google_map/markerclusterer.js', array('jquery'), false, true);
		wp_enqueue_script( 'google-richmarker', OVABRW_PLUGIN_URI.'assets/libs/google_map/richmarker-compiled.js', array('jquery'), false, true);

		/* Override market google map when more product the same location*/
		wp_enqueue_script('oms', OVABRW_PLUGIN_URI.'assets/libs/google_map/oms.js', array('jquery'), false, true);
		/* Jquery ui */
		wp_enqueue_script('jquery-ui', OVABRW_PLUGIN_URI.'assets/libs/jquery-ui/jquery-ui.min.js', array('jquery'), false, true);
		wp_enqueue_style('jquery-ui', OVABRW_PLUGIN_URI.'assets/libs/jquery-ui/jquery-ui.min.css', array(), null);

		/* Select2 */
		wp_enqueue_style('select2', OVABRW_PLUGIN_URI.'assets/libs/select2/select2.min.css', array(), null);
		wp_enqueue_script('select2', OVABRW_PLUGIN_URI.'assets/libs/select2/select2.min.js', array('jquery'),null,true);
		
		return [ 'script-elementor' ];
	}

	protected function register_controls() {

		$search_fields = array(
			'' 					=> esc_html__('Choose Field', 'ova-brw'),
			'name' 				=> esc_html__('Name', 'ova-brw'),
			'category' 			=> esc_html__('Category', 'ova-brw'),
			'location' 			=> esc_html__('Location', 'ova-brw'),
			'start_location' 	=> esc_html__('Pick-up Location', 'ova-brw'),
			'end_location' 		=> esc_html__('Drop-off Location', 'ova-brw'),
			'start_date' 		=> esc_html__('Pick-up Date', 'ova-brw'),
			'end_date' 			=> esc_html__('Drop-off Date', 'ova-brw'),
			'attribute' 		=> esc_html__('Name Attribute', 'ova-brw'),
			'tag' 				=> esc_html__('Tag', 'ova-brw'),
		);
		
		$this->start_controls_section(
			'section_setting',
			[
				'label' => esc_html__( 'Settings', 'ova-brw' ),
			]
		);

			if ( ovabrw_global_typography() ) {
				$this->add_control(
					'card',
					[
						'label'   => esc_html__( 'Choose Card', 'ova-brw' ),
						'type'    => Controls_Manager::SELECT,
						'options' => [
							'' 		=> esc_html__('Default', 'ova-brw'),
							'card1' => esc_html__('Card 1', 'ova-brw'),
							'card2' => esc_html__('Card 2', 'ova-brw'),
							'card3' => esc_html__('Card 3', 'ova-brw'),
							'card4' => esc_html__('Card 4', 'ova-brw'),
							'card5' => esc_html__('Card 5', 'ova-brw'),
							'card6' => esc_html__('Card 6', 'ova-brw'),
							
						],
						'default' => 'card1',
					]
				);
			}

			$this->add_control(
				'posts_per_page',
				[
					'label' 	=> esc_html__( 'Per Page', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'min' 		=> 1,
					'max' 		=> 50,
					'step' 		=> 1,
					'default' 	=> 12,
				]
			);

			$this->add_control(
				'column',
				[
					'label'   => esc_html__( 'Column', 'ova-brw' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'two-column',
					'options' => [
						'one-column' 	=> esc_html__('1 Column', 'ova-brw'),
						'two-column' 	=> esc_html__('2 Columns', 'ova-brw'),
						'three-column' 	=> esc_html__('3 Columns', 'ova-brw'),
					],
				]
			);

			$this->add_control(
				'orderby',
				[
					'label'   => esc_html__( 'Order By', 'ova-brw' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'date',
					'options' => [
						'title' 		=> esc_html__('Title', 'ova-brw'),
						'ID' 			=> esc_html__('ID', 'ova-brw'),
						'name' 			=> esc_html__('Name', 'ova-brw'),
						'date' 			=> esc_html__('Date', 'ova-brw'),
						'modified' 		=> esc_html__('Modified', 'ova-brw'),
						'rand' 			=> esc_html__('Random', 'ova-brw'),
						'menu_order' 	=> esc_html__( 'Menu Order', 'ova-brw' )
					],
				]
			);

			$this->add_control(
				'order',
				[
					'label'   => esc_html__( 'Order', 'ova-brw' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'DESC',
					'options' => [
						'ASC' 	=> esc_html__('Ascending', 'ova-brw'),
						'DESC' 	=> esc_html__('Descending', 'ova-brw'),
					],
				]
			);

			$this->add_control(
				'show_filter',
				[
					'label' 		=> esc_html__( 'Show Filters', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
					'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
					'default' 		=> '',
				]
			);

			$this->add_control(
				'show_featured',
				[
					'label' 		=> esc_html__( 'Show Featured', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
					'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
					'default' 		=> 'no',
				]
			);

			$this->add_control(
				'show_map',
				[
					'label' 		=> esc_html__( 'Show Map', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
					'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
					'default' 		=> 'yes',
				]
			);

			$this->add_control(
				'show_default_location',
				[
					'label' 		=> esc_html__( 'Default Location', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
					'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
					'default' 		=> 'no',
				]
			);

			$this->add_control(
				'zoom',
				[
					'label' 	=> esc_html__( 'Zoom Map', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'min' 		=> 1,
					'max' 		=> 20,
					'step' 		=> 1,
					'default' 	=> 4,
					'condition' => [
						'show_map' => 'yes'
					]
				]
			);

			$this->add_control(
				'marker_option',
				[
					'label'   		=> esc_html__( 'Marker Select', 'ova-brw' ),
					'description' 	=> esc_html__( 'You should use Icon to display exactly position', 'ova-brw' ),
					'type'    		=> Controls_Manager::SELECT,
					'default' 		=> 'icon',
					'options' 		=> [
						'icon' 		=> esc_html__('Icon', 'ova-brw'),
						'price' 	=> esc_html__('Price', 'ova-brw'),
						'date' 		=> esc_html__('Start Date', 'ova-brw'),
					],
				]
			);

			$this->add_control(
				'marker_icon',
				[
					'label' 	=> esc_html__( 'Choose Image', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::MEDIA,
					'condition' => [
						'marker_option' => 'icon'
					]
				]
			);

			$this->add_control(
				'field_1',
				[
					'label'   	=> esc_html__( 'Field 1', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_2',
				[
					'label'   	=> esc_html__( 'Field 2', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_3',
				[
					'label'   	=> esc_html__( 'Field 3', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_4',
				[
					'label'   	=> esc_html__( 'Field 4', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_5',
				[
					'label'   	=> esc_html__( 'Field 5', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_6',
				[
					'label'   	=> esc_html__( 'Field 6', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_7',
				[
					'label'   	=> esc_html__( 'Field 7', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_8',
				[
					'label'   	=> esc_html__( 'Field 8', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$this->add_control(
				'field_9',
				[
					'label'   	=> esc_html__( 'Field 9', 'ova-brw' ),
					'type'    	=> Controls_Manager::SELECT,
					'default' 	=> '',
					'separator' => 'before',
					'options' 	=> $search_fields,
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

			$taxonomies 		= get_option( 'ovabrw_custom_taxonomy', array() );
			$data_taxonomy[''] 	= esc_html__( 'Select Taxonomy', 'ova-brw' );

			if( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
				foreach( $taxonomies as $key => $value ) {
					$data_taxonomy[$key] = $value['name'];
				}
			}

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'taxonomy_custom', [
					'label' 		=> esc_html__( 'Taxonomy Custom', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SELECT,
					'label_block' 	=> true,
					'options' 		=> $data_taxonomy,
				]
			);

			$this->add_control(
				'list_taxonomy_custom',
				[
					'label' 	=> esc_html__( 'List Taxonomy Custom', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::REPEATER,
					'fields' 	=> $repeater->get_controls(),
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings();

		$template = apply_filters( 'ovabrw_ft_element_search_map', 'single/search_map.php' );

		ob_start();
		ovabrw_get_template( $template, $settings );
		echo ob_get_clean();
	}
}