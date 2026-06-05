<?php

/**
 * Foundation seeder.
 *
 * Usage: php scripts/seed.php
 */

define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

\App\Config::load(BASE_PATH . '/config');

$connection = new \App\Database\Connection(\App\Config::get('database.connections.mysql'));
$hasher = new \App\Security\Hasher();

$roles = [
    ['Super Admin', 'super-admin', 'Full system access', 1],
    ['Admin', 'admin', 'Content and shop management access', 1],
    ['Editor', 'editor', 'Content management access', 1],
    ['Sales Manager', 'sales-manager', 'Products and orders access', 1],
    ['Support Staff', 'support-staff', 'Enquiry and customer support access', 1],
];

foreach ($roles as $role) {
    $connection->execute(
        "INSERT INTO roles (name, slug, description, is_system_role)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description)",
        $role
    );
}

$permissions = [
    'dashboard.view',
    'pages.view',
    'pages.create',
    'pages.edit',
    'pages.delete',
    'pages.publish',
    'services.view',
    'services.create',
    'services.edit',
    'services.delete',
    'services.publish',
    'projects.view',
    'projects.create',
    'projects.edit',
    'projects.delete',
    'projects.publish',
    'media.view',
    'media.upload',
    'media.edit',
    'media.delete',
    'products.view',
    'products.create',
    'products.edit',
    'products.delete',
    'products.publish',
    'orders.view',
    'orders.edit',
    'orders.update_status',
    'orders.export',
    'enquiries.view',
    'enquiries.reply',
    'enquiries.delete',
    'newsletter.view',
    'newsletter.export',
    'newsletter.delete',
    'settings.view',
    'settings.edit',
    'admins.view',
    'admins.create',
    'admins.edit',
    'admins.delete',
    'admins.assign_roles',
    'admins.reset_password',
    'roles.view',
    'roles.create',
    'roles.edit',
    'roles.delete',
    'roles.assign_permissions',
    'permissions.view',
    'permissions.assign',
    'activity_logs.view',
];

foreach ($permissions as $permission) {
    [$module] = explode('.', $permission);
    $name = ucwords(str_replace(['.', '_'], [' ', ' '], $permission));

    $connection->execute(
        "INSERT INTO permissions (name, slug, module)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE name = VALUES(name), module = VALUES(module)",
        [$name, $permission, $module]
    );
}

$superAdminRole = $connection->queryOne("SELECT id FROM roles WHERE slug = ?", ['super-admin']);
if ($superAdminRole !== null) {
    $connection->execute("
        INSERT IGNORE INTO role_permissions (role_id, permission_id)
        SELECT ?, id FROM permissions
    ", [$superAdminRole['id']]);
}

$settings = [
    ['site.name', 'Desnky Global Resources Ltd', 'string', 1],
    ['site.url', 'https://www.desnkygroup.com/', 'string', 1],
    ['site.email', 'info@desnkygroup.com', 'string', 1],
    ['site.phone', '+2340000000000', 'string', 1],
    ['site.phone_display', '+234 000 000 0000', 'string', 1],
    ['site.whatsapp', '2340000000000', 'string', 1],
    ['site.address', 'Lagos, Nigeria', 'string', 1],
    ['site.hours', 'Mon–Fri, 9:00 AM – 5:00 PM', 'string', 1],
    ['social.linkedin', 'https://www.linkedin.com/company/desnkygroup', 'string', 1],
    ['social.x', 'https://x.com/desnkygroup', 'string', 1],
    ['social.facebook', 'https://www.facebook.com/desnkygroup', 'string', 1],
    ['social.instagram', 'https://www.instagram.com/desnkygroup', 'string', 1],
];

foreach ($settings as $setting) {
    $connection->execute(
        "INSERT INTO site_settings (setting_key, setting_value, setting_type, is_public)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            setting_value = VALUES(setting_value),
            setting_type = VALUES(setting_type),
            is_public = VALUES(is_public)",
        $setting
    );
}

$connection->execute(
    "INSERT INTO admin_users (full_name, email, password_hash, role, is_active)
     VALUES (?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE full_name = VALUES(full_name)",
    [
        'Super Admin',
        'admin@example.com',
        $hasher->make('password'),
        'super_admin',
        1,
    ]
);

$admin = $connection->queryOne("SELECT id FROM admin_users WHERE email = ?", ['admin@example.com']);
$legacyRole = $connection->queryOne("SELECT id FROM roles WHERE slug = ?", ['super-admin']);

if ($admin !== null && $legacyRole !== null) {
    $connection->execute(
        "INSERT IGNORE INTO admin_user_roles (admin_user_id, role_id) VALUES (?, ?)",
        [$admin['id'], $legacyRole['id']]
    );
}

$adminId = (int) ($admin['id'] ?? 1);

$encode = static fn (array $value): string => (string) json_encode(
    $value,
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
);

$homePage = [
    'title' => 'Home',
    'slug' => 'home',
    'content' => 'CMS-managed home page assembled from page sections, services, projects, clients and public site settings.',
    'excerpt' => 'Integrated energy, engineering, procurement, safety, ICT and agro solutions for organizations in Nigeria.',
    'meta_title' => 'Desnky Global Resources Ltd | Integrated Nigerian Services',
    'meta_description' => 'Desnky Global Resources Ltd delivers energy, engineering, procurement, HSE, ICT and agro solutions for businesses in Nigeria.',
    'meta_keywords' => 'engineering company in Nigeria, energy services Nigeria, procurement company Lagos, HSE services Nigeria, ICT solutions Nigeria, agro products Nigeria',
    'featured_image' => 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=1800&q=80',
];

$connection->execute(
    "INSERT INTO pages
        (title, slug, content, excerpt, meta_title, meta_description, meta_keywords, featured_image,
         is_published, published_by, published_at, created_by)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, NOW(), ?)
     ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        content = VALUES(content),
        excerpt = VALUES(excerpt),
        meta_title = VALUES(meta_title),
        meta_description = VALUES(meta_description),
        meta_keywords = VALUES(meta_keywords),
        featured_image = VALUES(featured_image),
        is_published = 1,
        published_by = VALUES(published_by),
        published_at = COALESCE(published_at, NOW())",
    [
        $homePage['title'],
        $homePage['slug'],
        $homePage['content'],
        $homePage['excerpt'],
        $homePage['meta_title'],
        $homePage['meta_description'],
        $homePage['meta_keywords'],
        $homePage['featured_image'],
        $adminId,
        $adminId,
    ]
);

$home = $connection->queryOne("SELECT id FROM pages WHERE slug = ?", ['home']);
$homeId = (int) ($home['id'] ?? 0);

$homeSections = [
    [
        'hero',
        'Integrated Energy, Engineering, Procurement, Safety, ICT and Agro Solutions in Nigeria',
        [
            'eyebrow' => 'Desnky Global Resources Ltd',
            'text' => 'We help organizations source materials, coordinate technical work, improve HSE readiness and support business operations across key industrial and commercial sectors.',
            'image' => 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=1800&q=80',
            'image_alt' => 'Industrial engineering worksite representing Desnky Global Resources services',
            'primary_cta_label' => 'Learn More',
            'primary_cta_url' => '/services',
            'secondary_cta_label' => 'Request a Quote',
            'secondary_cta_url' => '/contact',
        ],
        10,
    ],
    [
        'services_intro',
        'Services built around practical delivery',
        [
            'eyebrow' => 'What we do',
            'item_cta_label' => 'View More',
        ],
        20,
    ],
    [
        'why_choose_us',
        'A dependable partner for multi-sector operations',
        [
            'eyebrow' => 'Why choose us',
            'text' => 'Desnky Global Resources combines sector knowledge, vendor coordination and responsive communication so teams can keep procurement, technical and operational work moving.',
            'benefits' => [
                'Multi-sector experience',
                'Safety-aware execution',
                'Clear procurement follow-up',
                'Responsive project support',
            ],
        ],
        30,
    ],
    [
        'hse_commitment',
        'Safety is part of delivery, not an afterthought',
        [
            'eyebrow' => 'HSE commitment',
            'text' => 'We support safe work through risk awareness, appropriate materials, practical planning and responsible execution across each engagement.',
            'cta_label' => 'Read our HSE policy',
            'cta_url' => '/hse-policy',
            'image' => 'https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?auto=format&fit=crop&w=900&q=80',
            'image_alt' => 'Safety-focused industrial team planning site work',
        ],
        40,
    ],
    [
        'projects_intro',
        'Representative work highlights',
        [
            'eyebrow' => 'Featured projects',
            'cta_label' => 'View Gallery',
            'cta_url' => '/projects',
        ],
        50,
    ],
    [
        'clients',
        'Our Trusted Clients',
        [
            'items' => [
                'Energy Operators',
                'Industrial Teams',
                'Construction Firms',
                'Agro Processors',
            ],
        ],
        60,
    ],
    [
        'cta',
        'Ready to work with us?',
        [
            'text' => 'Send your enquiry and our team will respond with the next practical step.',
            'email_label' => 'Email address',
            'email_placeholder' => 'Email address',
            'newsletter_button_label' => 'Subscribe',
            'contact_label' => 'Contact Us',
            'contact_url' => '/contact',
        ],
        70,
    ],
];

foreach ($homeSections as [$sectionKey, $heading, $body, $sortOrder]) {
    $existingSection = $connection->queryOne(
        "SELECT id FROM page_sections WHERE page_id = ? AND section_key = ?",
        [$homeId, $sectionKey]
    );

    if ($existingSection !== null) {
        $connection->execute(
            "UPDATE page_sections
             SET heading = ?, body = ?, sort_order = ?
             WHERE id = ?",
            [$heading, $encode($body), $sortOrder, $existingSection['id']]
        );
        continue;
    }

    $connection->execute(
        "INSERT INTO page_sections (page_id, section_key, heading, body, sort_order)
         VALUES (?, ?, ?, ?, ?)",
        [$homeId, $sectionKey, $heading, $encode($body), $sortOrder]
    );
}

$aboutPage = [
    'title' => 'About Desnky Global Resources Ltd',
    'slug' => 'about',
    'content' => 'CMS-managed about page assembled from structured page sections.',
    'excerpt' => 'Learn about Desnky Global Resources Ltd, a Nigerian company supporting engineering, energy, procurement, HSE, ICT and agro value-chain needs.',
    'meta_title' => 'About Desnky Global Resources Ltd',
    'meta_description' => 'Learn about Desnky Global Resources Ltd, a Nigerian corporate services company supporting engineering, energy, procurement, HSE, ICT and agro value-chain operations.',
    'meta_keywords' => 'Desnky Global Resources, Nigerian engineering company, procurement company Lagos, energy services Nigeria, HSE services Nigeria',
    'featured_image' => 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1800&q=80',
];

$connection->execute(
    "INSERT INTO pages
        (title, slug, content, excerpt, meta_title, meta_description, meta_keywords, featured_image,
         is_published, published_by, published_at, created_by)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, NOW(), ?)
     ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        content = VALUES(content),
        excerpt = VALUES(excerpt),
        meta_title = VALUES(meta_title),
        meta_description = VALUES(meta_description),
        meta_keywords = VALUES(meta_keywords),
        featured_image = VALUES(featured_image),
        is_published = 1,
        published_by = VALUES(published_by),
        published_at = COALESCE(published_at, NOW())",
    [
        $aboutPage['title'],
        $aboutPage['slug'],
        $aboutPage['content'],
        $aboutPage['excerpt'],
        $aboutPage['meta_title'],
        $aboutPage['meta_description'],
        $aboutPage['meta_keywords'],
        $aboutPage['featured_image'],
        $adminId,
        $adminId,
    ]
);

