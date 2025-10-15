<?php

namespace NewfoldLabs\WP\Module\AI;

use NewfoldLabs\WP\ModuleLoader\Container;
use NewfoldLabs\WP\Module\AI\MCP\MCPServer;

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
	 * MCP Server instance.
	 *
	 * @var MCPServer
	 */
	protected $mcp_server;

	/**
	 * Constructor.
	 *
	 * @param Container $container The primary module container
	 * Instantiate controllers and register routes.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		\add_action( 'init', array( __CLASS__, 'load_text_domain' ), 100 );

		// Initialize MCP Server
		$this->mcp_server = new MCPServer( $container );
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
	 * Load text domain for Module
	 *
	 * @return void
	 */
	public static function load_text_domain() {

		\load_plugin_textdomain(
			'wp-module-ai',
			false,
			NFD_MODULE_AI_DIR . '/languages'
		);
	}

	/**
	 * Get the MCP Server instance
	 *
	 * @return MCPServer
	 */
	public function get_mcp_server() {
		return $this->mcp_server;
	}

	/**
	 * Get the abilities registry
	 *
	 * @return \NewfoldLabs\WP\Module\AI\MCP\AbilitiesRegistry
	 */
	public function get_abilities_registry() {
		return $this->mcp_server ? $this->mcp_server->get_abilities_registry() : null;
	}

	/**
	 * Get the abilities manager
	 *
	 * @return \NewfoldLabs\WP\Module\AI\MCP\AbilitiesManager
	 */
	public function get_abilities_manager() {
		return $this->mcp_server ? $this->mcp_server->get_abilities_manager() : null;
	}
}
