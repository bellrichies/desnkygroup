<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Sanitizes editor-generated blog HTML before storage and before rendering.
 */
class BlogContentSanitizer extends BaseService
{
    /**
     * @var array<int, string>
     */
    private const ALLOWED_TAGS = [
        'p',
        'br',
        'hr',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'strong',
        'b',
        'em',
        'i',
        'u',
        'ul',
        'ol',
        'li',
        'a',
        'img',
        'figure',
        'figcaption',
        'blockquote',
        'pre',
        'code',
        'table',
        'thead',
        'tbody',
        'tfoot',
        'tr',
        'th',
        'td',
        'span',
        'div',
        'iframe',
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'loading', 'decoding'],
        'blockquote' => ['cite'],
        'iframe' => ['src', 'title', 'loading', 'allow', 'allowfullscreen', 'width', 'height'],
        'th' => ['colspan', 'rowspan', 'scope'],
        'td' => ['colspan', 'rowspan'],
        'h2' => ['id'],
        'h3' => ['id'],
        'h4' => ['id'],
        'h5' => ['id'],
        'h6' => ['id'],
    ];

    public function sanitize(string $html): string
    {
        $html = trim($html);

        if ($html === '') {
            return '';
        }

        if (!class_exists(DOMDocument::class)) {
            return $this->fallbackSanitize($html);
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="utf-8" ?><div data-blog-root="1">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = null;
        foreach ($document->getElementsByTagName('div') as $node) {
            if ($node instanceof DOMElement && $node->getAttribute('data-blog-root') === '1') {
                $root = $node;
                break;
            }
        }

        if (!$root instanceof DOMElement) {
            return '';
        }

        $this->cleanChildren($root);

        $output = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim($output);
    }

    private function cleanChildren(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE) {
                $node->removeChild($child);
                continue;
            }

            if (!$child instanceof DOMElement) {
                continue;
            }

            $tagName = strtolower($child->tagName);
            if (!in_array($tagName, self::ALLOWED_TAGS, true)) {
                $this->unwrapOrRemove($child);
                continue;
            }

            $this->cleanAttributes($child);
            $this->cleanChildren($child);
        }
    }

    private function unwrapOrRemove(DOMElement $element): void
    {
        $parent = $element->parentNode;

        if ($parent === null) {
            return;
        }

        $tagName = strtolower($element->tagName);
        if (in_array($tagName, ['script', 'style', 'object', 'embed'], true)) {
            $parent->removeChild($element);
            return;
        }

        while ($element->firstChild !== null) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }

    private function cleanAttributes(DOMElement $element): void
    {
        $tagName = strtolower($element->tagName);
        $allowed = self::ALLOWED_ATTRIBUTES[$tagName] ?? [];

        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->nodeName);
            $value = trim((string) $attribute->nodeValue);

            if (!in_array($name, $allowed, true) || !$this->isSafeAttribute($tagName, $name, $value)) {
                $element->removeAttribute($attribute->nodeName);
            }
        }

        if ($tagName === 'a') {
            $target = $element->getAttribute('target');
            if ($target === '_blank') {
                $element->setAttribute('rel', 'noopener noreferrer');
            }
        }

        if ($tagName === 'img') {
            if ($element->getAttribute('alt') === '') {
                $element->setAttribute('alt', '');
            }
            $element->setAttribute('loading', 'lazy');
            $element->setAttribute('decoding', 'async');
        }

        if ($tagName === 'iframe') {
            $element->setAttribute('loading', 'lazy');
            $element->setAttribute('title', $element->getAttribute('title') ?: 'Embedded media');
        }
    }

    private function isSafeAttribute(string $tagName, string $attribute, string $value): bool
    {
        if ($attribute === 'id') {
            return preg_match('/^[a-z][a-z0-9_-]{0,80}$/i', $value) === 1;
        }

        if (in_array($attribute, ['href', 'src', 'cite'], true)) {
            if (!$this->isSafeUrl($value)) {
                return false;
            }

            return $tagName !== 'iframe' || $this->isAllowedEmbedUrl($value);
        }

        if (in_array($attribute, ['width', 'height', 'colspan', 'rowspan'], true)) {
            return preg_match('/^[1-9][0-9]{0,3}$/', $value) === 1;
        }

        if ($attribute === 'target') {
            return in_array($value, ['_blank', '_self'], true);
        }

        if ($attribute === 'scope') {
            return in_array($value, ['col', 'row', 'colgroup', 'rowgroup'], true);
        }

        if ($attribute === 'allowfullscreen') {
            return true;
        }

        return !str_contains(strtolower($value), 'javascript:');
    }

    private function isSafeUrl(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        if (str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return true;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
    }

    private function isAllowedEmbedUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return str_ends_with($host, 'youtube.com')
            || str_ends_with($host, 'youtube-nocookie.com')
            || str_ends_with($host, 'vimeo.com');
    }

    private function fallbackSanitize(string $html): string
    {
        $html = strip_tags($html, '<' . implode('><', self::ALLOWED_TAGS) . '>');
        $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? '';
        $html = preg_replace('/\s+(href|src|cite)\s*=\s*("|\')?\s*javascript:[^"\'>\s]*(\2)?/i', '', $html) ?? '';
        $html = preg_replace('/<(script|style|object|embed|form|input|button)\b[^>]*>.*?<\/\1>/is', '', $html) ?? '';

        return trim($html);
    }
}
