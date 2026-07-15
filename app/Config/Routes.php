<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public - full page loads
$routes->get('/', 'HomeController::index');
$routes->get('quiz/(:segment)', 'QuizController::play/$1');
$routes->get('quiz/(:segment)/play', 'QuizController::play/$1');

// Info static pages
$routes->get('info/about', 'InfoController::about');
$routes->get('info/contact', 'InfoController::contact');
$routes->get('info/privacy', 'InfoController::privacy');
$routes->get('info/terms', 'InfoController::terms');
$routes->get('info/disclaimer', 'InfoController::disclaimer');

// Auth - full page loads
$routes->get('login', 'Auth\LoginController::index');
$routes->post('login', 'Auth\LoginController::authenticate');
$routes->get('register', 'Auth\RegisterController::index');
$routes->post('register', 'Auth\RegisterController::store');
$routes->get('logout', 'Auth\LoginController::logout');

// User - full page loads, auth filter
$routes->group('user', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'User\DashboardController::index');
    $routes->get('history', 'User\DashboardController::history');
    $routes->get('profile', 'User\DashboardController::profile');
    $routes->post('profile/update', 'User\DashboardController::update');
});

// Admin - full page loads, adminAuth filter
$routes->group('admin', ['filter' => 'adminAuth'], function($routes) {
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('quizzes', 'Admin\QuizController::index');
    $routes->get('quizzes/create', 'Admin\QuizController::create');
    $routes->post('quizzes/store', 'Admin\QuizController::store');
    $routes->get('quizzes/edit/(:num)', 'Admin\QuizController::edit/$1');
    $routes->post('quizzes/update/(:num)', 'Admin\QuizController::update/$1');
    $routes->post('quizzes/delete/(:num)', 'Admin\QuizController::delete/$1');
    $routes->get('questions/(:num)', 'Admin\QuestionController::index/$1');
    $routes->get('questions/create/(:num)', 'Admin\QuestionController::create/$1');
    $routes->post('questions/store', 'Admin\QuestionController::store');
    $routes->get('questions/edit/(:num)', 'Admin\QuestionController::edit/$1');
    $routes->post('questions/update/(:num)', 'Admin\QuestionController::update/$1');
    $routes->post('questions/delete/(:num)', 'Admin\QuestionController::delete/$1');
    $routes->post('questions/import/(:num)', 'Admin\QuestionController::csvImport/$1');
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('users/view/(:num)', 'Admin\UserController::view/$1');
    $routes->post('users/toggle-ban/(:num)', 'Admin\UserController::toggleBan/$1');
    $routes->get('categories', 'Admin\CategoryController::index');
    $routes->get('categories/create', 'Admin\CategoryController::create');
    $routes->post('categories/store', 'Admin\CategoryController::store');
    $routes->get('categories/edit/(:num)', 'Admin\CategoryController::edit/$1');
    $routes->post('categories/update/(:num)', 'Admin\CategoryController::update/$1');
    $routes->post('categories/delete/(:num)', 'Admin\CategoryController::delete/$1');
    $routes->get('ad-settings', 'Admin\AdSettingsController::index');
    $routes->post('ad-settings/update', 'Admin\AdSettingsController::update');

    // Player / Lead Information Routes
    $routes->get('players', 'Admin\PlayerController::index');
    $routes->get('players/download', 'Admin\PlayerController::download');
});

// AJAX API — all return JSON, no page reload ever
$routes->group('api/quiz', function($routes) {
    $routes->post('start', 'Api\QuizApiController::start');
    $routes->post('answer', 'Api\QuizApiController::submitAnswer');
    $routes->post('next', 'Api\QuizApiController::nextQuestion');
    $routes->post('complete', 'Api\QuizApiController::complete');
    $routes->post('restart', 'Api\QuizApiController::restart');
    $routes->post('save-lead', 'Api\QuizApiController::saveLead');
});
