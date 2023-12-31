<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main content container" role="main">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="post-thumbnail">
							<?php the_post_thumbnail(); ?>
						</div>

						<h3 class="entry-title">
							<?php the_title(); ?>
						</h3>
						<div class="entry-content"><?php the_content(); ?></div>

					</article>
				<?php endwhile; ?>

				<?php the_posts_pagination( array(
					'prev_text'          => __( 'Previous page', 'apus-nerf' ),
					'next_text'          => __( 'Next page', 'apus-nerf' ),
					'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'apus-nerf' ) . ' </span>',
				) ); ?>
			<?php else : ?>
				<?php get_template_part( 'content', 'none' ); ?>
			<?php endif; ?>

		</main>
	</section>

<?php get_footer(); ?>
