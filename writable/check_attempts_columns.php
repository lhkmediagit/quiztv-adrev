<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$res = $conn->query("SHOW COLUMNS FROM quiz_attempts");
while ($row = $res->fetch_assoc()) {
    echo "Field: {$row['Field']} | Type: {$row['Type']} | Null: {$row['Null']}\n";
}
$conn->close();
