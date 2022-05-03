<?php
namespace Depicter\WordPress;

class PostsService
{

	/**
	 * Get All Post Types info
	 * @return array
	 */
    public function getPostTypes() {
        $availablePostType = array(
            'post' => get_post_type_object('post'),
            'page' => get_post_type_object('page')
        );
        $postTypes = get_post_types(
            array(
                'public'    => true,
                '_builtin'  => false
            ),
	        'objects'
        );

        $postTypes = array_merge( $availablePostType, $postTypes );
        $result = [];
        foreach( $postTypes as $id => $postType ) {
	        $postTypeInfo = [
		        'id' => $id,
		        'label' => $postType->label
	        ];

	        $taxonomies = get_object_taxonomies( $id );
	        foreach( $taxonomies as $taxonomy ) {
	        	$terms = get_terms([
	        		'taxonomy' => $taxonomy,
			        'hide_empty' => true
		        ]);
	        	$postTypeInfo[ $taxonomy ] = [];
	        	if ( !empty( $terms ) ) {
	        		foreach( $terms as $term ) {
	        			$postTypeInfo[ $taxonomy ][] = [
	        				'id' => $term->term_id,
					        'slug' => $term->slug,
					        'label' => $term->name
				        ];
			        }
		        }
	        }

	        $result[] = $postTypeInfo;
        }

        return $result;
    }

	/**
	 * Lists available posts for provided post type
	 *
	 * @param string $postType
	 *
	 * @return array
	 */
    public function getPosts( $postType = 'post' ) {
		$availablePosts = get_posts([
			'post_type' => $postType,
			'per_page'  => -1
		]);

		$posts = [];
		if ( !empty( $availablePosts ) ) {
			$taxonomies = get_object_taxonomies( $postType );
			foreach( $availablePosts as $post ) {
				$postInfo = [
					'id' => $post->ID,
					'title' => $post->post_title,
					'url' => get_permalink( $post->ID ),
					'featured_image' => has_post_thumbnail( $post->ID ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) )[0] : '',
					'date' => $post->post_date,
					'excerpt' => $post->post_excerpt,
					'author' => [
						'name' => get_the_author_meta( 'display_name', $post->post_author ),
						'page' => get_author_posts_url( $post->post_author ),
					],
					'content' => $post->post_content
				];

				if ( !empty( $taxonomies ) ) {
					foreach( $taxonomies as $taxonomy ) {
						$postInfo[ $taxonomy ] = [];
						$terms = wp_get_post_terms( $post->ID, $taxonomy );
						if ( !empty( $terms ) ) {
							foreach( $terms as $term ) {
								$postInfo[ $taxonomy ][] = [
									'link' => get_term_link( $term->term_id ),
									'label' => $term->name
								];
							}
						}
					}
				}
				$posts[] = $postInfo;
			}
		}

		return $posts;
    }
}
