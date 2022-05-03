<?php
namespace Depicter\WordPress;

use Averta\WordPress\Utility\Sanitize;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Register shortcodes.
 */
class ShortcodesServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		// Nothing to register.
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		add_shortcode('depicter'   , [ $this, 'process_shortcode' ]);
	}

	/**
	 * Shortcode callback
	 *
	 * @param array  $attrs
	 * @param null   $content
	 *
	 * @return string
	 * @throws \Exception
	 */
	function process_shortcode( $attrs, $content = null ) {

		extract( shortcode_atts([
			'id'    => '',
			'alias' => '',
			'slug'  => ''
		],
			$attrs,
			'depicter'
		));

		$documentId = $id;
		$result = __( 'Slider not found.', PLUGIN_DOMAIN );


		if ( empty( $documentId ) ) {

			if( empty( $slug ) && ! empty( $alias ) ){
				$slug = $alias;
			}

			if( ! $document = \Depicter::document()->repository()->findOne( null, ['slug' => $slug] ) ){
				return esc_html( $result );
			}
			$documentId = $document->getID();
		}

		try{
			$result = \Depicter::front()->render()->document( $documentId, ['useCache' => true] );
		} catch( \Exception $e ){}

		return Sanitize::html( $result, null, 'depicter/output' );
	}
}
