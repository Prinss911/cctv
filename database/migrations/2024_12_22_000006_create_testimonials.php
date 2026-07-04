<?php

return new class {
    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS testimonials (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                customer_name TEXT NOT NULL,
                content TEXT,
                screenshot TEXT,
                rating INTEGER DEFAULT 5,
                sort_order INTEGER NOT NULL DEFAULT 0,
                is_active INTEGER NOT NULL DEFAULT 1,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS testimonials");
    }
};
