
<article id="post-<?php the_ID(); ?>" itemscope itemtype="" <?php post_class(); ?>>
	
	<?php if ( has_post_thumbnail() ) { ?>
		<div class="event-thumb">
			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
				<?php the_post_thumbnail( 'post-thumbnail', array( 'alt' => get_the_title() ) ); ?>
			</a>
		</div>
	<?php } ?>
	
	<?php the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
	<?php the_excerpt(); ?>
	
	<meta itemprop="url" content="<?php the_permalink(); ?>" />
</article>