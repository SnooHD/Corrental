<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress
 * @subpackage Nerf
 * @since Nerf 1.0
 */
/*
*Template Name: 404 Page
*/
get_header();

$bg_img = nerf_get_config('404_bg_img');
$style = '';

if ( !empty($bg_img) ) {
	$style = 'style="background-image: url('.$bg_img.');"';
} else {
	$style = 'style="background-image: url('.get_template_directory_uri().'/images/bg-404.jpg'.');"';
}

?>
<section class="page-404 justify-content-center d-flex align-items-center" <?php echo trim($style); ?>>
	<div id="main-container" class="inner">
		<div id="main-content" class="main-page">
			<section class="error-404 not-found clearfix">
				<div class="container">
						<div class="clearfix">
							<div class="content-inner text-center">
								<h1 class="heading-404">
									<?php
									$heading = nerf_get_config('404_heading');
									if ( !empty($heading) ) {
										echo esc_html($heading);
									} else {
										esc_html_e('404', 'nerf');
									}
									?>
								</h1>
								<h3 class="title-404">
									<?php
									$title = nerf_get_config('404_title');
									if ( !empty($title) ) {
										echo esc_html($title);
									} else {
										esc_html_e('Sorry we can\'t find that page!', 'nerf');
									}
									?>
								</h3>
								<div class="description">
									<?php
									$description = nerf_get_config('404_description');
									if ( !empty($description) ) {
										echo esc_html($description);
									} else {
										esc_html_e(' The page you \'re looking for isn\'t available.', 'nerf');
									}
									?>
								</div>
								<div class="page-content">
									<div class="return">
										<a class="btn-theme btn btn-lg" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e('Go Back To Homepage','nerf') ?></a>
									</div>
								</div><!-- .page-content -->
							</div>
						</div>
				</div>
			</section><!-- .error-404 -->
		</div><!-- .content-area -->
	</div>
</section>
<?php get_footer(); ?>