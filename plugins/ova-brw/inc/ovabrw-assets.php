<?php defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'OVABRW_Assets' ) ) {
	class OVABRW_Assets {

		public function __construct() {
			/**
			 * Enqueue CSS, Javascript
			 */
			add_action( 'admin_enqueue_scripts', array( $this, 'ovabrw_admin_enqueue_scripts' ) );

			/**
			 * Load google font from customize
			 */
			add_action('wp_enqueue_scripts', array( $this, 'ovabrw_load_google_fonts' ) );

			/**
			 * Enqueue CSS, Javascript
			 */
			add_action( 'wp_enqueue_scripts', array( $this, 'ovabrw_enqueue_scripts' ) );

			/**
			 * Admin Head
			 */
			add_action( 'admin_head', array( $this, 'ovabrw_admin_head' ) );

			/**
			 * Admin Enqueue CSS, Javascript
			 */
			add_action( 'wp_enqueue_scripts', array( $this, 'ovabrw_admin_head' ) );

			/**
			 * Enqueue style from customize
			 */
			add_action('wp_enqueue_scripts', array( $this, 'ovabrw_enqueue_customize' ), 11 );
		}

		public function ovabrw_admin_enqueue_scripts() {
			// Map
			if ( get_option( 'ova_brw_google_key_map', false ) ) {
				wp_enqueue_script( 'google_map','https://maps.googleapis.com/maps/api/js?key='.get_option( 'ova_brw_google_key_map', '' ).'&callback=Function.prototype&libraries=places', false, true );
			} else {
				global $pagenow, $typenow;

				if ( $pagenow === 'post.php' && $typenow === 'product' ) {
					wp_enqueue_script( 'google_map','//maps.googleapis.com/maps/api/js?sensor=false&callback=Function.prototype&libraries=places', array('jquery'), false, true );
				}
			}

			//fullcalendar
			wp_enqueue_script( 'moment', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/moment.min.js', array('jquery'),null,true );
		    wp_enqueue_script( 'fullcalendar', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/main.js', array('jquery'),null,true );
		    wp_enqueue_script( 'locale-all', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/locales-all.js', array('jquery'),null,true );
		    wp_enqueue_style( 'fullcalendar', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/main.min.css', array(), null );
			wp_enqueue_script( 'calendar_booking', OVABRW_PLUGIN_URI.'assets/js/admin/calendar.js', array('jquery'), false, true );
			wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );		
			wp_enqueue_script( 'jquery-timepicker', OVABRW_PLUGIN_URI.'assets/libs/jquery-timepicker/jquery.timepicker.min.js', array('jquery'), false, true );
			wp_enqueue_style( 'jquery-timepicker', OVABRW_PLUGIN_URI.'assets/libs/jquery-timepicker/jquery.timepicker.min.css' );

			// Date Time Picker
			wp_enqueue_script( 'datetimepicker', OVABRW_PLUGIN_URI.'assets/libs/datetimepicker/jquery.datetimepicker.js', array('jquery'), null, true );

			// Admin Css
			wp_enqueue_style( 'datetimepicker', OVABRW_PLUGIN_URI.'assets/libs/datetimepicker/jquery.datetimepicker.css', array(), null );
			wp_enqueue_style( 'ovabrw_admin', OVABRW_PLUGIN_URI.'assets/css/admin/ovabrw_admin.css', array(), null );

			//Admin js
			wp_enqueue_script( 'admin_script', OVABRW_PLUGIN_URI.'assets/js/admin/admin_script.min.js', array('jquery'),false,true );
		    wp_localize_script( 'admin_script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		    //Add select2
		    if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'product' && isset( $_GET['page'] ) && ( $_GET['page'] == 'ovabrw-create-order' || $_GET['page'] == 'ovabrw-check-product' || $_GET['page'] == 'ovabrw-import-location' ) ) {
		    	wp_enqueue_style( 'select2css', OVABRW_PLUGIN_URI.'assets/libs/select2/select2.min.css', array(), null );
				wp_enqueue_script( 'select2', OVABRW_PLUGIN_URI.'assets/libs/select2/select2.min.js', array('jquery'),null,true );
				wp_enqueue_style( 'flaticon_essential_font', OVABRW_PLUGIN_URI.'assets/libs/flaticon/essential_set/flaticon.css', array(), null );
		    }
		}

		public function ovabrw_load_google_fonts() {
			if ( ovabrw_global_typography() ) {
				$primary_font 	= get_option( 'ovabrw_glb_primary_font', 'Poppins' );
				$font_weight 	= get_option( 'ovabrw_glb_primary_font_weight', array(
					"100",
			        "100italic",
			        "200",
			        "200italic",
			        "300",
			        "300italic",
			        "regular",
			        "italic",
			        "500",
			        "500italic",
			        "600",
			        "600italic",
			        "700",
			        "700italic",
			        "800",
			        "800italic",
			        "900",
			        "900italic",
				));

				$str_font_weight = '100,200,300,400,500,600,700,800,900';

				if ( ! empty( $font_weight ) && is_array( $font_weight ) ) {
					$str_font_weight = implode( ',', $font_weight );
				}

				if ( $primary_font && $str_font_weight ) {
					$font_url = add_query_arg(
						array(
							'family' => urlencode( $primary_font.':'.$str_font_weight ),
						),
						'//fonts.googleapis.com/css'
					);

					$google_font = esc_url_raw( $font_url );

					wp_enqueue_style( 'ovabrw-google-font', $google_font, array(), null );
				}
			}
		}

		public function ovabrw_enqueue_scripts() {
			// reCAPTCHA
			if ( get_option( 'ova_brw_recapcha_enable', 'no' ) === 'yes' ) {
				$recaptcha_type = ovabrw_get_recaptcha_type();
				$site_key 		= ovabrw_get_recaptcha_site_key();

				wp_enqueue_script( 'ovabrw_recapcha_loading', OVABRW_PLUGIN_URI.'assets/js/frontend/ova-brw-recaptcha.js', [], false, false );
				wp_localize_script( 'ovabrw_recapcha_loading', 'ovabrw_recaptcha', array( 'site_key' => $site_key, 'form' => get_option( 'ova_brw_recapcha_form', '' ) ) );

				if ( $recaptcha_type === 'v3' ) {
					wp_enqueue_script( 'ovabrw_recaptcha', 'https://www.google.com/recaptcha/api.js?onload=ovabrwLoadingReCAPTCHAv3&render='.$site_key, [], false, false );
				} else {
					wp_enqueue_script( 'ovabrw_recaptcha', 'https://www.google.com/recaptcha/api.js?onload=ovabrwLoadingReCAPTCHAv2&render=explicit', [], false, false );
				}
			}
			
		    wp_enqueue_script( 'jquery-ui-autocomplete' );

		    // Single Product
			if ( is_product() ) {
				$product_id = get_the_id();

				if ( $product_id ) {
					$rental_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

					if ( $rental_type === 'taxi' ) {
						// Map
						if ( get_option( 'ova_brw_google_key_map', false ) ) {
							wp_enqueue_script( 'google_map','https://maps.googleapis.com/maps/api/js?key='.get_option( 'ova_brw_google_key_map', '' ).'&libraries=places&callback=Function.prototype', false, true );
						}
					}
				}
			}

		    // Fullcalendar
		    wp_enqueue_script( 'moment', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/moment.min.js', array('jquery'), null, true );
		    wp_enqueue_script( 'fullcalendar', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/main.js', array('jquery'), null,true );
		    wp_enqueue_script( 'locale-all', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/locales-all.js', array('jquery'), null, true );
		    wp_enqueue_style('fullcalendar', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/main.min.css', array(), null );

		    // Datepicker
		    wp_enqueue_script( 'datetimepicker', OVABRW_PLUGIN_URI.'assets/libs/datetimepicker/jquery.datetimepicker.js', array('jquery'), null, true );
		    wp_enqueue_style( 'datetimepicker', OVABRW_PLUGIN_URI.'assets/libs/datetimepicker/jquery.datetimepicker.css', array(), null );

		    if ( ovabrw_global_typography() ) {
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
		    }

			// Add elegant
			if ( apply_filters( 'ovabrw_use_elegant_font', true ) ) {
				wp_enqueue_style( 'elegant_font', OVABRW_PLUGIN_URI.'assets/libs/elegant_font/style.css', array(), null);	
			}

			// Add flat icon car service
			if ( apply_filters( 'ovabrw_use_flaticon_font', true ) ) {
				wp_enqueue_style( 'flaticon_car_service_font', OVABRW_PLUGIN_URI.'assets/libs/flaticon/car_service/flaticon.css', array(), null );
				wp_enqueue_style( 'flaticon_car2_font', OVABRW_PLUGIN_URI.'assets/libs/flaticon/car2/flaticon.css', array(), null );	
				wp_enqueue_style( 'flaticon_essential_font', OVABRW_PLUGIN_URI.'assets/libs/flaticon/essential_set/flaticon.css', array(), null );	
			}

			wp_enqueue_style( 'ovabrw-frontend', OVABRW_PLUGIN_URI.'assets/css/frontend/ovabrw_frontend.css', array(), null );
			wp_enqueue_script( 'ova_brw_js_frontend', OVABRW_PLUGIN_URI.'assets/js/frontend/ova-brw-frontend.min.js', array('jquery'), null, true );
			wp_localize_script( 'ova_brw_js_frontend', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}

		public function ovabrw_admin_head() {
			global $wp;

			if ( ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'product' ) || ( isset( $_GET['post'] ) && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) || ( isset( $wp->query_vars['wcfm-products-manage'] ) ) ) {
				// Custom taxonomies choosed in post
				$all_cus_tax = $exist_cus_tax = $cus_tax_hide_p_loaded = array();

				// Get All Custom taxonomy
				$ovabrw_custom_taxonomy = ovabrw_create_type_taxonomies();

				// All custom slug tax
				if ( $ovabrw_custom_taxonomy ) {
					foreach( $ovabrw_custom_taxonomy as $key => $value ) {
						array_push($all_cus_tax, $value['slug']);
					}
				}
		
				// Edit product in backend and WCFM plugin
				if ( ( isset( $_GET['post'] ) && $_GET['action'] == 'edit' ) || isset( $wp->query_vars['wcfm-products-manage'] ) ) {
					$id = isset( $_GET['post'] ) ? $_GET['post'] : '';

					if ( !$id &&  isset( $wp->query_vars['wcfm-products-manage'] ) &&  $wp->query_vars['wcfm-products-manage'] != '' ) {
						$id = $wp->query_vars['wcfm-products-manage'];
					}

					$terms_id = get_the_terms( $id, 'product_cat' );
					
					if ( $terms_id ) {
						foreach( $terms_id as $key => $term ) {
							$ovabrw_custom_tax = get_term_meta($term->term_id, 'ovabrw_custom_tax', true);	
							
							if ( $ovabrw_custom_tax ) {
								foreach( $ovabrw_custom_tax as $key => $value ) {
									array_push( $exist_cus_tax, $value );
								}	
							}
							
						}
					}

					if ( $ovabrw_custom_taxonomy ) {
						foreach ($ovabrw_custom_taxonomy as $key => $value) {
							if ( !in_array($value['slug'], $exist_cus_tax) ) {
								array_push($cus_tax_hide_p_loaded, $value['slug']);
							}
						}
					}
				} else { // Add new product
					$cus_tax_hide_p_loaded = $all_cus_tax;
				}
				
				// Check show custom taxonomy depend category	
				$ova_brw_search_show_tax_depend_cat = ovabrw_get_setting( get_option( 'ova_brw_search_show_tax_depend_cat', 'yes' ) );

				if ( $ova_brw_search_show_tax_depend_cat == 'no' ) {
					$cus_tax_hide_p_loaded = $all_cus_tax = array();
				}
				
				echo '<script type="text/javascript"> var ova_brw_search_show_tax_depend_cat = "'.$ova_brw_search_show_tax_depend_cat.'"; var cus_tax_hide_p_loaded = "'.implode(',', $cus_tax_hide_p_loaded).'"; var all_cus_tax = "'.implode(',', $all_cus_tax).'"; </script>';	
			}
		}

		public function ovabrw_enqueue_customize() {
			if ( ovabrw_global_typography() ) {
				add_filter( 'body_class', function( $classes ) {
					return array_merge( $classes, array( 'ovabrw-modern' ) );
				});

				$css = '';

				// Primary
				$primary_font 	= get_option( 'ovabrw_glb_primary_font', 'Poppins' );
				$primary_color 	= get_option( 'ovabrw_glb_primary_color', '#E56E00' );
				$light_color 	= get_option( 'ovabrw_glb_light_color', '#C3C3C3' );
				$css .= '--ovabrw-primary-font:'.$primary_font.';';
				$css .= '--ovabrw-primary-color:'.$primary_color.';';
				$css .= '--ovabrw-light-color:'.$light_color.';';

				// Heading
				$heading_size 			= get_option( 'ovabrw_glb_heading_font_size', '24px' );
				$heading_weight 		= get_option( 'ovabrw_glb_heading_font_weight', '600' );
				$heading_line_height 	= get_option( 'ovabrw_glb_heading_line_height', '36px' );
				$heading_color 			= get_option( 'ovabrw_glb_heading_color', '#222222' );
				$css .= '--ovabrw-heading-size:'.$heading_size.';';
				$css .= '--ovabrw-heading-weight:'.$heading_weight.';';
				$css .= '--ovabrw-heading-line-height:'.$heading_line_height.';';
				$css .= '--ovabrw-heading-color:'.$heading_color.';';

				// Second Heading
				$second_heading_size 		= get_option( 'ovabrw_glb_second_heading_font_size', '22px' );
				$second_heading_weight 		= get_option( 'ovabrw_glb_second_heading_font_weight', '600' );
				$second_heading_line_height = get_option( 'ovabrw_glb_second_heading_line_height', '33px' );
				$second_heading_color 		= get_option( 'ovabrw_glb_second_heading_color', '#222222' );
				$css .= '--ovabrw-second-heading-size:'.$second_heading_size.';';
				$css .= '--ovabrw-second-heading-weight:'.$second_heading_weight.';';
				$css .= '--ovabrw-second-heading-line-height:'.$second_heading_line_height.';';
				$css .= '--ovabrw-second-heading-color:'.$second_heading_color.';';

				// Label
				$label_size 		= get_option( 'ovabrw_glb_label_font_size', '16px' );
				$label_weight 		= get_option( 'ovabrw_glb_label_font_weight', '500' );
				$label_line_height 	= get_option( 'ovabrw_glb_label_line_height', '24px' );
				$label_color 		= get_option( 'ovabrw_glb_label_color', '#222222' );
				$css .= '--ovabrw-label-size:'.$label_size.';';
				$css .= '--ovabrw-label-weight:'.$label_weight.';';
				$css .= '--ovabrw-label-line-height:'.$label_line_height.';';
				$css .= '--ovabrw-label-color:'.$label_color.';';

				// Text
				$text_size 			= get_option( 'ovabrw_glb_text_font_size', '14px' );
				$text_weight 		= get_option( 'ovabrw_glb_text_font_weight', '400' );
				$text_line_height 	= get_option( 'ovabrw_glb_text_line_height', '22px' );
				$text_color 		= get_option( 'ovabrw_glb_text_color', '#555555' );
				$css .= '--ovabrw-text-size:'.$text_size.';';
				$css .= '--ovabrw-text-weight:'.$text_weight.';';
				$css .= '--ovabrw-text-line-height:'.$text_line_height.';';
				$css .= '--ovabrw-text-color:'.$text_color.';';

				$root = ":root{{$css}}";

				wp_add_inline_style( 'ovabrw-frontend', $root );
			}
		}
	}

	new OVABRW_Assets();
}