<?php
/**
 * Main Class
 * Entry point for the plugin.
 */

namespace ContentCreationTracker;

use ContentCreationTracker\Base\BasePlugin;
use ContentCreationTracker\Traits\Singleton;
use ContentCreationTracker\Core\TimeTracker;
use ContentCreationTracker\Admin\AdminWidget;

class Main {
    use Singleton;

    /**
     * Initializes the plugin by registering components.
     */
    public function init() {
        // Register the TimeTracker component
        (new TimeTracker())->register();

        // Initialize the DashboardWidget using the Singleton pattern
        AdminWidget::getInstance()->init();
    }
}
