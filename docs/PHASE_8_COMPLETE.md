# Phase 8 Complete: Security and Performance

Phase 8 implements the security hardening and performance optimization requirements from `04-DEVELOPMENT-PHASES.md` using the feature implementation pattern in `05-COPILOT-PROMPTS.md`.

## Delivered

- Global security headers for dynamic responses, including CSP, frame protection, MIME sniffing protection, referrer policy, permissions policy, and optional HSTS.
- File-backed rate limiting middleware with configurable limits and `Retry-After` support.
- File cache service with TTL, `remember()`, expired-entry cleanup, and tag-based invalidation.
- Published service and project read caching with automatic invalidation after create, update, or delete operations.
- Expanded validation rules for whitelist values, slugs, URLs, integers, booleans, HTML rejection, suspicious payload rejection, and input sanitization.
- Contact form sanitization before validation and persistence.
- Upload hardening with extension checks, MIME validation, image signature validation, randomized filenames, metadata stripping when GD is available, and non-executable upload directory rules.
- Apache gzip, static asset cache headers, and upload execution restrictions.
- Database performance index migration with schema-aware guards for optional module columns.
- Security and cache unit tests.

## Verification

- `Get-ChildItem app,config,database,tests -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }`
- `vendor\bin\phpunit`
- `vendor\bin\phpstan analyze app --level 5 --no-progress`
- `vendor\bin\phpcs --standard=PSR12 -n` on touched Phase 8 PHP files
- `php scripts\migrate.php --test`
- `php scripts\migrate.php`
- `composer audit`
- `npm audit --audit-level=moderate`
- `npm run build`

## Notes

- The broad repository PHPCS run still reports pre-existing CRLF/style issues outside the Phase 8 touched files. The focused Phase 8 PHPCS pass is clean.
- `npm run build` succeeds; Tailwind reports the standard stale Browserslist database notice.
- HSTS is controlled by `FORCE_HTTPS=true`, and rate limiting can be tuned with `RATE_LIMIT_ENABLED`, `RATE_LIMIT_MAX_ATTEMPTS`, and `RATE_LIMIT_WINDOW_SECONDS`.
