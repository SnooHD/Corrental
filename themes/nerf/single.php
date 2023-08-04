<?php
get_header();
$sidebar_configs = nerf_get_blog_layout_configs();
$checksidebar = (nerf_get_config('blog_single_layout') == 'main') ?'only-main':'has-sidebar';
?>
	<?php if(nerf_get_config('blog_single_layout') == 'main') { ?>
		<section id="main-container" class="main-content main-content-detail inner only-main">
			<div id="main-content" class="<?php echo esc_attr($sidebar_configs['main']['class']); ?> ">
				<div id="primary" class="content-area">
					<div id="content" class="site-content detail-post" role="main">
						<?php
							// Start the Loop.
							while ( have_posts() ) : the_post();

								/*
								 * Include the post format-specific template for the content. If you want to
								 * use this in a child theme, then include a file called called content-___.php
								 * (where ___ is the post format) and that will be used instead.
								 */
								?>
									<?php get_template_part( 'template-posts/single/inner-v2' );
					                // If comments are open or we have at least one comment, load up the comment template.
									if ( comments_open() || get_comments_number() ) :
										comments_template();
									endif;
								// End the loop.
						    	endwhile;
						?>
					</div><!-- #content -->
				</div><!-- #primary -->
			</div>	
		</section>

	<?php } else { ?>

		<section id="main-container" class="main-content main-content-detail <?php echo apply_filters( 'nerf_blog_content_class', 'container' ); ?> inner <?php echo trim($checksidebar); ?>">
			
			<?php while ( have_posts() ) : the_post(); get_template_part( 'template-posts/single/header-detail' ); endwhile; ?>

			<?php nerf_before_content( $sidebar_configs ); ?>
			<div class="row">

				<?php nerf_display_sidebar_left( $sidebar_configs ); ?>

				<div id="main-content" class="<?php echo esc_attr($sidebar_configs['main']['class']); ?> ">
					<div id="primary" class="content-area">
						<div id="content" class="site-content detail-post" role="main">
							<?php
								// Start the Loop.
								while ( have_posts() ) : the_post();

									/*
									 * Include the post format-specific template for the content. If you want to
									 * use this in a child theme, then include a file called called content-___.php
									 * (where ___ is the post format) and that will be used instead.
									 */
									?>
										<?php get_template_part( 'template-posts/single/inner' );
						                // If comments are open or we have at least one comment, load up the comment template.
										if ( comments_open() || get_comments_number() ) :
											comments_template();
										endif;
									// End the loop.
							    	endwhile;
							?>
						</div><!-- #content -->
					</div><!-- #primary -->
				</div>	
				
				<?php nerf_display_sidebar_right( $sidebar_configs ); ?>
				
			</div>	
		</section>

	<?php } ?>

	<?php if ( nerf_get_config('show_blog_related', false) ): ?>
		<?php get_template_part( 'template-parts/posts-related' ); ?>
	<?php endif; ?>
<?php get_footer(); ?>