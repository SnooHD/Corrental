<?php
/**
 * Post Type: Private Apartment
 *
 * @package    apus-nerf
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class Apus_Nerf_Post_Type_Apartment {
	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	  	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'metaboxes' ) );
	}

	public static function register_post_type() {

		$singular = __( 'Apartment', 'apus-nerf' );
		$plural   = __( 'Apartments', 'apus-nerf' );

		$labels = array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'add_new'               => sprintf(__( 'Add New %s', 'apus-nerf' ), $singular),
			'add_new_item'          => sprintf(__( 'Add New %s', 'apus-nerf' ), $singular),
			'edit_item'             => sprintf(__( 'Edit %s', 'apus-nerf' ), $singular),
			'new_item'              => sprintf(__( 'New %s', 'apus-nerf' ), $singular),
			'all_items'             => sprintf(__( 'All %s', 'apus-nerf' ), $plural),
			'view_item'             => sprintf(__( 'View %s', 'apus-nerf' ), $singular),
			'search_items'          => sprintf(__( 'Search %s', 'apus-nerf' ), $singular),
			'not_found'             => sprintf(__( 'No %s found', 'apus-nerf' ), $plural),
			'not_found_in_trash'    => sprintf(__( 'No %s found in Trash', 'apus-nerf' ), $plural),
			'parent_item_colon'     => '',
			'menu_name'             => $plural,
		);

		register_post_type( 'apartment',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' ),
				'public'            => true,
		        'has_archive'       => true,
		        'has_archive'       => _x( 'apartments', 'Apartments Archive slug - resave permalinks after changing this', 'apus-nerf' ),
				'rewrite'           => array(
						'slug'       => _x( 'apartment', 'Apartment slug - resave permalinks after changing this', 'apus-nerf' ),
						'with_front' => false
					),
				'show_in_rest'		=> true,
				'menu_icon'         => 'dashicons-admin-post',
			)
		);
	}

	/**
	 *
	 */
	public static function metaboxes( array $metaboxes ) {
		$prefix = APUS_NERF_PREFIX;
		
		$metaboxes[ $prefix . 'info' ] = array(
			'id'                        => $prefix . 'info',
			'title'                     => __( 'More Information', 'apus-nerf' ),
			'object_types'              => array( 'apartment' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'fields'                    => self::metaboxes_info_fields()
		);
		
		return $metaboxes;
	}
	/**
	 *
	 */	
	public static function metaboxes_info_fields() {
		$prefix = APUS_NERF_PREFIX;

		$fields = array(
			array(
			    'name' => __( 'Area', 'apus-nerf' ),
			    'id' => $prefix.'area',
			    'type' => 'text',
			)
		);
		
		return apply_filters( 'apus_nerf_postype_apartment_metaboxes_fields' , $fields, $prefix );
	}
}
Apus_Nerf_Post_Type_Apartment::init();


