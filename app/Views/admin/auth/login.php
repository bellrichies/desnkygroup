<div class="min-h-screen flex items-center justify-center bg-slate-950 px-4 py-10">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Desnky</h1>
            <p class="text-gray-600 mt-2">Admin Dashboard</p>
        </div>

        <form id="loginForm" class="space-y-6">
            <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token ?? ''); ?>">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email Address
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500"
                    autocomplete="email"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500"
                    autocomplete="current-password"
                >
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-blue-600 rounded">
                <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
                </div>
                <a href="#" class="text-sm text-blue-700 hover:text-blue-800">Forgot password?</a>
            </div>

            <div id="errorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <p id="errorText"></p>
            </div>

            <div id="loadingMessage" class="hidden bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3">
                <p>Logging in...</p>
            </div>

            <button
                type="submit"
                class="w-full px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 font-medium transition"
            >
                Sign In
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function (event) {
    event.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const token = document.querySelector('input[name="_token"]').value;
    const body = new URLSearchParams({ email, password, _token: token });

    document.getElementById('loadingMessage').classList.remove('hidden');
    document.getElementById('errorMessage').classList.add('hidden');

    try {
        const response = await fetch('/admin/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString()
        });

        const data = await response.json();
        if (data.success) {
            window.location.href = data.redirect;
            return;
        }

        document.getElementById('errorText').textContent = data.message || 'Login failed';
        document.getElementById('errorMessage').classList.remove('hidden');
    } catch (error) {
        document.getElementById('errorText').textContent = 'Network error. Please try again.';
        document.getElementById('errorMessage').classList.remove('hidden');
    } finally {
        document.getElementById('loadingMessage').classList.add('hidden');
    }
});
</script>
