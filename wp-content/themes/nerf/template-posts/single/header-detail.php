<?php
$post_format = get_post_format();
global $post;
?>
<div class="top-detail-post">
    <div class="d-md-flex">
        <div class="top-detail-info col-md-9 col-12">
            <?php if (get_the_title()) { ?>
                <h1 class="detail-title">
                    <?php the_title(); ?>
                </h1>
            <?php } ?>
            <div class="detail-post-info d-flex align-items-center flex-nowrap">
                <div class="d-flex align-items-center info-author">
                    <div class="avatar-img">
                        <?php echo get_avatar( get_the_author_meta( 'ID' ),40 ); ?>
                    </div>
                    <h4 class="author-title">
                        <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                            <?php echo get_the_author(); ?>
                        </a>
                    </h4>
                </div>
                <div class="date"><?php the_time( get_option('date_format', 'd M, Y') ); ?></div>
                <?php nerf_post_categories($post); ?>
            </div>
        </div>
        <div class="ms-auto d-none d-md-block">
            <a class="btn-readmore d-inline-flex align-items-center" href="<?php echo esc_url(get_permalink( get_option( 'page_for_posts' ) ))?>"> <i class="flex-shrink-0 direction-circle d-flex align-items-center justify-content-center flaticon-up-right-arrow"></i><span class="space-nowrap"><?php echo esc_html__('SEE ALL NEWS','nerf') ?></span></a>
        </div>
    </div>
</div>