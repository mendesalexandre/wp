<?php
namespace Depicter\Controllers\Ajax;

use Averta\WordPress\Utility\JSON;
use Averta\WordPress\Utility\Sanitize;
use Depicter\GuzzleHttp\Exception\ConnectException;
use Depicter\GuzzleHttp\Exception\GuzzleException;
use Depicter\Services\AssetsAPIService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


class CuratedAPIAjaxController
{

	/**
	 * search Elements
	 *
	 * @param RequestInterface $request
	 * @param string           $view
	 *
	 * @return ResponseInterface
	 * @throws GuzzleException
	 */
	public function searchElements( RequestInterface $request, $view )
	{
		$page     = !empty( $request->query('page'    ) ) ? Sanitize::int( $request->query('page') ) : 1;
		$perpage  = !empty( $request->query('perpage' ) ) ? Sanitize::int( $request->query('perpage') ) : 20;
		$category = !empty( $request->query('category') ) ? Sanitize::textfield( $request->query('category') ) : '';
		$search   = !empty( $request->query('s'  ) ) ? Sanitize::textfield( $request->query('s') ) : '';

		$options = [
			'page'      => $page,
			'perpage'   => $perpage,
			'category'  => $category,
			's'         => $search
		];

		try {
			return \Depicter::json( AssetsAPIService::searchElements( $options ) );
		} catch ( ConnectException $exception ) {
			return \Depicter::json([
				'errors' => [ 'Connection error ..', $exception->getMessage() ]
			])->withStatus(503);
		} catch ( \Exception  $exception ) {

			return \Depicter::json([
				'errors' => [ $exception->getMessage() ]
			]);
		}

	}

	/**
	 * search Document Templates
	 *
	 * @param RequestInterface $request
	 * @param string           $view
	 *
	 * @return ResponseInterface
	 * @throws GuzzleException
	 */
	public function searchDocumentTemplates( RequestInterface $request, $view )
	{
		$page     = !empty( $request->query('page') ) ? Sanitize::int( $request->query('page') ) : 1;
		$perpage  = !empty( $request->query('perpage') ) ? Sanitize::int( $request->query('perpage') ) : 20;
		$category = !empty( $request->query('category') ) ? Sanitize::textfield( $request->query('category') ) : '';
		$search   = !empty( $request->query('s') ) ? Sanitize::textfield( $request->query('s') ) : '';

		$options = [
			'page'      => $page,
			'perpage'   => $perpage,
			'category'  => $category,
			's'         => $search
		];

		try {
			return \Depicter::json( AssetsAPIService::searchDocumentTemplates( $options ) );
		} catch ( ConnectException $exception ) {
			return \Depicter::json([
				'errors' => [ 'Connection error ..', $exception->getMessage() ]
			])->withStatus(503);
		} catch ( \Exception  $exception ) {

			return \Depicter::json([
				'errors' => [ $exception->getMessage() ]
			]);
		}

	}

	public function getDocumentTemplateCategories( RequestInterface $request, $view )
	{
		$category = !empty( $request->query('category') ) ? Sanitize::textfield( $request->query('category') ) : '';

		$options = [
			'category' => $category
		];

		try {
			return \Depicter::json( AssetsAPIService::getDocumentTemplateCategories( $options ) );
		} catch ( ConnectException $exception ) {
			return \Depicter::json([
				'errors' => [ 'Connection error ..', $exception->getMessage() ]
			])->withStatus(503);
		} catch ( \Exception  $exception ) {

			return \Depicter::json([
				'errors' => [ $exception->getMessage() ]
			]);
		}
	}

