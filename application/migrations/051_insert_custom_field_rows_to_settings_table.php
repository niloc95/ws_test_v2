<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * @webSchedulr - Online Appointment Scheduler
 *
 * @package     @webSchedulr
 * @author      N. Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) Nilo Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

class Migration_Insert_custom_field_rows_to_settings_table extends WS_Migration
{
    /**
     * @var int
     */
    private const FIELD_NUMBER = 5;

    private const SETTINGS = [
        'display' => '0',
        'require' => '0',
        'label' => '',
    ];

    /**
     * Upgrade method.
     */
    public function up(): void
    {
        for ($i = 1; $i <= self::FIELD_NUMBER; $i++) {
            $field_name = 'custom_field_' . $i;

            foreach (self::SETTINGS as $name => $default_value) {
                $setting_name = $name . '_' . $field_name;

                if (!$this->db->get_where('settings', ['name' => $setting_name])->num_rows()) {
                    $this->db->insert('settings', [
                        'name' => $setting_name,
                        'value' => $default_value,
                    ]);
                }
            }
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        for ($i = 1; $i >= self::FIELD_NUMBER; $i++) {
            $field_name = 'custom_field_' . $i;

            foreach (self::SETTINGS as $name => $default_value) {
                $setting_name = $name . '_' . $field_name;

                if ($this->db->get_where('settings', ['name' => $setting_name])->num_rows()) {
                    $this->db->delete('settings', ['name' => $setting_name]);
                }
            }
        }
    }
}
