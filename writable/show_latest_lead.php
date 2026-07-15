<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, quiz_id, lead_name, lead_email, lead_phone, score, total_questions, completed_at 
        FROM quiz_attempts 
        ORDER BY id DESC 
        LIMIT 3";

$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    echo "=== LATEST QUIZ ATTEMPTS & LEADS ===\n";
    while ($row = $res->fetch_assoc()) {
        echo "Attempt ID: {$row['id']}\n";
        echo "Quiz ID: {$row['quiz_id']}\n";
        echo "Name: " . ($row['lead_name'] ?: "[Empty]") . "\n";
        echo "Email: " . ($row['lead_email'] ?: "[Empty]") . "\n";
        echo "Phone: " . ($row['lead_phone'] ?: "[Empty]") . "\n";
        echo "Score: {$row['score']} / {$row['total_questions']}\n";
        echo "Completed At: " . ($row['completed_at'] ?: "Not Completed") . "\n";
        echo "----------------------------------------\n";
    }
} else {
    echo "No quiz attempts found in database.\n";
}

$conn->close();
