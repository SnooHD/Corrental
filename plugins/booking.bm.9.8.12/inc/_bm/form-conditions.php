<?php 
/*
This is COMMERCIAL SCRIPT
We are not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
*/

/**
 * Get escaped  'season filter title'      for usage in     'CSS Classes'
 *
 * @param $title    string
 *
 * @return string|null
 */
function wpbc_esc__season_filter_name( $title ) {

	$title = str_replace( ' ', '_', $title );
	$title = strtolower( $title );
	$title = esc_attr( $title );
	return $title;
}


/*======= S E A S O N ===================================================<br />
[condition name="season-times" type="season" value="*"]
  Default: [select rangetime "14:00 - 16:00" "16:00 - 18:00" "18:00 - 20:00"]
[/condition]
[condition name="season-times" type="season" value="High season"]
  High season: [select rangetime  "10:00 - 12:00" "12:00 - 14:00" "14:00 - 16:00" "16:00 - 18:00" "18:00 - 20:00"]
[/condition]
[condition name="season-times" type="season" value="Low season"]
  Low season: [select rangetime "12:00 - 14:00" "14:00 - 16:00"]
[/condition]


============ W E E K D A Y ==============================================<br />
[condition name="weekday-condition" type="weekday" value="*"]
  Default:   [select rangetime  "10:00 - 11:00" "11:00 - 12:00" "12:00 - 13:00" "13:00 - 14:00" "14:00 - 15:00" "15:00 - 16:00" "16:00 - 17:00" "17:00 - 18:00"]
[/condition]
[condition name="weekday-condition" type="weekday" value="1,2"]  
  Monday, Tuesday:    [select rangetime  "10:00 - 12:00" "12:00 - 14:00"]
[/condition]
[condition name="weekday-condition" type="weekday" value="3,4"]
  Wednesday, Thursday:  [select rangetime  "14:00 - 16:00" "16:00 - 18:00" "18:00 - 20:00"]
[/condition]
[condition name="weekday-condition" type="weekday" value="5,6,0"]
  Friday, Saturday, Sunday:  [select rangetime  "10:00 - 12:00" "12:00 - 14:00" "14:00 - 16:00" "16:00 - 18:00" "18:00 - 20:00"]
[/condition]

=======Live========================================================<br />
<div class="conditional_section_season-times">
    <div class="conditional_section_element_season-times  wpdevbk_season_ wpdevbk_default_condition" id="cond_el_56_580769713784"> 
      Default: ...
    </div>
</div>
....
<div class="booking_form_garbage" id="booking_form_garbage56">
    <div style="" class="conditional_section_element_season-times  wpdevbk_season_low_season wpdevbk_optional_condition" id="cond_el_56_449274684248"> 
      Low season: ...
    </div><div style="" class="conditional_section_element_season-times  wpdevbk_season_high_season wpdevbk_optional_condition" id="cond_el_56_651996188116"> 
      High season: ...
    </div>
</div> 
==== TODO: Finish this conditon: name="visitors_selection" type="select" =================================================================<br />
  [condition name="visitors_selection" type="select" value="1" options="name:visitors"]
         [select rangetime  "12:00 - 14:00" "14:00 - 16:00"] 
  [/condition]
  [condition name="visitors_selection" type="select" value="2" options="name:visitors"]
         [select rangetime  "16:00 - 18:00" "18:00 - 20:00"] 
  [/condition]    
*/
// BOOKING FORM    PARSING    for the    C O N D I T I O N S ///////////////////////////////////////////////////////////////////////////////
function wpdev_bk_form_conditions_parsing($form, $resource_id){ global $wpdb;

	$conditions = wpbc_conditions_form__get_sections( $form );

    $seasons = array();
    
    // Replace the SHORTCODES in the FORM content of the form to the HTML ///////////
	foreach ( $conditions as $condition_name => $values ) {                   // Conditions doe the specific Name

		$class_condition_name = 'conditional_section_element_'.$condition_name .' ';

		for ( $i = 0; $i < count( $values ); $i ++ ) {

            $value = $values[$i];

	        if ( $i == 0 ) {
		        $my_html = '<div class="conditional_section_' . $condition_name . '">';             // First condition, so  we are start condition section
	        } else {
		        $my_html = '';
	        }

            $class_prefix = $class_condition_name;
            $c_value = explode(',', $value['value']);
			foreach ( $c_value as $c_v_orig ) {

	            $c_v = esc_attr( strtolower( str_replace( ' ', '_', $c_v_orig ) ) );

	            if ( ( $c_v == '*' ) || ( $c_v == '' ) ) {
		            $is_this_element_default = true;
	            } else {
		            $is_this_element_default = false;
	            }
                
                if ( $is_this_element_default ) $c_v =''; 

                switch ($value['type']) {
                    case 'season':   
                        $seasons[]     = $c_v_orig;
                        $class_prefix .= ' wpdevbk_season_' . $c_v;
                        break;
                    case 'weekday':
                        $class_prefix .= ' wpdevbk_weekday_' . $c_v;
                        break;
                    case 'select':                                              //TODO: Finish this condition logic
                        $class_prefix .= ' wpdevbk_select_' . $c_v;
                        break;
                    default:
                        $class_prefix .= ' wpdevbk_condition_' . $c_v;
                        break;
                } 
            }
            $my_random_id = time() * rand(0,1000);

            $my_html.= '<div id="cond_el_' . $resource_id . '_' . $my_random_id . '" ';
            $my_html.=       'class="' . $class_prefix . ( ($is_this_element_default) ? ' wpdevbk_default_condition' : ' wpdevbk_optional_condition' ) . '" ';
            $my_html.=       ( ($is_this_element_default) ? '' : ' style="display:none;" ' );
            $my_html.= '> ';
            $my_html.=      $value['content'];
            $my_html.= '</div>';

            if ($i==(count($values)-1))  $my_html .='</div>';               // Last condtion, so  we are close condition section

            $form = str_replace($value['structure'], $my_html, $form);      // Replace
        }
    }

    // Hide ALL optional  elemtns to the Garbage section using JavaScrip - after page is loaded.
    $start_script_code = "<script type='text/javascript'>";
    $start_script_code .="jQuery(document).ready( function(){ ";
    $start_script_code .= "   moveOptionalElementsToGarbage( " . $resource_id . " ); ";
    $start_script_code .="});";
    $start_script_code .= "</script>";   

	$start_script_code .= "<script type='text/javascript'> jQuery(document).ready( function(){ ";
	$seasons_y_m_d__esc_title__arr = wpbc_set__season_filters_dates( $seasons );
	$start_script_code .= "  _wpbc.seasons__set( " . $resource_id . ", " . wp_json_encode( $seasons_y_m_d__esc_title__arr ) . " ); ";
	$start_script_code .= " }); </script>";

    return $form . $start_script_code;

}



	function wpbc_conditions_form__get_sections( $form ){

		    // Types of the conditions
	    $condition_types =  'season|weekday|select';

	    $pattern_to_search='%\[\s*condition\s+name="(\s*[^"]*)"\s+type="('.$condition_types.')"\s+value="(\s*[^"]*)"\s*(options="(\s*[^"]*)"\s*)?\]%';

		preg_match_all( $pattern_to_search, $form, $matches, PREG_SET_ORDER );

	    // Matches Array itme structure: ////////////////////////////////////////////////*
	    /*
	    //Full:  [0] => [condition name="times2" type="weekday" value="1,2,3,4,5" options="name:data"]
	    //name:  [1] => times2
	    //type:  [2] => weekday
	    //value: [3] => 1,2,3,4,5
	    //options[5] => name:data
	    *///////////////////////////////////////////////////////////////////////////////

	    // Convert found items into the structure ///////////////////////////////////////
	    $conditions=array();
	    foreach ($matches as $condition) {

	        $c_full  = $condition[0]; // => [condition name="times2" type="weekday" value="1,2,3,4,5"]
	        $c_name  = $condition[1]; // => times2
	        $c_type  = $condition[2]; // => weekday
	        $c_value = $condition[3]; // => 1,2,3,4,5
	                                  // => name:data    - this value inside optional parameter: options="name:data"
	        if (isset($condition[5])) $c_options = $condition[5];
	        else $c_options = false;

	        $offset_open_start  = strpos($form, $c_full);
	        $offset_open_end = strpos($form, ']', $offset_open_start+1);

	        $offset_close_start = strpos($form, '[/condition]', $offset_open_end+1);
	        $offset_close_end = strpos($form, ']', $offset_close_start+1);

	        $c_content           = substr($form, $offset_open_end+1, ($offset_close_start-$offset_open_end-1) ) ;
	        $c_content_structure = substr($form, $offset_open_start, ($offset_close_end-$offset_open_start+1) ) ;


	        if (! isset($conditions[$c_name])) $conditions[$c_name] = array();

	        $conditions[$c_name][]=array(
	                                'type'=>$c_type,
	                                'value'=>$c_value,
	                                'options'=>$c_options,
	                                'content'=>$c_content,
	                                'structure'=>$c_content_structure           // Full Structure to Replace
	                                );
	    }
	    /////////////////////////////////////////////////////////////////////////////////

		return $conditions;
	}


	/**
	 * Get array  of season names from  conditional  season sections -> ["High season", "Weekend season"]
	 *
	 * @param $form             full booking form content configuration
	 *
	 * @return array            ["High season", "Weekend season"]
	 */
	function wpbc_conditions_get_season_titles_arr( $form ){

		$conditions = wpbc_conditions_form__get_sections( $form );

	    $season_names_arr = array();

		foreach ( $conditions as $condition_name => $condition_sections_arr ) {

			for ( $i = 0; $i < count( $condition_sections_arr ); $i ++ ) {

				$one_section_arr = $condition_sections_arr[ $i ];

				if ( 'season' == $one_section_arr['type'] ) {

					$c_value = explode( ',', $one_section_arr['value'] );

					foreach ( $c_value as $c_v_orig ) {
						if ( '*' != $c_v_orig ) {
							$season_names_arr[] = $c_v_orig;
						}
					}
				}
	        }
	    }

		return $season_names_arr;
	}


