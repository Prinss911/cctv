<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\FaqModel;
use App\Helpers\{View, Flash, Csrf, Request};

class FaqController
{
    private FaqModel $model;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->model = new FaqModel();
    }

    public function index(): void
    {
        $items = $this->model->getAll();
        View::render('admin/faqs/index', compact('items'), 'admin');
    }

    public function create(): void
    {
        View::render('admin/faqs/create', [], 'admin');
    }

    public function store(): void
    {
        Csrf::verify();

        $this->model->create([
            'question'   => trim(Request::post('question', '')),
            'answer'     => trim(Request::post('answer', '')),
            'sort_order' => (int)Request::post('sort_order', 0),
            'is_active'  => Request::has('is_active') ? 1 : 0,
        ]);

        Flash::set('success', 'FAQ berhasil ditambahkan.');
        redirect('/admin/faqs');
        exit;
    }

    public function edit(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'FAQ tidak ditemukan.');
            redirect('/admin/faqs');
            exit;
        }
        View::render('admin/faqs/edit', compact('item'), 'admin');
    }

    public function update(string $id): void
    {
        Csrf::verify();
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'FAQ tidak ditemukan.');
            redirect('/admin/faqs');
            exit;
        }

        $this->model->update((int)$id, [
            'question'   => trim(Request::post('question', '')),
            'answer'     => trim(Request::post('answer', '')),
            'sort_order' => (int)Request::post('sort_order', 0),
            'is_active'  => Request::has('is_active') ? 1 : 0,
        ]);

        Flash::set('success', 'FAQ berhasil diperbarui.');
        redirect('/admin/faqs');
        exit;
    }

    public function destroy(string $id): void
    {
        Csrf::verify();
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'FAQ tidak ditemukan.');
            redirect('/admin/faqs');
            return;
        }

        $this->model->delete((int)$id);
        Flash::set('success', 'FAQ berhasil dihapus.');
        redirect('/admin/faqs');
        exit;
    }

    public function reorder(): void
    {
        Csrf::verify();
        $ids = json_decode(file_get_contents('php://input'), true)['ids'] ?? [];
        $this->model->updateSortOrder($ids);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }
}
