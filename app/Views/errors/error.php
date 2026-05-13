<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-sm">
    <h1 class="text-3xl font-bold mb-4"><?php echo $this->escape((string) ($status ?? 500)); ?></h1>
    <p class="text-gray-700">
        <?php echo $this->escape($message ?? 'An unexpected error occurred.'); ?>
    </p>
</div>
