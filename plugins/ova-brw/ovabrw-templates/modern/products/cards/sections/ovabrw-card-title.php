<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;
	if ( ! $product ) return;

	$product_id = $product->get_id();

	$title 	= get_the_title( $product_id );
	$url 	= get_the_permalink( $product_id );	
?>
<?php if ( $title ): ?>
	<h2 class="ovabrw-title">
		<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
	</h2>
<?php endif; ?>