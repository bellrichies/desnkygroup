<?php

namespace App\Middleware;

use App\Config;

/**
 * SecurityHeaders - Add security headers to responses
 *
 * Sets headers like X-Frame-Options, X-Content-Type-Options, etc.
 */
class SecurityHeaders extends Middleware
{
    /**
     * Handle security headers
     *
     * @return mixed
     */
    public function handle()
    {
        // Prevent clickjacking
        header('X-Frame-Options: DENY');

        // Prevent MIME sniffing
        header('X-Content-Type-Options: nosniff');

        // Enable XSS protection (deprecated but still useful)
        header('X-XSS-Protection: 1; mode=block');

        // Disable client-side caching for sensitive data
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $contentSecurityPolicy = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' cdn.tailwindcss.com",
            "style-src 'self' 'unsafe-inline' cdn.tailwindcss.com",
            "img-src 'self' data: https:",
        ];
        header('Content-Security-Policy: ' . implode('; ', $contentSecurityPolicy) . ';');

        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // Feature Policy / Permissions Policy
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        if ((bool) Config::get('security.headers.hsts', false)) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }

        return null;
    }
}
