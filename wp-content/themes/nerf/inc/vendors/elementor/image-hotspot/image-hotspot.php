<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nerf_Elementor_Image_Hotspot extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_image_hotspot';
    }

	public function get_title() {
        return esc_html__( 'Apus Image Hotspot', 'nerf' );
    }
    
	public function get_categories() {
        return [ 'nerf-elements' ];
    }

	protected function register_controls() {
        $hotspots = array();

        $args = array(
            'post_type' => 'points_image',
            'numberposts' => -1,
            'post_status' => 'publish',
        );
        $posts = get_posts($args);

        if ( $posts ) {
            foreach ( $posts as $post ) {
                $hotspots[ $post->ID ] = $post->post_title;
            }
        } else {
            $hotspots[ 0 ] = esc_html__( 'No hotspot found', 'nerf' );
        }
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Image Hotspots', 'nerf' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'hotspot_id',
            [
                'label' => esc_html__( 'Image Hotspot', 'nerf' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $hotspots,
                'description' => esc_html__( 'Select your Image Hotspot.', 'nerf' ),
            ]
        );
        $this->add_control(
            'layout_type',
            [
                'label' => esc_html__( 'Layout Type', 'nerf' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'default' => esc_html__('Default', 'nerf'),
                    'style1' => esc_html__('Style 1', 'nerf'),
                ),
                'default' => 'default',
                'description' => esc_html__( 'Select your Image Hotspot.', 'nerf' ),
            ]
        );
        $this->add_control(
            'show_content',
            [
                'label' => esc_html__( 'Show Content', 'nerf' ),
                'type' => Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
                'label_on' => esc_html__( 'Hide', 'nerf' ),
                'label_off' => esc_html__( 'Show', 'nerf' ),
            ]
        );
   		$this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'nerf' ),
                'type'          => Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'nerf' ),
            ]
        );

        $this->end_controls_section();
    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings );
        
        if ( $hotspot_id ) {
            if ( get_post_status($hotspot_id) != "publish") {
                return;
            }
            ?>
            <div class="widget-image-hotspot d-lg-flex <?php echo esc_attr($el_class.' '.$layout_type); ?>">
                <?php

                $data_post = get_post_meta($hotspot_id, 'hotspot_content', true);

                if( !$data_post ) {
                    $data_post = maybe_unserialize(get_post_field('post_content', $hotspot_id));
                }
                    
                $maps_images = (isset($data_post['maps_images']))?$data_post['maps_images']:'';
                $data_points = (isset($data_post['data_points']))?$data_post['data_points']:'';
                $pins_image = (isset($data_post['pins_image']))?$data_post['pins_image']:'';
                $pins_image_hover = (isset($data_post['pins_image_hover']))?$data_post['pins_image_hover']:'';
                $pins_more_option = wp_parse_args($data_post['pins_more_option'],array(
                    'position'          =>  'center_center',
                    'custom_top'        =>  0,
                    'custom_left'       =>  0,
                    'custom_hover_top'  =>  0,
                    'custom_hover_left' =>  0,
                    'pins_animation'    =>  'none'
                )); 

                if($maps_images):
                    if ( $layout_type != 'style1' && is_array($data_points) && !empty($data_points) && ($show_content === 'yes') ) {
                ?>
                    <div class="wrap_svl_content col-lg-4 flex-shrink-0">
                        <?php $stt = 1;foreach ($data_points as $point) {
                            if( isset($point['content']) && !empty($point['content']) ) { ?>
                                <div id="image-hotspot-point-<?php echo esc_attr($stt); ?>" class="image-hotspot-point <?php echo esc_attr(($stt==1)?'active':''); ?>"><?php echo apply_filters('the_content', $point['content']);?></div>
                            <?php
                            }
                        $stt++; } ?>
                    </div>
                <?php } ?>
                <div class="wrap_svl_center flex-grow-1">
                    <div class="wrap_svl_center_box d-block">
                        <div class="wrap_svl" id="body_drag_<?php echo esc_attr($hotspot_id);?>">
                            <div class="images_wrap">
                                <img src="<?php echo esc_url($maps_images); ?>" alt="">
                            </div>  
                            <?php if(is_array($data_points)):?>
                                <?php $stt = 1;foreach ($data_points as $point):
                                    $pins_image = (isset($data_post['pins_image']))?$data_post['pins_image']:'';
                                    $pins_image_hover = (isset($data_post['pins_image_hover']))?$data_post['pins_image_hover']:'';
                                
                                    $linkpins = isset($point['linkpins'])?esc_url($point['linkpins']):'';   
                                    $link_target = isset($point['link_target'])?esc_attr($point['link_target']): '_self';
                                    $pins_image_custom = isset($point['pins_image_custom'])?esc_url($point['pins_image_custom']):'';
                                    $pins_image_hover_custom = isset($point['pins_image_hover_custom'])?esc_url($point['pins_image_hover_custom']):'';
                                    $placement = (isset($point['placement']) && $point['placement'] != '')?esc_attr($point['placement']):'n';
                                    $pins_id = (isset($point['pins_id']) && $point['pins_id'] != '')?esc_attr($point['pins_id']):'';
                                    $pins_class = (isset($point['pins_class']) && $point['pins_class'] != '')?esc_attr($point['pins_class']):'';

                                    if($pins_image_custom) {
                                        $pins_image = $pins_image_custom;
                                    }
                                    if($pins_image_hover_custom) {
                                        $pins_image_hover = $pins_image_hover_custom;
                                    }
                                 
                                    $noTooltip = false;
                                    ob_start();
                                    if(isset($point['content'])) {
                                        if(!empty($point['content'])) { ?>
                                            <div class="box_view_html"><?php echo apply_filters('the_content', $point['content']);?></div>
                                        <?php } else {
                                            $noTooltip = true;
                                        }
                                    }
                                    if($layout_type == 'style1'){
                                        $noTooltip = true;
                                    }
                                    $view_html = ob_get_clean();
                                    ?>
                                    <div data-content_id="image-hotspot-point-<?php echo esc_attr($stt); ?>" class="drag_element tips <?php echo esc_attr($pins_class ? $pins_class : ''); ?>" style="top:<?php echo esc_attr($point['top']); ?>%;left:<?php echo esc_attr($point['left']); ?>%;" <?php echo trim($pins_id ? 'id="'.$pins_id.'"':''); ?>>
                                        <div class="point_style <?php echo esc_attr($pins_image_hover)?'has-hover':''?> ihotspot_tooltop_html" data-placement="<?php echo esc_attr($placement);?>" data-html="<?php echo esc_html($view_html)?>">
                                            <?php if($linkpins):?><a href="<?php echo esc_url($linkpins);?>" <?php echo trim($link_target)?'target="'.$link_target.'"':'';?>><?php endif;?>
                                                <?php if ($pins_more_option['pins_animation'] != 'none') { ?>
                                                    <div class="pins_animation ihotspot_<?php echo esc_attr($pins_more_option['pins_animation']);?>" style="top:-<?php echo trim($pins_more_option['custom_top']);?>px;left:-<?php echo trim($pins_more_option['custom_left']);?>px;height:<?php echo intval($pins_more_option['custom_top']*2)?>px;width:<?php echo intval($pins_more_option['custom_left']*2)?>px"></div>
                                                <?php } ?>
                                                <img alt="" src="<?php echo esc_url($pins_image); ?>" class="pins_image <?php if(!$noTooltip):?>ihotspot_hastooltop<?php endif;?>" style="top:-<?php echo trim($pins_more_option['custom_top']);?>px;left:-<?php echo trim($pins_more_option['custom_left']);?>px">
                                                <?php if($pins_image_hover):?><img alt="" src="<?php echo esc_url($pins_image_hover); ?>" class="pins_image_hover <?php if(!$noTooltip):?>ihotspot_hastooltop<?php endif;?>"  style="top:-<?php echo trim($pins_more_option['custom_hover_top']);?>px;left:-<?php echo trim($pins_more_option['custom_hover_left']);?>px"><?php endif;?>
                                            <?php if($linkpins):?></a><?php endif;?>
                                            <?php if(!empty($point['content']) && ( $layout_type == 'style1' )) { ?>
                                                <div class="box_view_html d-none d-md-block"><?php echo apply_filters('the_content', $point['content']);?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php $stt++;endforeach;?>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <?php
                endif;
                ?>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Nerf_Elementor_Image_Hotspot );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Nerf_Elementor_Image_Hotspot );
}