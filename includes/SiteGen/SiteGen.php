<?php

namespace NewfoldLabs\WP\Module\AI\SiteGen;

use NewfoldLabs\WP\Module\Data\HiiveConnection;
use NewfoldLabs\WP\Module\Data\SiteCapabilities;

/**
 * The class to generate different parts of the site gen object.
 */
class SiteGen {
	/**
	 * The required validations
	 *
	 * @var array
	 */
	private static $required_validations = array(
		'siteclassification'   => array(
			'site_description',
		),
		'targetaudience'       => array(
			'site_description',
		),
		'contenttones'         => array(
			'site_description',
		),
		'contentstructure'     => array(
			'site_description',
		),
		'colorpalette'         => array(
			'site_description',
		),
		'sitemap'              => array(
			'site_description',
		),
		'pluginrecommendation' => array(
			'site_description',
		),
		'fontpair'             => array(
			'site_description',
		),
		'keywords'             => array(
			'site_description',
			'content_style',
		),
	);

	/**
	 * Function to check capabilities
	 */
	private static function check_capabilities() {
		$capability = new SiteCapabilities();

		$ai_enabled = $capability->get( 'canAccessAI' );

		return $ai_enabled;
	}

	/**
	 * Function to validate site info
	 *
	 * @param array  $site_info  The main input for forming the prompt
	 * @param string $identifier The identifier to be used for generating the required meta
	 */
	private static function validate_site_info( $site_info, $identifier ) {
		if ( array_key_exists( $identifier, self::$required_validations ) ) {
			$validations = self::$required_validations[ $identifier ];
			foreach ( $validations as $required_key ) {
				if ( ! array_key_exists( $required_key, $site_info ) ) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Function to get the site gen response from cache based on the identifier
	 *
	 * @param string $identifier The identifier to be used for generating the required meta
	 */
	private static function get_sitegen_from_cache( $identifier ) {
		$site_gen = get_option( NFD_SITEGEN_OPTION, null );
		if ( ! $site_gen ) {
			update_option( NFD_SITEGEN_OPTION, array() );
		}
		if ( $site_gen && array_key_exists( $identifier, $site_gen ) ) {
			return $site_gen[ $identifier ];
		}
		return null;
	}

	/**
	 * Function to cache the response from sitegen API
	 *
	 * @param string $identifier The identifier to be used for generating the required meta
	 * @param array  $response   The response from the sitegen API.
	 */
	private static function cache_sitegen_response( $identifier, $response ) {
		$site_gen                = get_option( NFD_SITEGEN_OPTION, array() );
		$site_gen[ $identifier ] = $response;
		update_option( NFD_SITEGEN_OPTION, $site_gen );
	}

	/**
	 * Function to generate the prompt from the JSON input.
	 *
	 * @param array $site_info The JSON input for the sitegen call.
	 */
	private static function get_prompt_from_info( $site_info ) {
		$prompt = '';
		foreach ( $site_info as $key => $value ) {
			$prompt = $prompt . $key . ': ' . $value . ', ';
		}
		return $prompt;
	}

	/**
	 * Get the patterns for a particular category.
	 *
	 * @param string $category The category to get patterns for.
	 */
	private static function get_patterns_for_category( $category ) {
		$response = wp_remote_get(
			NFD_PATTERNS_BASE . 'patterns?category=' . $category,
			array(
				'headers' => array(
					'Content-Type' => 'application/json',
				),
				'timeout' => 60,
			)
		);

		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return array(
				'error' => __( 'We are unable to process the request at this moment' ),
			);
		}

		$patterns           = json_decode( wp_remote_retrieve_body( $response ), true );
		$processed_patterns = array();

		foreach ( $patterns['data'] as $pattern ) {
			$processed_patterns[ $pattern['slug'] ] = $pattern;
		}

		return $processed_patterns;
	}

	/**
	 * Function to generate all possible patterns for the current generation.
	 *
	 * @param string  $site_description  The site description (the user prompt)
	 * @param array   $content_style     The generated content style.
	 * @param array   $target_audience   The generated target audience.
	 * @param array   $content_structure The content structures generated / cached
	 * @param boolean $skip_cache        If we need to skip cache.
	 */
	private static function generate_pattern_content(
		$site_description,
		$content_style,
		$target_audience,
		$content_structure,
		$skip_cache = false
	) {
		if ( ! $skip_cache ) {
			$generated_patterns = self::get_sitegen_from_cache( 'contentRegenerate' );
			if ( $generated_patterns ) {
				return $generated_patterns;
			}
		}

		$keywords = self::generate_site_meta(
			array(
				'site_description' => $site_description,
				'content_style'    => $content_style,
			),
			'keywords'
		);

		$unique_categories = array();
		foreach ( $content_structure as $homepage => $structure ) {
			foreach ( $structure as $category ) {
				if ( ! in_array( $category, $unique_categories, true ) ) {
					array_push( $unique_categories, $category );
				}
			}
		}

		$category_pattern_map = array();

		// Generate patterns randomly for the unique categories
		foreach ( $unique_categories as $category ) {
			$patterns_for_category = self::get_patterns_for_category( $category );
			if ( count( $patterns_for_category ) <= 5 ) {
				$random_selected_patterns = array_rand( $patterns_for_category, count( $patterns_for_category ) );
			} else {
				$random_selected_patterns = array_rand( $patterns_for_category, 5 );
			}

			$category_pattern_map[ $category ] = array();
			foreach ( $random_selected_patterns as $pattern_slug ) {
				$pattern = $patterns_for_category[ $pattern_slug ];
				// Generate content for these patterns
				$response = wp_remote_post(
					NFD_AI_BASE . 'generateSiteMeta',
					array(
						'headers' => array(
							'Content-Type' => 'application/json',
						),
						'timeout' => 60,
						'body'    => wp_json_encode(
							array(
								'hiivetoken' => HiiveConnection::get_auth_token(),
								'prompt'     => array(
									'pattern' => $pattern['content'],
									'prompt'  => self::get_prompt_from_info(
										array(
											'site_description' => $site_description,
											'keywords' => wp_json_encode( $keywords ),
											'content_style' => wp_json_encode( $content_style ),
											'target_audience' => wp_json_encode( $target_audience ),
										)
									),
								),
								'identifier' => 'contentRegenerate',
							)
						),
					)
				);

				$parsed_response   = json_decode( wp_remote_retrieve_body( $response ), true );
				$generated_pattern = $parsed_response['content'];
				array_push( $category_pattern_map[ $category ], $generated_pattern );
			}
		}

		// Store the categories
		self::cache_sitegen_response( 'contentRegenerate', $category_pattern_map );

		return $category_pattern_map;
	}

	/**
	 * Function to generate the site meta according to the arguments passed
	 *
	 * @param array   $site_info  The Site Info object, will be validated for required params.
	 * @param string  $identifier The identifier for generating the site meta
	 * @param boolean $skip_cache To skip returning the response from cache
	 */
	public static function generate_site_meta( $site_info, $identifier, $skip_cache = false ) {
		if ( ! self::check_capabilities() ) {
			return array(
				'error' => __( 'You do not have the permissions to perform this action' ),
			);
		}

		if ( ! self::validate_site_info( $site_info, $identifier ) ) {
			return array(
				'error' => __( 'Required values not provided' ),
			);
		}

		if ( ! $skip_cache ) {
			$site_gen_cached = self::get_sitegen_from_cache( $identifier );
			if ( $site_gen_cached ) {
				return $site_gen_cached;
			}
		}

		$response = wp_remote_post(
			NFD_AI_BASE . 'generateSiteMeta',
			array(
				'headers' => array(
					'Content-Type' => 'application/json',
				),
				'timeout' => 60,
				'body'    => wp_json_encode(
					array(
						'hiivetoken' => HiiveConnection::get_auth_token(),
						'prompt'     => self::get_prompt_from_info( $site_info ),
						'identifier' => $identifier,
					)
				),
			)
		);

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			if ( 400 === $response_code ) {
				$error = json_decode( wp_remote_retrieve_body( $response ), true );
				return array(
					'error' => $error['payload']['reason'],
				);
			}
			return array(
				'error' => __( 'We are unable to process the request at this moment' ),
			);
		}

		$parsed_response = json_decode( wp_remote_retrieve_body( $response ), true );

		self::cache_sitegen_response( $identifier, $parsed_response );

		try {
			return $parsed_response;
		} catch ( \Exception $exception ) {
			return array(
				'error' => __( 'We are unable to process the request at this moment' ),
			);
		}
	}

	/**
	 * Function to get the home page patterns. Randomly generates the patterns and substitutes with existing content.
	 * Set regenerate to get new combinations
	 *
	 * @param string  $site_description The site description (user prompt).
	 * @param array   $content_style    Generated from sitegen.
	 * @param array   $target_audience  Generated target audience.
	 * @param boolean $regenerate       If we need to regenerate.
	 */
	public static function get_home_pages( $site_description, $content_style, $target_audience, $regenerate = true ) {
		$generated_content_structures = self::generate_site_meta(
			array( 'site_description' => $site_description ),
			'contentstructure'
		);
		// Check if we have the response in cache already
		if ( ! $regenerate ) {
			$generated_homepages = self::get_sitegen_from_cache( 'homepages' );
			if ( $generated_homepages ) {
				return $generated_homepages;
			}
		}
		$random_homepages    = array_rand( $generated_content_structures, 3 );
		$generated_homepages = array();
		$generated_patterns  = self::generate_pattern_content(
			$site_description,
			$content_style,
			$target_audience,
			$generated_content_structures
		);

		// Choose random categories for the generated patterns and return
		foreach ( $random_homepages as $slug ) {
			$generated_homepages[ $slug ] = array();
			foreach ( $generated_content_structures[ $slug ] as $pattern_category ) {
				if ( ! $generated_patterns[ $pattern_category ] ) {
					continue;
				}
				// Get a random pattern for the category.
				$random_pattern = array_rand( $generated_patterns[ $pattern_category ] );
				$random_pattern = $generated_patterns[ $random_pattern ];
				array_push( $generated_homepages[ $slug ], $random_pattern );
			}
		}

		self::cache_sitegen_response( 'homepages', $generated_homepages );
		return $generated_homepages;
	}
}
