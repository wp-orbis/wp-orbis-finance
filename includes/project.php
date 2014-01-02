<?php

/**
 * Add project meta boxes
 */
function orbis_project_finance_add_meta_boxes() {
	add_meta_box(
		'orbis_project_finance',
		__( 'Project Finance', 'orbis_finance' ),
		'orbis_project_finance_meta_box',
		'orbis_project' ,
		'normal' ,
		'high'
	);
}

add_action( 'add_meta_boxes', 'orbis_project_finance_add_meta_boxes' );

/**
 * Project finance meta box
 *
 * @param array $post
*/
function orbis_project_finance_meta_box( $post ) {
	global $orbis_finance_plugin;

	$orbis_finance_plugin->plugin_include( 'admin/meta-box-project-finance.php' );
}

/**
 * Save project details
 */
function orbis_save_project_finance( $post_id, $post ) {
	// Doing autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) {
		return;
	}

	// Verify nonce
	$nonce = filter_input( INPUT_POST, 'orbis_project_finance_meta_box_nonce', FILTER_SANITIZE_STRING );
	if( ! wp_verify_nonce( $nonce, 'orbis_save_project_finance' ) ) {
		return;
	}

	// Check permissions
	if ( ! ( $post->post_type == 'orbis_project' && current_user_can( 'edit_post', $post_id ) ) ) {
		return;
	}

	// OK
	$definition = array();

	$definition['_orbis_project_is_invoicable']   = FILTER_VALIDATE_BOOLEAN;

	if ( current_user_can( 'edit_orbis_project_administration' ) ) {
		$definition['_orbis_project_is_invoiced']    = FILTER_VALIDATE_BOOLEAN;
		$definition['_orbis_project_invoice_number'] = FILTER_SANITIZE_STRING;
	}

	$data = filter_input_array( INPUT_POST, $definition );

	foreach ( $data as $key => $value ) {
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key);
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}

add_action( 'save_post', 'orbis_save_project_finance', 10, 2 );
