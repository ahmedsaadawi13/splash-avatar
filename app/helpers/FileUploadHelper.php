<?php
// FILE: /app/helpers/FileUploadHelper.php

class FileUploadHelper {
    private $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    private $maxFileSize = 10485760; // 10 MB
    private $uploadDir;

    public function __construct($uploadDir = null) {
        if ($uploadDir === null) {
            $uploadDir = __DIR__ . '/../../storage/uploads/';
        }
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
    }

    public function setMaxFileSize($bytes) {
        $this->maxFileSize = $bytes;
        return $this;
    }

    public function setAllowedMimeTypes($types) {
        $this->allowedMimeTypes = $types;
        return $this;
    }

    public function upload($file, $subDir = '', $customName = null) {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception('Invalid file upload');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception('File size exceeds limit');
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No file uploaded');
            default:
                throw new Exception('Upload error occurred');
        }

        if ($file['size'] > $this->maxFileSize) {
            throw new Exception('File size exceeds maximum allowed size of ' . ($this->maxFileSize / 1048576) . ' MB');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $this->allowedMimeTypes));
        }

        $extension = $this->getExtensionFromMime($mimeType);
        $fileName = $customName ? $customName : $this->generateUniqueFileName($extension);

        $targetDir = $this->uploadDir . ($subDir ? rtrim($subDir, '/') . '/' : '');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Failed to move uploaded file');
        }

        return [
            'file_path' => str_replace($this->uploadDir, '', $targetPath),
            'file_name' => $fileName,
            'original_name' => $file['name'],
            'mime_type' => $mimeType,
            'size' => $file['size'],
        ];
    }

    public function uploadMultiple($files, $subDir = '') {
        $uploaded = [];

        foreach ($files['tmp_name'] as $key => $tmp_name) {
            $file = [
                'name' => $files['name'][$key],
                'type' => $files['type'][$key],
                'tmp_name' => $tmp_name,
                'error' => $files['error'][$key],
                'size' => $files['size'][$key],
            ];

            try {
                $uploaded[] = $this->upload($file, $subDir);
            } catch (Exception $e) {
                // Log error but continue with other files
                error_log('File upload failed: ' . $e->getMessage());
            }
        }

        return $uploaded;
    }

    private function generateUniqueFileName($extension) {
        return uniqid('', true) . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    }

    private function getExtensionFromMime($mimeType) {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
        ];

        return isset($extensions[$mimeType]) ? $extensions[$mimeType] : 'jpg';
    }

    public function delete($filePath) {
        $fullPath = $this->uploadDir . $filePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}
