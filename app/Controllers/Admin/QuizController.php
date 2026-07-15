<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuizModel;
use App\Models\CategoryModel;
use App\Models\RecommendedQuizModel;

/**
 * Controller: QuizController
 * Admin controller for managing quiz configurations (listing, creating, updating, deleting).
 * Manages manual cascading deletions because of the absence of foreign key constraints.
 */
class QuizController extends BaseController
{
    /**
     * List all quizzes with categories.
     */
    public function index()
    {
        $quizModel = new QuizModel();
        $quizzes = $quizModel->getWithCategory();

        return view('admin/quizzes/index', [
            'quizzes' => $quizzes,
            'title'   => 'Manage Quizzes - QuizTv',
        ]);
    }

    /**
     * Render the form to build a new quiz.
     */
    public function create()
    {
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->findAll();

        return view('admin/quizzes/create', [
            'categories' => $categories,
            'title'      => 'Create New Quiz - QuizTv',
        ]);
    }

    /**
     * Process creation form and generate unique slugs.
     */
    public function store()
    {
        $rules = [
            'title'            => 'required|min_length[3]|max_length[255]',
            'description'      => 'required',
            'category_id'      => 'required|integer',
            'pass_rate'        => 'required|decimal',
            'duration_minutes' => 'required|integer',
            'difficulty'       => 'required|in_list[easy,medium,hard]',
            'is_active'        => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $quizModel = new QuizModel();

        // Auto generate URL slug from title
        $slug = url_title($this->request->getPost('title'), '-', true);
        $slugCount = $quizModel->where('slug', $slug)->countAllResults();
        if ($slugCount > 0) {
            $slug = $slug . '-' . time();
        }

        $data = [
            'title'            => $this->request->getPost('title'),
            'slug'             => $slug,
            'description'      => $this->request->getPost('description'),
            'category_id'      => (int)$this->request->getPost('category_id'),
            'pass_rate'        => (float)$this->request->getPost('pass_rate'),
            'duration_minutes' => (int)$this->request->getPost('duration_minutes'),
            'difficulty'       => $this->request->getPost('difficulty'),
            'is_active'        => $this->request->getPost('is_active') !== null ? (int)$this->request->getPost('is_active') : 1,
            'created_by'       => session()->get('admin_id') ?? session()->get('user_id') ?? 1,
        ];

        // Handle thumbnail file upload
        $thumbnail = $this->request->getFile('thumbnail');
        if ($thumbnail && $thumbnail->isValid() && !$thumbnail->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/quizzes/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $newName = $thumbnail->getRandomName();
            $thumbnail->move($uploadPath, $newName);
            $data['thumbnail'] = base_url('uploads/quizzes/' . $newName);
        }

        $quizModel->insert($data);

        return redirect()->to('/admin/quizzes')->with('success', 'Quiz configuration saved.');
    }

    /**
     * Render the form to edit an existing quiz.
     */
    public function edit($id)
    {
        $quizModel = new QuizModel();
        $quiz = $quizModel->find($id);
        if (!$quiz) {
            return redirect()->to('/admin/quizzes')->with('error', 'Quiz not found.');
        }

        $categoryModel = new CategoryModel();
        $categories = $categoryModel->findAll();

        // Fetch other quizzes for recommendation mapping
        $otherQuizzes = $quizModel->where('id !=', $id)->findAll();

        $recommendedModel = new RecommendedQuizModel();
        $recommendedIds = $recommendedModel->where('quiz_id', $id)->findColumn('recommended_quiz_id') ?? [];

        return view('admin/quizzes/edit', [
            'quiz'           => $quiz,
            'categories'     => $categories,
            'otherQuizzes'   => $otherQuizzes,
            'recommendedIds' => $recommendedIds,
            'title'          => 'Edit Quiz: ' . $quiz->title,
        ]);
    }

    /**
     * Process editing updates.
     */
    public function update($id)
    {
        $quizModel = new QuizModel();
        $quiz = $quizModel->find($id);
        if (!$quiz) {
            return redirect()->to('/admin/quizzes')->with('error', 'Quiz not found.');
        }

        $rules = [
            'title'            => 'required|min_length[3]|max_length[255]',
            'description'      => 'required',
            'category_id'      => 'required|integer',
            'pass_rate'        => 'required|decimal',
            'duration_minutes' => 'required|integer',
            'difficulty'       => 'required|in_list[easy,medium,hard]',
            'is_active'        => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title'            => $this->request->getPost('title'),
            'description'      => $this->request->getPost('description'),
            'category_id'      => (int)$this->request->getPost('category_id'),
            'pass_rate'        => (float)$this->request->getPost('pass_rate'),
            'duration_minutes' => (int)$this->request->getPost('duration_minutes'),
            'difficulty'       => $this->request->getPost('difficulty'),
            'is_active'        => $this->request->getPost('is_active') !== null ? (int)$this->request->getPost('is_active') : 0,
        ];

        // Handle updated thumbnail file upload
        $thumbnail = $this->request->getFile('thumbnail');
        if ($thumbnail && $thumbnail->isValid() && !$thumbnail->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/quizzes/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $newName = $thumbnail->getRandomName();
            $thumbnail->move($uploadPath, $newName);

            // Delete old file
            if ($quiz->thumbnail) {
                $oldFilename = basename($quiz->thumbnail);
                if (file_exists($uploadPath . $oldFilename)) {
                    @unlink($uploadPath . $oldFilename);
                }
            }

            $data['thumbnail'] = base_url('uploads/quizzes/' . $newName);
        }

        $quizModel->update($id, $data);

        // Update Recommended mapping
        $recommendedModel = new RecommendedQuizModel();
        $recommendedModel->where('quiz_id', $id)->delete();

        $recommendations = $this->request->getPost('recommended_quizzes') ?? [];
        if (!empty($recommendations)) {
            $recData = [];
            foreach ($recommendations as $recId) {
                $recData[] = [
                    'quiz_id'             => $id,
                    'recommended_quiz_id' => (int)$recId,
                ];
            }
            $recommendedModel->insertBatch($recData);
        }

        return redirect()->to('/admin/quizzes')->with('success', 'Quiz configuration updated.');
    }

    /**
     * Perform cascading deletion of a quiz and its linked questions, options, attempts, answers, and recommendation maps.
     */
    public function delete($id)
    {
        $quizModel = new QuizModel();
        $quiz = $quizModel->find($id);
        if (!$quiz) {
            return redirect()->to('/admin/quizzes')->with('error', 'Quiz not found.');
        }

        if ($quiz->thumbnail) {
            $uploadPath = FCPATH . 'uploads/quizzes/';
            $oldFilename = basename($quiz->thumbnail);
            if (file_exists($uploadPath . $oldFilename)) {
                @unlink($uploadPath . $oldFilename);
            }
        }

        $db = \Config\Database::connect();
        
        // Delete related questions
        $db->table('questions_and_options')->where('quiz_id', $id)->delete();

        // Delete recommended quiz relations
        $db->table('recommended_quizzes')->where('quiz_id', $id)->orWhere('recommended_quiz_id', $id)->delete();

        // Delete user attempts and answers manually (as no foreign key cascading exists)
        $attempts = $db->table('quiz_attempts')->select('id')->where('quiz_id', $id)->get()->getResult();
        foreach ($attempts as $attempt) {
            $db->table('user_answers')->where('attempt_id', $attempt->id)->delete();
        }
        $db->table('quiz_attempts')->where('quiz_id', $id)->delete();

        // Delete main quiz row
        $quizModel->delete($id);

        return redirect()->to('/admin/quizzes')->with('success', 'Quiz configuration and all associated question details and play history deleted.');
    }
}
