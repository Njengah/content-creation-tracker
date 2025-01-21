<?php
/**
 * Uninstall Hook for Content Creation Tracker Plugin
 *
 * This file will be triggered when the plugin is uninstalled.
 * Mets data will be deleted on this action 
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}


global $wpdb;


$meta_keys = array('_time_date', '_start_time', '_end_time');


foreach ($meta_keys as $meta_key) {
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
            $meta_key
        )
    );
}

