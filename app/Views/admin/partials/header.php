<header class="sticky top-0 z-20 border-b border-gray-200 bg-white">
    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded border border-gray-300 text-gray-700 lg:hidden"
                @click="sidebarOpen = true"
                aria-label="Open sidebar"
            >
                <span class="block h-0.5 w-5 bg-current"></span>
            </button>
            <h1 class="truncate text-xl font-semibold text-gray-950">
                <?php echo $this->escape((string) $title); ?>
            </h1>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden text-right sm:block">
                <p class="text-sm font-medium text-gray-950">
                    <?php echo $this->escape((string) ($user['full_name'] ?? 'Admin')); ?>
                </p>
                <p class="text-xs text-gray-500">
                    <?php echo $this->escape((string) ($user['email'] ?? '')); ?>
                </p>
            </div>

            <form action="/admin/logout" method="POST">
                <input type="hidden" name="_token" value="<?php echo $this->escape((string) $csrf_token); ?>">
                <button
                    type="submit"
                    class="rounded bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800"
                >
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>
