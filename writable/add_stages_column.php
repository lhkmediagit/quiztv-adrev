<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "ALTER TABLE quizzes ADD COLUMN stages TEXT DEFAULT NULL AFTER difficulty";
if ($conn->query($sql) === TRUE) {
    echo "Column 'stages' added successfully to 'quizzes'.\n";
} else {
    echo "Error adding column: " . $conn->error . "\n";
}

$conn->close();
