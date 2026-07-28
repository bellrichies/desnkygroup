<?php
$pagination = $pagination ?? ['page' => 1, 'total_pages' => 1];
$filters = is_array($filters ?? null) ? $filters : [];
if ((int) $pagination['total_pages'] <= 1) {
    return;
}
?>
<nav class="mt-5 flex flex-wrap justify-center gap-2" aria-label="Pagination">
    <?php for ($pageNumber = 1; $pageNumber <= (int) $pagination['total_pages']; $pageNumber++) : ?>
        <?php $query = array_merge($filters, ['page' => $pageNumber]); ?>
        <a href="?<?php echo $this->escape(http_build_query($query)); ?>" class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border px-3 text-sm font-semibold <?php echo $pageNumber === (int) $pagination['page'] ? 'border-blue-700 bg-blue-700 text-white' : 'border-gray-200 bg-white text-gray-700 hover:border-blue-400'; ?>" <?php echo $pageNumber === (int) $pagination['page'] ? 'aria-current="page"' : ''; ?>><?php echo $pageNumber; ?></a>
    <?php endfor; ?>
</nav>
