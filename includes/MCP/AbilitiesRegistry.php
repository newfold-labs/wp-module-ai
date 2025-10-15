<?php

namespace NewfoldLabs\WP\Module\AI\MCP;

/**
 * Abilities Registry for managing AI Module abilities
 */
class AbilitiesRegistry {


	/**
	 * Registered abilities
	 *
	 * @var array
	 */
	private $abilities = array();

	/**
	 * Register a new ability
	 *
	 * @param string $ability_id The ability ID
	 * @param array  $config     Optional ability configuration
	 */
	public function register_ability( $ability_id, $config = array() ) {
		$this->abilities[ $ability_id ] = array_merge(
			array(
				'id'            => $ability_id,
				'registered_at' => current_time( 'mysql' ),
			),
			$config
		);
	}

	/**
	 * Unregister an ability
	 *
	 * @param string $ability_id The ability ID
	 */
	public function unregister_ability( $ability_id ) {
		unset( $this->abilities[ $ability_id ] );
	}

	/**
	 * Get a specific ability
	 *
	 * @param string $ability_id The ability ID
	 * @return array|null
	 */
	public function get_ability( $ability_id ) {
		return isset( $this->abilities[ $ability_id ] ) ? $this->abilities[ $ability_id ] : null;
	}

	/**
	 * Get all registered abilities
	 *
	 * @return array
	 */
	public function get_all_abilities() {
		return array_keys( $this->abilities );
	}

	/**
	 * Get abilities with their configurations
	 *
	 * @return array
	 */
	public function get_abilities_with_config() {
		return $this->abilities;
	}

	/**
	 * Check if an ability is registered
	 *
	 * @param string $ability_id The ability ID
	 * @return bool
	 */
	public function is_ability_registered( $ability_id ) {
		return isset( $this->abilities[ $ability_id ] );
	}

	/**
	 * Get abilities by category
	 *
	 * @param string $category The category (e.g., 'ai', 'content', 'search')
	 * @return array
	 */
	public function get_abilities_by_category( $category ) {
		$filtered_abilities = array();

		foreach ( $this->abilities as $ability_id => $config ) {
			if ( strpos( $ability_id, $category . '/' ) === 0 ) {
				$filtered_abilities[ $ability_id ] = $config;
			}
		}

		return $filtered_abilities;
	}

	/**
	 * Get ability statistics
	 *
	 * @return array
	 */
	public function get_statistics() {
		$stats = array(
			'total_abilities' => count( $this->abilities ),
			'categories'      => array(),
		);

		foreach ( $this->abilities as $ability_id => $config ) {
			$category = explode( '/', $ability_id )[0];
			if ( ! isset( $stats['categories'][ $category ] ) ) {
				$stats['categories'][ $category ] = 0;
			}
			++$stats['categories'][ $category ];
		}

		return $stats;
	}

	/**
	 * Validate ability configuration
	 *
	 * @param string $ability_id The ability ID
	 * @param array  $config     The ability configuration
	 * @return bool|WP_Error
	 */
	public function validate_ability_config( $ability_id, $config ) {
		// Check required fields
		$required_fields = array( 'label', 'description', 'input_schema', 'output_schema', 'execute_callback' );

		foreach ( $required_fields as $field ) {
			if ( ! isset( $config[ $field ] ) ) {
				return new \WP_Error( 'missing_field', sprintf( 'Missing required field: %s', $field ) );
			}
		}

		// Validate ability ID format
		if ( ! preg_match( '/^[a-z0-9][a-z0-9\/\-_]*[a-z0-9]$/', $ability_id ) ) {
			return new \WP_Error( 'invalid_ability_id', 'Ability ID must contain only lowercase letters, numbers, hyphens, underscores, and forward slashes' );
		}

		// Validate callback
		if ( ! is_callable( $config['execute_callback'] ) ) {
			return new \WP_Error( 'invalid_callback', 'Execute callback must be callable' );
		}

		// Validate permission callback if provided
		if ( isset( $config['permission_callback'] ) && ! is_callable( $config['permission_callback'] ) ) {
			return new \WP_Error( 'invalid_permission_callback', 'Permission callback must be callable' );
		}

		return true;
	}

	/**
	 * Register multiple abilities at once
	 *
	 * @param array $abilities Array of ability configurations
	 * @return array Results of registration attempts
	 */
	public function register_multiple_abilities( $abilities ) {
		$results = array();

		foreach ( $abilities as $ability_id => $config ) {
			$validation = $this->validate_ability_config( $ability_id, $config );

			if ( is_wp_error( $validation ) ) {
				$results[ $ability_id ] = $validation;
			} else {
				$this->register_ability( $ability_id, $config );
				$results[ $ability_id ] = true;
			}
		}

		return $results;
	}

	/**
	 * Export abilities configuration
	 *
	 * @return array
	 */
	public function export_abilities() {
		return array(
			'abilities'   => $this->abilities,
			'exported_at' => current_time( 'mysql' ),
			'version'     => '1.0.0',
		);
	}

	/**
	 * Import abilities configuration
	 *
	 * @param array $data The exported abilities data
	 * @return bool|WP_Error
	 */
	public function import_abilities( $data ) {
		if ( ! isset( $data['abilities'] ) || ! is_array( $data['abilities'] ) ) {
			return new \WP_Error( 'invalid_import_data', 'Invalid import data format' );
		}

		$results = $this->register_multiple_abilities( $data['abilities'] );

		// Check if any registrations failed
		$failed = array_filter(
			$results,
			function ( $result ) {
				return is_wp_error( $result );
			}
		);

		if ( ! empty( $failed ) ) {
			return new \WP_Error( 'partial_import_failure', 'Some abilities failed to import', $failed );
		}

		return true;
	}
}
