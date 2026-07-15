<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "ALTER TABLE quiz_attempts 
        ADD COLUMN lead_name VARCHAR(100) NULL AFTER guest_token, 
        ADD COLUMN lead_email VARCHAR(100) NULL AFTER lead_name, 
        ADD COLUMN lead_phone VARCHAR(20) NULL AFTER lead_email";

if ($conn->query($sql) === TRUE) {
    echo "Columns lead_name, lead_email, lead_phone added successfully to quiz_attempts table.\n";
} else {
    echo "Error adding columns: " . $conn->error . "\n";
}

$conn->close();
