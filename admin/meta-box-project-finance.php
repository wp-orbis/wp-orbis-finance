<?php

global $wpdb, $post;

wp_nonce_field( 'orbis_save_project_finance', 'orbis_project_finance_meta_box_nonce' );

$orbis_id       = get_post_meta( $post->ID, '_orbis_project_id', true );
$principal_id   = get_post_meta( $post->ID, '_orbis_project_principal_id', true );
$is_invoicable  = filter_var( get_post_meta( $post->ID, '_orbis_project_is_invoicable', true ), FILTER_VALIDATE_BOOLEAN );
$is_invoiced    = filter_var( get_post_meta( $post->ID, '_orbis_project_is_invoiced', true ), FILTER_VALIDATE_BOOLEAN );
$invoice_number = get_post_meta( $post->ID, '_orbis_project_invoice_number', true );
$seconds        = get_post_meta( $post->ID, '_orbis_project_seconds_available', true );
$agreement_id   = get_post_meta( $post->ID, '_orbis_project_agreement_id', true );

if ( true ) {
	$project = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->orbis_projects WHERE post_id = %d;", $post->ID ) );

	if ( $project ) {
		$orbis_id	    = $project->id;
		$principal_id   = $project->principal_id;
		$is_invoicable  = $project->invoicable;
		$is_invoiced    = $project->invoiced;
		$invoice_number = $project->invoice_number;
		$seconds        = $project->number_seconds;
	}
}

?>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="_orbis_project_is_invoicable">
					<?php _e( 'Invoicable', 'orbis_finance' ); ?>
				</label>
			</th>
			<td>
				<label for="_orbis_project_is_invoicable">
					<input type="checkbox" value="yes" id="_orbis_project_is_invoicable" name="_orbis_project_is_invoicable" <?php checked( $is_invoicable ); ?> />
					<?php _e( 'Project is invoicable', 'orbis_finance' ); ?>
				</label>
			</td>
		</tr>

		<?php if ( current_user_can( 'edit_orbis_project_administration' ) ) : ?>

			<tr valign="top">
				<th scope="row">
					<label for="_orbis_project_is_invoiced">
						<?php _e( 'Invoiced', 'orbis_finance' ); ?>
					</label>
				</th>
				<td>
					<label for="_orbis_project_is_invoiced">
						<input type="checkbox" value="yes" id="_orbis_project_is_invoiced" name="_orbis_project_is_invoiced" <?php checked( $is_invoiced ); ?> />
						<?php _e( 'Project is invoiced', 'orbis_finance' ); ?>
					</label>
				</td>
			</tr>

		<?php endif; ?>

		<tr valign="top">
			<th scope="row">
				<label for="orbis_project_invoice_number">
					<?php _e( 'Invoice Number', 'orbis_finance' ); ?>
				</label>
			</th>
			<td>
				<input type="text" id="orbis_project_invoice_number" name="_orbis_project_invoice_number" value="<?php echo esc_attr( $invoice_number ); ?>" />
			</td>
		</tr>
	</tbody>
</table>
