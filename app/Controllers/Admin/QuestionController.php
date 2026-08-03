<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuestionOptionModel;
use App\Models\QuizModel;

/**
 * Controller: QuestionController
 * Admin controller managing questions and options for a quiz, including CSV bulk upload and cascading removals.
 */
class QuestionController extends BaseController
{
    /**
     * List all questions of a quiz.
     */
    public function index($quiz_id)
    {
        $quizModel = new QuizModel();
        $quiz = $quizModel->find($quiz_id);
        if (!$quiz) {
            return redirect()->to('/admin/quizzes')->with('error', 'Quiz not found.');
        }

        $questionModel = new QuestionOptionModel();
        $questions = $questionModel->getByQuizId($quiz_id);

        return view('admin/questions/index', [
            'quiz'      => $quiz,
            'questions' => $questions,
            'title'     => 'Manage Questions - ' . $quiz->title,
        ]);
    }

    /**
     * Render form to add a question. Suggestions next order index.
     */
    public function create($quiz_id)
    {
        $quizModel = new QuizModel();
        $quiz = $quizModel->find($quiz_id);
        if (!$quiz) {
            return redirect()->to('/admin/quizzes')->with('error', 'Quiz not found.');
        }

        $questionModel = new QuestionOptionModel();
        // Calculate the next logical order index
        $maxOrder = $questionModel->where('quiz_id', $quiz_id)->selectMax('order_index')->first();
        $nextOrder = ($maxOrder && $maxOrder->order_index !== null) ? (int)$maxOrder->order_index + 1 : 1;

        return view('admin/questions/create', [
            'quiz'      => $quiz,
            'nextOrder' => $nextOrder,
            'title'     => 'Add Question - ' . $quiz->title,
        ]);
    }

