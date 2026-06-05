<?php

namespace App\Middleware;

use App\Config;

/**
 * Adds baseline browser security headers to every dynamic response.
 */
class SecurityHeaders extends Middleware
{
    public function handle()
    {
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');

        $contentSecurityPolicy = [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "object-src 'none'",
            // 'unsafe-eval' is required by Alpine.js (public) and the Tailwind Play CDN (admin),
            // both of which compile expressions at runtime via Function().
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.tailwindcss.com",
            "style-src 'self' 'unsafe-inline' cdn.tailwindcss.com fonts.googleapis.com",
            "font-src 'self' fonts.gstatic.com data:",
            "img-src 'self' data: https:",
            "connect-src 'self'",
            // Google Maps embed (contact page map facade).
            "frame-src 'self' https://www.google.com https://maps.google.com",
        ];
        header('Content-Security-Policy: ' . implode('; ', $contentSecurityPolicy) . ';');

        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), geolocation=(), microphone=(), payment=(), usb=()');

        if ($this->isSensitiveRoute()) {
            header('Cache-Control: no-store, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
        } else {
            header('Cache-Control: private, no-cache, must-revalidate');
        }

        if ((bool) Config::get('security.headers.hsts', false)) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }

        return null;
    }

    private function isSensitiveRoute(): bool
    {
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $method = (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');

        return $method !== 'GET'
            || str_starts_with($uri, '/admin')
            || str_starts_with($uri, '/api')
            || str_starts_with($uri, '/shop/cart')
            || str_starts_with($uri, '/shop/checkout');
    }
}
