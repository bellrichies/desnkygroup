<?php

/**
 * Web Routes
 * 
 * Define all HTTP routes for the application.
 * Routes are matched in the order they are registered.
 * 
 * @var App\Router $router
 */

// Public Routes
$router->get('/', 'Frontend\HomeController@index');
$router->get('/services', 'Frontend\ServiceController@index');
$router->get('/services/{slug}', 'Frontend\ServiceController@show');
$router->get('/contact', 'Frontend\ContactController@show');
$router->post('/contact/submit', 'Frontend\ContactController@submit')->middleware(['csrf']);
$router->post('/api/contact', 'Frontend\ContactController@submit')->middleware(['csrf']);

// Future-phase public routes are intentionally stubbed during Phase 1.
$router->get('/shop', 'Frontend\StubController@notImplemented');
$router->get('/projects', 'Frontend\StubController@notImplemented');

// Admin Routes
$router->group('/admin', function ($router) {
    // Authentication
    $router->get('/login', 'Admin\AuthController@loginForm');
    $router->post('/login', 'Admin\AuthController@login');
    $router->post('/logout', 'Admin\AuthController@logout');

    // Dashboard (requires auth)
    $router->get('/', 'Admin\DashboardController@index')->middleware(['auth']);
    $router->get('/dashboard', 'Admin\DashboardController@index')->middleware(['auth']);

    // Future-phase admin modules are stubbed until their implementation phases.
    $router->get('/pages', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/services', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/products', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/orders', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/settings', 'Admin\DashboardController@notImplemented')->middleware(['auth', 'admin']);
});
