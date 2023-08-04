<?php

if ( ! function_exists( 'nerf_body_classes' ) ) {
	function nerf_body_classes( $classes ) {
		global $post;
		$show_footer_mobile = nerf_get_config('show_footer_mobile', true);

		if ( is_page() && is_object($post) ) {
			$class = get_post_meta( $post->ID, 'apus_page_extra_class', true );
			if ( !empty($class) ) {
				$classes[] = trim($class);
			}
			if(get_post_meta( $post->ID, 'apus_page_header_transparent',true) && get_post_meta( $post->ID, 'apus_page_header_transparent',true) == 'yes' ){
				$classes[] = 'header_transparent';
			}
			if(get_post_meta( $post->ID, 'apus_page_header_fixed',true) && get_post_meta( $post->ID, 'apus_page_header_fixed',true) == 'yes' ){
				$classes[] = 'header_fixed';
			}
			// layout
			if(get_post_meta( $post->ID, 'apus_page_layout', true )){
				$classes[] = get_post_meta( $post->ID, 'apus_page_layout', true );
			}
		}

		if( is_post_type_archive('apartment') ){
			$breadcrumb_img = nerf_get_config('apartments_breadcrumb_image');
			if ( !empty($breadcrumb_img) ) {
				$classes[] = 'header_transparent';
			}
		}

		if(is_singular('apartment')){
			if ( has_post_thumbnail() ) {
				$classes[] = 'header_transparent';
			}
		}

		if( is_category() || is_home() || is_search()){
			$breadcrumb_img = nerf_get_config('blog_breadcrumb_image');
			if ( !empty($breadcrumb_img) ) {
				$classes[] = 'header_transparent';
			}
		}

		if ( nerf_get_config('preload', true) ) {
			$classes[] = 'apus-body-loading';
		}
		if ( nerf_get_config('image_lazy_loading') ) {
			$classes[] = 'image-lazy-loading';
		}
		if ( $show_footer_mobile ) {
			$classes[] = 'body-footer-mobile';
		}

		if ( nerf_get_config('keep_header') ) {
			$classes[] = 'has-header-sticky';
		}

		if ( is_404() ) {
			$classes[] = 'header_transparent';
		}
		
		return $classes;
	}
	add_filter( 'body_class', 'nerf_body_classes' );
}

if ( !function_exists('nerf_get_header_layouts') ) {
	function nerf_get_header_layouts() {
		$headers = array();
		$args = array(
			'posts_per_page'   => -1,
			'offset'           => 0,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => 'apus_header',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			$headers[$post->post_name] = $post->post_title;
		}
		return $headers;
	}
}

if ( !function_exists('nerf_get_header_layout') ) {
	function nerf_get_header_layout() {
		global $post;
		if ( is_page() && is_object($post) && isset($post->ID) ) {
			global $post;
			$header = get_post_meta( $post->ID, 'apus_page_header_type', true );
			if ( empty($header) || $header == 'global' ) {
				return nerf_get_config('header_type');
			}
			return $header;
		}
		return nerf_get_config('header_type');
	}
	add_filter( 'nerf_get_header_layout', 'nerf_get_header_layout' );
}

function nerf_display_header_builder($header_slug) {
	$args = array(
		'name'        => $header_slug,
		'post_type'   => 'apus_header',
		'post_status' => 'publish',
		'numberposts' => 1
	);
	$posts = get_posts($args);
	foreach ( $posts as $post ) {
		if ( nerf_get_config('keep_header') ) {
			$classes = array('apus-header d-none d-xl-block');
		}else{
			$classes = array('apus-header no_keep_header d-none d-xl-block');
		}
		$classes[] = $post->post_name.'-'.$post->ID;

		echo '<div id="apus-header" class="'.esc_attr(implode(' ', $classes)).'">';
		if ( nerf_get_config('keep_header') ) {
	        echo '<div class="main-sticky-header">';
	    }
			echo apply_filters( 'nerf_generate_post_builder', do_shortcode( $post->post_content ), $post, $post->ID);
		if ( nerf_get_config('keep_header') ) {
			echo '</div>';
	    }
		echo '</div>';
	}
}

if ( !function_exists('nerf_get_footer_layouts') ) {
	function nerf_get_footer_layouts() {
		$footers = array();
		$args = array(
			'posts_per_page'   => -1,
			'offset'           => 0,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => 'apus_footer',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			$footers[$post->post_name] = $post->post_title;
		}
		return $footers;
	}
}

if ( !function_exists('nerf_get_footer_layout') ) {
	function nerf_get_footer_layout() {
		if ( is_page() ) {
			global $post;
			$footer = '';
			if ( is_object($post) && isset($post->ID) ) {
				$footer = get_post_meta( $post->ID, 'apus_page_footer_type', true );
				if ( empty($footer) || $footer == 'global' ) {
					return nerf_get_config('footer_type', '');
				}
			}
			return $footer;
		}
		return nerf_get_config('footer_type', '');
	}
	add_filter('nerf_get_footer_layout', 'nerf_get_footer_layout');
}

