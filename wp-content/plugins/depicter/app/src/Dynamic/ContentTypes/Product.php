<?php
namespace Depicter\Dynamic\ContentTypes;


class Product
{
	public function index()
	{
		if ( !function_exists( 'wc_get_product' ) ) {
			return [
				'success' => false,
				'message' => 'There is no product available',
				'code'    => 200,
				'data'    =>  []
			];
		}

		$args = [
			'post_type'			=> 'product',
			'posts_per_page'	=> -1
		];

		$posts = get_posts( $args );
		$products = [];
		if ( !empty( $posts ) ) {
			foreach( $posts as $post ) {
				$product = wc_get_product( $post->ID );
				
				$item = [
					'id' 			=> $post->ID,
					'title' 		=> $post->post_title,
					'featuredImage' => get_the_post_thumbnail_url( $post->ID, 'full' ),
					'url' 			=> $post->guid,
					'date'			=> $post->post_date,
					'excerpt'		=> $post->post_excerpt,
					'content'		=> $post->post_content,
					'sku'			=> $product->get_sku(),
					'price'			=> $product->get_price(),
					'regularPrice'	=> $product->get_regular_price(),
					'salePrice'		=> $product->get_sale_price(),
					'stockQuantity' => $product->get_stock_quantity(),
					'addToCart'		=> $product->add_to_cart_url(),
					'salesFromDate'	=> $product->get_date_on_sale_from(),
					'salesToDate'	=> $product->get_date_on_sale_to(),
					'sales'			=> $product->get_total_sales(),
					'ratingCounts'	=> $product->get_rating_counts(),
					'averageRating'	=> $product->get_average_rating(),
					'reviewCounts'	=> $product->get_review_count()
				];

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

				$products['items'][] = $item;

			}
		}

		return [
			'success' => true,
			'message' => 'List of products.',
			'code'    => 200,
			'data'    =>  $products
		];
	}
}
