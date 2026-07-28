<?php
$title = (string) ($title ?? '');
$content = (string) ($content ?? '');
$updatedAt = (string) ($page['updated_at'] ?? '');
?>

<section class="relative isolate overflow-hidden bg-desnky-dark text-white">
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-desnky-dark to-desnky-dark/90"></div>
    <div class="container-page py-14 sm:py-16">
        <nav class="text-sm text-gray-300" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="/" class="hover:text-white">Home</a></li>
                <li class="text-gray-500" aria-hidden="true">/</li>
                <li><span class="text-desnky-primary-200" aria-current="page"><?php echo $this->escape($title); ?></span></li>
            </ol>
        </nav>
        <h1 class="mt-5 text-3xl font-bold sm:text-4xl"><?php echo $this->escape($title); ?></h1>
        <?php if ($updatedAt !== '') : ?>
            <p class="mt-3 text-sm text-gray-300">Last updated <?php echo $this->escape($this->formatDate($updatedAt)); ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="section-band">
    <div class="container-page">
        <div class="prose-desnky">
            <?php echo $content; ?>
        </div>
        <div class="mt-10">
            <a href="/" class="btn-ghost">
                <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4 rotate-180']); ?>
                Back to home
            </a>
        </div>
    </div>
</section>
