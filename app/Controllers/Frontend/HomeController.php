<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Helpers\SeoHelper;

/**
 * HomeController - Handles public home page
 */
class HomeController extends BaseController
{
    /**
     * Display home page
     *
     * @return string
     */
    public function index(): string
    {
        $services = ServiceController::services();

        return $this->view('frontend/pages/home', [
            'title' => 'Integrated Solutions in Nigeria | Desnky Global',
            'active' => 'home',
            'services' => $services,
            'csrf_token' => $this->csrf(),
            'projects' => [
                [
                    'title' => 'Industrial Electrical Support',
                    'summary' => 'Electrical installation and maintenance support for industrial operations.',
                    'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=900&q=80',
                ],
                [
                    'title' => 'Procurement and Supply Coordination',
                    'summary' => 'Sourcing, vendor coordination and delivery support for technical materials.',
                    'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=900&q=80',
                ],
                [
                    'title' => 'Safety Equipment Deployment',
                    'summary' => 'HSE equipment supply and workplace safety readiness support.',
                    'image' => 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=900&q=80',
                ],
            ],
            'clients' => ['Energy Operators', 'Industrial Teams', 'Construction Firms', 'Agro Processors'],
            'seo' => [
                'title' => 'Desnky Global Resources Ltd | Integrated Nigerian Services',
                'description' => 'Desnky Global Resources Ltd delivers energy, engineering, procurement, HSE, ICT and agro solutions for businesses in Nigeria.',
                'keywords' => 'engineering company in Nigeria, energy services Nigeria, procurement company Lagos, HSE services Nigeria, ICT solutions Nigeria, agro products Nigeria',
                'canonical' => 'https://www.desnkygroup.com/',
                'schema' => [
                    SeoHelper::organizationSchema(),
                    SeoHelper::localBusinessSchema(),
                    SeoHelper::websiteSchema(),
                ],
            ],
        ]);
    }
}
