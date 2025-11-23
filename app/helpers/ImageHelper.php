<?php
// FILE: /app/helpers/ImageHelper.php

class ImageHelper {
    public static function validateDimensions($filePath, $minWidth = 200, $minHeight = 200, $maxWidth = 4000, $maxHeight = 4000) {
        if (!file_exists($filePath)) {
            throw new Exception('File not found');
        }

        $imageInfo = getimagesize($filePath);
        if ($imageInfo === false) {
            throw new Exception('Invalid image file');
        }

        list($width, $height) = $imageInfo;

        if ($width < $minWidth || $height < $minHeight) {
            throw new Exception("Image dimensions too small. Minimum: {$minWidth}x{$minHeight}px");
        }

        if ($width > $maxWidth || $height > $maxHeight) {
            throw new Exception("Image dimensions too large. Maximum: {$maxWidth}x{$maxHeight}px");
        }

        return [
            'width' => $width,
            'height' => $height,
            'mime' => $imageInfo['mime'],
        ];
    }

    public static function createThumbnail($sourcePath, $destinationPath, $width = 200, $height = 200) {
        if (!file_exists($sourcePath)) {
            throw new Exception('Source file not found');
        }

        $imageInfo = getimagesize($sourcePath);
        if ($imageInfo === false) {
            throw new Exception('Invalid image file');
        }

        list($sourceWidth, $sourceHeight, $imageType) = $imageInfo;

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            default:
                throw new Exception('Unsupported image type');
        }

        $aspectRatio = $sourceWidth / $sourceHeight;
        if ($width / $height > $aspectRatio) {
            $width = $height * $aspectRatio;
        } else {
            $height = $width / $aspectRatio;
        }

        $thumbnail = imagecreatetruecolor($width, $height);

        if ($imageType === IMAGETYPE_PNG) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }

        imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

        $destDir = dirname($destinationPath);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumbnail, $destinationPath, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbnail, $destinationPath, 8);
                break;
        }

        imagedestroy($sourceImage);
        imagedestroy($thumbnail);

        return $destinationPath;
    }

    public static function simulateFaceDetection($filePath) {
        // Simulated face detection - in production, integrate with real AI service
        // For now, randomly return true 90% of the time to simulate detection
        return (rand(1, 100) <= 90);
    }

    public static function addWatermark($sourcePath, $destinationPath, $watermarkText, $opacity = 50) {
        if (!file_exists($sourcePath)) {
            throw new Exception('Source file not found');
        }

        $imageInfo = getimagesize($sourcePath);
        if ($imageInfo === false) {
            throw new Exception('Invalid image file');
        }

        list($width, $height, $imageType) = $imageInfo;

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($sourcePath);
                break;
            default:
                throw new Exception('Unsupported image type');
        }

        $textColor = imagecolorallocatealpha($image, 255, 255, 255, 127 - ($opacity * 1.27));
        $fontSize = max(12, $width / 30);
        $angle = 0;
        $x = $width - 150;
        $y = $height - 20;

        imagettftext($image, $fontSize, $angle, $x, $y, $textColor, __DIR__ . '/../../public/assets/fonts/arial.ttf', $watermarkText);

        $destDir = dirname($destinationPath);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($image, $destinationPath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($image, $destinationPath, 8);
                break;
        }

        imagedestroy($image);

        return $destinationPath;
    }

    public static function resize($sourcePath, $destinationPath, $newWidth, $newHeight = null) {
        if (!file_exists($sourcePath)) {
            throw new Exception('Source file not found');
        }

        $imageInfo = getimagesize($sourcePath);
        list($width, $height, $imageType) = $imageInfo;

        if ($newHeight === null) {
            $aspectRatio = $width / $height;
            $newHeight = $newWidth / $aspectRatio;
        }

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            default:
                throw new Exception('Unsupported image type');
        }

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        if ($imageType === IMAGETYPE_PNG) {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
        }

        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($resizedImage, $destinationPath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($resizedImage, $destinationPath, 8);
                break;
        }

        imagedestroy($sourceImage);
        imagedestroy($resizedImage);

        return $destinationPath;
    }
}
