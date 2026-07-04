<?php

namespace App\Helpers;

class SecurityLogger
{
    /**
     * Log authentication failure
     *
     * @param string $email
     * @param string $ip
     * @param string $reason
     * @return void
     */
    public static function logAuthFailure(string $email, string $reason): void
    {
        $ip = Auth::getClientIp();
        self::log('auth_failure', [
            'email' => $email,
            'ip' => $ip,
            'reason' => $reason
        ]);
    }

    /**
     * Log file upload attempt
     *
     * @param string $filename
     * @param string $ip
     * @param bool $success
     * @return void
     */
    public static function logUpload(string $filename, bool $success): void
    {
        $ip = Auth::getClientIp();
        self::log('upload_attempt', [
            'filename' => $filename,
            'ip' => $ip,
            'success' => $success
        ]);
    }

    /**
     * Log access violation
     *
     * @param string $action
     * @param string $ip
     * @param string $details
     * @return void
     */
    public static function logAccessViolation(string $action, string $details): void
    {
        $ip = Auth::getClientIp();
        self::log('access_violation', [
            'action' => $action,
            'ip' => $ip,
            'details' => $details
        ]);
    }

    /**
     * Log CSRF failure
     *
     * @param string $ip
     * @param string $route
     * @return void
     */
    public static function logCsrfFailure(string $route): void
    {
        $ip = Auth::getClientIp();
        self::log('csrf_failure', [
            'ip' => $ip,
            'route' => $route
        ]);
    }

    /**
     * Write log entry to security.log
     *
     * @param string $type
     * @param array $data
     * @return void
     */
    private static function log(string $type, array $data): void
    {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'data' => $data
        ];

        $logMessage = json_encode($logEntry) . PHP_EOL;

        // Ensure storage/logs directory exists
        $logDir = BASE_PATH . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/security.log';
        error_log($logMessage, 3, $logFile);
    }
}