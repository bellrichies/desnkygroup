<?php
$hero = $hero ?? [];
$listing = $listing ?? [];
$projects = $projects ?? [];
$categories = $categories ?? [];
$allLabel = (string) ($listing['all_categories_label'] ?? 'All');
$introText = (string) ($listing['text'] ?? $hero['text'] ?? '');
?>

<section class="section-band">
    <div class="container-page" x-data='{ "category": <?php echo $this->escapeJson($allLabel); ?>, "search": "" }'>
        <div class="mb-10 max-w-3xl" data-reveal>
            <p class="eyebrow">Projects</p>
            <h1 class="mt-3 section-heading"><?php echo $this->escape((string) ($listing['heading'] ?? $title ?? 'Projects')); ?></h1>
            <?php if ($introText !== '') : ?>
                <p class="section-lead"><?php echo $this->escape($introText); ?></p>
            <?php endif; ?>
        </div>
        <div class="mb-8 grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center">
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-desnky-muted">
                    <?php echo $this->partial('frontend/partials/icon', ['name' => 'search', 'class' => 'h-5 w-5']); ?>
                </span>
                <label class="sr-only" for="project-search"><?php echo $this->escape((string) ($listing['search_label'] ?? 'Search projects')); ?></label>
                <input id="project-search" type="search" x-model="search" placeholder="<?php echo $this->escape((string) ($listing['search_placeholder'] ?? 'Search projects…')); ?>" class="form-field pl-10">
            </div>
            <div class="-mx-1 flex gap-2 overflow-x-auto px-1 pb-1 lg:mx-0 lg:flex-wrap lg:px-0" role="group" aria-label="<?php echo $this->escape((string) ($listing['filters_label'] ?? 'Filter by category')); ?>">
                <button type="button" class="chip" :class='category === <?php echo $this->escapeJson($allLabel); ?> && "chip-active"' @click='category = <?php echo $this->escapeJson($allLabel); ?>'><?php echo $this->escape($allLabel); ?></button>
                <?php foreach ($categories as $category) : ?>
                    <?php $cat = (string) $category; ?>
                    <button type="button" class="chip" :class='category === <?php echo $this->escapeJson($cat); ?> && "chip-active"' @click='category = <?php echo $this->escapeJson($cat); ?>'><?php echo $this->escape($cat); ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($projects !== []) : ?>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($projects as $project) : ?>
                    <?php
                    $cat = (string) ($project['category'] ?? '');
                    $title_ = (string) ($project['title'] ?? '');
                    $summary = (string) ($project['summary'] ?? '');
                    $haystack = strtolower($title_ . ' ' . $summary . ' ' . $cat);
                    ?>
                    <article
                        class="card-interactive flex flex-col"
                        data-reveal
                        data-category="<?php echo $this->escape($cat); ?>"
                        x-show='(category === <?php echo $this->escapeJson($allLabel); ?> || category === <?php echo $this->escapeJson($cat); ?>) && <?php echo $this->escapeJson($haystack); ?>.includes(search.toLowerCase())'
                x-transition.opacity
                    >
                        <?php if (!empty($project['image'])) : ?>
                            <button type="button" class="block overflow-hidden" data-lightbox="<?php echo $this->escape((string) $project['image']); ?>" data-lightbox-alt="<?php echo $this->escape($title_); ?>" aria-label="View image of <?php echo $this->escape($title_); ?>">
                                <img src="<?php echo $this->escape((string) $project['image']); ?>" alt="<?php echo $this->escape($title_); ?>" class="project-card__image" loading="lazy">
                            </button>
                        <?php endif; ?>
                        <div class="card-body flex flex-1 flex-col">
                            <h2 class="text-lg font-bold text-desnky-dark">
                                <?php if (!empty($project['slug'])) : ?>
                                    <a href="/projects/<?php echo $this->escape((string) $project['slug']); ?>" class="hover:text-desnky-primary"><?php echo $this->escape($title_); ?></a>
                                <?php else : ?>
                                    <?php echo $this->escape($title_); ?>
                                <?php endif; ?>
                            </h2>
                            <p class="mt-2 flex-1 text-sm leading-6 text-desnky-muted"><?php echo $this->escape($summary); ?></p>
                            <?php if (!empty($project['slug'])) : ?>
                                <a href="/projects/<?php echo $this->escape((string) $project['slug']); ?>" class="btn-ghost mt-4">
                                    <?php echo $this->escape((string) ($listing['card_cta_label'] ?? 'View case study')); ?>
                                    <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="rounded-lg border border-dashed border-gray-300 bg-desnky-surface py-16 text-center">
                <p class="text-desnky-muted">No projects to display yet. Check back soon.</p>
                <a href="/contact" class="btn-primary mt-5">Discuss your project</a>
            </div>
        <?php endif; ?>
    </div>
</section>
