# Phase 9 SEO Audit Report

## Scope

This audit covers the public website SEO layer implemented in Phase 9: metadata, canonical URLs, Open Graph/Twitter cards, JSON-LD schema, sitemap, robots.txt, analytics hooks, mobile readiness, and Core Web Vitals instrumentation.

## Metadata Coverage

- Homepage, service listing, service detail, project listing, project detail, shop listing, category, product detail, about, HSE, and contact pages provide page-specific titles, descriptions, canonical URLs, keywords, and social preview metadata.
- Default metadata is centralized in `config/seo.php` for fallback rendering.
- Search verification tags are configurable through `GOOGLE_SITE_VERIFICATION` and `BING_SITE_VERIFICATION`.

## Structured Data

- Homepage: Organization, LocalBusiness, and WebSite schema.
- Service pages: Service, BreadcrumbList, and FAQPage schema.
- Product pages: Product and BreadcrumbList schema.
- Contact page: Organization, LocalBusiness, ContactPoint, and BreadcrumbList schema.
- Static corporate pages: Organization and BreadcrumbList schema.

## Search Files

- `public/sitemap.xml` includes public pages, service landing pages, shop products, shop categories, and project detail URLs when published project slugs exist.
- `public/sitemap.xml.gz` is generated for compressed sitemap submission.
- `public/robots.txt` allows public crawling and blocks admin, storage, vendor, database, config, and script paths.

## Analytics

- Google Analytics 4 loads when `GA_MEASUREMENT_ID` is configured.
- Frontend event hooks track lead generation, checkout start, product selection, contact intent, and Core Web Vitals metrics.
- Web Vitals events include LCP, CLS, and FID where browser observer support exists.

## Mobile and Core Web Vitals

- The base layout includes the required responsive viewport tag.
- Generated CSS is minified through the existing Tailwind production build.
- JavaScript remains lightweight and deferred.
- Image tags already use responsive dimensions and lazy loading across service, project, and product grids.

## External Validation Checklist

- Validate JSON-LD with Schema Markup Validator.
- Submit `https://www.desnkygroup.com/sitemap.xml` in Google Search Console and Bing Webmaster Tools.
- Run PageSpeed Insights for homepage, service detail, product detail, and contact pages.
- Confirm GA4 real-time events after setting `GA_MEASUREMENT_ID`.
