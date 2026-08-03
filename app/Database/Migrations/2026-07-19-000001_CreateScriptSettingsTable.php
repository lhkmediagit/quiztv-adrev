<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: CreateScriptSettingsTable
 * Creates the 'script_settings' table for storing custom scripts (header/footer)
 * as key-value pairs.
 */
class CreateScriptSettingsTable extends Migration
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
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('script_settings');

        // Seed default settings
        $defaults = [
            [
                'setting_key'   => 'header_scripts',
                'setting_value' => '',
                'description'   => 'Custom HTML/JS/CSS code injected inside the head tag',
            ],
            [
                'setting_key'   => 'body_scripts',
                'setting_value' => '',
                'description'   => 'Custom HTML/JS/CSS code injected immediately after opening body tag',
            ],
            [
                'setting_key'   => 'footer_scripts',
                'setting_value' => '',
                'description'   => 'Custom HTML/JS/CSS code injected just before the closing body tag',
            ],
        ];

        $db = \Config\Database::connect();
        $builder = $db->table('script_settings');
        $now = date('Y-m-d H:i:s');
        foreach ($defaults as $row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            $builder->insert($row);
        }
    }

    public function down()
    {
        $this->forge->dropTable('script_settings');
    }
}
