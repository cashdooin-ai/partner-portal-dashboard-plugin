<?php
/**
 * Referral Program Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Referrals {

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

    public static function create_referral($referrer_id, $referred_name, $referred_email, $referred_phone = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_referrals';

        // Check if email already referred
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE referred_email = %s",
            $referred_email
        ));

        if ($existing) {
            return array('success' => false, 'message' => 'This person has already been referred');
        }

        $result = $wpdb->insert($table, array(
            'referrer_id' => intval($referrer_id),
            'referred_name' => sanitize_text_field($referred_name),
            'referred_email' => sanitize_email($referred_email),
            'referred_phone' => $referred_phone ? sanitize_text_field($referred_phone) : null,
            'status' => 'pending',
        ));

        if ($result) {
            $referral_id = $wpdb->insert_id;

            // Notify admins
            $admins = get_users(array('role' => 'administrator'));
            foreach ($admins as $admin) {
                PPD_Notifications::create_notification(
                    $admin->ID,
                    'New Partner Referral',
                    'Partner referred: ' . $referred_name,
                    'referral',
                    null
                );
            }

            return array('success' => true, 'referral_id' => $referral_id);
        }

        return array('success' => false, 'message' => 'Failed to create referral');
    }

    public static function get_partner_referrals($referrer_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_referrals';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE referrer_id = %d ORDER BY created_at DESC",
            $referrer_id
        ));
    }

    public static function get_all_referrals($status = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_referrals';

        if ($status) {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE status = %s ORDER BY created_at DESC",
                $status
            ));
        } else {
            return $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");
        }
    }

    public static function approve_referral($referral_id, $referred_partner_id, $commission_amount = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_referrals';

        $result = $wpdb->update($table, array(
            'status' => 'approved',
            'referred_partner_id' => intval($referred_partner_id),
            'commission_amount' => floatval($commission_amount),
            'approved_at' => current_time('mysql'),
        ), array('id' => $referral_id));

        if ($result) {
            // Get referral details
            $referral = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $referral_id));

            // Create commission if amount > 0
            if ($commission_amount > 0) {
                PPD_Commissions::add_commission(array(
                    'partner_id' => $referral->referrer_id,
                    'amount' => $commission_amount,
                    'commission_type' => 'referral',
                    'description' => 'Referral commission for ' . $referral->referred_name,
                ));
            }

            // Notify referrer
            PPD_Notifications::create_notification(
                $referral->referrer_id,
                'Referral Approved',
                'Your referral ' . $referral->referred_name . ' has been approved!',
                'success',
                '#referrals'
            );
        }

        return $result;
    }

    public static function get_referral_stats($referrer_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_referrals';

        $total = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE referrer_id = %d",
            $referrer_id
        ));

        $approved = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE referrer_id = %d AND status = 'approved'",
            $referrer_id
        ));

        $pending = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE referrer_id = %d AND status = 'pending'",
            $referrer_id
        ));

        $total_commission = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(commission_amount) FROM $table WHERE referrer_id = %d AND status = 'approved'",
            $referrer_id
        ));

        return array(
            'total' => (int) $total,
            'approved' => (int) $approved,
            'pending' => (int) $pending,
            'total_commission' => (float) $total_commission,
        );
    }

    public static function reject_referral($referral_id, $reason = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_referrals';

        $referral = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $referral_id));

        $result = $wpdb->update($table, array('status' => 'rejected'), array('id' => $referral_id));

        if ($result && $referral) {
            PPD_Notifications::create_notification(
                $referral->referrer_id,
                'Referral Not Approved',
                'Your referral ' . $referral->referred_name . ' was not approved.',
                'info',
                '#referrals'
            );
        }

        return $result;
    }
}
