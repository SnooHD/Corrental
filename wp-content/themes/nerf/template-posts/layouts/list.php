<?php
    $bcol = floor( 12 / $args['columns'] );
?>
<div class="layout-posts-list">
    <div class="row">
        <?php while ( have_posts() ) : the_post(); ?>
            <div class="col-xl-<?php echo esc_attr($bcol); ?> col-12">
                <?php get_template_part( 'template-posts/loop/inner-list', null, array('thumbsize' => $args['thumbsize']) ); ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>