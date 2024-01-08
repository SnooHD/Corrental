<?php defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'ovabrw_admin_ajax' ) ) {
	class ovabrw_admin_ajax{
		public function __construct(){
			$this->init();
		}

		public function init(){
			// Define All Ajax function
			$arr_ajax =  array(
				'update_order_status_woo',
				'ovabrw_get_custom_tax_in_cat',
				'ovabrw_update_ckf',
			);

			foreach($arr_ajax as $val){
				add_action( 'wp_ajax_'.$val, array( $this, $val ) );
				add_action( 'wp_ajax_nopriv_'.$val, array( $this, $val ) );
			}
		}

		/**
		 * Schedule Ajax
		 */
		public static function update_order_status_woo(){
			$order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : '';
			$new_order_status = isset( $_POST['new_order_status'] ) ? sanitize_text_field( $_POST['new_order_status'] ) : ''  ;

			if ( $order_id && $new_order_status ) {
				$order = new WC_Order($order_id);

				if ( ! current_user_can( apply_filters( 'ovabrw_update_order_status' ,'publish_posts' ) ) ) {
					echo 'error_permission';	
				} elseif ( $order->update_status( $new_order_status ) ) {
					echo 'true';
				} else {
					echo 'false';
				}
			} else {
				echo 'false';
			}
			
			wp_die();
		}

		/**
		 * Get Custom Taxonomy choosed in Category
		 */
		public static function ovabrw_get_custom_tax_in_cat() {
			$checked_tax = isset( $_POST['checked_tax'] ) ?  $_POST['checked_tax'] : '';
			
			$list_tax_values = array();
			
			if ( $checked_tax ) {
				foreach ( $checked_tax as $key => $term_id ) {
					$ovabrw_custom_tax = get_term_meta($term_id, 'ovabrw_custom_tax', true);
					
					if ( $ovabrw_custom_tax ) {
						foreach ( $ovabrw_custom_tax as $key => $value ) {
							if ( ! in_array( $value, $list_tax_values ) ) {
								if ( $value ) {
									array_push( $list_tax_values, $value);		
								}
							}
						}
					}
				}
			}
			
			echo implode(",", $list_tax_values ); 
			wp_die();
		}

		/**
		 * Update CKF when sortable
		 */
		public static function ovabrw_update_ckf() {
			if ( ! isset( $_POST['fields'] ) || empty( $_POST['fields'] ) ) wp_die();

			$fields = $_POST['fields'];

			if ( is_array( $fields ) ) {
				$new_fields 	= [];
				$list_fields 	= recursive_array_replace( '\\', '', get_option( 'ovabrw_booking_form', array() ) );

				foreach ( $fields as $field ) {
                    if ( ! empty( $field ) && array_key_exists( $field, $list_fields ) ) {
                        $new_fields[$field] = $list_fields[$field];
                    }
                }

                if ( ! empty( $new_fields ) && is_array( $new_fields ) ) {
                    $list_fields = $new_fields;
                }

                update_option( 'ovabrw_booking_form', $list_fields );
			}

			wp_die();
		}
	}

	new ovabrw_admin_ajax();
}