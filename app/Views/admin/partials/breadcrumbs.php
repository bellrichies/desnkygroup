<?php if (!empty($breadcrumbs)) : ?>
    <nav class="mb-6 text-sm text-gray-600" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2">
            <li>
                <a href="/admin/dashboard" class="hover:text-blue-700">Admin</a>
            </li>
            <?php foreach ($breadcrumbs as $crumb) : ?>
                <li class="text-gray-400">/</li>
                <li>
                    <?php if (!empty($crumb['url'])) : ?>
                        <a href="<?php echo $this->escape((string) $crumb['url']); ?>" class="hover:text-blue-700">
                            <?php echo $this->escape((string) $crumb['label']); ?>
                        </a>
                    <?php else : ?>
                        <span class="font-medium text-gray-900">
                            <?php echo $this->escape((string) $crumb['label']); ?>
                        </span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
<?php endif; ?>
