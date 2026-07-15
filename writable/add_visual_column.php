<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "ALTER TABLE questions_and_options ADD COLUMN visual TEXT DEFAULT NULL AFTER question";
if ($conn->query($sql) === TRUE) {
    echo "Column 'visual' added successfully.\n";
} else {
    echo "Error adding column: " . $conn->error . "\n";
}

$conn->close();
