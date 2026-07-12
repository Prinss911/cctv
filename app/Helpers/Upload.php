<?php
namespace App\Helpers;

use App\Helpers\SecurityLogger;
use App\Helpers\Flash;
use App\Helpers\Request;

class Upload
{
    /**
     * Maximum allowed image dimensions (width x height)
     */
    const MAX_IMAGE_DIMENSION = 4000;

    /**
     * Handle file upload
     * Returns array with 'path' on success, or ['error' => message] on failure
     */
    public static function handle(array $file, string $directory): array
    {
        // Validate directory parameter
        if (empty($directory) || !is_string($directory)) {
            SecurityLogger::logUpload('unknown_file', false);
            return ['error' => 'Direktori upload tidak valid.'];
        }
        
        // Check for upload errors
        if (!isset($file['error'])) {
            SecurityLogger::logUpload('unknown_file', false);
            return ['error' => 'Tidak ada file yang diupload.'];
        }
        
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
                return ['error' => 'Ukuran file melebihi batas yang diperbolehkan di konfigurasi server.'];
            case UPLOAD_ERR_PARTIAL:
                SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
                return ['error' => 'Upload file tidak lengkap.'];
            case UPLOAD_ERR_NO_FILE:
                SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
                return ['error' => 'Tidak ada file yang dipilih.'];
            case UPLOAD_ERR_NO_TMP_DIR:
                SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
                return ['error' => 'Folder temporary server tidak tersedia.'];
            case UPLOAD_ERR_CANT_WRITE:
                SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
                return ['error' => 'Gagal menulis file ke disk.'];
            case UPLOAD_ERR_EXTENSION:
                SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
                return ['error' => 'Upload dihentikan oleh ekstensi PHP.'];
            default:
                SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
                return ['error' => 'Terjadi kesalahan saat upload (kode: ' . $file['error'] . ').'];
        }
        
