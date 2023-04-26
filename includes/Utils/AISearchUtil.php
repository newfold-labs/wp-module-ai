<?php

namespace NewfoldLabs\WP\Module\AI\Utils;

/**
 * The utility pass through for interacting with the
 * AI service
 */
class AISearchUtil {

	/**
	 * The function to proxy to the AI service and get a response
	 *
	 * @param string     $hiive_token The Hive token
	 * @param string     $user_prompt The user search query
	 * @param string     $identifier  The identifier for the caller
	 * @param array|null $extra       Extra parameters to be included
	 * @return array
	 */
	public static function get_search_results(
		string $hiive_token, string $user_prompt, string $identifier, array $extra = null
	) {
		if ( ! function_exists( 'wp_json_encode' ) ) {
			require_once ABSPATH . 'wp-includes/functions.php';
		}

		$response = wp_remote_post(
			AI_SERVICE_BASE,
			array(
				'method'  => 'POST',
				'headers' => array(
					'Content-Type' => 'application/json'
				),
				'timeout' => 60,
				'body'    => wp_json_encode(
					array(
						'hiivetoken' => $hiive_token,
						'prompt'     => $user_prompt,
						'identifier' => $identifier,
						'extra'      => $extra
					)
				)
			)
		);
		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return array(
				'error' => __('We are unable to process the request at this moment')
			);
		}
		$parsed_response = json_decode( wp_remote_retrieve_body( $response ), true );
		return array(
			'result'  => $parsed_response['payload']['text'],
			'post_id' => $parsed_response['payload']['postId']
		);
	}
}
