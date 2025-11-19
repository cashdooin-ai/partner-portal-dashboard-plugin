<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>Leads Management</h1>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Partner</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Notes</th>
                <th>Admin Notes</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($leads)): ?>
                <tr>
                    <td colspan="10">No leads found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($leads as $lead):
                    $partner = get_userdata($lead->partner_id);
                ?>
                    <tr>
                        <td><?php echo esc_html($lead->id); ?></td>
                        <td><?php echo $partner ? esc_html($partner->display_name) : 'N/A'; ?></td>
                        <td><?php echo esc_html($lead->student_name); ?></td>
                        <td><?php echo esc_html($lead->student_email); ?></td>
                        <td><?php echo esc_html($lead->student_phone); ?></td>
                        <td>
                            <span class="ppd-status-badge status-<?php echo esc_attr($lead->status); ?>">
                                <?php echo esc_html(ucfirst($lead->status)); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html($lead->notes); ?></td>
                        <td><?php echo esc_html($lead->admin_notes); ?></td>
                        <td><?php echo esc_html(date('M d, Y', strtotime($lead->created_at))); ?></td>
                        <td>
                            <button class="button button-small ppd-edit-lead" data-lead-id="<?php echo esc_attr($lead->id); ?>">Edit</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
