<?php

if ( ! function_exists( 'nerf_post_tags' ) ) {
	function nerf_post_tags() {
		$posttags = get_the_tags();
		if ( $posttags ) {
			echo '<span class="entry-tags-list">';

			$size = count( $posttags );
			foreach ( $posttags as $tag ) {
				echo '<a href="' . get_tag_link( $tag->term_id ) . '">';
				echo esc_attr($tag->name);
				echo '</a>';
			}
			echo '</span>';
		}
	}
}

if ( !function_exists('nerf_get_page_title') ) {
	function nerf_get_page_title() {
		$title = '';
		if ( !is_front_page() || is_paged() ) {
			global $post;
			$homeLink = esc_url( home_url() );

			if ( is_home() ) {
				$posts_page_id = get_option( 'page_for_posts');
				if ( $posts_page_id ) {
					$title = get_the_title( $posts_page_id );
				} else {
					$title = esc_html__( 'Blog', 'nerf' );
				}
			} elseif (is_category()) {
				global $wp_query;
				$cat_obj = $wp_query->get_queried_object();
				$title = $cat_obj->name;
			} elseif (is_day()) {
				$title = get_the_time('d');
			} elseif (is_month()) {
				$title = get_the_time('F');
			} elseif (is_year()) {
				$title = get_the_time('Y');
			} elseif (is_single() && !is_attachment()) {
				$title = '';
			} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() && !is_author() && !is_search() ) {
				$post_type = get_post_type_object(get_post_type());

				if ( is_object($post_type) ) {
					$title = $post_type->labels->singular_name;
				}
			} elseif (is_404()) {
				$title = esc_html__('Error 404', 'nerf');
			} elseif (is_attachment()) {
				$title = get_the_title();
			} elseif ( is_page() && !$post->post_parent ) {
				$title = get_the_title();
			} elseif ( is_page() && $post->post_parent ) {
				$title = get_the_title();
			} elseif ( is_search() ) {
				$title = sprintf(esc_html__('Search results for "%s"', 'nerf'), get_search_query());
			} elseif ( is_tag() ) {
				$title = sprintf(esc_html__('Posts tagged "%s"', 'nerf'), single_tag_title('', false) );
			} elseif ( is_author() ) {
				global $author;
				$userdata = get_userdata($author);
				$title = $userdata->display_name;
			} elseif ( is_404() ) {
				$title = esc_html__('Error 404', 'nerf');
			}
		}else{
			$title = get_the_title();
		}
		return $title;
	}
}

