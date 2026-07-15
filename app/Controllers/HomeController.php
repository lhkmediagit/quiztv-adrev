<?php

namespace App\Controllers;

use App\Models\QuizModel;
use App\Models\CategoryModel;

/**
 * Controller: HomeController
 * Renders the public landing homepage, querying all active quizzes grouped by category.
 */
class HomeController extends BaseController
{
    public function index()
    {
        $quizModel = new QuizModel();
        $categoryModel = new CategoryModel();

        // Retrieve categories and active quizzes
        $categories = $categoryModel->findAll();
        $quizzes = $quizModel->select('quizzes.*, categories.name as category_name')
                             ->join('categories', 'categories.id = quizzes.category_id', 'left')
                             ->where('quizzes.is_active', 1)
                             ->findAll();

        // Sort quizzes to place the 'quiztv' slug at the first position
        usort($quizzes, function($a, $b) {
            if ($a->slug === 'quiztv') return -1;
            if ($b->slug === 'quiztv') return 1;
            return 0; // maintain original relative order for others
        });

        return view('home/index', [
            'categories' => $categories,
            'quizzes'    => $quizzes,
            'title'      => 'QuizTv - Test Your Knowledge'
        ]);
    }
}
