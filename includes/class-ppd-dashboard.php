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
                <a href="#analytics" class="ppd-nav-tab active" data-tab="analytics">Analytics</a>
                <a href="#profile" class="ppd-nav-tab" data-tab="profile">Profile</a>
                <a href="#pipeline" class="ppd-nav-tab" data-tab="pipeline">Lead Pipeline</a>
                <a href="#leads" class="ppd-nav-tab" data-tab="leads">Leads Board</a>
                <a href="#commissions" class="ppd-nav-tab" data-tab="commissions">Earnings</a>
                <a href="#colleges" class="ppd-nav-tab" data-tab="colleges">My Colleges</a>
                <a href="#services" class="ppd-nav-tab" data-tab="services">Services</a>
                <a href="#tasks" class="ppd-nav-tab" data-tab="tasks">Tasks</a>
                <a href="#documents" class="ppd-nav-tab" data-tab="documents">Documents</a>
                <a href="#google-sheets" class="ppd-nav-tab" data-tab="google-sheets">Google Sheets</a>
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
}
