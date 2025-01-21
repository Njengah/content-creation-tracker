<?php
/**
 * TimeTracker Class
 * Handles the time tracking functionality.
 */

namespace ContentCreationTracker\Core;

use ContentCreationTracker\Base\BasePlugin;

class TimeTracker extends BasePlugin {
    /**
     * Registers WordPress hooks for time tracking.
     */
    public function register() {
        $this->addAction('add_meta_boxes', [$this, 'addMetabox']);
        $this->addAction('save_post', [$this, 'saveTimeTrackingData']);
    }

    /**
     * Adds a meta box to the post edit screen.
     */
    public function addMetabox() {
        add_meta_box(
            'time_tracker_metabox',
            __('Content Time Tracker', 'content-creation-tracker'),
            [$this, 'renderMetabox'],
            'post',
            'side',
            'default'
        );
    }

    /**
     * Renders the meta box.
     *
     * @param WP_Post $post The post object.
     */
    public function renderMetabox($post) {
        // Retrieve saved data.
        $tracked_time = get_post_meta($post->ID, '_tracked_time', true);

        // Nonce for security.
        wp_nonce_field('save_time_tracking', 'time_tracking_nonce');

        echo '<label for="tracked_time">' . esc_html__('Time Spent (minutes):', 'content-creation-tracker') . '</label>';
        echo '<input type="number" id="tracked_time" name="tracked_time" value="' . esc_attr($tracked_time) . '" min="0" />';
    }

    /**
     * Saves the meta box data when the post is saved.
     *
     * @param int $post_id The post ID.
     */
    public function saveTimeTrackingData($post_id) {
        // Verify nonce.
        if (!isset($_POST['time_tracking_nonce']) || !wp_verify_nonce($_POST['time_tracking_nonce'], 'save_time_tracking')) {
            return;
        }

        // Check autosave.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check user permissions.
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Sanitize and save the data.
        if (isset($_POST['tracked_time'])) {
            $tracked_time = $this->sanitize($_POST['tracked_time'], 'intval');
            update_post_meta($post_id, '_tracked_time', $tracked_time);
        }
    }
}
