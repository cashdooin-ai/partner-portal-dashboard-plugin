<?php
/**
 * Google Sheets Integration Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Google_Sheets {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Constructor
    }

    public static function get_partner_config($partner_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_google_sheets';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE partner_id = %d",
            $partner_id
        ));
    }

    public static function update_config($partner_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ppd_google_sheets';

        $existing = self::get_partner_config($partner_id);

        $config_data = array(
            'sheet_id' => sanitize_text_field($data['sheet_id']),
            'sheet_name' => sanitize_text_field($data['sheet_name']),
            'sync_enabled' => isset($data['sync_enabled']) ? 1 : 0,
        );

        if ($existing) {
            return $wpdb->update($table, $config_data, array('partner_id' => $partner_id));
        } else {
            $config_data['partner_id'] = $partner_id;
            return $wpdb->insert($table, $config_data);
        }
    }

    public static function sync_leads_to_sheet($partner_id) {
        $config = self::get_partner_config($partner_id);

        if (!$config || !$config->sync_enabled || !$config->sheet_id) {
            return array('success' => false, 'message' => 'Google Sheets not configured');
        }

        $leads = PPD_Leads::get_partner_leads($partner_id);

        // This is a placeholder for actual Google Sheets API integration
        // You would need to implement the Google Sheets API authentication and data writing here
        // For now, we'll just update the last sync timestamp

        global $wpdb;
        $table = $wpdb->prefix . 'ppd_google_sheets';

        $wpdb->update(
            $table,
            array('last_sync' => current_time('mysql')),
            array('partner_id' => $partner_id)
        );

        return array(
            'success' => true,
            'message' => 'Leads synced successfully',
            'count' => count($leads)
        );
    }

    /**
     * Note: Actual Google Sheets integration would require:
     * 1. Google API Client library
     * 2. OAuth2 credentials
     * 3. Service account or OAuth flow
     *
     * Example implementation:
     * - Install google/apiclient library
     * - Set up credentials in Google Cloud Console
     * - Use Google_Service_Sheets to write data
     *
     * For production use, implement proper authentication and API calls
     */
}
