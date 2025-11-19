<?php
/**
 * Services Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Services {

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

    public static function get_partner_services($partner_id) {
        global $wpdb;

        $services_table = $wpdb->prefix . 'ppd_services';
        $partner_services_table = $wpdb->prefix . 'ppd_partner_services';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT s.* FROM $services_table s
            INNER JOIN $partner_services_table ps ON s.id = ps.service_id
            WHERE ps.partner_id = %d AND s.status = 'active'
            ORDER BY s.name ASC",
            $partner_id
        ));

        return $results;
    }

    public static function get_all_services() {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_services';

        return $wpdb->get_results(
            "SELECT * FROM $table WHERE status = 'active' ORDER BY name ASC"
        );
    }

    public static function add_service($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_services';

        return $wpdb->insert($table, array(
            'name' => sanitize_text_field($data['name']),
            'description' => sanitize_textarea_field($data['description']),
            'price' => floatval($data['price']),
            'status' => 'active',
        ));
    }

    public static function assign_service_to_partner($partner_id, $service_id, $assigned_by = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_partner_services';

        // Check if already assigned
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE partner_id = %d AND service_id = %d",
            $partner_id,
            $service_id
        ));

        if ($exists) {
            return false;
        }

        return $wpdb->insert($table, array(
            'partner_id' => $partner_id,
            'service_id' => $service_id,
            'assigned_by' => $assigned_by ? $assigned_by : get_current_user_id(),
        ));
    }

    public static function remove_service_from_partner($partner_id, $service_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_partner_services';

        return $wpdb->delete($table, array(
            'partner_id' => $partner_id,
            'service_id' => $service_id,
        ));
    }
}
