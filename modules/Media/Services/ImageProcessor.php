<?php
declare(strict_types=1);

namespace VeloCMS\Modules\Media\Services;

/**
 * ImageProcessor — EXIF-Strip + WebP conversion (GD-based, no Imagick needed)
 *
 * Security rules:
 * - Always strips EXIF (location, device info, etc.) — DSGVO compliance
 * - Converts JPEG/PNG to WebP for optimal web delivery
 * - GIF passes through as-is (animations preserved)
 * - Never executes code from image content
 */
class ImageProcessor
{
    private const MAX_DIMENSION = 2400; // px — downscale large uploads
    private const WEBP_QUALITY  = 82;
    private const JPEG_QUALITY  = 85;

    /**
     * Process an uploaded image: strip EXIF, convert to WebP, downscale if needed.
     * Returns array with new filename/path/dimensions/mime, or null on failure.
     *
     * @return array{filename:string,path:string,width:int,height:int,mime:string}|null
     */
    public function processImage(string $tmpPath, string $destPath, string $mime): ?array
    {
        $image = $this->loadImage($tmpPath, $mime);
        if ($image === null) return null;

        // Apply EXIF orientation (JPEG only) then strip EXIF by re-encoding
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $image = $this->applyExifOrientation($image, $tmpPath);
        }

        // Downscale if too large
        $w = imagesx($image);
        $h = imagesy($image);
        if ($w > self::MAX_DIMENSION || $h > self::MAX_DIMENSION) {
            $image = $this->resize($image, $w, $h);
            $w     = imagesx($image);
            $h     = imagesy($image);
        }

        // Convert destination to WebP (strip original extension)
        $destDir  = dirname($destPath);
        $basename = pathinfo($destPath, PATHINFO_FILENAME);
        $filename = $basename . '.webp';
        $newPath  = $destDir . '/' . $filename;

        $ok = imagewebp($image, $newPath, self::WEBP_QUALITY);
        imagedestroy($image);

        if (!$ok || !file_exists($newPath)) return null;

        return [
            'filename' => $filename,
            'path'     => $newPath,
            'width'    => $w,
            'height'   => $h,
            'mime'     => 'image/webp',
        ];
    }

    private function loadImage(string $path, string $mime): ?\GdImage
    {
        $result = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png'  => @imagecreatefrompng($path),
            'image/webp' => @imagecreatefromwebp($path),
            default      => false,
        };
        return ($result instanceof \GdImage) ? $result : null;
    }

    private function applyExifOrientation(\GdImage $image, string $path): \GdImage
    {
        try {
            $exif = @exif_read_data($path);
            $orientation = $exif['Orientation'] ?? 1;
        } catch (\Throwable) {
            return $image;
        }

        return match ((int) $orientation) {
            3 => imagerotate($image, 180, 0) ?: $image,
            6 => imagerotate($image, -90, 0) ?: $image,
            8 => imagerotate($image, 90, 0)  ?: $image,
            default => $image,
        };
    }

    private function resize(\GdImage $src, int $w, int $h): \GdImage
    {
        $max = self::MAX_DIMENSION;
        if ($w >= $h) {
            $nw = $max;
            $nh = (int) round($h * $max / $w);
        } else {
            $nh = $max;
            $nw = (int) round($w * $max / $h);
        }

        $dst = imagecreatetruecolor($nw, $nh);
        // Preserve transparency for PNG
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($src);
        return $dst;
    }
}
