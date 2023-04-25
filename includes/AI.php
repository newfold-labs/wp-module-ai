<?php

namespace NewfoldLabs\WP\Module\AI;

use NewfoldLabs\WP\ModuleLoader\Container;
use NewfoldLabs\WP\Module\AI\RestApi\RestApi;

/**
 * The class to initialize and load the module
 */
class AI {

	/**
	 * Dependency injection container.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * Constructor.
	 *
	 * @param Container $container The primary module container
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
		new RestApi();
	}
}
