<?php

namespace App\DTO;

/**
 * Normalized payload passed from the blog service to the repository layer.
 */
final class BlogPostPayload
{
    /**
     * @param array<string, mixed> $values
     * @param array<int, int> $categoryIds
     * @param array<int, int> $tagIds
     */
    public function __construct(
        public readonly array $values,
        public readonly array $categoryIds,
        public readonly array $tagIds,
        public readonly ?int $primaryCategoryId
    ) {
    }
}
