<?php
/**
 * Tasks Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Tasks {

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

    public static function get_partner_tasks($partner_id, $status = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_tasks';

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

    public static function add_task($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_tasks';

        return $wpdb->insert($table, array(
            'partner_id' => intval($data['partner_id']),
            'title' => sanitize_text_field($data['title']),
            'description' => sanitize_textarea_field($data['description']),
            'priority' => sanitize_text_field($data['priority']),
            'status' => 'pending',
            'due_date' => isset($data['due_date']) ? sanitize_text_field($data['due_date']) : null,
            'assigned_by' => get_current_user_id(),
        ));
    }

    public static function update_task_status($task_id, $status) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_tasks';

        $data = array('status' => sanitize_text_field($status));

        if ($status === 'completed') {
            $data['completed_date'] = current_time('mysql');
        }

        return $wpdb->update($table, $data, array('id' => $task_id));
    }

    public static function delete_task($task_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_tasks';

        return $wpdb->delete($table, array('id' => $task_id));
    }
}
