<?php
$url = "https://therainbowhub.com/airforce";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
$html = curl_exec($ch);
curl_close($ch);

// Search for class name of the card containing "About This"
// Looking backward from the "About This" text to find the nearest container tag
$pos = strpos($html, "About This");
if ($pos !== false) {
    // Find the enclosing <div class="quiz-info-panel">
    $snippet = substr($html, $pos - 1500, 3000);
    if (preg_match('/<div class="(quiz-info-panel[^"]*)"/i', $snippet, $m)) {
        echo "Found container class: {$m[1]}\n";
        // Let's capture from that <div class="quiz-info-panel..."> to its matching close
        $startPos = strpos($html, '<div class="' . $m[1] . '"');
        if ($startPos !== false) {
            // Find end tag by counting div depth
            $depth = 1;
            $currPos = $startPos + strlen('<div class="' . $m[1] . '"');
            while ($depth > 0 && $currPos < strlen($html)) {
                $nextDiv = strpos($html, '<div', $currPos);
                $nextClose = strpos($html, '</div', $currPos);
                
                if ($nextClose === false) break;
                
                if ($nextDiv !== false && $nextDiv < $nextClose) {
                    $depth++;
                    $currPos = $nextDiv + 4;
                } else {
                    $depth--;
                    $currPos = $nextClose + 5;
                }
            }
            
            $aboutBlock = substr($html, $startPos, $currPos - $startPos);
            echo "\nFull About Block HTML:\n";
            echo htmlspecialchars($aboutBlock) . "\n";
        }
    }
}
