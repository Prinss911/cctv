<?php
namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\UserModel;
use App\Helpers\{View, Flash, Csrf, Request};
use function trim;

class UsersController
{
    private UserModel $model;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->model = new UserModel();
    }

    public function index(): void
    {
        $users = $this->model->getAll('name ASC');
        View::render('admin/users/index', compact('users'), 'admin');
    }

    public function create(): void
    {
        View::render('admin/users/create', [], 'admin');
    }

    public function store(): void
    {
        Csrf::verify();

        $name  = trim(Request::post('name', ''));
        $email = trim(Request::post('email', ''));
        $password = Request::post('password', '');
        $role  = trim(Request::post('role', 'admin'));

        if (empty($name) || empty($email) || empty($password)) {
            Flash::set('error', 'Nama, email, dan password wajib diisi.');
            redirect('/admin/users/create');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::set('error', 'Format email tidak valid.');
            redirect('/admin/users/create');
            exit;
        }

        if (strlen($password) < 8) {
            Flash::set('error', 'Password minimal 8 karakter.');
            redirect('/admin/users/create');
            exit;
        }

        // Validate role whitelist
        if (!in_array($role, ['admin', 'superadmin'])) {
            Flash::set('error', 'Role tidak valid.');
            redirect('/admin/users/create');
            exit;
        }

        // Cek email sudah terdaftar
        if ($this->model->findByEmail($email)) {
            Flash::set('error', 'Email sudah terdaftar.');
            redirect('/admin/users/create');
            exit;
        }

        $this->model->create([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
            'role'     => $role,
        ]);

        Flash::set('success', 'Pengguna berhasil ditambahkan.');
        redirect('/admin/users');
        exit;
    }

    public function edit(string $id): void
    {
        $user = $this->model->find((int)$id);
        if (!$user) {
            Flash::set('error', 'Pengguna tidak ditemukan.');
            redirect('/admin/users');
            exit;
        }
        View::render('admin/users/edit', compact('user'), 'admin');
    }

    public function update(string $id): void
    {
        Csrf::verify();

        $user = $this->model->find((int)$id);
        if (!$user) {
            Flash::set('error', 'Pengguna tidak ditemukan.');
            redirect('/admin/users');
        }

        $name  = trim(Request::post('name', ''));
        $email = trim(Request::post('email', ''));
        $role  = trim(Request::post('role', 'admin'));

        // Validate role whitelist
        if (!in_array($role, ['admin', 'superadmin'])) {
            Flash::set('error', 'Role tidak valid.');
            redirect('/admin/users/edit/' . $id);
            exit;
        }

        if (empty($name) || empty($email)) {
            Flash::set('error', 'Nama dan email wajib diisi.');
            redirect('/admin/users/edit/' . $id);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::set('error', 'Format email tidak valid.');
            redirect('/admin/users/edit/' . $id);
            exit;
        }

        // Cek email sudah dipakai user lain
        $existing = $this->model->findByEmail($email);
        if ($existing && $existing['id'] !== (int)$id) {
            Flash::set('error', 'Email sudah digunakan pengguna lain.');
            redirect('/admin/users/edit/' . $id);
            exit;
        }

        $data = [
            'name'  => $name,
            'email' => $email,
            'role'  => $role,
        ];

        $this->model->update((int)$id, $data);
        Flash::set('success', 'Pengguna berhasil diperbarui.');
        redirect('/admin/users');
        exit;
    }

    public function destroy(string $id): void
    {
        Csrf::verify();

        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Pengguna tidak ditemukan.');
            redirect('/admin/users');
        }

        // Cegah hapus diri sendiri
        $currentUser = \App\Helpers\Auth::user();
        if ($currentUser && (int)$currentUser['id'] === (int)$id) {
            Flash::set('error', 'Tidak dapat menghapus akun sendiri.');
            redirect('/admin/users');
            exit;
        }

        $this->model->delete((int)$id);
        Flash::set('success', 'Pengguna berhasil dihapus.');
        redirect('/admin/users');
        exit;
    }

    public function password(): void
    {
        $user = \App\Helpers\Auth::user();
        View::render('admin/users/password', compact('user'), 'admin');
    }

    public function updatePassword(): void
    {
        Csrf::verify();

        $currentUser = \App\Helpers\Auth::user();
        if (!$currentUser) {
            Flash::set('error', 'Sesi berakhir. Silakan login ulang.');
            redirect('/admin/login');
            exit;
        }

        $currentPassword = Request::post('current_password', '');
        $newPassword     = Request::post('new_password', '');
        $confirmPassword = Request::post('confirm_password', '');

        // Validate password != current password
        if ($currentPassword !== '' && $newPassword !== '' && password_verify($newPassword, $currentUser['password'])) {
            Flash::set('error', 'Password baru tidak boleh sama dengan password saat ini.');
            redirect('/admin/users/password');
            exit;
        }

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            Flash::set('error', 'Semua field password wajib diisi.');
            redirect('/admin/users/password');
            exit;
        }

        if (!password_verify($currentPassword, $currentUser['password'])) {
            Flash::set('error', 'Password saat ini tidak cocok.');
            redirect('/admin/users/password');
            exit;
        }

        if (strlen($newPassword) < 8) {
            Flash::set('error', 'Password baru minimal 8 karakter.');
            redirect('/admin/users/password');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            Flash::set('error', 'Konfirmasi password tidak cocok.');
            redirect('/admin/users/password');
            exit;
        }

        $this->model->updatePassword((int)$currentUser['id'], password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]));
        Flash::set('success', 'Password berhasil diubah.');
        redirect('/admin');
        exit;
    }
}
