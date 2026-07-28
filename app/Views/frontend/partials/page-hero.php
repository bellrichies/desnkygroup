<?php
/**
 * Compact page hero — eyebrow + H1 + lead + breadcrumb (+ optional CTAs).
 *
 * @var array  $hero           Hero content (eyebrow, heading, text, image, image_alt, ctas…).
 * @var string $title          Fallback heading.
 * @var string $fallbackImage  Fallback background image.
 * @var array  $breadcrumbs    Optional list of ['label' => , 'href' => ] (current item omits href).
 */
$hero = $hero ?? [];
$heading = (string) ($hero['heading'] ?? $title ?? '');
$eyebrow = (string) ($hero['eyebrow'] ?? '');
$text = (string) ($hero['text'] ?? '');
$image = (string) ($hero['image'] ?? $fallbackImage ?? '');
$imageAlt = (string) ($hero['image_alt'] ?? $heading);

// Breadcrumb: prefer an explicit trail, else fall back to the legacy home/current labels.
$breadcrumbs = $breadcrumbs ?? [];
if ($breadcrumbs === []) {
    $homeLabel = (string) ($hero['breadcrumb_home_label'] ?? 'Home');
    $currentLabel = (string) ($hero['breadcrumb_current_label'] ?? $heading);
    if ($currentLabel !== '') {
        $breadcrumbs = [
            ['label' => $homeLabel, 'href' => '/'],
            ['label' => $currentLabel],
        ];
    }
}

$primaryLabel = (string) ($hero['primary_cta_label'] ?? '');
$primaryUrl = (string) ($hero['primary_cta_url'] ?? '');
$secondaryLabel = (string) ($hero['secondary_cta_label'] ?? '');
$secondaryUrl = (string) ($hero['secondary_cta_url'] ?? '');
?>

<?php if ($heading !== '') : ?>
<section class="relative isolate overflow-hidden bg-desnky-dark text-white">
    <?php if ($image !== '') : ?>
        <img
            src="<?php echo $this->escape($image); ?>"
            alt="<?php echo $this->escape($imageAlt); ?>"
            class="absolute inset-0 -z-10 h-full w-full object-cover opacity-30"
            loading="eager"
            fetchpriority="high"
        >
    <?php endif; ?>
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-desnky-dark/85 via-desnky-dark/80 to-desnky-dark"></div>

    <div class="container-page py-16 sm:py-20 lg:py-24">
        <?php if ($breadcrumbs !== []) : ?>
            <nav class="text-sm text-gray-300" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <?php foreach ($breadcrumbs as $i => $crumb) : ?>
                        <?php $isLast = $i === count($breadcrumbs) - 1; ?>
                        <li class="flex items-center gap-2">
                            <?php if (!$isLast && !empty($crumb['href'])) : ?>
                                <a href="<?php echo $this->escape((string) $crumb['href']); ?>" class="hover:text-white"><?php echo $this->escape((string) $crumb['label']); ?></a>
                                <span class="text-gray-500" aria-hidden="true">/</span>
                            <?php else : ?>
                                <span class="text-desnky-primary-200" aria-current="page"><?php echo $this->escape((string) $crumb['label']); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>
        <?php endif; ?>

        <div class="mt-6 max-w-3xl">
            <?php if ($eyebrow !== '') : ?>
                <p class="eyebrow-on-dark"><?php echo $this->escape($eyebrow); ?></p>
            <?php endif; ?>
            <h1 class="mt-4 text-3xl font-bold leading-tight sm:text-5xl"><?php echo $this->escape($heading); ?></h1>
            <?php if ($text !== '') : ?>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-gray-200"><?php echo $this->escape($text); ?></p>
            <?php endif; ?>

            <?php if (($primaryLabel !== '' && $primaryUrl !== '') || ($secondaryLabel !== '' && $secondaryUrl !== '')) : ?>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <?php if ($primaryLabel !== '' && $primaryUrl !== '') : ?>
                        <a href="<?php echo $this->escape($primaryUrl); ?>" class="btn-on-dark"><?php echo $this->escape($primaryLabel); ?></a>
                    <?php endif; ?>
                    <?php if ($secondaryLabel !== '' && $secondaryUrl !== '') : ?>
                        <a href="<?php echo $this->escape($secondaryUrl); ?>" class="btn-secondary-on-dark"><?php echo $this->escape($secondaryLabel); ?></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
