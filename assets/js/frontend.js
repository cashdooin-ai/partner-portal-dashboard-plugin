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

    // Communication Center Sub-tabs
    $('.ppd-comm-tab-btn').on('click', function(e) {
        e.preventDefault();

        var tabId = $(this).data('comm-tab');

        // Remove active class from all sub-tabs and content
        $('.ppd-comm-tab-btn').removeClass('active');
        $('.ppd-comm-tab-content').removeClass('active');

        // Add active class to clicked tab and corresponding content
        $(this).addClass('active');
        $('#ppd-comm-' + tabId).addClass('active');
    });

    // Notifications Panel Toggle
    $('#ppd-notifications-trigger').on('click', function() {
        $('#ppd-notifications-panel').fadeToggle();
    });

    // Mark Notification as Read
    $('.ppd-mark-read').on('click', function() {
        var notificationId = $(this).data('notification-id');

        $.ajax({
            url: ppdAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'ppd_mark_notification_read',
                nonce: ppdAjax.nonce,
                notification_id: notificationId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    });

    // Mark All Notifications as Read
    $('#ppd-mark-all-read').on('click', function() {
        $.ajax({
            url: ppdAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'ppd_mark_all_notifications_read',
                nonce: ppdAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
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

    // Referral Form Submission
    $('#ppd-referral-form').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        formData += '&action=ppd_submit_referral&nonce=' + ppdAjax.nonce;

        $.ajax({
            url: ppdAjax.ajaxurl,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#ppd-referral-form button[type="submit"]').prop('disabled', true).text('Submitting...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Referral submitted successfully!');
                    $('#ppd-referral-form')[0].reset();
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $('#ppd-referral-form button[type="submit"]').prop('disabled', false).text('Submit Referral');
            }
        });
    });

    // Document Category Filter
    $('#ppd-document-category-filter').on('change', function() {
        var category = $(this).val();

        if (category === '') {
            $('.ppd-document-card').show();
        } else {
            $('.ppd-document-card').hide();
            $('.ppd-document-card[data-category="' + category + '"]').show();
        }
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

    // Initialize Charts if Chart.js is loaded
    if (typeof Chart !== 'undefined' && typeof analyticsData !== 'undefined') {
        // Lead Status Distribution Chart
        if ($('#ppd-status-chart').length && analyticsData.lead_status) {
            var statusCtx = document.getElementById('ppd-status-chart').getContext('2d');
            new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(analyticsData.lead_status),
                    datasets: [{
                        data: Object.values(analyticsData.lead_status),
                        backgroundColor: [
                            '#667eea',
                            '#764ba2',
                            '#f093fb',
                            '#f5576c',
                            '#4facfe',
                            '#00f2fe'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // Monthly Trend Chart
        if ($('#ppd-monthly-chart').length && analyticsData.monthly_trend) {
            var monthlyCtx = document.getElementById('ppd-monthly-chart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: analyticsData.monthly_trend.labels,
                    datasets: [{
                        label: 'Leads',
                        data: analyticsData.monthly_trend.data,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // Top Colleges Chart
        if ($('#ppd-college-chart').length && analyticsData.top_colleges) {
            var collegeCtx = document.getElementById('ppd-college-chart').getContext('2d');
            new Chart(collegeCtx, {
                type: 'bar',
                data: {
                    labels: analyticsData.top_colleges.labels,
                    datasets: [{
                        label: 'Leads',
                        data: analyticsData.top_colleges.data,
                        backgroundColor: '#667eea'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    }

    // Initialize Sortable for Pipeline if Sortable.js is loaded
    if (typeof Sortable !== 'undefined') {
        $('.ppd-pipeline-cards').each(function() {
            var el = this;
            var stage = $(this).data('stage');

            Sortable.create(el, {
                group: 'leads',
                animation: 150,
                onEnd: function(evt) {
                    var leadId = $(evt.item).data('lead-id');
                    var newStage = $(evt.to).data('stage');

                    $.ajax({
                        url: ppdAjax.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'ppd_move_lead',
                            nonce: ppdAjax.nonce,
                            lead_id: leadId,
                            stage: newStage
                        },
                        success: function(response) {
                            if (!response.success) {
                                alert('Error moving lead');
                                location.reload();
                            }
                        }
                    });
                }
            });
        });
    }
});
