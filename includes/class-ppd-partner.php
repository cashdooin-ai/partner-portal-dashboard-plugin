<?php
/**
 * Partner Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Partner {

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

    public static function get_partner_data($user_id) {
        $user = get_userdata($user_id);
        if (!$user) {
            return false;
        }

        $partner_meta = PPD_Database::get_partner_meta($user_id);

        return array(
            'id' => $user_id,
            'name' => $user->display_name,
            'email' => $user->user_email,
            'username' => $user->user_login,
            'company_name' => $partner_meta ? $partner_meta->company_name : '',
            'phone' => $partner_meta ? $partner_meta->phone : '',
            'address' => $partner_meta ? $partner_meta->address : '',
            'city' => $partner_meta ? $partner_meta->city : '',
            'state' => $partner_meta ? $partner_meta->state : '',
            'country' => $partner_meta ? $partner_meta->country : '',
            'pincode' => $partner_meta ? $partner_meta->pincode : '',
            'website' => $partner_meta ? $partner_meta->website : '',
            'description' => $partner_meta ? $partner_meta->description : '',
            'status' => $partner_meta ? $partner_meta->status : 'pending',
            'joined_date' => $partner_meta ? $partner_meta->joined_date : '',
        );
    }

    public static function update_partner_profile($user_id, $data) {
        // Update WordPress user data
        $user_data = array(
            'ID' => $user_id,
        );

        if (isset($data['display_name'])) {
            $user_data['display_name'] = sanitize_text_field($data['display_name']);
        }

        if (isset($data['user_email'])) {
            $user_data['user_email'] = sanitize_email($data['user_email']);
        }

        if (!empty($user_data)) {
            wp_update_user($user_data);
        }

        // Update partner meta
        $meta_data = array();

        if (isset($data['company_name'])) {
            $meta_data['company_name'] = sanitize_text_field($data['company_name']);
        }

        if (isset($data['phone'])) {
            $meta_data['phone'] = sanitize_text_field($data['phone']);
        }

        if (isset($data['address'])) {
            $meta_data['address'] = sanitize_textarea_field($data['address']);
        }

        if (isset($data['city'])) {
            $meta_data['city'] = sanitize_text_field($data['city']);
        }

        if (isset($data['state'])) {
            $meta_data['state'] = sanitize_text_field($data['state']);
        }

        if (isset($data['country'])) {
            $meta_data['country'] = sanitize_text_field($data['country']);
        }

        if (isset($data['pincode'])) {
            $meta_data['pincode'] = sanitize_text_field($data['pincode']);
        }

        if (isset($data['website'])) {
            $meta_data['website'] = esc_url_raw($data['website']);
        }

        if (isset($data['description'])) {
            $meta_data['description'] = sanitize_textarea_field($data['description']);
        }

        if (!empty($meta_data)) {
            PPD_Database::update_partner_meta($user_id, $meta_data);
        }

        return true;
    }

    public static function get_all_partners() {
        $args = array(
            'role' => 'partner',
            'orderby' => 'registered',
            'order' => 'DESC',
        );

        $users = get_users($args);
        $partners = array();

        foreach ($users as $user) {
            $partners[] = self::get_partner_data($user->ID);
        }

        return $partners;
    }

    public static function get_partner_stats($user_id) {
        global $wpdb;

        // Get colleges count
        $colleges_table = $wpdb->prefix . 'ppd_partner_colleges';
        $colleges_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $colleges_table WHERE partner_id = %d",
            $user_id
        ));

        // Get services count
        $services_table = $wpdb->prefix . 'ppd_partner_services';
        $services_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $services_table WHERE partner_id = %d",
            $user_id
        ));

        // Get tasks count
        $tasks_table = $wpdb->prefix . 'ppd_tasks';
        $pending_tasks = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $tasks_table WHERE partner_id = %d AND status = 'pending'",
            $user_id
        ));

        $completed_tasks = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $tasks_table WHERE partner_id = %d AND status = 'completed'",
            $user_id
        ));

        // Get leads count
        $leads_table = $wpdb->prefix . 'ppd_leads';
        $leads_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $leads_table WHERE partner_id = %d",
            $user_id
        ));

        return array(
            'colleges_count' => (int) $colleges_count,
            'services_count' => (int) $services_count,
            'pending_tasks' => (int) $pending_tasks,
            'completed_tasks' => (int) $completed_tasks,
            'total_tasks' => (int) ($pending_tasks + $completed_tasks),
            'leads_count' => (int) $leads_count,
        );
    }
}
