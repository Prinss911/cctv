<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\TestimonialModel;
use App\Helpers\{View, Flash, Csrf, Upload};

class TestimonialController
{
    private TestimonialModel $model;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->model = new TestimonialModel();
    }

    public function index(): void
    {
        $items = $this->model->getAll();
        View::render('admin/testimonials/index', compact('items'), 'admin');
    }

    public function create(): void
    {
        View::render('admin/testimonials/create', [], 'admin');
    }

    public function store(): void
    {
        Csrf::verify();
        $data = [
            'customer_name' => trim($_POST['customer_name'] ?? ''),
            'content'       => trim($_POST['content'] ?? ''),
            'rating'        => (int)($_POST['rating'] ?? 5),
            'sort_order'    => (int)($_POST['sort_order'] ?? 0),
            'is_active'     => isset($_POST['is_active']) ? 1 : 0,
        ];

        if (!empty($_FILES['screenshot']['name'])) {
            $screenshot = Upload::handle($_FILES['screenshot'], 'testimonials');
            if ($screenshot) $data['screenshot'] = $screenshot;
        }

        $this->model->create($data);
        Flash::set('success', 'Testimoni berhasil ditambahkan.');
        redirect('/admin/testimonials');
    }

    public function edit(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Testimoni tidak ditemukan.');
            redirect('/admin/testimonials');
        }
        View::render('admin/testimonials/edit', compact('item'), 'admin');
    }

    public function update(string $id): void
    {
        Csrf::verify();
        $data = [
            'customer_name' => trim($_POST['customer_name'] ?? ''),
            'content'       => trim($_POST['content'] ?? ''),
            'rating'        => (int)($_POST['rating'] ?? 5),
            'sort_order'    => (int)($_POST['sort_order'] ?? 0),
            'is_active'     => isset($_POST['is_active']) ? 1 : 0,
        ];

        if (!empty($_FILES['screenshot']['name'])) {
            $screenshot = Upload::handle($_FILES['screenshot'], 'testimonials');
            if ($screenshot) {
                $old = $this->model->find((int)$id);
                if ($old && $old['screenshot']) Upload::delete($old['screenshot']);
                $data['screenshot'] = $screenshot;
            }
        }

        $this->model->update((int)$id, $data);
        Flash::set('success', 'Testimoni berhasil diperbarui.');
        redirect('/admin/testimonials');
    }

    public function destroy(string $id): void
    {
        Csrf::verify();
        $item = $this->model->find((int)$id);
        if ($item && $item['screenshot']) Upload::delete($item['screenshot']);
        $this->model->delete((int)$id);
        Flash::set('success', 'Testimoni berhasil dihapus.');
        redirect('/admin/testimonials');
    }

    public function reorder(): void
    {
        $ids = json_decode(file_get_contents('php://input'), true)['ids'] ?? [];
        $this->model->updateSortOrder($ids);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }
}
