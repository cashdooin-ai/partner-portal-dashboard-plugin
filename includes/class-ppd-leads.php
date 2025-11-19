<?php
/**
 * Leads Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Leads {

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

    public static function get_partner_leads($partner_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_leads';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE partner_id = %d ORDER BY created_at DESC",
            $partner_id
        ));
    }

    public static function get_all_leads() {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_leads';

        return $wpdb->get_results(
            "SELECT * FROM $table ORDER BY created_at DESC"
        );
    }

    public static function add_lead($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_leads';

        return $wpdb->insert($table, array(
            'partner_id' => intval($data['partner_id']),
            'student_name' => sanitize_text_field($data['student_name']),
            'student_email' => sanitize_email($data['student_email']),
            'student_phone' => sanitize_text_field($data['student_phone']),
            'college_id' => isset($data['college_id']) ? intval($data['college_id']) : null,
            'service_id' => isset($data['service_id']) ? intval($data['service_id']) : null,
            'notes' => sanitize_textarea_field($data['notes']),
            'status' => 'new',
        ));
    }

    public static function update_lead($lead_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_leads';

        $update_data = array();

        if (isset($data['status'])) {
            $update_data['status'] = sanitize_text_field($data['status']);
        }

        if (isset($data['admin_notes'])) {
            $update_data['admin_notes'] = sanitize_textarea_field($data['admin_notes']);
        }

        if (isset($data['notes'])) {
            $update_data['notes'] = sanitize_textarea_field($data['notes']);
        }

        if (!empty($update_data)) {
            return $wpdb->update($table, $update_data, array('id' => $lead_id));
        }

        return false;
    }

    public static function delete_lead($lead_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_leads';

        return $wpdb->delete($table, array('id' => $lead_id));
    }

    public static function get_lead($lead_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_leads';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $lead_id
        ));
    }
}
