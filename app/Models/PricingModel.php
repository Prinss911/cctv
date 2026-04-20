<?php

namespace App\Models;

use PDO;

class PricingModel extends BaseModel
{
    protected string $table = 'pricing_packages';

    public function getAllWithFeatures(string $orderBy = 'sort_order ASC'): array
    {
        $packages = $this->getAll($orderBy);
        foreach ($packages as &$pkg) {
            $pkg['features'] = $this->getFeatures($pkg['id']);
        }
        return $packages;
    }

    public function getActiveWithFeatures(): array
    {
        $packages = $this->getActive();
        foreach ($packages as &$pkg) {
            $pkg['features'] = $this->getFeatures($pkg['id']);
        }
        return $packages;
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
