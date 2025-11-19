<?php
/**
 * Lead Pipeline Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPD_Pipeline {

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

    public static function get_pipeline_stages() {
        return array(
            'new' => array(
                'label' => 'New',
                'color' => '#3498db',
                'icon' => '📋'
            ),
            'contacted' => array(
                'label' => 'Contacted',
                'color' => '#9b59b6',
                'icon' => '📞'
            ),
            'qualified' => array(
                'label' => 'Qualified',
                'color' => '#e67e22',
                'icon' => '✅'
            ),
            'proposal' => array(
                'label' => 'Proposal Sent',
                'color' => '#f39c12',
                'icon' => '📄'
            ),
            'negotiation' => array(
                'label' => 'Negotiation',
                'color' => '#d35400',
                'icon' => '🤝'
            ),
            'converted' => array(
                'label' => 'Converted',
                'color' => '#27ae60',
                'icon' => '🎉'
            ),
            'closed_won' => array(
                'label' => 'Closed Won',
                'color' => '#2ecc71',
                'icon' => '💰'
            ),
            'closed_lost' => array(
                'label' => 'Closed Lost',
                'color' => '#95a5a6',
                'icon' => '❌'
            ),
        );
    }

    public static function get_leads_by_stage($partner_id) {
        global $wpdb;
        $leads_table = $wpdb->prefix . 'ppd_leads';

        $stages = self::get_pipeline_stages();
        $pipeline_data = array();

        foreach ($stages as $stage_key => $stage_info) {
            $leads = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $leads_table
                WHERE partner_id = %d AND status = %s
                ORDER BY updated_at DESC",
                $partner_id,
                $stage_key
            ));

            $pipeline_data[$stage_key] = array(
                'info' => $stage_info,
                'leads' => $leads,
                'count' => count($leads)
            );
        }

        return $pipeline_data;
    }

    public static function move_lead_to_stage($lead_id, $new_stage) {
        global $wpdb;
        $leads_table = $wpdb->prefix . 'ppd_leads';

        // Validate stage
        $stages = self::get_pipeline_stages();
        if (!array_key_exists($new_stage, $stages)) {
            return false;
        }

        $result = $wpdb->update(
            $leads_table,
            array('status' => $new_stage, 'updated_at' => current_time('mysql')),
            array('id' => $lead_id)
        );

        if ($result !== false) {
            // Get lead info for notification
            $lead = PPD_Leads::get_lead($lead_id);
            if ($lead) {
                PPD_Notifications::notify_lead_status_changed(
                    $lead->partner_id,
                    $lead->student_name,
                    $new_stage
                );
            }
        }

        return $result;
    }

    public static function get_pipeline_stats($partner_id) {
        global $wpdb;
        $leads_table = $wpdb->prefix . 'ppd_leads';

        $total_value = 0; // This would need service pricing
        $win_rate = 0;

        $total_leads = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $leads_table WHERE partner_id = %d",
            $partner_id
        ));

        $won_leads = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $leads_table WHERE partner_id = %d AND status = 'closed_won'",
            $partner_id
        ));

        $lost_leads = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $leads_table WHERE partner_id = %d AND status = 'closed_lost'",
            $partner_id
        ));

        $closed_leads = $won_leads + $lost_leads;
        $win_rate = $closed_leads > 0 ? round(($won_leads / $closed_leads) * 100, 2) : 0;

        return array(
            'total_leads' => (int) $total_leads,
            'won_leads' => (int) $won_leads,
            'lost_leads' => (int) $lost_leads,
            'win_rate' => $win_rate,
        );
    }
}
