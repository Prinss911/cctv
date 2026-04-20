<?php

namespace App\Models;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

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
                if (env('APP_DEBUG', false)) {
                    die("Database connection failed: " . $e->getMessage());
                }
                die("Database connection failed.");
            }
        }

        return self::$instance;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }
}
