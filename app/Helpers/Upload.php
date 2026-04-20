<?php

namespace App\Helpers;

class Upload
{
    public static function handle(array $file, string $directory): ?string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $config = require BASE_PATH . '/config/storage.php';

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $config['allowed_types'])) {
            Flash::set('error', 'Tipe file tidak diizinkan. Gunakan JPG, PNG, WebP, atau GIF.');
            return null;
        }

        if ($file['size'] > $config['max_size']) {
            Flash::set('error', 'Ukuran file terlalu besar. Maksimal 5MB.');
            return null;
        }

        $extension = self::getExtension($mimeType);
        $filename = uniqid() . '_' . time() . '.' . $extension;

        $uploadDir = $config['uploads_path'] . '/' . $directory;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $directory . '/' . $filename;
        }

        return null;
    }

    public static function delete(string $path): bool
    {
        $config = require BASE_PATH . '/config/storage.php';
        $fullPath = $config['uploads_path'] . '/' . $path;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    private static function getExtension(string $mimeType): string
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];
        return $map[$mimeType] ?? 'jpg';
    }
}
