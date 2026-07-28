# Phase 10 Testing Report

## Automated Coverage Added

- Frontend rendering integration tests for homepage, contact, and projects pages.
- Middleware integration tests for admin authentication redirects, CSRF rejection, and request throttling.
- Shop workflow integration tests for cart add, stock capping, out-of-stock rejection, and empty checkout rejection.
- Static QA audit script for sitemap, robots, asset budgets, analytics asset presence, and upload execution protection.

## Test Matrix

| Area              | Coverage                                                                                      |
| ----------------- | --------------------------------------------------------------------------------------------- |
| Unit tests        | Config, container, router, form helper, validators, cache, SEO helper, sitemap service        |
| Integration tests | Frontend rendering, middleware behavior, cart and checkout workflows                          |
| API behavior      | JSON error/success paths in cart, checkout, CSRF, and throttling                              |
| Security          | CSRF failure, auth redirect, rate limit enforcement, dependency audits                        |
| Performance       | Minified CSS build, static asset budget checks, sitemap generation                            |
| Accessibility     | H1 checks, viewport checks, image alt checks, form label checks                               |
| Mobile            | Responsive viewport and existing mobile-first Tailwind layouts verified through static checks |

## Manual Browser Checklist

- Chrome latest: homepage, service detail, product detail, cart, checkout, contact form.
- Edge latest: homepage navigation, mobile menu, form validation, cart update.
- Firefox latest: service/project filters, shop browsing, admin login screen.
- Safari/iOS: mobile navigation, touch targets, contact and checkout forms.
- Android Chrome: homepage, services, shop product cards, cart quantity controls.

## Load Test Guidance

Run on a deployed or local Apache/PHP server:

```bash
ab -n 1000 -c 100 https://www.desnkygroup.com/
ab -n 500 -c 50 https://www.desnkygroup.com/services/engineering
ab -n 500 -c 50 https://www.desnkygroup.com/shop
```

Targets:

- No failed requests.
- 95th percentile response time under 750ms for cached public pages.
- No PHP fatal errors in `storage/logs/app.log`.

## Residual Notes

- Full line coverage requires Xdebug or PCOV on the machine running PHPUnit.
- External cross-browser and mobile device testing should be repeated after deployment because local PHP server behavior differs from Apache/cPanel.
