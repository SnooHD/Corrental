<?php

class WC_Product_Jet_Booking extends WC_Product {

	/**
	 * Initialize JetBooking product.
	 *
	 * @param WC_Product|int $product Product instance or ID.
	 */
	public function __construct( $product = 0 ) {
		parent::__construct( $product );
	}

	/**
	 * Get type.
	 *
	 * Get internal type.
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_type() {
		return 'jet_booking';
	}

	/**
	 * Is sold individually.
	 *
	 * Check if a product is sold individually (no quantities).
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_sold_individually() {
		return true;
	}

	/**
	 * Ger virtual.
	 *
	 * Set product as virtual.
	 *
	 * @access public
	 *
	 * @param string $context What the value is for. Valid values are `view` and `edit`.
	 *
	 * @return bool
	 */
	public function get_virtual( $context = 'view' ) {
		return true;
	}

	/**
	 * Is purchasable.
	 *
	 * Returns false if the product cannot be bought.
	 *
	 * @access public
	 *
	 * @return bool
	 */
	public function is_purchasable() {
		return apply_filters( 'woocommerce_is_purchasable', true, $this );
	}

	/**
	 * Add to cart url.
	 *
	 * Get the add to url used mainly in loops.
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function add_to_cart_url() {
		return apply_filters( 'woocommerce_product_add_to_cart_url', $this->get_permalink(), $this );
	}

	/**
	 * Add to cart text.
	 *
	 * Get the add to cart button text.
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function add_to_cart_text() {
		return apply_filters( 'woocommerce_product_add_to_cart_text', __( 'View Details', 'woocommerce' ), $this );
	}

	/**
	 * Single add to cart text.
	 *
	 * Get the add to cart button text for the single page.
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function single_add_to_cart_text() {
		return apply_filters( 'woocommerce_product_single_add_to_cart_text', __( 'Book now', 'woocommerce' ), $this );
	}

	/**
	 * Add to cart description.
	 *
	 * Get the add to cart button text description - used in aria tags.
	 *
	 * @since  3.3.0
	 * @access public
	 *
	 * @return string
	 */
	public function add_to_cart_description() {
		/* translators: %s: Product title */
		return apply_filters( 'woocommerce_product_add_to_cart_description', sprintf( __( 'Book &ldquo;%s&rdquo;', 'woocommerce' ), $this->get_name() ), $this );
	}

}
