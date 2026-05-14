<?php

namespace Tests\Unit;

use App\Helpers\SeoHelper;
use PHPUnit\Framework\TestCase;

class SeoHelperTest extends TestCase
{
    public function testRenderIncludesCanonicalSocialTagsAndJsonLd(): void
    {
        $html = SeoHelper::render([
            'title' => 'Engineering Services in Nigeria | Desnky Global',
            'description' => 'Electrical, mechanical and industrial engineering support from Desnky Global Resources Ltd for Nigerian businesses.',
            'canonical' => 'https://www.desnkygroup.com/services/engineering',
            'keywords' => 'engineering services Nigeria',
            'schema' => SeoHelper::breadcrumbSchema([
                'Home' => 'https://www.desnkygroup.com/',
                'Engineering' => 'https://www.desnkygroup.com/services/engineering',
            ]),
        ]);

        $this->assertStringContainsString('<link rel="canonical"', $html);
        $this->assertStringContainsString('og:title', $html);
        $this->assertStringContainsString('twitter:card', $html);
        $this->assertStringContainsString('application/ld+json', $html);
        $this->assertStringContainsString('BreadcrumbList', $html);
    }

    public function testFluentBuilderRendersSchemaGraph(): void
    {
        $html = (new SeoHelper())
            ->setTitle('Contact Desnky Global Resources Ltd')
            ->setDescription('Contact Desnky Global Resources Ltd for engineering and procurement enquiries in Nigeria.')
            ->setCanonical('https://www.desnkygroup.com/contact')
            ->addSchema(SeoHelper::organizationSchema())
            ->addSchema(SeoHelper::contactPointSchema())
            ->toHtml();

        $this->assertStringContainsString('@graph', $html);
        $this->assertStringContainsString('ContactPoint', $html);
    }
}
