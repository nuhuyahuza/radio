<?php
/**
 * Database Migration Runner
 * Runs all SQL migration files in the migrations directory
 */

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Database configuration
$host = $_ENV['DB_HOST'] ?? 'db';
$port = $_ENV['DB_PORT'] ?? '3306';
$dbname = $_ENV['DB_NAME'] ?? 'zaa_radio';
$username = $_ENV['DB_USER'] ?? 'zaa_radio';
$password = $_ENV['DB_PASSWORD'] ?? 'zaa_radio_password';

try {
    // Connect to database
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Connected to database successfully.\n";

    // Create migrations tracking table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_migration (migration)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Get list of migration files
    $migrationFiles = glob(__DIR__ . '/migrations/*.sql');
    sort($migrationFiles);

    if (empty($migrationFiles)) {
        echo "No migration files found.\n";
        exit(0);
    }

    // Get executed migrations
    $stmt = $pdo->query("SELECT migration FROM migrations ORDER BY batch, id");
    $executedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $batch = 1;
    if (!empty($executedMigrations)) {
        $stmt = $pdo->query("SELECT MAX(batch) as max_batch FROM migrations");
        $batch = $stmt->fetch()['max_batch'] + 1;
    }

    $executed = 0;

    foreach ($migrationFiles as $file) {
        $filename = basename($file);
        
        // Skip if already executed
        if (in_array($filename, $executedMigrations)) {
            echo "Skipping $filename (already executed)\n";
            continue;
        }

        echo "Executing $filename...\n";

        // Read and execute migration file
        $sql = file_get_contents($file);
        
        if ($sql === false) {
            echo "Error: Could not read file $filename\n";
            continue;
        }

        // Split SQL into individual statements
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            function($stmt) {
                return !empty($stmt) && !preg_match('/^\s*--/', $stmt);
            }
        );
        
        // If no statements found (file doesn't end with semicolon), treat entire content as one statement
        if (empty($statements)) {
            $statements = [trim($sql)];
        }

        // Execute each statement
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }

        // Record migration as executed
        $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->execute([$filename, $batch]);
        
        $executed++;
        echo "✓ $filename executed successfully\n";
    }

    if ($executed > 0) {
        echo "\nMigration completed successfully. $executed migration(s) executed.\n";
    } else {
        echo "\nNo new migrations to execute.\n";
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
