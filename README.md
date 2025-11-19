# Partner Portal Dashboard Plugin

A comprehensive WordPress plugin for managing a complete partner portal system with dashboard, profiles, colleges, services, tasks, leads board, Google Sheets integration, analytics, commissions, notifications, pipeline management, document management, communication center, calendar & events, performance metrics, referral program, student management, and activity tracking.

## Features

### Partner Features
- **Partner Dashboard** - Complete overview with statistics and quick access
- **Profile Management** - Partners can update their profile information
- **Assigned Colleges** - View colleges assigned by admin
- **Services** - View assigned services
- **Task Management** - View and complete tasks assigned by admin
- **Leads Board** - Add and manage student leads
- **Google Sheets Integration** - Sync leads data to Google Sheets
- **Analytics Dashboard** - Real-time performance metrics with charts
- **Commission Tracking** - Track earnings and payment history
- **Notifications** - Real-time alerts and updates
- **Lead Pipeline** - Visual Kanban board with drag-and-drop
- **Document Management** - Access and download shared documents
- **Communication Center** - Direct messaging, support tickets, announcements, and FAQ
- **Calendar & Events** - Meeting schedules, deadlines, and reminders
- **Performance Metrics** - Targets, leaderboard, and success rates
- **Referral Program** - Refer new partners and earn commissions
- **Student Management** - Track students through admission process
- **Activity Timeline** - View login history and action audit trail
- **Login/Logout** - Secure authentication system

### Admin Features
- **Partner Management** - View and manage all partners
- **College Management** - Add colleges and assign to partners
- **Service Management** - Add services and assign to partners
- **Task Assignment** - Create and assign tasks to partners
- **Lead Management** - View all leads and add admin notes
- **Application Management** - Review and approve partner applications
- **Commission Management** - Add, approve, and process partner commissions
- **Document Management** - Upload and categorize documents for partners
- **Communication Management** - Respond to messages and support tickets
- **Event Management** - Schedule meetings and events for partners
- **Performance Tracking** - Set targets and view partner leaderboard
- **Referral Management** - Approve referrals and process commissions
- **Student Management** - Track student admission status and verify documents
- **Activity Monitoring** - View partner activity logs and audit trails

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

**Core Tables (v1.0):**
- `wp_ppd_partners_meta` - Partner profile information
- `wp_ppd_colleges` - Colleges database
- `wp_ppd_partner_colleges` - College assignments
- `wp_ppd_services` - Services database
- `wp_ppd_partner_services` - Service assignments
- `wp_ppd_tasks` - Tasks database
- `wp_ppd_leads` - Leads database
- `wp_ppd_google_sheets` - Google Sheets configuration
- `wp_ppd_applications` - Partner applications

**Enhanced Features Tables (v2.0):**
- `wp_ppd_commissions` - Commission and earnings tracking
- `wp_ppd_notifications` - Real-time notification system
- `wp_ppd_documents` - Document management system

**Communication & Collaboration Tables (v3.0):**
- `wp_ppd_messages` - Direct messaging system
- `wp_ppd_tickets` - Support ticket system
- `wp_ppd_ticket_replies` - Ticket conversation threads
- `wp_ppd_announcements` - System-wide announcements
- `wp_ppd_faqs` - Frequently asked questions
- `wp_ppd_events` - Calendar events and meetings
- `wp_ppd_targets` - Performance targets for partners
- `wp_ppd_referrals` - Partner referral tracking
- `wp_ppd_students` - Student admission management
- `wp_ppd_student_documents` - Student document verification
- `wp_ppd_activity_log` - Complete activity and audit trail

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
│   ├── class-ppd-database.php         [Database schema management]
│   ├── class-ppd-auth.php             [Authentication & user management]
│   ├── class-ppd-partner.php          [Partner data management]
│   ├── class-ppd-admin.php            [Admin panel]
│   ├── class-ppd-dashboard.php        [Dashboard rendering]
│   ├── class-ppd-colleges.php         [College management]
│   ├── class-ppd-services.php         [Service management]
│   ├── class-ppd-tasks.php            [Task management]
│   ├── class-ppd-leads.php            [Lead management]
│   ├── class-ppd-google-sheets.php    [Google Sheets integration]
│   ├── class-ppd-shortcodes.php       [Shortcode handlers]
│   ├── class-ppd-ajax.php             [AJAX handlers]
│   ├── class-ppd-analytics.php        [Analytics & metrics (v2.0)]
│   ├── class-ppd-commissions.php      [Commission tracking (v2.0)]
│   ├── class-ppd-notifications.php    [Notification system (v2.0)]
│   ├── class-ppd-pipeline.php         [Lead pipeline Kanban (v2.0)]
│   ├── class-ppd-documents.php        [Document management (v2.0)]
│   ├── class-ppd-communication.php    [Messages & tickets (v3.0)]
│   ├── class-ppd-calendar.php         [Events & calendar (v3.0)]
│   ├── class-ppd-performance.php      [Performance metrics (v3.0)]
│   ├── class-ppd-referrals.php        [Referral program (v3.0)]
│   ├── class-ppd-students.php         [Student management (v3.0)]
│   └── class-ppd-activity.php         [Activity tracking (v3.0)]
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

