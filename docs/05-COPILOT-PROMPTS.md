# 🤖 AI Copilot Implementation Prompts
## Desnky Global Resources Ltd - Website Rebuild

**Document Version:** 1.0  
**Date:** May 2, 2026  
**Status:** Prompt Templates Ready

---

## Overview

This document provides structured, detailed prompts for GitHub Copilot and AI coding assistants to help build each phase of the Desnky website project.

### How to Use These Prompts

1. **Copy the exact prompt** from the appropriate section
2. **Paste into Copilot** (VS Code or GitHub Copilot Chat)
3. **Let Copilot generate code** based on the context
4. **Review and adjust** generated code as needed
5. **Follow the specification** provided in the prompt

### Prompt Quality Tips

- Use specific file paths from project structure
- Include relevant context (database schema, API format, etc.)
- Ask for specific patterns (Repository Pattern, Service Layer, etc.)
- Request proper error handling and validation
- Request PHP PSR-12 compliance
- Include PHPDoc comments requirement

---

## Phase 1: Foundation & Core Setup

### Prompt 1.1: Project Structure & Routing System

```
I'm building a custom PHP MVC framework for a corporate website.

Project Name: Desnky Global Resources Ltd Website
Base Path: /app

Requirements:
1. Create a Router class that:
   - Handles GET, POST, PUT, PATCH, DELETE methods
   - Supports route parameters (e.g., /products/{id})
   - Groups routes by prefix (e.g., /admin routes)
   - Supports middleware attachment
   - Stores routes in array structure
   - Matches incoming requests to registered routes
   - Returns matched route with parameters

2. Create a Route class that:
   - Stores method, path, controller, action
   - Stores attached middleware
   - Stores route parameters

3. Create routing file at /routes/web.php with example routes:
   - GET / → Frontend\HomeController@index
   - GET /services/{slug} → Frontend\ServiceController@show
   - POST /api/contact → Frontend\ContactController@submit

Follow PSR-12 coding standards and include PHPDoc comments.
```

### Prompt 1.2: Base Controller & View Engine

```
I need to create the base framework classes for my PHP MVC application.

Create two classes:

1. BaseController (app/Controllers/BaseController.php):
   - Constructor to receive Request object
   - view($template, $data = []) method to render views
   - json($data, $code = 200) method for JSON responses
   - redirect($url) method for redirects
   - abort($code, $message) method for error responses
   - validate($data, $rules) method for input validation
   - csrf() method to get/verify CSRF token
   - user() method to get current admin user

2. View class (app/View.php):
   - render($template, $data = []) method
   - Supports nested folders (e.g., 'frontend/pages/home')
   - Extracts $data variables into view scope
   - Includes layout wrapper
   - Captures and returns output as string

Example usage:
$this->view('frontend/pages/home', ['products' => $products]);

Include PHPDoc comments and follow PSR-12.
```

### Prompt 1.3: Database Connection & PDO Wrapper

```
Create a database connection manager using PDO for MySQL.

File: app/Database/Connection.php

Requirements:
1. Singleton pattern for single connection
2. __construct() accepts config array with:
   - host, port, database, username, password, charset
3. Methods:
   - prepare($sql) - returns PDOStatement
   - query($sql, $params = []) - executes and returns results
   - insert($sql, $params = []) - executes and returns lastInsertId
   - update($sql, $params = []) - executes and returns affected rows
   - delete($sql, $params = []) - executes and returns affected rows
   - beginTransaction()
   - commit()
   - rollBack()
4. Error handling:
   - Catch PDOException
   - Log errors
   - Throw custom DatabaseException
5. Connection pooling support

Include PHPDoc comments and PSR-12 compliance.
```

### Prompt 1.4: Dependency Injection Container

```
Create a lightweight DI Container for managing class dependencies.

File: app/Container.php

Requirements:
1. register($key, $callback) method to register services/classes
2. singleton($key, $callback) method for singleton services
3. make($key, $params = []) method to resolve and instantiate classes
4. Automatic constructor injection based on type hints
5. Support for circular dependency detection
6. get($key) method as alias for make()

Example usage:
$container->register('database', function() {
    return new Database([...config]);
});

$container->singleton('cache', function() {
    return new Cache();
});

$product = $container->make('ProductService');

Include PHPDoc, error handling, and PSR-12 compliance.
```

