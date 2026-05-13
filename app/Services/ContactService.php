<?php

namespace App\Services;

use App\Repositories\ContactRepository;

/**
 * ContactService - Business logic for contact submissions.
 */
class ContactService extends BaseService
{
    private ContactRepository $contacts;

    public function __construct(ContactRepository $contacts)
    {
        $this->contacts = $contacts;
    }

    /**
     * Save a contact submission.
     *
     * @param array $data Contact data.
     * @return int Contact ID.
     */
    public function submit(array $data): int
    {
        $data['source'] = $data['source'] ?? 'contact_form';

        return $this->contacts->create($data);
    }

    /**
     * Get contacts, optionally filtered by status.
     *
     * @param string|null $status Contact status.
     * @return array
     */
    public function getAll(?string $status = null): array
    {
        return $status === null
            ? $this->contacts->all()
            : $this->contacts->byStatus($status);
    }

    /**
     * Get contact by ID.
     *
     * @param int $id Contact ID.
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        return $this->contacts->find($id);
    }

    /**
     * Mark a contact as read.
     *
     * @param int $id Contact ID.
     * @return bool
     */
    public function markAsRead(int $id): bool
    {
        return $this->contacts->markAsRead($id);
    }

    /**
     * Assign a contact to an admin user.
     *
     * @param int $id Contact ID.
     * @param int $adminId Admin user ID.
     * @return bool
     */
    public function assign(int $id, int $adminId): bool
    {
        return $this->contacts->assign($id, $adminId);
    }
}
