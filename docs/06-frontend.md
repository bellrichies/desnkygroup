# 🎨 Frontend Redesign Blueprint
## Desnky Global Resources Ltd — Corporate Website Frontend Architecture & UX Strategy

**Document Version:** 1.0
**Date:** June 5, 2026
**Status:** Production-Ready Implementation Blueprint
**Owners:** Senior Frontend Architect · Senior UI/UX Designer · Product Experience Strategist
**Source of Truth:** `.github/BRIEF.md`, `.github/SKILL.md`, `docs/01-PRODUCT-STRATEGY.md`, `docs/02-ARCHITECTURE-DESIGN.md`, `docs/03-TECHNICAL-IMPLEMENTATION.md`

---

## How to Read This Document

This blueprint is the **single, authoritative reference** for the Desnky Group frontend redesign. It translates the documented business strategy, brand positioning, and technical architecture into an actionable design and engineering specification.

It is deliberately written to be **implementation-ready**: designers can build mockups directly from the design system and page plans; developers can build views directly from the component architecture, design tokens, and roadmap — all within the existing **custom PHP 8 MVC + Tailwind CSS + Alpine.js** stack mandated by `SKILL.md`.

> **Grounding principle:** This redesign *evolves* the existing implementation rather than discarding it. The current codebase already establishes a coherent token set (`desnky.*` palette, Inter typeface, `container-page` / `section-band` / `btn-*` component classes, CMS-backed `page_sections`). This document formalizes, extends, and elevates that foundation to an enterprise-grade standard.

### Table of Contents

