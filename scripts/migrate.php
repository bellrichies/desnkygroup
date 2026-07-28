<?php

/**
 * Migration Runner
 * 
 * CLI script to run database migrations
 * Usage: php scripts/migrate.php [--rollback]
 */

// Setup paths
define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');

// Load Composer autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Load environment. CI and container deployments may inject variables directly
// and intentionally omit a local .env file.
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

$requiredEnvironment = ['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
$missingEnvironment = array_values(array_filter(
    $requiredEnvironment,
    static function (string $key): bool {
        $isDefined = array_key_exists($key, $_ENV)
            || array_key_exists($key, $_SERVER)
            || getenv($key) !== false;

        if (!$isDefined) {
            return true;
        }

        // An empty password is valid for some local MySQL installations. All
        // other connection values must contain a usable value.
        if ($key === 'DB_PASSWORD') {
            return false;
        }

        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        return trim((string) $value) === '';
    }
));

if ($missingEnvironment !== []) {
    fwrite(
        STDERR,
        'Error: Missing required database environment variables: '
        . implode(', ', $missingEnvironment)
        . ". Copy .env.example to .env for local use or inject these variables in the runtime environment.\n"
    );
    exit(1);
}

// Load configuration
\App\Config::load(BASE_PATH . '/config');

try {
    // Create database connection
    $config = \App\Config::get('database.connections.mysql');
    $connection = new \App\Database\Connection($config);

    // Create migrations table if it doesn't exist
    $connection->execute("
        CREATE TABLE IF NOT EXISTS `migrations` (
            `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `migration` VARCHAR(255) NOT NULL UNIQUE,
            `batch` INT NOT NULL,
            `executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

// Parse arguments
$rollback = in_array('--rollback', $argv);
$refresh = in_array('--refresh', $argv);
$test = in_array('--test', $argv);

    if ($refresh) {
        // Rollback all migrations
        $migrations = $connection->query("SELECT * FROM migrations ORDER BY batch DESC, id DESC");
        foreach ($migrations as $row) {
            rollbackMigration($connection, $row['migration']);
            $connection->delete("DELETE FROM migrations WHERE migration = ?", [$row['migration']]);
            echo "✓ Rolled back: {$row['migration']}\n";
        }
        echo "\nAll migrations rolled back.\n";
        echo "Re-running migrations...\n\n";
    }

    if ($rollback) {
        // Rollback last batch
        $lastBatch = $connection->queryOne("SELECT MAX(batch) as batch FROM migrations");
        if ($lastBatch && $lastBatch['batch']) {
            $migrations = $connection->query("
                SELECT * FROM migrations 
                WHERE batch = ? 
                ORDER BY id DESC
            ", [$lastBatch['batch']]);

            foreach ($migrations as $row) {
                rollbackMigration($connection, $row['migration']);
                $connection->delete("DELETE FROM migrations WHERE migration = ?", [$row['migration']]);
                echo "✓ Rolled back: {$row['migration']}\n";
            }
        }
        echo "\nMigrations rolled back successfully.\n";
        exit(0);
    }

    // Get all migration files
    $migrationsPath = BASE_PATH . '/database/migrations';
    $files = glob($migrationsPath . '/*.php');

    if (empty($files)) {
        echo "No migrations found.\n";
        exit(0);
    }

    if ($test) {
        foreach ($files as $file) {
            $className = getMigrationClassNameFromFile($file);
            if (!$className) {
                echo "Error: Could not find class name in " . basename($file) . "\n";
                exit(1);
            }

            require_once $file;
            $class = 'Database\\Migrations\\' . $className;

            if (!class_exists($class)) {
                echo "Error: Migration class {$class} could not be loaded.\n";
                exit(1);
            }
        }

        echo count($files) . " migration file(s) validated successfully.\n";
        exit(0);
    }

    // Find next batch number
    $lastBatch = $connection->queryOne("SELECT MAX(batch) as batch FROM migrations");
    $nextBatch = ($lastBatch && $lastBatch['batch']) ? $lastBatch['batch'] + 1 : 1;

    // Get already executed migrations
    $executed = $connection->query("SELECT migration FROM migrations");
    $executedNames = array_column($executed, 'migration');

    // Run pending migrations
    $pending = 0;
    foreach ($files as $file) {
        $className = getMigrationClassNameFromFile($file);
        $fileName = basename($file, '.php');
        
        if (!$className) {
            echo "✗ Error: Could not find class name in {$fileName}\n";
            continue;
        }
        
        if (in_array($className, $executedNames)) {
            continue;
        }

        // Load the migration file to declare the class
        if (!class_exists('Database\\Migrations\\' . $className)) {
            require_once $file;
        }

        // Resolve class name from file
        $namespace = 'Database\\Migrations\\';
        $class = $namespace . $className;

        try {
            $migration = new $class($connection);
            $migration->up();
            
            $connection->insert(
                "INSERT INTO migrations (migration, batch) VALUES (?, ?)",
                [$className, $nextBatch]
            );
            
            echo "✓ Executed: {$className}\n";
            $pending++;
        } catch (\Exception $e) {
            echo "✗ Error in {$className}: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    if ($pending === 0) {
        echo "No pending migrations.\n";
    } else {
        echo "\n{$pending} migration(s) completed successfully.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

/**
 * Rollback a migration
 * 
 * @param \App\Database\Connection $connection Database connection
 * @param string $migration Migration class name
 * @return void
 */
function rollbackMigration(\App\Database\Connection $connection, string $migration): void
{
    $migrationFile = findMigrationFile($migration);
    if ($migrationFile !== null) {
        require_once $migrationFile;
    }

    $namespace = 'Database\\Migrations\\';
    $class = $namespace . $migration;

    if (!class_exists($class)) {
        throw new \Exception("Migration class {$class} not found or could not be loaded");
    }

    $migrationInstance = new $class(
        new \App\Database\Connection(\App\Config::get('database.connections.mysql'))
    );
    $migrationInstance->down();
}

/**
 * Extract the migration class name from a migration file.
 *
 * @param string $file Migration file path.
 * @return string|null
 */
function getMigrationClassNameFromFile(string $file): ?string
{
    $content = file_get_contents($file);
    if ($content !== false && preg_match('/class\s+(\w+)\s+extends/', $content, $matches)) {
        return $matches[1];
    }

    return null;
}

/**
 * Find the file containing a migration class.
 *
 * @param string $migration Migration class basename.
 * @return string|null
 */
function findMigrationFile(string $migration): ?string
{
    foreach (glob(BASE_PATH . '/database/migrations/*.php') as $file) {
        if (getMigrationClassNameFromFile($file) === $migration) {
            return $file;
        }
    }

    return null;
}
