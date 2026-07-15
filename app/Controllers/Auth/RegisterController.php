<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

/**
 * Controller: RegisterController
 * Manages user sign-up rendering and record creation.
 */
class RegisterController extends BaseController
{
    /**
     * Render the sign-up form.
     */
    public function index()
    {
        if (session()->has('user_id')) {
            return redirect()->to('/user/dashboard');
        }
        return view('auth/register', ['title' => 'Register - QuizTv']);
    }

    /**
     * Store a new user, auto-log in, and redirect.
     */
    public function store()
    {
        $rules = [
            'name'             => 'required|min_length[2]|max_length[100]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[6]|max_length[255]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();

        $userData = [
            'name'                => $this->request->getPost('name'),
            'email'               => $this->request->getPost('email'),
            'password'            => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'avatar'              => null,
            'total_quizzes_taken' => 0,
            'is_banned'           => 0,
        ];

        $userModel->insert($userData);
        $userId = $userModel->getInsertID();

        // Auto sign-in
        $session = session();
        $session->set([
            'user_id' => $userId,
            'name'    => $userData['name'],
            'email'   => $userData['email'],
        ]);

        return redirect()->to('/user/dashboard')->with('success', 'Registration successful! Welcome to QuizTv.');
    }
}
