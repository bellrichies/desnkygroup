<?php
$members ??= [];
$csrf_token ??= '';
?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Team Members</h1>
        <p class="mt-1 text-sm text-gray-500">Manage the leadership profiles displayed on the About page.</p>
    </div>
    <?php if ($this->can('pages.edit')) : ?>
        <button type="button" onclick="openTeamModal()" class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
            + Add team member
        </button>
    <?php endif; ?>
</div>

<?php if ($members === []) : ?>
    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-sm text-gray-500">
        No team members have been added yet.
    </div>
<?php else : ?>
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Photo</th>
                        <th class="px-5 py-3 text-left">Name</th>
                        <th class="px-5 py-3 text-left">Role</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($members as $index => $member) : ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <?php if (!empty($member['image'])) : ?>
                                    <img src="<?php echo $this->escape((string) $member['image']); ?>" alt="" class="h-14 w-14 rounded-full object-cover">
                                <?php else : ?>
                                    <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 font-bold text-gray-400">
                                        <?php echo $this->escape(strtoupper(substr((string) ($member['name'] ?? '?'), 0, 1))); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3 font-semibold text-gray-900"><?php echo $this->escape((string) ($member['name'] ?? '')); ?></td>
                            <td class="px-5 py-3 text-gray-600"><?php echo $this->escape((string) ($member['role'] ?? '')); ?></td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <?php if ($this->can('pages.edit')) : ?>
                                    <button type="button" onclick='openTeamModal(<?php echo $this->escapeJson(array_merge($member, ['index' => $index])); ?>)' class="font-semibold text-blue-700 hover:underline">Edit</button>
                                <?php endif; ?>
                                <?php if ($this->can('pages.delete')) : ?>
                                    <form method="post" action="/admin/team-members/<?php echo (int) $index; ?>/delete" class="inline" onsubmit="return confirm('Remove this team member from the About page?');">
                                        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                                        <button class="ml-3 font-semibold text-red-600 hover:underline">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if ($this->can('pages.edit')) : ?>
<dialog id="team-modal" class="w-full max-w-2xl rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/60">
    <form id="team-form" method="post" action="/admin/team-members" class="p-6">
        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
        <div class="flex items-center justify-between">
            <h2 id="team-modal-title" class="text-xl font-bold text-gray-900">Add team member</h2>
            <button type="button" onclick="this.closest('dialog').close()" class="text-2xl text-gray-500" aria-label="Close">&times;</button>
        </div>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <label>
                <span class="text-sm font-semibold text-gray-700">Full name</span>
                <input id="team-name" name="name" required maxlength="120" class="mt-1 w-full rounded border-gray-300">
            </label>
            <label>
                <span class="text-sm font-semibold text-gray-700">Role / title</span>
                <input id="team-role" name="role" required maxlength="120" class="mt-1 w-full rounded border-gray-300">
            </label>
            <label class="sm:col-span-2">
                <span class="text-sm font-semibold text-gray-700">Photo alternative text</span>
                <input id="team-image-alt" name="image_alt" maxlength="255" class="mt-1 w-full rounded border-gray-300" placeholder="e.g. Jane Doe, Operations Director">
            </label>
            <div class="sm:col-span-2 rounded-xl border border-dashed border-gray-300 p-4">
                <input id="team-image" name="image" type="hidden">
                <img id="team-image-preview" alt="" class="mb-3 hidden h-32 w-32 rounded-lg object-cover">
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="openTeamImagePicker()" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Select photo from library</button>
                    <button id="team-image-clear" type="button" onclick="clearTeamImage()" class="hidden rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Remove photo</button>
                </div>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" onclick="this.closest('dialog').close()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold">Cancel</button>
            <button class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save team member</button>
        </div>
    </form>
</dialog>

<script>
let restoreTeamDialogAfterMedia = false;

function openTeamModal(member) {
    member = member || {};
    const form = document.getElementById('team-form');
    form.action = Number.isInteger(member.index) ? '/admin/team-members/' + member.index : '/admin/team-members';
    document.getElementById('team-modal-title').textContent = Number.isInteger(member.index) ? 'Edit team member' : 'Add team member';
    document.getElementById('team-name').value = member.name || '';
    document.getElementById('team-role').value = member.role || '';
    document.getElementById('team-image-alt').value = member.image_alt || '';
    document.getElementById('team-image').value = member.image || '';
    updateTeamImagePreview(member.image || '');
    document.getElementById('team-modal').showModal();
}

function updateTeamImagePreview(source) {
    const preview = document.getElementById('team-image-preview');
    preview.src = source;
    preview.classList.toggle('hidden', !source);
    document.getElementById('team-image-clear').classList.toggle('hidden', !source);
}

function clearTeamImage() {
    document.getElementById('team-image').value = '';
    updateTeamImagePreview('');
}

function openTeamImagePicker() {
    const dialog = document.getElementById('team-modal');
    restoreTeamDialogAfterMedia = dialog.open;
    if (dialog.open) {
        dialog.close();
    }
    window.requestAnimationFrame(() => openMediaPicker('team-image', 'team-image-preview'));
}

window.addEventListener('media-picker:selected', event => {
    if (event.detail && event.detail.targetInputId === 'team-image') {
        updateTeamImagePreview(document.getElementById('team-image').value);
    }
});

window.addEventListener('media-picker:closed', () => {
    if (!restoreTeamDialogAfterMedia) {
        return;
    }
    restoreTeamDialogAfterMedia = false;
    updateTeamImagePreview(document.getElementById('team-image').value);
    const dialog = document.getElementById('team-modal');
    if (!dialog.open) {
        dialog.showModal();
    }
});
</script>
<?php echo $this->partial('admin/partials/media-picker'); ?>
<?php endif; ?>
