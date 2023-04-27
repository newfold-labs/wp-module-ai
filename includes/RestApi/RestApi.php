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
}
