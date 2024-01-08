<?php /**
 * @version 1.0
 * @package Booking Calendar 
 * @category UI elements for Toolbar Booking Listing / Calendar Overview pages
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2016-01-15
 * 
 * This is COMMERCIAL SCRIPT
 * We are not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly

	//FixIn: 9.6.3.5

    ////////////////////////////////////////////////////////////////////////////
    // B O O K I N G    R e s o u r c e s   S E L E C T O R    [C H O O S E N]
    ////////////////////////////////////////////////////////////////////////////

    /**
	 * Check   &   Modify:    $_REQUEST['wh_booking_type']
     * 
            Timeline - GET           
                         null  - Empty   - OLD ALL view 
                         56    - Single  - One Res View
                         56,5  - SEVERAL - Matrix View
                        56,....55 - All resources - Matrix      -- PROBLEM that in the field is listied all resources and "All resource" item - Next clik twice this view
            Listing - POST  
                         NOT SET - load default resource.
                         Array(56 )   - SINGLE - 
                         Array(56, 5) - SEVERAL -
                         Array([0] => 56,1,5,6,7,8,9,13,24,25,26,4,3,2,10,11,12,55
                               [1] => 56
                               [2] => 1
                                  All resources - Matrix      -- PROBLEM that in the field is listied all resources and "All resource" item - Next clik twice this view       
                         Array([0] => 56
                                ...
                               [17] => 55 )
                                  All resources 
     */
    function wpbc_check_request_param__wh_booking_type() {
        		
		if ( ! empty($_REQUEST['wh_booking_type'])) {			//FixIn: 7.2.1.18	

			//FixIn: 7.2.1.18	// Trick, if -999, then we are set  the value to "" and we are show all bookings in old view mode for "Resource not exist"
			if (		(   -999 == $_REQUEST['wh_booking_type'] )
					||  ( 'lost' == $_REQUEST['wh_booking_type'] )        //FixIn: 8.5.2.19
			){
				$_REQUEST['wh_booking_type'] = 'lost';
				return ;
			}
			
            // Firstly we get ARRAY if we had the String            
            if (is_string($_REQUEST['wh_booking_type'])) {
                if  ( strpos($_REQUEST['wh_booking_type'], ',') !== false ) {
                    $_REQUEST['wh_booking_type'] = explode(',', $_REQUEST['wh_booking_type']);
                }
            }    

            // Now transform array  to  the String
            if ( is_array($_REQUEST['wh_booking_type'])) {
                $wh_booking_type_array = $_REQUEST['wh_booking_type'] ;                    
                foreach ($wh_booking_type_array as $key=>$value) {
                    if (empty($value)) 
                        unset($wh_booking_type_array[$key]);
                }
                // If we are had some array and in array element was like this [0] => 56,1,5,55 ; [1] => 56, so now we are have this: => 56,1,5,55,56
                $_REQUEST['wh_booking_type'] = implode(',', $wh_booking_type_array);
            }

            // Remove dupplicates -  Now trasform to Array again; Remove dubplicates and Get Array again, its because issue if we are have ALL Resources option.
            $_REQUEST['wh_booking_type'] = explode(',', $_REQUEST['wh_booking_type']);
            $_REQUEST['wh_booking_type'] = array_unique($_REQUEST['wh_booking_type']);
            $_REQUEST['wh_booking_type'] = implode(',', $_REQUEST['wh_booking_type']);

            // If No any selections, its mean that  we are received NULL, so  then we are set  the value to "" and we are show all bookings in old view mode.
            if  ($_REQUEST['wh_booking_type']=='null') {
                $_REQUEST['wh_booking_type'] = '';
            }

        } else {  // E M P T Y     -  Load default parameter

			if ( ! wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) {                        //FixIn: 8.5.2.8
				$user       = get_bk_option( 'booking_default_booking_resource' );
				$super_user = get_option( 'booking_default_booking_resource' );
				if ( ( empty( $super_user ) ) && ( $super_user != $user ) ) {
					$_REQUEST['wh_booking_type'] = $super_user;
				}
			} else {
				$_REQUEST['wh_booking_type'] = get_bk_option( 'booking_default_booking_resource');

				// If default selection  is Empty so its mean load All resources.
				if (empty($_REQUEST['wh_booking_type'])) {
					$types_list = wpbc_get_br_as_objects();
					$types_list_id = array();
					foreach ($types_list as $tl) {
						$types_list_id[] = $tl->id;
					}
					$_REQUEST['wh_booking_type'] = implode(',',$types_list_id);
				}
            }
        }
//debuge($_REQUEST);        		
    }
    add_bk_action( 'wpbc_check_request_param__wh_booking_type', 'wpbc_check_request_param__wh_booking_type');


