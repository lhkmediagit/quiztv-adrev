<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
$res = $conn->query("SELECT id, quiz_id, question, visual FROM questions_and_options WHERE quiz_id = 8 LIMIT 3");
while ($row = $res->fetch_assoc()) {
    echo "ID: {$row['id']} | Q: " . substr($row['question'], 0, 35) . "\n";
    echo "Visual: " . ($row['visual'] ?: 'NULL') . "\n\n";
}
$conn->close();