$about = $connection->queryOne("SELECT id FROM pages WHERE slug = ?", ['about']);
$aboutId = (int) ($about['id'] ?? 0);

$aboutSections = [
    [
        'hero',
        'Built for practical delivery across Nigeria',
        [
            'eyebrow' => 'About Desnky Global Resources Ltd',
            'text' => 'We support organizations that need dependable sourcing, technical coordination, safety awareness and operations support across engineering, energy, procurement, HSE, ICT and agro value chains.',
            'image' => 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1800&q=80',
            'image_alt' => 'Professional operations team in a modern business environment',
            'breadcrumb_home_label' => 'Home',
            'breadcrumb_current_label' => 'About',
        ],
        10,
    ],
    [
        'overview',
        'A multi-sector partner focused on dependable execution',
        [
            'eyebrow' => 'Company profile',
            'paragraphs' => [
                'Desnky Global Resources Ltd is a Nigerian corporate services company helping public and private organizations move critical work from requirement to delivery. Our engagements span technical support, procurement coordination, HSE readiness, ICT enablement and agro-related supply needs.',
                'The company is structured around practical problem solving: clarify the requirement, coordinate the right resources, communicate progress clearly and close each engagement with attention to documentation, quality and safety.',
                'Our team works with industrial operators, construction firms, commercial buyers, energy-sector stakeholders, ICT users and food value-chain participants that need responsive support without unnecessary complexity.',
            ],
        ],
        20,
    ],
    [
        'mission_vision',
        'Mission and vision',
        [
            'mission' => [
                'label' => 'Mission',
                'heading' => 'To deliver reliable multi-sector support that keeps operations moving.',
                'text' => 'We help clients source, plan, coordinate and complete business-critical work with practical communication, safety awareness and accountability.',
            ],
            'vision' => [
                'label' => 'Vision',
                'heading' => 'To be a trusted Nigerian partner for integrated operational services.',
                'text' => 'We aim to be known for dependable delivery, clear follow-through and adaptable service across the industries we support.',
            ],
        ],
        30,
    ],
    [
        'sectors',
        'The sectors we support',
        [
            'eyebrow' => 'Service coverage',
            'text' => 'Our work is organized around complementary sectors so clients can access coordinated support from one responsive partner.',
            'items' => [
                ['code' => 'EN', 'title' => 'Engineering', 'text' => 'Electrical, mechanical and industrial support for maintenance, installation and project coordination needs.'],
                ['code' => 'EG', 'title' => 'Energy', 'text' => 'Equipment sourcing, operational support and logistics coordination for energy-sector and power-related requirements.'],
                ['code' => 'PR', 'title' => 'Procurement', 'text' => 'General and industrial procurement with specification review, supplier communication and delivery follow-up.'],
                ['code' => 'HS', 'title' => 'HSE', 'text' => 'Safety materials, workplace readiness support and practical health, safety and environment coordination.'],
                ['code' => 'IT', 'title' => 'ICT', 'text' => 'Business technology support, infrastructure coordination and digital enablement for growing teams.'],
                ['code' => 'AG', 'title' => 'Agro Value Chain', 'text' => 'Agro product sourcing, processing support and supply coordination for buyers and value-chain participants.'],
            ],
        ],
        40,
    ],
    [
        'operating_model',
        'How we work with clients',
        [
            'eyebrow' => 'Operating model',
            'text' => 'Each engagement follows a clear workflow that keeps requirements, responsibilities, timelines and delivery expectations visible.',
            'steps' => [
                ['title' => 'Understand', 'text' => 'We review the requirement, context, constraints and expected outcome before recommending next steps.'],
                ['title' => 'Plan', 'text' => 'We define scope, resources, sourcing needs and practical delivery milestones.'],
                ['title' => 'Coordinate', 'text' => 'We manage communication with vendors, technical parties and client stakeholders through execution.'],
                ['title' => 'Close out', 'text' => 'We document delivery, confirm outcomes and capture lessons for continuous improvement.'],
            ],
        ],
        50,
    ],
    [
        'values',
        'The principles behind our delivery',
        [
            'eyebrow' => 'Our values',
            'items' => [
                ['title' => 'Reliability', 'text' => 'We prioritize commitments that can be tracked, communicated and delivered responsibly.'],
                ['title' => 'Safety awareness', 'text' => 'We consider people, equipment, environment and worksite realities when supporting execution.'],
                ['title' => 'Clear communication', 'text' => 'We keep clients informed with practical updates and direct follow-up.'],
                ['title' => 'Adaptability', 'text' => 'We respond to changing requirements while keeping scope, quality and accountability visible.'],
            ],
        ],
        60,
    ],
    [
        'stats',
        'Operational focus',
        [
            'items' => [
                ['value' => '6', 'label' => 'Core service sectors'],
                ['value' => '4', 'label' => 'Delivery workflow stages'],
                ['value' => 'NG', 'label' => 'Nigeria-focused operations'],
            ],
        ],
        70,
    ],
    [
        'team',
        'Our Team',
        [
            'eyebrow' => 'Leadership',
            'text' => 'Desnky Global Resources Limited is led by a multidisciplinary team supporting operations, finance, technology, projects and business development.',
            'members' => [
                [
                    'name' => 'Desmond Nkwocha',
                    'role' => 'MD/CEO',
                    'image' => 'https://www.desnkygroup.com/images/1.jpg',
                    'image_alt' => 'Desmond Nkwocha, MD/CEO',
                ],
                [
                    'name' => 'Husteyn Alphat',
                    'role' => 'Project Coordinator',
                    'image' => 'https://www.desnkygroup.com/images/2.jpg',
                    'image_alt' => 'Husteyn Alphat, Project Coordinator',
                ],
                [
                    'name' => 'Patrick Osiegbu',
                    'role' => 'Financial Director',
                    'image' => 'https://www.desnkygroup.com/images/4.jpg',
                    'image_alt' => 'Patrick Osiegbu, Financial Director',
                ],
                [
                    'name' => 'Adeoye Bello',
                    'role' => 'IT Director',
                    'image' => 'https://www.desnkygroup.com/images/3.jpg',
                    'image_alt' => 'Adeoye Bello, IT Director',
                ],
                [
                    'name' => 'Blessing Desmond',
                    'role' => 'Business Director',
                    'image' => 'https://www.desnkygroup.com/images/user1.jpg',
                    'image_alt' => 'Blessing Desmond, Business Director',
                ],
                [
                    'name' => 'Virenra Bhardwai',
                    'role' => 'Executive Director',
                    'image' => 'https://www.desnkygroup.com/images/user1.jpg',
                    'image_alt' => 'Virenra Bhardwai, Executive Director',
                ],
                [
                    'name' => 'Charles Mofunanya',
                    'role' => 'Project Director',
                    'image' => 'https://www.desnkygroup.com/images/user1.jpg',
                    'image_alt' => 'Charles Mofunanya, Project Director',
                ],
            ],
        ],
        75,
    ],
    [
        'cta',
        'Start a practical conversation',
        [
            'text' => 'Share your requirement and our team will review the best way to support your sourcing, technical, safety, ICT or agro-related need.',
            'primary_cta_label' => 'Contact Us',
            'primary_cta_url' => '/contact',
        ],
        80,
    ],
];

