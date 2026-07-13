<?php

/**
 * Migration: Create about_features table
 * Feature items shown in the "Tentang Kami" section on the homepage
 */

return new class {
    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS about_features (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                icon TEXT NOT NULL DEFAULT 'fa-check',
                title TEXT NOT NULL,
                description TEXT NOT NULL,
                sort_order INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");

        // Seed initial 4 features (matching the previously hardcoded content)
        $stmt = $db->prepare("
            INSERT INTO about_features (icon, title, description, sort_order)
            VALUES (?, ?, ?, ?)
        ");

        $features = [
            ['fa-tools',       'Teknisi Berpengalaman',   'Tim tersertifikasi dengan pengalaman bertahun-tahun', 1],
            ['fa-award',       'Garansi Resmi',            'Produk dan pemasangan bergaransi penuh',               2],
            ['fa-mobile-alt',  'Pantau dari HP',           'Remote monitoring via smartphone kapan saja',           3],
            ['fa-headset',     'After-Sales Support',      'Layanan purna jual yang responsif',                     4],
        ];

        foreach ($features as $feature) {
            $stmt->execute($feature);
        }

        echo "Migration 010_create_about_features completed successfully.\n";
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS about_features");
        echo "Migration 010_create_about_features rolled back.\n";
    }
};