function nerf_display_footer_builder($footer_slug) {
	$show_footer_desktop_mobile = nerf_get_config('show_footer_desktop_mobile', false);
	$args = array(
		'name'        => $footer_slug,
		'post_type'   => 'apus_footer',
		'post_status' => 'publish',
		'numberposts' => 1
	);
	$posts = get_posts($args);
	foreach ( $posts as $post ) {
		$classes = array('apus-footer footer-builder-wrapper');
		if ( !$show_footer_desktop_mobile ) {
			$classes[] = '';
		}
		$classes[] = $post->post_name;


		echo '<div id="apus-footer" class="'.esc_attr(implode(' ', $classes)).'">';
		echo '<div class="apus-footer-inner">';
		echo apply_filters( 'nerf_generate_post_builder', do_shortcode( $post->post_content ), $post, $post->ID);
		echo '</div>';
		echo '</div>';
	}
}

if ( !function_exists('nerf_blog_content_class') ) {
	function nerf_blog_content_class( $class ) {
		$page = 'archive';
		if ( is_singular( 'post' ) ) {
            $page = 'single';
        }
		if ( nerf_get_config('blog_'.$page.'_fullwidth') ) {
			return 'container-fluid';
		}
		return $class;
	}
}
add_filter( 'nerf_blog_content_class', 'nerf_blog_content_class', 1 , 1  );


if ( !function_exists('nerf_get_blog_layout_configs') ) {
	function nerf_get_blog_layout_configs() {
		$page = 'archive';
		if ( is_singular( 'post' ) ) {
            $page = 'single';
        }
		$left = nerf_get_config('blog_'.$page.'_left_sidebar');
		$right = nerf_get_config('blog_'.$page.'_right_sidebar');

		switch ( nerf_get_config('blog_'.$page.'_layout') ) {
		 	case 'left-main':
			 	if ( is_active_sidebar( $left ) ) {
			 		$configs['left'] = array( 'sidebar' => $left, 'class' => 'sidebar-blog col-lg-4 col-12'  );
			 		$configs['main'] = array( 'class' => 'col-lg-8 col-12' );
			 	}
		 		break;
		 	case 'main-right':
		 		if ( is_active_sidebar( $right ) ) {
			 		$configs['right'] = array( 'sidebar' => $right,  'class' => 'sidebar-blog col-lg-4 col-12' ); 
			 		$configs['main'] = array( 'class' => 'col-lg-8 col-12' );
			 	}
		 		break;
	 		case 'main':
	 			$configs['main'] = array( 'class' => 'col-12' );
	 			break;
		 	default:
		 		if ( is_active_sidebar( 'sidebar-default' ) ) {
			 		$configs['right'] = array( 'sidebar' => 'sidebar-default',  'class' => 'sidebar-blog col-lg-4 col-12' ); 
			 		$configs['main'] = array( 'class' => 'col-lg-8 col-12' );
			 	} else {
			 		$configs['main'] = array( 'class' => 'col-12' );
			 	}
		 		break;
		}
		if ( empty($configs) ) {
			if ( is_active_sidebar( 'sidebar-default' ) ) {
		 		$configs['right'] = array( 'sidebar' => 'sidebar-default',  'class' => 'sidebar-blog col-lg-4 col-12' ); 
		 		$configs['main'] = array( 'class' => 'col-lg-8 col-12' );
		 	} else {
		 		$configs['main'] = array( 'class' => 'col-12' );
		 	}
		}
		return $configs; 
	}
}

if ( !function_exists('nerf_page_content_class') ) {
	function nerf_page_content_class( $class ) {
		global $post;
		if (is_object($post)) {
			$fullwidth = get_post_meta( $post->ID, 'apus_page_fullwidth', true );
			if ( !$fullwidth || $fullwidth == 'no' ) {
				return $class;
			}
		}
		return 'clearfix';
	}
}
add_filter( 'nerf_page_content_class', 'nerf_page_content_class', 1 , 1  );

