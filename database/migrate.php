<?php

require __DIR__ . '/../bootstrap/app.php';

$db = \App\Models\Database::getInstance();

// Create migrations tracking table
$db->exec("
    CREATE TABLE IF NOT EXISTS migrations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        migration TEXT NOT NULL UNIQUE,
        migrated_at TEXT NOT NULL DEFAULT (datetime('now'))
    )
");

$option = $argv[1] ?? '';

if ($option === '--fresh') {
    echo "Dropping all tables...\n";
    $migrations = glob(__DIR__ . '/migrations/*.php');
    rsort($migrations);
    foreach ($migrations as $file) {
        $migration = require $file;
        $migration->down($db);
        echo "  Dropped: " . basename($file) . "\n";
    }
    $db->exec("DELETE FROM migrations");
    echo "\n";
}

// Run pending migrations
$migrations = glob(__DIR__ . '/migrations/*.php');
sort($migrations);

$ran = $db->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

$pending = 0;
foreach ($migrations as $file) {
    $name = basename($file);
    if (in_array($name, $ran)) continue;

    $migration = require $file;
    $migration->up($db);

    $stmt = $db->prepare("INSERT INTO migrations (migration) VALUES (?)");
    $stmt->execute([$name]);

    echo "Migrated: $name\n";
    $pending++;
}

if ($pending === 0) {
    echo "Nothing to migrate.\n";
}

// Run seeders if --seed flag
if ($option === '--seed' || ($argv[2] ?? '') === '--seed') {
    echo "\nSeeding database...\n";
    require __DIR__ . '/seeds/DatabaseSeeder.php';
    $seeder = new DatabaseSeeder();
    $seeder->run($db);
    echo "Database seeded successfully!\n";
}

echo "\nDone.\n";
