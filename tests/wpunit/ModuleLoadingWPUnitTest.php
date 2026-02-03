<?php

namespace NewfoldLabs\WP\Module\AI;

use NewfoldLabs\WP\Module\AI\RestApi\AISearchController;
use NewfoldLabs\WP\Module\AI\Utils\AISearchUtil;
use NewfoldLabs\WP\Module\AI\Utils\PatternParser;

/**
 * Module loading wpunit tests.
 *
 * @coversDefaultClass \NewfoldLabs\WP\Module\AI\AI
 */
class ModuleLoadingWPUnitTest extends \lucatume\WPBrowser\TestCase\WPTestCase {

	/**
	 * Verify core module classes exist.
	 *
	 * @return void
	 */
	public function test_module_classes_load() {
		$this->assertTrue( class_exists( AI::class ) );
		$this->assertTrue( class_exists( Patterns::class ) );
		$this->assertTrue( class_exists( AISearchController::class ) );
		$this->assertTrue( class_exists( AISearchUtil::class ) );
		$this->assertTrue( class_exists( PatternParser::class ) );
		$this->assertTrue( class_exists( SiteGen\SiteGen::class ) );
	}

	/**
	 * Verify WordPress factory is available.
	 *
	 * @return void
	 */
	public function test_wordpress_factory_available() {
		$this->assertTrue( function_exists( 'get_option' ) );
		$this->assertNotEmpty( get_option( 'blogname' ) );
	}

	/**
	 * NFD_MODULE_AI_DIR constant is defined by bootstrap.
	 *
	 * @return void
	 */
	public function test_nfd_module_ai_dir_defined() {
		$this->assertTrue( defined( 'NFD_MODULE_AI_DIR' ) );
		$this->assertNotEmpty( NFD_MODULE_AI_DIR );
	}
}
