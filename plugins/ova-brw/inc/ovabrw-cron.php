<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Ovabrw_Cron' ) ) {

	/**
	 * Class Ovabrw_Mail
	 */
	class Ovabrw_Cron {
		public $hook_remind_pickup_date = 'ovabrw_cron_hook_remind_pickup_date';
		public $time_repeat_remind_pickup_date = 'time_repeat_remind_pickup_date';

		// Remaining Invoice
		public $hook_remaining_invoice 			= 'ovabrw_cron_hook_remaining_invoice';
		public $time_repeat_remaining_invoice 	= 'time_repeat_remaining_invoice';

		/**
		 * Ovabrw_Cron constructor.
		 */
		public function __construct() {
			add_filter( 'cron_schedules', array( $this, 'ovabrw_add_cron_interval' ) );
			add_action( 'init', array( $this, 'ovabrw_check_scheduled' ) );
			register_deactivation_hook( __FILE__, array( $this, 'ovabrw_deactivate_cron' ) ); 

			add_action( $this->hook_remind_pickup_date, array( $this, 'ovabrw_remind_event_time' ) );
			add_action( $this->hook_remaining_invoice, array( $this, 'ovabrw_remaining_invoice_event_time' ) );
		}

		/**
		 * init time repeat hook
		 * @param  array $schedules 
		 * @return array schedule
		 */
		public function ovabrw_add_cron_interval( $schedules ) {
			$remind_mail_send_per_seconds = intval( ovabrw_get_setting( get_option( 'remind_mail_send_per_seconds', 86400 ) ) );

		    $schedules[$this->time_repeat_remind_pickup_date] = array(
		        'interval' => $remind_mail_send_per_seconds,
		        'display' => sprintf( esc_html__( 'Every % seconds', 'ova-brw' ), $remind_mail_send_per_seconds )
		    );

		    // Remaining Invoice
		    $remaining_invoice_per_seconds = intval( ovabrw_get_setting( get_option( 'remaining_invoice_per_seconds', 86400 ) ) );

		    $schedules[$this->time_repeat_remaining_invoice] = array(
		        'interval' 	=> $remaining_invoice_per_seconds,
		        'display' 	=> sprintf( esc_html__( 'Every %s seconds', 'ova-brw' ), $remaining_invoice_per_seconds )
		    );

		    return $schedules;
		}

		public function ovabrw_check_scheduled() {
			if ( ! wp_next_scheduled( $this->hook_remind_pickup_date ) ) {
			    wp_schedule_event( time(), $this->time_repeat_remind_pickup_date, $this->hook_remind_pickup_date );
			}

			if ( ! wp_next_scheduled( $this->hook_remaining_invoice ) ) {
			    wp_schedule_event( time(), $this->time_repeat_remaining_invoice, $this->hook_remaining_invoice );
			}
		}

		public function ovabrw_deactivate_cron() {
		    $timestamp = wp_next_scheduled( $this->hook_remind_pickup_date );
		    wp_unschedule_event( $timestamp, $this->hook_remind_pickup_date );

		    // Remaining Invoice
		    $timestamp_next_remaining_invoice = wp_next_scheduled( $this->hook_remaining_invoice );
		    wp_unschedule_event( $timestamp_next_remaining_invoice, $this->hook_remaining_invoice );
		}

		public function ovabrw_remind_event_time() {
			if ( ovabrw_get_setting( get_option( 'remind_mail_enable', 'yes' ) ) == 'no' ) return;

			$send_x_day 		= intval( ovabrw_get_setting( get_option( 'remind_mail_before_xday', 1 ) ) );
			$send_before_x_time = current_time('timestamp') + $send_x_day*24*60*60;
			$order_ids 			= ovabrw_get_orders_feature();
			
			foreach( $order_ids as $key => $order_id ) {
				$order = wc_get_order( $order_id );

				// Get billing mail
				$customer_mail = $order->get_billing_email();

				// Get Meta Data type line_item of Order
    	    	$order_line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );

				foreach( $order_line_items as $item_id => $item ) {
					$product_name 	= $item->get_name();
					$product_id 	= $item->get_product_id();

					if ( $item && is_object( $item ) ) {
						if ( strtotime( $item->get_meta('ovabrw_pickup_date') ) > current_time('timestamp') && strtotime( $item->get_meta('ovabrw_pickup_date') ) < $send_before_x_time && apply_filters( 'ovabrw_reminder_other_condition', true, $item ) ) {
                            $ovabrw_pickup_date = $item->get_meta('ovabrw_pickup_date');
                            ovabrw_mail_remind_event_time( $order, $customer_mail, $product_name, $product_id, $ovabrw_pickup_date );
                        }
					}
				}
			}
		}

		public function ovabrw_remaining_invoice_event_time() {
			if ( ovabrw_get_setting( get_option( 'remaining_invoice_enable', 'yes' ) ) != 'yes' ) return;

			$send_x_day 		= intval( ovabrw_get_setting( get_option( 'remaining_invoice_before_xday', 1 ) ) );
			$send_email 		= ovabrw_get_setting( get_option( 'send_email_remaining_invoice_enable', 'yes' ) );
			$send_before_x_time = current_time('timestamp') + $send_x_day*24*60*60;
			$order_ids 			= ovabrw_get_orders_not_remaining_invoice();

			if ( ! empty( $order_ids ) && is_array( $order_ids ) ) {
				foreach( $order_ids as $order_id ) {
					$order = wc_get_order( $order_id );

					foreach( $order->get_items() as $item_id => $item ) {
						$remaining_amount 			= isset( $item['ovabrw_remaining_amount_product'] ) ? floatval( $item['ovabrw_remaining_amount_product'] ) : 0;
						$remaining_balance_order_id = isset( $item['ovabrw_remaining_balance_order_id'] ) ? absint( $item['ovabrw_remaining_balance_order_id'] ) : 0;

				        if ( ! $item || ! $remaining_amount || $remaining_balance_order_id || strtotime( $item['ovabrw_pickup_date'] ) > $send_before_x_time || strtotime( $item['ovabrw_pickup_date'] ) < current_time('timestamp') ) {
					        continue;
					    }

					    $total_remaining_amount = floatval( $item['ovabrw_remaining_amount_product'] );

			            $create_item = array(
			                'product'   => $item->get_product(),
			                'qty'       => $item['qty'],
			                'subtotal'  => $total_remaining_amount,
			                'total'     => $total_remaining_amount
			            );

			            $new_order_id = ovabrw_create_remaining_invoice( current_time( 'timestamp' ), $order_id, $create_item );

			            wc_add_order_item_meta( $item_id, 'ovabrw_remaining_balance_order_id', $new_order_id );

			            $original_payment = absint( get_post_meta( $order_id, '_ova_original_payment', true ) );
			            update_post_meta( $order_id, '_ova_original_payment', $original_payment + $total_remaining_amount );

			            if ( $send_email === 'yes' ) {
			                // Email invoice
			                $emails = WC_Emails::instance();
			                $emails->customer_invoice( wc_get_order( $new_order_id ) );
			            }
				    }
				}
			}
		}
	}
}

new Ovabrw_Cron();