### Prompt 1.5: Database Migrations System

```
Create a database migration system to manage schema versions.

Files needed:
1. app/Database/Migration.php - Base class
2. scripts/migrate.php - CLI runner

Requirements:
1. Migration class:
   - Abstract class with up() and down() methods
   - Methods: create($table, callable), table($table, callable)
   - Schema fluent interface for columns

2. Migration runner (scripts/migrate.php):
   - Scans database/migrations/ for PHP files
   - Tracks completed migrations in migrations table
   - Runs up() for new migrations
   - Supports rollback with down()
   - Command line usage: php scripts/migrate.php [--rollback]

3. Create 3 example migrations:
   - admin_users table
   - pages table  
   - products table

With proper timestamps, foreign keys, indexes, and utf8mb4 collation.

Include error handling and PSR-12 compliance.
```

### Prompt 1.6: Custom Exception Classes

```
Create custom exception hierarchy for the application.

Location: app/Exceptions/

Create these exception classes (extend Exception):
1. ApplicationException - Base exception
2. ValidationException - Input validation failures
3. AuthorizationException - Permission denied
4. NotFoundException - Resource not found
5. DatabaseException - Database errors
6. TokenMismatchException - CSRF token invalid
7. ThrottleRequestsException - Rate limit exceeded

Each should:
- Have __construct($message, $code = 0)
- Include default messages
- Be properly documented with PHPDoc

Also create global exception handler:
- File: app/ExceptionHandler.php
- Catches all exceptions
- Logs to file
- Returns appropriate HTTP response
- Respects Accept header (JSON vs HTML)

Follow PSR-12 standards.
```

### Prompt 1.7: Configuration Management

```
Create centralized configuration management for the application.

Create files:
1. config/app.php - Application settings
2. config/database.php - Database connection
3. config/mail.php - Email settings  
4. config/security.php - Security settings
5. app/Config/AppConfig.php - Config loader class

Requirements:
1. Each config file returns array of settings
2. AppConfig class:
   - Loads config files from /config directory
   - get($key, $default = null) method with dot notation
   - set($key, $value) method
   - all() method returns all config
   - Caches loaded config

Usage:
$appName = Config::get('app.name');
$dbHost = Config::get('database.host');

Load .env variables with phpdotenv.

Include PHPDoc comments and PSR-12 compliance.
```

---

## Phase 2: Frontend Infrastructure  

### Prompt 2.1: Tailwind CSS Setup & Build

```
I need to setup Tailwind CSS for my PHP project.

Create/Configure:
1. tailwind.config.js:
   - Content paths: app/Views/**/*.php, public/assets/**/*.js
   - Custom theme with Desnky colors
   - Custom spacing scale
   - Custom breakpoints if needed
   - Font: Inter
   - Dark mode support

2. package.json scripts:
   - dev: Watch mode with hot reload
   - build: Production build with minification
   - Include PostCSS and Autoprefixer

3. resources/css/main.css:
   - @tailwind directives
   - Custom component definitions
   - Custom utility classes
   - CSS variables for theming

4. Create public/assets/css/.gitkeep

Build output: public/assets/css/main.css and main.min.css

Include comments explaining custom utilities and components.
```

### Prompt 2.2: Base Layout & Partials

```
Create the base view layout system for the Desnky website.

Create these view files (app/Views/):

1. frontend/layouts/base.php:
   - HTML5 doctype
   - Meta tags (charset, viewport, description)
   - Title placeholder {{ $title }}
   - CSS links
   - Header partial
   - Main content area with yield
   - Footer partial
   - Script tags

2. frontend/partials/header.php:
   - Logo/brand
   - Navigation menu
   - Search bar (optional)
   - CTA button

3. frontend/partials/footer.php:
   - Company info
   - Quick links
   - Newsletter signup
   - Social links
   - Copyright

4. frontend/partials/navigation.php:
   - Menu items: Home, Services, Projects, Contact
   - Mobile hamburger menu
   - Active link highlighting

Use Tailwind CSS utilities for styling. Include responsive design.
Follow semantic HTML5 structure. Include accessibility attributes.
```

