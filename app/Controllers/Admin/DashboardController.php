<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\SliderModel;
use App\Models\GalleryModel;
use App\Models\PricingModel;
use App\Models\TestimonialModel;
use App\Models\ClientModel;
use App\Helpers\View;

class DashboardController
{
    public function index(): void
    {
        AuthMiddleware::handle();

        $stats = [
            'sliders'      => (new SliderModel())->count(),
            'gallery'      => (new GalleryModel())->count(),
            'packages'     => (new PricingModel())->count(),
            'testimonials' => (new TestimonialModel())->count(),
            'clients'      => (new ClientModel())->count(),
        ];

        View::render('admin/dashboard', compact('stats'), 'admin');
    }
}