foreach ($aboutSections as [$sectionKey, $heading, $body, $sortOrder]) {
    $existingSection = $connection->queryOne(
        "SELECT id FROM page_sections WHERE page_id = ? AND section_key = ?",
        [$aboutId, $sectionKey]
    );

    if ($existingSection !== null) {
        $connection->execute(
            "UPDATE page_sections
             SET heading = ?, body = ?, sort_order = ?
             WHERE id = ?",
            [$heading, $encode($body), $sortOrder, $existingSection['id']]
        );
        continue;
    }

    $connection->execute(
        "INSERT INTO page_sections (page_id, section_key, heading, body, sort_order)
         VALUES (?, ?, ?, ?, ?)",
        [$aboutId, $sectionKey, $heading, $encode($body), $sortOrder]
    );
}

$hsePage = [
    'title' => 'Corporate Health, Safety and Environment (HSE) Policy',
    'slug' => 'hse-policy',
    'content' => 'CMS-managed HSE policy page assembled from structured page sections.',
    'excerpt' => 'Read the Desnky Global Resources Limited corporate Health, Safety and Environment policy for safe, responsible and environmentally sustainable operations.',
    'meta_title' => 'Corporate HSE Policy | Desnky Global Resources Limited',
    'meta_description' => 'Read the Desnky Global Resources Limited corporate Health, Safety and Environment policy covering compliance, PPE, training, zero-compromise safety and environmental stewardship.',
    'meta_keywords' => 'Corporate HSE policy Nigeria, health safety environment policy, workplace safety Nigeria, environmental stewardship, Desnky Global Resources Limited',
    'featured_image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=1800&q=80',
];

$connection->execute(
    "INSERT INTO pages
        (title, slug, content, excerpt, meta_title, meta_description, meta_keywords, featured_image,
         is_published, published_by, published_at, created_by)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, NOW(), ?)
     ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        content = VALUES(content),
        excerpt = VALUES(excerpt),
        meta_title = VALUES(meta_title),
        meta_description = VALUES(meta_description),
        meta_keywords = VALUES(meta_keywords),
        featured_image = VALUES(featured_image),
        is_published = 1,
        published_by = VALUES(published_by),
        published_at = COALESCE(published_at, NOW())",
    [
        $hsePage['title'],
        $hsePage['slug'],
        $hsePage['content'],
        $hsePage['excerpt'],
        $hsePage['meta_title'],
        $hsePage['meta_description'],
        $hsePage['meta_keywords'],
        $hsePage['featured_image'],
        $adminId,
        $adminId,
    ]
);

$hse = $connection->queryOne("SELECT id FROM pages WHERE slug = ?", ['hse-policy']);
$hseId = (int) ($hse['id'] ?? 0);

