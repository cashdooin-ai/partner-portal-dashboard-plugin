<?php
/**
 * Database Handler Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Database {

    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Partners meta table
        $table_partners_meta = $wpdb->prefix . 'ppd_partners_meta';
        $sql_partners = "CREATE TABLE $table_partners_meta (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            company_name varchar(255) DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            address text DEFAULT NULL,
            city varchar(100) DEFAULT NULL,
            state varchar(100) DEFAULT NULL,
            country varchar(100) DEFAULT NULL,
            pincode varchar(20) DEFAULT NULL,
            website varchar(255) DEFAULT NULL,
            description text DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            joined_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql_partners);

        // Colleges table
        $table_colleges = $wpdb->prefix . 'ppd_colleges';
        $sql_colleges = "CREATE TABLE $table_colleges (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            location varchar(255) DEFAULT NULL,
            city varchar(100) DEFAULT NULL,
            state varchar(100) DEFAULT NULL,
            website varchar(255) DEFAULT NULL,
            contact_person varchar(255) DEFAULT NULL,
            contact_email varchar(100) DEFAULT NULL,
            contact_phone varchar(50) DEFAULT NULL,
            description text DEFAULT NULL,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_colleges);

        // Partner Colleges Assignment table
        $table_partner_colleges = $wpdb->prefix . 'ppd_partner_colleges';
        $sql_partner_colleges = "CREATE TABLE $table_partner_colleges (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            partner_id bigint(20) NOT NULL,
            college_id bigint(20) NOT NULL,
            assigned_date datetime DEFAULT CURRENT_TIMESTAMP,
            assigned_by bigint(20) DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY partner_id (partner_id),
            KEY college_id (college_id)
        ) $charset_collate;";
        dbDelta($sql_partner_colleges);

        // Services table
        $table_services = $wpdb->prefix . 'ppd_services';
        $sql_services = "CREATE TABLE $table_services (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text DEFAULT NULL,
            price decimal(10,2) DEFAULT 0.00,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_services);

        // Partner Services Assignment table
        $table_partner_services = $wpdb->prefix . 'ppd_partner_services';
        $sql_partner_services = "CREATE TABLE $table_partner_services (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            partner_id bigint(20) NOT NULL,
            service_id bigint(20) NOT NULL,
            assigned_date datetime DEFAULT CURRENT_TIMESTAMP,
            assigned_by bigint(20) DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY partner_id (partner_id),
            KEY service_id (service_id)
        ) $charset_collate;";
        dbDelta($sql_partner_services);

        // Tasks table
        $table_tasks = $wpdb->prefix . 'ppd_tasks';
        $sql_tasks = "CREATE TABLE $table_tasks (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            partner_id bigint(20) NOT NULL,
            title varchar(255) NOT NULL,
            description text DEFAULT NULL,
            priority varchar(20) DEFAULT 'medium',
            status varchar(20) DEFAULT 'pending',
            due_date datetime DEFAULT NULL,
            assigned_by bigint(20) DEFAULT NULL,
            completed_date datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY partner_id (partner_id)
        ) $charset_collate;";
        dbDelta($sql_tasks);

        // Leads table
        $table_leads = $wpdb->prefix . 'ppd_leads';
        $sql_leads = "CREATE TABLE $table_leads (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            partner_id bigint(20) NOT NULL,
            student_name varchar(255) NOT NULL,
            student_email varchar(100) DEFAULT NULL,
            student_phone varchar(50) DEFAULT NULL,
            college_id bigint(20) DEFAULT NULL,
            service_id bigint(20) DEFAULT NULL,
            status varchar(20) DEFAULT 'new',
            notes text DEFAULT NULL,
            admin_notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY partner_id (partner_id)
        ) $charset_collate;";
        dbDelta($sql_leads);

        // Google Sheets Settings table
        $table_google_sheets = $wpdb->prefix . 'ppd_google_sheets';
        $sql_google_sheets = "CREATE TABLE $table_google_sheets (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            partner_id bigint(20) NOT NULL,
            sheet_id varchar(255) DEFAULT NULL,
            sheet_name varchar(255) DEFAULT NULL,
            credentials text DEFAULT NULL,
            sync_enabled tinyint(1) DEFAULT 0,
            last_sync datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY partner_id (partner_id)
        ) $charset_collate;";
        dbDelta($sql_google_sheets);

        // Partner Applications table
        $table_applications = $wpdb->prefix . 'ppd_applications';
        $sql_applications = "CREATE TABLE $table_applications (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(50) DEFAULT NULL,
            company_name varchar(255) DEFAULT NULL,
            message text DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_applications);

        // Commissions table
        $table_commissions = $wpdb->prefix . 'ppd_commissions';
        $sql_commissions = "CREATE TABLE $table_commissions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            partner_id bigint(20) NOT NULL,
            lead_id bigint(20) DEFAULT NULL,
            amount decimal(10,2) NOT NULL DEFAULT 0.00,
            commission_type varchar(50) DEFAULT 'lead',
            description text DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            paid_date datetime DEFAULT NULL,
            payment_method varchar(50) DEFAULT NULL,
            transaction_id varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY partner_id (partner_id),
            KEY lead_id (lead_id),
            KEY status (status)
        ) $charset_collate;";
        dbDelta($sql_commissions);

        // Notifications table
        $table_notifications = $wpdb->prefix . 'ppd_notifications';
        $sql_notifications = "CREATE TABLE $table_notifications (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            title varchar(255) NOT NULL,
            message text NOT NULL,
            type varchar(50) DEFAULT 'info',
            link varchar(255) DEFAULT NULL,
            is_read tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY is_read (is_read)
        ) $charset_collate;";
        dbDelta($sql_notifications);

        // Documents table
        $table_documents = $wpdb->prefix . 'ppd_documents';
        $sql_documents = "CREATE TABLE $table_documents (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text DEFAULT NULL,
            file_name varchar(255) NOT NULL,
            file_path varchar(500) NOT NULL,
            file_size bigint(20) DEFAULT NULL,
            file_type varchar(100) DEFAULT NULL,
            category varchar(100) DEFAULT 'general',
            is_public tinyint(1) DEFAULT 0,
            partner_id bigint(20) DEFAULT NULL,
            uploaded_by bigint(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY category (category),
            KEY partner_id (partner_id)
        ) $charset_collate;";
        dbDelta($sql_documents);

        // Lead Pipeline Stages (update leads status to support pipeline)
        // Pipeline stages: new, contacted, qualified, proposal, negotiation, converted, closed_won, closed_lost

        // Messages/Communication table
        $table_messages = $wpdb->prefix . 'ppd_messages';
        $sql_messages = "CREATE TABLE $table_messages (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            sender_id bigint(20) NOT NULL,
            receiver_id bigint(20) NOT NULL,
            subject varchar(255) NOT NULL,
            message text NOT NULL,
            is_read tinyint(1) DEFAULT 0,
            parent_id bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY sender_id (sender_id),
            KEY receiver_id (receiver_id),
            KEY parent_id (parent_id)
        ) $charset_collate;";
        dbDelta($sql_messages);

        // Support Tickets table
        $table_tickets = $wpdb->prefix . 'ppd_tickets';
        $sql_tickets = "CREATE TABLE $table_tickets (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            partner_id bigint(20) NOT NULL,
            subject varchar(255) NOT NULL,
            message text NOT NULL,
            priority varchar(20) DEFAULT 'medium',
            status varchar(20) DEFAULT 'open',
            category varchar(100) DEFAULT 'general',
            assigned_to bigint(20) DEFAULT NULL,
            closed_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY partner_id (partner_id),
            KEY status (status)
        ) $charset_collate;";
        dbDelta($sql_tickets);

        // Ticket Replies table
        $table_ticket_replies = $wpdb->prefix . 'ppd_ticket_replies';
        $sql_ticket_replies = "CREATE TABLE $table_ticket_replies (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            ticket_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            message text NOT NULL,
            is_internal tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY ticket_id (ticket_id)
        ) $charset_collate;";
        dbDelta($sql_ticket_replies);

        // Announcements table
        $table_announcements = $wpdb->prefix . 'ppd_announcements';
        $sql_announcements = "CREATE TABLE $table_announcements (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            content text NOT NULL,
            type varchar(50) DEFAULT 'general',
            is_pinned tinyint(1) DEFAULT 0,
            published_by bigint(20) NOT NULL,
            published_at datetime DEFAULT CURRENT_TIMESTAMP,
            expires_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY is_pinned (is_pinned)
        ) $charset_collate;";
        dbDelta($sql_announcements);

        // FAQ table
        $table_faqs = $wpdb->prefix . 'ppd_faqs';
        $sql_faqs = "CREATE TABLE $table_faqs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            question varchar(500) NOT NULL,
            answer text NOT NULL,
            category varchar(100) DEFAULT 'general',
            display_order int DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY category (category)
        ) $charset_collate;";
        dbDelta($sql_faqs);

        // Events/Calendar table
        $table_events = $wpdb->prefix . 'ppd_events';
        $sql_events = "CREATE TABLE $table_events (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text DEFAULT NULL,
            event_type varchar(50) DEFAULT 'meeting',
            start_datetime datetime NOT NULL,
            end_datetime datetime DEFAULT NULL,
            location varchar(255) DEFAULT NULL,
            partner_id bigint(20) DEFAULT NULL,
            college_id bigint(20) DEFAULT NULL,
            created_by bigint(20) NOT NULL,
            reminder_sent tinyint(1) DEFAULT 0,
            status varchar(20) DEFAULT 'scheduled',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY partner_id (partner_id),
            KEY start_datetime (start_datetime),
            KEY event_type (event_type)
        ) $charset_collate;";
        dbDelta($sql_events);

        // Performance Targets table
        $table_targets = $wpdb->prefix . 'ppd_targets';
        $sql_targets = "CREATE TABLE $table_targets (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            partner_id bigint(20) NOT NULL,
            target_type varchar(50) NOT NULL,
            target_value decimal(10,2) NOT NULL,
            achieved_value decimal(10,2) DEFAULT 0.00,
            period_start date NOT NULL,
            period_end date NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY partner_id (partner_id),
            KEY period_start (period_start)
        ) $charset_collate;";
        dbDelta($sql_targets);

        // Referrals table
        $table_referrals = $wpdb->prefix . 'ppd_referrals';
        $sql_referrals = "CREATE TABLE $table_referrals (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            referrer_id bigint(20) NOT NULL,
            referred_name varchar(255) NOT NULL,
            referred_email varchar(100) NOT NULL,
            referred_phone varchar(50) DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            referred_partner_id bigint(20) DEFAULT NULL,
            commission_amount decimal(10,2) DEFAULT 0.00,
            commission_paid tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            approved_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY referrer_id (referrer_id),
            KEY status (status)
        ) $charset_collate;";
        dbDelta($sql_referrals);

        // Students Management table
        $table_students = $wpdb->prefix . 'ppd_students';
        $sql_students = "CREATE TABLE $table_students (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            partner_id bigint(20) NOT NULL,
            lead_id bigint(20) DEFAULT NULL,
            student_name varchar(255) NOT NULL,
            student_email varchar(100) DEFAULT NULL,
            student_phone varchar(50) DEFAULT NULL,
            college_id bigint(20) DEFAULT NULL,
            course varchar(255) DEFAULT NULL,
            application_status varchar(50) DEFAULT 'pending',
            document_verification_status varchar(50) DEFAULT 'pending',
            admission_status varchar(50) DEFAULT 'pending',
            notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY partner_id (partner_id),
            KEY lead_id (lead_id),
            KEY application_status (application_status)
        ) $charset_collate;";
        dbDelta($sql_students);

        // Student Documents table
        $table_student_docs = $wpdb->prefix . 'ppd_student_documents';
        $sql_student_docs = "CREATE TABLE $table_student_docs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            student_id bigint(20) NOT NULL,
            document_type varchar(100) NOT NULL,
            file_name varchar(255) NOT NULL,
            file_path varchar(500) NOT NULL,
            verification_status varchar(20) DEFAULT 'pending',
            verified_by bigint(20) DEFAULT NULL,
            verified_at datetime DEFAULT NULL,
            notes text DEFAULT NULL,
            uploaded_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY student_id (student_id),
            KEY verification_status (verification_status)
        ) $charset_collate;";
        dbDelta($sql_student_docs);

        // Activity Log table
        $table_activity_log = $wpdb->prefix . 'ppd_activity_log';
        $sql_activity_log = "CREATE TABLE $table_activity_log (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            action varchar(100) NOT NULL,
            description text DEFAULT NULL,
            entity_type varchar(50) DEFAULT NULL,
            entity_id bigint(20) DEFAULT NULL,
            ip_address varchar(50) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY action (action),
            KEY created_at (created_at)
        ) $charset_collate;";
        dbDelta($sql_activity_log);

        update_option('ppd_db_version', PPD_VERSION);
    }

    public static function get_partner_meta($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_partners_meta';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d",
            $user_id
        ));
    }

    public static function update_partner_meta($user_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_partners_meta';

        $existing = self::get_partner_meta($user_id);

        if ($existing) {
            return $wpdb->update($table, $data, array('user_id' => $user_id));
        } else {
            $data['user_id'] = $user_id;
            return $wpdb->insert($table, $data);
        }
    }
}
