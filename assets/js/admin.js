jQuery(document).ready(function($) {
    'use strict';

    // Add College Modal
    $('#ppd-add-college-btn').on('click', function() {
        $('#ppd-add-college-modal').fadeIn();
    });

    // Add Service Modal
    $('#ppd-add-service-btn').on('click', function() {
        $('#ppd-add-service-modal').fadeIn();
    });

    // Add Task Modal
    $('#ppd-add-task-btn').on('click', function() {
        $('#ppd-add-task-modal').fadeIn();
    });

    // Close Modal
    $('.ppd-admin-modal-close').on('click', function() {
        $(this).closest('.ppd-admin-modal').fadeOut();
    });

    // Close modal when clicking outside
    $('.ppd-admin-modal').on('click', function(e) {
        if ($(e.target).hasClass('ppd-admin-modal')) {
            $(this).fadeOut();
        }
    });

    // Add College Form
    $('#ppd-add-college-form').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        formData += '&action=ppd_admin_add_college&nonce=' + ppdAdmin.nonce;

        $.ajax({
            url: ppdAdmin.ajaxurl,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#ppd-add-college-form button[type="submit"]').prop('disabled', true).text('Adding...');
            },
            success: function(response) {
                if (response.success) {
                    alert('College added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $('#ppd-add-college-form button[type="submit"]').prop('disabled', false).text('Add College');
            }
        });
    });

    // Add Service Form
    $('#ppd-add-service-form').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        formData += '&action=ppd_admin_add_service&nonce=' + ppdAdmin.nonce;

        $.ajax({
            url: ppdAdmin.ajaxurl,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#ppd-add-service-form button[type="submit"]').prop('disabled', true).text('Adding...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Service added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $('#ppd-add-service-form button[type="submit"]').prop('disabled', false).text('Add Service');
            }
        });
    });

    // Add Task Form
    $('#ppd-add-task-form').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        formData += '&action=ppd_admin_add_task&nonce=' + ppdAdmin.nonce;

        $.ajax({
            url: ppdAdmin.ajaxurl,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#ppd-add-task-form button[type="submit"]').prop('disabled', true).text('Adding...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Task added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $('#ppd-add-task-form button[type="submit"]').prop('disabled', false).text('Add Task');
            }
        });
    });

    // Assign College to Partner
    $('.ppd-assign-college').on('click', function() {
        var collegeId = $(this).data('college-id');
        var partnerId = prompt('Enter Partner ID to assign this college:');

        if (!partnerId) {
            return;
        }

        $.ajax({
            url: ppdAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'ppd_admin_assign_college',
                nonce: ppdAdmin.nonce,
                partner_id: partnerId,
                college_id: collegeId
            },
            success: function(response) {
                if (response.success) {
                    alert('College assigned successfully!');
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Assign Service to Partner
    $('.ppd-assign-service').on('click', function() {
        var serviceId = $(this).data('service-id');
        var partnerId = prompt('Enter Partner ID to assign this service:');

        if (!partnerId) {
            return;
        }

        $.ajax({
            url: ppdAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'ppd_admin_assign_service',
                nonce: ppdAdmin.nonce,
                partner_id: partnerId,
                service_id: serviceId
            },
            success: function(response) {
                if (response.success) {
                    alert('Service assigned successfully!');
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Edit Lead
    $('.ppd-edit-lead').on('click', function() {
        var leadId = $(this).data('lead-id');
        var status = prompt('Enter new status (new, processing, completed):');

        if (!status) {
            return;
        }

        var adminNotes = prompt('Enter admin notes (optional):');

        $.ajax({
            url: ppdAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'ppd_admin_update_lead',
                nonce: ppdAdmin.nonce,
                lead_id: leadId,
                status: status,
                admin_notes: adminNotes
            },
            success: function(response) {
                if (response.success) {
                    alert('Lead updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Approve Application
    $('.ppd-approve-application').on('click', function() {
        var applicationId = $(this).data('application-id');

        if (!confirm('Are you sure you want to approve this application and create a partner account?')) {
            return;
        }

        var button = $(this);

        $.ajax({
            url: ppdAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'ppd_admin_approve_application',
                nonce: ppdAdmin.nonce,
                application_id: applicationId
            },
            beforeSend: function() {
                button.prop('disabled', true).text('Processing...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Partner created successfully! Login credentials have been sent to their email.');
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                button.prop('disabled', false).text('Approve & Create Partner');
            }
        });
    });
});
