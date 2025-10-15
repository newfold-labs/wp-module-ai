<?php

namespace NewfoldLabs\WP\Module\AI\MCP;

use NewfoldLabs\WP\ModuleLoader\Container;
use NewfoldLabs\WP\Module\AI\MCP\AbilitiesManager;

/**
 * MCP Server integration for the AI Module
 */
class MCPServer {


	/**
	 * Dependency injection container.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * Abilities registry instance.
	 *
	 * @var AbilitiesRegistry
	 */
	protected $abilities_registry;

	/**
	 * Abilities manager instance.
	 *
	 * @var AbilitiesManager
	 */
	protected $abilities_manager;

	/**
	 * Constructor.
	 *
	 * @param Container $container The primary module container
	 */
	public function __construct( Container $container ) {
		$this->container          = $container;
		$this->abilities_registry = new AbilitiesRegistry();
		$this->abilities_manager  = new AbilitiesManager( $this->abilities_registry );

		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Initialize the MCP Server
	 */
	public function init() {
		// Load Composer autoloader
		$this->loadComposerAutoloader();

		// Initialize Abilities API first
		$this->initAbilitiesAPI();

		// Initialize MCP Server
		$this->initMCPServer();

		// Admin functionality removed per user request
	}

	/**
	 * Load Composer autoloader
	 */
	private function loadComposerAutoloader() {
		$autoloader = NFD_MODULE_AI_DIR . '/vendor/autoload.php';
		if ( file_exists( $autoloader ) ) {
			require_once $autoloader;
		} else {
			add_action( 'admin_notices', array( $this, 'composerAutoloaderNotice' ) );
		}
	}

	/**
	 * Initialize Abilities API
	 */
	private function initAbilitiesAPI() {
		// Ensure the Abilities Registry is initialized
		if ( class_exists( 'WP_Abilities_Registry' ) ) {
			WP_Abilities_Registry::get_instance();
		}

		// Register AI-specific abilities
		$this->registerAIAbilities();
	}

	/**
	 * Register AI-specific abilities
	 */
	private function registerAIAbilities() {
		// Register AI content generation abilities
		$this->abilities_manager->register_ai_content_abilities();

		// Register content management abilities
		$this->abilities_manager->register_content_management_abilities();

		// Register additional custom abilities
		$this->registerCustomAbilities();
	}

	/**
	 * Register custom abilities specific to this module
	 */
	private function registerCustomAbilities() {
		// Register AI search ability
		$search_ability = $this->abilities_manager->create_ability_config(
			'ai/search',
			'AI Search',
			'Search using AI-powered search capabilities',
			array(
				'type'       => 'object',
				'properties' => array(
					'query' => array( 'type' => 'string' ),
					'type'  => array(
						'type' => 'string',
						'enum' => array( 'content', 'patterns', 'images' ),
					),
					'limit' => array(
						'type'    => 'integer',
						'default' => 10,
					),
				),
				'required'   => array( 'query' ),
			),
			$this->abilities_manager->create_standard_output_schema(
				array(
					'results' => array( 'type' => 'array' ),
					'count'   => array( 'type' => 'integer' ),
				)
			),
			array( $this, 'searchAbility' ),
			$this->abilities_manager->create_capability_permission_callback( 'read' )
		);

		// Register site generation ability
		$site_ability = $this->abilities_manager->create_ability_config(
			'ai/generate-site',
			'Generate AI Site',
			'Generate a complete website using AI',
			array(
				'type'       => 'object',
				'properties' => array(
					'description' => array( 'type' => 'string' ),
					'theme'       => array( 'type' => 'string' ),
					'pages'       => array( 'type' => 'array' ),
				),
				'required'   => array( 'description' ),
			),
			$this->abilities_manager->create_standard_output_schema(
				array(
					'site_id' => array( 'type' => 'string' ),
					'url'     => array( 'type' => 'string' ),
					'message' => array( 'type' => 'string' ),
				)
			),
			array( $this, 'generateSiteAbility' ),
			$this->abilities_manager->create_capability_permission_callback( 'manage_options' )
		);

		// Register the custom abilities
		$this->abilities_manager->register_ability( 'ai/search', $search_ability );
		$this->abilities_manager->register_ability( 'ai/generate-site', $site_ability );
	}

	/**
	 * Initialize MCP Server
	 */
	private function initMCPServer() {
		// Check if required functions and classes are available
		if ( ! function_exists( 'wp_register_ability' ) ||
			! class_exists( 'WP\\MCP\\Plugin' ) ) {
			add_action( 'admin_notices', array( $this, 'missingDependenciesNotice' ) );
			return;
		}

		// Initialize MCP Server
		$this->initMCPServerInstance();
	}

	/**
	 * Initialize MCP Server instance
	 */
	private function initMCPServerInstance() {
		// Initialize the MCP Adapter plugin
		add_action(
			'init',
			function () {
				if ( class_exists( 'WP\\MCP\\Plugin' ) ) {
					\WP\MCP\Plugin::instance();
				}
			}
		);

		// Register our MCP server
		add_action( 'mcp_adapter_init', array( $this, 'registerMCPServer' ) );
	}

	/**
	 * Register the MCP server with the adapter
	 */
	public function registerMCPServer( $adapter ) {
		try {
			$abilities = $this->abilities_registry->get_all_abilities();

			$adapter->create_server(
				'wp-module-ai-mcp',              // server_id
				'wp-module-ai',                  // server_route_namespace
				'v1',                            // server_route
				'WP Module AI MCP Server',       // server_name
				'WordPress AI Module MCP Server using Abilities API', // server_description
				'1.0.0',                         // server_version
				array( \WP\MCP\Transport\Http\RestTransport::class ), // mcp_transports
				\WP\MCP\Infrastructure\ErrorHandling\ErrorLogMcpErrorHandler::class, // error_handler
				\WP\MCP\Infrastructure\Observability\NullMcpObservabilityHandler::class, // observability_handler
				$abilities,                       // tools
				array(),                          // resources
				array(),                          // prompts
				array( $this, 'checkMcpPermission' ) // transport_permission_callback
			);
		} catch ( Exception $e ) {
			error_log( 'WP Module AI MCP Server registration failed: ' . $e->getMessage() );
		}
	}

	/**
	 * Check MCP permission - require authentication
	 */
	public function checkMcpPermission() {
		// Require logged-in user with appropriate capabilities
		return is_user_logged_in() && current_user_can( 'read' );
	}


	/**
	 * Search ability callback
	 */
	public function searchAbility( $args ) {
		// This would integrate with the existing AI search functionality
		// For now, return a placeholder response
		return array(
			'success' => true,
			'results' => array(
				array(
					'title'   => 'Search Result 1',
					'content' => 'Content related to: ' . sanitize_text_field( $args['query'] ),
					'type'    => sanitize_text_field( $args['type'] ?? 'content' ),
				),
			),
			'count'   => 1,
		);
	}

	/**
	 * Generate site ability callback
	 */
	public function generateSiteAbility( $args ) {
		// This would integrate with the existing site generation functionality
		// For now, return a placeholder response
		return array(
			'success' => true,
			'site_id' => 'ai-site-' . time(),
			'url'     => home_url( '/ai-generated-site/' ),
			'message' => 'AI site generation initiated for: ' . sanitize_text_field( $args['description'] ),
		);
	}


	/**
	 * Composer autoloader notice
	 */
	public function composerAutoloaderNotice() {
		echo '<div class="notice notice-error"><p>';
		echo '<strong>AI Module MCP:</strong> Composer dependencies not found. Please run <code>composer install</code> in the module directory.';
		echo '</p></div>';
	}

	/**
	 * Missing dependencies notice
	 */
	public function missingDependenciesNotice() {
		echo '<div class="notice notice-error"><p>';
		echo '<strong>AI Module MCP:</strong> Required dependencies are missing. Please ensure the Abilities API and MCP Adapter packages are installed.';
		echo '</p></div>';
	}

	/**
	 * Get the abilities registry
	 *
	 * @return AbilitiesRegistry
	 */
	public function get_abilities_registry() {
		return $this->abilities_registry;
	}

	/**
	 * Get the abilities manager
	 *
	 * @return AbilitiesManager
	 */
	public function get_abilities_manager() {
		return $this->abilities_manager;
	}
}
