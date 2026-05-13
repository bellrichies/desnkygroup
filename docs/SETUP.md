# Installation & Setup Guide
## Desnky Global Resources Ltd - Website Rebuild

---

## Prerequisites

Before starting, ensure you have:
- PHP 8.1+ installed
- MySQL 8.0+ installed
- Composer installed
- Git installed
- A local development server (Apache, Nginx, or built-in PHP server)

---

## Step 1: Clone/Setup Project

```bash
# Navigate to web root
cd /xampp/htdocs
# or cd to your web server directory

# If starting fresh:
# cd /path/to/desnkygroup
```

---

## Step 2: Install Dependencies

```bash
# Navigate to project directory
cd desnkygroup

# Install Composer dependencies
composer install
```

This will download and install:
- vlucas/phpdotenv - Environment configuration
- phpmailer/phpmailer - Email sending
- predis/predis - Redis client (for caching)
- monolog/monolog - Logging
- guzzlehttp/guzzle - HTTP client
- PHPUnit - Testing framework
- PHPStan - Static analysis
- PHP CodeSniffer - Code style checking

---

## Step 3: Configure Environment

```bash
# Copy environment template
cp .env.example .env

# Edit .env file with your settings
nano .env
# or use your preferred editor
```

### Important .env Variables

```env
# Application
APP_NAME=Desnky Global Resources
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=desnkygroup
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE=false
```

---

## Step 4: Create Database

```bash
# Using MySQL CLI
mysql -u root -p
# Enter your password if set

# In MySQL console:
CREATE DATABASE IF NOT EXISTS `desnkygroup` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

Or use a database manager like phpMyAdmin.

---

## Step 5: Run Migrations

```bash
# Run all pending migrations
php scripts/migrate.php

# Expected output:
# ✓ Executed: CreateAdminUsersTable
# ✓ Executed: CreatePagesTable
# ✓ Executed: CreateProductsTable
# ✓ Executed: CreateContactsTable
# ✓ Executed: CreateOrdersTable
```

### Migration Commands

```bash
# Run all pending migrations
php scripts/migrate.php

# Rollback last batch
php scripts/migrate.php --rollback

# Rollback all migrations
php scripts/migrate.php --refresh
```

---

## Step 6: Verify Installation

### Option 1: Using Built-in PHP Server

```bash
# Start development server
php -S localhost:8000 -t public/

# Open in browser: http://localhost:8000
```

### Option 2: Using Apache/Nginx

Configure your web server to point to the `public/` directory as the document root.

**Apache example:**
```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /xampp/htdocs/desnkygroup/public
    
    <Directory /xampp/htdocs/desnkygroup/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Option 3: Create .htaccess (for Apache)

Create `/public/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Remove index.php from URLs
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?/$1 [L]
</IfModule>
```

---

## Step 7: Run Tests

```bash
# Run all tests
composer test

# Run with coverage report
composer test-coverage

# Run specific test file
vendor/bin/phpunit tests/Unit/RouterTest.php
```

Expected output:
```
OK (9 tests, 15 assertions)
```

---

## Step 8: Check Code Quality

```bash
# PSR-12 compliance check
composer lint

# Static analysis (PHPStan level 5)
composer analysis

# Fix code style automatically
composer lint-fix
```

---

## Troubleshooting

### Database Connection Error

```
Error: Database connection failed
```

**Solution:**
1. Verify MySQL is running
2. Check DB credentials in `.env`
3. Ensure database exists:
   ```bash
   mysql -u root -p -e "SHOW DATABASES;" | grep desnkygroup
   ```

### Missing vendor directory

```
Error: vendor/autoload.php not found
```

**Solution:**
```bash
composer install
```

### Permission Denied errors

```
Error: Permission denied when writing to storage/
```

**Solution:**
```bash
# Give write permissions to storage directory
chmod -R 755 storage/
chmod -R 755 public/assets/
```

### Port already in use

```
Error: The port 8000 is already in use
```

**Solution:**
```bash
# Use a different port
php -S localhost:8001 -t public/
```

---

## Directory Structure After Setup

```
desnkygroup/
├── app/
│   ├── Controllers/
│   ├── Services/
│   ├── Repositories/
│   ├── Models/
│   ├── Middleware/
│   ├── Views/
│   ├── Helpers/
│   ├── Exceptions/
│   ├── Database/
│   ├── Router.php
│   ├── Route.php
│   ├── Container.php
│   ├── Config.php
│   └── View.php
├── config/
│   ├── app.php
│   └── database.php
├── database/
│   └── migrations/
│       └── [migration files]
├── routes/
│   └── web.php
├── public/
│   ├── index.php
│   └── assets/
│       ├── css/
│       ├── js/
│       └── images/
├── storage/
│   ├── logs/
│   ├── cache/
│   └── uploads/
├── tests/
│   ├── Unit/
│   ├── Integration/
│   └── bootstrap.php
├── scripts/
│   └── migrate.php
├── vendor/
├── composer.json
├── .env
├── .gitignore
└── phpunit.xml
```

---

## Database Schema

### Tables Created

1. **admin_users** - Administrator accounts
   - id, full_name, email, password_hash, role, is_active, last_login_at, created_at, updated_at

2. **pages** - CMS pages
   - id, title, slug, content, excerpt, meta_title, meta_description, featured_image, is_published, created_by, created_at, updated_at

3. **products** - Ecommerce products
   - id, name, slug, description, price, cost_price, quantity_in_stock, sku, is_active, category_id, created_by, created_at, updated_at

4. **contacts** - Contact form submissions
   - id, full_name, email, phone, subject, message, status, assigned_to, created_at, updated_at

5. **orders** - Ecommerce orders
   - id, order_number, customer_email, customer_name, shipping_address, total, payment_status, order_status, created_at, updated_at

---

## Development Workflow

### Creating a New Controller

```bash
# Controllers should extend BaseController
# Location: app/Controllers/[Domain]/YourController.php

<?php
namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        return $this->view('frontend/pages/home', [
            'title' => 'Home Page'
        ]);
    }
}
```

### Creating a New View

```bash
# Views are PHP files
# Location: app/Views/[domain]/[page].php

<div class="container">
    <h1><?php echo $this->escape($title); ?></h1>
    <p>Welcome to <?php echo \App\Config::get('app.name'); ?></p>
</div>
```

### Running Tests

```bash
# Run all tests
composer test

# Run specific test
vendor/bin/phpunit tests/Unit/RouterTest.php

# Watch mode (requires enqueue/fs-watcher)
vendor/bin/phpunit --testdox
```

### Checking Code Quality

```bash
# All checks
composer lint && composer analysis && composer test

# Just style
composer lint

# Auto-fix style
composer lint-fix

# Static analysis
composer analysis
```

---

## Useful Commands

```bash
# Start development server
php -S localhost:8000 -t public/

# Run migrations
php scripts/migrate.php

# Rollback migrations
php scripts/migrate.php --rollback

# Run tests
composer test

# Check code style
composer lint

# Auto-fix code style
composer lint-fix

# Static analysis
composer analysis

# Generate test coverage report
composer test-coverage
```

---

## Next Steps

1. **Create Controllers** - Build controllers for each route
2. **Create Views** - Build HTML templates
3. **Create Services** - Build business logic
4. **Create Repositories** - Build data access layer
5. **Run Tests** - Write and run tests for each component
6. **Deploy** - Deploy to production when ready

---

## Support

For issues or questions:
1. Check the documentation in `/docs/`
2. Review the planning documents
3. Check error logs in `/storage/logs/`
4. Run tests to verify setup

---

**Ready to develop? Start building Phase 2!**
