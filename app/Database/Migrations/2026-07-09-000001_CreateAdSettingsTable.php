<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: CreateAdSettingsTable
 * Creates the 'ad_settings' table for storing Google Ad Manager configuration
 * as key-value pairs. Allows admins to manage network codes and ad slots
 * from the admin panel without touching code.
 */
class CreateAdSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'setting_value' => [
                'type'    => 'TEXT',
                'null'    => true,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ad_settings');

        // Seed default settings
        $defaults = [
            ['setting_key' => 'ads_enabled',            'setting_value' => '0', 'description' => 'Global ads kill switch (1=enabled, 0=disabled)'],
            ['setting_key' => 'gam_network_code',       'setting_value' => '',  'description' => 'Google Ad Manager Network Code'],
            ['setting_key' => 'banner_enabled',         'setting_value' => '1', 'description' => 'Enable/disable banner ads'],
            ['setting_key' => 'banner_home_slot',       'setting_value' => '',  'description' => 'Banner ad unit path for home page'],
            ['setting_key' => 'banner_quiz_slot',       'setting_value' => '',  'description' => 'Banner ad unit path for quiz landing page'],
            ['setting_key' => 'banner_play_slot',       'setting_value' => '',  'description' => 'Banner ad unit path for quiz play/results page'],
            ['setting_key' => 'banner_refresh_seconds', 'setting_value' => '60','description' => 'Banner auto-refresh interval in seconds (minimum 30)'],
            ['setting_key' => 'banner_size',            'setting_value' => 'responsive', 'description' => 'Default banner size (responsive, 728x90, 336x280, 300x250)'],
            ['setting_key' => 'rewarded_enabled',       'setting_value' => '1', 'description' => 'Enable/disable rewarded ads'],
            ['setting_key' => 'rewarded_slot',          'setting_value' => '',  'description' => 'Rewarded ad unit path'],
            ['setting_key' => 'rewarded_message',       'setting_value' => 'Great job watching the ad! Keep going!', 'description' => 'Message shown after rewarded ad completion'],
            ['setting_key' => 'ads_txt_enabled',        'setting_value' => '1', 'description' => 'Enable/disable dynamic ads.txt serving (1=enabled, 0=disabled)'],
            ['setting_key' => 'ads_txt_content',        'setting_value' => "google.com, pub-7542579898564659, DIRECT, f08c47fec0942fa0", 'description' => 'Custom ads.txt content list'],
        ];

        $db = \Config\Database::connect();
        $builder = $db->table('ad_settings');
        $now = date('Y-m-d H:i:s');
        foreach ($defaults as $row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            $builder->insert($row);
        }
    }

    public function down()
    {
        $this->forge->dropTable('ad_settings');
    }
}
