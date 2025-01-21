<?php
/**
 * Main Class
 * Entry point for the plugin.
 */

namespace ContentCreationTracker;

use ContentCreationTracker\Base\BasePlugin;
use ContentCreationTracker\Traits\Singleton;
use ContentCreationTracker\Core\TimeTracker;

class Main {
    use Singleton;

    /**
     * Initializes the plugin by registering components.
     */
    public function init() {
        (new TimeTracker())->register();
    }
}
