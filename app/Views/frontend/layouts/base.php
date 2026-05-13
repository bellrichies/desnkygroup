<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta
        name="description"
        content="<?php echo $this->escape($meta_description ?? 'Desnky Global Resources Ltd provides energy, engineering, procurement, safety, ICT and agro solutions in Nigeria.'); ?>"
    >
    <meta name="theme-color" content="#0f2742">
    <title><?php echo $this->escape($title ?? 'Desnky Global Resources Ltd'); ?></title>
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
    <?php echo $scripts ?? ''; ?>
</body>
</html>
