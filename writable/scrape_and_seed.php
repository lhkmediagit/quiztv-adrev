<?php

/**
 * QuizTv Live Scraper and Database Seeder
 * Crawls therainbowhub.com to fetch live categories, quizzes, and questions,
 * and seeds them into the local MySQL database.
 */

// Define slugs to scrape
$slugs = [
    'medicine-test',
    'medicine',
    'navy',
    'airforce',
    'connection',
    'memory',
    'iq',
    'tools',
    'vision',
    'zodiac',
    'grammar',
    'history'
];

$conn = new mysqli("localhost", "root", "", "quiztv");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error . "\n");
}

// Enable UTF-8
$conn->set_charset("utf8mb4");

// 1. Truncate existing data tables to avoid duplicates
echo "Truncating database tables...\n";
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("TRUNCATE TABLE quiz_attempts");
$conn->query("TRUNCATE TABLE user_answers");
$conn->query("TRUNCATE TABLE questions_and_options");
$conn->query("TRUNCATE TABLE quizzes");
$conn->query("TRUNCATE TABLE categories");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// Helper function to slugify text
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

// Cache of categories: [name => id]
$categoryCache = [];

foreach ($slugs as $slug) {
    echo "\n----------------------------------------\n";
    echo "Scraping quiz: {$slug}...\n";

    $url = "https://therainbowhub.com/" . $slug;
    
    // Fetch page content
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$html) {
        echo "WARNING: Failed to fetch {$url} (HTTP Code: {$httpCode})\n";
        continue;
    }

    // Extract JS config object
    if (!preg_match('/var config = (\{.+?\});\s*(?:var|correct|function)/s', $html, $matches)) {
        echo "WARNING: Could not parse config JSON on page {$slug}\n";
        continue;
    }

    $jsonStr = $matches[1];
    $config = json_decode($jsonStr, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "WARNING: JSON decoding failed for {$slug}: " . json_last_error_msg() . "\n";
        continue;
    }

    $quizData = $config['quiz'] ?? null;
    if (!$quizData) {
        echo "WARNING: No quiz metadata block in config for {$slug}\n";
        continue;
    }

    // 1. Process Category
    $categoryName = trim($quizData['eyebrow'] ?? 'General Knowledge');
    if (empty($categoryName)) {
        $categoryName = 'General Knowledge';
    }

    if (!isset($categoryCache[$categoryName])) {
        $catSlug = slugify($categoryName);
        // Check if category exists in DB (just in case)
        $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->bind_param("s", $categoryName);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) {
            $catRow = $res->fetch_assoc();
            $categoryCache[$categoryName] = $catRow['id'];
        } else {
            $now = date('Y-m-d H:i:s');
            $stmtInsert = $conn->prepare("INSERT INTO categories (name, slug, created_at, updated_at) VALUES (?, ?, ?, ?)");
            $stmtInsert->bind_param("ssss", $categoryName, $catSlug, $now, $now);
            $stmtInsert->execute();
            $categoryCache[$categoryName] = $stmtInsert->insert_id;
            echo "Created Category: {$categoryName} (ID: {$categoryCache[$categoryName]})\n";
        }
    }
    $categoryId = $categoryCache[$categoryName];

    // 2. Process Quiz Details
    $title = trim($quizData['title'] ?? $quizData['seoTitle'] ?? 'Quiz');
    $description = trim($quizData['summary'] ?? $quizData['seoDescription'] ?? 'Take the quiz.');
    
    // Map absolute thumbnail path so it loads from CDN
    $thumbnail = $quizData['homepage']['thumbnailUrl'] ?? '';
    if (!empty($thumbnail) && !str_starts_with($thumbnail, 'http')) {
        $thumbnail = "https://therainbowhub.com" . $thumbnail;
    }

    // Clean pass rate (decimal)
    $passRateRaw = $quizData['passRate'] ?? '50%';
    $passRate = (float) preg_replace('/[^0-9.]/', '', $passRateRaw);
    if ($passRate <= 0) {
        $passRate = 50.00; // Default
    }

    // Clean duration
    $durationRaw = $quizData['duration'] ?? '5';
    $duration = (int) preg_replace('/[^0-9]/', '', $durationRaw);
    if ($duration <= 0) {
        $duration = 5; // Default
    }

    // Map difficulty
    $difficultyRaw = strtolower($quizData['difficulty'] ?? 'medium');
    $difficulty = 'medium';
    if ($difficultyRaw === 'quick' || $difficultyRaw === 'easy') {
        $difficulty = 'easy';
    } elseif ($difficultyRaw === 'medium') {
        $difficulty = 'medium';
    } elseif ($difficultyRaw === 'hard' || $difficultyRaw === 'expert') {
        $difficulty = 'hard';
    }

    // Seed stats
    $totalAttempts = 5000 + rand(1000, 20000); // Generate realistic attempt count

    // Parse stages array
    $stagesJson = json_encode($quizData['stages'] ?? []);

    $now = date('Y-m-d H:i:s');
    $createdBy = 1; // Admin user ID
    $isActive = 1;

    $stmtQuiz = $conn->prepare("INSERT INTO quizzes (slug, title, description, category_id, thumbnail, pass_rate, total_attempts, duration_minutes, difficulty, stages, is_active, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtQuiz->bind_param("sssisdiissiiis", $slug, $title, $description, $categoryId, $thumbnail, $passRate, $totalAttempts, $duration, $difficulty, $stagesJson, $isActive, $createdBy, $now, $now);
    $stmtQuiz->execute();
    $quizId = $stmtQuiz->insert_id;

    echo "Created Quiz: {$title} (ID: {$quizId}, Category: {$categoryName})\n";

    // 3. Process Questions
    $questions = $config['quiz']['questions'] ?? $config['questions'] ?? [];
    echo "Scraped " . count($questions) . " questions. Inserting...\n";

    $qCount = 0;
    foreach ($questions as $index => $q) {
        $prompt = trim($q['prompt'] ?? '');
        $explanation = trim($q['explanation'] ?? '');
        
        $visual = trim($q['visual'] ?? '');
        if (!empty($visual)) {
            // Replace relative URLs to absolute CDN URLs
            $visual = str_replace('src="/quizzes/', 'src="https://therainbowhub.com/quizzes/', $visual);
            $visual = str_replace('src="/images/', 'src="https://therainbowhub.com/images/', $visual);
        } else {
            $visual = null;
        }
        
        $choices = $q['choices'] ?? [];
        $option1 = trim($choices[0] ?? 'N/A');
        $option2 = trim($choices[1] ?? 'N/A');
        $option3 = trim($choices[2] ?? '');
        $option4 = trim($choices[3] ?? '');

        // 1-indexed correct option
        $correctOption = (int)($q['answerIndex'] ?? 0) + 1;
        if ($correctOption < 1 || $correctOption > 4) {
            $correctOption = 1;
        }

        // Round / stage number (1-indexed)
        $roundNumber = (int)($q['stage'] ?? 0) + 1;

        $orderIndex = $index + 1;

        $stmtQ = $conn->prepare("INSERT INTO questions_and_options (quiz_id, round_number, question, visual, explanation, option1, option2, option3, option4, correct_option, order_index, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtQ->bind_param("iisssssssiiis", $quizId, $roundNumber, $prompt, $visual, $explanation, $option1, $option2, $option3, $option4, $correctOption, $orderIndex, $now, $now);
        $stmtQ->execute();
        $qCount++;
    }

    echo "Successfully seeded {$qCount} questions for quiz: {$slug}.\n";
}

$conn->close();
echo "\n========================================\n";
echo "Scrape and Seeding finished successfully!\n";
echo "========================================\n";
