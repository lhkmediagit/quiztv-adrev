<?php
define('ENVIRONMENT', 'development');
define('FCPATH', __DIR__ . '/../public/');
require __DIR__ . '/../app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$model = new \App\Models\QuizModel();
$quiz = $model->getBySlug('medicine');
if ($quiz) {
    echo "ID: {$quiz->id} | Slug: {$quiz->slug} | Title: {$quiz->title}\n";
} else {
    echo "Quiz not found by slug 'medicine'\n";
}
