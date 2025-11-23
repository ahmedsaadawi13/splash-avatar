<?php
// FILE: /app/controllers/FaceUploadController.php

class FaceUploadController extends Controller {
    public function index() {
        $tenantId = Auth::tenantId();
        $userId = Auth::id();

        $faceModel = new FaceUpload();

        if (Auth::isEndUser()) {
            $uploads = $faceModel->getByUser($userId, $tenantId);
        } else {
            $uploads = $faceModel->getByTenant($tenantId);
        }

        return $this->view('faces/index', [
            'title' => 'Face Uploads',
            'uploads' => $uploads,
        ]);
    }

    public function showUpload() {
        return $this->view('faces/upload', [
            'title' => 'Upload Face Photos',
        ]);
    }

    public function upload() {
        CSRF::validate();

        if (!Request::hasFile('face_photos')) {
            Session::flash('error', 'Please select at least one photo to upload');
            Response::redirect('/faces/upload');
        }

        $tenantId = Auth::tenantId();
        $userId = Auth::id();

        $fileUploader = new FileUploadHelper();
        $fileUploader->setMaxFileSize(10485760);

        $uploadedCount = 0;
        $files = $_FILES['face_photos'];

        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i],
            ];

            try {
                $uploadedFile = $fileUploader->upload($file, "faces/{$tenantId}");

                $fullPath = __DIR__ . '/../../storage/uploads/' . $uploadedFile['file_path'];
                $faceDetected = ImageHelper::simulateFaceDetection($fullPath);

                $faceModel = new FaceUpload();
                $faceModel->createUpload([
                    'tenant_id' => $tenantId,
                    'user_id' => $userId,
                    'title' => pathinfo($uploadedFile['original_name'], PATHINFO_FILENAME),
                    'file_path' => $uploadedFile['file_path'],
                    'original_file_name' => $uploadedFile['original_name'],
                    'mime_type' => $uploadedFile['mime_type'],
                    'size_bytes' => $uploadedFile['size'],
                    'face_detected' => $faceDetected ? 1 : 0,
                    'status' => $faceDetected ? 'validated' : 'rejected',
                    'rejection_reason' => $faceDetected ? null : 'No face detected',
                ]);

                $uploadedCount++;

            } catch (Exception $e) {
                error_log('Face upload error: ' . $e->getMessage());
            }
        }

        if ($uploadedCount > 0) {
            Session::flash('success', "Successfully uploaded {$uploadedCount} photo(s)");
        } else {
            Session::flash('error', 'Failed to upload photos. Please try again.');
        }

        Response::redirect('/faces');
    }

    public function delete($id) {
        CSRF::validate();

        $tenantId = Auth::tenantId();
        $userId = Auth::id();

        $faceModel = new FaceUpload();
        $face = $faceModel->find($id);

        if (!$face || $face['tenant_id'] != $tenantId) {
            Session::flash('error', 'Face upload not found');
            Response::redirect('/faces');
        }

        if (Auth::isEndUser() && $face['user_id'] != $userId) {
            Session::flash('error', 'You do not have permission to delete this upload');
            Response::redirect('/faces');
        }

        $fileUploader = new FileUploadHelper();
        $fileUploader->delete($face['file_path']);

        $faceModel->delete($id);

        Session::flash('success', 'Face upload deleted successfully');
        Response::redirect('/faces');
    }
}
