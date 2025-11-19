<?php
/**
 * Analytics Handler Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Analytics {

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

    public static function get_partner_analytics($partner_id, $period = 'month') {
        global $wpdb;

        $leads_table = $wpdb->prefix . 'ppd_leads';
        $commissions_table = $wpdb->prefix . 'ppd_commissions';
        $tasks_table = $wpdb->prefix . 'ppd_tasks';

        // Date range based on period
        $date_condition = self::get_date_condition($period);

        // Lead Statistics
        $total_leads = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $leads_table WHERE partner_id = %d",
            $partner_id
        ));

        $period_leads = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $leads_table WHERE partner_id = %d $date_condition",
            $partner_id
        ));

        $converted_leads = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $leads_table WHERE partner_id = %d AND status = 'converted'",
            $partner_id
        ));

        $period_converted = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $leads_table WHERE partner_id = %d AND status = 'converted' $date_condition",
            $partner_id
        ));

        // Conversion Rate
        $conversion_rate = $total_leads > 0 ? round(($converted_leads / $total_leads) * 100, 2) : 0;
        $period_conversion_rate = $period_leads > 0 ? round(($period_converted / $period_leads) * 100, 2) : 0;

        // Lead Status Breakdown
        $status_breakdown = $wpdb->get_results($wpdb->prepare(
            "SELECT status, COUNT(*) as count FROM $leads_table
            WHERE partner_id = %d
            GROUP BY status",
            $partner_id
        ), ARRAY_A);

        // Monthly Trend (last 6 months)
        $monthly_data = self::get_monthly_trend($partner_id);

        // Commission Statistics
        $total_earnings = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM $commissions_table WHERE partner_id = %d AND status = 'paid'",
            $partner_id
        ));

        $pending_earnings = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM $commissions_table WHERE partner_id = %d AND status = 'pending'",
            $partner_id
        ));

        $period_earnings = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM $commissions_table WHERE partner_id = %d AND status = 'paid' $date_condition",
            $partner_id
        ));

        // Task Completion Rate
        $total_tasks = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $tasks_table WHERE partner_id = %d",
            $partner_id
        ));

        $completed_tasks = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $tasks_table WHERE partner_id = %d AND status = 'completed'",
            $partner_id
        ));

        $task_completion_rate = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100, 2) : 0;

        // Lead Source Analytics
        $lead_by_college = $wpdb->get_results($wpdb->prepare(
            "SELECT c.name as college_name, COUNT(l.id) as count
            FROM $leads_table l
            LEFT JOIN {$wpdb->prefix}ppd_colleges c ON l.college_id = c.id
            WHERE l.partner_id = %d AND l.college_id IS NOT NULL
            GROUP BY l.college_id
            ORDER BY count DESC
            LIMIT 5",
            $partner_id
        ), ARRAY_A);

        return array(
            'total_leads' => (int) $total_leads,
            'period_leads' => (int) $period_leads,
            'converted_leads' => (int) $converted_leads,
            'period_converted' => (int) $period_converted,
            'conversion_rate' => $conversion_rate,
            'period_conversion_rate' => $period_conversion_rate,
            'status_breakdown' => $status_breakdown,
            'monthly_trend' => $monthly_data,
            'total_earnings' => (float) $total_earnings,
            'pending_earnings' => (float) $pending_earnings,
            'period_earnings' => (float) $period_earnings,
            'task_completion_rate' => $task_completion_rate,
            'lead_by_college' => $lead_by_college,
        );
    }

    private static function get_date_condition($period) {
        switch ($period) {
            case 'today':
                return "AND DATE(created_at) = CURDATE()";
            case 'week':
                return "AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            case 'month':
                return "AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            case 'quarter':
                return "AND created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
            case 'year':
                return "AND created_at >= DATE_SUB(NOW(), INTERVAL 365 DAY)";
            default:
                return "AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }
    }

    private static function get_monthly_trend($partner_id) {
        global $wpdb;
        $leads_table = $wpdb->prefix . 'ppd_leads';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT
                DATE_FORMAT(created_at, '%%Y-%%m') as month,
                COUNT(*) as total_leads,
                SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as converted_leads
            FROM $leads_table
            WHERE partner_id = %d
            AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%%Y-%%m')
            ORDER BY month ASC",
            $partner_id
        ), ARRAY_A);

        return $results;
    }

    public static function get_comparison_data($partner_id) {
        global $wpdb;
        $leads_table = $wpdb->prefix . 'ppd_leads';

        // Current month
        $current_month = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $leads_table
            WHERE partner_id = %d
            AND MONTH(created_at) = MONTH(NOW())
            AND YEAR(created_at) = YEAR(NOW())",
            $partner_id
        ));

        // Last month
        $last_month = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $leads_table
            WHERE partner_id = %d
            AND MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
            AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))",
            $partner_id
        ));

        $percentage_change = $last_month > 0 ? round((($current_month - $last_month) / $last_month) * 100, 2) : 0;

        return array(
            'current_month' => (int) $current_month,
            'last_month' => (int) $last_month,
            'percentage_change' => $percentage_change,
            'trend' => $percentage_change >= 0 ? 'up' : 'down'
        );
    }
}
