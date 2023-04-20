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
	 * @param string $hiive_token
	 * @param string $user_prompt
	 * @param string $identifier
	 * @param array|null $extra
	 * @return array
	 */
	public static function get_search_results (
		string $hiive_token, string $user_prompt, string $identifier, array $extra = null
	) {
		$response = wp_remote_post( AI_SERVICE_BASE . '/search', array(
			'hiive_token' => $hiive_token,
			'user_prompt' => $user_prompt,
			'identifier'   => $identifier,
			'extra'       => $extra
		) );
		// TODO: Add filters for the response
		return json_decode( wp_remote_retrieve_body( $response ), true );
	}
}
