<?php
/**
 * Migration: Add Soft Delete (deleted_at) columns to all tables
 * Run after 2024_12_22_000000_add_foreign_keys_and_constraints.php
 * 
 * This migration:
 * 1. Adds deleted_at column to users table
 * 2. Adds deleted_at column to settings table
 * 3. Adds deleted_at column to sliders table
 * 4. Adds deleted_at column to pricing_packages table
 * 5. Adds deleted_at column to pricing_features table
 * 6. Adds deleted_at column to pricing_brands table
 * 7. Adds deleted_at column to gallery table
 * 8. Adds deleted_at column to testimonials table
 * 9. Adds deleted_at column to clients table
 * 10. Adds deleted_at column to login_attempts table
 */

return new class {
    public function up(PDO $db): void
    {
        // Enable foreign keys for SQLite
        $db->exec('PRAGMA foreign_keys = ON');

        $tables = [
            'users',
            'settings',
            'sliders',
            'pricing_packages',
            'pricing_features',
            'pricing_brands',
            'gallery',
            'testimonials',
            'clients',
            'login_attempts',
        ];

        foreach ($tables as $table) {
            // Check if deleted_at column already exists
            $columns = $db->query("PRAGMA table_info($table)")->fetchAll(PDO::FETCH_ASSOC);
            $hasDeletedAt = false;
            foreach ($columns as $col) {
                if ($col['name'] === 'deleted_at') {
                    $hasDeletedAt = true;
                    break;
                }
            }

            if (!$hasDeletedAt) {
                $db->exec("ALTER TABLE $table ADD COLUMN deleted_at TEXT");
                echo "Added deleted_at column to $table\n";
            } else {
                echo "deleted_at column already exists in $table, skipping\n";
            }
        }

        // Create indexes on deleted_at for soft delete queries
        $indexTables = [
            'users',
            'settings',
            'sliders',
            'pricing_packages',
            'pricing_features',
            'pricing_brands',
            'gallery',
            'testimonials',
            'clients',
            'login_attempts',
        ];

        foreach ($indexTables as $table) {
            $db->exec("CREATE INDEX IF NOT EXISTS idx_{$table}_deleted_at ON $table(deleted_at)");
        }

        echo "Migration 2025_06_22_000000_add_soft_deletes completed successfully.\n";
    }

    public function down(PDO $db): void
    {
        $db->exec('PRAGMA foreign_keys = ON');

        // Drop indexes
        $tables = [
            'users',
            'settings',
            'sliders',
            'pricing_packages',
            'pricing_features',
            'pricing_brands',
            'gallery',
            'testimonials',
            'clients',
            'login_attempts',
        ];

        foreach ($tables as $table) {
            $db->exec("DROP INDEX IF EXISTS idx_{$table}_deleted_at");
        }

        // Note: SQLite doesn't support DROP COLUMN directly
        // Would need to recreate tables - skipping for simplicity
        echo "Warning: This migration is not easily reversible due to SQLite DROP COLUMN limitations.\n";
        echo "Manual database restoration from backup recommended for rollback.\n";
    }
};