<?php

final class NFD_CLI {

	/**
	 * Returns the file path to save the site capabilities.
	 *
	 * @return string
	 */
	private static function capabilities_file_path() {
		return WP_CONTENT_DIR . '/mu-plugins/site-capability-override.php';
	}

	/**
	 * Enables and disables AI and AI Sitegen Capabilities.
	 *
	 * @when after_wp_load
	 */
	public function ai( $args ) {

		switch( $args[0] ) {
			case 'enable':
				self::enable( $args[1] );
				break;
			case 'disable':
				self::disable();
				break;
			default:
				break;
		}
	}

	/**
	 * Enable AI and AI Sitegen capability.
	 *
	 * @return null
	 */
	private static function enable( $token ) {

		if( empty( $token ) ) {
			WP_CLI::error( "Hiive token not provided. Cannot enable AI capabilities." );
			return;
		}

		$enable_ai_filter = '<?php 
		add_filter(
			"pre_transient_nfd_site_capabilities", 
			function () { 
				return [ 
					"canAccessAI" 			=> true, 
					"hasAISiteGen" 			=> true, 
				];
			}
		);';

		// create a php file that overrides the AI capabilities
		if ( file_put_contents( self::capabilities_file_path(), $enable_ai_filter ) !== false ) {
			WP_CLI::success( "AI Capabilities Enabled." );
		} else {
			WP_CLI::error( "Could not enable AI capabilities." );
		}

		// Update the hiive token in the option, it gets automatically encrypted while saving and decrypted while reading
		if ( update_option( 'nfd_data_token', $token) ) {
			WP_CLI::success( "Hiive token encrypted and saved." );
		}
	}

	/**
	 * Disable AI and AI Sitegen capability.
	 *
	 * @return null
	 */
	private static function disable() {

		// delete the php file that overrides the AI capabilities
		if ( unlink( self::capabilities_file_path() ) !== false ) {
			WP_CLI::success( "AI Capabilities Disabled." );
		} else {
			WP_CLI::error( "Could not disable AI capabilities." );
		}

		// delete the Hiive token
		if ( delete_option( 'nfd_data_token' ) ) {
			WP_CLI::success( "Hiive token deleted." );
		}

	}
}

