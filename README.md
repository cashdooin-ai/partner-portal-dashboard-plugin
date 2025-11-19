# Partner Portal Dashboard Plugin

A comprehensive WordPress plugin for managing a partner portal system with dashboard, profiles, colleges, services, tasks, leads board, and Google Sheets integration.

## Features

### Partner Features
- **Partner Dashboard** - Complete overview with statistics and quick access
- **Profile Management** - Partners can update their profile information
- **Assigned Colleges** - View colleges assigned by admin
- **Services** - View assigned services
- **Task Management** - View and complete tasks assigned by admin
- **Leads Board** - Add and manage student leads
- **Google Sheets Integration** - Sync leads data to Google Sheets
- **Login/Logout** - Secure authentication system

### Admin Features
- **Partner Management** - View and manage all partners
- **College Management** - Add colleges and assign to partners
- **Service Management** - Add services and assign to partners
- **Task Assignment** - Create and assign tasks to partners
- **Lead Management** - View all leads and add admin notes
- **Application Management** - Review and approve partner applications

## Installation

1. Upload the `partner-portal-dashboard-plugin` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. The plugin will automatically create default pages and database tables

## Default Pages Created

Upon activation, the plugin creates these pages:
- **Partner Dashboard** - Main dashboard for partners (`[partner_dashboard]`)
- **Partner Login** - Login page for partners (`[partner_login]`)
- **Partner Application** - Application form for new partners (`[partner_application_form]`)

## Shortcodes

### `[partner_dashboard]`
Displays the complete partner dashboard with all features.

**Usage:**
```
[partner_dashboard]
```

### `[partner_login]`
Displays the partner login form.

**Usage:**
```
[partner_login]
```

### `[partner_application_form]`
Displays the partner application form for new partner requests.

**Usage:**
```
[partner_application_form]
```

### `[partner_logout]`
Displays a logout button (only visible to logged-in partners).

**Usage:**
```
[partner_logout]
```

## Admin Panel

Access the admin panel through **Partner Portal** in the WordPress admin menu.

### Admin Sections

1. **Partners** - View all partners and their details
2. **Colleges** - Manage colleges and assignments
3. **Services** - Manage services and assignments
4. **Tasks** - Create and manage tasks
5. **Leads** - View all leads and add notes
6. **Applications** - Review and approve new partner applications

## Google Sheets Integration

Partners can integrate their leads with Google Sheets for automatic syncing.

### Setup Instructions

1. Create a Google Sheet or use an existing one
2. Copy the Sheet ID from the URL
3. Navigate to Dashboard > Google Sheets tab
4. Enter the Sheet ID and Sheet Name
5. Enable Auto Sync
6. Click "Sync Now" to test the connection

**Note:** For production use, you need to:
- Install the Google API Client library
- Set up OAuth2 credentials in Google Cloud Console
- Implement the Google Sheets API authentication

## User Roles

The plugin creates a custom `partner` role with the following capabilities:
- Read access to WordPress
- Access to partner dashboard
- Manage own profile and leads

## Database Tables

The plugin creates the following database tables:

- `wp_ppd_partners_meta` - Partner profile information
- `wp_ppd_colleges` - Colleges database
- `wp_ppd_partner_colleges` - College assignments
- `wp_ppd_services` - Services database
- `wp_ppd_partner_services` - Service assignments
- `wp_ppd_tasks` - Tasks database
- `wp_ppd_leads` - Leads database
- `wp_ppd_google_sheets` - Google Sheets configuration
- `wp_ppd_applications` - Partner applications

## Workflow

### For New Partners

1. Visit the Partner Application page
2. Fill out the application form
3. Admin reviews the application in admin panel
4. Admin approves and creates partner account
5. Partner receives login credentials via email
6. Partner logs in and accesses dashboard

### For Admins

1. Review applications in Applications page
2. Approve application to create partner account
3. Assign colleges and services to partners
4. Create tasks for partners
5. Monitor leads submitted by partners
6. Add admin notes to leads for processing

### For Partners

1. Log in to partner portal
2. View dashboard statistics
3. Update profile information
4. View assigned colleges and services
5. Complete assigned tasks
6. Add new student leads
7. Configure Google Sheets integration
8. Monitor lead status and admin notes

## File Structure

```
partner-portal-dashboard-plugin/
├── admin/
│   └── views/
│       ├── partners.php
│       ├── colleges.php
│       ├── services.php
│       ├── tasks.php
│       ├── leads.php
│       └── applications.php
├── assets/
│   ├── css/
│   │   ├── frontend.css
│   │   └── admin.css
│   └── js/
│       ├── frontend.js
│       └── admin.js
├── includes/
│   ├── class-ppd-database.php
│   ├── class-ppd-auth.php
│   ├── class-ppd-partner.php
│   ├── class-ppd-admin.php
│   ├── class-ppd-dashboard.php
│   ├── class-ppd-colleges.php
│   ├── class-ppd-services.php
│   ├── class-ppd-tasks.php
│   ├── class-ppd-leads.php
│   ├── class-ppd-google-sheets.php
│   ├── class-ppd-shortcodes.php
│   └── class-ppd-ajax.php
├── partner-portal-dashboard.php
└── README.md
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- MySQL 5.6 or higher

## Support

For support and bug reports, please contact the plugin developer.

## License

GPL v2 or later

## Changelog

### Version 1.0.0
- Initial release
- Partner dashboard with profile management
- College and service assignment system
- Task management
- Leads board with admin notes
- Google Sheets integration
- Partner application form
- Complete admin panel
- Shortcode system

## Credits

Developed for CollegeKampus Partner Portal
