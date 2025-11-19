<?php
/**
 * Notifications Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Notifications {

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

    public static function create_notification($user_id, $title, $message, $type = 'info', $link = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_notifications';

        return $wpdb->insert($table, array(
            'user_id' => intval($user_id),
            'title' => sanitize_text_field($title),
            'message' => sanitize_textarea_field($message),
            'type' => sanitize_text_field($type),
            'link' => $link ? esc_url_raw($link) : null,
            'is_read' => 0,
        ));
    }

    public static function get_user_notifications($user_id, $unread_only = false, $limit = 20) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_notifications';

        if ($unread_only) {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d AND is_read = 0 ORDER BY created_at DESC LIMIT %d",
                $user_id,
                $limit
            ));
        } else {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC LIMIT %d",
                $user_id,
                $limit
            ));
        }
    }

    public static function get_unread_count($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_notifications';

        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE user_id = %d AND is_read = 0",
            $user_id
        ));
    }

    public static function mark_as_read($notification_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_notifications';

        return $wpdb->update(
            $table,
            array('is_read' => 1),
            array('id' => $notification_id)
        );
    }

    public static function mark_all_as_read($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_notifications';

        return $wpdb->update(
            $table,
            array('is_read' => 1),
            array('user_id' => $user_id, 'is_read' => 0)
        );
    }

    public static function delete_notification($notification_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_notifications';

        return $wpdb->delete($table, array('id' => $notification_id));
    }

    public static function delete_old_notifications($days = 30) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_notifications';

        return $wpdb->query($wpdb->prepare(
            "DELETE FROM $table WHERE is_read = 1 AND created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ));
    }

    // Notification types helper functions

    public static function notify_task_assigned($partner_id, $task_title) {
        return self::create_notification(
            $partner_id,
            'New Task Assigned',
            'You have been assigned a new task: ' . $task_title,
            'task',
            '#tasks'
        );
    }

    public static function notify_task_due_soon($partner_id, $task_title, $days_left) {
        return self::create_notification(
            $partner_id,
            'Task Due Soon',
            'Task "' . $task_title . '" is due in ' . $days_left . ' day(s)',
            'warning',
            '#tasks'
        );
    }

    public static function notify_lead_status_changed($partner_id, $student_name, $new_status) {
        return self::create_notification(
            $partner_id,
            'Lead Status Updated',
            'Lead for ' . $student_name . ' has been updated to: ' . ucfirst($new_status),
            'lead',
            '#leads'
        );
    }

    public static function notify_admin_note_added($partner_id, $student_name) {
        return self::create_notification(
            $partner_id,
            'Admin Note Added',
            'Admin has added a note to lead: ' . $student_name,
            'info',
            '#leads'
        );
    }

    public static function notify_college_assigned($partner_id, $college_name) {
        return self::create_notification(
            $partner_id,
            'New College Assigned',
            'You have been assigned to: ' . $college_name,
            'success',
            '#colleges'
        );
    }

    public static function notify_service_assigned($partner_id, $service_name) {
        return self::create_notification(
            $partner_id,
            'New Service Assigned',
            'New service has been assigned to you: ' . $service_name,
            'success',
            '#services'
        );
    }

    public static function notify_document_uploaded($partner_id, $document_title) {
        return self::create_notification(
            $partner_id,
            'New Document Available',
            'A new document has been uploaded: ' . $document_title,
            'document',
            '#documents'
        );
    }

    public static function get_notification($notification_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_notifications';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $notification_id
        ));
    }
}
