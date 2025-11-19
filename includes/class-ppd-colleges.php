<?php
/**
 * Colleges Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Colleges {

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

    public static function get_partner_colleges($partner_id) {
        global $wpdb;

        $colleges_table = $wpdb->prefix . 'ppd_colleges';
        $partner_colleges_table = $wpdb->prefix . 'ppd_partner_colleges';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT c.* FROM $colleges_table c
            INNER JOIN $partner_colleges_table pc ON c.id = pc.college_id
            WHERE pc.partner_id = %d AND c.status = 'active'
            ORDER BY c.name ASC",
            $partner_id
        ));

        return $results;
    }

    public static function get_all_colleges() {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_colleges';

        return $wpdb->get_results(
            "SELECT * FROM $table WHERE status = 'active' ORDER BY name ASC"
        );
    }

    public static function add_college($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_colleges';

        return $wpdb->insert($table, array(
            'name' => sanitize_text_field($data['name']),
            'location' => sanitize_text_field($data['location']),
            'city' => sanitize_text_field($data['city']),
            'state' => sanitize_text_field($data['state']),
            'website' => esc_url_raw($data['website']),
            'contact_person' => sanitize_text_field($data['contact_person']),
            'contact_email' => sanitize_email($data['contact_email']),
            'contact_phone' => sanitize_text_field($data['contact_phone']),
            'description' => sanitize_textarea_field($data['description']),
            'status' => 'active',
        ));
    }

    public static function assign_college_to_partner($partner_id, $college_id, $assigned_by = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_partner_colleges';

        // Check if already assigned
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE partner_id = %d AND college_id = %d",
            $partner_id,
            $college_id
        ));

        if ($exists) {
            return false;
        }

        return $wpdb->insert($table, array(
            'partner_id' => $partner_id,
            'college_id' => $college_id,
            'assigned_by' => $assigned_by ? $assigned_by : get_current_user_id(),
        ));
    }

    public static function remove_college_from_partner($partner_id, $college_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_partner_colleges';

        return $wpdb->delete($table, array(
            'partner_id' => $partner_id,
            'college_id' => $college_id,
        ));
    }
}
