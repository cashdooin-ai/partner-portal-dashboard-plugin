<?php
/**
 * AJAX Handler Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Ajax {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Partner actions
        add_action('wp_ajax_ppd_update_profile', array($this, 'update_profile'));
        add_action('wp_ajax_ppd_add_lead', array($this, 'add_lead'));
        add_action('wp_ajax_ppd_mark_task_complete', array($this, 'mark_task_complete'));
        add_action('wp_ajax_ppd_update_google_sheets', array($this, 'update_google_sheets'));
        add_action('wp_ajax_ppd_sync_google_sheets', array($this, 'sync_google_sheets'));
        add_action('wp_ajax_ppd_submit_application', array($this, 'submit_application'));
        add_action('wp_ajax_nopriv_ppd_submit_application', array($this, 'submit_application'));

        // Admin actions
        add_action('wp_ajax_ppd_admin_add_college', array($this, 'admin_add_college'));
        add_action('wp_ajax_ppd_admin_assign_college', array($this, 'admin_assign_college'));
        add_action('wp_ajax_ppd_admin_add_service', array($this, 'admin_add_service'));
        add_action('wp_ajax_ppd_admin_assign_service', array($this, 'admin_assign_service'));
        add_action('wp_ajax_ppd_admin_add_task', array($this, 'admin_add_task'));
        add_action('wp_ajax_ppd_admin_update_lead', array($this, 'admin_update_lead'));
        add_action('wp_ajax_ppd_admin_approve_application', array($this, 'admin_approve_application'));
    }

    public function update_profile() {
        check_ajax_referer('ppd_nonce', 'nonce');

        if (!PPD_Auth::is_partner_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $partner_id = PPD_Auth::get_current_partner_id();

        PPD_Partner::update_partner_profile($partner_id, $_POST);

        wp_send_json_success(array('message' => 'Profile updated successfully'));
    }

    public function add_lead() {
        check_ajax_referer('ppd_nonce', 'nonce');

        if (!PPD_Auth::is_partner_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $partner_id = PPD_Auth::get_current_partner_id();

        $data = array(
            'partner_id' => $partner_id,
            'student_name' => sanitize_text_field($_POST['student_name']),
            'student_email' => sanitize_email($_POST['student_email']),
            'student_phone' => sanitize_text_field($_POST['student_phone']),
            'notes' => sanitize_textarea_field($_POST['notes']),
        );

        $result = PPD_Leads::add_lead($data);

        if ($result) {
            wp_send_json_success(array('message' => 'Lead added successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to add lead'));
        }
    }

    public function mark_task_complete() {
        check_ajax_referer('ppd_nonce', 'nonce');

        if (!PPD_Auth::is_partner_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $task_id = intval($_POST['task_id']);

        $result = PPD_Tasks::update_task_status($task_id, 'completed');

        if ($result) {
            wp_send_json_success(array('message' => 'Task marked as complete'));
        } else {
            wp_send_json_error(array('message' => 'Failed to update task'));
        }
    }

    public function update_google_sheets() {
        check_ajax_referer('ppd_nonce', 'nonce');

        if (!PPD_Auth::is_partner_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $partner_id = PPD_Auth::get_current_partner_id();

        $data = array(
            'sheet_id' => sanitize_text_field($_POST['sheet_id']),
            'sheet_name' => sanitize_text_field($_POST['sheet_name']),
            'sync_enabled' => isset($_POST['sync_enabled']) ? 1 : 0,
        );

        $result = PPD_Google_Sheets::update_config($partner_id, $data);

        if ($result !== false) {
            wp_send_json_success(array('message' => 'Google Sheets settings updated'));
        } else {
            wp_send_json_error(array('message' => 'Failed to update settings'));
        }
    }

    public function sync_google_sheets() {
        check_ajax_referer('ppd_nonce', 'nonce');

        if (!PPD_Auth::is_partner_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $partner_id = PPD_Auth::get_current_partner_id();

        $result = PPD_Google_Sheets::sync_leads_to_sheet($partner_id);

        wp_send_json($result);
    }

    public function submit_application() {
        check_ajax_referer('ppd_application', 'nonce');

        global $wpdb;
        $table = $wpdb->prefix . 'ppd_applications';

        $data = array(
            'name' => sanitize_text_field($_POST['applicant_name']),
            'email' => sanitize_email($_POST['applicant_email']),
            'phone' => sanitize_text_field($_POST['applicant_phone']),
            'company_name' => sanitize_text_field($_POST['applicant_company']),
            'message' => sanitize_textarea_field($_POST['applicant_message']),
            'status' => 'pending',
        );

        $result = $wpdb->insert($table, $data);

        if ($result) {
            wp_send_json_success(array('message' => 'Application submitted successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to submit application'));
        }
    }

    // Admin AJAX actions
    public function admin_add_college() {
        check_ajax_referer('ppd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $result = PPD_Colleges::add_college($_POST);

        if ($result) {
            wp_send_json_success(array('message' => 'College added successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to add college'));
        }
    }

    public function admin_assign_college() {
        check_ajax_referer('ppd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $partner_id = intval($_POST['partner_id']);
        $college_id = intval($_POST['college_id']);

        $result = PPD_Colleges::assign_college_to_partner($partner_id, $college_id);

        if ($result) {
            wp_send_json_success(array('message' => 'College assigned successfully'));
        } else {
            wp_send_json_error(array('message' => 'College already assigned or failed'));
        }
    }

    public function admin_add_service() {
        check_ajax_referer('ppd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $result = PPD_Services::add_service($_POST);

        if ($result) {
            wp_send_json_success(array('message' => 'Service added successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to add service'));
        }
    }

    public function admin_assign_service() {
        check_ajax_referer('ppd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $partner_id = intval($_POST['partner_id']);
        $service_id = intval($_POST['service_id']);

        $result = PPD_Services::assign_service_to_partner($partner_id, $service_id);

        if ($result) {
            wp_send_json_success(array('message' => 'Service assigned successfully'));
        } else {
            wp_send_json_error(array('message' => 'Service already assigned or failed'));
        }
    }

    public function admin_add_task() {
        check_ajax_referer('ppd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $result = PPD_Tasks::add_task($_POST);

        if ($result) {
            wp_send_json_success(array('message' => 'Task added successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to add task'));
        }
    }

    public function admin_update_lead() {
        check_ajax_referer('ppd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $lead_id = intval($_POST['lead_id']);
        $data = array(
            'status' => sanitize_text_field($_POST['status']),
            'admin_notes' => sanitize_textarea_field($_POST['admin_notes']),
        );

        $result = PPD_Leads::update_lead($lead_id, $data);

        if ($result !== false) {
            wp_send_json_success(array('message' => 'Lead updated successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to update lead'));
        }
    }

    public function admin_approve_application() {
        check_ajax_referer('ppd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        global $wpdb;
        $application_id = intval($_POST['application_id']);
        $applications_table = $wpdb->prefix . 'ppd_applications';

        $application = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $applications_table WHERE id = %d",
            $application_id
        ));

        if (!$application) {
            wp_send_json_error(array('message' => 'Application not found'));
        }

        // Create WordPress user
        $user_id = wp_create_user(
            sanitize_user($application->email),
            wp_generate_password(),
            $application->email
        );

        if (is_wp_error($user_id)) {
            wp_send_json_error(array('message' => $user_id->get_error_message()));
        }

        // Assign partner role
        $user = new WP_User($user_id);
        $user->set_role('partner');

        // Update user display name
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => $application->name,
        ));

        // Create partner meta
        PPD_Database::update_partner_meta($user_id, array(
            'company_name' => $application->company_name,
            'phone' => $application->phone,
            'status' => 'active',
        ));

        // Update application status
        $wpdb->update(
            $applications_table,
            array('status' => 'approved'),
            array('id' => $application_id)
        );

        // Send email to new partner
        wp_new_user_notification($user_id, null, 'both');

        wp_send_json_success(array('message' => 'Partner created successfully'));
    }
}
