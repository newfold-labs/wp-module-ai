<?php
/**
 * Bootstrap file for wpunit tests.
 *
 * @package NewfoldLabs\WP\Module\AI
 */

$module_root = dirname( dirname( __DIR__ ) );

require_once $module_root . '/vendor/autoload.php';

// Constants normally set in bootstrap on plugins_loaded; required for AI::load_text_domain etc.
if ( ! defined( 'NFD_MODULE_AI_DIR' ) ) {
	define( 'NFD_MODULE_AI_DIR', $module_root );
}
