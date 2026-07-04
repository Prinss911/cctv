<?php

return new class {
    public function up(PDO $db): void
    {
        // Check if column already exists
        $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM pragma_table_info('sliders') WHERE name = 'tag'");
        $stmt->execute();
        $exists = $stmt->fetchColumn();
        if (!$exists) {
            $db->exec("ALTER TABLE sliders ADD COLUMN tag TEXT DEFAULT 'Keamanan Terpercaya'");
        }
    }

    public function down(PDO $db): void
    {
        // SQLite doesn't support DROP COLUMN in older versions; skip
    }
};
