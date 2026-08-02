<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\AboutFeatureModel;
use App\Helpers\{View, Flash, Request};

class AboutFeatureController
{
    private AboutFeatureModel $model;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->model = new AboutFeatureModel();
    }

    public function index(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        $result = $this->model->paginate('sort_order ASC', 20, $page);
        View::render('admin/about-features/index', ['items' => $result['data'], 'pagination' => $result], 'admin');
    }

    public function create(): void
    {
        View::render('admin/about-features/create', [], 'admin');
    }

    public function store(): void
    {
        $this->model->create([
            'icon'        => trim(Request::post('icon', 'fa-check')),
            'title'       => trim(Request::post('title', '')),
            'description' => trim(Request::post('description', '')),
            'sort_order'  => (int)Request::post('sort_order', 0),
            'is_active'   => Request::has('is_active') ? 1 : 0,
        ]);

        Flash::set('success', 'Fitur tentang kami berhasil ditambahkan.');
        redirect('/admin/about-features');
        exit;
    }

    public function edit(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Fitur tidak ditemukan.');
            redirect('/admin/about-features');
            exit;
        }
        View::render('admin/about-features/edit', compact('item'), 'admin');
    }

    public function update(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Fitur tidak ditemukan.');
            redirect('/admin/about-features');
            exit;
        }

        $this->model->update((int)$id, [
            'icon'        => trim(Request::post('icon', 'fa-check')),
            'title'       => trim(Request::post('title', '')),
            'description' => trim(Request::post('description', '')),
            'sort_order'  => (int)Request::post('sort_order', 0),
            'is_active'   => Request::has('is_active') ? 1 : 0,
        ]);

        Flash::set('success', 'Fitur tentang kami berhasil diperbarui.');
        redirect('/admin/about-features');
        exit;
    }

    public function destroy(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Fitur tidak ditemukan.');
            redirect('/admin/about-features');
            return;
        }

        $this->model->delete((int)$id);
        Flash::set('success', 'Fitur tentang kami berhasil dihapus.');
        redirect('/admin/about-features');
        exit;
    }

    public function reorder(): void
    {
        $ids = json_decode(file_get_contents('php://input'), true)['ids'] ?? [];
        $this->model->updateSortOrder($ids);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }
}
