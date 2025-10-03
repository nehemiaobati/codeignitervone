<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

//--------------------------------------------------------------------
// Public Routes (No Authentication Required)
//--------------------------------------------------------------------
// These routes are accessible to everyone.
$routes->group('', static function ($routes) {
    // Home & Welcome Page
    $routes->get('/', 'HomeController::landing', ['as' => 'welcome']);

    // Authentication Routes
    $routes->get('register', 'AuthController::register', ['as' => 'register']);
    $routes->post('register/store', 'AuthController::store', ['as' => 'register.store']);
    $routes->get('verify-email/(:segment)', 'AuthController::verifyEmail/$1', ['as' => 'verify_email']);
    $routes->get('login', 'AuthController::login', ['as' => 'login']);
    $routes->post('login/authenticate', 'AuthController::authenticate', ['as' => 'login.authenticate']);
    $routes->get('logout', 'AuthController::logout', ['as' => 'logout']); // Moved logout here as it's often accessible before full auth

    // Contact Routes
    $routes->get('contact', 'ContactController::form', ['as' => 'contact.form']);
    $routes->post('contact/send', 'ContactController::send', ['as' => 'contact.send']);

    // Portfolio Routes
    $routes->get('portfolio', 'PortfolioController::index', ['as' => 'portfolio.index']);
    $routes->post('portfolio/send', 'PortfolioController::sendEmail', ['as' => 'portfolio.sendEmail']);

    // Legal Routes
    $routes->get('terms', 'HomeController::terms', ['as' => 'terms']);
    $routes->get('privacy', 'HomeController::privacy', ['as' => 'privacy']);
});

//--------------------------------------------------------------------
// Authenticated User Routes
//--------------------------------------------------------------------
// These routes require the user to be logged in.
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard/Home for logged-in users
    $routes->get('home', 'HomeController::index', ['as' => 'home']);

    // Admin Panel Routes
    $routes->group('admin', static function ($routes) {
        $routes->get('/', 'AdminController::index', ['as' => 'admin.index']);
        $routes->get('users/(:num)', 'AdminController::show/$1', ['as' => 'admin.users.show']);
        $routes->post('users/update_balance/(:num)', 'AdminController::updateBalance/$1', ['as' => 'admin.users.update_balance']);
        $routes->post('admin/users/delete/(:num)', 'AdminController::delete/$1', ['as' => 'admin.users.delete']); // Corrected path to 'admin/users/delete'
    });

    // Payment Routes
    $routes->group('payment', static function ($routes) {
        $routes->get('/', 'PaymentsController::index', ['as' => 'payment.index']);
        //$routes->get('initiate', 'Payments::initiate', ['as' => 'payment.initiate']); // Added GET route
        $routes->post('initiate', 'PaymentsController::initiate', ['as' => 'payment.initiate']);
        $routes->get('verify', 'PaymentsController::verify', ['as' => 'payment.verify']);
    });

    // Crypto Routes (with balance filter)
    $routes->group('crypto', ['filter' => 'balance'], static function ($routes) {
        $routes->get('/', 'CryptoController::index', ['as' => 'crypto.index']);
        $routes->post('query', 'CryptoController::query', ['as' => 'crypto.query']);
    });

    // Gemini API Routes (with balance filter)
    $routes->group('gemini', ['filter' => 'balance'], static function ($routes) {
        $routes->get('/', 'GeminiController::index', ['as' => 'gemini.index']);
        $routes->post('generate', 'GeminiController::generate', ['as' => 'gemini.generate']);
    });
});
