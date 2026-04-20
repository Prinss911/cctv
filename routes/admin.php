<?php

/** @var \App\Helpers\Router $router */

$router->get('/admin/login', 'Admin\AuthController@showLogin');
$router->post('/admin/login', 'Admin\AuthController@login');
$router->get('/admin/logout', 'Admin\AuthController@logout');

$router->get('/admin', 'Admin\DashboardController@index');

$router->get('/admin/sliders', 'Admin\SliderController@index');
$router->get('/admin/sliders/create', 'Admin\SliderController@create');
$router->post('/admin/sliders', 'Admin\SliderController@store');
$router->get('/admin/sliders/{id}/edit', 'Admin\SliderController@edit');
$router->post('/admin/sliders/{id}', 'Admin\SliderController@update');
$router->post('/admin/sliders/{id}/delete', 'Admin\SliderController@destroy');
$router->post('/admin/sliders/reorder', 'Admin\SliderController@reorder');

$router->get('/admin/gallery', 'Admin\GalleryController@index');
$router->get('/admin/gallery/create', 'Admin\GalleryController@create');
$router->post('/admin/gallery', 'Admin\GalleryController@store');
$router->get('/admin/gallery/{id}/edit', 'Admin\GalleryController@edit');
$router->post('/admin/gallery/{id}', 'Admin\GalleryController@update');
$router->post('/admin/gallery/{id}/delete', 'Admin\GalleryController@destroy');
$router->post('/admin/gallery/reorder', 'Admin\GalleryController@reorder');

$router->get('/admin/pricing', 'Admin\PricingController@index');
$router->get('/admin/pricing/create', 'Admin\PricingController@create');
$router->post('/admin/pricing', 'Admin\PricingController@store');
$router->get('/admin/pricing/{id}/edit', 'Admin\PricingController@edit');
$router->post('/admin/pricing/{id}', 'Admin\PricingController@update');
$router->post('/admin/pricing/{id}/delete', 'Admin\PricingController@destroy');
$router->post('/admin/pricing/reorder', 'Admin\PricingController@reorder');

$router->get('/admin/testimonials', 'Admin\TestimonialController@index');
$router->get('/admin/testimonials/create', 'Admin\TestimonialController@create');
$router->post('/admin/testimonials', 'Admin\TestimonialController@store');
$router->get('/admin/testimonials/{id}/edit', 'Admin\TestimonialController@edit');
$router->post('/admin/testimonials/{id}', 'Admin\TestimonialController@update');
$router->post('/admin/testimonials/{id}/delete', 'Admin\TestimonialController@destroy');
$router->post('/admin/testimonials/reorder', 'Admin\TestimonialController@reorder');

$router->get('/admin/clients', 'Admin\ClientController@index');
$router->get('/admin/clients/create', 'Admin\ClientController@create');
$router->post('/admin/clients', 'Admin\ClientController@store');
$router->get('/admin/clients/{id}/edit', 'Admin\ClientController@edit');
$router->post('/admin/clients/{id}', 'Admin\ClientController@update');
$router->post('/admin/clients/{id}/delete', 'Admin\ClientController@destroy');
$router->post('/admin/clients/reorder', 'Admin\ClientController@reorder');

$router->get('/admin/settings', 'Admin\SettingsController@index');
$router->post('/admin/settings', 'Admin\SettingsController@update');
