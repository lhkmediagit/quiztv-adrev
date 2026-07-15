<?php
/**
 * Scrapes the about page info panels from therainbowhub.com for all quizzes
 * and saves them into a local JSON cache.
 */

$slugs = [
    'medicine-test',
    'medicine',
    'navy',
    'airforce',
    'connection',
    'memory',
    'iq',
    'tools',
    'vision',
    'zodiac',
    'grammar',
    'history'
];

$results = [];

foreach ($slugs as $slug) {
    echo "Crawling about section for: {$slug}...\n";
    $url = "https://therainbowhub.com/" . $slug;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $html = curl_exec($ch);
    curl_close($ch);
    
    if (!$html) {
        echo "Failed to fetch {$slug}\n";
        continue;
    }
    
    // Extract the quiz-info-panel section
    // We search for `quiz-info-panel` and get its full outerHTML block matching start tag
    $startPos = strpos($html, 'quiz-info-panel');
    if ($startPos !== false) {
        // Go back to find the opening tag (could be <section or <div)
        $tagStart = strrpos(substr($html, 0, $startPos), '<');
        if ($tagStart !== false) {
            $tagOpen = substr($html, $tagStart, 8); // e.g. <section or <div
            $tagName = str_contains($tagOpen, 'section') ? 'section' : 'div';
            
            // Match tag balance to find end of element
            $depth = 1;
            $currPos = $tagStart + strlen('<' . $tagName);
            while ($depth > 0 && $currPos < strlen($html)) {
                $nextOpen = strpos($html, '<' . $tagName, $currPos);
                $nextClose = strpos($html, '</' . $tagName, $currPos);
                
                if ($nextClose === false) break;
                
                if ($nextOpen !== false && $nextOpen < $nextClose) {
                    $depth++;
                    $currPos = $nextOpen + strlen('<' . $tagName);
                } else {
                    $depth--;
                    $currPos = $nextClose + strlen('</' . $tagName . '>');
                }
            }
            
            $aboutHtml = substr($html, $tagStart, $currPos - $tagStart);
            $results[$slug] = trim($aboutHtml);
            echo "Successfully parsed about section for {$slug}\n";
        }
    } else {
        // Try fallback selector "About This"
        $pos = strpos($html, "About This");
        if ($pos !== false) {
            $snippet = substr($html, $pos - 1000, 3000);
            if (preg_match('/<div class="(quiz-info-panel[^"]*)"/i', $snippet, $m)) {
                $startPos = strpos($html, '<div class="' . $m[1] . '"');
                if ($startPos !== false) {
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
                    $aboutHtml = substr($html, $startPos, $currPos - $startPos);
                    $results[$slug] = trim($aboutHtml);
                    echo "Successfully parsed about section for {$slug} via fallback\n";
                }
            }
        }
    }
}

file_put_contents('writable/quiz_about_details.json', json_encode($results, JSON_PRETTY_PRINT));
echo "Finished scraping about sections. JSON written to writable/quiz_about_details.json\n";
