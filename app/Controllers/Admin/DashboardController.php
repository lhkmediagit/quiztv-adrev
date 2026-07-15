<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\QuizModel;
use App\Models\AttemptModel;

/**
 * Controller: DashboardController
 * Handles the admin panel dashboard main landing, showcasing app-wide statistics.
 */
class DashboardController extends BaseController
{
    /**
     * Render the admin home showing counts of users, quizzes, attempts, and overall averages.
     */
    public function index()
    {
        $userModel = new UserModel();
        $quizModel = new QuizModel();
        $attemptModel = new AttemptModel();

        $totalUsers = $userModel->countAllResults();
        $totalQuizzes = $quizModel->countAllResults();
        $totalAttempts = $attemptModel->where('completed', 1)->countAllResults();

        // Calculate average score across all system attempts
        $avgPercentage = 0;
        if ($totalAttempts > 0) {
            $avgPercentage = $attemptModel->selectAvg('percentage')->where('completed', 1)->first()->percentage ?? 0;
        }

        // Recent attempts list
        $recentAttempts = $attemptModel->select('quiz_attempts.*, quizzes.title as quiz_title, users.name as user_name')
                                        ->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id', 'left')
                                        ->join('users', 'users.id = quiz_attempts.user_id', 'left')
                                        ->where('quiz_attempts.completed', 1)
                                        ->orderBy('quiz_attempts.completed_at', 'DESC')
                                        ->limit(5)
                                        ->findAll();

        return view('admin/dashboard', [
            'totalUsers'     => $totalUsers,
            'totalQuizzes'   => $totalQuizzes,
            'totalAttempts'  => $totalAttempts,
            'avgPercentage'  => round($avgPercentage, 2),
            'recentAttempts' => $recentAttempts,
            'title'          => 'Admin Dashboard - QuizTv',
        ]);
    }
}
