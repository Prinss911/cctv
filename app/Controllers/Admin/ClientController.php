<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\ClientModel;
use App\Helpers\{View, Flash, Csrf, Upload};

class ClientController
{
    private ClientModel $model;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->model = new ClientModel();
    }

    public function index(): void
    {
        $items = $this->model->getAll();
        View::render('admin/clients/index', compact('items'), 'admin');
    }

    public function create(): void
    {
        View::render('admin/clients/create', [], 'admin');
    }

    public function store(): void
    {
        Csrf::verify();
        $logo = Upload::handle($_FILES['logo'] ?? [], 'clients');
        if (!$logo) {
            Flash::set('error', 'Logo wajib diupload.');
            redirect('/admin/clients/create');
        }

        $this->model->create([
            'name'       => trim($_POST['name'] ?? ''),
            'logo'       => $logo,
            'website'    => trim($_POST['website'] ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active'  => isset($_POST['is_active']) ? 1 : 0,
        ]);

        Flash::set('success', 'Client berhasil ditambahkan.');
        redirect('/admin/clients');
    }

    public function edit(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Client tidak ditemukan.');
            redirect('/admin/clients');
        }
        View::render('admin/clients/edit', compact('item'), 'admin');
    }

    public function update(string $id): void
    {
        Csrf::verify();
        $data = [
            'name'       => trim($_POST['name'] ?? ''),
            'website'    => trim($_POST['website'] ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active'  => isset($_POST['is_active']) ? 1 : 0,
        ];

        if (!empty($_FILES['logo']['name'])) {
            $logo = Upload::handle($_FILES['logo'], 'clients');
            if ($logo) {
                $old = $this->model->find((int)$id);
                if ($old && $old['logo']) Upload::delete($old['logo']);
                $data['logo'] = $logo;
            }
        }

        $this->model->update((int)$id, $data);
        Flash::set('success', 'Client berhasil diperbarui.');
        redirect('/admin/clients');
    }

    public function destroy(string $id): void
    {
        Csrf::verify();
        $item = $this->model->find((int)$id);
        if ($item && $item['logo']) Upload::delete($item['logo']);
        $this->model->delete((int)$id);
        Flash::set('success', 'Client berhasil dihapus.');
        redirect('/admin/clients');
    }

    public function reorder(): void
    {
        $ids = json_decode(file_get_contents('php://input'), true)['ids'] ?? [];
        $this->model->updateSortOrder($ids);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }
}
