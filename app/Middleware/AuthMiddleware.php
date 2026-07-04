<?php
namespace App\Middleware;

use App\Helpers\Auth;
use App\Helpers\SecurityLogger;
use App\Models\UserModel;

class AuthMiddleware
{
    public static function handle(): void
    {
        if (!Auth::check()) {
            $ip = Auth::getClientIp();
            SecurityLogger::logAccessViolation('unauthenticated_admin_access', 'Attempted to access admin panel without authentication');
            redirect('/admin/login');
        }

        // Check if user still exists (not deleted)
        $user = Auth::user();
        if ($user !== null) {
            $userModel = new UserModel();
            $existingUser = $userModel->findByEmail($user['email']);
            if (!$existingUser) {
                Auth::logout();
                redirect('/admin/login');
            }

            // Verify user has admin role
            $allowedRoles = ['admin', 'superadmin'];
            if (!in_array($user['role'], $allowedRoles, true)) {
                SecurityLogger::logAccessViolation('insufficient_role', 'User with role ' . ($user['role'] ?? 'none') . ' attempted to access admin panel');
                Auth::logout();
                redirect('/admin/login');
            }
        }
    }
}