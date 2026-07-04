<?php

/**
 * Migration: Create pricing_brands table and add brand_id to pricing_packages
 * Run after 004_create_pricing.php
 */

return new class {
    public function up(PDO $db): void
    {
        // Enable foreign keys for SQLite
        $db->exec('PRAGMA foreign_keys = ON');
        
        // Create pricing_brands table
        // Use datetime('now') for SQLite compatibility; works on MySQL/PostgreSQL too
        // For MySQL/PostgreSQL, you can change to TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        $db->exec("
            CREATE TABLE IF NOT EXISTS pricing_brands (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                logo TEXT,
                is_active INTEGER DEFAULT 1,
                sort_order INTEGER DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");
        
        // Add brand_id column to pricing_packages
        $db->exec("
            ALTER TABLE pricing_packages ADD COLUMN brand_id INTEGER
        ");
        
        // Add foreign key constraint (SQLite requires recreating table for FK)
        // We'll handle FK in application layer for simplicity, but add index
        $db->exec("
            CREATE INDEX IF NOT EXISTS idx_pricing_packages_brand_id 
            ON pricing_packages(brand_id)
        ");
        
        echo "Migration 008_create_pricing_brands completed successfully.\n";
    }
    
    public function down(PDO $db): void
    {
        $db->exec('PRAGMA foreign_keys = ON');
        
        // Drop index
        $db->exec("DROP INDEX IF EXISTS idx_pricing_packages_brand_id");
        
        // Note: SQLite doesn't support DROP COLUMN directly
        // Would need to recreate table - skipping for simplicity
        
        $db->exec("DROP TABLE IF EXISTS pricing_brands");
        
        echo "Migration 008_create_pricing_brands rolled back.\n";
    }
};