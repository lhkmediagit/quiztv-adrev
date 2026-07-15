<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

/**
 * Controller: LoginController
 * Handles login rendering, credential matching, banned status checks, and session termination.
 */
class LoginController extends BaseController
{
    public function index()
    {
        if (session()->has('admin_id')) {
            return redirect()->to('/admin');
        }
        if (session()->has('user_id')) {
            return redirect()->to('/user/dashboard');
        }
        return view('auth/login', ['title' => 'Login - QuizTv']);
    }

    /**
     * Process authentication credentials.
     */
    public function authenticate()
    {
        $session = session();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Check admins table first
        $adminModel = new \App\Models\AdminModel();
        $admin = $adminModel->where('email', $email)->first();

        if ($admin) {
            if (password_verify($password, $admin->password)) {
                $session->set([
                    'admin_id' => $admin->id,
                    'name'     => $admin->name,
                    'email'    => $admin->email,
                ]);
                return redirect()->to('/admin');
            }
        }

        // Check users table
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if ($user) {
            if (password_verify($password, $user->password)) {
                if ((int)$user->is_banned === 1) {
                    return redirect()->back()->with('error', 'Your account has been banned by an administrator.')->withInput();
                }

                // Set session data
                $session->set([
                    'user_id' => $user->id,
                    'name'    => $user->name,
                    'email'   => $user->email,
                ]);
                return redirect()->to('/user/dashboard');
            }
        }

        return redirect()->back()->with('error', 'Invalid email or password.')->withInput();
    }

    /**
     * Logout and destroy session data.
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
