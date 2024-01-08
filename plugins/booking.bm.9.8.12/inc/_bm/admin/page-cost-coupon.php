<?php /**
 * @version 1.0
 * @package Booking > Resources > Cost and rates page > "Coupon" section
 * @category Settings page 
 * @author snoo
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


class WPBC_Section_Coupon {    
    const HTML_PREFIX     = 'coupon_';
    const HTML_SECTION_ID = 'coupon';
    const ACTION_FORM = 'wpbc_rcosts_action'; 
    
    private $settings;
    private $loaded_meta_data = array();
    
    function __construct( $resource_id, $params ) {
        $defaults = array( 
                              'resource_id'     => 0
                            , 'resource_id_arr' => array()
                        );
        $params = wp_parse_args( $params, $defaults );
      
        if ( ! empty( $resource_id ) ) {
            
            $params[ 'resource_id_arr' ] = explode( ',', (string) $resource_id ); 
        
            $params[ 'resource_id' ]     = $params[ 'resource_id_arr' ][0];     // If we selected several booking resources, so by default we will show settings of first selected resource 
        }

        $this->settings = $params;
    }
    
    
    /** Show MetaBox */
    public function display() {
        
        ?><div class="clear" style="margin-top:20px;"></div><?php 
        ?><div id="wpbc_<?php echo self::HTML_PREFIX; ?>table_<?php echo self::HTML_SECTION_ID; ?>" class="wpbc_settings_row wpbc_settings_row_rightNO"><?php                   

            // Get data
            $resource_titles = array();                    
            $wpbc_br_cache = wpbc_br_cache();
            foreach ( $this->settings[ 'resource_id_arr' ] as $bk_res_id ) {
                
                $title_res = $wpbc_br_cache->get_resource_attr( $bk_res_id, 'title');
                if (! empty($title_res) ) {
                    $title_res =  apply_bk_filter('wpdev_check_for_active_language', $title_res );

                    ?> <h3> <?php _e('Change coupons for', 'booking'); echo ' ' . $title_res; ?> </h3> <?php

                    $resource_titles[]= $title_res;
                }
            }
       
            if (  ( ! empty( $this->settings[ 'resource_id_arr' ] ) ) && ( ! empty( $resource_titles ) )  ){
                    $this->costs_section( $resource_titles );
            }

            ?> 
            </div>
            </form>
            <form  name="<?php echo self::ACTION_FORM; ?>" id="<?php echo self::ACTION_FORM; ?>_2" action="<?php 

                    // Need to  exclude 'edit_resource_id' parameter from  $_GET,  if we was using direct link for editing,  in case for edit other season filters....
                    $exclude_params = array( 'edit_resource_id' );
                    $only_these_parameters = false;// array( 'tab', 'page_num', 'wh_search_id' );
                    $is_escape_url = false;
                    $only_get = true; 
                    echo wpbc_get_params_in_url( wpbc_get_resources_url( false, false ), $exclude_params, $only_these_parameters, $is_escape_url , $only_get );

                    ?>" method="post" autocomplete="off">
<div>
            <input type="hidden" name="is_form_sbmitted_<?php echo self::ACTION_FORM; ?>" id="is_form_sbmitted_<?php echo self::ACTION_FORM; ?>_2" value="1" />
            <input type="hidden" name="action_<?php echo self::ACTION_FORM; ?>"    id="action_<?php echo self::ACTION_FORM; ?>_2"    value="-1" />
            <input type="hidden" name="edit_resource_id_<?php echo self::ACTION_FORM; ?>" id="edit_resource_id_<?php echo self::ACTION_FORM; ?>_2" value="-1" />
        <?php                 

            
            wpbc_open_meta_box_section( self::HTML_SECTION_ID , __('Add', 'booking') );

                $this->add_coupon_section( $resource_titles );

            wpbc_close_meta_box_section();
        
            ?><div class="clear" style="margin-top:20px;"></div><?php 
        ?></div><?php                         
    }

    private function add_coupon_section( $resource_titles ){
        $meta_data = wpbc_get_resource_meta( $this->settings[ 'resource_id' ], 'coupons' );
        if ( count( $meta_data ) > 0 ) {
            $this->loaded_meta_data = maybe_unserialize( $meta_data[0]->value );
        
        }                                        
        $currency = wpbc_get_currency_symbol_for_user( $this->settings['resource_id'] ); 
        ?>
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row">
                    <?php 
                     _e('Code', 'booking')
                    ?>
                </th>
                <td class="description wpbc_edited_resource_label">
                   <?php

                     WPBC_Settings_API::field_text_row_static(                                              
                                                      self::HTML_PREFIX . 'code'
                                            , array(  
                                                      'type'              => 'text'
                                                    , 'title'             => __('Code', 'booking')
                                                    , 'description'       => ''
                                                    , 'placeholder'       => ''
                                                    , 'description_tag'   => 'span'
                                                    , 'tr_class'          => ''
                                                    , 'class'             => ''
                                                    , 'css'               => 'float:left;width:10em;'
                                                    , 'only_field'        => !false
                                                    , 'attr'              => array()                                                    
                                                    , 'validate_as'       => array( 'required' )
                                                    , 'value'             => ''
                                                )
                                    );                  
                    ?>
                    
                    <a href="javascript:void(0);" id="generate_code" class="button button-primary"><?php _e('Generate', 'booking') ?></a>
                    <div class="clear"></div>
                    <div style="display: none; color: red;" class="code_unique_error"><?php _e('Code should be unique', 'booking'); ?></div>
                    <script type="text/javascript">
                        document.addEventListener("DOMContentLoaded", () => {
                            const codes = <?php 
                                if(isset($this->loaded_meta_data)){
                                    echo json_encode($this->loaded_meta_data); 
                                }else{
                                    echo '[];';
                                }
                            ?>

                            const generateCode = (chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') => {
                                let text = "";

                                for ( var i=0; i < 10; i++ ) {
                                    text += chars.charAt(Math.floor(Math.random() * chars.length));
                                }
                                
                                return text;
                            }

                            document.querySelector('#generate_code').addEventListener('click', (event) => {
                                const code = generateCode();
                                document.querySelector('#coupon_code').value = code;

                                if(codes.some(({coupon_code}) => coupon_code === code)){
                                    errors.code = true;
                                }else{
                                    errors.code = false;
                                }
                                
                                checkErrors('code');
                            })

                            // Form submit
                            const errors = {
                                code: true,
                                amount: true
                            } 

                            const checkErrors = (check = false) => {
                                if(check === 'code'){
                                    if(errors.code){
                                        document.querySelector('.code_unique_error').style.display = 'block';
                                    }else{
                                        document.querySelector('.code_unique_error').style.display = 'none';
                                    }
                                }

                                if(check === 'amount'){
                                    if(errors.amount){
                                        document.querySelector('.amount_empty_error').style.display = 'block';
                                    }else{
                                        document.querySelector('.amount_empty_error').style.display = 'none';
                                    }
                                }

                                if(!errors.code && !errors.amount){
                                    // enable button
                                    document.querySelector('.add_coupon_button').style.opacity = '1';
                                    document.querySelector('.add_coupon_button').style.pointerEvents = 'auto'
                                    return;
                                }

                                // disable button
                                document.querySelector('.add_coupon_button').style.opacity = '0.6';
                                document.querySelector('.add_coupon_button').style.pointerEvents = 'none'
                            }

                            checkErrors();

                            // handle code check
                            const codeInputField = document.querySelector('#coupon_code');

                            const checkCodeInput = () => {
                                const val = document.querySelector('#coupon_code').value;
                                if(codes.some(({coupon_code}) => coupon_code === val)){
                                    errors.code = true;
                                }else{
                                    errors.code = false;
                                }
                                
                                checkErrors('code');
                            }

                            codeInputField.addEventListener('keyup', checkCodeInput);

                            // handle amount check
                            const amountInputField = document.querySelector('#coupon_amount');
                            amountInputField.addEventListener('keyup', (event) => {
                                const val = event.currentTarget.value;
                                if(!val || val === '0'){
                                    errors.amount = true;
                                }else{
                                    errors.amount = false;
                                }
                                
                                checkErrors('amount');
                            });
                        });
                    </script>
                </td>
            </tr>
            <tr valign="top" >
                <th scope="row" style="vertical-align: middle;">
                    <?php 
                     _e('Discount', 'booking')
                    ?>
                </th>
                <td class="description wpbc_edited_resource_label">
                <?php 
                    WPBC_Settings_API::field_text_row_static(                                              
                        self::HTML_PREFIX . 'amount'
                            , array(  
                                        'type'              => 'text'
                                    , 'title'             => __('Discount', 'booking')
                                    , 'description'       => ''
                                    , 'placeholder'       => ''
                                    , 'description_tag'   => 'span'
                                    , 'tr_class'          => ''
                                    , 'class'             => ''
                                    , 'css'               => 'float:left;width:6em;'
                                    , 'only_field'        => !false
                                    , 'attr'              => array()
                                    , 'validate_as'       => array( 'required' )
                                    , 'value'             => '0'
                                )
                    );                  
            
                    WPBC_Settings_API::field_select_row_static(                                              
                                                      self::HTML_PREFIX . 'type'
                                            , array(  
                                                      'type'              => 'select'
                                                
                                                    , 'title'             => __('Discount', 'booking')
                                                    , 'label'             => ''
                                                    , 'disabled'          => false
                                                    , 'disabled_options'  => array()
                                                    , 'multiple'          => false
                                                    
                                                    , 'description'       => ''
                                                    , 'description_tag'   => 'span'
                                                
                                                    , 'group'             => 'general'
                                                    , 'tr_class'          => ''
                                                    , 'class'             => ''
                                                    , 'css'               => 'float:left;width:10em;'
                                                    , 'only_field'        => ! false                                                
                                                    , 'attr'              => array()                                                    
                                                
                                                    , 'value'             => '%'
                                                    , 'options'           => array(
                                                                                    'fixed' => __('fixed total in' ,'booking') . ' ' . $currency
                                                                                  , '%'     => '% ' . __('of payment' ,'booking')
                                                                                )
                                                )
                                    );                  
            ?>
            <div class="clear"></div>
            <div style="display: none; color: red;" class="amount_empty_error"><?php _e('Amount should not be empty or zero', 'booking'); ?></div>
            </td>
            </tr>
        </tbody></table>
        <div class="clear" style="height: 10px;"></div>
        <a href="javascript:void(0);" 
            class="button button-primary add_coupon_button"
            onclick="javascript:
                                jQuery('#action_<?php echo $this->settings['action_form']; ?>_2').val('add_sql_coupon');
                                jQuery('#edit_resource_id_<?php echo $this->settings['action_form']; ?>_2').val('<?php echo implode( ',', $this->settings[ 'resource_id_arr' ] ); ?>');
                                jQuery('#<?php echo self::ACTION_FORM; ?>_2').trigger( 'submit' );"
            ><?php _e('Add coupon', 'booking') ?></a>
        <?php  
    }
    
    /**
	 * Section Content, Define Headers
     * 
     * @param string $resource_titles
     */
    private function costs_section( $resource_titles ){
        $meta_data = wpbc_get_resource_meta( $this->settings[ 'resource_id' ], 'coupons' );
        if ( count( $meta_data ) == 0 ) {
            return;
        
        }                                        
        $this->loaded_meta_data = maybe_unserialize( $meta_data[0]->value );
            
        if ( count( $this->loaded_meta_data ) > 0 ) {
            wpbc_open_meta_box_section( self::HTML_SECTION_ID , __('Coupons', 'booking') );                        
        ?>
        <table class="form-table"><tbody><?php   

$coupons_data = $this->loaded_meta_data;
foreach($coupons_data as &$coupon){
    ?>
            <tr valign="top" style="border-bottom: 1px solid #ccd0d4;">
                <td style="width: 25%;" class="description wpbc_edited_resource_label">
                    <strong><?php _e('Code:', 'booking'); ?></strong>&nbsp;
                    <?php echo $coupon['coupon_code']; ?>
                </td>
                <td style="width: 25%;" class="description wpbc_edited_resource_label">
                    <strong><?php _e('Discount:', 'booking'); ?></strong>&nbsp;
                    <?php 
                        if($coupon['coupon_type'] != '%'){
                            echo '€';
                        }
                        echo $coupon['coupon_amount']; 
                        if($coupon['coupon_type'] == '%'){
                            echo '%';
                        }
                        ?>
                </td>
                 <td style="width: 50%; text-align: right;">
                    <input hidden name="coupon_code" value="<?php echo $coupon['coupon_code']; ?>" />
                    <a
                        href="javascript:void(0);" 
                        class="button button-primary"
                        onclick="javascript:
                                jQuery('#<?php echo self::ACTION_FORM; ?>').find('[name=\'coupon_code\']').not(jQuery(this).parent().find('[name=\'coupon_code\']')).remove();
                                jQuery('#action_<?php echo $this->settings['action_form']; ?>').val('delete_sql_coupon');
                                jQuery('#edit_resource_id_<?php echo $this->settings['action_form']; ?>').val('<?php echo implode( ',', $this->settings[ 'resource_id_arr' ] ); ?>');
                                jQuery('#<?php echo self::ACTION_FORM; ?>').trigger( 'submit' );"
                    >
                        <i class="wpbc_icn_delete" style="color:white"></i>&nbsp;
                        <?php _e('delete', 'booking'); ?>
                    </a>
                </td>
            </tr>

            <?php } ?>


        </tbody></table>
        <?php     

            wpbc_close_meta_box_section();   
        }

    }
           
    /** Save changes */
    public function add_sql() {
                        
        $coupons = array();
        
        $meta_data = wpbc_get_resource_meta( $this->settings[ 'resource_id' ], 'coupons' );
        if ( count( $meta_data ) > 0 ) {  
            $coupons = maybe_unserialize( $meta_data[0]->value );  
        }

        $coupon_value = array();

        $coupon_value['coupon_amount'] = str_replace( ',', '.', $_POST['coupon_amount'] );                            // In case,  if someone was make mistake and use , instead of .
        $coupon_value['coupon_amount'] = floatval( $coupon_value['coupon_amount'] );
        
        if (   ( isset( $_POST['coupon_type' ] ) )
            && (        $_POST['coupon_type' ] == 'fixed'  )
           ) {            
            $coupon_value['coupon_type'] = 'fixed';                             // fixed
        } else {            
            $coupon_value['coupon_type'] = '%';                                 // Default %
        }

        $coupon_value['coupon_code'] = $_POST['coupon_code'];
        array_push($coupons, $coupon_value);

        // Loop all Resources
        foreach ( $this->settings[ 'resource_id_arr' ] as $resource_id ) {      

            // Save new meta rcosts data     
            wpbc_save_resource_meta( $resource_id, 'coupons', $coupons );
        }    
        
        wpbc_show_changes_saved_message();   

        make_bk_action( 'wpbc_reinit_seasonfilters_cache' );                                
    }

    public function delete_sql() {
                        
        
        $meta_data = wpbc_get_resource_meta( $this->settings[ 'resource_id' ], 'coupons' );
        if ( count( $meta_data ) == 0 ) {  
            return;
        }

        $coupons = maybe_unserialize( $meta_data[0]->value );  

        if ( count( $coupons ) == 0 ) {  
            return;
        }

        $new_coupons = array();
        foreach($coupons as &$coupon){
            if($coupon['coupon_code'] != $_POST['coupon_code']){
                array_push($new_coupons, $coupon);
            }
        }

        // Loop all Resources
        foreach ( $this->settings[ 'resource_id_arr' ] as $resource_id ) {      

            // Save new meta rcosts data     
            wpbc_save_resource_meta( $resource_id, 'coupons', $new_coupons );
        }    
        
        wpbc_show_changes_saved_message();   

        make_bk_action( 'wpbc_reinit_seasonfilters_cache' );                                
    }
}