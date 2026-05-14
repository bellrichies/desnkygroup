<?php

namespace App\Services;

use App\Repositories\ProjectRepository;

/**
 * Public project/gallery business logic.
 */
class ProjectService extends BaseService
{
    private ProjectRepository $projects;

    public function __construct(ProjectRepository $projects)
    {
        $this->projects = $projects;
    }

    /**
     * @return array<int, array>
     */
    public function published(): array
    {
        return array_map(function (array $project): array {
            $project['title'] = (string) ($project['title'] ?? '');
            $project['summary'] = (string) ($project['summary'] ?? '');
            $project['category'] = (string) ($project['category'] ?? 'Project');
            $project['image'] = $project['featured_image']
                ?: 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=900&q=80';

            return $project;
        }, $this->projects->published());
    }
}