### Version 3.0.0
- **NEW:** Communication Center with direct messaging and support tickets
- **NEW:** Announcement board for system-wide updates
- **NEW:** FAQ section for common questions
- **NEW:** Calendar & Events system with meeting scheduling
- **NEW:** Event reminders (24-hour advance notifications)
- **NEW:** Performance Metrics with partner leaderboard
- **NEW:** Target tracking and achievement monitoring
- **NEW:** Success rate analysis by college/service
- **NEW:** Referral Program for partner-to-partner referrals
- **NEW:** Referral commission tracking and approval
- **NEW:** Student Management through admission process
- **NEW:** Student document upload and verification system
- **NEW:** Activity Timeline with login history
- **NEW:** Complete audit trail for all partner actions
- **NEW:** IP address and user agent logging for security
- Enhanced database with 11 new tables (messages, tickets, events, students, etc.)
- Integrated notification system across all new features
- Automatic activity logging on key actions
- Document verification workflow for student management
- Ticket priority and category system
- Event RSVP and status tracking
- Partner ranking and performance comparison

### Version 2.0.0
- **NEW:** Analytics Dashboard with charts and performance metrics
- **NEW:** Commission/Earnings Tracking system
- **NEW:** Real-time Notifications system
- **NEW:** Lead Pipeline with Kanban board (drag & drop)
- **NEW:** Document Management system with categories
- Integrated Chart.js for visual analytics
- Added monthly trend analysis
- Conversion rate tracking
- Task completion metrics
- Payment history and commission management
- Document upload and download with access control
- Notifications bell with unread count
- Lead status distribution charts
- Top colleges by leads analysis
- Enhanced database with 3 new tables (commissions, notifications, documents)

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

## New Features in Version 2.0

### 1. Analytics Dashboard
- Real-time performance metrics
- Lead conversion rate tracking
- Monthly trend analysis (last 6 months)
- Task completion rate
- Earnings summary
- Lead status distribution pie chart
- Top performing colleges
- Comparative analysis (current vs previous month)

### 2. Commission/Earnings Tracking
- Track all commissions and earnings
- Pending vs paid amount breakdown
- Monthly earnings report
- Payment history with transaction details
- Commission types (lead-based, performance, etc.)
- Admin can add and manage commissions
- Automatic notifications when commissions are paid

### 3. Notifications System
- Real-time notifications for partners
- Notification bell with unread count
- Different notification types (task, lead, commission, document, etc.)
- Mark as read/unread functionality
- Mark all as read option
- Automatic notifications for:
  - New task assignments
  - Lead status changes
  - Admin notes on leads
  - College/service assignments
  - Commission payments
  - New document uploads
  - Task due date reminders

### 4. Lead Pipeline/Kanban Board
- Visual pipeline with 8 stages:
  - New
  - Contacted
  - Qualified
  - Proposal Sent
  - Negotiation
  - Converted
  - Closed Won
  - Closed Lost
- Drag and drop leads between stages
- Color-coded stages
- Lead count per stage
- Win rate calculation
- Pipeline statistics

### 5. Document Management
- Upload and organize documents
- 9 categories: Marketing, Contracts, Training, Guidelines, Certificates, etc.
- File type support: PDF, DOC, XLS, PPT, Images, ZIP
- 10MB file size limit
- Public vs partner-specific documents
- Secure download with access control
- File size display
- Category filtering
- Document search
- Automatic notifications when new documents are uploaded

## New Features in Version 3.0

### 1. Communication Center
- **Direct Messaging**: Send and receive messages with admin
- **Support Tickets**: Create and track support tickets with priority levels
- **Ticket Categories**: General, Technical, Billing, Lead Related, Document, Account
- **Priority Levels**: Low, Medium, High, Urgent
- **Ticket Replies**: Threaded conversation system
- **Internal Notes**: Admin-only notes on tickets
- **Announcements**: System-wide announcements with expiration dates
- **Pinned Announcements**: Keep important announcements at top
- **FAQ Section**: Searchable frequently asked questions
- **Read/Unread Tracking**: Mark messages and tickets as read/unread
- **Status Management**: Open, In Progress, Waiting, Resolved, Closed

