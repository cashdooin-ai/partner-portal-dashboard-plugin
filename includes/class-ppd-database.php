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
