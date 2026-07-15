<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
$res = $conn->query("SELECT q.slug, qo.question, qo.visual FROM questions_and_options qo JOIN quizzes q ON q.id = qo.quiz_id WHERE q.slug IN ('tools', 'vision', 'zodiac') LIMIT 3");
while ($row = $res->fetch_assoc()) {
    echo "Slug: {$row['slug']} | Question: {$row['question']}\n";
    echo "Visual: " . htmlspecialchars($row['visual']) . "\n\n";
}
$conn->close();
