<?php
namespace App\Controllers\Admin;

use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use App\Helpers\View;
use App\Helpers\Request;

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
        if (Auth::isLockedOut()) {
            $mins = ceil(Auth::lockoutRemaining() / 60);
            Flash::set('error', "Terlalu banyak percobaan login. Coba lagi dalam $mins menit.");
            redirect('/admin/login');
        }

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
        $token = $_POST['_csrf'] ?? $_GET['_csrf'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die('Forbidden: Invalid CSRF token');
        }
        Auth::logout();
        Flash::set('success', 'Berhasil logout.');
        redirect('/admin/login');
    }
}