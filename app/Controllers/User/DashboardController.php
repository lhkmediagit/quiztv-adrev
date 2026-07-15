<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\AttemptModel;

/**
 * Controller: DashboardController
 * Handles logged-in user dashboard statistical views, attempt history, and profile updates.
 */
class DashboardController extends BaseController
{
    /**
     * User dashboard dashboard. Calculates aggregate counts and fetches recent attempts.
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $attemptModel = new AttemptModel();

        $totalAttempts = $attemptModel->where('user_id', $userId)->where('completed', 1)->countAllResults();

        $avgPercentage = 0;
        if ($totalAttempts > 0) {
            $avgPercentage = $attemptModel->selectAvg('percentage')
                                          ->where('user_id', $userId)
                                          ->where('completed', 1)
                                          ->first()
                                          ->percentage ?? 0;
        }

        $recentAttempts = $attemptModel->select('quiz_attempts.*, quizzes.title as quiz_title, quizzes.slug as quiz_slug')
                                        ->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id', 'left')
                                        ->where('quiz_attempts.user_id', $userId)
                                        ->where('quiz_attempts.completed', 1)
                                        ->orderBy('quiz_attempts.completed_at', 'DESC')
                                        ->limit(5)
                                        ->findAll();

        return view('user/dashboard', [
            'totalAttempts'  => $totalAttempts,
            'avgPercentage'  => round($avgPercentage, 2),
            'recentAttempts' => $recentAttempts,
            'title'          => 'Dashboard - QuizTv',
        ]);
    }

    /**
     * Display all historical quiz play logs for the current user.
     */
    public function history()
    {
        $userId = session()->get('user_id');
        $attemptModel = new AttemptModel();
        
        $attempts = $attemptModel->getUserHistory($userId);

        return view('user/history', [
            'attempts' => $attempts,
            'title'    => 'My Quiz History - QuizTv',
        ]);
    }

    /**
     * Render the profile edit page.
     */
    public function profile()
    {
        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        return view('user/profile', [
            'user'  => $user,
            'title' => 'Edit Profile - QuizTv',
        ]);
    }

    /**
     * Process updates to user name, email, avatar, and password.
     */
    public function update()
    {
        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        $rules = [
            'name'  => 'required|min_length[2]|max_length[100]',
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'required|min_length[6]|max_length[255]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        // Manage file upload for profile avatar
        $avatarFile = $this->request->getFile('avatar');
        if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
            // Check if directory exists
            $uploadPath = FCPATH . 'uploads/avatars/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newName = $avatarFile->getRandomName();
            $avatarFile->move($uploadPath, $newName);

            // Clean up old avatar image file
            if ($user->avatar) {
                $oldFilename = basename($user->avatar);
                if (file_exists($uploadPath . $oldFilename)) {
                    @unlink($uploadPath . $oldFilename);
                }
            }

            $data['avatar'] = base_url('uploads/avatars/' . $newName);
        }

        $userModel->update($userId, $data);

        // Sync session details
        session()->set('name', $data['name']);

        return redirect()->to('/user/profile')->with('success', 'Profile configuration updated.');
    }
}
