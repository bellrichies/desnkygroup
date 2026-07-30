<?php

namespace App\Services;

use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;

/**
 * Manages team profiles stored in the About page's team section.
 */
class TeamMemberService
{
    public function __construct(
        private PageRepository $pages,
        private PageSectionRepository $sections
    ) {
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function all(): array
    {
        [, $body] = $this->teamSection();

        return array_values(array_filter(
            $body['members'] ?? [],
            static fn ($member): bool => is_array($member)
        ));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        [$section, $body] = $this->teamSection();
        $members = is_array($body['members'] ?? null) ? array_values($body['members']) : [];
        $members[] = $this->profile($data);
        $body['members'] = $members;
        $this->save((int) $section['id'], $body);

        return count($members) - 1;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $index, array $data): void
    {
        [$section, $body] = $this->teamSection();
        $members = is_array($body['members'] ?? null) ? array_values($body['members']) : [];
        $this->assertExists($members, $index);
        $members[$index] = $this->profile($data);
        $body['members'] = $members;
        $this->save((int) $section['id'], $body);
    }

    public function delete(int $index): void
    {
        [$section, $body] = $this->teamSection();
        $members = is_array($body['members'] ?? null) ? array_values($body['members']) : [];
        $this->assertExists($members, $index);
        array_splice($members, $index, 1);
        $body['members'] = $members;
        $this->save((int) $section['id'], $body);
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, mixed>}
     */
    private function teamSection(): array
    {
        $page = $this->pages->findBySlug('about');
        if ($page === null) {
            throw new \RuntimeException('The About page was not found.');
        }

        $section = $this->sections->findByPageAndKey((int) $page['id'], 'team');
        if ($section === null) {
            throw new \RuntimeException('The About page team section was not found.');
        }

        $body = json_decode((string) ($section['body'] ?? ''), true);
        if (!is_array($body)) {
            throw new \RuntimeException('The About page team section contains invalid content.');
        }

        return [$section, $body];
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    private function profile(array $data): array
    {
        $name = trim((string) ($data['name'] ?? ''));

        return [
            'name' => $name,
            'role' => trim((string) ($data['role'] ?? '')),
            'image' => trim((string) ($data['image'] ?? '')),
            'image_alt' => trim((string) ($data['image_alt'] ?? '')) ?: $name,
        ];
    }

    /**
     * @param array<int, mixed> $members
     */
    private function assertExists(array $members, int $index): void
    {
        if ($index < 0 || !array_key_exists($index, $members)) {
            throw new \OutOfBoundsException('Team member not found.');
        }
    }

    /**
     * @param array<string, mixed> $body
     */
    private function save(int $sectionId, array $body): void
    {
        $encoded = json_encode(
            $body,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
        $this->sections->updateBody($sectionId, $encoded);
    }
}