### Prompt 2.3: Form Components & Validation

```
Create form helpers and validation system.

Files needed:
1. app/Helpers/FormHelper.php:
   - textInput($name, $value = '', $placeholder = '', $attributes = [])
   - emailInput($name, $value = '', $placeholder = '', $attributes = [])
   - textarea($name, $value = '', $rows = 4, $attributes = [])
   - select($name, $options, $selected = '', $attributes = [])
   - checkbox($name, $value, $checked = false, $label = '', $attributes = [])
   - submit($text = 'Submit', $attributes = [])
   - token() - outputs CSRF token field
   - error($field) - displays field error if exists

2. app/Validators/BaseValidator.php:
   - Ruleset: required, email, min:N, max:N, unique:table, regex:pattern
   - validate($data, $rules) method
   - errors() returns validation errors
   - Supports custom messages

3. Example contact form validation in app/Validators/ContactValidator.php:
   - name: required, string, max:150
   - email: required, email, unique:contacts
   - phone: required, regex pattern
   - message: required, string, min:10, max:5000

Usage in controller:
$validator = new ContactValidator();
$errors = $validator->validate($input, $rules);

Include PHPDoc and PSR-12 compliance.
```

### Prompt 2.4: AJAX Helper System

```
Create JavaScript AJAX helper system for form submissions without page reload.

File: public/assets/js/ajax-handler.js

Requirements:
1. function submitForm(formSelector, options = {})
   - Intercepts form submission
   - Prevents default behavior
   - Validates required fields
   - Gets CSRF token from form
   - Sends data via POST/JSON
   - Shows loading state
   - Handles success response
   - Handles error response
   - Shows toast notification
   - Options: endpoint, method, callbacks

2. function createToastNotification(message, type = 'success', duration = 3000)
   - Types: success, error, warning, info
   - Auto-disappears after duration
   - Styled with Tailwind

3. function showLoadingSpinner(target = document.body)
   - Shows spinner overlay
   - hideLoadingSpinner() to hide

4. Example form integration:
   <form id="contact-form">
     <input type="hidden" name="_token" value="{{ csrf() }}">
     ...fields...
   </form>

   submitForm('#contact-form', {
     endpoint: '/api/contact',
     onSuccess: (response) => { ... },
     onError: (error) => { ... }
   });

Include error handling, loading states, and validation feedback.
```

---

## Phase 3: Public Website Pages

### Prompt 3.1: Homepage Implementation

```
Build the Desnky Global Resources homepage.

File: app/Views/frontend/pages/home.php

Requirements:
1. Hero Section:
   - Hero image background
   - Headline: "Integrated Energy, Engineering, Procurement, Safety, ICT and Agro Solutions in Nigeria"
   - Subheadline
   - CTA button: "Learn More"

2. Services Overview:
   - 6 service cards: Engineering, Energy, Procurement, HSE, ICT, Agro
   - Each card: title, icon, short description, "View More" link
   - Grid layout responsive (1 col mobile, 2 col tablet, 3 col desktop)

3. Why Choose Us:
   - 3-4 benefit statements
   - Icons for each benefit
   - Icon + text layout

4. HSE Commitment:
   - Section highlighting HSE values
   - Simple clean design
   - Link to full HSE policy

5. Featured Projects:
   - 3-4 project cards with images
   - Project title and brief description
   - "View Gallery" button

6. Clients Section:
   - Client logos in grid
   - "Our Trusted Clients" heading

7. Newsletter:
   - Email input
   - Subscribe button
   - Success message display

8. Call-to-Action:
   - "Ready to work with us?" section
   - Contact button

Use Tailwind CSS for all styling. Include responsive design.
Implement proper semantic HTML. Add accessibility attributes.
Include comments for maintainability.
```

### Prompt 3.2: Services Pages - Listing & Details

