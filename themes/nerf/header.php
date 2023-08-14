<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage Nerf
 * @since Nerf 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
	<link rel="profile" href="//gmpg.org/xfn/11">
	<?php
if( class_exists( 'WPCF7' ) ) {
  function inject_wpcf7() {
    // Pages to add CF7 scripts
    $pages_cf7_add_scripts = array( 'home' );

    if( is_page( $pages_cf7_add_scripts ) ) {
      if( function_exists( 'wpcf7_enqueue_scripts' ) ) {
        wpcf7_enqueue_scripts();
				
      } 
      if( function_exists( 'wpcf7_enqueue_styles' ) ) {
        wpcf7_enqueue_styles();
      }
    }
  }
  add_filter( 'wpcf7_load_js', '__return_false' ); // Disable CF7 JavaScript
  add_filter( 'wpcf7_load_css', '__return_false' ); // Disable CF7 CSS
  add_action( 'wp_enqueue_scripts', 'inject_wpcf7', 10, 3 );

	// Load custom input js after WPCF7
	add_action( 'wp_enqueue_scripts', function(){
		wp_enqueue_script( 'custom-inputs-js', get_template_directory_uri() . '/js/customInputs.js', array( 'jquery' ), '1.0', true );
	}, 20, 3 );
}
	?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php if ( nerf_get_config('preload', true) ) {
	$preload_icon = nerf_get_config('media-preload-icon');
	$styles = array();
	if ( !empty($preload_icon) ) {
		$preload_icon_id = attachment_url_to_postid($preload_icon);
		if ( !empty($preload_icon_id) ) {
			$img = wp_get_attachment_image_src($preload_icon_id, 'full');
			if ( !empty($img[0]) ) {
				$styles[] = 'background-image: url(\''.$img[0].'\');';
			}
			if ( !empty($img[1]) ) {
				$styles[] = 'width: '.$img[1].'px;';
			}
			if ( !empty($img[1]) ) {
				$styles[] = 'height: '.$img[2].'px;';
			}
	    } else {
	    	$styles[] = 'background-image: url(\''.$preload_icon.'\');';
	    }
    }
    $style_attr = '';
    if ( !empty($styles) ) {
    	$style_attr = 'style="'.implode(' ', $styles).'"';
    }
?>
	<div class="apus-page-loading">
        <div class="apus-loader-inner" <?php echo trim($style_attr); ?>></div>
    </div>
<?php } ?>

<?php
if ( function_exists( 'wp_body_open' ) ) {
    wp_body_open();
}

$addclass = '';
if ( is_page() ) {
	$sidebar_configs = nerf_get_page_layout_configs();
	if( is_active_sidebar( 'sidebar-fixed' ) && !empty($sidebar_configs['left']['sidebar']) && $sidebar_configs['left']['sidebar'] == 'sidebar-fixed' ){
		$addclass = 'sidebar-fixed p-left';
	} elseif ( is_active_sidebar( 'sidebar-fixed' ) && !empty($sidebar_configs['right']['sidebar']) && $sidebar_configs['right']['sidebar'] == 'sidebar-fixed' ){
		$addclass = 'sidebar-fixed p-right';
	} else {
		$addclass = '';
	}
}

?>
<div id="wrapper-container" class="wrapper-container <?php echo esc_attr($addclass); ?>">

	<?php
		$header = apply_filters( 'nerf_get_header_layout', nerf_get_config('header_type') );
		if ( !empty($header) ) {
			nerf_display_header_builder($header);
		} else {
			get_template_part( 'headers/default' );
		}
	?>
	<div id="apus-main-content">