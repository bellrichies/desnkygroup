<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Projects</h1>
        <p class="mt-1 text-sm text-gray-600">Manage project and gallery entries shown on the public website.</p>
    </div>
    <a href="/admin/projects/create" class="inline-flex items-center justify-center rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
        Create Project
    </a>
</div>

<div class="overflow-hidden rounded border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-4 py-3">Project</th>
                <th class="px-4 py-3">Client</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($projects as $project) : ?>
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900"><?php echo $this->escape((string) $project['title']); ?></p>
                        <p class="text-xs text-gray-500">/projects/<?php echo $this->escape((string) $project['slug']); ?></p>
                    </td>
                    <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) ($project['client_name'] ?? '')); ?></td>
                    <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) ($project['category'] ?? '')); ?></td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2 py-1 text-xs font-semibold <?php echo !empty($project['is_published']) ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'; ?>">
                            <?php echo !empty($project['is_published']) ? 'Published' : 'Draft'; ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="/admin/projects/<?php echo (int) $project['id']; ?>/edit" class="rounded border border-gray-300 px-3 py-1.5 font-medium text-gray-700 hover:bg-gray-50">Edit</a>
                            <form method="POST" action="/admin/projects/<?php echo (int) $project['id']; ?>/delete" onsubmit="return confirm('Delete this project?');">
                                <input type="hidden" name="_token" value="<?php echo $this->escape((string) ($_SESSION['csrf_token'] ?? '')); ?>">
                                <button class="rounded border border-red-200 px-3 py-1.5 font-medium text-red-700 hover:bg-red-50">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($projects)) : ?>
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No projects found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
