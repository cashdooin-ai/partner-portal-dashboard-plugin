<?php
/**
 * Performance Metrics & Targets Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Performance {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Constructor
    }

    public static function set_target($partner_id, $target_type, $target_value, $period_start, $period_end) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_targets';

        return $wpdb->insert($table, array(
            'partner_id' => intval($partner_id),
            'target_type' => sanitize_text_field($target_type),
            'target_value' => floatval($target_value),
            'period_start' => sanitize_text_field($period_start),
            'period_end' => sanitize_text_field($period_end),
        ));
    }

    public static function get_partner_targets($partner_id, $active_only = true) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_targets';

        if ($active_only) {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table
                WHERE partner_id = %d
                AND period_end >= CURDATE()
                ORDER BY period_start DESC",
                $partner_id
            ));
        } else {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE partner_id = %d ORDER BY period_start DESC",
                $partner_id
            ));
        }
    }

    public static function update_target_achievement($target_id, $achieved_value) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_targets';

        return $wpdb->update($table, array('achieved_value' => floatval($achieved_value)), array('id' => $target_id));
    }

    public static function get_leaderboard($period = 'month', $limit = 10) {
        global $wpdb;
        $leads_table = $wpdb->prefix . 'ppd_leads';

        $date_condition = '';
        switch ($period) {
            case 'week':
                $date_condition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $date_condition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            case 'quarter':
                $date_condition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
                break;
            case 'year':
                $date_condition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 365 DAY)";
                break;
        }

        return $wpdb->get_results($wpdb->prepare(
            "SELECT
                partner_id,
                COUNT(*) as total_leads,
                SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as converted_leads
            FROM $leads_table
            WHERE 1=1 $date_condition
            GROUP BY partner_id
            ORDER BY converted_leads DESC, total_leads DESC
            LIMIT %d",
            $limit
        ));
    }

    public static function get_partner_rank($partner_id, $period = 'month') {
        $leaderboard = self::get_leaderboard($period, 100);

        $rank = 0;
        foreach ($leaderboard as $index => $entry) {
            if ($entry->partner_id == $partner_id) {
                $rank = $index + 1;
                break;
            }
        }

        return $rank;
    }

    public static function get_success_rate_by_college($partner_id) {
        global $wpdb;
        $leads_table = $wpdb->prefix . 'ppd_leads';
        $colleges_table = $wpdb->prefix . 'ppd_colleges';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT
                c.name as college_name,
                COUNT(l.id) as total_leads,
                SUM(CASE WHEN l.status = 'converted' THEN 1 ELSE 0 END) as converted_leads,
                ROUND((SUM(CASE WHEN l.status = 'converted' THEN 1 ELSE 0 END) / COUNT(l.id)) * 100, 2) as success_rate
            FROM $leads_table l
            LEFT JOIN $colleges_table c ON l.college_id = c.id
            WHERE l.partner_id = %d AND l.college_id IS NOT NULL
            GROUP BY l.college_id
            ORDER BY success_rate DESC",
            $partner_id
        ));
    }

    public static function get_response_time_metrics($partner_id) {
        global $wpdb;
        $leads_table = $wpdb->prefix . 'ppd_leads';

        // This is simplified - in production, you'd track actual response times
        $avg_days_to_convert = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(DATEDIFF(updated_at, created_at)) as avg_days
            FROM $leads_table
            WHERE partner_id = %d AND status = 'converted'",
            $partner_id
        ));

        return array(
            'avg_days_to_convert' => round($avg_days_to_convert, 1),
        );
    }
}
