<?php 
global $post;
$thumbsize = !isset($args['thumbsize']) ? nerf_get_config( 'blog_item_thumbsize', 'full' ) : $args['thumbsize'];
$thumb = nerf_display_post_thumb($thumbsize);
?>
<article <?php post_class('post post-layout post-list-item'); ?>>
    <div class="clearfix">
        <?php
        if ( !empty($thumb) ) {
            ?>
            <div class="top-image">
                <?php
                    echo trim($thumb);
                ?>
                <div class="date d-flex align-items-center">
                    <div class="day"><?php the_time('d'); ?></div>
                    <?php the_time('M'); ?>
                </div>
             </div>
            <?php
        } ?>
        <div class="col-content">
            <?php nerf_post_categories_first($post); ?>
            <?php if ( empty($thumb) ) { ?>
                <div class="date"><?php the_time( get_option('date_format', 'd M, Y') ); ?></div>
            <?php } ?>
            <?php if (get_the_title()) { ?>
                <h4 class="entry-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h4>
            <?php } ?>
            <div class="description"><?php echo nerf_substring( get_the_excerpt(),25, '...' ); ?></div>
            <a class="btn-readmore d-inline-flex align-items-center" href="<?php the_permalink(); ?>"> <i class="flex-shrink-0 direction-circle d-flex align-items-center justify-content-center flaticon-up-right-arrow"></i><span class="space-nowrap"><?php echo esc_html__('Read More','nerf') ?></span></a>
        </div>
    </div>
</article>