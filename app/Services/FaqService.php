<?php

namespace App\Services;

use App\Repositories\FaqRepository;

class FaqService
{
    public function __construct(private FaqRepository $repo)
    {
    }

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        return $this->repo->all();
    }

    /** @return array<int, array<string, mixed>> */
    public function active(): array
    {
        return $this->repo->active();
    }

    public function find(int $id): ?array
    {
        return $this->repo->find($id);
    }

    public function create(array $data): int
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    public function toggleActive(int $id): bool
    {
        return $this->repo->toggleActive($id);
    }
}
