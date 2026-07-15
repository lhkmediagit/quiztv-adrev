<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Controller: PlayerController
 * Manages player/lead information collected during quiz completion.
 * Allows viewing player details (Name, Email, Phone) and exporting to CSV.
 */
class PlayerController extends BaseController
{
    /**
     * Display a listing of the players / leads captured.
     */
    public function index()
    {
        $db = \Config\Database::connect();
        
        $players = $db->table('quiz_attempts')
            ->select('quiz_attempts.id, quiz_attempts.lead_name, quiz_attempts.lead_email, quiz_attempts.lead_phone, quiz_attempts.created_at, quizzes.title as quiz_title')
            ->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id', 'left')
            ->where('quiz_attempts.lead_name IS NOT NULL')
            ->where('quiz_attempts.lead_name !=', '')
            ->orderBy('quiz_attempts.created_at', 'DESC')
            ->get()
            ->getResult();

        return view('admin/players/index', [
            'players' => $players,
            'title'   => 'Player Information - QuizTv',
        ]);
    }

    /**
     * Download the player / lead information list as a CSV file.
     */
    public function download()
    {
        $db = \Config\Database::connect();
        
        $players = $db->table('quiz_attempts')
            ->select('quiz_attempts.lead_name, quiz_attempts.lead_email, quiz_attempts.lead_phone, quiz_attempts.created_at, quizzes.title as quiz_title')
            ->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id', 'left')
            ->where('quiz_attempts.lead_name IS NOT NULL')
            ->where('quiz_attempts.lead_name !=', '')
            ->orderBy('quiz_attempts.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $stream = fopen('php://temp', 'w+');
        
        // Write UTF-8 BOM for Excel compatibility
        fprintf($stream, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Write CSV Header
        fputcsv($stream, ['Name', 'Email Address', 'Phone Number', 'Quiz Attempted', 'Submitted At']);
        
        // Write CSV Rows
        foreach ($players as $row) {
            fputcsv($stream, [
                $row['lead_name'],
                $row['lead_email'],
                $row['lead_phone'],
                $row['quiz_title'] ?? 'Deleted Quiz',
                $row['created_at']
            ]);
        }
        
        rewind($stream);
        $csvData = stream_get_contents($stream);
        fclose($stream);

        $filename = 'player_information_' . date('Ymd_His') . '.csv';
        return $this->response->download($filename, $csvData);
    }
}