        // Validate file exists
        if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
            return ['error' => 'File upload tidak ditemukan di server temporary.'];
        }
        
        $config = require BASE_PATH . '/config/storage.php';
        
        // 1. Validate file is actually an uploaded file (not a fake)
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
            return ['error' => 'File tidak valid atau bukan hasil upload yang sah.'];
        }
        
        // Get file info
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        // 2. Validate MIME type
        if (!in_array($mimeType, $config['allowed_types'])) {
            SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
            return ['error' => 'Tipe file tidak diizinkan. Gunakan JPG, PNG, WebP, atau GIF.'];
        }
        
        // 3. Validate file extension from original filename matches MIME type
        $originalExtension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        $expectedExtension = self::getExtension($mimeType);
        
        if ($originalExtension && $originalExtension !== $expectedExtension) {
            SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
            return ['error' => 'Ekstensi file tidak cocok dengan tipe konten.'];
        }
        
        // 4. Validate file size
        if ($file['size'] > $config['max_size']) {
            $maxMB = $config['max_size'] / (1024 * 1024);
            SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
            return ['error' => "Ukuran file terlalu besar. Maksimal {$maxMB}MB."];
        }
        
        // 5. Validate minimum file size (prevent empty file upload)
        if ($file['size'] < 1) {
            SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
            return ['error' => 'File kosong tidak diperbolehkan.'];
        }
        
        // 6. Generate Re-encoded filename from MIME type (not from user filename)
        $extension = $expectedExtension;
        $filename = uniqid() . '_' . time() . '.' . $extension;
        
        // 7. Validate filename contains only safe characters
        if (!preg_match('/^[\w\-\.]+$/', $filename)) {
            SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
            return ['error' => 'Nama file tidak valid.'];
        }
        
        // 8. Content integrity check for images: verify it's a real image
        if (str_starts_with($mimeType, 'image/')) {
            // Use getimagesize() to verify it's a valid image
            $imageInfo = @getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
                return ['error' => 'File tidak valid atau gambar rusak.'];
            }
            
            // 9. Validate image dimensions (max 4000x4000)
            $width = $imageInfo[0] ?? 0;
            $height = $imageInfo[1] ?? 0;
            if ($width > self::MAX_IMAGE_DIMENSION || $height > self::MAX_IMAGE_DIMENSION) {
                SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
                return ['error' => "Ukuran gambar terlalu besar. Maksimal " . self::MAX_IMAGE_DIMENSION . "x" . self::MAX_IMAGE_DIMENSION . " piksel."];
            }
            
            // Verify dimensions are positive
            if ($width < 1 || $height < 1) {
                SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
                return ['error' => 'File gambar tidak valid.'];
            }
        }
        
        // Validate filename contains only safe characters
        if (!preg_match('/^[\w\-\.]+$/', $filename)) {
            SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
            return ['error' => 'Nama file tidak valid.'];
        }
        
        try {
            $sanitizedDirectory = self::sanitizePath($directory);
        } catch (\InvalidArgumentException $e) {
            SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
            return ['error' => 'Direktori upload tidak valid: ' . $e->getMessage()];
        }
        
        $uploadDir = $config['uploads_path'] . '/' . $sanitizedDirectory;
        
        // Ensure the upload directory is within the configured uploads path
        $realUploadDir = realpath($config['uploads_path']);
        if ($realUploadDir === false) {
            SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
            return ['error' => 'Konfigurasi path upload tidak valid.'];
        }
        
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
                return ['error' => 'Gagal membuat direktori upload.'];
            }
        }
        
        $realUploadSubDir = realpath($uploadDir);
        if ($realUploadSubDir === false || strpos($realUploadSubDir, $realUploadDir) !== 0) {
            SecurityLogger::logUpload($file['name'] ?? 'unknown', false);
            return ['error' => 'Path upload tidak valid.'];
        }
        
        $destination = $uploadDir . '/' . $filename;
        
         if (move_uploaded_file($file['tmp_name'], $destination)) {
             // Log successful upload
             SecurityLogger::logUpload($file['name'] ?? $filename, true);

             // Generate thumbnails for images if GD or Imagick is available
             $thumbnails = [];
             if (str_starts_with($mimeType, 'image/') && (extension_loaded('gd') || extension_loaded('imagick'))) {
                 $thumbnails = self::generateThumbnails($destination, $sanitizedDirectory, $filename, $extension);
             }

             $result = ['path' => $sanitizedDirectory . '/' . $filename];
             if (!empty($thumbnails)) {
                 $result['thumbnails'] = $thumbnails;
             }
             return $result;
         }
        
        // Log failed upload
        SecurityLogger::logUpload($file['name'] ?? $filename, false);
        
        return ['error' => 'Gagal memindahkan file yang diupload.'];
    }

    /**
     * Handle image upload from URL
     * Downloads image from URL, validates it, and saves to uploads directory
     * Returns array with 'path' on success, or ['error' => message] on failure
     */
    public static function handleFromUrl(string $url, string $directory): array
    {
        // Validate directory parameter
        if (empty($directory) || !is_string($directory)) {
            SecurityLogger::logUpload('url_upload', false);
            return ['error' => 'Direktori upload tidak valid.'];
        }

        // Validate URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            SecurityLogger::logUpload('url_upload', false);
            return ['error' => 'URL tidak valid.'];
        }

        // Only allow http/https protocols
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['scheme']) || !in_array(strtolower($parsedUrl['scheme']), ['http', 'https'])) {
            SecurityLogger::logUpload('url_upload', false);
            return ['error' => 'Hanya protokol HTTP dan HTTPS yang diizinkan.'];
        }

        // Create stream context for downloading
        $context = stream_context_create([
            'http' => [
                'timeout' => 15,
                'user_agent' => 'BayuCCTV/1.0',
                'follow_location' => 1,
                'max_redirects' => 5,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        // Download the file
        $imageData = @file_get_contents($url, false, $context);
        if ($imageData === false) {
            SecurityLogger::logUpload('url_upload', false);
            return ['error' => 'Gagal mengunduh gambar dari URL.'];
        }

        // Save to temp file
        $tempFile = tempnam(sys_get_temp_dir(), 'url_upload_');
        if ($tempFile === false) {
            SecurityLogger::logUpload('url_upload', false);
            return ['error' => 'Gagal membuat file temporary.'];
        }

        $written = file_put_contents($tempFile, $imageData);
        if ($written === false) {
            unlink($tempFile);
            SecurityLogger::logUpload('url_upload', false);
            return ['error' => 'Gagal menyimpan file temporary.'];
        }

        $config = require BASE_PATH . '/config/storage.php';

        // Check file size against config
        $fileSize = filesize($tempFile);
        if ($fileSize > $config['max_size']) {
            unlink($tempFile);
            $maxMB = $config['max_size'] / (1024 * 1024);
            SecurityLogger::logUpload('url_upload', false);
            return ['error' => "Ukuran file terlalu besar. Maksimal {$maxMB}MB."];
        }

        // Validate minimum file size
        if ($fileSize < 1) {
            unlink($tempFile);
            SecurityLogger::logUpload('url_upload', false);
            return ['error' => 'File kosong tidak diperbolehkan.'];
        }

        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tempFile);
        finfo_close($finfo);

        if (!in_array($mimeType, $config['allowed_types'])) {
            unlink($tempFile);
            SecurityLogger::logUpload('url_upload', false);
            return ['error' => 'Tipe file tidak diizinkan. Gunakan JPG, PNG, WebP, atau GIF.'];
        }

        // Get expected extension from MIME type
        $extension = self::getExtension($mimeType);

        // Content integrity check for images
        if (str_starts_with($mimeType, 'image/')) {
            $imageInfo = @getimagesize($tempFile);
            if ($imageInfo === false) {
                unlink($tempFile);
                SecurityLogger::logUpload('url_upload', false);
                return ['error' => 'File tidak valid atau gambar rusak.'];
            }

            // Validate image dimensions
            $width = $imageInfo[0] ?? 0;
            $height = $imageInfo[1] ?? 0;
            if ($width > self::MAX_IMAGE_DIMENSION || $height > self::MAX_IMAGE_DIMENSION) {
                unlink($tempFile);
                SecurityLogger::logUpload('url_upload', false);
                return ['error' => "Ukuran gambar terlalu besar. Maksimal " . self::MAX_IMAGE_DIMENSION . "x" . self::MAX_IMAGE_DIMENSION . " piksel."];
            }

            if ($width < 1 || $height < 1) {
                unlink($tempFile);
                SecurityLogger::logUpload('url_upload', false);
                return ['error' => 'File gambar tidak valid.'];
            }
        }

        // Generate random filename
        $filename = uniqid() . '_' . time() . '.' . $extension;

        // Validate filename contains only safe characters
        if (!preg_match('/^[\w\-\.]+$/', $filename)) {
            unlink($tempFile);
            SecurityLogger::logUpload('url_upload', false);
            return ['error' => 'Nama file tidak valid.'];
        }

        try {
            $sanitizedDirectory = self::sanitizePath($directory);
        } catch (\InvalidArgumentException $e) {
            unlink($tempFile);
            SecurityLogger::logUpload('url_upload', false);
            return ['error' => 'Direktori upload tidak valid: ' . $e->getMessage()];
        }

        $uploadDir = $config['uploads_path'] . '/' . $sanitizedDirectory;

        // Ensure the upload directory is within the configured uploads path
        $realUploadDir = realpath($config['uploads_path']);
        if ($realUploadDir === false) {
            unlink($tempFile);
            SecurityLogger::logUpload('url_upload', false);
            return ['error' => 'Konfigurasi path upload tidak valid.'];
        }

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                unlink($tempFile);
                SecurityLogger::logUpload('url_upload', false);
                return ['error' => 'Gagal membuat direktori upload.'];
            }
        }

        $realUploadSubDir = realpath($uploadDir);
        if ($realUploadSubDir === false || strpos($realUploadSubDir, $realUploadDir) !== 0) {
            unlink($tempFile);
            SecurityLogger::logUpload('url_upload', false);
            return ['error' => 'Path upload tidak valid.'];
        }

        $destination = $uploadDir . '/' . $filename;

        // Use copy() then unlink() for cross-filesystem safety
        if (copy($tempFile, $destination)) {
            unlink($tempFile);

            // Log successful upload
            SecurityLogger::logUpload(basename($url), true);

            // Generate thumbnails for images if GD or Imagick is available
            $thumbnails = [];
            if (str_starts_with($mimeType, 'image/') && (extension_loaded('gd') || extension_loaded('imagick'))) {
                $thumbnails = self::generateThumbnails($destination, $sanitizedDirectory, $filename, $extension);
            }

            $result = ['path' => $sanitizedDirectory . '/' . $filename];
            if (!empty($thumbnails)) {
                $result['thumbnails'] = $thumbnails;
            }
            return $result;
        }

        // Clean up temp file on failure
        unlink($tempFile);

        // Log failed upload
        SecurityLogger::logUpload('url_upload', false);

        return ['error' => 'Gagal menyimpan file yang diunduh.'];
    }

    public static function delete(string $path): bool
    {
        $config = require BASE_PATH . '/config/storage.php';

        try {
            $sanitizedPath = self::sanitizePath($path);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        $fullPath = $config['uploads_path'] . '/' . $sanitizedPath;

        // Ensure the path is within the uploads directory
        $realUploadDir = realpath($config['uploads_path']);
        $realFullPath = realpath($fullPath);
        if ($realFullPath === false || strpos($realFullPath, $realUploadDir) !== 0) {
            return false;
        }

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

    private static function generateThumbnails(string $sourcePath, string $directory, string $filename, string $extension): array
    {
        $widths = [400, 800, 1200, 1600];
        $thumbnails = [];

        // Get image dimensions and create image resource based on extension
        list($origWidth, $origHeight) = getimagesize($sourcePath);
        if ($origWidth === false) {
            return $thumbnails;
        }

        // Determine image create function based on extension
        $createFunc = null;
        $saveFunc = null;
        $mime = null;

        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                $createFunc = 'imagecreatefromjpeg';
                $saveFunc = function($img, $path) {
                    return imagejpeg($img, $path, 85); // quality 85
                };
                $mime = 'image/jpeg';
                break;
            case 'png':
                $createFunc = 'imagecreatefrompng';
                $saveFunc = function($img, $path) {
                    return imagepng($img, $path, 9); // Maximum compression for PNG
                };
                $mime = 'image/png';
                break;
            case 'webp':
                if (function_exists('imagecreatefromwebp')) {
                    $createFunc = 'imagecreatefromwebp';
                    $saveFunc = function($img, $path) {
                        return imagewebp($img, $path, 80);
                    };
                } else {
                    // fallback to GD if webp not supported
                    return $thumbnails;
                }
                $mime = 'image/webp';
                break;
            case 'gif':
                $createFunc = 'imagecreatefromgif';
                $saveFunc = function($img, $path) {
                    return imagegif($img, $path);
                };
                $mime = 'image/gif';
                break;
            default:
                return $thumbnails;
        }

        if (!$createFunc || !$saveFunc) {
            return $thumbnails;
        }

        $originalImage = $createFunc($sourcePath);
        if (!$originalImage) {
            return $thumbnails;
        }

        foreach ($widths as $width) {
            // Calculate height to maintain aspect ratio
            $height = intval($origHeight * ($width / $origWidth));
            if ($height <= 0) {
                $height = 1;
            }

            // Create thumbnail image
            $thumbnail = imagecreatetruecolor($width, $height);
            // Preserve transparency for PNG and GIF
            if ($extension === 'png' || $extension === 'gif') {
                imagecolortransparent($thumbnail, imagecolorallocatealpha($thumbnail, 0, 0, 0, 127));
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
            }
            imagecopyresampled($thumbnail, $originalImage, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);

            // Generate thumbnail filename
            $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
            $thumbFilename = $nameWithoutExt . '_' . $width . '.' . $extension;
            $thumbPath = $directory . '/' . $thumbFilename;
            $fullThumbPath = BASE_PATH . '/public/uploads/' . $thumbPath;

            // Save thumbnail
            if ($saveFunc($thumbnail, $fullThumbPath)) {
                $thumbnails[$width] = $thumbPath;
            }

            imagedestroy($thumbnail);
        }

        imagedestroy($originalImage);
        return $thumbnails;
    }

    private static function sanitizePath(string $path): string
    {
        // Reject null bytes
        if (strpos($path, "\0") !== false) {
            throw new \InvalidArgumentException('Null byte in path');
        }

        // Reject directory traversal sequences
        if (preg_match('/\.\.(\/|\\\\\\/)/', $path) || preg_match('/\.(\/|\\\\\\/)/', $path)) {
            throw new \InvalidArgumentException('Path contains directory traversal sequences');
        }

        // Reject absolute paths (Unix and Windows)
        if (preg_match('#^(/|[a-zA-Z]:\\\\)#', $path)) {
            throw new \InvalidArgumentException('Absolute paths are not allowed');
        }

        // Split into components and validate each
        $parts = preg_split('#[/\\\\]+#', $path);
        foreach ($parts as $part) {
            if ($part === '' || $part === '.' || $part === '..') {
                throw new \InvalidArgumentException('Invalid path component');
            }
            // Only allow alphanumeric, underscore, hyphen, dot
            if (!preg_match('/^[\w\-\.]+$/', $part)) {
                throw new \InvalidArgumentException('Invalid characters in path component');
            }
        }

        return $path;
    }
}