```
Create service pages for Desnky website.

Files needed:
1. app/Views/frontend/pages/services/index.php:
   - Page title "Our Services"
   - Service cards grid (3 columns desktop, 2 tablet, 1 mobile)
   - Each card: Icon, title, description, "Learn More" button
   - Filter/category tabs (optional)
   - Breadcrumbs

2. app/Views/frontend/pages/services/show.php (template for individual service):
   - Breadcrumbs
   - Service hero section with image
   - Service title and overview
   - Service features/details list
   - Why choose this service section
   - Process/workflow diagram
   - Related services section
   - Call-to-action section
   - Related projects section

3. Controllers needed:
   - Frontend\ServiceController:
     - index() - list all services
     - show($slug) - show service detail

Create specific service pages for:
- Engineering Services
- Energy Solutions
- Procurement Services
- HSE/Safety Services
- ICT Solutions
- Agro Products & Food Processing

Each should have relevant content, images, and CTAs.

Use Tailwind CSS. Include SEO meta tags.
Implement proper HTML structure. Add accessibility.
Include pagination if many services.
```

### Prompt 3.3: Contact Form Implementation

```
Build the Contact Us page with functional form.

File: app/Views/frontend/pages/contact.php

Requirements:
1. Page layout:
   - Breadcrumbs
   - Page heading
   - Two columns: Form on left, Info on right

2. Contact Form:
   - Fields: name, email, phone, company, service_interested, message
   - All fields required
   - Email validation
   - Phone validation (Nigeria format)
   - Textarea for message
   - CSRF token
   - Submit button
   - Client-side validation
   - AJAX submission

3. Company Info Column:
   - Company name and description
   - Phone number (clickable tel: link)
   - Email (clickable mailto: link)
   - Physical address
   - Google Maps embedded (if available)
   - Business hours

4. Form Handling:
   - Frontend\ContactController@index (GET) - show form
   - Frontend\ContactController@submit (POST) - process form
   - Validate input server-side
   - Store in contacts table
   - Send email to admin
   - Return JSON success response

5. Success/Error Handling:
   - Show success toast notification
   - Clear form after success
   - Show validation errors
   - Rate limiting (max 5 per hour per IP)

Use Tailwind CSS. Include proper accessibility.
Add loading spinner during submission.
Include honeypot spam protection (optional).
```

### Prompt 3.4: Shop Pages - Listing & Product Detail

```
Create product shop pages for Desnky website.

Files needed:
1. app/Views/frontend/pages/shop/index.php:
   - Product grid (3 columns desktop, 2 tablet, 1 mobile)
   - Each product card: image, title, price, "View" button, "Add to Cart" button
   - Category filter sidebar
   - Price range filter
   - Search box
   - Sort options (name, price, newest)
   - Pagination

2. app/Views/frontend/pages/shop/product.php (single product):
   - Product hero image (large)
   - Image gallery/carousel
   - Product title, description
   - Price display (original and discount if applicable)
   - Stock status
   - Add to cart form:
     - Quantity selector
     - Add to cart button
   - Product details section
   - Related products
   - Reviews section (optional)
   - Breadcrumbs

3. Controllers:
   - Frontend\ShopController:
     - index() - list products with filters
     - category($slug) - show category products
     - show($slug) - show product detail

4. Features:
   - Image lazy loading
   - Add to cart via AJAX
   - Cart update without page reload
   - Responsive design
   - Mobile-friendly

Use Tailwind CSS. Include SEO for products.
Add proper accessibility. Include loading states.
Handle out-of-stock products.
```

### Prompt 3.5: Cart & Checkout Pages

```
Build shopping cart and checkout pages.

Files needed:
1. app/Views/frontend/pages/shop/cart.php:
   - Cart items table:
     - Product image, name, price, quantity, subtotal
     - Update quantity button
     - Remove button
   - Cart summary:
     - Subtotal
     - Shipping (if applicable)
     - Tax (if applicable)
     - Total
   - Proceed to checkout button
   - Continue shopping button
   - Empty cart message

2. app/Views/frontend/pages/shop/checkout.php:
   - Multi-step checkout (optional):
     - Step 1: Billing info
     - Step 2: Delivery address
     - Step 3: Order review
     - Step 4: Confirmation
   - Form fields:
     - Full name (required)
     - Email (required, email validation)
     - Phone (required, Nigeria format)
     - Delivery address (required)
     - City/State (required)
     - Order notes (optional)
     - Payment method select
   - Order review section:
     - Items list
     - Totals recap
     - Confirm order button
   - Form validation
   - CSRF token

3. Controllers:
   - Frontend\CartController:
     - index() - show cart
     - add() - add item via AJAX
     - update() - update quantity
     - remove() - remove item
   - Frontend\CheckoutController:
     - index() - show checkout form
     - store() - process checkout, create order

4. Features:
   - Prevent checkout with empty cart
   - Validate stock availability before checkout
   - Calculate totals server-side
   - Create order in database
   - Send confirmation email
   - Redirect to order confirmation page

Use Tailwind CSS. Include proper validation.
Add loading states. Handle errors gracefully.
Include accessibility features.
```

