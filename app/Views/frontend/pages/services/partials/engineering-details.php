<?php
$engineeringSections = [
    [
        'id' => 'our-engineering-capabilities',
        'eyebrow' => 'Core capabilities',
        'title' => 'Engineering expertise, connected end to end',
        'intro' => 'One coordinated team brings mechanical, electrical, control and automation disciplines together—reducing interfaces and keeping every system aligned.',
        'image' => '/assets/images/engineering/capabilities.webp',
        'alt' => 'DESNKY engineers inspecting process equipment and control systems',
        'icon' => 'cog',
        'items' => [
            ['Mechanical engineering', 'Equipment design, installation, alignment and performance improvement.'],
            ['Electrical installation support', 'Power distribution, protection, control panels and industrial installations.'],
            ['Instrumentation & control', 'Accurate measurement, calibration and dependable process control.'],
            ['Industrial automation', 'PLC, HMI and control solutions that improve consistency and visibility.'],
        ],
    ],
    [
        'id' => 'industrial-utility-services',
        'eyebrow' => 'Plant utilities',
        'title' => 'Reliable utilities that keep production moving',
        'intro' => 'We design, install and maintain the critical systems behind your operation, with practical solutions built around capacity, efficiency and uptime.',
        'image' => '/assets/images/engineering/utilities.webp',
        'alt' => 'Engineer inspecting an industrial utility plant with pumps and piping',
        'icon' => 'bolt',
        'items' => [
            ['Thermal systems', 'Boilers, heat exchangers, chillers and cooling infrastructure.'],
            ['Compressed air', 'Compressors, distribution networks and efficiency improvements.'],
            ['Water systems', 'Boreholes, treatment, storage, pumps and process-water distribution.'],
            ['Power & processing', 'Generators, CO₂ plants and production equipment support.'],
        ],
    ],
    [
        'id' => 'engineering-procurement-and-construction',
        'eyebrow' => 'EPC delivery',
        'title' => 'From concept to commissioning—one accountable team',
        'intro' => 'Our integrated EPC model connects design, sourcing, construction and start-up under clear technical and commercial control.',
        'image' => '/assets/images/engineering/epc.webp',
        'alt' => 'Engineering project team reviewing plans at an industrial construction site',
        'icon' => 'truck',
        'items' => [
            ['Engineer', 'Define requirements, develop designs and resolve technical risks early.'],
            ['Procure', 'Source compliant equipment with disciplined quality and schedule control.'],
            ['Construct', 'Execute installation safely with coordinated site supervision.'],
            ['Commission', 'Test, validate and hand over a production-ready system.'],
        ],
    ],
    [
        'id' => 'maintenance-and-reliability-engineering',
        'eyebrow' => 'Asset reliability',
        'title' => 'Move from reactive repairs to predictable performance',
        'intro' => 'We combine preventive care, condition monitoring and root-cause analysis to extend asset life, reduce disruption and protect output.',
        'image' => '/assets/images/engineering/reliability.webp',
        'alt' => 'Maintenance engineer performing vibration analysis on an industrial motor',
        'icon' => 'shield-check',
        'items' => [
            ['Preventive maintenance', 'Planned routines matched to operating conditions and asset criticality.'],
            ['Predictive insight', 'Condition checks that reveal developing faults before failure.'],
            ['Root-cause correction', 'Practical fixes that address recurring problems—not only symptoms.'],
            ['Lifecycle support', 'Repairs, overhauls, spares planning and performance optimisation.'],
        ],
    ],
    [
        'id' => 'industries-we-support',
        'eyebrow' => 'Industry experience',
        'title' => 'Built for demanding production environments',
        'intro' => 'Our approach adapts to each facility’s operating realities, compliance needs and production priorities.',
        'image' => '/assets/images/engineering/capabilities.webp',
        'alt' => 'Modern industrial production facility supported by engineering professionals',
        'icon' => 'users',
        'items' => [
            ['Manufacturing', 'Integrated plant, utility, automation and maintenance support.'],
            ['Breweries & beverages', 'Process utilities, packaging systems and production automation.'],
            ['Food processing', 'Hygienic utilities, temperature control and reliable processing equipment.'],
            ['Heavy industry', 'Robust mechanical, electrical and reliability solutions for demanding assets.'],
            ['Commercial facilities', 'Power, water, HVAC-related utilities and facility engineering.'],
        ],
    ],
    [
        'id' => 'our-project-delivery-approach',
        'eyebrow' => 'Our process',
        'title' => 'A clear path from requirement to reliable operation',
        'intro' => 'Every engagement follows a controlled, transparent process with defined decisions, deliverables and accountability.',
        'image' => '/assets/images/engineering/epc.webp',
        'alt' => 'Project team coordinating engineering delivery on an industrial site',
        'icon' => 'arrow-right',
        'steps' => [
            ['Discover', 'Site assessment and operational priorities'],
            ['Evaluate', 'Engineering review and solution options'],
            ['Plan', 'Scope, schedule, budget and risk controls'],
            ['Mobilise', 'Procurement, logistics and site readiness'],
            ['Execute', 'Safe installation with quality supervision'],
            ['Commission', 'Testing, training and performance validation'],
            ['Support', 'Handover, maintenance and continuous improvement'],
        ],
    ],
    [
        'id' => 'safety-quality-and-operational-excellence',
        'eyebrow' => 'Built-in assurance',
        'title' => 'Safety and quality shape every decision',
        'intro' => 'We integrate safe work methods, technical standards and quality controls throughout planning and execution—protecting people, assets and production.',
        'image' => '/assets/images/engineering/reliability.webp',
        'alt' => 'Engineering team applying safe maintenance and quality procedures',
        'icon' => 'shield-check',
        'items' => [
            ['Safe execution', 'Task planning, risk controls and disciplined site practices.'],
            ['Quality control', 'Documented inspections, testing and acceptance criteria.'],
            ['Operational fit', 'Solutions designed around the realities of your facility.'],
        ],
    ],
    [
        'id' => 'why-work-with-desnky',
        'eyebrow' => 'Why DESNKY',
        'title' => 'Technical depth with practical business focus',
        'intro' => 'We deliver solutions that are engineered well, executable on site and aligned with the outcomes that matter to your operation.',
        'image' => '/assets/images/engineering/capabilities.webp',
        'alt' => 'Engineering professionals collaborating inside a modern industrial facility',
        'icon' => 'sparkles',
        'items' => [
            ['Integrated capability', 'One team across engineering, utilities, procurement and maintenance.'],
            ['Industrial experience', 'Recommendations grounded in real production environments.'],
            ['Reliability focus', 'Decisions built around uptime, asset life and maintainability.'],
            ['End-to-end support', 'Continuity from initial assessment through long-term improvement.'],
            ['Facility-specific solutions', 'Scope and priorities shaped around your plant and budget.'],
            ['Reduced downtime', 'Planned interventions that protect availability and output.'],
        ],
    ],
];
?>

