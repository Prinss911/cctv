<?php
namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\TestimonialModel;
use App\Helpers\{View, Flash, Csrf, Upload, Request};
use function trim;
use function array_filter;

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
            'customer_name' => trim(Request::post('customer_name', '')),
            'content'       => trim(Request::post('content', '')),
            'rating'        => (int)Request::post('rating', 5),
            'sort_order'    => (int)Request::post('sort_order', 0),
            'is_active'     => Request::has('is_active') ? 1 : 0,
        ];

        if (!empty(Request::file('screenshot')['name'] ?? '')) {
            $result = Upload::handle(Request::file('screenshot'), 'testimonials');
            if (isset($result['error'])) {
                Flash::set('error', $result['error']);
                redirect('/admin/testimonials/create');
                exit;
            }
            if (isset($result['path'])) {
                $data['screenshot'] = $result['path'];
            }
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
            exit;
        }
        View::render('admin/testimonials/edit', compact('item'), 'admin');
    }

    public function update(string $id): void
    {
        Csrf::verify();
        $data = [
            'customer_name' => trim(Request::post('customer_name', '')),
            'content'       => trim(Request::post('content', '')),
            'rating'        => (int)Request::post('rating', 5),
            'sort_order'    => (int)Request::post('sort_order', 0),
            'is_active'     => Request::has('is_active') ? 1 : 0,
        ];

        if (!empty(Request::file('screenshot')['name'] ?? '')) {
            $result = Upload::handle(Request::file('screenshot'), 'testimonials');
            if (isset($result['error'])) {
                Flash::set('error', $result['error']);
                redirect('/admin/testimonials/edit/' . $id);
                exit;
            }
            if (isset($result['path'])) {
                $old = $this->model->find((int)$id);
                if ($old && $old['screenshot']) Upload::delete($old['screenshot']);
                $data['screenshot'] = $result['path'];
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
        if (!$item) {
            Flash::set('error', 'Testimoni tidak ditemukan.');
            redirect('/admin/testimonials');
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

                // Delete screenshot file if exists (after successful DB commit)
                if ($item['screenshot']) {
                    try {
                        Upload::delete($item['screenshot']);
                    } catch (Exception $e) {
                        // Log file deletion error but don't affect transaction outcome
                        if (env('APP_DEBUG', false)) {
                            error_log('TestimonialController::destroy file deletion error: ' . $e->getMessage());
                        }
                        // Note: file orphan is acceptable per requirement
                    }
                }

                Flash::set('success', 'Testimoni berhasil dihapus.');
            } else {
                // Delete failed, rollback
                $db->rollBack();
                Flash::set('error', 'Gagal menghapus testimoni.');
                redirect('/admin/testimonials');
                return;
            }
        } catch (Exception $e) {
            // Rollback on any exception
            $db->rollBack();
            Flash::set('error', 'Terjadi kesalahan saat menghapus testimoni.');
            // Log the error
            if (env('APP_DEBUG', false)) {
                error_log('TestimonialController::destroy error: ' . $e->getMessage());
            }
        }

        redirect('/admin/testimonials');
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