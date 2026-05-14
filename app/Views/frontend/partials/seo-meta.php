<?php echo \App\Helpers\SeoHelper::render($seo ?? [
    'title' => $title ?? 'Desnky Global Resources Ltd',
    'description' => $meta_description ?? '',
]); ?>
