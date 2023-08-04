<?php
$post_format = get_post_format();
global $post;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="inner">
        <div class="entry-content-detail <?php echo esc_attr((!has_post_thumbnail())?'not-img-featured':'' ); ?>">

            <div class="single-info">
                    <div class="top-detail-post">
                        <div class="container">
                            <div class="d-md-flex">
                                <div class="top-detail-info col-md-10 col-12">
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
                    </div>
                    <?php if(has_post_thumbnail()) { ?>
                        <div class="entry-thumb text-center">
                            <?php
                                $thumb = nerf_post_thumbnail();
                                echo trim($thumb);
                            ?>
                        </div>
                    <?php } ?>
                    <div class="inner-detail">
                        <div class="entry-description">
                            <?php
                                the_content();
                            ?>
                        </div>
                        <?php
                        wp_link_pages( array(
                            'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'nerf' ) . '</span>',
                            'after'       => '</div>',
                            'link_before' => '<span>',
                            'link_after'  => '</span>',
                            'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'nerf' ) . ' </span>%',
                            'separator'   => '',
                        ) );
                        ?>
                    
                        <?php  
                            $posttags = get_the_tags();
                        ?>
                        <?php if( !empty($posttags) || nerf_get_config('show_blog_social_share', false) ){ ?>
                            <div class="tag-social d-md-flex align-items-center w-100">
                                <?php if( nerf_get_config('show_blog_social_share', false) ) { ?>
                                    <?php get_template_part( 'template-parts/sharebox' ); ?>
                                <?php } ?>
                                <?php if(!empty($posttags)){ ?>
                                    <div class="<?php echo esc_attr( (nerf_get_config('show_blog_social_share', false))?'ms-auto':'' ); ?>">
                                        <?php nerf_post_tags(); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php get_template_part( 'template-parts/author-bio' ); ?>
                        
                        <?php
                            //Previous/next post navigation.
                            the_post_navigation( array(
                                'next_text' => 
                                    '<div class="inner inner-right">'.
                                    '<div class="navi">' . esc_html__( 'Next', 'nerf' ) . '<i class="ti-angle-right"></i></div>'.
                                    '<span class="title-direct">%title</span></div>',
                                'prev_text' => 
                                    '<div class="inner inner-left">'.
                                    '<div class="navi"><i class="ti-angle-left"></i>' . esc_html__( 'Prev', 'nerf' ) . '</div>'.
                                    '<span class="title-direct">%title</span></div>',
                            ) );
                        ?>
                    </div>
            </div>
        </div>
    </div>
</article>