<?php
define('ENVIRONMENT', 'development');
define('FCPATH', __DIR__ . '/../public/');
require __DIR__ . '/../app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

// Mock request
$_POST['quiz_id'] = 2;
$_POST['user_id'] = '';
$_POST['guest_token'] = 'test_guest';

$controller = new \App\Controllers\Api\QuizApiController();
$controller->initController(
    \Config\Services::request(),
    \Config\Services::response(),
    \Config\Services::logger()
);

$response = $controller->start();
echo $response->getBody() . "\n";
