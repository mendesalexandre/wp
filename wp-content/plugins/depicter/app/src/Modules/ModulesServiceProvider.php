<?php

namespace Depicter\Modules;

use Depicter\Modules\Elementor\SliderWidget;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Register widgets.
 */
class ModulesServiceProvider implements ServiceProviderInterface {
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
		add_action( 'elementor/widgets/widgets_registered', [$this, 'registerElementorWidgets'] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueueEditorScripts'] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_elementor_widget_script'] );
	}

	/**
	 * Register Elementor widgets.
	 *
	 * @return void
	 */
	public function registerElementorWidgets() {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new SliderWidget() );
	}

	/**
	 * load required script for elementor widget in elementor editor env
	 */
	public function load_elementor_widget_script() {
		if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			\Depicter::front()->assets()->enqueueScripts('widget');
		}
	}

	public function enqueueEditorScripts() {
		\Depicter::core()->assets()->enqueueScript(
			'depicter-admin',
			\Depicter::core()->assets()->getUrl() . '/resources/scripts/admin/index.js',
			['jquery'],
			true
		);

		wp_localize_script( 'depicter-admin', 'depicterParams',[
			'editorUrl' => \Depicter::editor()->getEditUrl('1')
		]);
	}
}
