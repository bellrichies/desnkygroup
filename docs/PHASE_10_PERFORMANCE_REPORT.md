# Phase 10 Performance Report

## Automated Checks

- `npm run build` generates minified Tailwind CSS.
- `scripts/qa_audit.php` checks CSS and analytics JavaScript size budgets.
- Public sitemap and robots files are generated and validated.
- Phase 8 caching and headers remain in place for public performance.

## Current Budgets

- `public/assets/css/main.min.css`: target under 250 KB.
- `public/assets/js/analytics.js`: target under 20 KB.
- Public pages should avoid blocking scripts; analytics is deferred unless GA4 is configured.

Latest local QA audit:

- `public/assets/css/main.min.css`: 36,053 bytes.
- `public/assets/js/analytics.js`: 3,239 bytes.
- Static QA audit: passed.

## Load Testing Plan

Use ApacheBench or an equivalent load tool against production-like hosting:

```bash
ab -n 1000 -c 100 https://www.desnkygroup.com/
ab -n 1000 -c 100 https://www.desnkygroup.com/services
ab -n 500 -c 50 https://www.desnkygroup.com/contact
```

Acceptance targets:

- Zero failed requests.
- No 5xx responses.
- Stable memory usage.
- No slow query or fatal error spikes in application logs.
