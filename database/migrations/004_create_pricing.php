<?php

return new class {
    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS pricing_packages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                camera_count INTEGER NOT NULL,
                price INTEGER NOT NULL,
                price_original INTEGER,
                description TEXT,
                whatsapp_message TEXT NOT NULL,
                is_featured INTEGER NOT NULL DEFAULT 0,
                is_active INTEGER NOT NULL DEFAULT 1,
                sort_order INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");

        $db->exec("
            CREATE TABLE IF NOT EXISTS pricing_features (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                package_id INTEGER NOT NULL,
                feature TEXT NOT NULL,
                is_included INTEGER NOT NULL DEFAULT 1,
                sort_order INTEGER NOT NULL DEFAULT 0,
                FOREIGN KEY (package_id) REFERENCES pricing_packages(id) ON DELETE CASCADE
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS pricing_features");
        $db->exec("DROP TABLE IF EXISTS pricing_packages");
    }
};
