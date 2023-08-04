<?php
/**
 * Mixes
 *
 * @package    apus-nerf
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class Apus_Nerf_Mixes {
	
	public static function is_allowed_to_remove( $user_id, $item_id ) {
		$item = get_post( $item_id );

		if ( ! empty( $item->post_author ) ) {
			return $item->post_author == $user_id ;
		}

		return false;
	}
	
	public static function redirect($redirect_url) {
		if ( ! $redirect_url ) {
			$redirect_url = home_url( '/' );
		}

		wp_redirect( $redirect_url );
		exit();
	}
}