### Prompt 3.6: SEO Layer Implementation

```
Implement comprehensive SEO features across the website.

Requirements:
1. Meta Tags Helper (app/Helpers/SeoHelper.php):
   - render() method outputs meta tags
   - setTitle($title)
   - setDescription($description)
   - setKeywords($keywords)
   - setOgImage($url)
   - setCanonical($url)
   - addSchema($schema)

2. Global SEO View (app/Views/frontend/partials/seo-meta.php):
   - Included in base layout
   - Renders all meta tags
   - Open Graph tags
   - Twitter card tags
   - JSON-LD schema

3. Service Schema (product schema for services):
   - @type: Service
   - name, description, provider, areaServed
   - Include for each service

4. Product Schema:
   - @type: Product
   - name, description, price, image
   - For each shop product

5. Organization Schema:
   - @type: Organization
   - name, logo, contact info, social profiles

6. Breadcrumb Schema:
   - Include on detail pages
   - Auto-generate from route

7. Page-specific implementation:
   - Homepage: Organization schema
   - Service pages: Service schema + breadcrumbs
   - Product pages: Product schema + breadcrumbs
   - Contact: Organization + local business schema

Each page must have:
- Unique, descriptive title (50-60 chars)
- Unique meta description (150-160 chars)
- H1 heading (only one per page)
- Proper H2/H3 hierarchy
- Image alt text on all images
- Internal links to related pages

Include validation and best practices.
```

---

## Phase 4: Admin Dashboard

### Prompt 4.1: Admin Authentication System

```
Build secure admin authentication system.

Files needed:
1. app/Controllers/Admin/AuthController.php:
   - login() - GET, show login form
   - authenticate() - POST, validate credentials
   - logout() - POST, clear session

2. app/Views/admin/login.php:
   - Minimal layout (no sidebar)
   - Email input
   - Password input
   - "Remember me" checkbox
   - Login button
   - "Forgot password?" link (future)
   - Styling: centered card, professional

3. app/Services/AdminUserService.php:
   - authenticate($email, $password)
   - Returns AdminUser or null
   - Uses password_verify()

4. app/Models/AdminUser.php:
   - Properties: id, full_name, email, password_hash, status, last_login_at
   - Methods: checkPassword($password), updateLastLogin()

5. Middleware (app/Middleware/AuthMiddleware.php):
   - Check if admin logged in
   - Check session validity
   - Enforce HTTPS
   - Regenerate session after login

6. Security features:
   - Password hashing with PASSWORD_DEFAULT (BCRYPT)
   - Login attempt throttling (max 5 per 15 min per IP)
   - Account lockout after failed attempts
   - Secure session settings (httpOnly, Secure, SameSite)
   - CSRF token validation
   - Session timeout (1 hour)

7. Remember me (optional):
   - Persistent remember token in database
   - Validate token on subsequent visits
   - Auto-login if valid

Include error messages for invalid credentials.
Log failed login attempts. Include PHPDoc comments.
Follow PSR-12 standards.
```

### Prompt 4.2: Admin Dashboard & KPI Widgets

