<div class="max-w-2xl mx-auto">
    <h1 class="text-4xl font-bold mb-2"><?php echo $this->escape($title); ?></h1>
    <p class="text-gray-600 mb-8">
        Send us a message and we will get back to you as soon as possible.
    </p>

    <form id="contactForm" class="bg-white p-8 rounded-lg shadow-sm">
        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token ?? ''); ?>">

        <div class="mb-6">
            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
            <input
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                type="text"
                id="full_name"
                name="full_name"
                required
            >
        </div>

        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
            <input
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                type="email"
                id="email"
                name="email"
                required
            >
        </div>

        <div class="mb-6">
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
            <input
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                type="tel"
                id="phone"
                name="phone"
                required
            >
        </div>

        <div class="mb-6">
            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
            <input
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                type="text"
                id="subject"
                name="subject"
                required
            >
        </div>

        <div class="mb-6">
            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
            <textarea
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                id="message"
                name="message"
                rows="6"
                required
            ></textarea>
        </div>

        <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg font-medium">
            Send Message
        </button>
    </form>

    <div id="successMessage" class="hidden mt-6 bg-green-50 border border-green-200 text-green-700 px-6 py-4">
        <p class="font-medium">Thank you for your message.</p>
        <p>We have received your inquiry and will get back to you soon.</p>
    </div>

    <div id="errorMessage" class="hidden mt-6 bg-red-50 border border-red-200 text-red-700 px-6 py-4">
        <p class="font-medium">There was an error submitting your message.</p>
        <p id="errorDetails"></p>
    </div>
</div>

<script>
document.getElementById('contactForm').addEventListener('submit', async function (event) {
    event.preventDefault();

    try {
        const response = await fetch('/contact/submit', {
            method: 'POST',
            body: new FormData(this)
        });
        const data = await response.json();

        if (data.success) {
            document.getElementById('errorMessage').classList.add('hidden');
            document.getElementById('contactForm').reset();
            document.getElementById('contactForm').classList.add('hidden');
            document.getElementById('successMessage').classList.remove('hidden');
            return;
        }

        document.getElementById('successMessage').classList.add('hidden');
        document.getElementById('errorDetails').textContent = data.message || 'An error occurred';
        document.getElementById('errorMessage').classList.remove('hidden');
    } catch (error) {
        document.getElementById('successMessage').classList.add('hidden');
        document.getElementById('errorDetails').textContent = 'Network error. Please try again.';
        document.getElementById('errorMessage').classList.remove('hidden');
    }
});
</script>
