<?php if ( ! defined( 'ABSPATH' ) ) exit(); ?>
<div class="ovabrw-wcst-global">
	<?php do_action( 'ovabrw-ac-wcst-global-before' ); ?>
	<!-- Font -->
	<?php include( OVABRW_PLUGIN_PATH.'/admin/setting/views/ovabrw-wcst-global-font.php' ); ?>
	<!-- End Font -->

	<!-- Color -->
	<?php include( OVABRW_PLUGIN_PATH.'/admin/setting/views/ovabrw-wcst-global-color.php' ); ?>
	<!-- End Color -->

	<!-- Heading -->
	<?php include( OVABRW_PLUGIN_PATH.'/admin/setting/views/ovabrw-wcst-global-heading.php' ); ?>
	<!-- End Heading -->

	<!-- Second Heading -->
	<?php include( OVABRW_PLUGIN_PATH.'/admin/setting/views/ovabrw-wcst-global-second-heading.php' ); ?>
	<!-- End Second Heading -->

	<!-- Label -->
	<?php include( OVABRW_PLUGIN_PATH.'/admin/setting/views/ovabrw-wcst-global-label.php' ); ?>
	<!-- End Label -->

	<!-- Text -->
	<?php include( OVABRW_PLUGIN_PATH.'/admin/setting/views/ovabrw-wcst-global-text.php' ); ?>
	<!-- End Text -->

	<!-- Card -->
	<?php include( OVABRW_PLUGIN_PATH.'/admin/setting/views/ovabrw-wcst-global-card.php' ); ?>
	<!-- End Card -->
	<?php do_action( 'ovabrw-ac-wcst-global-after' ); ?>
</div>