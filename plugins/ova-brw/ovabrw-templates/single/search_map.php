<?php if( ! defined( 'ABSPATH' ) ) exit();

extract( $args );

$date_format 	= ovabrw_get_date_format();
$hour_default 	= ovabrw_get_setting( get_option( 'ova_brw_booking_form_default_hour', '07:00' ) );
$time_step 		= ovabrw_get_setting( get_option( 'ova_brw_booking_form_step_time', '30' ) );
$lat_default 	= ovabrw_get_setting( get_option( 'ova_brw_latitude_map_default', '39.177972' ) );
$lng_default 	= ovabrw_get_setting( get_option( 'ova_brw_longitude_map_default', '-100.36375' ) );

if ( empty( $lat_default ) ) {
	$lat_default = '39.177972';
}

if ( empty( $lng_default ) ) {
	$lng_default = '-100.36375';
}

$data = array(
	'orderby' 	=> $orderby,
	'order' 	=> $order,
	'posts_per_page' => $posts_per_page,
);

$products = ovabrw_search_products( $data );
$have_map = $show_map === 'yes' ? ' ova_have_map' : '';

// Card Template
$card = isset( $args['card'] ) ? $args['card'] : '';

if ( $card === 'card5' || $card === 'card6' ) $column = 'one-column';

?>