if ( ! function_exists( 'nerf_breadcrumbs' ) ) {
	function nerf_breadcrumbs() {

		$delimiter = ' ';
		$home = esc_html__('Home', 'nerf');
		$before = '<li><span class="active">';
		$after = '</span></li>';
		
		if ( !is_front_page() || is_paged()) {
			global $post;
			$homeLink = esc_url( home_url() );
			
			echo '<ol class="breadcrumb">';
			echo '<li><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . '</li> ';

			if (is_category()) {
				global $wp_query;
				$cat_obj = $wp_query->get_queried_object();
				$thisCat = $cat_obj->term_id;
				$thisCat = get_category($thisCat);
				$parentCat = get_category($thisCat->parent);
				echo '<li>';
				if ($thisCat->parent != 0)
					echo get_category_parents($parentCat, TRUE, '</li><li>');
				echo '<span class="active">'.single_cat_title('', false) . $after;
			} elseif (is_day()) {
				echo '<li><a href="' . esc_url( get_year_link(get_the_time('Y')) ) . '">' . get_the_time('Y') . '</a></li> ' . $delimiter . ' ';
				echo '<li><a href="' . esc_url( get_month_link(get_the_time('Y'),get_the_time('m')) ) . '">' . get_the_time('F') . '</a></li> ' . $delimiter . ' ';
				echo trim($before) . get_the_time('d') . $after;
			} elseif (is_month()) {
				echo '<a href="' . esc_url( get_year_link(get_the_time('Y')) ) . '">' . get_the_time('Y') . '</a></li> ' . $delimiter . ' ';
				echo trim($before) . get_the_time('F') . $after;
			} elseif (is_year()) {
				echo trim($before) . get_the_time('Y') . $after;
			} elseif (is_single() && !is_attachment()) {

				if ( get_post_type() != 'post' ) {
					$post_type = get_post_type_object(get_post_type());
					$slug = $post_type->rewrite;
					
					echo '<li><a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a></li> ' . $delimiter . ' ';
					echo trim($before) . get_the_title() . $after;
				} elseif ( get_post_type() == 'post' ) {
					global $post;
					$cat = get_the_category(); $cat = $cat[0];
					echo '<li>'.get_category_parents($cat, TRUE, '</li><li class="hidden">');
					echo '<span class="active">'. $post->post_title . $after;
				} else {
					$cat = get_the_category(); $cat = $cat[0];
					echo '<li>'.get_category_parents($cat, TRUE, '</li>');
				}
			} elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404() && !is_author() && !is_search()) {

				$post_type = get_post_type_object(get_post_type());
				if (is_object($post_type)) {
					echo trim($before) . $post_type->labels->singular_name . $after;
				}
			} elseif (is_404()) {
				echo trim($before) .esc_html__('Error 404', 'nerf') . $after;
			} elseif (is_attachment()) {
				$parent = get_post($post->post_parent);
				$cat = get_the_category($parent->ID);
				echo '<li>';
				if ( !empty($cat) ) {
					$cat = $cat[0];
					echo get_category_parents($cat, TRUE, '</li><li>');
				}
				if ( !empty($parent) ) {
					echo '<a href="' . esc_url( get_permalink($parent) ) . '">' . $parent->post_title . '</a></li><li>';
				}
				echo '<span class="active">'.get_the_title() . $after;
			} elseif ( is_page() && !$post->post_parent ) {
				echo trim($before) . get_the_title() . $after;
			} elseif ( is_page() && $post->post_parent ) {
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					$breadcrumbs[] = '<li><a href="' . esc_url( get_permalink($page->ID) ) . '">' . get_the_title($page->ID) . '</a></li>';
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				foreach ($breadcrumbs as $crumb) {
					echo trim($crumb) . ' ' . $delimiter . ' ';
				}
				echo trim($before) . get_the_title() . $after;
			} elseif ( is_search() ) {
				echo trim($before) . sprintf(esc_html__('Search results for "%s"','nerf'), get_search_query()) . $after;
			} elseif ( is_tag() ) {
				echo trim($before) . sprintf(esc_html__('Posts tagged "%s"', 'nerf'), single_tag_title('', false)) . $after;
			} elseif ( is_author() ) {
				global $author;
				$userdata = get_userdata($author);
				echo trim($before) . esc_html__('Articles posted by ', 'nerf') . $userdata->display_name . $after;
			} elseif ( is_404() ) {
				echo trim($before) . esc_html__('Error 404', 'nerf') . $after;
			} elseif ( is_home() ) {
				$posts_page_id = get_option( 'page_for_posts');
				if ( $posts_page_id ) {
					$label = get_the_title( $posts_page_id );
				} else {
					$label = esc_html__( 'Blog', 'nerf' );
				}
				echo trim($before) . $label . $after;
			}

			echo '</ol>';
		}
	}
}

function nerf_get_taxonomy_parents( $id, $taxonomy = 'category', $link = false, $separator = '/', $nicename = false, $visited = array() ) {
    $chain = '';
    $parent = get_term( $id, $taxonomy );
    if ( is_wp_error( $parent ) ) {
        return $parent;
    }
    if ( $nicename ) {
        $name = $parent->slug;
    } else {
        $name = $parent->name;
    }

    if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
        $visited[] = $parent->parent;
        $chain .= nerf_get_taxonomy_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
    }

    if ( $link ) {
        $chain .= '<a href="' . esc_url( get_term_link( $parent,$taxonomy ) ) . '" title="' . esc_attr( sprintf( esc_html__( 'View all posts in %s', 'nerf' ), $parent->name ) ) . '">'.$name.'</a>' . $separator;
 	} else {
        $chain .= $name.$separator;
    }
    return $chain;
}

