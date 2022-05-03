<?php

namespace Depicter\WordPress;

use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Register widgets and sidebars.
 */
class ContentTypesServiceProvider implements ServiceProviderInterface
{

	/**
	 * post-type slug.
	 */
	const CPT = 'depicter';

	/**
	 * Post type object.
	 *
	 * @access private
	 *
	 * @var \WP_Post_Type
	 */
	private $post_type_object;

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
		add_action( 'init', [ $this, 'registerPostTypes' ] );
		add_action( 'init', [ $this, 'registerTaxonomies'] );
	}

	/**
	 * Register post types.
	 *
	 * @return void
	 */
	public function registerPostTypes() {

		$this->post_type_object = register_post_type(
			'depicter',
			array(
				'labels'              => array(
					'name'               => __( 'Projects', PLUGIN_DOMAIN ),
					'singular_name'      => __( 'Project', PLUGIN_DOMAIN ),
					'add_new'            => __( 'Add New', PLUGIN_DOMAIN ),
					'add_new_item'       => __( 'Add new Project', PLUGIN_DOMAIN ),
					'view_item'          => __( 'View Project', PLUGIN_DOMAIN ),
					'edit_item'          => __( 'Edit Project', PLUGIN_DOMAIN ),
					'new_item'           => __( 'New Project', PLUGIN_DOMAIN ),
					'search_items'       => __( 'Search Projects', PLUGIN_DOMAIN ),
					'not_found'          => __( 'No Sliders found', PLUGIN_DOMAIN ),
					'not_found_in_trash' => __( 'No Sliders found in trash', PLUGIN_DOMAIN ),
				),
				'public'              => true,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'capability_type'     => 'post',
				'hierarchical'        => false,
				'query_var'           => true,
				'menu_icon'   		  => 'dashicons-slides',
				'supports'            => [ 'title','editor','thumbnail','page-attributes', 'author', 'revisions' ],
				'rewrite'             => false
			)
		);

	}

	/**
	 * Register taxonomies.
	 *
	 * @return void
	 */
	public function registerTaxonomies() {

		register_taxonomy(
			'depicter-category',
			[ 'depicter' ],
			[
				'labels'            => [
					'name'              => __( 'Categories', PLUGIN_DOMAIN ),
					'singular_name'     => __( 'Category', PLUGIN_DOMAIN ),
					'search_items'      => __( 'Search Categories', PLUGIN_DOMAIN ),
					'all_items'         => __( 'All Categories', PLUGIN_DOMAIN ),
					'parent_item'       => __( 'Parent category', PLUGIN_DOMAIN ),
					'parent_item_colon' => __( 'Parent category:', PLUGIN_DOMAIN ),
					'view_item'         => __( 'View category', PLUGIN_DOMAIN ),
					'edit_item'         => __( 'Edit category', PLUGIN_DOMAIN ),
					'update_item'       => __( 'Update category', PLUGIN_DOMAIN ),
					'add_new_item'      => __( 'Add New category', PLUGIN_DOMAIN ),
					'new_item_name'     => __( 'New category Name', PLUGIN_DOMAIN ),
					'menu_name'         => __( 'Categories', PLUGIN_DOMAIN ),
				],
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => [ 'slug' => 'depicter-category' ]
			]
		);

	}
}
