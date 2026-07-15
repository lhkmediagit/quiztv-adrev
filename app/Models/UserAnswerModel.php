<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model: UserAnswerModel
 * Interacts with the 'user_answers' table. Stores detailed option selection state per attempt.
 */
class UserAnswerModel extends Model
{
    protected $table            = 'user_answers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'attempt_id',
        'question_id',
        'selected_option',
        'is_correct',
        'answered_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Save an individual user answer.
     */
    public function saveAnswer($attempt_id, $question_id, $selected_option, $is_correct)
    {
        $data = [
            'attempt_id'      => $attempt_id,
            'question_id'     => $question_id,
            'selected_option' => $selected_option,
            'is_correct'      => $is_correct,
            'answered_at'     => date('Y-m-d H:i:s'),
        ];
        return $this->insert($data);
    }

    /**
     * Retrieve all answers submitted for a specific attempt, joined with question details.
     */
    public function getByAttempt($attempt_id)
    {
        return $this->select('user_answers.*, questions_and_options.question, questions_and_options.explanation')
                    ->join('questions_and_options', 'questions_and_options.id = user_answers.question_id', 'left')
                    ->where('user_answers.attempt_id', $attempt_id)
                    ->findAll();
    }
}
