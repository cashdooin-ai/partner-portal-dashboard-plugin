<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>Tasks Management
        <button class="button button-primary" id="ppd-add-task-btn">Add New Task</button>
    </h1>

    <div id="ppd-add-task-modal" class="ppd-admin-modal" style="display:none;">
        <div class="ppd-admin-modal-content">
            <span class="ppd-admin-modal-close">&times;</span>
            <h2>Add New Task</h2>
            <form id="ppd-add-task-form">
                <table class="form-table">
                    <tr>
                        <th><label for="task_partner">Partner *</label></th>
                        <td>
                            <select id="task_partner" name="partner_id" class="regular-text" required>
                                <option value="">Select Partner</option>
                                <?php
                                $partners = PPD_Partner::get_all_partners();
                                foreach ($partners as $partner):
                                ?>
                                    <option value="<?php echo esc_attr($partner['id']); ?>">
                                        <?php echo esc_html($partner['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="task_title">Title *</label></th>
                        <td><input type="text" id="task_title" name="title" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="task_description">Description</label></th>
                        <td><textarea id="task_description" name="description" rows="4" class="large-text"></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="task_priority">Priority</label></th>
                        <td>
                            <select id="task_priority" name="priority" class="regular-text">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="task_due_date">Due Date</label></th>
                        <td><input type="date" id="task_due_date" name="due_date" class="regular-text"></td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary">Add Task</button>
                </p>
            </form>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Partner</th>
                <th>Title</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tasks)): ?>
                <tr>
                    <td colspan="7">No tasks found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($tasks as $task):
                    $partner = get_userdata($task->partner_id);
                ?>
                    <tr>
                        <td><?php echo esc_html($task->id); ?></td>
                        <td><?php echo $partner ? esc_html($partner->display_name) : 'N/A'; ?></td>
                        <td><?php echo esc_html($task->title); ?></td>
                        <td>
                            <span class="ppd-priority-badge priority-<?php echo esc_attr($task->priority); ?>">
                                <?php echo esc_html(ucfirst($task->priority)); ?>
                            </span>
                        </td>
                        <td>
                            <span class="ppd-status-badge status-<?php echo esc_attr($task->status); ?>">
                                <?php echo esc_html(ucfirst($task->status)); ?>
                            </span>
                        </td>
                        <td><?php echo $task->due_date ? esc_html(date('M d, Y', strtotime($task->due_date))) : '-'; ?></td>
                        <td><?php echo esc_html(date('M d, Y', strtotime($task->created_at))); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