1. [Frontend Vision & Design Strategy](#1-frontend-vision--design-strategy)
2. [Information Architecture](#2-information-architecture)
3. [Design System Specification](#3-design-system-specification)
4. [Page-by-Page Redesign Plan](#4-page-by-page-redesign-plan)
5. [Homepage Redesign](#5-homepage-redesign)
6. [Component Architecture](#6-component-architecture)
7. [Mobile Experience Strategy](#7-mobile-experience-strategy)
8. [Accessibility Standards](#8-accessibility-standards)
9. [Frontend Technology Recommendations](#9-frontend-technology-recommendations)
10. [Performance Optimization Plan](#10-performance-optimization-plan)
11. [Visual Inspiration & Creative Direction](#11-visual-inspiration--creative-direction)
12. [Implementation Roadmap](#12-implementation-roadmap)
13. [Appendix: Quality & Consistency Review](#13-appendix-quality--consistency-review)

---

## 1. Frontend Vision & Design Strategy

### 1.1 Overall Design Philosophy

Desnky Global Resources Ltd operates across **six industrial sectors** — Energy, Engineering, Procurement, Safety/HSE, ICT, and Agro Products & Food Processing — serving a primarily **B2B audience** (oil & gas operators, industrial manufacturers, energy companies, government agencies, construction firms) with a secondary **B2C channel** (safety equipment, agro-products, general procurement).

This duality defines the design philosophy:

> **"Industrial Confidence, Digital Clarity."**

The frontend must feel like the website of a **company you would trust with a multi-million-naira engineering contract** — substantial, precise, and credible — while remaining **fast, frictionless, and conversion-focused** enough to capture a quick safety-equipment order or a same-day project enquiry.

**Five design pillars:**

| Pillar | Definition | How It Manifests |
|---|---|---|
| **Credibility-first** | Every pixel earns trust | Real project imagery, certifications, HSE proof points, concrete statistics over generic stock photography |
| **Structured clarity** | Industrial complexity made navigable | Strong visual hierarchy, generous whitespace, scannable service cards, predictable layouts |
| **Premium restraint** | Confidence through discipline, not decoration | Limited palette, purposeful motion, no gratuitous gradients or effects |
| **Conversion-engineered** | Every page has a job | Persistent, contextual CTAs ("Request a Quote", "Discuss a Project") with measurable goals |
| **Performance as design** | Speed is a brand attribute | Sub-2.5s LCP, lazy media, minimal JS — the 90+ PageSpeed target is a *design constraint*, not an afterthought |

### 1.2 Brand Experience Guidelines

The brand experience is built on the documented visual direction in `BRIEF.md §9` (corporate, clean, trustworthy, industrial, modern, professional) and the established palette.

- **Tone:** Authoritative but approachable. Copy is direct, jargon-light, outcome-oriented. We sell *reliability and capability*, not buzzwords.
- **Color story:** **Corporate purple `#6c3483`** (the *primary brand color* — authority, distinction, premium quality) drives **all primary CTAs**, links, and accents; **brand green `#008000`** (the *secondary brand color* — safety, sustainability, growth, agro) is reserved for success states, HSE/Agro accents, and supporting highlights (**not CTAs**); a **deep aubergine dark `#1d1228`** anchors heroes, dark sections, and the footer. The two brand colors carry the identity; the dark anchor and neutrals give it gravitas.
- **One consistent CTA color:** the call-to-action is **always purple**. On light backgrounds it is a solid purple button; on dark backgrounds — where solid purple lacks contrast — it *inverts* to a white surface with a purple label (still reads as the same brand CTA). Green is never used as a CTA.
- **Imagery doctrine:** Prefer authentic, on-site, well-lit photography of industrial environments, equipment, teams in PPE, and completed projects. Apply a consistent dark-aubergine duotone/overlay treatment on hero and section backgrounds for cohesion. Never use cliché handshake/skyscraper stock.
- **Voice across sectors:** A single brand voice with **sector-specific accent colors** so a visitor always knows whether they're reading about Energy, HSE, or Agro without the brand fragmenting.
- **Consistency contract:** Identical header, footer, spacing rhythm, button behavior, and motion vocabulary on **every** page, public and shop.

### 1.3 User-Centric Design Principles

Derived from the seven personas in `01-PRODUCT-STRATEGY.md §4`, the public frontend optimizes for **Guest/Anonymous → B2B Lead** and **Guest → B2C Customer** journeys.

1. **Anticipate intent, reduce decisions.** Procurement managers arrive task-focused. Surface the right CTA at the right scroll depth; never make a visitor hunt for "how do I contact / get a quote / buy."
2. **Progressive disclosure.** Lead with outcomes and proof; let detail (specs, process, policy) unfold for those who want it. The home page sells the *company*; service pages sell the *capability*; the contact/quote flow *closes*.
3. **Scannability over density.** F-pattern and Z-pattern layouts, short paragraphs, descriptive headings, iconography, and "at-a-glance" stat blocks respect busy professionals.
4. **Trust at every scroll depth.** Certifications, client logos, project counts, HSE commitment, and testimonials are distributed throughout — not buried in an "About" page.
5. **Mobile is the primary canvas.** Nigerian B2B and B2C traffic skews heavily mobile; the desktop experience is an *enhancement* of a mobile-complete design.
6. **Accessibility is non-negotiable.** WCAG 2.1 AA is a baseline acceptance criterion, not a feature.

### 1.4 Conversion Optimization Strategy

The business success metrics (`01-PRODUCT-STRATEGY.md §7`) define the conversion targets: **≥2% lead conversion, ≥50 contact submissions/month, ≥100 newsletter subscriptions/month, 20+ orders/month**.

**Primary conversion actions (ranked):**

1. **Request a Quote / Discuss a Project** (B2B lead — highest value)
2. **Contact / Submit Enquiry** (B2B lead)
3. **Add to Cart → Checkout** (B2C revenue)
4. **Newsletter Subscribe** (nurture funnel)

**Conversion mechanics:**

| Technique | Implementation |
|---|---|
| **Persistent primary CTA** | "Request a Quote" button anchored in header (desktop) and mobile sticky bar |
| **Contextual mid-page CTAs** | Each service section ends with a sector-relevant CTA ("Discuss your Energy project") |
| **Closing conversion band** | Every page ends with a high-contrast navy CTA block before the footer |
| **Friction reduction** | Contact form: AJAX submit, inline validation, no page reload, instant success state |
| **Trust adjacency** | Place social proof (logos, stats, testimonials) immediately *before* CTAs to prime action |
| **Sticky mobile action bar** | Persistent "Call · WhatsApp · Quote" bar on mobile for one-tap conversion |
| **Exit-aware newsletter** | Newsletter capture in footer + optional gentle inline prompt, never an intrusive modal that harms CWV/UX |
| **Shop urgency cues** | Stock status badges, "low stock" indicators, clear pricing, frictionless add-to-cart |

**Measurement:** Every CTA carries `data-analytics-event` attributes wired to GA4 (per `analytics.js`), enabling per-CTA conversion tracking and A/B iteration.

### 1.5 Mobile-First Implementation Approach

Mobile-first is **mandated** by `SKILL.md §9` and `BRIEF.md`. Concretely:

- **Author base styles for the 360–414px viewport**, then layer enhancements at `sm → 2xl` Tailwind breakpoints. Never write desktop-first overrides.
- **Single-column by default**, expanding to multi-column grids at `md`/`lg`.
- **Touch-first targets**: ≥44×44px tap areas, generous spacing, thumb-reachable primary actions.
- **Performance budget enforced on mid-range Android over 4G**, not just desktop fiber.
- **Conditional enhancement**: heavier interactions (hover reveals, parallax, multi-column mega-menus) are additive for pointer/large-screen contexts and degrade gracefully.

---

## 2. Information Architecture

### 2.1 Complete Sitemap

The IA consolidates the structures defined in `BRIEF.md §5`, `SKILL.md §26`, and the live routes in `routes/web.php`.

```
Desnky Global Resources Ltd
│
├── Home (/)
│
├── About Us (/about)
│   ├── Company Overview
│   ├── Mission · Vision · Core Values
│   ├── Leadership / Team
│   └── Certifications & Credentials
│
├── Services (/services)                         [Hub / index]
│   ├── Engineering Services (/services/engineering)
│   ├── Energy Solutions (/services/energy-solutions)
│   ├── Procurement Services (/services/procurement)
│   ├── Safety / HSE Services (/services/hse-safety)
│   ├── ICT Solutions (/services/ict-solutions)
│   └── Agro Products & Food Processing (/services/agro-food-processing)
│
├── Projects (/projects)                         [Portfolio index]
│   └── Project Detail (/projects/{slug})
│
├── HSE Policy (/hse-policy)
│
├── Shop (/shop)                                 [Ecommerce]
│   ├── Category (/shop/category/{slug})
│   ├── Product Detail (/shop/product/{slug})
│   ├── Cart (/shop/cart)
│   ├── Checkout (/shop/checkout)
│   └── Order Confirmation (/shop/checkout → confirmation state)
│
├── Blog / Insights (/blog)                      [Editorial CMS — roadmap]
│   ├── Post Detail (/blog/{slug})
│   ├── Category Archive (/blog/category/{slug})
│   ├── Tag Archive (/blog/tag/{slug})
│   └── Author Archive (/blog/author/{slug})
│
├── Contact (/contact)
│
└── Utility / Legal
    ├── Privacy Policy (/privacy-policy)
    ├── Terms of Use (/terms-of-use)
    ├── Sitemap (sitemap.xml)
    └── 404 / Error pages
```

**Legacy redirects (preserve SEO equity, per `BRIEF.md §8`):**

```
/services.html  → 301 → /services
/contact.html   → 301 → /contact
/hse.html       → 301 → /hse-policy
/gallery.html   → 301 → /projects
```

### 2.2 Navigation Structure

**Primary navigation (desktop header):**

```
[ DG Logo ]   Home   About   Services ▾   Projects   Shop   Contact      [ Request a Quote ]
```

- **Services ▾** is a **mega-menu** (pointer devices) exposing all six sector pages with icon + one-line descriptor, plus a "View all services" link and a contextual "Discuss a project" CTA panel.
- **Shop** appears in primary nav (ecommerce is a core revenue feature); cart indicator with item count when items present.
- **Request a Quote** is the persistent primary CTA — **always purple**: a solid purple button on the light/solid header, automatically *inverted* to a white-surface/purple-label button on the transparent home header over the dark hero (same brand CTA, contrast-safe on dark).

**Secondary navigation (footer):**

- **Column 1 — Company:** About, HSE Policy, Projects, Blog, Contact
- **Column 2 — Services:** all six sector links
- **Column 3 — Shop:** Shop home, Categories, Cart, Order info
- **Column 4 — Contact block:** address (Lagos), phone(s), email, social icons, newsletter form
- **Footer base bar:** © Desnky Global Resources Ltd · Privacy Policy · Terms of Use · Sitemap

**Header behavior (already implemented, formalized here):**

- On **home**: header is `fixed` + **transparent** over the hero, transitioning to a solid white, blurred, shadowed state on scroll (`site-header--scrolled`). Logic lives in `header-scroll.js`.
- On **all other pages**: header is `sticky`, solid white with subtle border/blur from first paint.

### 2.3 Menu Hierarchy

```
Level 0 (Global, always visible)
├── Logo (→ Home)
├── Primary nav links
├── Request a Quote (primary CTA)
└── Mobile menu toggle (< lg)

Level 1 (Services mega-menu / mobile accordion)
├── Engineering Services
├── Energy Solutions
├── Procurement Services
├── Safety / HSE Services
├── ICT Solutions
├── Agro Products & Food Processing
├── → View all services
└── → Discuss a project (CTA panel)

Level 2 (Shop sub-navigation, contextual on /shop/*)
├── All Products
├── Category filters (dynamic from product_categories)
├── Search
└── Cart

Level 3 (In-page anchored sub-nav on long pages)
└── Sticky section anchors on Service detail & About (e.g., Overview · Capabilities · Process · Projects · FAQ)
```

The menu is **CMS-aware**: navigation items map to published `pages`/`services`, so adding a sector or page in the admin surfaces it in nav without code changes (driven by a `NavigationService`/menu config).

### 2.4 User Flow Diagrams

**Flow A — B2B Lead Generation (primary):**

```
Entry (Home / Service page / Organic search)
   ↓
Scan value proposition + trust signals
   ↓
Explore specific Service sector page
   ↓
Read capabilities + view relevant projects
   ↓ ───────────────┐
Click "Request a    │  (or) Click header "Request a Quote"
Quote / Discuss"    │
   ↓ ───────────────┘
Contact form (pre-filled service interest)
   ↓
AJAX submit → inline success → confirmation email
   ↓
Admin notified → lead enters CRM/enquiry pipeline
```

**Flow B — B2C Product Purchase (revenue):**

```
Entry (Shop / Product search / Category)
   ↓
Browse / filter products by category
   ↓
Product detail → review price, stock, description, images
   ↓
Add to Cart  → cart drawer/confirmation
   ↓
Cart page → adjust quantities → Proceed to Checkout
   ↓
Checkout: contact + delivery + payment method (Pay on Delivery / Bank Transfer)
   ↓
Server-side total + stock validation → Place Order
   ↓
Order confirmation page + email (customer & admin)
```

**Flow C — Trust & Research (nurture):**

```
Entry → About / Projects / HSE Policy / Blog
   ↓
Consume proof content (certifications, case studies, policy, insights)
   ↓
Newsletter subscribe  (low-commitment conversion)
   ↓
Nurtured → returns later → enters Flow A or B
```

### 2.5 Content Organization Strategy

- **Hub-and-spoke for Services:** `/services` is the hub that frames the six sectors; each spoke (`/services/{slug}`) is a deep, SEO-optimized landing page with its own keyword target (`BRIEF.md §7`).
- **Proof distributed, not siloed:** Projects, stats, testimonials, and client logos appear contextually across Home, Service, and About pages — reinforcing credibility wherever the visitor is.
- **CMS-driven sections:** All page content is composed of editable `page_sections` (hero, intro, cards, CTA, etc.), so the structure below maps to admin-manageable blocks. Frontend views render **defensively** (every section guarded by `if (!empty(...))`), exactly as `home.php` already does.
- **Internal linking:** Service pages cross-link to related services, relevant projects, and the shop where applicable (e.g., HSE Services → Safety Equipment category) — strengthening SEO and journey continuity.
- **One H1 per page, strict H2/H3 hierarchy** (`SKILL.md §11`), with content authored for both humans and search engines.

---

## 3. Design System Specification

The design system formalizes and extends the tokens already present in `tailwind.config.js` and `resources/css/main.css`. **All values below are the canonical source**; `tailwind.config.js` is the implementation of record.

### 3.1 Color Palette

The brand is built on **two corporate colors** — **purple (primary)** and **green (secondary)** — anchored by a deep aubergine dark and neutral grays. These are implemented in `tailwind.config.js` (canonical) and mirrored as CSS variables in `resources/css/main.css`.

**Brand & core tokens (`desnky.*`):**

| Token | Hex | Role |
|---|---|---|
| `desnky.primary` | `#6c3483` | **Primary brand color** — primary buttons (on light), links, eyebrows (on light), active states, focus rings, icon accents |
| `desnky.primary-700` | `#58296b` | Primary hover / pressed shade |
| `desnky.primary-200` | `#d8c4e4` | Light tint — accent text, links, eyebrows, breadcrumb hovers **on dark backgrounds** |
| `desnky.primary-50` | `#f3edf7` | Tinted surfaces, selected/active backgrounds |
| `desnky.secondary` | `#008000` | **Secondary brand color** — success states, HSE/Agro accents, supporting highlights/numerals (**not used for CTAs**) |
| `desnky.secondary-700` | `#006b00` | Secondary hover shade |
| `desnky.secondary-50` | `#e6f2e6` | Success/agro tinted surfaces |
| `desnky.dark` | `#1d1228` | Deep aubergine anchor — hero overlays, dark sections, headings |
| `desnky.darker` | `#150d1d` | **Footer** (true dark theme) |
| `desnky.ink` | `#1f1626` | Body text |
| `desnky.muted` | `#6b6675` | Secondary text, captions, metadata |
| `desnky.surface` | `#f7f5fa` | Section backgrounds, cards, subtle bands |

> **Legacy aliases:** the prior token names remain in the config remapped to the new brand so existing view classes keep working during migration — `desnky.navy → #1d1228` (dark), `desnky.blue → #6c3483` (primary), `desnky.gold → #d8c4e4` (on-dark light accent), `desnky.green → #008000` (secondary). New work should use the semantic names above; aliases are deprecated and should be migrated opportunistically.

**Sector accent map (semantic aliases → reuse the two brand hues + dark):**

```js
// tailwind.config.js → theme.extend.colors.desnky.sector
sector: {
  engineering: '#6c3483',  // primary (purple)
  energy:      '#008000',  // secondary (green)
  procurement: '#1d1228',  // dark anchor
  hse:         '#008000',  // secondary (green)
  ict:         '#6c3483',  // primary (purple — distinguished by icon/imagery)
  agro:        '#008000',  // secondary (green)
},
```

> Sectors are differentiated primarily by **iconography and imagery**; the two-color brand keeps the system disciplined rather than assigning six competing hues.

**Semantic / status colors (align with `form-error` already using `text-red-700`):**

| State | Token | Notes |
|---|---|---|
| Success | `desnky.secondary` / `green-700` | Confirmation, in-stock, valid |
| Error/Danger | `red-700` | Form errors, destructive, out-of-stock |
| Warning | `amber-600` | Low stock, caution |
| Info | `desnky.primary` | Neutral/brand notices |

**Contrast compliance (WCAG AA — verified):**

- **Purple `#6c3483` + white text = 8.55:1** ✓✓ (AAA) — primary buttons, primary CTA on light backgrounds.
- **Green `#008000` + white text = 5.13:1** ✓ (AA normal text) — success badges, HSE/Agro accents (not CTAs).
- **Inverted CTA on dark (`.btn-on-dark`):** white surface + purple label = **8.55:1** ✓ (AAA). This is how the single purple CTA stays contrast-safe on the dark anchor (solid purple on dark aubergine is only ~2:1).
- Purple `#6c3483` on white = **8.55:1** ✓ (links, eyebrows, accent text on light).
- Dark `#1d1228` on white ≈ **16:1** ✓; white on dark ≈ **16:1** ✓ (headings, dark sections, footer).
- Light tint `desnky.primary-200 #d8c4e4` on dark `#1d1228` ≈ **9:1** ✓ (eyebrows/links on dark heroes).
- `desnky.muted #6b6675` on white ≈ **5.0:1** ✓ (body/secondary text).
- **Rule:** the CTA is always purple. Never place *solid* purple on the dark anchor (low contrast) — use the inverted `.btn-on-dark` (white surface + purple label) there. Use `primary-200` light purple for purple *text* (eyebrows/links) on dark.

### 3.2 Typography System

**Typeface:** `Inter` (per `SKILL.md §10` and base layout), loaded via Google Fonts with `preconnect` (already in `base.php`), weights 400/500/600/700/800, `font-display: swap`.

> **Optional premium upgrade:** a display serif/grotesque could be considered for hero H1s to add editorial gravitas, but the **single-family Inter system is recommended** for performance and consistency. If a display face is introduced, self-host it and limit to hero usage only.

**Type scale (mobile-first; values are Tailwind classes):**

| Element | Mobile | Desktop (`sm`+) | Weight | Color | Notes |
|---|---|---|---|---|---|
| Hero H1 | `text-4xl` (2.25rem) | `sm:text-6xl` (3.75rem) | 700 | white/navy | `leading-tight`, `max-w-4xl` — matches `home.php` |
| Page H1 | `text-3xl` | `md:text-5xl` | 700 | navy | One per page |
| Section H2 | `text-3xl` | `text-3xl`/`text-4xl` | 700 | navy | `home.php` uses `text-3xl` |
| Card/Sub H3 | `text-xl` | `text-xl` | 700 | navy | |
| Eyebrow/Kicker | `text-sm` | `text-sm` | 600 | sector accent | `uppercase tracking-wide` |
| Body large (lead) | `text-lg` | `text-lg` | 400 | ink/gray-100 | `leading-8` |
| Body | `text-base` | `text-base` | 400 | ink | `leading-7` |
| Small/Meta | `text-sm` | `text-sm` | 400/500 | muted | `leading-6` |
| Overline/Caption | `text-xs` | `text-xs` | 500 | muted | |

**Typographic rules:**

- **Line length:** body capped at `max-w-2xl`/`max-w-3xl` (~65–75ch) for readability.
- **Line height:** headings `leading-tight`; body `leading-7`/`leading-8`.
- **Letter spacing:** eyebrows `tracking-wide` + `uppercase`; headings default; avoid tracking on body.
- **Rich text (CMS/blog):** apply `@tailwindcss/typography` (`prose`) scoped to `desnky` colors for TipTap-rendered HTML.

### 3.3 Spacing System

Built on Tailwind's 4px base scale, extended with the existing custom steps (`18`, `22`, `30` in config).

| Token | Value | Use |
|---|---|---|
| `2 / 3 / 4` | 8 / 12 / 16px | Intra-component gaps, inline spacing |
| `6 / 8` | 24 / 32px | Card padding (`p-6` is the card standard), element groups |
| `10 / 12` | 40 / 48px | Sub-section spacing |
| `16 / 20` | 64 / 80px | **Section vertical rhythm** — `.section-band` = `py-16 sm:py-20` |
| `18 / 22 / 30` | 72 / 88 / 120px | Hero/large feature spacing (custom tokens) |

**Section rhythm contract:** every full-width section uses `.section-band` for consistent vertical cadence; alternating sections use `bg-white` and `bg-desnky-surface` to create rhythm without heavy dividers (the pattern `home.php` already establishes).

**Container:** `.container-page` = `mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8` — the single horizontal container for all page content.

### 3.4 Grid System

- **Base:** CSS Grid via Tailwind utilities. **12-column mental model**, implemented with `grid` + `gap-6` defaults.
- **Standard content grids:**
  - Service/feature cards: `grid gap-6 md:grid-cols-2 lg:grid-cols-3`
  - Projects: `grid gap-6 md:grid-cols-3`
  - Asymmetric feature splits: `lg:grid-cols-[0.9fr_1.1fr]` / `lg:grid-cols-[1fr_0.85fr]` (already used on home)
  - Stats: `grid grid-cols-2 lg:grid-cols-4`
  - Shop listing: `grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4`
- **Gutters:** `gap-6` (24px) standard; `gap-4` dense; `gap-8/10` spacious.
- **Breakpoints:** `sm:640 · md:768 · lg:1024 · xl:1280 · 2xl:1536` (existing config — do not change).

### 3.5 Iconography Guidelines

- **Library:** **Heroicons** (outline for navigation/UI, solid for emphasis) as inline SVG — MIT-licensed, Tailwind-native, zero runtime cost. Avoid icon fonts.
- **Delivery:** Inline SVG `<symbol>` sprite or PHP icon partial (`partials/icon.php`) for reuse and a11y. Decorative icons get `aria-hidden="true"`; meaningful icons get `<title>`/`aria-label`.
- **Sizing scale:** `h-5 w-5` (inline/UI), `h-6 w-6` (nav), `h-11 w-11` container tiles (matches the service-card icon tile in `home.php`).
- **Sector iconography:** a curated, consistent-stroke set — Engineering (gear/wrench), Energy (bolt/flame), Procurement (truck/box), HSE (shield-check), ICT (server/cpu), Agro (leaf/grain). Rendered in a `rounded-md bg-desnky-surface` tile with the sector accent color.
- **Style rules:** 1.5–2px stroke, consistent corner radius, no mixed icon styles, no emoji as production icons.

### 3.6 Button Styles

Extends the existing `.btn-primary` / `.btn-secondary` component classes. Formalize a full button system in `@layer components`:

| Variant | Class | Spec | Use |
|---|---|---|---|
| **Primary** | `.btn-primary` | `bg-desnky-primary text-white hover:bg-desnky-primary-700`, `rounded-md px-5 py-3 text-sm font-semibold`, focus ring | Default primary action on light backgrounds |
| **Primary on dark (CTA)** | `.btn-on-dark` | `bg-white text-desnky-primary hover:bg-desnky-primary-50 hover:text-desnky-primary-700` | Conversion CTAs on dark/hero/CTA bands — "Request a Quote", hero buttons, newsletter submit. **Inverted purple** (white surface + purple label) so the CTA is the same brand color everywhere, contrast-safe on dark |
| **Secondary** | `.btn-secondary` | `border border-desnky-dark text-desnky-dark hover:bg-desnky-dark hover:text-white` | Secondary action |
| **Secondary (on dark)** | `.btn-secondary` + `border-white text-white hover:bg-white hover:text-desnky-dark` | On dark sections |
| **Ghost/Text link** | `inline-flex text-sm font-semibold text-desnky-primary hover:text-desnky-primary-700` | "Learn more →", card links |
| **Destructive** | `.btn-danger` (new) | `bg-red-700 text-white hover:bg-red-800` | Remove from cart, delete |
| **Icon button** | `.btn-icon` (new) | `h-11 w-11 inline-flex items-center justify-center rounded-md` | Mobile toggles, qty steppers |

**Button rules:**

- **Min target 44px** height (mobile); `py-3` satisfies this.
- **Focus-visible** ring always present (`focus:ring-2 focus:ring-offset-2`) — already in base classes.
- **One primary CTA per viewport** to preserve hierarchy.
- **Loading state:** disabled + spinner + `aria-busy="true"` during AJAX (see Component §6.14).
- Add subtle motion: `transition` + `hover:-translate-y-0.5` optional on primary CTAs (respect `prefers-reduced-motion`).

### 3.7 Form Components

Built on `@tailwindcss/forms` (already installed) and existing `.form-field` / `.form-label` / `.form-error` classes.

| Component | Class / Spec |
|---|---|
| **Label** | `.form-label` — `mb-2 block text-sm font-semibold text-desnky-ink` |
| **Text/Email/Tel input** | `.form-field` — `w-full rounded-md border-gray-300 focus:border-desnky-blue focus:ring-desnky-blue` |
| **Textarea** | `.form-field` + `min-h-[8rem]` |
| **Select** | `.form-field` (forms plugin styles native select) |
| **Checkbox/Radio** | forms plugin + `text-desnky-blue focus:ring-desnky-blue` (consent checkbox required on contact) |
| **Field error** | `.form-error` — `mt-2 text-sm text-red-700`; input gets `border-red-500 aria-invalid="true"` |
| **Field success** | green border + check affordance |
| **Help text** | `text-sm text-desnky-muted` linked via `aria-describedby` |
| **Fieldset/Group** | semantic `<fieldset><legend>` for grouped inputs (checkout sections) |

**Form UX standards (per `SKILL.md §13`):**

- **Inline, real-time validation** (Alpine.js) with server-side validation as source of truth.
- **AJAX submission** via existing `ajax-handler.js` (`submitForm(...)` already wired for newsletter & contact); show loading → success/error states without reload.
- **CSRF token** hidden field on every form (`_token`) — pattern already present.
- **Accessible errors:** `role="alert"`, focus moved to first invalid field, `aria-describedby` linking.
- **Honeypot + rate limiting** for spam (backend), invisible to users.

### 3.8 Card Components

Standardize on the existing card pattern (`overflow-hidden border border-gray-200 bg-white shadow-card`, `p-6` body, `shadow-card` token).

| Card type | Anatomy |
|---|---|
| **Service card** | Image (h-48 cover) → icon tile (`h-11 w-11 rounded-md bg-desnky-surface`) → H3 → summary → text-link CTA |
| **Project card** | Image (h-52 cover) → H3 → summary → (optional category badge, hover lift) |
| **Product card** | Image (square, lazy) → title → price → stock badge → "Add to cart" / "View" |
| **Stat card** | Big number (`text-4xl font-bold text-desnky-navy`) → label (muted) |
| **Testimonial card** | Quote → attribution (name, role, company) → optional logo/avatar |
| **Value/benefit card** | Check icon tile (green) → label (`home.php` "Why choose us" pattern) |
| **Article/Blog card** | Featured image → category chip → title → excerpt → meta (date · read time) |

**Card rules:** consistent radius (`rounded-md` or unify to `rounded-lg` — see §13 consistency note), `shadow-card` for elevation, `hover:shadow-lg hover:-translate-y-1 transition` for interactive cards, full-card clickable where appropriate (with accessible link semantics).

### 3.9 Alert & Notification Patterns

| Pattern | Use | Spec |
|---|---|---|
| **Inline alert** | Form-level success/error, page notices | Colored left border + tinted bg (`bg-green-50 border-l-4 border-green-600`), `role="alert"` for errors, `role="status"` for success |
| **Toast** | Transient AJAX feedback (added to cart, subscribed, saved) | Top-right (desktop) / bottom (mobile), auto-dismiss ~4s, dismissible, Alpine.js-driven, `aria-live="polite"` |
| **Banner** | Site-wide notices (maintenance, promo) | Full-width dark/purple band above header, dismissible with persistence |
| **Empty/Validation summary** | Multi-error forms (checkout) | Summary box listing errors with anchor links to fields |

**Color semantics:** success=green, error=red-700, warning=amber-600, info=blue. Always pair color with an **icon and text** (never color alone — a11y).

### 3.10 Design Tokens (Consolidated Reference)

```jsonc
{
  "color": {
    "brand": { "primary": "#6c3483", "primary-700": "#58296b", "primary-200": "#d8c4e4", "secondary": "#008000", "secondary-700": "#006b00" },
    "text":  { "ink": "#1f1626", "muted": "#6b6675", "inverse": "#ffffff" },
    "surface": { "base": "#ffffff", "subtle": "#f7f5fa", "dark": "#1d1228", "footer": "#150d1d" },
    "border": { "base": "#e5e7eb", "strong": "#1d1228" },
    "status": { "success": "#008000", "error": "#b91c1c", "warning": "#d97706", "info": "#6c3483" }
  },
  "font": { "family": "Inter, ui-sans-serif, system-ui, sans-serif",
            "weight": { "regular": 400, "medium": 500, "semibold": 600, "bold": 700, "extrabold": 800 } },
  "radius": { "sm": "0.375rem", "md": "0.5rem", "lg": "0.75rem", "full": "9999px" },
  "shadow": { "card": "0 12px 30px rgba(29,18,40,0.10)",
              "lg":   "0 20px 40px rgba(29,18,40,0.14)" },
  "space":  { "section": "4rem | 5rem (py-16/20)", "container-x": "1rem/1.5rem/2rem" },
  "container": { "max": "80rem (max-w-7xl)" },
  "motion": { "fast": "150ms", "base": "220ms", "slow": "400ms", "ease": "cubic-bezier(0.4,0,0.2,1)" },
  "z": { "header": 50, "dropdown": 40, "modal": 60, "toast": 70 }
}
```

> Tokens live in `tailwind.config.js` (`theme.extend`) and CSS custom properties in `resources/css/main.css` (`:root` already declares brand vars). Keep both in sync; Tailwind config is canonical.

---

## 4. Page-by-Page Redesign Plan

Each page below follows the same template: **Purpose · Audience · Content Hierarchy · Sections · UX · Layout · CTA Strategy · Mobile**. All sections are CMS-backed `page_sections`, rendered defensively.

### 4.1 Homepage (`/`)
*See the dedicated, expanded blueprint in [Section 5](#5-homepage-redesign).*

### 4.2 About Us (`/about`)

- **Purpose:** Build institutional trust; communicate scale, values, credentials, and team credibility for B2B decision-makers vetting a partner.
- **Audience:** B2B procurement/management evaluating legitimacy; recruits; partners.
- **Content hierarchy:** Who we are → what we stand for → proof (stats, certifications) → people → CTA.
- **Sections:**
  1. **Page hero** (`page-hero` partial) — eyebrow "About Desnky", H1, intro line, breadcrumb.
  2. **Company overview** — narrative + supporting image (asymmetric split).
  3. **Mission · Vision · Core Values** — three-card or tabbed block.
  4. **Stats band** — years in operation, projects delivered, sectors served, clients (dark aubergine band, **green** or light numerals).
  5. **Certifications & credentials** — logo/badge row (ISO, regulatory, memberships).
  6. **Leadership/Team** — optional grid (photo, name, role).
  7. **HSE commitment teaser** → links to `/hse-policy`.
  8. **Closing CTA band** — "Partner with Desnky".
- **UX:** Scannable values; concrete numbers over adjectives; certifications as trust anchors.
- **Layout:** Alternating surface/white bands; asymmetric splits for narrative; centered grids for values/stats.
- **CTA:** Secondary CTAs to Services & Projects mid-page; primary "Contact / Partner with us" in closing band.
- **Mobile:** Single column; stats `grid-cols-2`; team grid `grid-cols-1 → sm:grid-cols-2`.

### 4.3 Services Index (`/services`)

- **Purpose:** Frame the full capability portfolio and route visitors to the correct sector spoke.
- **Audience:** All B2B segments, unsure which service they need or evaluating breadth.
- **Content hierarchy:** Capability promise → six sectors → why Desnky → CTA.
- **Sections:**
  1. **Page hero** — "Integrated Solutions Across Six Sectors".
  2. **Services grid** — six cards (icon, title, summary, "Explore →") → `/services/{slug}`.
  3. **Cross-sector value** — integrated delivery, single accountable partner.
  4. **Process overview** — Enquiry → Scope → Deliver → Support (numbered steps).
  5. **Featured projects** strip.
  6. **CTA band** — "Discuss your requirements".
- **UX:** Each card maps to one keyword-targeted landing page; consistent card anatomy.
- **Layout:** `md:grid-cols-2 lg:grid-cols-3` card grid.
- **CTA:** Per-card spoke links + closing primary CTA.
- **Mobile:** Single-column cards; process steps stack vertically with connectors.

### 4.4 Service Detail (`/services/{slug}`) — ×6 sectors

- **Purpose:** Deep, SEO-optimized landing page that converts sector-specific intent into a qualified enquiry. Each targets a documented keyword (`BRIEF.md §7`).
- **Audience:** Buyers with specific need (e.g., "energy services in Nigeria").
- **Content hierarchy:** Sector promise → capabilities → process/approach → proof → FAQ → CTA.
- **Sections:**
  1. **Sector hero** — sector-accent eyebrow, H1 (keyword-led), intro, primary CTA, hero image with sector duotone.
  2. **Capabilities grid** — sub-services (e.g., Engineering → Electrical/Mechanical/Civil).
  3. **Approach/Process** — how we deliver, with HSE/quality emphasis.
  4. **Why choose us (sector-specific)** — differentiators, certifications.
  5. **Related projects** — filtered to this sector.
  6. **Related products** (where relevant, e.g., HSE → Safety Equipment shop category).
  7. **FAQ** (accordion) — SEO + objection handling, FAQ schema.
  8. **Sticky in-page sub-nav** (desktop) anchoring the above.
  9. **CTA band** — "Discuss your {sector} project".
- **UX:** Sector accent color theming; in-page anchors for long-form; FAQ reduces enquiry friction.
- **Layout:** Hero → alternating bands; capabilities `md:grid-cols-2/3`.
- **CTA:** Hero CTA + mid-page CTA + closing band; all pre-set `service_interest` for the contact form.
- **Mobile:** Sub-nav collapses to a select/scrollspy; accordions for FAQ & capabilities.

### 4.5 Projects Index (`/projects`)

- **Purpose:** Portfolio proof; demonstrate delivered capability across sectors.
- **Audience:** B2B evaluating track record.
- **Sections:** Page hero → **category filter bar** (Alpine.js, by sector) → **project grid** (cards, lazy images, lightbox-ready) → pagination → CTA band.
- **UX:** Client-side filter for snappy interaction; **lightbox** for galleries (per `BRIEF.md §10`); lazy-loaded images.
- **Layout:** `md:grid-cols-3` masonry-feel grid.
- **CTA:** "Have a similar project? Let's talk."
- **Mobile:** Filter as horizontal scroll chips; single-column grid; tap → detail.

### 4.6 Project Detail (`/projects/{slug}`)

- **Purpose:** Tell a credible delivery story (challenge → solution → outcome).
- **Sections:** Hero (title, sector, client, year) → image gallery (lightbox) → overview → scope/services used → outcomes/metrics → testimonial → related projects → CTA.
- **UX:** Case-study narrative; metrics quantify success; gallery with keyboard-navigable lightbox.
- **CTA:** Link to relevant Service page + "Start your project".
- **Mobile:** Gallery as swipeable carousel; stacked narrative.

### 4.7 HSE Policy (`/hse-policy`)

- **Purpose:** Present the corporate Health, Safety & Environment policy — a core differentiator and trust/compliance asset for industrial B2B.
- **Audience:** Compliance-conscious clients (oil & gas, government, construction).
- **Sections:** Page hero → commitment statement → policy pillars (cards) → full policy document (structured, prose) → certifications → **Download PDF** → CTA to HSE Services.
- **UX:** Authoritative, document-like clarity; green sector accent; downloadable PDF; printable.
- **CTA:** "Engage our HSE/Safety services" → `/services/hse-safety`.
- **Mobile:** Readable single column; sticky "Download PDF" affordance.

### 4.8 Contact (`/contact`)

- **Purpose:** Convert intent into a captured lead — the primary B2B conversion endpoint.
- **Audience:** Ready-to-engage prospects.
- **Sections:**
  1. **Page hero** — "Let's discuss your project".
  2. **Two-column block:** left = **contact form**; right = contact details (Lagos address, phones, email), hours, response-time expectation, map.
  3. **Map** — Google Maps embed (lazy-loaded, facade pattern for performance).
  4. **FAQ** — common pre-sales questions.
- **Form fields (per `BRIEF.md §10`):** Full name, Email, Phone, Company, Service interest (select — pre-filled from referrer), Message, **Consent checkbox**.
- **UX:** AJAX submit, inline validation, instant success state, no reload; honeypot + rate-limit; phone/WhatsApp click-to-action.
- **CTA:** The form itself; secondary "Browse services" for the undecided.
- **Mobile:** Form first (above details), single column; click-to-call/WhatsApp buttons prominent; map lazy-loaded below.

### 4.9 Shop Index (`/shop`)

- **Purpose:** Ecommerce storefront for B2C/B2B product sales.
- **Sections:** Hero/intro → **category nav + search + filters** → **product grid** → pagination → trust strip (delivery, payment options) → CTA.
- **UX:** Faceted filtering (category, price, availability) via Alpine.js; search; stock badges; add-to-cart from grid; cart indicator updates live.
- **Layout:** `sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4`; optional sidebar filters on `lg`.
- **CTA:** "Add to cart" / "View"; "Need bulk/B2B pricing? Contact us".
- **Mobile:** Filters in a slide-over drawer; sticky cart bar.

### 4.10 Shop Category (`/shop/category/{slug}`)
- Same as Shop Index, scoped to category, with category hero + breadcrumb + category SEO copy.

### 4.11 Product Detail (`/shop/product/{slug}`)

- **Purpose:** Convert product interest into cart additions.
- **Sections:** Breadcrumb → **gallery** (main + thumbnails, zoom/lightbox) → product info (title, SKU, price, discount, stock badge, short desc) → **quantity + Add to cart** → full description tabs (Description / Specs / Delivery) → related products → CTA.
- **UX:** Server-trusted pricing/stock (never trust frontend — `SKILL.md §29`); disable add-to-cart when out of stock; quantity stepper; sticky add-to-cart on scroll (mobile).
- **SEO:** Product schema, breadcrumb schema, OG product image, alt text (`SKILL.md §23`).
- **Mobile:** Gallery carousel; sticky bottom "Add to cart" bar with price.

### 4.12 Cart (`/shop/cart`)

- **Purpose:** Review and adjust before checkout.
- **Sections:** Line items (image, title, price, qty stepper, remove) → order summary (subtotal, delivery note, total) → "Proceed to Checkout" → continue shopping → cross-sell.
- **UX:** AJAX qty update/remove with live total recalculation; empty-cart state with CTA to shop; server-side totals.
- **Mobile:** Stacked cards per line item; sticky summary/checkout bar.

### 4.13 Checkout (`/shop/checkout`)

- **Purpose:** Complete the order with minimal friction.
- **Sections (per `SKILL.md §23`):** Customer info (name, email, phone) → delivery (address, city/state) → order notes → **payment method** (Pay on Delivery / Manual Bank Transfer) → order review → Place Order.
- **UX:** Single-page accordion or short multi-step with progress indicator; validation per step; empty-cart guard; server-side total + stock validation before placing; clear bank-transfer instructions when selected.
- **Confirmation:** Order confirmation page (order #, summary, next steps) + email to customer & admin.
- **Mobile:** Single-column stacked sections; sticky "Place Order" with total.

### 4.14 Blog/Insights (`/blog`) — *Roadmap (Editorial CMS)*

- **Purpose:** SEO content engine + thought leadership nurturing Flow C.
- **Sections:** Hero → featured post → post grid (card: image, category chip, title, excerpt, meta) → category/tag filters → pagination → newsletter CTA.
- **Detail page:** Hero (title, author, date, read time, featured image) → `prose` body (TipTap-rendered, sanitized HTML) → tags → author bio → related posts → CTA → BlogPosting/Article schema.
- **Mobile:** Single-column cards; readable `prose` width.
- *(Implement when the editorial CMS phase ships; views should follow all component standards here.)*

### 4.15 Utility Pages

- **404 / Error:** Branded, helpful — search, popular links, "back home", contact. Friendly copy, on-brand illustration.
- **Privacy Policy / Terms:** Clean `prose` legal layout, table of contents, last-updated date.
- **Order confirmation / Thank-you:** Reassurance, next steps, order reference.

---

## 5. Homepage Redesign

The homepage is the brand's primary first impression and top-of-funnel for both B2B and B2C. It expands `BRIEF.md §6` and the existing `home.php` structure into a premium, conversion-engineered experience. **Section order is deliberate**, alternating proof and action.

### 5.1 Section-by-Section Blueprint

**① Hero (`bg-desnky-navy`, full-viewport, fixed transparent header over it)**

- **Headline (H1):** *"Integrated Energy, Engineering, Procurement, Safety, ICT & Agro Solutions in Nigeria."* (per `BRIEF.md`).
- **Sub-headline:** one-line value proposition emphasizing reliability + breadth.
- **Eyebrow:** light-purple (`primary-200`) uppercase kicker over the dark hero (e.g., "Desnky Global Resources Ltd").
- **Dual CTA:** Primary **"Request a Quote"** (`.btn-on-dark` — inverted purple: white surface + purple label, contrast-safe on the dark hero) + Secondary **"View Our Services"** (outline-white).
- **Background:** full-bleed industrial photograph with dark-aubergine `/70` overlay (existing pattern), `loading="eager"` for LCP.
- **Hero slider strategy:** A **rotating hero is OPTIONAL and conditional.** *Recommendation:* default to a **single, static, fast-LCP hero** (best for Core Web Vitals and message focus). If marketing requires rotation, implement a **lightweight Alpine.js slider** (max 3 slides, one sector message each) with: pause-on-hover, swipe support, accessible controls (prev/next + dot tabs with `aria-`), `prefers-reduced-motion` → no auto-advance, and **only the first slide image eager-loaded** (rest lazy). Auto-advance ≥6s. Never let the slider regress LCP below targets.
- **Mobile:** H1 `text-4xl`, stacked CTAs full-width, min-height `calc(100vh-5rem)` with content vertically centered (existing).

**② Trust Indicators (immediately below hero — prime credibility before scroll)**

- Slim band: **client/partner logos** (grayscale → color on hover), or **key stats** (e.g., "15+ Years · 200+ Projects · 6 Sectors · 100% HSE Commitment").
- Optionally a certifications strip (ISO/regulatory badges).
- **Rationale:** front-loading proof reduces bounce and primes downstream CTAs.

**③ Company Introduction (short, outcome-focused)**

- Eyebrow + H2 + 2–3 sentence positioning + "About Desnky →" link.
- Asymmetric split with a supporting on-site image.

**④ Services Overview (the core navigational + SEO block)**

- H2 + intro; **six service cards** (icon tile, title, summary, "Explore →") in `md:grid-cols-2 lg:grid-cols-3`.
- Each card → `/services/{slug}`. This is the existing `home.php` services section, elevated with sector-accent icon tiles and hover lift.

**⑤ Company Highlights / Why Choose Desnky**

- "Why choose us" benefits grid (green check tiles — existing pattern): integrated delivery, HSE-first, local expertise + global standards, qualified team, responsive support.
- Asymmetric narrative + benefit cards.

**⑥ Statistics & Achievements (dark aubergine band, green/light numerals)**

- Animated count-up stat cards (`grid-cols-2 lg:grid-cols-4`): years, projects, sectors, clients/satisfaction.
- Count-up triggered on scroll via `IntersectionObserver`; respects reduced-motion (renders final value instantly).

**⑦ Industry Expertise (sector depth)**

- Visual showcase of the six sectors with imagery — a tabbed/segmented block (Alpine.js) or icon-led grid letting visitors self-identify their industry and jump to the relevant service page.

**⑧ Featured Projects**

- "Selected work" — three project cards (`md:grid-cols-3`) + "View all projects" secondary CTA (existing pattern), `bg-desnky-surface` band.

**⑨ HSE Commitment**

- Dedicated section (existing) — statement + image + "Read our HSE Policy →". Green accent. Reinforces the safety differentiator critical to industrial buyers.

**⑩ Testimonials**

- 1–3 client testimonials (quote, name, role, company, optional logo). Carousel on mobile (Alpine.js), grid on desktop. Adds human credibility.

**⑪ Client Showcase**

- Logo grid of clients/partners (`sm:grid-cols-2 lg:grid-cols-4`) — existing clients section, upgraded to logos where available (alt text per client).

**⑫ Lead Generation + Contact/Conversion Block (navy CTA band)**

- Split: left = conversion headline + "Request a Quote / Contact" primary CTA; right = **newsletter subscription** form (AJAX, existing `data-newsletter-form` wiring).
- This is the existing closing CTA, formalized as the homepage's primary conversion anchor.

**⑬ Footer** (global — see §6.9).

### 5.2 Homepage Conversion Logic

| Scroll depth | Visitor state | Conversion lever |
|---|---|---|
| Hero | Arriving, evaluating | Request a Quote (purple, inverted `.btn-on-dark`) + clear value prop |
| Trust band | Skeptical | Logos/stats/certifications de-risk |
| Services | Exploring | Route to relevant sector (micro-conversion) |
| Stats/HSE/Testimonials | Building confidence | Proof stacking |
| Closing band | Decided/curious | Quote CTA + newsletter (capture either intent) |
| Sticky mobile bar | Any time | One-tap Call · WhatsApp · Quote |

### 5.3 Homepage Performance Notes

- Hero image is the **LCP element** — serve responsive WebP/AVIF, `fetchpriority="high"`, `loading="eager"`, preloaded; everything below the fold lazy-loads.
- Count-up and testimonial carousel JS deferred and IO-triggered.
- Target homepage LCP < 2.0s on 4G mid-range mobile.

---

## 6. Component Architecture

Components are implemented as **PHP view partials** under `app/Views/frontend/partials/` (and `components/`), styled with Tailwind component classes, made interactive with **Alpine.js**. Each is **CMS-data-driven and renders defensively**. Naming follows the existing convention (`header.php`, `footer.php`, `navigation.php`, `page-hero.php`, `seo-meta.php`, `analytics.php`).

> **Recommendation:** introduce `app/Views/frontend/components/` for reusable, parameterized UI atoms/molecules (button, card, alert, stat, accordion, breadcrumb, pagination) consumed via `$this->partial('frontend/components/...', [...])`, keeping `partials/` for page-region composites.

### 6.1 Header (`partials/header.php`) — *exists*

- Brand mark + name, primary nav, persistent "Request a Quote" CTA, mobile toggle, skip-link.
- **Dual mode:** transparent-over-hero on home (`site-header--home/--transparent`) → solid on scroll (`--scrolled`), via `header-scroll.js`; solid sticky elsewhere. **Retain and refine.**
- Add: cart indicator (item count) when shop items present; mega-menu trigger for Services.

### 6.2 Navigation (`partials/navigation.php`) — *exists*

- **Desktop:** horizontal links + **Services mega-menu** (Alpine.js dropdown: six sectors with icons/descriptors + CTA panel), `active` state styling per current page.
- **Mobile:** off-canvas drawer (Alpine.js) — slide-in panel, focus-trapped, ESC/overlay close, Services as accordion, full-width CTA + click-to-call.
- A11y: `aria-expanded`, `aria-current="page"`, keyboard navigable, focus management.

### 6.3 Hero Sections

- **Variants:** Home hero (full-viewport, navy overlay, dual CTA), **Page hero** (`partials/page-hero.php`, compact, eyebrow + H1 + breadcrumb), Sector hero (accent-themed), Project/Product hero.
- Parameterized: `eyebrow, heading, text, image, image_alt, primary_cta, secondary_cta, breadcrumb`.

### 6.4 Cards (`components/card-*.php`)

- Service, Project, Product, Stat, Testimonial, Benefit, Article cards — all per §3.8 anatomy, sharing `shadow-card`, consistent radius, hover lift for interactive variants.

### 6.5 Forms

- **Contact form** (AJAX, validation, consent, CSRF), **Newsletter form** (inline, AJAX — exists), **Checkout forms** (multi-section), **Cart update** (AJAX qty/remove), **Shop filters/search**.
- Shared field partials (`components/form-field.php`) for label+input+error consistency; `ajax-handler.js` for submission.

### 6.6 Tables

- **Public:** product specs table, comparison tables, order summary (cart/checkout).
- **Pattern:** responsive — horizontal scroll wrapper on mobile (`overflow-x-auto`) or **card-collapse** (stack rows into labeled cards < `md`). Zebra rows (`desnky.surface`), sticky header for long tables, semantic `<thead>/<th scope>`.

### 6.7 Testimonials (`components/testimonials.php`)

- Quote, attribution, optional avatar/logo. Grid (desktop) / swipeable carousel (mobile, Alpine.js, `aria-live`, keyboard controls, reduced-motion safe).

### 6.8 FAQ Sections (`components/faq.php`)

- Accessible **accordion** (Alpine.js): `button[aria-expanded]` + `aria-controls`, one-or-many open, smooth height transition (reduced-motion safe). Emits **FAQPage schema** (JSON-LD) for SEO. Used on Service detail, Contact, Shop.

### 6.9 Footer (`partials/footer.php`) — *exists*

- Four-column: Company · Services · Shop · Contact+Newsletter; social icons; legal base bar (Privacy, Terms, Sitemap, ©). **Dark theme** — `bg-desnky-darker` (#150d1d) with a `border-t-4 border-desnky-primary` brand accent; white brand heading, `gray-200/300` body and links with `hover:text-white`; SEO internal links (`BRIEF.md §6`). Newsletter form (AJAX). Fully responsive stack on mobile.

### 6.10 Modals / Dialogs (`components/modal.php`)

- Alpine.js dialog: focus-trap, ESC/overlay close, `role="dialog" aria-modal="true"`, scroll-lock, return focus to trigger.
- Uses: product quick-view, image lightbox, cart drawer, confirmation dialogs, cookie consent.

### 6.11 Search Interfaces (`components/search.php`)

- Shop search (input + results), with debounced AJAX (optional live results), clear button, `role="search"`, accessible labels. Future: site-wide search.

### 6.12 Pagination (`components/pagination.php`)

- Prev/Next + numbered pages, `aria-label="Pagination"`, `aria-current="page"`, disabled states, truncation for large ranges. Used on Projects, Shop, Blog. Mobile: simplified Prev/Next + "Page X of Y".

### 6.13 Dashboard Widgets

- *Out of scope for the public frontend* but the public design tokens/components (stat cards, tables, alerts, badges) **feed the admin dashboard** (`SKILL.md §27`) to maintain one visual language across public + admin. Reuse `shadow-card`, palette, button system.

### 6.14 Loading States

- **Button loading:** disabled + inline spinner + `aria-busy` during AJAX (contact, add-to-cart, checkout).
- **Skeleton screens:** for async content (shop filter results, lazy sections) — shimmer placeholders matching final layout.
- **Page transition:** subtle top progress bar (optional) for navigations.
- **Lazy media:** blur-up / low-quality placeholder → image (CWV-friendly, reserves space to avoid CLS).

### 6.15 Empty States (`components/empty-state.php`)

- Empty cart, no search results, no projects in filter, no products in category. Pattern: icon + short message + **primary CTA** to recover (e.g., "Browse all products"). Friendly, never a dead end.

### 6.16 Error States

- **Field errors** (inline, `role="alert"`), **form-level error summary**, **AJAX failure toast** ("Something went wrong, please retry"), **404/500 pages** (branded, with navigation recovery). Never expose technical detail to users (`SKILL.md §14`).

---

## 7. Mobile Experience Strategy

Mobile is the **primary design target** (`SKILL.md §9`, `BRIEF.md`). Nigerian B2B/B2C traffic is mobile-dominant.

### 7.1 Mobile Navigation

- **Off-canvas drawer** (Alpine.js): hamburger → slide-in panel, focus-trapped, overlay + ESC to close, scroll-locked body.
- **Services accordion** within the drawer (no hover dependency).
- **Full-width primary CTA** ("Request a Quote") + **click-to-call** and **WhatsApp** at the top of the drawer.
- **Sticky bottom action bar** on key pages (Home, Service, Contact, Product): `Call · WhatsApp · Quote` (and `Add to Cart · Price` on product) — one-tap conversion.
- Cart icon with badge in the mobile header on shop pages.

### 7.2 Touch Optimization

- **≥44×44px** tap targets; `py-3` buttons satisfy this.
- Generous spacing between interactive elements (avoid mis-taps).
- **Swipe gestures**: hero slider (if used), product galleries, testimonial carousel.
- **No hover-only interactions** — every hover affordance has a tap/focus equivalent.
- Quantity steppers, filter chips, and accordions sized for thumbs.
- Inputs use correct `type`/`inputmode` (`email`, `tel`, `numeric`) + `autocomplete` for fast entry.

### 7.3 Responsive Breakpoints (existing config — canonical)

| Breakpoint | Min width | Primary layout shift |
|---|---|---|
| *(base)* | 0–639 | Single column, drawer nav, stacked CTAs, sticky action bar |
| `sm` | 640 | 2-col card grids, side-by-side CTAs |
| `md` | 768 | 2–3 col grids, inline nav begins |
| `lg` | 1024 | Full horizontal nav + mega-menu, 3–4 col grids, sidebars |
| `xl` | 1280 | Wider gutters, 4-col shop grid |
| `2xl` | 1536 | Max container, enhanced spacing |

### 7.4 Progressive Enhancement

- **Core content & navigation work without JS** (server-rendered PHP). JS adds interactivity (drawer, accordions, AJAX, carousels) but is never required to read content or follow links.
- Forms **degrade to standard POST** if AJAX fails (server handles both — contact already exposes `/contact/submit` and `/api/contact`).
- Enhanced visuals (parallax, count-up, hover reveals) are additive and gated by capability + `prefers-reduced-motion`.

### 7.5 Mobile Performance Considerations

- Performance budget enforced on **mid-range Android / 4G** (not desktop).
- Responsive images with `srcset`/`sizes`, WebP/AVIF, correct intrinsic dimensions (prevent CLS).
- Lazy-load all below-fold media; eager-load only the LCP hero image.
- Minimal JS payload (Alpine.js ~15KB + small custom scripts); defer non-critical scripts (already deferred in `base.php`).
- Avoid layout shift: reserve space for images/embeds; lazy-load map/video via facade.

---

## 8. Accessibility Standards

**Target: WCAG 2.1 Level AA** as an acceptance criterion (`INDEX.md` lists accessibility as an enforced best practice; Phase 10 includes an accessibility report).

### 8.1 WCAG Compliance Requirements

- **Perceivable:** text alternatives for all meaningful images (CMS-managed alt text — already enforced in views); captions for any video; content meaningful without color alone.
- **Operable:** full keyboard operability; visible focus; no keyboard traps; skip-link (exists); no content flashing > 3×/s.
- **Understandable:** consistent navigation; clear labels/instructions; predictable behavior; helpful, specific error messages.
- **Robust:** valid semantic HTML; correct ARIA only where needed; works across assistive tech.

### 8.2 Keyboard Navigation Support

- All interactive elements reachable and operable via keyboard in logical tab order.
- **Visible `focus-visible` rings** on every interactive element (already in button/field base classes; never remove outlines without a replacement).
- **Skip to content** link (exists in `header.php`).
- Mega-menu, drawer, modals, accordions, carousels, lightbox: full keyboard support (Arrow/Tab/ESC/Enter/Space) + **focus management** (trap in modals, return focus on close).

### 8.3 Screen Reader Compatibility

- **Semantic landmarks:** `<header><nav><main id="main-content"><footer>` (exists), one `<main>` per page.
- **Heading order:** exactly one H1; no skipped levels.
- **ARIA:** `aria-current="page"` on active nav, `aria-expanded`/`aria-controls` on disclosures, `aria-live` regions for AJAX/toasts/cart updates, `aria-label` on icon-only buttons, `role="alert"` on errors.
- **Forms:** every input has an associated `<label>` (or `aria-label`); errors linked via `aria-describedby`; required fields marked programmatically.
- **Images/icons:** decorative → `alt=""`/`aria-hidden`; informative → descriptive alt.

### 8.4 Contrast Requirements

- **Normal text ≥ 4.5:1**, **large text (≥24px / ≥18.66px bold) ≥ 3:1**, **UI/graphical ≥ 3:1**.
- Verified pairings (see §3.1): dark/white ~16:1 ✓; muted/white 5.0:1 ✓; **purple buttons use white text** (8.55:1 ✓); **inverted CTA on dark = white surface + purple label** (8.55:1 ✓); `primary-200` light tint for accent text on dark (9:1 ✓); green for success/accents + white text (5.13:1 ✓). Never use *solid* purple as a CTA on the dark anchor — use the inverted `.btn-on-dark`.
- **Do not** convey state by color alone — pair with icon + text (stock status, validation, alerts).

### 8.5 Semantic HTML Guidelines

- Use the right element for the job: `<button>` for actions, `<a>` for navigation, `<nav>`, `<article>`, `<section>` (with accessible name), `<figure>/<figcaption>`, `<ul>/<ol>` for lists, `<table>` only for tabular data with `<th scope>`.
- No `<div>`-as-button; no heading misuse for styling.
- Output escaping on all dynamic content (`$this->escape(...)` — already standard) to prevent XSS while preserving correct rendering.
- Respect `prefers-reduced-motion`: disable/relax non-essential animation.

---

## 9. Frontend Technology Recommendations

Strictly aligned with the mandated stack (`SKILL.md §3`, `BRIEF.md §4`): **HTML5 · Tailwind CSS · Alpine.js · vanilla JS/jQuery**. No heavy SPA frameworks (`SKILL.md §18`).

### 9.1 HTML5

- Server-rendered PHP MVC views (existing) — SEO-friendly, fast first paint, no hydration cost.
- Semantic, accessible markup; structured data via JSON-LD (`seo-meta.php` + per-page schema).
- Progressive enhancement baseline.

### 9.2 Tailwind CSS

- **Utility-first, compiled via the existing npm/PostCSS pipeline** (`tailwind.config.js`, `postcss.config.js`); minified output to `public/assets/css/main.min.css`.
- **Component classes in `@layer components`** (`resources/css/main.css`) for repeated patterns (`btn-*`, `form-*`, `container-page`, `section-band`, card patterns) — keeps views clean and consistent. This is the established pattern; extend it rather than scattering long utility strings.
- **JIT/content scanning** already configured (`./app/Views/**/*.php`, `./public/assets/**/*.js`) — ensure any new `components/` dir is in `content`.
- Add `@tailwindcss/typography` for `prose` (blog/legal/HSE). `@tailwindcss/forms` already installed.
- `darkMode: 'class'` is configured — **dark mode is out of scope for v1** but the token structure leaves the door open.

### 9.3 Alpine.js

- The **sole interactivity layer** for UI state: mobile drawer, mega-menu, accordions (FAQ), tabs, modals/lightbox, toasts, carousels, filter UI, quantity steppers, count-up triggers.
- Loaded once globally (deferred); declarative `x-data`/`x-show`/`x-transition`/`x-on`. Lightweight (~15KB), no build step required.
- Extract repeated behaviors into small reusable Alpine components/`Alpine.data()` registrations in a single `alpine-components.js`.

### 9.4 JavaScript Architecture

- **Module organization** under `public/assets/js/`:
  - `ajax-handler.js` (exists) — `submitForm()` + fetch wrappers, CSRF, success/error handling for all forms.
  - `header-scroll.js` (exists) — transparent→solid header on home.
  - `analytics.js` (exists) — GA4 events, CTA tracking via `data-analytics-event`.
  - **(new)** `alpine-components.js` — registered Alpine data/components.
  - **(new)** `shop.js` — cart/qty/add-to-cart, filter, live cart count.
  - **(new)** `media.js` — lightbox, lazy-load fallbacks, count-up (`IntersectionObserver`).
- **Principles:** vanilla-first; jQuery only where a legacy dependency demands it (prefer native fetch/DOM). All scripts `defer`-loaded (already in `base.php`). No global leakage; feature-detect and degrade gracefully.
- **Progressive enhancement:** every JS feature has a no-JS fallback.

### 9.5 Animation Libraries

- **Prefer CSS transitions + Alpine `x-transition`** for the vast majority of motion (cheap, GPU-friendly, no payload).
- **`IntersectionObserver`** for scroll-reveal and count-up (no library).
- **Avoid heavy libraries** (GSAP/AOS) unless a specific high-value interaction justifies it; if introduced, lazy-load and gate by `prefers-reduced-motion`.
- Motion tokens: `fast 150ms · base 220ms · slow 400ms`, `ease cubic-bezier(0.4,0,0.2,1)` (matches existing 220ms header transition).

### 9.6 Performance Optimization Techniques

- Critical CSS inlined or single minified stylesheet (already compiled); purge unused utilities (Tailwind content scan).
- Deferred, minimal JS; code-split per-page scripts (`$scripts` slot in `base.php` already supports per-page JS).
- Responsive WebP/AVIF images, lazy-loading, `fetchpriority` on LCP.
- HTTP caching, gzip/brotli, asset fingerprinting (see §10).

---

## 10. Performance Optimization Plan

Targets from `BRIEF.md §12`, `SKILL.md §12`, and success metrics: **PageSpeed ≥ 90 (mobile & desktop)**.

### 10.1 Core Web Vitals Targets

| Metric | Target | Strategy |
|---|---|---|
| **LCP** (Largest Contentful Paint) | **< 2.5s** (aim < 2.0s) | Preload + `fetchpriority="high"` hero image; responsive WebP/AVIF; minimal render-blocking CSS; font `display=swap` + preconnect (exists) |
| **INP** (Interaction to Next Paint) | **< 200ms** | Lightweight Alpine.js handlers; debounced inputs; avoid long tasks; deferred JS |
| **CLS** (Cumulative Layout Shift) | **< 0.1** | Explicit image dimensions; reserved space for media/embeds; no late-injected content above fold; `font-display: swap` with metric-matched fallback |
| **FCP** | < 1.8s | Minified critical CSS, server-rendered HTML |
| **TTFB** | < 0.6s | PHP opcache, query/page caching (`02-ARCHITECTURE`), CDN option |

### 10.2 Image Optimization Strategy

- **Formats:** AVIF → WebP → JPEG fallback via `<picture>`; PNG only for transparency/logos (or SVG).
- **Responsive:** `srcset` + `sizes` for all content images; serve appropriately sized derivatives (the media library generates thumbnails/responsive/WebP per `01-PRODUCT-STRATEGY` media spec).
- **Compression:** automated on upload (admin media library); target high visual quality at minimal bytes.
- **Dimensions:** always set `width`/`height` (or aspect-ratio) to prevent CLS.
- **Hero/LCP:** preloaded, eager, high priority; **all other images lazy** (`loading="lazy"` — already standard in views).
- **Decorative/icons:** inline SVG.

### 10.3 Lazy Loading Strategy

- Native `loading="lazy"` on below-fold images/iframes (already applied in `home.php` service/project images).
- **Map & video via facade pattern** (static thumbnail → load embed on interaction) — critical for Contact page.
- IO-triggered loading for heavy sections (testimonials, galleries) and count-up animations.
- Blur-up/LQIP placeholders to reserve space and improve perceived performance.

### 10.4 Asset Bundling Approach

- **CSS:** single Tailwind-compiled, purged, minified `main.min.css` (exists) — content-scanned to strip unused utilities.
- **JS:** small, purpose-split, `defer`-loaded files; per-page scripts via the `$scripts` slot to avoid shipping shop JS on the About page.
- **Fingerprinting/cache-busting:** versioned asset filenames or query hashes for long-cache safety.
- Minify HTML output where feasible; remove dev-only artifacts in production build.

### 10.5 Caching Recommendations

- **Browser caching:** long `Cache-Control` (1y, immutable) for fingerprinted CSS/JS/images; short/validated for HTML.
- **Server-side:** page/fragment cache for CMS-rendered public pages (invalidate on admin publish — aligns with `02-ARCHITECTURE` multi-level caching), query cache, PHP opcache.
- **Compression:** gzip/brotli at server (`.htaccess`/Nginx).
- **CDN (optional):** static assets + image delivery for Nigerian + international edge performance.
- **Fonts:** preconnect (exists); consider self-hosting Inter subset to remove third-party RTT.

### 10.6 SEO Considerations (frontend-rendered)

- **Per-page** unique `<title>`, meta description, **single H1**, proper H2/H3, canonical, OG + Twitter cards (`seo-meta.php` + `seo_metadata` CMS).
- **JSON-LD schema:** Organization, LocalBusiness, Breadcrumb, Service, Product, FAQPage, Article/BlogPosting (`BRIEF.md §8`, `SKILL.md §11`).
- **Semantic HTML**, descriptive alt text, internal linking between services/projects/shop.
- **`sitemap.xml` + `robots.txt`**, SEO-friendly URLs, 301 redirects from legacy `.html` (in routes).
- Fast, mobile-friendly, accessible pages **are** SEO — CWV is a ranking factor.
- `noindex` controls for draft/private/archived CMS content.

---

## 11. Visual Inspiration & Creative Direction

### 11.1 Corporate Visual Identity

- **Mood:** *Industrial confidence with a premium edge* — substantial, precise, trustworthy. Corporate **purple** authority + **green** support, anchored by a deep aubergine dark.
- **Reference archetypes:** the clarity of enterprise SaaS marketing sites (Stripe-like structure, generous whitespace, strong typography) applied to an **industrial/energy/engineering** context (think reputable EPC, energy-services, and industrial-group corporate sites).
- **Signature elements:** dark-aubergine hero with photographic overlay + **inverted purple CTA** (white button, purple label); purple section eyebrows (light-purple on dark); `shadow-card` elevated white cards on subtle surface bands; stat bands with light/green numerals on the dark anchor; a dark-themed footer with a purple top-accent.

### 11.2 Modern Enterprise Website Patterns

- Full-viewport hero with one clear message + dual CTA.
- Alternating `white` / `surface` section bands for rhythm (existing).
- Hub-and-spoke service architecture with deep landing pages.
- Distributed social proof (logos, stats, testimonials, certifications).
- Sticky, conversion-oriented header CTA; persistent mobile action bar.
- Case-study storytelling for projects (challenge → solution → outcome).

### 11.3 Premium UI Elements

- Refined card elevation (`shadow-card` → `shadow-lg` on hover) with restrained borders.
- Sector-accent icon tiles in soft surface squares (purple/green on `surface`).
- Purple and green as *disciplined* brand accents (purple = all CTAs/links/eyebrows; green = success, HSE/Agro, supporting numerals) — never overused; let whitespace carry the premium feel.
- Crisp Inter typography with confident size jumps and disciplined whitespace.
- Subtle dark-aubergine duotone treatment unifying disparate photography.

### 11.4 Animation Guidelines

- **Purposeful, fast, subtle.** Motion clarifies state and guides attention; it never decorates for its own sake.
- **Vocabulary:** fade/translate on scroll-reveal (≤16px travel), `220ms` base transitions, gentle `hover:-translate-y-1` card lift, smooth accordion/drawer transitions, count-up on stats.
- **Always honor `prefers-reduced-motion`** (render final states instantly).
- No autoplaying distracting motion; no parallax that harms performance or accessibility.

### 11.5 Micro-interactions

- Button hover/active states + focus rings; CTA subtle lift.
- Form field focus (blue ring), inline validation tick/error reveal.
- Add-to-cart → cart badge bump + toast confirmation.
- Mega-menu fade/slide; drawer slide; accordion chevron rotate.
- Link underline-grow; logo grayscale→color on hover.
- Loading spinners and skeletons for async feedback.

### 11.6 Visual Storytelling Techniques

- **Show, don't tell:** authentic on-site/project photography over stock; teams in PPE; real equipment and installations.
- **Numbers tell credibility:** prominent stats (years, projects, sectors).
- **Sequence as narrative:** process steps (Enquiry → Scope → Deliver → Support) and project case studies.
- **Sector self-identification:** let visitors see *their* industry reflected, then route them to depth.
- **HSE as a story of responsibility**, reinforcing trust for industrial buyers.

---

## 12. Implementation Roadmap

A logical, dependency-ordered sequence. It **builds on the existing implementation** (the codebase already renders CMS-backed public pages), so much of this is *elevation, formalization, and gap-closing* rather than greenfield. Each phase lists scope, key deliverables, and exit criteria.

### Phase 1 — Design System Foundation
- **Scope:** Finalize tokens in `tailwind.config.js` (palette extensions, sector aliases, radius/shadow/motion), consolidate component classes in `@layer components` (buttons, forms, cards, badges, alerts), typography scale, spacing rhythm, icon set/partial.
- **Deliverables:** Updated config + `main.css` component layer; a **living style guide / component preview page** (internal route or static) documenting every token and component state.
- **Exit:** All tokens centralized; no ad-hoc colors/spacing in views; style guide approved.

### Phase 2 — Core Layout Components
- **Scope:** Header (dual-mode, cart indicator), Navigation (mega-menu + mobile drawer), Footer (4-col + newsletter), `page-hero`, breadcrumb, base layout slots, skip-link, sticky mobile action bar, toast/alert system, modal/lightbox scaffold.
- **Deliverables:** Production partials in `partials/` + `components/`; Alpine.js component registrations; a11y-complete nav.
- **Exit:** Global chrome consistent across all routes; keyboard + screen-reader pass on nav/footer.

### Phase 3 — Homepage
- **Scope:** Implement the full §5 blueprint — hero (static-first, optional slider), trust band, services grid, highlights, stats (count-up), industry expertise, featured projects, HSE, testimonials, client showcase, conversion + newsletter band.
- **Deliverables:** Elevated `home.php` + section components, all CMS-driven and defensive.
- **Exit:** Homepage LCP < 2.0s mobile; all sections render from CMS; CTAs tracked.

### Phase 4 — Internal Pages
- **Scope:** About, Services index, **6× Service detail** (sector-themed, FAQ, in-page nav), Projects index + detail (filter, lightbox), HSE Policy (+PDF), Shop index/category/product, Cart, Checkout, legal/utility, 404. (Blog when editorial CMS ships.)
- **Deliverables:** All page views per §4 standards, consistent components, SEO/schema per page.
- **Exit:** Every documented route has a complete, on-brand, responsive, schema-equipped page.

### Phase 5 — Forms & Interactive Features
- **Scope:** Contact (AJAX, validation, consent, pre-filled service), Newsletter, Shop filters/search, Cart AJAX (qty/remove, live totals), Checkout flow (validation, payment methods, confirmation), lightbox, carousels, accordions.
- **Deliverables:** `ajax-handler.js`, `shop.js`, `media.js`, Alpine components; server-validated, progressively enhanced forms.
- **Exit:** All flows work with and without JS; validation + error/success states complete; CSRF on every form.

### Phase 6 — Performance Optimization
- **Scope:** Image pipeline (WebP/AVIF, srcset, lazy, LCP preload), asset purge/minify/fingerprint, per-page JS splitting, caching headers, font self-host/subset, facade-loaded map/embeds.
- **Deliverables:** Optimized build; Lighthouse runs; CWV instrumentation.
- **Exit:** PageSpeed ≥ 90 mobile & desktop; LCP/INP/CLS within targets on representative pages.

### Phase 7 — Accessibility & QA
- **Scope:** WCAG 2.1 AA audit (axe + manual), keyboard + screen-reader passes, contrast verification, cross-browser/device testing, form/edge-case testing, reduced-motion checks.
- **Deliverables:** Accessibility report (extends Phase 10 reports), defect log + fixes, QA sign-off checklist.
- **Exit:** Zero critical a11y issues; AA conformance; cross-browser/device parity.

### Phase 8 — Final Review & Launch
- **Scope:** Content/SEO final pass (titles, meta, schema, alt text, internal links, sitemap/robots, 301s), analytics/event verification, visual consistency review (§13), stakeholder UAT, performance re-check, launch + post-launch monitoring.
- **Deliverables:** Launch checklist, SEO checklist sign-off, monitoring dashboards, maintenance notes.
- **Exit:** All success metrics instrumented; redirects live; CWV green; stakeholder approval; **production launch**.

> **Sequencing note:** Phases 1–2 are prerequisites for everything. 3–5 can partially parallelize across developers once the design system is frozen. 6–8 are continuous concerns formalized as gates, not afterthoughts.

---

## 13. Appendix: Quality & Consistency Review

A self-review against the brief's quality requirements, plus flagged decisions for implementation.

### 13.1 Adherence to Source Documentation

| Requirement (source) | Addressed in |
|---|---|
| 6 service landing pages, hub-and-spoke (`BRIEF §5/§7`) | §2.1, §4.3–4.4 |
| Mobile-first, Tailwind-only, Alpine.js (`SKILL §3/§9`) | §1.5, §7, §9 |
| Corporate visual direction — brand colors purple `#6c3483` + green `#008000`, dark footer (`BRIEF §9`, client brand) | §1.2, §3.1, §6.9, §11 |
| Conversion CTAs ("Request a Quote", etc.) (`BRIEF §9`) | §1.4, §5, §6 |
| Contact + Newsletter functional specs (`BRIEF §10`) | §4.8, §6.5 |
| Ecommerce shop/cart/checkout (`SKILL §23/§29`) | §4.9–4.13 |
| SEO + schema + redirects (`BRIEF §7/§8`) | §2.1, §10.6 |
| PageSpeed ≥ 90 / CWV (`BRIEF §12`) | §10 |
| WCAG accessibility (`INDEX`, Phase 10) | §8 |
| Blog/editorial CMS (`SKILL §22/§24`) | §4.14 (roadmap) |
| CMS-driven, defensive section rendering | §2.5, §6 (matches `home.php`) |

### 13.2 Consistency Decisions to Standardize at Phase 1

These minor inconsistencies in the current codebase should be resolved as a deliberate token decision (not left ad-hoc):

1. **Border radius:** views mix implicit sharp corners (service/project cards in `home.php` have no radius) with `rounded-md` (buttons/inputs). **Decision:** adopt `rounded-md` (0.5rem) as the default card/control radius for a softer premium feel, or intentionally keep cards square for an industrial look — **pick one and apply globally.** *Recommendation: `rounded-md` cards + `rounded-lg` for large feature panels.*
2. **Brand-color contrast rules (resolved & implemented):** the CTA is **always purple** — solid purple + white text on light; **inverted (white surface + purple label) on dark** via `.btn-on-dark` (solid purple fails contrast on the dark anchor). Green is reserved for success/HSE/Agro accents (never CTAs); accent *text* on dark = `primary-200` light purple. Legacy `desnky.navy/blue/gold/green` aliases remain mapped to the new brand for back-compat and should be migrated to the semantic `primary/secondary/dark` tokens over time.
3. **Card elevation:** standardize `shadow-card` + `border-gray-200` vs. borderless `shadow-card` — currently both exist. *Recommendation: `shadow-card` with a hairline border for definition on white.*
4. **Section background alternation:** keep the `white`/`bg-desnky-surface` alternation as a documented rule so new sections slot into the rhythm automatically.
5. **Component directory:** introduce `components/` and migrate repeated card/alert/button markup out of page views to enforce reuse.

### 13.3 Completeness Check

- ✅ All 12 required deliverable sections present and expanded.
- ✅ Every page in the project IA has a redesign plan.
- ✅ All 16 component categories documented.
- ✅ Rationale provided for major decisions (palette use, hero-slider stance, static-first rendering, no heavy JS).
- ✅ Grounded in the **actual stack and existing tokens/components** — directly implementable.
- ✅ Scalability/maintainability addressed (CMS-driven, tokenized, component-based, roadmap-phased).

### 13.4 Open Items for Stakeholder Input

- **Hero slider:** confirm static-first (recommended) vs. rotating.
- **Display typeface:** confirm single-family Inter (recommended) vs. adding a display face for heroes.
- **Testimonials/clients:** confirm availability of real logos, quotes, and project imagery (critical to the credibility-first philosophy — placeholders undermine it).
- **Blog launch timing:** confirm when the editorial CMS frontend (§4.14) enters scope.

---

**Document End — `docs/frontend.md`**
*This blueprint is production-ready and intended for direct use by designers and developers. It should be versioned alongside the codebase and updated as the design system evolves.*