    /**
     * Store new question record.
     */
    public function store()
    {
        $quizId = $this->request->getPost('quiz_id');

        $rules = [
            'quiz_id'        => 'required|integer',
            'round_number'   => 'permit_empty|integer',
            'question'       => 'required',
            'explanation'    => 'permit_empty',
            'option1'        => 'required|max_length[500]',
            'option2'        => 'required|max_length[500]',
            'option3'        => 'required|max_length[500]',
            'option4'        => 'required|max_length[500]',
            'correct_option' => 'required|integer|in_list[1,2,3,4]',
            'order_index'    => 'required|integer',
            'image_url'      => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $questionModel = new QuestionOptionModel();

        $data = [
            'quiz_id'        => (int)$this->request->getPost('quiz_id'),
            'round_number'   => (int)($this->request->getPost('round_number') ?: 1),
            'question'       => $this->request->getPost('question'),
            'explanation'    => $this->request->getPost('explanation'),
            'option1'        => $this->request->getPost('option1'),
            'option2'        => $this->request->getPost('option2'),
            'option3'        => $this->request->getPost('option3'),
            'option4'        => $this->request->getPost('option4'),
            'correct_option' => (int)$this->request->getPost('correct_option'),
            'order_index'    => (int)$this->request->getPost('order_index'),
        ];

        // Handle visual image file upload OR image URL input
        $visualFile = $this->request->getFile('visual');
        $imageUrlInput = $this->request->getPost('image_url');

        if ($visualFile && $visualFile->isValid() && !$visualFile->hasMoved()) {
            $visualUrl = upload_to_docservice($visualFile, 'quizhive/questions');
            if ($visualUrl === null) {
                return redirect()->back()->withInput()->with('errors', ['visual' => 'Failed to upload visual image to Document Service.']);
            }
            $data['visual'] = '<img class="legacy-question-image" src="' . htmlspecialchars($visualUrl) . '" alt="Visual Clue" />';
        } elseif (!empty($imageUrlInput)) {
            // Store the input image URL as the image tag
            $data['visual'] = '<img class="legacy-question-image" src="' . htmlspecialchars($imageUrlInput) . '" alt="Visual Clue" />';
        }

        $questionModel->insert($data);

        return redirect()->to('/admin/questions/' . $quizId)->with('success', 'Question details saved.');
    }

    /**
     * Edit form view.
     */
    public function edit($id)
    {
        $questionModel = new QuestionOptionModel();
        $question = $questionModel->find($id);
        if (!$question) {
            return redirect()->to('/admin/quizzes')->with('error', 'Question not found.');
        }

        $quizModel = new QuizModel();
        $quiz = $quizModel->find($question->quiz_id);

        return view('admin/questions/edit', [
            'question' => $question,
            'quiz'     => $quiz,
            'title'    => 'Edit Question - QuizTv',
        ]);
    }

    /**
     * Process question edit form submission.
     */
    public function update($id)
    {
        $questionModel = new QuestionOptionModel();
        $question = $questionModel->find($id);
        if (!$question) {
            return redirect()->to('/admin/quizzes')->with('error', 'Question not found.');
        }

        $rules = [
            'round_number'   => 'permit_empty|integer',
            'question'       => 'required',
            'explanation'    => 'permit_empty',
            'option1'        => 'required|max_length[500]',
            'option2'        => 'required|max_length[500]',
            'option3'        => 'required|max_length[500]',
            'option4'        => 'required|max_length[500]',
            'correct_option' => 'required|integer|in_list[1,2,3,4]',
            'order_index'    => 'required|integer',
            'image_url'      => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'round_number'   => (int)($this->request->getPost('round_number') ?: ($question->round_number ?? 1)),
            'question'       => $this->request->getPost('question'),
            'explanation'    => $this->request->getPost('explanation'),
            'option1'        => $this->request->getPost('option1'),
            'option2'        => $this->request->getPost('option2'),
            'option3'        => $this->request->getPost('option3'),
            'option4'        => $this->request->getPost('option4'),
            'correct_option' => (int)$this->request->getPost('correct_option'),
            'order_index'    => (int)$this->request->getPost('order_index'),
        ];

        // Fetch current visual value
        $currentVisual = $question->visual;

        // Check if remove visual is checked
        if ($this->request->getPost('remove_visual') == '1') {
            if ($currentVisual && $currentVisual !== 'none') {
                if (preg_match('/src="([^"]+)"/', $currentVisual, $matches)) {
                    $src = $matches[1];
                    $filename = basename($src);
                    $filePath = FCPATH . 'uploads/questions/' . $filename;
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
            }
            $currentVisual = 'none';
        }

        // Check if restore default is checked
        if ($this->request->getPost('restore_default') == '1') {
            $currentVisual = null;
        }

        // Handle uploaded file OR URL input
        $visualFile = $this->request->getFile('visual');
        $imageUrlInput = $this->request->getPost('image_url');

        // Check if the previous image was an external URL
        $previousWasUrl = false;
        if ($question->visual && $question->visual !== 'none') {
            if (preg_match('/src="([^"]+)"/', $question->visual, $matches)) {
                $src = $matches[1];
                if (strpos($src, 'uploads/questions/') === false) {
                    $previousWasUrl = true;
                }
            }
        }

        if ($visualFile && $visualFile->isValid() && !$visualFile->hasMoved()) {
            $visualUrl = upload_to_docservice($visualFile, 'quizhive/questions');
            if ($visualUrl === null) {
                return redirect()->back()->withInput()->with('errors', ['visual' => 'Failed to upload visual image to Document Service.']);
            }

            // Delete old file locally only if it was local
            if ($question->visual && $question->visual !== 'none') {
                if (preg_match('/src="([^"]+)"/', $question->visual, $matches)) {
                    $url = $matches[1];
                    $isLocal = str_contains($url, base_url('uploads/questions/'));
                    if ($isLocal) {
                        $localPath = str_replace(base_url(), FCPATH, $url);
                        if (is_file($localPath)) {
                            @unlink($localPath);
                        }
                    }
                }
            }

            $currentVisual = '<img class="legacy-question-image" src="' . htmlspecialchars($visualUrl) . '" alt="Visual Clue" />';
        } elseif ($this->request->getPost('remove_visual') != '1') {
            if (!empty($imageUrlInput)) {
                // Delete old local file if there was one
                if ($question->visual) {
                    if (preg_match('/src="([^"]+)"/', $question->visual, $matches)) {
                        $src = $matches[1];
                        if (strpos($src, base_url('uploads/questions/')) !== false) {
                            $filename = basename($src);
                            $filePath = FCPATH . 'uploads/questions/' . $filename;
                            if (file_exists($filePath)) {
                                @unlink($filePath);
                            }
                        }
                    }
                }
                $currentVisual = '<img class="legacy-question-image" src="' . htmlspecialchars($imageUrlInput) . '" alt="Visual Clue" />';
            } elseif ($previousWasUrl) {
                // If they cleared the URL, set to 'none'
                $currentVisual = 'none';
            }
        }

        $data['visual'] = $currentVisual;

        $questionModel->update($id, $data);

        return redirect()->to('/admin/questions/' . $question->quiz_id)->with('success', 'Question details updated.');
    }

    /**
     * Delete question and clean up answers associated with it.
     */
    public function delete($id)
    {
        $questionModel = new QuestionOptionModel();
        $question = $questionModel->find($id);
        if (!$question) {
            return redirect()->to('/admin/quizzes')->with('error', 'Question not found.');
        }

        $quizId = $question->quiz_id;

        // Clean user responses referencing this question
        $db = \Config\Database::connect();
        $db->table('user_answers')->where('question_id', $id)->delete();

        // Delete visual file if it exists
        if ($question->visual) {
            if (preg_match('/src="([^"]+)"/', $question->visual, $matches)) {
                $src = $matches[1];
                $filename = basename($src);
                $filePath = FCPATH . 'uploads/questions/' . $filename;
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
        }

        $questionModel->delete($id);

        return redirect()->to('/admin/questions/' . $quizId)->with('success', 'Question deleted.');
    }

    /**
     * Parse uploaded CSV file and insert rows into questions_and_options table.
     */
    public function csvImport($quiz_id)
    {
        $quizModel = new QuizModel();
        $quiz = $quizModel->find($quiz_id);
        if (!$quiz) {
            return redirect()->to('/admin/quizzes')->with('error', 'Quiz not found.');
        }

        $file = $this->request->getFile('csv_file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Please upload a valid CSV file.');
        }

        $filePath = $file->getTempName();
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return redirect()->back()->with('error', 'Could not open CSV file.');
        }

        $questionModel = new QuestionOptionModel();
        $successCount = 0;
        $rowCount = 0;

        // Read the first row
        $firstRow = fgetcsv($handle);
        if ($firstRow !== false && !empty($firstRow)) {
            // Clear UTF-8 BOM if present
            $firstRow[0] = preg_replace('/[\x{00EF}\x{00BB}\x{00BF}]/u', '', $firstRow[0]);

            // Check if the 7th column (correct_option) is numeric and between 1 and 4.
            // If it is, this is a data row. Otherwise, treat it as a header row and skip it.
            $isHeader = true;
            if (isset($firstRow[6])) {
                $val = trim($firstRow[6]);
                if (is_numeric($val) && (int)$val >= 1 && (int)$val <= 4) {
                    $isHeader = false;
                }
            }

            if (!$isHeader) {
                $rowCount++;
                $question    = trim($firstRow[0]);
                $explanation = trim($firstRow[1]);
                $opt1        = trim($firstRow[2]);
                $opt2        = trim($firstRow[3]);
                $opt3        = trim($firstRow[4]);
                $opt4        = trim($firstRow[5]);
                $correct     = (int)trim($firstRow[6]);
                $round       = (int)trim($firstRow[7]);
                $order       = (int)trim($firstRow[8]);

                if (!empty($question) && !empty($opt1) && !empty($opt2) && !empty($opt3) && !empty($opt4)) {
                    $insertData = [
                        'quiz_id'        => (int)$quiz_id,
                        'round_number'   => $round ?: 1,
                        'question'       => $question,
                        'explanation'    => $explanation,
                        'option1'        => $opt1,
                        'option2'        => $opt2,
                        'option3'        => $opt3,
                        'option4'        => $opt4,
                        'correct_option' => $correct,
                        'order_index'    => $order ?: $rowCount,
                    ];

                    if (isset($firstRow[9])) {
                        $imageUrl = trim($firstRow[9]);
                        if (!empty($imageUrl)) {
                            $insertData['visual'] = '<img class="legacy-question-image" src="' . htmlspecialchars($imageUrl) . '" alt="Visual Clue" />';
                        }
                    }

                    $questionModel->insert($insertData);
                    $successCount++;
                }
            }
        }

        while (($row = fgetcsv($handle)) !== false) {
            $rowCount++;
            if (count($row) < 9) {
                continue; // Skip malformed rows
            }

            $question    = trim($row[0]);
            $explanation = trim($row[1]);
            $opt1        = trim($row[2]);
            $opt2        = trim($row[3]);
            $opt3        = trim($row[4]);
            $opt4        = trim($row[5]);
            $correct     = (int)trim($row[6]);
            $round       = (int)trim($row[7]);
            $order       = (int)trim($row[8]);

            if (empty($question) || empty($opt1) || empty($opt2) || empty($opt3) || empty($opt4) || $correct < 1 || $correct > 4) {
                continue; // Skip row with invalid/missing fields
            }

            $insertData = [
                'quiz_id'        => (int)$quiz_id,
                'round_number'   => $round ?: 1,
                'question'       => $question,
                'explanation'    => $explanation,
                'option1'        => $opt1,
                'option2'        => $opt2,
                'option3'        => $opt3,
                'option4'        => $opt4,
                'correct_option' => $correct,
                'order_index'    => $order ?: $rowCount,
            ];

            if (isset($row[9])) {
                $imageUrl = trim($row[9]);
                if (!empty($imageUrl)) {
                    $insertData['visual'] = '<img class="legacy-question-image" src="' . htmlspecialchars($imageUrl) . '" alt="Visual Clue" />';
                }
            }

            $questionModel->insert($insertData);
            $successCount++;
        }

        fclose($handle);

        return redirect()->to('/admin/questions/' . $quiz_id)->with('success', "CSV Import complete. {$successCount} questions added successfully.");
    }
}
