<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\SettingModel;
use App\Helpers\{View, Flash, Upload, Request};

class SettingsController
{
    private SettingModel $model;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->model = new SettingModel();
    }

    public function index(): void
    {
        $settings = $this->model->getAllGrouped();
        View::render('admin/settings/index', compact('settings'), 'admin');
    }

    public function update(): void
    {
        $all = $this->model->getAll('id ASC');
        foreach ($all as $setting) {
            $key = $setting['key'];

            if ($setting['type'] === 'image') {
                $fileKey = 'setting_' . $key;
                if (!empty(Request::file($fileKey)['name'] ?? '')) {
                    $result = Upload::handle(Request::file($fileKey), 'logo');
                    if (isset($result['path'])) {
                        if ($setting['value']) Upload::delete($setting['value']);
                        $this->model->updateByKey($key, $result['path']);
                    }
                }
                continue;
            }

            if (Request::has($key)) {
                $value = trim(Request::post($key));
                if ($key === 'maps_embed' && !empty($value)) {
                    // Auto-extract src URL from full iframe HTML
                    if (str_starts_with($value, '<iframe')) {
                        if (preg_match('#src="([^"]+)"#i', $value, $m)) {
                            $value = $m[1];
                        }
                    }
                    if (!preg_match('#^https?://(www\.)?(google\.com/maps|maps\.app\.goo\.gl|goo\.gl/maps)#i', $value)) {
                        continue;
                    }
                }
                $this->model->updateByKey($key, $value);
            }
        }

        Flash::set('success', 'Pengaturan berhasil disimpan.');
        redirect('/admin/settings');
        exit;
    }
}
