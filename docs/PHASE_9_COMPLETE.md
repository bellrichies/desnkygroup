# Phase 9 Complete: SEO and Analytics

Phase 9 implements the SEO and analytics requirements from `04-DEVELOPMENT-PHASES.md` using the feature implementation pattern from `05-COPILOT-PROMPTS.md`.

## Delivered

- Config-backed SEO defaults, production base URL, search verification tags, organization contact data, and analytics settings.
- Expanded SEO helper with fluent metadata setters, canonical URLs, keywords, Open Graph tags, Twitter card tags, and JSON-LD graph support.
- Global SEO partial and analytics partial included from the frontend base layout.
- Organization, WebSite, LocalBusiness, Service, Product, BreadcrumbList, FAQPage, and ContactPoint schema support.
- Page-specific schema and keywords for homepage, services, service details, shop pages, product details, projects, about, HSE, and contact.
- Public project detail route for SEO-friendly project URLs.
- Sitemap service and CLI generator for `sitemap.xml`, `sitemap.xml.gz`, and `robots.txt`.
- Robots rules that allow public pages and block admin/private directories.
- Google Analytics 4 hook through `GA_MEASUREMENT_ID`.
- Frontend analytics event tracking for lead forms, product selection, contact intent, checkout start, and Core Web Vitals metrics.
- SEO audit report and implementation checklist.
- Unit tests for SEO metadata rendering and sitemap/robots generation.

## Verification

- `Get-ChildItem app,config,scripts,tests -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }`
- `vendor\bin\phpunit`
- `vendor\bin\phpstan analyze app --level 5 --no-progress`
- `vendor\bin\phpcs --standard=PSR12 -n` on touched Phase 9 PHP class/config/script/test files
- `php scripts\generate_seo.php`
- `php scripts\migrate.php --test`
- `composer audit`
- `npm run audit`
- `npm run build`
- Local HTTP checks for `/services/engineering`, `/sitemap.xml`, and `/robots.txt`

## Notes

- `npm run build` succeeds; Tailwind reports the existing stale Browserslist database notice.
- GA4 tracking is inactive until `GA_MEASUREMENT_ID` is set.
- Google/Bing verification tags are inactive until `GOOGLE_SITE_VERIFICATION` and `BING_SITE_VERIFICATION` are set.
- External tools still need live-domain validation after deployment: Schema Markup Validator, PageSpeed Insights, Google Search Console, and Bing Webmaster Tools.
