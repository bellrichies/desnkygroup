<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Helpers\SeoHelper;
use App\Services\ServiceService;

/**
 * Phase 1 service page placeholder.
 */
class ServiceController extends BaseController
{
    public function __construct(private ServiceService $serviceService)
    {
    }

    public function index(): string
    {
        $services = $this->publishedServices();

        return $this->view('frontend/pages/services/index', [
            'title' => 'Our Services',
            'active' => 'services',
            'services' => $services,
            'seo' => [
                'title' => 'Services | Desnky Global Resources Ltd',
                'description' => 'Explore Desnky Global Resources services in engineering, energy, procurement, HSE, ICT and agro food processing across Nigeria.',
                'canonical' => 'https://www.desnkygroup.com/services',
            ],
        ]);
    }

    public function show(string $slug): string
    {
        $services = $this->publishedServices();
        $service = $services[$slug] ?? null;

        if ($service === null) {
            http_response_code(404);

            return $this->view('frontend/pages/show', [
                'title' => 'Service Not Found',
                'content' => '<p>The requested service page could not be found.</p>',
                'active' => 'services',
            ]);
        }

        return $this->view('frontend/pages/services/show', [
            'title' => $service['title'],
            'active' => 'services',
            'service' => $service,
            'relatedServices' => array_filter($services, fn ($item) => $item['slug'] !== $slug),
            'seo' => [
                'title' => $service['seo_title'],
                'description' => $service['seo_description'],
                'canonical' => 'https://www.desnkygroup.com/services/' . $slug,
                'image' => $service['image'],
                'schema' => SeoHelper::serviceSchema($service),
            ],
        ]);
    }

