<?php
/**
 * Migration: Add Foreign Keys and Constraints
 * Run after 008_create_pricing_brands.php
 * 
 * This migration:
 * 1. Adds user_id column to testimonials with FK to users
 * 2. Recreates pricing_packages with proper FK to pricing_brands
 * 3. Adds CHECK constraints for status columns (is_active, is_featured IN 0,1)
 * 4. Adds missing indexes on FK columns and frequently queried columns
 * 5. Fixes pricing_brands timestamps to use datetime('now') for SQLite compatibility
 */

return new class {
    public function up(PDO $db): void
    {
        // Enable foreign keys for SQLite
        $db->exec('PRAGMA foreign_keys = ON');

        // ============================================
        // 1. Fix testimonials - add user_id with FK to users
        // ============================================
        // SQLite doesn't support ALTER TABLE ADD COLUMN with FK, so we recreate the table
        $db->exec("
            CREATE TABLE IF NOT EXISTS testimonials_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                customer_name TEXT NOT NULL,
                content TEXT,
                screenshot TEXT,
                rating INTEGER DEFAULT 5,
                sort_order INTEGER NOT NULL DEFAULT 0,
                is_active INTEGER NOT NULL DEFAULT 1 CHECK (is_active IN (0,1)),
                user_id INTEGER,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now')),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            )
        ");

        // Copy data from old table
        $db->exec("
            INSERT INTO testimonials_new (id, customer_name, content, screenshot, rating, sort_order, is_active, created_at, updated_at)
            SELECT id, customer_name, content, screenshot, rating, sort_order, is_active, created_at, updated_at
            FROM testimonials
        ");

        // Drop old table and rename new one
        $db->exec("DROP TABLE testimonials");
        $db->exec("ALTER TABLE testimonials_new RENAME TO testimonials");

        // Create index on user_id
        $db->exec("CREATE INDEX IF NOT EXISTS idx_testimonials_user_id ON testimonials(user_id)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_testimonials_is_active ON testimonials(is_active)");

        // ============================================
        // 2. Fix pricing_packages - recreate with proper FK to pricing_brands
        // ============================================
        $db->exec("
            CREATE TABLE IF NOT EXISTS pricing_packages_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                brand_id INTEGER,
                name TEXT NOT NULL,
                camera_count INTEGER NOT NULL,
                price INTEGER NOT NULL,
                price_original INTEGER,
                description TEXT,
                whatsapp_message TEXT NOT NULL,
                is_featured INTEGER NOT NULL DEFAULT 0 CHECK (is_featured IN (0,1)),
                is_active INTEGER NOT NULL DEFAULT 1 CHECK (is_active IN (0,1)),
                sort_order INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now')),
                FOREIGN KEY (brand_id) REFERENCES pricing_brands(id) ON DELETE SET NULL
            )
        ");

        // Copy data from old table, setting invalid brand_ids to NULL
        $validBrands = $db->query("SELECT id FROM pricing_brands")->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($validBrands)) {
            $brandList = implode(',', $validBrands);
            $db->exec("
                INSERT INTO pricing_packages_new (id, brand_id, name, camera_count, price, price_original, description, whatsapp_message, is_featured, is_active, sort_order, created_at, updated_at)
                SELECT id, 
                    CASE WHEN brand_id IN ($brandList) THEN brand_id ELSE NULL END,
                    name, camera_count, price, price_original, description, whatsapp_message, is_featured, is_active, sort_order, created_at, updated_at
                FROM pricing_packages
            ");
        } else {
            // No valid brands, set all to NULL
            $db->exec("
                INSERT INTO pricing_packages_new (id, brand_id, name, camera_count, price, price_original, description, whatsapp_message, is_featured, is_active, sort_order, created_at, updated_at)
                SELECT id, NULL, name, camera_count, price, price_original, description, whatsapp_message, is_featured, is_active, sort_order, created_at, updated_at
                FROM pricing_packages
            ");
        }

        // Drop old table and rename new one
        $db->exec("DROP TABLE pricing_packages");
        $db->exec("ALTER TABLE pricing_packages_new RENAME TO pricing_packages");

        // Recreate pricing_features with FK to new pricing_packages
        $db->exec("
            CREATE TABLE IF NOT EXISTS pricing_features_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                package_id INTEGER NOT NULL,
                feature TEXT NOT NULL,
                is_included INTEGER NOT NULL DEFAULT 1,
                sort_order INTEGER NOT NULL DEFAULT 0,
                FOREIGN KEY (package_id) REFERENCES pricing_packages(id) ON DELETE CASCADE
            )
        ");

        $db->exec("
            INSERT INTO pricing_features_new (id, package_id, feature, is_included, sort_order)
            SELECT id, package_id, feature, is_included, sort_order
            FROM pricing_features
        ");

        $db->exec("DROP TABLE pricing_features");
        $db->exec("ALTER TABLE pricing_features_new RENAME TO pricing_features");

        // Create indexes on pricing_packages
        $db->exec("CREATE INDEX IF NOT EXISTS idx_pricing_packages_brand_id ON pricing_packages(brand_id)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_pricing_packages_is_active ON pricing_packages(is_active)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_pricing_packages_is_featured ON pricing_packages(is_featured)");

        // Create index on pricing_features for package_id (performance critical for eager loading)
        $db->exec("CREATE INDEX IF NOT EXISTS idx_pricing_features_package_id ON pricing_features(package_id)");

        // ============================================
        // 3. Fix pricing_brands - fix timestamps and add CHECK constraint
        // ============================================
        // Recreate with proper SQLite-compatible timestamps and CHECK constraint
        $db->exec("
            CREATE TABLE IF NOT EXISTS pricing_brands_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                logo TEXT,
                is_active INTEGER DEFAULT 1 CHECK (is_active IN (0,1)),
                sort_order INTEGER DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");

        $db->exec("
            INSERT INTO pricing_brands_new (id, name, slug, logo, is_active, sort_order, created_at, updated_at)
            SELECT id, name, slug, logo, is_active, sort_order, created_at, updated_at
            FROM pricing_brands
        ");

        $db->exec("DROP TABLE pricing_brands");
        $db->exec("ALTER TABLE pricing_brands_new RENAME TO pricing_brands");

        // Create indexes on pricing_brands
        $db->exec("CREATE INDEX IF NOT EXISTS idx_pricing_brands_is_active ON pricing_brands(is_active)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_pricing_brands_slug ON pricing_brands(slug)");

        // ============================================
        // 4. Add indexes to other tables
        // ============================================
        // Users table indexes
        $db->exec("CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_users_role ON users(role)");

        // Settings table
        $db->exec("CREATE INDEX IF NOT EXISTS idx_settings_key ON settings(`key`)");

        // Sliders table
        $db->exec("CREATE INDEX IF NOT EXISTS idx_sliders_is_active ON sliders(is_active)");

        // Gallery table
        $db->exec("CREATE INDEX IF NOT EXISTS idx_gallery_is_active ON gallery(is_active)");

        // Clients table
        $db->exec("CREATE INDEX IF NOT EXISTS idx_clients_is_active ON clients(is_active)");

        echo "Migration 2024_12_22_000000_add_foreign_keys_and_constraints completed successfully.\n";
    }

    public function down(PDO $db): void
    {
        $db->exec('PRAGMA foreign_keys = ON');

        // Drop indexes
        $db->exec("DROP INDEX IF EXISTS idx_testimonials_user_id");
        $db->exec("DROP INDEX IF EXISTS idx_testimonials_is_active");
        $db->exec("DROP INDEX IF EXISTS idx_pricing_packages_brand_id");
        $db->exec("DROP INDEX IF EXISTS idx_pricing_packages_is_active");
        $db->exec("DROP INDEX IF EXISTS idx_pricing_packages_is_featured");
        $db->exec("DROP INDEX IF EXISTS idx_pricing_features_package_id");
        $db->exec("DROP INDEX IF EXISTS idx_pricing_brands_is_active");
        $db->exec("DROP INDEX IF EXISTS idx_pricing_brands_slug");
        $db->exec("DROP INDEX IF EXISTS idx_users_email");
        $db->exec("DROP INDEX IF EXISTS idx_users_role");
        $db->exec("DROP INDEX IF EXISTS idx_settings_key");
        $db->exec("DROP INDEX IF EXISTS idx_sliders_is_active");
        $db->exec("DROP INDEX IF EXISTS idx_gallery_is_active");
        $db->exec("DROP INDEX IF EXISTS idx_clients_is_active");
        $db->exec("DROP INDEX IF EXISTS idx_login_attempts_ip_email");
        $db->exec("DROP INDEX IF EXISTS idx_login_attempts_attempted_at");
        $db->exec("DROP INDEX IF EXISTS idx_login_attempts_lockout_until");

        // Note: SQLite doesn't easily support recreating tables to remove FKs in down()
        // This is a one-way migration for safety - manual intervention needed for rollback
        echo "Warning: This migration is not easily reversible due to SQLite FK limitations.\n";
        echo "Manual database restoration from backup recommended for rollback.\n";
    }
};