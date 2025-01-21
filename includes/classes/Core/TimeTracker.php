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
        $this->addAction('save_post', [$this, 'save_time_tracker_metabox']);
        $this->addAction('manage_posts_custom_column', [$this, 'populate_custom_columns'], 10, 2);
        $this->addAction('manage_posts_columns', [$this, 'add_custom_columns']);
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
        $start_time = get_post_meta($post->ID, '_start_time', true);
        $end_time = get_post_meta($post->ID, '_end_time', true);
        $time_date = get_post_meta($post->ID, '_time_date', true);
    
        // Calculate the time difference if both start and end times are set
        $time_difference = '';
        if ($start_time && $end_time) {
            $start_timestamp = strtotime($start_time);
            $end_timestamp = strtotime($end_time);
            if ($start_timestamp && $end_timestamp) {
                $time_diff = $end_timestamp - $start_timestamp;
                $hours = floor($time_diff / 3600);
                $minutes = floor(($time_diff % 3600) / 60);
                $time_difference = sprintf('%02d:%02d', $hours, $minutes);
            }
        }
    
        // Calculate the word count
        $content = $post->post_content;
        $word_count = str_word_count(strip_tags($content));
    
        wp_nonce_field('save_time_tracker', 'time_tracker_nonce');
        ?>
        <p>
            <label for="time_date">Date:</label>
            <input type="date" id="time_date" name="time_date" value="<?php echo esc_attr($time_date); ?>" />
        </p>
        <p>
            <label for="start_time">Start Time:</label>
            <input type="time" id="start_time" name="start_time" value="<?php echo esc_attr($start_time); ?>" />
        </p>
        <p>
            <label for="end_time">End Time:</label>
            <input type="time" id="end_time" name="end_time" value="<?php echo esc_attr($end_time); ?>" />
        </p>
        <?php if ($time_difference): ?>
            <p><strong>Time Spent:</strong> <?php echo esc_html($time_difference); ?></p>
        <?php endif; ?>
        <p><strong>Total Words:</strong> <?php echo esc_html($word_count); ?></p>
    <?php
    }

    /**
     * Saves the meta box data when the post is saved.
     *
     * @param int $post_id The post ID.
     */
    public function save_time_tracker_metabox($post_id) {
        if (!isset($_POST['time_tracker_nonce']) || !wp_verify_nonce($_POST['time_tracker_nonce'], 'save_time_tracker')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        if (isset($_POST['time_date'])) {
            update_post_meta($post_id, '_time_date', sanitize_text_field($_POST['time_date']));
        }
        if (isset($_POST['start_time'])) {
            update_post_meta($post_id, '_start_time', sanitize_text_field($_POST['start_time']));
        }
        if (isset($_POST['end_time'])) {
            update_post_meta($post_id, '_end_time', sanitize_text_field($_POST['end_time']));
        }
    }


    // Add custom columns to the Posts table
        public function add_custom_columns($columns) {
            $columns['start_time'] = __('Start Time', 'content-creation-tracker');
            $columns['end_time']   = __('End Time', 'content-creation-tracker');
            $columns['total_time'] = __('Total Time', 'content-creation-tracker');
            $columns['word_count'] = __('Word Count', 'content-creation-tracker');

            return $columns;
        }
       
       // Populate custom columns with meta data

        public function populate_custom_columns($column_name, $post_id) {
            switch ($column_name) {
                case 'start_time':
                    
                    $start_time = get_post_meta($post_id, '_start_time', true);
                    echo $start_time ? esc_html($start_time) : __('N/A', 'content-creation-tracker');
                    break;

                case 'end_time':
                    
                    $end_time = get_post_meta($post_id, '_end_time', true);
                    echo $end_time ? esc_html($end_time) : __('N/A', 'content-creation-tracker');
                    break;

                case 'total_time':
                    
                    $start_time = get_post_meta($post_id, '_start_time', true);
                    $end_time = get_post_meta($post_id, '_end_time', true);

                    if ($start_time && $end_time) {
                        $start_timestamp = strtotime($start_time);
                        $end_timestamp = strtotime($end_time);
                        $total_time = $end_timestamp - $start_timestamp;

                       
                        $hours = floor($total_time / 3600);
                        $minutes = floor(($total_time % 3600) / 60);
                        $seconds = $total_time % 60;

                        echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds); 
                    } else {
                        echo __('N/A', 'content-creation-tracker');
                    }
                    break;

                case 'word_count':
                    $post_content = get_post_field('post_content', $post_id);
                    $word_count = str_word_count(strip_tags($post_content));
                    echo $word_count ? $word_count : __('N/A', 'content-creation-tracker');
                    break;
            }
        }




}