if ( ! function_exists( 'nerf_render_breadcrumbs' ) ) {
	function nerf_render_breadcrumbs($show_title = true) {
		global $post;
		$has_bg = '';
		$show = true;
		$style = $classes = array();
		$full_width = 'container';
		if ( is_page() && is_object($post) ) {
			$show = get_post_meta( $post->ID, 'apus_page_show_breadcrumb', true );
			if ( $show == 'no' ) {
				return ''; 
			}
			$bgimage_id = get_post_meta( $post->ID, 'apus_page_breadcrumb_image_id', true );
			$bgcolor = get_post_meta( $post->ID, 'apus_page_breadcrumb_color', true );
			$style = array();
			if ( $bgcolor ) {
				$style[] = 'background-color:'.$bgcolor;
			}
			if ( $bgimage_id ) {
				$img = wp_get_attachment_image_src($bgimage_id, 'full');
				if ( !empty($img[0]) ) {
					$style[] = 'background-image:url(\''.esc_url($img[0]).'\')';
					$has_bg = 1;
				}
			}
			$full_width = apply_filters('nerf_page_content_class', 'container');
		} elseif ( is_singular('post') || is_category() || is_home() || is_search() ) {
			$show = nerf_get_config('show_blog_breadcrumbs', true);
			if ( !$show || is_front_page() ) {
				return ''; 
			}
			$breadcrumb_img = nerf_get_config('blog_breadcrumb_image');
	        $breadcrumb_color = nerf_get_config('blog_breadcrumb_color');
	        $style = array();
	        if ( $breadcrumb_color ) {
	            $style[] = 'background-color:'.$breadcrumb_color;
	        }
	        if ( !empty($breadcrumb_img) ) {
	            $style[] = 'background-image:url(\''.esc_url($breadcrumb_img).'\')';
	            $has_bg = 1;
	        }
	        
	        $full_width = apply_filters('nerf_blog_content_class', 'container');
		} elseif ( is_singular('apartment') || is_post_type_archive('apartment') || is_tax('apartment_category') ) {
			$show = nerf_get_config('apartments_breadcrumbs', true);
			if ( !$show || is_front_page() ) {
				return ''; 
			}
			$breadcrumb_img = nerf_get_config('apartments_breadcrumb_image');
	        $breadcrumb_color = nerf_get_config('apartments_breadcrumb_color');

	        $style = array();
	        if ( $breadcrumb_color ) {
	            $style[] = 'background-color:'.$breadcrumb_color;
	        }
	        if ( !empty($breadcrumb_img) ) {
	            $style[] = 'background-image:url(\''.esc_url($breadcrumb_img).'\')';
	            $has_bg = 1;
	        }

	        $full_width = apply_filters('nerf_apartments_content_class', 'container');
		}
		$estyle = !empty($style)? ' style="'.implode(";", $style).'"':"";
		$classes[] = $has_bg ? 'has_bg' :'';

		if ( $show_title ) {
			$classes[] = 'show-title';
		}

		echo '<section id="apus-breadscrumb" class="breadcrumb-page apus-breadscrumb '.implode(' ', $classes).'"'.$estyle.'><div class="'.$full_width.'"><div class="wrapper-breads">
		<div class="wrapper-breads-inner">';
			if ( $show_title ) {
				$title = nerf_get_page_title();
				
				echo '<div class="breadscrumb-inner clearfix">';
				echo '<h2 class="bread-title">'.$title.'</h2>';
				echo '</div>';
			}

			nerf_breadcrumbs();
			
		echo '</div>';

		echo '</div></div></section>';
	}
}

function nerf_render_breadcrumbs_simple() {
	echo '<div class="breadcrumbs-simple">';
			nerf_breadcrumbs();
	echo '</div>';
}

if ( ! function_exists( 'nerf_paging_nav' ) ) {
	function nerf_paging_nav() {
		global $wp_query, $wp_rewrite;

		if ( $wp_query->max_num_pages < 2 ) {
			return;
		}

		$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$query_args   = array();
		$url_parts    = explode( '?', $pagenum_link );

		if ( isset( $url_parts[1] ) ) {
			wp_parse_str( $url_parts[1], $query_args );
		}

		$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
		$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

		$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

		// Set up paginated links.
		$links = paginate_links( array(
			'base'     => $pagenum_link,
			'format'   => $format,
			'total'    => $wp_query->max_num_pages,
			'current'  => $paged,
			'mid_size' => 1,
			'add_args' => array_map( 'urlencode', $query_args ),
			'prev_text' => '<i class="flaticon-left-arrow"></i>',
			'next_text' => '<i class="flaticon-right-arrow"></i>',
		) );

		if ( $links ) :

		?>
		<nav class="navigation paging-navigation" role="navigation">
			<h1 class="screen-reader-text hidden"><?php esc_html_e( 'Posts navigation', 'nerf' ); ?></h1>
			<div class="apus-pagination">
				<?php echo trim($links); ?>
			</div><!-- .pagination -->
		</nav><!-- .navigation -->
		<?php
		endif;
	}
}

if ( !function_exists('nerf_comment_form') ) {
	function nerf_comment_form($arg) {
		global $post;
		if ('open' == $post->comment_status) {
			ob_start();
	      	comment_form($arg);
	      	$form = ob_get_clean();
	      	?>
	      	<div class="commentform reset-button-default">
		    	<div class="clearfix">
			      	<?php
			      	echo str_replace('id="commentform"','id="commentform" enctype="multipart/form-data"', $form);
			      	?>
		      	</div>
	      	</div>
	      	<?php
	      }
	}
}

