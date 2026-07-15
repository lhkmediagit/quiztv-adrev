<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

/**
 * Controller: CategoryController
 * Admin controller managing quiz categories, including cascading deletion of linked quizzes.
 */
class CategoryController extends BaseController
{
    /**
     * List all categories and display the count of quizzes inside each.
     */
    public function index()
    {
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->findAll();

        $db = \Config\Database::connect();
        foreach ($categories as $cat) {
            $cat->quiz_count = $db->table('quizzes')->where('category_id', $cat->id)->countAllResults();
        }

        return view('admin/categories/index', [
            'categories' => $categories,
            'title'      => 'Manage Categories - QuizTv',
        ]);
    }

    /**
     * Show form to create a new category.
     */
    public function create()
    {
        return view('admin/categories/create', [
            'title' => 'Create Category - QuizTv',
        ]);
    }

    /**
     * Store new category. Generates custom unique slug.
     */
    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $categoryModel = new CategoryModel();

        $name = $this->request->getPost('name');
        $slug = url_title($name, '-', true);
        
        // Ensure slug is unique
        $slugCount = $categoryModel->where('slug', $slug)->countAllResults();
        if ($slugCount > 0) {
            $slug = $slug . '-' . time();
        }

        $data = [
            'name' => $name,
            'slug' => $slug,
        ];

        $categoryModel->insert($data);

        return redirect()->to('/admin/categories')->with('success', 'Category created successfully.');
    }

    /**
     * Show edit form for category.
     */
    public function edit($id)
    {
        $categoryModel = new CategoryModel();
        $category = $categoryModel->find($id);
        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Category not found.');
        }

        return view('admin/categories/edit', [
            'category' => $category,
            'title'    => 'Edit Category: ' . $category->name,
        ]);
    }

    /**
     * Process updates to name and icon.
     */
    public function update($id)
    {
        $categoryModel = new CategoryModel();
        $category = $categoryModel->find($id);
        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Category not found.');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
        ];

        $categoryModel->update($id, $data);

        return redirect()->to('/admin/categories')->with('success', 'Category updated successfully.');
    }

    /**
     * Delete category and perform manual cascading deletions of all quizzes, questions, and attempts inside.
     */
    public function delete($id)
    {
        $categoryModel = new CategoryModel();
        $category = $categoryModel->find($id);
        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Category not found.');
        }

        $db = \Config\Database::connect();
        
        // Fetch all quizzes in this category to delete questions and attempts
        $quizzes = $db->table('quizzes')->where('category_id', $id)->get()->getResult();
        foreach ($quizzes as $quiz) {
            // Delete questions
            $db->table('questions_and_options')->where('quiz_id', $quiz->id)->delete();
            // Delete recommended links
            $db->table('recommended_quizzes')->where('quiz_id', $quiz->id)->orWhere('recommended_quiz_id', $quiz->id)->delete();
            // Delete attempts and user answers
            $attempts = $db->table('quiz_attempts')->select('id')->where('quiz_id', $quiz->id)->get()->getResult();
            foreach ($attempts as $attempt) {
                $db->table('user_answers')->where('attempt_id', $attempt->id)->delete();
            }
            $db->table('quiz_attempts')->where('quiz_id', $quiz->id)->delete();
            
            // Delete thumbnail file
            if ($quiz->thumbnail) {
                $oldFilename = basename($quiz->thumbnail);
                if (file_exists(FCPATH . 'uploads/quizzes/' . $oldFilename)) {
                    @unlink(FCPATH . 'uploads/quizzes/' . $oldFilename);
                }
            }
        }

        // Delete quizzes in this category
        $db->table('quizzes')->where('category_id', $id)->delete();

        // Finally, delete the category record
        $categoryModel->delete($id);

        return redirect()->to('/admin/categories')->with('success', 'Category and all its associated quizzes, questions, and play statistics deleted.');
    }
}
