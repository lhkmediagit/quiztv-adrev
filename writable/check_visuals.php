<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
$res = $conn->query("SELECT id, quiz_id, question, visual FROM questions_and_options WHERE visual IS NOT NULL AND visual != '' LIMIT 5");
while ($row = $res->fetch_assoc()) {
    echo "ID: {$row['id']} | Quiz ID: {$row['quiz_id']} | Question: {$row['question']}\n";
    echo "Visual: " . htmlspecialchars($row['visual']) . "\n\n";
}
$conn->close();
