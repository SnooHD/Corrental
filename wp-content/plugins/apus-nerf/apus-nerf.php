<?php
/**
 * Plugin Name: Apus Nerf
 * Plugin URI: http://apusthemes.com/apus-nerf/
 * Description: Powerful plugin to create a apartment on your website.
 * Version: 1.0.0
 * Author: Habq
 * Author URI: http://apusthemes.com/
 * Requires at least: 3.8
 * Tested up to: 5.2
 *
 * Text Domain: apus-nerf
 * Domain Path: /languages/
 *
 * @package apus-nerf
 * @category Plugins
 * @author Habq
 */
if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

if ( !class_exists("Apus_Nerf") ) {
	
	final class Apus_Nerf {

		private static $instance;

		public static function getInstance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Apus_Nerf ) ) {
				self::$instance = new Apus_Nerf;
				self::$instance->setup_constants();
				self::$instance->load_textdomain();

				self::$instance->includes();
			}

			return self::$instance;
		}
		/**
		 *
		 */
		public function setup_constants(){
			define( 'APUS_NERF_PLUGIN_VERSION', '1.0.0' );

			define( 'APUS_NERF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			define( 'APUS_NERF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			define( 'APUS_NERF_PREFIX', 'apartment_' );
		}

		public function includes() {
			// post type
			require_once APUS_NERF_PLUGIN_DIR . 'includes/post-types/class-post-type-apartment.php';

			//
			require_once APUS_NERF_PLUGIN_DIR . 'includes/class-template-loader.php';
			require_once APUS_NERF_PLUGIN_DIR . 'includes/class-mixes.php';
		}

		/**
		 *
		 */
		public function load_textdomain() {
			// Set filter for Apus_Nerf's languages directory
			$lang_dir = APUS_NERF_PLUGIN_DIR . 'languages/';
			$lang_dir = apply_filters( 'apus_nerf_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'apus-nerf' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'apus-nerf', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/apus-nerf/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/apus-nerf folder
				load_textdomain( 'apus-nerf', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/apus-nerf/languages/ folder
				load_textdomain( 'apus-nerf', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'apus-nerf', false, $lang_dir );
			}
		}
	}
}

function Apus_Nerf() {
	return Apus_Nerf::getInstance();
}

add_action( 'plugins_loaded', 'Apus_Nerf' );