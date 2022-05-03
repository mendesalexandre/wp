<?php

namespace Depicter\Dynamic\ContentTypes;

use Averta\Core\Utility\Trim;

class Post
{
	public function index()
	{

		$args = [
			'post_type'			=> 'post',
			'posts_per_page'	=> -1
		];

		if ( !empty( $_GET['postType'] ) ) {
			$args['post_type'] = sanitize_text_field( $_GET['postType'] );
		}

		if ( !empty( $_GET['count'] ) ) {
			$args['posts_per_page'] = absint( sanitize_text_field( $_GET['count'] ) );
		}

		if ( !empty( $_GET['offset'] ) ) {
			$args['offset'] = sanitize_text_field( $_GET['offset'] );
		}

		if ( !empty( $_GET['orderby'] ) ) {
			$args['orderby'] = sanitize_text_field( $_GET['orderby'] );
		}

		if ( !empty( $_GET['order'] ) ) {
			$args['order'] = sanitize_text_field( $_GET['order'] );
		}

		if ( !empty( $_GET['excludedPosts'] ) ) {
			$excludedPosts = explode( ',', sanitize_text_field( $_GET['excludedPosts'] ) );
			$args['post__not_in '] = $excludedPosts;
		}

		if ( !empty( $_GET['includedPosts'] ) ) {
			$includedPosts = explode( ',', sanitize_text_field( $_GET['includedPosts'] ) );
			$args['post__in '] = $includedPosts;
		}

		if ( !empty( $_GET['taxonomy'] ) && !empty( $_GET['terms'] ) ) {
			$args['tax_query'] = [
				[
					'taxonomy'	=> sanitize_text_field( $_GET['taxonomy'] ),
					'field'		=> 'slug',
					'terms'		=> sanitize_text_field( $_GET['terms'] )
				]
			];
		}

		$posts = get_posts( $args );
		$output = [];
		foreach( $posts as $post ) {

			if ( isset( $_GET['excludeNonMedia'] ) && !get_the_post_thumbnail_url( $post->ID ) ) {
				continue;
			}

			$item = [
				'id'			=> $post->ID,
				'title'			=> $post->post_title,
				'featuredImage'	=> get_the_post_thumbnail_url( $post->ID ),
				'url'			=> $post->guid,
				'date'			=> $post->post_date,
				'excerpt'		=> $post->post_excerpt,
				'author'		=> [
					'name'		=> get_userdata( $post->post_author )->display_name,
					'page'		=> get_author_posts_url( $post->post_author )
				],
				'content'		=> $post->post_content,
				'readmore'		=> $post->guid
			];

			if ( !empty( $_GET['excerptLength'] ) && absint( sanitize_text_field( $_GET['excerptLength'] ) ) ) {
				$item['excerpt'] = Trim::text( $item['excerpt'],  absint( sanitize_text_field( $_GET['excerptLength'] ) ) );
			}

			$taxes = get_object_taxonomies( $args['post_type'], 'objects' );
			foreach ( $taxes as $tax_slug => $tax ) {

				$terms = get_terms( [ 'taxonomy' => $tax_slug ] );
				foreach ( $terms as $key => $term ) {
					$item[ $tax_slug ][] = [
						'link' 	=> get_term_link( $term ),
						'lable'	=> $term->name
					];
				}
			}

			$output[ 'items' ][] = $item;
		}

		return [
			'success' => true,
			'message' => 'List of posts.',
			'code'    => 200,
			'data'    =>  $output
		];
	}

}