```
Build admin dashboard with KPI widgets.

Files needed:
1. app/Controllers/Admin/DashboardController.php:
   - index() method
   - Gather KPI data
   - Pass to view

2. app/Views/admin/pages/dashboard.php:
   - Grid layout for widgets
   - Responsive (1 col mobile, 2 col tablet, 4 col desktop)

3. KPI Widgets to create:
   - Total pages published
   - Total products listed
   - Pending orders count
   - Completed orders total
   - Total inquiries (unread count)
   - Newsletter subscribers count
   - Low stock products count
   - Total admin users

4. Recent Activity Section:
   - Recent inquiries (last 5)
   - Recent orders (last 5)
   - Recent activities (last 10)
   - Each shows: action, user, timestamp

5. Quick Actions:
   - Create new page button
   - Create new product button
   - View pending orders
   - View new inquiries

6. Charts/Graphs (optional):
   - Orders by status (pie chart)
   - Products by category (bar chart)
   - Revenue trend (line chart)
   - Use Chart.js library

Services needed:
1. DashboardService.php:
   - getKpis() - return all KPI data
   - getRecentInquiries($limit = 5)
   - getRecentOrders($limit = 5)
   - getRecentActivities($limit = 10)

Use Tailwind CSS for styling.
Make widgets clickable to drill down.
Include refresh button.
Display last updated timestamp.
```

### Prompt 4.3: Admin Navigation & Layout

```
Create admin dashboard layout with sidebar navigation.

Files needed:
1. app/Views/admin/layouts/base.php:
   - Two-column layout: sidebar + main content
   - Header with logo and user profile
   - Sidebar navigation
   - Main content area (yield)
   - Footer

2. app/Views/admin/partials/sidebar.php:
   - Admin menu items with icons
   - Collapse/expand on mobile
   - Active menu highlighting
   - Menu structure:
     - Dashboard
     - Content:
       - Pages
       - Services
       - Projects
     - Ecommerce:
       - Products
       - Categories
       - Orders
       - Customers (optional)
     - Admin:
       - Users
       - Roles
       - Permissions
       - Activity Logs
     - Settings
       - Site Settings
       - Email Settings
     - Logout

3. app/Views/admin/partials/header.php:
   - Logo/brand
   - Search bar (optional)
   - Notifications dropdown (optional)
   - User profile dropdown:
     - Edit profile link
     - Settings link
     - Logout button
   - Responsive toggle for sidebar

4. app/Views/admin/partials/breadcrumbs.php:
   - Breadcrumb navigation
   - Links to parent pages
   - Current page highlighted

5. Styling:
   - Sidebar: Dark background (dark gray or navy)
   - Main content: Light background
   - Responsive: Collapse sidebar on mobile
   - Smooth transitions
   - Mobile hamburger menu

Use Tailwind CSS utilities.
Include accessibility features.
Add keyboard navigation.
Include active menu highlighting.
```

---

## Phase 5-11: Advanced Features

### Prompt 5.x: Blog / Editorial CMS With TipTap

