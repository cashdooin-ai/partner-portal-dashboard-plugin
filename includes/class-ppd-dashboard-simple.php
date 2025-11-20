<?php
/**
 * Simplified Dashboard Handler Class - Hero Style Layout
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Dashboard_Simple {

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

    public static function render_dashboard() {
        if (!PPD_Auth::is_partner_logged_in()) {
            return '<div class="ppd-notice">Please log in to access the partner dashboard.</div>';
        }

        $partner_id = PPD_Auth::get_current_partner_id();
        $partner_data = PPD_Partner::get_partner_data($partner_id);
        $stats = PPD_Partner::get_partner_stats($partner_id);

        ob_start();
        ?>
        <div class="ppd-hero-dashboard">

            <!-- Hero Header -->
            <div class="ppd-hero-header">
                <div class="ppd-hero-content">
                    <h1 class="ppd-hero-title">Welcome Back, <?php echo esc_html($partner_data['name']); ?>! 👋</h1>
                    <p class="ppd-hero-subtitle">Here's what's happening with your partnership today</p>
                </div>
                <div class="ppd-hero-actions">
                    <a href="<?php echo esc_url(PPD_Auth::get_logout_url()); ?>" class="ppd-btn-logout">Logout</a>
                </div>
            </div>

            <!-- Quick Stats Grid -->
            <div class="ppd-stats-grid">
                <div class="ppd-stat-box">
                    <div class="ppd-stat-icon-circle">🏫</div>
                    <div class="ppd-stat-number"><?php echo esc_html($stats['colleges_count']); ?></div>
                    <div class="ppd-stat-label">Colleges</div>
                </div>

                <div class="ppd-stat-box">
                    <div class="ppd-stat-icon-circle">👥</div>
                    <div class="ppd-stat-number"><?php echo esc_html($stats['leads_count']); ?></div>
                    <div class="ppd-stat-label">Total Leads</div>
                </div>

                <div class="ppd-stat-box">
                    <div class="ppd-stat-icon-circle">📋</div>
                    <div class="ppd-stat-number"><?php echo esc_html($stats['pending_tasks']); ?></div>
                    <div class="ppd-stat-label">Pending Tasks</div>
                </div>

                <div class="ppd-stat-box">
                    <div class="ppd-stat-icon-circle">🛎️</div>
                    <div class="ppd-stat-number"><?php echo esc_html($stats['services_count']); ?></div>
                    <div class="ppd-stat-label">Services</div>
                </div>
            </div>

            <!-- Main Content Cards -->
            <div class="ppd-content-grid">

                <!-- Profile Card -->
                <div class="ppd-card">
                    <div class="ppd-card-header">
                        <h3>👤 My Profile</h3>
                    </div>
                    <div class="ppd-card-body">
                        <p><strong>Email:</strong> <?php echo esc_html($partner_data['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo esc_html($partner_data['phone']); ?></p>
                        <p><strong>Company:</strong> <?php echo esc_html($partner_data['company']); ?></p>
                        <a href="#" class="ppd-btn ppd-btn-primary" data-section="profile">Edit Profile</a>
                    </div>
                </div>

                <!-- Colleges Card -->
                <div class="ppd-card">
                    <div class="ppd-card-header">
                        <h3>🏫 My Colleges</h3>
                    </div>
                    <div class="ppd-card-body">
                        <?php
                        $colleges = PPD_Colleges::get_partner_colleges($partner_id);
                        if (!empty($colleges)): ?>
                            <ul class="ppd-simple-list">
                                <?php foreach (array_slice($colleges, 0, 3) as $college): ?>
                                    <li>✓ <?php echo esc_html($college->college_name); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (count($colleges) > 3): ?>
                                <p class="ppd-text-muted">+ <?php echo count($colleges) - 3; ?> more colleges</p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="ppd-text-muted">No colleges assigned yet.</p>
                        <?php endif; ?>
                        <a href="#" class="ppd-btn ppd-btn-secondary" data-section="colleges">View All</a>
                    </div>
                </div>

                <!-- Leads Card -->
                <div class="ppd-card">
                    <div class="ppd-card-header">
                        <h3>👥 Recent Leads</h3>
                    </div>
                    <div class="ppd-card-body">
                        <?php
                        $leads = PPD_Leads::get_partner_leads($partner_id, 5);
                        if (!empty($leads)): ?>
                            <ul class="ppd-simple-list">
                                <?php foreach ($leads as $lead): ?>
                                    <li>
                                        <?php echo esc_html($lead->student_name); ?>
                                        <span class="ppd-badge badge-<?php echo esc_attr($lead->status); ?>">
                                            <?php echo esc_html($lead->status); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="ppd-text-muted">No leads yet.</p>
                        <?php endif; ?>
                        <a href="#" class="ppd-btn ppd-btn-primary" data-section="leads">Add New Lead</a>
                    </div>
                </div>

                <!-- Tasks Card -->
                <div class="ppd-card">
                    <div class="ppd-card-header">
                        <h3>📋 Pending Tasks</h3>
                    </div>
                    <div class="ppd-card-body">
                        <?php
                        $tasks = PPD_Tasks::get_partner_tasks($partner_id, 'pending', 5);
                        if (!empty($tasks)): ?>
                            <ul class="ppd-simple-list">
                                <?php foreach ($tasks as $task): ?>
                                    <li>
                                        <strong><?php echo esc_html($task->title); ?></strong><br>
                                        <small><?php echo esc_html($task->description); ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="ppd-text-muted">No pending tasks.</p>
                        <?php endif; ?>
                        <a href="#" class="ppd-btn ppd-btn-secondary" data-section="tasks">View All Tasks</a>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="ppd-card ppd-card-highlight">
                    <div class="ppd-card-header">
                        <h3>⚡ Quick Actions</h3>
                    </div>
                    <div class="ppd-card-body">
                        <div class="ppd-quick-actions">
                            <button class="ppd-action-btn" data-section="leads">
                                <span class="ppd-action-icon">➕</span>
                                <span>Add Lead</span>
                            </button>
                            <button class="ppd-action-btn" data-section="profile">
                                <span class="ppd-action-icon">👤</span>
                                <span>Update Profile</span>
                            </button>
                            <button class="ppd-action-btn" data-section="documents">
                                <span class="ppd-action-icon">📄</span>
                                <span>View Documents</span>
                            </button>
                            <button class="ppd-action-btn" data-section="analytics">
                                <span class="ppd-action-icon">📊</span>
                                <span>Analytics</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Services Card -->
                <div class="ppd-card">
                    <div class="ppd-card-header">
                        <h3>🛎️ My Services</h3>
                    </div>
                    <div class="ppd-card-body">
                        <?php
                        $services = PPD_Services::get_partner_services($partner_id);
                        if (!empty($services)): ?>
                            <ul class="ppd-simple-list">
                                <?php foreach ($services as $service): ?>
                                    <li>✓ <?php echo esc_html($service->service_name); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="ppd-text-muted">No services assigned yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Full Sections (Hidden by default, shown when clicked) -->
            <div id="ppd-full-sections" style="display:none;">
                <!-- Sections will be loaded here via AJAX or shown/hidden -->
            </div>

        </div>
        <?php
        return ob_get_clean();
    }
}
