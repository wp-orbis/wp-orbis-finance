<?php

/**
 * Add person meta boxes
 */
function orbis_company_finance_add_meta_boxes() {
	add_meta_box(
		'orbis_company_finance',
		__( 'Company Finance', 'orbis' ),
		'orbis_company_finance_meta_box',
		'orbis_company' ,
		'normal' ,
		'high'
	);
}

add_action( 'add_meta_boxes', 'orbis_company_finance_add_meta_boxes' );

/**
 * Company finance meta box
 *
 * @param array $post
 */
function orbis_company_finance_meta_box( $post ) {
	global $orbis_finance_plugin;

	$orbis_finance_plugin->plugin_include( 'admin/meta-box-company-finance.php' );
}

/**
 * Save company finance
 */
function orbis_save_company_finance( $post_id, $post ) {
	// Doing autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) {
		return;
	}

	// Verify nonce
	$nonce = filter_input( INPUT_POST, 'orbis_company_finance_meta_box_nonce', FILTER_SANITIZE_STRING );
	if( ! wp_verify_nonce( $nonce, 'orbis_save_company_finance' ) ) {
		return;
	}

	// Check permissions
	if ( ! ( $post->post_type == 'orbis_company' && current_user_can( 'edit_post', $post_id ) ) ) {
		return;
	}

	// OK
	$definition = array(
		'_orbis_company_ebilling' => FILTER_VALIDATE_BOOLEAN,
	);

	$data = filter_input_array(INPUT_POST, $definition);

	foreach ( $data as $key => $value ) {
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key);
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}

add_action( 'save_post', 'orbis_save_company_finance', 10, 2 );
