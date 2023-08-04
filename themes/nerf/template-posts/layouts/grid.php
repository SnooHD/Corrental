<?php
    $bcol = floor( 12 / $args['columns'] );
?>
<div class="layout-blog">
    <div class="row">
        <?php while ( have_posts() ) : the_post(); ?>
            <div class="col-md-<?php echo esc_attr($bcol); ?> col-12 col-sm-6">
                <?php get_template_part( 'template-posts/loop/inner', $args['items_style'], array('thumbsize' => $args['thumbsize']) ); ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>