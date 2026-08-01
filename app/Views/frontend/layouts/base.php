<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <?php
    $publicPath = dirname(__DIR__, 4) . '/public';
    $assetUrl = static function (string $path) use ($publicPath): string {
        $normalizedPath = '/' . ltrim($path, '/');
        $file = $publicPath . str_replace('/', DIRECTORY_SEPARATOR, $normalizedPath);
        $version = is_file($file) ? (string) filemtime($file) : '1';

        return $normalizedPath . '?v=' . rawurlencode($version);
    };
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1d1228">
    <?php
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    ?>
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
    <script>document.documentElement.classList.remove('no-js');document.documentElement.classList.add('js');</script>
    <link rel="icon" type="image/png" href="<?php echo $assetUrl('/assets/images/favicon.png'); ?>">
    <link rel="apple-touch-icon" href="<?php echo $assetUrl('/assets/images/favicon.png'); ?>">
    <?php echo $this->partial('frontend/partials/seo-meta', [
        'seo' => $seo ?? [],
        'title' => $title ?? 'Desnky Global Resources Ltd',
        'meta_description' => $meta_description ?? '',
    ]); ?>
    <?php echo $this->partial('frontend/partials/analytics'); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >
    <link rel="stylesheet" href="<?php echo $assetUrl('/assets/css/main.css'); ?>">
</head>
<body class="min-h-screen bg-white text-desnky-ink">
    <?php echo $this->partial('frontend/partials/header', ['active' => $active ?? '']); ?>

    <main id="main-content" class="min-h-[60vh]">
        <?php echo $content ?? ''; ?>
    </main>

    <?php echo $this->partial('frontend/partials/footer'); ?>

    <?php echo $this->partial('frontend/partials/mobile-action-bar', ['active' => $active ?? '']); ?>
    <?php echo $this->partial('frontend/partials/cookie-consent'); ?>

    <!-- Live region container for AJAX toast notifications -->
    <div data-toast-container aria-live="polite" aria-atomic="true"></div>

    <script src="<?php echo $assetUrl('/assets/js/lib/alpine-collapse.min.js'); ?>" defer></script>
    <script src="<?php echo $assetUrl('/assets/js/lib/alpine.min.js'); ?>" defer></script>
    <script src="<?php echo $assetUrl('/assets/js/ajax-handler.js'); ?>" defer></script>
    <script src="<?php echo $assetUrl('/assets/js/header-scroll.js'); ?>" defer></script>
    <script src="<?php echo $assetUrl('/assets/js/media.js'); ?>" defer></script>
    <script src="<?php echo $assetUrl('/assets/js/blog.js'); ?>" defer></script>
    <script src="<?php echo $assetUrl('/assets/js/shop.js'); ?>" defer></script>
    <script src="<?php echo $assetUrl('/assets/js/analytics.js'); ?>" defer></script>
    <?php echo $scripts ?? ''; ?>
</body>
</html>
