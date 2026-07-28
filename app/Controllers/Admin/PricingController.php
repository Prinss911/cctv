<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\PricingModel;
use App\Models\PricingBrandModel;
use App\Helpers\{View, Flash, Request};
use function trim;
use function array_filter;

class PricingController
{
    private PricingModel $model;
    private PricingBrandModel $brandModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->model = new PricingModel();
        $this->brandModel = new PricingBrandModel();
    }

    public function index(): void
    {
        $items = $this->model->getAllWithBrand();
        $brands = $this->brandModel->getAll();
        View::render('admin/pricing/index', compact('items', 'brands'), 'admin');
    }

    public function create(): void
    {
        $brands = $this->brandModel->getActive();
        View::render('admin/pricing/create', compact('brands'), 'admin');
    }

    public function store(): void
    {
        // Validate brand_id if provided
        $brandId = !empty(Request::post('brand_id')) ? (int)Request::post('brand_id') : null;
        if ($brandId !== null) {
            $brand = $this->brandModel->find($brandId);
            if (!$brand) {
                Flash::set('error', 'Brand tidak ditemukan.');
                redirect('/admin/pricing/create');
                exit;
            }
        }

        $cameraCount = (int)Request::post('camera_count', 0);
        $price = (int)Request::post('price', 0);
        $priceOriginal = (int)Request::post('price_original', 0);

        // Validate numeric fields >= 0
        if ($cameraCount < 0) {
            Flash::set('error', 'Jumlah kamera tidak boleh negatif.');
            redirect('/admin/pricing/create');
            exit;
        }
        if ($price < 0) {
            Flash::set('error', 'Harga tidak boleh negatif.');
            redirect('/admin/pricing/create');
            exit;
        }
        if ($priceOriginal < 0) {
            Flash::set('error', 'Harga asli tidak boleh negatif.');
            redirect('/admin/pricing/create');
            exit;
        }

        $data = [
            'name'              => trim(Request::post('name', '')),
            'camera_count'      => $cameraCount,
            'price'             => $price,
            'price_original'    => $priceOriginal,
            'description'       => trim(Request::post('description', '')),
            'whatsapp_message'  => trim(Request::post('whatsapp_message', '')),
            'is_featured'       => Request::has('is_featured') ? 1 : 0,
            'is_active'         => Request::has('is_active') ? 1 : 0,
            'sort_order'        => (int)Request::post('sort_order', 0),
            'brand_id'          => $brandId,
        ];

        $id = $this->model->create($data);

        $features = array_filter(explode("\n", Request::post('features', '')));
        $this->model->syncFeatures($id, $features);

        Flash::set('success', 'Paket berhasil ditambahkan.');
        redirect('/admin/pricing');
        exit;
    }

    public function edit(string $id): void
    {
        $item = $this->model->find((int)$id);
        if (!$item) {
            Flash::set('error', 'Paket tidak ditemukan.');
            redirect('/admin/pricing');
            exit;
        }
        $item['features'] = $this->model->getFeatures((int)$id);
        $brands = $this->brandModel->getActive();
        View::render('admin/pricing/edit', compact('item', 'brands'), 'admin');
    }

    public function update(string $id): void
    {
        // Validate brand_id if provided
        $brandId = !empty(Request::post('brand_id')) ? (int)Request::post('brand_id') : null;
        if ($brandId !== null) {
            $brand = $this->brandModel->find($brandId);
            if (!$brand) {
                Flash::set('error', 'Brand tidak ditemukan.');
                redirect('/admin/pricing/edit/'.$id);
                return;
            }
        }
        
        $this->model->update((int)$id, [
            'name'              => trim(Request::post('name', '')),
            'camera_count'      => (int)Request::post('camera_count', 0),
            'price'             => (int)Request::post('price', 0),
            'price_original'    => (int)Request::post('price_original', 0),
            'description'       => trim(Request::post('description', '')),
            'whatsapp_message'  => trim(Request::post('whatsapp_message', '')),
            'is_featured'       => Request::has('is_featured') ? 1 : 0,
            'is_active'         => Request::has('is_active') ? 1 : 0,
            'sort_order'        => (int)Request::post('sort_order', 0),
            'brand_id'          => $brandId,
        ]);

        $features = array_filter(explode("\n", Request::post('features', '')));
        $this->model->syncFeatures((int)$id, $features);

        Flash::set('success', 'Paket berhasil diperbarui.');
        redirect('/admin/pricing');
        exit;
    }

    public function destroy(string $id): void
    {
        $this->model->delete((int)$id);
        Flash::set('success', 'Paket berhasil dihapus.');
        redirect('/admin/pricing');
        exit;
    }

    public function reorder(): void
    {
        $ids = json_decode(file_get_contents('php://input'), true)['ids'] ?? [];
        $this->model->updateSortOrder($ids);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }
}