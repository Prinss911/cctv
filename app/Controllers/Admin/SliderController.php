<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\SliderModel;
use App\Helpers\{View, Flash, Csrf, Upload};

class SliderController
{
    private SliderModel $model;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->model = new SliderModel();
    }

    public function index(): void
    {
        $items = $this->model->getAll();
        View::render('admin/sliders/index', compact('items'), 'admin');
    }

    public function create(): void
    {
        View::render('admin/sliders/create', [], 'admin');
    }

    public function store(): void
    {
        Csrf::verify();
        $image = Upload::handle($_FILES['image'] ?? [], 'sliders');
        if (!$image) {
            Flash::set('error', 'Gambar wajib diupload.');
            redirect('/admin/sliders/create');
        }

        $this->model->create([
            'title'       => trim($_POST['title'] ?? ''),
            'subtitle'    => trim($_POST['subtitle'] ?? ''),
            'image'       => $image,
            'button_text' => trim($_POST['button_text'] ?? 'Hubungi Kami'),
            'button_url'  => trim($_POST['button_url'] ?? '#'),
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
        ]);

        Flash::set('success', 'Slider berhasil ditambahkan.');
        redirect('/admin/sliders');
    }

    public function edit(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Slider tidak ditemukan.');
            redirect('/admin/sliders');
        }
        View::render('admin/sliders/edit', compact('item'), 'admin');
    }

    public function update(string $id): void
    {
        Csrf::verify();
        $data = [
            'title'       => trim($_POST['title'] ?? ''),
            'subtitle'    => trim($_POST['subtitle'] ?? ''),
            'button_text' => trim($_POST['button_text'] ?? 'Hubungi Kami'),
            'button_url'  => trim($_POST['button_url'] ?? '#'),
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
        ];

        if (!empty($_FILES['image']['name'])) {
            $image = Upload::handle($_FILES['image'], 'sliders');
            if ($image) {
                $old = $this->model->find((int)$id);
                if ($old && $old['image']) Upload::delete($old['image']);
                $data['image'] = $image;
            }
        }

        $this->model->update((int)$id, $data);
        Flash::set('success', 'Slider berhasil diperbarui.');
        redirect('/admin/sliders');
    }

    public function destroy(string $id): void
    {
        Csrf::verify();
        $item = $this->model->find((int)$id);
        if ($item && $item['image']) Upload::delete($item['image']);
        $this->model->delete((int)$id);
        Flash::set('success', 'Slider berhasil dihapus.');
        redirect('/admin/sliders');
    }

    public function reorder(): void
    {
        $ids = json_decode(file_get_contents('php://input'), true)['ids'] ?? [];
        $this->model->updateSortOrder($ids);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }
}
