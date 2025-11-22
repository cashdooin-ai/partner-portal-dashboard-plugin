<?php
/**
 * Plugin Name: Partner Portal Dashboard
 * Plugin URI: https://collegekampus.com
 * Description: Complete partner management system with dashboard, profile, colleges, services, tasks, leads board, and Google Sheets integration
 * Version: 3.0.0
 * Author: CollegeKampus
 * Author URI: https://collegekampus.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: partner-portal-dashboard
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PPD_VERSION', '3.0.0');
define('PPD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PPD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PPD_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include required files
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-database.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-auth.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-partner.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-admin.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-dashboard.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-dashboard-simple.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-colleges.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-services.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-tasks.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-leads.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-google-sheets.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-shortcodes.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-ajax.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-analytics.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-commissions.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-notifications.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-pipeline.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-documents.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-communication.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-calendar.php';
require_once PPD_PLUGIN_DIR . 'includes/class-ppd-performance.php';
// Temporarily disabled v3.0 classes to debug 500 error - testing one by one
// require_once PPD_PLUGIN_DIR . 'includes/class-ppd-referrals.php';
// require_once PPD_PLUGIN_DIR . 'includes/class-ppd-students.php';
// require_once PPD_PLUGIN_DIR . 'includes/class-ppd-activity.php';

/**
 * Main Plugin Class
 */
class Partner_Portal_Dashboard {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Initialize plugin
        add_action('plugins_loaded', array($this, 'init'));

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function activate() {
        PPD_Database::create_tables();
        $this->create_default_pages();
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

    private function create_default_pages() {
        // Create Partner Dashboard page
        $dashboard_page = get_page_by_title('Partner Dashboard');
        if (!$dashboard_page) {
            wp_insert_post(array(
                'post_title' => 'Partner Dashboard',
                'post_content' => '[partner_dashboard]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => 1,
            ));
        }

        // Create Partner Login page
        $login_page = get_page_by_title('Partner Login');
        if (!$login_page) {
            wp_insert_post(array(
                'post_title' => 'Partner Login',
                'post_content' => '[partner_login]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => 1,
            ));
        }

        // Create Partner Application page
        $application_page = get_page_by_title('Partner Application');
        if (!$application_page) {
            wp_insert_post(array(
                'post_title' => 'Partner Application',
                'post_content' => '[partner_application_form]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => 1,
            ));
        }
    }

    public function init() {
        // Initialize classes
        PPD_Auth::get_instance();
        PPD_Partner::get_instance();
        PPD_Admin::get_instance();
        PPD_Dashboard::get_instance();
        PPD_Colleges::get_instance();
        PPD_Services::get_instance();
        PPD_Tasks::get_instance();
        PPD_Leads::get_instance();
        PPD_Google_Sheets::get_instance();
        PPD_Shortcodes::get_instance();
        PPD_Ajax::get_instance();
        PPD_Analytics::get_instance();
        PPD_Commissions::get_instance();
        PPD_Notifications::get_instance();
        PPD_Pipeline::get_instance();
        PPD_Documents::get_instance();
        PPD_Communication::get_instance();
        PPD_Calendar::get_instance();
        PPD_Performance::get_instance();
        // Temporarily disabled v3.0 classes to debug 500 error - testing one by one
        // PPD_Referrals::get_instance();
        // PPD_Students::get_instance();
        // PPD_Activity::get_instance();
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style('ppd-frontend-hero', PPD_PLUGIN_URL . 'assets/css/frontend-hero.css', array(), PPD_VERSION);

        // Enqueue Chart.js from CDN
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js', array(), '3.9.1', true);

        // Enqueue Sortable.js for drag and drop
        wp_enqueue_script('sortablejs', 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js', array(), '1.15.0', true);

        wp_enqueue_script('ppd-frontend', PPD_PLUGIN_URL . 'assets/js/frontend.js', array('jquery', 'chartjs', 'sortablejs'), PPD_VERSION, true);

        wp_localize_script('ppd-frontend', 'ppdAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ppd_nonce'),
        ));
    }

    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'partner-portal') === false) {
            return;
        }

        wp_enqueue_style('ppd-admin', PPD_PLUGIN_URL . 'assets/css/admin.css', array(), PPD_VERSION);
        wp_enqueue_script('ppd-admin', PPD_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), PPD_VERSION, true);

        wp_localize_script('ppd-admin', 'ppdAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ppd_admin_nonce'),
        ));
    }
}

// Initialize the plugin
function ppd_init() {
    return Partner_Portal_Dashboard::get_instance();
}

ppd_init();
