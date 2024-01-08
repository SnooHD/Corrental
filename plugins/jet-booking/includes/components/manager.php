<?php

namespace JET_ABAF\Components;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

class Manager {

	/**
	 * Components list.
	 *
	 * @var array
	 */
	private $_components = [];

	public function __construct() {
		add_action( 'init', [ $this, 'register_components' ] );
		add_action( 'init', [ $this, 'init_components' ] );
	}

	/**
	 * Register components.
	 *
	 * Register components before run init to allow unregister before init.
	 *
	 * @since 3.1.0
	 */
	public function register_components() {

		$components = [
			'bricks_views' => __NAMESPACE__ . '\Bricks_Views\Manager'
		];

		foreach ( $components as $component_slug => $component_class ) {
			$this->register_component( $component_slug, $component_class );
		}

	}

	/**
	 * Init components.
	 *
	 * Initialize main components.
	 *
	 * @since 3.1.0
	 */
	public function init_components() {
		foreach ( $this->_components as $slug => $class ) {
			jet_abaf()->$slug = new $class();
		}
	}

	/**
	 * Register component.
	 *
	 * Register plugin component.
	 *
	 * @since 3.1.0
	 *
	 * @param string $slug Component slug
	 * @param string $class Component class
	 */
	public function register_component( $slug = '', $class = '' ) {
		$this->_components[ $slug ] = $class;
	}

}