<?php
if ( !function_exists ('nerf_custom_styles') ) {
	function nerf_custom_styles() {
		global $post;	
		
		ob_start();	
		?>
		
			<?php if ( nerf_get_config('main_color') != "" ) {
				$main_color = nerf_get_config('main_color');
			} else {
				$main_color = '#C29F7B';
			}

			if ( nerf_get_config('main_hover_color') != "" ) {
				$main_hover_color = nerf_get_config('main_hover_color');
			} else {
				$main_hover_color = '#191D23';
			}

			if ( nerf_get_config('text_color') != "" ) {
				$text_color = nerf_get_config('text_color');
			} else {
				$text_color = '#181D24';
			}

			if ( nerf_get_config('link_color') != "" ) {
				$link_color = nerf_get_config('link_color');
			} else {
				$link_color = '#181D24';
			}

			if ( nerf_get_config('link_hover_color') != "" ) {
				$link_hover_color = nerf_get_config('link_hover_color');
			} else {
				$link_hover_color = '#C29F7B';
			}

			if ( nerf_get_config('heading_color') != "" ) {
				$heading_color = nerf_get_config('heading_color');
			} else {
				$heading_color = '#181D24';
			}

			$main_color_rgb = nerf_hex2rgb($main_color);
			
			// font
			$main_font = nerf_get_config('main-font');
			$main_font = !empty($main_font) ? json_decode($main_font, true) : array();
			$main_font_family = !empty($main_font['fontfamily']) ? $main_font['fontfamily'] : 'Jost';
			$main_font_weight = !empty($main_font['fontweight']) ? $main_font['fontweight'] : 400;
			$main_font_size = !empty(nerf_get_config('main-font-size')) ? nerf_get_config('main-font-size').'px' : '15px';

			$main_font_arr = explode(',', $main_font_family);
			if ( count($main_font_arr) == 1 ) {
				$main_font_family = "'".$main_font_family."'";
			}
			
			$heading_font = nerf_get_config('heading-font');
			$heading_font = !empty($heading_font) ? json_decode($heading_font, true) : array();
			$heading_font_family = !empty($heading_font['fontfamily']) ? $heading_font['fontfamily'] : 'Jost';
			$heading_font_weight = !empty($heading_font['fontweight']) ? $heading_font['fontweight'] : 500;

			$heading_font_arr = explode(',', $heading_font_family);
			if ( count($heading_font_arr) == 1 ) {
				$heading_font_family = "'".$heading_font_family."'";
			}
			?>
			:root {
			  --nerf-theme-color: <?php echo trim($main_color); ?>;
			  --nerf-text-color: <?php echo trim($text_color); ?>;
			  --nerf-link-color: <?php echo trim($link_color); ?>;
			  --nerf-link_hover_color: <?php echo trim($link_hover_color); ?>;
			  --nerf-heading-color: <?php echo trim($heading_color); ?>;
			  --nerf-theme-hover-color: <?php echo trim($main_hover_color); ?>;

			  --nerf-main-font: <?php echo trim($main_font_family); ?>;
			  --nerf-main-font-size: <?php echo trim($main_font_size); ?>;
			  --nerf-main-font-weight: <?php echo trim($main_font_weight); ?>;
			  --nerf-heading-font: <?php echo trim($heading_font_family); ?>;
			  --nerf-heading-font-weight: <?php echo trim($heading_font_weight); ?>;

			  --nerf-theme-color-005: <?php echo nerf_generate_rgba($main_color_rgb, 0.05); ?>
			  --nerf-theme-color-007: <?php echo nerf_generate_rgba($main_color_rgb, 0.07); ?>
			  --nerf-theme-color-010: <?php echo nerf_generate_rgba($main_color_rgb, 0.1); ?>
			  --nerf-theme-color-015: <?php echo nerf_generate_rgba($main_color_rgb, 0.15); ?>
			  --nerf-theme-color-020: <?php echo nerf_generate_rgba($main_color_rgb, 0.2); ?>
			  --nerf-theme-color-050: <?php echo nerf_generate_rgba($main_color_rgb, 0.5); ?>
			}
			
			<?php if (  nerf_get_config('header_mobile_color') != "" ) : ?>
				#apus-header-mobile {
					background-color: <?php echo esc_html( nerf_get_config('header_mobile_color') ); ?>;
				}
			<?php endif; ?>

	<?php
		$content = ob_get_clean();
		$content = str_replace(array("\r\n", "\r"), "\n", $content);
		$lines = explode("\n", $content);
		$new_lines = array();
		foreach ($lines as $i => $line) {
			if (!empty($line)) {
				$new_lines[] = trim($line);
			}
		}
		
		return implode($new_lines);
	}
}