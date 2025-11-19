<?php
/**
 * Authentication Handler Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Auth {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', array($this, 'handle_login'));
        add_action('init', array($this, 'handle_logout'));
        add_action('init', array($this, 'create_partner_role'));
    }

    public function create_partner_role() {
        if (get_role('partner')) {
            return;
        }

        add_role('partner', 'Partner', array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
        ));
    }

    public function handle_login() {
        if (!isset($_POST['ppd_login_nonce']) || !wp_verify_nonce($_POST['ppd_login_nonce'], 'ppd_login')) {
            return;
        }

        $username = sanitize_text_field($_POST['ppd_username']);
        $password = $_POST['ppd_password'];
        $remember = isset($_POST['ppd_remember']) ? true : false;

        $user = wp_authenticate($username, $password);

        if (is_wp_error($user)) {
            wp_redirect(add_query_arg('login', 'failed', wp_get_referer()));
            exit;
        }

        // Check if user has partner role
        if (!in_array('partner', $user->roles) && !in_array('administrator', $user->roles)) {
            wp_redirect(add_query_arg('login', 'not_partner', wp_get_referer()));
            exit;
        }

        wp_set_auth_cookie($user->ID, $remember);
        wp_set_current_user($user->ID);
        do_action('wp_login', $user->user_login, $user);

        $redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : home_url('/partner-dashboard/');
        wp_safe_redirect($redirect_to);
        exit;
    }

    public function handle_logout() {
        if (isset($_GET['ppd_logout']) && $_GET['ppd_logout'] === 'true') {
            if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'ppd_logout')) {
                return;
            }

            wp_logout();
            wp_safe_redirect(home_url());
            exit;
        }
    }

    public static function is_partner_logged_in() {
        if (!is_user_logged_in()) {
            return false;
        }

        $user = wp_get_current_user();
        return in_array('partner', $user->roles) || in_array('administrator', $user->roles);
    }

    public static function get_current_partner_id() {
        if (!self::is_partner_logged_in()) {
            return 0;
        }

        return get_current_user_id();
    }

    public static function get_logout_url() {
        return wp_nonce_url(add_query_arg('ppd_logout', 'true', home_url()), 'ppd_logout');
    }
}
