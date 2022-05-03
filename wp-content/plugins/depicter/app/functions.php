<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function depicter( $documentID  = null ) {

	$result = __( 'Slider not found.', PLUGIN_DOMAIN );
	if ( empty( $documentID ) ) {
        echo esc_html( $result );
    }

    if ( is_string( $documentID ) ) {
        if ( ! $document = \Depicter::document()->repository()->findOne( null, ['slug' => $documentID] ) ) {
            echo esc_html( $result );
        }
        $documentID = $document->getID();
    }

    try{
        $result = \Depicter::front()->render()->document( $documentID, ['useCache' => true] );
    } catch( \Exception $e ){}

	echo \Averta\WordPress\Utility\Sanitize::html( $result, null, 'depicter/output' );
}
