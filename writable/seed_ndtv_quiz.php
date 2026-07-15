<?php

/**
 * QuizTv - NDTV Quiz Seeder
 * Reads local full_questions.json and seeds it into the quiztv database.
 */

$jsonFile = 'C:\\Users\\admin\\.gemini\\antigravity-ide\\brain\\6ab0c573-a144-4f95-8b0d-aae051bd0d82\\scratch\\quiz_questions_full.json';
if (!file_exists($jsonFile)) {
    die("JSON file not found: $jsonFile\n");
}

$conn = new mysqli("localhost", "root", "", "quiztv");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error . "\n");
}
$conn->set_charset("utf8mb4");

// 1. Process Category
$categoryName = 'Logic & Critical Thinking';
$categorySlug = 'logic-critical-thinking';

$stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
$stmt->bind_param("s", $categoryName);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $catRow = $res->fetch_assoc();
    $categoryId = $catRow['id'];
} else {
    $now = date('Y-m-d H:i:s');
    $stmtInsert = $conn->prepare("INSERT INTO categories (name, slug, created_at, updated_at) VALUES (?, ?, ?, ?)");
    $stmtInsert->bind_param("ssss", $categoryName, $categorySlug, $now, $now);
    $stmtInsert->execute();
    $categoryId = $stmtInsert->insert_id;
    echo "Created Category: {$categoryName} (ID: {$categoryId})\n";
}

// 2. Delete existing quiz if exists to allow re-runs
$quizSlug = 'quiztv';
$resQuiz = $conn->query("SELECT id FROM quizzes WHERE slug = '$quizSlug'");
if ($resQuiz->num_rows > 0) {
    $oldQuiz = $resQuiz->fetch_assoc();
    $oldQuizId = $oldQuiz['id'];
    $conn->query("DELETE FROM user_answers WHERE question_id IN (SELECT id FROM questions_and_options WHERE quiz_id = $oldQuizId)");
    $conn->query("DELETE FROM questions_and_options WHERE quiz_id = $oldQuizId");
    $conn->query("DELETE FROM quiz_attempts WHERE quiz_id = $oldQuizId");
    $conn->query("DELETE FROM quizzes WHERE id = $oldQuizId");
    echo "Removed existing duplicate quiz and questions for slug: $quizSlug\n";
}

// 3. Process Quiz Details
$title = 'Can You Catch the Trick?';
$description = 'Some questions look simple—until you realize they’re not asking what you thought they were. It’s not about what you know, but how carefully you think. Let’s see how many tricks you can catch.';
$thumbnail = 'https://cloud.appwrite.io/v1/storage/buckets/65969bd3b8e2a0b364e1/files/69e1ece4000ae266b0a3/preview?project=659526d9b73971c0b8b3';
$passRate = 70.00;
$totalAttempts = 15420;
$duration = 15;
$difficulty = 'medium';
$stages = json_encode([
    "First Intentions",
    "Double Takes",
    "Mind Benders",
    "Wordplay Tricks",
    "Visual Illusions",
    "Critical Logic",
    "Unconventional Thinking",
    "The Final Paradox"
]);
$isActive = 1;
$createdBy = 1;
$now = date('Y-m-d H:i:s');

$stmtQuiz = $conn->prepare("INSERT INTO quizzes (slug, title, description, category_id, thumbnail, pass_rate, total_attempts, duration_minutes, difficulty, stages, is_active, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmtQuiz->bind_param("sssisdiissiiis", $quizSlug, $title, $description, $categoryId, $thumbnail, $passRate, $totalAttempts, $duration, $difficulty, $stages, $isActive, $createdBy, $now, $now);
$stmtQuiz->execute();
$newQuizId = $stmtQuiz->insert_id;

echo "Created Quiz: {$title} (ID: {$newQuizId})\n";

// 4. Load Questions from JSON
$jsonContent = file_get_contents($jsonFile);
$questionsData = json_decode($jsonContent, true);
$questions = $questionsData['documents'] ?? [];

echo "Found " . count($questions) . " questions in JSON. Inserting...\n";

$insertedCount = 0;
foreach ($questions as $idx => $q) {
    $prompt = trim($q['question'] ?? '');
    $explanation = trim($q['answerParagraph'] ?? '');
    
    // Construct Visual HTML
    $imageUrl = trim($q['imageUrl'] ?? '');
    $visual = null;
    if (!empty($imageUrl)) {
        $visual = '<img class="legacy-question-image" src="' . htmlspecialchars($imageUrl) . '" alt="Visual Clue" />';
    }
    
    // Parse Options JSON
    $optionsRaw = $q['options'] ?? '[]';
    $optionsArr = json_decode($optionsRaw, true);
    if (is_string($optionsArr)) {
        $optionsArr = json_decode($optionsArr, true);
    }
    
    $option1 = trim($optionsArr[0] ?? 'N/A');
    $option2 = trim($optionsArr[1] ?? 'N/A');
    $option3 = trim($optionsArr[2] ?? '');
    $option4 = trim($optionsArr[3] ?? '');
    
    // Find correct option index (1-based)
    $correctOption = 1;
    foreach ($optionsArr as $oIdx => $opt) {
        if (trim($opt) === trim($q['correctAnswer'])) {
            $correctOption = $oIdx + 1;
            break;
        }
    }
    
    $roundNumber = floor($idx / 10) + 1;
    $orderIndex = $idx + 1;
    
    $stmtQ = $conn->prepare("INSERT INTO questions_and_options (quiz_id, round_number, question, visual, explanation, option1, option2, option3, option4, correct_option, order_index, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtQ->bind_param("iisssssssiiss", $newQuizId, $roundNumber, $prompt, $visual, $explanation, $option1, $option2, $option3, $option4, $correctOption, $orderIndex, $now, $now);
    $stmtQ->execute();
    $insertedCount++;
}

echo "Successfully seeded $insertedCount questions to the database.\n";
$conn->close();
