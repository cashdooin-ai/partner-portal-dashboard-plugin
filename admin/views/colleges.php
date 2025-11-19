<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>Colleges Management
        <button class="button button-primary" id="ppd-add-college-btn">Add New College</button>
    </h1>

    <div id="ppd-add-college-modal" class="ppd-admin-modal" style="display:none;">
        <div class="ppd-admin-modal-content">
            <span class="ppd-admin-modal-close">&times;</span>
            <h2>Add New College</h2>
            <form id="ppd-add-college-form">
                <table class="form-table">
                    <tr>
                        <th><label for="college_name">College Name *</label></th>
                        <td><input type="text" id="college_name" name="name" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="college_location">Location</label></th>
                        <td><input type="text" id="college_location" name="location" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="college_city">City</label></th>
                        <td><input type="text" id="college_city" name="city" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="college_state">State</label></th>
                        <td><input type="text" id="college_state" name="state" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="college_website">Website</label></th>
                        <td><input type="url" id="college_website" name="website" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="college_contact_person">Contact Person</label></th>
                        <td><input type="text" id="college_contact_person" name="contact_person" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="college_contact_email">Contact Email</label></th>
                        <td><input type="email" id="college_contact_email" name="contact_email" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="college_contact_phone">Contact Phone</label></th>
                        <td><input type="text" id="college_contact_phone" name="contact_phone" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="college_description">Description</label></th>
                        <td><textarea id="college_description" name="description" rows="4" class="large-text"></textarea></td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary">Add College</button>
                </p>
            </form>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Location</th>
                <th>City</th>
                <th>State</th>
                <th>Contact Person</th>
                <th>Contact Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($colleges)): ?>
                <tr>
                    <td colspan="8">No colleges found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($colleges as $college): ?>
                    <tr>
                        <td><?php echo esc_html($college->id); ?></td>
                        <td><?php echo esc_html($college->name); ?></td>
                        <td><?php echo esc_html($college->location); ?></td>
                        <td><?php echo esc_html($college->city); ?></td>
                        <td><?php echo esc_html($college->state); ?></td>
                        <td><?php echo esc_html($college->contact_person); ?></td>
                        <td><?php echo esc_html($college->contact_email); ?></td>
                        <td>
                            <button class="button button-small ppd-assign-college" data-college-id="<?php echo esc_attr($college->id); ?>">Assign to Partner</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
