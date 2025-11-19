<?php
/**
 * Student Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Students {

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

    public static function add_student($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_students';

        return $wpdb->insert($table, array(
            'partner_id' => intval($data['partner_id']),
            'lead_id' => isset($data['lead_id']) ? intval($data['lead_id']) : null,
            'student_name' => sanitize_text_field($data['student_name']),
            'student_email' => sanitize_email($data['student_email']),
            'student_phone' => sanitize_text_field($data['student_phone']),
            'college_id' => isset($data['college_id']) ? intval($data['college_id']) : null,
            'course' => isset($data['course']) ? sanitize_text_field($data['course']) : null,
        ));
    }

    public static function get_partner_students($partner_id, $status = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_students';

        if ($status) {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE partner_id = %d AND application_status = %s ORDER BY created_at DESC",
                $partner_id, $status
            ));
        } else {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE partner_id = %d ORDER BY created_at DESC",
                $partner_id
            ));
        }
    }

    public static function get_student($student_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_students';

        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $student_id));
    }

    public static function update_student_status($student_id, $status_type, $status_value) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_students';

        $allowed_types = array('application_status', 'document_verification_status', 'admission_status');

        if (!in_array($status_type, $allowed_types)) {
            return false;
        }

        $result = $wpdb->update($table, array($status_type => sanitize_text_field($status_value)), array('id' => $student_id));

        if ($result) {
            $student = self::get_student($student_id);
            if ($student) {
                // Notify partner of status change
                PPD_Notifications::create_notification(
                    $student->partner_id,
                    'Student Status Updated',
                    'Status update for ' . $student->student_name . ': ' . str_replace('_', ' ', $status_type) . ' - ' . $status_value,
                    'student',
                    '#students'
                );
            }
        }

        return $result;
    }

    public static function upload_student_document($student_id, $file, $document_type) {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return array('success' => false, 'message' => 'File upload error');
        }

        // Validate file size (5MB max)
        if ($file['size'] > 5242880) {
            return array('success' => false, 'message' => 'File size exceeds 5MB limit');
        }

        $upload_dir = wp_upload_dir();
        $student_docs_dir = $upload_dir['basedir'] . '/ppd-student-documents';

        if (!file_exists($student_docs_dir)) {
            wp_mkdir_p($student_docs_dir);
        }

        $unique_filename = $student_id . '_' . uniqid() . '_' . sanitize_file_name($file['name']);
        $file_path = $student_docs_dir . '/' . $unique_filename;

        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            return array('success' => false, 'message' => 'Failed to move uploaded file');
        }

        global $wpdb;
        $table = $wpdb->prefix . 'ppd_student_documents';

        $result = $wpdb->insert($table, array(
            'student_id' => intval($student_id),
            'document_type' => sanitize_text_field($document_type),
            'file_name' => $unique_filename,
            'file_path' => $file_path,
            'verification_status' => 'pending',
        ));

        if ($result) {
            return array('success' => true, 'document_id' => $wpdb->insert_id);
        }

        return array('success' => false, 'message' => 'Failed to save document record');
    }

    public static function get_student_documents($student_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_student_documents';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE student_id = %d ORDER BY uploaded_at DESC",
            $student_id
        ));
    }

    public static function verify_document($document_id, $status, $notes = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_student_documents';

        return $wpdb->update($table, array(
            'verification_status' => sanitize_text_field($status),
            'verified_by' => get_current_user_id(),
            'verified_at' => current_time('mysql'),
            'notes' => $notes ? sanitize_textarea_field($notes) : null,
        ), array('id' => $document_id));
    }

    public static function get_students_stats($partner_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_students';

        $total = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE partner_id = %d",
            $partner_id
        ));

        $admitted = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE partner_id = %d AND admission_status = 'admitted'",
            $partner_id
        ));

        $in_progress = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE partner_id = %d AND admission_status IN ('pending', 'in_progress')",
            $partner_id
        ));

        return array(
            'total' => (int) $total,
            'admitted' => (int) $admitted,
            'in_progress' => (int) $in_progress,
        );
    }
}
