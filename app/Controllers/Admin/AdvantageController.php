<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\AdvantageModel;
use App\Helpers\{View, Flash, Request};
use function trim;

class AdvantageController
{
    private AdvantageModel $model;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->model = new AdvantageModel();
    }

    public function index(): void
    {
        $items = $this->model->getAll();
        View::render('admin/advantages/index', compact('items'), 'admin');
    }

    public function create(): void
    {
        View::render('admin/advantages/create', [], 'admin');
    }

    public function store(): void
    {
        $this->model->create([
            'title'        => trim(Request::post('title', '')),
            'description'  => trim(Request::post('description', '')),
            'icon'         => trim(Request::post('icon', 'fa-check-circle')),
            'border_color' => trim(Request::post('border_color', '#d4a373')),
            'sort_order'   => (int)Request::post('sort_order', 0),
            'is_active'    => Request::has('is_active') ? 1 : 0,
        ]);

        Flash::set('success', 'Keunggulan berhasil ditambahkan.');
        redirect('/admin/advantages');
        exit;
    }

    public function edit(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Keunggulan tidak ditemukan.');
            redirect('/admin/advantages');
            exit;
        }
        View::render('admin/advantages/edit', compact('item'), 'admin');
    }

    public function update(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Keunggulan tidak ditemukan.');
            redirect('/admin/advantages');
            exit;
        }

        $this->model->update((int)$id, [
            'title'        => trim(Request::post('title', '')),
            'description'  => trim(Request::post('description', '')),
            'icon'         => trim(Request::post('icon', 'fa-check-circle')),
            'border_color' => trim(Request::post('border_color', '#d4a373')),
            'sort_order'   => (int)Request::post('sort_order', 0),
            'is_active'    => Request::has('is_active') ? 1 : 0,
        ]);

        Flash::set('success', 'Keunggulan berhasil diperbarui.');
        redirect('/admin/advantages');
        exit;
    }

    public function destroy(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Keunggulan tidak ditemukan.');
            redirect('/admin/advantages');
            return;
        }

        $this->model->delete((int)$id);
        Flash::set('success', 'Keunggulan berhasil dihapus.');
        redirect('/admin/advantages');
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
