<?php

return new class {
    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS clients (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                logo TEXT NOT NULL,
                website TEXT,
                sort_order INTEGER NOT NULL DEFAULT 0,
                is_active INTEGER NOT NULL DEFAULT 1,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS clients");
    }
};
