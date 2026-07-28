<?php

namespace App\Controllers\Admin;


use App\Helpers\Flash;
use App\Helpers\Router;
use App\Helpers\View;
use App\Helpers\Request;
use App\Models\UserModel;
use App\Models\PasswordResetModel;

/**
 * Admin Password Reset Controller
 * Handles forgot password and reset password flows
 */
class PasswordResetController
{
    private UserModel $userModel;
    private PasswordResetModel $passwordResetModel;
    private string $baseUrl;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->passwordResetModel = new PasswordResetModel();
        $this->baseUrl = setting('app_url', 'http://localhost:8000');
    }

    /**
     * Display the forgot password form
     */
    public function showForgotForm(): void
    {
        if (\App\Helpers\Auth::check()) {
            redirect('/admin');
            exit;
        }
        View::render('admin/forgot', [], 'admin');
    }

    /**
     * Process forgot password request
     * Generates reset token and displays it (in production, would email it)
     */
    public function sendResetLink(): void
    {
        $email = trim(Request::post('email', ''));

        // Basic validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::set('error', 'Email tidak valid.');
            redirect('/admin/forgot-password');
            exit;
        }

        // Find user by email
        $user = $this->userModel->findByEmail($email);
        
        // Always show the same message to prevent email enumeration
        Flash::set('success', 'Jika email terdaftar, Anda akan menerima link reset password.');
        
        if (!$user) {
            redirect('/admin/forgot-password');
            exit;
        }

        // Rate limit: max 3 requests per email per hour
        $db = \App\Models\Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM password_resets WHERE email = ? AND created_at > datetime('now', '-1 hour')");
        $stmt->execute([$email]);
        if ((int)$stmt->fetchColumn() >= 3) {
            Flash::set('error', 'Terlalu banyak permintaan reset password. Coba lagi nanti.');
            redirect('/admin/forgot-password');
            exit;
        }

        // Generate reset token
        $token = $this->passwordResetModel->createToken($email);

        // Build reset link
        $resetLink = $this->baseUrl . '/admin/reset-password?token=' . $token;

        // Display the reset link (in production, would email it)
        if (setting('app_env', 'development') === 'production') {
            redirect('/admin/login');
            exit;
        } else {
            // Development mode: show the link directly with warning
            Flash::set('success', 'Link reset password: <a href="' . $resetLink . '" class="alert-link">' . $resetLink . '</a> <br><small class="text-muted">Catatan: Dalam production, link ini akan dikirim via email.</small>');
            redirect('/admin/login');
            exit;
        }
    }

    /**
     * Display the reset password form
     * Uses query parameter token instead of route parameter for simplicity
     */
    public function showResetForm(): void
    {
        if (\App\Helpers\Auth::check()) {
            redirect('/admin');
            exit;
        }

        $token = Request::get('token', '');
        
        // Verify token
        $reset = $this->passwordResetModel->verifyToken($token);
        if (!$reset) {
            Flash::set('error', 'Link reset password tidak valid atau sudah kadaluarsa.');
            redirect('/admin/forgot-password');
            exit;
        }

        View::render('admin/reset', ['token' => $token], 'admin');
    }

    /**
     * Process password reset
     * Validates token, updates password, marks token as used
     */
    public function resetPassword(): void
    {
        $token = Request::post('token', '');
        $password = Request::post('password', '');
        $confirmPassword = Request::post('confirm_password', '');

        // Validate token
        $reset = $this->passwordResetModel->verifyToken($token);
        if (!$reset) {
            Flash::set('error', 'Link reset password tidak valid atau sudah kadaluarsa.');
            redirect('/admin/forgot-password');
            exit;
        }

        // Validate password - minimum 8 chars, must contain uppercase, lowercase, and number
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            Flash::set('error', 'Password minimal 8 karakter dengan huruf besar, huruf kecil, dan angka.');
            redirect('/admin/reset-password?token=' . urlencode($token));
            exit;
        }

        if ($password !== $confirmPassword) {
            Flash::set('error', 'Konfirmasi password tidak cocok.');
            redirect('/admin/reset-password?token=' . urlencode($token));
            exit;
        }

        // Get user by email from reset record
        $user = $this->userModel->findByEmail($reset['email']);
        if (!$user) {
            Flash::set('error', 'User tidak ditemukan.');
            redirect('/admin/forgot-password');
            exit;
        }

        // Hash password and update
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        if (!$this->userModel->updatePassword($user['id'], $hashedPassword)) {
            Flash::set('error', 'Gagal mengupdate password.');
            redirect('/admin/reset-password?token=' . urlencode($token));
            exit;
        }

        // Mark token as used
        $this->passwordResetModel->markAsUsed((int)$reset['id']);

        // Clean up expired tokens
        $this->passwordResetModel->cleanupExpired();

        Flash::set('success', 'Password berhasil diubah. Silakan login dengan password baru.');
        redirect('/admin/login');
        exit;
    }
}
