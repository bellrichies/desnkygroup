<!-- Hero Section -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20 rounded-lg mb-12">
    <h1 class="text-5xl font-bold mb-4"><?php echo $this->escape($title ?? 'Welcome'); ?></h1>
    <p class="text-xl text-blue-100">
        <?php echo $this->escape($subtitle ?? 'Professional Solutions for Global Enterprises'); ?>
    </p>
</div>

<!-- Features Section -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="bg-white p-8 rounded-lg shadow-sm">
        <div class="text-3xl mb-4">01</div>
        <h3 class="text-lg font-bold mb-2">Innovation</h3>
        <p class="text-gray-600">Focused solutions for modern industrial challenges</p>
    </div>
    <div class="bg-white p-8 rounded-lg shadow-sm">
        <div class="text-3xl mb-4">02</div>
        <h3 class="text-lg font-bold mb-2">Reliable Delivery</h3>
        <p class="text-gray-600">Structured execution across core business sectors</p>
    </div>
    <div class="bg-white p-8 rounded-lg shadow-sm">
        <div class="text-3xl mb-4">03</div>
        <h3 class="text-lg font-bold mb-2">Quality</h3>
        <p class="text-gray-600">Committed to professional standards and accountability</p>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-blue-50 border-l-4 border-blue-600 p-8 rounded-lg text-center">
    <h2 class="text-2xl font-bold mb-4">Ready to get started?</h2>
    <p class="text-gray-600 mb-6">Contact us today to learn how we can help your business</p>
    <a href="/contact" class="inline-block px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        Contact Us
    </a>
</div>
