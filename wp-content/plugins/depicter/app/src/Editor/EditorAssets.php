<?php
namespace Depicter\Editor;

use Averta\Core\Utility\Data;
use Averta\WordPress\Utility\Escape;
use Averta\WordPress\Utility\JSON;
use Averta\WordPress\Utility\Sanitize;
use Depicter\Security\CSRF;


class EditorAssets
{
	public function bootstrap(){
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueueAdminAssets' ] );
	}

	public function enqueueAdminAssets(){

		// This will enqueue the Media Uploader script
		wp_enqueue_media();

		\Depicter::core()->assets()->enqueueScript(
			'depicter-admin',
			\Depicter::core()->assets()->getUrl() . '/resources/scripts/admin/index.js',
			['jquery'],
			true
		);

		wp_enqueue_style('common');

		// Enqueue scripts.
		\Depicter::core()->assets()->enqueueScript(
			'depicter-editor-vendors',
			\Depicter::core()->assets()->getUrl() . '/resources/scripts/editor/vendors-main.js',
			[],
			true
		);
		\Depicter::core()->assets()->enqueueScript(
			'depicter-editor-js',
			\Depicter::core()->assets()->getUrl() . '/resources/scripts/editor/depicter-editor.js',
			['depicter-editor-vendors'],
			true
		);

		global $wp_version;
		$currentUser = wp_get_current_user();

		// Add Environment variables
		wp_add_inline_script( 'depicter-editor-vendors', 'window.depicterEditorEnv = ' . JSON::encode([
			"assetsAPI"   => Escape::url('https://api.wp.depicter.com/' ),
			"useMockData" => false,
			"wpRestAPI"   => Escape::url( get_rest_url() ),
			"dashboardURL"=> Escape::url( menu_page_url('depicter-dashboard', false) ),
    		"scriptsPath" => \Depicter::core()->assets()->getUrl(). '/resources/scripts/editor/',
			"documentId"  => Data::cast( Sanitize::key( $_GET['document'] ), 'int' ),
			'editorAPI'   => admin_url( 'admin-ajax.php' ),
			'clientKey'   => \Depicter::options()->get( 'client_key', 'anon' ),
			'csrfToken'   => \Depicter::csrf()->getToken( CSRF::EDITOR_ACTION ),
			'wpVersion'   => $wp_version,
			'user' => [
				'subscriptionPlan' => 'content-creator',
				'name'  => Escape::html( $currentUser->display_name ),
				'email' => Escape::html( $currentUser->user_email   ),
				'hasSubscribed' => !! \Depicter::options()->get('has_subscribed')
			]
		]), 'before' );

		wp_add_inline_script( 'depicter-editor-vendors', 'window.depicterKitEnv = ' . JSON::encode([
    		"scriptsPath" => \Depicter::core()->assets()->getUrl(). '/resources/scripts/editor/',
			'editorAPI'   => admin_url( 'admin-ajax.php' ),
			'clientKey'   => \Depicter::options()->get( 'client_key', 'anon' ),
			'csrfToken'   => \Depicter::csrf()->getToken( CSRF::EDITOR_ACTION )
		]), 'before' );

		// Enqueue styles.
		\Depicter::core()->assets()->enqueueStyle(
			'depicter-editor-css',
			\Depicter::core()->assets()->getUrl() . '/resources/styles/editor/editor.css'
		);
	}
}