if (!function_exists('nerf_comment_item') ) {
	function nerf_comment_item($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
		ob_start();
		?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">

			<div class="the-comment">
				<?php
					if ( ($comment->comment_type != 'trackback') && ($comment->comment_type != 'pingback') ) {
				?>
					<div class="avatar">
						<?php echo get_avatar( $comment->user_id, 70 ); ?>
					</div>
				<?php } ?>
				<div class="comment-box">
					<div class="clearfix">
						
						<div class="d-sm-flex">
							<div class="inner-left">
								<h3 class="name-comment"><?php echo get_comment_author_link() ?></h3>
								<div class="date text-theme"><?php printf(esc_html__('%1$s', 'nerf'), get_comment_date()) ?></div>
							</div>
							<div class="ms-auto">
								<div class="comment-author">
									<?php edit_comment_link('<i class="ti-pencil"></i>'.esc_html__('Edit', 'nerf'),'','') ?>
									<?php comment_reply_link(array_merge( $args, array( 'reply_text' => '<span class="text-reply"><i class="ti-back-left"></i>'.esc_html__('Reply', 'nerf').'</span>', 'add_below' => 'comment', 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
								</div>
							</div>
						</div>
					</div>
					<div class="comment-text">
						<?php if ($comment->comment_approved == '0') : ?>
						<em><?php esc_html_e('Your comment is awaiting moderation.', 'nerf') ?></em>
						<br />
						<?php endif; ?>
						<?php comment_text() ?>
					</div>
				</div>
			</div>
		<?php
		$output = ob_get_clean();
		echo apply_filters('nerf_comment_item', $output, $comment, $args, $depth);
	}
}

function nerf_comment_field_to_bottom( $fields ) {
	$comment_field = $fields['comment'];
	unset( $fields['comment'] );
	$fields['comment'] = $comment_field;
	return $fields;
}
//add_filter( 'comment_form_fields', 'nerf_comment_field_to_bottom' );


function nerf_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'nerf_pingback_header' );


function nerf_paginate_links( $args = array() ) {
    global $wp_rewrite;

    $defaults = array(
        'base' => add_query_arg( 'cpage', '%#%' ),
        'format' => '',
        'total' => 1,
        'current' => 1,
        'add_fragment' => '#comments',
        'prev_text' => '&larr;',
        'next_text' => '&rarr;',
        'type'      => 'list',
        'end_size'  => 3,
        'mid_size'  => 3
    );
    if ( $wp_rewrite->using_permalinks() )
        $defaults['base'] = user_trailingslashit(trailingslashit(get_permalink()) . $wp_rewrite->comments_pagination_base . '-%#%', 'commentpaged');

    $args = wp_parse_args( $args, $defaults );
    ?>
    <nav class="manager-pagination apus-pagination">
        <?php echo paginate_links( $args ); ?>
    </nav>
    <?php
}

/*
 * create placeholder
 * var size: array( width, height )
 */
function nerf_create_placeholder($size) {
	return "data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20viewBox%3D'0%200%20".$size[0]."%20".$size[1]."'%2F%3E";
}

function nerf_display_sidebar_left( $sidebar_configs ) {
	if ( isset($sidebar_configs['left']) ) : ?>
		<div class="sidebar-wrapper <?php echo esc_attr($sidebar_configs['left']['class']) ;?>">
		  	<aside class="sidebar sidebar-left" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
		  		<div class="close-sidebar-btn d-lg-none"> <i class="ti-close"></i> <span><?php esc_html_e('Close', 'nerf'); ?></span></div>
		   		<?php if ( is_active_sidebar( $sidebar_configs['left']['sidebar'] ) ): ?>
		   			<?php dynamic_sidebar( $sidebar_configs['left']['sidebar'] ); ?>
		   		<?php endif; ?>
		  	</aside>
		</div>
	<?php endif;
}

function nerf_display_sidebar_right( $sidebar_configs ) {
	if ( isset($sidebar_configs['right']) ) : ?>
		<div class="sidebar-wrapper <?php echo esc_attr($sidebar_configs['right']['class']) ;?>">
		  	<aside class="sidebar sidebar-right" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
		  		<div class="close-sidebar-btn d-lg-none"><i class="ti-close"></i> <span><?php esc_html_e('Close', 'nerf'); ?></span></div>
		   		<?php if ( is_active_sidebar( $sidebar_configs['right']['sidebar'] ) ): ?>
			   		<?php dynamic_sidebar( $sidebar_configs['right']['sidebar'] ); ?>
			   	<?php endif; ?>
		  	</aside>
		</div>
	<?php endif;
}

function nerf_before_content( $sidebar_configs ) {
	if ( isset($sidebar_configs['left']) || isset($sidebar_configs['right']) ) : ?>
		<a href="javascript:void(0)" class="mobile-sidebar-btn d-lg-none <?php echo esc_attr( isset($sidebar_configs['left']) ? 'btn-left':'btn-right' ); ?>"><i class="ti-menu-alt"></i></a>
		<div class="mobile-sidebar-panel-overlay"></div>
	<?php endif;
}
