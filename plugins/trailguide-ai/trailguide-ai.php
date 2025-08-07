
<?php
/**
 * Plugin Name: Trailguide AI
 * Description: Adds AI Q&A and helper tools for travel guides. Provides a settings page for API key and a REST endpoint.
 * Version: 0.1.0
 * Requires at least: 6.5
 * Requires PHP: 8.0
 * Author: You
 * License: GPL2+
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define('TRAILGUIDE_AI_VER', '0.1.0');
define('TRAILGUIDE_AI_DIR', plugin_dir_path(__FILE__));

require_once TRAILGUIDE_AI_DIR . 'admin/settings.php';
require_once TRAILGUIDE_AI_DIR . 'includes/rest-endpoints.php';
require_once TRAILGUIDE_AI_DIR . 'blocks/ai-qa/block.php';

// Minimal styles for the Q&A block
add_action('wp_enqueue_scripts', function(){
  wp_register_style('trailguide-ai', plugins_url('assets/ai.css', __FILE__), [], TRAILGUIDE_AI_VER);
});
