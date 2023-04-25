<?php

namespace NewfoldLabs\WP\Module\AI\RestApi;

/**
 * Instantiate controllers and register routes.
 */
class RestApi {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Function to register custom API routes and controllers
	 */
	final public function register_routes() {
		$controllers = array(
			'NewfoldLabs\\WP\\Module\\AI\\RestApi\\AISearchController',
		);

		foreach ( $controllers as $controller ) {
			$instance = new $controller();
			$instance->register_routes();
		}
	}

	/**
	 * Attempt to authenticate the REST API request
	 *
	 * @param mixed $status Result of any other authentication attempts
	 *
	 * @return \WP_Error|null|bool
	 */
	public function authenticate( $status ) {

		// Make sure there wasn't a different authentication method used before this
		if ( ! is_null( $status ) ) {
			return $status;
		}

		// Make sure this is a REST API request
		if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
			return $status;
		}

		return true;
	}
}
