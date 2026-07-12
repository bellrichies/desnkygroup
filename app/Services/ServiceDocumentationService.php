<?php

namespace App\Services;

/**
 * Parses the service Markdown documents used as the public-page source of truth.
 */
class ServiceDocumentationService
{
    /**
     * @var array<string, string>
     */
    private const DOCUMENTS = [
        'engineering' => 'docs/services/engineering.md',
        'energy-solutions' => 'docs/services/energy_solutions.md',
        'procurement' => 'docs/services/procurement.md',
        'ict-solutions' => 'docs/services/ict_solutions.md',
        'agro-food-processing' => 'docs/services/agro_solutions.md',
    ];

    /**
     * @return array<string, mixed>|null
     */
    public function forSlug(string $slug): ?array
    {
        if (!isset(self::DOCUMENTS[$slug])) {
            return null;
        }

        $path = base_path(self::DOCUMENTS[$slug]);
        if (!is_file($path)) {
            return null;
        }

        $markdown = (string) file_get_contents($path);
        $parsed = $this->parse($markdown);
        $parsed['source_path'] = self::DOCUMENTS[$slug];

        return $parsed;
    }

    /**
     * @return array<string, mixed>
     */
    private function parse(string $markdown): array
    {
        $markdown = str_replace(["\r\n", "\r"], "\n", $markdown);
        [$bodyMarkdown, $seoMarkdown] = $this->splitAppendix($markdown);

        $title = '';
        $introHeading = '';
        $introBlocks = [];
        $sections = [];
        $primaryCtas = [];
        $secondaryCtas = [];
        $formButton = '';
        $ids = [];

        $currentSection = null;
        $currentChild = null;
        $paragraph = [];
        $list = [];

        $uniqueId = function (string $text) use (&$ids): string {
            $id = strtolower(trim($text));
            $id = preg_replace('/[^a-z0-9]+/u', '-', $id) ?: 'section';
            $id = trim($id, '-');
            $id = $id !== '' ? $id : 'section';

            if (!isset($ids[$id])) {
                $ids[$id] = 0;

                return $id;
            }

            $ids[$id]++;

            return $id . '-' . $ids[$id];
        };

        $addBlock = function (array $block) use (&$sections, &$introBlocks, &$currentSection, &$currentChild): void {
            if ($currentSection !== null && $currentChild !== null) {
                $sections[$currentSection]['children'][$currentChild]['blocks'][] = $block;

                return;
            }

            if ($currentSection !== null) {
                $sections[$currentSection]['blocks'][] = $block;

                return;
            }

            $introBlocks[] = $block;
        };

        $flushParagraph = function () use (&$paragraph, $addBlock): void {
            if ($paragraph === []) {
                return;
            }

            $addBlock([
                'type' => 'paragraph',
                'text' => trim(implode(' ', $paragraph)),
            ]);
            $paragraph = [];
        };

        $flushList = function () use (&$list, $addBlock): void {
            if ($list === []) {
                return;
            }

            $addBlock([
                'type' => 'list',
                'items' => array_values($list),
            ]);
            $list = [];
        };

        $flushAll = function () use ($flushParagraph, $flushList): void {
            $flushParagraph();
            $flushList();
        };

        foreach (explode("\n", $bodyMarkdown) as $rawLine) {
            $line = trim($rawLine);

            if ($line === '') {
                $flushAll();
                continue;
            }

            if ($line === ':::' || preg_match('/^-{3,}$/', $line) === 1) {
                $flushAll();
                continue;
            }

            if (preg_match('/^\*\*(Primary CTA|Secondary CTA|Form Button):\*\*\s*(.+)$/u', $line, $cta)) {
                $flushAll();
                $entry = [
                    'label' => trim($cta[2]),
                    'section_id' => $currentSection !== null ? $sections[$currentSection]['id'] : null,
                ];

                if ($cta[1] === 'Primary CTA') {
                    $primaryCtas[] = $entry;
                } elseif ($cta[1] === 'Secondary CTA') {
                    $secondaryCtas[] = $entry;
                } else {
                    $formButton = $entry['label'];
                }

                continue;
            }

            if (preg_match('/^(#{1,6})\s+(.+)$/u', $line, $heading)) {
                $flushAll();
                $level = strlen($heading[1]);
                $headingText = trim($heading[2]);

                if ($level === 1) {
                    if ($title === '') {
                        $title = $headingText;
                        continue;
                    }

                    $sections[] = [
                        'level' => 1,
                        'title' => $headingText,
                        'id' => $uniqueId($headingText),
                        'blocks' => [],
                        'children' => [],
                    ];
                    $currentSection = array_key_last($sections);
                    $currentChild = null;
                    continue;
                }

                if ($level === 2 && $currentSection === null) {
                    if ($introHeading === '') {
                        $introHeading = $headingText;
                        continue;
                    }

                    $addBlock([
                        'type' => 'heading',
                        'level' => 2,
                        'text' => $headingText,
                    ]);
                    continue;
                }

                if ($level === 2 && str_starts_with($headingText, 'Contact Our')) {
                    $sections[] = [
                        'level' => 1,
                        'title' => $headingText,
                        'id' => $uniqueId($headingText),
                        'blocks' => [],
                        'children' => [],
                    ];
                    $currentSection = array_key_last($sections);
                    $currentChild = null;
                    continue;
                }

                if ($level === 2 && $currentSection !== null) {
                    $sections[$currentSection]['children'][] = [
                        'level' => 2,
                        'title' => $headingText,
                        'id' => $uniqueId($headingText),
                        'blocks' => [],
                        'children' => [],
                    ];
                    $currentChild = array_key_last($sections[$currentSection]['children']);
                    continue;
                }

                $addBlock([
                    'type' => 'heading',
                    'level' => $level,
                    'text' => $headingText,
                ]);
                continue;
            }

            if (preg_match('/^[-*]\s+(.+)$/u', $line, $item)) {
                $flushParagraph();
                $list[] = trim($item[1]);
                continue;
            }

            $paragraph[] = $line;
        }

        $flushAll();

        $faqSection = $this->findSection($sections, 'Frequently Asked Questions');
        $contactSection = $this->findSectionByPrefix($sections, 'Contact Our');

        return [
            'title' => $title,
            'intro_heading' => $introHeading,
            'intro_blocks' => $introBlocks,
            'sections' => $sections,
            'faq_section' => $faqSection,
            'faqs' => $this->faqsFromSection($faqSection),
            'contact_section' => $contactSection,
            'primary_cta' => $primaryCtas[0] ?? null,
            'secondary_cta' => $secondaryCtas[0] ?? null,
            'final_primary_cta' => $primaryCtas !== [] ? $primaryCtas[array_key_last($primaryCtas)] : null,
            'final_secondary_cta' => $secondaryCtas !== [] ? $secondaryCtas[array_key_last($secondaryCtas)] : null,
            'form_button' => $formButton,
            'seo' => $this->parseSeo($seoMarkdown),
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitAppendix(string $markdown): array
    {
        $marker = "\n# SEO Information";
        $position = strpos($markdown, $marker);

        if ($position === false) {
            return [$markdown, ''];
        }

        return [
            substr($markdown, 0, $position),
            substr($markdown, $position),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $sections
     * @return array<string, mixed>|null
     */
    private function findSection(array $sections, string $title): ?array
    {
        foreach ($sections as $section) {
            if ((string) ($section['title'] ?? '') === $title) {
                return $section;
            }
        }

        return null;
    }

    /**
     * @param array<int, array<string, mixed>> $sections
     * @return array<string, mixed>|null
     */
    private function findSectionByPrefix(array $sections, string $prefix): ?array
    {
        foreach ($sections as $section) {
            if (str_starts_with((string) ($section['title'] ?? ''), $prefix)) {
                return $section;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed>|null $section
     * @return array<int, array<string, string>>
     */
    private function faqsFromSection(?array $section): array
    {
        if ($section === null || !is_array($section['children'] ?? null)) {
            return [];
        }

        $faqs = [];

        foreach ($section['children'] as $child) {
            if (!is_array($child)) {
                continue;
            }

            $faqs[] = [
                'question' => (string) ($child['title'] ?? ''),
                'answer' => $this->plainText((array) ($child['blocks'] ?? [])),
            ];
        }

        return $faqs;
    }

    /**
     * @param array<int, array<string, mixed>> $blocks
     */
    private function plainText(array $blocks): string
    {
        $parts = [];

        foreach ($blocks as $block) {
            if (($block['type'] ?? '') === 'paragraph') {
                $parts[] = (string) ($block['text'] ?? '');
            }

            if (($block['type'] ?? '') === 'list' && is_array($block['items'] ?? null)) {
                $parts[] = implode('; ', array_map('strval', $block['items']));
            }
        }

        return trim(implode(' ', $parts));
    }

    /**
     * @return array<string, mixed>
     */
    private function parseSeo(string $markdown): array
    {
        $designMarker = "\n## Recommended modern page design";
        $designPosition = strpos($markdown, $designMarker);
        if ($designPosition !== false) {
            $markdown = substr($markdown, 0, $designPosition);
        }

        return [
            'title' => $this->seoField($markdown, 'Recommended Page Title'),
            'description' => $this->seoField($markdown, 'Recommended Meta Description'),
            'url' => trim($this->seoField($markdown, 'Recommended URL'), '`'),
            'primary_keyword' => $this->seoField($markdown, 'Recommended Primary Keyword'),
            'supporting_keywords' => $this->seoKeywords($markdown),
        ];
    }

    private function seoField(string $markdown, string $field): string
    {
        $pattern = '/\*\*' . preg_quote($field, '/') . ':\*\*\s*(.*?)(?=\n\*\*|\n#|\z)/su';

        if (preg_match($pattern, $markdown, $match) !== 1) {
            return '';
        }

        $value = trim((string) $match[1]);
        $value = preg_replace('/\s+/', ' ', $value) ?: '';

        return trim($value);
    }

    /**
     * @return array<int, string>
     */
    private function seoKeywords(string $markdown): array
    {
        if (preg_match('/\*\*Supporting Keywords:\*\*\s*(.*)$/su', $markdown, $match) !== 1) {
            return [];
        }

        $keywords = [];
        foreach (explode("\n", (string) $match[1]) as $line) {
            $line = trim($line);
            if (preg_match('/^[-*]\s+(.+)$/u', $line, $item) !== 1) {
                continue;
            }

            $keywords[] = trim($item[1]);
        }

        return $keywords;
    }
}
