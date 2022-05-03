<?php
namespace Depicter\Front;


use Averta\Core\Utility\Arr;
use Averta\WordPress\Models\WPOptions;
use Depicter\Exception\DocumentNotPublished;
use Exception;

class Render
{
	/**
	 * @var WPOptions
	 */
	private $cache;

	public function __construct(){
		$this->cache = \Depicter::cache('document');
	}

	/**
	 * Renders a document markup
	 *
	 * @param       $documentId
	 * @param array $args
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function document( $documentId, $args = [] )
	{
		$defaults = [
			'loadStyleMode' => 'auto',// ["auto", "inline", "file"]."auto" loads custom css if available, otherwise prints styles inline
			'useCache'      => false,
			'status'        => 'publish'
		];

		$args = Arr::merge( $args, $defaults );

		$output = '';
		$where  = [ 'status' => $args['status'] ];

		if( $args['useCache'] && ( false !== $cacheOutput = $this->getDocumentCache( $documentId ) ) ){
			return $cacheOutput;
		}

		try{
			if( \Depicter::document()->repository()->isNotPublished( $documentId, $where ) ){
				throw new DocumentNotPublished( __( 'Slider is not published yet and saved as "draft"', PLUGIN_DOMAIN ), 0, $where );
			}

			if( $documentModel = \Depicter::document()->getModel( $documentId, $where ) ){

				$output = $documentModel->prepare()->render();

				if( ( $args['loadStyleMode'] == 'inline' ) ){
					$output = $documentModel->getInlineCssTag() . $output;

				} elseif( ( $args['loadStyleMode'] == 'auto' ) ) {
					// fallback to inline if css file cannot be generated
					if( ! $documentModel->getCssFileUrl() ){
						$output = $documentModel->getInlineCssTag() . $output;
					}
				}

				//----------

				$cssLinksToEnqueue = $documentModel->getCustomCssFiles( [ 'google-font' ] );

				if( ( $args['loadStyleMode'] == 'auto' ) ) {
					if( $documentModel->getCssFileUrl() ){
						$cssLinksToEnqueue = $documentModel->getCustomCssFiles( 'all' );
					}

				} elseif( $args['loadStyleMode'] == 'file' ) {
					$cssLinksToEnqueue = $documentModel->getCustomCssFiles( 'all' );
				}

				if( $args['useCache'] ){
					$this->cache->set( $documentId . '_css_files', $cssLinksToEnqueue, WEEK_IN_SECONDS );
				}

				$this->enqueueCustomStyles( $cssLinksToEnqueue );
			}
		} catch( \Exception $exception ){
			$output = $exception->getMessage();
		}

		if( $args['useCache'] ){
			$this->cache->set( $documentId, $output, WEEK_IN_SECONDS );
		}

		return $output;
	}


	/**
	 * Retrieves the cached markup and enqueues custom styles
	 *
	 * @param $documentId
	 *
	 * @return bool|mixed
	 */
	protected function getDocumentCache( $documentId ){
		if( false !== $cacheOutput = $this->cache->get( $documentId ) && !empty( $cacheOutput ) ){
			if( false !== $cssLinksToEnqueue = $this->cache->get( $documentId . '_css_files' ) ){
				$this->enqueueCustomStyles( $cssLinksToEnqueue );
			}
			return $cacheOutput;
		}

		return false;
	}

	/**
	 * Enqueues css links in footer
	 *
	 * @param $cssLinksToEnqueue
	 */
	protected function enqueueCustomStyles( $cssLinksToEnqueue ){
		if( $cssLinksToEnqueue && is_array( $cssLinksToEnqueue ) ){
			add_action( 'wp_footer', function() use ( $cssLinksToEnqueue ){
				foreach( $cssLinksToEnqueue as $cssId => $cssLink ){
					\Depicter::core()->assets()->enqueueStyle( $cssId, $cssLink );
				}
			} );
		}
	}

}
