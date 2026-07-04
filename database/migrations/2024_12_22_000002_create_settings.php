<?php

return new class {
    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                `key` TEXT NOT NULL UNIQUE,
                value TEXT,
                `group` TEXT NOT NULL DEFAULT 'general',
                type TEXT NOT NULL DEFAULT 'text',
                label TEXT NOT NULL,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS settings");
    }
};
