<?php

/**
 * Migration: Create advantages table
 * Points of excellence shown on homepage
 */

return new class {
    public function up(PDO $db): void
    {
        // Enable foreign keys for SQLite
        $db->exec('PRAGMA foreign_keys = ON');

        $db->exec("
            CREATE TABLE IF NOT EXISTS advantages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT NOT NULL,
                icon TEXT NOT NULL DEFAULT 'fa-check',
                border_color TEXT NOT NULL DEFAULT '#d4a373',
                sort_order INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");

        // Seed initial 6 advantages
        $stmt = $db->prepare("
            INSERT INTO advantages (title, description, icon, border_color, sort_order)
            VALUES (?, ?, ?, ?, ?)
        ");

        $advantages = [
            [
                'Variasi Paket Lengkap',
                'Kami menyediakan berbagai pilihan paket CCTV yang bisa disesuaikan dengan budget dan kebutuhan Anda, dari rumah tinggal hingga bisnis.',
                'fa-layer-group',
                '#d4a373',
                1,
            ],
            [
                'Produk Berkualitas & Bergaransi',
                'Menggunakan produk CCTV dengan teknologi terbaru dan garansi resmi, sehingga Anda tidak perlu khawatir soal kualitas.',
                'fa-shield-alt',
                '#6b705c',
                2,
            ],
            [
                'Tim Berpengalaman',
                'Didukung tim teknisi yang berpengalaman menangani proyek CCTV mulai dari skala rumah tangga hingga pabrik dan gudang besar.',
                'fa-user-tie',
                '#a5a58d',
                3,
            ],
            [
                'Tim Teknisi Profesional',
                'Teknisi kami bekerja dengan rapi, cepat, dan tepat waktu. Kami menjaga area kerja tetap bersih setelah pemasangan.',
                'fa-hard-hat',
                '#cb997e',
                4,
            ],
            [
                'Gratis Konsultasi & Survey',
                'Dapatkan konsultasi dan survey lokasi secara gratis. Kami akan merekomendasikan solusi terbaik sesuai kondisi lokasi Anda.',
                'fa-hand-holding-heart',
                '#b5838d',
                5,
            ],
            [
                'Layanan Customer Service',
                'Tim customer service kami siap membantu Anda kapan saja, baik sebelum maupun setelah pemasangan, untuk memastikan kepuasan Anda.',
                'fa-headset',
                '#83a5b3',
                6,
            ],
        ];

        foreach ($advantages as $advantage) {
            $stmt->execute($advantage);
        }

        echo "Migration 009_create_advantages completed successfully.\n";
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS advantages");

        echo "Migration 009_create_advantages rolled back.\n";
    }
};