### 2. Calendar & Events
- **Event Scheduling**: Schedule meetings, deadlines, and training sessions
- **Event Types**: Meeting, Training, Deadline, College Visit, Webinar, Conference
- **Location Tracking**: Physical location or online meeting links
- **RSVP System**: Confirmed, Tentative, Declined status
- **Event Reminders**: Automatic reminders 24 hours before events
- **All-Day Events**: Support for full-day events
- **Upcoming Events**: View upcoming events in dashboard
- **Event Filtering**: Filter by date range and status
- **Recurring Events**: Track regular scheduled events

### 3. Performance Metrics
- **Target Setting**: Set and track performance targets
- **Target Types**: Leads, Conversions, Revenue, Students
- **Progress Tracking**: Real-time progress towards targets
- **Leaderboard**: Compare performance with other partners
- **Rankings**: Monthly, quarterly, yearly rankings
- **Success Rate Analysis**: Track success rates by college and service
- **Performance Periods**: Track targets by time periods
- **Achievement Notifications**: Get notified when targets are reached
- **Top Performers**: Identify and celebrate top partners

### 4. Referral Program
- **Partner Referrals**: Refer new partners to the program
- **Duplicate Prevention**: Check for existing referrals by email
- **Referral Tracking**: Track all referrals with status
- **Status Management**: Pending, Approved, Rejected
- **Commission Integration**: Automatic commission creation on approval
- **Referral Statistics**: View total, approved, pending referrals
- **Total Commission Tracking**: Track all referral earnings
- **Admin Approval Workflow**: Admin reviews and approves referrals
- **Automatic Notifications**: Notify referrer on status changes

### 5. Student Management
- **Student Tracking**: Track students through admission process
- **Application Status**: Pending, In Progress, Submitted, Under Review, etc.
- **Document Verification**: Upload and verify student documents
- **Document Types**: ID Proof, Academic Records, Address Proof, Photos, etc.
- **Verification Status**: Pending, Approved, Rejected
- **Document Upload**: Secure file upload with 5MB limit
- **College Assignment**: Link students to specific colleges
- **Course Tracking**: Track student's intended course
- **Lead Integration**: Link students to original leads
- **Status Updates**: Multiple status types (application, document, admission)
- **Verification Notes**: Admin can add notes during verification
- **Student Statistics**: View total students, admitted, in-progress

### 6. Activity Timeline
- **Automatic Login Tracking**: Log every partner login
- **Activity Logging**: Log all important partner actions
- **IP Address Tracking**: Record IP address for security
- **User Agent Logging**: Track browser and device information
- **Entity Tracking**: Link activities to specific entities (leads, tasks, etc.)
- **Recent Activities**: View activities from last 7 days
- **Login History**: Detailed login history with timestamps
- **Activity Summary**: Group activities by type and count
- **Action Audit Trail**: Complete history of all actions
- **Automatic Cleanup**: Remove logs older than 90 days
- **Security Monitoring**: Track suspicious activity patterns

## Technical Implementation

### Database Tables (v3.0)
- `wp_ppd_messages` - Direct messaging system
- `wp_ppd_tickets` - Support ticket tracking
- `wp_ppd_ticket_replies` - Ticket conversation threads
- `wp_ppd_announcements` - System announcements
- `wp_ppd_faqs` - FAQ database
- `wp_ppd_events` - Calendar events
- `wp_ppd_targets` - Performance targets
- `wp_ppd_referrals` - Referral tracking
- `wp_ppd_students` - Student management
- `wp_ppd_student_documents` - Document verification
- `wp_ppd_activity_log` - Activity audit trail

### Database Tables (v2.0)
- `wp_ppd_commissions` - Commission tracking
- `wp_ppd_notifications` - Notification system
- `wp_ppd_documents` - Document management

### External Libraries
- Chart.js 3.9.1 - For analytics charts
- Sortable.js 1.15.0 - For drag and drop pipeline

### Security Features
- Nonce verification on all AJAX calls
- File type validation for uploads
- Access control for document downloads
- Sanitization of all user inputs
- Role-based permissions
- IP address logging for security monitoring
- User agent tracking for device identification
- Activity audit trail for compliance
- Prepared statements for SQL security

## Credits

Developed for CollegeKampus Partner Portal
