<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
	<section id="primary" class="content-area">
		<main id="main" class="site-main content container" role="main">
			<?php if ( have_posts() ) : ?>
				<header class="page-header">
					<?php
						the_archive_title( '<h1 class="page-title">', '</h1>' );
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
					?>
				</header><!-- .page-header -->
				
				
				<div class="apuseslate-archive-container">
					<?php while ( have_posts() ) : the_post(); ?>
							<?php echo Apus_Nerf_Template_Loader::get_template_part( 'content-apartment' ); ?>
					<?php endwhile; ?>
				</div>
				<?php the_posts_pagination( array(
					'prev_text'          => __( 'Previous page', 'apus-nerf' ),
					'next_text'          => __( 'Next page', 'apus-nerf' ),
					'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'apus-nerf' ) . ' </span>',
				) ); ?>
				
			<?php else : ?>
				<?php get_template_part( 'content', 'none' ); ?>
			<?php endif; ?>

		</main><!-- .site-main -->
	</section><!-- .content-area -->


<?php get_footer(); ?>
