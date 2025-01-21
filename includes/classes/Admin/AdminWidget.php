<?php
/**
 * DashboardWidget Class
 * Handles the display of the dashboard widget for the Content Creation Tracker plugin.
 */

 namespace ContentCreationTracker\Admin;

 use ContentCreationTracker\Traits\Singleton;
 
 class AdminWidget{

     use Singleton;
     
    /**
     * Initialize the dashboard widget.
     */
    public function init() {
        add_action('wp_dashboard_setup', [$this, 'registerDashboardWidget']);
    }

    /**
     * Registers the dashboard widget.
     */
    public function registerDashboardWidget() {
        wp_add_dashboard_widget(
            'time_tracker_widget', // Widget ID
            __('Content Creation Time Tracker', 'content-creation-tracker'), // Widget title
            [$this, 'renderDashboardWidget'] // Callback for rendering the widget
        );
    }

    /**
     * Renders the content of the dashboard widget.
     */
    public function renderDashboardWidget() {
        // Query all posts with time-tracking metadata
        $args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => '_start_time',
                    'compare' => 'EXISTS',
                ],
                [
                    'key' => '_end_time',
                    'compare' => 'EXISTS',
                ],
                [
                    'key' => '_time_date',
                    'compare' => 'EXISTS',
                ],
            ],
        ];
        $query = new \WP_Query($args);

        if (!$query->have_posts()) {
            echo __('No time tracking data available.', 'content-creation-tracker');
            return;
        }

        $labels = [];
        $data = [];
        $colors = [];
        $borderColors = [];

        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $start_time = get_post_meta($post_id, '_start_time', true);
            $end_time = get_post_meta($post_id, '_end_time', true);
            $time_date = get_post_meta($post_id, '_time_date', true);

            // Calculate total time in minutes
            $start = strtotime($start_time);
            $end = strtotime($end_time);
            $time_spent = ($end - $start) / 60;

            if ($time_spent > 0) {
                $labels[] = esc_js("Post ID: $post_id ($time_date)");
                $data[] = $time_spent;
                $colors[] = 'rgba(75, 192, 192, 0.2)';
                $borderColors[] = 'rgba(75, 192, 192, 1)';
            }
        }

        wp_reset_postdata();

        ?>
        <canvas id="dashboardTimeTrackerChart" width="800" height="400"></canvas>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('dashboardTimeTrackerChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: '<?php echo esc_js(__('Time Spent (minutes)', 'content-creation-tracker')); ?>',
                        data: <?php echo json_encode($data); ?>,
                        backgroundColor: <?php echo json_encode($colors); ?>,
                        borderColor: <?php echo json_encode($borderColors); ?>,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
        <?php
    }
}