$hseSections = [
    [
        'hero',
        'Corporate Health, Safety and Environment (HSE) Policy',
        [
            'eyebrow' => 'HSE Policy',
            'text' => 'Desnky Global Resources Limited is committed to safe, responsible and environmentally sustainable business operations across every area of our work.',
            'image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=1800&q=80',
            'image_alt' => 'Safety equipment and industrial worksite preparation',
            'breadcrumb_home_label' => 'Home',
            'breadcrumb_current_label' => 'HSE Policy',
        ],
        10,
    ],
    [
        'policy_statement',
        'Our corporate HSE commitment',
        [
            'eyebrow' => 'Policy statement',
            'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1200&q=80',
            'image_alt' => 'Industrial team reviewing safety planning documents',
            'paragraphs' => [
                'At Desnky Global Resources Limited, we are fully committed to protecting the health, safety, and well-being of our employees, contractors, clients, stakeholders, host communities, and the environment in which we operate.',
                'We recognize that effective Health, Safety, and Environmental (HSE) management is fundamental to the success and sustainability of our business operations.',
            ],
        ],
        20,
    ],
    [
        'objective',
        'Our HSE objective',
        [
            'eyebrow' => 'Objective',
            'text' => 'Our objective is to conduct all business activities in a safe, responsible, and environmentally sustainable manner while complying with all applicable local, national, and international laws, regulations, standards, and industry best practices governing our operations.',
            'image' => 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=1200&q=80',
            'image_alt' => 'Industrial site representing responsible operations and compliance',
        ],
        30,
    ],
    [
        'management_commitments',
        'Management shall ensure the following',
        [
            'eyebrow' => 'Management commitments',
            'text' => 'To achieve this commitment, Management of Desnky Global Resources Limited shall ensure the following:',
            'image' => 'https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?auto=format&fit=crop&w=1200&q=80',
            'image_alt' => 'Safety-focused team coordinating worksite responsibilities',
            'items' => [
                ['title' => 'Integrate HSE into operations', 'text' => 'Integrate Health, Safety, and Environmental requirements into every stage of our business and operational processes, including project bidding and tendering, planning, design, procurement, recruitment, promotion, project execution, operations, and maintenance activities. All activities shall align with and support our HSE objectives and standards.'],
                ['title' => 'Ensure compliance', 'text' => 'Ensure full compliance with all applicable local, state, federal, and international HSE laws, regulations, codes, and statutory requirements relevant to our operations.'],
                ['title' => 'Provide resources and PPE', 'text' => 'Provide adequate and timely resources, tools, personal protective equipment (PPE), welfare facilities, and emergency response arrangements necessary for employees and contractors to perform their duties safely and effectively.'],
                ['title' => 'Promote competence and awareness', 'text' => 'Promote continuous training, retraining, competency development, and HSE awareness for all employees to ensure that only qualified and competent personnel undertake assigned tasks and responsibilities.'],
                ['title' => 'Maintain zero compromise', 'text' => 'Maintain a zero-compromise approach toward health, safety, and environmental standards. Any operation or activity identified as unsafe shall be suspended immediately until adequate control measures are implemented to ensure safe execution.'],
                ['title' => 'Protect the environment', 'text' => 'Utilize environmentally responsible machinery, equipment, materials, and chemicals in order to minimize pollution, prevent spills, reduce emissions, and protect the environment. Appropriate emergency preparedness and spill containment measures shall be established and maintained at all operational locations.'],
                ['title' => 'Encourage participation', 'text' => 'Encourage open communication, consultation, and active participation across all levels of the organization by engaging employees, contractors, clients, and stakeholders in HSE-related discussions, feedback, and decision-making processes.'],
                ['title' => 'Set measurable objectives', 'text' => 'Establish measurable and achievable Health, Safety, and Environmental objectives aimed at eliminating hazards, reducing risks, preventing incidents, and continually improving HSE performance. These objectives shall be reviewed periodically to ensure effectiveness and continuous improvement.'],
            ],
        ],
        40,
    ],
    [
        'communication',
        'Policy communication and accessibility',
        [
            'eyebrow' => 'Communication',
            'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=80',
            'image_alt' => 'Team consultation meeting for policy communication',
            'text' => 'This Health, Safety, and Environment Policy shall be communicated to all employees, contractors, and stakeholders, and prominently displayed at all offices, project sites, and business premises of the company. Where necessary, the policy shall be translated into relevant languages to ensure proper understanding and compliance.',
        ],
        50,
    ],
    [
        'culture',
        'Management commitment to HSE culture',
        [
            'eyebrow' => 'HSE culture',
            'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1200&q=80',
            'image_alt' => 'Modern business premises representing operational excellence',
            'text' => 'Management remains committed to fostering a strong HSE culture that prioritizes safety, environmental stewardship, operational excellence, and continuous improvement across all areas of our business activities.',
        ],
        60,
    ],
    [
        'cta',
        'Need HSE-aware project or procurement support?',
        [
            'text' => 'Send your requirement and our team will review the practical safety, sourcing and delivery considerations for your engagement.',
            'primary_cta_label' => 'Contact Us',
            'primary_cta_url' => '/contact',
        ],
        70,
    ],
];

foreach ($hseSections as [$sectionKey, $heading, $body, $sortOrder]) {
    $existingSection = $connection->queryOne(
        "SELECT id FROM page_sections WHERE page_id = ? AND section_key = ?",
        [$hseId, $sectionKey]
    );

    if ($existingSection !== null) {
        $connection->execute(
            "UPDATE page_sections
             SET heading = ?, body = ?, sort_order = ?
             WHERE id = ?",
            [$heading, $encode($body), $sortOrder, $existingSection['id']]
        );
        continue;
    }

    $connection->execute(
        "INSERT INTO page_sections (page_id, section_key, heading, body, sort_order)
         VALUES (?, ?, ?, ?, ?)",
        [$hseId, $sectionKey, $heading, $encode($body), $sortOrder]
    );
}

$servicesPage = [
    'title' => 'Services',
    'slug' => 'services',
    'content' => 'CMS-managed services landing page assembled from page sections and published service records.',
    'excerpt' => 'Explore Desnky Global Resources services in engineering, energy, procurement, HSE, ICT and agro food processing across Nigeria.',
    'meta_title' => 'Services | Desnky Global Resources Ltd',
    'meta_description' => 'Explore Desnky Global Resources services in engineering, energy, procurement, HSE, ICT and agro food processing across Nigeria.',
    'meta_keywords' => 'engineering services Nigeria, energy solutions Nigeria, procurement services Lagos, HSE safety services Nigeria, ICT solutions Nigeria',
    'featured_image' => 'https://images.unsplash.com/photo-1581092335878-2d9ff86ca2bf?auto=format&fit=crop&w=1600&q=80',
];

$connection->execute(
    "INSERT INTO pages
        (title, slug, content, excerpt, meta_title, meta_description, meta_keywords, featured_image,
         is_published, published_by, published_at, created_by)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, NOW(), ?)
     ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        content = VALUES(content),
        excerpt = VALUES(excerpt),
        meta_title = VALUES(meta_title),
        meta_description = VALUES(meta_description),
        meta_keywords = VALUES(meta_keywords),
        featured_image = VALUES(featured_image),
        is_published = 1,
        published_by = VALUES(published_by),
        published_at = COALESCE(published_at, NOW())",
    [
        $servicesPage['title'],
        $servicesPage['slug'],
        $servicesPage['content'],
        $servicesPage['excerpt'],
        $servicesPage['meta_title'],
        $servicesPage['meta_description'],
        $servicesPage['meta_keywords'],
        $servicesPage['featured_image'],
        $adminId,
        $adminId,
    ]
);

$servicesPageRow = $connection->queryOne("SELECT id FROM pages WHERE slug = ?", ['services']);
$servicesPageId = (int) ($servicesPageRow['id'] ?? 0);

$servicePageSections = [
    [
        'hero',
        'Our Services',
        [
            'text' => 'Explore our core service areas across engineering, energy, procurement, HSE, ICT and agro food processing.',
            'breadcrumb_home_label' => 'Home',
            'breadcrumb_current_label' => 'Services',
        ],
        10,
    ],
    [
        'listing',
        'Service Directory',
        [
            'filters_label' => 'Service categories',
            'all_services_label' => 'All Services',
            'card_cta_label' => 'Learn More',
        ],
        20,
    ],
    [
        'detail',
        'Service Detail',
        [
            'breadcrumb_home_label' => 'Home',
            'breadcrumb_services_label' => 'Services',
            'primary_cta_label' => 'Discuss This Service',
            'primary_cta_url' => '/contact',
            'features_heading' => 'Service features',
            'feature_label' => 'Feature',
            'process_heading' => 'Our process',
            'benefits_heading' => 'Why choose this service',
            'secondary_cta_label' => 'Request a Quote',
            'secondary_cta_url' => '/contact',
            'related_heading' => 'Related services',
        ],
        30,
    ],
];

foreach ($servicePageSections as [$sectionKey, $heading, $body, $sortOrder]) {
    $existingSection = $connection->queryOne(
        "SELECT id FROM page_sections WHERE page_id = ? AND section_key = ?",
        [$servicesPageId, $sectionKey]
    );

    if ($existingSection !== null) {
        $connection->execute(
            "UPDATE page_sections
             SET heading = ?, body = ?, sort_order = ?
             WHERE id = ?",
            [$heading, $encode($body), $sortOrder, $existingSection['id']]
        );
        continue;
    }

    $connection->execute(
        "INSERT INTO page_sections (page_id, section_key, heading, body, sort_order)
         VALUES (?, ?, ?, ?, ?)",
        [$servicesPageId, $sectionKey, $heading, $encode($body), $sortOrder]
    );
}

