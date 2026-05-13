<!-- Page Content -->
<div class="max-w-4xl">
    <h1 class="text-4xl font-bold mb-6">
        <?php echo $this->escape($title); ?>
    </h1>

    <div class="bg-white p-8 rounded-lg shadow-sm prose prose-sm max-w-none">
        <?php echo $content; ?>
    </div>

    <!-- Back Link -->
    <div class="mt-8">
        <a href="/" class="text-blue-600 hover:text-blue-700">&larr; Back to Home</a>
    </div>
</div>
