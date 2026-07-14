<?php

namespace App\Controllers;

use App\Models\SliderModel;
use App\Models\PricingModel;
use App\Models\PricingBrandModel;
use App\Models\GalleryModel;
use App\Models\TestimonialModel;
use App\Models\ClientModel;
use App\Models\AdvantageModel;
use App\Models\AboutFeatureModel;
use App\Models\FaqModel;
use App\Helpers\View;

class HomeController
{
    public function index(): void
    {
        $sliders = (new SliderModel())->getActive();
        $pricingModel = new PricingModel();
        $packages = $pricingModel->getActiveWithFeatures(); // grouped by brand_id
        $brands = (new PricingBrandModel())->getActive();
        $gallery = (new GalleryModel())->getActive();
        $testimonials = (new TestimonialModel())->getActive();
        $clients = (new ClientModel())->getActive();
        $advantages = (new AdvantageModel())->getActive('sort_order ASC');
        $aboutFeatures = (new AboutFeatureModel())->getActive('sort_order ASC');
        $faqs = (new FaqModel())->getActive('sort_order ASC');

        View::render('home/index', compact(
            'sliders', 'packages', 'brands', 'gallery', 'testimonials', 'advantages', 'clients', 'aboutFeatures', 'faqs'
        ), 'main');
    }
}