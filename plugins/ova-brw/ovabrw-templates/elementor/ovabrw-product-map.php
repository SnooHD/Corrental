<?php if ( ! defined( 'ABSPATH' ) ) exit();

$product_id = isset( $args['id'] ) && $args['id'] ? $args['id'] : get_the_id();
$zoom       = isset( $args['zoom'] ) && $args['zoom'] ? $args['zoom'] : 17;
$address    = get_post_meta( $product_id, 'ovabrw_address', true );
$latitude   = get_post_meta( $product_id, 'ovabrw_latitude', true );
$longitude  = get_post_meta( $product_id, 'ovabrw_longitude', true );

if ( ! $latitude ) $latitude 	= get_option( 'ova_brw_latitude_map_default', '39.177972' );
if ( ! $longitude ) $longitude 	= get_option( 'ova_brw_longitude_map_default', '-100.36375' );

?>
<div class="ovabrw-product-map">
	<div id="ovabrw-show-map" class="ovabrw-show-map"></div>
	<input
		type="hidden"
		class="ovabrw-data-product-map"
		data-zoom="<?php echo esc_attr( $zoom ); ?>"
		latitude="<?php echo esc_attr( $latitude ); ?>"
		longitude="<?php echo esc_attr( $longitude ); ?>"/>
	<input
		type="hidden"
		name="pac-input"
		id="pac-input"
		class="pac-input"
		value="<?php echo esc_attr( $address ); ?>"
		autocomplete="off"
		autocapitalize="none"
	/>
</div>