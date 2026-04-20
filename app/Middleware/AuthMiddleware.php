<?php

namespace App\Middleware;

use App\Helpers\Auth;

class AuthMiddleware
{
    public static function handle(): void
    {
        if (!Auth::check()) {
            redirect('/admin/login');
        }
    }
}
