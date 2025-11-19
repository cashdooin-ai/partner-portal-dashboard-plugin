<?php
/**
 * Commissions Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Commissions {

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

    public static function get_partner_commissions($partner_id, $status = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_commissions';

        if ($status) {
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE partner_id = %d AND status = %s ORDER BY created_at DESC",
                $partner_id,
                $status
            ));
        } else {
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE partner_id = %d ORDER BY created_at DESC",
                $partner_id
            ));
        }

        return $results;
    }

    public static function get_all_commissions($status = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_commissions';

        if ($status) {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE status = %s ORDER BY created_at DESC",
                $status
            ));
        } else {
            return $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");
        }
    }

    public static function add_commission($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_commissions';

        $result = $wpdb->insert($table, array(
            'partner_id' => intval($data['partner_id']),
            'lead_id' => isset($data['lead_id']) ? intval($data['lead_id']) : null,
            'amount' => floatval($data['amount']),
            'commission_type' => sanitize_text_field($data['commission_type']),
            'description' => sanitize_textarea_field($data['description']),
            'status' => 'pending',
        ));

        if ($result) {
            // Create notification for partner
            PPD_Notifications::create_notification(
                $data['partner_id'],
                'New Commission Added',
                'A new commission of ₹' . number_format($data['amount'], 2) . ' has been added to your account.',
                'commission',
                null
            );
        }

        return $result;
    }

    public static function update_commission($commission_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_commissions';

        $update_data = array();

        if (isset($data['status'])) {
            $update_data['status'] = sanitize_text_field($data['status']);
        }

        if (isset($data['payment_method'])) {
            $update_data['payment_method'] = sanitize_text_field($data['payment_method']);
        }

        if (isset($data['transaction_id'])) {
            $update_data['transaction_id'] = sanitize_text_field($data['transaction_id']);
        }

        if (isset($data['status']) && $data['status'] === 'paid') {
            $update_data['paid_date'] = current_time('mysql');
        }

        if (!empty($update_data)) {
            $result = $wpdb->update($table, $update_data, array('id' => $commission_id));

            // If marked as paid, create notification
            if (isset($data['status']) && $data['status'] === 'paid') {
                $commission = self::get_commission($commission_id);
                if ($commission) {
                    PPD_Notifications::create_notification(
                        $commission->partner_id,
                        'Commission Paid',
                        'Your commission of ₹' . number_format($commission->amount, 2) . ' has been paid.',
                        'commission',
                        null
                    );
                }
            }

            return $result;
        }

        return false;
    }

    public static function get_commission($commission_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_commissions';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $commission_id
        ));
    }

    public static function get_partner_earnings_summary($partner_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_commissions';

        $total_earned = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM $table WHERE partner_id = %d AND status = 'paid'",
            $partner_id
        ));

        $pending_amount = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM $table WHERE partner_id = %d AND status = 'pending'",
            $partner_id
        ));

        $this_month = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM $table
            WHERE partner_id = %d
            AND status = 'paid'
            AND MONTH(paid_date) = MONTH(NOW())
            AND YEAR(paid_date) = YEAR(NOW())",
            $partner_id
        ));

        $total_commissions = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE partner_id = %d",
            $partner_id
        ));

        return array(
            'total_earned' => (float) $total_earned,
            'pending_amount' => (float) $pending_amount,
            'this_month' => (float) $this_month,
            'total_commissions' => (int) $total_commissions,
        );
    }

    public static function delete_commission($commission_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_commissions';

        return $wpdb->delete($table, array('id' => $commission_id));
    }

    public static function get_payment_history($partner_id, $limit = 10) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_commissions';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table
            WHERE partner_id = %d AND status = 'paid'
            ORDER BY paid_date DESC
            LIMIT %d",
            $partner_id,
            $limit
        ));
    }
}
