<?php

namespace App\Controllers\Admin;

use App\Middleware\AuthMiddleware;
use App\Models\SettingModel;
use App\Helpers\{View, Flash, Csrf, Upload};

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
                if (!empty($_FILES[$fileKey]['name'])) {
                    $uploaded = Upload::handle($_FILES[$fileKey], 'logo');
                    if ($uploaded) {
                        if ($setting['value']) Upload::delete($setting['value']);
                        $this->model->updateByKey($key, $uploaded);
                    }
                }
                continue;
            }

            if (isset($_POST[$key])) {
                $this->model->updateByKey($key, trim($_POST[$key]));
            }
        }

        Flash::set('success', 'Pengaturan berhasil disimpan.');
        redirect('/admin/settings');
    }
}