$services = [
    [
        'Engineering Services',
        'engineering',
        'Electrical, mechanical and industrial engineering support for reliable operations.',
        $encode([
            'overview' => 'Our engineering support helps teams plan, coordinate and close out practical electrical, mechanical and industrial work with attention to site realities, documentation and safety controls.',
            'features' => ['Electrical installation support', 'Mechanical maintenance coordination', 'Industrial project support', 'Instrumentation and calibration assistance'],
            'process' => ['Assess requirements', 'Plan scope and resources', 'Execute with safety controls', 'Document completion'],
            'benefits' => ['Practical technical planning', 'Experienced vendor coordination', 'Strong safety discipline'],
        ]),
        'EN',
        'Engineering',
        'https://images.unsplash.com/photo-1581092335878-2d9ff86ca2bf?auto=format&fit=crop&w=1200&q=80',
        1,
        10,
        'Engineering Services in Nigeria | Desnky Global',
        'Electrical, mechanical and industrial engineering support from Desnky Global Resources Ltd for Nigerian businesses.',
        'engineering services Nigeria, industrial engineering support, electrical maintenance Nigeria',
    ],
    [
        'Energy Solutions',
        'energy-solutions',
        'Energy sector support, equipment sourcing and operational services for business continuity.',
        $encode([
            'overview' => 'We support energy-sector operators and business teams with equipment sourcing, logistics coordination and operational follow-up that keeps continuity needs visible from request to delivery.',
            'features' => ['Oil and gas support services', 'Power equipment procurement', 'Energy project logistics', 'Operations support'],
            'process' => ['Define need', 'Source equipment', 'Coordinate delivery', 'Support operations'],
            'benefits' => ['Reliable sector knowledge', 'Vendor and logistics control', 'Delivery-focused execution'],
        ]),
        'EG',
        'Energy',
        'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?auto=format&fit=crop&w=1200&q=80',
        1,
        20,
        'Energy Solutions in Nigeria | Desnky Global',
        'Energy services, equipment sourcing and operational support for organizations across Nigeria.',
        'energy services Nigeria, power equipment procurement, oil and gas support',
    ],
    [
        'Procurement Services',
        'procurement',
        'General and industrial procurement with sourcing, verification and delivery coordination.',
        $encode([
            'overview' => 'Our procurement service reduces sourcing burden by coordinating specifications, supplier communication, availability checks and delivery updates for business-critical materials.',
            'features' => ['Industrial materials sourcing', 'Supplier coordination', 'Price and availability checks', 'Delivery follow-up'],
            'process' => ['Receive RFQ', 'Validate specification', 'Source and quote', 'Deliver and close out'],
            'benefits' => ['Clear procurement process', 'Reduced sourcing burden', 'Accountable communication'],
        ]),
        'PR',
        'Procurement',
        'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?auto=format&fit=crop&w=1200&q=80',
        1,
        30,
        'Procurement Company in Lagos | Desnky Global',
        'Procurement services for industrial materials, safety equipment and business supplies in Lagos and across Nigeria.',
        'procurement company Lagos, industrial procurement Nigeria, supplier coordination',
    ],
    [
        'HSE and Safety Services',
        'hse-safety',
        'Health, safety and environment support, safety materials and compliance-focused advisory.',
        $encode([
            'overview' => 'We help organizations improve workplace readiness through safety equipment supply, HSE support and practical coordination aligned with responsible operations.',
            'features' => ['Safety equipment supply', 'HSE policy support', 'Workplace readiness checks', 'Safety training coordination'],
            'process' => ['Review worksite risks', 'Recommend controls', 'Supply materials', 'Support continuous improvement'],
            'benefits' => ['HSE-first delivery', 'Practical site awareness', 'Compliance-minded documentation'],
        ]),
        'HS',
        'HSE',
        'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=1200&q=80',
        1,
        40,
        'HSE and Safety Services Nigeria | Desnky Global',
        'Safety equipment, HSE support and workplace safety services for Nigerian organizations.',
        'HSE services Nigeria, safety equipment supply, workplace safety',
    ],
    [
        'ICT Solutions',
        'ict-solutions',
        'ICT infrastructure, business technology support and digital enablement services.',
        $encode([
            'overview' => 'Our ICT support covers practical business technology needs, from infrastructure advice and hardware sourcing to deployment coordination and user support.',
            'features' => ['Network and systems support', 'Business technology advisory', 'Hardware sourcing', 'Digital workflow support'],
            'process' => ['Assess environment', 'Design solution', 'Deploy equipment', 'Support users'],
            'benefits' => ['Business-focused technology', 'Scalable implementation', 'Responsive support'],
        ]),
        'IT',
        'ICT',
        'https://images.unsplash.com/photo-1518779578993-ec3579fee39f?auto=format&fit=crop&w=1200&q=80',
        1,
        50,
        'ICT Solutions Company Nigeria | Desnky Global',
        'ICT infrastructure, hardware sourcing and business technology support services in Nigeria.',
        'ICT solutions Nigeria, business technology support, hardware sourcing',
    ],
    [
        'Agro Products and Food Processing',
        'agro-food-processing',
        'Agro product sourcing, processing support and supply coordination for food value chains.',
        $encode([
            'overview' => 'We coordinate agro product sourcing and processing support with attention to quality, buyer requirements and practical delivery timelines within local value chains.',
            'features' => ['Agro product supply', 'Food processing support', 'Quality-focused coordination', 'Market linkage support'],
            'process' => ['Confirm product need', 'Source and inspect', 'Coordinate processing', 'Deliver to buyer'],
            'benefits' => ['Agro sector awareness', 'Quality and delivery focus', 'Flexible supply support'],
        ]),
        'AG',
        'Agro',
        'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=80',
        1,
        60,
        'Agro Products and Food Processing Nigeria | Desnky',
        'Agro products, food processing support and supply coordination from Desnky Global Resources Ltd.',
        'agro products Nigeria, food processing support, agro supply coordination',
    ],
];

foreach ($services as $service) {
    $connection->execute(
        "INSERT INTO services
            (title, slug, summary, content, icon, category, featured_image, is_published, sort_order,
             meta_title, meta_description, meta_keywords)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            summary = VALUES(summary),
            content = VALUES(content),
            icon = VALUES(icon),
            category = VALUES(category),
            featured_image = VALUES(featured_image),
            is_published = VALUES(is_published),
            sort_order = VALUES(sort_order),
            meta_title = VALUES(meta_title),
            meta_description = VALUES(meta_description),
            meta_keywords = VALUES(meta_keywords)",
        $service
    );
}

$projectsPage = [
    'title' => 'Projects and Gallery',
    'slug' => 'projects',
    'content' => 'CMS-managed projects landing page assembled from page sections and published project records.',
    'excerpt' => 'A representative gallery of sectors supported by Desnky Global Resources, including engineering, HSE, procurement and agro supply coordination.',
    'meta_title' => 'Projects and Gallery | Desnky Global Resources',
    'meta_description' => 'View representative project and gallery highlights from Desnky Global Resources across engineering, HSE, procurement and agro sectors.',
    'meta_keywords' => 'Desnky projects, engineering gallery Nigeria, procurement projects, HSE projects Nigeria',
    'featured_image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1600&q=80',
];

$connection->execute(
    "INSERT INTO pages
        (title, slug, content, excerpt, meta_title, meta_description, meta_keywords, featured_image,
         is_published, published_by, published_at, created_by)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, NOW(), ?)
     ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        content = VALUES(content),
        excerpt = VALUES(excerpt),
        meta_title = VALUES(meta_title),
        meta_description = VALUES(meta_description),
        meta_keywords = VALUES(meta_keywords),
        featured_image = VALUES(featured_image),
        is_published = 1,
        published_by = VALUES(published_by),
        published_at = COALESCE(published_at, NOW())",
    [
        $projectsPage['title'],
        $projectsPage['slug'],
        $projectsPage['content'],
        $projectsPage['excerpt'],
        $projectsPage['meta_title'],
        $projectsPage['meta_description'],
        $projectsPage['meta_keywords'],
        $projectsPage['featured_image'],
        $adminId,
        $adminId,
    ]
);

