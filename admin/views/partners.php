<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>Partner Management</h1>

    <div class="ppd-admin-stats">
        <div class="ppd-admin-stat-box">
            <h3><?php echo count($partners); ?></h3>
            <p>Total Partners</p>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Company</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Joined Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($partners)): ?>
                <tr>
                    <td colspan="8">No partners found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($partners as $partner): ?>
                    <tr>
                        <td><?php echo esc_html($partner['id']); ?></td>
                        <td><?php echo esc_html($partner['name']); ?></td>
                        <td><?php echo esc_html($partner['email']); ?></td>
                        <td><?php echo esc_html($partner['company_name']); ?></td>
                        <td><?php echo esc_html($partner['phone']); ?></td>
                        <td>
                            <span class="ppd-status-badge status-<?php echo esc_attr($partner['status']); ?>">
                                <?php echo esc_html(ucfirst($partner['status'])); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html($partner['joined_date'] ? date('M d, Y', strtotime($partner['joined_date'])) : '-'); ?></td>
                        <td>
                            <a href="<?php echo admin_url('user-edit.php?user_id=' . $partner['id']); ?>" class="button button-small">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