<div class="elementor_search_map<?php printf( $have_map ); ?><?php echo $card ? ' ovabrw-search-modern' : ''; ?>">
	<?php if ( $show_map == 'yes'): ?>
		<div class="toggle_wrap">
			<span data-value="wrap_search" class="active"><?php esc_html_e( 'Results', 'ova-brw' ); ?></span>
			<span data-value="wrap_map"><?php esc_html_e( 'Map', 'ova-brw' ); ?></span>
		</div>
	<?php endif; ?>
	<div class="wrap_search_map">
		<!-- Search Map -->
		<div class="wrap_search">
			<?php if ( $show_filter == 'yes' ): ?>
				<div class="fields_search ovabrw_wd_search">
					<span class="toggle_filters ">
						<?php esc_html_e( 'Toggle Filters', 'ova-brw' ); ?>
						<i class="icon_down arrow_triangle-down"></i>
						<i class="icon_up arrow_triangle-up"></i>
					</span>
					<form class="form_search_map" autocomplete="nope" autocorrect="off" autocapitalize="none">
						<div class="wrap_content field">
							<?php
								$show_radius = false;
								// Fields
								foreach ( $args as $key => $value) {
									if ( strpos( $key,'field_' ) !== false ) {
										switch ( $args[$key] ) {
											case 'name': ?>
												<div class="label_search wrap_search_name">
													<input
														type="text"
														name="ovabrw_name_product"
														autocomplete="nope"
														autocorrect="off"
														autocapitalize="none"
														placeholder="<?php esc_attr_e( 'Name Product', 'ova-brw' ); ?>"
													/>
												</div>
											<?php break;
											case 'category': ?>
												<div class="label_search wrap_search_category">
													<?php echo ovabrw_cat_rental( '', '', '' ); ?>
												</div>
											<?php break;
											case 'location': ?>
												<div class="label_search wrap_search_location">
													<input
														type="hidden"
														name="map_lat"
														id="map_lat"
													/>
													<input
														type="hidden"
														name="map_lng"
														id="map_lng"
													/>
													<input
														type="text"
														id="pac-input"
														name="map_address"
														value=""
														class="controls"
														placeholder="<?php esc_attr_e( 'Location', 'ova-brw' ); ?>"
														autocomplete="nope"
														autocorrect="off"
														autocapitalize="none"
													/>
													<i class="locate_me icon_circle-slelected" id="locate_me" title="<?php esc_attr_e( 'Use My Location', 'ova-brw' ); ?>"></i>
													<input
														type="hidden"
														value=""
														name="map_name"
														id="map_name"
													/>
												</div>
											<?php break;
											case 'start_location': ?>
												<div class="label_search wrap_search_start_location">
													<?php echo ovabrw_get_locations_html( 'ovabrw_pickup_loc', 'ovabrw_pickup_loc', '' ); ?>
												</div>
											<?php break;
											case 'end_location': ?>
												<div class="label_search wrap_search_end_location">
													<?php echo ovabrw_get_locations_html( 'ovabrw_dropoff_loc', 'ovabrw_pickup_loc', '' ); ?>
												</div>
											<?php break;
											case 'start_date': ?>
												<div class="label_search wrap_search_start_date">
													<input
														type="text"
														name="ovabrw_pickup_date"
														value=""
														class="ovabrw_datetimepicker ovabrw_start_date"
														placeholder="<?php esc_html_e( 'Pick-up date ...', 'ova-brw' ); ?>"
														autocomplete="off" 
														data-hour_default="<?php echo esc_attr( $hour_default ); ?>"
														data-time_step="<?php echo esc_attr( $time_step ); ?>"
														data-dateformat="<?php echo esc_attr( $date_format ); ?>"
														onfocus="blur();"
													/>
												</div>
											<?php break;
											case 'end_date': ?>
												<div class="label_search wrap_search_end_date">
													<input
														type="text"
														name="ovabrw_pickoff_date"
														value=""
														class="ovabrw_datetimepicker ovabrw_end_date"
														placeholder="<?php esc_html_e( 'Drop-off date ...', 'ova-brw' ); ?>"
														autocomplete="off"
														data-hour_default="<?php echo esc_attr( $hour_default ); ?>"
														data-time_step="<?php echo esc_attr( $time_step ); ?>"
														data-dateformat="<?php echo esc_attr( $date_format ); ?>"
														onfocus="blur();"
													/>
												</div>
											<?php break;
											case 'attribute':
												$data_html_attr = ovabrw_dropdown_attributes();

												if ( $data_html_attr['html_attr'] ):
												?>
													<div class="label_search wrap_search_attribute ovabrw_search">
														<?php echo $data_html_attr['html_attr']; ?>
													</div>
												<?php
												endif;

												if ( $data_html_attr['html_attr_value'] ):
												?>
													<?php echo $data_html_attr['html_attr_value']; ?>
												<?php
												endif;
												break;
											case 'tag': ?>
												<div class="label_search wrap_search_tag ovabrw_wd_search">
													<input
														type="text"
														name="ovabrw_tag_product"
														autocomplete="nope"
														autocorrect="off"
														autocapitalize="none"
														placeholder="<?php esc_html_e('Tag Product', 'ova-brw'); ?>"
													/>
												</div>
											<?php

											default:
												// code...
												break;
										}
									}
								}
								// End Fields

								// Taxonomies
								$args_taxonomy 	= array();
								$taxonomies 	= get_option( 'ovabrw_custom_taxonomy', array() );
								$show_taxonomy 	= get_option( 'ova_brw_search_show_tax_depend_cat', 'yes' );
								if ( $list_taxonomy_custom && is_array( $list_taxonomy_custom ) ) {
									foreach ( $list_taxonomy_custom as $obj_taxonomy ) {
										$taxonomy_slug = $obj_taxonomy['taxonomy_custom'];

										if ( isset( $taxonomies[$taxonomy_slug] ) && ! empty( $taxonomies[$taxonomy_slug] ) ) {
											$taxonomy_name = $taxonomies[$taxonomy_slug]['name'];
											$html_taxonomy = ovabrw_search_taxonomy_dropdown( $taxonomy_slug, $taxonomy_name );
											if ( ! empty( $taxonomy_name ) && $html_taxonomy ):
												$args_taxonomy[$taxonomy_slug] = $taxonomy_name;
											?>
												<div class="label_search wrap_search_taxonomies <?php echo $taxonomy_slug; ?>">
													<?php echo $html_taxonomy; ?>
												</div>
											<?php
											endif;
										}
									}
									?>
									<div class="show_taxonomy" data-show_taxonomy="<?php echo esc_html( $show_taxonomy ); ?>"></div>
									<?php
								}
								// End Taxonomies
							?>
							<input 	type="hidden" id="data_taxonomy_custom" name="data_taxonomy_custom" 
									value="<?php echo esc_attr( json_encode( $args_taxonomy ) ); ?>" />
						</div><!-- wrap_content -->

						<!-- Radius -->
						<div class="wrap_search_radius" 
							data-map_range_radius="<?php echo apply_filters( 'ovabrw_ft_map_range_radius', 50 ); ?>" 
							data-map_range_radius_min="<?php echo apply_filters( 'ovabrw_ft_map_range_radius_min', 0 ); ?>" 
							data-map_range_radius_max="<?php echo apply_filters( 'ovabrw_ft_map_range_radius_max', 100 ); ?>">
							<span><?php esc_html_e( 'Radius:', 'ova-brw' ); ?></span>
							<span class="result_radius"><?php esc_html_e( '50km', 'ova-brw' ); ?></span>
							<div id="wrap_pointer"></div>
							<input
								type="hidden"
								value="<?php echo apply_filters( 'ovabrw_ft_map_range_radius', 50 ); ?>"
								name="radius"
							/>
						</div>
						<!-- End Radius -->

						<!-- Filter title -->
						<div class="wrap_search_filter_title">
							<div class="results_found">
								<?php if ( $products->found_posts == 1 ): ?>
								<span>
									<?php echo sprintf( esc_html__( '%s Result Found', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
								</span>
								<?php else: ?>
								<span>
									<?php echo sprintf( esc_html__( '%s Results Found', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
								</span>
								<?php endif; ?>

								<?php if ( 1 == ceil( $products->found_posts/ $products->query_vars['posts_per_page']) && $products->have_posts() ): ?>
									<span>
										<?php echo sprintf( esc_html__( '(Showing 1-%s)', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
									</span>
								<?php elseif ( !$products->have_posts() ): ?>
									<span></span>
								<?php else: ?>
									<span>
										<?php echo sprintf( esc_html__( '(Showing 1-%s)', 'ova-brw' ), esc_html( $products->query_vars['posts_per_page'] ) ); ?>
									</span>
								<?php endif; ?>
							</div>

							<div id="search_sort">
								<?php $search_sort_default = apply_filters( 'search_sort_default', 'date-desc' ); ?>
								<select name="sort">
									<option value=""><?php esc_html_e( 'Sort By', 'ova-brw' ); ?></option>
									<option value="date-desc" <?php if( $search_sort_default == 'date-desc' ) echo 'selected'; ?>>
										<?php esc_html_e( 'Newest First', 'ova-brw' ); ?>
									</option>
									<option value="date-asc" <?php if( $search_sort_default == 'date-asc' ) echo 'selected'; ?>>
										<?php esc_html_e( 'Oldest First', 'ova-brw' ); ?>
									</option>
									<option value="a-z" <?php if( $search_sort_default == 'a-z' ) echo 'selected'; ?>>
										<?php esc_html_e( 'A-Z', 'ova-brw' ); ?>
									</option>
									<option value="z-a" <?php if( $search_sort_default == 'z-a' ) echo 'selected'; ?> >
										<?php esc_html_e( 'Z-A', 'ova-brw' ); ?>
									</option>
								</select>
							</div>
						</div><!-- End filter title -->
					</form>
				</div><!-- fields_search -->
			<?php endif; ?>

			<!-- Load more -->
			<div class="wrap_load_more" style="display: none;">
				<svg class="loader" width="50" height="50">
					<circle cx="25" cy="25" r="10" stroke="#e86c60"/>
					<circle cx="25" cy="25" r="20" stroke="#e86c60"/>
				</svg>
			</div>
			<!-- End load more -->

			<!-- Search result -->
			<div
				id="search_result"
				class="search_result"
				data-card="<?php echo esc_attr( $card ); ?>"
				data-column="<?php echo esc_attr( $column ); ?>"
				data-zoom="<?php echo esc_attr( $zoom ); ?>"
				data-default-location="<?php echo esc_attr( $show_default_location ); ?>"
				data-order="<?php echo esc_attr( $order ); ?>"
				data-orderby="<?php echo esc_attr( $orderby ); ?>"
				data-per_page="<?php echo esc_attr( $posts_per_page ); ?>"
				data-lat="<?php echo esc_attr( $lat_default ); ?>"
				data-lng="<?php echo esc_attr( $lng_default ); ?>"
				data-marker_option="<?php echo esc_attr( $marker_option ); ?>"
				data-marker_icon="<?php echo esc_attr( $marker_icon['url'] ); ?>"
				data-show_featured="<?php echo esc_attr( $show_featured ); ?>">
				<?php
					$total = $products->max_num_pages;
					if (  $total > 1 ): ?>
						<div class="ovabrw_pagination_ajax">
						<?php
							echo ovabrw_pagination_ajax( $products->found_posts, $products->query_vars['posts_per_page'], 1 );
						?>
						</div>
						<?php
					endif;
				?>
			</div><!-- search_result -->
			<!-- Search result -->
		</div><!-- wrap_search -->

		<?php if ( $show_map == 'yes' ): ?>
			<div class="wrap_map">
				<div id="show_map"></div>
			</div>
		<?php endif; ?>
	</div>
</div>
<!-- wc_set_loop_prop( 'columns', 1 ); -->