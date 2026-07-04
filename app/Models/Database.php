<?php

namespace App\Models;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static ?string $driver = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require BASE_PATH . '/config/database.php';
            $driver = $config['driver'] ?? 'sqlite';
            try {
                switch ($driver) {
                    case 'mysql':
                        $c = $config['mysql'];
                        $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset={$c['charset']}";
                        self::$instance = new PDO($dsn, $c['user'], $c['pass']);
                        break;

                    case 'pgsql':
                        $c = $config['pgsql'];
                        $dsn = "pgsql:host={$c['host']};port={$c['port']};dbname={$c['name']}";
                        self::$instance = new PDO($dsn, $c['user'], $c['pass']);
                        break;

                    case 'sqlite':
                    default:
                        $path = $config['sqlite']['path'];
                        $dir = dirname($path);
                        if (!is_dir($dir)) {
                            mkdir($dir, 0755, true);
                        }
                        self::$instance = new PDO("sqlite:$path");
                        self::$instance->exec('PRAGMA journal_mode = WAL');
                        self::$instance->exec('PRAGMA foreign_keys = ON');
                        break;
                }

                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$instance->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $e) {
                throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public static function getDriver(): string
    {
        if (self::$driver === null) {
            $config = require BASE_PATH . '/config/database.php';
            self::$driver = $config['driver'] ?? 'sqlite';
        }
        return self::$driver;
    }

    /**
     * Compile schema type for current database driver
     * Translates SQLite-specific types to driver-appropriate equivalents
     * 
     * @param string $type Original type (SQLite style)
     * @return string Compiled type for current driver
     */
    public static function compileSchema(string $type): string
    {
        $driver = self::getDriver();

        // Auto-increment primary key patterns
        $patterns = [
            '/INTEGER PRIMARY KEY AUTOINCREMENT/i' => match ($driver) {
                'mysql' => 'INT AUTO_INCREMENT PRIMARY KEY',
                'pgsql' => 'SERIAL PRIMARY KEY',
                'sqlite' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
            },
            '/INTEGER NOT NULL AUTOINCREMENT/i' => match ($driver) {
                'mysql' => 'INT NOT NULL AUTO_INCREMENT',
                'pgsql' => 'SERIAL NOT NULL',
                'sqlite' => 'INTEGER NOT NULL AUTOINCREMENT',
            },
        ];

        foreach ($patterns as $pattern => $replacement) {
            if (preg_match($pattern, $type)) {
                return $replacement;
            }
        }

        return $type;
    }

    public static function reset(): void
    {
        self::$instance = null;
        self::$driver = null;
    }
}
