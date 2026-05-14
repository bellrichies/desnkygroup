<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <nav class="text-sm text-desnky-muted" aria-label="Breadcrumb">
            <a href="/" class="hover:text-desnky-blue">Home</a> / <span>Projects</span>
        </nav>
        <div class="mt-6 max-w-3xl">
            <h1 class="text-4xl font-bold text-desnky-navy sm:text-5xl">Projects and Gallery</h1>
            <p class="mt-5 text-lg leading-8 text-desnky-muted">
                A representative gallery of the sectors we support, including engineering, HSE, procurement and agro
                supply coordination.
            </p>
        </div>
    </div>
</section>

<section class="section-band">
    <div class="container-page">
        <div class="mb-8 grid gap-4 md:grid-cols-[1fr_auto]">
            <label class="sr-only" for="project-search">Search projects</label>
            <input id="project-search" type="search" placeholder="Search projects" class="form-field" data-project-search>
            <div class="flex flex-wrap gap-2" data-project-filters>
                <?php foreach (['All', 'Engineering', 'HSE', 'Procurement', 'Agro'] as $category) : ?>
                    <button type="button" class="rounded-md border border-gray-200 px-4 py-2 text-sm font-semibold text-desnky-navy hover:border-desnky-blue" data-category="<?php echo $this->escape($category); ?>">
                        <?php echo $this->escape($category); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($projects as $project) : ?>
                <article class="bg-white shadow-card" data-project-card data-category="<?php echo $this->escape($project['category']); ?>">
                    <img src="<?php echo $this->escape($project['image']); ?>" alt="<?php echo $this->escape($project['title']); ?>" class="h-52 w-full object-cover" loading="lazy">
                    <div class="p-5">
                        <span class="text-xs font-bold uppercase tracking-wide text-desnky-blue"><?php echo $this->escape($project['category']); ?></span>
                        <h2 class="mt-2 text-lg font-bold text-desnky-navy"><?php echo $this->escape($project['title']); ?></h2>
                        <p class="mt-2 text-sm leading-6 text-desnky-muted"><?php echo $this->escape($project['summary']); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var cards = Array.from(document.querySelectorAll('[data-project-card]'));
    var search = document.querySelector('[data-project-search]');

    function applyFilter(category) {
        var term = search ? search.value.toLowerCase() : '';
        cards.forEach(function (card) {
            var matchesCategory = category === 'All' || card.dataset.category === category;
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
            applyFilter('All');
        });
    }
});
</script>
