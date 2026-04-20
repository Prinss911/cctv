<?php

namespace App\Middleware;

use App\Helpers\Csrf;

class CsrfMiddleware
{
    public static function handle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::verify();
        }
    }
}
