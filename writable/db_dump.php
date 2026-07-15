<?php

$conn = new mysqli("localhost", "root", "", "quiztv");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== CATEGORIES ===\n";
$res = $conn->query("SELECT * FROM categories");
while ($row = $res->fetch_assoc()) {
    echo "ID: {$row['id']} | Name: {$row['name']} | Slug: {$row['slug']}\n";
}

echo "\n=== QUIZZES ===\n";
$res = $conn->query("SELECT * FROM quizzes");
while ($row = $res->fetch_assoc()) {
    echo "ID: {$row['id']} | Title: {$row['title']} | Slug: {$row['slug']} | Category: {$row['category_id']}\n";
}

echo "\n=== QUESTIONS COUNT ===\n";
$res = $conn->query("SELECT COUNT(*) as count FROM questions_and_options");
$row = $res->fetch_assoc();
echo "Total Questions: {$row['count']}\n";

$res = $conn->query("SELECT quiz_id, COUNT(*) as count FROM questions_and_options GROUP BY quiz_id");
while ($row = $res->fetch_assoc()) {
    echo "Quiz ID: {$row['quiz_id']} | Questions: {$row['count']}\n";
}

$conn->close();
