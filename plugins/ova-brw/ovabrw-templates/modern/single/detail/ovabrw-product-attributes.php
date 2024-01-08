<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;

	if ( isset( $args['product_id'] ) && $args['product_id'] ) {
		$product = wc_get_product( $args['product_id'] );
	}

	if ( ! $product ) return;

	$attributes = $product->get_attributes();
	$attr_args 	= [];

	if ( ! empty( $attributes ) && is_array( $attributes ) ) {
		foreach ( $attributes as $attr_name => $obj_attr ) {
			$attr_label = wc_attribute_label( $attr_name, $product );
			$attr_text 	= $product->get_attribute( $attr_name );

			if ( $attr_label && $attr_text ) $attr_args[$attr_label] = $attr_text;
		}
	}
?>
<?php if ( ! empty( $attr_args ) ): ?>
	<ul class="ovabrw-product-attributes">
		<?php foreach ( $attr_args as $label => $val ): ?>
			<li class="item-attribute">
				<span class="label"><?php echo esc_html( $label.':' ); ?></span>
				<span class="value"><?php echo esc_html( $val ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>