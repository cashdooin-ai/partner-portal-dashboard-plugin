<?php
/**
 * Activity Timeline & Logging Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Activity {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Hook into WordPress actions to log activities
        add_action('wp_login', array($this, 'log_login'), 10, 2);
    }

    public static function log_activity($user_id, $action, $description = null, $entity_type = null, $entity_id = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_activity_log';

        return $wpdb->insert($table, array(
            'user_id' => intval($user_id),
            'action' => sanitize_text_field($action),
            'description' => $description ? sanitize_textarea_field($description) : null,
            'entity_type' => $entity_type ? sanitize_text_field($entity_type) : null,
            'entity_id' => $entity_id ? intval($entity_id) : null,
            'ip_address' => self::get_client_ip(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 500) : null,
        ));
    }

    public static function get_user_activities($user_id, $limit = 50) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_activity_log';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC LIMIT %d",
            $user_id, $limit
        ));
    }

    public static function get_recent_activities($user_id, $days = 7) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_activity_log';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table
            WHERE user_id = %d
            AND created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)
            ORDER BY created_at DESC",
            $user_id, $days
        ));
    }

    public static function get_login_history($user_id, $limit = 20) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_activity_log';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table
            WHERE user_id = %d AND action = 'login'
            ORDER BY created_at DESC
            LIMIT %d",
            $user_id, $limit
        ));
    }

    public function log_login($user_login, $user) {
        if (in_array('partner', $user->roles) || in_array('administrator', $user->roles)) {
            self::log_activity($user->ID, 'login', 'User logged in');
        }
    }

    private static function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');

        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
    }

    public static function get_activity_summary($user_id, $period = 'week') {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_activity_log';

        $date_condition = '';
        switch ($period) {
            case 'today':
                $date_condition = "AND DATE(created_at) = CURDATE()";
                break;
            case 'week':
                $date_condition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $date_condition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
        }

        return $wpdb->get_results($wpdb->prepare(
            "SELECT action, COUNT(*) as count
            FROM $table
            WHERE user_id = %d $date_condition
            GROUP BY action
            ORDER BY count DESC",
            $user_id
        ));
    }

    public static function clean_old_logs($days = 90) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_activity_log';

        return $wpdb->query($wpdb->prepare(
            "DELETE FROM $table WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ));
    }
}
