<?php

/** @var \App\Helpers\Router $router */

$router->get('/admin/login', 'Admin\AuthController@showLogin');
$router->post('/admin/login', 'Admin\AuthController@login', [\App\Middleware\CsrfMiddleware::class]);
$router->get('/admin/logout', 'Admin\AuthController@logout');

// Password reset routes (without auth middleware - accessible without login)
$router->get('/admin/forgot-password', 'Admin\PasswordResetController@showForgotForm');
$router->post('/admin/forgot-password', 'Admin\PasswordResetController@sendResetLink', [\App\Middleware\CsrfMiddleware::class]);
$router->get('/admin/reset-password', 'Admin\PasswordResetController@showResetForm');
$router->post('/admin/reset-password', 'Admin\PasswordResetController@resetPassword', [\App\Middleware\CsrfMiddleware::class]);

$router->get('/admin', 'Admin\DashboardController@index', [\App\Middleware\AuthMiddleware::class]);
$router->get('/admin/sliders', 'Admin\SliderController@index', [\App\Middleware\AuthMiddleware::class]);
$router->get('/admin/sliders/create', 'Admin\SliderController@create', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/sliders', 'Admin\SliderController@store', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->get('/admin/sliders/{id}/edit', 'Admin\SliderController@edit', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/sliders/{id}', 'Admin\SliderController@update', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->post('/admin/sliders/{id}/delete', 'Admin\SliderController@destroy', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->post('/admin/sliders/reorder', 'Admin\SliderController@reorder', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);

$router->get('/admin/gallery', 'Admin\GalleryController@index', [\App\Middleware\AuthMiddleware::class]);
$router->get('/admin/gallery/create', 'Admin\GalleryController@create', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/gallery', 'Admin\GalleryController@store', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->get('/admin/gallery/{id}/edit', 'Admin\GalleryController@edit', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/gallery/{id}', 'Admin\GalleryController@update', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->post('/admin/gallery/{id}/delete', 'Admin\GalleryController@destroy', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->post('/admin/gallery/reorder', 'Admin\GalleryController@reorder', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);

$router->get('/admin/pricing', 'Admin\PricingController@index', [\App\Middleware\AuthMiddleware::class]);
$router->get('/admin/pricing/create', 'Admin\PricingController@create', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/pricing', 'Admin\PricingController@store', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->get('/admin/pricing/{id}/edit', 'Admin\PricingController@edit', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/pricing/{id}', 'Admin\PricingController@update', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->post('/admin/pricing/{id}/delete', 'Admin\PricingController@destroy', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->post('/admin/pricing/reorder', 'Admin\PricingController@reorder', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);

$router->get('/admin/brands', 'Admin\BrandController@index', [\App\Middleware\AuthMiddleware::class]);
$router->get('/admin/brands/create', 'Admin\BrandController@create', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/brands', 'Admin\BrandController@store', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->get('/admin/brands/{id}/edit', 'Admin\BrandController@edit', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/brands/{id}', 'Admin\BrandController@update', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->post('/admin/brands/{id}/delete', 'Admin\BrandController@destroy', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->post('/admin/brands/reorder', 'Admin\BrandController@reorder', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);

$router->get('/admin/testimonials', 'Admin\TestimonialController@index', [\App\Middleware\AuthMiddleware::class]);
$router->get('/admin/testimonials/create', 'Admin\TestimonialController@create', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/testimonials', 'Admin\TestimonialController@store', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->get('/admin/testimonials/{id}/edit', 'Admin\TestimonialController@edit', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/testimonials/{id}', 'Admin\TestimonialController@update', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->post('/admin/testimonials/{id}/delete', 'Admin\TestimonialController@destroy', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->post('/admin/testimonials/reorder', 'Admin\TestimonialController@reorder', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);

$router->get('/admin/clients', 'Admin\ClientController@index', [\App\Middleware\AuthMiddleware::class]);
$router->get('/admin/clients/create', 'Admin\ClientController@create', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/clients', 'Admin\ClientController@store', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->get('/admin/clients/{id}/edit', 'Admin\ClientController@edit', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/clients/{id}', 'Admin\ClientController@update', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->post('/admin/clients/{id}/delete', 'Admin\ClientController@destroy', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->post('/admin/clients/reorder', 'Admin\ClientController@reorder', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);

$router->get('/admin/users', 'Admin\UsersController@index', [\App\Middleware\AuthMiddleware::class]);
$router->get('/admin/users/create', 'Admin\UsersController@create', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/users', 'Admin\UsersController@store', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->get('/admin/users/{id}/edit', 'Admin\UsersController@edit', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/users/{id}', 'Admin\UsersController@update', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->post('/admin/users/{id}/delete', 'Admin\UsersController@destroy', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
$router->get('/admin/users/password', 'Admin\UsersController@password', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/users/password', 'Admin\UsersController@updatePassword', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);

$router->get('/admin/settings', 'Admin\SettingsController@index', [\App\Middleware\AuthMiddleware::class]);
$router->post('/admin/settings', 'Admin\SettingsController@update', [\App\Middleware\AuthMiddleware::class, \App\Middleware\CsrfMiddleware::class]);
