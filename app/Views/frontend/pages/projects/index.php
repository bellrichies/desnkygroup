<?php
$hero = $hero ?? [];
$listing = $listing ?? [];
$projects = $projects ?? [];
$categories = $categories ?? [];
$allLabel = (string) ($listing['all_categories_label'] ?? '');
?>

<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <nav class="text-sm text-desnky-muted" aria-label="Breadcrumb">
            <a href="/" class="hover:text-desnky-blue"><?php echo $this->escape((string) ($hero['breadcrumb_home_label'] ?? '')); ?></a> /
            <span><?php echo $this->escape((string) ($hero['breadcrumb_current_label'] ?? $hero['heading'] ?? '')); ?></span>
        </nav>
        <div class="mt-6 max-w-3xl">
            <h1 class="text-4xl font-bold text-desnky-navy sm:text-5xl"><?php echo $this->escape((string) ($hero['heading'] ?? '')); ?></h1>
            <?php if (!empty($hero['text'])) : ?>
                <p class="mt-5 text-lg leading-8 text-desnky-muted">
                    <?php echo $this->escape((string) $hero['text']); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($projects !== []) : ?>
<section class="section-band">
    <div class="container-page">
        <div class="mb-8 grid gap-4 md:grid-cols-[1fr_auto]">
            <label class="sr-only" for="project-search"><?php echo $this->escape((string) ($listing['search_label'] ?? '')); ?></label>
            <input id="project-search" type="search" placeholder="<?php echo $this->escape((string) ($listing['search_placeholder'] ?? '')); ?>" class="form-field" data-project-search>
            <div class="flex flex-wrap gap-2" data-project-filters aria-label="<?php echo $this->escape((string) ($listing['filters_label'] ?? '')); ?>">
                <button type="button" class="rounded-md border border-gray-200 px-4 py-2 text-sm font-semibold text-desnky-navy hover:border-desnky-blue" data-category="<?php echo $this->escape($allLabel); ?>">
                    <?php echo $this->escape($allLabel); ?>
                </button>
                <?php foreach ($categories as $category) : ?>
                    <button type="button" class="rounded-md border border-gray-200 px-4 py-2 text-sm font-semibold text-desnky-navy hover:border-desnky-blue" data-category="<?php echo $this->escape((string) $category); ?>">
                        <?php echo $this->escape((string) $category); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($projects as $project) : ?>
                <article class="bg-white shadow-card" data-project-card data-category="<?php echo $this->escape((string) $project['category']); ?>">
                    <?php if (!empty($project['image'])) : ?>
                        <img src="<?php echo $this->escape((string) $project['image']); ?>" alt="<?php echo $this->escape((string) $project['title']); ?>" class="h-52 w-full object-cover" loading="lazy">
                    <?php endif; ?>
                    <div class="p-5">
                        <span class="text-xs font-bold uppercase tracking-wide text-desnky-blue"><?php echo $this->escape((string) $project['category']); ?></span>
                        <h2 class="mt-2 text-lg font-bold text-desnky-navy"><?php echo $this->escape((string) $project['title']); ?></h2>
                        <p class="mt-2 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) $project['summary']); ?></p>
                        <?php if (!empty($project['slug'])) : ?>
                            <a href="/projects/<?php echo $this->escape((string) $project['slug']); ?>" class="mt-4 inline-flex text-sm font-semibold text-desnky-blue">
                                <?php echo $this->escape((string) ($listing['card_cta_label'] ?? '')); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var cards = Array.from(document.querySelectorAll('[data-project-card]'));
    var search = document.querySelector('[data-project-search]');
    var allCategory = <?php echo $this->escapeJson($allLabel); ?>;

    function applyFilter(category) {
        var term = search ? search.value.toLowerCase() : '';
        cards.forEach(function (card) {
            var matchesCategory = category === allCategory || card.dataset.category === category;
            var matchesSearch = card.textContent.toLowerCase().indexOf(term) !== -1;
            card.classList.toggle('hidden', !(matchesCategory && matchesSearch));
        });
    }

    document.querySelectorAll('[data-project-filters] button').forEach(function (button) {
        button.addEventListener('click', function () {
            applyFilter(button.dataset.category);
        });
    });

    if (search) {
        search.addEventListener('input', function () {
            applyFilter(allCategory);
        });
    }
});
</script>
