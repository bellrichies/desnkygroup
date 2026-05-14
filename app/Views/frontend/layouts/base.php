<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f2742">
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
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body class="min-h-screen bg-white text-desnky-ink">
    <?php echo $this->partial('frontend/partials/header', ['active' => $active ?? '']); ?>

    <main id="main-content" class="min-h-[60vh]">
        <?php echo $content ?? ''; ?>
    </main>

    <?php echo $this->partial('frontend/partials/footer'); ?>

    <script src="/assets/js/ajax-handler.js" defer></script>
    <script src="/assets/js/analytics.js" defer></script>
    <?php echo $scripts ?? ''; ?>
</body>
</html>
