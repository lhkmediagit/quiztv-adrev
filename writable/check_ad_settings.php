<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
$res = $conn->query("SELECT * FROM ad_settings");
while ($row = $res->fetch_assoc()) {
    echo "Key: {$row['setting_key']} | Val: {$row['setting_value']}\n";
}
$conn->close();
