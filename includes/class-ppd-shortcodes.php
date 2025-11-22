<?php
/**
 * Shortcodes Handler Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Shortcodes {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('partner_dashboard', array($this, 'render_dashboard'));
        add_shortcode('partner_login', array($this, 'render_login'));
        add_shortcode('partner_application_form', array($this, 'render_application_form'));
        add_shortcode('partner_logout', array($this, 'render_logout_button'));
    }

    public function render_dashboard($atts) {
        return PPD_Dashboard::render_dashboard();
    }

    public function render_login($atts) {
        if (PPD_Auth::is_partner_logged_in()) {
            $dashboard_url = home_url('/partner-dashboard/');
            return '<div class="ppd-already-logged-in">
                <p>You are already logged in. <a href="' . esc_url($dashboard_url) . '">Go to Dashboard</a></p>
            </div>';
        }

        $error_message = '';
        if (isset($_GET['login'])) {
            if ($_GET['login'] === 'failed') {
                $error_message = '<div class="ppd-error">Invalid username or password.</div>';
            } elseif ($_GET['login'] === 'not_partner') {
                $error_message = '<div class="ppd-error">You do not have partner access.</div>';
            }
        }

        ob_start();
        ?>
        <div class="ppd-login-wrapper">
            <div class="ppd-login-form-container">
                <h2>Partner Login</h2>
                <?php echo $error_message; ?>
                <form method="post" action="" class="ppd-login-form">
                    <?php wp_nonce_field('ppd_login', 'ppd_login_nonce'); ?>

                    <div class="ppd-form-group">
                        <label for="ppd_username">Username or Email</label>
                        <input type="text" id="ppd_username" name="ppd_username" required>
                    </div>

                    <div class="ppd-form-group">
                        <label for="ppd_password">Password</label>
                        <input type="password" id="ppd_password" name="ppd_password" required>
                    </div>

                    <div class="ppd-form-group">
                        <label>
                            <input type="checkbox" name="ppd_remember" value="1"> Remember Me
                        </label>
                    </div>

                    <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/partner-dashboard/')); ?>">

                    <button type="submit" class="ppd-btn ppd-btn-primary ppd-btn-block">Login</button>
                </form>

                <div class="ppd-login-footer">
                    <p>Not a partner yet? <a href="<?php echo home_url('/partner-application/'); ?>">Apply Now</a></p>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_application_form($atts) {
        $success_message = '';
        $error_message = '';

        if (isset($_GET['application'])) {
            if ($_GET['application'] === 'success') {
                $success_message = '<div class="ppd-success">Your application has been submitted successfully! We will review it and get back to you soon.</div>';
            } elseif ($_GET['application'] === 'error') {
                $error_message = '<div class="ppd-error">There was an error submitting your application. Please try again.</div>';
            }
        }

        ob_start();
        ?>
        <div class="ppd-application-wrapper">
            <div class="ppd-application-form-container">
                <h2>Partner Application Form</h2>
                <p>Join our partner network and grow your business with us!</p>

                <?php echo $success_message; ?>
                <?php echo $error_message; ?>

                <form id="ppd-application-form" class="ppd-form">
                    <?php wp_nonce_field('ppd_application', 'ppd_application_nonce'); ?>

                    <div class="ppd-form-group">
                        <label for="applicant_name">Full Name *</label>
                        <input type="text" id="applicant_name" name="applicant_name" required>
                    </div>

                    <div class="ppd-form-group">
                        <label for="applicant_email">Email Address *</label>
                        <input type="email" id="applicant_email" name="applicant_email" required>
                    </div>

                    <div class="ppd-form-group">
                        <label for="applicant_phone">Phone Number *</label>
                        <input type="text" id="applicant_phone" name="applicant_phone" required>
                    </div>

                    <div class="ppd-form-group">
                        <label for="applicant_company">Company Name</label>
                        <input type="text" id="applicant_company" name="applicant_company">
                    </div>

                    <div class="ppd-form-group">
                        <label for="applicant_message">Why do you want to become a partner?</label>
                        <textarea id="applicant_message" name="applicant_message" rows="5"></textarea>
                    </div>

                    <button type="submit" class="ppd-btn ppd-btn-primary ppd-btn-block">Submit Application</button>
                </form>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_logout_button($atts) {
        if (!PPD_Auth::is_partner_logged_in()) {
            return '';
        }

        return '<a href="' . esc_url(PPD_Auth::get_logout_url()) . '" class="ppd-btn ppd-btn-secondary">Logout</a>';
    }
}
