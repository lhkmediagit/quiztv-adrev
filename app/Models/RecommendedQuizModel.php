<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model: RecommendedQuizModel
 * Interacts with the 'recommended_quizzes' table which links quizzes to other relevant suggestions.
 */
class RecommendedQuizModel extends Model
{
    protected $table            = 'recommended_quizzes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['quiz_id', 'recommended_quiz_id'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
