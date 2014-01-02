<?php

global $post;

$ebilling   = get_post_meta( $post->ID, '_orbis_company_ebilling', true );

wp_nonce_field( 'orbis_save_company_finance', 'orbis_company_finance_meta_box_nonce' );

?>
<table class="form-table">
	<tbody>
		<tr>
			<th scope="row">
				<label for="orbis_company_ebilling"><?php _e( 'Electronic billing', 'orbis_finance' ); ?></label>
			</th>
			<td>
				<label for="orbis_company_ebilling">
					<input id="orbis_company_ebilling" name="_orbis_company_ebilling" value="1" type="checkbox" <?php checked( $ebilling ); ?> />
					<?php _e( 'Send bills electronically', 'orbis_finance' ); ?>
				</label>
			</td>
		</tr>
	</tbody>
</table>