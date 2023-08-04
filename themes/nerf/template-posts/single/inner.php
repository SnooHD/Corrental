<?php
$post_format = get_post_format();
global $post;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="inner">
        <div class="entry-content-detail <?php echo esc_attr((!has_post_thumbnail())?'not-img-featured':'' ); ?>">

            <div class="single-info">
                    <?php if(has_post_thumbnail()) { ?>
                        <div class="entry-thumb">
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