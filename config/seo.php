<?php

return [
    'site_name' => env('SEO_SITE_NAME', 'Desnky Global Resources Ltd'),
    'base_url' => rtrim((string) env('SEO_BASE_URL', 'https://www.desnkygroup.com'), '/'),
    'default_title' => 'Desnky Global Resources Ltd | Integrated Nigerian Services',
    'default_description' => 'Desnky Global Resources Ltd delivers energy, engineering, procurement, HSE, ICT and agro solutions for businesses in Nigeria.',
    'default_keywords' => 'engineering company in Nigeria, energy services Nigeria, procurement company Lagos, HSE services Nigeria, ICT solutions Nigeria, agro products Nigeria',
    'default_image' => 'https://www.desnkygroup.com/assets/images/og-default.webp',
    'logo' => 'https://www.desnkygroup.com/assets/images/logo.png',
    'logo_width' => 500,
    'logo_height' => 500,
    'favicon' => 'https://www.desnkygroup.com/favicon-48x48.png',
    'address' => [
        'street' => env('SEO_ADDRESS_STREET', 'Lagos, Nigeria'),
        'locality' => env('SEO_ADDRESS_LOCALITY', 'Lagos'),
        'region' => env('SEO_ADDRESS_REGION', 'Lagos'),
        'country' => env('SEO_ADDRESS_COUNTRY', 'NG'),
    ],
    'contact' => [
        'email' => env('SEO_CONTACT_EMAIL', 'info@desnkygroup.com'),
        'phone' => env('SEO_CONTACT_PHONE', '+234'),
    ],
    'social_profiles' => array_filter(array_map('trim', explode(',', (string) env('SEO_SOCIAL_PROFILES', '')))),
    'analytics' => [
        'ga_measurement_id' => env('GA_MEASUREMENT_ID', ''),
        'google_site_verification' => env('GOOGLE_SITE_VERIFICATION', ''),
        'bing_site_verification' => env('BING_SITE_VERIFICATION', ''),
    ],
];