```
Build a complete blog/editorial CMS module for the Desnky PHP MVC application.

Context:
- PHP 8.1+ custom MVC architecture
- Controllers must be thin
- Business logic belongs in services
- Data access belongs in repositories
- Database: MySQL with PDO prepared statements
- Admin UI: Tailwind CSS, Alpine.js/jQuery where needed
- Editor: TipTap WYSIWYG
- Existing RBAC, activity logs, SEO helper, sitemap generator, media library and admin layout are available

Database:
1. posts
   - id, category_id, title, slug, excerpt
   - content_json JSON as canonical TipTap document
   - content_html LONGTEXT sanitized for frontend rendering
   - featured_image_id, og_image_id
   - author_id, reviewed_by, published_by
   - status: draft, review, scheduled, published, archived
   - visibility: public, private
   - allow_indexing
   - published_at, scheduled_at, archived_at
   - meta_title, meta_description, meta_keywords, canonical_url
   - reading_time_minutes
   - timestamps, soft delete
   - fulltext index on title/excerpt/content_html

2. post_categories
   - parent_id, name, slug, description, featured_image_id
   - meta_title, meta_description, canonical_url
   - sort_order, is_active
   - created_by, updated_by, timestamps, soft delete

3. post_tags
   - name, slug, description
   - meta_title, meta_description
   - is_active, created_by, updated_by, timestamps, soft delete

4. post_tag
   - post_id, tag_id composite primary key

5. post_media
   - post_id, media_id, usage_type: featured, inline, og_image, gallery
   - sort_order

6. post_revisions
   - post_id, title, excerpt, content_json, content_html, status, created_by, created_at

Backend:
1. Repositories:
   - PostRepository
   - PostCategoryRepository
   - PostTagRepository
   - MediaRepository extensions for usage lookup

2. Services:
   - PostService handles slugging, validation, TipTap JSON sanitation, HTML sanitation, status transitions, scheduled publishing, revision capture, reading-time calculation, sitemap/cache invalidation and activity logging.
   - PostCategoryService handles hierarchy validation, duplicate prevention and safe delete.
   - PostTagService handles duplicate prevention, normalization and safe delete.

3. Controllers:
   - Admin\PostController: index, create, store, edit, update, preview, publish, schedule, archive, destroy, restore
   - Admin\PostCategoryController: CRUD
   - Admin\PostTagController: CRUD
   - Frontend\BlogController: index, show, category, tag, author, rss

4. Routes:
   - Public: /blog, /blog/{slug}, /blog/category/{slug}, /blog/tag/{slug}, /blog/rss.xml
   - Admin: /admin/posts, /admin/post-categories, /admin/post-tags

Frontend/Admin:
1. Admin post index:
   - Search, status/category/tag/author/date filters
   - Bulk actions where safe
   - Published/scheduled/draft badges

2. Post create/edit:
   - TipTap editor
   - Autosave draft
   - Featured image and inline media picker
   - Category selector
   - Tag selector with create-on-demand if permitted
   - SEO preview with title/description counters
   - Publish controls gated by RBAC
   - Revision history panel

3. Public blog:
   - Blog index with pagination
   - Post detail page
   - Category and tag archive pages
   - Related posts by category/tag
   - Internal links to relevant services
   - RSS feed

Security:
- CSRF on all state-changing routes
- RBAC permissions:
  posts.view/create/edit/delete/publish/schedule/restore
  post_categories.view/create/edit/delete
  post_tags.view/create/edit/delete
  media.attach
- Sanitize TipTap JSON against an allowlist of nodes and marks
- Sanitize rendered HTML server-side
- Escape all frontend output
- Validate media type, size, dimensions and ownership/usage
- Do not allow executable uploads

SEO:
- Unique title/description/canonical per post/category/tag
- BlogPosting/Article schema for posts
- Breadcrumb schema on all blog pages
- ImageObject schema for featured media
- Include published posts/categories/tags in sitemap
- Exclude drafts, private posts and noindex posts

Testing:
- Unit tests for PostService, category/tag validation, TipTap sanitizer and reading-time calculation
- Integration tests for admin CRUD, RBAC, CSRF, publish workflow, scheduled publishing and public rendering
- E2E workflow tests for create draft -> preview -> publish -> archive
- Media attachment tests

Follow PSR-12, include PHPDoc, and use prepared statements only.
```

### General Prompt Template for Remaining Phases

```
[For any feature in Phases 5-11, use this template]

I'm building a feature for my PHP MVC application.

Feature: [Name of feature]
Database Tables: [Tables involved]
User Roles: [Who can access]

Requirements:
1. Database:
   [Table structure needed]

2. Repository Layer (app/Repositories/):
   [Methods needed with specific queries]

3. Service Layer (app/Services/):
   [Business logic methods]
   [Error handling needed]

4. Controller (app/Controllers/Admin/):
   [Actions needed]
   [Input validation]
   [Response format]

5. Views (app/Views/admin/pages/):
   [Forms/lists needed]
   [UI components]
   [Form fields]

6. API Endpoints (optional):
   [JSON endpoints]
   [Response format]

Follow architecture:
- Thin controllers
- Business logic in services
- Data access in repositories
- PDO prepared statements (no SQL injection)
- Proper error handling and logging
- Input validation on server side
- Output escaping in views
- PSR-12 code style
- Complete PHPDoc comments

Provide:
- Complete code with all methods
- Database queries as needed
- Test cases (unit/integration)
- Usage examples
```

### Feature Implementation Template

For each feature (Phases 5-11), follow this pattern:

1. **Database Requirements**
   - Tables needed
   - Foreign keys
   - Indexes
   - Constraints

2. **Backend Implementation**
   - Repository methods
   - Service business logic
   - Controller actions
   - Error handling

3. **Frontend Implementation**
   - Views/templates
   - Forms
   - Validation
   - Styling with Tailwind

