<?php 
global $post;
$thumb = nerf_display_post_thumb('135x135');
?>
<article <?php post_class('post post-layout post-list-item-small'); ?>>
    <div class="d-flex align-items-center">
        <?php
        if ( !empty($thumb) ) {
            ?>
            <div class="top-image flex-shrink-0">
                <?php
                    echo trim($thumb);
                ?>
             </div>
            <?php
        } ?>
        <div class="col-content flex-grow-1">
            <?php nerf_post_categories_first($post); ?>
            <?php if (get_the_title()) { ?>
                <h4 class="entry-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h4>
            <?php } ?>
            <div class="date"><?php the_time( get_option('date_format', 'd M, Y') ); ?></div>
        </div>
    </div>
</article>