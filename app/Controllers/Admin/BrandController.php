<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\PricingBrandModel;
use App\Models\PricingModel;
use App\Helpers\{View, Flash, Csrf, Upload};

class BrandController
{
    private PricingBrandModel $brandModel;
    private PricingModel $pricingModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->brandModel = new PricingBrandModel();
        $this->pricingModel = new PricingModel();
    }

    public function index(): void
    {
        $brands = $this->brandModel->getWithPackageCount();
        View::render('admin/brand/index', compact('brands'), 'admin');
    }

    public function create(): void
    {
        View::render('admin/brand/create', [], 'admin');
    }

    public function store(): void
    {
        Csrf::verify();
        
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $logo = Upload::handle($_FILES['logo'] ?? [], 'logo');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        // Auto-generate slug if empty
        if (empty($slug)) {
            $slug = $this->brandModel->generateSlug($name);
        } elseif ($this->brandModel->slugExists($slug)) {
            Flash::set('error', 'Slug sudah digunakan. Gunakan slug lain.');
            redirect('/admin/brands/create');
            return;
        }

        // Validate upload result
        if ($logo === null && isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
            Flash::set('error', 'Upload logo gagal. Periksa ukuran dan format file.');
            redirect('/admin/brands/create');
            return;
        }

        $id = $this->brandModel->create([
            'name' => $name,
            'slug' => $slug,
            'logo' => $logo,
            'is_active' => $isActive,
            'sort_order' => $sortOrder,
        ]);

        if ($id) {
            Flash::set('success', 'Brand berhasil ditambahkan.');
            redirect('/admin/brands');
        } else {
            Flash::set('error', 'Gagal menambahkan brand.');
            redirect('/admin/brands/create');
        }
    }

    public function edit(string $id): void
    {
        $brand = $this->brandModel->getById((int)$id);
        if (!$brand) {
            Flash::set('error', 'Brand tidak ditemukan.');
            redirect('/admin/brands');
        }
        View::render('admin/brand/edit', compact('brand'), 'admin');
    }

    public function update(string $id): void
    {
        Csrf::verify();
        
        $brand = $this->brandModel->getById((int)$id);
        if (!$brand) {
            Flash::set('error', 'Brand tidak ditemukan.');
            redirect('/admin/brands');
        }

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        // Check slug uniqueness (excluding current brand)
        if ($slug !== $brand['slug'] && $this->brandModel->slugExists($slug, (int)$id)) {
            Flash::set('error', 'Slug sudah digunakan. Gunakan slug lain.');
            redirect("/admin/brands/{$id}/edit");
            return;
        }

        // Handle logo upload
        $uploadLogo = Upload::handle($_FILES['logo'] ?? [], 'logo');
        if ($uploadLogo === null && isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
            Flash::set('error', 'Upload logo gagal. Periksa ukuran dan format file.');
            redirect("/admin/brands/{$id}/edit");
            return;
        }

        // Handle logo deletion for new upload
        if ($uploadLogo !== null) {
            // Delete old logo if exists
            if ($brand['logo']) {
                Upload::delete($brand['logo']);
            }
        }

        $result = $this->brandModel->update((int)$id, [
            'name' => $name,
            'slug' => $slug,
            'logo' => $uploadLogo !== null ? $uploadLogo : ($brand['logo'] ?? ''),
            'is_active' => $isActive,
            'sort_order' => $sortOrder,
        ]);

        if ($result) {
            Flash::set('success', 'Brand berhasil diperbarui.');
            redirect('/admin/brands');
        } else {
            Flash::set('error', 'Gagal memperbarui brand.');
            redirect("/admin/brands/{$id}/edit");
        }
    }

    public function destroy(string $id): void
    {
        Csrf::verify();
        
        $brand = $this->brandModel->getById((int)$id);
        if (!$brand) {
            Flash::set('error', 'Brand tidak ditemukan.');
            redirect('/admin/brands');
        }

        // Check if brand has packages
        $packages = $this->pricingModel->getByBrand((int)$id);
        if (!empty($packages)) {
            Flash::set('error', 'Tidak dapat menghapus brand yang masih memiliki paket harga. Hapus atau pindahkan paket terlebih dahulu.');
            redirect('/admin/brands');
            return;
        }

        // Delete logo file if exists
        if ($brand['logo']) {
            Upload::delete($brand['logo']);
        }

        $this->brandModel->delete((int)$id);
        Flash::set('success', 'Brand berhasil dihapus.');
        redirect('/admin/brands');
    }

    public function reorder(): void
    {
        $ids = json_decode(file_get_contents('php://input'), true)['ids'] ?? [];
        $updates = [];
        foreach ($ids as $order => $id) {
            $updates[] = ['id' => $id, 'sort_order' => $order + 1];
        }
        $this->brandModel->updateSortOrder($updates);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }
}
