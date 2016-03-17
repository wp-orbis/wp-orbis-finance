<?php

/**
 * Add project meta boxes
 */
function orbis_project_finance_add_meta_boxes() {
	add_meta_box(
		'orbis_project_finance',
		__( 'Project Finance', 'orbis_finance' ),
		'orbis_project_finance_meta_box',
		'orbis_project',
		'normal',
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

	if ( ! wp_verify_nonce( $nonce, 'orbis_save_project_finance' ) ) {
		return;
	}

	// Check permissions
	if ( ! ( $post->post_type == 'orbis_project' && current_user_can( 'edit_post', $post_id ) ) ) {
		return;
	}

	// OK
	$definition = array();

	$definition['_orbis_project_is_invoicable']  = FILTER_VALIDATE_BOOLEAN;
	$definition['_orbis_project_invoice_number'] = FILTER_SANITIZE_STRING;

	if ( current_user_can( 'edit_orbis_project_administration' ) ) {
		$definition['_orbis_project_is_invoiced'] = FILTER_VALIDATE_BOOLEAN;
	}

	$data = filter_input_array( INPUT_POST, $definition );
	
	// Invoice number
	$invoice_number_old = get_post_meta( $post_id, '_orbis_project_invoice_number', true );
	$invoice_number_new = $data['_orbis_project_invoice_number'] ;

	// Data
	foreach ( $data as $key => $value ) {
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key);
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	// Action
	if ( $post->post_status == 'publish' && $invoice_number_old != $invoice_number_new ) {
		// @see https://github.com/woothemes/woocommerce/blob/v2.1.4/includes/class-wc-order.php#L1274
		do_action( 'orbis_project_invoice_number_update', $post_id, $invoice_number_old, $invoice_number_new );
	}
}

add_action( 'save_post', 'orbis_save_project_finance', 10, 2 );

/**
 * Project finished update
 *
 * @param int $post_id
 */
function orbis_project_invoice_number_update( $post_id, $invoice_number_old, $invoice_number_new ) {
	// Date
	update_post_meta( $post_id, '_orbis_project_invoice_number_modified', time() );

	// Comment
	$user = wp_get_current_user();

	$text = $invoice_number_new;
	
	$invoice_link = orbis_get_invoice_link( $invoice_number_new );
	if ( ! empty( $invoice_link ) ) {
		$text = sprintf(
			'<a href="%s">%s</a>',
			esc_attr( $invoice_link ),
			$invoice_number_new
		);
	}
	
	$comment_content = sprintf(
		__( "Invoice Number '%s' was registered on this project by %s.", 'orbis_finance' ),
		$text,
		$user->display_name
	);

	$data = array(
		'comment_post_ID' => $post_id,
		'comment_content' => $comment_content,
		'comment_author'  => 'Orbis',
		'comment_type'    => 'orbis_comment',
	);

	$comment_id = wp_insert_comment( $data );
}

add_action( 'orbis_project_invoice_number_update', 'orbis_project_invoice_number_update', 10, 3 );

/**
 * Pre get posts
 * @param WP_Query $query
 */
function orbis_finance_pre_get_posts( $query ) {
	$orderby = $query->get( 'orderby' );
	
	if ( 'project_invoice_number_modified' == $orderby ) {
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'meta_key', '_orbis_project_invoice_number_modified' );
	}

	if ( 'project_invoice_number' == $orderby ) {
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'meta_key', '_orbis_project_invoice_number' );
	}

	$invoicable = $query->get( 'orbis_invoicable', null );

	if ( null !== $invoicable ) {
		$invoicable = filter_var( $invoicable, FILTER_VALIDATE_BOOLEAN );

		$meta_query = array();

		if ( $invoicable ) {
			$meta_query[] = array(
				'key'     => '_orbis_project_is_invoicable',
				'value'   => '1',
				'compare' => '=',
			);
		} else {
			$meta_query['relation'] = 'OR';

			$meta_query[] = array(
				'key'     => '_orbis_project_is_invoicable',
				'value'   => '1',
				'compare' => '!=',
			);

			$meta_query[] = array(
				'key'     => '_orbis_project_is_invoicable',
				'compare' => 'NOT EXISTS',
			);
		}

		$query->set( 'meta_query', $meta_query );
	}
}

add_action( 'pre_get_posts', 'orbis_finance_pre_get_posts' );

function orbis_finance_query_vars( $query_vars ) {
	$query_vars[] = 'orbis_invoicable';

	return $query_vars;
}

add_filter( 'query_vars', 'orbis_finance_query_vars' );