$projectsPageRow = $connection->queryOne("SELECT id FROM pages WHERE slug = ?", ['projects']);
$projectsPageId = (int) ($projectsPageRow['id'] ?? 0);

$projectPageSections = [
    [
        'hero',
        'Projects and Gallery',
        [
            'text' => 'A representative gallery of the sectors we support, including engineering, HSE, procurement and agro supply coordination.',
            'breadcrumb_home_label' => 'Home',
            'breadcrumb_current_label' => 'Projects',
        ],
        10,
    ],
    [
        'listing',
        'Project Directory',
        [
            'search_label' => 'Search projects',
            'search_placeholder' => 'Search projects',
            'filters_label' => 'Project categories',
            'all_categories_label' => 'All',
            'card_cta_label' => 'View Project',
        ],
        20,
    ],
    [
        'detail',
        'Project Detail',
        [
            'breadcrumb_home_label' => 'Home',
            'breadcrumb_projects_label' => 'Projects',
            'overview_heading' => 'Project overview',
            'highlights_heading' => 'Scope highlights',
            'highlight_label' => 'Highlight',
            'outcomes_heading' => 'Delivery outcomes',
            'summary_heading' => 'Project summary',
            'category_label' => 'Category',
            'client_label' => 'Client',
            'date_label' => 'Project date',
            'cta_label' => 'Discuss a Similar Project',
            'cta_url' => '/contact',
        ],
        30,
    ],
];

foreach ($projectPageSections as [$sectionKey, $heading, $body, $sortOrder]) {
    $existingSection = $connection->queryOne(
        "SELECT id FROM page_sections WHERE page_id = ? AND section_key = ?",
        [$projectsPageId, $sectionKey]
    );

    if ($existingSection !== null) {
        $connection->execute(
            "UPDATE page_sections
             SET heading = ?, body = ?, sort_order = ?
             WHERE id = ?",
            [$heading, $encode($body), $sortOrder, $existingSection['id']]
        );
        continue;
    }

    $connection->execute(
        "INSERT INTO page_sections (page_id, section_key, heading, body, sort_order)
         VALUES (?, ?, ?, ?, ?)",
        [$projectsPageId, $sectionKey, $heading, $encode($body), $sortOrder]
    );
}

$projects = [
    [
        'Industrial Electrical Support',
        'industrial-electrical-support',
        'Electrical installation and maintenance support for industrial operations.',
        $encode([
            'overview' => 'Representative electrical support work covering site coordination, materials readiness and technical close-out for industrial operating environments.',
            'highlights' => ['Installation support planning', 'Electrical materials readiness', 'Maintenance coordination', 'Completion documentation'],
            'outcomes' => ['Improved readiness for site work', 'Clearer vendor and team coordination', 'Documented handover for follow-up'],
        ]),
        'Engineering',
        'Industrial Operations Team',
        '2026-01-15',
        'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=900&q=80',
        1,
        10,
        'Industrial Electrical Support | Desnky Projects',
        'Electrical installation and maintenance support for industrial operations by Desnky Global Resources.',
        'industrial electrical support, engineering project Nigeria, Desnky projects',
    ],
    [
        'Procurement and Supply Coordination',
        'procurement-and-supply-coordination',
        'Sourcing, vendor coordination and delivery support for technical materials.',
        $encode([
            'overview' => 'Procurement coordination for technical materials with specification checks, supplier follow-up and delivery tracking from RFQ through close-out.',
            'highlights' => ['Specification review', 'Supplier communication', 'Availability checks', 'Delivery tracking'],
            'outcomes' => ['Reduced sourcing friction', 'Improved procurement visibility', 'Accountable delivery updates'],
        ]),
        'Procurement',
        'Technical Materials Buyer',
        '2026-02-10',
        'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=900&q=80',
        1,
        20,
        'Procurement and Supply Coordination | Desnky Projects',
        'Sourcing, vendor coordination and delivery support for technical materials by Desnky Global Resources.',
        'procurement coordination, technical materials sourcing, supply support Nigeria',
    ],
    [
        'Safety Equipment Deployment',
        'safety-equipment-deployment',
        'HSE equipment supply and workplace safety readiness support.',
        $encode([
            'overview' => 'Safety materials deployment with workplace readiness support, practical HSE coordination and responsible delivery follow-up.',
            'highlights' => ['PPE and safety materials supply', 'Readiness checks', 'HSE documentation support', 'Delivery coordination'],
            'outcomes' => ['Better workplace safety preparedness', 'Improved material availability', 'Practical support for responsible operations'],
        ]),
        'HSE',
        'Safety Operations Team',
        '2026-03-05',
        'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=900&q=80',
        1,
        30,
        'Safety Equipment Deployment | Desnky Projects',
        'HSE equipment supply and workplace safety readiness support by Desnky Global Resources.',
        'safety equipment supply, HSE project Nigeria, workplace safety readiness',
    ],
    [
        'Agro Supply Coordination',
        'agro-supply-coordination',
        'Agro product sourcing and supply coordination for local value chains.',
        $encode([
            'overview' => 'Agro supply coordination covering source verification, quality checks, processing support and buyer delivery coordination.',
            'highlights' => ['Product need confirmation', 'Source verification', 'Quality checks', 'Buyer delivery support'],
            'outcomes' => ['Improved buyer-supplier coordination', 'Better quality visibility', 'More predictable supply handoff'],
        ]),
        'Agro',
        'Food Value Chain Buyer',
        '2026-04-12',
        'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=900&q=80',
        1,
        40,
        'Agro Supply Coordination | Desnky Projects',
        'Agro product sourcing and supply coordination for local value chains by Desnky Global Resources.',
        'agro supply coordination, food value chain Nigeria, agro products',
    ],
];

foreach ($projects as $project) {
    $connection->execute(
        "INSERT INTO projects
            (title, slug, summary, description, category, client_name, project_date, featured_image,
             is_published, sort_order, meta_title, meta_description, meta_keywords)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            summary = VALUES(summary),
            description = VALUES(description),
            category = VALUES(category),
            client_name = VALUES(client_name),
            project_date = VALUES(project_date),
            featured_image = VALUES(featured_image),
            is_published = VALUES(is_published),
            sort_order = VALUES(sort_order),
            meta_title = VALUES(meta_title),
            meta_description = VALUES(meta_description),
            meta_keywords = VALUES(meta_keywords)",
        $project
    );
}

