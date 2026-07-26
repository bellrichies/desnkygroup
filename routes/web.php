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

$router->get('/blog', 'Frontend\BlogController@index');
$router->get('/blog/search', 'Frontend\BlogController@search');
$router->get('/blog/feed.xml', 'Frontend\BlogController@feed');
$router->get('/blog/rss.xml', 'Frontend\BlogController@feed');
$router->get('/blog/category/{slug}', 'Frontend\BlogController@category');
$router->get('/blog/tag/{slug}', 'Frontend\BlogController@tag');
$router->get('/blog/{slug}', 'Frontend\BlogController@show');

$router->get('/privacy-policy', 'Frontend\LegalController@privacy');
$router->get('/cookie-policy', 'Frontend\LegalController@cookies');
$router->get('/terms-of-use', 'Frontend\LegalController@terms');
$router->get('/terms-and-conditions', 'Frontend\LegalController@terms');

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
    $router->get('/services/{serviceId}/heroes', 'Admin\ServiceHeroController@index')->middleware(['auth', 'permission:services.view']);
    $router->get('/services/{serviceId}/heroes/create', 'Admin\ServiceHeroController@create')->middleware(['auth', 'permission:services.edit']);
    $router->post('/services/{serviceId}/heroes', 'Admin\ServiceHeroController@store')->middleware(['auth', 'csrf', 'permission:services.edit']);
    $router->post('/services/{serviceId}/heroes/reorder', 'Admin\ServiceHeroController@reorder')->middleware(['auth', 'csrf', 'permission:services.edit']);
    $router->get('/services/{serviceId}/heroes/{id}/edit', 'Admin\ServiceHeroController@edit')->middleware(['auth', 'permission:services.edit']);
    $router->post('/services/{serviceId}/heroes/{id}', 'Admin\ServiceHeroController@update')->middleware(['auth', 'csrf', 'permission:services.edit']);
    $router->post('/services/{serviceId}/heroes/{id}/toggle', 'Admin\ServiceHeroController@toggle')->middleware(['auth', 'csrf', 'permission:services.edit']);
    $router->post('/services/{serviceId}/heroes/{id}/delete', 'Admin\ServiceHeroController@destroy')->middleware(['auth', 'csrf', 'permission:services.delete']);
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
    $router->get('/media/json', 'Admin\MediaController@listJson')->middleware(['auth', 'permission:media.view']);
    $router->post('/media', 'Admin\MediaController@store')->middleware(['auth', 'csrf', 'permission:media.upload']);
    $router->post('/media/{id}', 'Admin\MediaController@update')->middleware(['auth', 'csrf', 'permission:media.edit']);
    $router->post('/media/{id}/delete', 'Admin\MediaController@destroy')->middleware(['auth', 'csrf', 'permission:media.delete']);

    $router->get('/homepage-hero', 'Admin\HeroSliderController@index')->middleware(['auth', 'permission:settings.view']);
    $router->get('/homepage-hero/create', 'Admin\HeroSliderController@create')->middleware(['auth', 'permission:settings.edit']);
    $router->post('/homepage-hero', 'Admin\HeroSliderController@store')->middleware(['auth', 'csrf', 'permission:settings.edit']);
    $router->post('/homepage-hero/reorder', 'Admin\HeroSliderController@reorder')->middleware(['auth', 'csrf', 'permission:settings.edit']);
    $router->get('/homepage-hero/{id}/edit', 'Admin\HeroSliderController@edit')->middleware(['auth', 'permission:settings.edit']);
    $router->post('/homepage-hero/{id}', 'Admin\HeroSliderController@update')->middleware(['auth', 'csrf', 'permission:settings.edit']);
    $router->post('/homepage-hero/{id}/toggle', 'Admin\HeroSliderController@toggle')->middleware(['auth', 'csrf', 'permission:settings.edit']);
    $router->post('/homepage-hero/{id}/delete', 'Admin\HeroSliderController@destroy')->middleware(['auth', 'csrf', 'permission:settings.edit']);

    // Blog management.
    $router->get('/blog', 'Admin\BlogController@index')->middleware(['auth', 'permission:blog.view']);
    $router->get('/blog/create', 'Admin\BlogController@create')->middleware(['auth', 'permission:blog.create']);
    $router->post('/blog', 'Admin\BlogController@store')->middleware(['auth', 'csrf', 'permission:blog.create']);
    $router->post('/blog/bulk-status', 'Admin\BlogController@bulkStatus')->middleware(['auth', 'csrf', 'permission:blog.publish']);
    $router->get('/blog/categories', 'Admin\BlogController@taxonomy')->middleware(['auth', 'permission:blog.taxonomy']);
    $router->post('/blog/categories', 'Admin\BlogController@categoryStore')->middleware(['auth', 'csrf', 'permission:blog.taxonomy']);
    $router->post('/blog/categories/{id}', 'Admin\BlogController@categoryUpdate')->middleware(['auth', 'csrf', 'permission:blog.taxonomy']);
    $router->post('/blog/categories/{id}/delete', 'Admin\BlogController@categoryDelete')->middleware(['auth', 'csrf', 'permission:blog.taxonomy']);
    $router->post('/blog/tags', 'Admin\BlogController@tagStore')->middleware(['auth', 'csrf', 'permission:blog.taxonomy']);
    $router->post('/blog/tags/{id}', 'Admin\BlogController@tagUpdate')->middleware(['auth', 'csrf', 'permission:blog.taxonomy']);
    $router->post('/blog/tags/{id}/delete', 'Admin\BlogController@tagDelete')->middleware(['auth', 'csrf', 'permission:blog.taxonomy']);
    $router->get('/blog/authors', 'Admin\BlogController@authors')->middleware(['auth', 'permission:blog.edit']);
    $router->post('/blog/authors', 'Admin\BlogController@authorStore')->middleware(['auth', 'csrf', 'permission:blog.edit']);
    $router->post('/blog/authors/{id}', 'Admin\BlogController@authorUpdate')->middleware(['auth', 'csrf', 'permission:blog.edit']);
    $router->post('/blog/authors/{id}/delete', 'Admin\BlogController@authorDelete')->middleware(['auth', 'csrf', 'permission:blog.edit']);
    $router->get('/blog/ads', 'Admin\BlogController@ads')->middleware(['auth', 'permission:blog.ads']);
    $router->post('/blog/ads/{id}', 'Admin\BlogController@adUpdate')->middleware(['auth', 'csrf', 'permission:blog.ads']);
    $router->get('/blog/{id}/preview', 'Admin\BlogController@preview')->middleware(['auth', 'permission:blog.view']);
    $router->get('/blog/{id}/edit', 'Admin\BlogController@edit')->middleware(['auth', 'permission:blog.edit']);
    $router->post('/blog/{id}', 'Admin\BlogController@update')->middleware(['auth', 'csrf', 'permission:blog.edit']);
    $router->post('/blog/{id}/delete', 'Admin\BlogController@destroy')->middleware(['auth', 'csrf', 'permission:blog.delete']);
    $router->post('/blog/{id}/restore', 'Admin\BlogController@restore')->middleware(['auth', 'csrf', 'permission:blog.restore']);

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

    $router->get('/customers', 'Admin\CustomerController@index')->middleware(['auth', 'permission:orders.view']);
    $router->get('/customers/export', 'Admin\CustomerController@export')->middleware(['auth', 'permission:orders.export']);
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

    // Trusted Clients
    $router->get('/trusted-clients', 'Admin\TrustedClientController@index')->middleware(['auth', 'permission:settings.view']);
    $router->get('/trusted-clients/create', 'Admin\TrustedClientController@create')->middleware(['auth', 'permission:settings.edit']);
    $router->post('/trusted-clients', 'Admin\TrustedClientController@store')->middleware(['auth', 'csrf', 'permission:settings.edit']);
    $router->get('/trusted-clients/{id}/edit', 'Admin\TrustedClientController@edit')->middleware(['auth', 'permission:settings.edit']);
    $router->post('/trusted-clients/{id}', 'Admin\TrustedClientController@update')->middleware(['auth', 'csrf', 'permission:settings.edit']);
    $router->post('/trusted-clients/{id}/delete', 'Admin\TrustedClientController@destroy')->middleware(['auth', 'csrf', 'permission:settings.edit']);
    $router->post('/trusted-clients/{id}/toggle', 'Admin\TrustedClientController@toggle')->middleware(['auth', 'csrf', 'permission:settings.edit']);

    // Contact Inquiries
    $router->get('/contacts', 'Admin\ContactController@index')->middleware(['auth', 'permission:pages.view']);
    $router->get('/contacts/{id}', 'Admin\ContactController@show')->middleware(['auth', 'permission:pages.view']);
    $router->post('/contacts/{id}/status', 'Admin\ContactController@status')->middleware(['auth', 'csrf', 'permission:pages.view']);
    $router->post('/contacts/{id}/notes', 'Admin\ContactController@notes')->middleware(['auth', 'csrf', 'permission:pages.view']);
    $router->post('/contacts/{id}/delete', 'Admin\ContactController@destroy')->middleware(['auth', 'csrf', 'permission:pages.edit']);

    // Newsletter Subscribers
    $router->get('/subscribers', 'Admin\SubscriberController@index')->middleware(['auth', 'permission:pages.view']);
    $router->get('/subscribers/export', 'Admin\SubscriberController@export')->middleware(['auth', 'permission:pages.view']);
    $router->post('/subscribers/{id}/unsubscribe', 'Admin\SubscriberController@unsubscribe')->middleware(['auth', 'csrf', 'permission:pages.edit']);
    $router->post('/subscribers/{id}/delete', 'Admin\SubscriberController@destroy')->middleware(['auth', 'csrf', 'permission:pages.edit']);

    // Settings (with FAQs tab)
    $router->get('/settings', 'Admin\SettingController@index')->middleware(['auth', 'permission:settings.view']);
    $router->post('/settings', 'Admin\SettingController@update')->middleware(['auth', 'csrf', 'permission:settings.edit']);
    $router->post('/settings/cache-clear', 'Admin\SettingController@clearCache')->middleware(['auth', 'csrf', 'permission:settings.edit']);
    $router->post('/settings/faqs', 'Admin\SettingController@faqStore')->middleware(['auth', 'csrf', 'permission:settings.edit']);
    $router->get('/settings/faqs/{id}/edit', 'Admin\SettingController@faqEdit')->middleware(['auth', 'permission:settings.edit']);
    $router->post('/settings/faqs/{id}', 'Admin\SettingController@faqUpdate')->middleware(['auth', 'csrf', 'permission:settings.edit']);
    $router->post('/settings/faqs/{id}/delete', 'Admin\SettingController@faqDestroy')->middleware(['auth', 'csrf', 'permission:settings.edit']);
    $router->post('/settings/faqs/{id}/toggle', 'Admin\SettingController@faqToggle')->middleware(['auth', 'csrf', 'permission:settings.edit']);
});
