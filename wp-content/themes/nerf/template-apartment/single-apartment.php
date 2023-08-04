<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main content" role="main">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post();
					global $post;
					$style = '';
					$addclass = 'no-img';
					if ( has_post_thumbnail() ) {
						$url = get_the_post_thumbnail_url($post, 'full');
						$style = 'style="background-image:url('.$url.');"';
						$addclass = "has-img";
					}
				?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="apartment-heading position-relative <?php echo esc_attr($addclass); ?>" <?php echo trim($style); ?>>
							<div class="container d-lg-flex align-items-center position-relative">
								<div class="apartment-heading-left">
									<h3 class="entry-title">
										<?php the_title(); ?>
									</h3>
									<?php nerf_render_breadcrumbs_simple(); ?>
								</div>
								<div class="ms-auto">
									<a class="btn-readmore d-inline-flex align-items-center flex-wrap" href="<?php echo esc_url(get_post_type_archive_link('apartment')); ?>"> <i class="direction-circle d-flex align-items-center justify-content-center flaticon-up-right-arrow"></i> <?php echo esc_html__('SEE ALL APARTMENTS','nerf') ?></a>

								</div>
							</div>
						</div>
						<div class="entry-content container"><?php the_content(); ?></div>
					</article>
				<?php endwhile; ?>

				<?php the_posts_pagination( array(
					'prev_text'          => __( 'Previous page', 'nerf' ),
					'next_text'          => __( 'Next page', 'nerf' ),
					'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'nerf' ) . ' </span>',
				) ); ?>
			<?php else : ?>
				<?php get_template_part( 'content', 'none' ); ?>
			<?php endif; ?>

		</main>
	</section>

<?php get_footer(); ?>