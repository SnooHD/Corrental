<?php
extract( $args );

extract( $args );
extract( $instance );
echo trim($before_widget);

$content_html  = '';
$ele_obj = \Elementor\Plugin::$instance;
if ( '0' !== $item_template_id ) {
    $template_content = $ele_obj->frontend->get_builder_content_for_display( $item_template_id );

    if ( ! empty( $template_content ) ) {
        $content_html .= $template_content;
    } else {
        $content_html = '<div class="no-template-message"><span>' . esc_html__( 'The tabs are working. Please, note, that you have to add a template to the library in order to be able to display it inside the tabs.', 'nerf' ) . '</span></div>';
    }
} else {
    $content_html = '<div class="no-template-message"><span>' . esc_html__( 'Template is not defined.', 'nerf' ) . '</span></div>';
}
echo trim($content_html);
echo trim($after_widget);