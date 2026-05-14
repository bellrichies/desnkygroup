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
$router->get('/projects', 'Frontend\PageController@projects');
$router->get('/about', 'Frontend\PageController@about');
$router->get('/hse-policy', 'Frontend\PageController@hse');
$router->get('/contact', 'Frontend\ContactController@show');
$router->post('/contact/submit', 'Frontend\ContactController@submit')->middleware(['csrf']);
$router->post('/api/contact', 'Frontend\ContactController@submit')->middleware(['csrf']);
$router->post('/newsletter', 'Frontend\NewsletterController@subscribe')->middleware(['csrf']);

$router->get('/shop', 'Frontend\ShopController@index');
$router->get('/shop/category/{slug}', 'Frontend\ShopController@category');
$router->get('/shop/product/{slug}', 'Frontend\ShopController@show');
$router->get('/shop/cart', 'Frontend\CartController@index');
$router->post('/shop/cart/add', 'Frontend\CartController@add')->middleware(['csrf']);
$router->post('/shop/cart/update', 'Frontend\CartController@update')->middleware(['csrf']);
$router->post('/shop/cart/remove', 'Frontend\CartController@remove')->middleware(['csrf']);
$router->get('/shop/checkout', 'Frontend\CheckoutController@index');
$router->post('/shop/checkout', 'Frontend\CheckoutController@store')->middleware(['csrf']);

$router->get('/services.html', 'Frontend\PageController@redirectToServices');
$router->get('/contact.html', 'Frontend\PageController@redirectToContact');
$router->get('/hse.html', 'Frontend\PageController@redirectToHse');
$router->get('/gallery.html', 'Frontend\PageController@redirectToProjects');

// Admin Routes
$router->group('/admin', function ($router) {
    // Authentication
    $router->get('/login', 'Admin\AuthController@login');
    $router->post('/login', 'Admin\AuthController@authenticate')->middleware(['csrf']);
    $router->post('/logout', 'Admin\AuthController@logout')->middleware(['auth', 'csrf']);

    // Dashboard (requires auth)
    $router->get('/', 'Admin\DashboardController@index')->middleware(['auth']);
    $router->get('/dashboard', 'Admin\DashboardController@index')->middleware(['auth']);

    // Future-phase admin modules are stubbed until their implementation phases.
    $router->get('/pages', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/services', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/projects', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/products', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/product-categories', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/orders', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/customers', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/users', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/roles', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/permissions', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/activity-logs', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    $router->get('/settings', 'Admin\DashboardController@notImplemented')->middleware(['auth', 'admin']);
});
