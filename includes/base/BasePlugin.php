<?php
/**
 * BasePlugin Class
 * Provides core functionality for other plugin classes.
 */

namespace ContentCreationTracker\Base;

abstract class BasePlugin {
    /**
     * Registers WordPress hooks for the plugin.
     * Every extending class must implement this method.
     */
    abstract public function register();

    /**
     * Adds a WordPress action hook.
     *
     * @param string $hook Hook name.
     * @param callable $callback Callback function.
     * @param int $priority Priority of the action.
     * @param int $acceptedArgs Number of accepted arguments.
     */
    protected function addAction($hook, $callback, $priority = 10, $acceptedArgs = 1) {
        add_action($hook, $callback, $priority, $acceptedArgs);
    }

    /**
     * Adds a WordPress filter hook.
     *
     * @param string $hook Hook name.
     * @param callable $callback Callback function.
     * @param int $priority Priority of the filter.
     * @param int $acceptedArgs Number of accepted arguments.
     */
    protected function addFilter($hook, $callback, $priority = 10, $acceptedArgs = 1) {
        add_filter($hook, $callback, $priority, $acceptedArgs);
    }

    /**
     * Sanitizes data using a callback.
     *
     * @param mixed $data Data to sanitize.
     * @param callable $callback Sanitization callback.
     * @return mixed Sanitized data.
     */
    protected function sanitize($data, $callback) {
        return call_user_func($callback, $data);
    }
}
