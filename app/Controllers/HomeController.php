<?php

namespace App\Controllers;

use App\Models\SliderModel;
use App\Models\PricingModel;
use App\Models\GalleryModel;
use App\Models\TestimonialModel;
use App\Models\ClientModel;
use App\Helpers\View;

class HomeController
{
    public function index(): void
    {
        $sliders = (new SliderModel())->getActive();
        $packages = (new PricingModel())->getActiveWithFeatures();
        $gallery = (new GalleryModel())->getActive();
        $testimonials = (new TestimonialModel())->getActive();
        $clients = (new ClientModel())->getActive();

        View::render('home/index', compact(
            'sliders', 'packages', 'gallery', 'testimonials', 'clients'
        ), 'main');
    }
}
