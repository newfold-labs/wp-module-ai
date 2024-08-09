<?php
namespace NewfoldLabs\WP\Module\AI;

use NewfoldLabs\WP\Module\Installer\Services\PluginInstaller;

/**
 * Class Plugins
 */
final class Plugins {

	/**
	 * Install the plugin.
	 *
	 * @param string $plugin_slug the slug of the plugin that needs to be installed
	 * @return boolean
	 */
	public static function install( $plugin_slug ) {
		// the install function checks and installs the plugin
		if ( ! PluginInstaller::install( $plugin_slug, true ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Activate Jetpack modules.
	 *
	 * @param string $module the name of the module to activate
	 *
	 * @return boolean
	 */
	public static function activate_jetpack_module( $module ) {

		$request = new \WP_REST_Request(
			'POST',
			'/jetpack/v4/settings'
		);
		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body( \wp_json_encode( array( $module => true ) ) );
		$response = \rest_do_request( $request );

		if ( 200 !== $response->status ) {
			return false;
		}
		return true;
	}
}
