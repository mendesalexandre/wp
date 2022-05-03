<?php
namespace Depicter\WordPress;

use Depicter\GuzzleHttp\Exception\GuzzleException;
use Pimple\Container;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

class DeactivationFeedbackService
{

	/**
	 * DeactivationFeedbackService constructor.
	 */
	public function __construct(){
		add_action( 'current_screen', [ $this, 'check_current_screen' ] );
	}

	/**
	 * Check if current screen is plugins page then print feedback markup
	 */
	public function check_current_screen() {
		if ( in_array( get_current_screen()->id, [ 'plugins', 'plugins-network' ] ) ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueueScripts']);
			add_action( 'admin_footer', [ $this, 'enqueue_feedback_dialog_scripts' ] );
		}
	}

	/**
	 * Enqueue required scripts in plugins page
	 */
	public function enqueueScripts() {
		\Depicter::core()->assets()->enqueueScript( 'depicter-admin', \Depicter::core()->assets()->getUrl() . '/resources/scripts/admin/index.js', ['jquery'], true );
		wp_localize_script('depicter-admin', 'depDeactivationParams',[
			'ajaxUrl' => esc_url( admin_url( 'admin-ajax.php' ) ),
		]);
	}

	/**
	 * @return array[]
	 */
	protected function reasonsList() {
		return [
			[
				'key'   => 'no-longer-need-it',
				'text'   => __(  'I no longer need the plugin', PLUGIN_DOMAIN ),
				'user-description' => ''
			],
			[
				'key'   => 'broke-my-site',
				'text'  => __( 'It broke my site', PLUGIN_DOMAIN ),
				'user-description' => ''
			],
			[
				'key'   => 'found-better-plugin',
				'text'  => __( 'I found a better plugin or solution', PLUGIN_DOMAIN ),
				'user-description'   => __( 'Please share which plugin?', PLUGIN_DOMAIN ),
			],
			[
				'key'   => 'does-not-work',
				'text'  => __( 'I couldn\'t get the plugin to work', PLUGIN_DOMAIN ),
				'user-description'   => __( 'How we can improve it?', PLUGIN_DOMAIN ),
			],
			[
				'key'   => 'temporary',
				'text'  => __( 'It\'s a temporary deactivation', PLUGIN_DOMAIN ),
				'user-description' => ''
			],
			[
				'key'   => 'deactivation-other',
				'text'  => __( 'Other', PLUGIN_DOMAIN ),
				'user-description'   => __( 'What we can do better?', PLUGIN_DOMAIN ),
			]
		];
	}

	/**
	 * Render feedback markup
	 */
	public function enqueue_feedback_dialog_scripts() {
		\Depicter::render('admin/survey/feedback', [
			'reasons' => $this->reasonsList()
		] );
	}

	/**
	 * @param $feedback
	 *
	 * @return bool
	 * @throws GuzzleException
	 */
	public function sendFeedback( $feedback ) {
		if ( empty( $feedback['userDescription'] ) ) {
			foreach( $this->reasonsList() as $reason ){
				if ( $reason['key'] == $feedback['issueRelatesTo'] ) {
					$feedback['userDescription'] = $reason['text'];
				}
			}
		}
		return ( in_array( $feedback['issueRelatesTo'], wp_list_pluck( $this->reasonsList(), 'key' ) ) || $feedback['issueRelatesTo'] == 'skip' ) &&
		       \Depicter::client()->reportIssue( $feedback );
	}
}
