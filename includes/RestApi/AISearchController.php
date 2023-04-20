<?php

namespace NewfoldLabs\WP\Module\AI\RestApi;

use NewfoldLabs\WP\Module\AI\Utils\AISearchUtil;

/**
 * APIs for getting the result from the AI service
 */
class AISearchController extends \WP_REST_Controller {
	/**
	 * The namespace of this controller's route.
	 *
	 * @var string
	 */
	protected $namespace = 'nfd-ai/v1';

	/**
	 * The base of this controller's route
	 *
	 * @var string
	 */
	protected $rest_base = 'search';

	/**
	 * Register the routes for this objects of the controller
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_search_result' ),
					'args'                => array(
						'hive_token'  => array(
							'required' => true,
							'type'     => 'string',
						),
						'user_prompt' => array(
							'required' => true,
							'type'     => 'string',
						),
						'identifier'  => array(
							'required' => true,
							'type'     => 'string',
						),
						'extra'       => array(
							'required' => false,
							'type'     => 'array',
						),
					),
					'permission_callback' => array( $this, 'check_permission' ),
				),
			)
		);
	}

	/**
	 * Proxy to the AI service to get the responses.
	 *
	 * @param \WP_REST_Request $request Request object
	 *
	 * @returns \WP_REST_Response|\WP_Error
	 */
	public function get_search_result( \WP_REST_Request $request ) {
		$hiive_token = $request['hiive_token'];
		$user_prompt = $request['user_prompt'];
		$identifier  = $request['identifier'];
		$extra       = $request['extra'];

		$response = AISearchUtil::get_search_results( $hiive_token, $user_prompt, $identifier, $extra );
		return new \WP_REST_Response( $response, 200 );
	}
}
