<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

// Add about_html column to quizzes table
$sql = "ALTER TABLE quizzes ADD COLUMN about_html TEXT NULL AFTER stages";
if ($conn->query($sql) === TRUE) {
    echo "Column 'about_html' added successfully.\n";
} else {
    echo "Error adding column: " . $conn->error . "\n";
}
$conn->close();
