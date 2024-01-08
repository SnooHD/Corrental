<?php

namespace JET_ABAF\Components\Bricks_Views;

use \Bricks\Elements;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

class Manager {

	public function __construct() {

		if ( ! defined( 'BRICKS_VERSION' ) ) {
			return;
		}

		// Register custom elements.
		add_action( 'init', [ $this, 'register_elements' ], 11 );

		// Provide a translatable category string for the builder.
		add_filter( 'bricks/builder/i18n', function( $i18n ) {
			$i18n['jetbooking'] = __( 'JetBooking', 'jet-booking' );

			return $i18n;
		} );

		// Add JetBooking icons font.
		add_action( 'wp_enqueue_scripts', function() {
			if ( bricks_is_builder() ) {
				wp_enqueue_style(
					'jet-booking-icons',
					JET_ABAF_URL . 'assets/lib/jet-booking-icons/icons.css',
					[],
					JET_ABAF_VERSION
				);
			}
		} );

	}

	/**
	 * Register elements.
	 *
	 * Load and register custom elements.
	 *
	 * @since 3.1.0
	 */
	public function register_elements() {

		$element_files = [
			JET_ABAF_PATH . 'includes/components/bricks-views/elements/calendar.php',
		];

		foreach ( $element_files as $file ) {
			Elements::register_element( $file );
		}

	}

}
