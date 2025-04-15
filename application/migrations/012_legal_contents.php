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
class Migration_Legal_contents extends WS_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->get_where('settings', ['name' => 'display_cookie_notice'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'display_cookie_notice',
                'value' => '0',
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'cookie_notice_content'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'cookie_notice_content',
                'value' => 'Cookie notice content.',
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'display_terms_and_conditions'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'display_terms_and_conditions',
                'value' => '0',
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'terms_and_conditions_content'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'terms_and_conditions_content',
                'value' => 'Terms and conditions content.',
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'display_privacy_policy'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'display_privacy_policy',
                'value' => '0',
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'privacy_policy_content'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'privacy_policy_content',
                'value' => 'Privacy policy content.',
            ]);
        }

        if (!$this->db->table_exists('consents')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => true,
                ],
                'created' => [
                    'type' => 'TIMESTAMP',
                    'null' => true,
                ],
                'modified' => [
                    'type' => 'TIMESTAMP',
                    'null' => true,
                ],
                'first_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => '256',
                    'null' => true,
                ],
                'last_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => '256',
                    'null' => true,
                ],
                'email' => [
                    'type' => 'VARCHAR',
                    'constraint' => '512',
                    'null' => true,
                ],
                'ip' => [
                    'type' => 'VARCHAR',
                    'constraint' => '256',
                    'null' => true,
                ],
                'type' => [
                    'type' => 'VARCHAR',
                    'constraint' => '256',
                    'null' => true,
                ],
            ]);

            $this->dbforge->add_key('id', true);

            $this->dbforge->create_table('consents', true, ['engine' => 'InnoDB']);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->get_where('settings', ['name' => 'display_cookie_notice'])->num_rows()) {
            $this->db->delete('settings', [
                'name' => 'display_cookie_notice',
            ]);
        }

        if ($this->db->get_where('settings', ['name' => 'cookie_notice_content'])->num_rows()) {
            $this->db->delete('settings', [
                'name' => 'cookie_notice_content',
            ]);
        }

        if ($this->db->get_where('settings', ['name' => 'display_terms_and_conditions'])->num_rows()) {
            $this->db->delete('settings', [
                'name' => 'display_terms_and_conditions',
            ]);
        }

        if ($this->db->get_where('settings', ['name' => 'terms_and_conditions_content'])->num_rows()) {
            $this->db->delete('settings', [
                'name' => 'terms_and_conditions_content',
            ]);
        }

        if ($this->db->get_where('settings', ['name' => 'display_privacy_policy'])->num_rows()) {
            $this->db->delete('settings', [
                'name' => 'display_privacy_policy',
            ]);
        }

        if ($this->db->get_where('settings', ['name' => 'privacy_policy_content'])->num_rows()) {
            $this->db->delete('settings', [
                'name' => 'privacy_policy_content',
            ]);
        }

        if ($this->db->table_exists('consents')) {
            $this->dbforge->drop_table('consents');
        }
    }
}
