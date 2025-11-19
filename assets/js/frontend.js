jQuery(document).ready(function($) {
    'use strict';

    // Tab Navigation
    $('.ppd-nav-tab').on('click', function(e) {
        e.preventDefault();

        var tabId = $(this).data('tab');

        // Remove active class from all tabs and content
        $('.ppd-nav-tab').removeClass('active');
        $('.ppd-tab-content').removeClass('active');

        // Add active class to clicked tab and corresponding content
        $(this).addClass('active');
        $('#ppd-tab-' + tabId).addClass('active');
    });

    // Profile Form Submission
    $('#ppd-profile-form').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        formData += '&action=ppd_update_profile&nonce=' + ppdAjax.nonce;

        $.ajax({
            url: ppdAjax.ajaxurl,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#ppd-profile-form button[type="submit"]').prop('disabled', true).text('Updating...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Profile updated successfully!');
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $('#ppd-profile-form button[type="submit"]').prop('disabled', false).text('Update Profile');
            }
        });
    });

    // Add Lead Modal
    $('#ppd-add-lead-btn').on('click', function() {
        $('#ppd-add-lead-form').fadeIn();
    });

    $('.ppd-modal-close').on('click', function() {
        $(this).closest('.ppd-modal').fadeOut();
    });

    // Close modal when clicking outside
    $('.ppd-modal').on('click', function(e) {
        if ($(e.target).hasClass('ppd-modal')) {
            $(this).fadeOut();
        }
    });

    // Add Lead Form Submission
    $('#ppd-lead-form').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        formData += '&action=ppd_add_lead&nonce=' + ppdAjax.nonce;

        $.ajax({
            url: ppdAjax.ajaxurl,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#ppd-lead-form button[type="submit"]').prop('disabled', true).text('Adding...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Lead added successfully!');
                    $('#ppd-lead-form')[0].reset();
                    $('#ppd-add-lead-form').fadeOut();
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $('#ppd-lead-form button[type="submit"]').prop('disabled', false).text('Add Lead');
            }
        });
    });

    // Mark Task as Complete
    $('.ppd-mark-task-complete').on('click', function() {
        var taskId = $(this).data('task-id');
        var button = $(this);

        if (!confirm('Are you sure you want to mark this task as complete?')) {
            return;
        }

        $.ajax({
            url: ppdAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'ppd_mark_task_complete',
                nonce: ppdAjax.nonce,
                task_id: taskId
            },
            beforeSend: function() {
                button.prop('disabled', true).text('Updating...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Task marked as complete!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                button.prop('disabled', false).text('Mark as Complete');
            }
        });
    });

    // Google Sheets Form Submission
    $('#ppd-google-sheets-form').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        formData += '&action=ppd_update_google_sheets&nonce=' + ppdAjax.nonce;

        $.ajax({
            url: ppdAjax.ajaxurl,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#ppd-google-sheets-form button[type="submit"]').prop('disabled', true).text('Saving...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Google Sheets settings updated!');
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $('#ppd-google-sheets-form button[type="submit"]').prop('disabled', false).text('Save Settings');
            }
        });
    });

    // Sync Now Button
    $('#ppd-sync-now-btn').on('click', function() {
        var button = $(this);

        $.ajax({
            url: ppdAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'ppd_sync_google_sheets',
                nonce: ppdAjax.nonce
            },
            beforeSend: function() {
                button.prop('disabled', true).text('Syncing...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Leads synced successfully! Count: ' + response.count);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                button.prop('disabled', false).text('Sync Now');
            }
        });
    });

    // Application Form Submission
    $('#ppd-application-form').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        formData += '&action=ppd_submit_application&nonce=' + $('input[name="ppd_application_nonce"]').val();

        $.ajax({
            url: ppdAjax.ajaxurl,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#ppd-application-form button[type="submit"]').prop('disabled', true).text('Submitting...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Application submitted successfully! We will review it and get back to you soon.');
                    $('#ppd-application-form')[0].reset();
                    window.location.href = window.location.pathname + '?application=success';
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $('#ppd-application-form button[type="submit"]').prop('disabled', false).text('Submit Application');
            }
        });
    });
});
