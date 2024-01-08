<?php
/**
 * JetEngine compatibility package manager.
 *
 * @package JET_ABAF\Compatibility\Packages
 */

namespace JET_ABAF\Compatibility\Packages;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Engine {

	/**
	 * A reference to an instance of this class.
	 *
	 * @var object
	 */
	private static $instance = null;

	public function __construct() {

		require_once $this->package_path( 'query-builder/manager.php' );
		Jet_Engine\Query_Builder\Manager::instance();

		require_once $this->package_path( 'listings/manager.php' );
		Jet_Engine\Listings\Manager::instance();

	}

	/**
	 * Package path.
	 *
	 * Return path inside package.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param string $path Relative package path.
	 *
	 * @return string
	 */
	public function package_path( $path = '' ) {
		return JET_ABAF_PATH . 'includes/compatibility/packages/jet-engine/' . $path;
	}

	/**
	 * Package URL.
	 *
	 * Return URL inside package.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @param string $url Relative package URL.
	 *
	 * @return string
	 */
	public function package_url( $url = '' ) {
		return JET_ABAF_URL . 'includes/compatibility/packages/jet-engine/' . $url;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return object
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

}

new Jet_Engine();
