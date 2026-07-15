<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder: MainSeeder
 * Seeds database tables with categories, quizzes, questions, recommended quizzes,
 * and sets up a single administrative user while clearing all end-user data tables.
 */
class MainSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // 1. Truncate user tables
        $this->db->table('users')->emptyTable();
        $this->db->table('quiz_attempts')->emptyTable();
        $this->db->table('user_answers')->emptyTable();

        // 2. Seed single Admin in admins table
        $admins = [
            [
                'id'         => 1,
                'name'       => 'Administrator',
                'email'      => 'admin@quiztv.com',
                'password'   => password_hash('Admin@123', PASSWORD_DEFAULT),
                'avatar'     => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];
        $this->db->table('admins')->emptyTable();
        $this->db->table('admins')->insertBatch($admins);
    }
}
