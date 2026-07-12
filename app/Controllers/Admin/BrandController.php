<?php
namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\PricingBrandModel;
use App\Models\PricingModel;
use App\Models\Database;
use App\Helpers\{View, Flash, Csrf, Upload, Request};
use function trim;
use function array_filter;

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
        
        $name = trim(Request::post('name', ''));
        $slug = trim(Request::post('slug', ''));
        $isActive = Request::has('is_active') ? 1 : 0;
        $sortOrder = (int)Request::post('sort_order', 0);

        // Auto-generate slug if empty
        if (empty($slug)) {
            $slug = $this->brandModel->generateSlug($name);
        } elseif ($this->brandModel->slugExists($slug)) {
            Flash::set('error', 'Slug sudah digunakan. Gunakan slug lain.');
            redirect('/admin/brands/create');
            return;
        }

        // Handle logo upload (optional)
        $logoUrl = trim(Request::post('logo_url', ''));
        $logoPath = null;

        if (!empty($logoUrl)) {
            $result = Upload::handleFromUrl($logoUrl, 'logo');
            if (isset($result['error'])) {
                Flash::set('error', $result['error']);
                redirect('/admin/brands/create');
                return;
            }
            $logoPath = $result['path'];
        } else {
            $result = Upload::handle(Request::file('logo'), 'logo');
            
            if (isset($result['error'])) {
                // Only show error if a file was actually selected but failed
                if (Request::file('logo') !== null && Request::file('logo')['error'] !== UPLOAD_ERR_NO_FILE) {
                    Flash::set('error', $result['error']);
                    redirect('/admin/brands/create');
                    return;
                }
            } elseif (isset($result['path'])) {
                $logoPath = $result['path'];
            }
        }

        $id = $this->brandModel->create([
            'name'       => $name,
            'slug'       => $slug,
            'logo'       => $logoPath,
            'is_active'  => $isActive,
            'sort_order' => $sortOrder,
        ]);

        if ($id) {
            Flash::set('success', 'Brand berhasil ditambahkan.');
            redirect('/admin/brands');
            exit;
        } else {
            Flash::set('error', 'Gagal menambahkan brand.');
            redirect('/admin/brands/create');
            exit;
        }
    }

    public function edit(string $id): void
    {
        $brand = $this->brandModel->getById((int)$id);
        if (!$brand) {
            Flash::set('error', 'Brand tidak ditemukan.');
            redirect('/admin/brands');
            exit;
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

        $name = trim(Request::post('name', ''));
        $slug = trim(Request::post('slug', ''));
        $isActive = Request::has('is_active') ? 1 : 0;
        $sortOrder = (int)Request::post('sort_order', 0);

        // Check slug uniqueness (excluding current brand)
        if ($slug !== $brand['slug'] && $this->brandModel->slugExists($slug, (int)$id)) {
            Flash::set('error', 'Slug sudah digunakan. Gunakan slug lain.');
            redirect("/admin/brands/{$id}/edit");
            return;
        }

        // Handle logo upload (optional)
        $logoUrl = trim(Request::post('logo_url', ''));
        $uploadLogo = null;

        if (!empty($logoUrl)) {
            $result = Upload::handleFromUrl($logoUrl, 'logo');
            if (isset($result['error'])) {
                Flash::set('error', $result['error']);
                redirect("/admin/brands/{$id}/edit");
                return;
            }
            $uploadLogo = $result['path'];
        } else {
            $result = Upload::handle(Request::file('logo'), 'logo');
            
            if (isset($result['error'])) {
                if (Request::file('logo') !== null && Request::file('logo')['error'] !== UPLOAD_ERR_NO_FILE) {
                    Flash::set('error', $result['error']);
                    redirect("/admin/brands/{$id}/edit");
                    return;
                }
            } elseif (isset($result['path'])) {
                $uploadLogo = $result['path'];
            }
        }

        // Determine final logo path
        $finalLogo = $brand['logo'];
        if ($uploadLogo !== null) {
            // Delete old logo if exists
            if ($brand['logo']) {
                Upload::delete($brand['logo']);
            }
            $finalLogo = $uploadLogo;
        }

        $result = $this->brandModel->update((int)$id, [
            'name'       => $name,
            'slug'       => $slug,
            'logo'       => $finalLogo,
            'is_active'  => $isActive,
            'sort_order' => $sortOrder,
        ]);

        if ($result) {
            Flash::set('success', 'Brand berhasil diperbarui.');
            redirect('/admin/brands');
            exit;
        } else {
            Flash::set('error', 'Gagal memperbarui brand.');
            redirect("/admin/brands/{$id}/edit");
            exit;
        }
    }

    public function destroy(string $id): void
    {
        Csrf::verify();
        
        $brand = $this->brandModel->getById((int)$id);
        if (!$brand) {
            Flash::set('error', 'Brand tidak ditemukan.');
            redirect('/admin/brands');
            exit;
        }

        // Check if brand has packages
        $packages = $this->pricingModel->getByBrand((int)$id);
        if (!empty($packages)) {
            Flash::set('error', 'Tidak dapat menghapus brand yang masih memiliki paket harga. Hapus atau pindahkan paket terlebih dahulu.');
            redirect('/admin/brands');
            return;
        }
    
        // Begin transaction
        $db = Database::getInstance();
        $db->beginTransaction();
        
        try {
            // Delete from database first
            $result = $this->brandModel->delete((int)$id);
            
            if ($result) {
                // Commit transaction
                $db->commit();
                
                // Delete logo file if exists (after successful DB commit)
                if ($brand['logo']) {
                    try {
                        Upload::delete($brand['logo']);
                    } catch (Exception $e) {
                        // Log file deletion error but don't affect transaction outcome
                        error_log('BrandController::destroy file deletion error: ' . $e->getMessage());
                        // Note: file orphan is acceptable as per requirement
                    }
                }
                
                Flash::set('success', 'Brand berhasil dihapus.');
            } else {
                // Delete failed, rollback
                $db->rollBack();
                Flash::set('error', 'Gagal menghapus brand.');
                redirect('/admin/brands');
                return;
            }
        } catch (Exception $e) {
            // Rollback on any exception
            $db->rollBack();
            Flash::set('error', 'Terjadi kesalahan saat menghapus brand.');
            error_log('BrandController::destroy error: ' . $e->getMessage());
            redirect('/admin/brands');
            return;
        }
        
        redirect('/admin/brands');
        exit;
    }

    public function reorder(): void
    {
        Csrf::verify();
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