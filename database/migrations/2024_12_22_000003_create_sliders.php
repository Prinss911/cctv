<?php

return new class {
    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS sliders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                subtitle TEXT,
                image TEXT NOT NULL,
                button_text TEXT DEFAULT 'Hubungi Kami',
                button_url TEXT,
                sort_order INTEGER NOT NULL DEFAULT 0,
                is_active INTEGER NOT NULL DEFAULT 1,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS sliders");
    }
};
