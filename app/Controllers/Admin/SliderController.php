<?php
namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\SliderModel;
use App\Helpers\{View, Flash, Csrf, Upload, Request};
use function trim;
use function array_filter;

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
        $result = Upload::handle(Request::file('image'), 'sliders');
        
        if (isset($result['error'])) {
            Flash::set('error', $result['error']);
            redirect('/admin/sliders/create');
            exit;
        }

        $this->model->create([
            'title'       => trim(Request::post('title', '')),
            'subtitle'    => trim(Request::post('subtitle', '')),
            'tag'          => trim(Request::post('tag', 'Keamanan Terpercaya')),
            'image'       => $result['path'],
            'button_text' => trim(Request::post('button_text', 'Hubungi Kami')),
            'button_url'  => trim(Request::post('button_url', '#')),
            'sort_order'  => (int)Request::post('sort_order', 0),
            'is_active'   => Request::has('is_active') ? 1 : 0,
        ]);

        Flash::set('success', 'Slider berhasil ditambahkan.');
        redirect('/admin/sliders');
        exit;
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
            'title'       => trim(Request::post('title', '')),
            'subtitle'    => trim(Request::post('subtitle', '')),
            'tag'          => trim(Request::post('tag', 'Keamanan Terpercaya')),
            'button_text' => trim(Request::post('button_text', 'Hubungi Kami')),
            'button_url'  => trim(Request::post('button_url', '#')),
            'sort_order'  => (int)Request::post('sort_order', 0),
            'is_active'   => Request::has('is_active') ? 1 : 0,
        ];

        if (!empty(Request::file('image')['name'] ?? '')) {
            $result = Upload::handle(Request::file('image'), 'sliders');
            if (isset($result['path'])) {
                $old = $this->model->find((int)$id);
                if ($old && $old['image']) Upload::delete($old['image']);
                $data['image'] = $result['path'];
            } elseif (isset($result['error'])) {
                Flash::set('error', $result['error']);
                redirect('/admin/sliders/edit/' . $id);
                exit;
            }
        }

        $this->model->update((int)$id, $data);
        Flash::set('success', 'Slider berhasil diperbarui.');
        redirect('/admin/sliders');
        exit;
    }

    public function destroy(string $id): void
    {
        Csrf::verify();
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Slider tidak ditemukan.');
            redirect('/admin/sliders');
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
                            error_log('SliderController::destroy file deletion error: ' . $e->getMessage());
                        }
                        // Note: file orphan is acceptable as per requirement
                    }
                }

                Flash::set('success', 'Slider berhasil dihapus.');
            } else {
                // Delete failed, rollback
                $db->rollBack();
                Flash::set('error', 'Gagal menghapus slider.');
            }
        } catch (Exception $e) {
            // Rollback on any exception
            $db->rollBack();
            Flash::set('error', 'Terjadi kesalahan saat menghapus slider.');
            // Log the error
            if (env('APP_DEBUG', false)) {
                error_log('SliderController::destroy error: ' . $e->getMessage());
            }
        }

        redirect('/admin/sliders');
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