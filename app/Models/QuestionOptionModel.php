<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model: QuestionOptionModel
 * Interacts with the 'questions_and_options' table. Holds questions, choice options, and answers.
 */
class QuestionOptionModel extends Model
{
    protected $table            = 'questions_and_options';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'quiz_id',
        'round_number',
        'question',
        'visual',
        'explanation',
        'option1',
        'option2',
        'option3',
        'option4',
        'correct_option',
        'order_index',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all questions for a quiz ordered by round and order index.
     */
    public function getByQuizId($quiz_id)
    {
        return $this->where('quiz_id', $quiz_id)
                    ->orderBy('round_number', 'ASC')
                    ->orderBy('order_index', 'ASC')
                    ->findAll();
    }

    /**
     * Get the first question of a quiz (order_index = 1).
     */
    public function getFirstQuestion($quiz_id)
    {
        return $this->where('quiz_id', $quiz_id)
                    ->where('order_index', 1)
                    ->first();
    }

    /**
     * Get the next question of a quiz (current order_index + 1).
     */
    public function getNextQuestion($quiz_id, $current_order_index)
    {
        return $this->where('quiz_id', $quiz_id)
                    ->where('order_index', $current_order_index + 1)
                    ->first();
    }

    /**
     * Get question details by ID.
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * Get total question count in a quiz.
     */
    public function getTotalCount($quiz_id)
    {
        return $this->where('quiz_id', $quiz_id)->countAllResults();
    }
}