	/**
	 * @param  RequestInterface  $request
	 * @param $view
	 *
	 * @return ResponseInterface
	 * @throws GuzzleException
	 */
	public function importDocumentTemplate( RequestInterface  $request, $view ) {
		$templateID = !empty( $request->query('ID') ) ? Sanitize::textfield( $request->query('ID') ) : '';
		try {
			$result = AssetsAPIService::getDocumentTemplateData( $templateID );
			if ( !empty( $result->hits ) ) {
				$editorData = JSON::encode( $result->hits );
				$document = \Depicter::documentRepository()->create();

				$updateData = ['content' => $editorData ];
				if ( !empty( $result->title ) ) {
					$updateData['name'] = $result->title . ' ' . $document->getID();
				}
				if ( !empty( $result->image ) ) {
					$previewImage = file_get_contents( $result->image );
					\Depicter::storage()->filesystem()->write( \Depicter::documentRepository()->getPreviewImagePath( $document->getID() ) , $previewImage );
				}

				\Depicter::documentRepository()->update( $document->getID(), $updateData );
				\Depicter::media()->importDocumentAssets( $editorData );

				return \Depicter::json([
					'hits' => [
						'documentID' => $document->getID()
					]
				]);

			} else {
				// Return the error message received from server
				return \Depicter::json( $result );
			}

		} catch( ConnectException $exception ){
			return \Depicter::json([
				'errors' => [
					'Cannot reach the server for downloading images.',
					$exception->getMessage()
				]
			])->withStatus(421);
		} catch ( \Exception  $exception ) {

			return \Depicter::json([
				'errors' => [ $exception->getMessage() ]
			]);
		}
	}

	/**
	 * @param RequestInterface  $request
	 * @param $view
	 *
	 * @return ResponseInterface
	 * @throws GuzzleException
	 */
	public function previewDocumentTemplate( RequestInterface  $request, $view ) {
		$templateID = !empty( $request->query('ID') ) ? Sanitize::textfield( $request->query('ID') ) : '';
		try {
			$result = AssetsAPIService::previewDocumentTemplate( $templateID );
			return \Depicter::output( $result );
		} catch ( \Exception  $exception ) {

			return \Depicter::json([
				'errors' => [ $exception->getMessage() ]
			]);
		}
	}

	/**
	 * search Animations
	 *
	 * @param RequestInterface $request
	 * @param string           $view
	 *
	 * @return ResponseInterface
	 * @throws GuzzleException
	 */
	public function searchAnimations( RequestInterface $request, $view )
	{

		$page = !empty( $request->query('page') ) ? Sanitize::int( $request->query('page') ) : 1;
		$perpage = !empty( $request->query('perpage') ) ? Sanitize::int( $request->query('perpage') ) : 20;
		$phase = !empty( $request->query('phase') ) ? Sanitize::textfield( $request->query('phase') ) : '';
		$search   = !empty( $request->query('s'  ) ) ? Sanitize::textfield( $request->query('s') ) : '';
		$category   = !empty( $request->query('category'  ) ) ? Sanitize::textfield( $request->query('category') ) : '';

		$options = [
			'page'     => $page,
			'perpage'  => $perpage,
			'phase'    => $phase,
			's'        => $search,
			'category' => $category
		];


		try {
			return \Depicter::json( AssetsAPIService::searchAnimations( $options ) );
		} catch ( ConnectException $exception ) {
			return \Depicter::json([
				'errors' => [ 'Connection error ..', $exception->getMessage() ]
			])->withStatus(503);
		} catch ( \Exception  $exception ) {

			return \Depicter::json([
				'errors' => [ $exception->getMessage() ]
			]);
		}

	}

	/**
	 * Get list of animation categories
	 *
	 * @param RequestInterface $request
	 * @param                  $view
	 *
	 * @return ResponseInterface
	 * @throws GuzzleException
	 */
	public function getAnimationsCategories( RequestInterface $request, $view )
	{

		$phase = !empty( $request->query('phase') ) ? Sanitize::textfield( $request->query('phase') ) : '';

		$options = [
			'phase'      => $phase
		];

		try {
			return \Depicter::json( AssetsAPIService::getAnimationsCategories( $options ) );
		} catch ( ConnectException $exception ) {
			return \Depicter::json([
				'errors' => [ 'Connection error ..', $exception->getMessage() ]
			])->withStatus(503);
		} catch ( \Exception  $exception ) {

			return \Depicter::json([
				'errors' => [ $exception->getMessage() ]
			]);
		}
	}

}
