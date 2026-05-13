# Technical Architecture Design
## Desnky Global Resources Ltd - Website Rebuild

**Document Version:** 1.0  
**Date:** May 2, 2026  
**Status:** Architecture Planning Phase

---

## Table of Contents

1. [System Architecture Overview](#system-architecture-overview)
2. [Frontend Architecture](#frontend-architecture)
3. [Backend Architecture](#backend-architecture)
4. [Database Design](#database-design)
5. [API Design Standards](#api-design-standards)
6. [Authentication & Authorization](#authentication--authorization)
7. [Security Architecture](#security-architecture)
8. [Scalability & Performance](#scalability--performance)
9. [Caching Strategy](#caching-strategy)
10. [Queue & Asynchronous Processing](#queue--asynchronous-processing)
11. [Deployment & Infrastructure](#deployment--infrastructure)

---

## System Architecture Overview

### High-Level Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                        FRONTEND LAYER                       │
├─────────────────────────────────────────────────────────────┤
│  HTML5 | Tailwind CSS | JavaScript | Alpine.js | jQuery     │
├─────────────────────────────────────────────────────────────┤
│         CDN (Images, CSS, JS) | SEO Layer (Schema)          │
└─────────────────────────────────────────────────────────────┘
                            ↓ (HTTP/HTTPS)
┌─────────────────────────────────────────────────────────────┐
│                      API GATEWAY LAYER                      │
├─────────────────────────────────────────────────────────────┤
│   Routing | CSRF Protection | Rate Limiting | Compression   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                    BACKEND LAYER (PHP 8+)                   │
├─────────────────────────────────────────────────────────────┤
│  MVC Framework | Controllers | Services | Repositories      │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │              Controller Layer (Thin)                 │   │
│  │  - Route handling                                    │   │
│  │  - Request validation                                │   │
│  │  - Response formatting                               │   │
│  └──────────────────────────────────────────────────────┘   │
│                           ↓                                 │
│  ┌──────────────────────────────────────────────────────┐   │
│  │           Business Logic Layer (Services)            │   │
│  │  - ContactService         - CartService              │   │
│  │  - NewsletterService      - CheckoutService          │   │
│  │  - ProjectService         - OrderService             │   │
│  │  - ProductService         - AuthService              │   │
│  │  - PageService            - AdminUserService         │   │
│  └──────────────────────────────────────────────────────┘   │
│                           ↓                                 │
│  ┌──────────────────────────────────────────────────────┐   │
│  │        Data Access Layer (Repositories)              │   │
│  │  - Repository Pattern (PDO, Prepared Statements)     │   │
│  │  - Query building                                    │   │
│  │  - Result mapping                                    │   │
│  └──────────────────────────────────────────────────────┘   │
│                           ↓                                 │
│  ┌──────────────────────────────────────────────────────┐   │
│  │           Cache Layer (Redis Optional)               │   │
│  │  - Session cache                                     │   │
│  │  - Query result cache                                │   │
│  │  - Page fragment cache                               │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │           Queue Layer (Queuing System)               │   │
│  │  - Email notifications                               │   │
│  │  - Report generation                                 │   │
│  │  - Image processing                                  │   │
│  │  - Data exports                                      │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                  DATA LAYER (MySQL/InnoDB)                  │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────┐ ┌─────────────┐ ┌──────────┐ ┌────────────┐    │
│  │  Pages  │ │  Products   │ │  Orders  │ │   Users    │    │
│  ├─────────┤ ├─────────────┤ ├──────────┤ ├────────────┤    │
│  │ Content │ │ Inventory   │ │ Payments │ │ Inquiries  │    │
│  └─────────┘ └─────────────┘ └──────────┘ └────────────┘    │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐    │
│  │  Relationships: FK, Indexes, Constraints, Triggers  │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│              EXTERNAL SERVICES & INTEGRATIONS               │
├─────────────────────────────────────────────────────────────┤
│  Email: PHPMailer          SEO: Schema.org                  │
│  Analytics: Google Analytics    Monitoring: Error Tracking  │
│  CRM: Optional integration      Payment: Future gateways    │
└─────────────────────────────────────────────────────────────┘
```

### Architecture Principles

1. **Layered Architecture:** Clear separation between presentation, business logic, and data layers
2. **Dependency Injection:** Services injected into controllers; repositories into services
3. **Repository Pattern:** All database access through repositories
4. **Service Layer Pattern:** All business logic in services, not controllers
5. **PSR Standards:** PSR-4 autoloading, PSR-12 coding standards
6. **SOLID Principles:**
   - Single Responsibility: Each class has one reason to change
   - Open/Closed: Open for extension, closed for modification
   - Liskov Substitution: Implementations can replace interfaces
   - Interface Segregation: Clients depend on specific interfaces
   - Dependency Inversion: Depend on abstractions, not concretions

---

## Frontend Architecture

### Technology Stack

| Layer         | Technology             | Purpose                        |
|---------------|------------------------|--------------------------------|
| **Markup**    | HTML5                  | Semantic, accessible structure |
| **Styling**   | Tailwind CSS           | Utility-first CSS framework    |
| **Scripting** | Vanilla JS             | Core functionality             |
| **jQuery**    | DOM manipulation, AJAX | Legacy compatibility           |
| **Alpine.js** | Lightweight reactivity | UI interactions                |
| **Icons**     | Heroicons / Feather    | Icon system                    |
| **Forms**     | HTML5 + Validation     | Form handling                  |

### Frontend Structure

```
/public
├── index.php                 # Single entry point
├── .htaccess                 # Routing and security rules
│
├── /assets
│   ├── /css
│   │   ├── main.css         # Tailwind compiled CSS
│   │   └── vendor.css       # Third-party CSS
│   │
│   ├── /js
│   │   ├── main.js          # Application JS
│   │   ├── app.js           # Alpine.js app
│   │   ├── utils.js         # Utility functions
│   │   ├── ajax-handler.js  # AJAX wrapper
│   │   ├── form-validator.js # Client-side validation
│   │   └── vendors/
│   │       ├── jquery.min.js
│   │       ├── alpine.min.js
│   │       └── ...
│   │
│   ├── /images
│   │   ├── /hero            # Hero section images
│   │   ├── /services        # Service icons
│   │   ├── /projects        # Project images
│   │   ├── /clients         # Client logos
│   │   ├── logo.svg
│   │   ├── favicon.ico
│   │   └── ...
│   │
│   └── /fonts
│       ├── inter-regular.woff2
│       ├── inter-bold.woff2
│       └── ...
```

### Frontend Component Architecture

#### Global Layout
```
base.html (extends)
├── Header (Navigation, Logo, CTA)
├── Main Content Area (child view)
└── Footer (Links, Copyright, Newsletter)
```

#### Common Components
```
components/
├── navbar.html
├── footer.html
├── hero-section.html
├── card.html
├── button.html
├── form-group.html
├── alert.html
├── modal.html
├── pagination.html
├── breadcrumbs.html
├── loading-spinner.html
└── toast-notification.html
```

#### Page Structure Example
```
pages/
├── home.html
├── services/
│   ├── index.html (Services listing)
│   ├── engineering.html (Service detail)
│   ├── energy.html
│   ├── procurement.html
│   ├── hse.html
│   ├── ict.html
│   └── agro.html
├── projects/
│   ├── index.html (Gallery)
│   └── [slug].html (Project detail)
├── shop/
│   ├── index.html
│   ├── category.html
│   ├── product-detail.html
│   ├── cart.html
│   └── checkout.html
├── about.html
├── contact.html
└── admin/
    ├── login.html
    ├── dashboard.html
    └── ...
```

### Frontend Responsive Design

- **Mobile-First Approach:** Base styles for mobile, then breakpoints
- **Tailwind Breakpoints:**
  - sm: 640px
  - md: 768px
  - lg: 1024px
  - xl: 1280px
  - 2xl: 1536px

### Client-Side Interactions

#### AJAX Form Submission
```javascript
// User fills contact form
// JavaScript validates input
// AJAX POST to /api/contact (no page reload)
// Server validates and processes
// JSON response returned
// JavaScript shows success/error message
```

#### Dynamic UI Updates (Alpine.js)
```javascript
// Shopping cart updates without page reload
// Product filters
// Image gallery lightbox
// Modal dialogs
// Tab switching
// Dropdown menus
```

---

## Backend Architecture

### Technology Stack

| Layer               | Technology | Purpose |
|---------------------|-----------------------------|---------------------------|
| **Language**        | PHP 8.1+                    | Server-side logic         |
| **Framework**       | Custom MVC                  | Application structure     |
| **Database**        | MySQL 8.0+ (InnoDB)         | Persistent data           |
| **Database Access** | PDO                         | Database abstraction      |
| **Templating**      | PHP (views)                 | Markup generation         |
| **Caching**         | File-based (Redis optional) | Performance               |
| **Mail**            | PHPMailer                   | Email delivery            |
| **Environment**     | PHP-DotEnv                  | Configuration management  |

### Backend Project Structure

```
app/
├── /Controllers
│   ├── /Frontend
│   │   ├── HomeController.php
│   │   ├── ServiceController.php
│   │   ├── ProjectController.php
│   │   ├── ContactController.php
│   │   ├── ShopController.php
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   └── PageController.php
│   │
│   └── /Admin
│       ├── DashboardController.php
│       ├── PageController.php
│       ├── ServiceController.php
│       ├── ProjectController.php
│       ├── ProductController.php
│       ├── ProductCategoryController.php
│       ├── OrderController.php
│       ├── AdminUserController.php
│       ├── RoleController.php
│       ├── PermissionController.php
│       ├── SettingController.php
│       ├── MediaController.php
│       ├── AuthController.php
│       └── ReportController.php
│
├── /Services
│   ├── PageService.php
│   ├── ServiceService.php
│   ├── ProjectService.php
│   ├── ProductService.php
│   ├── CartService.php
│   ├── OrderService.php
│   ├── CheckoutService.php
│   ├── ContactService.php
│   ├── NewsletterService.php
│   ├── AdminUserService.php
│   ├── RoleService.php
│   ├── PermissionService.php
│   ├── AccessControlService.php
│   ├── MediaService.php
│   ├── EmailService.php
│   ├── SeoService.php
│   └── AnalyticsService.php
│
├── /Repositories
│   ├── PageRepository.php
│   ├── ServiceRepository.php
│   ├── ProjectRepository.php
│   ├── ProductRepository.php
│   ├── ProductCategoryRepository.php
│   ├── CartRepository.php
│   ├── OrderRepository.php
│   ├── OrderItemRepository.php
│   ├── ContactRepository.php
│   ├── NewsletterRepository.php
│   ├── AdminUserRepository.php
│   ├── RoleRepository.php
│   ├── PermissionRepository.php
│   ├── MediaRepository.php
│   ├── SettingRepository.php
│   └── ActivityLogRepository.php
│
├── /Models
│   ├── Page.php
│   ├── Service.php
│   ├── Project.php
│   ├── Product.php
│   ├── Cart.php
│   ├── Order.php
│   ├── Contact.php
│   ├── Newsletter.php
│   ├── AdminUser.php
│   ├── Role.php
│   ├── Permission.php
│   ├── Media.php
│   └── Setting.php
│
├── /Middleware
│   ├── AuthMiddleware.php
│   ├── AdminMiddleware.php
│   ├── CsrfMiddleware.php
│   ├── RoleMiddleware.php
│   ├── PermissionMiddleware.php
│   ├── SuperAdminMiddleware.php
│   ├── RateLimitMiddleware.php
│   └── LoggingMiddleware.php
│
├── /Views
│   ├── /frontend
│   │   ├── /layouts
│   │   │   ├── base.php
│   │   │   ├── minimal.php
│   │   │   └── no-sidebar.php
│   │   │
│   │   ├── /partials
│   │   │   ├── header.php
│   │   │   ├── footer.php
│   │   │   ├── navigation.php
│   │   │   └── sidebar.php
│   │   │
│   │   └── /pages
│   │       ├── home.php
│   │       ├── services/index.php
│   │       ├── services/show.php
│   │       ├── contact.php
│   │       ├── shop/index.php
│   │       ├── shop/product.php
│   │       ├── cart.php
│   │       └── checkout.php
│   │
│   └── /admin
│       ├── /layouts
│       │   ├── base.php
│       │   └── minimal.php
│       │
│       ├── /partials
│       │   ├── header.php
│       │   ├── sidebar.php
│       │   ├── footer.php
│       │   └── alerts.php
│       │
│       └── /pages
│           ├── dashboard.php
│           ├── pages/index.php
│           ├── pages/form.php
│           ├── products/index.php
│           ├── orders/index.php
│           ├── users/index.php
│           └── ...
│
├── /Helpers
│   ├── StringHelper.php
│   ├── ValidationHelper.php
│   ├── FormHelper.php
│   ├── HtmlHelper.php
│   └── UrlHelper.php
│
├── /Traits
│   ├── HasTimestamps.php
│   ├── HasSlug.php
│   └── HasMeta.php
│
├── /Exceptions
│   ├── ApplicationException.php
│   ├── ValidationException.php
│   ├── AuthorizationException.php
│   ├── NotFoundException.php
│   └── DatabaseException.php
│
└── /Config
    ├── AppConfig.php
    ├── DatabaseConfig.php
    └── MailConfig.php

config/
├── app.php
├── database.php
├── mail.php
└── constants.php

routes/
├── web.php          # Frontend routes
└── admin.php        # Admin routes

storage/
├── /logs
│   ├── app.log
│   ├── error.log
│   └── activity.log
│
├── /cache
│   ├── /queries
│   ├── /pages
│   └── /temp

public/
├── index.php        # Entry point

vendor/              # Composer dependencies

.env                 # Environment configuration

composer.json        # Dependencies
```

### Core MVC Flow

```
HTTP Request
    ↓
├─ Routing (.htaccess → /public/index.php)
│   ↓
├─ Route Matching (web.php or admin.php)
│   ↓
├─ Middleware Pipeline
│   ├─ CSRF verification
│   ├─ Authentication
│   ├─ Authorization
│   └─ Logging
│   ↓
├─ Controller Instantiation
│   ├─ Resolve dependencies via DI container
│   ├─ Inject services
│   └─ Call controller method
│   ↓
├─ Service Layer Execution
│   ├─ Validate input
│   ├─ Apply business logic
│   ├─ Interact with repositories
│   └─ Handle errors
│   ↓
├─ Repository Layer Execution
│   ├─ Validate queries
│   ├─ Execute PDO prepared statements
│   ├─ Map results to models
│   └─ Return data
│   ↓
├─ View Rendering
│   ├─ Load view template
│   ├─ Pass data to view
│   ├─ Include partials/layouts
│   └─ Buffer HTML output
│   ↓
├─ Response Formatting
│   ├─ Set HTTP headers
│   ├─ Set status code
│   └─ Output HTML/JSON
│   ↓
HTTP Response
```

---

## Database Design

### Core Database Schema

#### Authentication & Admin Tables

```sql
-- Admin users table
CREATE TABLE admin_users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30) NULL,
    password_hash VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) NULL,
    status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    last_login_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles table
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    is_system_role TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permissions table
CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    module VARCHAR(100) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_module (module),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin user roles mapping
CREATE TABLE admin_user_roles (
    admin_user_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (admin_user_id, role_id),
    FOREIGN KEY (admin_user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Role permissions mapping
CREATE TABLE role_permissions (
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Content Management Tables

```sql
-- Pages table
CREATE TABLE pages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    excerpt TEXT NULL,
    featured_image_id BIGINT UNSIGNED NULL,
    status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(255) NULL,
    meta_keywords VARCHAR(255) NULL,
    og_image_id BIGINT UNSIGNED NULL,
    canonical_url VARCHAR(255) NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (featured_image_id) REFERENCES media(id) ON DELETE SET NULL,
    FOREIGN KEY (og_image_id) REFERENCES media(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES admin_users(id),
    FOREIGN KEY (updated_by) REFERENCES admin_users(id),
    FULLTEXT INDEX ft_search (title, content),
    INDEX idx_slug (slug),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Services table
CREATE TABLE services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    content LONGTEXT NOT NULL,
    short_description VARCHAR(500) NULL,
    icon VARCHAR(100) NULL,
    featured_image_id BIGINT UNSIGNED NULL,
    price_range VARCHAR(100) NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(255) NULL,
    meta_keywords VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (featured_image_id) REFERENCES media(id),
    FULLTEXT INDEX ft_search (name, description),
    INDEX idx_slug (slug),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Projects table
CREATE TABLE projects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    content LONGTEXT NOT NULL,
    client_name VARCHAR(150) NULL,
    featured_image_id BIGINT UNSIGNED NULL,
    service_id BIGINT UNSIGNED NULL,
    project_date DATE NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(255) NULL,
    meta_keywords VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (featured_image_id) REFERENCES media(id),
    FOREIGN KEY (service_id) REFERENCES services(id),
    FULLTEXT INDEX ft_search (title, description),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Project images table
CREATE TABLE project_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    media_id BIGINT UNSIGNED NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Media library table
CREATE TABLE media (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    alt_text VARCHAR(500) NULL,
    title VARCHAR(255) NULL,
    description TEXT NULL,
    width INT NULL,
    height INT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id),
    INDEX idx_mime (mime_type),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Ecommerce Tables

```sql
-- Product categories table
CREATE TABLE product_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT NULL,
    featured_image_id BIGINT UNSIGNED NULL,
    parent_id BIGINT UNSIGNED NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (featured_image_id) REFERENCES media(id),
    FOREIGN KEY (parent_id) REFERENCES product_categories(id),
    INDEX idx_slug (slug),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products table
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    sku VARCHAR(100) NOT NULL UNIQUE,
    category_id BIGINT UNSIGNED NOT NULL,
    short_description VARCHAR(500) NULL,
    full_description LONGTEXT NULL,
    price DECIMAL(10, 2) NOT NULL,
    discount_price DECIMAL(10, 2) NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    featured_image_id BIGINT UNSIGNED NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('active', 'inactive', 'discontinued') NOT NULL DEFAULT 'active',
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(255) NULL,
    meta_keywords VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id),
    FOREIGN KEY (featured_image_id) REFERENCES media(id),
    FULLTEXT INDEX ft_search (name, short_description, full_description),
    INDEX idx_slug (slug),
    INDEX idx_sku (sku),
    INDEX idx_status (status),
    INDEX idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product images table
CREATE TABLE product_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    media_id BIGINT UNSIGNED NOT NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE,
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Carts table
CREATE TABLE carts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    customer_email VARCHAR(150) NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_session (session_id),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cart items table
CREATE TABLE cart_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders table
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(150) NOT NULL,
    customer_phone VARCHAR(30) NOT NULL,
    delivery_address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    order_notes TEXT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    shipping_cost DECIMAL(10, 2) NOT NULL DEFAULT 0,
    tax DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending',
    payment_method ENUM('pay_on_delivery', 'bank_transfer') NOT NULL,
    payment_status ENUM('unpaid', 'paid', 'refunded') NOT NULL DEFAULT 'unpaid',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY order_number (order_number),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_customer_email (customer_email),
    FULLTEXT INDEX ft_search (customer_name, customer_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order items table
CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments table
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(100) NOT NULL,
    reference VARCHAR(255) NULL,
    status ENUM('pending', 'completed', 'failed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Contact & Newsletter Tables

```sql
-- Contacts table
CREATE TABLE contacts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NULL,
    company_name VARCHAR(150) NULL,
    service_interested VARCHAR(150) NULL,
    message LONGTEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'closed', 'spam') NOT NULL DEFAULT 'new',
    assigned_to BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES admin_users(id),
    INDEX idx_status (status),
    INDEX idx_email (email),
    FULLTEXT INDEX ft_search (full_name, email, message)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Newsletter subscribers table
CREATE TABLE newsletter_subscribers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    name VARCHAR(150) NULL,
    status ENUM('subscribed', 'unsubscribed', 'bounced') NOT NULL DEFAULT 'subscribed',
    subscribed_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Logging & Activity Tables

```sql
-- Admin activity logs table
CREATE TABLE admin_activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_user_id BIGINT UNSIGNED NULL,
    action VARCHAR(150) NOT NULL,
    module VARCHAR(100) NOT NULL,
    record_id BIGINT UNSIGNED NULL,
    description TEXT NULL,
    old_values LONGTEXT NULL,
    new_values LONGTEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_user_id) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_action (action),
    INDEX idx_module (module),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(255) NOT NULL UNIQUE,
    setting_value LONGTEXT NOT NULL,
    group_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key),
    INDEX idx_group (group_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Database Relationships Diagram

```
admin_users
├── roles (many-to-many via admin_user_roles)
├── admin_activity_logs (one-to-many)
├── pages (created_by, updated_by)
└── media (created_by)

roles
├── admin_users (many-to-many via admin_user_roles)
└── permissions (many-to-many via role_permissions)

permissions
└── roles (many-to-many via role_permissions)

pages
├── media (featured_image_id, og_image_id)
└── admin_users (created_by, updated_by)

services
└── media (featured_image_id)

projects
├── media (featured_image_id)
├── services (one-to-many)
└── project_images (one-to-many)

products
├── product_categories (one-to-many)
├── media (featured_image_id)
├── product_images (one-to-many)
├── cart_items (one-to-many)
└── order_items (one-to-many)

carts
├── cart_items (one-to-many)
└── orders (one-to-many)

orders
├── order_items (one-to-many)
└── payments (one-to-many)

media
├── pages (featured_image_id, og_image_id)
├── projects (featured_image_id)
├── project_images (one-to-many)
├── products (featured_image_id)
├── product_images (one-to-many)
└── admin_users (created_by)
```

---

## API Design Standards

### RESTful Principles

All APIs follow REST conventions:

| Method | Operation       | Example                    |
|--------|-----------------|----------------------------|
| GET    | Read/Retrieve   | GET /api/products          |
| POST   | Create          | POST /api/products         |
| PUT    | Replace         | PUT /api/products/{id}     |
| PATCH  | Partial Update  | PATCH /api/products/{id}   |
| DELETE | Delete          | DELETE /api/products/{id}  |

### API Endpoints

#### Contact & Forms
```
POST /api/contact                     # Submit contact form
POST /api/newsletter/subscribe        # Subscribe to newsletter
POST /api/newsletter/unsubscribe      # Unsubscribe from newsletter
```

#### Shop & Products
```
GET /api/products                     # List products (paginated)
GET /api/products/{id}                # Get product details
GET /api/categories                   # List categories
GET /api/search?q=keyword             # Search products
POST /api/cart/add                    # Add to cart
POST /api/cart/update                 # Update cart items
POST /api/cart/remove                 # Remove from cart
GET /api/cart                         # View cart
POST /api/checkout                    # Process checkout
```

#### Admin APIs
```
# Pages
GET /admin/api/pages                  # List pages
POST /admin/api/pages                 # Create page
PUT /admin/api/pages/{id}             # Update page
DELETE /admin/api/pages/{id}          # Delete page

# Products
GET /admin/api/products               # List products
POST /admin/api/products              # Create product
PUT /admin/api/products/{id}          # Update product
DELETE /admin/api/products/{id}       # Delete product

# Orders
GET /admin/api/orders                 # List orders
PUT /admin/api/orders/{id}            # Update order
PUT /admin/api/orders/{id}/status     # Update order status

# Media
POST /admin/api/media/upload          # Upload media
DELETE /admin/api/media/{id}          # Delete media
```

### Response Format

#### Success Response (200-201)
```json
{
    "success": true,
    "message": "Operation successful",
    "data": {
        // Response data
    }
}
```

#### Paginated Response
```json
{
    "success": true,
    "data": [
        // Array of items
    ],
    "pagination": {
        "total": 100,
        "per_page": 20,
        "current_page": 1,
        "last_page": 5,
        "from": 1,
        "to": 20
    }
}
```

#### Error Response (4xx-5xx)
```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": ["Error message"]
    }
}
```

### HTTP Status Codes

| Code | Meaning              | Usage                      |
|------|----------------------|----------------------------|
| 200  | OK                   | Successful GET, PUT, PATCH |
| 201  | Created              | Successful POST            |
| 204  | No Content           | Successful DELETE          |
| 400  | Bad Request          | Validation error           |
| 401  | Unauthorized         | Authentication required    |
| 403  | Forbidden            | Authorization failed       |
| 404  | Not Found            | Resource not found         |
| 409  | Conflict             | Duplicate resource         |
| 422  | Unprocessable Entity | Validation error           |
| 429  | Too Many Requests    | Rate limit exceeded        |
| 500  | Server Error         | Internal server error      |

---

## Authentication & Authorization

### Authentication Mechanism

#### Admin Authentication
1. **Login Process**
   - Admin submits email and password
   - Server validates credentials via `password_verify()`
   - Server creates session with admin ID and roles
   - Session stored in PHP `$_SESSION` (can upgrade to database sessions)
   - Session cookie with httpOnly, Secure, SameSite flags

2. **Session Management**
   - Session timeout: 1 hour (configurable)
   - Automatic session regeneration after login
   - Multiple concurrent sessions: Allowed (configurable)
   - Remember-me: Optional (future feature)

3. **Logout**
   - Session destroyed
   - CSRF token invalidated
   - Redirect to login page

#### Customer Authentication (Optional - Phase 2)
- Registration form
- Email verification
- Secure password reset
- Session-based authentication

### Authorization Strategy

#### Role-Based Access Control (RBAC)

```php
// Middleware checks
// 1. Is user authenticated?
// 2. Does user have required role?
// 3. Does role have required permission?
// 4. Is user allowed to perform action on resource?

// Backend enforcement
$route->middleware(['auth', 'role:admin|editor'])
```

#### Permission Hierarchy

```
Super Admin (can manage everything)
└── Admin (can manage content/products/orders)
    ├── Editor (can manage content only)
    ├── Sales Manager (can manage products/orders)
    └── Support Staff (can view inquiries/respond)
```

### Password Security

- Hash: `password_hash()` with default algorithm (BCRYPT)
- Verification: `password_verify()`
- Salt: Automatically generated by BCRYPT
- Minimum length: 8 characters (enforce on frontend)
- Complexity: Optional (numbers, symbols encouraged)
- Password reset: Unique token with 30-minute expiration
- Password history: Track last 5 passwords (prevent reuse)

---

## Security Architecture

### Input Validation

#### Frontend Validation
- HTML5 form validation
- JavaScript validation (UX only)
- Regular expressions for formats

#### Backend Validation (CRITICAL)
- All input validated on server
- Whitelist approach (allow known good, reject rest)
- Type checking
- Range checking
- Format validation
- File upload validation

```php
// Example validation
$rules = [
    'name' => 'required|string|max:150',
    'email' => 'required|email|unique:contacts',
    'phone' => 'required|regex:/^[0-9\-\+\s()]+$/',
    'message' => 'required|string|min:10|max:5000'
];

validate($input, $rules);
```

### Output Escaping

#### Template Context Escaping
```php
// HTML context
echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8');

// URL context
echo urlencode($param);

// JavaScript context
echo json_encode($data);

// Attribute context
echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
```

### CSRF Protection

#### Implementation
- Generate unique CSRF token per form
- Store token in session
- Include token in form hidden field
- Verify token on POST/PUT/DELETE requests
- Regenerate token after validation

```php
// Middleware
public function verifyCsrfToken(Request $request) {
    if (!in_array($request->getMethod(), ['GET', 'HEAD', 'OPTIONS'])) {
        $token = $request->get('_token') ?? $request->header('X-CSRF-Token');
        if (!hash_equals(session('_token'), $token)) {
            throw new TokenMismatchException('CSRF token invalid');
        }
    }
}
```

### SQL Injection Prevention

- **Prepared Statements:** PDO with placeholders (mandatory)
- **No String Concatenation:** Never build SQL with string concatenation

```php
// WRONG ❌
$query = "SELECT * FROM users WHERE id = " . $id;

// CORRECT ✓
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
```

### Authentication Security

- **Password Hashing:** BCRYPT algorithm
- **Login Rate Limiting:** Max 5 attempts per 15 minutes per IP
- **Account Lockout:** After 5 failed attempts (30-minute lockout)
- **Secure Cookies:**
  - `httpOnly`: Not accessible via JavaScript
  - `Secure`: HTTPS only
  - `SameSite=Strict`: No cross-site cookies

### SSL/TLS

- **HTTPS Enforcement:** All pages over HTTPS
- **Certificate:** Let's Encrypt (free, auto-renewing)
- **HSTS Header:** Force HTTPS for future visits
- **SSL Version:** TLS 1.2+ only

```
Strict-Transport-Security: max-age=31536000; includeSubDomains
```

### Security Headers

```
X-Content-Type-Options: nosniff           # Prevent MIME type sniffing
X-Frame-Options: SAMEORIGIN               # Prevent clickjacking
X-XSS-Protection: 1; mode=block           # Enable XSS filter
Content-Security-Policy: default-src 'self' # Strict CSP
Referrer-Policy: strict-origin-when-cross-origin
```

### File Upload Security

- **Whitelist File Types:** Only allow jpg, png, gif, pdf, webp
- **File Size Limit:** Max 5MB per file
- **Filename Sanitization:** Rename uploaded files with hash
- **Storage:** Store outside web root if possible
- **MIME Type Check:** Verify actual MIME type, not just extension

```php
$allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
if (!in_array($mime, $allowed_mimes)) {
    throw new Exception('Invalid file type');
}
```

### API Security

- **Authentication:** Session-based or JWT tokens (future)
- **Rate Limiting:** Max 100 requests per minute per IP
- **CORS:** Only allow requests from own domain
- **API Versioning:** /api/v1/ endpoints for future compatibility

### Data Protection

- **Sensitive Data:** Never log passwords, emails, card numbers
- **Data Encryption:** Encrypt at-rest (database level)
- **Data Validation:** Encrypted communication over HTTPS
- **Backup Security:** Encrypt backups, secure storage
- **GDPR Compliance:** Data export, deletion, right to be forgotten

---

## Scalability & Performance

### Horizontal Scalability

#### Load Balancing
```
                  User Traffic
                        ↓
                 [Load Balancer]
                        ↓
    ┌────────────┬────────────┬────────────┐
    ↓            ↓            ↓            ↓
  [WEB1]      [WEB2]       [WEB3]       [WEB4]

                  (Stateless)
                      ↓
               [Shared Database]
                      ↓
               [Shared File Storage]
```

#### Session Sharing
- Implement database sessions (not file-based)
- Allows stateless web servers
- Any web server can handle any request

#### Shared Storage
- Store uploaded files on shared storage (NFS, S3-like)
- Database for session data
- Cache (Redis) for session data redundancy

### Vertical Scalability

- **PHP Memory:** Increase from 128MB to 256MB or 512MB
- **Database Buffer Pool:** 25-50% of system RAM
- **Database Query Cache:** Useful for repeated queries
- **Disk I/O:** Use SSD for database and file storage
- **CPU Cores:** Multi-threaded request processing

### Database Optimization

#### Indexing Strategy
```sql
-- Search queries
FULLTEXT INDEX on (name, description)
BTREE INDEX on (slug, id)

-- Filtering
INDEX on (status, created_at)
INDEX on (category_id)

-- Sorting
INDEX on (sort_order, created_at DESC)

-- Joins
INDEX on (foreign_key_id)
```

#### Query Optimization
- Use EXPLAIN to analyze queries
- Avoid N+1 queries (use JOIN instead of loops)
- Pagination for large result sets
- Archive old data (contacts, logs)
- Regular ANALYZE TABLE to update statistics

#### Connection Pooling
- Reuse database connections
- Max connections: 20-50 (depends on traffic)
- Connection timeout: 30 seconds idle

### Code-Level Performance

#### Lazy Loading
```php
// Load data only when needed
$user = getUser(1);
$posts = $user->posts(); // Load only when called
```

#### Caching Layers
```
Request
  ↓
Cache Layer 1: Page fragment cache (Redis)
  ↓ (miss)
Cache Layer 2: Database query cache (Redis)
  ↓ (miss)
Cache Layer 3: Database lookup
```

---

## Caching Strategy

### Multi-Level Caching

#### 1. Page-Level Caching (User-Facing)
- Cache entire page HTML in file or Redis
- TTL: 1 hour (or on content update)
- Skip for authenticated admin pages
- Invalidate on content update

```php
// Check cache
$cached = Cache::get('page:home');
if ($cached) {
    return $cached;
}

// Generate page
$page = generatePage();

// Store in cache
Cache::set('page:home', $page, 3600);

return $page;
```

#### 2. Fragment Caching
- Cache parts of pages (header, footer, navigation)
- TTL: 6 hours
- Invalidate when content changes

#### 3. Query Result Caching
- Cache database query results
- TTL: 30 minutes to 1 hour
- Invalidate on related data update

```php
$products = Cache::remember('products:all', 3600, function() {
    return ProductRepository::all();
});
```

#### 4. Configuration Caching
- Cache application settings
- TTL: 24 hours (or indefinite with manual clear)
- Clear on settings update

```php
$settings = Cache::get('site:settings');
if (!$settings) {
    $settings = SettingRepository::all();
    Cache::set('site:settings', $settings);
}
```

### Cache Invalidation Strategy

#### Time-Based Invalidation
- Set appropriate TTL for each cache type
- Balance between freshness and performance

#### Event-Based Invalidation
```php
// When product updated
Event::dispatch('product.updated', $product);

// Listener clears cache
Cache::forget('products:all');
Cache::forget('product:' . $product->id);
```

#### Manual Invalidation
```
Admin Dashboard → Settings → Clear Cache
Clears: page cache, query cache, configuration cache
```

---

## Queue & Asynchronous Processing

### Queue System Architecture

```
Task Generated (User Action)
        ↓
   [Queue System]
        ↓
   [Worker Process]
        ↓
   Execute Task
        ↓
   Task Complete (Log Result)
```

### Queued Tasks

#### Priority 1: Send Immediately (Email)
- Contact form submission confirmation
- Order confirmation
- Admin notifications

#### Priority 2: Send Within 1 Hour
- Newsletter campaigns
- Order updates
- System notifications

#### Priority 3: Process Daily
- Generate reports
- Export data
- Archive old logs
- Backup database

### Queue Implementation

#### Simple File-Based Queue (Initial)
```
/storage/queue/
├── pending/
│   ├── email_contact_12345.json
│   ├── email_order_67890.json
│   └── ...
├── processing/
├── completed/
└── failed/
```

#### Database-Based Queue (Scalable)
```sql
CREATE TABLE queue_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(100) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts INT DEFAULT 0,
    reserved_at TIMESTAMP NULL,
    available_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Optional: Third-Party Queue Service
- AWS SQS
- RabbitMQ (future)
- Redis Queue (future)

### Worker Process

```bash
# Run worker (process queued jobs)
php artisan queue:work

# Or as cron job (every minute)
* * * * * php /app/cli/process-queue.php
```

---

## Deployment & Infrastructure

### Server Requirements

#### Minimum Specification
- CPU: 2 cores (1 GHz+)
- RAM: 2GB
- Storage: 50GB SSD
- Bandwidth: Unlimited (or 5TB/month)
- PHP: 8.1+
- MySQL: 8.0+
- Web Server: Apache or Nginx

#### Recommended Specification
- CPU: 4 cores (2 GHz+)
- RAM: 4GB
- Storage: 100GB SSD (with backup)
- Bandwidth: Unlimited
- PHP: 8.2+
- MySQL: 8.0+
- Redis: 2GB (optional)

### Hosting Options

#### Shared Hosting (Budget)
- Hostinger, Bluehost, SiteGround
- One-click installers
- Automated backups
- Email hosting included
- Cost: $5-15/month

#### VPS Hosting (Recommended)
- Linode, DigitalOcean, Vultr, AWS Lightsail
- Full control
- Scalable resources
- Better performance
- Cost: $5-20/month

#### Managed WordPress Hosting (Alternative)
- Kinsta, WP Engine
- Optimized for WordPress (if we use it)
- Automatic updates
- Expert support
- Cost: $35-100/month

### Server Configuration

#### Apache Setup
```apache
<VirtualHost *:443>
    ServerName desnkygroup.com
    ServerAlias www.desnkygroup.com
    
    DocumentRoot /var/www/desnkygroup/public
    
    <Directory /var/www/desnkygroup/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^ index.php [QSA,L]
        </IfModule>
    </Directory>
    
    # Security headers
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
    
    # SSL certificate
    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/desnkygroup.com.crt
    SSLCertificateKeyFile /etc/ssl/private/desnkygroup.com.key
    
    # HTTPS redirect
    Redirect permanent / https://desnkygroup.com/
</VirtualHost>
```

#### Nginx Setup
```nginx
server {
    listen 443 ssl http2;
    server_name desnkygroup.com www.desnkygroup.com;
    
    root /var/www/desnkygroup/public;
    index index.php;
    
    ssl_certificate /etc/ssl/certs/desnkygroup.com.crt;
    ssl_certificate_key /etc/ssl/private/desnkygroup.com.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
    
    # HTTPS redirect
    server {
        listen 80;
        server_name desnkygroup.com www.desnkygroup.com;
        return 301 https://$server_name$request_uri;
    }
}
```

### Backup Strategy

#### Automated Backups
- **Frequency:** Daily at 2 AM (off-peak)
- **Full Database Backup:** Daily
- **Incremental:** Hourly (daily + hourly = full restore capability)
- **File Backup:** Daily (user uploads, configuration)
- **Retention:** 30 days

#### Backup Locations
1. **On-Site:** Local server storage
2. **Off-Site:** Cloud storage (AWS S3, Google Cloud Storage)
3. **Redundant:** Multiple backup copies

#### Backup Verification
- Weekly restore test
- Backup integrity check
- Document restoration procedure
- Time-to-restore goal: < 4 hours

### Monitoring & Alerts

#### Server Monitoring
- CPU usage > 80%
- Memory usage > 85%
- Disk usage > 90%
- Swap usage > 50%
- Process uptime tracking

#### Application Monitoring
- Page load time > 3 seconds
- Error rate > 1%
- Database connection pool exhausted
- Queue job failures
- API response time > 500ms

#### Uptime Monitoring
- Ping service every 5 minutes
- Check key pages (homepage, shop, contact)
- Alert on downtime > 5 minutes
- Uptime target: 99.5%

### Continuous Integration / Continuous Deployment (CI/CD)

#### Version Control
- Repository: GitHub, GitLab, or Gitea
- Branching: main (production), develop, feature branches
- Protected branches: Main branch protected, requires review

#### Automated Tests
- Unit tests (Services, Repositories)
- Integration tests (Database, APIs)
- E2E tests (User workflows)
- Code quality checks (PHPStan, PHPCodeSniffer)

#### Deployment Pipeline
```
Push to main branch
    ↓
Run automated tests
    ↓
Code quality checks
    ↓
Security scan
    ↓
Build deployment package
    ↓
Deploy to staging
    ↓
Run smoke tests
    ↓
Manual approval
    ↓
Deploy to production
    ↓
Verify deployment
    ↓
Monitor for errors
```

---

## Summary: Architecture Checklist

- [x] Layered architecture with clear separation of concerns
- [x] Service-oriented business logic
- [x] Repository pattern for data access
- [x] Dependency injection for loose coupling
- [x] RESTful API design
- [x] Comprehensive security measures
- [x] Multi-level caching strategy
- [x] Asynchronous job processing
- [x] Scalable infrastructure design
- [x] Automated backup and disaster recovery
- [x] CI/CD pipeline ready
- [x] Monitoring and alerting
- [x] Production-ready configuration

**Next Document:** Comprehensive Implementation Blueprint

---

**Document End**
