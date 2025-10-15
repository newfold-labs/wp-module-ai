<?php

namespace NewfoldLabs\WP\Module\AI\MCP;

/**
 * Abilities Manager for standardized ability registration
 */
class AbilitiesManager {


	/**
	 * Abilities registry instance
	 *
	 * @var AbilitiesRegistry
	 */
	private $registry;

	/**
	 * Constructor
	 *
	 * @param AbilitiesRegistry $registry The abilities registry
	 */
	public function __construct( AbilitiesRegistry $registry ) {
		$this->registry = $registry;
	}

	/**
	 * Register a new ability with standardized configuration
	 *
	 * @param string $ability_id The unique ability identifier
	 * @param array  $config     The ability configuration
	 * @return bool|WP_Error
	 */
	public function register_ability( $ability_id, $config ) {
		// Validate the configuration
		$validation = $this->registry->validate_ability_config( $ability_id, $config );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}

		// Register with WordPress Abilities API
		$result = wp_register_ability( $ability_id, $config );

		if ( $result ) {
			// Register with our internal registry
			$this->registry->register_ability( $ability_id, $config );
			return true;
		}

		return new \WP_Error( 'registration_failed', 'Failed to register ability with WordPress Abilities API' );
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
			$results[ $ability_id ] = $this->register_ability( $ability_id, $config );
		}

		return $results;
	}

	/**
	 * Create a standardized ability configuration
	 *
	 * @param string        $id          The ability ID
	 * @param string        $label       The human-readable label
	 * @param string        $description The ability description
	 * @param array         $input_schema The input schema
	 * @param array         $output_schema The output schema
	 * @param callable      $execute_callback The execution callback
	 * @param callable|null $permission_callback The permission callback
	 * @return array
	 */
	public function create_ability_config( $id, $label, $description, $input_schema, $output_schema, $execute_callback, $permission_callback = null ) {
		$config = array(
			'label'            => $label,
			'description'      => $description,
			'input_schema'     => $input_schema,
			'output_schema'    => $output_schema,
			'execute_callback' => $execute_callback,
		);

		if ( $permission_callback ) {
			$config['permission_callback'] = $permission_callback;
		}

		return $config;
	}

	/**
	 * Create a simple text input schema
	 *
	 * @param string $field_name The field name
	 * @param string $description The field description
	 * @param bool   $required Whether the field is required
	 * @return array
	 */
	public function create_text_input_schema( $field_name, $description = '', $required = true ) {
		$schema = array(
			'type'       => 'object',
			'properties' => array(
				$field_name => array(
					'type'        => 'string',
					'description' => $description,
				),
			),
		);

		if ( $required ) {
			$schema['required'] = array( $field_name );
		}

		return $schema;
	}

	/**
	 * Create a simple success/error output schema
	 *
	 * @param array $additional_properties Additional properties to include
	 * @return array
	 */
	public function create_standard_output_schema( $additional_properties = array() ) {
		$properties = array(
			'success' => array(
				'type'        => 'boolean',
				'description' => 'Whether the operation was successful',
			),
		);

		$properties = array_merge( $properties, $additional_properties );

		return array(
			'type'       => 'object',
			'properties' => $properties,
		);
	}

	/**
	 * Create a permission callback for a specific capability
	 *
	 * @param string $capability The WordPress capability
	 * @return callable
	 */
	public function create_capability_permission_callback( $capability ) {
		return function () use ( $capability ) {
			return current_user_can( $capability );
		};
	}

	/**
	 * Create a permission callback for multiple capabilities (OR logic)
	 *
	 * @param array $capabilities Array of WordPress capabilities
	 * @return callable
	 */
	public function create_multiple_capabilities_permission_callback( $capabilities ) {
		return function () use ( $capabilities ) {
			foreach ( $capabilities as $capability ) {
				if ( current_user_can( $capability ) ) {
					return true;
				}
			}
			return false;
		};
	}

	/**
	 * Register AI content generation abilities
	 *
	 * @return array Results of registration attempts
	 */
	public function register_ai_content_abilities() {
		$abilities = array();

		// Text generation ability
		$abilities['ai/generate-text'] = $this->create_ability_config(
			'ai/generate-text',
			'Generate AI Text',
			'Generate text content using AI',
			$this->create_text_input_schema( 'prompt', 'The text prompt for generation' ),
			$this->create_standard_output_schema(
				array(
					'content' => array(
						'type'        => 'string',
						'description' => 'The generated text content',
					),
				)
			),
			array( $this, 'generate_text_callback' ),
			$this->create_capability_permission_callback( 'edit_posts' )
		);

		// Image generation ability
		$abilities['ai/generate-image'] = $this->create_ability_config(
			'ai/generate-image',
			'Generate AI Image',
			'Generate images using AI',
			array(
				'type'       => 'object',
				'properties' => array(
					'prompt' => array(
						'type'        => 'string',
						'description' => 'The image prompt for generation',
					),
					'style'  => array(
						'type'    => 'string',
						'enum'    => array( 'realistic', 'artistic', 'cartoon', 'abstract' ),
						'default' => 'realistic',
					),
					'size'   => array(
						'type'    => 'string',
						'enum'    => array( 'small', 'medium', 'large' ),
						'default' => 'medium',
					),
				),
				'required'   => array( 'prompt' ),
			),
			$this->create_standard_output_schema(
				array(
					'image_url' => array(
						'type'        => 'string',
						'description' => 'URL of the generated image',
					),
					'image_id'  => array(
						'type'        => 'integer',
						'description' => 'WordPress attachment ID of the generated image',
					),
				)
			),
			array( $this, 'generate_image_callback' ),
			$this->create_capability_permission_callback( 'upload_files' )
		);

		return $this->register_multiple_abilities( $abilities );
	}

	/**
	 * Register content management abilities
	 *
	 * @return array Results of registration attempts
	 */
	public function register_content_management_abilities() {
		$abilities = array();

		// Create post ability
		$abilities['content/create-post'] = $this->create_ability_config(
			'content/create-post',
			'Create Post',
			'Create a new WordPress post',
			array(
				'type'       => 'object',
				'properties' => array(
					'title'     => array(
						'type'        => 'string',
						'description' => 'The post title',
					),
					'content'   => array(
						'type'        => 'string',
						'description' => 'The post content',
					),
					'status'    => array(
						'type'    => 'string',
						'enum'    => array( 'draft', 'publish', 'private' ),
						'default' => 'draft',
					),
					'post_type' => array(
						'type'    => 'string',
						'default' => 'post',
					),
				),
				'required'   => array( 'title', 'content' ),
			),
			$this->create_standard_output_schema(
				array(
					'post_id'  => array(
						'type'        => 'integer',
						'description' => 'The created post ID',
					),
					'post_url' => array(
						'type'        => 'string',
						'description' => 'URL of the created post',
					),
				)
			),
			array( $this, 'create_post_callback' ),
			$this->create_capability_permission_callback( 'edit_posts' )
		);

		// Update post ability
		$abilities['content/update-post'] = $this->create_ability_config(
			'content/update-post',
			'Update Post',
			'Update an existing WordPress post',
			array(
				'type'       => 'object',
				'properties' => array(
					'post_id' => array(
						'type'        => 'integer',
						'description' => 'The post ID to update',
					),
					'title'   => array(
						'type'        => 'string',
						'description' => 'The new post title',
					),
					'content' => array(
						'type'        => 'string',
						'description' => 'The new post content',
					),
					'status'  => array(
						'type' => 'string',
						'enum' => array( 'draft', 'publish', 'private' ),
					),
				),
				'required'   => array( 'post_id' ),
			),
			$this->create_standard_output_schema(
				array(
					'post_id' => array(
						'type'        => 'integer',
						'description' => 'The updated post ID',
					),
				)
			),
			array( $this, 'update_post_callback' ),
			$this->create_capability_permission_callback( 'edit_posts' )
		);

		return $this->register_multiple_abilities( $abilities );
	}

	/**
	 * Generate text callback
	 *
	 * @param array $args The ability arguments
	 * @return array
	 */
	public function generate_text_callback( $args ) {
		// This would integrate with the actual AI service
		// For now, return a placeholder response
		return array(
			'success' => true,
			'content' => 'Generated text based on: ' . sanitize_text_field( $args['prompt'] ),
		);
	}

	/**
	 * Generate image callback
	 *
	 * @param array $args The ability arguments
	 * @return array
	 */
	public function generate_image_callback( $args ) {
		// This would integrate with the actual AI image generation service
		// For now, return a placeholder response
		return array(
			'success'   => true,
			'image_url' => home_url( '/placeholder-image.jpg' ),
			'image_id'  => 0,
		);
	}

	/**
	 * Create post callback
	 *
	 * @param array $args The ability arguments
	 * @return array
	 */
	public function create_post_callback( $args ) {
		$post_data = array(
			'post_title'   => sanitize_text_field( $args['title'] ),
			'post_content' => wp_kses_post( $args['content'] ),
			'post_status'  => sanitize_text_field( $args['status'] ?? 'draft' ),
			'post_type'    => sanitize_text_field( $args['post_type'] ?? 'post' ),
			'post_author'  => get_current_user_id(),
		);

		$post_id = wp_insert_post( $post_data );

		if ( is_wp_error( $post_id ) ) {
			return array(
				'success' => false,
				'error'   => $post_id->get_error_message(),
			);
		}

		return array(
			'success'  => true,
			'post_id'  => $post_id,
			'post_url' => get_permalink( $post_id ),
		);
	}

	/**
	 * Update post callback
	 *
	 * @param array $args The ability arguments
	 * @return array
	 */
	public function update_post_callback( $args ) {
		$post_id = intval( $args['post_id'] );

		// Check if post exists and user can edit it
		if ( ! get_post( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return array(
				'success' => false,
				'error'   => 'Post not found or insufficient permissions',
			);
		}

		$post_data = array( 'ID' => $post_id );

		if ( isset( $args['title'] ) ) {
			$post_data['post_title'] = sanitize_text_field( $args['title'] );
		}

		if ( isset( $args['content'] ) ) {
			$post_data['post_content'] = wp_kses_post( $args['content'] );
		}

		if ( isset( $args['status'] ) ) {
			$post_data['post_status'] = sanitize_text_field( $args['status'] );
		}

		$result = wp_update_post( $post_data );

		if ( is_wp_error( $result ) ) {
			return array(
				'success' => false,
				'error'   => $result->get_error_message(),
			);
		}

		return array(
			'success' => true,
			'post_id' => $post_id,
		);
	}

	/**
	 * Get the abilities registry
	 *
	 * @return AbilitiesRegistry
	 */
	public function get_registry() {
		return $this->registry;
	}
}
