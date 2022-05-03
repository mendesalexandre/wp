<?php
namespace Depicter\WordPress;


use Averta\WordPress\Utility\JSON;

class AutoUpdateCheckService
{

	/**
	 * The plugin remote update path
	 * @var string
	 */
	public $update_path;

	/**
	 * Plugin Slug (plugin_directory/plugin_file.php)
	 * @var string
	 */
	public $plugin_slug;

	/**
	 * Plugin name (plugin_file)
	 * @var string
	 */
	public $slug;

	/**
	 * The item ID in marketplace
	 * @var string
	 */
	public $plugin_id;


	/**
	 * The item name while requesting to update api
	 * @var string
	 */
	public $banners;


	/**
	 * Initialize a new instance of the WordPress Auto-Update class
	 *
	 */
	function __construct() {
		$this->update_path      = \Depicter::remote()->endpoint();
		$this->plugin_slug      = 'depicter/depicter.php';
		$this->slug             = 'depicter';

		// define the alternative API for checking for updates
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update') );
	}


	/**
	 * Add our self-hosted autoupdate plugin to the filter transient
	 *
	 * @param $transient
	 *
	 * @return object $ transient
	 * @throws \Depicter\GuzzleHttp\Exception\GuzzleException
	 */
	public function check_update( $transient ) {

		if( apply_filters( 'depicter_disable_automatic_update', 0 ) )
			return $transient;

		// Get the remote version
		$remote_version = $this->get_remote_version();

		// If a newer version is available, add the update info to update transient
		if ( version_compare( DEPICTER_VERSION, $remote_version, '<' ) ) {
			$obj = new \stdClass();
			$obj->slug      = $this->slug;
			$obj->plugin    = $this->plugin_slug;
			$obj->new_version = $remote_version;
			$obj->url       = '';
			$obj->package   = $this->get_download_url();
			$transient->response[ $this->plugin_slug ] = $obj;
		}

		return $transient;
	}


	/**
	 * Return the remote version
	 *
	 * @return string $remote_version
	 * @throws \Depicter\GuzzleHttp\Exception\GuzzleException
	 */
	public function get_remote_version() {

		$response = \Depicter::remote()->get( $this->update_path . 'v1/core/version-check/latest' );
		$responseBody = JSON::decode( $response->getBody(), true );
		if ( $response->getStatusCode() == 200 && ! empty( $responseBody['version'] ) ) {
			return $responseBody['version'];
		}
		return false;
	}


	/**
	 * Get download url from API
	 *
	 * @return mixed|bool
	 * @throws \Depicter\GuzzleHttp\Exception\GuzzleException
	 */
	public function get_download_url () {
		if( $custom_download = apply_filters( 'depicter_plugin_updater_custom_package_download_url', 0 ) ) {
			return $custom_download;
		}
		$api_url = \Depicter::remote()->endpoint() . 'v1/core/releases/latest';
		$response = \Depicter::remote()->get( $api_url );
		$responseBody = JSON::decode( $response->getBody(), true );
		if ( $response->getStatusCode() !== 200 || empty( $responseBody ) ) {
			return false;
		}

		return $responseBody['download'];
	}

}
