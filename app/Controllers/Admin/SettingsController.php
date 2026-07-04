<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\SettingModel;
use App\Helpers\{View, Flash, Csrf, Upload, Request};

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
        Csrf::verify();

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
                    if (!preg_match('#^https?://(www\.)?google\.com/maps/embed#i', $value)) {
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
