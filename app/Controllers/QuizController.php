<?php

namespace App\Controllers;

use App\Models\QuizModel;
use App\Models\RecommendedQuizModel;

/**
 * Controller: QuizController
 * Coordinates quiz landing pages and the single-page shell playing interface.
 */
class QuizController extends BaseController
{
    /**
     * Render the quiz landing page. Fetches quiz stats and recommendations.
     */
    public function landing($slug)
    {
        $quizModel = new QuizModel();
        $recommendedModel = new RecommendedQuizModel();

        $quiz = $quizModel->getBySlug($slug);
        if (!$quiz || (int)$quiz->is_active === 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Quiz not found or inactive.');
        }

        // Fetch recommended quizzes
        $recommendations = $recommendedModel->select('quizzes.*, categories.name as category_name')
                                            ->join('quizzes', 'quizzes.id = recommended_quizzes.recommended_quiz_id')
                                            ->join('categories', 'categories.id = quizzes.category_id', 'left')
                                            ->where('recommended_quizzes.quiz_id', $quiz->id)
                                            ->where('quizzes.is_active', 1)
                                            ->findAll();

        // Fetch about HTML text block from local cache
        $aboutHtml = '';
        $cacheFile = WRITEPATH . 'quiz_about_details.json';
        if (file_exists($cacheFile)) {
            $cache = json_decode(file_get_contents($cacheFile), true);
            $aboutHtml = $cache[$slug] ?? '';
        }

        return view('quiz/landing', [
            'quiz'            => $quiz,
            'recommendations' => $recommendations,
            'title'           => $quiz->title,
            'aboutHtml'       => $aboutHtml,
        ]);
    }

    /**
     * Render the single-page quiz play interface shell.
     * Sets user/guest tokens for AJAX sessions.
     */
    public function play($slug)
    {
        $quizModel = new QuizModel();
        $quiz = $quizModel->getBySlug($slug);
        if (!$quiz || (int)$quiz->is_active === 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Quiz not found or inactive.');
        }

        $session = session();
        if ($session->has('user_id')) {
            $userId = $session->get('user_id');
            $guestToken = '';
        } else {
            $userId = '';
            if (!$session->has('guest_token')) {
                $session->set('guest_token', 'guest_' . bin2hex(random_bytes(8)));
            }
            $guestToken = $session->get('guest_token');
        }

        return view('quiz/play', [
            'quiz'       => $quiz,
            'userId'     => $userId,
            'guestToken' => $guestToken,
            'title'      => 'Playing: ' . $quiz->title,
        ]);
    }
}
