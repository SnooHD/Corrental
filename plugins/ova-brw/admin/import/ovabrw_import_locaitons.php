<?php if ( !defined( 'ABSPATH' ) ) exit();

if ( ! function_exists( 'ovabrw_import_locations' ) ) {
	function ovabrw_import_locations() {
		$all_rooms 		= get_all_rooms();
		$all_locations 	= get_all_rooms( 'transportation' );
		$all_products 	= get_all_rooms( 'transportation', true );
		$bytes      	= wp_max_upload_size();
		$size       	= size_format( $bytes );
		$upload_dir 	= wp_upload_dir();

		?>
		<div class="ova-import-locations">
			<form class="import-locations" enctype="multipart/form-data" method="post">
				<h2 class="heading"><?php esc_html_e( 'Import locations from a CSV file', 'ova-brw' ); ?></h2>
        		<select name="product_id">
        			<option value="">
                        <?php esc_html_e( '-- Choose Product --', 'ova-brw' ); ?>
                    </option>
        			<?php 
        				if ( $all_locations->have_posts() ) : while ( $all_locations->have_posts() ) : $all_locations->the_post(); ?>
        					<option value="<?php the_id(); ?>">
                                <?php the_title(); ?>
                            </option>
        				<?php endwhile;endif;wp_reset_postdata();
        			?>
        		</select>
				<p class="note"><?php esc_html_e( 'Only applicable for Rental: Location', 'ova-brw' ); ?></p>
				<div class="import-locations-fields">
					<h4 for="upload">
						<?php esc_html_e( 'Choose a CSV file from your computer:', 'ova-brw' ); ?>
					</h4>
					<input type="file" id="upload" name="location_file" size="25">
					<input type="hidden" name="action_import" value="import_locations">
					<input type="hidden" name="max_file_size" value="<?php echo esc_attr( $bytes ); ?>" />
					<br/>
					<span class="max-size">
						<?php
						printf(
							esc_html__( 'Maximum size: %s', 'ova-brw' ),
							esc_html( $size )
						);
						?>
					</span>
					<a href="<?php echo esc_url( OVABRW_PLUGIN_URI.'/admin/import/demo.csv' ); ?>" class="demo" download>
						<?php esc_html_e( 'Demo file.csv', 'ova-brw' ); ?>
					</a>
				</div>
				<div class="import-locations-submit">
					<button type="submit" class="button">
						<?php esc_html_e( 'Import', 'ova-brw' ); ?>
					</button>
				</div>
			</form>
			<form class="import-setup-locations" enctype="multipart/form-data" method="post">
        		<select name="product_id">
        			<option value="">
                        <?php esc_html_e( '-- Choose Product --', 'ova-brw' ); ?>
                    </option>
        			<?php 
        				if ( $all_products->have_posts() ) : while ( $all_products->have_posts() ) : $all_products->the_post(); ?>
        					<option value="<?php the_id(); ?>">
                                <?php the_title(); ?>
                            </option>
        				<?php endwhile;endif;wp_reset_postdata();
        			?>
        		</select>
				<p class="note"><?php esc_html_e( 'Only applicable for Rental: Day, Hour, Mixed, Period of Time', 'ova-brw' ); ?></p>
				<div class="import-locations-fields">
					<h4 for="upload">
						<?php esc_html_e( 'Choose a CSV file from your computer:', 'ova-brw' ); ?>
					</h4>
					<input type="file" id="upload" name="location_file" size="25">
					<input type="hidden" name="action_import" value="import_setup_locations">
					<input type="hidden" name="max_file_size" value="<?php echo esc_attr( $bytes ); ?>" />
					<br/>
					<span class="max-size">
						<?php
						printf(
							esc_html__( 'Maximum size: %s', 'ova-brw' ),
							esc_html( $size )
						);
						?>
					</span>
					<a href="<?php echo esc_url( OVABRW_PLUGIN_URI.'/admin/import/demo2.csv' ); ?>" class="demo" download>
						<?php esc_html_e( 'Demo file.csv', 'ova-brw' ); ?>
					</a>
				</div>
				<div class="import-locations-submit">
					<button type="submit" class="button">
						<?php esc_html_e( 'Import', 'ova-brw' ); ?>
					</button>
				</div>
			</form>
			<form class="remove-locations" enctype="multipart/form-data" method="post">
				<h2 class="heading"><?php esc_html_e( 'Remove Locations', 'ova-brw' ); ?></h2>
        		<select name="product_id">
        			<option value="">
                        <?php esc_html_e( '-- Choose Product --', 'ova-brw' ); ?>
                    </option>
        			<?php 
        				if ( $all_rooms->have_posts() ) : while ( $all_rooms->have_posts() ) : $all_rooms->the_post(); ?>
        					<option value="<?php the_id(); ?>">
                                <?php the_title(); ?>
                            </option>
        				<?php endwhile;endif;wp_reset_postdata();
        			?>
        		</select>
				<input type="hidden" name="action_import" value="remove_locations">
				<div class="remove-locations-submit">
					<button type="submit" class="button">
						<?php esc_html_e( 'Remove', 'ova-brw' ); ?>
					</button>
				</div>
			</form>
		</div>
		<?php
	}
}