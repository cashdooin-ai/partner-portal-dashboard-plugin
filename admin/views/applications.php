<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>Partner Applications</h1>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Company</th>
                <th>Message</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($applications)): ?>
                <tr>
                    <td colspan="9">No applications found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($applications as $application): ?>
                    <tr>
                        <td><?php echo esc_html($application->id); ?></td>
                        <td><?php echo esc_html($application->name); ?></td>
                        <td><?php echo esc_html($application->email); ?></td>
                        <td><?php echo esc_html($application->phone); ?></td>
                        <td><?php echo esc_html($application->company_name); ?></td>
                        <td><?php echo esc_html(substr($application->message, 0, 50)) . '...'; ?></td>
                        <td>
                            <span class="ppd-status-badge status-<?php echo esc_attr($application->status); ?>">
                                <?php echo esc_html(ucfirst($application->status)); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html(date('M d, Y', strtotime($application->created_at))); ?></td>
                        <td>
                            <?php if ($application->status === 'pending'): ?>
                                <button class="button button-primary button-small ppd-approve-application" data-application-id="<?php echo esc_attr($application->id); ?>">
                                    Approve & Create Partner
                                </button>
                            <?php else: ?>
                                <span class="dashicons dashicons-yes-alt" style="color: green;"></span> Approved
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
