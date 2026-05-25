<?php
declare(strict_types=1);

namespace VeloCMS\Modules\Media\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Media\Models\MediaModel;
use VeloCMS\Modules\Media\Services\ImageProcessor;

class MediaController extends Controller
{
    private MediaModel   $model;
    private ImageProcessor $processor;

    private const ALLOWED_MIME = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf',
    ];
    private const MAX_SIZE = 10 * 1024 * 1024; // 10 MB

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->model     = new MediaModel();
        $this->processor = new ImageProcessor();
    }

    public function index(): void
    {
        $page    = max(1, (int) ($this->input('page', 1)));
        $perPage = 24;
        $offset  = ($page - 1) * $perPage;
        $total   = $this->model->count();
        $media   = $this->model->getAll($perPage, $offset);

        $this->view->extend('admin');
        $this->render('admin/index', [
            'media'   => $media,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
        ]);
    }

    public function upload(): void
    {
        Auth::verifyCsrf();

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['ok' => false, 'error' => 'Upload fehlgeschlagen'], 400);
        }

        $file = $_FILES['file'];

        // Size check
        if ($file['size'] > self::MAX_SIZE) {
            $this->json(['ok' => false, 'error' => 'Datei zu groß (max. 10 MB)'], 400);
        }

        // MIME check via fileinfo (not extension)
        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($file['tmp_name']);

        if (!in_array($mimeReal, self::ALLOWED_MIME, true)) {
            $this->json(['ok' => false, 'error' => 'Dateityp nicht erlaubt'], 415);
        }

        // Generate safe filename
        $ext      = $this->safeExt($file['name'], $mimeReal);
        $basename = bin2hex(random_bytes(8)) . '_' . time();
        $filename = $basename . '.' . $ext;
        $subDir   = date('Y/m');
        $destDir  = BASE_PATH . '/public/uploads/' . $subDir;

        if (!is_dir($destDir) && !mkdir($destDir, 0775, true)) {
            $this->json(['ok' => false, 'error' => 'Upload-Verzeichnis konnte nicht erstellt werden'], 500);
        }

        $destPath = $destDir . '/' . $filename;

        // Image processing: EXIF strip + WebP conversion
        $width = $height = null;
        if (str_starts_with($mimeReal, 'image/') && $mimeReal !== 'image/gif') {
            $result = $this->processor->processImage($file['tmp_name'], $destPath, $mimeReal);
            if ($result === null) {
                $this->json(['ok' => false, 'error' => 'Bildverarbeitung fehlgeschlagen'], 500);
            }
            $filename = $result['filename'];
            $destPath = $result['path'];
            $width    = $result['width'];
            $height   = $result['height'];
            $mimeReal = $result['mime'];
            $ext      = pathinfo($filename, PATHINFO_EXTENSION);
        } else {
            // Non-image: just move
            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                $this->json(['ok' => false, 'error' => 'Datei konnte nicht gespeichert werden'], 500);
            }
        }

        $relativePath = '/uploads/' . $subDir . '/' . $filename;

        $id = $this->model->insert([
            'filename'    => $filename,
            'original'    => $file['name'],
            'mime'        => $mimeReal,
            'size'        => filesize($destPath),
            'width'       => $width,
            'height'      => $height,
            'path'        => $relativePath,
            'uploaded_by' => Auth::id() ?? 1,
        ]);

        $this->json([
            'ok'   => true,
            'id'   => $id,
            'path' => $relativePath,
            'mime' => $mimeReal,
        ]);
    }

    public function updateAlt(string $id): void
    {
        Auth::verifyCsrf();
        $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        $this->model->updateAlt(
            (int) $id,
            substr((string) ($payload['alt_de'] ?? ''), 0, 255),
            substr((string) ($payload['alt_en'] ?? ''), 0, 255)
        );
        $this->json(['ok' => true]);
    }

    public function delete(string $id): void
    {
        Auth::verifyCsrf();
        $path = $this->model->delete((int) $id);
        if ($path !== null) {
            $abs = BASE_PATH . '/public' . $path;
            if (file_exists($abs)) {
                unlink($abs);
            }
        }
        $this->json(['ok' => true]);
    }

    // JSON list for media picker modal
    public function listJson(): void
    {
        $media = $this->model->getAll(120, 0);
        $this->json(['ok' => true, 'media' => $media]);
    }

    private function safeExt(string $originalName, string $mime): string
    {
        $map = [
            'image/jpeg'       => 'jpg',
            'image/png'        => 'png',
            'image/gif'        => 'gif',
            'image/webp'       => 'webp',
            'application/pdf'  => 'pdf',
        ];
        return $map[$mime] ?? 'bin';
    }
}
