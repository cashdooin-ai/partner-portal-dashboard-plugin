<?php
/**
 * Admin Panel Handler Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Admin {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'Partner Portal',
            'Partner Portal',
            'manage_options',
            'partner-portal',
            array($this, 'render_dashboard_page'),
            'dashicons-groups',
            30
        );

        add_submenu_page(
            'partner-portal',
            'Partners',
            'Partners',
            'manage_options',
            'partner-portal',
            array($this, 'render_dashboard_page')
        );

        add_submenu_page(
            'partner-portal',
            'Colleges',
            'Colleges',
            'manage_options',
            'partner-portal-colleges',
            array($this, 'render_colleges_page')
        );

        add_submenu_page(
            'partner-portal',
            'Services',
            'Services',
            'manage_options',
            'partner-portal-services',
            array($this, 'render_services_page')
        );

        add_submenu_page(
            'partner-portal',
            'Tasks',
            'Tasks',
            'manage_options',
            'partner-portal-tasks',
            array($this, 'render_tasks_page')
        );

        add_submenu_page(
            'partner-portal',
            'Leads',
            'Leads',
            'manage_options',
            'partner-portal-leads',
            array($this, 'render_leads_page')
        );

        add_submenu_page(
            'partner-portal',
            'Applications',
            'Applications',
            'manage_options',
            'partner-portal-applications',
            array($this, 'render_applications_page')
        );
    }

    public function render_dashboard_page() {
        $partners = PPD_Partner::get_all_partners();
        include PPD_PLUGIN_DIR . 'admin/views/partners.php';
    }

    public function render_colleges_page() {
        $colleges = PPD_Colleges::get_all_colleges();
        include PPD_PLUGIN_DIR . 'admin/views/colleges.php';
    }

    public function render_services_page() {
        $services = PPD_Services::get_all_services();
        include PPD_PLUGIN_DIR . 'admin/views/services.php';
    }

    public function render_tasks_page() {
        global $wpdb;
        $tasks_table = $wpdb->prefix . 'ppd_tasks';
        $tasks = $wpdb->get_results("SELECT * FROM $tasks_table ORDER BY created_at DESC");
        include PPD_PLUGIN_DIR . 'admin/views/tasks.php';
    }

    public function render_leads_page() {
        $leads = PPD_Leads::get_all_leads();
        include PPD_PLUGIN_DIR . 'admin/views/leads.php';
    }

    public function render_applications_page() {
        global $wpdb;
        $applications_table = $wpdb->prefix . 'ppd_applications';
        $applications = $wpdb->get_results("SELECT * FROM $applications_table ORDER BY created_at DESC");
        include PPD_PLUGIN_DIR . 'admin/views/applications.php';
    }
}
