<?php

namespace App\Controllers\Admin;

use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use App\Helpers\View;

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
        Csrf::verify();

        if (Auth::isLockedOut()) {
            $mins = ceil(Auth::lockoutRemaining() / 60);
            Flash::set('error', "Terlalu banyak percobaan login. Coba lagi dalam $mins menit.");
            redirect('/admin/login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::attempt($email, $password)) {
            Flash::set('success', 'Selamat datang kembali!');
            redirect('/admin');
        }

        Flash::set('error', 'Email atau password salah.');
        redirect('/admin/login');
    }

    public function logout(): void
    {
        Auth::logout();
        session_start();
        Flash::set('success', 'Berhasil logout.');
        redirect('/admin/login');
    }
}