$shopPages = [
    'shop' => [
        'title' => 'Shop',
        'content' => 'CMS-managed shop landing page assembled from page sections, product categories and active products.',
        'excerpt' => 'Browse safety equipment, ICT devices, industrial supplies and agro product options from Desnky Global Resources.',
        'meta_title' => 'Shop Safety, ICT and Industrial Products | Desnky',
        'meta_description' => 'Browse safety equipment, ICT devices, agro products and industrial supplies from Desnky Global Resources Ltd.',
        'meta_keywords' => 'safety equipment Nigeria, ICT devices Nigeria, industrial supplies Lagos, agro products Nigeria',
        'featured_image' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=1600&q=80',
        'sections' => [
            [
                'hero',
                'Shop',
                [
                    'text' => 'Browse safety equipment, ICT devices, industrial supplies and agro product options.',
                    'breadcrumb_home_label' => 'Home',
                    'breadcrumb_current_label' => 'Shop',
                ],
                10,
            ],
            [
                'listing',
                'Product Listing',
                [
                    'search_label' => 'Search products',
                    'search_placeholder' => 'Search by name or SKU',
                    'categories_heading' => 'Categories',
                    'all_products_label' => 'All products',
                    'count_label' => '{count} products available',
                    'sort_label' => 'Sort products',
                    'sort_name_label' => 'Sort by name',
                    'sort_price_low_label' => 'Price: low to high',
                    'sort_price_high_label' => 'Price: high to low',
                    'currency_label' => 'NGN',
                    'in_stock_label' => 'In stock',
                    'out_of_stock_label' => 'Out of stock',
                    'view_label' => 'View',
                    'add_to_cart_label' => 'Add to Cart',
                ],
                20,
            ],
            [
                'product_detail',
                'Product Detail',
                [
                    'breadcrumb_home_label' => 'Home',
                    'breadcrumb_shop_label' => 'Shop',
                    'gallery_image_label' => 'gallery image',
                    'currency_label' => 'NGN',
                    'in_stock_label' => 'In stock: {stock} available',
                    'out_of_stock_label' => 'Out of stock',
                    'quantity_label' => 'Quantity',
                    'add_to_cart_label' => 'Add to Cart',
                    'details_heading' => 'Product details',
                    'sku_label' => 'SKU',
                    'category_label' => 'Category',
                    'payment_label' => 'Payment',
                    'payment_value' => 'Bank transfer or pay on delivery',
                    'related_heading' => 'Related products',
                ],
                30,
            ],
        ],
    ],
    'shop-cart' => [
        'title' => 'Shopping Cart',
        'content' => 'CMS-managed shopping cart page labels and summary copy.',
        'excerpt' => 'Review selected products before checkout on the Desnky Global Resources shop.',
        'meta_title' => 'Shopping Cart | Desnky Shop',
        'meta_description' => 'Review selected products before checkout on the Desnky Global Resources shop.',
        'meta_keywords' => 'shopping cart, Desnky shop, Nigeria business supplies',
        'featured_image' => null,
        'sections' => [
            [
                'hero',
                'Shopping Cart',
                [
                    'text' => 'Review your selected products before checkout.',
                ],
                10,
            ],
            [
                'cart',
                'Cart Content',
                [
                    'empty_heading' => 'Your cart is empty',
                    'empty_text' => 'Browse the shop to add safety, ICT, industrial or agro products.',
                    'continue_shopping_label' => 'Continue Shopping',
                    'product_heading' => 'Product',
                    'price_heading' => 'Price',
                    'quantity_heading' => 'Quantity',
                    'subtotal_heading' => 'Subtotal',
                    'currency_label' => 'NGN',
                    'update_cart_label' => 'Update Cart',
                    'summary_heading' => 'Cart Summary',
                    'subtotal_label' => 'Subtotal',
                    'shipping_label' => 'Shipping estimate',
                    'total_label' => 'Total',
                    'checkout_label' => 'Proceed to Checkout',
                ],
                20,
            ],
        ],
    ],
    'shop-checkout' => [
        'title' => 'Checkout',
        'content' => 'CMS-managed checkout page labels, payment options and order review copy.',
        'excerpt' => 'Complete your Desnky Global Resources order with delivery details and payment preference.',
        'meta_title' => 'Checkout | Desnky Shop',
        'meta_description' => 'Complete your Desnky Global Resources order with delivery details and payment preference.',
        'meta_keywords' => 'checkout, Desnky shop, order delivery Nigeria',
        'featured_image' => null,
        'sections' => [
            [
                'hero',
                'Checkout',
                [
                    'text' => 'Enter delivery details and choose your payment method.',
                ],
                10,
            ],
            [
                'checkout',
                'Checkout Content',
                [
                    'full_name_label' => 'Full Name *',
                    'email_label' => 'Email *',
                    'phone_label' => 'Phone *',
                    'city_state_label' => 'City / State *',
                    'delivery_address_label' => 'Delivery Address *',
                    'payment_method_label' => 'Payment Method *',
                    'payment_method_placeholder' => 'Choose payment method',
                    'payment_methods' => [
                        ['value' => 'bank_transfer', 'label' => 'Manual bank transfer'],
                        ['value' => 'pay_on_delivery', 'label' => 'Pay on delivery'],
                    ],
                    'order_notes_label' => 'Order Notes',
                    'submit_label' => 'Confirm Order',
                    'review_heading' => 'Order Review',
                    'empty_text' => 'Your cart is empty.',
                    'return_to_shop_label' => 'Return to Shop',
                    'quantity_separator' => 'x',
                    'currency_label' => 'NGN',
                    'subtotal_label' => 'Subtotal',
                    'shipping_label' => 'Shipping estimate',
                    'total_label' => 'Total',
                ],
                20,
            ],
        ],
    ],
];

foreach ($shopPages as $slug => $shopPage) {
    $connection->execute(
        "INSERT INTO pages
            (title, slug, content, excerpt, meta_title, meta_description, meta_keywords, featured_image,
             is_published, published_by, published_at, created_by)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, NOW(), ?)
         ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            content = VALUES(content),
            excerpt = VALUES(excerpt),
            meta_title = VALUES(meta_title),
            meta_description = VALUES(meta_description),
            meta_keywords = VALUES(meta_keywords),
            featured_image = VALUES(featured_image),
            is_published = 1,
            published_by = VALUES(published_by),
            published_at = COALESCE(published_at, NOW())",
        [
            $shopPage['title'],
            $slug,
            $shopPage['content'],
            $shopPage['excerpt'],
            $shopPage['meta_title'],
            $shopPage['meta_description'],
            $shopPage['meta_keywords'],
            $shopPage['featured_image'],
            $adminId,
            $adminId,
        ]
    );

    $pageRow = $connection->queryOne("SELECT id FROM pages WHERE slug = ?", [$slug]);
    $pageId = (int) ($pageRow['id'] ?? 0);

    foreach ($shopPage['sections'] as [$sectionKey, $heading, $body, $sortOrder]) {
        $existingSection = $connection->queryOne(
            "SELECT id FROM page_sections WHERE page_id = ? AND section_key = ?",
            [$pageId, $sectionKey]
        );

        if ($existingSection !== null) {
            $connection->execute(
                "UPDATE page_sections
                 SET heading = ?, body = ?, sort_order = ?
                 WHERE id = ?",
                [$heading, $encode($body), $sortOrder, $existingSection['id']]
            );
            continue;
        }

        $connection->execute(
            "INSERT INTO page_sections (page_id, section_key, heading, body, sort_order)
             VALUES (?, ?, ?, ?, ?)",
            [$pageId, $sectionKey, $heading, $encode($body), $sortOrder]
        );
    }
}

