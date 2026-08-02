<?php
namespace App\Controllers\Admin;

use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use App\Helpers\View;
use App\Helpers\Request;
use App\Helpers\SecurityLogger;

class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('/admin');
        }
        View::render('admin/login', [], 'admin');
    }

    public function login(): void
    {
        $email = Request::post('email', '');
        $password = Request::post('password');

        if (Auth::attempt($email, $password)) {
            Flash::set('success', 'Selamat datang kembali!');
            redirect('/admin');
        }

        Flash::set('error', 'Email atau password salah.');
        redirect('/admin/login');
    }

    public function logout(): void
    {
        try {
            Csrf::verify();
        } catch (\RuntimeException $e) {
            SecurityLogger::logCsrfFailure('/admin/logout');
            http_response_code(403);
            die('Forbidden: Invalid CSRF token');
        }
        Auth::logout();
        Flash::set('success', 'Berhasil logout.');
        redirect('/admin/login');
    }
}