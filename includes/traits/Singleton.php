<?php
/**
 * Singleton Trait
 * Ensures a class has only one instance.
 */

namespace ContentCreationTracker\Traits;

trait Singleton {
    private static $instance = null;

    /**
     * Gets the instance of the class.
     *
     * @return static
     */
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Prevent direct instantiation.
    private function __construct() {}

    // Prevent cloning.
    private function __clone() {}

    // Prevent unserialization.
    private function __wakeup() {}
}
