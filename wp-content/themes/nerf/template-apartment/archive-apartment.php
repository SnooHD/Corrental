<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$sidebar_configs = nerf_get_apartment_layout_configs();
nerf_render_breadcrumbs();
$columns = nerf_get_config('apartments_columns', 3);
$bcol  = 12/$columns;
$item_style = nerf_get_config('apartments_style');

if ( defined('NERF_DEMO_MODE') && NERF_DEMO_MODE ) {
	if (!empty($_GET['style']) && ($_GET['style'] =='apartment1') ){
	    $item_style = '-v1';
	} elseif (!empty($_GET['style']) && ($_GET['style'] =='apartment2') ){
		$item_style = '-v2';
	}
}

?>
<section id="main-container" class="main-content  <?php echo apply_filters('nerf_apartment_content_class', 'container');?> inner">
	<?php nerf_before_content( $sidebar_configs ); ?>
	<div class="row">
		<?php nerf_display_sidebar_left( $sidebar_configs ); ?>

		<div id="main-content" class="col-12 <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
			<main id="main" class="site-main layout-apartment <?php echo esc_attr( (count($sidebar_configs)>1)?'has-sidebar':'' ); ?>" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header hidden">
					<?php
						the_archive_title( '<h1 class="page-title">', '</h1>' );
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
					?>
				</header><!-- .page-header -->

				<div class="row">
			        <?php while ( have_posts() ) : the_post(); ?>
			            <div class="col-12 col-sm-6 col-lg-<?php echo esc_attr($bcol); ?>">
			                <?php echo Apus_Nerf_Template_Loader::get_template_part( 'loop/inner-grid'. $item_style ); ?>
			            </div>
			        <?php endwhile; ?>
			    </div>

				<?php

				// Previous/next page navigation.
				nerf_paging_nav();

			// If no content, include the "No posts found" template.
			else :
				get_template_part( 'template-posts/content', 'none' );

			endif;
			?>

			</main><!-- .site-main -->
		</div><!-- .content-area -->
		
		<?php nerf_display_sidebar_right( $sidebar_configs ); ?>
		
	</div>
</section>
<?php get_footer(); ?>