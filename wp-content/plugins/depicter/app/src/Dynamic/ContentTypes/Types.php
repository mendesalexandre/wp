<?php

namespace Depicter\Dynamic\ContentTypes;


class Types
{
	public function index()
	{
		$args = array(
			'public'   => true,
			'_builtin' => true
		);
		   
		$output = [];
		$operator = 'and'; // 'and' or 'or' (default: 'and')
		
		$post_types = get_post_types( $args, 'objects', $operator );
		unset( $post_types['attachment'] );
		$output['items'][] = $this->get_post_types_info( $post_types );

		$args['_builtin'] = false;
		$post_types = get_post_types( $args, 'objects', $operator );
		unset( $post_types['elementor_library'] );
		$output['items'][] = $this->get_post_types_info( $post_types );

		return [
			'success' => true,
			'message' => 'List of post types.',
			'code'    => 200,
			'data'    =>  $output
		];
	}

	/**
	 * Get Post types required info
	 *
	 * @param array $post_types
	 * @return array $items
	 */
	public function get_post_types_info( $post_types ) {
		$items = [];
		foreach ( $post_types as $slug => $post_type ) {
			$items[ $slug ] = [
				'slug' 	=> $slug,
				'label'	=> $post_type->label
			];

			$taxes = get_object_taxonomies( $slug, 'objects' );
			foreach ( $taxes as $tax_slug => $tax ) {
				$items[ $slug ]['taxonomies'][ $tax_slug ] = [
					'slug'	=> $tax_slug,
					'label'	=> $tax->label
				];

				$terms = get_terms( [ 'taxonomy' => $tax_slug ] );
				foreach ( $terms as $key => $term ) {
					$items[ $slug ]['taxonomies'][ $tax_slug ]['terms'][] = [
						'slug' 	=> $term->slug,
						'lable'	=> $term->name
					];
				}
			}
		}

		return $items;
	}
}
