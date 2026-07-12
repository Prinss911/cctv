<?php
namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\GalleryModel;
use App\Helpers\{View, Flash, Csrf, Upload, Request};
use function trim;
use function array_filter;

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
        $imageUrl = trim(Request::post('image_url', ''));
        if (!empty($imageUrl)) {
            $result = Upload::handleFromUrl($imageUrl, 'gallery');
        } else {
            $result = Upload::handle(Request::file('image'), 'gallery');
        }
        
        if (isset($result['error'])) {
            Flash::set('error', $result['error']);
            redirect('/admin/gallery/create');
            exit;
        }

        $this->model->create([
            'title'      => trim(Request::post('title', '')),
            'image'      => $result['path'],
            'category'   => trim(Request::post('category', 'indoor')),
            'sort_order' => (int)Request::post('sort_order', 0),
            'is_active'  => Request::has('is_active') ? 1 : 0,
        ]);

        Flash::set('success', 'Foto berhasil ditambahkan.');
        redirect('/admin/gallery');
        exit;
    }

    public function edit(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Foto tidak ditemukan.');
            redirect('/admin/gallery');
            exit;
        }
        View::render('admin/gallery/edit', compact('item'), 'admin');
    }

    public function update(string $id): void
    {
        Csrf::verify();
        $data = [
            'title'      => trim(Request::post('title', '')),
            'category'   => trim(Request::post('category', 'indoor')),
            'sort_order' => (int)Request::post('sort_order', 0),
            'is_active'  => Request::has('is_active') ? 1 : 0,
        ];

        $imageUrl = trim(Request::post('image_url', ''));
        if (!empty($imageUrl)) {
            $result = Upload::handleFromUrl($imageUrl, 'gallery');
            if (isset($result['path'])) {
                $old = $this->model->find((int)$id);
                if ($old && $old['image']) Upload::delete($old['image']);
                $data['image'] = $result['path'];
            } elseif (isset($result['error'])) {
                Flash::set('error', $result['error']);
                redirect('/admin/gallery/edit/' . $id);
                exit;
            }
        } elseif (!empty(Request::file('image')['name'] ?? '')) {
            $result = Upload::handle(Request::file('image'), 'gallery');
            if (isset($result['path'])) {
                $old = $this->model->find((int)$id);
                if ($old && $old['image']) Upload::delete($old['image']);
                $data['image'] = $result['path'];
            } elseif (isset($result['error'])) {
                Flash::set('error', $result['error']);
                redirect('/admin/gallery/edit/' . $id);
                exit;
            }
        }

        $this->model->update((int)$id, $data);
        Flash::set('success', 'Foto berhasil diperbarui.');
        redirect('/admin/gallery');
        exit;
    }

    public function destroy(string $id): void
    {
        Csrf::verify();
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Foto tidak ditemukan.');
            redirect('/admin/gallery');
            return;
        }

        // Begin transaction
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            // Delete from database first
            $result = $this->model->delete((int)$id);

            if ($result) {
                // Commit transaction
                $db->commit();

                // Delete image file if exists (after successful DB commit)
                if ($item['image']) {
                    try {
                        Upload::delete($item['image']);
                    } catch (Exception $e) {
                        // Log file deletion error but don't affect transaction outcome
                        if (env('APP_DEBUG', false)) {
                            error_log('GalleryController::destroy file deletion error: ' . $e->getMessage());
                        }
                        // Note: file orphan is acceptable per requirement
                    }
                }

                Flash::set('success', 'Foto berhasil dihapus.');
            } else {
                // Delete failed, rollback
                $db->rollBack();
                Flash::set('error', 'Gagal menghapus foto.');
            }
        } catch (Exception $e) {
            // Rollback on any exception
            $db->rollBack();
            Flash::set('error', 'Terjadi kesalahan saat menghapus foto.');
            // Log the error
            if (env('APP_DEBUG', false)) {
                error_log('GalleryController::destroy error: ' . $e->getMessage());
            }
        }

        redirect('/admin/gallery');
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