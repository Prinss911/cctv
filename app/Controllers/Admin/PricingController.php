<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\PricingModel;
use App\Helpers\{View, Flash, Csrf};

class PricingController
{
    private PricingModel $model;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->model = new PricingModel();
    }

    public function index(): void
    {
        $items = $this->model->getAllWithFeatures();
        View::render('admin/pricing/index', compact('items'), 'admin');
    }

    public function create(): void
    {
        View::render('admin/pricing/create', [], 'admin');
    }

    public function store(): void
    {
        Csrf::verify();
        $id = $this->model->create([
            'name'              => trim($_POST['name'] ?? ''),
            'camera_count'      => (int)($_POST['camera_count'] ?? 0),
            'price'             => (int)($_POST['price'] ?? 0),
            'price_original'    => (int)($_POST['price_original'] ?? 0),
            'description'       => trim($_POST['description'] ?? ''),
            'whatsapp_message'  => trim($_POST['whatsapp_message'] ?? ''),
            'is_featured'       => isset($_POST['is_featured']) ? 1 : 0,
            'is_active'         => isset($_POST['is_active']) ? 1 : 0,
            'sort_order'        => (int)($_POST['sort_order'] ?? 0),
        ]);

        $features = array_filter(explode("\n", $_POST['features'] ?? ''));
        $this->model->syncFeatures($id, $features);

        Flash::set('success', 'Paket berhasil ditambahkan.');
        redirect('/admin/pricing');
    }

    public function edit(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Paket tidak ditemukan.');
            redirect('/admin/pricing');
        }
        $item['features'] = $this->model->getFeatures((int)$id);
        View::render('admin/pricing/edit', compact('item'), 'admin');
    }

    public function update(string $id): void
    {
        Csrf::verify();
        $this->model->update((int)$id, [
            'name'              => trim($_POST['name'] ?? ''),
            'camera_count'      => (int)($_POST['camera_count'] ?? 0),
            'price'             => (int)($_POST['price'] ?? 0),
            'price_original'    => (int)($_POST['price_original'] ?? 0),
            'description'       => trim($_POST['description'] ?? ''),
            'whatsapp_message'  => trim($_POST['whatsapp_message'] ?? ''),
            'is_featured'       => isset($_POST['is_featured']) ? 1 : 0,
            'is_active'         => isset($_POST['is_active']) ? 1 : 0,
            'sort_order'        => (int)($_POST['sort_order'] ?? 0),
        ]);

        $features = array_filter(explode("\n", $_POST['features'] ?? ''));
        $this->model->syncFeatures((int)$id, $features);

        Flash::set('success', 'Paket berhasil diperbarui.');
        redirect('/admin/pricing');
    }

    public function destroy(string $id): void
    {
        Csrf::verify();
        $this->model->delete((int)$id);
        Flash::set('success', 'Paket berhasil dihapus.');
        redirect('/admin/pricing');
    }

    public function reorder(): void
    {
        $ids = json_decode(file_get_contents('php://input'), true)['ids'] ?? [];
        $this->model->updateSortOrder($ids);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }
}
