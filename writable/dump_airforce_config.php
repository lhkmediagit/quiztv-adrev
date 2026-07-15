<?php
$url = "https://therainbowhub.com/airforce";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
$html = curl_exec($ch);
curl_close($ch);

if (preg_match('/var config = (\{.+?\});\s*(?:var|correct|function)/s', $html, $matches)) {
    $config = json_decode($matches[1], true);
    $quiz = $config['quiz'] ?? [];
    
    echo "Quiz Keys:\n";
    print_r(array_keys($quiz));
    
    echo "\nAbout Section Content:\n";
    if (isset($quiz['about'])) {
        print_r($quiz['about']);
    } else {
        echo "No 'about' key found in config JSON.\n";
    }
} else {
    echo "Failed to parse config!\n";
}