    /**
     * Static phase-three service content.
     *
     * @return array<string, array>
     */
    public static function services(): array
    {
        return [
            'engineering' => [
                'slug' => 'engineering',
                'title' => 'Engineering Services',
                'icon' => 'EN',
                'summary' => 'Electrical, mechanical and industrial engineering support for reliable operations.',
                'image' => 'https://images.unsplash.com/photo-1581092335878-2d9ff86ca2bf?auto=format&fit=crop&w=1200&q=80',
                'features' => ['Electrical installation support', 'Mechanical maintenance coordination', 'Industrial project support', 'Instrumentation and calibration assistance'],
                'process' => ['Assess requirements', 'Plan scope and resources', 'Execute with safety controls', 'Document completion'],
                'benefits' => ['Practical technical planning', 'Experienced vendor coordination', 'Strong safety discipline'],
                'seo_title' => 'Engineering Services in Nigeria | Desnky Global',
                'seo_description' => 'Electrical, mechanical and industrial engineering support from Desnky Global Resources Ltd for Nigerian businesses.',
            ],
            'energy-solutions' => [
                'slug' => 'energy-solutions',
                'title' => 'Energy Solutions',
                'icon' => 'EG',
                'summary' => 'Energy sector support, equipment sourcing and operational services for business continuity.',
                'image' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?auto=format&fit=crop&w=1200&q=80',
                'features' => ['Oil and gas support services', 'Power equipment procurement', 'Energy project logistics', 'Operations support'],
                'process' => ['Define need', 'Source equipment', 'Coordinate delivery', 'Support operations'],
                'benefits' => ['Reliable sector knowledge', 'Vendor and logistics control', 'Delivery-focused execution'],
                'seo_title' => 'Energy Solutions in Nigeria | Desnky Global',
                'seo_description' => 'Energy services, equipment sourcing and operational support for organizations across Nigeria.',
            ],
            'procurement' => [
                'slug' => 'procurement',
                'title' => 'Procurement Services',
                'icon' => 'PR',
                'summary' => 'General and industrial procurement with sourcing, verification and delivery coordination.',
                'image' => 'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?auto=format&fit=crop&w=1200&q=80',
                'features' => ['Industrial materials sourcing', 'Supplier coordination', 'Price and availability checks', 'Delivery follow-up'],
                'process' => ['Receive RFQ', 'Validate specification', 'Source and quote', 'Deliver and close out'],
                'benefits' => ['Clear procurement process', 'Reduced sourcing burden', 'Accountable communication'],
                'seo_title' => 'Procurement Company in Lagos | Desnky Global',
                'seo_description' => 'Procurement services for industrial materials, safety equipment and business supplies in Lagos and across Nigeria.',
            ],
            'hse-safety' => [
                'slug' => 'hse-safety',
                'title' => 'HSE and Safety Services',
                'icon' => 'HS',
                'summary' => 'Health, safety and environment support, safety materials and compliance-focused advisory.',
                'image' => 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=1200&q=80',
                'features' => ['Safety equipment supply', 'HSE policy support', 'Workplace readiness checks', 'Safety training coordination'],
                'process' => ['Review worksite risks', 'Recommend controls', 'Supply materials', 'Support continuous improvement'],
                'benefits' => ['HSE-first delivery', 'Practical site awareness', 'Compliance-minded documentation'],
                'seo_title' => 'HSE and Safety Services Nigeria | Desnky Global',
                'seo_description' => 'Safety equipment, HSE support and workplace safety services for Nigerian organizations.',
            ],
            'ict-solutions' => [
                'slug' => 'ict-solutions',
                'title' => 'ICT Solutions',
                'icon' => 'IT',
                'summary' => 'ICT infrastructure, business technology support and digital enablement services.',
                'image' => 'https://images.unsplash.com/photo-1518779578993-ec3579fee39f?auto=format&fit=crop&w=1200&q=80',
                'features' => ['Network and systems support', 'Business technology advisory', 'Hardware sourcing', 'Digital workflow support'],
                'process' => ['Assess environment', 'Design solution', 'Deploy equipment', 'Support users'],
                'benefits' => ['Business-focused technology', 'Scalable implementation', 'Responsive support'],
                'seo_title' => 'ICT Solutions Company Nigeria | Desnky Global',
                'seo_description' => 'ICT infrastructure, hardware sourcing and business technology support services in Nigeria.',
            ],
            'agro-food-processing' => [
                'slug' => 'agro-food-processing',
                'title' => 'Agro Products and Food Processing',
                'icon' => 'AG',
                'summary' => 'Agro product sourcing, processing support and supply coordination for food value chains.',
                'image' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=80',
                'features' => ['Agro product supply', 'Food processing support', 'Quality-focused coordination', 'Market linkage support'],
                'process' => ['Confirm product need', 'Source and inspect', 'Coordinate processing', 'Deliver to buyer'],
                'benefits' => ['Agro sector awareness', 'Quality and delivery focus', 'Flexible supply support'],
                'seo_title' => 'Agro Products and Food Processing Nigeria | Desnky',
                'seo_description' => 'Agro products, food processing support and supply coordination from Desnky Global Resources Ltd.',
            ],
        ];
    }

    /**
     * Get CMS services with a static fallback for fresh installations.
     *
     * @return array<string, array<string, mixed>>
     */
    private function publishedServices(): array
    {
        try {
            $rows = $this->serviceService->published();
        } catch (\Throwable) {
            return self::services();
        }

        if (empty($rows)) {
            return self::services();
        }

        $services = [];
        foreach ($rows as $row) {
            $slug = (string) $row['slug'];
            $services[$slug] = [
                'slug' => $slug,
                'title' => (string) $row['title'],
                'icon' => (string) ($row['icon'] ?? 'SR'),
                'summary' => (string) ($row['summary'] ?? ''),
                'image' => (string) ($row['featured_image'] ?? 'https://images.unsplash.com/photo-1581092335878-2d9ff86ca2bf?auto=format&fit=crop&w=1200&q=80'),
                'features' => [],
                'process' => [],
                'benefits' => [],
                'content' => (string) ($row['content'] ?? ''),
                'seo_title' => (string) ($row['meta_title'] ?? $row['title']),
                'seo_description' => (string) ($row['meta_description'] ?? $row['summary'] ?? ''),
            ];
        }

        return $services;
    }
}
