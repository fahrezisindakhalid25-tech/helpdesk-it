<?php

namespace App\Support;

use App\Models\Ticket;
use DOMDocument;
use DOMElement;
use DOMNode;

class TicketSecurity
{
    private const ALLOWED_TAGS = [
        'a',
        'blockquote',
        'br',
        'code',
        'div',
        'em',
        'figcaption',
        'figure',
        'img',
        'li',
        'ol',
        'p',
        'pre',
        'strong',
        'u',
        'ul',
    ];

    private const DROP_TAGS = [
        'embed',
        'iframe',
        'object',
        'script',
        'style',
    ];

    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt'],
    ];

    public static function generateAccessToken(Ticket $ticket): string
    {
        $payload = implode('|', [
            (string) $ticket->uuid,
            strtolower((string) $ticket->email),
            (string) optional($ticket->created_at)->timestamp,
        ]);

        return hash_hmac('sha256', $payload, (string) config('app.key'));
    }

    public static function hasValidAccessToken(Ticket $ticket, ?string $token): bool
    {
        if (! is_string($token) || $token === '') {
            return false;
        }

        return hash_equals(self::generateAccessToken($ticket), $token);
    }

    public static function trackingUrl(Ticket $ticket): string
    {
        return route('laporan.cek', [
            'uuid' => $ticket->uuid,
            'token' => self::generateAccessToken($ticket),
        ], true);
    }

    public static function sanitizePlainText(?string $value): string
    {
        return trim(strip_tags((string) $value));
    }

    public static function sanitizeRichText(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        $previousState = libxml_use_internal_errors(true);

        $document = new DOMDocument('1.0', 'UTF-8');
        $wrappedHtml = '<div>' . $html . '</div>';
        $loaded = $document->loadHTML('<?xml encoding="utf-8" ?>' . $wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        libxml_clear_errors();
        libxml_use_internal_errors($previousState);

        if (! $loaded) {
            return nl2br(e(self::sanitizePlainText($html)));
        }

        $root = $document->getElementsByTagName('div')->item(0);

        if (! $root instanceof DOMElement) {
            return nl2br(e(self::sanitizePlainText($html)));
        }

        self::sanitizeNode($root);

        $output = '';

        foreach (iterator_to_array($root->childNodes) as $childNode) {
            $output .= $document->saveHTML($childNode);
        }

        return trim($output);
    }

    public static function plainTextFromRichText(?string $html): string
    {
        return trim(html_entity_decode(strip_tags((string) $html), ENT_QUOTES, 'UTF-8'));
    }

    private static function sanitizeNode(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $childNode) {
            self::sanitizeNode($childNode);
        }

        if (! $node instanceof DOMElement) {
            return;
        }

        $tag = strtolower($node->tagName);

        if (in_array($tag, self::DROP_TAGS, true)) {
            $node->parentNode?->removeChild($node);
            return;
        }

        if (! in_array($tag, self::ALLOWED_TAGS, true)) {
            self::unwrapNode($node);
            return;
        }

        $allowedAttributes = self::ALLOWED_ATTRIBUTES[$tag] ?? [];

        foreach (iterator_to_array($node->attributes) as $attribute) {
            $name = strtolower($attribute->nodeName);

            if (str_starts_with($name, 'on') || ! in_array($name, $allowedAttributes, true)) {
                $node->removeAttribute($attribute->nodeName);
                continue;
            }

            $value = trim($attribute->nodeValue);

            if (in_array($name, ['href', 'src'], true) && ! self::isSafeUrl($value)) {
                if ($tag === 'img') {
                    $node->parentNode?->removeChild($node);
                    return;
                }

                $node->removeAttribute($attribute->nodeName);
                continue;
            }

            if ($tag === 'a' && $name === 'target' && $value !== '_blank') {
                $node->removeAttribute('target');
            }
        }

        if ($tag === 'a' && $node->hasAttribute('target')) {
            $node->setAttribute('rel', 'noopener noreferrer');
        }
    }

    private static function unwrapNode(DOMElement $node): void
    {
        $parent = $node->parentNode;

        if (! $parent) {
            return;
        }

        while ($node->firstChild) {
            $parent->insertBefore($node->firstChild, $node);
        }

        $parent->removeChild($node);
    }

    private static function isSafeUrl(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        if (str_starts_with($url, ['/','./','../','#'])) {
            return true;
        }

        if (preg_match('/^(https?):/i', $url) === 1) {
            return true;
        }

        return false;
    }
}
