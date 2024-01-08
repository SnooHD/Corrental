<?php if ( ! defined( 'ABSPATH' ) ) exit();

if ( ! class_exists( 'Ovabrw_Settings' ) ) {

    class Ovabrw_Settings {

        public function __construct() {
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'ovabrw_add_settings_tab' ), 10, 1 );

			// Global Settings
			add_action( 'woocommerce_settings_ovabrw_global_typography_after', array( $this, 'wcst_ovabrw_global_typography_after' ) );

			// Save global settings
			add_action( 'woocommerce_update_option', array( $this, 'ovabrw_wc_update_option' ) );
        }

        public function ovabrw_add_settings_tab( $settings ) {
		  	$settings[] = include( OVABRW_PLUGIN_PATH.'/admin/setting/ovabrw-settings-tab.php' );

		  	return $settings;
		}

		public function wcst_ovabrw_global_typography_after() {
			include( OVABRW_PLUGIN_PATH.'/admin/setting/views/ovabrw-wcst-global-typography.php' );
		}

		public function ovabrw_wc_update_option( $option ) {
			$data = $_POST;

			if ( empty( $data ) ) {
				return false;
			}

			$update_options = [];

			// Font
			if ( isset( $data['ovabrw_glb_primary_font'] ) ) {
				$update_options['ovabrw_glb_primary_font'] = trim( $data['ovabrw_glb_primary_font'] );
			}
			if ( isset( $data['ovabrw_glb_primary_font_weight'] ) ) {
				$update_options['ovabrw_glb_primary_font_weight'] = $data['ovabrw_glb_primary_font_weight'];
			}
			if ( isset( $data['ovabrw_glb_custom_font'] ) ) {
				$update_options['ovabrw_glb_custom_font'] = trim( $data['ovabrw_glb_custom_font'] );
			}
			// End Font

			// Color
			if ( isset( $data['ovabrw_glb_primary_color'] ) ) {
				$update_options['ovabrw_glb_primary_color'] = trim( $data['ovabrw_glb_primary_color'] );
			}
			if ( isset( $data['ovabrw_glb_light_color'] ) ) {
				$update_options['ovabrw_glb_light_color'] = trim( $data['ovabrw_glb_light_color'] );
			}
			// End Color

			// Heading
			if ( isset( $data['ovabrw_glb_heading_font_size'] ) ) {
				$update_options['ovabrw_glb_heading_font_size'] = trim( $data['ovabrw_glb_heading_font_size'] );
			}
			if ( isset( $data['ovabrw_glb_heading_font_weight'] ) ) {
				$update_options['ovabrw_glb_heading_font_weight'] = trim( $data['ovabrw_glb_heading_font_weight'] );
			}
			if ( isset( $data['ovabrw_glb_heading_line_height'] ) ) {
				$update_options['ovabrw_glb_heading_line_height'] = trim( $data['ovabrw_glb_heading_line_height'] );
			}
			if ( isset( $data['ovabrw_glb_heading_color'] ) ) {
				$update_options['ovabrw_glb_heading_color'] = trim( $data['ovabrw_glb_heading_color'] );
			}
			// End Heading
			
			// Second Heading
			if ( isset( $data['ovabrw_glb_second_heading_font_size'] ) ) {
				$update_options['ovabrw_glb_second_heading_font_size'] = trim( $data['ovabrw_glb_second_heading_font_size'] );
			}
			if ( isset( $data['ovabrw_glb_second_heading_font_weight'] ) ) {
				$update_options['ovabrw_glb_second_heading_font_weight'] = trim( $data['ovabrw_glb_second_heading_font_weight'] );
			}
			if ( isset( $data['ovabrw_glb_second_heading_line_height'] ) ) {
				$update_options['ovabrw_glb_second_heading_line_height'] = trim( $data['ovabrw_glb_second_heading_line_height'] );
			}
			if ( isset( $data['ovabrw_glb_second_heading_color'] ) ) {
				$update_options['ovabrw_glb_second_heading_color'] = trim( $data['ovabrw_glb_second_heading_color'] );
			}
			// End Second Heading
			
			// Label
			if ( isset( $data['ovabrw_glb_label_font_size'] ) ) {
				$update_options['ovabrw_glb_label_font_size'] = trim( $data['ovabrw_glb_label_font_size'] );
			}
			if ( isset( $data['ovabrw_glb_label_font_weight'] ) ) {
				$update_options['ovabrw_glb_label_font_weight'] = trim( $data['ovabrw_glb_label_font_weight'] );
			}
			if ( isset( $data['ovabrw_glb_label_line_height'] ) ) {
				$update_options['ovabrw_glb_label_line_height'] = trim( $data['ovabrw_glb_label_line_height'] );
			}
			if ( isset( $data['ovabrw_glb_label_color'] ) ) {
				$update_options['ovabrw_glb_label_color'] = trim( $data['ovabrw_glb_label_color'] );
			}
			// End Label
			
			// Text
			if ( isset( $data['ovabrw_glb_text_font_size'] ) ) {
				$update_options['ovabrw_glb_text_font_size'] = trim( $data['ovabrw_glb_text_font_size'] );
			}
			if ( isset( $data['ovabrw_glb_text_font_weight'] ) ) {
				$update_options['ovabrw_glb_text_font_weight'] = trim( $data['ovabrw_glb_text_font_weight'] );
			}
			if ( isset( $data['ovabrw_glb_text_line_height'] ) ) {
				$update_options['ovabrw_glb_text_line_height'] = trim( $data['ovabrw_glb_text_line_height'] );
			}
			if ( isset( $data['ovabrw_glb_text_color'] ) ) {
				$update_options['ovabrw_glb_text_color'] = trim( $data['ovabrw_glb_text_color'] );
			}
			// End Text
			
			// Card
			if ( isset( $data['ovabrw_glb_card_template'] ) ) {
				$update_options['ovabrw_glb_card_template'] = trim( $data['ovabrw_glb_card_template'] );
			}
			
			$card_prx = 'ovabrw_glb_card';
			for ( $i = 1; $i <= 6; $i++ ) {
				// Featured
				if ( isset( $data[$card_prx.$i.'_featured'] ) ) {
					$update_options[$card_prx.$i.'_featured'] = $data[$card_prx.$i.'_featured'];
				}

				// Special
				if ( isset( $data[$card_prx.$i.'_feature_featured'] ) ) {
					$update_options[$card_prx.$i.'_feature_featured'] = $data[$card_prx.$i.'_feature_featured'];
				}

				// Thumbnail type
				if ( isset( $data[$card_prx.$i.'_thumbnail_type'] ) ) {
					$update_options[$card_prx.$i.'_thumbnail_type'] = $data[$card_prx.$i.'_thumbnail_type'];
				}

				// Price
				if ( isset( $data[$card_prx.$i.'_price'] ) ) {
					$update_options[$card_prx.$i.'_price'] = $data[$card_prx.$i.'_price'];
				}

				// Features
				if ( isset( $data[$card_prx.$i.'_features'] ) ) {
					$update_options[$card_prx.$i.'_features'] = $data[$card_prx.$i.'_features'];
				}

				// Custom Taxonomy
				if ( isset( $data[$card_prx.$i.'_custom_taxonomy'] ) ) {
					$update_options[$card_prx.$i.'_custom_taxonomy'] = $data[$card_prx.$i.'_custom_taxonomy'];
				}

				// Attribute
				if ( isset( $data[$card_prx.$i.'_attribute'] ) ) {
					$update_options[$card_prx.$i.'_attribute'] = $data[$card_prx.$i.'_attribute'];
				}

				// Short description
				if ( isset( $data[$card_prx.$i.'_short_description'] ) ) {
					$update_options[$card_prx.$i.'_short_description'] = $data[$card_prx.$i.'_short_description'];
				}

				// Review
				if ( isset( $data[$card_prx.$i.'_review'] ) ) {
					$update_options[$card_prx.$i.'_review'] = $data[$card_prx.$i.'_review'];
				}

				// Button
				if ( isset( $data[$card_prx.$i.'_button'] ) ) {
					$update_options[$card_prx.$i.'_button'] = $data[$card_prx.$i.'_button'];
				}
			}
			// End Card

			foreach ( $update_options as $name => $value ) {
				update_option( $name, $value );
			}
		}
    }
}

new Ovabrw_Settings();