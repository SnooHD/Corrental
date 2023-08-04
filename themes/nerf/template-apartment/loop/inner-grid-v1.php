<?php
global $post;
$thumbsize = !isset($args['thumbsize']) ? 'nerf-apartment' : $args['thumbsize'];
$thumb = nerf_display_post_thumb($thumbsize);
?>
<article id="post-<?php the_ID(); ?>" itemscope <?php post_class(); ?>>
		<?php if ( has_post_thumbnail() ) { ?>
			<div class="apartment-layout position-relative v1">
				<?php
                    echo trim($thumb);
                ?>
				<a class="explore" href="<?php the_permalink(); ?>"><?php esc_html_e('EXPLORE','nerf') ?></a>
		<?php } else { ?>
			<div class="apartment-layout">
		<?php } ?>
		<div class="info">
			<?php the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
			
			<?php
				$area = get_post_meta($post->ID, APUS_NERF_PREFIX.'area', true);
				if ( $area ) {
					?>
					<div class="area">
						<?php echo trim($area); ?>
					</div>
					<?php
				}
			?>
			<meta itemprop="url" content="<?php the_permalink(); ?>" />
		</div>
	</div>
</article>