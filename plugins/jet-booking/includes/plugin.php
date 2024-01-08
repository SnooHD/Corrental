<?php

namespace JET_ABAF;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Plugin.
 *
 * The main plugin handler class is responsible for initializing JetBooking. The
 * class registers and all the components required to run the plugin.
 *
 *
 * @property Components\Manager            components
 * @property Dashboard\Manager             dashboard
 * @property DB                            db
 * @property Elementor_Integration\Manager elementor
 * @property Engine_Plugin                 engine_plugin
 * @property Google_Calendar               google_cal
 * @property iCal                          ical
 * @property Rest_API\Manager              rest_api
 * @property Settings                      settings
 * @property Set_Up                        setup
 * @property Statuses                      statuses
 * @property Stores\Manager                stores
 * @property Tools                         tools
 * @property WC_Integration\Manager        wc
 *
 * @package JET_ABAF
 */
#[\AllowDynamicProperties]
class Plugin {

	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since  1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Register autoloader.
	 *
	 * JetBooking autoloader loads all the classes needed to run the plugin.
	 *
	 * @since  2.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function register_autoloader() {
		require JET_ABAF_PATH . 'includes/autoloader.php';

		Autoloader::run();
	}

	/**
	 * Init components.
	 *
	 * Initialize JetBooking components. Register actions, run setting manager,
	 * initialize all the components that run plugin, and if in admin page
	 * initialize admin components.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function init_components() {

		$this->settings      = new Settings();
		$this->statuses      = new Statuses();
		$this->db            = new DB();
		$this->elementor     = new Elementor_Integration\Manager();
		$this->engine_plugin = new Engine_Plugin();
		$this->google_cal    = new Google_Calendar();
		$this->ical          = new iCal();
		$this->rest_api      = new Rest_API\Manager();
		$this->setup         = new Set_Up();
		$this->stores        = new Stores\Manager();
		$this->tools         = new Tools();
		$this->wc            = new WC_Integration\Manager();
		$this->components    = new Components\Manager();
		$this->dashboard     = new Dashboard\Manager( [
			new Dashboard\Pages\Bookings(),
			new Dashboard\Pages\Settings(),
			new Dashboard\Pages\Calendars(),
			new Dashboard\Pages\Set_Up(),
		] );

		new Compatibility\Manager();
		new Dashboard\Post_Meta\Price_Meta();
		new Dashboard\Post_Meta\Custom_Schedule_Meta();
		new Dashboard\Post_Meta\Configuration_Meta();
		new Dashboard\Booking_Meta();
		new Dashboard\Order_Meta();
		new Formbuilder_Plugin\Jfb_Plugin();

		if ( is_admin() ) {
			new Upgrade();

			new Updater\Plugin( [
				'version' => JET_ABAF_VERSION,
				'slug'    => 'jet-booking',
			] );

			new Updater\Changelog( [
				'name'     => 'JetBooking',
				'slug'     => 'jet-booking',
				'version'  => JET_ABAF_VERSION,
				'author'   => '<a href="https://crocoblock.com/">Crocoblock</a>',
				'homepage' => 'https://crocoblock.com/plugins/jetbooking/',
				'banners'  => [
					'high' => JET_ABAF_URL . 'assets/images/banner.png',
					'low'  => JET_ABAF_URL . 'assets/images/banner.png',
				],
			] );
		}

		Cron\Manager::instance();

	}

	/**
	 * Get template.
	 *
	 * Returns path to template file.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param string $name Template name.
	 *
	 * @return string|bool
	 */
	public function get_template( $name = null ) {

		$template_path = apply_filters( 'jet-abaf/template-path', 'jet-booking/' );
		$template      = locate_template( $template_path . $name );

		if ( ! $template ) {
			$template = JET_ABAF_PATH . 'templates/' . $name;
		}

		return file_exists( $template ) ? $template : false;

	}

	/**
	 * Init the JetDashboard module.
	 *
	 * @return void
	 */
	public function jet_dashboard_init() {
		if ( is_admin() ) {
			if ( ! class_exists( 'Jet_Dashboard\Dashboard' ) ) {
				return;
			}

			$jet_dashboard = \Jet_Dashboard\Dashboard::get_instance();

			$jet_dashboard->init( [
				'path'           => $jet_dashboard->get_dashboard_path(),
				'url'            => $jet_dashboard->get_dashboard_url(),
				'cx_ui_instance' => [ $this, 'jet_dashboard_ui_instance_init' ],
				'plugin_data'    => [
					'slug'         => 'jet-booking',
					'file'         => JET_ABAF_PLUGIN_BASE,
					'version'      => JET_ABAF_VERSION,
					'plugin_links' => [],
				],
			] );
		}
	}

	/**
	 * JetDashboard UI instance initialization.
	 *
	 * @return \CX_Vue_UI
	 */
	public function jet_dashboard_ui_instance_init() {
		$cx_ui_module_data = jet_engine()->framework->get_included_module_data( 'cherry-x-vue-ui.php' );

		return new \CX_Vue_UI( $cx_ui_module_data );
	}

	/**
	 * Deactivation.
	 *
	 * Ran when any plugin is deactivated.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	function deactivation() {
		if ( $this->settings->get( 'ical_synch' ) ) {
			$sync_calendars = Cron\Manager::instance()->get_schedules( 'jet-booking-sync-calendars' );
			$sync_calendars->unschedule_event();
		}
	}

	private function __construct() {

		if ( ! function_exists( 'jet_engine' ) ) {
			add_action( 'admin_notices', function () {
				$message = __( '<b>WARNING!</b> <b>JetBooking</b> plugin requires <b>JetEngine</b> plugin to work properly!', 'jet-booking' );
				printf( '<div class="notice notice-error"><p>%s</p></div>', wp_kses_post( $message ) );
			} );

			return;
		}

		$this->register_autoloader();

		add_action( 'init', [ $this, 'init_components' ], 0 );
		add_action( 'init', [ $this, 'jet_dashboard_init' ], - 999 );

		register_deactivation_hook( JET_ABAF__FILE__, [ $this, 'deactivation' ] );

	}

}

Plugin::instance();
