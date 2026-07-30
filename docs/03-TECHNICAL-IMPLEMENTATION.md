# Technical Implementation Guide
## Desnky Global Resources Ltd - Website Rebuild

**Document Version:** 1.0  
**Date:** May 2, 2026  
**Status:** Implementation Planning Phase

---

## Table of Contents

1. [Project Structure](#project-structure)
2. [Development Standards](#development-standards)
3. [Tools & Dependencies](#tools--dependencies)
4. [Environment Configuration](#environment-configuration)
5. [Development Workflow](#development-workflow)
6. [Build Process](#build-process)
7. [Testing Strategy](#testing-strategy)
8. [Documentation Standards](#documentation-standards)

---

## Project Structure

### Complete Directory Tree

```
desnkygroup/
│
├── .github/
│   ├── SKILL.md              # Project architecture guide
│   ├── BRIEF.md              # Technical brief
│   └── workflows/            # GitHub Actions CI/CD (optional)
│       └── deploy.yml
│
├── app/
│   ├── Controllers/
│   │   ├── Frontend/
│   │   │   ├── BaseController.php
│   │   │   ├── HomeController.php
│   │   │   ├── ServiceController.php
│   │   │   ├── ProjectController.php
│   │   │   ├── ContactController.php
│   │   │   ├── ShopController.php
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php
│   │   │   └── PageController.php
│   │   │
│   │   └── Admin/
│   │       ├── BaseAdminController.php
│   │       ├── AuthController.php
│   │       ├── DashboardController.php
│   │       ├── PageController.php
│   │       ├── ServiceController.php
│   │       ├── ProjectController.php
│   │       ├── ProductController.php
│   │       ├── ProductCategoryController.php
│   │       ├── OrderController.php
│   │       ├── AdminUserController.php
│   │       ├── RoleController.php
│   │       ├── PermissionController.php
│   │       ├── SettingController.php
│   │       ├── MediaController.php
│   │       ├── InquiryController.php
│   │       ├── NewsletterController.php
│   │       └── ReportController.php
│   │
│   ├── Services/
│   │   ├── BaseService.php
│   │   ├── PageService.php
│   │   ├── ServiceService.php
│   │   ├── ProjectService.php
│   │   ├── ProductService.php
│   │   ├── ProductCategoryService.php
│   │   ├── CartService.php
│   │   ├── OrderService.php
│   │   ├── CheckoutService.php
│   │   ├── ContactService.php
│   │   ├── NewsletterService.php
│   │   ├── AdminUserService.php
│   │   ├── RoleService.php
│   │   ├── PermissionService.php
│   │   ├── AccessControlService.php
│   │   ├── MediaService.php
│   │   ├── EmailService.php
│   │   ├── SeoService.php
│   │   ├── CacheService.php
│   │   ├── AnalyticsService.php
│   │   └── ReportService.php
│   │
│   ├── Repositories/
│   │   ├── BaseRepository.php
│   │   ├── PageRepository.php
│   │   ├── ServiceRepository.php
│   │   ├── ProjectRepository.php
│   │   ├── ProductRepository.php
│   │   ├── ProductCategoryRepository.php
│   │   ├── CartRepository.php
│   │   ├── OrderRepository.php
│   │   ├── OrderItemRepository.php
│   │   ├── ContactRepository.php
│   │   ├── NewsletterRepository.php
│   │   ├── AdminUserRepository.php
│   │   ├── RoleRepository.php
│   │   ├── PermissionRepository.php
│   │   ├── MediaRepository.php
│   │   ├── SettingRepository.php
│   │   └── ActivityLogRepository.php
│   │
│   ├── Models/
│   │   ├── BaseModel.php
│   │   ├── Page.php
│   │   ├── Service.php
│   │   ├── Project.php
│   │   ├── Product.php
│   │   ├── Cart.php
│   │   ├── Order.php
│   │   ├── Contact.php
│   │   ├── Newsletter.php
│   │   ├── AdminUser.php
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   ├── Media.php
│   │   └── Setting.php
│   │
│   ├── Middleware/
│   │   ├── MiddlewareInterface.php
│   │   ├── AuthMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   ├── CsrfMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   ├── PermissionMiddleware.php
│   │   ├── SuperAdminMiddleware.php
│   │   ├── RateLimitMiddleware.php
│   │   ├── LoggingMiddleware.php
│   │   ├── CorsMiddleware.php
│   │   └── CompressionMiddleware.php
│   │
│   ├── Views/
│   │   ├── frontend/
│   │   │   ├── layouts/
│   │   │   │   ├── base.php
│   │   │   │   ├── minimal.php
│   │   │   │   └── admin.php
│   │   │   │
│   │   │   ├── partials/
│   │   │   │   ├── header.php
│   │   │   │   ├── footer.php
│   │   │   │   ├── navigation.php
│   │   │   │   ├── sidebar.php
│   │   │   │   ├── alert.php
│   │   │   │   ├── breadcrumbs.php
│   │   │   │   └── pagination.php
│   │   │   │
│   │   │   └── pages/
│   │   │       ├── home.php
│   │   │       ├── services/
│   │   │       │   ├── index.php
│   │   │       │   └── show.php
│   │   │       ├── projects/
│   │   │       │   ├── index.php
│   │   │       │   └── show.php
│   │   │       ├── shop/
│   │   │       │   ├── index.php
│   │   │       │   ├── category.php
│   │   │       │   ├── product.php
│   │   │       │   ├── cart.php
│   │   │       │   └── checkout.php
│   │   │       ├── contact.php
│   │   │       ├── about.php
│   │   │       ├── 404.php
│   │   │       └── error.php
│   │   │
│   │   └── admin/
│   │       ├── layouts/
│   │       │   ├── base.php
│   │       │   └── minimal.php
│   │       │
│   │       ├── partials/
│   │       │   ├── header.php
│   │       │   ├── sidebar.php
│   │       │   ├── footer.php
│   │       │   ├── alerts.php
│   │       │   ├── nav.php
│   │       │   └── breadcrumbs.php
│   │       │
│   │       └── pages/
│   │           ├── login.php
│   │           ├── dashboard.php
│   │           ├── pages/
│   │           │   ├── index.php
│   │           │   ├── create.php
│   │           │   └── edit.php
│   │           ├── products/
│   │           │   ├── index.php
│   │           │   ├── create.php
│   │           │   ├── edit.php
│   │           │   └── form.php
│   │           ├── orders/
│   │           │   ├── index.php
│   │           │   └── show.php
│   │           ├── users/
│   │           │   ├── index.php
│   │           │   ├── create.php
│   │           │   └── edit.php
│   │           ├── roles/
│   │           │   ├── index.php
│   │           │   ├── create.php
│   │           │   └── edit.php
│   │           └── settings/
│   │               └── index.php
│   │
│   ├── Helpers/
│   │   ├── StringHelper.php
│   │   ├── ValidationHelper.php
│   │   ├── FormHelper.php
│   │   ├── HtmlHelper.php
│   │   ├── UrlHelper.php
│   │   ├── DateHelper.php
│   │   ├── CurrencyHelper.php
│   │   └── FileHelper.php
│   │
│   ├── Traits/
│   │   ├── HasTimestamps.php
│   │   ├── HasSlug.php
│   │   ├── HasMeta.php
│   │   ├── Filterable.php
│   │   └── Searchable.php
│   │
│   ├── Exceptions/
│   │   ├── ApplicationException.php
│   │   ├── ValidationException.php
│   │   ├── AuthorizationException.php
│   │   ├── NotFoundException.php
│   │   ├── DatabaseException.php
│   │   ├── TokenMismatchException.php
│   │   └── ThrottleRequestsException.php
│   │
│   ├── Validators/
│   │   ├── ContactValidator.php
│   │   ├── ProductValidator.php
│   │   ├── OrderValidator.php
│   │   ├── AdminUserValidator.php
│   │   └── PageValidator.php
│   │
│   ├── Config/
│   │   ├── AppConfig.php
│   │   ├── DatabaseConfig.php
│   │   ├── MailConfig.php
│   │   ├── CacheConfig.php
│   │   └── Constants.php
│   │
│   ├── Database/
│   │   ├── Connection.php        # PDO connection manager
│   │   ├── QueryBuilder.php      # SQL query builder
│   │   └── Migration.php         # Database versioning
│   │
│   └── Router/
│       ├── Router.php            # Main routing class
│       ├── Route.php             # Single route
│       └── RouteGroup.php        # Route grouping
│
├── config/
│   ├── app.php                   # Application configuration
│   ├── database.php              # Database configuration
│   ├── mail.php                  # Email configuration
│   ├── cache.php                 # Caching configuration
│   ├── security.php              # Security settings
│   └── constants.php             # Application constants
│
├── routes/
│   ├── web.php                   # Frontend routes
│   └── admin.php                 # Admin routes
│
├── database/
│   ├── migrations/
│   │   ├── 2026_05_01_000001_create_users_table.php
│   │   ├── 2026_05_01_000002_create_pages_table.php
│   │   ├── 2026_05_01_000003_create_products_table.php
│   │   └── ...
│   │
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── AdminUserSeeder.php
│   │   ├── RoleSeeder.php
│   │   ├── PermissionSeeder.php
│   │   └── ServiceSeeder.php
│   │
│   └── schema.sql                # Database schema dump
│
├── storage/
│   ├── logs/
│   │   ├── app.log
│   │   ├── error.log
│   │   └── activity.log
│   │
│   ├── cache/
│   │   ├── queries/
│   │   ├── pages/
│   │   └── temp/
│   │
│   ├── uploads/
│   │   ├── images/
│   │   ├── products/
│   │   ├── projects/
│   │   └── temp/
│   │
│   └── backups/
│       └── ...
│
├── public/
│   ├── index.php                 # Single entry point
│   ├── .htaccess                 # Apache routing rules
│   │
│   ├── assets/
│   │   ├── css/
│   │   │   ├── main.css         # Tailwind compiled
│   │   │   ├── main.min.css     # Minified
│   │   │   ├── admin.css
│   │   │   └── admin.min.css
│   │   │
│   │   ├── js/
│   │   │   ├── main.js
│   │   │   ├── main.min.js
│   │   │   ├── app.js           # Alpine.js app
│   │   │   ├── ajax-handler.js
│   │   │   ├── form-validator.js
│   │   │   ├── admin.js
│   │   │   ├── admin.min.js
│   │   │   └── vendors/
│   │   │       ├── jquery.min.js
│   │   │       ├── alpine.min.js
│   │   │       ├── chart.js
│   │   │       └── ...
│   │   │
│   │   ├── images/
│   │   │   ├── hero/
│   │   │   ├── services/
│   │   │   ├── projects/
│   │   │   ├── clients/
│   │   │   ├── logo.svg
│   │   │   ├── favicon.ico
│   │   │   └── ...
│   │   │
│   │   ├── fonts/
│   │   │   ├── inter-regular.woff2
│   │   │   ├── inter-bold.woff2
│   │   │   └── ...
│   │   │
│   │   └── dist/
│   │       └── (compiled assets)
│   │
│   ├── sitemap.xml               # Auto-generated
│   ├── robots.txt                # SEO robots file
│   └── security.txt              # Security policy
│
├── tests/
│   ├── Unit/
│   │   ├── Services/
│   │   │   ├── ProductServiceTest.php
│   │   │   ├── OrderServiceTest.php
│   │   │   └── ...
│   │   │
│   │   └── Repositories/
│   │       ├── ProductRepositoryTest.php
│   │       └── ...
│   │
│   ├── Integration/
│   │   ├── Controllers/
│   │   │   ├── ContactControllerTest.php
│   │   │   ├── CheckoutControllerTest.php
│   │   │   └── ...
│   │   │
│   │   └── Middleware/
│   │       ├── CsrfMiddlewareTest.php
│   │       └── ...
│   │
│   └── Feature/
│       ├── HomePageTest.php
│       ├── ShopPageTest.php
│       ├── CheckoutFlowTest.php
│       └── ...
│
├── docs/
│   ├── 01-PRODUCT-STRATEGY.md
│   ├── 02-ARCHITECTURE-DESIGN.md
│   ├── 03-TECHNICAL-IMPLEMENTATION.md (this file)
│   ├── 04-DEVELOPMENT-PHASES.md
│   ├── 05-COPILOT-PROMPTS.md
│   ├── API-DOCUMENTATION.md
│   ├── DEPLOYMENT-GUIDE.md
│   ├── TROUBLESHOOTING.md
│   └── CHANGELOG.md
│
├── scripts/
│   ├── setup.sh                  # Initial setup script
│   ├── deploy.sh                 # Deployment script
│   ├── migrate.php               # Database migration runner
│   ├── seed.php                  # Database seeder runner
│   ├── backup.sh                 # Backup script
│   ├── cache-clear.php           # Clear application cache
│   └── queue-process.php         # Process queue jobs
│
├── .env.example                  # Environment template
├── .env.test                     # Testing environment
├── .htaccess                     # Root .htaccess (redirects to public/)
├── .gitignore                    # Git ignore file
├── .github/                      # GitHub configuration
├── composer.json                 # PHP dependencies
├── composer.lock                 # Locked dependencies
├── phpunit.xml                   # Testing configuration
├── tailwind.config.js            # Tailwind CSS configuration
├── webpack.mix.js                # Asset compilation (optional)
├── package.json                  # Node dependencies (for Tailwind)
├── package-lock.json             # Locked Node dependencies
├── docker-compose.yml            # Docker setup (optional)
├── Dockerfile                    # Docker image (optional)
├── CONTRIBUTING.md               # Contribution guidelines
├── LICENSE                       # License file
└── README.md                     # Project readme
```

---

## Development Standards

### Code Style & Conventions

#### PSR-4 Autoloading
```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Config\\": "config/",
            "Routes\\": "routes/",
            "Database\\": "database/",
            "Tests\\": "tests/"
        },
        "files": [
            "config/constants.php"
        ]
    }
}
```

#### PSR-12 Coding Standards

**Classes:**
```php
// Class declaration
final class MyClass extends ParentClass implements InterfaceOne, InterfaceTwo
{
    // Constants
    public const CONSTANT_NAME = 'value';
    
    // Properties
    public int $publicProperty;
    private string $privateProperty;
    
    // Constructor
    public function __construct(int $id)
    {
        $this->id = $id;
    }
    
    // Methods
    public function publicMethod(): void
    {
        // 4 space indentation
    }
    
    private function privateMethod(): string
    {
        return 'result';
    }
}
```

**Methods:**
```php
// Method signature
public function updateProduct(
    int $productId,
    string $name,
    string $description,
    float $price
): Product {
    // Implementation
}
```

**Variable Naming:**
```php
// Constants: UPPER_SNAKE_CASE
const DATABASE_CONNECTION = 'mysql';

// Properties/variables: camelCase
private $productName;
private $itemCount;

// Constants in classes: UPPER_CASE
class MyClass {
    public const MAX_ATTEMPTS = 5;
}
```

**Control Structures:**
```php
// if/elseif/else
if ($condition) {
    // Code
} elseif ($anotherCondition) {
    // Code
} else {
    // Code
}

// switch
switch ($value) {
    case 'option1':
        // Code
        break;
    
    case 'option2':
        // Code
        break;
    
    default:
        // Code
}

// for/foreach/while
foreach ($items as $key => $item) {
    // Code
}
```

### PHP Coding Best Practices

#### Type Declarations
```php
// Always use type declarations
public function getProduct(int $id): Product {
    return $this->repository->find($id);
}

// Use nullable types when appropriate
public function findByName(?string $name): ?Product {
    if ($name === null) {
        return null;
    }
    return $this->repository->findByName($name);
}

// Use union types (PHP 8+)
public function handle(Product|Service $entity): void {
    // Handle entity
}
```

#### Error Handling
```php
try {
    $product = $this->productRepository->find($productId);
    if (!$product) {
        throw new NotFoundException('Product not found');
    }
    // Process product
} catch (NotFoundException $e) {
    $this->logger->error('Product not found', ['id' => $productId]);
    throw $e;
} catch (Exception $e) {
    $this->logger->error('Unexpected error', ['error' => $e->getMessage()]);
    throw new ApplicationException('An unexpected error occurred');
}
```

#### String Handling
```php
// Use double quotes for interpolation
$message = "Welcome, {$user->getName()}!";

// Use single quotes for plain strings
$constant = 'DATABASE_CONNECTION';

// Use concatenation for complex strings
$query = 'SELECT * FROM ' . $table . ' WHERE ' . $condition;

// Use sprintf for formatted strings
$formatted = sprintf('Price: %s%.2f', $currency, $price);
```

### Frontend Code Standards

#### HTML Standards
```html
<!-- Use semantic HTML5 -->
<header>
    <nav>Navigation</nav>
</header>

<main>
    <article>
        <h1>Page Title</h1>
        <section>Content</section>
    </article>
</main>

<footer>Footer</footer>

<!-- Use proper indentation (2 spaces) -->
<div class="container">
  <div class="row">
    <div class="col">Content</div>
  </div>
</div>

<!-- Close all tags -->
<img src="image.jpg" alt="Description" />
```

#### CSS Standards
```css
/* Use utility-first CSS (Tailwind) */
<div class="bg-white p-4 rounded-lg shadow">
    <h2 class="text-2xl font-bold text-gray-900">Title</h2>
    <p class="text-gray-600 mt-2">Description</p>
</div>

/* Only use custom CSS when necessary */
.custom-component {
    /* Use meaningful class names */
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* Use CSS variables for theming */
:root {
    --primary-color: #1f2937;
    --accent-color: #f59e0b;
    --spacing-unit: 0.25rem;
}

.button {
    color: var(--primary-color);
    background: var(--accent-color);
}
```

#### JavaScript Standards
```javascript
// Use meaningful variable names
const maxRetries = 3;
const isUserAuthenticated = true;

// Use const by default, let if needed
const configuration = {};
let counter = 0;

// Use arrow functions for callbacks
items.forEach(item => {
    console.log(item);
});

// Use template literals
const message = `Hello, ${user.name}!`;

// Use async/await
async function fetchProducts() {
    try {
        const response = await fetch('/api/products');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Failed to fetch products:', error);
    }
}

// Use destructuring
const { name, email, phone } = customer;
const [first, second, ...rest] = array;
```

### File Naming Conventions

| File Type          | Example                                     | Convention              |
|--------------------|---------------------------------------------|-------------------------|
| Controller         | ProductController.php                       | PascalCase + Controller |
| Service            | ProductService.php                          | PascalCase + Service    |
| Repository         | ProductRepository.php                       | PascalCase + Repository |
| Model              | Product.php                                 | PascalCase              |
| Middleware         | AuthMiddleware.php                          | PascalCase + Middleware |
| Trait              | HasTimestamps.php                           | PascalCase              |
| Exception          | NotFoundException.php                       | PascalCase + Exception  |
| View               | products/index.php                          | snake_case + .php       |
| CSS                | main.css                                    | kebab-case.css          |
| JavaScript         | main.js                                     | kebab-case.js           |
| Database Migration | 2026_05_01_000001_create_products_table.php | timestamp_snake_case    |

### Comments & Documentation

#### Code Comments
```php
// Use comments for WHY, not WHAT
// Good: Explains business logic
// Cache products for 1 hour to reduce database load
$this->cache->set('products', $products, 3600);

// Bad: Explains obvious code
// Loop through products
foreach ($products as $product) {
    // ...
}
```

#### PHPDoc Comments
```php
/**
 * Create a new product.
 *
 * @param string $name The product name
 * @param float $price The product price
 * @return Product The created product
 * @throws ValidationException
 *
 * @example
 * $product = $service->create('Widget', 99.99);
 */
public function create(string $name, float $price): Product
{
    // Implementation
}
```

### Git Commit Standards

```
Format: <type>(<scope>): <subject>

Types:
  - feat:     New feature
  - fix:      Bug fix
  - docs:     Documentation
  - style:    Code style (formatting, missing semicolons, etc)
  - refactor: Code refactoring without feature changes
  - test:     Adding or updating tests
  - chore:    Build process, dependency updates

Examples:
  feat(products): Add product filtering by category
  fix(checkout): Fix cart total calculation
  docs(api): Add API endpoint documentation
  test(contact-form): Add contact form validation tests
```

---

## Tools & Dependencies

### Required Tools

#### Backend
| Tool         | Version | Purpose                |
|--------------|---------|------------------------|
| PHP          | 8.1+    | Server-side language   |
| Composer     | 2.0+    | PHP dependency manager |
| MySQL        | 8.0+    | Database               |
| Apache/Nginx | Latest  | Web server             |
| Git          | 2.0+    | Version control        |
| PHPUnit      | 9.0+    | Testing framework      |
| PHPStan      | 1.0+    | Static analysis        |
| PHPCS        | 3.0+    | Code sniffer           |

#### Frontend
| Tool         | Version | Purpose                    |
|--------------|---------|----------------------------|
| Node.js      | 16+     | JavaScript runtime         |
| npm          | 8+      | JavaScript package manager |
| Tailwind CSS | 3.0+    | CSS framework              |
| Webpack      | 5.0+    | Asset bundler (optional)   |

### PHP Dependencies (composer.json)

```json
{
    "name": "desnkygroup/website",
    "description": "Desnky Global Resources Ltd Website",
    "type": "project",
    "require": {
        "php": "^8.1",
        "vlucas/phpdotenv": "^5.5",
        "phpmailer/phpmailer": "^6.8",
        "predis/predis": "^2.0",
        "guzzlehttp/guzzle": "^7.5",
        "monolog/monolog": "^3.0",
        "symfony/var-dumper": "^6.2"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.5",
        "phpstan/phpstan": "^1.8",
        "squizlabs/php_codesniffer": "^3.7",
        "symfony/var-dumper": "^6.2"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Config\\": "config/",
            "Routes\\": "routes/",
            "Database\\": "database/",
            "Tests\\": "tests/"
        },
        "files": [
            "config/constants.php"
        ]
    }
}
```

### Node Dependencies (package.json)

```json
{
    "name": "desnkygroup-assets",
    "private": true,
    "scripts": {
        "dev": "tailwindcss -i ./resources/css/main.css -o ./public/assets/css/main.css --watch",
        "build": "tailwindcss -i ./resources/css/main.css -o ./public/assets/css/main.css --minify",
        "watch": "tailwindcss -i ./resources/css/main.css -o ./public/assets/css/main.css --watch"
    },
    "devDependencies": {
        "tailwindcss": "^3.3.0",
        "autoprefixer": "^10.4.14",
        "postcss": "^8.4.24"
    }
}
```

---

## Environment Configuration

### .env File Structure

```bash
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://desnkygroup.com
APP_NAME="Desnky Global Resources Ltd"
APP_TIMEZONE=Africa/Lagos

# Database
DB_HOST=localhost
DB_PORT=3306
DB_NAME=desnkygroup
DB_USER=desnky_user
DB_PASS=SecurePassword123
DB_CHARSET=utf8mb4

# Cache
CACHE_DRIVER=file
CACHE_TTL=3600
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_DB=0

# Email
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USER=user@mailtrap.io
MAIL_PASS=password
MAIL_FROM=noreply@desnkygroup.com
MAIL_FROM_NAME="Desnky Global Resources Ltd"

# Security
APP_KEY=base64:your-secret-key-here
SESSION_LIFETIME=3600
CSRF_TOKEN_NAME=_token

# Payment (Future)
PAYSTACK_KEY=pk_test_xxxxx
FLUTTERWAVE_KEY=FLWPUBK_TEST_xxxxx

# Analytics
GOOGLE_ANALYTICS_ID=GA-000000000-0

# Admin
ADMIN_EMAIL=admin@desnkygroup.com
ADMIN_PHONE=+234123456789

# Development
LOG_LEVEL=info
DEBUG_EMAIL=debug@desnkygroup.com
```

### Environment Files

```
.env.example         # Template (commit to repo)
.env                 # Local (DO NOT commit - add to .gitignore)
.env.test            # Testing environment
.env.staging         # Staging server
.env.production      # Production server (use secure methods)
```

### .gitignore

```
# Environment
.env
.env.local
.env.*.local

# Dependencies
vendor/
node_modules/
composer.lock
package-lock.json

# Compiled assets
public/assets/dist/
public/assets/css/main.css
public/assets/js/main.js

# Logs
storage/logs/*
!storage/logs/.gitkeep

# Cache
storage/cache/*
!storage/cache/.gitkeep

# Uploads
storage/uploads/*
!storage/uploads/.gitkeep

# Backups
storage/backups/*

# IDE
.vscode/
.idea/
*.sublime-workspace
*.swp
*.swo

# OS
.DS_Store
Thumbs.db

# Testing
coverage/
.phpunit.result.cache

# Build
build/
dist/
```

---

## Development Workflow

### Local Development Setup

#### Prerequisites
- PHP 8.1+ installed
- MySQL 8.0+ installed
- Composer installed
- Node.js and npm installed
- Git installed

#### Step 1: Clone Repository
```bash
git clone https://github.com/desnkygroup/website.git
cd website
```

#### Step 2: Install PHP Dependencies
```bash
composer install
```

#### Step 3: Install Node Dependencies
```bash
npm install
```

#### Step 4: Configure Environment
```bash
cp .env.example .env
# Edit .env with local settings
php -S localhost:8000 -t public public/index.php
```

#### Step 5: Setup Database
```bash
php scripts/migrate.php       # Run migrations
php scripts/seed.php          # Seed data
```

#### Step 6: Generate Application Key
```bash
php scripts/generate-key.php
```

#### Step 7: Build Frontend Assets
```bash
npm run dev      # Development mode with watch
npm run build    # Production build
```

### Development Server

```bash
# PHP built-in server
php -S localhost:8000 -t public public/index.php

# With Ngrok for mobile testing
ngrok http 8000

# Using Docker (if available)
docker-compose up
```

### Code Quality Tools

#### PHPStan (Static Analysis)
```bash
# Analyze code
./vendor/bin/phpstan analyze app/

# With level (1-8, default 0)
./vendor/bin/phpstan analyze app/ --level 5
```

#### PHPCS (Code Sniffer)
```bash
# Check code style
./vendor/bin/phpcs app/ --standard=PSR12

# Auto-fix style issues
./vendor/bin/phpcbf app/ --standard=PSR12
```

---

## Build Process

### Asset Compilation

#### Tailwind CSS
```bash
# Development mode (with watch)
npm run dev

# Production build (minified)
npm run build

# One-time build
npx tailwindcss -i ./resources/css/main.css -o ./public/assets/css/main.css --minify
```

#### JavaScript
```bash
# Currently no bundler (using vanilla JS)
# Files loaded directly in views
# Optional: Add webpack for bundling in future
```

#### Manual Asset Optimization
```bash
# Minify CSS manually
gzip public/assets/css/main.css -9 > public/assets/css/main.css.gz

# Minify JavaScript manually
npm install -g terser
terser public/assets/js/main.js -o public/assets/js/main.min.js
```

---

## Blog / Editorial CMS Technical Plan

### Application Structure

The blog module follows the existing MVC, service, and repository architecture:

```text
app/Controllers/Frontend/BlogController.php
app/Controllers/Admin/PostController.php
app/Controllers/Admin/PostCategoryController.php
app/Controllers/Admin/PostTagController.php
app/Services/PostService.php
app/Services/PostCategoryService.php
app/Services/PostTagService.php
app/Services/TipTapSanitizer.php
app/Repositories/PostRepository.php
app/Repositories/PostCategoryRepository.php
app/Repositories/PostTagRepository.php
app/Views/frontend/pages/blog/
app/Views/admin/posts/
app/Views/admin/post-categories/
app/Views/admin/post-tags/
```

### TipTap Editor Rules

- TipTap JSON is stored in `posts.content_json` as the canonical editable document.
- Sanitized HTML is stored in `posts.content_html` for fast frontend rendering.
- Server-side sanitation must allow only approved nodes and marks such as paragraphs, headings, lists, blockquotes, links, images, tables, code blocks, bold, italic, underline, and text alignment.
- Inline images must reference records in the centralized media library.
- Autosave should write drafts without changing the public published version until the editor explicitly publishes.

### Public Routes

```php
$router->get('/blog', 'Frontend\\BlogController@index');
$router->get('/blog/{slug}', 'Frontend\\BlogController@show');
$router->get('/blog/category/{slug}', 'Frontend\\BlogController@category');
$router->get('/blog/tag/{slug}', 'Frontend\\BlogController@tag');
$router->get('/blog/rss.xml', 'Frontend\\BlogController@rss');
```

### Admin Routes

```php
$router->resource('/admin/posts', 'Admin\\PostController');
$router->resource('/admin/post-categories', 'Admin\\PostCategoryController');
$router->resource('/admin/post-tags', 'Admin\\PostTagController');
$router->post('/admin/posts/{id}/preview', 'Admin\\PostController@preview');
$router->post('/admin/posts/{id}/publish', 'Admin\\PostController@publish');
$router->post('/admin/posts/{id}/schedule', 'Admin\\PostController@schedule');
$router->post('/admin/posts/{id}/archive', 'Admin\\PostController@archive');
$router->post('/admin/posts/{id}/restore', 'Admin\\PostController@restore');
```

### Scalability and Maintainability

- Public blog queries must paginate and use indexed `status`, `published_at`, `category_id`, and slug fields.
- Post archives must be cacheable and invalidated when posts, categories, tags, or media change.
- Search uses fulltext indexes on post title, excerpt, and sanitized HTML.
- Public queries must exclude drafts, private posts, archived posts, soft-deleted posts, and posts with future scheduled dates.
- Editorial services must be independently testable and keep controllers thin.

---

## Testing Strategy

### Testing Pyramid

```
            /\
           /  \ E2E Tests (10%)
          /    \
         /------\
        /        \ Integration Tests (30%)
       /          \
      /____________\
    Unit Tests (60%)
```

### Unit Tests (Services & Repositories)

```php
// tests/Unit/Services/ProductServiceTest.php
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase
{
    private $service;
    private $repository;
    
    protected function setUp(): void
    {
        $this->repository = $this->createMock(ProductRepository::class);
        $this->service = new ProductService($this->repository);
    }
    
    public function testCreateProduct(): void
    {
        $product = $this->service->create([
            'name' => 'Widget',
            'price' => 99.99
        ]);
        
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Widget', $product->name);
    }
}
```

### Integration Tests (Controllers & APIs)

```php
// tests/Integration/ContactFormTest.php
class ContactFormTest extends TestCase
{
    public function testContactFormSubmission(): void
    {
        $response = $this->post('/api/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'message' => 'Test message'
        ]);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas('contacts', [
            'email' => 'john@example.com'
        ]);
    }
}
```

### Test Running

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test file
./vendor/bin/phpunit tests/Unit/Services/ProductServiceTest.php

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage/

# Watch mode (requires phpunit-watch or similar)
phpunit-watch
```

### phpunit.xml

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/9.5/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         colors="true"
         verbose="true">
    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">app/</directory>
        </include>
        <exclude>
            <directory>app/Views</directory>
        </exclude>
        <report>
            <html outputDirectory="coverage"/>
        </report>
    </coverage>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

---

## Documentation Standards

### Code Documentation

#### PHPDoc Standards
```php
/**
 * Get a product by ID with relationships.
 *
 * @param int $productId The product ID to fetch
 * @param array $with Relationships to eager load ['images', 'category']
 * @return Product The product instance
 * @throws NotFoundException If product not found
 * @throws ValidationException If ID is invalid
 *
 * @example
 * $product = $service->getProduct(1, ['images', 'category']);
 * echo $product->name;
 */
public function getProduct(int $productId, array $with = []): Product
{
    // Implementation
}
```

### README Documentation

```markdown
# Desnky Global Resources Ltd - Website

## Overview
Modern, SEO-optimized corporate website...

## Tech Stack
- PHP 8.1+
- MySQL 8.0+
- Tailwind CSS
- Alpine.js

## Quick Start
1. Clone repo
2. `composer install`
3. `npm install`
4. Configure `.env`
5. Run migrations
6. `npm run dev`

## Project Structure
- `/app` - Application code
- `/public` - Web root
- `/config` - Configuration
- `/storage` - Logs, cache, uploads

## Testing
```bash
./vendor/bin/phpunit
```

## Deployment
See DEPLOYMENT.md

## Contributing
See CONTRIBUTING.md
```

---

## Summary: Implementation Checklist

- [x] Project structure defined
- [x] Development standards documented
- [x] Tools and dependencies specified
- [x] Environment configuration template provided
- [x] Development workflow outlined
- [x] Build process explained
- [x] Testing strategy established
- [x] Documentation standards set

**Next Document:** Phase-by-Phase Development Workflow

---

**Document End**
