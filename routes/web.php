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
$router->get('/projects/{slug}', 'Frontend\PageController@project');
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

    // Phase 5 CMS modules
    $router->get('/pages', 'Admin\PageController@index')->middleware(['auth', 'permission:pages.view']);
    $router->get('/pages/create', 'Admin\PageController@create')->middleware(['auth', 'permission:pages.create']);
    $router->post('/pages', 'Admin\PageController@store')->middleware(['auth', 'csrf', 'permission:pages.create']);
    $router->get('/pages/{id}/edit', 'Admin\PageController@edit')->middleware(['auth', 'permission:pages.edit']);
    $router->post('/pages/{id}', 'Admin\PageController@update')->middleware(['auth', 'csrf', 'permission:pages.edit']);
    $router->post('/pages/{id}/delete', 'Admin\PageController@destroy')->middleware(['auth', 'csrf', 'permission:pages.delete']);

    $router->get('/services', 'Admin\ServiceController@index')->middleware(['auth', 'permission:services.view']);
    $router->get('/services/create', 'Admin\ServiceController@create')->middleware(['auth', 'permission:services.create']);
    $router->post('/services', 'Admin\ServiceController@store')->middleware(['auth', 'csrf', 'permission:services.create']);
    $router->get('/services/{id}/edit', 'Admin\ServiceController@edit')->middleware(['auth', 'permission:services.edit']);
    $router->post('/services/{id}', 'Admin\ServiceController@update')->middleware(['auth', 'csrf', 'permission:services.edit']);
    $router->post('/services/{id}/delete', 'Admin\ServiceController@destroy')->middleware(['auth', 'csrf', 'permission:services.delete']);

    $router->get('/projects', 'Admin\ProjectController@index')->middleware(['auth', 'permission:projects.view']);
    $router->get('/projects/create', 'Admin\ProjectController@create')->middleware(['auth', 'permission:projects.create']);
    $router->post('/projects', 'Admin\ProjectController@store')->middleware(['auth', 'csrf', 'permission:projects.create']);
    $router->get('/projects/{id}/edit', 'Admin\ProjectController@edit')->middleware(['auth', 'permission:projects.edit']);
    $router->post('/projects/{id}', 'Admin\ProjectController@update')->middleware(['auth', 'csrf', 'permission:projects.edit']);
    $router->post('/projects/{id}/delete', 'Admin\ProjectController@destroy')->middleware(['auth', 'csrf', 'permission:projects.delete']);

    $router->get('/media', 'Admin\MediaController@index')->middleware(['auth', 'permission:media.view']);
    $router->post('/media', 'Admin\MediaController@store')->middleware(['auth', 'csrf', 'permission:media.upload']);
    $router->post('/media/{id}', 'Admin\MediaController@update')->middleware(['auth', 'csrf', 'permission:media.edit']);
    $router->post('/media/{id}/delete', 'Admin\MediaController@destroy')->middleware(['auth', 'csrf', 'permission:media.delete']);

    // Phase 6 ecommerce management.
    $router->get('/products', 'Admin\ProductController@index')->middleware(['auth', 'permission:products.view']);
    $router->get('/products/create', 'Admin\ProductController@create')->middleware(['auth', 'permission:products.create']);
    $router->post('/products', 'Admin\ProductController@store')->middleware(['auth', 'csrf', 'permission:products.create']);
    $router->post('/products/import', 'Admin\ProductController@import')->middleware(['auth', 'csrf', 'permission:products.create']);
    $router->get('/products/{id}/edit', 'Admin\ProductController@edit')->middleware(['auth', 'permission:products.edit']);
    $router->post('/products/{id}', 'Admin\ProductController@update')->middleware(['auth', 'csrf', 'permission:products.edit']);
    $router->post('/products/{id}/delete', 'Admin\ProductController@destroy')->middleware(['auth', 'csrf', 'permission:products.delete']);
    $router->post('/products/{id}/stock', 'Admin\ProductController@stock')->middleware(['auth', 'csrf', 'permission:products.edit']);

    $router->get('/product-categories', 'Admin\ProductCategoryController@index')->middleware(['auth', 'permission:products.view']);
    $router->get('/product-categories/create', 'Admin\ProductCategoryController@create')->middleware(['auth', 'permission:products.create']);
    $router->post('/product-categories', 'Admin\ProductCategoryController@store')->middleware(['auth', 'csrf', 'permission:products.create']);
    $router->get('/product-categories/{id}/edit', 'Admin\ProductCategoryController@edit')->middleware(['auth', 'permission:products.edit']);
    $router->post('/product-categories/{id}', 'Admin\ProductCategoryController@update')->middleware(['auth', 'csrf', 'permission:products.edit']);
    $router->post('/product-categories/{id}/delete', 'Admin\ProductCategoryController@destroy')->middleware(['auth', 'csrf', 'permission:products.delete']);

    $router->get('/orders', 'Admin\OrderController@index')->middleware(['auth', 'permission:orders.view']);
    $router->get('/orders/export', 'Admin\OrderController@export')->middleware(['auth', 'permission:orders.export']);
    $router->get('/orders/{id}', 'Admin\OrderController@show')->middleware(['auth', 'permission:orders.view']);
    $router->post('/orders/{id}/status', 'Admin\OrderController@status')->middleware(['auth', 'csrf', 'permission:orders.update_status']);
    $router->post('/orders/{id}/refund', 'Admin\OrderController@refund')->middleware(['auth', 'csrf', 'permission:orders.edit']);

    // Future-phase admin modules are stubbed until their implementation phases.
    $router->get('/customers', 'Admin\DashboardController@notImplemented')->middleware(['auth']);
    // Phase 7 RBAC and audit management.
    $router->get('/users', 'Admin\UserController@index')->middleware(['auth', 'permission:admins.view']);
    $router->get('/users/export', 'Admin\UserController@export')->middleware(['auth', 'permission:admins.view']);
    $router->get('/users/create', 'Admin\UserController@create')->middleware(['auth', 'permission:admins.create']);
    $router->post('/users', 'Admin\UserController@store')->middleware(['auth', 'csrf', 'permission:admins.create']);
    $router->get('/users/{id}/edit', 'Admin\UserController@edit')->middleware(['auth', 'permission:admins.edit']);
    $router->post('/users/{id}', 'Admin\UserController@update')->middleware(['auth', 'csrf', 'permission:admins.edit']);
    $router->post('/users/{id}/delete', 'Admin\UserController@destroy')->middleware(['auth', 'csrf', 'permission:admins.delete']);
    $router->post('/users/{id}/reset-password', 'Admin\UserController@resetPassword')->middleware(['auth', 'csrf', 'permission:admins.reset_password']);

    $router->get('/roles', 'Admin\RoleController@index')->middleware(['auth', 'permission:roles.view']);
    $router->get('/roles/create', 'Admin\RoleController@create')->middleware(['auth', 'permission:roles.create']);
    $router->post('/roles', 'Admin\RoleController@store')->middleware(['auth', 'csrf', 'permission:roles.create']);
    $router->get('/roles/{id}/edit', 'Admin\RoleController@edit')->middleware(['auth', 'permission:roles.edit']);
    $router->post('/roles/{id}', 'Admin\RoleController@update')->middleware(['auth', 'csrf', 'permission:roles.edit']);
    $router->post('/roles/{id}/delete', 'Admin\RoleController@destroy')->middleware(['auth', 'csrf', 'permission:roles.delete']);

    $router->get('/permissions', 'Admin\PermissionController@index')->middleware(['auth', 'permission:permissions.view']);
    $router->get('/permissions/create', 'Admin\PermissionController@create')->middleware(['auth', 'permission:permissions.assign']);
    $router->post('/permissions', 'Admin\PermissionController@store')->middleware(['auth', 'csrf', 'permission:permissions.assign']);
    $router->get('/permissions/{id}/edit', 'Admin\PermissionController@edit')->middleware(['auth', 'permission:permissions.assign']);
    $router->post('/permissions/{id}', 'Admin\PermissionController@update')->middleware(['auth', 'csrf', 'permission:permissions.assign']);

    $router->get('/activity-logs', 'Admin\ActivityLogController@index')->middleware(['auth', 'permission:activity_logs.view']);
    $router->get('/settings', 'Admin\DashboardController@notImplemented')->middleware(['auth', 'admin']);
});
