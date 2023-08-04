<?php
get_header();
$sidebar_configs = nerf_get_blog_layout_configs();
$columns = nerf_get_config('blog_columns', 1);
$layout = nerf_get_config( 'blog_display_mode', 'list' );
$items_style = nerf_get_config( 'blog_item_style','grid');
nerf_render_breadcrumbs();

$thumbsize = !isset($thumbsize) ? nerf_get_config( 'blog_item_thumbsize', 'full' ) : $thumbsize;

if ( defined('NERF_DEMO_MODE') && NERF_DEMO_MODE ) {
	if (!empty($_GET['style']) && ($_GET['style'] =='gridsidebar2') ){
	    $columns = 3;
	    $layout = 'grid';
	    $items_style = 'grid-v2';
	    $sidebar_configs['main'] = array( 'class' => 'col-12' );
	    $sidebar_configs['right']['sidebar'] = 'd-none';
	} elseif (!empty($_GET['style']) && ($_GET['style'] =='gridsidebar3') ){
		$columns = 3;
	    $layout = 'grid';
	    $items_style = 'grid-v3';
	    $thumbsize = '450x450';
	    $sidebar_configs['main'] = array( 'class' => 'col-12' );
	    $sidebar_configs['right']['sidebar'] = 'd-none';
	} elseif (!empty($_GET['style']) && ($_GET['style'] =='list') ){
		$columns = 1;
	    $layout = 'list';
	    $thumbsize = '930x500';
	   	$sidebar_configs['main'] = array( 'class' => 'col-lg-8 col-12' );
	    $sidebar_configs['right']= array( 'sidebar' => 'blog-sidebar',  'class' => 'sidebar-blog col-lg-4 col-12' );
	}
}
?>
<section id="main-container" class="main-content <?php echo apply_filters('nerf_blog_content_class', 'container');?> inner">
	<?php nerf_before_content( $sidebar_configs ); ?>
	<div class="row responsive-medium">
		<?php nerf_display_sidebar_left( $sidebar_configs ); ?>

		<div id="main-content" class="col-12 <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
			<div id="main" class="site-main layout-blog" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header d-none">
					<?php
						the_archive_title( '<h1 class="page-title">', '</h1>' );
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
					?>
				</header><!-- .page-header -->

				<?php
				if ( empty($sidebar_configs['left']) && empty($sidebar_configs['right']) && nerf_get_config('blog_archive_top_categories', false) )	{
				?>
					<div class="blog-header-categories">
						<?php
						$terms = get_terms(array(
							'taxonomy' => 'category',
							'hide_empty' => true,
						));
						if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
							$selected = '';
							if ( is_category() ) {
								global $wp_query;
								$term =	$wp_query->queried_object;
								if ( isset( $term->term_id) ) {
									$selected = $term->term_id;
								}
							}
							?>
						    <ul class="categories-list">
							    <?php foreach ( $terms as $term ) { ?>
							        <li><a href="<?php get_term_link($term); ?>" class="<?php echo esc_attr($term->term_id == $selected ? 'active' : ''); ?>"><?php echo esc_html($term->name); ?></a></li>
							    <?php } ?>
						    </ul>
						<?php } ?>
					</div>
				<?php }
				get_template_part( 'template-posts/layouts/'.$layout, null, array('columns' => $columns, 'thumbsize' => $thumbsize, 'items_style' => $items_style) );

				// Previous/next page navigation.
				nerf_paging_nav();

			// If no content, include the "No posts found" template.
			else :
				get_template_part( 'template-posts/content', 'none' );

			endif;
			?>

			</div><!-- .site-main -->
		</div><!-- .content-area -->
		
		<?php nerf_display_sidebar_right( $sidebar_configs ); ?>
		
	</div>
</section>
<?php get_footer(); ?>