$productCategories = [
    ['Safety Equipment', 'safety-equipment', 'Protective equipment and HSE materials for site and field teams.', null, 'https://images.unsplash.com/photo-1591101501707-1264a4b14500?auto=format&fit=crop&w=900&q=80', 10, 'Safety Equipment Nigeria | Desnky Shop', 'Safety equipment and protective materials available through Desnky Global Resources.', 1],
    ['Industrial Supplies', 'industrial-supplies', 'Industrial supplies and practical technical materials for operations support.', null, 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=900&q=80', 20, 'Industrial Supplies Nigeria | Desnky Shop', 'Industrial supplies and technical materials from Desnky Global Resources.', 1],
    ['ICT Devices', 'ict-devices', 'ICT devices and connectivity equipment for business technology support.', null, 'https://images.unsplash.com/photo-1600267165477-6d4cc741b379?auto=format&fit=crop&w=900&q=80', 30, 'ICT Devices Nigeria | Desnky Shop', 'ICT devices and business technology equipment available through Desnky Global Resources.', 1],
    ['Agro Products', 'agro-products', 'Agro product supply options for buyers and food value-chain teams.', null, 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=900&q=80', 40, 'Agro Products Nigeria | Desnky Shop', 'Agro product supply options from Desnky Global Resources.', 1],
];

foreach ($productCategories as $category) {
    $connection->execute(
        "INSERT INTO product_categories
            (name, slug, description, parent_id, image, sort_order, meta_title, meta_description, is_active)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            description = VALUES(description),
            parent_id = VALUES(parent_id),
            image = VALUES(image),
            sort_order = VALUES(sort_order),
            meta_title = VALUES(meta_title),
            meta_description = VALUES(meta_description),
            is_active = VALUES(is_active)",
        $category
    );
}

$categoryIds = [];
foreach ($connection->query("SELECT id, slug FROM product_categories") as $categoryRow) {
    $categoryIds[(string) $categoryRow['slug']] = (int) $categoryRow['id'];
}

$products = [
    [
        'Industrial Safety Helmet',
        'industrial-safety-helmet',
        'A durable safety helmet suitable for industrial, engineering and site operations requiring head protection.',
        'Durable protective helmet for industrial and construction teams.',
        22000,
        18500,
        12000,
        35,
        10,
        'HSE-HELMET-01',
        0.8,
        $categoryIds['safety-equipment'] ?? null,
        'https://images.unsplash.com/photo-1591101501707-1264a4b14500?auto=format&fit=crop&w=900&q=80',
        'Industrial Safety Helmet | Desnky Shop',
        'Durable protective helmet for industrial and construction teams.',
        1,
        'active',
        1,
        $adminId,
    ],
    [
        'Reflective Safety Vest',
        'reflective-safety-vest',
        'A lightweight high-visibility vest for safety identification across field and site activities.',
        'High-visibility vest for field, logistics and worksite personnel.',
        7500,
        null,
        4200,
        80,
        20,
        'HSE-VEST-02',
        0.3,
        $categoryIds['safety-equipment'] ?? null,
        'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=900&q=80',
        'Reflective Safety Vest | Desnky Shop',
        'High-visibility vest for field, logistics and worksite personnel.',
        1,
        'active',
        1,
        $adminId,
    ],
    [
        'Industrial Maintenance Toolkit',
        'industrial-toolkit',
        'A practical toolkit for routine maintenance and field support tasks in industrial environments.',
        'Curated maintenance toolkit for technical teams and field support.',
        145000,
        null,
        98000,
        12,
        5,
        'IND-TOOL-03',
        6.5,
        $categoryIds['industrial-supplies'] ?? null,
        'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=900&q=80',
        'Industrial Maintenance Toolkit | Desnky Shop',
        'Curated maintenance toolkit for technical teams and field support.',
        1,
        'active',
        1,
        $adminId,
    ],
    [
        'Business Network Router Kit',
        'network-router-kit',
        'A business network kit for office connectivity deployments and ICT support projects.',
        'Router and basic network setup kit for small business connectivity.',
        115000,
        98000,
        76000,
        9,
        4,
        'ICT-NET-04',
        1.2,
        $categoryIds['ict-devices'] ?? null,
        'https://images.unsplash.com/photo-1600267165477-6d4cc741b379?auto=format&fit=crop&w=900&q=80',
        'Business Network Router Kit | Desnky Shop',
        'Router and basic network setup kit for small business connectivity.',
        1,
        'active',
        1,
        $adminId,
    ],
    [
        'Processed Agro Supply Pack',
        'processed-agro-pack',
        'A packaged agro product option for buyers that need coordinated sourcing and delivery.',
        'Packaged agro product supply option for food value-chain buyers.',
        42000,
        null,
        31000,
        0,
        10,
        'AGRO-PACK-05',
        12.0,
        $categoryIds['agro-products'] ?? null,
        'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=900&q=80',
        'Processed Agro Supply Pack | Desnky Shop',
        'Packaged agro product supply option for food value-chain buyers.',
        1,
        'active',
        0,
        $adminId,
    ],
];

foreach ($products as $product) {
    $connection->execute(
        "INSERT INTO products
            (name, slug, description, short_description, price, discount_price, cost_price,
             quantity_in_stock, reorder_level, sku, weight, category_id, featured_image,
             meta_title, meta_description, is_active, status, is_featured, created_by)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            description = VALUES(description),
            short_description = VALUES(short_description),
            price = VALUES(price),
            discount_price = VALUES(discount_price),
            cost_price = VALUES(cost_price),
            quantity_in_stock = VALUES(quantity_in_stock),
            reorder_level = VALUES(reorder_level),
            weight = VALUES(weight),
            category_id = VALUES(category_id),
            featured_image = VALUES(featured_image),
            meta_title = VALUES(meta_title),
            meta_description = VALUES(meta_description),
            is_active = VALUES(is_active),
            status = VALUES(status),
            is_featured = VALUES(is_featured)",
        $product
    );
}

$productGalleryImages = [
    'industrial-safety-helmet' => [
        ['https://images.unsplash.com/photo-1591101501707-1264a4b14500?auto=format&fit=crop&w=900&q=80', 'Industrial safety helmet front view', 10],
        ['https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=900&q=80', 'Safety helmet with worksite protective gear', 20],
        ['https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=900&q=80', 'Industrial safety equipment on site', 30],
    ],
    'reflective-safety-vest' => [
        ['https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=900&q=80', 'Reflective safety vest on worksite', 10],
        ['https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=900&q=80', 'High visibility worksite safety gear', 20],
        ['https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?auto=format&fit=crop&w=900&q=80', 'Safety planning gear and protective equipment', 30],
    ],
    'industrial-toolkit' => [
        ['https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=900&q=80', 'Industrial maintenance toolkit with hand tools', 10],
        ['https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=900&q=80', 'Technical tools for industrial support work', 20],
        ['https://images.unsplash.com/photo-1581092335878-2d9ff86ca2bf?auto=format&fit=crop&w=900&q=80', 'Engineering maintenance tools and equipment', 30],
    ],
    'network-router-kit' => [
        ['https://images.unsplash.com/photo-1600267165477-6d4cc741b379?auto=format&fit=crop&w=900&q=80', 'Business network router kit', 10],
        ['https://images.unsplash.com/photo-1518779578993-ec3579fee39f?auto=format&fit=crop&w=900&q=80', 'ICT hardware and network equipment', 20],
        ['https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=900&q=80', 'Server and network connectivity hardware', 30],
    ],
    'processed-agro-pack' => [
        ['https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=900&q=80', 'Processed agro supply pack', 10],
        ['https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=900&q=80', 'Agro products and farm supply field', 20],
        ['https://images.unsplash.com/photo-1560493676-04071c5f467b?auto=format&fit=crop&w=900&q=80', 'Packaged agricultural produce for supply', 30],
    ],
];

foreach ($productGalleryImages as $productSlug => $images) {
    $productRow = $connection->queryOne("SELECT id FROM products WHERE slug = ?", [$productSlug]);
    if ($productRow === null) {
        continue;
    }

    $productId = (int) $productRow['id'];

    foreach ($images as [$path, $altText, $sortOrder]) {
        $existingImage = $connection->queryOne(
            "SELECT id FROM product_images WHERE product_id = ? AND path = ?",
            [$productId, $path]
        );

        if ($existingImage !== null) {
            $connection->execute(
                "UPDATE product_images
                 SET alt_text = ?, sort_order = ?
                 WHERE id = ?",
                [$altText, $sortOrder, $existingImage['id']]
            );
            continue;
        }

        $connection->execute(
            "INSERT INTO product_images (product_id, path, alt_text, sort_order)
             VALUES (?, ?, ?, ?)",
            [$productId, $path, $altText, $sortOrder]
        );
    }
}

foreach ([
    ['Manual bank transfer', 'bank_transfer', 1],
    ['Pay on delivery', 'pay_on_delivery', 1],
] as $paymentMethod) {
    $connection->execute(
        "INSERT INTO payment_methods (name, slug, is_active)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE name = VALUES(name), is_active = VALUES(is_active)",
        $paymentMethod
    );
}

echo "Foundation seed data loaded successfully." . PHP_EOL;
