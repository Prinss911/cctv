<?php

class DatabaseSeeder
{
    public function run(PDO $db): void
    {
        $this->seedUsers($db);
        $this->seedSettings($db);
        $this->seedSliders($db);
        $this->seedPricing($db);
        $this->seedTestimonials($db);
        $this->seedClients($db);

        echo "  All seeders completed.\n";
    }

    private function seedUsers(PDO $db): void
    {
        $db->exec("DELETE FROM users");
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            'Administrator',
            'admin@bayucctv.com',
            password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]),
            'admin'
        ]);
        echo "  Seeded: users\n";
    }

    private function seedSettings(PDO $db): void
    {
        $db->exec("DELETE FROM settings");
        $settings = [
            ['site_name', 'Bayu CCTV', 'general', 'text', 'Nama Website'],
            ['site_tagline', 'Solusi Keamanan Terpercaya', 'general', 'text', 'Tagline'],
            ['logo', '', 'general', 'image', 'Logo'],
            ['favicon', '', 'general', 'image', 'Favicon'],
            ['about_title', 'Tentang Kami', 'general', 'text', 'Judul About'],
            ['about_text', 'Bayu CCTV adalah penyedia layanan pemasangan CCTV profesional yang telah melayani ribuan pelanggan di seluruh Indonesia. Kami menyediakan produk berkualitas dengan harga terjangkau dan garansi resmi.', 'general', 'textarea', 'Deskripsi About'],
            ['about_experience_years', '5', 'general', 'text', 'Tahun Pengalaman'],
            ['about_total_clients', '1000', 'general', 'text', 'Total Klien'],
            ['about_total_cities', '20', 'general', 'text', 'Total Kota'],
            ['whatsapp_number', '6281234567890', 'contact', 'text', 'Nomor WhatsApp'],
            ['phone_number', '0812-3456-7890', 'contact', 'text', 'Nomor Telepon'],
            ['email', 'info@bayucctv.com', 'contact', 'text', 'Email'],
            ['address', 'Jl. Contoh No. 123, Kota, Indonesia', 'contact', 'textarea', 'Alamat'],
            ['maps_embed', '', 'contact', 'textarea', 'Google Maps Embed URL'],
            ['meta_title', 'Bayu CCTV - Solusi Keamanan Terpercaya', 'seo', 'text', 'Meta Title'],
            ['meta_description', 'Jasa pemasangan CCTV profesional dengan harga terjangkau. Paket lengkap termasuk kamera, DVR, hardisk, kabel, dan pemasangan.', 'seo', 'textarea', 'Meta Description'],
            ['og_image', '', 'seo', 'image', 'OG Image'],
            ['footer_copyright', '© 2024 Bayu CCTV. All rights reserved.', 'general', 'text', 'Footer Copyright'],
            ['whatsapp_greeting', 'Halo! Ada yang bisa kami bantu?', 'contact', 'text', 'WhatsApp Greeting'],
        ];

        $stmt = $db->prepare("INSERT INTO settings (`key`, value, `group`, type, label) VALUES (?, ?, ?, ?, ?)");
        foreach ($settings as $s) {
            $stmt->execute($s);
        }
        echo "  Seeded: settings\n";
    }

    private function seedSliders(PDO $db): void
    {
        $db->exec("DELETE FROM sliders");
        $sliders = [
            ['Solusi Keamanan Terpercaya', 'Pasang CCTV untuk keamanan rumah dan bisnis Anda', 'slider-1.jpg', 'Hubungi Kami', '#', 1],
            ['Paket CCTV Terlengkap', 'Harga sudah termasuk pemasangan dan garansi', 'slider-2.jpg', 'Lihat Paket', '#Harga', 2],
            ['Teknisi Berpengalaman', 'Pemasangan rapi dan profesional oleh teknisi tersertifikasi', 'slider-3.jpg', 'Hubungi Kami', '#', 3],
        ];

        $stmt = $db->prepare("INSERT INTO sliders (title, subtitle, image, button_text, button_url, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($sliders as $s) {
            $stmt->execute($s);
        }
        echo "  Seeded: sliders\n";
    }

    private function seedPricing(PDO $db): void
    {
        $db->exec("DELETE FROM pricing_features");
        $db->exec("DELETE FROM pricing_packages");

        $packages = [
            [
                'name' => 'Paket 2 Kamera',
                'camera_count' => 2,
                'price' => 2700000,
                'price_original' => 3500000,
                'description' => 'Cocok untuk rumah kecil dan toko',
                'whatsapp_message' => 'Halo, saya tertarik dengan Paket 2 Kamera CCTV. Mohon info lebih lanjut.',
                'is_featured' => 0,
                'sort_order' => 1,
                'features' => ['2 Kamera 2MP Outdoor/Indoor', 'DVR 4 Channel', 'Hardisk 500GB', 'Kabel 40 Meter', 'Pemasangan', 'Garansi 1 Tahun'],
            ],
            [
                'name' => 'Paket 4 Kamera',
                'camera_count' => 4,
                'price' => 4000000,
                'price_original' => 5200000,
                'description' => 'Paling populer untuk rumah dan toko',
                'whatsapp_message' => 'Halo, saya tertarik dengan Paket 4 Kamera CCTV. Mohon info lebih lanjut.',
                'is_featured' => 1,
                'sort_order' => 2,
                'features' => ['4 Kamera 2MP Outdoor/Indoor', 'DVR 4 Channel', 'Hardisk 1TB', 'Kabel 80 Meter', 'Pemasangan', 'Garansi 1 Tahun'],
            ],
            [
                'name' => 'Paket 8 Kamera',
                'camera_count' => 8,
                'price' => 7200000,
                'price_original' => 9000000,
                'description' => 'Untuk gedung dan area luas',
                'whatsapp_message' => 'Halo, saya tertarik dengan Paket 8 Kamera CCTV. Mohon info lebih lanjut.',
                'is_featured' => 0,
                'sort_order' => 3,
                'features' => ['8 Kamera 2MP Outdoor/Indoor', 'DVR 8 Channel', 'Hardisk 2TB', 'Kabel 160 Meter', 'Pemasangan', 'Garansi 1 Tahun'],
            ],
            [
                'name' => 'Paket 16 Kamera',
                'camera_count' => 16,
                'price' => 13200000,
                'price_original' => 16500000,
                'description' => 'Untuk kantor dan pabrik besar',
                'whatsapp_message' => 'Halo, saya tertarik dengan Paket 16 Kamera CCTV. Mohon info lebih lanjut.',
                'is_featured' => 0,
                'sort_order' => 4,
                'features' => ['16 Kamera 2MP Outdoor/Indoor', 'DVR 16 Channel', 'Hardisk 4TB', 'Kabel 320 Meter', 'Pemasangan', 'Garansi 1 Tahun'],
            ],
        ];

        $stmtPkg = $db->prepare("INSERT INTO pricing_packages (name, camera_count, price, price_original, description, whatsapp_message, is_featured, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtFeat = $db->prepare("INSERT INTO pricing_features (package_id, feature, sort_order) VALUES (?, ?, ?)");

        foreach ($packages as $pkg) {
            $stmtPkg->execute([
                $pkg['name'], $pkg['camera_count'], $pkg['price'], $pkg['price_original'],
                $pkg['description'], $pkg['whatsapp_message'], $pkg['is_featured'], $pkg['sort_order']
            ]);
            $pkgId = $db->lastInsertId();
            foreach ($pkg['features'] as $i => $feat) {
                $stmtFeat->execute([$pkgId, $feat, $i + 1]);
            }
        }
        echo "  Seeded: pricing_packages & pricing_features\n";
    }

    private function seedTestimonials(PDO $db): void
    {
        $db->exec("DELETE FROM testimonials");
        $testimonials = [
            ['Budi Santoso', 'Pelayanan sangat memuaskan, teknisi datang tepat waktu dan pemasangan rapi.', 5, 1],
            ['Siti Rahayu', 'CCTV nya jernih, bisa pantau dari HP. Recommended!', 5, 2],
            ['Ahmad Fauzi', 'Harga terjangkau, kualitas bagus. Sudah pasang di 2 toko saya.', 5, 3],
            ['Dewi Lestari', 'Proses cepat, hasil memuaskan. Terima kasih Bayu CCTV!', 5, 4],
            ['Rudi Hartono', 'Garansi terjamin, kalau ada masalah langsung ditangani.', 4, 5],
        ];

        $stmt = $db->prepare("INSERT INTO testimonials (customer_name, content, rating, sort_order) VALUES (?, ?, ?, ?)");
        foreach ($testimonials as $t) {
            $stmt->execute($t);
        }
        echo "  Seeded: testimonials\n";
    }

    private function seedClients(PDO $db): void
    {
        $db->exec("DELETE FROM clients");
        $clients = [
            ['Hikvision', 'hikvision.png', 'https://www.hikvision.com', 1],
            ['Dahua', 'dahua.png', 'https://www.dahuasecurity.com', 2],
            ['SPC', 'spc.png', null, 3],
            ['Hilook', 'hilook.png', 'https://www.hilook.com', 4],
        ];

        $stmt = $db->prepare("INSERT INTO clients (name, logo, website, sort_order) VALUES (?, ?, ?, ?)");
        foreach ($clients as $c) {
            $stmt->execute($c);
        }
        echo "  Seeded: clients\n";
    }
}
