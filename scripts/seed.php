<?php

/**
 * Foundation seeder.
 *
 * Usage: php scripts/seed.php
 */

define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

\App\Config::load(BASE_PATH . '/config');

$connection = new \App\Database\Connection(\App\Config::get('database.connections.mysql'));
$hasher = new \App\Security\Hasher();

$roles = [
    ['Super Admin', 'super-admin', 'Full system access', 1],
    ['Admin', 'admin', 'Content and shop management access', 1],
    ['Editor', 'editor', 'Content management access', 1],
    ['Sales Manager', 'sales-manager', 'Products and orders access', 1],
    ['Support Staff', 'support-staff', 'Enquiry and customer support access', 1],
];

foreach ($roles as $role) {
    $connection->execute(
        "INSERT INTO roles (name, slug, description, is_system_role)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description)",
        $role
    );
}

$permissions = [
    'dashboard.view',
    'pages.view',
    'pages.create',
    'pages.edit',
    'pages.delete',
    'pages.publish',
    'services.view',
    'services.create',
    'services.edit',
    'services.delete',
    'services.publish',
    'projects.view',
    'projects.create',
    'projects.edit',
    'projects.delete',
    'projects.publish',
    'media.view',
    'media.upload',
    'media.edit',
    'media.delete',
    'products.view',
    'products.create',
    'products.edit',
    'products.delete',
    'products.publish',
    'orders.view',
    'orders.edit',
    'orders.update_status',
    'orders.export',
    'enquiries.view',
    'enquiries.reply',
    'enquiries.delete',
    'newsletter.view',
    'newsletter.export',
    'newsletter.delete',
    'settings.view',
    'settings.edit',
    'admins.view',
    'admins.create',
    'admins.edit',
    'admins.delete',
    'admins.assign_roles',
    'admins.reset_password',
    'roles.view',
    'roles.create',
    'roles.edit',
    'roles.delete',
    'roles.assign_permissions',
    'permissions.view',
    'permissions.assign',
    'activity_logs.view',
];

foreach ($permissions as $permission) {
    [$module] = explode('.', $permission);
    $name = ucwords(str_replace(['.', '_'], [' ', ' '], $permission));

    $connection->execute(
        "INSERT INTO permissions (name, slug, module)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE name = VALUES(name), module = VALUES(module)",
        [$name, $permission, $module]
    );
}

$superAdminRole = $connection->queryOne("SELECT id FROM roles WHERE slug = ?", ['super-admin']);
if ($superAdminRole !== null) {
    $connection->execute("
        INSERT IGNORE INTO role_permissions (role_id, permission_id)
        SELECT ?, id FROM permissions
    ", [$superAdminRole['id']]);
}

$settings = [
    ['site.name', 'Desnky Global Resources', 'string', 1],
    ['site.email', 'info@desnkygroup.com', 'string', 1],
    ['site.phone', '', 'string', 1],
];

foreach ($settings as $setting) {
    $connection->execute(
        "INSERT INTO site_settings (setting_key, setting_value, setting_type, is_public)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)",
        $setting
    );
}

$connection->execute(
    "INSERT INTO admin_users (full_name, email, password_hash, role, is_active)
     VALUES (?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE full_name = VALUES(full_name)",
    [
        'Super Admin',
        'admin@example.com',
        $hasher->make('password'),
        'super_admin',
        1,
    ]
);

$admin = $connection->queryOne("SELECT id FROM admin_users WHERE email = ?", ['admin@example.com']);
$legacyRole = $connection->queryOne("SELECT id FROM roles WHERE slug = ?", ['super-admin']);

if ($admin !== null && $legacyRole !== null) {
    $connection->execute(
        "INSERT IGNORE INTO admin_user_roles (admin_user_id, role_id) VALUES (?, ?)",
        [$admin['id'], $legacyRole['id']]
    );
}

echo "Foundation seed data loaded successfully." . PHP_EOL;
