<?php

function nerf_apartment_theme_folder($folder) {
    return "template-apartment";
}
add_filter( 'apus-nerf-theme-folder-name', 'nerf_apartment_theme_folder', 10 );

if ( !function_exists('nerf_apartment_content_class') ) {
    function nerf_apartment_content_class( $class ) {
        $prefix = 'apartments';
        if ( is_singular( 'apartment' ) ) {
            $prefix = 'apartment';
        }
        if ( nerf_get_config($prefix.'_fullwidth') ) {
            return 'container-fluid';
        }
        return $class;
    }
}
add_filter( 'nerf_apartment_content_class', 'nerf_apartment_content_class', 1 , 1  );


if ( !function_exists('nerf_get_apartment_layout_configs') ) {
    function nerf_get_apartment_layout_configs() {
        $prefix = 'apartments';
        if ( is_singular( 'apartment' ) ) {
            $prefix = 'apartment';
        }
        $left = nerf_get_config($prefix.'_left_sidebar');
        $right = nerf_get_config($prefix.'_right_sidebar');

        switch ( nerf_get_config($prefix.'_layout') ) {
            case 'left-main':
                if ( is_active_sidebar( $left ) ) {
                    $configs['left'] = array( 'sidebar' => $left, 'class' => 'col-md-4 col-sm-12 col-xs-12'  );
                    $configs['main'] = array( 'class' => 'col-md-8 col-sm-12 col-xs-12 pull-right' );
                }
                break;
            case 'main-right':
                if ( is_active_sidebar( $right ) ) {
                    $configs['right'] = array( 'sidebar' => $right,  'class' => 'col-md-4 col-sm-12 col-xs-12 pull-right' ); 
                    $configs['main'] = array( 'class' => 'col-md-8 col-sm-12 col-xs-12' );
                }
                break;
            case 'main':
                $configs['main'] = array( 'class' => 'col-md-12 col-sm-12 col-xs-12' );
                break;
            default:
                $configs['right'] = array( 'sidebar' => 'sidebar-default',  'class' => 'col-md-3 col-xs-12' ); 
                $configs['main'] = array( 'class' => 'col-md-9 col-xs-12' );
                break;
        }
        if ( empty($configs) ) {
            $configs['right'] = array( 'sidebar' => 'sidebar-default',  'class' => 'col-md-3 col-xs-12' ); 
            $configs['main'] = array( 'class' => 'col-md-9 col-xs-12' );
        }
        return $configs; 
    }
}

add_action( 'pre_get_posts', 'nerf_apartment_archive' );
function nerf_apartment_archive($query) {
    $suppress_filters = ! empty( $query->query_vars['suppress_filters'] ) ? $query->query_vars['suppress_filters'] : '';

    if ( ! is_post_type_archive( 'apartment' ) || ! $query->is_main_query() || is_admin() || $query->query_vars['post_type'] != 'apartment' || $suppress_filters ) {
        return;
    }

    $limit = nerf_get_config('number_apartments_per_page', 10);
    $query_vars = &$query->query_vars;
    $query_vars['posts_per_page'] = $limit;
    $query->query_vars = $query_vars;
    
    return $query;
}
