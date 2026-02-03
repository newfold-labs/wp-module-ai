<?php

namespace NewfoldLabs\WP\Module\AI;

use NewfoldLabs\WP\Module\AI\RestApi\AISearchController;

/**
 * RestApi wpunit tests.
 *
 * @coversDefaultClass \NewfoldLabs\WP\Module\AI\RestApi\AISearchController
 */
class RestApiWPUnitTest extends \lucatume\WPBrowser\TestCase\WPTestCase {

	/**
	 * rest_api_init registers newfold-ai REST routes.
	 *
	 * @return void
	 */
	public function test_rest_api_init_registers_ai_routes() {
		$controller = new AISearchController();
		$controller->register_routes();
		do_action( 'rest_api_init' );
		$server = rest_get_server();
		$routes = $server->get_routes();
		$found  = array_filter(
			array_keys( $routes ),
			function ( $route ) {
				return strpos( $route, 'newfold-ai' ) !== false;
			}
		);
		$this->assertNotEmpty( $found, 'Expected newfold-ai routes to be registered' );
	}
}