<div class="engineering-details">
    <?php foreach ($engineeringSections as $sectionIndex => $engineeringSection) : ?>
        <section id="<?php echo $this->escape($engineeringSection['id']); ?>" class="engineering-feature scroll-mt-28">
            <div class="container-page">
                <div class="engineering-feature__layout <?php echo $sectionIndex % 2 === 1 ? 'engineering-feature__layout--reverse' : ''; ?>">
                    <div class="engineering-feature__visual" data-reveal>
                        <img
                            src="<?php echo $this->escape($engineeringSection['image']); ?>"
                            alt="<?php echo $this->escape($engineeringSection['alt']); ?>"
                            loading="lazy"
                            width="1536"
                            height="1024"
                        >
                        <span class="engineering-feature__icon" aria-hidden="true">
                            <?php echo $this->partial('frontend/partials/icon', ['name' => $engineeringSection['icon'], 'class' => 'h-6 w-6']); ?>
                        </span>
                    </div>

                    <div class="engineering-feature__content" data-reveal>
                        <p class="eyebrow"><?php echo $this->escape($engineeringSection['eyebrow']); ?></p>
                        <h2 class="mt-3 section-heading"><?php echo $this->escape($engineeringSection['title']); ?></h2>
                        <p class="mt-5 max-w-2xl text-base leading-7 text-desnky-muted"><?php echo $this->escape($engineeringSection['intro']); ?></p>

                        <?php if (!empty($engineeringSection['steps'])) : ?>
                            <ol class="engineering-steps mt-8">
                                <?php foreach ($engineeringSection['steps'] as $stepIndex => $step) : ?>
                                    <li>
                                        <span><?php echo str_pad((string) ($stepIndex + 1), 2, '0', STR_PAD_LEFT); ?></span>
                                        <div>
                                            <h3><?php echo $this->escape($step[0]); ?></h3>
                                            <p><?php echo $this->escape($step[1]); ?></p>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        <?php else : ?>
                            <div class="engineering-feature__cards mt-8">
                                <?php foreach ($engineeringSection['items'] as $item) : ?>
                                    <article class="engineering-mini-card">
                                        <span class="engineering-mini-card__check" aria-hidden="true">
                                            <?php echo $this->partial('frontend/partials/icon', ['name' => 'check', 'class' => 'h-3.5 w-3.5']); ?>
                                        </span>
                                        <div>
                                            <h3><?php echo $this->escape($item[0]); ?></h3>
                                            <p><?php echo $this->escape($item[1]); ?></p>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endforeach; ?>
</div>
