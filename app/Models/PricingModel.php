<?php

namespace App\Models;

use PDO;

class PricingModel extends BaseModel
{
    protected string $table = 'pricing_packages';

    /**
     * Get all packages with features, grouped by brand
     */
    public function getAllGroupedByBrand(): array
    {
        $packages = $this->getAll('brand_id ASC, sort_order ASC');
        $grouped = [];
        foreach ($packages as $pkg) {
            $pkg['features'] = $this->getFeatures($pkg['id']);
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
        $grouped = [];
        foreach ($packages as $pkg) {
            $pkg['features'] = $this->getFeatures($pkg['id']);
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
        foreach ($packages as &$pkg) {
            $pkg['features'] = $this->getFeatures($pkg['id']);
        }
        return $packages;
    }

    /**
     * Get all packages with brand info (for admin listing)
     */
    public function getAllWithBrand(string $orderBy = 'b.sort_order ASC, p.sort_order ASC'): array
    {
        $sql = "
            SELECT p.*, b.name as brand_name, b.slug as brand_slug
            FROM {$this->table} p
            LEFT JOIN pricing_brands b ON b.id = p.brand_id
            ORDER BY $orderBy
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $packages = $stmt->fetchAll();
        foreach ($packages as &$pkg) {
            $pkg['features'] = $this->getFeatures($pkg['id']);
        }
        return $packages;
    }

    /**
     * Get packages by brand ID
     */
    public function getByBrand(int $brandId): array
    {
        $packages = $this->getAll('sort_order ASC');
        $filtered = [];
        foreach ($packages as $pkg) {
            if (($pkg['brand_id'] ?? 0) == $brandId) {
                $pkg['features'] = $this->getFeatures($pkg['id']);
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
        $filtered = [];
        foreach ($packages as $pkg) {
            if (($pkg['brand_id'] ?? 0) == $brandId) {
                $pkg['features'] = $this->getFeatures($pkg['id']);
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
}
