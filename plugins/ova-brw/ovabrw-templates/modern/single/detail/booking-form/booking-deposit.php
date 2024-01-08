<?php if ( ! defined( 'ABSPATH' ) ) exit();
	global $product;
	if ( ! $product ) return;

	$product_id = $product->get_id();

	$enable_deposit = get_post_meta( $product_id, 'ovabrw_enable_deposit', true );
?>
<?php if ( $enable_deposit === 'yes' ):
	$pay_full 	= get_post_meta( $product_id, 'ovabrw_force_deposit', true );
	$type 		= get_post_meta( $product_id, 'ovabrw_type_deposit', true );
	$value 		= get_post_meta( $product_id, 'ovabrw_amount_deposit', true );

	if ( $type === 'value' ) $value = ovabrw_wc_price( $value );
	if ( $type === 'percent' ) $value .= '%';
?>
	<div class="ovabrw-modern-deposit">
		<?php if ( $pay_full === 'yes' ): ?>
			<div class="deposit-label pay-full active">
				<?php esc_html_e( 'Pay 100%', 'ova-brw' ); ?>
			</div>
			<div class="deposit-label pay-deposit">
				<?php printf( esc_html__( 'Deposit Option %s Per item', 'ova-brw' ), $value ); ?>
			</div>
		<?php else: ?>
			<div class="deposit-label pay-deposit active">
				<?php printf( esc_html__( 'Deposit Option %s Per item', 'ova-brw' ), $value ); ?>
			</div>
		<?php endif; ?>
		<div class="deposit-type">
			<?php if ( $pay_full === 'yes' ): ?>
				<label class="ovabrw-label-field">
					<input 
						type="radio"
						name="ova_type_deposit"
						class="pay-full" 
						value="full"
						checked="checked"
					/>
					<span class="checkmark"><?php esc_html_e( 'Full Payment', 'ova-brw' ); ?></span>
				</label>
				<label class="ovabrw-label-field">
					<input 
						type="radio"
						name="ova_type_deposit"
						class="pay-deposit" 
						value="deposit"
					/>
					<span class="checkmark"><?php esc_html_e( 'Pay Deposit', 'ova-brw' ); ?></span>
				</label>
			<?php else: ?>
				<label class="ovabrw-label-field">
					<input 
						type="radio"
						name="ova_type_deposit"
						class="pay-deposit" 
						value="deposit"
						checked="checked"
					/>
					<span class="checkmark"><?php esc_html_e( 'Pay Deposit', 'ova-brw' ); ?></span>
				</label>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>