<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model: AttemptModel
 * Interacts with the 'quiz_attempts' table. Tracks quiz session progress, score increments, and history.
 */
class AttemptModel extends Model
{
    protected $table            = 'quiz_attempts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'quiz_id',
        'user_id',
        'guest_token',
        'lead_name',
        'lead_email',
        'lead_phone',
        'score',
        'total_questions',
        'percentage',
        'completed',
        'started_at',
        'completed_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Start a new quiz attempt.
     */
    public function createAttempt($quiz_id, $user_id, $guest_token)
    {
        $db = \Config\Database::connect();
        $totalQuestions = $db->table('questions_and_options')->where('quiz_id', $quiz_id)->countAllResults();

        $data = [
            'quiz_id'         => $quiz_id,
            'user_id'         => $user_id ?: null,
            'guest_token'     => $guest_token ?: null,
            'score'           => 0,
            'total_questions' => $totalQuestions,
            'percentage'      => 0.00,
            'completed'       => 0,
            'started_at'      => date('Y-m-d H:i:s'),
        ];

        $this->insert($data);
        return $this->getInsertID();
    }

    /**
     * Get details of an attempt by ID.
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * Increment/update score for a specific attempt.
     */
    public function updateScore($attempt_id, $score)
    {
        return $this->update($attempt_id, [
            'score'      => $score,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Mark attempt as completed, calculate percentage, and save completion details.
     */
    public function completeAttempt($attempt_id, $score, $total, $percentage)
    {
        return $this->update($attempt_id, [
            'completed'       => 1,
            'score'           => $score,
            'total_questions' => $total,
            'percentage'      => $percentage,
            'completed_at'    => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get quiz attempts history for a user.
     */
    public function getUserHistory($user_id)
    {
        return $this->select('quiz_attempts.*, quizzes.title as quiz_title, quizzes.slug as quiz_slug, quizzes.thumbnail as quiz_thumbnail')
                    ->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id', 'left')
                    ->where('quiz_attempts.user_id', $user_id)
                    ->where('quiz_attempts.completed', 1)
                    ->orderBy('quiz_attempts.started_at', 'DESC')
                    ->findAll();
    }

    /**
     * Increment the quiz attempts counter by 1.
     */
    public function incrementQuizAttempts($quiz_id)
    {
        $db = \Config\Database::connect();
        return $db->table('quizzes')->where('id', $quiz_id)->increment('total_attempts', 1);
    }
}
