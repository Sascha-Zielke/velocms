<?php
declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Modules\Media;

use PHPUnit\Framework\TestCase;
use VeloCMS\Modules\Media\Services\ImageProcessor;

class ImageProcessorTest extends TestCase
{
    private ImageProcessor $processor;
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->processor = new ImageProcessor();
        $this->tmpDir    = sys_get_temp_dir() . '/vcms_test_' . uniqid();
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->tmpDir . '/*') ?: []);
        @rmdir($this->tmpDir);
    }

    public function testProcessImage_convertsJpegToWebp(): void
    {
        if (!function_exists('imagecreatefromjpeg') || !function_exists('imagewebp')) {
            $this->markTestSkipped('GD extension not available');
        }

        $src = $this->tmpDir . '/test.jpg';
        $img = imagecreatetruecolor(100, 80);
        imagefill($img, 0, 0, imagecolorallocate($img, 200, 100, 50));
        imagejpeg($img, $src, 90);
        imagedestroy($img);

        $result = $this->processor->processImage($src, $this->tmpDir . '/output.jpg', 'image/jpeg');

        $this->assertNotNull($result);
        $this->assertSame('image/webp', $result['mime']);
        $this->assertSame(100, $result['width']);
        $this->assertSame(80, $result['height']);
        $this->assertStringEndsWith('.webp', $result['filename']);
        $this->assertFileExists($result['path']);
    }

    public function testProcessImage_returnsNull_forInvalidFile(): void
    {
        $result = $this->processor->processImage(
            '/nonexistent/image.jpg',
            $this->tmpDir . '/out.webp',
            'image/jpeg'
        );
        $this->assertNull($result);
    }

    public function testProcessImage_downscalesLargeImages(): void
    {
        if (!function_exists('imagecreatetruecolor') || !function_exists('imagewebp')) {
            $this->markTestSkipped('GD extension not available');
        }

        // 3000x2000 — exceeds MAX_DIMENSION of 2400
        $src = $this->tmpDir . '/large.jpg';
        $img = imagecreatetruecolor(3000, 2000);
        imagefill($img, 0, 0, imagecolorallocate($img, 100, 150, 200));
        imagejpeg($img, $src, 85);
        imagedestroy($img);

        $result = $this->processor->processImage($src, $this->tmpDir . '/out_large.jpg', 'image/jpeg');

        $this->assertNotNull($result);
        $this->assertLessThanOrEqual(2400, $result['width']);
        $this->assertLessThanOrEqual(2400, $result['height']);
    }

    public function testProcessImage_preservesAspectRatio(): void
    {
        if (!function_exists('imagecreatetruecolor') || !function_exists('imagewebp')) {
            $this->markTestSkipped('GD extension not available');
        }

        $src = $this->tmpDir . '/ratio.jpg';
        $img = imagecreatetruecolor(200, 100); // 2:1 ratio
        imagejpeg($img, $src, 90);
        imagedestroy($img);

        $result = $this->processor->processImage($src, $this->tmpDir . '/out_ratio.jpg', 'image/jpeg');

        $this->assertNotNull($result);
        // Aspect ratio must be preserved (w/h ≈ 2.0)
        $ratio = $result['width'] / $result['height'];
        $this->assertEqualsWithDelta(2.0, $ratio, 0.01);
    }
}
