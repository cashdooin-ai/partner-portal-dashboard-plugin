<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>Services Management
        <button class="button button-primary" id="ppd-add-service-btn">Add New Service</button>
    </h1>

    <div id="ppd-add-service-modal" class="ppd-admin-modal" style="display:none;">
        <div class="ppd-admin-modal-content">
            <span class="ppd-admin-modal-close">&times;</span>
            <h2>Add New Service</h2>
            <form id="ppd-add-service-form">
                <table class="form-table">
                    <tr>
                        <th><label for="service_name">Service Name *</label></th>
                        <td><input type="text" id="service_name" name="name" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="service_description">Description</label></th>
                        <td><textarea id="service_description" name="description" rows="4" class="large-text"></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="service_price">Price (₹)</label></th>
                        <td><input type="number" id="service_price" name="price" step="0.01" class="regular-text"></td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary">Add Service</button>
                </p>
            </form>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($services)): ?>
                <tr>
                    <td colspan="6">No services found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo esc_html($service->id); ?></td>
                        <td><?php echo esc_html($service->name); ?></td>
                        <td><?php echo esc_html($service->description); ?></td>
                        <td>₹<?php echo number_format($service->price, 2); ?></td>
                        <td><?php echo esc_html(ucfirst($service->status)); ?></td>
                        <td>
                            <button class="button button-small ppd-assign-service" data-service-id="<?php echo esc_attr($service->id); ?>">Assign to Partner</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
