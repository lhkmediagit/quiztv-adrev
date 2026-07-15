<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
$res = $conn->query("SELECT id, quiz_id, order_index, question FROM questions_and_options WHERE quiz_id = 1 LIMIT 5");
while ($row = $res->fetch_assoc()) {
    echo "ID: {$row['id']} | Quiz: {$row['quiz_id']} | OrderIndex: {$row['order_index']} | Q: " . substr($row['question'], 0, 30) . "\n";
}
$conn->close();
