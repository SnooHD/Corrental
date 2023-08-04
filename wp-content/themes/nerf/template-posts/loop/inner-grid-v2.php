<?php 
global $post;
$thumbsize = !isset($args['thumbsize']) ? nerf_get_config( 'blog_item_thumbsize', 'full' ) : $args['thumbsize'];
$thumb = nerf_display_post_thumb($thumbsize);
?>
<article <?php post_class('post post-layout'); ?>>
    <div class="d-flex flex-column post-grid v2">
        <?php nerf_post_categories_first($post); ?>
        <?php if (get_the_title()) { ?>
            <h4 class="entry-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h4>
        <?php } ?>
        <div class="date-bottom d-flex align-items-center">
            <div class="day"><?php the_time('d'); ?></div>
            <div class="ms-auto"><?php the_time('M'); ?></div>
        </div>
    </div>
</article>