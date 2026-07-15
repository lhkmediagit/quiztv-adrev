<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\QuizModel;
use App\Models\QuestionOptionModel;
use App\Models\AttemptModel;
use App\Models\UserAnswerModel;
use App\Models\UserModel;
use App\Models\RecommendedQuizModel;

/**
 * Controller: QuizApiController
 * Handles all JSON AJAX play requests, tracking attempt records, answer submission,
 * progression, state restarts, and quiz completions.
 */
class QuizApiController extends BaseController
{
    /**
     * Start a quiz attempt.
     */
    public function start()
    {
        $quizId = $this->request->getPost('quiz_id');
        $userId = $this->request->getPost('user_id') ?: null;
        $guestToken = $this->request->getPost('guest_token') ?: null;

        if (!$quizId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Quiz ID is required.']);
        }

        $quizModel = new QuizModel();
        $quiz = $quizModel->find($quizId);
        if (!$quiz) {
            return $this->response->setJSON(['success' => false, 'message' => 'Quiz not found.']);
        }

        $attemptModel = new AttemptModel();
        $attemptId = $attemptModel->createAttempt($quizId, $userId, $guestToken);
        $attemptModel->incrementQuizAttempts($quizId);

        $questionModel = new QuestionOptionModel();
        $firstQuestion = $questionModel->getFirstQuestion($quizId);
        $totalQuestions = $questionModel->getTotalCount($quizId);

        if (!$firstQuestion) {
            return $this->response->setJSON(['success' => false, 'message' => 'No questions found for this quiz.']);
        }

        $progress = $this->getRoundProgress($quizId, $firstQuestion->round_number, $firstQuestion->order_index);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'attempt_id' => $attemptId,
                'question' => [
                    'id'           => $firstQuestion->id,
                    'question'     => $firstQuestion->question,
                    'visual'       => $firstQuestion->visual,
                    'option1'      => $firstQuestion->option1,
                    'option2'      => $firstQuestion->option2,
                    'option3'      => $firstQuestion->option3,
                    'option4'      => $firstQuestion->option4,
                    'round_number' => $firstQuestion->round_number,
                    'order_index'  => $firstQuestion->order_index,
                    'round_total'  => $progress['round_total'],
                    'round_index'  => $progress['round_index'],
                    'stage_title'  => $progress['stage_title'],
                ],
                'total_questions' => $totalQuestions,
                'score_so_far'    => 0,
            ]
        ]);
    }

    /**
     * Process an answered question.
     */
    public function submitAnswer()
    {
        $attemptId = $this->request->getPost('attempt_id');
        $questionId = $this->request->getPost('question_id');
        $selectedOption = (int)$this->request->getPost('selected_option');

        if (!$attemptId || !$questionId || !$selectedOption) {
            return $this->response->setJSON(['success' => false, 'message' => 'All parameters are required.']);
        }

        $questionModel = new QuestionOptionModel();
        $question = $questionModel->find($questionId);
        if (!$question) {
            return $this->response->setJSON(['success' => false, 'message' => 'Question not found.']);
        }

        $attemptModel = new AttemptModel();
        $attempt = $attemptModel->find($attemptId);
        if (!$attempt) {
            return $this->response->setJSON(['success' => false, 'message' => 'Attempt not found.']);
        }

        $isCorrect = ($selectedOption === (int)$question->correct_option) ? 1 : 0;
        
        $userAnswerModel = new UserAnswerModel();
        $userAnswerModel->saveAnswer($attemptId, $questionId, $selectedOption, $isCorrect);

        // Update score
        $newScore = $attempt->score + $isCorrect;
        $attemptModel->updateScore($attemptId, $newScore);

        $questionsAnswered = $userAnswerModel->where('attempt_id', $attemptId)->countAllResults();

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'is_correct'         => $isCorrect ? true : false,
                'correct_option'     => (int)$question->correct_option,
                'explanation'        => $question->explanation,
                'score_so_far'       => $newScore,
                'questions_answered' => $questionsAnswered,
            ]
        ]);
    }

    /**
     * Fetch the next question.
     */
    public function nextQuestion()
    {
        $attemptId = $this->request->getPost('attempt_id');
        $currentOrderIndex = (int)$this->request->getPost('current_order_index');

        if (!$attemptId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Attempt ID is required.']);
        }

        $attemptModel = new AttemptModel();
        $attempt = $attemptModel->find($attemptId);
        if (!$attempt) {
            return $this->response->setJSON(['success' => false, 'message' => 'Attempt not found.']);
        }

        $questionModel = new QuestionOptionModel();
        $nextQuestion = $questionModel->getNextQuestion($attempt->quiz_id, $currentOrderIndex);

        if (!$nextQuestion) {
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'is_last' => true,
                ]
            ]);
        }

        $progress = $this->getRoundProgress($attempt->quiz_id, $nextQuestion->round_number, $nextQuestion->order_index);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'is_last' => false,
                'question' => [
                    'id'           => $nextQuestion->id,
                    'question'     => $nextQuestion->question,
                    'visual'       => $nextQuestion->visual,
                    'option1'      => $nextQuestion->option1,
                    'option2'      => $nextQuestion->option2,
                    'option3'      => $nextQuestion->option3,
                    'option4'      => $nextQuestion->option4,
                    'round_number' => $nextQuestion->round_number,
                    'order_index'  => $nextQuestion->order_index,
                    'round_total'  => $progress['round_total'],
                    'round_index'  => $progress['round_index'],
                    'stage_title'  => $progress['stage_title'],
                ]
            ]
        ]);
    }

    /**
     * Complete the quiz and calculate final scores.
     */
    public function complete()
    {
        $attemptId = $this->request->getPost('attempt_id');
        if (!$attemptId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Attempt ID is required.']);
        }

        $attemptModel = new AttemptModel();
        $attempt = $attemptModel->find($attemptId);
        if (!$attempt) {
            return $this->response->setJSON(['success' => false, 'message' => 'Attempt not found.']);
        }

        $quizModel = new QuizModel();
        $quiz = $quizModel->find($attempt->quiz_id);

        $questionModel = new QuestionOptionModel();
        $totalQuestions = $questionModel->getTotalCount($attempt->quiz_id);

        $percentage = $totalQuestions > 0 ? ($attempt->score / $totalQuestions) * 100 : 0.00;

        // Set completed in db
        $attemptModel->completeAttempt($attemptId, $attempt->score, $totalQuestions, $percentage);

        // Update user counters
        if ($attempt->user_id) {
            $userModel = new UserModel();
            $user = $userModel->find($attempt->user_id);
            if ($user) {
                $userModel->update($attempt->user_id, [
                    'total_quizzes_taken' => (int)$user->total_quizzes_taken + 1
                ]);
            }
        }

        $passFailLabel = ($percentage >= $quiz->pass_rate) ? 'Pass' : 'Fail';

        // Recommended quizzes
        $recommendedModel = new RecommendedQuizModel();
        $recommended = $recommendedModel->select('quizzes.title, quizzes.slug, quizzes.thumbnail, categories.name as category_name')
                                         ->join('quizzes', 'quizzes.id = recommended_quizzes.recommended_quiz_id')
                                         ->join('categories', 'categories.id = quizzes.category_id', 'left')
                                         ->where('recommended_quizzes.quiz_id', $quiz->id)
                                         ->where('quizzes.is_active', 1)
                                         ->findAll();

        $db = \Config\Database::connect();
        $roundStats = $db->table('user_answers')
                         ->select('questions_and_options.round_number, SUM(user_answers.is_correct) as round_score, COUNT(user_answers.id) as round_total')
                         ->join('questions_and_options', 'questions_and_options.id = user_answers.question_id')
                         ->where('user_answers.attempt_id', $attemptId)
                         ->groupBy('questions_and_options.round_number')
                         ->orderBy('questions_and_options.round_number', 'ASC')
                         ->get()
                         ->getResult();

        $stages = json_decode($quiz->stages ?? '[]', true);
        foreach ($roundStats as $stat) {
            $stat->stage_title = $stages[$stat->round_number - 1] ?? ('Round ' . $stat->round_number);
        }

        $leadName = $attempt->lead_name ?? '';

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'score'           => $attempt->score,
                'total_questions' => $totalQuestions,
                'percentage'      => round($percentage, 2),
                'pass_fail_label' => $passFailLabel,
                'recommended'     => $recommended,
                'round_stats'     => $roundStats,
                'lead_name'       => $leadName,
            ]
        ]);
    }

    /**
     * Save user name, email, and phone number (lead generation).
     */
    public function saveLead()
    {
        $attemptId = $this->request->getPost('attempt_id');
        $name = trim($this->request->getPost('lead_name') ?? '');
        $email = trim($this->request->getPost('lead_email') ?? '');
        $phone = trim($this->request->getPost('lead_phone') ?? '');

        if (!$attemptId || $name === '' || $email === '' || $phone === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'All fields are required.']);
        }

        $attemptModel = new AttemptModel();
        $attempt = $attemptModel->find($attemptId);
        if (!$attempt) {
            return $this->response->setJSON(['success' => false, 'message' => 'Attempt not found.']);
        }

        $attemptModel->update($attemptId, [
            'lead_name'  => $name,
            'lead_email' => $email,
            'lead_phone' => $phone,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Lead details saved successfully.',
            'data' => [
                'success' => true
            ]
        ]);
    }

    /**
     * Restart a quiz run, building a new attempt row.
     */
    public function restart()
    {
        $quizId = $this->request->getPost('quiz_id');
        $oldAttemptId = $this->request->getPost('old_attempt_id');

        if (!$quizId || !$oldAttemptId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Quiz ID and Old Attempt ID are required.']);
        }

        $attemptModel = new AttemptModel();
        $oldAttempt = $attemptModel->find($oldAttemptId);
        if (!$oldAttempt) {
            return $this->response->setJSON(['success' => false, 'message' => 'Old attempt details not found.']);
        }

        $userId = $oldAttempt->user_id;
        $guestToken = $oldAttempt->guest_token;

        $newAttemptId = $attemptModel->createAttempt($quizId, $userId, $guestToken);
        $attemptModel->incrementQuizAttempts($quizId);

        $questionModel = new QuestionOptionModel();
        $firstQuestion = $questionModel->getFirstQuestion($quizId);
        $totalQuestions = $questionModel->getTotalCount($quizId);

        if (!$firstQuestion) {
            return $this->response->setJSON(['success' => false, 'message' => 'No questions found for this quiz.']);
        }

        $progress = $this->getRoundProgress($quizId, $firstQuestion->round_number, $firstQuestion->order_index);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'attempt_id' => $newAttemptId,
                'question' => [
                    'id'           => $firstQuestion->id,
                    'question'     => $firstQuestion->question,
                    'visual'       => $firstQuestion->visual,
                    'option1'      => $firstQuestion->option1,
                    'option2'      => $firstQuestion->option2,
                    'option3'      => $firstQuestion->option3,
                    'option4'      => $firstQuestion->option4,
                    'round_number' => $firstQuestion->round_number,
                    'order_index'  => $firstQuestion->order_index,
                    'round_total'  => $progress['round_total'],
                    'round_index'  => $progress['round_index'],
                    'stage_title'  => $progress['stage_title'],
                ],
                'total_questions' => $totalQuestions,
                'score_so_far'    => 0,
            ]
        ]);
    }

    /**
     * Helper to compute progress details of the current round.
     */
    private function getRoundProgress($quizId, $roundNumber, $orderIndex)
    {
        $db = \Config\Database::connect();
        
        // Count total questions in this round
        $roundTotal = $db->table('questions_and_options')
                         ->where('quiz_id', $quizId)
                         ->where('round_number', $roundNumber)
                         ->countAllResults();
                         
        // Count how many questions in this round have order_index < current order_index
        $priorInRound = $db->table('questions_and_options')
                           ->where('quiz_id', $quizId)
                           ->where('round_number', $roundNumber)
                           ->where('order_index <', $orderIndex)
                           ->countAllResults();
                           
        $roundIndex = $priorInRound + 1;
        
        // Fetch stage title
        $quizModel = new \App\Models\QuizModel();
        $quiz = $quizModel->find($quizId);
        $stages = json_decode($quiz->stages ?? '[]', true);
        $stageTitle = $stages[$roundNumber - 1] ?? ('Round ' . $roundNumber);
        
        return [
            'round_total' => $roundTotal,
            'round_index' => $roundIndex,
            'stage_title' => $stageTitle
        ];
    }
}
