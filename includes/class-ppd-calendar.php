<?php
/**
 * Calendar & Events Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Calendar {

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

    public static function create_event($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_events';

        $result = $wpdb->insert($table, array(
            'title' => sanitize_text_field($data['title']),
            'description' => sanitize_textarea_field($data['description']),
            'event_type' => sanitize_text_field($data['event_type']),
            'start_datetime' => sanitize_text_field($data['start_datetime']),
            'end_datetime' => isset($data['end_datetime']) ? sanitize_text_field($data['end_datetime']) : null,
            'location' => isset($data['location']) ? sanitize_text_field($data['location']) : null,
            'partner_id' => isset($data['partner_id']) ? intval($data['partner_id']) : null,
            'college_id' => isset($data['college_id']) ? intval($data['college_id']) : null,
            'created_by' => get_current_user_id(),
            'status' => 'scheduled',
        ));

        if ($result && isset($data['partner_id'])) {
            $event_id = $wpdb->insert_id;

            // Notify partner
            PPD_Notifications::create_notification(
                $data['partner_id'],
                '📅 New Event: ' . $data['title'],
                'Event scheduled for ' . date('M d, Y h:i A', strtotime($data['start_datetime'])),
                'event',
                '#calendar'
            );
        }

        return $result;
    }

    public static function get_partner_events($partner_id, $from_date = null, $to_date = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_events';

        $where = "WHERE partner_id = %d";
        $params = array($partner_id);

        if ($from_date) {
            $where .= " AND start_datetime >= %s";
            $params[] = $from_date;
        }

        if ($to_date) {
            $where .= " AND start_datetime <= %s";
            $params[] = $to_date;
        }

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table $where ORDER BY start_datetime ASC",
            ...$params
        ));
    }

    public static function get_upcoming_events($partner_id, $limit = 10) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_events';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table
            WHERE partner_id = %d
            AND start_datetime >= NOW()
            AND status = 'scheduled'
            ORDER BY start_datetime ASC
            LIMIT %d",
            $partner_id, $limit
        ));
    }

    public static function get_event($event_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_events';

        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $event_id));
    }

    public static function update_event_status($event_id, $status) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_events';

        return $wpdb->update($table, array('status' => sanitize_text_field($status)), array('id' => $event_id));
    }

    public static function get_events_by_month($partner_id, $year, $month) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_events';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table
            WHERE partner_id = %d
            AND YEAR(start_datetime) = %d
            AND MONTH(start_datetime) = %d
            ORDER BY start_datetime ASC",
            $partner_id, $year, $month
        ));
    }

    public static function get_today_events($partner_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_events';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table
            WHERE partner_id = %d
            AND DATE(start_datetime) = CURDATE()
            ORDER BY start_datetime ASC",
            $partner_id
        ));
    }

    public static function send_event_reminders() {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_events';

        // Get events happening in next 24 hours that haven't sent reminder
        $events = $wpdb->get_results(
            "SELECT * FROM $table
            WHERE start_datetime BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
            AND reminder_sent = 0
            AND status = 'scheduled'"
        );

        foreach ($events as $event) {
            if ($event->partner_id) {
                PPD_Notifications::create_notification(
                    $event->partner_id,
                    '⏰ Event Reminder: ' . $event->title,
                    'Event starts on ' . date('M d, Y h:i A', strtotime($event->start_datetime)),
                    'reminder',
                    '#calendar'
                );

                // Mark as sent
                $wpdb->update($table, array('reminder_sent' => 1), array('id' => $event->id));
            }
        }
    }
}
