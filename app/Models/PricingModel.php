<?php

namespace App\Models;

use PDO;

class PricingModel extends BaseModel
{
    protected string $table = 'pricing_packages';

    // Allowed columns for ORDER BY clause (prevents SQL injection)
    protected array $allowedOrderBy = ['id', 'sort_order', 'created_at', 'updated_at', 'name', 'camera_count', 'price', 'is_featured', 'is_active', 'brand_id'];

    /**
     * Eager load features for all given packages in ONE query (fixes N+1)
     * 
     * IN clause is SAFE: Uses prepared statement placeholders for each ID value.
     * The $placeholders string is generated dynamically based on the count of IDs,
     * but each value is bound separately via execute(), preventing SQL injection.
     */
    private function loadAllFeatures(array $packages): array
    {
        $packageIds = array_filter(array_column($packages, 'id'));
        if (empty($packageIds)) return $packages;

        // IN clause safety: Each ID is bound as a separate parameter via prepared statement
        $placeholders = implode(',', array_fill(0, count($packageIds), '?'));
        $stmt = $this->db->prepare(
            "SELECT * FROM pricing_features WHERE package_id IN ($placeholders) ORDER BY sort_order ASC"
        );
        $stmt->execute(array_values($packageIds));
        $allFeatures = $stmt->fetchAll();

        // Group features by package_id
        $featuresByPackage = [];
        foreach ($allFeatures as $feature) {
            $featuresByPackage[(int)$feature['package_id']][] = $feature;
        }

        // Attach features to each package
        foreach ($packages as &$pkg) {
            $pkg['features'] = $featuresByPackage[(int)$pkg['id']] ?? [];
        }

        return $packages;
    }

    /**
     * Get all packages with features, grouped by brand
     */
    public function getAllGroupedByBrand(): array
    {
        $packages = $this->getAll('brand_id ASC, sort_order ASC');
        $packages = $this->loadAllFeatures($packages);
        $grouped = [];
        foreach ($packages as $pkg) {
            $brandId = $pkg['brand_id'] ?? 0;
            $grouped[$brandId][] = $pkg;
        }
        return $grouped;
    }

    /**
     * Get active packages with features, grouped by brand
     */
    public function getActiveWithFeatures(): array
    {
        $packages = $this->getActive('brand_id ASC, sort_order ASC');
        $packages = $this->loadAllFeatures($packages);
        $grouped = [];
        foreach ($packages as $pkg) {
            $brandId = $pkg['brand_id'] ?? 0;
            $grouped[$brandId][] = $pkg;
        }
        return $grouped;
    }

    /**
     * Get active packages as flat array with brand info (for admin listing)
     */
    public function getActiveWithBrand(): array
    {
        $sql = "
            SELECT p.*, b.name as brand_name, b.slug as brand_slug
            FROM {$this->table} p
            LEFT JOIN pricing_brands b ON b.id = p.brand_id
            WHERE p.is_active = 1
            ORDER BY b.sort_order ASC, p.sort_order ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $packages = $stmt->fetchAll();
        return $this->loadAllFeatures($packages);
    }

    /**
     * Get all packages with brand info (for admin listing)
     */
    public function getAllWithBrand(string $orderBy = 'b.sort_order ASC, p.sort_order ASC'): array
    {
        // Validate orderBy against whitelist of allowed patterns
        $allowed = [
            'b.sort_order ASC', 'b.sort_order DESC',
            'p.sort_order ASC', 'p.sort_order DESC',
            'b.name ASC', 'b.name DESC',
            'p.price ASC', 'p.price DESC',
            'p.created_at ASC', 'p.created_at DESC',
        ];
        if (!in_array($orderBy, $allowed, true)) {
            $orderBy = 'b.sort_order ASC, p.sort_order ASC';
        }
        
        $sql = "SELECT p.*, b.name as brand_name, b.slug as brand_slug
            FROM {$this->table} p
            LEFT JOIN pricing_brands b ON b.id = p.brand_id
            ORDER BY $orderBy";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $packages = $stmt->fetchAll();
        return $this->loadAllFeatures($packages);
    }

    /**
     * Get packages by brand ID
     */
    public function getByBrand(int $brandId): array
    {
        $packages = $this->getAll('sort_order ASC');
        $packages = $this->loadAllFeatures($packages);
        $filtered = [];
        foreach ($packages as $pkg) {
            if (($pkg['brand_id'] ?? 0) === $brandId) {
                $filtered[] = $pkg;
            }
        }
        return $filtered;
    }

    /**
     * Get active packages by brand ID
     */
    public function getActiveByBrand(int $brandId): array
    {
        $packages = $this->getActive('sort_order ASC');
        $packages = $this->loadAllFeatures($packages);
        $filtered = [];
        foreach ($packages as $pkg) {
            if (($pkg['brand_id'] ?? 0) === $brandId) {
                $filtered[] = $pkg;
            }
        }
        return $filtered;
    }

    public function getFeatures(int $packageId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM pricing_features WHERE package_id = ? ORDER BY sort_order ASC");
        $stmt->execute([$packageId]);
        return $stmt->fetchAll();
    }

    public function syncFeatures(int $packageId, array $features): void
    {
        $this->db->prepare("DELETE FROM pricing_features WHERE package_id = ?")->execute([$packageId]);

        $stmt = $this->db->prepare("INSERT INTO pricing_features (package_id, feature, is_included, sort_order) VALUES (?, ?, ?, ?)");
        foreach ($features as $i => $feature) {
            if (trim($feature) === '') continue;
            $stmt->execute([$packageId, trim($feature), 1, $i + 1]);
        }
    }

    /**
     * Get brand for a package (belongsTo relationship)
     */
    public function getBrand(int $brandId): ?array
    {
        $brandModel = new PricingBrandModel();
        return $brandModel->getById($brandId);
    }

    /**
     * Eager load brands for multiple packages to avoid N+1
     * 
     * IN clause safety: Uses prepared statement placeholders for each brand ID.
     * Each ID is bound separately during execute(), preventing SQL injection.
     */
    public function loadBrands(array $packages): array
    {
        $brandIds = array_unique(array_filter(array_column($packages, 'brand_id')));
        if (empty($brandIds)) return $packages;

        $brandModel = new PricingBrandModel();
        // IN clause: Safely uses prepared statement - each ID is bound separately
        $placeholders = implode(',', array_fill(0, count($brandIds), '?'));
        $stmt = $this->db->prepare("SELECT * FROM pricing_brands WHERE id IN ($placeholders)");
        $stmt->execute(array_values($brandIds));
        $brands = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        foreach ($packages as &$pkg) {
            $brandId = $pkg['brand_id'] ?? 0;
            if ($brandId && isset($brands[$brandId])) {
                $pkg['brand'] = $brands[$brandId];
            }
        }
        return $packages;
    }
}