<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

/**
 * Public legal and advertising compliance pages.
 */
class LegalController extends BaseController
{
    public function privacy(): string
    {
        return $this->legalPage(
            'Privacy Policy',
            'How Desnky Global Resources Ltd handles personal data, analytics, advertising, and communication preferences.',
            [
                ['Information we collect', 'We collect information you submit through contact, newsletter, order, and enquiry forms, plus limited technical information needed to secure and improve the website.'],
                ['How we use information', 'We use submitted information to respond to enquiries, process requested services, operate the website, improve content, prevent abuse, and comply with legal obligations.'],
                ['Advertising and analytics', 'When advertising is enabled, third-party services such as Google AdSense may use cookies or similar technologies to deliver and measure ads after applicable consent choices.'],
                ['Data sharing', 'We do not sell personal information. We may share limited data with service providers that support hosting, email delivery, analytics, advertising, security, or order fulfilment.'],
                ['Your choices', 'You can contact us to request access, correction, deletion, or restriction of personal information where applicable. You can also change cookie and advertising preferences from the cookie banner.'],
                ['Contact', 'For privacy questions, contact us through the public contact page or the email address listed in the site footer.'],
            ]
        );
    }

    public function cookies(): string
    {
        return $this->legalPage(
            'Cookie Policy',
            'Cookie, analytics, and advertising consent information for visitors to Desnky Global Resources Ltd.',
            [
                ['Essential cookies', 'Essential cookies keep the site secure, remember form protection tokens, support sessions, and enable requested functionality. These are required for the website to operate.'],
                ['Advertising cookies', 'Advertising cookies and scripts are optional. AdSense containers are reserved in the layout, but ad scripts are only loaded after advertising consent where required.'],
                ['Analytics cookies', 'Analytics may be used to understand content performance and improve the site. We aim to use privacy-conscious configuration and avoid exposing sensitive visitor information.'],
                ['Changing preferences', 'You can accept or decline optional advertising storage from the consent banner. Clearing browser storage will reset your preference.'],
            ]
        );
    }

    public function terms(): string
    {
        return $this->legalPage(
            'Terms of Use',
            'Terms and conditions for using the Desnky Global Resources Ltd website, blog, shop, and enquiry forms.',
            [
                ['Website content', 'Content is provided for general information about our services, products, and industry topics. It is not a substitute for project-specific professional advice.'],
                ['Acceptable use', 'Do not misuse the website, attempt unauthorized access, submit harmful content, scrape protected areas, or interfere with normal operation.'],
                ['Orders and enquiries', 'Product availability, pricing, delivery, and service scope are confirmed directly with our team. Submitting an enquiry does not create a binding service agreement.'],
                ['Intellectual property', 'Website text, images, layouts, and brand assets are owned by or licensed to Desnky Global Resources Ltd unless otherwise stated.'],
                ['Third-party links', 'The website may link to third-party websites or embedded services. We are not responsible for their content, privacy practices, or availability.'],
                ['Updates', 'We may update these terms as the website, legal requirements, or services change. The latest version is available on this page.'],
            ]
        );
    }

    /**
     * @param array<int, array{0: string, 1: string}> $sections
     */
    private function legalPage(string $title, string $description, array $sections): string
    {
        return $this->view('frontend/pages/legal/show', [
            'title' => $title,
            'active' => '',
            'description' => $description,
            'sections' => $sections,
            'seo' => [
                'title' => $title . ' | Desnky Global Resources Ltd',
                'description' => $description,
                'canonical' => rtrim((string) ($this->siteSettings()['url'] ?? 'https://www.desnkygroup.com'), '/') . ($_SERVER['REQUEST_URI'] ?? ''),
                'robots' => 'index, follow',
            ],
        ]);
    }
}
