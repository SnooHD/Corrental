<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Tabs extends Widget_Base {

    public function get_name() {
        return 'apus_element_tabs';
    }

    public function get_title() {
        return esc_html__( 'Apus Tabs', 'nerf' );
    }

    public function get_icon() {
        return 'eicon-tabs';
    }

    public function get_categories() {
        return [ 'nerf-elements' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Tabs', 'nerf' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $tabs = new Repeater();

        $tabs->add_control(
            'title', [
                'label' => esc_html__( 'Title', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $ele_obj = \Elementor\Plugin::$instance;
        $templates = $ele_obj->templates_manager->get_source( 'local' )->get_items();

        if ( empty( $templates ) ) {

            $this->add_control(
                'no_templates',
                array(
                    'label' => false,
                    'type'  => Controls_Manager::RAW_HTML,
                    'raw'   => $this->empty_templates_message(),
                )
            );

            return;
        }

        $options = [
            '0' => '— ' . esc_html__( 'Select', 'nerf' ) . ' —',
        ];

        $types = [];

        foreach ( $templates as $template ) {
            $options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
            $types[ $template['template_id'] ] = $template['type'];
        }

        $tabs->add_control(
            'content_type',
            [
                'label'       => esc_html__( 'Content Type', 'nerf' ),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'template',
                'options'     => [
                    'template' => esc_html__( 'Template', 'nerf' ),
                    'editor'   => esc_html__( 'Editor', 'nerf' ),
                ],
                'label_block' => 'true',
            ]
        );

        $tabs->add_control(
            'item_template_id',
            [
                'label'       => esc_html__( 'Choose Template', 'nerf' ),
                'type'        => Controls_Manager::SELECT,
                'default'     => '0',
                'options'     => $options,
                'types'       => $types,
                'label_block' => 'true',
                'condition'   => [
                    'content_type' => 'template',
                ]
            ]
        );

        $tabs->add_control(
            'content',
            [
                'label'      => esc_html__( 'Content', 'nerf' ),
                'type'       => Controls_Manager::WYSIWYG,
                'default'    => esc_html__( 'Tab Item Content', 'nerf' ),
                'dynamic' => [
                    'active' => true,
                ],
                'condition'   => [
                    'content_type' => 'editor',
                ]
            ]
        );


        $this->add_control(
            'title', [
                'label' => esc_html__( 'Title', 'nerf' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Enter title here' , 'nerf' ),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'tabs',
            [
                'label' => esc_html__( 'Tabs', 'nerf' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $tabs->get_controls(),
            ]
        );
        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'nerf' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'nerf'),
                    'st_white' => esc_html__('White', 'nerf'),
                ),
                'default' => ''
            ]
        );

        $this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'nerf' ),
                'type'          => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'nerf' ),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings();

        extract( $settings );
        $_id = nerf_random_key();

        ?>
        <div class="widget-tabs <?php echo esc_attr($el_class.' '.$style); ?>">
            <div class="d-lg-flex align-items-center top-widget-info">

                <?php if ( !empty($title) ): ?>
                    <h2 class="title">
                        <?php echo trim( $title ); ?>
                    </h2>
                <?php endif; ?>

                <div class="<?php echo esc_attr ( !empty($title)?'ms-auto':'clearfix' ); ?>">
                    <ul role="tablist" class="nav nav-tabs tabs-apartment flex-nowrap <?php echo esc_attr($style); ?>">
                        <?php $i = 0; foreach ($tabs as $tab) : ?>
                            <li>
                                <a href="#tab-<?php echo esc_attr($_id);?>-<?php echo esc_attr($i); ?>" class="<?php echo esc_attr($i == 0 ? 'active' : '');?>" data-bs-toggle="tab">
                                    <?php if ( !empty($tab['title']) ) { ?>
                                        <?php echo esc_attr($tab['title']); ?>
                                    <?php } ?>
                                </a>
                            </li>
                        <?php $i++; endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="tab-content">

                <?php $i = 0; foreach ($tabs as $tab) : ?>
                    <div id="tab-<?php echo esc_attr($_id);?>-<?php echo esc_attr($i); ?>" class="tab-pane fade <?php echo esc_attr($i == 0 ? 'show active' : ''); ?>">

                        <div class="tabs-inner">

                            <?php
                            $ele_obj = \Elementor\Plugin::$instance;
                            $content_html = '';
                            switch ( $tab[ 'content_type' ] ) {
                                case 'template':

                                    if ( '0' !== $tab['item_template_id'] ) {

                                        $template_content = $ele_obj->frontend->get_builder_content_for_display( $tab['item_template_id'] );

                                        if ( ! empty( $template_content ) ) {
                                            $content_html .= $template_content;

                                            if ( Plugin::$instance->editor->is_edit_mode() ) {
                                                $link = add_query_arg(
                                                    array(
                                                        'elementor' => '',
                                                    ),
                                                    get_permalink( $tab['item_template_id'] )
                                                );

                                                $content_html .= sprintf( '<div class="nerf__edit-cover" data-template-edit-link="%s"><i class="fa fa-pencil"></i><span>%s</span></div>', $link, esc_html__( 'Edit Template', 'nerf' ) );
                                            }
                                        } else {
                                            $content_html = $this->no_template_content_message();
                                        }
                                    } else {
                                        $content_html = $this->no_templates_message();
                                    }
                                break;

                                case 'editor':
                                    if ( !empty($tab['content']) ) {
                                        $content_html = trim( $tab['content'] );
                                    }
                                break;
                            }
                            echo trim($content_html);
                            ?>
                            
                        </div>
                    </div>
                <?php $i++; endforeach; ?>
            </div>
        </div>
        <?php
    }

    public function no_templates_message() {
        return '<div class="no-template-message"><span>' . esc_html__( 'Template is not defined.', 'nerf' ) . '</span></div>';
    }

    public function no_template_content_message() {
        return '<div class="no-template-message"><span>' . esc_html__( 'The tabs are working. Please, note, that you have to add a template to the library in order to be able to display it inside the tabs.', 'nerf' ) . '</span></div>';
    }

    public function empty_templates_message() {
        $output = '<div id="elementor-widget-template-empty-templates">';
            $output .= '<div class="elementor-widget-template-empty-templates-icon"><i class="eicon-nerd"></i></div>';
            $output .= '<div class="elementor-widget-template-empty-templates-title">' . esc_html__( 'You Haven’t Saved Templates Yet.', 'nerf' ) . '</div>';
            $output .= '<div class="elementor-widget-template-empty-templates-footer">';
                $output .= esc_html__( 'What is Library?', 'nerf' );
                $output .= '<a class="elementor-widget-template-empty-templates-footer-url" href="https://go.elementor.com/docs-library/" target="_blank">' . esc_html__( 'Read our tutorial on using Library templates.', 'nerf' ) . '</a>';
            $output .= '</div>';
        $output .= '</div>';

        return $output;
    }
    
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Tabs );
} else {
    Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Tabs );
}