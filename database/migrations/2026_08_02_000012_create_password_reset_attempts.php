<?php
/**
 * Migration: Create password_reset_attempts table for reset rate limiting
 */
return new class {
    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS password_reset_attempts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT NOT NULL,
                ip_address TEXT NOT NULL,
                attempted_at INTEGER DEFAULT 0
            )
        ");

        // Create indexes
        $db->exec("CREATE INDEX IF NOT EXISTS idx_reset_attempts_email ON password_reset_attempts(email, attempted_at)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_reset_attempts_ip ON password_reset_attempts(ip_address, attempted_at)");

        echo "Migration 012_create_password_reset_attempts completed successfully.\n";
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS password_reset_attempts");
        echo "Migration 012_create_password_reset_attempts rolled back.\n";
    }
};
