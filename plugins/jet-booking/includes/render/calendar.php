<?php

namespace JET_ABAF\Render;

use JET_ABAF\Plugin;

if ( ! class_exists( '\Jet_Engine_Render_Base' ) ) {
	require jet_engine()->plugin_path( 'includes/components/listings/render/base.php' );
}

class Calendar extends \Jet_Engine_Render_Base {

	private $instance_id = false;

	public function get_name() {
		return 'jet-booking-calendar';
	}

	public function render() {

		if ( ! $this->instance_id ) {
			$this->instance_id = 'calendar_' . rand( 1000, 9999 );
		}

		Plugin::instance()->engine_plugin->enqueue_deps( get_the_ID() );

		$settings        = $this->get_settings();
		$select_dates    = ! empty( $settings['select_dates'] ) ? filter_var( $settings['select_dates'], FILTER_VALIDATE_BOOLEAN ) : false;
		$scroll_to_form  = ! empty( $settings['scroll_to_form'] ) ? filter_var( $settings['scroll_to_form'], FILTER_VALIDATE_BOOLEAN ) : false;
		$wrapper_classes = [ 'jet-booking-calendar' ];

		if ( ! $select_dates ) {
			$wrapper_classes[] = 'disable-dates-select';
		}

		printf(
			'<div class="%2$s"><input type="hidden" class="jet-booking-calendar__input"/><div id="%1$s" class="jet-booking-calendar__container" data-scroll-to-form="%3$s"></div></div>',
			$this->instance_id,
			implode( ' ', $wrapper_classes ),
			$scroll_to_form
		);

	}

}
