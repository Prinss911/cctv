<?php
/**
 * Migration: Create login_attempts table for IP-based rate limiting
 */
return new class {
    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS login_attempts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ip_address TEXT NOT NULL,
                email TEXT NOT NULL,
                attempted_at INTEGER DEFAULT 0,
                lockout_until INTEGER DEFAULT 0,
                UNIQUE(ip_address, email)
            )
        ");
        
        // Create indexes
        $db->exec("CREATE INDEX IF NOT EXISTS idx_login_attempts_ip_email ON login_attempts(ip_address, email)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_login_attempts_attempted_at ON login_attempts(attempted_at)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_login_attempts_lockout_until ON login_attempts(lockout_until)");
        
        echo "Migration 009_create_login_attempts completed successfully.\n";
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS login_attempts");
        echo "Migration 009_create_login_attempts rolled back.\n";
    }
};