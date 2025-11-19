<?php
/**
 * Communication Center Class - Messages, Tickets, Announcements, FAQs
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Communication {

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

    // ========== DIRECT MESSAGING ==========

    public static function send_message($sender_id, $receiver_id, $subject, $message, $parent_id = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_messages';

        $result = $wpdb->insert($table, array(
            'sender_id' => intval($sender_id),
            'receiver_id' => intval($receiver_id),
            'subject' => sanitize_text_field($subject),
            'message' => sanitize_textarea_field($message),
            'parent_id' => $parent_id ? intval($parent_id) : null,
        ));

        if ($result) {
            // Send notification to receiver
            $sender = get_userdata($sender_id);
            PPD_Notifications::create_notification(
                $receiver_id,
                'New Message from ' . $sender->display_name,
                substr($message, 0, 100) . '...',
                'message',
                '#messages'
            );
        }

        return $result;
    }

    public static function get_user_messages($user_id, $unread_only = false) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_messages';

        if ($unread_only) {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE receiver_id = %d AND is_read = 0 ORDER BY created_at DESC",
                $user_id
            ));
        } else {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE receiver_id = %d OR sender_id = %d ORDER BY created_at DESC",
                $user_id, $user_id
            ));
        }
    }

    public static function mark_message_read($message_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_messages';

        return $wpdb->update($table, array('is_read' => 1), array('id' => $message_id));
    }

    // ========== SUPPORT TICKETS ==========

    public static function create_ticket($partner_id, $subject, $message, $priority = 'medium', $category = 'general') {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_tickets';

        $result = $wpdb->insert($table, array(
            'partner_id' => intval($partner_id),
            'subject' => sanitize_text_field($subject),
            'message' => sanitize_textarea_field($message),
            'priority' => sanitize_text_field($priority),
            'category' => sanitize_text_field($category),
            'status' => 'open',
        ));

        if ($result) {
            $ticket_id = $wpdb->insert_id;

            // Notify admins
            $admins = get_users(array('role' => 'administrator'));
            foreach ($admins as $admin) {
                PPD_Notifications::create_notification(
                    $admin->ID,
                    'New Support Ticket',
                    'Ticket #' . $ticket_id . ': ' . $subject,
                    'ticket',
                    null
                );
            }

            return $ticket_id;
        }

        return false;
    }

    public static function get_partner_tickets($partner_id, $status = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_tickets';

        if ($status) {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE partner_id = %d AND status = %s ORDER BY created_at DESC",
                $partner_id, $status
            ));
        } else {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE partner_id = %d ORDER BY created_at DESC",
                $partner_id
            ));
        }
    }

    public static function get_all_tickets($status = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_tickets';

        if ($status) {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE status = %s ORDER BY created_at DESC",
                $status
            ));
        } else {
            return $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");
        }
    }

    public static function add_ticket_reply($ticket_id, $user_id, $message, $is_internal = false) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_ticket_replies';

        $result = $wpdb->insert($table, array(
            'ticket_id' => intval($ticket_id),
            'user_id' => intval($user_id),
            'message' => sanitize_textarea_field($message),
            'is_internal' => $is_internal ? 1 : 0,
        ));

        if ($result && !$is_internal) {
            // Update ticket timestamp
            $tickets_table = $wpdb->prefix . 'ppd_tickets';
            $wpdb->update($tickets_table, array('updated_at' => current_time('mysql')), array('id' => $ticket_id));

            // Notify relevant parties
            $ticket = self::get_ticket($ticket_id);
            if ($ticket) {
                $user = get_userdata($user_id);
                if (user_can($user_id, 'manage_options')) {
                    // Admin replied, notify partner
                    PPD_Notifications::create_notification(
                        $ticket->partner_id,
                        'Ticket Reply - #' . $ticket_id,
                        'Admin replied to your ticket: ' . $ticket->subject,
                        'ticket',
                        '#support'
                    );
                } else {
                    // Partner replied, notify admins
                    $admins = get_users(array('role' => 'administrator'));
                    foreach ($admins as $admin) {
                        PPD_Notifications::create_notification(
                            $admin->ID,
                            'Ticket Update - #' . $ticket_id,
                            'Partner replied to ticket: ' . $ticket->subject,
                            'ticket',
                            null
                        );
                    }
                }
            }
        }

        return $result;
    }

    public static function get_ticket_replies($ticket_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_ticket_replies';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE ticket_id = %d AND is_internal = 0 ORDER BY created_at ASC",
            $ticket_id
        ));
    }

    public static function get_ticket($ticket_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_tickets';

        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $ticket_id));
    }

    public static function update_ticket_status($ticket_id, $status) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_tickets';

        $data = array('status' => sanitize_text_field($status));

        if ($status === 'closed') {
            $data['closed_at'] = current_time('mysql');
        }

        return $wpdb->update($table, $data, array('id' => $ticket_id));
    }

    // ========== ANNOUNCEMENTS ==========

    public static function create_announcement($title, $content, $type = 'general', $is_pinned = false, $expires_at = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_announcements';

        $result = $wpdb->insert($table, array(
            'title' => sanitize_text_field($title),
            'content' => wp_kses_post($content),
            'type' => sanitize_text_field($type),
            'is_pinned' => $is_pinned ? 1 : 0,
            'published_by' => get_current_user_id(),
            'expires_at' => $expires_at ? sanitize_text_field($expires_at) : null,
        ));

        if ($result) {
            // Notify all partners
            $partners = PPD_Partner::get_all_partners();
            foreach ($partners as $partner) {
                if ($partner['status'] === 'active') {
                    PPD_Notifications::create_notification(
                        $partner['id'],
                        '📢 New Announcement',
                        $title,
                        'announcement',
                        '#announcements'
                    );
                }
            }
        }

        return $result;
    }

    public static function get_active_announcements() {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_announcements';

        return $wpdb->get_results(
            "SELECT * FROM $table
            WHERE (expires_at IS NULL OR expires_at > NOW())
            ORDER BY is_pinned DESC, published_at DESC"
        );
    }

    public static function get_all_announcements() {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_announcements';

        return $wpdb->get_results("SELECT * FROM $table ORDER BY published_at DESC");
    }

    // ========== FAQs ==========

    public static function add_faq($question, $answer, $category = 'general', $display_order = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_faqs';

        return $wpdb->insert($table, array(
            'question' => sanitize_text_field($question),
            'answer' => wp_kses_post($answer),
            'category' => sanitize_text_field($category),
            'display_order' => intval($display_order),
        ));
    }

    public static function get_faqs($category = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_faqs';

        if ($category) {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE category = %s ORDER BY display_order ASC, id ASC",
                $category
            ));
        } else {
            return $wpdb->get_results("SELECT * FROM $table ORDER BY category, display_order ASC, id ASC");
        }
    }

    public static function get_faq_categories() {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_faqs';

        return $wpdb->get_col("SELECT DISTINCT category FROM $table ORDER BY category ASC");
    }
}
