<?php
namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\ClientModel;
use App\Helpers\{View, Flash, Csrf, Upload, Request};
use function trim;
use function array_filter;

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
        $result = Upload::handle(Request::file('logo'), 'clients');
        
        if (isset($result['error'])) {
            Flash::set('error', $result['error']);
            redirect('/admin/clients/create');
            exit;
        }

        $this->model->create([
            'name'       => trim(Request::post('name', '')),
            'logo'       => $result['path'],
            'website'    => trim(Request::post('website', '')),
            'sort_order' => (int)Request::post('sort_order', 0),
            'is_active'  => Request::has('is_active') ? 1 : 0,
        ]);

        Flash::set('success', 'Client berhasil ditambahkan.');
        redirect('/admin/clients');
        exit;
    }

    public function edit(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Client tidak ditemukan.');
            redirect('/admin/clients');
            exit;
        }
        View::render('admin/clients/edit', compact('item'), 'admin');
    }

    public function update(string $id): void
    {
        Csrf::verify();
        $data = [
            'name'       => trim(Request::post('name', '')),
            'website'    => trim(Request::post('website', '')),
            'sort_order' => (int)Request::post('sort_order', 0),
            'is_active'  => Request::has('is_active') ? 1 : 0,
        ];

        if (!empty(Request::file('logo')['name'] ?? '')) {
            $result = Upload::handle(Request::file('logo'), 'clients');
            if (isset($result['path'])) {
                $old = $this->model->find((int)$id);
                if ($old && $old['logo']) Upload::delete($old['logo']);
                $data['logo'] = $result['path'];
            } elseif (isset($result['error'])) {
                Flash::set('error', $result['error']);
                redirect('/admin/clients/edit/' . $id);
                exit;
            }
        }

        $this->model->update((int)$id, $data);
        Flash::set('success', 'Client berhasil diperbarui.');
        redirect('/admin/clients');
        exit;
    }

    public function destroy(string $id): void
    {
        Csrf::verify();
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Client tidak ditemukan.');
            redirect('/admin/clients');
            return;
        }

        // Begin transaction
        $db = \App\Models\Database::getInstance();
        $db->beginTransaction();

        try {
            // Delete from database first
            $result = $this->model->delete((int)$id);

            if ($result) {
                // Commit transaction
                $db->commit();

                // Delete logo file if exists (after successful DB commit)
                if ($item['logo']) {
                    try {
                        Upload::delete($item['logo']);
                    } catch (Exception $e) {
                        // Log file deletion error but don't affect transaction outcome
                        error_log('ClientController::destroy file deletion error: ' . $e->getMessage());
                        // Note: file orphan is acceptable as per requirement
                    }
                }

                Flash::set('success', 'Client berhasil dihapus.');
            } else {
                // Delete failed, rollback
                $db->rollBack();
                Flash::set('error', 'Gagal menghapus client.');
                redirect('/admin/clients');
                return;
            }
        } catch (Exception $e) {
            // Rollback on any exception
            $db->rollBack();
            Flash::set('error', 'Terjadi kesalahan saat menghapus client.');
            error_log('ClientController::destroy error: ' . $e->getMessage());
            redirect('/admin/clients');
            return;
        }

        redirect('/admin/clients');
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