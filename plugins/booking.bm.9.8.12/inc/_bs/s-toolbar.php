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
    //  B u t t o n s   -  ADD NEW Booking page
    ////////////////////////////////////////////////////////////////////////////  

    /** Auto Fill booking form  Button*/
    function wpbc_toolbar_btn__auto_fill() {

        if ( isset( $_GET['booking_type'] ) )
             $bk_type = intval ( $_GET['booking_type'] );
        else $bk_type = 1;

        ?><a href="javascript:void(0)" onclick="javascript:wpbc_autofill_booking_form();" class="button-secondary button" style="margin-right: 15px;"><?php _e('Auto-fill form' , 'booking') ?></a><?php

        ?><script type="text/javascript">
            function wpbc_autofill_booking_form(){

                var my_element_value = '---';
                var form_elements = jQuery('.booking_form_div input');

                jQuery.each(form_elements, function(){

                    if (       ( this.type !== 'button' ) 
                            && ( this.type !== 'hidden' ) 
                            && ( this.name.search('starttime') == -1 ) 
                            && ( this.name.search('endtime') == -1 ) 
                       ) {        //FixIn:6.0.1.12    

                        if ( this.type == 'checkbox' ) {
                            jQuery( this ).prop('checked', true);
                        }
                        this.value = my_element_value;
                        if ( this.name.search('email') != -1 ) {
							this.value = 'blank@wpbookingmanager.com';
                        }
                        if ( this.name.search('starttime') != -1 ) { this.name = 'temp';  this.value=''; } // set name of time to someother name
                        if ( this.name.search('endtime')   != -1 ) { this.name = 'temp2'; this.value=''; }  // set name of time to someother name
                    }
                });

                mybooking_submit( 
                                    document.getElementById('booking_form<?php echo $bk_type; ?>' )
                                    , <?php echo $bk_type; ?>
                                    , '<?php echo wpbc_get_maybe_reloaded_booking_locale(); ?>'
                                );                    
            }
        </script><?php     
    }
