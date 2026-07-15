<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
$res = $conn->query("SELECT id, title FROM quizzes");
while ($row = $res->fetch_assoc()) {
    $quizId = $row['id'];
    $title = $row['title'];
    
    // Check total questions
    $countRes = $conn->query("SELECT COUNT(*) as count FROM questions_and_options WHERE quiz_id = $quizId");
    $countRow = $countRes->fetch_assoc();
    $total = $countRow['count'];
    
    // Check if order_index = 1 exists
    $firstRes = $conn->query("SELECT COUNT(*) as count FROM questions_and_options WHERE quiz_id = $quizId AND order_index = 1");
    $firstRow = $firstRes->fetch_assoc();
    $hasFirst = $firstRow['count'] > 0 ? "YES" : "NO";
    
    echo "Quiz ID: $quizId | Total Q: $total | Has OrderIndex=1: $hasFirst | Title: $title\n";
}
$conn->close();
