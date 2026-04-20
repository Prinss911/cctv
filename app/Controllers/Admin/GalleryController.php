<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\GalleryModel;
use App\Helpers\{View, Flash, Csrf, Upload};

class GalleryController
{
    private GalleryModel $model;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->model = new GalleryModel();
    }

    public function index(): void
    {
        $items = $this->model->getAll();
        View::render('admin/gallery/index', compact('items'), 'admin');
    }

    public function create(): void
    {
        View::render('admin/gallery/create', [], 'admin');
    }

    public function store(): void
    {
        Csrf::verify();
        $image = Upload::handle($_FILES['image'] ?? [], 'gallery');
        if (!$image) {
            Flash::set('error', 'Gambar wajib diupload.');
            redirect('/admin/gallery/create');
        }

        $this->model->create([
            'title'      => trim($_POST['title'] ?? ''),
            'image'      => $image,
            'category'   => trim($_POST['category'] ?? 'instalasi'),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active'  => isset($_POST['is_active']) ? 1 : 0,
        ]);

        Flash::set('success', 'Foto berhasil ditambahkan.');
        redirect('/admin/gallery');
    }

    public function edit(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Foto tidak ditemukan.');
            redirect('/admin/gallery');
        }
        View::render('admin/gallery/edit', compact('item'), 'admin');
    }

    public function update(string $id): void
    {
        Csrf::verify();
        $data = [
            'title'      => trim($_POST['title'] ?? ''),
            'category'   => trim($_POST['category'] ?? 'instalasi'),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active'  => isset($_POST['is_active']) ? 1 : 0,
        ];

        if (!empty($_FILES['image']['name'])) {
            $image = Upload::handle($_FILES['image'], 'gallery');
            if ($image) {
                $old = $this->model->find((int)$id);
                if ($old && $old['image']) Upload::delete($old['image']);
                $data['image'] = $image;
            }
        }

        $this->model->update((int)$id, $data);
        Flash::set('success', 'Foto berhasil diperbarui.');
        redirect('/admin/gallery');
    }

    public function destroy(string $id): void
    {
        Csrf::verify();
        $item = $this->model->find((int)$id);
        if ($item && $item['image']) Upload::delete($item['image']);
        $this->model->delete((int)$id);
        Flash::set('success', 'Foto berhasil dihapus.');
        redirect('/admin/gallery');
    }

    public function reorder(): void
    {
        $ids = json_decode(file_get_contents('php://input'), true)['ids'] ?? [];
        $this->model->updateSortOrder($ids);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }
}
