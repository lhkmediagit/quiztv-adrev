<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
$res = $conn->query("SELECT id, title, stages FROM quizzes LIMIT 3");
while ($row = $res->fetch_assoc()) {
    echo "ID: {$row['id']} | Title: {$row['title']}\n";
    echo "Stages: {$row['stages']}\n\n";
}
$conn->close();
