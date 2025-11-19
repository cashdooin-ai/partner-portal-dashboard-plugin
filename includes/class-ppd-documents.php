<?php
/**
 * Documents Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Documents {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', array($this, 'create_upload_directory'));
    }

    public function create_upload_directory() {
        $upload_dir = wp_upload_dir();
        $ppd_upload_dir = $upload_dir['basedir'] . '/ppd-documents';

        if (!file_exists($ppd_upload_dir)) {
            wp_mkdir_p($ppd_upload_dir);

            // Add .htaccess for security
            $htaccess_content = "Options -Indexes\n<Files *.php>\ndeny from all\n</Files>";
            file_put_contents($ppd_upload_dir . '/.htaccess', $htaccess_content);
        }
    }

    public static function get_documents($partner_id = null, $category = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_documents';

        $where_clauses = array();
        $params = array();

        if ($partner_id) {
            $where_clauses[] = "(is_public = 1 OR partner_id = %d OR partner_id IS NULL)";
            $params[] = $partner_id;
        } else {
            $where_clauses[] = "is_public = 1";
        }

        if ($category) {
            $where_clauses[] = "category = %s";
            $params[] = $category;
        }

        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

        if (!empty($params)) {
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table $where_sql ORDER BY created_at DESC",
                ...$params
            ));
        } else {
            $results = $wpdb->get_results("SELECT * FROM $table $where_sql ORDER BY created_at DESC");
        }

        return $results;
    }

    public static function get_all_documents() {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_documents';

        return $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");
    }

    public static function upload_document($file, $data) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return array('success' => false, 'message' => 'Invalid file upload');
        }

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return array('success' => false, 'message' => 'Upload error occurred');
        }

        // Validate file size (10MB max)
        if ($file['size'] > 10485760) {
            return array('success' => false, 'message' => 'File size exceeds 10MB limit');
        }

        // Allowed file types
        $allowed_types = array(
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'zip' => 'application/zip',
        );

        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!array_key_exists($file_extension, $allowed_types)) {
            return array('success' => false, 'message' => 'File type not allowed');
        }

        // Generate unique filename
        $upload_dir = wp_upload_dir();
        $ppd_upload_dir = $upload_dir['basedir'] . '/ppd-documents';
        $unique_filename = uniqid() . '_' . sanitize_file_name($file['name']);
        $file_path = $ppd_upload_dir . '/' . $unique_filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            return array('success' => false, 'message' => 'Failed to move uploaded file');
        }

        // Save to database
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_documents';

        $result = $wpdb->insert($table, array(
            'title' => sanitize_text_field($data['title']),
            'description' => sanitize_textarea_field($data['description']),
            'file_name' => $unique_filename,
            'file_path' => $file_path,
            'file_size' => $file['size'],
            'file_type' => $file['type'],
            'category' => sanitize_text_field($data['category']),
            'is_public' => isset($data['is_public']) ? 1 : 0,
            'partner_id' => isset($data['partner_id']) ? intval($data['partner_id']) : null,
            'uploaded_by' => get_current_user_id(),
        ));

        if ($result) {
            $document_id = $wpdb->insert_id;

            // Notify partners if public document
            if (isset($data['is_public']) && $data['is_public']) {
                self::notify_partners_new_document($data['title']);
            }

            return array(
                'success' => true,
                'message' => 'Document uploaded successfully',
                'document_id' => $document_id
            );
        }

        return array('success' => false, 'message' => 'Failed to save document to database');
    }

    private static function notify_partners_new_document($document_title) {
        $partners = PPD_Partner::get_all_partners();

        foreach ($partners as $partner) {
            if ($partner['status'] === 'active') {
                PPD_Notifications::notify_document_uploaded($partner['id'], $document_title);
            }
        }
    }

    public static function delete_document($document_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_documents';

        // Get document info
        $document = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $document_id
        ));

        if (!$document) {
            return false;
        }

        // Delete physical file
        if (file_exists($document->file_path)) {
            unlink($document->file_path);
        }

        // Delete from database
        return $wpdb->delete($table, array('id' => $document_id));
    }

    public static function get_document($document_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_documents';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $document_id
        ));
    }

    public static function get_download_url($document_id) {
        return add_query_arg(array(
            'action' => 'ppd_download_document',
            'document_id' => $document_id,
            'nonce' => wp_create_nonce('ppd_download_' . $document_id)
        ), admin_url('admin-ajax.php'));
    }

    public static function get_categories() {
        return array(
            'general' => 'General',
            'marketing' => 'Marketing Materials',
            'contracts' => 'Contracts & Agreements',
            'training' => 'Training Materials',
            'guidelines' => 'Guidelines & Policies',
            'certificates' => 'Certificates',
            'presentations' => 'Presentations',
            'brochures' => 'Brochures',
            'other' => 'Other'
        );
    }

    public static function format_file_size($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
