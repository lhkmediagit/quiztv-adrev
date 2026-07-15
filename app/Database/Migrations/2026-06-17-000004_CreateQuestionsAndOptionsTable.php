<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: CreateQuestionsAndOptionsTable
 * Creates the combined 'questions_and_options' table for holding quiz items,
 * four text choices, correct index reference, explanations, and sorting order.
 */
class CreateQuestionsAndOptionsTable extends Migration
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
            'quiz_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'round_number' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'question' => [
                'type' => 'TEXT',
            ],
            'explanation' => [
                'type' => 'TEXT',
            ],
            'option1' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
            ],
            'option2' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
            ],
            'option3' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
            ],
            'option4' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
            ],
            'correct_option' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
            ],
            'order_index' => [
                'type'       => 'INT',
                'constraint' => 11,
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
        $this->forge->createTable('questions_and_options');
    }

    public function down()
    {
        $this->forge->dropTable('questions_and_options');
    }
}
