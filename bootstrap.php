<?php

use NewfoldLabs\WP\Module\AI\AI;
use NewfoldLabs\WP\ModuleLoader\Container;

use function NewfoldLabs\WP\ModuleLoader\register;

if ( function_exists( 'add_action' ) ) {

	add_action(
		'plugins_loaded',
		function () {
			// Set Global Constants
			if ( ! defined( 'MODULE_AI_VERSION' ) ) {
				define( 'MODULE_TASKS_VERSION', '0.0.1' );
			}

			if ( ! defined( 'MODULE_AI_DIR' ) ) {
				define( 'MODULE_TASKS_DIR', __DIR__ );
			}

			if ( ! defined( 'AI_SERVICE_BASE' ) ) {
				define( 'AI_SERVICE_BASE', 'http://localhost:8000/api/v1' );
			}

            // Initialize the rest api


			register(
				[
					'name'     => 'ai',
					'label'    => __( 'ai', 'newfold-ai-module' ),
					'callback' => function ( Container $container ) {
						return new AI( $container );
					},
					'isActive' => true,
					'isHidden' => true,
				]
			);

		}
	);

}
