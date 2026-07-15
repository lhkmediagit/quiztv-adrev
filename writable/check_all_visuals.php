<?php
$conn = new mysqli("localhost", "root", "", "quiztv");
$res = $conn->query("SELECT q.slug, COUNT(*) as total, SUM(CASE WHEN qo.visual IS NOT NULL AND qo.visual != '' THEN 1 ELSE 0 END) as with_visual FROM quizzes q JOIN questions_and_options qo ON q.id = qo.quiz_id GROUP BY q.slug");
while ($row = $res->fetch_assoc()) {
    echo "Slug: {$row['slug']} | Total: {$row['total']} | With Visual: {$row['with_visual']}\n";
}
$conn->close();