4. **Testing**
   - Unit tests
   - Integration tests
   - Edge cases

5. **Security**
   - Input validation
   - Authorization checks
   - CSRF protection
   - Rate limiting (if needed)

---

## Best Practices for Using AI Copilot

### Do's ✓

- ✓ Provide complete context and requirements
- ✓ Ask for specific design patterns (Repository, Service, etc.)
- ✓ Request proper error handling
- ✓ Ask for comprehensive PHPDoc comments
- ✓ Request PSR-12 compliance
- ✓ Provide file paths explicitly
- ✓ Ask for security best practices
- ✓ Request test cases
- ✓ Ask for refactoring after generation
- ✓ Verify generated code before committing

### Don'ts ✗

- ✗ Don't ask for code without specifying architecture
- ✗ Don't accept code without reviewing it first
- ✗ Don't mix multiple unrelated features in one prompt
- ✗ Don't skip security requirements
- ✗ Don't accept code without tests
- ✗ Don't ignore performance implications
- ✗ Don't mix database access with business logic
- ✗ Don't hardcode configuration values
- ✗ Don't accept incomplete error handling
- ✗ Don't skip code documentation

---

## Useful Copilot Follow-up Prompts

After initial code generation, use these follow-ups:

### Code Review & Refactoring

```
Review the code you just generated for:
1. Security vulnerabilities
2. Performance issues
3. Code style (PSR-12 compliance)
4. Error handling coverage
5. Edge cases not handled

Suggest improvements and refactor if needed.
```

### Add Tests

```
Write comprehensive unit and integration tests for the code above.
Include:
- Happy path tests
- Error cases
- Edge cases
- Input validation tests
- Authorization tests

Use PHPUnit and follow testing best practices.
```

### Add Documentation

```
Add comprehensive PHPDoc comments to all classes and methods.
Include:
- @param descriptions
- @return type and description
- @throws documented exceptions
- @example usage
```

### Performance Optimization

```
Review the code for performance issues:
1. N+1 query problems
2. Missing indexes
3. Unnecessary loops
4. Cache opportunities
5. Query optimization

Provide optimized version.
```

---

## Example: Complete Feature Build Prompt

Here's a template for a complete feature build:

```
Build [Feature Name] module for admin dashboard.

Context:
- PHP 8.1+ application using custom MVC framework
- Database: MySQL 8.0+
- Current architecture: Controllers → Services → Repositories

Feature Requirements:
[Detailed feature requirements]

Database:
- Table: [table_name]
- Columns: [list with types]
- Foreign keys: [relationships]
- Indexes: [performance indexes]

APIs:
- GET /admin/api/[resource]
- POST /admin/api/[resource]
- PUT /admin/api/[resource]/{id}
- DELETE /admin/api/[resource]/{id}

Views Needed:
- List view with pagination, search, filters
- Create form
- Edit form
- Confirmation dialogs

Implementation:
1. Create migration: database/migrations/YYYY_MM_DD_create_[table].php
2. Create repository: app/Repositories/[Name]Repository.php
3. Create service: app/Services/[Name]Service.php
4. Create controller: app/Controllers/Admin/[Name]Controller.php
5. Create views: app/Views/admin/pages/[resource]/ (index, create, edit, show)

Requirements:
- Use Repository pattern for data access
- All database queries as PDO prepared statements
- Comprehensive error handling
- Input validation for all user input
- Server-side authorization checks
- Activity logging for all changes
- Responsive design with Tailwind CSS
- PSR-12 code style
- Complete PHPDoc comments
- Unit tests with 80%+ coverage

Provide all necessary code files.
```

---

## Next Steps After Code Generation

1. **Review Generated Code**
   - Check for security issues
   - Verify against requirements
   - Check code style

2. **Test the Code**
   - Run unit tests
   - Run integration tests
   - Manual testing in browser

3. **Integrate into Project**
   - Copy files to correct locations
   - Run migrations if needed
   - Update routes if needed
   - Clear cache

4. **Document Changes**
   - Update CHANGELOG
   - Update API documentation
   - Add comments for complex logic

5. **Commit to Git**
   - Review git diff
   - Write descriptive commit message
   - Push to feature branch

---

**Document End**
