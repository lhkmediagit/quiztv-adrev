<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM questions_and_options WHERE option2 LIKE '%wristband and chart%' OR question LIKE '%wristband%' LIMIT 5";
$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        echo "ID: " . $row['id'] . "\n";
        echo "Question: " . $row['question'] . "\n";
        echo "Visual: " . ($row['visual'] !== null ? "'" . $row['visual'] . "'" : "NULL") . "\n";
        echo "Explanation: " . $row['explanation'] . "\n";
    }
} else {
    echo "Question not found in the database.\n";
}

$conn->close();
