<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\AttemptModel;

/**
 * Controller: UserController
 * Admin controller for managing system users, toggling roles, banning, and viewing individual history.
 */
class UserController extends BaseController
{
    /**
     * List all users.
     */
    public function index()
    {
        $userModel = new UserModel();
        $users = $userModel->findAll();

        return view('admin/users/index', [
            'users' => $users,
            'title' => 'Manage Users - QuizTv',
        ]);
    }

    /**
     * View detailed user statistics and their attempt history.
     */
    public function view($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        $attemptModel = new AttemptModel();
        $attempts = $attemptModel->getUserHistory($id);

        return view('admin/users/view', [
            'user'     => $user,
            'attempts' => $attempts,
            'title'    => 'User Profile: ' . $user->name,
        ]);
    }

    /**
     * Toggle the ban state of a user.
     */
    public function toggleBan($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        $newBan = ((int)$user->is_banned === 1) ? 0 : 1;
        $userModel->update($id, ['is_banned' => $newBan]);

        $statusMsg = $newBan ? 'User has been banned.' : 'User has been unbanned.';
        return redirect()->back()->with('success', $statusMsg);
    }
}
