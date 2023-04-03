<?php

use NewfoldLabs\WP\Module\AI\AI;
use NewfoldLabs\WP\ModuleLoader\Container;

use function NewfoldLabs\WP\ModuleLoader\register;

if ( function_exists( 'add_action' ) ) {

	add_action(
		'plugins_loaded',
		function () {

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
