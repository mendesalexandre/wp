<?php
namespace Depicter\Controllers\Ajax;

use Averta\WordPress\Utility\Sanitize;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Requests\RequestInterface;

class PostsAjaxController
{
	/**
	 * list available post types with their taxonomies
	 * @param RequestInterface $request
	 * @param                  $view
	 *
	 * @return ResponseInterface
	 */
    public function getPostTypes( RequestInterface $request, $view ) {
    	$result = \Depicter::postsService()->getPostTypes();

		return \Depicter::json([
			'hits' => $result
		])->withStatus(200);
    }

	/**
	 * List available posts for custom post type
	 * @param RequestInterface $request
	 * @param                  $view
	 *
	 * @return ResponseInterface
	 */
    public function getPosts( RequestInterface $request, $view ) {
		$postType = !empty( $request->query('type') ) ? Sanitize::textfield( $request->query('type') ) : 'post';
		$posts = \Depicter::postsService()->getPosts( $postType );

	    return \Depicter::json([
	    	'hits' => $posts
	    ])->withStatus(200);
    }
}
