<?php
/**
 * Declare any actions and filters here.
 * In most cases you should use a service provider, but in cases where you
 * just need to add an action/filter and forget about it you can add it here.
 *
 * @package Depicter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:ignore
// add_action( 'some_action', 'some_function' );
function depicter_add_thumbnail_size() {
	add_image_size( 'depicter-thumbnail', 200, 9999, false );
}
add_action( 'after_setup_theme', 'depicter_add_thumbnail_size' );


function depicter_purge_document_cache( $documentID, $properties ) {
	if ( $properties['status'] === 'publish' ) {
		\Depicter::cache('document')->delete( $documentID );
	}
}
add_action( 'depicter/editor/after/store', 'depicter_purge_document_cache', 10, 2 );


function depicter_sanitize_html_tags( $allowed_tags ) {
	$allowed_tags['style'] = [
		'type'  => true
	];
	$allowed_tags['script'] = [
		'id'    => true,
		'src'   => true
	];
	$allowed_tags['link'] = [
		'rel'   => true,
		'id'    => true,
		'href'  => true,
		'media' => true,
	];
	return $allowed_tags;
}
add_filter( 'averta/wordpress/sanitize/html/tags/default', 'depicter_sanitize_html_tags' );


/**
 * Depicter sanitize html tags for depicter slider output
 *
 * @param array $allowed_tags
 * @return void
 */
function depicter_sanitize_html_tags_for_output( $allowed_tags ) {
	return array_merge( $allowed_tags, wp_kses_allowed_html( 'post' ) );
}
add_filter( 'averta/wordpress/sanitize/html/tags/depicter/output', 'depicter_sanitize_html_tags_for_output' );


function depicter_disable_nocache_headers( $headers ) {
	unset( $headers['Expires'] );
	unset( $headers['Cache-Control'] );
	return $headers;
}
