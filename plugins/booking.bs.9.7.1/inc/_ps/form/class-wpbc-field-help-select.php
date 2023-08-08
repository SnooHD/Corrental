<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WPBC_Field_Help_Select extends WPBC_Field_Help_Text {

    public    $title = '';
    protected $help =array();
    
    protected $type = '';
    protected $htmlid = '';
    
    protected $update_js_function_name = '';

    
    function __construct( $params = array() ) {
        
        $this->type     = $params['type'];
        $this->htmlid   = $params['id'];
        $this->title    = $params['title'];
        $this->update_js_function_name = 'wpbc_'. $this->htmlid .'_field_help_update';
        if (isset($params['help'])){
            if (is_array($params['help']))
                $this->help   = $params['help'];
            else 
                $this->help[] = $params['help'];
        }
        $this->js();
        $this->init();
        $this->setAdvancedParameters( $params );
    }
    
    
    public function init(){
        ?>        
        <div id="wpbc_field_help_section_<?php echo $this->htmlid; ?>" 
             class="wpbc_field_help_panel_background form-horizontal code_description wpbc_field_help_panel_field" 
             style="display:none;" >

            <div class="wpbc_field_help_panel_header"><?php echo $this->title ; ?></div><hr/>

            <?php 

            $this->setRequiredField(); $this->setMultipleField();
            
            $this->setNameField();      $this->setDefaultValueField();
            
            
            
            ?><div class="clear"></div><?php
            
            $this->setOptionsField();   $this->setTitlesField();
            
            $this->setIdField();        $this->setClassField();
            
            ?><div class="clear"></div><hr/><?php

            $this->setPutInFormField('one-row');

            $this->setPutInContentField('one-row');

            ?><div class="clear"></div><?php
            if (count($this->help)>0) echo '<hr/>';
            foreach ($this->help as $help_text_section) {
                if (! empty($help_text_section))
                    $this->setHelpInfo( $help_text_section, 'one-row');
            }
            ?><div class="clear"></div>
            <script type="text/javascript">  <?php echo $this->update_js_function_name; ?>(); </script>
        </div>            
        <?php
    }
 
    
    public function setDefaultValueField($group_css=''){
        ?>
        <div class="parameter-group <?php echo $group_css; ?>">    
            <label for="<?php echo $this->htmlid; ?>_default" class="control-label"><?php 
                _e('Default value' ,'booking'); ?> (<?php _e('optional' ,'booking'); ?>):</label>
            <input type="text" 
                   name="<?php echo $this->htmlid; ?>_default" id="<?php echo $this->htmlid; ?>_default"
                   onchange="javascript:<?php echo $this->update_js_function_name; ?>();" 
                   onkeypress="javascript:this.onchange();" 
                   onpaste="javascript:this.onchange();" 
                   oninput="javascript:this.onchange();"
                   />
            <p class="help-block" style="white-space: nowrap;"><?php printf(__('One Value from %sOptions%s list' ,'booking'),'<strong>','</strong>'); ?></p>
        </div>
        <?php
    }

    
    public function setMultipleField($group_css=''){
        ?>
        <div class="parameter-group <?php echo $group_css; ?>" style="white-space: nowrap;">
            <input type="checkbox"  style="width: auto; padding: 0px; margin: 0 5px 0 0;"
                   id="<?php echo $this->htmlid; ?>_multiple" name="<?php echo $this->htmlid; ?>_multiple"
                   onchange="javascript:<?php echo $this->update_js_function_name; ?>();" 
                   onclick="javascript:this.onchange();"
                   />
            <label class="" for="<?php echo $this->htmlid; ?>_multiple" style="width: auto; display: inline;"><?php 
            printf(__('Allow %smultiple%s selections' ,'booking'),'<strong>','</strong>'); ?>.</label>            
        </div>
        <?php
    }


    public function setOptionsField($group_css=''){
        ?>
        <div class="parameter-group <?php echo $group_css; ?>">    
            <label for="<?php echo $this->htmlid; ?>_options" class="control-label"><strong><?php 
                _e('Options' ,'booking'); ?></strong> (<?php _e('required' ,'booking'); ?>):</label>
            <textarea rows="5" style="width:100%"
                   name="<?php echo $this->htmlid; ?>_options" id="<?php echo $this->htmlid; ?>_options"
                   onchange="javascript:<?php echo $this->update_js_function_name; ?>();" 
                   onkeypress="javascript:this.onchange();" 
                   onpaste="javascript:this.onchange();" 
                   oninput="javascript:this.onchange();"
                   ></textarea>
            <p class="help-block"><?php _e('One option per line' ,'booking'); ?></p>
        </div>
        <?php        
    }

    
    public function setTitlesField($group_css=''){
        ?>
        <div class="parameter-group <?php echo $group_css; ?>">    
            <label for="<?php echo $this->htmlid; ?>_titles" class="control-label"><?php 
                _e('Titles of options' ,'booking'); ?> (<?php _e('optional' ,'booking'); ?>):</label>
            <textarea rows="5" style="width:100%"
                   name="<?php echo $this->htmlid; ?>_titles" id="<?php echo $this->htmlid; ?>_titles"
                   onchange="javascript:<?php echo $this->update_js_function_name; ?>();" 
                   onkeypress="javascript:this.onchange();" 
                   onpaste="javascript:this.onchange();" 
                   oninput="javascript:this.onchange();"
                   ></textarea>
            <p class="help-block"><?php _e('One title per line' ,'booking'); ?></p>
        </div>
        <?php            
    }


    public function js(){
        /* General Format: [text name 9/19 id:88id class:77class "Default_value"] */
        ?><script type="text/javascript">

            function <?php echo $this->update_js_function_name; ?>(){

                var p_name      = '';
                var p_required  = '';                
                var p_id        = '';
                var p_class     = '';
                
                var p_default   = '';
                var p_multiple  = '';
                var p_options   = '';
                var p_titles    = '';

                if ( jQuery('#<?php echo $this->htmlid; ?>_required').prop("checked") ) {
                    p_required = '*';
                }
                
                if ( jQuery('#<?php echo $this->htmlid; ?>_multiple').prop("checked") ) {
                    p_multiple = ' multiple';
                }
                
                if (jQuery('#<?php echo $this->htmlid; ?>_default').val() != '') {
                    p_default = jQuery('#<?php echo $this->htmlid; ?>_default').val().replace(/[\[\]\.,]/g,'').replace(/"/g,'&quot;').replace(/@@/g,'@');
                    jQuery('#<?php echo $this->htmlid; ?>_default').val(p_default);
                    if (p_default != '') {                        
                        p_default = ' default:' + p_default;
                    }                    
                }
                
                
                // Set Name only Letters
                if (jQuery('#<?php echo $this->htmlid; ?>_name').val() != '') {
                    p_name = jQuery('#<?php echo $this->htmlid; ?>_name').val();
                    p_name = p_name.replace(/[^A-Za-z0-9_-]*[0-9]*$/g,'').replace(/[^A-Za-z0-9_-]/g,'');
                    jQuery('#<?php echo $this->htmlid; ?>_name').val(p_name);
                }

                if (jQuery('#<?php echo $this->htmlid; ?>_id').val() != '') {
                    p_id = jQuery('#<?php echo $this->htmlid; ?>_id').val().replace(/[^A-Za-z-_0-9]/g, "");
                    jQuery('#<?php echo $this->htmlid; ?>_id').val(p_id);
                    if (p_id != '')
                        p_id = ' id:' + p_id;
                }

                if (jQuery('#<?php echo $this->htmlid; ?>_class').val() != '') {
                    p_class = jQuery('#<?php echo $this->htmlid; ?>_class').val().replace(/[^A-Za-z-_0-9]/g, "");
                    jQuery('#<?php echo $this->htmlid; ?>_class').val(p_class);
                    if (p_class != '')
                        p_class = ' class:' + p_class;
                }

                var p_titles_list = new Array();
                if (jQuery('#<?php echo $this->htmlid; ?>_titles').val() != '') {
                    p_titles = jQuery('#<?php echo $this->htmlid; ?>_titles').val().replace(/[\[\]]/g,'').replace(/"/g,'&quot;').replace(/@@/g,'@');
                    jQuery('#<?php echo $this->htmlid; ?>_titles').val(p_titles);
                    if (p_titles != '') {
                        p_titles_list = p_titles.split(/\n/);
                    }                    
                }


                if (jQuery('#<?php echo $this->htmlid; ?>_options').val() != '') {
                    p_options = jQuery('#<?php echo $this->htmlid; ?>_options').val().replace(/[\[\]\.,]/g,'').replace(/"/g,'&quot;').replace(/@@/g,'@');
                    jQuery('#<?php echo $this->htmlid; ?>_options').val(p_options);
                    if (p_options != '') {
                        var p_options_list = p_options.split(/\n/);
                        p_options = '';
                        jQuery.each(p_options_list, function(o_ind){
                            
                            if ( o_ind < p_titles_list.length ) 
                                p_options +=  ' "' + p_titles_list[o_ind] +'@@' + p_options_list[o_ind] + '"';
                            else
                                p_options +=  ' "' + p_options_list[o_ind] + '"';
                        });
                    }                    
                }


                if ( (p_name != '') && (p_options != '') ){
                    jQuery('#<?php echo $this->htmlid; ?>_put_in_form').val('[<?php echo $this->get_type(); ?>' 
                            + p_required + ' ' 
                            + p_name 
                            + p_id 
                            + p_class
                            + p_default
                            + p_multiple
                            + p_options
                            + ']');
                    jQuery('#<?php echo $this->htmlid; ?>_put_in_content').val('['+p_name+']');
                } else {
                    jQuery('#<?php echo $this->htmlid; ?>_put_in_form').val('');
                    jQuery('#<?php echo $this->htmlid; ?>_put_in_content').val('');
                }
            }

        </script>
        <?php
    } 
}
