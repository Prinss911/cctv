<?php

/**
 * Migration: Create faqs table
 * Frequently Asked Questions about CCTV installation, warranty, pricing, etc.
 */

return new class {
    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS faqs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                question TEXT NOT NULL,
                answer TEXT NOT NULL,
                sort_order INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");

        // Seed initial 7 FAQ items
        $stmt = $db->prepare("
            INSERT INTO faqs (question, answer, sort_order)
            VALUES (?, ?, ?)
        ");

        $faqs = [
            [
                'Berapa lama proses pemasangan CCTV?',
                'Proses pemasangan CCTV bervariasi tergantung jumlah kamera. Untuk paket 2 kamera biasanya selesai dalam 2-3 jam, paket 4 kamera sekitar 3-5 jam, dan paket 8 kamera membutuhkan waktu 5-8 jam. Tim teknisi kami akan bekerja cepat dan rapi.',
                1,
            ],
            [
                'Apakah ada garansi untuk produk dan pemasangan?',
                'Ya, semua produk CCTV yang kami jual dilengkapi garansi resmi 1 tahun untuk hardware. Selain itu, pemasangan juga kami beri garansi selama 1 bulan. Jika ada kerusakan atau masalah, tim kami siap membantu.',
                2,
            ],
            [
                'Bisakah CCTV dipantau dari smartphone?',
                'Tentu bisa. Semua paket CCTV kami mendukung remote monitoring via smartphone menggunakan aplikasi resmi dari masing-masing brand (Hikvision, Dahua, SPC). Anda bisa memantau gambar secara real-time kapan saja dan di mana saja selama terhubung internet.',
                3,
            ],
            [
                'Apakah bisa menggunakan CCTV tanpa internet?',
                'Bisa. CCTV tetap dapat merekam dan menyimpan video ke DVR meskipun tanpa koneksi internet. Namun untuk fitur remote monitoring via smartphone, Anda memerlukan koneksi internet pada DVR.',
                4,
            ],
            [
                'Berapa lama rekaman CCTV bisa disimpan?',
                'Lama penyimpanan tergantung pada kapasitas hardisk dan jumlah kamera. Dengan hardisk 1TB untuk 4 kamera, rekaman bisa bertahan hingga 30 hari. Kami menyediakan pilihan hardisk mulai dari 500GB hingga 2TB sesuai kebutuhan.',
                5,
            ],
            [
                'Apakah harga sudah termasuk biaya pemasangan?',
                'Ya, semua harga paket CCTV yang tercantum sudah termasuk biaya pemasangan oleh teknisi berpengalaman kami. Juga termasuk kabel, bracket, dan aksesoris lainnya. Tidak ada biaya tambahan tersembunyi.',
                6,
            ],
            [
                'Bagaimana cara memilih paket CCTV yang tepat?',
                'Pemilihan paket tergantung pada kebutuhan Anda. Untuk rumah tinggal, paket 2-4 kamera sudah cukup. Untuk toko atau gudang, kami rekomendasikan paket 4-8 kamera. Konsultasikan kebutuhan Anda dengan tim kami melalui WhatsApp untuk rekomendasi terbaik.',
                7,
            ],
        ];

        foreach ($faqs as $faq) {
            $stmt->execute($faq);
        }

        echo "Migration 011_create_faqs completed successfully.\n";
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS faqs");
        echo "Migration 011_create_faqs rolled back.\n";
    }
};
