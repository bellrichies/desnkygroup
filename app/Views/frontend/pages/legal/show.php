<?php
$sections = $sections ?? [];
?>
<section class="section-band bg-white">
    <div class="container-page">
        <div class="max-w-3xl">
            <p class="eyebrow">Legal</p>
            <h1 class="mt-3 section-heading"><?php echo $this->escape((string) ($title ?? 'Legal')); ?></h1>
            <p class="section-lead"><?php echo $this->escape((string) ($description ?? '')); ?></p>
        </div>

        <div class="prose-desnky mt-10">
            <?php foreach ($sections as $section) : ?>
                <h2><?php echo $this->escape((string) ($section[0] ?? '')); ?></h2>
                <p><?php echo $this->escape((string) ($section[1] ?? '')); ?></p>
            <?php endforeach; ?>
        </div>

        <div class="mt-10 rounded-lg border border-gray-200 bg-desnky-surface p-5">
            <p class="text-sm leading-6 text-desnky-muted">
                Last updated: <?php echo date('F j, Y'); ?>. For questions about these terms or privacy practices, please use the
                <a href="/contact" class="font-semibold text-desnky-primary hover:underline">contact page</a>.
            </p>
        </div>
    </div>
</section>