/**
 * Get specific conditional  section content that  belong to  specific date ($my_day_tag)
 *
 * @param $conditions                              Array of condition sections parsed from ->  wpbc_conditions_form__get_sections( $full_booking_form_content );
 *                                                               = ["season-times":[{"type":"season","value":"*","options":false,"content":"\r\n     ...]
 * @param $my_day_tag                              SQL date
 *                                                               =  '2023-10-26'
 * @param $cached__sql_data__season_filters_arr    Optional. Required for conditional SEASON sections.
 *                                                               =  [    0 =>  stdClass(object)(
 *                                                                                                  'id' => '3',
 *                                                                                                  'title' => 'High season',
 *                                                                                                  'filter' => 'a:4:{s:8:"weekdays";a:7:{i:0;s:2:"On";i:1;s:2:"On";...',
 *                                                                                               ),
 *                                                                       1 =>  stdClass(object)(
 *                                                                                                  'id' => '4',
 *                                                                                                  'title' => 'Weekend season',
 *                                                                                                  'filter' => 'a:4:{s:8:"weekdays";a:7:{i:0;s:2:"On";i:1;...',
 *                                                                                              )
 *                                                                  ]
 *
 * @return mixed|string
 */
function wpbc_conditions_form__get_section__depend_from_date( $conditions, $my_day_tag, $cached__sql_data__season_filters_arr = array() ){

	$default_section_content = '';

	$conditions = array_reverse( $conditions );

	foreach ( $conditions as $condition_name => $condition_sections_arr ) {                   // Conditions doe the specific Name

		for ( $i = 0; $i < count( $condition_sections_arr ); $i ++ ) {

			/**
			 *   [  type = "weekday"
			 *		value = "*"
			 *		options = false
			 *		content = "\r\n  Default:   [select rangetime  "10:00 - 11:00" "11:00 - 12:00" "12:00 - 13:00" "13:00 - 14:00" "14:00 - 15:00" "15:00 - 16:00" "16:00 - 17:00" "17:00 - 18:00"]\r\n"
			 *		structure = "[condition name="weekday-condition" type="weekday" value="*"]\r\n  Default:   [select rangetime  "10:00 - 11:00" "11:00 - 12:00" "12:00 - 13:00" "13:00 - 14:00" "14:00 - 15:00" "15:00 - 16:00" "16:00 - 17:00" "17:00 - 18:00"]\r\n[/condition]"
			 *   ]
			 */
			$one_section_arr = $condition_sections_arr[ $i ];

			$c_value = explode( ',', $one_section_arr['value'] );

			foreach ( $c_value as $c_v_orig ) {

				$c_v = esc_attr( strtolower( str_replace( ' ', '_', $c_v_orig ) ) );        // ?
				$is_this_element_default = ( ( $c_v == '*' ) || ( $c_v == '' ) );

				if ( $is_this_element_default ) {
					$default_section_content = $one_section_arr['content'];
					continue;
				}


				switch ( $one_section_arr['type'] ) {

                    case 'season':
                        // $c_v  <= Season Title
                        if ( wpbc_is_date_in_this_season_title( $my_day_tag, $c_v_orig, $cached__sql_data__season_filters_arr )  ) {
							return $one_section_arr['content'];
                        }
                        break;

                    case 'weekday':
                        // $c_v  <= week day  number
	                    if ( wpbc_date_get_week_day_num( $my_day_tag ) == $c_v ) {
		                    return $one_section_arr['content'];
	                    }
                        break;

                    default:

                        break;
                }
            }


		}
	}


	return $default_section_content;
}


			/**
			 * Check  Is this date  $MY_DAY_TAG  in this season filter  $SEASON_TITLE
			 *
			 * @param $my_day_tag                                 '2023-10-26'
			 * @param $season_title                               'High season'
			 * @param $cached__sql_data__season_filters_arr       = [   0 = stdClass{
			 *															`	            id = "3"
			 *																            title = "High season"
			 *																            filter = "a:4:{s:8:"weekdays";a:7:{i:..."
			 *                                                                      ),
			 *															1 = stdClass{ ... }
		  `	 *                                                      ]
			 *
			 * @return bool
			 */
			function wpbc_is_date_in_this_season_title( $sql_day_tag, $season_title, $cached__sql_data__season_filters_arr ){

				// TODO:  check  this wpbc_set__season_filters_dates()  for season  conditions

				list( $year, $month, $day ) = explode( '-', $sql_day_tag );
				$day   = intval( $day );
				$month = intval( $month );
				$year  = intval( $year );

				foreach ( $cached__sql_data__season_filters_arr as $filter_value ) {                                    // Season filters

					if (
						   ( ! empty( $filter_value->filter ) )
						&& ( ! empty( $filter_value->title  ) )
						&& ( $filter_value->title == $season_title )
					) {

						$is_day_inside_of_filter = wpbc_is_day_in_season_filter( $day, $month, $year, $filter_value->filter );

						if ( $is_day_inside_of_filter ) {
							return true;
						}
					}
				}

			    return  false;
			}

