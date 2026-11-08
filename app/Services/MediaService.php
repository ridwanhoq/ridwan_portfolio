<?php

namespace App\Services;

use App\Models\Media;
use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Stores uploaded images safely.
 *
 * Every upload is decoded and re-encoded with GD. This drops EXIF/GPS metadata
 * and anything appended to the file (polyglot payloads). Files get random
 * names, and WebP variants are generated for responsive delivery.
 */
class MediaService
{
    public const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    public const MAX_KILOBYTES = 8192;

    private const MAX_DIMENSION = 2400;

    private const THUMB_WIDTH = 720;

    private const MAX_SOURCE_PIXELS = 40_000_000;

    public function __construct(private readonly string $disk = 'public') {}

    public function store(UploadedFile $file, ?string $alt = null, ?int $userId = null): Media
    {
        $mime = (string) $file->getMimeType();

        if (! in_array($mime, self::ALLOWED_MIME_TYPES, true)) {
            throw new RuntimeException('Unsupported image type.');
        }

        $info = @getimagesize($file->getRealPath());

        if ($info === false || $info[0] * $info[1] > self::MAX_SOURCE_PIXELS) {
            throw new RuntimeException('The image could not be read or is too large.');
        }

        $source = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if (! $source instanceof GdImage) {
            throw new RuntimeException('The image could not be decoded.');
        }

        $image = $this->resize($source, self::MAX_DIMENSION);
        $directory = 'media/'.now()->format('Y/m');
        $basename = Str::lower(Str::random(24));
        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
            default => 'png', // PNG and GIF (first frame) are stored as PNG.
        };

        $filename = "{$basename}.{$extension}";
        $this->write($image, "{$directory}/{$filename}", $extension);

        $variants = [];

        if ($extension !== 'webp') {
            $variants['webp'] = "{$basename}.webp";
            $this->write($image, "{$directory}/{$variants['webp']}", 'webp');
        }

        if (imagesx($image) > self::THUMB_WIDTH) {
            $variants['thumb'] = "{$basename}-thumb.webp";
            $this->write($this->resize($image, self::THUMB_WIDTH), "{$directory}/{$variants['thumb']}", 'webp');
        }

        $media = Media::query()->create([
            'disk' => $this->disk,
            'directory' => $directory,
            'filename' => $filename,
            'original_name' => Str::limit($this->cleanName($file->getClientOriginalName()), 250, ''),
            'mime_type' => $extension === 'jpg' ? 'image/jpeg' : "image/{$extension}",
            'size' => Storage::disk($this->disk)->size("{$directory}/{$filename}"),
            'width' => imagesx($image),
            'height' => imagesy($image),
            'variants' => $variants ?: null,
            'alt' => $alt,
            'uploaded_by' => $userId,
        ]);

        return $media;
    }

    public function delete(Media $media): void
    {
        Storage::disk($media->disk)->delete($media->allPaths());
        $media->delete();
    }

    private function resize(GdImage $image, int $max): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width <= $max && $height <= $max) {
            return $this->normalize($image);
        }

        $ratio = min($max / $width, $max / $height);
        $resized = imagescale($image, max(1, (int) round($width * $ratio)), max(1, (int) round($height * $ratio)), IMG_BICUBIC);

        if (! $resized instanceof GdImage) {
            throw new RuntimeException('The image could not be resized.');
        }

        return $this->normalize($resized);
    }

    /**
     * Convert palette images to true colour and keep transparency.
     */
    private function normalize(GdImage $image): GdImage
    {
        if (! imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);

        return $image;
    }

    private function write(GdImage $image, string $path, string $format): void
    {
        ob_start();

        $ok = match ($format) {
            'jpg' => imagejpeg($image, null, 85),
            'webp' => imagewebp($image, null, 82),
            default => imagepng($image, null, 8),
        };

        $contents = (string) ob_get_clean();

        if (! $ok || $contents === '') {
            throw new RuntimeException('The image could not be encoded.');
        }

        Storage::disk($this->disk)->put($path, $contents, 'public');
    }

    private function cleanName(string $name): string
    {
        return preg_replace('/[^\w.\- ]+/u', '', $name) ?: 'upload';
    }
}
