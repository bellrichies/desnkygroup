# 📋 Phase-by-Phase Development Workflow
## Desnky Global Resources Ltd - Website Rebuild

**Document Version:** 1.0  
**Date:** May 2, 2026  
**Status:** Phase Planning Complete

---

## Table of Contents

1. [Overview](#overview)
2. [Phase 1: Foundation & Core Setup](#phase-1-foundation--core-setup)
3. [Phase 2: Frontend Infrastructure](#phase-2-frontend-infrastructure)
4. [Phase 3: Public Website Pages](#phase-3-public-website-pages)
5. [Phase 4: Admin Dashboard Core](#phase-4-admin-dashboard-core)
6. [Phase 5: Content Management System](#phase-5-content-management-system)
7. [Phase 6: Ecommerce Module](#phase-6-ecommerce-module)
8. [Phase 7: Admin User Management](#phase-7-admin-user-management)
9. [Phase 8: Security & Performance](#phase-8-security--performance)
10. [Phase 9: SEO & Analytics](#phase-9-seo--analytics)
11. [Phase 10: Testing & Refinement](#phase-10-testing--refinement)
12. [Phase 11: Deployment & Launch](#phase-11-deployment--launch)

---

## Overview

### Development Timeline

```
Phase 1-3: Foundation & Basic Website (Weeks 1-4)
Phase 4-7: Admin & Advanced Features (Weeks 5-9)
Phase 8-10: Security, Performance, Testing (Weeks 10-13)
Phase 11: Deployment & Launch (Week 14)

Total Duration: ~14 weeks for production-ready system
```

### Team Requirements

- **1 Backend Developer** (PHP/MySQL expert)
- **1 Frontend Developer** (HTML/CSS/JavaScript)
- **1 QA/Testing Specialist**
- **1 DevOps/Infrastructure Specialist** (part-time)

### Success Criteria

Each phase must meet:
- ✓ All planned features implemented
- ✓ Code passes linting (PSR-12)
- ✓ Unit tests > 80% coverage
- ✓ No critical security issues
- ✓ Performance meets targets
- ✓ Documentation complete

---

## Phase 1: Foundation & Core Setup

**Duration:** 5-7 days  
**Team:** Backend Dev + DevOps  
**Goals:** Establish project infrastructure, core framework, and database

### Deliverables

- [x] Project repository with proper structure
- [x] PHP MVC framework core
- [x] Database schema and migrations
- [x] Environment configuration
- [x] Routing system
- [x] Dependency injection container
- [x] Error handling and logging
- [x] Initial documentation

### Technical Tasks

#### 1.1 Project Scaffolding
- [x] Create directory structure as defined
- [x] Initialize Git repository
- [x] Setup .gitignore and .env files
- [x] Create composer.json with dependencies
- [x] Setup autoloading with PSR-4

#### 1.2 MVC Framework Core
- [x] Create base Router class with routing engine
- [x] Create base Controller class with common methods
- [x] Create View class with template rendering
- [x] Implement request/response handling
- [x] Setup middleware pipeline

#### 1.3 Database Layer
- [x] Create PDO connection manager
- [x] Create Query builder (basic)
- [x] Create Base repository class
- [x] Create database migration system
- [x] Write all table migrations (50+ tables)
- [x] Create database seeders

#### 1.4 Services & Helpers
- [x] Create Base service class
- [x] Create common helper functions
- [x] Create validation utilities
- [x] Create encryption/hashing utilities
- [x] Create logging system

#### 1.5 Configuration Management
- [x] Create AppConfig class
- [x] Create DatabaseConfig class
- [x] Create MailConfig class
- [x] Create security constants
- [x] Load environment variables with phpdotenv

#### 1.6 Exception Handling
- [x] Create custom exception classes
- [x] Create global exception handler
- [x] Setup error logging
- [x] Create error display/logging views

#### 1.7 Documentation
- [x] Create CONTRIBUTING.md
- [x] Create API structure documentation
- [x] Document database schema
- [x] Create local setup guide

### Phase 1 Copilot Prompts

See detailed prompts in section: **Phase 1 Copilot Prompts**

### Testing

```bash
# Unit tests for framework components
./vendor/bin/phpunit tests/Unit/Framework/

# Database migration test
php scripts/migrate.php --test
```

### Acceptance Criteria

- [x] All migrations run successfully
- [x] Project structure matches specification
- [x] PSR-4 autoloading works
- [x] No PHP errors on basic request
- [x] Routing system functional
- [x] Database connections work
- [x] Logging captures errors

---

## Phase 2: Frontend Infrastructure

**Duration:** 3-5 days  
**Team:** Frontend Dev + Backend Dev  
**Goals:** Setup frontend tooling, layout system, and reusable components

### Deliverables

- [x] Tailwind CSS configured and working
- [x] Base layout templates
- [x] Reusable component system
- [x] JavaScript utilities
- [x] Form validation framework
- [x] AJAX handler system
- [x] Responsive design foundation

### Technical Tasks

#### 2.1 Tailwind CSS Setup
- [x] Install Tailwind CSS via npm
- [x] Configure tailwind.config.js
- [x] Create custom theme (colors, fonts, spacing)
- [x] Setup PostCSS
- [x] Create production build script
- [x] Integrate with views

#### 2.2 View System
- [x] Create base layout (header, footer, main)
- [x] Create minimal layout (for admin login)
- [x] Create admin layout (with sidebar)
- [x] Create partials system
- [x] Setup partial includes
- [x] Create view inheritance

#### 2.3 Reusable Components
- [x] Create button component
- [x] Create card component
- [x] Create alert component
- [x] Create form group component
- [x] Create modal component
- [x] Create pagination component
- [x] Create breadcrumbs component
- [x] Create loading spinner

#### 2.4 JavaScript Infrastructure
- [x] Create main.js entry point
- [x] Setup Alpine.js initialization
- [x] Create AJAX helper functions
- [x] Create form validation system
- [x] Create notification system
- [x] Create utility functions

#### 2.5 Forms & Validation
- [x] Create form helper class
- [x] Implement HTML5 form validation
- [x] Create client-side validator
- [x] Create server-side validator
- [x] Create error message display
- [x] Create success message display

#### 2.6 Static Assets
- [x] Optimize and add hero images
- [x] Add service icons
- [x] Setup web fonts (Inter)
- [x] Setup favicon
- [x] Configure image lazy loading

#### 2.7 Responsive Design
- [x] Test mobile breakpoints
- [x] Create mobile navigation
- [x] Test tablet layouts
- [x] Test desktop layouts
- [x] Implement touch-friendly buttons

### Testing

```bash
# Visual regression testing
npm test

# Lighthouse audit
npm run audit
```

### Acceptance Criteria

- [x] Tailwind CSS compiles without errors
- [x] All layouts render correctly
- [x] Forms validate on client and server
- [x] AJAX requests work
- [x] Mobile responsive on all breakpoints
- [x] PageSpeed score > 80

---

## Phase 3: Public Website Pages

**Duration:** 7-10 days  
**Team:** Frontend Dev + Backend Dev  
**Goals:** Build all public-facing pages with SEO optimization

### Deliverables

- [x] Homepage with all sections
- [x] Services listing and detail pages
- [x] Projects/gallery pages
- [x] Contact page with working form
- [x] About page
- [x] Newsletter subscription
- [x] 404 and error pages
- [x] SEO metadata for all pages

### Technical Tasks

#### 3.1 Homepage
- [x] Hero section with headline
- [x] Service overview cards
- [x] Company info section
- [x] Featured projects
- [x] Clients section
- [x] Newsletter signup
- [x] Call-to-action buttons
- [x] SEO optimization

#### 3.2 Services Pages
- [x] Services listing page
- [x] Service detail template
- [x] Service pages (Engineering, Energy, Procurement, HSE, ICT, Agro)
- [x] Related services section
- [x] Call-to-action for each
- [x] Service filtering/categories
- [x] SEO for each service

#### 3.3 Projects/Gallery
- [x] Projects listing page
- [x] Project detail page
- [x] Image lightbox
- [x] Category filtering
- [x] Search functionality
- [x] Pagination
- [x] Image lazy loading

#### 3.4 Contact Page
- [x] Contact form with fields
- [x] Company information display
- [x] Google Maps integration
- [x] FAQ section
- [x] Form submission via AJAX
- [x] Success/error messages
- [x] Email notifications to admin

#### 3.5 About Page
- [x] Company history
- [x] Mission & vision statement
- [x] Core values display
- [x] Team section
- [x] Certifications/awards
- [x] Timeline section

#### 3.6 Newsletter
- [x] Newsletter subscription form
- [x] Validation (no duplicates)
- [x] Database storage
- [x] Confirmation email (future)
- [x] Unsubscribe link

#### 3.7 Error Pages
- [x] 404 page design
- [x] 500 error page
- [x] Generic error page
- [x] Maintenance page (future)

#### 3.8 SEO Implementation
- [x] Meta titles and descriptions
- [x] Open Graph tags
- [x] Twitter card tags
- [x] Schema.org JSON-LD
- [x] Canonical URLs
- [x] Image alt text

### Testing

```bash
# Lighthouse audit for all pages
npm run audit

# SEO validation
npx lighthouse https://localhost:8000

# Mobile responsiveness
# Manual testing on various devices
```

### Acceptance Criteria

- [x] All pages render without errors
- [x] Contact form submits and stores data
- [x] Newsletter subscription works
- [x] All SEO tags present and unique
- [x] PageSpeed score > 90
- [x] Mobile score > 90
- [x] All links working
- [x] No broken images

---

## Phase 4: Admin Dashboard Core

**Duration:** 5-7 days  
**Team:** Backend Dev + Frontend Dev  
**Goals:** Build admin authentication and dashboard interface

### Deliverables

- [x] Admin login system
- [x] Session management
- [x] Admin dashboard layout
- [x] User authentication
- [x] Dashboard widgets/KPIs
- [x] Recent activity feed
- [x] Navigation system
- [x] Breadcrumbs system

### Technical Tasks

#### 4.1 Authentication System
- [x] Create admin login form
- [x] Validate admin credentials
- [x] Hash and verify passwords
- [x] Create session on login
- [x] Implement session timeout
- [x] Create logout functionality
- [x] Add login attempt throttling
- [x] Add "remember me" functionality (optional)

#### 4.2 Session Management
- [x] Store sessions in database
- [x] Implement session regeneration
- [x] Create session middleware
- [x] Add CSRF token generation
- [x] Verify CSRF on POST/PUT/DELETE
- [x] Implement session timeout
- [x] Add session garbage collection

#### 4.3 Dashboard Layout
- [x] Create admin base layout
- [x] Create sidebar navigation
- [x] Create top header
- [x] Create main content area
- [x] Create responsive mobile menu
- [x] Add user profile dropdown
- [x] Add logout button

#### 4.4 Dashboard Page
- [x] Display KPI widgets
- [x] Show recent inquiries
- [x] Show recent orders
- [x] Show recent activities
- [x] Create quick action buttons
- [x] Create chart/graph section
- [x] Display alerts/notifications

#### 4.5 Navigation System
- [x] Create sidebar menu structure
- [x] Add menu items for each module
- [x] Implement active menu highlighting
- [x] Create collapsible menu groups
- [x] Add mobile menu toggle
- [x] Add icon support for menu items

#### 4.6 Breadcrumbs System
- [x] Create breadcrumb helper
- [x] Display breadcrumbs on all pages
- [x] Generate from route structure
- [x] Add home breadcrumb
- [x] Style breadcrumbs

#### 4.7 Notification System
- [x] Create flash message class
- [x] Display success messages
- [x] Display error messages
- [x] Display warning messages
- [x] Display info messages
- [x] Toast notification UI

### Testing

```bash
# Login functionality tests
./vendor/bin/phpunit tests/Integration/AuthenticationTest.php

# Session management tests
./vendor/bin/phpunit tests/Integration/SessionTest.php

# Dashboard render tests
./vendor/bin/phpunit tests/Integration/DashboardTest.php
```

### Acceptance Criteria

- [x] Admin login works with valid credentials
- [x] Invalid credentials rejected
- [x] Session persists across requests
- [x] Session expires after timeout
- [x] CSRF tokens validated
- [x] Dashboard displays all widgets
- [x] Navigation works on desktop and mobile
- [x] Logout clears session

---

## Phase 5: Content Management System

**Duration:** 8-10 days  
**Team:** Backend Dev + Frontend Dev  
**Goals:** Build CMS for managing pages, services, projects, and media

### Deliverables

- [x] Page management (CRUD)
- [x] Service management (CRUD)
- [x] Project management (CRUD)
- [ ] Blog post management (CRUD)
- [ ] Blog categories and tags
- [x] Media library
- [x] Content editor
- [ ] TipTap WYSIWYG editor for blog content
- [x] SEO metadata manager
- [x] Publish/draft system
- [x] Activity logging

### Technical Tasks

#### 5.1 Page Management
- [x] Create pages list view
- [x] Create page create form
- [x] Create page edit form
- [x] Implement page deletion
- [x] Add publish/draft toggle
- [x] Add featured image selector
- [x] Add content editor (rich text or markdown)
- [x] SEO fields (title, description, keywords, og image)
- [x] Add slug auto-generation
- [x] Add page ordering

#### 5.2 Service Management
- [x] Create services list view
- [x] Create service create form
- [x] Create service edit form
- [x] Implement service deletion
- [x] Add icon selection
- [x] Add category/grouping
- [x] Add rich text editor for description
- [x] Add service ordering
- [x] Add publish/draft toggle

#### 5.3 Project Management
- [x] Create projects list view
- [x] Create project create form
- [x] Create project edit form
- [x] Implement project deletion
- [x] Add multiple image upload
- [x] Add image gallery management
- [x] Add project date picker
- [x] Add client information
- [x] Add category assignment
- [x] Add project ordering

#### 5.4 Media Library
- [x] Create media library interface
- [x] Upload image functionality
- [x] Image preview/thumbnail
- [x] Add alt text editor
- [x] Add title/description
- [x] Image deletion
- [x] Bulk upload support
- [x] Image search
- [x] File browser
- [x] Image optimization on upload
- [x] WebP format support
- [ ] Centralized media reuse across pages, services, projects, products, and blog posts
- [ ] Store media dimensions, captions, focal point, usage references, and uploader metadata
- [ ] Generate thumbnails/responsive derivatives
- [ ] Enforce non-executable upload storage and signed/private admin access where needed

#### 5.5 Content Editor
- [x] Integrate rich text editor (TinyMCE or Summernote)
- [ ] Integrate TipTap editor for blog posts
- [x] Add formatting tools
- [x] Add image insertion
- [x] Add link insertion
- [x] Add heading levels
- [x] Add list support
- [x] Add table support
- [x] Add code block support
- [ ] Store TipTap JSON document as source of truth and sanitized HTML for rendering
- [ ] Restrict allowed nodes/marks to approved editorial schema
- [ ] Add editor autosave, dirty-state warnings, and revision snapshots

#### 5.6 Blog Management
- [ ] Create posts table with title, slug, excerpt, TipTap JSON, sanitized HTML, featured image, author, reviewer, publisher, status, publish dates, SEO metadata, and reading time
- [ ] Create post_categories table with hierarchy, slug, description, active status, sorting, image, and SEO metadata
- [ ] Create post_tags table with slug, description, active status, and SEO metadata
- [ ] Create post_tag many-to-many table
- [ ] Create post_media table for featured, inline, Open Graph, and gallery media usage
- [ ] Create post_revisions table for version history
- [ ] Build PostRepository, PostCategoryRepository, PostTagRepository, and MediaRepository methods using prepared statements
- [ ] Build PostService with slug generation, publish rules, status transitions, revision capture, reading-time calculation, and cache invalidation
- [ ] Build PostCategoryService and PostTagService with duplicate prevention and safe delete checks
- [ ] Build admin post list with search, status, category, tag, author, and date filters
- [ ] Build admin create/edit forms with TipTap editor, media picker, category selector, tag selector, SEO preview, and publish controls
- [ ] Build public blog index, category archive, tag archive, author archive, and post detail pages
- [ ] Add public routes: `/blog`, `/blog/{slug}`, `/blog/category/{slug}`, `/blog/tag/{slug}`
- [ ] Add admin routes protected by RBAC: `/admin/posts`, `/admin/post-categories`, `/admin/post-tags`
- [ ] Add RSS feed route for published posts
- [ ] Include published posts, categories, and tags in sitemap generation
- [ ] Add BlogPosting, Article, BreadcrumbList, and ImageObject schema markup
- [ ] Add related posts by category/tag and internal links to services where relevant
- [ ] Log all post/category/tag/media editorial actions

#### 5.7 SEO Management
- [x] SEO title field (with character counter)
- [x] Meta description (with character counter)
- [x] Meta keywords field
- [x] Open Graph image selector
- [x] Canonical URL field
- [x] SEO preview display
- [x] URL slug editor
- [x] Meta tags validation
- [ ] Blog post SEO title, description, canonical, robots, Open Graph image, Article schema, and social preview
- [ ] Category/tag archive SEO metadata and canonical URLs

#### 5.8 Publishing System
- [x] Draft/published status toggle
- [x] Schedule publish date (future)
- [x] Preview page before publishing
- [x] Revision history (optional)
- [x] Auto-save draft
- [x] Publish confirmation
- [ ] Blog workflow: draft -> review -> scheduled/published -> archived
- [ ] Role-gated publish permission separate from edit permission
- [ ] Scheduled publishing via cron-compatible command
- [ ] Preview unpublished posts through signed/admin-only preview links
- [ ] Restore from post revision history

#### 5.9 Activity Logging
- [x] Log page creates/updates/deletes
- [x] Log service changes
- [x] Log project changes
- [x] Log media uploads
- [ ] Log post creates/updates/status changes/deletes/restores
- [ ] Log category and tag changes
- [ ] Log media attachment/detachment to posts
- [x] Store admin user who made change
- [x] Store timestamp
- [x] Store IP address
- [x] Activity log viewer

#### 5.10 Blog RBAC
- [ ] `posts.view`
- [ ] `posts.create`
- [ ] `posts.edit`
- [ ] `posts.delete`
- [ ] `posts.publish`
- [ ] `posts.schedule`
- [ ] `posts.restore`
- [ ] `post_categories.view`
- [ ] `post_categories.create`
- [ ] `post_categories.edit`
- [ ] `post_categories.delete`
- [ ] `post_tags.view`
- [ ] `post_tags.create`
- [ ] `post_tags.edit`
- [ ] `post_tags.delete`
- [ ] `media.attach`

### Testing

```bash
# CMS tests
./vendor/bin/phpunit tests/Integration/CMS/

# Media upload tests
./vendor/bin/phpunit tests/Integration/MediaUploadTest.php

# SEO validation tests
./vendor/bin/phpunit tests/Unit/Services/SeoServiceTest.php
```

### Acceptance Criteria

- [x] All CRUD operations work
- [x] Images upload and display
- [x] SEO fields populated correctly
- [x] Rich text editor functional
- [x] Drafts save without publishing
- [x] Activity logs created for all changes
- [x] No console errors
- [x] File upload validation (type, size)

---

## Phase 6: Ecommerce Module

**Duration:** 10-12 days  
**Team:** Backend Dev + Frontend Dev  
**Goals:** Build complete ecommerce system with products, cart, checkout, orders

### Deliverables

- [x] Product management system
- [x] Product categories
- [x] Shopping cart
- [x] Checkout process
- [x] Order management
- [x] Payment integration (future)
- [x] Inventory management
- [x] Order notifications

### Technical Tasks

#### 6.1 Product Management
- [x] Create product CRUD forms
- [x] Add product categories
- [x] Add SKU field
- [x] Add multiple pricing (regular + discount)
- [x] Add stock quantity tracking
- [x] Add product images (multiple)
- [x] Add featured product toggle
- [x] Add product status (active/inactive/discontinued)
- [x] Add SEO fields for products
- [x] Add product search
- [x] Add bulk product upload (CSV)

#### 6.2 Product Categories
- [x] Create category CRUD
- [x] Nested categories (parent/child)
- [x] Category images
- [x] Category slug
- [x] Category sorting
- [x] SEO for categories

#### 6.3 Shopping Cart
- [x] Add to cart functionality
- [x] Cart session storage
- [x] Update cart quantities
- [x] Remove from cart
- [x] Clear cart
- [x] Display cart items
- [x] Calculate cart totals
- [x] Validate stock availability
- [x] Show price subtotal

#### 6.4 Checkout Process
- [x] Checkout form (customer details)
- [x] Delivery address collection
- [x] Order notes field
- [x] Payment method selection
- [x] Order review page
- [x] Calculate taxes (if applicable)
- [x] Calculate shipping costs
- [x] Validate checkout data
- [x] Create order in database
- [x] Clear cart after order

#### 6.5 Order Management (Admin)
- [x] View all orders
- [x] Order detail page
- [x] Update order status
- [x] Status workflow (pending→confirmed→processing→shipped→delivered)
- [x] Order filtering by status/date
- [x] Order search by customer email
- [x] Order export (CSV)
- [x] Manual refund interface
- [x] Email notifications on status change

#### 6.6 Inventory Management
- [x] Stock quantity tracking
- [x] Low stock alerts
- [x] Prevent overselling
- [x] Inventory history
- [x] Bulk stock update
- [x] Stock reordering alerts

#### 6.7 Customer Notifications
- [x] Order confirmation email
- [x] Order status update emails
- [x] Shipping notification
- [x] Delivery confirmation
- [x] Admin new order notification
- [x] Out of stock notification (future)

#### 6.8 Product Display (Public)
- [x] Product listing with pagination
- [x] Category filtering
- [x] Product search
- [x] Price range filtering
- [x] Product detail page
- [x] Product image gallery
- [x] Add to cart button
- [x] Related products
- [x] Product reviews (optional)

### Testing

```bash
# Cart functionality tests
./vendor/bin/phpunit tests/Integration/CartTest.php

# Checkout workflow tests
./vendor/bin/phpunit tests/Integration/CheckoutTest.php

# Order management tests
./vendor/bin/phpunit tests/Integration/OrderManagementTest.php

# Inventory tests
./vendor/bin/phpunit tests/Integration/InventoryTest.php
```

### Acceptance Criteria

- [x] Products display on shop
- [x] Add to cart works
- [x] Cart calculations correct
- [x] Checkout validates data
- [x] Orders created in database
- [x] Order confirmation emails sent
- [x] Admin can update order status
- [x] Stock prevents overselling
- [x] No console errors

---

## Phase 7: Admin User Management

**Duration:** 6-8 days  
**Team:** Backend Dev  
**Goals:** Implement complete RBAC system with users, roles, and permissions

### Deliverables

- [x] Admin user CRUD
- [x] Role management
- [x] Permission management
- [x] Role-permission assignment
- [x] User-role assignment
- [x] Permission enforcement
- [x] Activity audit logging
- [x] Super Admin protection

### Technical Tasks

#### 7.1 Admin User Management
- [x] Create admin users list
- [x] Create admin user form
- [x] Edit admin user details
- [x] Delete admin users
- [x] Suspend/unsuspend users
- [x] Password reset functionality
- [x] Temporary password generation
- [x] Assign roles to users
- [x] Show last login date
- [x] Export admin users list

#### 7.2 Role Management
- [x] Create roles list
- [x] Create role form
- [x] Edit role details
- [x] Delete roles
- [x] Assign permissions to roles
- [x] View users with role
- [x] System roles (cannot delete)
- [x] Role ordering
- [x] Role description field

#### 7.3 Permission Management
- [x] Create permissions list
- [x] Manually seed all permissions
- [x] Group permissions by module
- [x] Assign permissions to roles
- [x] View permission details
- [x] Permission hierarchy

#### 7.4 Authorization Middleware
- [x] Create auth middleware (check logged in)
- [x] Create admin middleware (check admin role)
- [x] Create permission middleware (check specific permission)
- [x] Create role middleware (check role)
- [x] Apply middleware to routes
- [x] Middleware error handling

#### 7.5 Permission Checks in Code
- [x] Service-level permission checks
- [x] View-level permission checks (hide buttons)
- [x] Controller-level permission checks
- [x] Route-level permission checks
- [x] Permission denied error handling

#### 7.6 Activity Audit Logging
- [x] Log admin login/logout
- [x] Log failed login attempts
- [x] Log all CRUD operations
- [x] Log permission changes
- [x] Log order status changes
- [x] Store IP address
- [x] Store user agent
- [x] Activity log viewer
- [x] Activity log search/filter

#### 7.7 Super Admin Protection
- [x] Prevent deleting Super Admin role
- [x] Prevent modifying Super Admin permissions
- [x] Prevent deleting last Super Admin user
- [x] Prevent suspending own account
- [x] Log Super Admin actions
- [x] Super Admin override capabilities

#### 7.8 Session Security
- [x] Implement login throttling
- [x] Account lockout after failed attempts
- [x] Session regeneration after login
- [x] Session timeout enforcement
- [x] Secure cookie settings
- [x] Multiple concurrent sessions management

### Testing

```bash
# RBAC tests
./vendor/bin/phpunit tests/Integration/AuthorizationTest.php

# Permission enforcement tests
./vendor/bin/phpunit tests/Integration/PermissionTest.php

# Activity logging tests
./vendor/bin/phpunit tests/Integration/ActivityLoggingTest.php
```

### Acceptance Criteria

- [x] Users can be created and assigned roles
- [x] Permissions enforced at all levels
- [x] Super Admin protected
- [x] Activity logs created for all actions
- [x] Permission denied shows appropriate message
- [x] Session timeout works
- [x] Login throttling prevents brute force
- [x] All RBAC tests pass > 90% coverage

---

## Phase 8: Security & Performance

**Duration:** 7-10 days  
**Team:** Backend Dev + DevOps  
**Goals:** Implement security hardening and performance optimization

### Deliverables

- [x] Security headers implemented
- [x] Input validation & sanitization
- [x] SQL injection prevention
- [x] XSS protection
- [x] CSRF token validation
- [x] Password encryption
- [x] API rate limiting
- [x] Caching layer
- [x] Performance optimization
- [x] Security audit passed

### Technical Tasks

#### 8.1 Security Headers
- [x] Add X-Frame-Options header
- [x] Add X-Content-Type-Options header
- [x] Add X-XSS-Protection header
- [x] Add Content-Security-Policy header
- [x] Add Referrer-Policy header
- [x] Add Strict-Transport-Security (HSTS)
- [x] Add Permissions-Policy header

#### 8.2 Input Validation
- [x] Create validation rules for all forms
- [x] Implement custom validators
- [x] Create error message translations
- [x] Validate all API inputs
- [x] Whitelist allowed values
- [x] Reject suspicious input
- [x] Sanitize user input

#### 8.3 Output Escaping
- [x] Escape all HTML context output
- [x] Escape all URL context output
- [x] Escape all JavaScript context output
- [x] Escape all attribute context output
- [x] Create escaping helpers
- [x] Apply escaping in all views

#### 8.4 SQL Injection Prevention
- [x] Use PDO prepared statements everywhere
- [x] Never concatenate SQL strings
- [x] Create QueryBuilder with parameterized queries
- [x] Add SQL injection test cases
- [x] Audit all database queries

#### 8.5 CSRF Protection
- [x] Generate CSRF token for each session
- [x] Include token in all forms
- [x] Verify token on POST/PUT/DELETE
- [x] Token rotation after validation
- [x] Add X-CSRF-Token header support
- [x] Exclude safe methods from check

#### 8.6 Password Security
- [x] Use password_hash() with BCRYPT
- [x] Never store plaintext passwords
- [x] Implement password reset securely
- [x] Add token expiration to reset
- [x] Enforce minimum password length
- [x] Password strength validation

#### 8.7 File Upload Security
- [x] Validate file type (MIME + extension)
- [x] Limit file size
- [x] Rename uploaded files
- [x] Store outside web root
- [x] Prevent script execution
- [x] Scan files for malware (optional)
- [x] Clean EXIF data from images

#### 8.8 Session Security
- [x] Use secure cookie flags (httpOnly, Secure, SameSite)
- [x] Regenerate session ID after login
- [x] Implement session timeout
- [x] Bind session to IP/User-Agent
- [x] Clear sensitive data on logout
- [x] Prevent session fixation

#### 8.9 API Security
- [x] Implement rate limiting middleware
- [x] Add API authentication tokens
- [x] Add CORS protection
- [x] Validate API input
- [x] Log API errors
- [x] Implement API versioning

#### 8.10 Caching Layer
- [x] Implement file-based cache
- [x] Add Redis caching (optional)
- [x] Create cache service
- [x] Cache database queries
- [x] Cache page fragments
- [x] Implement cache invalidation
- [x] Monitor cache hit rate

#### 8.11 Performance Optimization
- [x] Minify CSS and JavaScript
- [x] Lazy load images
- [x] Use WebP format
- [x] Enable gzip compression
- [x] Add browser caching headers
- [x] Optimize database indexes
- [x] Query optimization (N+1 prevention)
- [x] Database connection pooling

#### 8.12 Security Audit
- [x] Run PHPStan (level 5)
- [x] Run PHPCS (PSR-12)
- [x] Run security scanning tool
- [x] Audit dependencies for CVEs
- [x] Manual security review
- [x] Penetration testing (optional)

### Testing

```bash
# Security tests
./vendor/bin/phpunit tests/Security/

# Performance tests
npm run audit

# Static analysis
./vendor/bin/phpstan analyze app/ --level 5

# Code standards
./vendor/bin/phpcs app/ --standard=PSR12
```

### Acceptance Criteria

- [x] All security headers present
- [x] PHPStan level 5 passes
- [x] PHPCS PSR-12 passes
- [x] No OWASP Top 10 vulnerabilities
- [x] All inputs validated
- [x] All outputs escaped
- [x] PageSpeed score > 90
- [x] No security warnings in audit

---

## Phase 9: SEO & Analytics

**Duration:** 5-7 days  
**Team:** Backend Dev + Frontend Dev  
**Goals:** Implement comprehensive SEO and analytics

### Deliverables

- [x] XML sitemap
- [x] Robots.txt
- [x] Schema.org markup
- [x] Google Analytics integration
- [x] Meta tags verification
- [x] Canonical URLs
- [x] Open Graph tags
- [x] Mobile-friendly optimization
- [x] Core Web Vitals optimization
- [x] SEO checklist verification

### Technical Tasks

#### 9.1 XML Sitemap
- [x] Create sitemap generator
- [x] Include all pages
- [x] Include all services
- [x] Include all projects
- [x] Include all products
- [x] Set priority levels
- [x] Set change frequency
- [x] Add last modified date
- [x] Compress sitemap (gzip)
- [x] Submit to search engines

#### 9.2 Robots.txt
- [x] Create robots.txt file
- [x] Allow crawling of important pages
- [x] Disallow admin pages
- [x] Disallow private files
- [x] Specify sitemap location
- [x] Set crawl delay

#### 9.3 Schema.org Markup (JSON-LD)
- [x] Organization schema
- [x] Service schema for each service
- [x] Product schema for each product
- [x] BreadcrumbList schema
- [x] LocalBusiness schema
- [x] FAQPage schema
- [x] ContactPoint schema
- [x] Schema validation

#### 9.4 Google Analytics
- [x] Add tracking code
- [x] Create GA property
- [x] Configure goal tracking
- [x] Track form submissions
- [x] Track add to cart events
- [x] Track checkout steps
- [x] Track order completions
- [x] Setup conversion funnels
- [x] View GA dashboard in admin

#### 9.5 Meta Tags Verification
- [x] Verify unique titles on all pages
- [x] Verify unique descriptions
- [x] Check title length (50-60 chars)
- [x] Check description length (150-160 chars)
- [x] Verify H1 presence
- [x] Verify H2/H3 hierarchy
- [x] Check keyword usage
- [x] Create SEO audit report

#### 9.6 Open Graph Tags
- [x] Add og:title
- [x] Add og:description
- [x] Add og:image
- [x] Add og:type
- [x] Add og:url
- [x] Twitter card tags
- [x] Social media preview test

#### 9.7 Mobile Optimization
- [x] Verify responsive design
- [x] Test on mobile devices
- [x] Optimize touch targets
- [x] Verify font sizes readable
- [x] Test mobile navigation
- [x] Check viewport settings
- [x] Mobile-friendly test

#### 9.8 Core Web Vitals
- [x] Measure LCP (Largest Contentful Paint)
- [x] Measure FID (First Input Delay)
- [x] Measure CLS (Cumulative Layout Shift)
- [x] Optimize images for LCP
- [x] Reduce JavaScript for FID
- [x] Prevent layout shifts for CLS

#### 9.9 SEO Tools Integration
- [x] Google Search Console setup
- [x] Bing Webmaster Tools setup
- [x] Google My Business setup
- [x] Schema markup validator
- [x] Mobile-friendly test tool
- [x] PageSpeed Insights monitoring

#### 9.10 Content Optimization
- [x] Optimize keyword usage
- [x] Internal linking strategy
- [x] Create keyword map
- [x] Optimize image alt text
- [x] Add structured data
- [x] Content length optimization
- [x] Readability optimization

### Testing

```bash
# SEO validation
npm run audit

# Schema markup validation
# Use https://validator.schema.org/

# Lighthouse audit
npx lighthouse https://desnkygroup.com

# Mobile-friendly test
# Use Google Mobile-Friendly Test
```

### Acceptance Criteria

- [x] Sitemap.xml accessible and valid
- [x] Robots.txt configured correctly
- [x] Schema markup validates
- [x] All meta tags present and unique
- [x] Google Analytics tracking
- [x] PageSpeed score > 90
- [x] Mobile score > 90
- [x] Core Web Vitals optimized

---

## Phase 10: Testing & Refinement

**Duration:** 5-7 days  
**Team:** QA Specialist + All Developers  
**Goals:** Comprehensive testing, bug fixes, and refinement

### Deliverables

- [x] Unit tests (80%+ coverage)
- [x] Integration tests
- [x] End-to-end tests
- [x] Bug fixes
- [x] Performance tuning
- [x] Cross-browser testing
- [x] Mobile testing
- [x] Load testing

### Technical Tasks

#### 10.1 Unit Tests
- [x] Service layer tests
- [x] Repository tests
- [x] Helper function tests
- [x] Validator tests
- [x] Target 80%+ code coverage
- [x] Generate coverage report

#### 10.2 Integration Tests
- [x] Controller tests
- [x] API endpoint tests
- [x] Middleware tests
- [x] Database transaction tests
- [x] Authentication tests
- [x] Authorization tests
- [x] Email sending tests

#### 10.3 End-to-End Tests
- [x] Homepage flow
- [x] Service browsing flow
- [x] Product shopping flow
- [x] Contact form submission
- [x] Order checkout flow
- [x] Admin login and dashboard
- [x] Admin content creation

#### 10.4 Bug Fixes
- [x] Fix reported issues
- [x] Fix edge cases
- [x] Fix form validation issues
- [x] Fix display bugs
- [x] Fix mobile issues
- [x] Fix browser compatibility
- [x] Fix performance issues

#### 10.5 Performance Testing
- [x] Load testing (100 concurrent users)
- [x] Stress testing
- [x] Endurance testing
- [x] Spike testing
- [x] Database query optimization
- [x] Caching effectiveness
- [x] Generate performance report

#### 10.6 Cross-Browser Testing
- [x] Chrome latest
- [x] Firefox latest
- [x] Safari latest
- [x] Edge latest
- [x] Mobile Safari (iOS)
- [x] Chrome Mobile (Android)
- [x] Fix browser-specific issues

#### 10.7 Mobile Testing
- [x] iPhone (various sizes)
- [x] Android (various sizes)
- [x] Tablet landscape/portrait
- [x] Touch interactions
- [x] Mobile forms
- [x] Mobile navigation
- [x] Responsive images

#### 10.8 Accessibility Testing
- [x] Keyboard navigation
- [x] Screen reader compatibility
- [x] Color contrast
- [x] WCAG 2.1 compliance
- [x] Fix accessibility issues
- [x] Generate accessibility report

### Testing Commands

```bash
# Run all tests
./vendor/bin/phpunit

# Generate coverage report
./vendor/bin/phpunit --coverage-html coverage/

# Performance test
ab -n 1000 -c 100 https://localhost/

# Lighthouse audit
npx lighthouse https://desnkygroup.com --output-path=report.html

# Mobile testing
ngrok http 8000  # For mobile testing with tunnel
```

### Acceptance Criteria

- [x] All tests passing
- [x] Code coverage > 80%
- [x] No critical bugs
- [x] No security issues
- [x] No performance issues
- [x] Cross-browser compatible
- [x] Mobile responsive
- [x] Accessibility compliant

---

## Phase 11: Deployment & Launch

**Duration:** 3-5 days  
**Team:** DevOps + Backend Dev  
**Goals:** Deploy to production and monitor

### Deliverables

- [x] Production environment setup
- [x] Database migration to production
- [x] SSL certificate installation
- [x] Backup system configured
- [x] Monitoring enabled
- [x] Health checks configured
- [x] Documentation complete
- [x] Launch checklist verified

### Technical Tasks

#### 11.1 Production Environment
- [x] Provision server
- [x] Install PHP 8.1+
- [x] Install MySQL 8.0+
- [x] Install Apache/Nginx
- [x] Configure web server
- [x] Install SSL certificate
- [x] Configure DNS
- [x] Setup email service

#### 11.2 Database Migration
- [x] Create production database
- [x] Run migrations
- [x] Seed initial data (roles, permissions)
- [x] Create admin user
- [x] Verify data integrity
- [x] Test backup/restore

#### 11.3 Application Deployment
- [x] Clone repository
- [x] Install dependencies (composer)
- [x] Build frontend assets (npm)
- [x] Configure .env
- [x] Set file permissions
- [x] Generate application key
- [x] Clear caches
- [x] Run database migrations
- [x] Create storage directories

#### 11.4 SSL/TLS Setup
- [x] Install Let's Encrypt certificate
- [x] Configure auto-renewal
- [x] Update web server config
- [x] Test HTTPS
- [x] Redirect HTTP to HTTPS
- [x] Update sitemap URL
- [x] Update robots.txt

#### 11.5 Backup System
- [x] Configure daily backups
- [x] Database backup script
- [x] Files backup script
- [x] Backup to remote storage (S3)
- [x] Test restore process
- [x] Document backup procedure
- [x] Setup backup monitoring

#### 11.6 Monitoring & Logging
- [x] Enable PHP error logging
- [x] Setup application logs
- [x] Setup server monitoring
- [x] CPU usage monitoring
- [x] Memory usage monitoring
- [x] Disk usage monitoring
- [x] Uptime monitoring
- [x] Alert configuration

#### 11.7 Performance Tuning
- [x] Enable gzip compression
- [x] Configure browser caching
- [x] Enable CDN (optional)
- [x] Optimize database
- [x] Setup Redis caching
- [x] Monitor response times
- [x] Monitor database queries

#### 11.8 Domain & DNS
- [x] Point domain to server
- [x] Setup DNS records
- [x] Configure email (MX records)
- [x] Add SPF record
- [x] Add DKIM record
- [x] Test mail sending
- [x] Verify configuration

#### 11.9 Admin Accounts
- [x] Create Super Admin account
- [x] Verify login works
- [x] Test permissions
- [x] Configure notification emails
- [x] Document admin setup
- [x] Create admin guide

#### 11.10 Final Testing
- [x] Verify homepage loads
- [x] Verify all pages accessible
- [x] Verify contact form works
- [x] Verify shop functions
- [x] Verify admin login
- [x] Verify email notifications
- [x] Run Lighthouse audit
- [x] Run security scan

#### 11.11 Documentation
- [x] Create deployment guide
- [x] Create admin user guide
- [x] Create troubleshooting guide
- [x] Create backup/restore guide
- [x] Create monitoring guide
- [x] Document API endpoints
- [x] Create FAQ document

#### 11.12 Launch Checklist
- [x] Production server ready
- [x] SSL certificate installed
- [x] Database migrated
- [x] Admin account created
- [x] Email service working
- [x] Backups configured
- [x] Monitoring enabled
- [x] All tests passing
- [x] Performance optimized
- [x] Security hardened
- [x] Documentation complete
- [x] Team trained
- [x] Launch approved

### Deployment Commands

```bash
# SSH to production
ssh user@desnkygroup.com

# Clone repository
git clone https://github.com/desnkygroup/website.git
cd website

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci

# Build frontend assets
npm run build

# Configure environment
cp .env.example .env
# Edit .env with production settings

# Setup database
php scripts/migrate.php
php scripts/seed.php

# Set permissions
chmod -R 775 storage/
chmod -R 775 public/assets/

# Clear cache
php scripts/cache-clear.php

# Verify installation
php scripts/health-check.php
```

### Acceptance Criteria

- [x] Site loads without errors
- [x] All pages accessible
- [x] Forms work correctly
- [x] Emails send successfully
- [x] Admin dashboard accessible
- [x] SSL certificate valid
- [x] PageSpeed score > 90
- [x] Uptime monitoring active
- [x] Backups running
- [x] No critical issues

---

## Timeline Overview

```
Week 1-2:  Phase 1-2  (Foundation + Frontend Setup)
Week 3-4:  Phase 3    (Public Website)
Week 5-6:  Phase 4-5  (Admin Core + CMS)
Week 7-8:  Phase 6-7  (Ecommerce + User Management)
Week 9-10: Phase 8-9  (Security + SEO)
Week 11:   Phase 10   (Testing + Refinement)
Week 12:   Phase 11   (Deployment + Launch)

Total: 12 weeks for production-ready system
```

---

## Resource Allocation

### Team Assignment

| Role          | Phase 1-3 | Phase 4-7 | Phase 8-10 | Phase 11 |
|---------------|-----------|-----------|------------|----------|
| Backend Dev   | 100%      | 80%       | 60%        | 50%      |
| Frontend Dev  | 50%       | 50%       | 40%        | 20%      |
| QA Specialist | 20%       | 30%       | 100%       | 50%      |
| DevOps        | 20%       | 0%        | 20%        | 100%     |

---

## Risk Mitigation

### Identified Risks

1. **Scope Creep**
   - Mitigation: Fixed scope, change control process
   - Owner: Project Manager

2. **Technical Challenges**
   - Mitigation: Proof of concept for complex features
   - Owner: Lead Developer

3. **Performance Issues**
   - Mitigation: Performance testing in Phase 8
   - Owner: Backend Developer

4. **Security Vulnerabilities**
   - Mitigation: Security audit in Phase 8
   - Owner: Security Specialist

5. **Timeline Delays**
   - Mitigation: Daily standups, issue tracking
   - Owner: Project Manager

---

## Success Metrics (Post-Launch)

- ✓ Website accessible 99.5% of the time
- ✓ Average response time < 2 seconds
- ✓ PageSpeed score > 90
- ✓ Mobile score > 90
- ✓ 100+ organic visits per month (Month 1)
- ✓ 5+ leads per week from contact form
- ✓ 0 critical security issues
- ✓ Admin operations < 5 hours per week

---

**Document End**
