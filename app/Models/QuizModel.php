<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model: QuizModel
 * Interacts with the 'quizzes' table. Includes helper queries for category joining and status filtering.
 */
class QuizModel extends Model
{
    protected $table            = 'quizzes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'slug',
        'title',
        'description',
        'category_id',
        'thumbnail',
        'pass_rate',
        'total_attempts',
        'duration_minutes',
        'difficulty',
        'stages',
        'is_active',
        'created_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Fetch a quiz details based on its slug.
     */
    public function getBySlug($slug)
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Get all active quizzes.
     */
    public function getActiveQuizzes()
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Fetch all quizzes, including their joined categories info.
     */
    public function getWithCategory()
    {
        return $this->select('quizzes.*, categories.name as category_name')
                    ->join('categories', 'categories.id = quizzes.category_id', 'left')
                    ->findAll();
    }
}
