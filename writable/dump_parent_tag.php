<?php
$url = "https://therainbowhub.com/airforce";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
$html = curl_exec($ch);
curl_close($ch);

$pos = strpos($html, "quiz-info-panel__intro");
if ($pos !== false) {
    // Print 300 characters preceding the class to see parent tags
    echo "Preceding HTML:\n";
    echo htmlspecialchars(substr($html, $pos - 300, 350)) . "\n";
}
