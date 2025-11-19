<?php
/**
 * Dashboard Handler Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Dashboard {

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
            return self::render_login_message();
        }

        $partner_id = PPD_Auth::get_current_partner_id();
        $partner_data = PPD_Partner::get_partner_data($partner_id);
        $stats = PPD_Partner::get_partner_stats($partner_id);

        ob_start();
        ?>
        <div class="ppd-dashboard-wrapper">
            <div class="ppd-dashboard-header">
                <h1>Welcome, <?php echo esc_html($partner_data['name']); ?>!</h1>
                <a href="<?php echo esc_url(PPD_Auth::get_logout_url()); ?>" class="ppd-logout-btn">Logout</a>
            </div>

            <div class="ppd-dashboard-stats">
                <div class="ppd-stat-card">
                    <div class="ppd-stat-icon">🏫</div>
                    <div class="ppd-stat-content">
                        <h3><?php echo esc_html($stats['colleges_count']); ?></h3>
                        <p>Colleges Assigned</p>
                    </div>
                </div>

                <div class="ppd-stat-card">
                    <div class="ppd-stat-icon">🛎️</div>
                    <div class="ppd-stat-content">
                        <h3><?php echo esc_html($stats['services_count']); ?></h3>
                        <p>Services</p>
                    </div>
                </div>

                <div class="ppd-stat-card">
                    <div class="ppd-stat-icon">📋</div>
                    <div class="ppd-stat-content">
                        <h3><?php echo esc_html($stats['pending_tasks']); ?></h3>
                        <p>Pending Tasks</p>
                    </div>
                </div>

                <div class="ppd-stat-card">
                    <div class="ppd-stat-icon">👥</div>
                    <div class="ppd-stat-content">
                        <h3><?php echo esc_html($stats['leads_count']); ?></h3>
                        <p>Total Leads</p>
                    </div>
                </div>
            </div>

            <!-- Notifications Badge -->
            <?php
            $unread_count = PPD_Notifications::get_unread_count($partner_id);
            if ($unread_count > 0): ?>
                <div class="ppd-notifications-bell">
                    <span class="ppd-bell-icon" id="ppd-notifications-trigger">🔔</span>
                    <span class="ppd-notification-count"><?php echo esc_html($unread_count); ?></span>
                </div>
            <?php endif; ?>

            <div class="ppd-dashboard-navigation">
                <a href="#analytics" class="ppd-nav-tab active" data-tab="analytics">📊 Analytics</a>
                <a href="#profile" class="ppd-nav-tab" data-tab="profile">👤 Profile</a>
                <a href="#communication" class="ppd-nav-tab" data-tab="communication">💬 Communication</a>
                <a href="#calendar" class="ppd-nav-tab" data-tab="calendar">📅 Calendar</a>
                <a href="#performance" class="ppd-nav-tab" data-tab="performance">🏆 Performance</a>
                <a href="#referrals" class="ppd-nav-tab" data-tab="referrals">🤝 Referrals</a>
                <a href="#students" class="ppd-nav-tab" data-tab="students">🎓 Students</a>
                <a href="#pipeline" class="ppd-nav-tab" data-tab="pipeline">🔄 Pipeline</a>
                <a href="#leads" class="ppd-nav-tab" data-tab="leads">👥 Leads</a>
                <a href="#commissions" class="ppd-nav-tab" data-tab="commissions">💰 Earnings</a>
                <a href="#colleges" class="ppd-nav-tab" data-tab="colleges">🏫 Colleges</a>
                <a href="#services" class="ppd-nav-tab" data-tab="services">🛎️ Services</a>
                <a href="#tasks" class="ppd-nav-tab" data-tab="tasks">📋 Tasks</a>
                <a href="#documents" class="ppd-nav-tab" data-tab="documents">📄 Documents</a>
                <a href="#activity" class="ppd-nav-tab" data-tab="activity">⏱️ Activity</a>
                <a href="#google-sheets" class="ppd-nav-tab" data-tab="google-sheets">📊 Sheets</a>
            </div>

            <div class="ppd-dashboard-content">
                <div id="ppd-tab-analytics" class="ppd-tab-content active">
                    <?php echo self::render_analytics_tab($partner_id); ?>
                </div>

                <div id="ppd-tab-profile" class="ppd-tab-content">
                    <?php echo self::render_profile_tab($partner_data); ?>
                </div>

                <div id="ppd-tab-pipeline" class="ppd-tab-content">
                    <?php echo self::render_pipeline_tab($partner_id); ?>
                </div>

                <div id="ppd-tab-leads" class="ppd-tab-content">
                    <?php echo self::render_leads_tab($partner_id); ?>
                </div>

                <div id="ppd-tab-commissions" class="ppd-tab-content">
                    <?php echo self::render_commissions_tab($partner_id); ?>
                </div>

                <div id="ppd-tab-colleges" class="ppd-tab-content">
                    <?php echo self::render_colleges_tab($partner_id); ?>
                </div>

                <div id="ppd-tab-services" class="ppd-tab-content">
                    <?php echo self::render_services_tab($partner_id); ?>
                </div>

                <div id="ppd-tab-tasks" class="ppd-tab-content">
                    <?php echo self::render_tasks_tab($partner_id); ?>
                </div>

                <div id="ppd-tab-documents" class="ppd-tab-content">
                    <?php echo self::render_documents_tab($partner_id); ?>
                </div>

                <div id="ppd-tab-google-sheets" class="ppd-tab-content">
                    <?php echo self::render_google_sheets_tab($partner_id); ?>
                </div>

                <div id="ppd-tab-communication" class="ppd-tab-content">
                    <?php echo self::render_communication_tab($partner_id); ?>
                </div>

                <div id="ppd-tab-calendar" class="ppd-tab-content">
                    <?php echo self::render_calendar_tab($partner_id); ?>
                </div>

                <div id="ppd-tab-performance" class="ppd-tab-content">
                    <?php echo self::render_performance_tab($partner_id); ?>
                </div>

                <div id="ppd-tab-referrals" class="ppd-tab-content">
                    <?php echo self::render_referrals_tab($partner_id); ?>
                </div>

                <div id="ppd-tab-students" class="ppd-tab-content">
                    <?php echo self::render_students_tab($partner_id); ?>
                </div>

                <div id="ppd-tab-activity" class="ppd-tab-content">
                    <?php echo self::render_activity_tab($partner_id); ?>
                </div>
            </div>

            <!-- Notifications Panel -->
            <div id="ppd-notifications-panel" class="ppd-notifications-panel" style="display:none;">
                <?php echo self::render_notifications_panel($partner_id); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_login_message() {
        return '<div class="ppd-login-required">
            <p>Please <a href="' . home_url('/partner-login/') . '">login</a> to access the partner dashboard.</p>
        </div>';
    }

    private static function render_profile_tab($partner_data) {
        ob_start();
        ?>
        <div class="ppd-profile-section">
            <h2>My Profile</h2>
            <form id="ppd-profile-form" class="ppd-form">
                <div class="ppd-form-row">
                    <div class="ppd-form-group">
                        <label for="display_name">Full Name *</label>
                        <input type="text" id="display_name" name="display_name" value="<?php echo esc_attr($partner_data['name']); ?>" required>
                    </div>

                    <div class="ppd-form-group">
                        <label for="user_email">Email *</label>
                        <input type="email" id="user_email" name="user_email" value="<?php echo esc_attr($partner_data['email']); ?>" required>
                    </div>
                </div>

                <div class="ppd-form-row">
                    <div class="ppd-form-group">
                        <label for="company_name">Company Name</label>
                        <input type="text" id="company_name" name="company_name" value="<?php echo esc_attr($partner_data['company_name']); ?>">
                    </div>

                    <div class="ppd-form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" value="<?php echo esc_attr($partner_data['phone']); ?>">
                    </div>
                </div>

                <div class="ppd-form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3"><?php echo esc_textarea($partner_data['address']); ?></textarea>
                </div>

                <div class="ppd-form-row">
                    <div class="ppd-form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="<?php echo esc_attr($partner_data['city']); ?>">
                    </div>

                    <div class="ppd-form-group">
                        <label for="state">State</label>
                        <input type="text" id="state" name="state" value="<?php echo esc_attr($partner_data['state']); ?>">
                    </div>
                </div>

                <div class="ppd-form-row">
                    <div class="ppd-form-group">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" value="<?php echo esc_attr($partner_data['country']); ?>">
                    </div>

                    <div class="ppd-form-group">
                        <label for="pincode">Pincode</label>
                        <input type="text" id="pincode" name="pincode" value="<?php echo esc_attr($partner_data['pincode']); ?>">
                    </div>
                </div>

                <div class="ppd-form-group">
                    <label for="website">Website</label>
                    <input type="url" id="website" name="website" value="<?php echo esc_url($partner_data['website']); ?>">
                </div>

                <div class="ppd-form-group">
                    <label for="description">About You</label>
                    <textarea id="description" name="description" rows="5"><?php echo esc_textarea($partner_data['description']); ?></textarea>
                </div>

                <button type="submit" class="ppd-btn ppd-btn-primary">Update Profile</button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_colleges_tab($partner_id) {
        $colleges = PPD_Colleges::get_partner_colleges($partner_id);

        ob_start();
        ?>
        <div class="ppd-colleges-section">
            <h2>My Assigned Colleges</h2>

            <?php if (empty($colleges)): ?>
                <p class="ppd-no-data">No colleges assigned yet.</p>
            <?php else: ?>
                <div class="ppd-table-wrapper">
                    <table class="ppd-table">
                        <thead>
                            <tr>
                                <th>College Name</th>
                                <th>Location</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Contact Person</th>
                                <th>Contact Email</th>
                                <th>Contact Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($colleges as $college): ?>
                                <tr>
                                    <td><?php echo esc_html($college->name); ?></td>
                                    <td><?php echo esc_html($college->location); ?></td>
                                    <td><?php echo esc_html($college->city); ?></td>
                                    <td><?php echo esc_html($college->state); ?></td>
                                    <td><?php echo esc_html($college->contact_person); ?></td>
                                    <td><?php echo esc_html($college->contact_email); ?></td>
                                    <td><?php echo esc_html($college->contact_phone); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_services_tab($partner_id) {
        $services = PPD_Services::get_partner_services($partner_id);

        ob_start();
        ?>
        <div class="ppd-services-section">
            <h2>My Assigned Services</h2>

            <?php if (empty($services)): ?>
                <p class="ppd-no-data">No services assigned yet.</p>
            <?php else: ?>
                <div class="ppd-services-grid">
                    <?php foreach ($services as $service): ?>
                        <div class="ppd-service-card">
                            <h3><?php echo esc_html($service->name); ?></h3>
                            <p><?php echo esc_html($service->description); ?></p>
                            <?php if ($service->price > 0): ?>
                                <div class="ppd-service-price">₹<?php echo number_format($service->price, 2); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_tasks_tab($partner_id) {
        $tasks = PPD_Tasks::get_partner_tasks($partner_id);

        ob_start();
        ?>
        <div class="ppd-tasks-section">
            <h2>My Tasks</h2>

            <?php if (empty($tasks)): ?>
                <p class="ppd-no-data">No tasks assigned yet.</p>
            <?php else: ?>
                <div class="ppd-tasks-list">
                    <?php foreach ($tasks as $task): ?>
                        <div class="ppd-task-item <?php echo esc_attr($task->status); ?>">
                            <div class="ppd-task-header">
                                <h3><?php echo esc_html($task->title); ?></h3>
                                <span class="ppd-task-priority priority-<?php echo esc_attr($task->priority); ?>">
                                    <?php echo esc_html(ucfirst($task->priority)); ?>
                                </span>
                            </div>
                            <p><?php echo esc_html($task->description); ?></p>
                            <div class="ppd-task-meta">
                                <span class="ppd-task-status">Status: <?php echo esc_html(ucfirst($task->status)); ?></span>
                                <?php if ($task->due_date): ?>
                                    <span class="ppd-task-due">Due: <?php echo esc_html(date('M d, Y', strtotime($task->due_date))); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($task->status !== 'completed'): ?>
                                <button class="ppd-btn ppd-btn-small ppd-mark-task-complete" data-task-id="<?php echo esc_attr($task->id); ?>">
                                    Mark as Complete
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_leads_tab($partner_id) {
        $leads = PPD_Leads::get_partner_leads($partner_id);

        ob_start();
        ?>
        <div class="ppd-leads-section">
            <div class="ppd-section-header">
                <h2>Leads Board</h2>
                <button class="ppd-btn ppd-btn-primary" id="ppd-add-lead-btn">Add New Lead</button>
            </div>

            <div id="ppd-add-lead-form" class="ppd-modal" style="display:none;">
                <div class="ppd-modal-content">
                    <span class="ppd-modal-close">&times;</span>
                    <h3>Add New Lead</h3>
                    <form id="ppd-lead-form" class="ppd-form">
                        <div class="ppd-form-group">
                            <label for="student_name">Student Name *</label>
                            <input type="text" id="student_name" name="student_name" required>
                        </div>

                        <div class="ppd-form-group">
                            <label for="student_email">Student Email</label>
                            <input type="email" id="student_email" name="student_email">
                        </div>

                        <div class="ppd-form-group">
                            <label for="student_phone">Student Phone *</label>
                            <input type="text" id="student_phone" name="student_phone" required>
                        </div>

                        <div class="ppd-form-group">
                            <label for="notes">Notes</label>
                            <textarea id="notes" name="notes" rows="4"></textarea>
                        </div>

                        <button type="submit" class="ppd-btn ppd-btn-primary">Add Lead</button>
                    </form>
                </div>
            </div>

            <?php if (empty($leads)): ?>
                <p class="ppd-no-data">No leads added yet.</p>
            <?php else: ?>
                <div class="ppd-table-wrapper">
                    <table class="ppd-table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Admin Notes</th>
                                <th>Date Added</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leads as $lead): ?>
                                <tr>
                                    <td><?php echo esc_html($lead->student_name); ?></td>
                                    <td><?php echo esc_html($lead->student_email); ?></td>
                                    <td><?php echo esc_html($lead->student_phone); ?></td>
                                    <td>
                                        <span class="ppd-lead-status status-<?php echo esc_attr($lead->status); ?>">
                                            <?php echo esc_html(ucfirst($lead->status)); ?>
                                        </span>
                                    </td>
                                    <td><?php echo esc_html($lead->notes); ?></td>
                                    <td><?php echo esc_html($lead->admin_notes); ?></td>
                                    <td><?php echo esc_html(date('M d, Y', strtotime($lead->created_at))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_google_sheets_tab($partner_id) {
        $config = PPD_Google_Sheets::get_partner_config($partner_id);

        ob_start();
        ?>
        <div class="ppd-google-sheets-section">
            <h2>Google Sheets Integration</h2>
            <p>Connect your Google Sheets to automatically sync your leads data.</p>

            <form id="ppd-google-sheets-form" class="ppd-form">
                <div class="ppd-form-group">
                    <label for="sheet_id">Google Sheet ID</label>
                    <input type="text" id="sheet_id" name="sheet_id" value="<?php echo esc_attr($config ? $config->sheet_id : ''); ?>" placeholder="Enter your Google Sheet ID">
                    <small>You can find the Sheet ID in the URL: docs.google.com/spreadsheets/d/<strong>SHEET_ID</strong>/edit</small>
                </div>

                <div class="ppd-form-group">
                    <label for="sheet_name">Sheet Name</label>
                    <input type="text" id="sheet_name" name="sheet_name" value="<?php echo esc_attr($config ? $config->sheet_name : ''); ?>" placeholder="Sheet1">
                </div>

                <div class="ppd-form-group">
                    <label>
                        <input type="checkbox" id="sync_enabled" name="sync_enabled" value="1" <?php checked($config && $config->sync_enabled, 1); ?>>
                        Enable Auto Sync
                    </label>
                </div>

                <?php if ($config && $config->last_sync): ?>
                    <p class="ppd-info">Last synced: <?php echo esc_html(date('M d, Y h:i A', strtotime($config->last_sync))); ?></p>
                <?php endif; ?>

                <button type="submit" class="ppd-btn ppd-btn-primary">Save Settings</button>
                <button type="button" class="ppd-btn ppd-btn-secondary" id="ppd-sync-now-btn">Sync Now</button>
            </form>

            <div class="ppd-info-box">
                <h4>Setup Instructions:</h4>
                <ol>
                    <li>Create a Google Sheet or use an existing one</li>
                    <li>Copy the Sheet ID from the URL</li>
                    <li>Make sure the sheet is shared with view access (for reading) or edit access (for writing)</li>
                    <li>Enter the Sheet ID and Sheet Name above</li>
                    <li>Enable Auto Sync to automatically sync your leads</li>
                </ol>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_analytics_tab($partner_id) {
        $analytics = PPD_Analytics::get_partner_analytics($partner_id);
        $comparison = PPD_Analytics::get_comparison_data($partner_id);

        ob_start();
        ?>
        <div class="ppd-analytics-section">
            <h2>Analytics Dashboard</h2>

            <div class="ppd-analytics-overview">
                <div class="ppd-analytics-card">
                    <h3>Performance Overview</h3>
                    <div class="ppd-analytics-metrics">
                        <div class="ppd-metric">
                            <span class="ppd-metric-label">Conversion Rate</span>
                            <span class="ppd-metric-value"><?php echo esc_html($analytics['conversion_rate']); ?>%</span>
                        </div>
                        <div class="ppd-metric">
                            <span class="ppd-metric-label">Task Completion</span>
                            <span class="ppd-metric-value"><?php echo esc_html($analytics['task_completion_rate']); ?>%</span>
                        </div>
                        <div class="ppd-metric">
                            <span class="ppd-metric-label">This Month</span>
                            <span class="ppd-metric-value"><?php echo esc_html($comparison['current_month']); ?> leads</span>
                            <?php if ($comparison['trend'] == 'up'): ?>
                                <span class="ppd-trend-up">↑ <?php echo abs($comparison['percentage_change']); ?>%</span>
                            <?php else: ?>
                                <span class="ppd-trend-down">↓ <?php echo abs($comparison['percentage_change']); ?>%</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ppd-charts-grid">
                <div class="ppd-chart-card">
                    <h3>Lead Status Distribution</h3>
                    <canvas id="ppd-status-chart"></canvas>
                </div>

                <div class="ppd-chart-card">
                    <h3>Monthly Trend</h3>
                    <canvas id="ppd-monthly-chart"></canvas>
                </div>

                <div class="ppd-chart-card">
                    <h3>Top Colleges by Leads</h3>
                    <canvas id="ppd-college-chart"></canvas>
                </div>

                <div class="ppd-chart-card">
                    <h3>Earnings Summary</h3>
                    <div class="ppd-earnings-summary">
                        <div class="ppd-earnings-item">
                            <span>Total Earned:</span>
                            <strong>₹<?php echo number_format($analytics['total_earnings'], 2); ?></strong>
                        </div>
                        <div class="ppd-earnings-item">
                            <span>Pending:</span>
                            <strong>₹<?php echo number_format($analytics['pending_earnings'], 2); ?></strong>
                        </div>
                        <div class="ppd-earnings-item">
                            <span>This Period:</span>
                            <strong>₹<?php echo number_format($analytics['period_earnings'], 2); ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                var analyticsData = <?php echo json_encode($analytics); ?>;
            </script>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_pipeline_tab($partner_id) {
        $pipeline_data = PPD_Pipeline::get_leads_by_stage($partner_id);
        $stats = PPD_Pipeline::get_pipeline_stats($partner_id);

        ob_start();
        ?>
        <div class="ppd-pipeline-section">
            <div class="ppd-pipeline-header">
                <h2>Lead Pipeline</h2>
                <div class="ppd-pipeline-stats">
                    <span>Total: <?php echo esc_html($stats['total_leads']); ?></span>
                    <span>Won: <?php echo esc_html($stats['won_leads']); ?></span>
                    <span>Lost: <?php echo esc_html($stats['lost_leads']); ?></span>
                    <span>Win Rate: <?php echo esc_html($stats['win_rate']); ?>%</span>
                </div>
            </div>

            <div class="ppd-pipeline-board">
                <?php foreach ($pipeline_data as $stage_key => $stage_data): ?>
                    <div class="ppd-pipeline-column" data-stage="<?php echo esc_attr($stage_key); ?>">
                        <div class="ppd-pipeline-column-header" style="background-color: <?php echo esc_attr($stage_data['info']['color']); ?>">
                            <span class="ppd-stage-icon"><?php echo $stage_data['info']['icon']; ?></span>
                            <span class="ppd-stage-label"><?php echo esc_html($stage_data['info']['label']); ?></span>
                            <span class="ppd-stage-count"><?php echo esc_html($stage_data['count']); ?></span>
                        </div>
                        <div class="ppd-pipeline-cards" data-stage="<?php echo esc_attr($stage_key); ?>">
                            <?php foreach ($stage_data['leads'] as $lead): ?>
                                <div class="ppd-pipeline-card" data-lead-id="<?php echo esc_attr($lead->id); ?>">
                                    <h4><?php echo esc_html($lead->student_name); ?></h4>
                                    <p><?php echo esc_html($lead->student_email); ?></p>
                                    <p><?php echo esc_html($lead->student_phone); ?></p>
                                    <?php if ($lead->notes): ?>
                                        <div class="ppd-lead-notes"><?php echo esc_html(substr($lead->notes, 0, 50)) . '...'; ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_commissions_tab($partner_id) {
        $commissions = PPD_Commissions::get_partner_commissions($partner_id);
        $summary = PPD_Commissions::get_partner_earnings_summary($partner_id);
        $payment_history = PPD_Commissions::get_payment_history($partner_id);

        ob_start();
        ?>
        <div class="ppd-commissions-section">
            <h2>Earnings & Commissions</h2>

            <div class="ppd-earnings-cards">
                <div class="ppd-earnings-card">
                    <div class="ppd-earnings-icon">💰</div>
                    <div class="ppd-earnings-content">
                        <h3>₹<?php echo number_format($summary['total_earned'], 2); ?></h3>
                        <p>Total Earned</p>
                    </div>
                </div>

                <div class="ppd-earnings-card">
                    <div class="ppd-earnings-icon">⏳</div>
                    <div class="ppd-earnings-content">
                        <h3>₹<?php echo number_format($summary['pending_amount'], 2); ?></h3>
                        <p>Pending Amount</p>
                    </div>
                </div>

                <div class="ppd-earnings-card">
                    <div class="ppd-earnings-icon">📅</div>
                    <div class="ppd-earnings-content">
                        <h3>₹<?php echo number_format($summary['this_month'], 2); ?></h3>
                        <p>This Month</p>
                    </div>
                </div>

                <div class="ppd-earnings-card">
                    <div class="ppd-earnings-icon">📊</div>
                    <div class="ppd-earnings-content">
                        <h3><?php echo esc_html($summary['total_commissions']); ?></h3>
                        <p>Total Commissions</p>
                    </div>
                </div>
            </div>

            <h3>All Commissions</h3>
            <?php if (empty($commissions)): ?>
                <p class="ppd-no-data">No commissions yet.</p>
            <?php else: ?>
                <div class="ppd-table-wrapper">
                    <table class="ppd-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Paid Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commissions as $commission): ?>
                                <tr>
                                    <td><?php echo esc_html(date('M d, Y', strtotime($commission->created_at))); ?></td>
                                    <td><?php echo esc_html($commission->description); ?></td>
                                    <td><?php echo esc_html(ucfirst($commission->commission_type)); ?></td>
                                    <td>₹<?php echo number_format($commission->amount, 2); ?></td>
                                    <td>
                                        <span class="ppd-status-badge status-<?php echo esc_attr($commission->status); ?>">
                                            <?php echo esc_html(ucfirst($commission->status)); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $commission->paid_date ? esc_html(date('M d, Y', strtotime($commission->paid_date))) : '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_documents_tab($partner_id) {
        $documents = PPD_Documents::get_documents($partner_id);
        $categories = PPD_Documents::get_categories();

        ob_start();
        ?>
        <div class="ppd-documents-section">
            <h2>Documents & Resources</h2>

            <div class="ppd-documents-filter">
                <select id="ppd-document-category-filter">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $key => $label): ?>
                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (empty($documents)): ?>
                <p class="ppd-no-data">No documents available yet.</p>
            <?php else: ?>
                <div class="ppd-documents-grid">
                    <?php foreach ($documents as $document): ?>
                        <div class="ppd-document-card" data-category="<?php echo esc_attr($document->category); ?>">
                            <div class="ppd-document-icon">
                                <?php
                                $extension = pathinfo($document->file_name, PATHINFO_EXTENSION);
                                switch ($extension) {
                                    case 'pdf': echo '📄'; break;
                                    case 'doc':
                                    case 'docx': echo '📝'; break;
                                    case 'xls':
                                    case 'xlsx': echo '📊'; break;
                                    case 'ppt':
                                    case 'pptx': echo '📊'; break;
                                    case 'jpg':
                                    case 'jpeg':
                                    case 'png': echo '🖼️'; break;
                                    case 'zip': echo '📦'; break;
                                    default: echo '📁'; break;
                                }
                                ?>
                            </div>
                            <div class="ppd-document-info">
                                <h4><?php echo esc_html($document->title); ?></h4>
                                <p><?php echo esc_html($document->description); ?></p>
                                <div class="ppd-document-meta">
                                    <span><?php echo esc_html($categories[$document->category]); ?></span>
                                    <span><?php echo PPD_Documents::format_file_size($document->file_size); ?></span>
                                </div>
                            </div>
                            <div class="ppd-document-actions">
                                <a href="<?php echo esc_url(PPD_Documents::get_download_url($document->id)); ?>" class="ppd-btn ppd-btn-small" download>
                                    Download
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_notifications_panel($partner_id) {
        $notifications = PPD_Notifications::get_user_notifications($partner_id);

        ob_start();
        ?>
        <div class="ppd-notifications-content">
            <div class="ppd-notifications-header">
                <h3>Notifications</h3>
                <button id="ppd-mark-all-read" class="ppd-btn ppd-btn-small">Mark All Read</button>
            </div>

            <div class="ppd-notifications-list">
                <?php if (empty($notifications)): ?>
                    <p class="ppd-no-data">No notifications yet.</p>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="ppd-notification-item <?php echo $notification->is_read ? 'read' : 'unread'; ?>" data-notification-id="<?php echo esc_attr($notification->id); ?>">
                            <div class="ppd-notification-icon ppd-notification-<?php echo esc_attr($notification->type); ?>">
                                <?php
                                switch ($notification->type) {
                                    case 'task': echo '📋'; break;
                                    case 'lead': echo '👥'; break;
                                    case 'commission': echo '💰'; break;
                                    case 'document': echo '📄'; break;
                                    case 'success': echo '✅'; break;
                                    case 'warning': echo '⚠️'; break;
                                    default: echo 'ℹ️'; break;
                                }
                                ?>
                            </div>
                            <div class="ppd-notification-content">
                                <h4><?php echo esc_html($notification->title); ?></h4>
                                <p><?php echo esc_html($notification->message); ?></p>
                                <span class="ppd-notification-time"><?php echo human_time_diff(strtotime($notification->created_at), current_time('timestamp')) . ' ago'; ?></span>
                            </div>
                            <?php if (!$notification->is_read): ?>
                                <button class="ppd-mark-read" data-notification-id="<?php echo esc_attr($notification->id); ?>">×</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_communication_tab($partner_id) {
        $messages = PPD_Communication::get_user_messages($partner_id);
        $tickets = PPD_Communication::get_user_tickets($partner_id);
        $announcements = PPD_Communication::get_active_announcements();
        $faqs = PPD_Communication::get_all_faqs();

        ob_start();
        ?>
        <div class="ppd-communication-section">
            <h2>Communication Center</h2>

            <div class="ppd-comm-tabs">
                <button class="ppd-comm-tab-btn active" data-comm-tab="messages">Messages</button>
                <button class="ppd-comm-tab-btn" data-comm-tab="tickets">Support Tickets</button>
                <button class="ppd-comm-tab-btn" data-comm-tab="announcements">Announcements</button>
                <button class="ppd-comm-tab-btn" data-comm-tab="faq">FAQ</button>
            </div>

            <!-- Messages Tab -->
            <div id="ppd-comm-messages" class="ppd-comm-tab-content active">
                <div class="ppd-section-header">
                    <h3>Direct Messages</h3>
                    <button class="ppd-btn ppd-btn-primary" id="ppd-new-message-btn">New Message</button>
                </div>

                <?php if (empty($messages)): ?>
                    <div class="ppd-empty-state">
                        <span class="ppd-empty-icon">✉️</span>
                        <p>No messages yet.</p>
                    </div>
                <?php else: ?>
                    <div class="ppd-messages-list">
                        <?php foreach ($messages as $message): ?>
                            <div class="ppd-message-card <?php echo $message->is_read ? '' : 'unread'; ?>">
                                <div class="ppd-message-header">
                                    <h4><?php echo esc_html($message->subject); ?></h4>
                                    <span class="ppd-message-date"><?php echo human_time_diff(strtotime($message->created_at), current_time('timestamp')) . ' ago'; ?></span>
                                </div>
                                <p><?php echo esc_html(substr($message->message, 0, 100)) . '...'; ?></p>
                                <button class="ppd-btn ppd-btn-small ppd-view-message" data-message-id="<?php echo esc_attr($message->id); ?>">View Message</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tickets Tab -->
            <div id="ppd-comm-tickets" class="ppd-comm-tab-content">
                <div class="ppd-section-header">
                    <h3>Support Tickets</h3>
                    <button class="ppd-btn ppd-btn-primary" id="ppd-new-ticket-btn">Create Ticket</button>
                </div>

                <?php if (empty($tickets)): ?>
                    <div class="ppd-empty-state">
                        <span class="ppd-empty-icon">🎫</span>
                        <p>No support tickets yet.</p>
                    </div>
                <?php else: ?>
                    <div class="ppd-tickets-grid">
                        <?php foreach ($tickets as $ticket): ?>
                            <div class="ppd-ticket-card">
                                <div class="ppd-ticket-header">
                                    <h4>#<?php echo esc_html($ticket->id); ?> - <?php echo esc_html($ticket->subject); ?></h4>
                                    <span class="ppd-ticket-priority priority-<?php echo esc_attr($ticket->priority); ?>">
                                        <?php echo esc_html(ucfirst($ticket->priority)); ?>
                                    </span>
                                </div>
                                <p><?php echo esc_html(substr($ticket->message, 0, 80)) . '...'; ?></p>
                                <div class="ppd-ticket-meta">
                                    <span class="ppd-ticket-status status-<?php echo esc_attr($ticket->status); ?>">
                                        <?php echo esc_html(str_replace('_', ' ', ucfirst($ticket->status))); ?>
                                    </span>
                                    <span class="ppd-ticket-category"><?php echo esc_html($ticket->category); ?></span>
                                    <span class="ppd-ticket-date"><?php echo date('M d, Y', strtotime($ticket->created_at)); ?></span>
                                </div>
                                <button class="ppd-btn ppd-btn-small ppd-view-ticket" data-ticket-id="<?php echo esc_attr($ticket->id); ?>">View Ticket</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Announcements Tab -->
            <div id="ppd-comm-announcements" class="ppd-comm-tab-content">
                <h3>Announcements</h3>

                <?php if (empty($announcements)): ?>
                    <div class="ppd-empty-state">
                        <span class="ppd-empty-icon">📢</span>
                        <p>No announcements at this time.</p>
                    </div>
                <?php else: ?>
                    <div class="ppd-announcements-list">
                        <?php foreach ($announcements as $announcement): ?>
                            <div class="ppd-announcement-card <?php echo $announcement->is_pinned ? 'pinned' : ''; ?> type-<?php echo esc_attr($announcement->announcement_type); ?>">
                                <?php if ($announcement->is_pinned): ?>
                                    <span class="ppd-pinned-badge">📌 Pinned</span>
                                <?php endif; ?>
                                <h4><?php echo esc_html($announcement->title); ?></h4>
                                <p><?php echo nl2br(esc_html($announcement->content)); ?></p>
                                <span class="ppd-announcement-date"><?php echo date('M d, Y', strtotime($announcement->created_at)); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- FAQ Tab -->
            <div id="ppd-comm-faq" class="ppd-comm-tab-content">
                <h3>Frequently Asked Questions</h3>

                <?php if (empty($faqs)): ?>
                    <div class="ppd-empty-state">
                        <span class="ppd-empty-icon">❓</span>
                        <p>No FAQs available yet.</p>
                    </div>
                <?php else: ?>
                    <div class="ppd-faq-list">
                        <?php
                        $categories = [];
                        foreach ($faqs as $faq) {
                            $categories[$faq->category][] = $faq;
                        }

                        foreach ($categories as $category => $category_faqs): ?>
                            <div class="ppd-faq-category">
                                <h4><?php echo esc_html(ucfirst($category)); ?></h4>
                                <?php foreach ($category_faqs as $faq): ?>
                                    <div class="ppd-faq-item">
                                        <div class="ppd-faq-question">
                                            <strong>Q:</strong> <?php echo esc_html($faq->question); ?>
                                        </div>
                                        <div class="ppd-faq-answer">
                                            <strong>A:</strong> <?php echo nl2br(esc_html($faq->answer)); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_calendar_tab($partner_id) {
        $upcoming_events = PPD_Calendar::get_upcoming_events($partner_id, 20);

        ob_start();
        ?>
        <div class="ppd-calendar-section">
            <h2>Calendar & Events</h2>

            <div class="ppd-calendar-header">
                <button class="ppd-btn ppd-btn-secondary" id="ppd-calendar-view-btn">📅 Calendar View</button>
            </div>

            <h3>Upcoming Events</h3>

            <?php if (empty($upcoming_events)): ?>
                <div class="ppd-empty-state">
                    <span class="ppd-empty-icon">📅</span>
                    <p>No upcoming events scheduled.</p>
                </div>
            <?php else: ?>
                <div class="ppd-events-timeline">
                    <?php foreach ($upcoming_events as $event): ?>
                        <div class="ppd-event-card type-<?php echo esc_attr($event->event_type); ?>">
                            <div class="ppd-event-date-badge">
                                <div class="ppd-event-month"><?php echo date('M', strtotime($event->start_time)); ?></div>
                                <div class="ppd-event-day"><?php echo date('d', strtotime($event->start_time)); ?></div>
                            </div>
                            <div class="ppd-event-content">
                                <h4><?php echo esc_html($event->title); ?></h4>
                                <p><?php echo esc_html($event->description); ?></p>
                                <div class="ppd-event-meta">
                                    <span class="ppd-event-type">
                                        <?php
                                        $type_icons = [
                                            'meeting' => '👥',
                                            'training' => '📚',
                                            'deadline' => '⏰',
                                            'college_visit' => '🏫',
                                            'webinar' => '💻',
                                            'conference' => '🎤'
                                        ];
                                        echo $type_icons[$event->event_type] ?? '📌';
                                        ?>
                                        <?php echo esc_html(str_replace('_', ' ', ucfirst($event->event_type))); ?>
                                    </span>
                                    <span class="ppd-event-time">
                                        🕒 <?php echo date('h:i A', strtotime($event->start_time)); ?>
                                        <?php if ($event->end_time): ?>
                                            - <?php echo date('h:i A', strtotime($event->end_time)); ?>
                                        <?php endif; ?>
                                    </span>
                                    <?php if ($event->location): ?>
                                        <span class="ppd-event-location">📍 <?php echo esc_html($event->location); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="ppd-event-rsvp">
                                    <span class="ppd-rsvp-status status-<?php echo esc_attr($event->rsvp_status); ?>">
                                        <?php echo esc_html(ucfirst($event->rsvp_status)); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_performance_tab($partner_id) {
        $targets = PPD_Performance::get_partner_targets($partner_id);
        $leaderboard = PPD_Performance::get_leaderboard('month', 10);
        $partner_rank = PPD_Performance::get_partner_rank($partner_id, 'month');

        ob_start();
        ?>
        <div class="ppd-performance-section">
            <h2>Performance Metrics</h2>

            <!-- Rank Card -->
            <div class="ppd-rank-card">
                <div class="ppd-rank-content">
                    <h3>Your Current Rank</h3>
                    <div class="ppd-rank-display">
                        <span class="ppd-rank-number">#<?php echo esc_html($partner_rank['rank']); ?></span>
                        <span class="ppd-rank-total">out of <?php echo esc_html($partner_rank['total']); ?> partners</span>
                    </div>
                </div>
                <div class="ppd-rank-icon">🏆</div>
            </div>

            <!-- Performance Targets -->
            <h3>My Targets</h3>

            <?php if (empty($targets)): ?>
                <div class="ppd-empty-state">
                    <span class="ppd-empty-icon">🎯</span>
                    <p>No targets set yet.</p>
                </div>
            <?php else: ?>
                <div class="ppd-targets-grid">
                    <?php foreach ($targets as $target):
                        $progress_percentage = ($target->current_value / $target->target_value) * 100;
                        $progress_percentage = min($progress_percentage, 100);
                        $status_class = $progress_percentage >= 100 ? 'achieved' : ($progress_percentage >= 75 ? 'on-track' : 'behind');
                    ?>
                        <div class="ppd-target-card <?php echo $status_class; ?>">
                            <div class="ppd-target-header">
                                <h4>
                                    <?php
                                    $type_icons = [
                                        'leads' => '👥',
                                        'conversions' => '✅',
                                        'revenue' => '💰',
                                        'students' => '🎓'
                                    ];
                                    echo $type_icons[$target->target_type] ?? '🎯';
                                    ?>
                                    <?php echo esc_html(ucfirst($target->target_type)); ?>
                                </h4>
                                <span class="ppd-target-period">
                                    <?php echo date('M d', strtotime($target->period_start)); ?> - <?php echo date('M d', strtotime($target->period_end)); ?>
                                </span>
                            </div>
                            <div class="ppd-target-progress">
                                <div class="ppd-progress-bar">
                                    <div class="ppd-progress-fill" style="width: <?php echo $progress_percentage; ?>%"></div>
                                </div>
                                <div class="ppd-progress-text">
                                    <span><?php echo esc_html($target->current_value); ?> / <?php echo esc_html($target->target_value); ?></span>
                                    <span><?php echo round($progress_percentage, 1); ?>%</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Leaderboard -->
            <h3>🏆 Partner Leaderboard</h3>

            <?php if (empty($leaderboard)): ?>
                <div class="ppd-empty-state">
                    <span class="ppd-empty-icon">🏆</span>
                    <p>No leaderboard data available.</p>
                </div>
            <?php else: ?>
                <div class="ppd-leaderboard">
                    <?php foreach ($leaderboard as $index => $entry):
                        $is_current_user = ($entry->partner_id == $partner_id);
                    ?>
                        <div class="ppd-leaderboard-item <?php echo $is_current_user ? 'current-user' : ''; ?> rank-<?php echo $index + 1; ?>">
                            <div class="ppd-leaderboard-rank">
                                <?php if ($index < 3): ?>
                                    <span class="ppd-medal">
                                        <?php echo $index === 0 ? '🥇' : ($index === 1 ? '🥈' : '🥉'); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="ppd-rank-number">#<?php echo $index + 1; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="ppd-leaderboard-info">
                                <h4><?php echo esc_html($entry->partner_name); ?> <?php echo $is_current_user ? '(You)' : ''; ?></h4>
                                <div class="ppd-leaderboard-stats">
                                    <span>👥 <?php echo esc_html($entry->total_leads); ?> leads</span>
                                    <span>✅ <?php echo esc_html($entry->conversions); ?> conversions</span>
                                    <span>📈 <?php echo esc_html($entry->conversion_rate); ?>% rate</span>
                                </div>
                            </div>
                            <div class="ppd-leaderboard-score">
                                <span class="ppd-score"><?php echo esc_html($entry->score); ?></span>
                                <span class="ppd-score-label">points</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_referrals_tab($partner_id) {
        $referrals = PPD_Referrals::get_partner_referrals($partner_id);
        $stats = PPD_Referrals::get_referral_stats($partner_id);

        ob_start();
        ?>
        <div class="ppd-referrals-section">
            <h2>Referral Program</h2>

            <!-- Referral Stats -->
            <div class="ppd-referral-stats-grid">
                <div class="ppd-referral-stat-card">
                    <div class="ppd-stat-icon">🤝</div>
                    <div class="ppd-stat-content">
                        <h3><?php echo esc_html($stats['total_referrals']); ?></h3>
                        <p>Total Referrals</p>
                    </div>
                </div>

                <div class="ppd-referral-stat-card">
                    <div class="ppd-stat-icon">✅</div>
                    <div class="ppd-stat-content">
                        <h3><?php echo esc_html($stats['approved_referrals']); ?></h3>
                        <p>Approved</p>
                    </div>
                </div>

                <div class="ppd-referral-stat-card">
                    <div class="ppd-stat-icon">⏳</div>
                    <div class="ppd-stat-content">
                        <h3><?php echo esc_html($stats['pending_referrals']); ?></h3>
                        <p>Pending</p>
                    </div>
                </div>

                <div class="ppd-referral-stat-card">
                    <div class="ppd-stat-icon">💰</div>
                    <div class="ppd-stat-content">
                        <h3>₹<?php echo number_format($stats['total_commission'], 2); ?></h3>
                        <p>Total Commission</p>
                    </div>
                </div>
            </div>

            <!-- Refer New Partner -->
            <div class="ppd-referral-form-card">
                <h3>Refer a New Partner</h3>
                <p>Earn commissions by referring new partners to our program!</p>

                <form id="ppd-referral-form" class="ppd-form">
                    <div class="ppd-form-row">
                        <div class="ppd-form-group">
                            <label for="referred_name">Full Name *</label>
                            <input type="text" id="referred_name" name="referred_name" required>
                        </div>

                        <div class="ppd-form-group">
                            <label for="referred_email">Email *</label>
                            <input type="email" id="referred_email" name="referred_email" required>
                        </div>
                    </div>

                    <div class="ppd-form-row">
                        <div class="ppd-form-group">
                            <label for="referred_phone">Phone</label>
                            <input type="text" id="referred_phone" name="referred_phone">
                        </div>

                        <div class="ppd-form-group">
                            <label for="referred_company">Company Name</label>
                            <input type="text" id="referred_company" name="referred_company">
                        </div>
                    </div>

                    <div class="ppd-form-group">
                        <label for="referral_notes">Notes (Optional)</label>
                        <textarea id="referral_notes" name="referral_notes" rows="3"></textarea>
                    </div>

                    <button type="submit" class="ppd-btn ppd-btn-primary">Submit Referral</button>
                </form>
            </div>

            <!-- My Referrals -->
            <h3>My Referrals</h3>

            <?php if (empty($referrals)): ?>
                <div class="ppd-empty-state">
                    <span class="ppd-empty-icon">🤝</span>
                    <p>No referrals yet. Start referring partners to earn commissions!</p>
                </div>
            <?php else: ?>
                <div class="ppd-table-wrapper">
                    <table class="ppd-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Commission</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($referrals as $referral): ?>
                                <tr>
                                    <td><?php echo esc_html($referral->referred_name); ?></td>
                                    <td><?php echo esc_html($referral->referred_email); ?></td>
                                    <td><?php echo esc_html($referral->referred_phone); ?></td>
                                    <td>
                                        <span class="ppd-status-badge status-<?php echo esc_attr($referral->status); ?>">
                                            <?php echo esc_html(ucfirst($referral->status)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($referral->commission_amount > 0): ?>
                                            ₹<?php echo number_format($referral->commission_amount, 2); ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($referral->created_at)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_students_tab($partner_id) {
        $students = PPD_Students::get_partner_students($partner_id);
        $stats = PPD_Students::get_partner_student_stats($partner_id);

        ob_start();
        ?>
        <div class="ppd-students-section">
            <h2>Student Management</h2>

            <!-- Student Stats -->
            <div class="ppd-student-stats-grid">
                <div class="ppd-student-stat-card">
                    <div class="ppd-stat-icon">🎓</div>
                    <div class="ppd-stat-content">
                        <h3><?php echo esc_html($stats['total_students']); ?></h3>
                        <p>Total Students</p>
                    </div>
                </div>

                <div class="ppd-student-stat-card">
                    <div class="ppd-stat-icon">✅</div>
                    <div class="ppd-stat-content">
                        <h3><?php echo esc_html($stats['admitted']); ?></h3>
                        <p>Admitted</p>
                    </div>
                </div>

                <div class="ppd-student-stat-card">
                    <div class="ppd-stat-icon">⏳</div>
                    <div class="ppd-stat-content">
                        <h3><?php echo esc_html($stats['in_progress']); ?></h3>
                        <p>In Progress</p>
                    </div>
                </div>

                <div class="ppd-student-stat-card">
                    <div class="ppd-stat-icon">📄</div>
                    <div class="ppd-stat-content">
                        <h3><?php echo esc_html($stats['pending_verification']); ?></h3>
                        <p>Pending Verification</p>
                    </div>
                </div>
            </div>

            <!-- Add Student Button -->
            <div class="ppd-section-header">
                <h3>All Students</h3>
                <button class="ppd-btn ppd-btn-primary" id="ppd-add-student-btn">Add Student</button>
            </div>

            <?php if (empty($students)): ?>
                <div class="ppd-empty-state">
                    <span class="ppd-empty-icon">🎓</span>
                    <p>No students added yet.</p>
                </div>
            <?php else: ?>
                <div class="ppd-students-grid">
                    <?php foreach ($students as $student): ?>
                        <div class="ppd-student-card">
                            <div class="ppd-student-header">
                                <h4><?php echo esc_html($student->student_name); ?></h4>
                                <span class="ppd-student-status status-<?php echo esc_attr($student->application_status); ?>">
                                    <?php echo esc_html(str_replace('_', ' ', ucfirst($student->application_status))); ?>
                                </span>
                            </div>
                            <div class="ppd-student-info">
                                <p><strong>Email:</strong> <?php echo esc_html($student->email); ?></p>
                                <p><strong>Phone:</strong> <?php echo esc_html($student->phone); ?></p>
                                <p><strong>Course:</strong> <?php echo esc_html($student->intended_course); ?></p>
                                <?php if ($student->college_name): ?>
                                    <p><strong>College:</strong> <?php echo esc_html($student->college_name); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="ppd-student-progress">
                                <div class="ppd-progress-item">
                                    <span>Application:</span>
                                    <span class="ppd-badge badge-<?php echo esc_attr($student->application_status); ?>">
                                        <?php echo esc_html(ucfirst($student->application_status)); ?>
                                    </span>
                                </div>
                                <div class="ppd-progress-item">
                                    <span>Documents:</span>
                                    <span class="ppd-badge badge-<?php echo esc_attr($student->document_status); ?>">
                                        <?php echo esc_html(ucfirst($student->document_status)); ?>
                                    </span>
                                </div>
                                <div class="ppd-progress-item">
                                    <span>Admission:</span>
                                    <span class="ppd-badge badge-<?php echo esc_attr($student->admission_status); ?>">
                                        <?php echo esc_html(ucfirst($student->admission_status)); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="ppd-student-actions">
                                <button class="ppd-btn ppd-btn-small ppd-view-student" data-student-id="<?php echo esc_attr($student->id); ?>">View Details</button>
                                <button class="ppd-btn ppd-btn-small ppd-upload-documents" data-student-id="<?php echo esc_attr($student->id); ?>">Upload Documents</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_activity_tab($partner_id) {
        $activities = PPD_Activity::get_user_activities($partner_id, 50);
        $login_history = PPD_Activity::get_login_history($partner_id, 10);
        $activity_summary = PPD_Activity::get_activity_summary($partner_id, 'week');

        ob_start();
        ?>
        <div class="ppd-activity-section">
            <h2>Activity Timeline</h2>

            <!-- Activity Summary -->
            <div class="ppd-activity-summary-grid">
                <?php foreach ($activity_summary as $action => $count): ?>
                    <div class="ppd-activity-summary-card">
                        <h3><?php echo esc_html($count); ?></h3>
                        <p><?php echo esc_html(ucfirst(str_replace('_', ' ', $action))); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Recent Activities -->
            <h3>Recent Activities</h3>

            <?php if (empty($activities)): ?>
                <div class="ppd-empty-state">
                    <span class="ppd-empty-icon">⏱️</span>
                    <p>No recent activities.</p>
                </div>
            <?php else: ?>
                <div class="ppd-timeline">
                    <?php foreach ($activities as $activity): ?>
                        <div class="ppd-timeline-item">
                            <div class="ppd-timeline-icon">
                                <?php
                                $icons = [
                                    'login' => '🔐',
                                    'profile_update' => '👤',
                                    'lead_created' => '👥',
                                    'task_completed' => '✅',
                                    'document_uploaded' => '📄',
                                    'message_sent' => '✉️',
                                    'ticket_created' => '🎫'
                                ];
                                echo $icons[$activity->action] ?? '📌';
                                ?>
                            </div>
                            <div class="ppd-timeline-content">
                                <h4><?php echo esc_html(ucfirst(str_replace('_', ' ', $activity->action))); ?></h4>
                                <?php if ($activity->description): ?>
                                    <p><?php echo esc_html($activity->description); ?></p>
                                <?php endif; ?>
                                <div class="ppd-timeline-meta">
                                    <span>🕒 <?php echo human_time_diff(strtotime($activity->created_at), current_time('timestamp')) . ' ago'; ?></span>
                                    <?php if ($activity->ip_address): ?>
                                        <span>🌐 <?php echo esc_html($activity->ip_address); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Login History -->
            <h3>Login History</h3>

            <?php if (empty($login_history)): ?>
                <div class="ppd-empty-state">
                    <span class="ppd-empty-icon">🔐</span>
                    <p>No login history available.</p>
                </div>
            <?php else: ?>
                <div class="ppd-table-wrapper">
                    <table class="ppd-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>IP Address</th>
                                <th>Device/Browser</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($login_history as $login): ?>
                                <tr>
                                    <td><?php echo date('M d, Y h:i A', strtotime($login->created_at)); ?></td>
                                    <td><?php echo esc_html($login->ip_address); ?></td>
                                    <td>
                                        <small><?php echo esc_html(substr($login->user_agent, 0, 60)) . '...'; ?></small>
                                    </td>
                                    <td>
                                        <span class="ppd-status-badge status-success">✅ Success</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
