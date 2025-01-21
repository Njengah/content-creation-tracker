<?php
/**
 * Plugin Name: Content Creation Tracker
 * Description: Tracks time spent on content creation and helps improve efficiency.
 * Version: 1.0
 * Author: Joe Njenga
 * Text Domain: content-creation-tracker
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/includes/traits/Singleton.php';
require_once __DIR__ . '/includes/base/BasePlugin.php';
require_once __DIR__ . '/includes/classes/Core/TimeTracker.php';
require_once __DIR__ . '/includes/classes/Main.php';

use ContentCreationTracker\Main;

function content_creation_tracker_init() {
    Main::getInstance()->init();
}
add_action('plugins_loaded', 'content_creation_tracker_init');
