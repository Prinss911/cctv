<?php

return new class {
    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS password_resets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT NOT NULL,
                token TEXT NOT NULL,
                expires_at TEXT NOT NULL,
                used INTEGER DEFAULT 0,
                created_at TEXT DEFAULT (datetime('now'))
            )
        ");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_password_resets_email ON password_resets(email)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_password_resets_token ON password_resets(token)");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS password_resets");
        $db->exec("DROP INDEX IF EXISTS idx_password_resets_email");
        $db->exec("DROP INDEX IF EXISTS idx_password_resets_token");
    }
};
