<?php
global $post;
wp_enqueue_script('addthis');
?>
<div class="apus-social-share text-center">
	
	<?php if ( nerf_get_config('facebook_share', 1) ): ?>

		<a class="facebook" href="https://www.facebook.com/sharer.php?s=100&u=<?php the_permalink(); ?>&i=<?php echo urlencode($img); ?>" target="_blank" title="<?php echo esc_attr__('Share on facebook', 'nerf'); ?>">
			<i class="fab fa-facebook-f"></i>
		</a>

	<?php endif; ?>
	<?php if ( nerf_get_config('twitter_share', 1) ): ?>
		<a class="twitter" href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>" target="_blank" title="<?php echo esc_attr__('Share on Twitter', 'nerf'); ?>">
			<i class="fab fa-twitter"></i>
		</a>

	<?php endif; ?>
	<?php if ( nerf_get_config('linkedin_share', 1) ): ?>

		<a class="linkedin" href="https://linkedin.com/shareArticle?mini=true&amp;url=<?php the_permalink(); ?>&amp;title=<?php the_title(); ?>" target="_blank" title="<?php echo esc_attr__('Share on LinkedIn', 'nerf'); ?>">
			<i class="fab fa-linkedin-in"></i>
		</a>

	<?php endif; ?>

	<?php if ( nerf_get_config('pinterest_share', 1) ): ?>

		<a class="pinterest" href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink()); ?>&amp;media=<?php echo urlencode($img); ?>" target="_blank" title="<?php echo esc_attr__('Share on Pinterest', 'nerf'); ?>">
			<i class="fab fa-pinterest-p"></i>
		</a>

	<?php endif; ?>
</div>