//FixIn: 9.6.3.5
    
        
    /** Resources Selection for Timeline */
    function wpbc_br_selection_for_timeline(){    
        
        ?><div class="clear"></div><?php

        wpbc_get_data_for_resource_selection();

        ?><script type="text/javascript">
            function reload_booking_calendar_oveview_page(){
                var resource_value = jQuery( '#wh_booking_type' ).val();				
                if ( resource_value == null ) resource_value = jQuery("#wh_booking_type option:first").val();       // Assign  first value - all booking resources 
                if ( resource_value == null ) resource_value = '';      
				//FixIn: 7.2.1.18
				if ( resource_value instanceof Array ) {
					jQuery.each( resource_value, function( r_index, r_value ) {
						r_value = String( r_value );			// converto to string
						if ( r_value.length > 200 ) {
							resource_value[ r_index ] = '';		// all resources - empty  string
						}							
					});
				}
				//FixIn: 7.2.1.18   end
					
                window.location.assign("<?php 
                                                 $bk_admin_url = wpbc_get_params_in_url( wpbc_get_bookings_url( false ), array('wh_booking_type') );
                                            echo $bk_admin_url . '&wh_booking_type='; ?>" +  resource_value );
            }        
        </script><?php
    }
    add_bk_action( 'wpbc_br_selection_for_timeline', 'wpbc_br_selection_for_timeline');

                
    /** Get data for Resource Selection elemnt - [C H O O S E N] */
    function wpbc_get_data_for_resource_selection(){

        $types_list = wpbc_get_br_as_objects();
        $wpdevbk_id = 'wh_booking_type';                                        //  {'', '1', '4,7,5', .... }
        $wpdevbk_selectors = array();
        $all_ids = array();
        foreach ($types_list as $bkr) {
            $all_ids[] = $bkr->id;
        }
        if (count($all_ids)>1)
        $wpdevbk_selectors['<strong>'.__('All resources' ,'booking').'</strong>']=implode(',',$all_ids);

        foreach ($types_list as $bkr) {
            $bkr_title = $bkr->title;
            if (isset($bkr->parent)) {
                if ($bkr->parent == 0)
                    $bkr_title = $bkr_title;
                else
                    $bkr_title = '&nbsp;&nbsp;&nbsp;' . $bkr_title ;
            }
            $wpdevbk_selectors[ $bkr_title  ] = $bkr->id;
        }

        $wpdevbk_control_label =   '';
        $wpdevbk_help_block =      __('Booking resources' ,'booking');

        wpbc_toolbar_filter_choosen( $wpdevbk_id, $wpdevbk_selectors, $wpdevbk_control_label, $wpdevbk_help_block );
    }
    

    /**
	 * B o o k i n g    R e s o u r c e s    S e l e c t o r    [C H O O S E N]
     * 
     * @param string $wpdevbk_id                - HTML ID of element
     * @param array $wpdevbk_selectors          - array( 'Title' => Value )
     * @param string $wpdevbk_control_label
     * @param string $wpdevbk_help_block
     */
    function wpbc_toolbar_filter_choosen( $wpdevbk_id, $wpdevbk_selectors, $wpdevbk_control_label, $wpdevbk_help_block){
        
        if ( isset( $_REQUEST[$wpdevbk_id] ) )      $wpdevbk_value = $_REQUEST[$wpdevbk_id];
        else                                        $wpdevbk_value = '';
        
        if ( strpos($wpdevbk_value,',') !== false ) $wpdevbk_value_array = explode (',', $wpdevbk_value);
        else                                        $wpdevbk_value_array = array();
        
        $wpdevbk_selector_default = array_search( $wpdevbk_value, $wpdevbk_selectors );
//debuge($_REQUEST,$wpdevbk_selector_default, $wpdevbk_value, $wpdevbk_selectors);
        if ( $wpdevbk_selector_default === false ) 
            $wpdevbk_selector_default = current($wpdevbk_selectors);
        
        ?>
        <div class="control-group">
            <div class="btn-toolbar">
                <select multiple="multiple" class="chzn-select" style="float:left;"
                        id="<?php echo $wpdevbk_id; ?>" name="<?php echo $wpdevbk_id; ?>[]" data-placeholder="<?php echo $wpdevbk_help_block; ?>"                       
                         >
                  <?php
                  $is_all_resources_selected = false;
                  foreach ($wpdevbk_selectors as $key=>$value) {
                    if ($value != 'divider') {
                        $is_in_array = in_array($value, $wpdevbk_value_array);
                        ?><option <?php if ( ( ($wpdevbk_value == $value ) || ($is_in_array)  ) && (! $is_all_resources_selected) ) { echo ' selected="SELECTED" ';
                                        if ( strpos($value,',') !== false ) {
                                            $is_all_resources_selected = true;
                                        }
                                   } ?> 
                            <?php if (strpos($key , '&nbsp;') === false) echo ' style="font-weight:600;" '; ?>
                            value="<?php echo $value; ?>" title="<?php echo $key; ?>"><?php
								//echo $key;
								echo substr( $key, 0, 80 ) . ( ( strlen( $key ) > 80 ) ? '...' : '' );    				//FixIn: 9.1.2.3
						?></option><?php
                    } else {
                        ?><?php
                    }
                  } ?>
              </select>
              <div class="chzn-right-buttons btn-group">  
                    <input type="hidden" name="blank_field__this_field_only_for_formatting_buttons" value=""> 
                    <a  data-original-title="<?php _e('Clear booking resources selection' ,'booking'); ?>"  rel="tooltip" 
                        class="tooltip_top button button-secondary wpbc_stick_left wpbc_stick_right"
                        onclick="javascript:remove_all_options_from_choozen('#<?php echo $wpdevbk_id; ?>');"
                        ><i class="wpbc_icn_close"></i></a>
                    <a data-original-title="<?php _e('Apply booking resources selection' ,'booking'); ?>"  rel="tooltip" 
                       class="tooltip_top button button-primary wpbc_stick_left"
                       onclick="javascript:reload_booking_calendar_oveview_page();"
                       ><i class="wpbc_icn_refresh"></i></a>
              </div>
            </div>                                
        </div>

        <script type="text/javascript">

            function remove_all_options_from_choozen( selectbox_id ){
				jQuery( selectbox_id + ' option' ).removeAttr( 'selected' );    	// Disable selection in the real selectbox
				jQuery( selectbox_id ).trigger( 'chosen:updated' );            		// Remove all fields from the Choozen field	//FixIn: 8.7.9.9
            } 

            //jQuery(document).ready( function(){	//FixIn: 8.5.2.23

			if ( 'function' === typeof( jQuery("#<?php echo $wpdevbk_id; ?>").chosen ) ) {


				  jQuery("#<?php echo $wpdevbk_id; ?>").chosen({no_results_text: "No results matched"});

				  // Catch any selections in the Choozen
				  jQuery("#<?php echo $wpdevbk_id; ?>").chosen().on('change', function(va){

					  if( jQuery("#<?php echo $wpdevbk_id; ?>").val() != null ) {
						  //So we are having aready values
						  jQuery.each( jQuery("#<?php echo $wpdevbk_id; ?>").val() , function(index, value) {

							  if (value.indexOf(',')>0) { // Ok we are have array with  all booking resources ID
								  jQuery( '#<?php echo $wpdevbk_id; ?>' + ' option').removeAttr('selected');    // Disable selection in the real selectbox
								  jQuery( '#<?php echo $wpdevbk_id; ?>' + ' option:first-child').prop("selected", true);    // Disable selection in the real selectbox
								  jQuery( '#<?php echo $wpdevbk_id; ?>' ).trigger('liszt:updated');            // Update all fields from the Choozen field
								  var my_message = '<?php echo html_entity_decode( esc_js( __('Please note, its not possible to add new resources, if "All resources" option is selected. Please clear the selection, then add new resources.' ,'booking') ),ENT_QUOTES) ; ?>';
								  wpbc_admin_show_message( my_message, 'warning', 10000 );
							  }
						  });
					  }
				  });

			} else {
				alert( 'WPBC Error. JavaScript library "chosen" was not defined.' );
			}
            //});									//FixIn: 8.5.2.23
        </script>
        <style type="text/css">   
              .chzn-right-buttons {
                  float:left;
                  margin:0 0 0 -100px;
              }
              .bookingpage .wpdevelop a.chzn-single {
                  height: 23px;
                  margin-top: 2px;
              }
              #<?php echo $wpdevbk_id; ?>, 
              .chzn-container-multi  {
                   float: left;
                  margin: 0 -5px 0 5px;
                  width: auto !important;
                  box-shadow:0 1px 0 #fff inset, 0 1px 0 rgba(0, 0, 0, 0.07);
                  border-color:#999;
              }
              .chzn-container .chzn-drop,
              #<?php echo $wpdevbk_id; ?>, 
              .chzn-container-multi {
                  min-width:150px;
              }
              /* LI options */
              .chzn-container-multi .chzn-choices {
                  height:auto !important;
                  -webkit-border-radius: 2px 0 0 2px;
                  -moz-border-radius: 2px 0 0 2px;
                  border-radius: 2px 0 0 2px;
              }
              /* Search  hidden button */
              .chzn-container-multi .chzn-choices  {
                  min-height: 28px;
              }
              .chzn-container-multi .chzn-choices .search-field input{
                  height: 26px;
                  line-height:14px;
                  font-size:12px;
                  margin:0;
                  padding: 0 0 0 10px;                        
              }
              .chzn-container-multi .chzn-choices .search-choice {   
                  white-space: nowrap;
                  background: #eee;    
                  margin: 4px 0 0px 5px;
                  padding: 2px 20px 0px 5px;                        
              }
              .chzn-container-multi .chzn-choices .search-choice a.search-choice-close{
                  background:none;
                  display: inline-block;
                  font-family: "Glyphicons Halflings";
                  font-style: normal;
                  font-weight: 400;
                  font-size:9px;
                  line-height: 1;
                  position: relative;
                  top: 1px;  
                  left:12px;
                  color:#555;
                  text-decoration: none;
              }
              .chzn-container-multi .chzn-choices .search-choice a.search-choice-close:hover{
                  text-decoration: none;
              }
              .chzn-container-multi .chzn-choices .search-choice a.search-choice-close:before {
                  content: "\e014";
              }

              .chzn-container {
                  font-size: 12px;
                  font-weight: 400;
              }
              .chzn-container-multi .chzn-choices {
                  border: 1px solid #bbb;
              }    
              .chzn-container .chzn-results .highlighted {
                  background:#08C;
              }
              @media (max-width: 782px) {
                  .chzn-container-multi .chzn-choices {
                      min-height: 34px;
                  }
                  .chzn-container-multi .chzn-choices .search-field input {
                      height: 30px !important;
                      margin:1px 0 !important;
                      line-height:30px !important;
                      font-size:13px;
                  }
                  .chzn-container-multi .chzn-choices .search-choice {
                      margin: 5px 0 1px 5px;
                      padding: 4px 20px 3px 5px;
                  }                        
              }
        </style>
        <?php
    }

    
    ////////////////////////////////////////////////////////////////////////////    
    //  B u t t o n s   -  ADD NEW Booking page
    ////////////////////////////////////////////////////////////////////////////  
    
    /** Selection of booking resources */
    function wpbc_toolbar_btn__resource_selection( $params = array() ) {

        
        $defaults = array( 
                              'on_change'   => false
                            , 'title'       => __('Booking resource', 'booking') . ':'
                            , 'resource_type'   => 'all'                            // single_parent
                            , 'resources' => array()
                        );
        $params = wp_parse_args( $params, $defaults );
        
        
        
        //$resource_objects = wpbc_get_br_as_objects();
    
        $resources_cache = wpbc_br_cache();                                     // Get booking resources from  cache        
        
        if ( $params['resource_type'] == 'single_parent' )
            $resource_objects = $resources_cache->get_single_parent_resources();
        else 
            $resource_objects = $resources_cache->get_resources();
            

       
        
        $resource_options = $params['resources'];

	    foreach ( $resource_objects as $br ) {

		    $br_option          = array();
		    $br_option['title'] = apply_bk_filter( 'wpdev_check_for_active_language', $br['title'] );

		    if ( ( isset( $br['parent'] ) ) && ( $br['parent'] == 0 ) && ( isset( $br['count'] ) ) && ( $br['count'] > 1 ) ) {
			    $br_option['title'] .= ' [' . __( 'parent resource', 'booking' ) . ']';
		    }

		    $br_option['class'] = 'wpbc_single_resource';
		    if ( isset( $br['parent'] ) ) {
			    if ( $br['parent'] == 0 ) {
				    if ( ( isset( $br['count'] ) ) && ( $br['count'] > 1 ) ) {
					    $br_option['class'] = 'wpbc_parent_resource';
				    }
			    } else {
				    $br_option['class'] = 'wpbc_child_resource';
			    }
		    }

		    if ( ( isset( $_GET['booking_type'] ) ) && ( $_GET['booking_type'] == $br['id'] ) ) {
			    $br_option['selected'] = true;
		    }

		    if ( ( isset( $br['parent'] ) ) && ( $br['parent'] == 0 ) && ( isset( $br['count'] ) ) && ( $br['count'] > 1 ) ) {
			    $sufix = '&parent_res=1';
			    if ( ! empty( $_GET['as_single_resource'] ) ) {
				    $br_option['selected'] = false;
			    }
		    } else {
			    $sufix = '';
		    }

		    $resource_options[ $br['id'] . $sufix ] = $br_option;

			$sufix = '';
		    //Parent booking resource show as "child booking resource"
		    if ( ( isset( $br['parent'] ) ) && ( $br['parent'] == 0 ) && ( isset( $br['count'] ) ) && ( $br['count'] > 1 ) ) {

			    $br_option['title'] = substr( $br_option['title'], 0, - 1 * strlen( ' (' . __( 'parent resource', 'booking' ) . ')' ) );

			    $br_option['class'] = 'wpbc_child_resource';
				$sufix = '&as_single_resource=1';

			    if ( isset( $_GET['booking_type'] ) && ( ! empty( $_GET['as_single_resource'] ) && ( $_GET['booking_type'] == $br['id'] ) ) ) {
				    $br_option['selected'] = true;
			    } else {
				    $br_option['selected'] = false;
			    }

			    $resource_options[ $br['id'] . $sufix ] = $br_option;
		    }

		    if ( $resource_options[ $br['id'] . $sufix ]['class'] === 'wpbc_child_resource' ) {
			    $resource_options[ $br['id'] . $sufix ]['title'] = ' &nbsp;&nbsp;&nbsp; ' . $resource_options[ $br['id'] . $sufix ]['title'];
		    }

	    }

        ////////////////////////////////////////////////////////////////////////////

        $parameter_name = 'booking_type';
        
//        if ( isset( $_GET[ $parameter_name ] ) )    $selected_value = intval ( $_GET[ $parameter_name ]  );
//        else                                        $selected_value = 0;
             
        if ( $params['on_change'] === false ) {
            
            $link_base = wpbc_get_new_booking_url__base( array( $parameter_name, 'booking_form', 'parent_res' ) ) . '&' . $parameter_name . '=' ;        
            
            $on_change = 'location.href=\'' . $link_base . '\' + this.value;';
            
        } else {
            $on_change = $params['on_change'];
        }
        
//        $on_change = 'location.href=\'' . wpbc_get_new_booking_url(true, false ) . '&booking_type=' . '\' + this.value;';

        $params = array(  
                          'label_for' => 'calendar_type'                        // "For" parameter  of label element
                        , 'label' => ''                                         // Label above the input group
                        , 'style' => ''                                         // CSS Style of entire div element
                        , 'items' => array(
                                        array(      
                                            'type' => 'addon' 
                                            , 'element' => 'text'               // text | radio | checkbox
                                            , 'text' => $params['title']
                                            , 'class' => ''                     // Any CSS class here
                                            , 'style' => 'font-weight:600;'     // CSS Style of entire div element
                                        )  
                                        , array(    
                                              'type' => 'select'  
                                            , 'id' => 'select_booking_resource'             // HTML ID  of element  - previously - calendar_type
                                            , 'name' => 'select_booking_resource'           // HTML ID  of element
                                            , 'options' => $resource_options                // Associated array  of titles and values 
                                            //, 'disabled_options' => array( 'any' )        // If some options disbaled,  then its must list  here
                                            //, 'default' => 'specific'         // Some Value from optins array that selected by default                                      
                                            , 'style' => ''                     // CSS of select element
                                            , 'class' => ''                     // CSS Class of select element
                                            , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element 
                                            , 'onchange' => $on_change                  /* if (this.value == '+') location.href='<?php echo $link_base_plus; ?>'; else location.href='<?php echo $link_base; ?>' + this.value; */
                                        )
                        )
                  );     
        ?><div class="control-group wpbc-no-padding"><?php 
                wpbc_bs_input_group( $params );                   
        ?></div><?php

    }

    
    ////////////////////////////////////////////////////////////////////////////
    //  Toolbar for Booking > Resources page -          Add New Resource
    ////////////////////////////////////////////////////////////////////////////

    /**
	 * Show Help Dropdown menu at Booking > Resources page at Top Right side of Toolbar
     * 
     * @param string $menu_in_page_tag - active page
     */    
    function wpbc_toolbar_add_new_booking_resource__help( $menu_in_page_tag ) {

        if ( $menu_in_page_tag == 'wpbc-resources' ) {

           wpbc_bs_dropdown_menu_help();
        }
    }
    add_action( 'wpbc_toolbar_top_tabs_after', 'wpbc_toolbar_add_new_booking_resource__help' );


    /** Show Toolbar at  Booking > Resources page - Add New Resource */
    function wpbc_add_new_booking_resource_toolbar() {

        wpbc_clear_div();

        $max_resources = 200; 
                
        $max_resources = apply_filters( 'wpbc_check_max_allowed_booking_resources', $max_resources );       // Here we need to check about number of exist booking resources and maximum allowed booking resources in  Booking Calendar MultiUser version and update this value
     
        if ( $max_resources <= 0 ) return;

        
        //  Toolbar ////////////////////////////////////////////////////////////////

        ?><div id="toolbar_booking_resources" style="position:relative;"><?php


            // <editor-fold     defaultstate="collapsed"                        desc=" T O P    T A B s "  >

            // Show Tabs only in for PS and BS versions other versions already  have it.
            if ( ! class_exists( 'wpdev_bk_biz_m' ) ) {                             

                wpbc_bs_toolbar_tabs_html_container_start();

                    wpbc_bs_display_tab(   array(
                                                        'title'         => __('Add booking resource - New Calendar', 'booking')
                                                        // , 'hint' => array( 'title' => __('Manage bookings' ,'booking') , 'position' => 'top' )
                                                        , 'onclick'     =>  ""
                                                        , 'font_icon'   => 'wpbc_icn_add_circle_outline'
                                                        , 'default'     => true
                                        ) ); 

                    wpbc_bs_dropdown_menu_help();

                wpbc_bs_toolbar_tabs_html_container_end();
            } 
            // </editor-fold>

            wpbc_bs_toolbar_sub_html_container_start();

            //  T o o l b a r
            ?><div id="booking_resources_toolbar_container" class="visibility_container clearfix-height" style="display:block;margin-top:-5px;"><?php 

            $submit_form_name = 'wpbc_form_add_new_booking_resources';
            ?><form  name="<?php echo $submit_form_name; ?>" id="<?php echo $submit_form_name; ?>" action="" method="post" autocomplete="off"><?php         

                // N o n c e   field, and key for checking   S u b m i t 
                wp_nonce_field( 'wpbc_settings_page_' . $submit_form_name );
                ?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name; ?>" id="is_form_sbmitted_<?php echo $submit_form_name; ?>" value="1" /><?php                 

                //                                                                              <editor-fold   defaultstate="collapsed"   desc=" Resource Title " >    
                $params = array(  
                              'label_for' => 'booking_resource_name'                // "For" parameter  of label element
                            , 'label' => '' //__('Add New Field', 'booking')        // Label above the input group
                            , 'style' => ''                                         // CSS Style of entire div element
                            , 'items' => array(     
                                                array(
                                                    'type'          => 'text' 
                                                    , 'id'          => 'booking_resource_name'  
                                                    , 'name'        => 'booking_resource_name'  
                                                    , 'attr' => array( 'maxlength' => '200' )
                                                    , 'label'       => ''  
                                                    , 'disabled'    => false
                                                    , 'class'       => ''
                                                    , 'style'       => 'min-width:220px;'
                                                    , 'placeholder' => __('Enter name of booking resource' ,'booking')                                                                                                                                    
                                                    , 'attr'        => array()
                                                    , 'value' => ''
                                                    , 'onfocus' => ''                                            
                                                )
                                                , array( 
                                                    'type' => 'button'
                                                    , 'title' => __( 'Add New', 'booking' )  // __('Reset', 'booking')
                                                    , 'hint' => array( 'title' => __('Add New Booking Resource(s)' ,'booking') , 'position' => 'top' )
                                                    , 'class' => 'button tooltip_top' 
                                                    , 'font_icon' => 'wpbc_icn_add_circle_outline'
                                                    , 'icon_position' => 'left'                                                                            
                                                    , 'action' => " if (jQuery('#booking_resource_name' ).val() == '' ) { wpbc_field_highlight( '#booking_resource_name' ); } else { jQuery('#wpbc_form_add_new_booking_resources').trigger( 'submit' ); }"
                                                )                            
                                    )
                            );

                ?><div class="new_booking_resource_control_group control-group wpbc-no-padding"><?php 
                        wpbc_bs_input_group( $params );                   
                ?></div><?php     
                //                                                                              </editor-fold>
                
                wpbc_toolbar_btn__selection_element( array(
                                                                'name' => 'resources_count'
                                                              , 'title' => __('Resources count' ,'booking') . ':'
                                                              , 'selected' => 1  
                                                              , 'options' => array_combine( range(1, $max_resources) ,range(1, $max_resources) ) 
                                                ) ) ;                        
                ////////////////////////////////////////////////////////////////////

                ?><div class="control-group wpbc-no-padding" style="float:right;margin-right: 0;margin-left: 15px;"><?php 

                    //                                                                              <editor-fold   defaultstate="collapsed"   desc=" Show | Hide Children " >    
                    if ( class_exists( 'wpdev_bk_biz_l' ) ) {
                        
                        ?><a href="javascript:void(0);" onclick="javascript:jQuery('.wpbc_resource_child').toggle(500);jQuery('.wpbc_show_hide_children').toggle();" 
                             class="button wpbc_show_hide_children tooltip_left" data-original-title="<?php _e('Show Children Resources' , 'booking') ?>" style="display:none;"><span class="wpbc_icn_visibility" aria-hidden="true"></span></a><?php    
                        ?><a href="javascript:void(0);" onclick="javascript:jQuery('.wpbc_resource_child').toggle(500);jQuery('.wpbc_show_hide_children').toggle();" 
                             class="button wpbc_show_hide_children tooltip_left" data-original-title="<?php _e('Hide Children Resources' , 'booking') ?>"><span class="wpbc_icn_visibility_off" aria-hidden="true"></span></a><?php    
                    }
                    //                                                                              </editor-fold>
                    
                    /**
	 				 * Save Button
                     * Note! This button submit saving of chnages to Booking Resources Table
                        
                        ?><a                 
                             class="button button-primary " 
                             href="javascript:void(0)"
                             onclick="javascript:jQuery('#wpbc_bresources').trigger( 'submit' );"
                             ><?php _e('Save Changes' , 'booking') ?></a><?php    
                    */
                    
                ?></div><?php
                ////////////////////////////////////////////////////////////////////


                ?><span class="advanced_booking_filter" style="display:none;"><div class="clear" style="width:100%;border-bottom:1px solid #ccc;height:10px;"></div><?php 

                //                                                                              <editor-fold   defaultstate="collapsed"   desc=" Parent | Custom Form | Priority " >    
                if ( class_exists( 'wpdev_bk_biz_l' ) ) 
                    wpbc_toolbar_btn__resource_selection(   array( 
                                                                  'on_change'   => ''
                                                                , 'title'       => __('Parent', 'booking') . ':'
                                                                , 'resource_type'   => 'single_parent'                            
                                                                , 'resources'   => array( 0 => array( 'id' => 0, 'title' => ' - ' ) )
                                                        )
                                            );

                if ( class_exists( 'wpdev_bk_biz_m' ) ) 
                    wpbc_toolbar_btn__form_selection(   array( 
                                                                  'on_change'   => ''
                                                                , 'title'       => __('Default Form', 'booking') . ':'
                                                            )            
                                                    );


                if ( class_exists( 'wpdev_bk_biz_l' ) ) {
                    
                    wpbc_toolbar_btn__selection_element( array(
                                                                'name' => 'resources_priority'
                                                              , 'title' => __('Priority' ,'booking') . ':'
                                                              , 'selected' => 0
                                                              , 'options' => array_combine( range(0, 500) ,range(0, 500) ) 
                                                ) ) ;                      
                }

	    		//FixIn: 9.6.3.4
				$params = array(
								  'label_for' => 'actions_find_resource'                              // "For" parameter  of button group element
								, 'label' => '' //__('Actions:', 'booking')                  // Label above the button group
								, 'style' => 'float:right;margin-right: 0;'                                         // CSS Style of entire div element
								, 'items' => array(
													array(
														  'type' => 'button'
														, 'title' => __('Find lost booking resources', 'booking') . '&nbsp;&nbsp;'    // Title of the button
														, 'hint' => array( 'title' => __('Find lost booking resources' ,'booking') , 'position' => 'top' ) // Hint
														, 'link' => wpbc_get_resources_url() . '&show_all_resources=1'		//'javascript:void(0)'        // Direct link or skip  it
														//, 'action' => "js_func();"                // Some JavaScript to execure, for example run  the function
														, 'class' => ''                        // button-secondary  | button-primary
														, 'icon' => ''
														, 'font_icon' => 'wpbc_icn_search'
														, 'icon_position' => 'right'     	// Position  of icon relative to Text: left | right
														, 'style' => ''                 	// Any CSS class here
														, 'mobile_show_text' => true       	// Show  or hide text,  when viewing on Mobile devices (small window size).
														, 'attr' => array()
													)
												)
				);
				wpbc_bs_button_group( $params );

                //                                                                              </editor-fold>

                /**
	 				* Add New Button
                    * Note! This button submit Add New Resource

                    ?><div class="control-group wpbc-no-padding"><?php 
                        ?><a                 
                             class="button button-primary " 
                             href="javascript:void(0)"
                             onclick="javascript:jQuery('#wpbc_form_add_new_booking_resources').trigger( 'submit' );"
                             ><?php _e('Add New Booking Resource(s)' , 'booking') ?></a><?php    
                    ?></div><?php
                */
                
                ?><div class="clear"></div></span><?php

                wpbc_clear_div();
                
                //if ( class_exists( 'wpdev_bk_biz_m' ) )
				wpbc_toolbar_expand_collapse_btn( 'advanced_booking_filter' );

            ?></div><?php

            wpbc_bs_toolbar_sub_html_container_end();       

        ?></form><?php
        ?></div><?php

        wpbc_clear_div();

    }

    
    /**
	 * Submit of New booking resource(s)
     * 
     * @global type $wpdb
     */    
    function wpbc_bresources_check_submit_actions() {

        global $wpdb;
                             
        $submit_form_name = 'wpbc_form_add_new_booking_resources';              // Define form name

        // $this->get_api()->validated_form_id = $submit_form_name;             // Define ID of Form for ability to  validate fields (like required field) before submit.

        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name ] ) ) {

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'wpbc_settings_page_' . $submit_form_name );  // Its stop show anything on submiting, if its not refear to the original page
//debuge($_POST);

            // Save Changes 

            // Validate Number of booking resources to  create
            $validated_resources_count = WPBC_Settings_API::validate_text_post_static( 'resources_count' );
            $validated_resources_count = intval( $validated_resources_count );
            if ( $validated_resources_count < 1 ) $validated_resources_count = 1;
            if ( $validated_resources_count > 200 ) $validated_resources_count = 200;

            // Here we need to check about number of exist booking resources and maximum allowed booking resources in  Booking Calendar MultiUser version and update this value
            $validated_resources_count = apply_filters( 'wpbc_check_max_allowed_booking_resources', $validated_resources_count );
       
            // Validate Title
            $validated_title = WPBC_Settings_API::validate_text_post_static( 'booking_resource_name' );


            for ( $i = 0; $i < $validated_resources_count; $i++ ) {

                if ( $validated_resources_count > 1 ) $sufix = '-' . ( $i + 1 );
                else                                  $sufix = '';


                // Need this complex query  for ability to  define different paramaters in differnt versions.
                $sql_arr = apply_filters(   'wpbc_resources_table__add_new_sql_array'
                                                    , array(
                                                            'sql'       => array(
                                                                                  'start'      => "INSERT INTO {$wpdb->prefix}bookingtypes "
                                                                                , 'params'     => array( 'title' )    
                                                                                , 'param_types' => array( '%s' )    
                                                                        )
                                                            , 'values'  => array( $validated_title . $sufix )
                                                        )
                                                    , array( 'sufix' => $sufix , 'index' => $i )
                                    );                                                                                                                                                                                    
                $sql = $wpdb->prepare(    $sql_arr['sql']['start']                                                              // SQL
                                            .         '( ' . implode( ', ' , $sql_arr['sql']['params'] ) . ') '
                                            . ' VALUES ( ' . implode( ', ' , $sql_arr['sql']['param_types'] ) . ') '
                                        , $sql_arr[ 'values' ]                                                                  // Array of validated parameters
                                    ); 

//debuge($sql);die;
                if ( false === $wpdb->query( $sql )  ){                                                                         // Insert into DB
                    debuge_error( 'Error inserting into DB' ,__FILE__ , __LINE__);                     
                }
            }
            
            make_bk_action( 'wpbc_reinit_booking_resource_cache' );
        }                        
    }        
    add_action('wpbc_bresources_check_submit_actions', 'wpbc_bresources_check_submit_actions', 10, 0);   