if ( !function_exists('nerf_get_page_layout_configs') ) {
	function nerf_get_page_layout_configs() {
		global $post;
		if ( is_object($post) ) {
			$left = get_post_meta( $post->ID, 'apus_page_left_sidebar', true );
			$right = get_post_meta( $post->ID, 'apus_page_right_sidebar', true );

			switch ( get_post_meta( $post->ID, 'apus_page_layout', true ) ) {
			 	case 'left-main':
			 		if ( is_active_sidebar( $left ) ) {
				 		$configs['left'] = array( 'sidebar' => $left, 'class' => 'sidebar-blog col-lg-4 col-12'  );
				 		$configs['main'] = array( 'class' => 'col-lg-8 col-12' );
				 	}
			 		break;
			 	case 'main-right':
			 		if ( is_active_sidebar( $right ) ) {
				 		$configs['right'] = array( 'sidebar' => $right,  'class' => 'sidebar-blog col-lg-4 col-12' ); 
				 		$configs['main'] = array( 'class' => 'col-lg-8 col-12' );
				 	}
			 		break;
		 		case 'main':
		 			$configs['main'] = array( 'class' => 'col-12' );
		 			break;
			 	default:
			 		if ( nerf_is_woocommerce_activated() && (is_cart() || is_checkout()) ) {
			 			$configs['main'] = array( 'class' => 'col-12' );
			 		} elseif ( is_active_sidebar( 'sidebar-default' ) ) {
				 		$configs['right'] = array( 'sidebar' => 'sidebar-default',  'class' => 'sidebar-blog col-lg-4 col-12' ); 
				 		$configs['main'] = array( 'class' => 'col-lg-8 col-12' );
				 	} else {
				 		$configs['main'] = array( 'class' => 'col-12' );
				 	}
			 		break;
			}

			if ( empty($configs) ) {
				if ( is_active_sidebar( 'sidebar-default' ) ) {
			 		$configs['right'] = array( 'sidebar' => 'sidebar-default',  'class' => 'sidebar-blog col-lg-4 col-12' ); 
			 		$configs['main'] = array( 'class' => 'col-lg-8 col-12' );
			 	} else {
			 		$configs['main'] = array( 'class' => 'col-12' );
			 	}
			}
		} else {
			$configs['main'] = array( 'class' => 'col-12' );
		}
		return $configs; 
	}
}

if ( !function_exists( 'nerf_random_key' ) ) {
    function nerf_random_key($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $return = '';
        for ($i = 0; $i < $length; $i++) {
            $return .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $return;
    }
}

if ( !function_exists('nerf_substring') ) {
    function nerf_substring($string, $limit, $afterlimit = '[...]') {
        if ( empty($string) ) {
        	return $string;
        }
       	$string = explode(' ', wp_strip_all_tags( $string ), $limit);

        if (count($string) >= $limit) {
            array_pop($string);
            $string = implode(" ", $string) .' '. $afterlimit;
        } else {
            $string = implode(" ", $string);
        }
        $string = preg_replace('`[[^]]*]`','',$string);
        return strip_shortcodes( $string );
    }
}

function nerf_hex2rgb( $color ) {
	$color = trim( $color, '#' );

	if ( strlen( $color ) == 3 ) {
		$r = hexdec( substr( $color, 0, 1 ).substr( $color, 0, 1 ) );
		$g = hexdec( substr( $color, 1, 1 ).substr( $color, 1, 1 ) );
		$b = hexdec( substr( $color, 2, 1 ).substr( $color, 2, 1 ) );
	} else if ( strlen( $color ) == 6 ) {
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
	} else {
		return array();
	}

	return array( 'r' => $r, 'g' => $g, 'b' => $b );
}

function nerf_generate_rgba( $rgb, $opacity ) {
	$output = 'rgba('.$rgb['r'].', '.$rgb['g'].', '.$rgb['b'].', '.$opacity.');';

	return $output;
}

function nerf_is_apus_frame_activated() {
	return defined('APUS_FRAME_VERSION') ? true : false;
}

function nerf_is_cmb2_activated() {
	return defined('CMB2_LOADED') ? true : false;
}

function nerf_is_woocommerce_activated() {
	return class_exists( 'woocommerce' ) ? true : false;
}

function nerf_is_revslider_activated() {
	return class_exists( 'RevSlider' ) ? true : false;
}

function nerf_is_mailchimp_activated() {
	return class_exists( 'MC4WP_Form_Manager' ) ? true : false;
}

function nerf_is_devvn_ihotspot_activated() {
	return defined('DEVVN_IHOTSPOT_VER') ? true : false;
}

function abolire_is_apus_nerf_activated() {
	return class_exists( 'Apus_Nerf' ) ? true : false;
}

function nerf_header_footer_templates( $template ) {
	$post_type = get_post_type();
	if ( $post_type ) {
		$custom_post_types = array( 'apus_footer', 'apus_header', 'apus_megamenu', 'elementor_library' );
		if ( in_array( $post_type, $custom_post_types ) ) {
			if ( is_single() ) {
				$post_type = str_replace('_', '-', $post_type);
				return get_template_directory() . '/single-apus-elementor.php';
			}
		}
	}

	return $template;
}
add_filter( 'template_include', 'nerf_header_footer_templates' );



// add_action('init', 'nerf_add_cpt_points_image', 100);
// function nerf_add_cpt_points_image()
// {
//     add_post_type_support('points_image', 'editor');
// }

function nerf_add_author_metabox_to_cpt_points_image( $args, $post_type ) {
    if ($post_type != 'points_image') {
        return $args;
    }
    $args['supports'] = array( 'title', 'editor' ); // set the 'supports' array
    $args['show_in_rest'] = false;
    return $args;
}
add_filter( 'register_post_type_args', 'nerf_add_author_metabox_to_cpt_points_image', 10, 2 );